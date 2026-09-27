<?php

use App\Http\Controllers\Landlord\AnnouncementController;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboard;
use App\Http\Controllers\Landlord\DocumentController;
use App\Http\Controllers\Landlord\LeaseController as LandlordLeaseController;
use App\Http\Controllers\Landlord\NotificationController as LandlordNotificationController;
use App\Http\Controllers\Landlord\PaymentController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\PropertyPhotoController;
use App\Http\Controllers\Landlord\ReportController;
use App\Http\Controllers\Landlord\RequestController as LandlordRequestController;
use App\Http\Controllers\Landlord\SettingsController as LandlordSettingsController;
use App\Http\Controllers\Landlord\TenantController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboard;
use App\Http\Controllers\Tenant\LeaseController as TenantLeaseController;
use App\Http\Controllers\Tenant\LeaseDocumentController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\ReceiptController;
use App\Http\Controllers\Tenant\RequestController as TenantRequestController;
use App\Http\Controllers\Tenant\SettingsController as TenantSettingsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return redirect()->route($user->dashboardRoute());
    }
    return view('landing');
});

Route::get('/auth/{provider}/redirect', function ($provider) {
    return redirect()->route('login');
})->name('social.redirect');

Route::get('/auth/{provider}/callback', function ($provider) {
    return redirect()->route('login');
})->name('social.callback');

/* ================= TENANT AREA ================= */
Route::middleware(['auth', 'role:tenant'])
    ->prefix('tenant')
    ->name('tenant.')
    ->group(function () {

    // Sidebar pages
    Route::get('/dashboard', [TenantDashboard::class, 'index'])->name('dashboard');
    Route::get('/rent', [TenantDashboard::class, 'rent'])->name('rent');
    Route::get('/requests', [TenantDashboard::class, 'requests'])->name('requests');
    Route::get('/utilities', [TenantDashboard::class, 'utilities'])->name('utilities');
    Route::get('/applications', [TenantDashboard::class, 'applications'])->name('applications');
    Route::get('/files', [TenantDashboard::class, 'files'])->name('files');
    Route::get('/downloads', [TenantDashboard::class, 'downloads'])->name('downloads');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read',   [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/{notification}/unread', [NotificationController::class, 'markUnread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/mark-all-read',         [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-all-unread',       [NotificationController::class, 'markAllUnread'])->name('notifications.markAllUnread');
    Route::post('/notifications/delete-all',            [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    // Lease documents (uploaded by tenant)
    Route::post('/documents', [LeaseDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [LeaseDocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [LeaseDocumentController::class, 'destroy'])->name('documents.destroy');

    // Profile
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings', [TenantSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile', [TenantSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [TenantSettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/avatar', [TenantSettingsController::class, 'updateAvatar'])->name('settings.avatar');

    // Maintenance requests (tenant — submit)
    Route::post('/requests', [TenantRequestController::class, 'store'])->name('requests.store');

    // Payments
    Route::get('/pay', [TenantPaymentController::class, 'create'])->name('pay');
    Route::post('/payments', [TenantPaymentController::class, 'store'])->name('payments.store');

    // Receipts
    Route::get('/receipts/{payment}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    // Leases
    Route::get('/leases/{lease}/download', [TenantLeaseController::class, 'download'])->name('leases.download');
    Route::post('/leases/{lease}/acknowledge', [TenantLeaseController::class, 'acknowledge'])->name('leases.acknowledge');
});

/* ================= LANDLORD AREA ================= */
Route::middleware(['auth', 'role:landlord'])
    ->prefix('landlord')
    ->name('landlord.')
    ->group(function () {

    Route::get('/dashboard', [LandlordDashboard::class, 'index'])->name('dashboard');

    // Properties
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::patch('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    // Photo management
    Route::delete('/properties/photos/{photo}', [PropertyPhotoController::class, 'destroy'])->name('properties.photos.destroy');
    Route::patch('/properties/photos/{photo}/cover', [PropertyPhotoController::class, 'setCover'])->name('properties.photos.cover');

    // Tenants
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/invite', [TenantController::class, 'invite'])->name('tenants.invite');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenancy}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenancy}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::patch('/tenants/{tenancy}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenancy}', [TenantController::class, 'destroy'])->name('tenants.destroy');

    // Maintenance requests
    Route::get('/requests', [LandlordRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{maintenanceRequest}', [LandlordRequestController::class, 'updateStatus'])->name('requests.updateStatus');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Settings
    Route::get('/settings', [LandlordSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile', [LandlordSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [LandlordSettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/avatar', [LandlordSettingsController::class, 'updateAvatar'])->name('settings.avatar');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Documents
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Leases
    Route::get('/leases', [LandlordLeaseController::class, 'index'])->name('leases.index');
    Route::post('/leases', [LandlordLeaseController::class, 'store'])->name('leases.store');
    Route::delete('/leases/{lease}', [LandlordLeaseController::class, 'destroy'])->name('leases.destroy');

    // Notifications ← NEW
    Route::get('/notifications', [LandlordNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read',   [LandlordNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/{notification}/unread', [LandlordNotificationController::class, 'markUnread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/delete', [LandlordNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/mark-all-read',         [LandlordNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-all-unread',       [LandlordNotificationController::class, 'markAllUnread'])->name('notifications.markAllUnread');
    Route::post('/notifications/delete-all',            [LandlordNotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});

require __DIR__.'/auth.php';