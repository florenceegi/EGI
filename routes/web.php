<?php

use App\Actions\Jetstream\UpdateTeamName;
use App\Enums\NotificationStatus;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\EgiController;
use App\Http\Controllers\CreatorOnboardingSummaryController;
use App\Http\Controllers\EPPController;
use App\Http\Controllers\Formazione;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\TraitsApiController;
use App\Http\Controllers\Notifications\Invitations\NotificationInvitationResponseController;
use App\Http\Controllers\Notifications\NotificationDetailsController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletResponseController;
use App\Http\Controllers\Notifications\Wallets\NotificationWalletRequestController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\User\UserCollectionController;
use App\Http\Controllers\WalletConnectController;
use App\Livewire\Collections\CollectionCarousel;
use App\Livewire\Collections\CollectionEdit;
use App\Livewire\Collections\CollectionUserMember;
use App\Livewire\Collections\CreateCollection;
use App\Livewire\Collections\HeadImagesManager;
use App\Livewire\Notifications\Wallets\EditWalletModal;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Livewire\PhotoUploader;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Api\BiographyController;
use App\Http\Controllers\Api\BiographyChapterController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EgiReservationCertificateController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\IconAdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Notifications\Gdpr\GdprNotificationResponseController;
use App\Http\Controllers\Notifications\NotificationReservationResponseController;
use App\Http\Middleware\SetLanguage;
use App\Livewire\Collections\CollectionOpen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

use Livewire\Livewire;
use Ultra\EgiModule\Http\Controllers\EgiUploadController;
use Ultra\EgiModule\Http\Controllers\EgiUploadPageController;
use App\Http\Controllers\Upload\Config\GlobalConfigController;
use App\Http\Controllers\Payment\PspWebhookController; // Import Controller

// P0 FIX: Explicit Webhook Route in WEB to avoid Redirects/Middleware issues
Route::post('/stripe/webhook', [PspWebhookController::class, 'handleStripeWebhook'])
    ->name('stripe.webhook.direct');

use App\Http\Controllers\Web\BiographyWebController;

// DEBUG: Live log viewer
Route::get('/live-logs', [App\Http\Controllers\LiveLogController::class, 'show'])->middleware('auth');

// SECURITY: Debug routes available only when APP_DEBUG is true
// Route::get('/debug-logs', [App\Http\Controllers\DebugLogController::class, 'show'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 📜 Oracode Routes: FlorenceEGI Application Routes
| Organized by functionality and access level for clarity
|
*/

// Broadcast Authentication Routes
Broadcast::routes(['middleware' => ['web', 'auth']]);


// SECURITY: Debug routes available only when APP_DEBUG is true
if (config('app.debug')) {
    Route::get('/test-loadstats', function () {
        Log::channel('florenceegi')->info('Direct auth test', [
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'auth_user' => Auth::user()?->name,
            'session_auth_status' => session('auth_status'),
            'session_user_id' => session('connected_user_id'),
        ]);

        return 'Check the logs!';
    });

    Route::get('/test-create-fegi', function () {
        $request = request();
        $request->merge(['create_new' => true]);

        $controller = new \App\Http\Controllers\WalletConnectController(
            app(\Ultra\UltraLogManager\UltraLogManager::class),
            app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class),
            app(\App\Services\CollectionService::class)
        );

        return $controller->connect($request);
    });
}

// 📄 PUBLIC CERTIFICATE ENDPOINT (No auth required - blockchain transparency)
Route::post('/mint/{egiId}/certificate/pdf/check', [App\Http\Controllers\EgiReservationCertificateController::class, 'checkMintCertificatePdf'])
    ->name('mint.certificate.pdf-check');

// Mint routes (blockchain integration) - NEW ARCHITECTURE 2-PAGES
// PAGINA 2: Mint Result (READ-ONLY, riapribile) - accesso pubblico
Route::get('/mint/{egiBlockchainId}', [App\Http\Controllers\MintController::class, 'showMintResult'])
    ->name('mint.show');

// Mint data readiness check (AJAX polling for loading state)
Route::get('/mint/{egiBlockchainId}/data-ready', [App\Http\Controllers\MintController::class, 'checkMintDataReady'])
    ->name('mint.data-ready');

Route::middleware('auth')->group(function () {
    Route::get('/creator/onboarding-summary', [CreatorOnboardingSummaryController::class, 'show'])
        ->name('creator.onboarding.summary');

    // PAGINA 1: Payment Form
    Route::get('/mint/payment/{egiId}', [App\Http\Controllers\MintController::class, 'showPaymentForm'])
        ->name('mint.payment-form');

    // Regenerate certificate (without new mint)
    Route::post('/mint/{egiBlockchainId}/regenerate-certificate', [App\Http\Controllers\MintController::class, 'regenerateCertificate'])
        ->name('mint.regenerate-certificate');

    // Process payment + mint
    Route::post('/mint/process', [App\Http\Controllers\MintController::class, 'processMint'])
        ->name('mint.process');

    // Mint status polling endpoint (AJAX)
    Route::get('/mint/status/{egiId}', [App\Http\Controllers\MintController::class, 'checkMintStatus'])
        ->name('mint.status');

    // Phase 2: Direct mint route (dual path: mint OR reserve)
    Route::get('/egi/{id}/mint-direct', [App\Http\Controllers\MintController::class, 'showDirectMint'])
        ->name('egi.mint-direct');

    Route::post('/egi/{id}/mint-direct', function($id, \Illuminate\Http\Request $request) {
        \Log::channel('stack')->emergency('🔴 ROUTE HIT: egi.mint-direct.process', [
            'id' => $id,
            'user_id' => Auth::id(),
            'payment_method' => $request->input('payment_method'),
            'all_input' => $request->all()
        ]);
        $controller = app(App\Http\Controllers\MintController::class);
        return $controller->processDirectMint($id, app(App\Http\Requests\MintDirectRequest::class));
    })->name('egi.mint-direct.process');

    // Phase 3: Rebind route (secondary market - purchase from owner)
    Route::get('/egi/{id}/rebind', [App\Http\Controllers\RebindController::class, 'show'])
        ->name('egi.rebind');

    Route::post('/egi/{id}/rebind', [App\Http\Controllers\RebindController::class, 'process'])
        ->name('egi.rebind.process');

    // Dual Architecture: Auto-Mint & Pre-Mint actions
    Route::prefix('egi/{egi}/dual-arch')->name('egi.dual-arch.')->group(function () {
        Route::post('/auto-mint/enable', [App\Http\Controllers\EgiDualArchitectureController::class, 'enableAutoMint'])
            ->name('auto-mint.enable');
        Route::post('/auto-mint/disable', [App\Http\Controllers\EgiDualArchitectureController::class, 'disableAutoMint'])
            ->name('auto-mint.disable');
        Route::post('/pre-mint/request-analysis', [App\Http\Controllers\EgiDualArchitectureController::class, 'requestPreMintAnalysis'])
            ->name('pre-mint.request-analysis');
        Route::post('/pre-mint/promote', [App\Http\Controllers\EgiDualArchitectureController::class, 'promoteToOnChain'])
            ->name('pre-mint.promote');
        Route::post('/ai/generate-description', [App\Http\Controllers\EgiDualArchitectureController::class, 'generateDescription'])
            ->name('ai.generate-description');
        Route::post('/ai/improve-description', [App\Http\Controllers\EgiDualArchitectureController::class, 'improveDescription'])
            ->name('ai.improve-description');

        // AI Traits Generation
        Route::post('/traits/generate', [App\Http\Controllers\AiTraitController::class, 'generate'])
            ->name('traits.generate');
    });

    // Egili Purchase System (EUR/Crypto → Egili)
    Route::prefix('egili')->name('egili.')->group(function () {
        Route::post('/purchase/process', [App\Http\Controllers\EgiliPurchaseController::class, 'processPurchase'])
            ->name('purchase.process');

        Route::get('/purchase/{orderReference}/confirmation', [App\Http\Controllers\EgiliPurchaseController::class, 'showConfirmation'])
            ->name('purchase.confirmation');

        Route::get('/purchase/pricing', [App\Http\Controllers\EgiliPurchaseController::class, 'getPricing'])
            ->name('purchase.pricing');
    });

    // Feature Purchase System (Hybrid Approach - Generic for ALL features)
    Route::prefix('features')->name('features.')->group(function () {
        Route::get('/{code}/purchase', [App\Http\Controllers\FeaturePurchaseController::class, 'showPurchaseForm'])
            ->name('purchase');
        Route::post('/purchase/process', [App\Http\Controllers\FeaturePurchaseController::class, 'processPurchase'])
            ->name('purchase.process');
        Route::get('/purchase/success', [App\Http\Controllers\FeaturePurchaseController::class, 'purchaseSuccess'])
            ->name('purchase.success');
        Route::get('/purchase/cancel', [App\Http\Controllers\FeaturePurchaseController::class, 'purchaseCancel'])
            ->name('purchase.cancel');
    });

    // AI Traits Management (outside egi prefix for easier access)
    Route::prefix('traits/generations')->name('traits.generations.')->group(function () {
        Route::get('/{generation}', [App\Http\Controllers\AiTraitController::class, 'show'])
            ->name('show');
        Route::post('/{generation}/review', [App\Http\Controllers\AiTraitController::class, 'review'])
            ->name('review');
        Route::post('/{generation}/apply', [App\Http\Controllers\AiTraitController::class, 'apply'])
            ->name('apply');
    });
});

