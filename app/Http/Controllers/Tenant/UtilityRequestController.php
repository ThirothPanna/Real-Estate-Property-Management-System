<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Tenancy;
use App\Models\UtilityRequest as TenantUtilityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UtilityRequestController extends Controller
{
    public function index()
    {
        $tenancies = Tenancy::with('property')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        $requests = TenantUtilityRequest::with('tenancy.property')
            ->whereHas('tenancy', fn ($query) => $query->where('user_id', Auth::id()))
            ->latest()
            ->get();

        return view('tenant.utility-requests.index', compact('tenancies', 'requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenancy_id' => [
                'required',
                Rule::exists('tenancies', 'id')->where(fn ($query) => $query
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')),
            ],
            'utility_type' => ['required', Rule::in(['electricity', 'water', 'gas', 'internet', 'trash', 'other'])],
            'provider_name' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $tenancy = Tenancy::with('property')
            ->whereKey($validated['tenancy_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        TenantUtilityRequest::create([
            'tenancy_id' => $tenancy->id,
            'utility_type' => $validated['utility_type'],
            'provider_name' => $validated['provider_name'] ?? null,
            'message' => $validated['message'],
        ]);

        Notification::create([
            'user_id' => $tenancy->landlord_id,
            'title' => 'Utility request received',
            'body' => 'A tenant submitted a ' . $validated['utility_type'] . ' request for ' . $tenancy->property->name . '.',
            'icon' => 'request',
        ]);

        return redirect()
            ->route('tenant.utility-requests.index')
            ->with('success', 'Utility request sent to your landlord.');
    }
}