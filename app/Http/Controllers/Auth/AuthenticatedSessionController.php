<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Send user to their role-specific dashboard
        /** @var \App\Models\User $user */
        $user = $request->user();

        $accountIds = $request->session()->get('account_switch_ids', []);
        $accountIds[] = $user->id;
        $request->session()->put('account_switch_ids', array_values(array_unique($accountIds)));

        return redirect()->intended(
            route($user->dashboardRoute(), absolute: false)
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->endSession($request);

        return redirect('/');
    }

    public function addAccount(Request $request): RedirectResponse
    {
        $accountIds = $request->session()->get('account_switch_ids', []);
        $accountIds[] = $request->user()->id;

        $this->endSession($request);
        $request->session()->put('account_switch_ids', array_values(array_unique($accountIds)));

        return redirect()->route('login');
    }

    private function endSession(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
    public function switchAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $accountIds = collect($request->session()->get('account_switch_ids', []))
            ->map(fn ($id) => (int) $id);

        abort_unless($accountIds->contains($validated['user_id']), 403);

        $targetUser = \App\Models\User::findOrFail($validated['user_id']);

        Auth::login($targetUser);
        $request->session()->regenerate();

        return redirect()->route($targetUser->dashboardRoute());
    }
}