// ====================================================
// AI ART ADVISOR - Multi-Expert Chat Assistant
// ====================================================
Route::prefix('art-advisor')->name('art-advisor.')->middleware('auth')->group(function () {
    Route::post('/chat', [App\Http\Controllers\ArtAdvisorController::class, 'chat'])
        ->name('chat');
    Route::get('/test', [App\Http\Controllers\ArtAdvisorController::class, 'test'])
        ->name('test');
});

// SECURITY: Debug routes available only when APP_DEBUG is true
if (config('app.debug')) {
    Route::get('/debug-user-lookup', function () {
        $userId = session('connected_user_id');

        if (!$userId) {
            return ['error' => 'No user ID in session'];
        }

        $user = \App\Models\User::find($userId);

        return [
            'session_user_id' => $userId,
            'user_found' => $user ? true : false,
            'user_data' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_weak_auth' => $user->is_weak_auth,
                'wallet' => $user->wallet,
                'created_at' => $user->created_at
            ] : null,
            'users_count' => \App\Models\User::count(),
            'weak_auth_users' => \App\Models\User::where('is_weak_auth', true)->get(['id', 'name', 'email'])
        ];
    });

    // Route di test per il carousel delle collection
    Route::get('/test-carousel', function () {
        return view('test-carousel');
    })->name('test.carousel');

    // Route di test per la sfera Three.js con immagine
    Route::get('/test-sphere', function () {
        return view('test.sphere-experiment');
    })->name('test.sphere');

    // Route di test per il Composition Builder
    Route::get('/test-composition', function () {
        $egis = \App\Models\Egi::where('user_id', 26)
            ->select('id', 'title', 'collection_id', 'user_id')
            ->take(12)
            ->get();
        return view('test.composition-builder', compact('egis'));
    })->name('test.composition');

    // Route di test per la Shapes Gallery
    Route::get('/test-shapes', function () {
        return view('test.shapes-gallery');
    })->name('test.shapes');

    // Route di test per il 3D Laboratory
    Route::get('/test-lab', function () {
        return view('test.lab-3d');
    })->name('test.lab');

    // Route di test per il Polyhedron Composer
    Route::get('/test-polyhedron', function () {
        $egis = \App\Models\Egi::where('user_id', 26)
            ->select('id', 'title', 'collection_id', 'user_id')
            ->take(24)
            ->get();
        return view('test.polyhedron-composer', compact('egis'));
    })->name('test.polyhedron');

    // Route di test per il Prism Viewer (EGI 3D Card Component)
    Route::get('/test-prism', function () {
        $egis = \App\Models\Egi::where('user_id', 30)
            ->with('collection:id,collection_name')
            ->select('id', 'title', 'description', 'collection_id', 'user_id')
            ->take(12)
            ->get();
        return view('test.prism-viewer', compact('egis'));
    })->name('test.prism');

    // Route di test per il Collection Cube (3D Cube Cards nel Carousel)
    Route::get('/test-cube', function () {
        return view('test.collection-cube');
    })->name('test.cube');

    Route::get('/debug-session-direct', function () {
        return [
            'session_direct' => [
                'auth_status' => session('auth_status'),
                'user_id' => session('connected_user_id'),
            ],
            'fegi_guard_debug' => [
                'user_resolved' => Auth::guard('fegi')->user(),
                'check' => Auth::guard('fegi')->check(),
            ]
        ];
    });
}

// Route::get('/test-upload-dir', function() {
//     return response()->json([
//         'upload_tmp_dir' => ini_get('upload_tmp_dir')
//     ]);
// });



/*
|--------------------------------------------------------------------------
| Public Routes - Homepage & Redirects
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
// Universal Search Results
Route::get('/search/results', [SearchController::class, 'results'])->name('search.results');
Route::get('/search/panel', [SearchController::class, 'panel'])->name('search.panel');
// Alias dedicati per risultati filtrati per singolo tipo
Route::get('/egis/search', function (\Illuminate\Http\Request $r) {
    return redirect()->route('search.results', array_merge($r->all(), ['types' => 'egi']));
})->name('egis.search');
Route::get('/creators/search', function (\Illuminate\Http\Request $r) {
    return redirect()->route('search.results', array_merge($r->all(), ['types' => 'creator']));
})->name('creators.search');
// Collection Banner Upload (creator-only)
Route::middleware(['auth'])
    ->prefix('collections')
    ->name('collections.')
    ->group(function () {
        Route::post('{collection}/banner', [\App\Http\Controllers\CollectionBannerController::class, 'store'])
            ->name('banner.upload');

        // CRUD metadata Collection (Spatie permissions checked in controller)
        Route::patch('{collection}', [\App\Http\Controllers\CollectionCrudController::class, 'update'])
            ->name('update');
        Route::delete('{collection}', [\App\Http\Controllers\CollectionCrudController::class, 'destroy'])
            ->name('destroy');

        // Collection Payment Settings
        Route::prefix('{collection}/settings/payments')->name('settings.payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\PaymentSettingsController::class, 'indexCollection'])
                ->name('index');
            Route::post('/{method}/toggle', [App\Http\Controllers\PaymentSettingsController::class, 'toggleCollection'])
                ->name('toggle');
            Route::post('/{method}/default', [App\Http\Controllers\PaymentSettingsController::class, 'setDefaultCollection'])
                ->name('set-default');
            Route::post('/bank-transfer/config', [App\Http\Controllers\PaymentSettingsController::class, 'updateBankConfigCollection'])
                ->name('bank-config');
        });

        // P0 Commerce: Collection Commercial Setup Wizard
        Route::prefix('{collection}/commerce')->name('commerce.')->group(function () {
            Route::get('/wizard', [\App\Http\Controllers\CollectionCommerceWizardController::class, 'show'])
                ->name('wizard');
            Route::post('/wizard/update', [\App\Http\Controllers\CollectionCommerceWizardController::class, 'update'])
                ->name('wizard.update');
            Route::post('/enable', [\App\Http\Controllers\CollectionCommerceWizardController::class, 'enable'])
                ->name('enable');
        });
    });

// P0 Commerce: EGI Listing Wizard (separate prefix for clarity)
Route::middleware(['auth'])->prefix('egis/{egi}/listing')->name('egi.listing.')->group(function () {
    Route::get('/wizard', [\App\Http\Controllers\EgiListingWizardController::class, 'show'])
        ->name('wizard');
    Route::post('/wizard/update', [\App\Http\Controllers\EgiListingWizardController::class, 'update'])
        ->name('wizard.update');
    Route::post('/publish', [\App\Http\Controllers\EgiListingWizardController::class, 'publish'])
        ->name('publish');
});

// Notification Center (ex-Dashboard)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Type-specific dashboards (currently aliased to main dashboard, future-proof for separate views)
Route::get('/creator/dashboard', function () {
    return view('dashboard');
})->name('creator.dashboard');

Route::get('/company/dashboard', function () {
    return view('dashboard');
})->name('company.dashboard');

Route::get('/collector/dashboard', function () {
    return view('dashboard');
})->name('collector.dashboard');

// Alias per chiarezza semantica
Route::get('/notifications', function () {
    return view('dashboard');
})->name('notifications');


Route::get('/archetypes/patron', function () {
    return view('archetypes.patron-standalone');
})->name('archetypes.patron');

Route::get('/archetypes/collector', function () {
    return view('archetypes.collector');
})->name('archetypes.collector');

Route::get('/archetypes/pa-entity', function () {
    return view('archetypes.pa-entity');
})->name('archetypes.pa-entity');

/*
|--------------------------------------------------------------------------
| Platform Info Routes - EGI, Attivare, Impatto
|--------------------------------------------------------------------------
*/
Route::prefix('info')->name('info.')->group(function () {
    Route::get('/florenceegi', [App\Http\Controllers\Info\FlorenceEgiController::class, 'index'])->name('florence-egi');
    Route::get('/florenceegi-light', [App\Http\Controllers\Info\FlorenceEgiController::class, 'light'])->name('florence-egi-light');
    Route::get('/florenceegi-v4', [App\Http\Controllers\Info\FlorenceEgiController::class, 'v4'])->name('florence-egi-v4');
    Route::get('/florenceegi-v4-wheel', [App\Http\Controllers\Info\FlorenceEgiController::class, 'v4wheel'])->name('florence-egi-v4-wheel');

    Route::get('/disclaimer', function () {
        return view('info.disclaimer');
    })->name('disclaimer');

    Route::get('/epp', function () {
        return view('info.epp-info');
    })->name('epp');

    Route::get('/egi', function () {
        return view('info.egi-info');
    })->name('egi');

    Route::get('/co-create-ecosystem', function () {
        return view('info.co-create-ecosystem');
    })->name('co-create-ecosystem');

    Route::get('/white-paper-finanziario', function () {
        return view('info.white-paper-finanziario');
    })->name('white-paper-finanziario');

    Route::get('/florenceegi-source-truth', function () {
        return view('info.florenceegi_source_truth');
    })->name('florenceegi-source-truth');

    Route::get('/attivare', function () {
        return view('info.under-construction', ['title' => 'Come co-creare un EGI?', 'subtitle' => 'Guida step-by-step per co-creare il tuo EGI (Environmental Goods Invent)']);
    })->name('attivare');

    // Route::get('/impatto', function () {
    //     return view('info.under-construction', ['title' => 'Che Impatto hanno gli EGI?', 'subtitle' => 'Scopri l\'impatto ambientale e sociale degli EGI (Environmental Goods Invent)']);
    // })->name('impatto');

    Route::get('/creator', function () {
        return view('info.under-construction', ['title' => 'Chi sono i Creator?', 'subtitle' => 'Scopri il ruolo dei creatori di contenuti nell\'ecosistema FlorenceEGI']);
    })->name('creator');

    Route::get('/attivatori', function () {
        return view('info.under-construction', ['title' => 'Chi sono i Co Creator?', 'subtitle' => 'Scopri il ruolo dei Co Creator e come contribuiscono alla piattaforma']);
    })->name('attivatori');

    Route::get('/mecenati', function () {
        return view('archetypes.patron-standalone');
    })->name('mecenati');

    Route::get('/aziende', function () {
        return view('info.under-construction', ['title' => 'Le Aziende Partner', 'subtitle' => 'Scopri come le aziende partecipano all\'ecosistema sostenibile']);
    })->name('aziende');

    Route::get('/trader-pro', function () {
        return view('info.under-construction', ['title' => 'Trader Professionali', 'subtitle' => 'Scopri gli strumenti avanzati per il trading di EGI']);
    })->name('trader-pro');

    // EPP Info - redirect to EPP Projects page
    Route::get('/epps', function () {
        return redirect()->route('epp-projects.index');
    })->name('epps');
});

