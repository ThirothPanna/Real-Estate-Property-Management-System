<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        $announcements = Announcement::with(['property', 'recipients'])
            ->where('landlord_id', $landlordId)
            ->latest()
            ->paginate(15);

        $properties = Property::where('landlord_id', $landlordId)
            ->orderBy('name')
            ->get();

        // Get tenant list grouped by property
        $tenancies = Tenancy::with(['tenant', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->get();

        $tenants = $tenancies->pluck('tenant')->filter();

        return view('landlord.announcements.index', compact(
            'announcements', 'properties', 'tenants'
        ));
    }

    public function store(Request $request)
    {
        $landlordId = Auth::id();

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'body'        => ['required', 'string', 'max:2000'],
            'audience'    => ['required', 'in:all,property,individual'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'user_id'     => ['nullable', 'exists:users,id'],
        ]);

        // Determine recipients
        $tenancies = Tenancy::with(['tenant'])
            ->where('landlord_id', $landlordId)
            ->where('status', 'active');

        if ($validated['audience'] === 'property' && $validated['property_id']) {
            // Verify property belongs to this landlord
            $property = Property::findOrFail($validated['property_id']);
            if ($property->landlord_id !== $landlordId) {
                abort(403);
            }
            $tenancies->where('property_id', $validated['property_id']);
        } elseif ($validated['audience'] === 'individual' && $validated['user_id']) {
            $tenancies->where('user_id', $validated['user_id']);
        }

        $recipientIds = $tenancies->pluck('user_id')->unique();

        if ($recipientIds->isEmpty()) {
            return back()->withErrors(['title' => 'No recipients match your selection.']);
        }

        // Create the announcement
        $announcement = Announcement::create([
            'landlord_id' => $landlordId,
            'title'       => $validated['title'],
            'body'        => $validated['body'],
            'audience'    => $validated['audience'],
            'property_id' => $validated['property_id'] ?? null,
        ]);

        // Attach recipients
        $announcement->recipients()->attach($recipientIds);

        // Create a notification for each recipient
        foreach ($recipientIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title'   => '📢 ' . $announcement->title,
                'body'    => $announcement->body,
                'icon'    => 'system',
            ]);
        }

        return redirect()
            ->route('landlord.announcements.index')
            ->with('status', 'Announcement sent to ' . $recipientIds->count() . ' tenant(s).');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->landlord_id !== Auth::id()) {
            abort(403);
        }

        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }
}