<?php

use App\Http\Controllers\GdprController;
use App\Http\Controllers\CookieConsentController;
use Illuminate\Support\Facades\Route;

/**
 * @Oracode Routes: GDPR & Privacy Management
 * 🎯 Purpose: Handle all GDPR compliance routes
 * 🛡️ Security: Authenticated users only, CSRF protected
 * 🧱 Architecture: RESTful naming, clear intent, audit-ready
 *
 * @package Routes\GDPR
 * @version 1.0.0
 * @context gdpr
 */

// === PRIVACY POLICY & TRANSPARENCY ===
Route::get('/terms-of-service', [GdprController::class, 'termsOfService'])
    ->name('gdpr.terms');

Route::get('/privacy-policy', [GdprController::class, 'privacyPolicy'])
    ->name('gdpr.privacy-policy');

Route::group(['prefix' => 'gdpr', 'as' => 'gdpr.'], function () {

    // Privacy Policy Download
    Route::get('/privacy-policy/download', [GdprController::class, 'privacyPolicyDownload'])
        ->name('privacy-policy.download');

    // Alternative routes per diversi document types
    Route::get('/cookie-policy/download', [GdprController::class, 'cookiePolicyDownload'])
        ->name('cookie-policy.download');

    Route::get('/policy/{type}/download', [GdprController::class, 'policyDownload'])
        ->name('policy.download')
        ->where('type', 'privacy-policy|cookie-policy|terms-of-service');

    Route::get('/privacy-policy/version/{version}', [GdprController::class, 'privacyPolicyVersion'])
        ->name('privacy-policy.version');
});

// === COOKIE CONSENT API (Public Access - per tutti i visitatori) ===
Route::prefix('cookie-consent')->name('cookie-consent.')->group(function () {

    // Get current cookie consent status (authenticated & anonymous users)
    Route::get('/status', [CookieConsentController::class, 'getConsentStatus'])
        ->name('status');

    // Save cookie consent preferences (authenticated & anonymous users)
    Route::post('/save', [CookieConsentController::class, 'saveConsent'])
        ->name('save');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard/gdpr')->name('gdpr.')->group(function () {

    // === CONSENT MANAGEMENT ===
    Route::get('/consent', [GdprController::class, 'consent'])
        ->name('consent');

    Route::post('/consent/update', [GdprController::class, 'updateConsent'])
        ->name('consent.update');

    Route::get('/consent/history', [GdprController::class, 'consentHistory'])
        ->name('consent.history');

    Route::get('/consent/preferences', [GdprController::class, 'consentPreferences'])
        ->name('consent.preferences');

    Route::post('/gdpr/consent/withdraw', [App\Http\Controllers\GdprController::class, 'withdraw'])->name('consent.withdraw');
    Route::post('/gdpr/consent/renew', [App\Http\Controllers\GdprController::class, 'renew'])->name('consent.renew');

    // === DATA EXPORT & PORTABILITY ===
    Route::get('/export-data', [GdprController::class, 'exportData'])
        ->name('export-data');

    Route::post('/export-data/generate', [GdprController::class, 'generateExport'])
        ->name('export-data.generate');

    Route::get('/export-data/download/{token}', [GdprController::class, 'downloadExport'])
        ->name('export-data.download');

    // === PERSONAL DATA MANAGEMENT ===
    Route::get('/edit-personal-data', [GdprController::class, 'editPersonalData'])
        ->name('edit-personal-data');

    Route::put('/edit-personal-data/update', [GdprController::class, 'updatePersonalData'])
        ->name('edit-personal-data.update');

    Route::post('/edit-personal-data/request-rectification', [GdprController::class, 'requestRectification'])
        ->name('edit-personal-data.rectification');

    // === PROCESSING LIMITATION ===
    Route::get('/limit-processing', [GdprController::class, 'limitProcessing'])
        ->name('limit-processing');

    Route::post('/limit-processing/update', [GdprController::class, 'updateProcessingLimits'])
        ->name('limit-processing.update');

    // === ACCOUNT DELETION (Right to be Forgotten) ===
    Route::get('/delete-account', [GdprController::class, 'deleteAccount'])
        ->name('delete-account');

    Route::post('/delete-account/request', [GdprController::class, 'requestAccountDeletion'])
        ->name('delete-account.request');

    Route::delete('/delete-account/confirm', [GdprController::class, 'confirmAccountDeletion'])
        ->name('delete-account.confirm')
        ->middleware(['password.confirm', 'throttle:3,60']);

    // === ACTIVITY LOG & AUDIT ===
    Route::get('/activity-log', [GdprController::class, 'activityLog'])
        ->name('activity-log');

    Route::get('/activity-log/export', [GdprController::class, 'exportActivityLog'])
        ->name('activity-log.export');

    // === BREACH REPORTING ===
    Route::get('/breach-report', [GdprController::class, 'breachReport'])
        ->name('breach-report');

    Route::post('/breach-report/submit', [GdprController::class, 'submitBreachReport'])
        ->name('breach-report.submit');

    Route::get('/breach-report/status/{report}', [GdprController::class, 'breachReportStatus'])
        ->name('breach-report.status');

    Route::get('/privacy-policy/changelog', [GdprController::class, 'privacyPolicyChangelog'])
        ->name('privacy-policy.changelog');

    Route::get('/data-processing-info', [GdprController::class, 'dataProcessingInfo'])
        ->name('data-processing-info');

    // === DPO CONTACT & SUPPORT ===
    Route::get('/contact-dpo', [GdprController::class, 'contactDpo'])
        ->name('contact-dpo');

    Route::post('/contact-dpo/send', [GdprController::class, 'sendDpoMessage'])
        ->name('contact-dpo.send');

    // === API ROUTES (per frontend dinamico) ===
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/consent-status', [GdprController::class, 'getConsentStatus'])
            ->name('consent-status');

        Route::get('/processing-limits', [GdprController::class, 'getProcessingLimits'])
            ->name('processing-limits');

        Route::get('/export-status/{token}', [GdprController::class, 'getExportStatus'])
            ->name('export-status');
    });
});