/*
|--------------------------------------------------------------------------
| Collector Routes - Public Profiles & Portfolios
|--------------------------------------------------------------------------
*/
Route::prefix('collector')->name('collector.')->group(function () {
    // Collector index page (list all collectors)
    Route::get('/', [\App\Http\Controllers\CollectorHomeController::class, 'index'])->name('index');

    // Collector public profiles and portfolio
    Route::get('/{id}', [\App\Http\Controllers\CollectorHomeController::class, 'home'])->name('home')->where('id', '[0-9]+');
    Route::get('/{id}/portfolio', [\App\Http\Controllers\CollectorHomeController::class, 'portfolio'])->name('portfolio')->where('id', '[0-9]+');
    Route::get('/{id}/collections', [\App\Http\Controllers\CollectorHomeController::class, 'collections'])->name('collections')->where('id', '[0-9]+');
    Route::get('/{id}/collection/{collection}', [\App\Http\Controllers\CollectorHomeController::class, 'showCollection'])->name('collection.show')->where(['id' => '[0-9]+', 'collection' => '[0-9]+']);

    // API endpoints for collector stats
    Route::get('/{id}/stats', [\App\Http\Controllers\CollectorHomeController::class, 'getStats'])->name('stats')->where('id', '[0-9]+');

    // Placeholder for future features
    Route::get('/under-construction', [\App\Http\Controllers\CollectorHomeController::class, 'underConstruction'])->name('under-construction');
});

/*
|--------------------------------------------------------------------------
| Public Routes - Collections & EGIs
|--------------------------------------------------------------------------
*/

Route::middleware('collection_can:view_collection_header')->group(function () {
    Route::get('collections/{id}/edit', CollectionEdit::class)
        ->name('collections.edit');

    Route::get('collections/open', CollectionOpen::class)
        ->name('collections.open');

    Route::get('/{id}/head-images', HeadImagesManager::class)
        ->name('collections.head_images');
});


Route::prefix('home')->name('home.')->group(function () {
    // Public collection viewing (accessible to all)
    Route::get('/collections', [CollectionsController::class, 'index'])->name('collections.index');
    Route::get('/collections/{id}', [CollectionsController::class, 'show'])->name('collections.show');

    // Collection subscription (Egili payment)
    Route::post('/collections/{id}/monetization/switch-to-subscription', [CollectionsController::class, 'switchToSubscription'])
        ->name('collections.subscription.activate')
        ->middleware('auth');

    Route::post('/collections/{id}/monetization/toggle-auto-renew', [CollectionsController::class, 'toggleAutoRenew'])
        ->name('collections.subscription.toggle-auto-renew')
        ->middleware('auth');

    Route::post('/collections/{id}/monetization/cancel-subscription', [CollectionsController::class, 'cancelSubscription'])
        ->name('collections.subscription.cancel')
        ->middleware('auth');

    // Collection management (restricted to creators)
    // Route::middleware(['can:manage-collections'])->group(function () {
    //     Route::get('/collections/create', [CollectionsController::class, 'create'])->name('collections.create');
    //     Route::post('/collections', [CollectionsController::class, 'store'])->name('collections.store');
    //     Route::get('/collections/{id}/edit', [CollectionsController::class, 'edit'])->name('collections.edit');
    //     Route::put('/collections/{collection}', [CollectionsController::class, 'update'])->name('collections.update');
    //     Route::delete('/collections/{collection}', [CollectionsController::class, 'destroy'])->name('collections.destroy');
    // });

    // // Collection interaction
    // Route::post('/collections/{collection}/report', [CollectionsController::class, 'report'])->name('collections.report');
});

// Fallback legacy route: direct /collections/{id} GET (usato da suggerimenti / vecchi link)
Route::get('/collections/{id}', function ($id) {
    return redirect()->route('home.collections.show', ['id' => $id]);
})->where('id', '[0-9]+');


// EGI routes (Universal Architecture - Service Layer)

Route::group(['prefix' => 'egis'], function () {

    // List EGI (public + authenticated) - role-based filtering via Service
    Route::get('/', [App\Http\Controllers\EgiController::class, 'index'])
        ->name('egis.index');

    // Create EGI Form - authenticated only
    Route::middleware('auth')->group(function () {
        Route::get('/create', [App\Http\Controllers\EgiController::class, 'create'])
            ->name('egis.create');

        // Store new EGI
        Route::post('/', [App\Http\Controllers\EgiController::class, 'store'])
            ->name('egis.store');
    });

    // Mostra singolo EGI (già esistente, confermo per completezza)
    Route::get('/{egi}', [App\Http\Controllers\EgiController::class, 'show'])
        ->name('egis.show');

    // Edit EGI Form - authenticated only
    Route::middleware('auth')->group(function () {
        Route::get('/{egi}/edit', [App\Http\Controllers\EgiController::class, 'edit'])
            ->name('egis.edit');
    });

    // Update EGI - PUT for full update
    Route::put('/{egi}', [App\Http\Controllers\EgiController::class, 'update'])
        ->name('egis.update');

    // Delete EGI - DELETE for deletion
    Route::delete('/{egi}', [App\Http\Controllers\EgiController::class, 'destroy'])
        ->name('egis.destroy');

    // Dossier API endpoint
    Route::get('/{egi}/dossier', [App\Http\Controllers\EgiController::class, 'dossier'])
        ->name('egis.dossier');

    // Trait management routes
    Route::middleware('auth')->group(function () {
        Route::get('/{egi}/traits', [TraitsApiController::class, 'getEgiTraits'])
            ->name('egis.traits.get');
        Route::post('/{egi}/traits', [TraitsApiController::class, 'saveEgiTraits'])
            ->name('egis.traits.save');
        Route::post('/{egi}/traits/add', [TraitsApiController::class, 'addEgiTraits'])
            ->name('egis.traits.add');
        Route::post('/{egi}/traits/add-single', [TraitsApiController::class, 'addSingleTrait'])
            ->name('egis.traits.add-single');
        Route::delete('/{egi}/traits/{trait}', [TraitsApiController::class, 'deleteTrait'])
            ->name('egis.traits.delete');

        // Master Clonable System Routes
        Route::post('/{egi}/master/toggle', [\App\Http\Controllers\EgiMasterController::class, 'toggleMaster'])
            ->name('egis.master.toggle');
        Route::post('/{egi}/master/toggle-buyer', [\App\Http\Controllers\EgiMasterController::class, 'toggleBuyerCloning'])
            ->name('egis.master.toggle-buyer');

        Route::post('/{egi}/master/clone', [\App\Http\Controllers\EgiMasterController::class, 'clone'])
            ->name('egis.master.clone');
    });
});

