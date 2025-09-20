<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\AnnexController;
use App\Http\Controllers\CoaAddendumController;

/*
|--------------------------------------------------------------------------
| Certificate of Authenticity (CoA) Routes
|--------------------------------------------------------------------------
|
| Routes per il sistema CoA (Certificate of Authenticity) integrato
| nella piattaforma FlorenceEGI. Include funzionalità base e Pro.
|
| Struttura:
| - CoA Base: Emissione, verifica, gestione certificati
| - CoA Pro: Annessi (A_PROVENANCE, B_CONDITION, C_EXHIBITIONS, D_PHOTOS)
| - Addendums: Policy versionate e addendum Pro
| - Verification: Verifica pubblica senza autenticazione
|
*/

// ====================================================
// PUBLIC VERIFICATION ROUTES (No Auth Required)
// ====================================================

Route::group(['prefix' => 'coa/verify', 'as' => 'coa.verify.'], function () {

    // Verifica pubblica certificato per numero seriale
    Route::get('/certificate/{serial}', [VerifyController::class, 'verify'])
        ->name('certificate')
        ->where('serial', '[A-Z0-9\-]+');

    // Visualizza certificato specifico tramite hash di verifica
    Route::get('/view/{hash}', [VerifyController::class, 'viewCertificate'])
        ->name('view')
        ->where('hash', '[a-f0-9]{64}');

    // Visualizza certificato specifico tramite numero seriale (HTML)
    Route::get('/certificate/{serial}/view', [VerifyController::class, 'viewCertificateBySerial'])
        ->name('certificate.view')
        ->where('serial', '[A-Z0-9\-]+');

    // Pagina verifica pubblica con form
    Route::get('/page', [VerifyController::class, 'verificationPage'])
        ->name('page');

    // Verifica tramite hash sicurezza
    Route::post('/hash', [VerifyController::class, 'verifyHash'])
        ->name('hash');

    // Verifica annessi pubblici
    Route::get('/certificate/{serial}/annexes', [VerifyController::class, 'verifyAnnexes'])
        ->name('annexes')
        ->where('serial', '[A-Z0-9\-]+');

    // Verifica QR code
    Route::post('/qr', [VerifyController::class, 'verifyQr'])
        ->name('qr');

    // Verifica batch (multipli certificati)
    Route::post('/batch', [VerifyController::class, 'batchVerify'])
        ->name('batch');

    // Statistiche pubbliche verifiche
    Route::get('/stats', [VerifyController::class, 'publicStats'])
        ->name('stats');
});

