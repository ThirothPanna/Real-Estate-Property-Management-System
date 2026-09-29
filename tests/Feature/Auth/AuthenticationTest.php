<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_users_can_add_another_account(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $anotherUser = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($user)->post(route('account.add'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $anotherUser->email,
            'password' => 'password',
        ])->assertRedirect(route('tenant.dashboard', absolute: false));

        $this->assertAuthenticatedAs($anotherUser);
        $this->assertSame(
            [$user->id, $anotherUser->id],
            session('account_switch_ids')
        );

        $this->get(route('tenant.dashboard'))
            ->assertOk()
            ->assertSee('Switch account')
            ->assertSee($user->email)
            ->assertSee($anotherUser->email)
            ->assertSee('Active');

        $this->post(route('switch-account'), ['user_id' => $user->id])
            ->assertRedirect(route('tenant.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->get(route('tenant.dashboard'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee($anotherUser->email)
            ->assertSee('Active');
    }

    public function test_users_cannot_switch_to_an_unlinked_account(): void
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $unlinkedUser = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($user)
            ->post(route('switch-account'), ['user_id' => $unlinkedUser->id])
            ->assertForbidden();

        $this->assertAuthenticatedAs($user);
    }
}
