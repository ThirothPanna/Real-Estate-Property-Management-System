<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvailablePropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_browse_only_available_properties_and_open_details(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);
        $available = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'River View Apartment',
            'address' => '20 River Road',
            'city' => 'Springfield',
            'state' => 'IL',
            'rent_amount' => 1450,
            'description' => 'Bright apartment near the park.',
            'status' => 'available',
        ]);
        $occupied = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Leased Townhouse',
            'address' => '8 Oak Lane',
            'status' => 'occupied',
        ]);
        Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Maintenance Cottage',
            'address' => '7 Hill Street',
            'status' => 'maintenance',
        ]);

        Storage::fake('public');
        Storage::disk('public')->put('properties/river-view.jpg', 'fake image');
        PropertyPhoto::create([
            'property_id' => $available->id,
            'file_path' => 'properties/river-view.jpg',
            'is_cover' => true,
        ]);

        $listingResponse = $this->actingAs($tenant)->get(route('tenant.properties.index'));

        $listingResponse->assertOk();
        $listingResponse->assertSee('River View Apartment');
        $listingResponse->assertSee('/storage/properties/river-view.jpg');
        $listingResponse->assertDontSee('Leased Townhouse');
        $listingResponse->assertDontSee('Maintenance Cottage');

        $detailResponse = $this->get(route('tenant.properties.show', $available));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Bright apartment near the park.');
        $detailResponse->assertSee('title="Map showing River View Apartment"', false);

        $this->get(route('tenant.properties.show', $occupied))->assertNotFound();
    }

    public function test_tenant_can_filter_available_properties_by_location(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);

        Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Downtown Flat',
            'address' => '1 Main Street',
            'city' => 'Springfield',
            'status' => 'available',
        ]);
        Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Lakeside Cabin',
            'address' => '2 Lake Road',
            'city' => 'Shelbyville',
            'status' => 'available',
        ]);

        $response = $this->actingAs($tenant)->get(route('tenant.properties.index', ['search' => 'Springfield']));

        $response->assertOk();
        $response->assertSee('Downtown Flat');
        $response->assertDontSee('Lakeside Cabin');
    }
}