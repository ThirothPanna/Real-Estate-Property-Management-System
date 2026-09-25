<?php

use App\Http\Controllers\Tenant\DashboardController;
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
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');

    Route::get('/pay', [PaymentController::class, 'create'])->name('pay');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
});

require __DIR__.'/auth.php';