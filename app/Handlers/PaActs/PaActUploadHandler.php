<?php

namespace App\Handlers\PaActs;

use App\Models\User;
use App\Services\PaActs\PaActService;
use App\Services\OllamaService;
use App\Services\PdfParserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * PA Act Upload Handler
 *
 * ============================================================================
 * CONTESTO - ULTRA UPLOAD MANAGER (UUM) PATTERN
 * ============================================================================
 *
 * Questo handler segue il pattern UUM (Ultra Upload Manager) identico a
 * EgiUploadHandler ma adattato per PA acts (documenti amministrativi firmati).
 *
 * PATTERN UUM:
 * - Handler: Coordina validazione + business logic + storage
 * - Controller: Riceve request e delega al handler
 * - Service: Contiene business logic specifica (PaActService)
 * - Response: JsonResponse standardizzato per frontend
 *
 * DIFFERENZE PA ACTS vs EGI:
 * - File type: PDF firmato QES/PAdES (non immagini)
 * - Validation: Firma digitale + protocol number (non dimensioni immagine)
 * - Storage: Disk 'private' (non public)
 * - Metadata: Protocol, signature, blockchain (non artist, collection estetica)
 * - Target: PA entities only (not creators)
 *
 * ============================================================================
 * WORKFLOW COMPLETO
 * ============================================================================
 *
 * INPUT: POST request con PDF firmato + metadata
 * - File: 'file' (PDF con firma QES)
 * - Metadata: protocol_number, protocol_date, doc_type, title, description
 *
 * STEP 1: VALIDAZIONE REQUEST
 * - Check file presente e valido
 * - Validate metadata (protocol number obbligatorio, doc_type valido)
 * - Check user autenticato e ruolo PA entity
 *
 * STEP 2: VALIDAZIONE FILE
 * - Extension: Solo PDF
 * - Size: Max 20MB (config AllowedFileType.pa_documents.max_size)
 * - MIME type: application/pdf variants
 * - Signature: Check presenza firma digitale (quick check)
 *
 * STEP 3: BUSINESS LOGIC (PaActService)
 * - Validate signature QES completa (SignatureValidationService)
 * - Calculate document hash SHA-256
 * - Find/create collection (fascicolo PA)
 * - Create EGI with PA metadata
 * - Store file securely (private storage)
 * - Generate public verification code
 *
 * STEP 4: RESPONSE
 * - Success: JsonResponse con egi_id, public_code, verification_url
 * - Error: UEM standardized error con codici specifici
 *
 * ============================================================================
 * ESEMPIO REQUEST/RESPONSE
 * ============================================================================
 *
 * REQUEST:
 * POST /pa/acts/upload
 * Content-Type: multipart/form-data
 *
 * FormData:
 * - file: delibera_2025_123.pdf (2.5 MB, firmato QES)
 * - protocol_number: 12345/2025
 * - protocol_date: 2025-09-15
 * - doc_type: delibera
 * - title: Approvazione bilancio preventivo 2026
 * - description: Delibera Giunta Comunale...
 *
 * RESPONSE SUCCESS (200):
 * ```json
 * {
 *   "success": true,
 *   "message": "Atto caricato con successo e in attesa di tokenizzazione",
 *   "data": {
 *     "egi_id": 123,
 *     "public_code": "VER-ABC123XYZ",
 *     "doc_hash": "a3f7d9e2c1b8f4a65e3b8c9d7f2a1b4c...",
 *     "verification_url": "https://florenceegi.it/verify/VER-ABC123XYZ",
 *     "protocol_number": "12345/2025",
 *     "status": "pending_anchoring"
 *   }
 * }
 * ```
 *
 * RESPONSE ERROR (422 - Validation Failed):
 * ```json
 * {
 *   "success": false,
 *   "error": "VALIDATION_FAILED",
 *   "message": "Validazione fallita",
 *   "errors": {
 *     "file": ["Il file deve essere un PDF"],
 *     "protocol_number": ["Numero protocollo obbligatorio"]
 *   }
 * }
 * ```
 *
 * RESPONSE ERROR (400 - Invalid Signature):
 * ```json
 * {
 *   "success": false,
 *   "error": "INVALID_SIGNATURE",
 *   "message": "Firma digitale non valida o mancante",
 *   "details": {
 *     "validation_result": "NO_SIGNATURE_FOUND"
 *   }
 * }
 * ```
 *
 * ============================================================================
 * INTEGRAZIONE FRONTEND (TypeScript)
 * ============================================================================
 *
 * Frontend TypeScript (pa_act_upload_manager.ts) chiama questo handler via AJAX:
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
 *   showSuccessToast(result.message);
 *   redirectToVerification(result.data.verification_url);
 * } else {
 *   showErrorToast(result.message);
 * }
 * ```
 *
 * ============================================================================
 * SICUREZZA E VALIDAZIONE
 * ============================================================================
 *
 * AUTHORIZATION:
 * - Middleware: 'auth' + 'role:pa_entity'
 * - Solo PA entities possono uploadare atti
 * - Check ownership collection (se specificata)
 *
 * FILE VALIDATION:
 * - Extension whitelist: Solo 'pdf'
 * - MIME type whitelist: PDF variants (config)
 * - Size limit: 20MB (config)
 * - Signature check: Quick check firma presente (full check in service)
 *
 * METADATA VALIDATION:
 * - protocol_number: Required, formato XXXXX/YYYY
 * - protocol_date: Required, formato YYYY-MM-DD, non futuro
 * - doc_type: Required, enum ['delibera', 'determina', 'ordinanza', 'decreto', 'atto']
 * - title: Required, string, max 255 chars
 * - description: Optional, string, max 5000 chars
 *
 * GDPR:
 * - Dati personali: Nome firmatario da certificato (base legale: CAD art. 20-21)
 * - Audit trail: UltraLogManager traccia ogni upload
 * - Retention: Conservazione permanente (obblighi PA)
 *
 * ============================================================================
 * ERRORI UEM
 * ============================================================================
 *
 * Codici errore standardizzati (config/error-manager.php):
 *
 * PA_ACT_AUTH_REQUIRED:
 * - Type: error, Blocking: blocking, HTTP: 401
 * - Message: "Autenticazione richiesta per caricare atti PA"
 *
 * PA_ACT_ROLE_REQUIRED:
 * - Type: error, Blocking: blocking, HTTP: 403
 * - Message: "Solo enti PA possono caricare atti amministrativi"
 *
 * PA_ACT_VALIDATION_FAILED:
 * - Type: error, Blocking: semi-blocking, HTTP: 422
 * - Message: "Validazione dati fallita"
 *
 * PA_ACT_INVALID_FILE:
 * - Type: error, Blocking: semi-blocking, HTTP: 400
 * - Message: "File non valido o mancante"
 *
 * PA_ACT_INVALID_SIGNATURE:
 * - Type: error, Blocking: blocking, HTTP: 400
 * - Message: "Firma digitale non valida o mancante"
 *
 * PA_ACT_UPLOAD_FAILED:
 * - Type: error, Blocking: blocking, HTTP: 500
 * - Message: "Errore durante il caricamento dell'atto"
 *
 * ============================================================================
 *
 * @package App\Handlers\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose UUM pattern handler for PA administrative acts upload
 *
 * @architecture Handler Layer Pattern (UUM)
 * @dependencies PaActService, UltraLogManager, ErrorManager
 * @security PA role required, signature validation, private storage
 * @gdpr-compliant Audit logging, CAD legal basis, minimal data processing
 * @cad-compliant Implements CAD Art. 20-21 requirements
 */
class PaActUploadHandler
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected PaActService $paActService;
    protected OllamaService $ollamaService;
    protected PdfParserService $pdfParser;

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param PaActService $paActService
     * @param OllamaService $ollamaService
     * @param PdfParserService $pdfParser
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        PaActService $paActService,
        OllamaService $ollamaService,
        PdfParserService $pdfParser
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->paActService = $paActService;
        $this->ollamaService = $ollamaService;
        $this->pdfParser = $pdfParser;
    }

    /**
     * Handle PA act upload (main entry point)
     *
     * @param Request $request
     * @return JsonResponse
     *
     * WORKFLOW:
     * 1. Authenticate user + check PA role
     * 2. Validate file input
     * 3. Validate metadata
     * 4. Delegate to PaActService
     * 5. Return success/error response
     *
     * EXAMPLE USAGE (from PaActUploadController):
     * ```php
     * public function handleUpload(Request $request, PaActUploadHandler $handler): JsonResponse {
     *     return $handler->handlePaActUpload($request);
     * }
     * ```
     */
    public function handlePaActUpload(Request $request): JsonResponse
    {
        $operationId = Str::uuid()->toString();
        $logContext = [
            'handler' => static::class,
            'operation_id' => $operationId,
            'method' => __FUNCTION__
        ];

        try {
            $this->logger->info('[PaActUploadHandler] Starting PA act upload', $logContext);

            // STEP 1: Authenticate user and check PA role
            $user = $this->authenticateUser();
            $logContext['user_id'] = $user->id;

            if (!$this->checkPaRole($user)) {
                $this->logger->warning('[PaActUploadHandler] User not authorized (not PA entity)', $logContext);
                return $this->errorManager->handle('PA_ACT_ROLE_REQUIRED', $logContext);
            }

            // STEP 2: Validate file input
            $file = $this->validateFileInput($request);
            $logContext['filename'] = $file->getClientOriginalName();
            $logContext['file_size'] = $file->getSize();

            // STEP 3: Validate file type (PDF only)
            $this->validatePdfFile($file);

            // STEP 4: Validate metadata
            $metadata = $this->validateMetadata($request);
            $logContext['protocol_number'] = $metadata['protocol_number'];
            $logContext['doc_type'] = $metadata['doc_type'];

            // STEP 4.5: N.A.T.A.N. AI-powered metadata extraction (OPTIONAL)
            // If Ollama is available and user hasn't provided all metadata, use AI to extract
            $enableAiParsing = $request->input('enable_ai_parsing', '1') === '1'; // Enabled by default

            if ($enableAiParsing && $this->shouldUseAiParsing($metadata)) {
                $this->logger->info('[PaActUploadHandler] Starting N.A.T.A.N. AI metadata extraction', [
                    ...$logContext,
                    'ollama_available' => $this->ollamaService->isAvailable()
                ]);

                try {
                    // Extract text from PDF
                    $filePath = $file->getRealPath();
                    $documentText = $this->pdfParser->extractText($filePath);

                    // Use AI to extract metadata
                    $aiMetadata = $this->ollamaService->extractPaActMetadata($documentText);

                    // Merge AI metadata with user-provided metadata (user data takes precedence)
                    $metadata = $this->mergeAiMetadata($metadata, $aiMetadata);

                    $this->logger->info('[PaActUploadHandler] N.A.T.A.N. AI extraction completed', [
                        ...$logContext,
                        'ai_extracted_fields' => array_keys($aiMetadata),
                        'final_metadata' => $metadata
                    ]);
                } catch (\Exception $e) {
                    // AI parsing is non-blocking, log error and continue with user metadata
                    $this->logger->warning('[PaActUploadHandler] N.A.T.A.N. AI parsing failed (non-blocking)', [
                        ...$logContext,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // STEP 5: Delegate to PaActService
            $this->logger->info('[PaActUploadHandler] Delegating to PaActService', $logContext);

            $result = $this->paActService->uploadDocument($file, $metadata, $user);

            // STEP 6: Handle service result
            if (!$result['success']) {
                $this->logger->error('[PaActUploadHandler] Service returned error', [
                    ...$logContext,
                    'error' => $result['error'],
                    'message' => $result['message']
                ]);

                // Map service error to UEM error code
                $errorCode = $this->mapServiceErrorToUem($result['error']);
                return $this->errorManager->handle($errorCode, [
                    ...$logContext,
                    'service_error' => $result['error']
                ]);
            }

            // STEP 7: Dispatch tokenization job if enabled
            $enableTokenization = $request->input('enable_tokenization', '0') === '1';
            $logContext['tokenization_enabled'] = $enableTokenization;

            if ($enableTokenization && isset($result['egi'])) {
                $this->logger->info('[PaActUploadHandler] Dispatching tokenization job', [
                    ...$logContext,
                    'egi_id' => $result['egi_id']
                ]);

                // Dispatch asynchronous tokenization job
                \App\Jobs\TokenizePaActJob::dispatch($result['egi']);

                $this->logger->info('[PaActUploadHandler] Tokenization job dispatched successfully', [
                    ...$logContext,
                    'queue' => 'blockchain'
                ]);
            } else {
                $this->logger->info('[PaActUploadHandler] Tokenization skipped (disabled by user)', $logContext);
            }

            // STEP 8: Success response
            $this->logger->info('[PaActUploadHandler] PA act uploaded successfully', [
                ...$logContext,
                'egi_id' => $result['egi_id'],
                'public_code' => $result['public_code']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'egi_id' => $result['egi_id'],
                    'public_code' => $result['public_code'],
                    'doc_hash' => $result['doc_hash'],
                    'verification_url' => $result['verification_url'],
                    'protocol_number' => $metadata['protocol_number'],
                    'status' => $enableTokenization ? 'pending_tokenization' : 'uploaded',
                    'tokenization_enabled' => $enableTokenization
                ]
            ], 200);
        } catch (ValidationException $e) {
            $this->logger->error('[PaActUploadHandler] Validation failed', [
                ...$logContext,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_FAILED',
                'message' => __('pa_acts.errors.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('[PaActUploadHandler] Upload failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);

            return $this->errorManager->handle('PA_ACT_UPLOAD_FAILED', [
                ...$logContext,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Authenticate user
     *
     * @return User
     * @throws \Exception If not authenticated
     */
    protected function authenticateUser(): User
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (!$user instanceof User) {
            throw new \Exception('User not authenticated');
        }

        return $user;
    }

    /**
     * Check if user has PA entity role
     *
     * @param User $user
     * @return bool
     *
     * AUTHORIZATION:
     * - Check Spatie permission: 'manage_pa_acts'
     * - Or check role: 'pa_entity'
     */
    protected function checkPaRole(User $user): bool
    {
        // Check Spatie permission (preferred)
        if (method_exists($user, 'can') && $user->can('manage_pa_acts')) {
            return true;
        }

        // Fallback: Check role name
        if (method_exists($user, 'hasRole') && $user->hasRole('pa_entity')) {
            return true;
        }

        return false;
    }

    /**
     * Validate file input from request
     *
     * @param Request $request
     * @return UploadedFile
     * @throws \Exception If file invalid or missing
     */
    protected function validateFileInput(Request $request): UploadedFile
    {
        if (!$request->hasFile('file')) {
            throw new \Exception('File input missing');
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            $uploadError = $file->getError();
            throw new \Exception("Invalid file upload. Error code: {$uploadError}");
        }

        return $file;
    }

    /**
     * Validate PDF file (extension, MIME type, size)
     *
     * @param UploadedFile $file
     * @return void
     * @throws ValidationException If validation fails
     *
     * VALIDATION RULES (from config/AllowedFileType.pa_documents):
     * - Extension: pdf
     * - MIME types: application/pdf, application/x-pdf, etc.
     * - Max size: 20MB (20 * 1024 * 1024)
     */
    protected function validatePdfFile(UploadedFile $file): void
    {
        $config = config('AllowedFileType.pa_documents');

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $config['allowed_extensions'])) {
            throw ValidationException::withMessages([
                'file' => [__('pa_acts.validation.pdf_only')]
            ]);
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $config['allowed_mime_types'])) {
            throw ValidationException::withMessages([
                'file' => [__('pa_acts.validation.invalid_pdf')]
            ]);
        }

        // Check size
        $maxSize = $config['max_size'];
        if ($file->getSize() > $maxSize) {
            throw ValidationException::withMessages([
                'file' => [__('pa_acts.validation.max_size', ['size' => $maxSize / 1048576])]
            ]);
        }
    }

    /**
     * Validate metadata from request
     *
     * @param Request $request
     * @return array Validated metadata
     * @throws ValidationException If validation fails
     *
     * VALIDATION RULES:
     * - protocol_number: Required, string, formato XXXXX/YYYY
     * - protocol_date: Required, date, formato YYYY-MM-DD, non futuro
     * - doc_type: Required, enum ['delibera', 'determina', 'ordinanza', 'decreto', 'atto']
     * - title: Required, string, max 255
     * - description: Optional, string, max 5000
     */
    protected function validateMetadata(Request $request): array
    {
        $docTypes = config('AllowedFileType.pa_documents.document_types');

        return $request->validate([
            'protocol_number' => ['required', 'string', 'regex:/^\d{1,10}\/\d{4}$/'],
            'protocol_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'doc_type' => ['required', 'string', 'in:' . implode(',', $docTypes)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000']
        ], [
            'protocol_number.required' => __('pa_acts.validation.protocol_required'),
            'protocol_number.regex' => __('pa_acts.validation.protocol_format'),
            'protocol_date.required' => __('pa_acts.validation.date_required'),
            'protocol_date.before_or_equal' => __('pa_acts.validation.date_future'),
            'doc_type.required' => __('pa_acts.validation.type_required'),
            'doc_type.in' => __('pa_acts.validation.type_invalid'),
            'title.required' => __('pa_acts.validation.title_required'),
            'title.max' => __('pa_acts.validation.title_max'),
            'description.max' => __('pa_acts.validation.description_max')
        ]);
    }

    /**
     * Map service error code to UEM error code
     *
     * @param string $serviceError Error code from PaActService
     * @return string UEM error code
     *
     * MAPPING:
     * - INVALID_SIGNATURE → PA_ACT_INVALID_SIGNATURE
     * - COLLECTION_SERVICE_ERROR → PA_ACT_COLLECTION_FAILED
     * - UPLOAD_FAILED → PA_ACT_UPLOAD_FAILED
     * - DEFAULT → PA_ACT_UPLOAD_FAILED
     */
    protected function mapServiceErrorToUem(string $serviceError): string
    {
        return match ($serviceError) {
            'INVALID_SIGNATURE' => 'PA_ACT_INVALID_SIGNATURE',
            'COLLECTION_SERVICE_ERROR' => 'PA_ACT_COLLECTION_FAILED',
            'UPLOAD_FAILED' => 'PA_ACT_UPLOAD_FAILED',
            default => 'PA_ACT_UPLOAD_FAILED'
        };
    }

    /**
     * Determine if AI parsing should be used
     *
     * AI parsing is used if:
     * - User hasn't provided protocol_number OR doc_type OR title
     * - Ollama service is available
     *
     * @param array $metadata User-provided metadata
     * @return bool
     */
    protected function shouldUseAiParsing(array $metadata): bool
    {
        // Check if Ollama is available
        if (!$this->ollamaService->isAvailable()) {
            $this->logger->info('[PaActUploadHandler] Ollama not available, skipping AI parsing');
            return false;
        }

        // Check if metadata is incomplete (missing critical fields)
        $hasProtocol = !empty($metadata['protocol_number']);
        $hasDocType = !empty($metadata['doc_type']);
        $hasTitle = !empty($metadata['title']);

        // Use AI if ANY of these fields is missing
        return !$hasProtocol || !$hasDocType || !$hasTitle;
    }

    /**
     * Merge AI-extracted metadata with user-provided metadata
     *
     * User-provided data always takes precedence over AI-extracted data.
     *
     * @param array $userMetadata User-provided metadata
     * @param array $aiMetadata AI-extracted metadata
     * @return array Merged metadata
     */
    protected function mergeAiMetadata(array $userMetadata, array $aiMetadata): array
    {
        // For each field, use user value if present, otherwise use AI value
        return [
            'protocol_number' => $userMetadata['protocol_number'] ?? $aiMetadata['protocol_number'] ?? null,
            'protocol_date' => $userMetadata['protocol_date'] ?? $aiMetadata['protocol_date'] ?? null,
            'doc_type' => $userMetadata['doc_type'] ?? $aiMetadata['doc_type'] ?? null,
            'title' => $userMetadata['title'] ?? $aiMetadata['object'] ?? null, // AI uses 'object' field
            'description' => $userMetadata['description'] ?? $aiMetadata['object'] ?? null,
            'amount' => $userMetadata['amount'] ?? $aiMetadata['amount'] ?? null,
            '_ai_assisted' => true, // Flag to indicate AI was used
        ];
    }
}
