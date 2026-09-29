<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\Payment;
use App\Models\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_payment_is_persisted(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Tenant Rental',
            'address' => '10 Rental Road',
        ]);
        Tenancy::create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'lease_start' => now()->startOfYear()->toDateString(),
            'lease_end' => now()->addYear()->toDateString(),
            'rent_amount' => 850,
            'status' => 'active',
        ]);
        $paidOn = now()->toDateString();

        $response = $this->actingAs($tenant)->post(route('tenant.payments.store'), [
            'amount' => '850.00',
            'paid_on' => $paidOn,
            'method' => 'card',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => '12/30',
            'card_cvc' => '123',
            'card_name' => $tenant->name,
        ]);

        $response->assertRedirect(route('tenant.dashboard'));
        $response->assertSessionHas('payment_success', '850.00');
        $this->assertDatabaseHas('payments', [
            'user_id' => $tenant->id,
            'amount' => 850,
            'paid_on' => $paidOn . ' 00:00:00',
            'status' => 'completed',
            'category' => 'Rent',
            'card_last4' => '4242',
            'card_expiry' => '12/30',
            'card_name' => $tenant->name,
        ]);
        $this->assertDatabaseHas('notifications', ['user_id' => $tenant->id, 'title' => 'Payment received']);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $landlord->id,
            'title' => 'Tenant rent payment received',
        ]);

        $paymentId = Payment::where('user_id', $tenant->id)->value('id');
        $this->actingAs($tenant)->get(route('tenant.rent'))
            ->assertViewHas('totalPaid', 850)
            ->assertViewHas('thisYearTotal', 850);
        $tenantDashboard = $this->actingAs($tenant)->get(route('tenant.dashboard'));
        $tenantDashboard->assertSee('Recent Transactions');
        $tenantDashboard->assertSee('$850.00');
        $tenantDashboard->assertSee(route('tenant.rent'));
        $tenantDashboard->assertSee('View all transactions');
        $tenantDashboard
            ->assertViewHas('completedPayments', fn ($payments) => $payments->count() === 1);

        $landlordDashboard = $this->actingAs($landlord)->get(route('landlord.dashboard'));
        $landlordDashboard->assertSee('Recent Payments');
        $landlordDashboard->assertSee('Tenant Rental');
        $landlordDashboard->assertSee('$850.00');
        $landlordDashboard->assertSee(route('landlord.payments.index'));
        $landlordDashboard->assertSee('View all transactions');
        $landlordDashboard->assertViewHas('recentPayments', fn ($payments) => $payments->contains('id', $paymentId));

        $this->actingAs($landlord)->get(route('landlord.payments.index'))
            ->assertViewHas('payments', fn ($payments) => $payments->getCollection()->contains('id', $paymentId));
        $this->actingAs($landlord)->get(route('landlord.notifications'))
            ->assertSee('Tenant rent payment received')
            ->assertSee('Tenant Rental');
    }

    public function test_payment_page_uses_previous_card_details_form(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)->get(route('tenant.pay'));

        $response->assertOk();
        $response->assertSee('name="card_number"', false);
        $response->assertSee('name="card_expiry"', false);
        $response->assertSee('name="card_cvc"', false);
    }

    public function test_rent_page_shows_only_the_tenants_active_properties(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $otherTenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);

        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Maple Court Unit 4',
            'address' => '123 Maple Street',
            'city' => 'Springfield',
            'state' => 'IL',
            'zip' => '62701',
        ]);
        Storage::fake('public');
        Storage::disk('public')->put('properties/maple.jpg', 'fake image');
        PropertyPhoto::create([
            'property_id' => $property->id,
            'file_path' => 'properties/maple.jpg',
            'is_cover' => true,
        ]);
        $otherProperty = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Cedar House',
            'address' => '456 Cedar Avenue',
        ]);

        foreach ([[$tenant, $property], [$otherTenant, $otherProperty]] as [$renter, $rentalProperty]) {
            Tenancy::create([
                'user_id' => $renter->id,
                'property_id' => $rentalProperty->id,
                'landlord_id' => $landlord->id,
                'lease_start' => '2026-01-01',
                'lease_end' => '2026-12-31',
                'rent_amount' => '1200.00',
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($tenant)->get(route('tenant.rent'));

        $response->assertOk();
        $response->assertSee('Maple Court Unit 4');
        $response->assertSee('123 Maple Street, Springfield, IL, 62701, USA');
        $response->assertSee('/storage/properties/maple.jpg');
        $response->assertSee('Open in Google Maps');
        $response->assertSee('title="Map showing Maple Court Unit 4"', false);
        $response->assertDontSee('Cedar House');

        $this->actingAs($tenant)->get(route('tenant.pay'))
            ->assertViewHas('defaultAmount', 1200);
    }


    public function test_legacy_utilities_page_uses_the_provider_manager(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)->get(route('tenant.utilities'));

        $response->assertOk();
        $response->assertSee('Add a provider');
        $response->assertDontSee('Coming soon');
    }

    public function test_file_manager_page_has_no_merge_conflict_content(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)->get(route('tenant.files'));

        $response->assertOk();
        $response->assertSee('Upload a document');
        $response->assertDontSee('<<<<<<< HEAD');
        $response->assertDontSee('Coming soon');
    }

    public function test_tenant_can_register_interest_in_rent_reporting_once(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $response = $this->actingAs($tenant)->post(route('tenant.rent-reporting.store'));

        $response->assertRedirect(route('tenant.dashboard'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('rent_reporting_interests', ['user_id' => $tenant->id]);

        $this->actingAs($tenant)->post(route('tenant.rent-reporting.store'));
        $this->assertDatabaseCount('rent_reporting_interests', 1);
    }
}