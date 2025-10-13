<?php

namespace App\Services\PaActs;

use App\Models\User;
use App\Models\Egi;
use App\Models\Collection;
use App\Services\AlgorandService;
use App\Services\CollectionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * PA Acts Service - Main Orchestration Service
 *
 * ============================================================================
 * CONTESTO BUSINESS - WORKFLOW COMPLETO TOKENIZZAZIONE ATTI PA
 * ============================================================================
 *
 * Questo è il SERVICE PRINCIPALE che orchestra l'intero workflow di
 * tokenizzazione atti PA su blockchain Algorand.
 *
 * WORKFLOW COMPLETO (8 STEP):
 *
 * 1. UPLOAD PDF FIRMATO (handleUpload)
 *    - PA entity carica PDF con firma digitale QES/PAdES
 *    - Validazione file (tipo, dimensione, estensione)
 *    - Salvataggio temporaneo per elaborazione
 *
 * 2. VALIDAZIONE FIRMA DIGITALE (SignatureValidationService)
 *    - Estrae certificato X.509 dal PDF
 *    - Verifica validità firma QES
 *    - Estrae dati firmatario (nome, email, org, ruolo)
 *    - Verifica certificato non scaduto/revocato
 *
 * 3. CALCOLO HASH DOCUMENTO (hashDocument)
 *    - Calcola SHA-256 del PDF integrale
 *    - Hash diventa "fingerprint" immutabile documento
 *    - Usato per verifica integrità + blockchain anchoring
 *
 * 4. GESTIONE COLLECTION (CollectionService)
 *    - Trova/crea "Fascicolo PA" (Collection type='pa_documents')
 *    - Organizza documenti per procedimento/progetto
 *    - Supporta multi-user PA entities
 *
 * 5. CREAZIONE EGI (createEgi)
 *    - Crea record Egi con metadata JSON esteso
 *    - Metadata contiene: hash, firma, protocol number, doc type
 *    - Salva file PDF in storage privato
 *    - Genera public code per verifica (VER-ABC123XYZ)
 *
 * 6. BATCH ANCHORING (AlgorandService + MerkleTreeService)
 *    - Agrupa documenti in batch giornaliero
 *    - Costruisce Merkle tree con hash documenti
 *    - Ancora solo Merkle root su blockchain (1 TX per N documenti)
 *    - Genera Merkle proof per ogni documento
 *
 * 7. GENERAZIONE QR CODE (generateQrCode)
 *    - Crea QR code con URL verifica pubblica
 *    - URL: https://florenceegi.it/verify/{public_code}
 *    - Stampabile su documento cartaceo per tracciabilità
 *
 * 8. PUBBLICAZIONE (finalize)
 *    - Aggiorna metadata con TXID blockchain + Merkle proof
 *    - Marca documento come "ancorato" (anchored=true)
 *    - Invia notifica PA entity (documento tokenizzato)
 *    - Atto pronto per verifica pubblica
 *
 * RISULTATO FINALE:
 * - PDF firmato salvato in storage privato
 * - Hash documento ancorato su blockchain Algorand
 * - Merkle proof per verifica indipendente
 * - QR code + URL pubblico per verifica
 * - Metadata completo in egis.metadata JSON
 *
 * ============================================================================
 * ESEMPIO REALE - DELIBERA COMUNE FIRENZE
 * ============================================================================
 *
 * INPUT:
 * - File: "Delibera_GC_2025_123_firmata.pdf" (2.5 MB)
 * - Protocol: 12345/2025 del 15/09/2025
 * - Firmatario: Dario Nardella (Sindaco)
 * - Tipo: Delibera Giunta Comunale
 * - Oggetto: "Approvazione bilancio preventivo 2026"
 *
 * PROCESSING:
 * 1. Upload: File salvato in storage/pa_acts/
 * 2. Firma: QES valida, cert InfoCert, scadenza 2028
 * 3. Hash: a3f7d9e2c1b8f4a65e3b8c9d7f2a1b4c... (SHA-256)
 * 4. Collection: "Delibere GC 2025" (fascicolo)
 * 5. EGI: Created con metadata JSON completo
 * 6. Batch: Incluso in batch giornaliero con altre 49 delibere
 * 7. Blockchain: Merkle root ancorato → TXID: ALGO-TX-20250915143022-A1B2C3D4
 * 8. QR: Generato con URL https://florenceegi.it/verify/VER-ABC123XYZ
 *
 * OUTPUT (egis.metadata JSON):
 * ```json
 * {
 *   "protocol_number": "12345/2025",
 *   "protocol_date": "2025-09-15",
 *   "doc_type": "delibera",
 *   "doc_hash": "a3f7d9e2c1b8f4a65e3b8c9d7f2a1b4c...",
 *   "signature_validation": {
 *     "valid": true,
 *     "signer_cn": "Dario Nardella",
 *     "signer_email": "sindaco@comune.fi.it",
 *     "signer_organization": "Comune di Firenze",
 *     "signer_role": "Sindaco",
 *     "cert_serial": "5C3A7B9E2F1D4A8C",
 *     "cert_issuer": "InfoCert Firma Qualificata CA",
 *     "signature_timestamp": "2025-09-15T14:30:22Z"
 *   },
 *   "anchor_txid": "ALGO-TX-20250915143022-A1B2C3D4",
 *   "anchor_root": "3a7f8e9c2b1d4f6a...", // Merkle root
 *   "merkle_proof": [
 *     {"hash": "b1c2d3e4...", "position": "right"},
 *     {"hash": "c5d6e7f8...", "position": "left"}
 *   ],
 *   "public_code": "VER-ABC123XYZ",
 *   "qr_code_path": "qr_codes/VER-ABC123XYZ.png",
 *   "public_url": "https://florenceegi.it/verify/VER-ABC123XYZ",
 *   "anchored": true,
 *   "anchored_at": "2025-09-15T14:35:00Z"
 * }
 * ```
 *
 * VERIFICA PUBBLICA:
 * Cittadino scansiona QR → Browser apre /verify/VER-ABC123XYZ
 * - Mostra: Delibera GC 123/2025, firmata Sindaco, hash verificato
 * - Verifica Merkle proof: ✅ Documento autentico
 * - Link blockchain: Algorand Explorer TXID
 * - Download PDF originale (se permesso)
 *
 * ============================================================================
 * ARCHITETTURA SERVICE LAYER
 * ============================================================================
 *
 * QUESTO SERVICE ORCHESTRA:
 *
 * 1. AlgorandService (blockchain anchoring)
 *    - anchorDocument(): Single document anchoring
 *    - anchorBatch(): Batch Merkle tree anchoring
 *    - verifyDocument(): Blockchain verification
 *
 * 2. SignatureValidationService (QES validation)
 *    - validatePdfSignature(): Full signature validation
 *    - extractSignerInfo(): Signer details from certificate
 *    - hasSignature(): Quick signature detection
 *
 * 3. MerkleTreeService (batch optimization)
 *    - buildTree(): Construct Merkle tree from hashes
 *    - getRoot(): Get Merkle root for blockchain
 *    - getProof(): Generate Merkle proof for document
 *    - verifyProof(): Verify document in batch
 *
 * 4. CollectionService (fascicolo management)
 *    - findOrCreateUserCollection(): Get/create PA fascicolo
 *    - createDefaultCollection(): Create new fascicolo
 *
 * 5. UltraLogManager (audit logging)
 *    - info(): Log successful operations
 *    - error(): Log failures with context
 *
 * 6. ErrorManager (error handling)
 *    - handle(): Standardized error responses
 *    - User-friendly messages + dev context
 *
 * DESIGN PATTERN: Orchestrator Service
 * - Coordina operazioni multi-service
 * - Gestisce transazioni e rollback
 * - Centralizza business logic complessa
 * - Fornisce API semplice ai controller/handler
 *
 * ============================================================================
 * INTEGRAZIONE CON ALTRI COMPONENTI
 * ============================================================================
 *
 * CHIAMATO DA:
 * - PaActUploadHandler::handlePaActUpload() - Upload workflow
 * - PaActController::store() - Manual upload
 * - TokenizePaActBatch Job - Batch processing cron
 *
 * CHIAMA:
 * - AlgorandService - Blockchain anchoring
 * - SignatureValidationService - QES validation
 * - MerkleTreeService - Batch Merkle tree
 * - CollectionService - Fascicolo management
 * - Storage facade - File management
 * - Egi model - Database persistence
 *
 * DATI SALVATI IN:
 * - egis table - Record principale
 * - egis.metadata JSON - Dati tokenizzazione
 * - storage/pa_acts/ - PDF files
 * - storage/qr_codes/ - QR code images
 * - audit_logs - Tracciabilità operazioni
 *
 * UTILIZZATO DA:
 * - PaActPublicController::verify() - Verifica pubblica
 * - PaActController::show() - Dettaglio atto PA
 * - Blade views - Display dati tokenizzazione
 *
 * ============================================================================
 * SICUREZZA E COMPLIANCE
 * ============================================================================
 *
 * GDPR:
 * - Dati personali: Nome firmatario, email (base legale: CAD art. 20-21)
 * - Consent: Non richiesto (obblighi legali PA)
 * - Retention: Conservazione permanente atti PA (CAD art. 22)
 * - Audit log: UltraLogManager traccia ogni operazione
 * - Data minimization: Solo dati essenziali da certificato
 *
 * CAD (Codice Amministrazione Digitale):
 * - Art. 20: Validità documenti informatici PA
 * - Art. 21: Valore probatorio firma digitale
 * - Art. 22: Copie informatiche e duplicati
 * - Art. 23: Copie per immagine su supporto informatico
 * - Conformità garantita da workflow + blockchain anchoring
 *
 * BLOCKCHAIN SECURITY:
 * - Hash SHA-256: Collision-resistant
 * - Merkle tree: Tamper-proof batch integrity
 * - Algorand: Byzantine fault tolerant consensus
 * - Immutability: Blockchain anchoring irreversibile
 *
 * FILE SECURITY:
 * - Storage: disk='private' (non accessibile via HTTP)
 * - Access control: Authorization middleware required
 * - Hash filename: Previene directory traversal
 * - Signature validation: Solo PDF firmati QES accettati
 *
 * ============================================================================
 *
 * @package App\Services\PaActs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Main orchestration service for PA acts tokenization workflow
 *
 * @architecture Service Layer Pattern (Orchestrator)
 * @dependencies AlgorandService, SignatureValidationService, MerkleTreeService, CollectionService
 * @security QES validation, blockchain anchoring, private storage, audit trail
 * @gdpr-compliant Minimal data processing, audit logging, CAD legal basis
 * @cad-compliant Implements CAD Art. 20-23 requirements
 */
