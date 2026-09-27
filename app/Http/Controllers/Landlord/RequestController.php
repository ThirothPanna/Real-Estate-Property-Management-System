<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();

        // Get all tenant IDs under this landlord
        $tenantIds = Tenancy::where('landlord_id', $landlordId)
            ->pluck('user_id')
            ->unique();

        // Base query
        $query = MaintenanceRequest::with('user')
            ->whereIn('user_id', $tenantIds)
            ->latest();

        // Tab filter
        $tab = $request->get('tab', 'all');
        if ($tab === 'pending')     $query->where('status', 'pending');
        if ($tab === 'in_progress') $query->where('status', 'in_progress');
        if ($tab === 'resolved')    $query->where('status', 'resolved');

        // Search
        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        // Counts for tabs
        $counts = [
            'all'         => MaintenanceRequest::whereIn('user_id', $tenantIds)->count(),
            'pending'     => MaintenanceRequest::whereIn('user_id', $tenantIds)->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::whereIn('user_id', $tenantIds)->where('status', 'in_progress')->count(),
            'resolved'    => MaintenanceRequest::whereIn('user_id', $tenantIds)->where('status', 'resolved')->count(),
        ];

        return view('landlord.requests.index', compact('requests', 'counts', 'tab', 'search'));
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        // Ownership check
        $tenantIds = Tenancy::where('landlord_id', Auth::id())
            ->pluck('user_id')
            ->unique();

        if (!$tenantIds->contains($maintenanceRequest->user_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,resolved'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $maintenanceRequest->status;

        $maintenanceRequest->update([
            'status' => $validated['status'],
        ]);

        // Notify the tenant
        if ($oldStatus !== $validated['status']) {
            Notification::create([
                'user_id' => $maintenanceRequest->user_id,
                'title'   => 'Request updated',
                'body'    => '"' . $maintenanceRequest->title . '" is now ' . str_replace('_', ' ', $validated['status']) . '.',
                'icon'    => 'request',
            ]);
        }

        return back()->with('status', 'Request updated.');
    }
}