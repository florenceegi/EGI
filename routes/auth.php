<?php

// routes/auth.php - REPLACE Fortify default routes with our independent controller

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
});

// Logout route - OUR independent controller with auth middleware
Route::middleware(['web'])->group(function () {
    Route::post('/custom-logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('custom.logout');

        // niente ->middleware('auth') qui: vogliamo gestire anche sessioni scadute
    Route::post('/logout', [MyAuth::class, 'destroy'])->name('logout');
});