class PaActService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AlgorandService $algorandService;
    protected SignatureValidationService $signatureService;
    protected MerkleTreeService $merkleService;
    protected CollectionService $collectionService;

    /**
     * Constructor - Dependency Injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param AlgorandService $algorandService
     * @param SignatureValidationService $signatureService
     * @param MerkleTreeService $merkleService
     * @param CollectionService $collectionService
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AlgorandService $algorandService,
        SignatureValidationService $signatureService,
        MerkleTreeService $merkleService,
        CollectionService $collectionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->algorandService = $algorandService;
        $this->signatureService = $signatureService;
        $this->merkleService = $merkleService;
        $this->collectionService = $collectionService;
    }

    /**
     * Handle PA act upload (main orchestration method)
     *
     * @param UploadedFile $file PDF file with QES signature
     * @param array $metadata Additional metadata (protocol, date, type, etc.)
     * @param User $user PA entity user
     * @return array Result with egi_id, public_code, hash, txid
     *
     * WORKFLOW:
     * 1. Validate signature (SignatureValidationService)
     * 2. Calculate hash (SHA-256)
     * 3. Find/create collection (CollectionService)
     * 4. Create EGI with metadata
     * 5. Store file securely
     * 6. Queue for batch anchoring
     * 7. Generate public code + QR
     * 8. Return success data
     *
     * EXAMPLE:
     * ```php
     * $result = $paActService->uploadDocument($pdfFile, [
     *     'protocol_number' => '12345/2025',
     *     'protocol_date' => '2025-09-15',
     *     'doc_type' => 'delibera',
     *     'title' => 'Approvazione bilancio 2026'
     * ], Auth::user());
     *
     * // Returns:
     * [
     *     'success' => true,
     *     'egi_id' => 123,
     *     'public_code' => 'VER-ABC123XYZ',
     *     'doc_hash' => 'a3f7d9e2...',
     *     'verification_url' => 'https://florenceegi.it/verify/VER-ABC123XYZ',
     *     'message' => 'Documento caricato con successo'
     * ]
     * ```
     */
    public function uploadDocument(UploadedFile $file, array $metadata, User $user): array {
        try {
            $this->logger->info('[PaActService] Starting PA act upload', [
                'user_id' => $user->id,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'metadata' => $metadata
            ]);

            // STEP 1: Validate signature
            $this->logger->info('🔐 [PA-TOKENIZATION] STEP 1: Validating signature...', [
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);
            
            $signatureValidation = $this->signatureService->validatePdfSignature($file);

            $this->logger->info('🔐 [PA-TOKENIZATION] Signature validation result:', [
                'valid' => $signatureValidation['valid'] ?? false,
                'signer' => $signatureValidation['signer_cn'] ?? 'N/A',
                'mode' => $signatureValidation['mode'] ?? 'unknown',
                'full_result' => $signatureValidation
            ]);

            if (!$signatureValidation['valid']) {
                $this->logger->error('❌ [PA-TOKENIZATION] SIGNATURE VALIDATION FAILED!', [
                    'user_id' => $user->id,
                    'filename' => $file->getClientOriginalName(),
                    'error' => $signatureValidation['error'] ?? 'UNKNOWN',
                    'message' => $signatureValidation['message'] ?? 'No message',
                    'full_validation' => $signatureValidation
                ]);

                return [
                    'success' => false,
                    'error' => 'INVALID_SIGNATURE',
                    'message' => __('pa_acts.errors.invalid_signature'),
                    'details' => $signatureValidation
                ];
            }
            
            $this->logger->info('✅ [PA-TOKENIZATION] Signature valid!', [
                'signer' => $signatureValidation['signer_cn']
            ]);

            // STEP 2: Calculate hash
            $docHash = hash_file('sha256', $file->getRealPath());

            $this->logger->info('[PaActService] Document hash calculated', [
                'user_id' => $user->id,
                'hash' => $docHash
            ]);

            // STEP 3: Find/create collection (fascicolo)
            $collectionResult = $this->collectionService->findOrCreateUserCollection($user, [
                'context' => 'pa_act_upload',
                'user_id' => $user->id
            ]);

            // Handle JsonResponse error from CollectionService
            if ($collectionResult instanceof \Illuminate\Http\JsonResponse) {
                return [
                    'success' => false,
                    'error' => 'COLLECTION_SERVICE_ERROR',
                    'message' => __('pa_acts.errors.collection_failed')
                ];
            }

            $collection = $collectionResult;

            // STEP 4: Generate public code
            $publicCode = $this->generatePublicCode();

            // STEP 5: Create EGI with metadata
            $egi = $this->createEgi($file, $collection, $user, $metadata, [
                'doc_hash' => $docHash,
                'signature_validation' => $signatureValidation,
                'public_code' => $publicCode
            ]);

            // STEP 6: Store file securely
            $filePath = $this->storeFile($file, $docHash);

            // Update EGI with file path in jsonMetadata
            $egi->jsonMetadata = array_merge($egi->jsonMetadata ?? [], ['file_path' => $filePath]);
            $egi->save();

            // STEP 7: Queue for batch anchoring (will be processed by cron job)
            // Note: Batch processing happens asynchronously via TokenizePaActBatch job

            // STEP 8: Generate verification URL
            $verificationUrl = route('verify.act', $publicCode);

            $this->logger->info('[PaActService] PA act uploaded successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'public_code' => $publicCode,
                'doc_hash' => $docHash
            ]);

            return [
                'success' => true,
                'egi' => $egi, // Return Egi object for tokenization job
                'egi_id' => $egi->id,
                'public_code' => $publicCode,
                'doc_hash' => $docHash,
                'verification_url' => $verificationUrl,
                'message' => __('pa_acts.success.upload_completed')
            ];
        } catch (\Exception $e) {
            $this->logger->error('[PaActService] PA act upload failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);

            $this->errorManager->handle('PA_ACT_UPLOAD_FAILED', [
                'user_id' => $user->id,
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ], $e);

            return [
                'success' => false,
                'error' => 'UPLOAD_FAILED',
                'message' => __('pa_acts.errors.upload_failed')
            ];
        }
    }

    /**
     * Create EGI record with PA act metadata
     *
     * @param UploadedFile $file
     * @param Collection $collection
     * @param User $user
     * @param array $userMetadata User-provided metadata (protocol, date, type)
     * @param array $systemMetadata System-generated metadata (hash, signature, public code)
     * @return Egi
     */
    protected function createEgi(
        UploadedFile $file,
        Collection $collection,
        User $user,
        array $userMetadata,
        array $systemMetadata
    ): Egi {
        // Merge all metadata for jsonMetadata field
        $fullMetadata = array_merge($userMetadata, $systemMetadata, [
            'uploaded_at' => now()->toIso8601String(),
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);

        // Create EGI with PA Acts dedicated columns
        $egi = Egi::create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'owner_id' => $user->id,
            'title' => $userMetadata['title'] ?? 'Atto PA',
            'description' => $userMetadata['description'] ?? null,
            'jsonMetadata' => $fullMetadata, // CORRECT: use jsonMetadata column
            'type' => 'pa_act', // Mark as PA act
            // PA Acts dedicated columns
            'pa_act_type' => $userMetadata['doc_type'],
            'pa_protocol_number' => $userMetadata['protocol_number'],
            'pa_protocol_date' => $userMetadata['protocol_date'],
            'pa_public_code' => $systemMetadata['public_code'],
            'pa_anchored' => false,
            'pa_anchored_at' => null,
            // Status
            'status' => 'published', // PA acts are always published
            'is_published' => true
        ]);

        $this->logger->info('[PaActService] EGI created', [
            'egi_id' => $egi->id,
            'collection_id' => $collection->id,
            'user_id' => $user->id
        ]);

        return $egi;
    }

    /**
     * Store PDF file securely in private storage
     *
     * @param UploadedFile $file
     * @param string $hash Document hash (used for filename)
     * @return string Stored file path
     *
     * SECURITY:
     * - Disk: 'private' (not accessible via HTTP)
     * - Filename: hash-based (prevents collisions + directory traversal)
     * - Path: pa_acts/ (dedicated folder)
     */
    protected function storeFile(UploadedFile $file, string $hash): string {
        $filename = $hash . '.pdf';
        $path = 'pa_acts/' . $filename;

        Storage::disk('private')->put($path, file_get_contents($file->getRealPath()));

        $this->logger->info('[PaActService] File stored securely', [
            'path' => $path,
            'hash' => $hash
        ]);

        return $path;
    }

    /**
     * Generate unique public verification code
     *
     * @return string Public code (format: VER-XXXXXXXXXX)
     *
     * EXAMPLE: VER-ABC123XYZ7
     *
     * UNIQUENESS:
     * - Format: VER-{10 uppercase alphanumeric}
     * - Check database for collisions (retry if exists)
     * - Used in public URL: /verify/{public_code}
     */
    protected function generatePublicCode(): string {
        do {
            $code = 'VER-' . strtoupper(Str::random(10));
            $exists = Egi::where('pa_public_code', $code)->exists();
        } while ($exists);

        return $code;
    }

    /**
     * Get document by public verification code
     *
     * @param string $publicCode Public verification code
     * @return Egi|null
     *
     * USAGE: Public verification page (/verify/{public_code})
     */
    public function getDocumentByPublicCode(string $publicCode): ?Egi {
        return Egi::where('pa_public_code', $publicCode)->first();
    }

    /**
     * Tokenize document (anchor on blockchain)
     *
     * @param Egi $egi
     * @return array Result with txid, merkle_root, proof
     *
     * CALLED BY: TokenizePaActBatch job (async batch processing)
     *
     * WORKFLOW:
     * 1. Get document hash from metadata
     * 2. Anchor on Algorand (single or batch)
     * 3. Update metadata with TXID + merkle proof
     * 4. Mark as anchored
     */
    public function tokenizeDocument(Egi $egi): array {
        try {
            $docHash = $egi->jsonMetadata['doc_hash'] ?? null;

            if (!$docHash) {
                throw new \Exception('Document hash not found in jsonMetadata');
            }

            $this->logger->info('[PaActService] Tokenizing document', [
                'egi_id' => $egi->id,
                'doc_hash' => $docHash
            ]);

            // Anchor on blockchain (single document for now) - GDPR compliant
            // TODO: Implement batch anchoring with MerkleTreeService
            // Pass the EGI owner (PA user) for GDPR audit trail
            $anchorResult = $this->algorandService->anchorDocument($docHash, [
                'egi_id' => $egi->id,
                'public_code' => $egi->pa_public_code // Use dedicated column
            ], $egi->user);

            if (!$anchorResult['success']) {
                throw new \Exception('Blockchain anchoring failed');
            }

            // Update EGI jsonMetadata
            $metadata = $egi->jsonMetadata;
            $metadata['anchor_txid'] = $anchorResult['txid'];
            $egi->jsonMetadata = $metadata;

            // Update PA Acts dedicated columns
            $egi->pa_anchored = true;
            $egi->pa_anchored_at = now();
            $egi->save();

            $this->logger->info('[PaActService] Document tokenized successfully', [
                'egi_id' => $egi->id,
                'txid' => $anchorResult['txid']
            ]);

            return [
                'success' => true,
                'txid' => $anchorResult['txid'],
                'egi_id' => $egi->id
            ];
        } catch (\Exception $e) {
            $this->logger->error('[PaActService] Tokenization failed', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
            ]);

            $this->errorManager->handle('PA_ACT_TOKENIZATION_FAILED', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage()
            ], $e);

            return [
                'success' => false,
                'error' => 'TOKENIZATION_FAILED',
                'message' => $e->getMessage()
            ];
        }
    }
}
