<?php

use App\Http\Controllers\Superadmin\SuperadminDashboardController;
use App\Http\Controllers\Superadmin\SuperadminAiConsultationsController;
use App\Http\Controllers\Superadmin\SuperadminAiCreditsController;
use App\Http\Controllers\Superadmin\SuperadminAiFeaturesController;
use App\Http\Controllers\Superadmin\SuperadminAiStatisticsController;
use App\Http\Controllers\Superadmin\SuperadminEgiliController;
use App\Http\Controllers\Superadmin\SuperadminEquilibriumController;
use Illuminate\Support\Facades\Route;

/**
 * @Oracode Routes: SuperAdmin Management
 * 🎯 Purpose: AI Consultations, Credits, Tokenomics, Platform Management
 * 🔐 Security: Protected by 'superadmin' middleware
 *
 * @package Routes
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */

Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    // ═══════════════════════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════════════════════
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])
        ->name('dashboard');

    // ═══════════════════════════════════════════════════════════════
    // AI MANAGEMENT
    // ═══════════════════════════════════════════════════════════════
    Route::prefix('ai')->name('ai.')->group(function () {

        // AI Consultations Management
        Route::prefix('consultations')->name('consultations.')->group(function () {
            Route::get('/', [SuperadminAiConsultationsController::class, 'index'])->name('index');
            Route::get('/{generation}', [SuperadminAiConsultationsController::class, 'show'])->name('show');
            Route::get('/egi/{egi}', [SuperadminAiConsultationsController::class, 'byEgi'])->name('by-egi');
            Route::get('/user/{user}', [SuperadminAiConsultationsController::class, 'byUser'])->name('by-user');
            Route::get('/analytics', [SuperadminAiConsultationsController::class, 'analytics'])->name('analytics');
        });

        // AI Credits Management
        Route::prefix('credits')->name('credits.')->group(function () {
            Route::get('/', [SuperadminAiCreditsController::class, 'index'])->name('index');
            Route::post('/assign', [SuperadminAiCreditsController::class, 'assign'])->name('assign');
            Route::get('/transactions', [SuperadminAiCreditsController::class, 'transactions'])->name('transactions');
            Route::get('/packages', [SuperadminAiCreditsController::class, 'packages'])->name('packages');
        });

        // AI Features Configuration
        Route::prefix('features')->name('features.')->group(function () {
            Route::get('/', [SuperadminAiFeaturesController::class, 'index'])->name('index');
            Route::post('/toggle', [SuperadminAiFeaturesController::class, 'toggle'])->name('toggle');
            Route::post('/limits', [SuperadminAiFeaturesController::class, 'updateLimits'])->name('update-limits');
        });

        // AI Statistics
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/', [SuperadminAiStatisticsController::class, 'index'])->name('index');
            Route::get('/usage', [SuperadminAiStatisticsController::class, 'usage'])->name('usage');
            Route::get('/performance', [SuperadminAiStatisticsController::class, 'performance'])->name('performance');
        });
    });

    // ═══════════════════════════════════════════════════════════════
    // TOKENOMICS MANAGEMENT
    // ═══════════════════════════════════════════════════════════════

    // Egili Management
    Route::prefix('egili')->name('egili.')->group(function () {
        Route::get('/', [SuperadminEgiliController::class, 'index'])->name('index');
        Route::get('/transactions', [SuperadminEgiliController::class, 'transactions'])->name('transactions');
        Route::get('/analytics', [SuperadminEgiliController::class, 'analytics'])->name('analytics');
        Route::post('/mint', [SuperadminEgiliController::class, 'mint'])->name('mint');
        Route::post('/burn', [SuperadminEgiliController::class, 'burn'])->name('burn');
    });

    // Equilibrium Management
    Route::prefix('equilibrium')->name('equilibrium.')->group(function () {
        Route::get('/', [SuperadminEquilibriumController::class, 'index'])->name('index');
        Route::get('/{equilibrium}', [SuperadminEquilibriumController::class, 'show'])->name('show');
        Route::get('/analytics', [SuperadminEquilibriumController::class, 'analytics'])->name('analytics');
    });
});


