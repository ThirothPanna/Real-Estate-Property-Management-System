<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Notification;
use App\Models\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaseController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        $leases = Lease::with(['tenant', 'tenancy.property'])
            ->where('landlord_id', $landlordId)
            ->latest()
            ->paginate(15);

        $tenancies = Tenancy::with(['tenant', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->get();

        return view('landlord.leases.index', compact('leases', 'tenancies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenancy_id' => ['required', 'exists:tenancies,id'],
            'title'      => ['required', 'string', 'max:255'],
            'notes'      => ['nullable', 'string', 'max:2000'],
            'lease_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ]);

        $tenancy = Tenancy::with('property')->findOrFail($validated['tenancy_id']);

        if ($tenancy->landlord_id !== Auth::id()) {
            abort(403);
        }

        $file = $request->file('lease_file');
        $path = $file->store('leases/' . Auth::id(), 'public');

        $lease = Lease::create([
            'tenancy_id'    => $tenancy->id,
            'landlord_id'   => Auth::id(),
            'user_id'       => $tenancy->user_id,
            'title'         => $validated['title'],
            'notes'         => $validated['notes'] ?? null,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
        ]);

        Notification::create([
            'user_id' => $tenancy->user_id,
            'title'   => '📄 New lease shared',
            'body'    => $validated['title'] . ' is now available in your dashboard.',
            'icon'    => 'lease',
        ]);

        return redirect()
            ->route('landlord.leases.index')
            ->with('status', 'Lease shared successfully.');
    }

    public function destroy(Lease $lease)
    {
        if ($lease->landlord_id !== Auth::id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($lease->file_path)) {
            Storage::disk('public')->delete($lease->file_path);
        }

        $lease->delete();

        return back()->with('status', 'Lease deleted.');
    }
}