// Trait system routes
Route::prefix('traits')->group(function () {
    Route::get('/categories', [TraitsApiController::class, 'getCategories'])
        ->name('traits.categories');
    Route::get('/types', [TraitsApiController::class, 'getTraitTypes'])
        ->name('traits.types');
    Route::get('/categories/{category}/types', [TraitsApiController::class, 'getTraitTypesByCategory'])
        ->name('traits.categories.types');
    Route::post('/clear-cache', [TraitsApiController::class, 'clearCache'])
        ->name('traits.clear-cache')
        ->middleware('auth'); // Solo utenti autenticati possono pulire la cache

    // Trait image management routes (usando il controller dedicato)
    Route::post('/image/upload', [App\Http\Controllers\TraitImageController::class, 'uploadImage'])
        ->name('traits.image.upload')
        ->middleware('auth');
    Route::delete('/image/{trait}', [App\Http\Controllers\TraitImageController::class, 'deleteImage'])
        ->name('traits.image.delete')
        ->middleware('auth');
});

// Worker status check (AJAX for progress bar - PUBLIC endpoint for status monitoring)
Route::get('worker/status', [App\Http\Controllers\Api\WorkerStatusController::class, 'getStatus'])
    ->name('worker.status.check');

// Utility management routes
Route::middleware(['auth'])->group(function () {
    Route::post('utilities', [App\Http\Controllers\UtilityController::class, 'store'])->name('utilities.store');
    Route::put('utilities/{utility}', [App\Http\Controllers\UtilityController::class, 'update'])->name('utilities.update');
    Route::delete('utilities/{utility}', [App\Http\Controllers\UtilityController::class, 'destroy'])->name('utilities.destroy');

    // Mint status check (AJAX internal - session-based auth)
    Route::get('mint/status/{egiId}', [App\Http\Controllers\Api\MintStatusController::class, 'getMintStatus'])
        ->name('mint.status.check');

    // Worker start (admin only - requires auth)
    Route::post('worker/start', [App\Http\Controllers\Api\WorkerStatusController::class, 'attemptStart'])
        ->name('worker.attempt.start');
});

// Fallback Stripe Webhook (Root Level Access)
Route::post('/stripe/webhook', [\App\Http\Controllers\Payment\PspWebhookController::class, 'handleStripeWebhook'])
    ->name('web.stripe.webhook.fallback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// EPP routes (DEPRECATED - old epps table)
Route::get('/epps', [EppController::class, 'index'])->name('epps.index');
Route::get('/epps/{epp}', [EppController::class, 'show'])->name('epps.show');
Route::get('/epps/dashboard', [EppController::class, 'dashboard'])->name('epps.dashboard');

// EPP PROJECTS routes (NEW - epp_projects table) - PUBLIC
Route::get('/epp-projects', [App\Http\Controllers\EppProjectController::class, 'index'])->name('epp-projects.index');
Route::get('/epp-projects/{eppProject}', [App\Http\Controllers\EppProjectController::class, 'show'])->name('epp-projects.show');
Route::get('/equilibrium', [App\Http\Controllers\EppProjectController::class, 'dashboard'])->name('equilibrium.dashboard');

// EPP DASHBOARD routes (PRIVATE - EPP users only)
Route::prefix('epp/dashboard')->name('epp.dashboard.')->middleware(['auth', 'check.user.type:EPP'])->group(function () {
    // Dashboard home
    Route::get('/', [App\Http\Controllers\Epp\EppDashboardController::class, 'index'])->name('index');

    // Projects management
    Route::get('/projects', [App\Http\Controllers\Epp\EppDashboardController::class, 'projects'])->name('projects');
    Route::get('/projects/create', [App\Http\Controllers\Epp\EppDashboardController::class, 'createProject'])->name('projects.create');
    Route::post('/projects', [App\Http\Controllers\Epp\EppDashboardController::class, 'storeProject'])->name('projects.store');
    Route::get('/projects/{project}/edit', [App\Http\Controllers\Epp\EppDashboardController::class, 'editProject'])->name('projects.edit');
    Route::put('/projects/{project}', [App\Http\Controllers\Epp\EppDashboardController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [App\Http\Controllers\Epp\EppDashboardController::class, 'destroyProject'])->name('projects.destroy');

    // Collections management
    Route::get('/collections', [App\Http\Controllers\Epp\EppDashboardController::class, 'collections'])->name('collections');
});

// EPP API routes
Route::get('/api/epp-projects/active', [EppController::class, 'getActiveEppProjects'])->name('api.epp-projects.active');
Route::post('/api/collections/{id}/epp-project', [CollectionsController::class, 'updateEppProject'])->name('api.collections.update-epp-project');


/*
|--------------------------------------------------------------------------
| Wallet & Authentication Routes
|--------------------------------------------------------------------------
*/
// FEGI Key-based wallet (simulated/MVP)
Route::post('/wallet/connect', [WalletConnectController::class, 'connect'])->name('wallet.connect');
Route::post('/api/wallet/disconnect', [WalletConnectController::class, 'disconnect'])->name('wallet.disconnect');
Route::get('/api/wallet/status', [WalletConnectController::class, 'status'])->name('wallet.status');

// Real Algorand Wallet routes
Route::post('/wallet/real/verify', [\App\Http\Controllers\RealWalletConnectController::class, 'verify'])->name('wallet.real.verify');
Route::post('/wallet/real/connect', [\App\Http\Controllers\RealWalletConnectController::class, 'connect'])->name('wallet.real.connect');
Route::post('/wallet/real/create-guest', [\App\Http\Controllers\RealWalletConnectController::class, 'createGuest'])->name('wallet.real.create-guest');
Route::post('/wallet/real/prepare-register', [\App\Http\Controllers\RealWalletConnectController::class, 'prepareRegister'])->name('wallet.real.prepare-register');

// Wallet Redemption routes (Creator claims their wallet seed phrase)
Route::middleware(['auth'])->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/redemption', [WalletController::class, 'redemption'])->name('redemption');
    Route::post('/redemption/confirm', [WalletController::class, 'confirmRedemption'])->name('redemption.confirm');
    Route::get('/redemption/download', [WalletController::class, 'downloadSeedPhrase'])->name('redemption.download');
    Route::post('/redemption/finalize', [WalletController::class, 'finalizeRedemption'])->name('redemption.finalize');

    // NEW v2.0: Full redemption with ASA transfer
    Route::post('/redemption/execute', [WalletController::class, 'executeRedemption'])->name('redemption.execute');
    Route::get('/redemption/cost', [WalletController::class, 'getRedemptionCost'])->name('redemption.cost');
    Route::get('/redemption/egis', [WalletController::class, 'getUserEgisForRedemption'])->name('redemption.egis');
});

/*
|--------------------------------------------------------------------------
| Upload Routes
|--------------------------------------------------------------------------
*/
Route::post('/upload/egi', [EgiUploadController::class, 'handleUpload'])
    ->name('egi.upload.store');

// Photo uploader component
Route::get('/photo-uploader', PhotoUploader::class)->name('photo-uploader');


