<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_creates_a_tenant_and_uses_the_tenant_dashboard(): void
    {
        $socialUser = Mockery::mock();
        $socialUser->shouldReceive('getEmail')->once()->andReturn('oauth-tenant@example.com');
        $socialUser->shouldReceive('getName')->once()->andReturn('OAuth Tenant');
        $socialUser->shouldReceive('getNickname')->never();
        $socialUser->shouldReceive('getId')->once()->andReturn('google-account-123');

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->once()->andReturn($socialUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($driver);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('tenant.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'oauth-tenant@example.com',
            'role' => 'tenant',
            'provider' => 'google',
            'provider_id' => 'google-account-123',
        ]);
    }

    public function test_unsupported_social_provider_is_not_routed(): void
    {
        $this->get('/auth/apple/redirect')->assertNotFound();
    }

    public function test_social_login_does_not_take_over_an_existing_password_account(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'landlord',
        ]);
        $socialUser = Mockery::mock();
        $socialUser->shouldReceive('getEmail')->once()->andReturn($existingUser->email);
        $socialUser->shouldReceive('getId')->once()->andReturn('google-account-456');

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->once()->andReturn($socialUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($driver);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'role' => 'landlord',
            'provider_id' => null,
        ]);
    }

    public function test_social_login_redirects_back_with_error_when_provider_is_unconfigured(): void
    {
        config(['services.facebook.client_id' => null, 'services.facebook.client_secret' => null]);

        $response = $this->get(route('social.redirect', 'facebook'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('error');
        $this->get(route('login'))->assertSee('Facebook sign-in is not configured yet.');
    }
}