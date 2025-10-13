<?php

namespace App\Http\Controllers\PaActs;

use App\Handlers\PaActs\PaActUploadHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * PA Act Upload Controller
 * 
 * @package App\Http\Controllers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Upload form display + upload endpoint for PA Acts
 * 
 * ROUTES:
 * - GET  /pa/acts/upload → showUploadForm() - Display form
 * - POST /pa/acts/upload → handleUpload() - Process upload
 * 
 * RESPONSE SUCCESS (200):
 * ```json
 * {
 *   "success": true,
 *   "message": "Atto caricato con successo",
 *   "data": {
 *     "egi_id": 123,
 *     "public_code": "VER-ABC123XYZ",
 *     "doc_hash": "a3f7d9e2...",
 *     "verification_url": "https://florenceegi.it/verify/VER-ABC123XYZ",
 *     "protocol_number": "12345/2025",
 *     "status": "pending_anchoring"
 *   }
 * }
 * ```
 * 
 * RESPONSE ERROR (4xx/5xx):
 * ```json
 * {
 *   "success": false,
 *   "error": "ERROR_CODE",
 *   "message": "Error message",
 *   "errors": {...} // Optional validation errors
 * }
 * ```
 * 
 * ============================================================================
 * INTEGRAZIONE FRONTEND
 * ============================================================================
 * 
 * TypeScript (pa_act_upload_manager.ts) chiama questo endpoint:
 * 
 * ```typescript
 * const formData = new FormData();
 * formData.append('file', pdfFile);
 * formData.append('protocol_number', '12345/2025');
 * formData.append('protocol_date', '2025-09-15');
 * formData.append('doc_type', 'delibera');
 * formData.append('title', 'Approvazione bilancio...');
 * 
 * const response = await fetch('/pa/acts/upload', {
 *   method: 'POST',
 *   body: formData,
 *   headers: {
 *     'X-CSRF-TOKEN': csrfToken,
 *     'Accept': 'application/json'
 *   }
 * });
 * 
 * const result = await response.json();
 * if (result.success) {
 *   showSuccessMessage(result.message);
 *   redirectTo(result.data.verification_url);
 * } else {
 *   showErrorMessage(result.message);
 *   displayValidationErrors(result.errors);
 * }
 * ```
 * 
 * ============================================================================
 * SICUREZZA
 * ============================================================================
 * 
 * MIDDLEWARE:
 * - 'auth': Richiede autenticazione
 * - 'role:pa_entity': Solo enti PA possono accedere
 * 
 * CSRF PROTECTION:
 * - Token CSRF verificato automaticamente da middleware Laravel
 * - Frontend deve inviare X-CSRF-TOKEN header
 * 
 * RATE LIMITING:
 * - Suggerito: throttle:60,1 (60 upload/minuto per PA)
 * - Configurabile in routes/web.php
 * 
 * ============================================================================
 * 
 * @package App\Http\Controllers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose UUM upload endpoint for PA administrative acts
 * 
 * @architecture Controller Layer (Thin routing + DI)
 * @dependencies PaActUploadHandler, UltraLogManager, ErrorManager
 * @middleware auth, role:pa_entity
 * @route POST /pa/acts/upload (name: pa.acts.upload)
 */
class PaActUploadController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Constructor - Dependency Injection
     * 
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        // Middleware applicato in routes/web.php
        // $this->middleware(['auth', 'role:pa_entity']);
    }

    /**
     * Show upload form
     * 
     * @return \Illuminate\View\View
     */
    public function showUploadForm()
    {
        $user = auth()->user();

        $this->logger->info('[PaActUploadController] Showing upload form', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Doc types from config
        $docTypes = [
            'delibera' => __('pa_acts.doc_types.delibera.label'),
            'determina' => __('pa_acts.doc_types.determina.label'),
            'ordinanza' => __('pa_acts.doc_types.ordinanza.label'),
            'decreto' => __('pa_acts.doc_types.decreto.label'),
            'atto' => __('pa_acts.doc_types.atto.label'),
        ];

        // Get PA entity collections (all types for now - PA can organize acts in any collection)
        // Note: Fully qualified column names to avoid ambiguity with pivot table
        // collections table has: deleted_at (NOT removed_at), status
        // collection_user pivot has: removed_at, status
        $collections = $user->collections()
            ->where('collections.status', '!=', 'removed')
            ->whereNull('collection_user.removed_at') // User hasn't been removed from collection
            ->orderBy('collections.collection_name')
            ->get();

        $this->logger->info('[PaActUploadController] Collections loaded', [
            'collections_count' => $collections->count(),
            'collections' => $collections->pluck('id', 'collection_name')->toArray()
        ]);

        // PA-specific terminology
        $collectionLabel = 'Fascicolo';

        return view('pa.acts.upload', compact('docTypes', 'collections', 'collectionLabel'));
    }

    /**
     * Handle PA act upload
     * 
     * @param Request $request
     * @param PaActUploadHandler $handler Injected by DI container
     * @return JsonResponse
     * 
     * WORKFLOW:
     * 1. Log incoming request
     * 2. Delegate to PaActUploadHandler
     * 3. Return handler response
     * 4. Catch exceptions and use ErrorManager
     * 
     * EXAMPLE USAGE (route definition):
     * ```php
     * Route::post('/pa/acts/upload', [PaActUploadController::class, 'handleUpload'])
     *     ->middleware(['auth', 'role:pa_entity'])
     *     ->name('pa.acts.upload');
     * ```
     */
    public function handleUpload(Request $request, PaActUploadHandler $handler): JsonResponse
    {
        $logContext = [
            'controller' => static::class,
            'method' => __FUNCTION__,
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ];

        try {
            $this->logger->info('🔵 [PA-TOKENIZATION-CONTROLLER] ========== START UPLOAD ==========', $logContext);
            $this->logger->info('🔵 [PA-TOKENIZATION-CONTROLLER] Request data:', [
                'has_file' => $request->hasFile('file'),
                'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'NO FILE',
                'metadata' => $request->except(['file', '_token'])
            ]);

            // Delegate to handler (contains all business logic)
            $result = $handler->handlePaActUpload($request);

            $this->logger->info('🔵 [PA-TOKENIZATION-CONTROLLER] Handler response:', [
                'status_code' => $result->getStatusCode(),
                'content' => substr($result->getContent(), 0, 500)
            ]);

            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('[PaActUploadController] Upload failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Use ErrorManager for standardized error response
            return $this->errorManager->handle('PA_ACT_UPLOAD_FAILED', [
                ...$logContext,
                'error' => $e->getMessage()
            ], $e);
        }
    }
}