/*
|--------------------------------------------------------------------------
| User Collections API
|--------------------------------------------------------------------------
*/
Route::prefix('api/user')->name('api.user.')->group(function () {
    Route::get('/accessible-collections', [UserCollectionController::class, 'getAccessibleCollections'])
        ->name('accessible.collections');

    // New: Collections where the user can create EGI (filtered by role permission)
    Route::get('/egi-creatable-collections', [UserCollectionController::class, 'getEgiCreatableCollections'])
        ->name('egi.creatable.collections');

    Route::post('/set-current-collection/{collection}', [UserCollectionController::class, 'setCurrentCollection'])
        ->name('setCurrentCollection');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {


        // Override Jetstream profile route with our GDPR-compliant version
        Route::get('/user/profile', [GdprController::class, 'showProfile'])
            ->name('profile.show');

        // Alternative route for direct access
        Route::get('/profile', [GdprController::class, 'showProfile'])
            ->name('gdpr.profile');

        // Dedicated GDPR view routes for separated interface
        Route::get('/profile/security', [GdprController::class, 'showSecurity'])
            ->name('gdpr.security');
        Route::get('/profile/images', [GdprController::class, 'showProfileImages'])
            ->name('gdpr.profile-images');

        // Profile Image Management Routes
        Route::post('/profile/upload-image', [App\Http\Controllers\ProfileImageController::class, 'uploadImage'])
            ->name('profile.upload-image');
        Route::post('/profile/set-current-image', [App\Http\Controllers\ProfileImageController::class, 'setCurrentImage'])
            ->name('profile.set-current-image');
        Route::delete('/profile/delete-image', [App\Http\Controllers\ProfileImageController::class, 'deleteImage'])
            ->name('profile.delete-image');

        // Profile Banner Management Routes
        Route::post('/profile/upload-banner', [App\Http\Controllers\ProfileImageController::class, 'uploadBanner'])
            ->name('profile.upload-banner');
        Route::post('/profile/set-current-banner', [App\Http\Controllers\ProfileImageController::class, 'setCurrentBanner'])
            ->name('profile.set-current-banner');
        Route::delete('/profile/delete-banner', [App\Http\Controllers\ProfileImageController::class, 'deleteBanner'])
            ->name('profile.delete-banner');

        // Account Statements Routes
        Route::get('/account/statements', [App\Http\Controllers\AccountStatementsController::class, 'index'])
            ->name('account.statements');
        Route::get('/account/statements/egili/pdf', [App\Http\Controllers\AccountStatementsController::class, 'downloadEgiliPdf'])
            ->name('account.statements.egili.pdf');
        Route::get('/account/statements/eur/pdf', [App\Http\Controllers\AccountStatementsController::class, 'downloadEurPdf'])
            ->name('account.statements.eur.pdf');

        // Invoices routes
        Route::get('/account/invoices', [App\Http\Controllers\InvoiceController::class, 'index'])
            ->name('account.invoices');
        Route::get('/account/invoices/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])
            ->name('account.invoices.show');
        Route::post('/account/invoices/aggregation/{aggregationId}/generate', [App\Http\Controllers\InvoiceController::class, 'generateFromAggregation'])
            ->name('account.invoices.aggregation.generate');
        Route::get('/account/invoices/aggregation/{aggregationId}/export', [App\Http\Controllers\InvoiceController::class, 'exportAggregation'])
            ->name('account.invoices.aggregation.export');
        Route::get('/account/invoices/aggregation/{aggregationId}/details/{type}', [App\Http\Controllers\InvoiceController::class, 'getAggregationDetails'])
            ->name('account.invoices.aggregation.details');
        Route::post('/account/invoices/settings', [App\Http\Controllers\InvoiceController::class, 'updateSettings'])
            ->name('account.invoices.settings.update');
        Route::get('/account/invoices/{id}/pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPdf'])
            ->name('account.invoices.download.pdf');

        // Payment Settings Routes (Sellers only - Collectors excluded)
        Route::prefix('settings/payments')->name('settings.payments.')->group(function () {
            Route::get('/modal', [App\Http\Controllers\PaymentSettingsController::class, 'modal'])
                ->name('modal');
            Route::get('/', [App\Http\Controllers\PaymentSettingsController::class, 'index'])
                ->name('index');
            Route::post('/{method}/toggle', [App\Http\Controllers\PaymentSettingsController::class, 'toggle'])
                ->name('toggle');
            Route::post('/{method}/default', [App\Http\Controllers\PaymentSettingsController::class, 'setDefault'])
                ->name('set-default');
            Route::post('/stripe/config', [App\Http\Controllers\PaymentSettingsController::class, 'updateStripeConfig'])
                ->name('stripe.config');
            Route::post('/bank-transfer/config', [App\Http\Controllers\PaymentSettingsController::class, 'updateBankConfig'])
                ->name('bank-config');
            Route::get('/available', [App\Http\Controllers\PaymentSettingsController::class, 'getAvailable'])
                ->name('available');
        });

        // Upload configuration and authorization (override vendor routes)
        Route::get('/config/global-config', [GlobalConfigController::class, 'getGlobalConfig'])
            ->name('global.config');
        Route::get('/api/check-upload-authorization', [GlobalConfigController::class, 'checkUploadAuthorization'])
            ->name('upload.authorization');

        // Dashboard statica temporanea per test
        Route::get('/dashboard-static', [App\Http\Controllers\DashboardStaticController::class, 'index'])->name('dashboard.static');

        Route::get('/debug-context', function () {
            return Route::currentRouteName();
        })->name('debug.context');
        // EGI upload routes
        Route::middleware('collection_can:manage_egi')->group(function () {
            // Upload routes are defined here when needed
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('roles', RoleController::class)
                ->middleware(['role_or_permission:manage_roles']);

            Route::resource('icons', IconAdminController::class)
                ->middleware(['role_or_permission: manage_icons']);

            Route::get('/assign-role/form', [RoleController::class, 'showAssignRoleForm'])
                ->name('assign.role.form')
                ->middleware(['role_or_permission:manage_roles']);

            Route::post('/assign-role', [RoleController::class, 'assignRole'])
                ->name('assign.role')
                ->middleware(['role_or_permission:manage_roles']);

            Route::get('/assign-permissions', [RoleController::class, 'showAssignPermissionsForm'])
                ->name('assign.permissions.form')
                ->middleware(['role_or_permission:manage_roles']);

            Route::post('/assign-permissions', [RoleController::class, 'assignPermissions'])
                ->name('assign.permissions')
                ->middleware(['role_or_permission:manage_roles']);

            // Feature Pricing Manager (Task 4.1)
            Route::prefix('pricing')->name('pricing.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\FeaturePricingController::class, 'index'])
                    ->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\FeaturePricingController::class, 'store'])
                    ->name('store');
                Route::put('/{id}', [\App\Http\Controllers\Admin\FeaturePricingController::class, 'update'])
                    ->name('update');
                Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\FeaturePricingController::class, 'toggleActive'])
                    ->name('toggle');
            });

            // Feature Promotions Manager (Task 4.2)
            Route::prefix('promotions')->name('promotions.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\FeaturePromotionController::class, 'index'])
                    ->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\FeaturePromotionController::class, 'store'])
                    ->name('store');
                Route::post('/{id}/activate', [\App\Http\Controllers\Admin\FeaturePromotionController::class, 'activate'])
                    ->name('activate');
                Route::post('/{id}/deactivate', [\App\Http\Controllers\Admin\FeaturePromotionController::class, 'deactivate'])
                    ->name('deactivate');
            });

            // Egili Management (Task 4.3 - SuperAdmin only)
            Route::prefix('egili')->name('egili.')->middleware('role:superadmin')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\EgiliManagementController::class, 'index'])
                    ->name('index');
                Route::post('/grant-lifetime', [\App\Http\Controllers\Admin\EgiliManagementController::class, 'grantLifetime'])
                    ->name('grant.lifetime');
                Route::post('/grant-gift', [\App\Http\Controllers\Admin\EgiliManagementController::class, 'grantGift'])
                    ->name('grant.gift');
            });

            // Featured EGI Calendar (Task 4.4)
            Route::prefix('featured')->name('featured.')->group(function () {
                Route::get('/calendar', [\App\Http\Controllers\Admin\FeaturedCalendarController::class, 'calendar'])
                    ->name('calendar');
                Route::get('/pending', [\App\Http\Controllers\Admin\FeaturedCalendarController::class, 'pending'])
                    ->name('pending');
                Route::post('/{id}/approve', [\App\Http\Controllers\Admin\FeaturedCalendarController::class, 'approve'])
                    ->name('approve');
                Route::post('/{id}/reject', [\App\Http\Controllers\Admin\FeaturedCalendarController::class, 'reject'])
                    ->name('reject');
            });

            // Consumption Ledger Dashboard (Granular Tracking)
            Route::prefix('consumption')->name('consumption.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\ConsumptionLedgerController::class, 'summary'])
                    ->name('summary');
                Route::get('/by-feature/{featureCode?}', [\App\Http\Controllers\Admin\ConsumptionLedgerController::class, 'byFeature'])
                    ->name('by-feature');
                Route::get('/by-user/{userId?}', [\App\Http\Controllers\Admin\ConsumptionLedgerController::class, 'byUser'])
                    ->name('by-user');
                Route::get('/entry/{id}', [\App\Http\Controllers\Admin\ConsumptionLedgerController::class, 'details'])
                    ->name('entry-detail');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Collections Management Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('collections')->group(function () {
            // Read collection permission
            Route::middleware('collection_can:read_collection')->group(function () {
                Route::get('/carousel', CollectionCarousel::class)
                    ->name('collections.carousel');
            });

            // View collection header permission
            Route::middleware('collection_can:view_collection_header')->group(function () {

                Route::get('/{id}/members', CollectionUserMember::class)
                    ->name('collections.collection_user');
            });

            // Create collection permission

            Route::post('/create', [CollectionsController::class, 'create'])
                ->name('collections.create');


            // Add team member permission
            Route::middleware('collection_can:add_team_member')->group(function () {
                Route::delete('/{id}/invitations/{invitationId}', [CollectionUserMember::class, 'deleteProposalInvitation'])
                    ->name('invitations.delete');
            });

            // Delete wallet permission
            Route::middleware('collection_can:delete_wallet')->group(function () {
                Route::delete('/{id}/wallets/{walletId}', [CollectionUserMember::class, 'deleteProposalWallet'])
                    ->name('wallets.delete');
            });

            // Create wallet permission
            Route::middleware('collection_can:create_wallet')->group(function () {
                Route::post('/{id}/wallets/create', [NotificationWalletRequestController::class, 'requestCreateWallet'])
                    ->name('wallets.create')
                    ->middleware('check.pending.wallet');
            });

            // Update wallet permission
            Route::middleware('collection_can:update_wallet')->group(function () {
                Route::post('/{id}/wallets/update', [NotificationWalletRequestController::class, 'requestUpdateWallet'])
                    ->name('wallets.update')
                    ->middleware('check.pending.wallet');

                Route::post('/{id}/wallets/donation', [NotificationWalletRequestController::class, 'requestDonation'])
                    ->name('wallets.donation')
                    ->middleware('check.pending.wallet');
            });
        });
    });

