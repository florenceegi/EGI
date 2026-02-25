<?php

use App\Http\Controllers\Superadmin\SuperadminDashboardController;
use App\Http\Controllers\Superadmin\SuperadminAiConsultationsController;
use App\Http\Controllers\Superadmin\SuperadminAiCreditsController;
use App\Http\Controllers\Superadmin\SuperadminAiFeaturesController;
use App\Http\Controllers\Superadmin\SuperadminAiStatisticsController;
use App\Http\Controllers\Superadmin\SuperadminEgiliController;
use App\Http\Controllers\Superadmin\SuperadminEquilibriumController;
use App\Http\Controllers\Superadmin\SuperadminNatanConfigController;
use App\Http\Controllers\Superadmin\PadminController;
use App\Http\Controllers\Superadmin\SuperadminFeaturePricingController;
use App\Http\Controllers\Superadmin\SuperadminPlatformSettingsController;
use App\Http\Controllers\Superadmin\SuperadminRolesController;
use App\Http\Controllers\Superadmin\MigrationOrchestratorController;
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
    // PADMIN ANALYZER (OS3 Guardian)
    // ═══════════════════════════════════════════════════════════════
    Route::prefix('padmin')->name('padmin.')->group(function () {
        Route::get('/', [PadminController::class, 'dashboard'])->name('dashboard');
        Route::get('/violations', [PadminController::class, 'violations'])->name('violations');
        Route::get('/symbols', [PadminController::class, 'symbols'])->name('symbols');
        Route::get('/search', [PadminController::class, 'search'])->name('search');
        Route::get('/statistics', [PadminController::class, 'statistics'])->name('statistics');

        // API Endpoints
        Route::post('/violations/{violationId}/fix', [PadminController::class, 'markViolationFixed'])->name('violations.fix');
        Route::post('/scan/run', [PadminController::class, 'runScan'])->name('scan.run');
        Route::post('/violations/{violationId}/ai-fix', [PadminController::class, 'requestAiFix'])->name('violations.ai-fix');

        // AI Auto-Fix Endpoints
        Route::post('/violations/{id}/ai-preview', [PadminController::class, 'previewAiFix'])->name('violations.ai-preview');
        Route::post('/violations/{id}/ai-apply', [PadminController::class, 'applyAiFix'])->name('violations.ai-apply');
    });

    // ═══════════════════════════════════════════════════════════════
    // NATAN AI CONFIGURATION
    // ═══════════════════════════════════════════════════════════════
    Route::prefix('natan')->name('natan.')->group(function () {
        Route::get('/config', [SuperadminNatanConfigController::class, 'index'])->name('config');
        Route::post('/config', [SuperadminNatanConfigController::class, 'update'])->name('config.update');
        Route::post('/config/reset', [SuperadminNatanConfigController::class, 'reset'])->name('config.reset');
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

    // ═══════════════════════════════════════════════════════════════
    // PLATFORM MANAGEMENT
    // ═══════════════════════════════════════════════════════════════

    // Roles & Permissions Management (Enterprise RBAC Center)
    Route::resource('roles', SuperadminRolesController::class)->except(['show']);

    // Feature Pricing Management (CRUD completo)
    Route::resource('pricing', SuperadminFeaturePricingController::class)->except(['show']);

    // Platform Settings Management (impostazioni tecniche di piattaforma nel DB)
    Route::prefix('platform-settings')->name('platform-settings.')->group(function () {
        Route::get('/', [SuperadminPlatformSettingsController::class, 'index'])->name('index');
        Route::put('/group/{group}', [SuperadminPlatformSettingsController::class, 'updateGroup'])->name('update-group');
    });

    // Migration Orchestrator - Gestione centralizzata migration database condiviso
    Route::prefix('migration-orchestrator')->name('migration-orchestrator.')->group(function () {
        Route::get('/', [MigrationOrchestratorController::class, 'index'])->name('index');
        Route::get('/status/{project}', [MigrationOrchestratorController::class, 'status'])->name('status');
        Route::post('/execute', [MigrationOrchestratorController::class, 'execute'])->name('execute');
        Route::get('/backups', [MigrationOrchestratorController::class, 'backups'])->name('backups');
        Route::post('/backups/create', [MigrationOrchestratorController::class, 'createBackup'])->name('backups.create');
        Route::post('/backups/restore', [MigrationOrchestratorController::class, 'restoreBackup'])->name('backups.restore');
        Route::delete('/backups/delete', [MigrationOrchestratorController::class, 'deleteBackup'])->name('backups.delete');
        Route::get('/backups/download', [MigrationOrchestratorController::class, 'downloadBackup'])->name('backups.download');
        Route::get('/backup-config', [MigrationOrchestratorController::class, 'getBackupConfig'])->name('backup-config.get');
        Route::post('/backup-config', [MigrationOrchestratorController::class, 'updateBackupConfig'])->name('backup-config.update');
    });
});
