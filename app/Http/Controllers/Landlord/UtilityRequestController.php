<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UtilityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtilityRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = UtilityRequest::with(['tenancy.tenant', 'tenancy.property'])
            ->whereHas('tenancy', fn ($tenancy) => $tenancy->where('landlord_id', Auth::id()));

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $counts = [
            'all' => $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'approved' => (int) ($statusCounts['approved'] ?? 0),
            'rejected' => (int) ($statusCounts['rejected'] ?? 0),
        ];

        $tab = $request->query('tab', 'pending');
        if (in_array($tab, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $tab);
        } else {
            $tab = 'all';
        }

        $utilityRequests = $query->latest()->paginate(15)->withQueryString();

        return view('landlord.utility-requests.index', compact('utilityRequests', 'counts', 'tab'));
    }

    public function update(Request $request, UtilityRequest $utilityRequest)
    {
        abort_unless($utilityRequest->tenancy->landlord_id === Auth::id(), 403);
        abort_unless($utilityRequest->status === 'pending', 409);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'landlord_response' => ['nullable', 'string', 'max:1000'],
        ]);

        $utilityRequest->update([
            'status' => $validated['status'],
            'landlord_response' => $validated['landlord_response'] ?? null,
            'decided_at' => now(),
        ]);

        Notification::create([
            'user_id' => $utilityRequest->tenancy->user_id,
            'title' => 'Utility request ' . $validated['status'],
            'body' => 'Your ' . $utilityRequest->utility_type . ' utility request was ' . $validated['status'] . '.',
            'icon' => 'request',
        ]);

        return redirect()
            ->route('landlord.utility-requests.index')
            ->with('success', 'Utility request ' . $validated['status'] . '.');
    }
}