/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/
Route::prefix('notifications')->group(function () {
    // Route specifiche devono essere PRIMA di quelle parametrizzate
    Route::get('/head-thumbnails', [NotificationWalletResponseController::class, 'fetchHeadThumbnailList'])
        ->name('head.thumbnails.list');

    // TEST: endpoint semplice per debugging
    Route::get('/test-ajax', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'is_ajax' => $request->ajax(),
            'user_id' => Auth::id(),
            'authenticated' => Auth::check(),
            'message' => 'Test endpoint funziona'
        ]);
    });

    // TEST: endpoint senza autenticazione
    Route::get('/test-no-auth', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'is_ajax' => $request->ajax(),
            'message' => 'Test endpoint senza auth funziona',
            'headers' => $request->headers->all()
        ]);
    });

    // Add route to mark notification as read - deve essere prima di {id}
    Route::post('{id}/read', function ($id) {
        $user = App\Helpers\FegiAuth::user();
        if ($user) {
            $notification = $user->customNotifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['error' => 'Not found'], 404);
    })->name('notifications.read');

    // Route parametrizzate ALLA FINE
    Route::get('{id}/details', [NotificationDetailsController::class, 'show'])
        ->name('notifications.details');

    // Add alias for notification badge component - ULTIMA route parametrizzata
    Route::get('{id}', [NotificationDetailsController::class, 'show'])
        ->name('notifications.show');

    // Wallet notifications
    Route::prefix('wallet')->group(function () {
        Route::post('/response', [NotificationWalletResponseController::class, 'response'])
            ->name('notifications.wallets.response');
        Route::post('/archive', [NotificationWalletResponseController::class, 'notificationArchive'])
            ->name('notifications.wallets.notificationArchive');
    });

    // Commerce notifications
    Route::prefix('commerce')->group(function () {
        Route::post('/shipped', [\App\Http\Controllers\Notifications\Commerce\NotificationCommerceResponseController::class, 'handleShipped'])
            ->name('notifications.commerce.shipped');
    });

    // Invitation notifications
    Route::prefix('invitation')->group(function () {
        Route::post('/response', [NotificationInvitationResponseController::class, 'response'])
            ->name('notifications.invitations.response');

        Route::post('/archive', [NotificationInvitationResponseController::class, 'notificationArchive'])
            ->name('notifications.invitations.notificationArchive');
    });

    // Reservation notifications
    Route::prefix('reservation')->group(function () {
        Route::post('/response', [NotificationReservationResponseController::class, 'response']);
        Route::post('/archive', [NotificationReservationResponseController::class, 'notificationArchive']);
        Route::get('/details', [NotificationReservationResponseController::class, 'getDetails']);
        Route::get('/ranking', [NotificationReservationResponseController::class, 'getRanking']);
    });
});


// ===================================================================
// 📖 BIOGRAPHY DISPLAY ROUTES
// ===================================================================
/**
 * Biography listing page
 * Public access with authentication awareness
 */
Route::get('/biographies', [BiographyWebController::class, 'index'])
    ->name('biography.index');

/**
 * Biography detail page with slug-based routing
 * Access control handled in controller (public vs private biographies)
 */
Route::get('/biographies/{biography:slug}', [BiographyWebController::class, 'show'])
    ->name('biography.public.show')
    ->where('biography', '[a-z0-9\-]+'); // SEO-friendly slug

/*
|--------------------------------------------------------------------------
| Biography Routes (FlorenceEGI Brand Compliant)
|--------------------------------------------------------------------------
|
| User biography management and public viewing
| Authentication: Required for management, public for viewing
| Version: 1.0.0 (FlorenceEGI - Biography Integration)
|
*/

// Fuori da auth per compatibilità legacy (da rimuovere in futuro)
Route::post('/user/preferred-currency', [App\Http\Controllers\Api\UserPreferenceController::class, 'updatePreferredCurrency'])
    ->name('user.currency.update.legacy');

// User currency preference routes
Route::get('/user/preferences/currency', [App\Http\Controllers\Api\CurrencyController::class, 'getUserPreference'])
    ->name('user.currency.get');


Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Multi-Currency User Preferences (WEB Routes)
    |--------------------------------------------------------------------------
    |
    | Internal routes called by the frontend components via AJAX.
    | Uses session authentication (not API tokens).
    |
    */
    Route::put('/user/preferences/currency', [App\Http\Controllers\Api\CurrencyController::class, 'updateUserPreference'])
        ->name('user.currency.update');


    // Biography management (user's own biographies)
    Route::get('/biography/manage', [App\Http\Controllers\Web\BiographyController::class, 'manage'])
        ->name('biography.manage');

    // Unified create route - usa la stessa vista di edit
    Route::get('/biography/create', [App\Http\Controllers\Web\BiographyController::class, 'create'])
        ->name('biography.create');
    Route::post('/biography/create', [App\Http\Controllers\Web\BiographyController::class, 'store'])
        ->name('biography.store');

    // Edit route - vista unificata
    Route::get('/biography/{biography}/edit', [App\Http\Controllers\Web\BiographyController::class, 'edit'])
        ->name('biography.edit');
    Route::put('/biography/{biography}', [App\Http\Controllers\Web\BiographyController::class, 'update'])
        ->name('biography.update');

    Route::get('/biography/view', [App\Http\Controllers\Web\BiographyController::class, 'viewOwn'])
        ->name('biography.view');

    // CRUD Capitoli Biografia
    Route::post('/biography/{biography}/chapters', [App\Http\Controllers\Web\BiographyChapterController::class, 'store'])
        ->name('biography.chapters.store');
    Route::put('/biography/{biography}/chapters/{chapter}', [App\Http\Controllers\Web\BiographyChapterController::class, 'update'])
        ->name('biography.chapters.update');
    Route::delete('/biography/{biography}/chapters/{chapter}', [App\Http\Controllers\Web\BiographyChapterController::class, 'destroy'])
        ->name('biography.chapters.destroy');
    Route::get('/biography/{biography}/chapters/{chapter}', [App\Http\Controllers\Web\BiographyChapterController::class, 'show'])
        ->name('biography.chapters.show');
});

// Public biography viewing (no auth required)
// Route::get('/biography/{user}', [App\Http\Controllers\Web\BiographyWebController::class, 'show'])
//     ->name('biography.user.show');

// Biography media upload route
Route::post('/biography/upload-media', [App\Http\Controllers\Web\BiographyController::class, 'uploadMedia'])
    ->name('biography.upload-media')
    ->middleware('auth');

// Biography remove media route
Route::delete('/biography/remove-media', [App\Http\Controllers\Web\BiographyController::class, 'removeMedia'])
    ->name('biography.remove-media')
    ->middleware('auth');

// Biography chapter media routes
Route::post('/biography/chapters/{chapter}/media', [App\Http\Controllers\Web\BiographyChapterController::class, 'uploadMedia'])
    ->name('biography.chapters.media.upload')
    ->middleware('auth');

Route::delete('/biography/chapters/{chapter}/remove-media', [App\Http\Controllers\Web\BiographyChapterController::class, 'removeMedia'])
    ->name('biography.chapters.remove-media')
    ->middleware('auth');
Route::delete('/biography/chapters/{chapter}/media', [App\Http\Controllers\Web\BiographyChapterController::class, 'removeMedia'])
    ->name('biography.chapters.media.remove')
    ->middleware('auth');

// Biography set avatar route
Route::post('/biography/set-avatar', [App\Http\Controllers\Web\BiographyController::class, 'setAvatar'])
    ->name('biography.set-avatar')
    ->middleware('auth');

// Biography delete route (web version for session auth)
Route::delete('/biography/{biography}', [App\Http\Controllers\Web\BiographyController::class, 'destroy'])
    ->name('biography.destroy')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Biography Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/biography.php';

Route::get('/info', function () {
    // Controlliamo sia la configurazione caricata che la funzione di sistema
    echo 'sys_get_temp_dir(): ' . sys_get_temp_dir() . '<br>';
    echo 'ini_get("upload_tmp_dir"): ' . ini_get('upload_tmp_dir');
});

