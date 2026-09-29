<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\UtilityRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilityRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_provider_directory_still_renders(): void
    {
        /** @var User $tenant */
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant)
            ->get(route('tenant.utility-providers.index'))
            ->assertOk()
            ->assertSee('Utility Providers');
    }

    public function test_tenant_can_submit_request_and_landlord_can_approve_it(): void
    {
        /** @var User $landlord */
        $landlord = User::factory()->create(['role' => 'landlord']);
        /** @var User $tenant */
        $tenant = User::factory()->create(['role' => 'tenant']);
        $tenancy = $this->createTenancy($tenant, $landlord, 'Maple Apartments');

        $this->actingAs($tenant)
            ->post(route('tenant.utility-requests.store'), [
                'tenancy_id' => $tenancy->id,
                'utility_type' => 'water',
                'provider_name' => 'City Water',
                'message' => 'Please approve a water connection.',
            ])
            ->assertRedirect(route('tenant.utility-requests.index'));

        $utilityRequest = UtilityRequest::firstOrFail();
        $this->assertSame('pending', $utilityRequest->status);
        $this->assertSame($tenancy->id, $utilityRequest->tenancy_id);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $landlord->id,
            'title' => 'Utility request received',
        ]);

        $this->actingAs($landlord)
            ->get(route('landlord.utility-requests.index'))
            ->assertOk()
            ->assertSee('City Water')
            ->assertSee($tenant->name);

        $this->patch(route('landlord.utility-requests.update', $utilityRequest), [
            'status' => 'approved',
            'landlord_response' => 'Approved. Please coordinate installation.',
        ])->assertRedirect(route('landlord.utility-requests.index'));

        $this->assertDatabaseHas('utility_requests', [
            'id' => $utilityRequest->id,
            'status' => 'approved',
            'landlord_response' => 'Approved. Please coordinate installation.',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $tenant->id,
            'title' => 'Utility request approved',
        ]);
    }

    public function test_landlord_cannot_review_another_landlords_tenant_request(): void
    {
        /** @var User $landlord */
        $landlord = User::factory()->create(['role' => 'landlord']);
        $otherLandlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'tenant']);
        $tenancy = $this->createTenancy($tenant, $otherLandlord, 'Pine Street House');
        $utilityRequest = UtilityRequest::create([
            'tenancy_id' => $tenancy->id,
            'utility_type' => 'electricity',
            'message' => 'Request an electricity connection.',
        ]);

        $this->actingAs($landlord)
            ->patch(route('landlord.utility-requests.update', $utilityRequest), ['status' => 'approved'])
            ->assertForbidden();

        $this->assertDatabaseHas('utility_requests', [
            'id' => $utilityRequest->id,
            'status' => 'pending',
        ]);
    }

    private function createTenancy(User $tenant, User $landlord, string $propertyName): Tenancy
    {
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => $propertyName,
            'address' => '10 Main Street',
        ]);

        return Tenancy::create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'lease_start' => now()->toDateString(),
            'lease_end' => now()->addYear()->toDateString(),
            'rent_amount' => 1500,
            'status' => 'active',
        ]);
    }
}