<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * Redirect to the provider's OAuth page.
     */
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        if (blank(config("services.{$provider}.client_id"))
            || blank(config("services.{$provider}.client_secret"))
            || blank(config("services.{$provider}.redirect"))) {
            return redirect()->route('login')->withErrors([
                'error' => ucfirst($provider) . ' sign-in is not configured yet.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the provider.
     */
    public function callback(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
            $email = $socialUser->getEmail();
            $providerId = $socialUser->getId();

            if (! $email || ! $providerId) {
                return redirect()->route('login')->withErrors([
                    'error' => 'The provider did not return a verified account identity. Use email and password to sign in.',
                ]);
            }

            $user = User::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if (! $user) {
                if (User::where('email', $email)->exists()) {
                    return redirect()->route('login')->withErrors([
                        'error' => 'This email already has an account. Continue with email and password.',
                    ]);
                }

                $user = new User([
                    'email' => $email,
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Tenant',
                    'role' => 'tenant',
                    'password' => Hash::make(Str::random(64)),
                ]);
            }

            $user->provider = $provider;
            $user->provider_id = $providerId;
            $user->save();

            Auth::login($user, true);

            return redirect()->route($user->dashboardRoute());
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'error' => 'Social sign-in failed. Check the provider configuration and try again.',
            ]);
        }
    }
}