/*
|--------------------------------------------------------------------------
| Natan Assistant Routes
|--------------------------------------------------------------------------
*/

Route::get('/why-cant-buy-egis', function () {
    return view('info.why-cant-buy-egis');
})->name('info.why-cant-buy-egis');


/*
|--------------------------------------------------------------------------
| Reservation, configuration and like routes
|--------------------------------------------------------------------------
*/

// Certificate routes
Route::prefix('egi-certificates')->name('egi-certificates.')->group(function () {
    Route::get('/{uuid}', [EgiReservationCertificateController::class, 'show'])
        ->name('show');
    Route::get('/{uuid}/download', [EgiReservationCertificateController::class, 'download'])
        ->name('download');
    Route::get('/{uuid}/verify', [EgiReservationCertificateController::class, 'verify'])
        ->name('verify');
    Route::get('/egi/{egiId}', [EgiReservationCertificateController::class, 'listByEgi'])
        ->name('list-by-egi');
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // User certificates
    Route::get('/my-certificates', [EgiReservationCertificateController::class, 'listByUser'])
        ->name('my-certificates');

    // Post-mint certificate generation (AJAX endpoint)
    Route::post('/mint/{egiId}/certificate/generate', [EgiReservationCertificateController::class, 'generatePostMintCertificate'])
        ->name('mint.certificate.generate');
});


Route::prefix('notifications/{notification}/gdpr')
    ->name('notifications.gdpr.')
    ->group(function () {

        // // Rotta per la conferma semplice (rate limit standard)
        Route::patch('/confirm', [GdprNotificationResponseController::class, 'confirm'])->name('confirm');

        // Rotta per la revoca semplice (rate limit standard)
        Route::patch('/revoke', [GdprNotificationResponseController::class, 'revoke'])->name('revoke');

        // Fortino Digitale #2: Rate Limiting restrittivo per l'azione di sicurezza
        // Permette massimo 3 chiamate ogni ora per prevenire abusi del protocollo di allerta.
        Route::patch('/disavow', [GdprNotificationResponseController::class, 'disavow'])
            ->name('disavow')
            ->middleware('throttle:3,60');
    });

Route::prefix('api')->name('api.')->group(function () {
    // Reservation API endpoints
    Route::post('/egis/{egiId}/reserve', [ReservationController::class, 'apiReserve'])
        ->name('egis.reserve');
    Route::delete('/reservations/{id}', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::get('/my-reservations', [ReservationController::class, 'listUserReservations'])
        ->name('my-reservations');
    Route::get('/egis/{egiId}/reservation-status', [ReservationController::class, 'getEgiReservationStatus'])
        ->name('egis.reservation-status');
    Route::get('/reservations/egi/{egiId}/history', [ReservationController::class, 'getReservationHistory'])
        ->name('reservations.history');

    // Route API per status prenotazioni (existing)
    Route::get('/egis/{egiId}/reservation-status-old', [ReservationController::class, 'getReservationStatus']);
    Route::get('/reservations/egi/{egiId}/history-old', [ReservationController::class, 'getReservationHistory']);
    Route::get('/egis/{egiId}/modal-info', [ReservationController::class, 'getEgiModalInfo'])
        ->name('egis.modal-info');

    // Route API per statistiche Payment Distribution
    Route::get('/stats/global', [App\Http\Controllers\Api\PaymentDistributionStatsController::class, 'getGlobalStats']);
    Route::get('/stats/collection/{collectionId}', [App\Http\Controllers\Api\PaymentDistributionStatsController::class, 'getCollectionStats']);

    // Like/Unlike routes
    Route::post('/collections/{collectionId}/toggle-like', [LikeController::class, 'toggleCollectionLike'])
        ->name('toggle.collection.like');

    Route::post('/egis/{egi}/toggle-like', [LikeController::class, 'toggleEgiLike'])
        ->name('toggle.egi.like'); // Like/Unlike routes
    // API di configurazione
    Route::get('/app-config', [App\Http\Controllers\Api\AppConfigController::class, 'getAppConfig'])
        ->name('app.config');
});

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
*/
// Debug routes
Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/debug/livewire/{component}', function ($component) {
    return Livewire::test($component)->render();
});

Route::get('/session', function () {
    dd((session()->all()));
});

// CSRF refresh
Route::get('/api/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
});

// Translations JSON endpoint
Route::get('/translations.json', function () {
    $translations = [
        'notification' => [
            'no_notifications' => __('notification.no_notifications'),
            'select_notification' => __('notification.select_notification'),
            'notification_list_error' => __('collection.wallet.notification_list_error'),
        ],
        'collection' => [
            'wallet' => [
                'donation' => __('collection.wallet.donation'),
                'donation_success' => __('collection.wallet.donation_success'),
                'accept' => __('label.accept'),
                'decline' => __('label.decline'),
                'archived' => __('label.archived'),
                'save' => __('label.save'),
                'cancel' => __('label.cancel'),
                'address' => __('collection.wallet.address'),
                'royalty_mint' => __('collection.wallet.royalty_mint'),
                'royalty_rebind' => __('collection.wallet.royalty_rebind'),
                'confirmation_title' => __('collection.wallet.confirmation_title'),
                'confirmation_text' => __('collection.wallet.confirmation_text', ['walletId' => ':walletId']),
                'confirm_delete' => __('collection.wallet.confirm_delete'),
                'cancel_delete' => __('collection.wallet.cancel_delete'),
                'deletion_error' => __('collection.wallet.deletion_error'),
                'deletion_error_generic' => __('collection.wallet.deletion_error_generic'),
                'create_the_wallet' => __('collection.wallet.create_the_wallet'),
                'update_the_wallet' => __('collection.wallet.update_the_wallet'),
                'address_placeholder' => __('collection.wallet.address_placeholder'),
                'royalty_mint_placeholder' => __('collection.wallet.royalty_mint_placeholder'),
                'royalty_rebind_placeholder' => __('collection.wallet.royalty_rebind_placeholder'),
                'success_title' => __('collection.wallet.success_title'),
                'creation_success_detail' => __('collection.wallet.creation_success_detail'),
                'validation' => [
                    'address_required' => __('collection.wallet.validation.address_required'),
                    'mint_invalid' => __('collection.wallet.validation.mint_invalid'),
                    'rebind_invalid' => __('collection.wallet.validation.rebind_invalid'),
                ],
                'error' => [
                    'error_title' => __('errors.error'),
                    'creation_error_generic' => __('collection.wallet.creation_error_generic'),
                    'creation_error' => __('collection.wallet.creation_error'),
                    'permission_denied' => __('collection.wallet.permission_denied'),
                ],
                'creation_success' => __('collection.wallet.creation_success'),
            ],
            'invitation' => [
                'confirmation_title' => __('collection.invitation.confirmation_title'),
                'confirmation_text' => __('collection.invitation.confirmation_text', ['invitationId' => ':invitationId']),
                'confirm_delete' => __('collection.invitation.confirm_delete'),
                'cancel_delete' => __('collection.invitation.cancel_delete'),
                'deletion_error' => __('collection.invitation.deletion_error'),
                'deletion_error_generic' => __('collection.invitation.deletion_error_generic'),
                'create_invitation' => __('collection.invitation.create_invitation'),
            ]
        ]
    ];

    return response()->json($translations);
});

// Enums constants endpoint
Route::get('/js/enums', function (Request $request) {
    // Cache for 1 hour (3600 seconds) as Enums only change on deployment
    $enums = Cache::remember('js_enums_json', 3600, function () {
        return [
            'NotificationStatus' => collect(NotificationStatus::cases())->mapWithKeys(fn($enum) => [$enum->name => $enum->value])
        ];
    });

    return response()->json($enums);
});

// External API proxy
Route::get('/api/quote', function () {
    $response = Http::get('https://zenquotes.io/api/random');
    return response($response->body())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*');
});

Route::get('/under-construction/{key}', [App\Http\Controllers\UnderConstructionController::class, 'show'])->name('under_construction');

Route::post('/api/assistant/auto-open', [App\Http\Controllers\AssistantController::class, 'setAutoOpen']);

