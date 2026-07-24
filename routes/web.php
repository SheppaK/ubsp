<?php

use App\Http\Controllers\Auth\BusinessRegistrationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\KcpayCallbackController;
use App\Http\Controllers\KcpaySettingsController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteMediaController;
use App\Http\Controllers\ThemeSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/api/kcpay/callback', KcpayCallbackController::class)->name('kcpay.callback');

Route::middleware('guest')->group(function () {
    Route::get('/register/business', [BusinessRegistrationController::class, 'create'])->name('register.business');
    Route::post('/register/business', [BusinessRegistrationController::class, 'store'])->name('register.business.store');
});

Route::prefix('register/business')->name('register.business.')->group(function () {
    Route::get('/checkout/{payment}', [BusinessRegistrationController::class, 'checkout'])->name('checkout');
    Route::post('/pay/{payment}', [BusinessRegistrationController::class, 'pay'])->name('pay');
    Route::get('/waiting/{payment}', [BusinessRegistrationController::class, 'waiting'])->name('waiting');
    Route::get('/status/{payment}', [BusinessRegistrationController::class, 'status'])->name('status');
    Route::get('/success/{payment}', [BusinessRegistrationController::class, 'success'])->name('success');
    Route::get('/return/{payment}', [BusinessRegistrationController::class, 'return'])->name('return');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('business.paid')->group(function () {
        Route::get('/dashboard', [PlatformController::class, 'dashboard'])->name('platform.dashboard');
    });
    Route::get('/modules/manage', [PlatformController::class, 'modules'])->name('platform.modules');
    Route::patch('/modules/{slug}/toggle', [PlatformController::class, 'toggleModule'])->name('platform.modules.toggle');
    Route::patch('/modules/{slug}/price', [PlatformController::class, 'updateModulePrice'])->name('platform.modules.price');

    Route::prefix('business')->name('platform.business.')->group(function () {
        Route::get('/payment', [BusinessDashboardController::class, 'payment'])->name('payment');
        Route::middleware('business.paid')->group(function () {
            Route::get('/', [BusinessDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/users', [BusinessDashboardController::class, 'users'])->name('users');
        });
    });

    Route::middleware('role:super-admin')->group(function () {
        Route::get('/admin/deployment', [DeploymentController::class, 'index'])->name('platform.deployment');
        Route::post('/admin/deployment/artisan', [DeploymentController::class, 'artisan'])->name('platform.deployment.artisan');
        Route::post('/admin/deployment/git-pull', [DeploymentController::class, 'gitPull'])->name('platform.deployment.git-pull');
        Route::post('/admin/deployment/git-status', [DeploymentController::class, 'refreshGitStatus'])->name('platform.deployment.git-status');
        Route::post('/admin/deployment/storage-link', [DeploymentController::class, 'linkStorage'])->name('platform.deployment.storage-link');
    });

    Route::middleware('role:super-admin|administrator')->group(function () {
        Route::get('/admin/theme-settings', [ThemeSettingsController::class, 'edit'])->name('platform.theme-settings');
        Route::put('/admin/theme-settings', [ThemeSettingsController::class, 'update'])->name('platform.theme-settings.update');
        Route::post('/admin/theme-settings/reset', [ThemeSettingsController::class, 'reset'])->name('platform.theme-settings.reset');
        Route::get('/admin/site-media', [SiteMediaController::class, 'edit'])->name('platform.site-media');
        Route::put('/admin/site-media', [SiteMediaController::class, 'update'])->name('platform.site-media.update');
        Route::post('/admin/site-media/reset', [SiteMediaController::class, 'reset'])->name('platform.site-media.reset');
        Route::get('/admin/email-settings', [EmailSettingsController::class, 'edit'])->name('platform.email-settings');
        Route::put('/admin/email-settings', [EmailSettingsController::class, 'update'])->name('platform.email-settings.update');
        Route::post('/admin/email-settings/test', [EmailSettingsController::class, 'test'])->name('platform.email-settings.test');
        Route::get('/admin/kcpay-settings', [KcpaySettingsController::class, 'edit'])->name('platform.kcpay-settings');
        Route::put('/admin/kcpay-settings', [KcpaySettingsController::class, 'update'])->name('platform.kcpay-settings.update');
        Route::post('/admin/kcpay-settings/test', [KcpaySettingsController::class, 'testConnection'])->name('platform.kcpay-settings.test');
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
