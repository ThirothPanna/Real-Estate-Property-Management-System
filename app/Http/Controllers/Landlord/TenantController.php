<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenancies = Tenancy::with(['tenant', 'property'])
            ->where('landlord_id', Auth::id())
            ->latest()
            ->paginate(15);

        $activeCount = Tenancy::where('landlord_id', Auth::id())->where('status', 'active')->count();

        return view('landlord.tenants.index', compact('tenancies', 'activeCount'));
    }

    public function invite()
    {
        $properties = Property::where('landlord_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('landlord.tenants.invite', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'             => ['required', 'email', 'max:255'],
            'property_id'       => ['required', 'exists:properties,id'],
            'lease_start'       => ['required', 'date'],
            'lease_end'         => ['required', 'date', 'after:lease_start'],
            'rent_amount'       => ['required', 'numeric', 'min:0'],
            'security_deposit'  => ['nullable', 'numeric', 'min:0'],
        ]);

        // Make sure the property belongs to this landlord
        $property = Property::findOrFail($validated['property_id']);
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }

        // Find or create the tenant user
        $tenant = User::where('email', $validated['email'])->first();
        $tempPassword = null;

        if ($tenant) {
            // Existing user — make sure they're a tenant
            if ($tenant->role !== 'tenant') {
                return back()->withErrors(['email' => 'This email belongs to a landlord account.']);
            }
        } else {
            // New user — create with a temp password
            $tempPassword = 'Temp-' . Str::random(8);
            $tenant = User::create([
                'name'     => explode('@', $validated['email'])[0],
                'email'    => $validated['email'],
                'role'     => 'tenant',
                'password' => Hash::make($tempPassword),
            ]);
        }

        // Check for an existing active tenancy for this tenant
        $existing = Tenancy::where('user_id', $tenant->id)->where('status', 'active')->first();
        if ($existing) {
            return back()->withErrors(['email' => 'This tenant already has an active tenancy.']);
        }

        // Create the tenancy
        $tenancy = Tenancy::create([
            'user_id'          => $tenant->id,
            'property_id'      => $property->id,
            'landlord_id'      => Auth::id(),
            'lease_start'      => $validated['lease_start'],
            'lease_end'        => $validated['lease_end'],
            'rent_amount'      => $validated['rent_amount'],
            'security_deposit' => $validated['security_deposit'] ?? null,
            'status'           => 'active',
        ]);

        // Mark property as occupied
        $property->update(['status' => 'occupied']);

        // Notify the tenant
        Notification::create([
            'user_id' => $tenant->id,
            'title'   => 'You have a new tenancy',
            'body'    => 'You have been added to ' . $property->name . '.',
            'icon'    => 'lease',
        ]);

        // Notify the landlord
        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Tenant added',
            'body'    => $tenant->name . ' is now a tenant at ' . $property->name . '.',
            'icon'    => 'system',
        ]);

        // If we created a new account, show the temp password
        if ($tempPassword) {
            return redirect()
                ->route('landlord.tenants.show', $tenancy)
                ->with('status', 'Tenant invited successfully.')
                ->with('temp_password', $tempPassword);
        }

        return redirect()
            ->route('landlord.tenants.show', $tenancy)
            ->with('status', 'Tenant added successfully.');
    }

    public function show(Tenancy $tenancy)
    {
        $this->checkOwnership($tenancy);
        $tenancy->load(['tenant', 'property']);

        $recentPayments = $tenancy->tenant
            ? \App\Models\Payment::where('user_id', $tenancy->tenant->id)
                ->latest('paid_on')->take(5)->get()
            : collect();

        $recentRequests = $tenancy->tenant
            ? \App\Models\MaintenanceRequest::where('user_id', $tenancy->tenant->id)
                ->latest()->take(5)->get()
            : collect();

        return view('landlord.tenants.show', compact('tenancy', 'recentPayments', 'recentRequests'));
    }

    public function edit(Tenancy $tenancy)
    {
        $this->checkOwnership($tenancy);
        $tenancy->load(['tenant', 'property']);

        $properties = Property::where('landlord_id', Auth::id())->orderBy('name')->get();

        return view('landlord.tenants.edit', compact('tenancy', 'properties'));
    }

    public function update(Request $request, Tenancy $tenancy)
    {
        $this->checkOwnership($tenancy);

        $validated = $request->validate([
            'lease_start'      => ['required', 'date'],
            'lease_end'        => ['required', 'date', 'after:lease_start'],
            'rent_amount'      => ['required', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'status'           => ['required', 'in:pending,active,ended'],
        ]);

        $tenancy->update($validated);

        // Sync property status
        if ($validated['status'] === 'ended') {
            $tenancy->property->update(['status' => 'available']);
        } else {
            $tenancy->property->update(['status' => 'occupied']);
        }

        return redirect()
            ->route('landlord.tenants.show', $tenancy)
            ->with('status', 'Tenancy updated.');
    }

    public function destroy(Tenancy $tenancy)
    {
        $this->checkOwnership($tenancy);

        // End the tenancy (keep history)
        $tenancy->update(['status' => 'ended']);
        $tenancy->property->update(['status' => 'available']);

        Notification::create([
            'user_id' => $tenancy->tenant_id,
            'title'   => 'Tenancy ended',
            'body'    => 'Your tenancy at ' . $tenancy->property->name . ' has been ended.',
            'icon'    => 'system',
        ]);

        return redirect()
            ->route('landlord.tenants.index')
            ->with('status', 'Tenancy ended successfully.');
    }

    private function checkOwnership(Tenancy $tenancy): void
    {
        if ($tenancy->landlord_id !== Auth::id()) {
            abort(403);
        }
    }
}