// Test route for HEIC/HEIF debugging - DEVELOPMENT ONLY
if (app()->environment(['local', 'development'])) {
    Route::post('/test/heic-upload', function (Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Log::info('[HEIC Test] Starting test upload', [
            'files' => $request->allFiles(),
            'has_file' => $request->hasFile('file'),
            'user_agent' => $request->userAgent()
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'error' => 'No file uploaded',
                'debug' => [
                    'all_files' => $request->allFiles(),
                    'all_input' => $request->all()
                ]
            ], 400);
        }

        $file = $request->file('file');

        $debug = [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'guessed_extension' => $file->guessExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'is_valid' => $file->isValid(),
            'error' => $file->getError(),
            'real_path' => $file->getRealPath(),
            'path' => $file->path(),
        ];

        // Check file content start (for debugging fake files)
        if ($file->isValid() && $file->getRealPath()) {
            $content = file_get_contents($file->getRealPath(), false, null, 0, 20);
            $debug['content_hex'] = bin2hex($content);
            $debug['content_preview'] = substr($content, 0, 10);
        }

        // Check config
        $config = [
            'allowed_extensions' => config('AllowedFileType.collection.allowed_extensions'),
            'allowed_mime_types' => config('AllowedFileType.collection.allowed_mime_types'),
            'max_size' => config('AllowedFileType.collection.max_size')
        ];

        \Illuminate\Support\Facades\Log::info('[HEIC Test] File analysis complete', [
            'debug' => $debug,
            'config' => $config
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File analyzed successfully',
            'debug' => $debug,
            'config' => $config,
            'validation' => [
                'extension_allowed' => in_array(strtolower($file->getClientOriginalExtension()), $config['allowed_extensions']),
                'mime_allowed' => in_array($file->getMimeType(), $config['allowed_mime_types']),
                'size_ok' => $file->getSize() <= $config['max_size']
            ]
        ]);
    });

    // Test page for HEIC upload
    Route::get('/test/heic', function () {
        return view('test.heic-test');
    });
}

/*
|--------------------------------------------------------------------------
| Currency User Preferences (Web Routes with CSRF)
|--------------------------------------------------------------------------
|
| Routes web per la gestione delle preferenze currency utente.
| Usano autenticazione web standard con protezione CSRF.
|
*/
Route::middleware(['web', 'auth'])->prefix('user/preferences')->name('web.user.preferences.')->group(function () {
    Route::get('/currency', [App\Http\Controllers\Api\CurrencyController::class, 'getUserPreference'])
        ->name('currency.get');
    Route::put('/currency', [App\Http\Controllers\Api\CurrencyController::class, 'updateUserPreference'])
        ->name('currency.update');

    // Collection badge data for authenticated users
    Route::get('/current-collection', [App\Http\Controllers\Api\UserPreferenceController::class, 'getCurrentCollection'])
        ->name('current-collection.get');
});

// Route temporanea per testare il cookie banner
Route::get('/test-cookie-banner', function () {
    return view('test-cookie-banner');
})->name('test.cookie.banner');

// Route temporanea per testare il cookie banner in inglese
Route::get('/test-cookie-banner-en', function () {
    app()->setLocale('en');
    return view('test-cookie-banner');
})->name('test.cookie.banner.en');

// Route temporanea per testare il cookie banner con debug
Route::get('/test-cookie-banner-debug', function () {
    return view('test-cookie-banner-debug');
})->name('test.cookie.banner.debug');

/*
|--------------------------------------------------------------------------
| Trait Image Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing trait images with Spatie Media Library.
| Handles upload, deletion, and information retrieval for trait images.
|
*/
Route::middleware(['web', 'auth'])->prefix('traits')->name('traits.')->group(function () {
    // Upload image for a trait
    Route::post('/upload-image', [App\Http\Controllers\TraitImageController::class, 'uploadImage'])
        ->name('upload-image');

    // Delete image for a specific trait
    Route::delete('/{trait}/delete-image', [App\Http\Controllers\TraitImageController::class, 'deleteImage'])
        ->name('delete-image');

    // Get image information for a specific trait
    Route::get('/{trait}/image-info', [App\Http\Controllers\TraitImageController::class, 'getImageInfo'])
        ->name('image-info');
});

/*
|--------------------------------------------------------------------------
| CoA Vocabulary Routes (Internal)
|--------------------------------------------------------------------------
|
| Routes web interne per la gestione del vocabolario CoA traits.
| Utilizzate dalla modale traits nel CRUD panel EGI.
|
*/

// Test route senza middleware
Route::get('/vocabulary-test', function () {
    return response()->json(['message' => 'Test senza middleware', 'time' => now()]);
})->withoutMiddleware('auth');

// Public vocabulary routes (no auth required)
Route::prefix('vocabulary')->name('vocabulary.')->withoutMiddleware('auth')->group(function () {
    // Get categories for modal tabs
    Route::get('/categories', [App\Http\Controllers\VocabularyController::class, 'getCategories'])
        ->name('categories');

    // Get groups by category for modal group selection
    Route::get('/category/{category}/groups', [App\Http\Controllers\VocabularyController::class, 'getGroupsByCategory'])
        ->name('category.groups');

    // Get terms by category and group for modal content
    Route::get('/category/{category}/group/{group}', [App\Http\Controllers\VocabularyController::class, 'getTermsByGroup'])
        ->name('category.group');

    // Get terms by category for modal content (legacy, for backwards compatibility)
    Route::get('/category/{category}', [App\Http\Controllers\VocabularyController::class, 'getByCategory'])
        ->name('category');

    // Search terms (AJAX endpoint for modal search)
    Route::get('/search', [App\Http\Controllers\VocabularyController::class, 'search'])
        ->name('search');

    // Debug search endpoint
    Route::get('/debug-search', [App\Http\Controllers\VocabularyController::class, 'debugSearch'])
        ->name('debug-search');
});

// Authenticated vocabulary management routes
Route::middleware(['auth'])->prefix('vocabulary')->name('vocabulary.admin.')->group(function () {
    // Add any admin-only vocabulary routes here if needed
});

/*
|--------------------------------------------------------------------------
| EGI CoA Traits Routes
|--------------------------------------------------------------------------
|
| Routes per la gestione dei traits CoA associati agli EGI.
| Utilizzate dal sistema di gestione vocabulary traits.
|
*/

Route::middleware(['auth'])->name('egi.coa-traits.')->group(function () {
    // Update CoA traits for an EGI
    Route::put('/egi/{egi}/coa-traits', [App\Http\Controllers\CoaEgiTraitsController::class, 'update'])
        ->name('update');

    // Get CoA traits for an EGI (AJAX)
    Route::get('/egi/{egi}/coa-traits', [App\Http\Controllers\CoaEgiTraitsController::class, 'show'])
        ->name('show');

    // Delete CoA traits for an EGI
    Route::delete('/egi/{egi}/coa-traits', [App\Http\Controllers\CoaEgiTraitsController::class, 'destroy'])
        ->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Certificate of Authenticity (CoA) Routes
|--------------------------------------------------------------------------
|
| Routes per il sistema CoA integrato nella piattaforma FlorenceEGI.
| Include funzionalità base e Pro con verifica pubblica, gestione
| certificati, annessi, addendum e amministrazione.
|
*/

require __DIR__ . '/coa.php';

/*
|--------------------------------------------------------------------------
| PA Acts Tokenization Routes
|--------------------------------------------------------------------------
|
| Routes per il sistema di tokenizzazione atti PA su blockchain Algorand.
| Include upload PDF firmati QES, gestione atti, e verifica pubblica.
|
| Access (authenticated): role:pa_entity (Pubbliche Amministrazioni)
| Access (public): verify/{public_code} (no auth - verifica pubblica)
|
| Features:
| - Upload PDF firmati QES/PAdES
| - Validazione firma qualificata
| - Ancoraggio blockchain Algorand (batch via Merkle tree)
| - Verifica pubblica autenticità documento
| - QR code per verifica mobile
|
| Workflow:
| 1. PA upload → PaActUploadController::handleUpload()
| 2. PA index → PaActController::index() (lista atti)
| 3. PA detail → PaActController::show() (dettaglio atto)
| 4. Public verify → PaActPublicController::verify() (verifica pubblica)
|
*/

use App\Http\Controllers\PaActs\PaActUploadController;
use App\Http\Controllers\PaActs\PaActController;
use App\Http\Controllers\PaActs\PaActPublicController;

// PA Acts Routes (Authenticated PA entities only)
// Route::prefix('pa/acts')
//     ->middleware(['auth', 'role:pa_entity'])
//     ->group(function () {
//         Route::get('/', [PaActController::class, 'index'])->name('pa.acts.index');
//         Route::get('/upload', [PaActUploadController::class, 'showUploadForm'])->name('pa.acts.upload');
//         Route::post('/upload', [PaActUploadController::class, 'handleUpload'])->name('pa.acts.upload.post');
//         Route::get('/{egi}', [PaActController::class, 'show'])->name('pa.acts.show');
//     });

// Public Verification (NO authentication, rate limited)
Route::get('/verify/{public_code}', [PaActPublicController::class, 'verify'])
    ->middleware(['throttle:60,1'])
    ->name('verify.act');

/*
|--------------------------------------------------------------------------
| PA/ENTERPRISE ROUTES
|--------------------------------------------------------------------------
|
| Routes per il sistema PA/Enterprise: dashboard, heritage management,
| CoA display istituzionale, e gestione ispettori.
|
| Access: role:pa_entity (Pubbliche Amministrazioni)
| Features: KPI dashboard, heritage list/detail, CoA verification
|
*/

require __DIR__ . '/pa-enterprise.php';