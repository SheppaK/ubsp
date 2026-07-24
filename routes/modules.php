<?php

use App\Http\Controllers\Modules\BoardingHouse\AnalyticsController as BhAnalyticsController;
use App\Http\Controllers\Modules\BoardingHouse\AvailabilityController as BhAvailabilityController;
use App\Http\Controllers\Modules\BoardingHouse\BookingController as BhBookingController;
use App\Http\Controllers\Modules\BoardingHouse\CompareController as BhCompareController;
use App\Http\Controllers\Modules\BoardingHouse\DashboardController as BhDashboardController;
use App\Http\Controllers\Modules\BoardingHouse\FavoriteController as BhFavoriteController;
use App\Http\Controllers\Modules\BoardingHouse\MessageController as BhMessageController;
use App\Http\Controllers\Modules\BoardingHouse\PaymentController as BhPaymentController;
use App\Http\Controllers\Modules\BoardingHouse\PropertyController as BhPropertyController;
use App\Http\Controllers\Modules\BoardingHouse\ReviewController as BhReviewController;
use App\Http\Controllers\Modules\BoardingHouse\RoomController as BhRoomController;
use App\Http\Controllers\Modules\BoardingHouse\RoommateController as BhRoommateController;
use App\Http\Controllers\Modules\BoardingHouse\SearchController as BhSearchController;
use App\Http\Controllers\Modules\BoardingHouse\TenantController as BhTenantController;
use App\Http\Controllers\Modules\Clinic\AppointmentController as ClAppointmentController;
use App\Http\Controllers\Modules\Clinic\DashboardController as ClDashboardController;
use App\Http\Controllers\Modules\Clinic\PatientPortalController as ClPatientPortalController;
use App\Http\Controllers\Modules\Clinic\ProviderController as ClProviderController;
use App\Http\Controllers\Modules\BalancedScorecardController;
use App\Http\Controllers\Modules\ElectronicsTrackerController;
use App\Http\Controllers\Modules\ExchangeTrackerController;
use App\Http\Controllers\Modules\MarketplaceController;
use App\Http\Controllers\Modules\MonitoringEvaluationController;
use App\Http\Controllers\Modules\SportsLeagueController;
use App\Http\Controllers\Modules\SubscriptionSharingController;
use App\Http\Controllers\Modules\UniversitySocialController;
use App\Http\Controllers\Modules\WeatherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'business.paid'])->prefix('modules')->group(function () {
    Route::middleware('module.enabled:electronics-tracker')
        ->prefix('electronics-tracker')
        ->name('modules.electronics-tracker.')
        ->group(function () {
            Route::get('/', [ElectronicsTrackerController::class, 'dashboard'])->name('dashboard');
            Route::resource('assets', ElectronicsTrackerController::class)->except(['show']);
        });

    Route::middleware('module.enabled:university-social')
        ->prefix('university-social')
        ->name('modules.university-social.')
        ->group(function () {
            Route::get('/', [UniversitySocialController::class, 'dashboard'])->name('dashboard');
            Route::resource('posts', UniversitySocialController::class)->except(['show']);
        });

    Route::middleware('module.enabled:balanced-scorecard')
        ->prefix('balanced-scorecard')
        ->name('modules.balanced-scorecard.')
        ->group(function () {
            Route::get('/', [BalancedScorecardController::class, 'dashboard'])->name('dashboard');
            Route::resource('kpis', BalancedScorecardController::class)->except(['show']);
        });

    Route::middleware('module.enabled:marketplace')
        ->prefix('marketplace')
        ->name('modules.marketplace.')
        ->group(function () {
            Route::get('/', [MarketplaceController::class, 'dashboard'])->name('dashboard');
            Route::resource('products', MarketplaceController::class)->except(['show']);
        });

    Route::middleware('module.enabled:boarding-house')
        ->prefix('boarding-house')
        ->name('modules.boarding-house.')
        ->group(function () {
            Route::get('/', BhDashboardController::class)->name('dashboard');

            // Student / tenant: search & browse
            Route::get('/browse', [BhSearchController::class, 'index'])->name('search.index');
            Route::get('/browse/{property}', [BhSearchController::class, 'show'])->name('search.show');

            // Favorites / wishlist
            Route::get('/wishlist', [BhFavoriteController::class, 'index'])->name('favorites.index');
            Route::post('/browse/{property}/favorite', [BhFavoriteController::class, 'store'])->name('favorites.store');
            Route::delete('/browse/{property}/favorite', [BhFavoriteController::class, 'destroy'])->name('favorites.destroy');

            // Compare properties
            Route::get('/compare', [BhCompareController::class, 'index'])->name('compare.index');
            Route::post('/browse/{property}/compare', [BhCompareController::class, 'store'])->name('compare.store');
            Route::delete('/browse/{property}/compare', [BhCompareController::class, 'destroy'])->name('compare.destroy');
            Route::delete('/compare', [BhCompareController::class, 'clear'])->name('compare.clear');

            // Roommate matching
            Route::get('/roommates', [BhRoommateController::class, 'index'])->name('roommates.index');
            Route::post('/roommates', [BhRoommateController::class, 'store'])->name('roommates.store');
            Route::delete('/roommates/{post}', [BhRoommateController::class, 'destroy'])->name('roommates.destroy');

            // Messaging
            Route::get('/messages', [BhMessageController::class, 'index'])->name('messages.index');
            Route::get('/messages/{conversation}', [BhMessageController::class, 'show'])->name('messages.show');
            Route::post('/messages/{conversation}', [BhMessageController::class, 'store'])->name('messages.store');
            Route::get('/bookings/{booking}/chat', [BhMessageController::class, 'forBooking'])->name('messages.booking');

            // Payments
            Route::get('/payments/{payment}/checkout', [BhPaymentController::class, 'checkout'])->name('payments.checkout');
            Route::get('/payments/{payment}/success', [BhPaymentController::class, 'success'])->name('payments.success');

            // Availability calendar (public view)
            Route::get('/browse/{property}/rooms/{room}/calendar', [BhAvailabilityController::class, 'show'])->name('availability.show');

            // Bookings
            Route::get('/my-bookings', [BhBookingController::class, 'mine'])->name('bookings.mine');
            Route::post('/browse/{property}/rooms/{room}/book', [BhBookingController::class, 'store'])->name('bookings.store');
            Route::post('/browse/{property}/reviews', [BhReviewController::class, 'store'])->name('reviews.store');

            // Landlord / admin: property management
            Route::middleware('boarding-house.manager')->prefix('admin')->name('admin.')->group(function () {
                Route::resource('properties', BhPropertyController::class);
                Route::post('properties/{property}/rooms', [BhRoomController::class, 'store'])->name('properties.rooms.store');
                Route::put('properties/{property}/rooms/{room}', [BhRoomController::class, 'update'])->name('properties.rooms.update');
                Route::delete('properties/{property}/rooms/{room}', [BhRoomController::class, 'destroy'])->name('properties.rooms.destroy');

                Route::get('bookings', [BhBookingController::class, 'manage'])->name('bookings.manage');
                Route::patch('bookings/{booking}/approve', [BhBookingController::class, 'approve'])->name('bookings.approve');
                Route::patch('bookings/{booking}/reject', [BhBookingController::class, 'reject'])->name('bookings.reject');

                Route::get('analytics', BhAnalyticsController::class)->name('analytics');

                Route::get('tenants', [BhTenantController::class, 'index'])->name('tenants.index');
                Route::get('tenants/create', [BhTenantController::class, 'create'])->name('tenants.create');
                Route::post('tenants', [BhTenantController::class, 'store'])->name('tenants.store');

                Route::post('properties/{property}/rooms/{room}/availability', [BhAvailabilityController::class, 'store'])->name('availability.store');
                Route::delete('properties/{property}/rooms/{room}/availability/{block}', [BhAvailabilityController::class, 'destroy'])->name('availability.destroy');
            });
        });

    Route::middleware('module.enabled:exchange-tracker')
        ->prefix('exchange-tracker')
        ->name('modules.exchange-tracker.')
        ->group(function () {
            Route::get('/', [ExchangeTrackerController::class, 'dashboard'])->name('dashboard');
            Route::resource('rates', ExchangeTrackerController::class)
                ->parameters(['rates' => 'rate'])
                ->except(['show']);
        });

    Route::middleware('module.enabled:weather')
        ->prefix('weather')
        ->name('modules.weather.')
        ->group(function () {
            Route::get('/', [WeatherController::class, 'dashboard'])->name('dashboard');
            Route::resource('locations', WeatherController::class)->except(['show']);
        });

    Route::middleware('module.enabled:clinic')
        ->prefix('clinic')
        ->name('modules.clinic.')
        ->group(function () {
            Route::get('/', ClDashboardController::class)->name('dashboard');

            // Patient portal
            Route::get('/health-history', [ClPatientPortalController::class, 'healthHistory'])->name('health-history');
            Route::get('/profile', [ClPatientPortalController::class, 'profile'])->name('profile');
            Route::put('/profile', [ClPatientPortalController::class, 'updateProfile'])->name('profile.update');

            Route::get('/book-appointment', [ClAppointmentController::class, 'create'])->name('appointments.create');
            Route::post('/book-appointment', [ClAppointmentController::class, 'store'])->name('appointments.store');
            Route::get('/my-appointments', [ClAppointmentController::class, 'mine'])->name('appointments.mine');
            Route::patch('/appointments/{appointment}/cancel', [ClAppointmentController::class, 'cancel'])->name('appointments.cancel');

            // Healthcare provider
            Route::middleware('clinic.provider')->prefix('provider')->name('provider.')->group(function () {
                Route::get('/appointments', [ClProviderController::class, 'appointments'])->name('appointments');
                Route::patch('/appointments/{appointment}/confirm', [ClProviderController::class, 'confirm'])->name('appointments.confirm');
                Route::patch('/appointments/{appointment}/reject', [ClProviderController::class, 'reject'])->name('appointments.reject');
                Route::patch('/appointments/{appointment}/complete', [ClProviderController::class, 'complete'])->name('appointments.complete');

                Route::get('/patients', [ClProviderController::class, 'patients'])->name('patients.index');
                Route::get('/patients/{patient}', [ClProviderController::class, 'showPatient'])->name('patients.show');
                Route::post('/patients/{patient}/medical-records', [ClProviderController::class, 'storeMedicalRecord'])->name('patients.medical-records.store');
                Route::post('/patients/{patient}/prescriptions', [ClProviderController::class, 'storePrescription'])->name('patients.prescriptions.store');
                Route::post('/patients/{patient}/lab-results', [ClProviderController::class, 'storeLabResult'])->name('patients.lab-results.store');
            });
        });

    Route::middleware('module.enabled:monitoring-evaluation')
        ->prefix('monitoring-evaluation')
        ->name('modules.monitoring-evaluation.')
        ->group(function () {
            Route::get('/', [MonitoringEvaluationController::class, 'dashboard'])->name('dashboard');
            Route::resource('projects', MonitoringEvaluationController::class)->except(['show']);
        });

    Route::middleware('module.enabled:subscription-sharing')
        ->prefix('subscription-sharing')
        ->name('modules.subscription-sharing.')
        ->group(function () {
            Route::get('/', [SubscriptionSharingController::class, 'dashboard'])->name('dashboard');
            Route::resource('plans', SubscriptionSharingController::class)->except(['show']);
        });

    Route::middleware('module.enabled:sports-league')
        ->prefix('sports-league')
        ->name('modules.sports-league.')
        ->group(function () {
            Route::get('/', [SportsLeagueController::class, 'dashboard'])->name('dashboard');
            Route::resource('leagues', SportsLeagueController::class)->except(['show']);
        });
});
