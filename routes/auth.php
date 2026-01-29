<?php

// routes/auth.php - REPLACE Fortify default routes with our independent controller

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisterWizardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController as MyAuth;

// Login routes - OUR independent controller with explicit middleware
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:enhanced-registration')
        ->name('register.store');

    // =========================================================================
    // REGISTRATION WIZARD - Multi-step guided registration
    // =========================================================================
    Route::prefix('join')->name('register.wizard.')->group(function () {
        // Step 1: User Type Selection
        Route::get('/', [RegisterWizardController::class, 'step1'])->name('step1');
        Route::post('/step1', [RegisterWizardController::class, 'storeStep1'])->name('step1.store');

        // Step 2: Consents
        Route::get('/consents', [RegisterWizardController::class, 'step2'])->name('step2');
        Route::post('/step2', [RegisterWizardController::class, 'storeStep2'])->name('step2.store');

        // Step 3: Personal Data
        Route::get('/details', [RegisterWizardController::class, 'step3'])->name('step3');
        Route::post('/step3', [RegisterWizardController::class, 'storeStep3'])->name('step3.store');

        // Step 4: Summary & Confirm
        Route::get('/confirm', [RegisterWizardController::class, 'step4'])->name('step4');
        Route::post('/complete', [RegisterWizardController::class, 'complete'])->name('complete');
    });
});

// Logout route - OUR independent controller with auth middleware
Route::middleware(['web'])->group(function () {
    Route::post('/custom-logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('custom.logout');

    // niente ->middleware('auth') qui: vogliamo gestire anche sessioni scadute
    Route::post('/logout', [MyAuth::class, 'destroy'])->name('logout');
});
