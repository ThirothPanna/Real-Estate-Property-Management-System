<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Document;
use App\Models\Lease;
use App\Models\LeaseDocument;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_open_owned_notification_and_it_is_marked_read(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $notification = Notification::create([
            'user_id' => $tenant->id,
            'title' => 'Lease shared',
            'body' => 'Your updated lease is ready.',
            'icon' => 'lease',
        ]);

        $response = $this->actingAs($tenant)->get(route('tenant.notifications.show', $notification));

        $response->assertOk();
        $response->assertSee('Your updated lease is ready.');
        $response->assertSee(route('tenant.files'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_tenant_cannot_open_another_users_notification(): void
    {
        $owner = User::factory()->create(['role' => 'tenant']);
        $otherTenant = User::factory()->create(['role' => 'tenant']);
        $notification = Notification::create([
            'user_id' => $owner->id,
            'title' => 'Private update',
            'body' => 'Private message.',
            'icon' => 'system',
        ]);

        $this->actingAs($otherTenant)
            ->get(route('tenant.notifications.show', $notification))
            ->assertForbidden();
    }

    public function test_landlord_can_open_owned_notification(): void
    {
        $landlord = User::factory()->create(['role' => 'landlord']);
        $notification = Notification::create([
            'user_id' => $landlord->id,
            'title' => 'Payment recorded',
            'body' => 'A tenant payment is ready to review.',
            'icon' => 'payment',
        ]);

        $response = $this->actingAs($landlord)->get(route('landlord.notifications.show', $notification));

        $response->assertOk();
        $response->assertSee('A tenant payment is ready to review.');
        $response->assertSee(route('landlord.payments.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_tenant_can_view_and_download_owned_documents_and_receipts(): void
    {
        Storage::fake('public');
        $tenant = User::factory()->create(['role' => 'tenant']);
        $otherTenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Test Rental',
            'address' => '10 Test Street',
        ]);
        $tenancy = Tenancy::create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'lease_start' => '2026-01-01',
            'lease_end' => '2026-12-31',
            'rent_amount' => 1200,
            'status' => 'active',
        ]);

        Storage::disk('public')->put('tenant/document.pdf', "%PDF-1.4\n%%EOF");
        Storage::disk('public')->put('tenant/lease.pdf', "%PDF-1.4\n%%EOF");
        $document = LeaseDocument::create([
            'user_id' => $tenant->id,
            'original_name' => 'my-document.pdf',
            'file_path' => 'tenant/document.pdf',
            'mime_type' => 'application/pdf',
            'size' => 12,
        ]);
        $lease = Lease::create([
            'tenancy_id' => $tenancy->id,
            'landlord_id' => $landlord->id,
            'user_id' => $tenant->id,
            'title' => 'My Lease',
            'file_path' => 'tenant/lease.pdf',
            'original_name' => 'lease.pdf',
            'mime_type' => 'application/pdf',
            'size' => 12,
        ]);
        $payment = Payment::create([
            'user_id' => $tenant->id,
            'amount' => 1200,
            'paid_on' => '2026-09-29',
            'method' => 'card',
            'status' => 'completed',
            'category' => 'Rent',
        ]);

        $this->actingAs($tenant)
            ->get(route('tenant.documents.view', $document))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=my-document.pdf');
        $this->get(route('tenant.documents.download', $document))->assertDownload('my-document.pdf');
        $this->get(route('tenant.leases.view', $lease))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=lease.pdf');
        $this->get(route('tenant.receipts.view', $payment))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($otherTenant)
            ->get(route('tenant.documents.view', $document))
            ->assertForbidden();
    }

    public function test_landlord_can_view_owned_documents_and_leases_but_not_others(): void
    {
        Storage::fake('public');
        $landlord = User::factory()->create(['role' => 'landlord']);
        $otherLandlord = User::factory()->create(['role' => 'landlord']);
        $tenant = User::factory()->create(['role' => 'tenant']);
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Test Rental',
            'address' => '10 Test Street',
        ]);
        $tenancy = Tenancy::create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'lease_start' => '2026-01-01',
            'lease_end' => '2026-12-31',
            'rent_amount' => 1200,
            'status' => 'active',
        ]);
        Storage::disk('public')->put('landlord/document.pdf', "%PDF-1.4\n%%EOF");
        Storage::disk('public')->put('landlord/lease.pdf', "%PDF-1.4\n%%EOF");
        $document = Document::create([
            'landlord_id' => $landlord->id,
            'title' => 'House Rules',
            'file_path' => 'landlord/document.pdf',
            'original_name' => 'rules.pdf',
            'mime_type' => 'application/pdf',
            'size' => 12,
        ]);
        $lease = Lease::create([
            'tenancy_id' => $tenancy->id,
            'landlord_id' => $landlord->id,
            'user_id' => $tenant->id,
            'title' => 'Lease Agreement',
            'file_path' => 'landlord/lease.pdf',
            'original_name' => 'landlord-lease.pdf',
            'mime_type' => 'application/pdf',
            'size' => 12,
        ]);
        $payment = Payment::create([
            'user_id' => $tenant->id,
            'amount' => 1200,
            'paid_on' => '2026-09-29',
            'method' => 'card',
            'status' => 'completed',
            'category' => 'Rent',
        ]);

        $this->actingAs($landlord)
            ->get(route('landlord.documents.view', $document))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=rules.pdf');
        $this->get(route('landlord.leases.view', $lease))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=landlord-lease.pdf');
        $this->get(route('landlord.leases.download', $lease))->assertDownload('landlord-lease.pdf');
        $this->get(route('landlord.payments.receipt.view', $payment))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($otherLandlord)
            ->get(route('landlord.documents.view', $document))
            ->assertForbidden();
        $this->get(route('landlord.payments.receipt.view', $payment))->assertForbidden();
    }

    public function test_tenant_file_manager_lists_only_documents_shared_with_active_tenancies(): void
    {
        Storage::fake('public');
        $tenant = User::factory()->create(['role' => 'tenant']);
        $landlord = User::factory()->create(['role' => 'landlord']);
        $otherLandlord = User::factory()->create(['role' => 'landlord']);
        $property = Property::create([
            'landlord_id' => $landlord->id,
            'name' => 'Tenant Rental',
            'address' => '1 Rental Road',
        ]);
        Tenancy::create([
            'user_id' => $tenant->id,
            'property_id' => $property->id,
            'landlord_id' => $landlord->id,
            'lease_start' => '2026-01-01',
            'lease_end' => '2026-12-31',
            'rent_amount' => 1200,
            'status' => 'active',
        ]);

        $propertyDocument = Document::create([
            'landlord_id' => $landlord->id,
            'property_id' => $property->id,
            'title' => 'Property guide',
            'file_path' => 'property-guide.pdf',
            'original_name' => 'property-guide.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10,
        ]);
        Storage::disk('public')->put('property-guide.pdf', "%PDF-1.4\n%%EOF");
        $landlordDocument = Document::create([
            'landlord_id' => $landlord->id,
            'title' => 'Building handbook',
            'file_path' => 'building-handbook.pdf',
            'original_name' => 'building-handbook.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10,
        ]);
        $otherDocument = Document::create([
            'landlord_id' => $otherLandlord->id,
            'title' => 'Private handbook',
            'file_path' => 'private-handbook.pdf',
            'original_name' => 'private-handbook.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10,
        ]);

        $response = $this->actingAs($tenant)->get(route('tenant.files'));

        $response->assertOk();
        $response->assertSee('Property guide');
        $response->assertSee('Building handbook');
        $response->assertDontSee('Private handbook');
        $this->assertTrue($response->viewData('sharedDocuments')->contains('id', $propertyDocument->id));
        $this->assertTrue($response->viewData('sharedDocuments')->contains('id', $landlordDocument->id));
        $this->assertFalse($response->viewData('sharedDocuments')->contains('id', $otherDocument->id));

        $this->get(route('tenant.shared-documents.view', $propertyDocument))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename=property-guide.pdf');
        $this->get(route('tenant.shared-documents.view', $otherDocument))->assertForbidden();
    }

    public function test_docx_preview_renders_document_text_in_a_sandboxed_viewer(): void
    {
        Storage::fake('public');
        $tenant = User::factory()->create(['role' => 'tenant']);
        $word = new \PhpOffice\PhpWord\PhpWord();
        $word->addSection()->addText('DOCX preview content');
        $temporaryFile = tempnam(sys_get_temp_dir(), 'docx-preview-');
        \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($temporaryFile);
        $contents = file_get_contents($temporaryFile);
        unlink($temporaryFile);
        Storage::disk('public')->put('tenant/preview.docx', $contents);

        $document = LeaseDocument::create([
            'user_id' => $tenant->id,
            'original_name' => 'preview.docx',
            'file_path' => 'tenant/preview.docx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'size' => strlen($contents),
        ]);

        $response = $this->actingAs($tenant)->get(route('tenant.documents.view', $document));

        $response->assertOk();
        $response->assertHeader('Content-Security-Policy');
        $response->assertSee('sandbox=""', false);
        $response->assertSee('DOCX preview content');
        $response->assertSee(route('tenant.documents.download', $document));
    }
}