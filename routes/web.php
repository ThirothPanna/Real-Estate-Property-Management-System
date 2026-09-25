<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===========================================
// SOCIAL LOGIN ROUTES
// ===========================================
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->where('provider', 'google|apple|facebook|github|linkedin|twitter|discord|microsoft|gitlab|bitbucket|slack|twitch|tiktok')
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->where('provider', 'google|apple|facebook|github|linkedin|twitter|discord|microsoft|gitlab|bitbucket|slack|twitch|tiktok')
    ->name('social.callback');

require __DIR__.'/auth.php';