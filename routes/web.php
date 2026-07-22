<?php

use App\Http\Controllers\Auth\BusinessRegistrationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register/business', [BusinessRegistrationController::class, 'create'])->name('register.business');
    Route::post('/register/business', [BusinessRegistrationController::class, 'store']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PlatformController::class, 'dashboard'])->name('platform.dashboard');
    Route::get('/modules/manage', [PlatformController::class, 'modules'])->name('platform.modules');
    Route::patch('/modules/{slug}/toggle', [PlatformController::class, 'toggleModule'])->name('platform.modules.toggle');

    Route::prefix('business')->name('platform.business.')->group(function () {
        Route::get('/', [BusinessDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [BusinessDashboardController::class, 'users'])->name('users');
    });

    Route::middleware('role:super-admin|administrator')->group(function () {
        Route::get('/admin/email-settings', [EmailSettingsController::class, 'edit'])->name('platform.email-settings');
        Route::put('/admin/email-settings', [EmailSettingsController::class, 'update'])->name('platform.email-settings.update');
        Route::post('/admin/email-settings/test', [EmailSettingsController::class, 'test'])->name('platform.email-settings.test');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::delete('/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

Route::get('/two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
Route::post('/two-factor/challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';