// ====================================================
// AUTHENTICATED COA ROUTES
// ====================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // ====================================================
    // COA MAIN ROUTES (Base System)
    // ====================================================

    Route::group(['prefix' => 'coa', 'as' => 'coa.'], function () {

        // Lista certificati con filtri e paginazione
        Route::get('/', [CoaController::class, 'index'])
            ->name('index');

        // Dettagli certificato specifico
        Route::get('/{coa}', [CoaController::class, 'show'])
            ->name('show')
            ->where('coa', '[0-9]+');

        // Emissione nuovo certificato (richiede permessi)
        Route::post('/issue', [CoaController::class, 'issue'])
            ->name('issue');

        // Ri-emissione certificato esistente
        Route::post('/{coa}/reissue', [CoaController::class, 'reissue'])
            ->name('reissue')
            ->where('coa', '[0-9]+')
            ->middleware(['throttle:coa_reissue,5,1', 'role:admin|expert']);

        // Revoca certificato
        Route::post('/{coa}/revoke', [CoaController::class, 'revoke'])
            ->name('revoke')
            ->where('coa', '[0-9]+')
            ->middleware(['throttle:coa_revoke,5,1', 'role:admin|expert']);

        // Creazione bundle con annessi
        Route::post('/{coa}/bundle', [CoaController::class, 'createBundle'])
            ->name('bundle')
            ->where('coa', '[0-9]+')
            ->middleware('throttle:coa_bundle,20,1');

        // Statistiche avanzate (admin only)
        Route::get('/statistics', [CoaController::class, 'statistics'])
            ->name('statistics')
            ->middleware('role:admin');
    });

    // ====================================================
    // COA ANNEXES ROUTES (Pro System)
    // ====================================================

    Route::group(['prefix' => 'coa/{coa}/annexes', 'as' => 'coa.annexes.'], function () {

        // Lista annessi del certificato
        Route::get('/', [AnnexController::class, 'index'])
            ->name('index')
            ->where('coa', '[0-9]+');

        // Dettagli annesso specifico
        Route::get('/{annex}', [AnnexController::class, 'show'])
            ->name('show')
            ->where(['coa' => '[0-9]+', 'annex' => '[0-9]+']);

        // Creazione nuovo annesso
        Route::post('/', [AnnexController::class, 'store'])
            ->name('store')
            ->where('coa', '[0-9]+')
            ->middleware(['throttle:annex_create,15,1', 'role:admin|expert']);

        // Aggiornamento annesso esistente
        Route::put('/{annex}', [AnnexController::class, 'update'])
            ->name('update')
            ->where(['coa' => '[0-9]+', 'annex' => '[0-9]+'])
            ->middleware(['throttle:annex_update,20,1', 'role:admin|expert']);

        // Cronologia modifiche annesso
        Route::get('/{annex}/history', [AnnexController::class, 'history'])
            ->name('history')
            ->where(['coa' => '[0-9]+', 'annex' => '[0-9]+']);

        // Verifica integrità annesso
        Route::post('/{annex}/verify', [AnnexController::class, 'verifyIntegrity'])
            ->name('verify')
            ->where(['coa' => '[0-9]+', 'annex' => '[0-9]+'])
            ->middleware('throttle:annex_verify,30,1');
    });

    // Tipi annessi disponibili (route helper)
    Route::get('/coa/annex-types', [AnnexController::class, 'types'])
        ->name('coa.annex-types');

    // ====================================================
    // COA ADDENDUMS ROUTES (Pro System)
    // ====================================================

    Route::group(['prefix' => 'coa/{coa}/addendums', 'as' => 'coa.addendums.'], function () {

        // Lista addendum del certificato
        Route::get('/', [CoaAddendumController::class, 'index'])
            ->name('index')
            ->where('coa', '[0-9]+');

        // Dettagli addendum specifico
        Route::get('/{addendum}', [CoaAddendumController::class, 'show'])
            ->name('show')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+']);

        // Creazione nuovo addendum
        Route::post('/', [CoaAddendumController::class, 'store'])
            ->name('store')
            ->where('coa', '[0-9]+')
            ->middleware(['throttle:addendum_create,5,1', 'role:admin|expert']);

        // Aggiornamento addendum (solo bozze)
        Route::put('/{addendum}', [CoaAddendumController::class, 'update'])
            ->name('update')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+'])
            ->middleware(['throttle:addendum_update,10,1', 'role:admin|expert']);

        // Creazione revisione addendum
        Route::post('/{addendum}/revise', [CoaAddendumController::class, 'revise'])
            ->name('revise')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+'])
            ->middleware(['throttle:addendum_create,5,1', 'role:admin|expert']);

        // Pubblicazione addendum
        Route::post('/{addendum}/publish', [CoaAddendumController::class, 'publish'])
            ->name('publish')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+'])
            ->middleware(['throttle:addendum_create,5,1', 'role:admin|expert']);

        // Cronologia versioni addendum
        Route::get('/{addendum}/history', [CoaAddendumController::class, 'history'])
            ->name('history')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+']);

        // Archiviazione addendum
        Route::post('/{addendum}/archive', [CoaAddendumController::class, 'archive'])
            ->name('archive')
            ->where(['coa' => '[0-9]+', 'addendum' => '[0-9]+'])
            ->middleware(['role:admin', 'throttle:addendum_update,10,1']);
    });

    // ====================================================
    // GLOBAL ADDENDUM ROUTES (Cross-CoA)
    // ====================================================

    Route::group(['prefix' => 'addendums', 'as' => 'addendums.'], function () {

        // Lista globale addendum con filtri avanzati
        Route::get('/', [CoaAddendumController::class, 'index'])
            ->name('index')
            ->middleware('role:admin|expert');

        // Policy di rarità attive
        Route::get('/rarity-policies', [CoaAddendumController::class, 'rarityPolicies'])
            ->name('rarity-policies');
    });

    // ====================================================
    // EGI INTEGRATION ROUTES
    // ====================================================

    Route::group(['prefix' => 'egis/{egi}/coa', 'as' => 'egis.coa.'], function () {

        // Verifica se EGI ha certificato
        Route::get('/check', [CoaController::class, 'checkEgiCertificate'])
            ->name('check')
            ->where('egi', '[0-9]+');

        // Emissione certificato per EGI
        Route::post('/issue', [CoaController::class, 'issue'])
            ->name('issue')
            ->where('egi', '[0-9]+');

        // Mostra certificato EGI (se esiste)
        Route::get('/', function ($egi) {
            $coa = \App\Models\Coa::where('egi_id', $egi)->first();
            if (!$coa) {
                abort(404, 'Certificate not found for this EGI');
            }
            return redirect()->route('coa.show', $coa->id);
        })->name('show')->where('egi', '[0-9]+');
    });
});

// ====================================================
// ADMIN ONLY ROUTES
// ====================================================

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::group(['prefix' => 'admin/coa', 'as' => 'admin.coa.'], function () {

        // Dashboard amministrativo CoA
        Route::get('/dashboard', [CoaController::class, 'adminDashboard'])
            ->name('dashboard');

        // Gestione batch operazioni
        Route::post('/batch/revoke', [CoaController::class, 'batchRevoke'])
            ->name('batch.revoke')
            ->middleware('throttle:admin_batch,2,1');

        // Rapporti e analytics avanzati
        Route::get('/reports', [CoaController::class, 'reports'])
            ->name('reports');

        // Configurazione sistema
        Route::get('/settings', [CoaController::class, 'settings'])
            ->name('settings');

        // Export dati per compliance
        Route::post('/export', [CoaController::class, 'exportData'])
            ->name('export')
            ->middleware('throttle:admin_export,1,1');
    });
});

// ====================================================
// AJAX/API INTERNAL ROUTES
// ====================================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::group(['prefix' => 'api/coa', 'as' => 'api.coa.'], function () {

        // Ricerca rapida certificati
        Route::get('/search', [CoaController::class, 'search'])
            ->name('search')
            ->middleware('throttle:search,60,1');

        // Validazione seriale in tempo reale
        Route::post('/validate-serial', [CoaController::class, 'validateSerial'])
            ->name('validate-serial')
            ->middleware('throttle:validation,100,1');

        // Anteprima bundle prima della creazione
        Route::post('/{coa}/bundle/preview', [CoaController::class, 'previewBundle'])
            ->name('bundle.preview')
            ->where('coa', '[0-9]+')
            ->middleware('throttle:preview,30,1');

        // Controllo prerequisiti emissione
        Route::post('/issue/check', [CoaController::class, 'checkIssueRequirements'])
            ->name('issue.check')
            ->middleware('throttle:check,30,1');
    });
});
