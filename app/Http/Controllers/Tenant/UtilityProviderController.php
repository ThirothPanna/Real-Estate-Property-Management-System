<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\UtilityProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UtilityProviderController extends Controller
{
    public function index()
    {
        $providers = UtilityProvider::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tenant.utility-providers.index', compact('providers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['required', Rule::in([
                'electricity', 'water', 'gas', 'internet', 'trash', 'other',
            ])],
            'account_number' => ['nullable', 'string', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['user_id'] = Auth::id();

        UtilityProvider::create($validated);

        return redirect()
            ->route('tenant.utility-providers.index')
            ->with('success', 'Utility provider added.');
    }

    public function update(Request $request, UtilityProvider $utilityProvider)
    {
        abort_unless($utilityProvider->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['required', Rule::in([
                'electricity', 'water', 'gas', 'internet', 'trash', 'other',
            ])],
            'account_number' => ['nullable', 'string', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ]);

        $utilityProvider->update($validated);

        return redirect()
            ->route('tenant.utility-providers.index')
            ->with('success', 'Utility provider updated.');
    }

    public function destroy(UtilityProvider $utilityProvider)
    {
        abort_unless($utilityProvider->user_id === Auth::id(), 403);

        $utilityProvider->delete();

        return redirect()
            ->route('tenant.utility-providers.index')
            ->with('success', 'Utility provider removed.');
    }

}