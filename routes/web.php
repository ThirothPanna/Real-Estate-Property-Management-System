<?php

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\PaymentController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\RequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect('/tenant/dashboard')
        : redirect('/login');
});

Route::get('/auth/{provider}/redirect', function ($provider) {
    return redirect()->route('login');
})->name('social.redirect');

Route::get('/auth/{provider}/callback', function ($provider) {
    return redirect()->route('login');
})->name('social.callback');

Route::middleware(['auth'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read',   [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/{notification}/unread', [NotificationController::class, 'markUnread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/mark-all-read',         [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-all-unread',       [NotificationController::class, 'markAllUnread'])->name('notifications.markAllUnread');
    Route::post('/notifications/delete-all',            [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/pay', [PaymentController::class, 'create'])->name('pay');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
});

require __DIR__.'/auth.php';