<?php

namespace Ultra\EgiModule\Handlers;

// PHP & Laravel Imports

use App\Helpers\FegiAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;
use Exception;
use LogicException;

// Application/Package Specific Imports
use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use Ultra\EgiModule\Helpers\EgiHelper;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\UploadManager\Traits\HasValidation;
use App\Traits\HasUtilitys;
use Carbon\Carbon;

// UEM Imports
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

// Service Imports - NEW DI APPROACH
use App\Services\CollectionService;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use App\Contracts\ImageOptimizationManagerInterface;
use App\Contracts\IpfsServiceInterface;

/**
 * @Oracode Handler: EgiUploadHandler (v2.0 - Service-Based Architecture)
 * Handles backend logic for EGI image uploads using proper service dependency injection.
 * Replaces trait-based approach with clean DI pattern for better testability and maintainability.
 *
 * @package     Ultra\EgiModule\Handlers
 * @version     2.0.0 // Refactored to use CollectionService, WalletService, UserRoleService
 * @author      Padmin D. Curtis (for Fabio Cherici)
 * @copyright   2025 Fabio Cherici
 * @license     MIT
 * @since       2025-05-25
 *
 * @purpose     🎯 Orchestrates complete EGI upload process using proper service layer:
 *              validation, collection management via CollectionService, file storage,
 *              metadata processing, and response generation via UEM for errors.
 *
 * @context     🧩 Instantiated via DI container with injected services (CollectionService,
 *              WalletService, UserRoleService). Operates within authenticated HTTP context.
 *              Uses proper service contracts for better abstraction.
 *
 * @state       💾 Modifies Database via service layer. Modifies File Storage. Modifies Cache.
 *              All state changes go through proper service boundaries.
 *
 * @feature     🗝️ Service-based collection management (replaces trait approach)
 * @feature     🗝️ Proper DI pattern with service interfaces
 * @feature     🗝️ Enhanced error handling with service-specific UEM codes
 * @feature     🗝️ Improved testability through service mocking
 * @feature     🗝️ Consistent architecture with other FlorenceEGI components
 * @feature     🗝️ Full Oracode 3.0 compliance with enhanced documentation
 *
 * @signal      🚦 Returns JsonResponse (200 on success, 4xx/5xx via UEM on error)
 * @signal      🚦 All service operations properly logged and error-handled
 * @signal      🚦 Service failures trigger appropriate rollback strategies
 *
 * @privacy     🛡️ GDPR-compliant through service layer abstraction
 * @privacy     🛡️ All user data handling delegated to privacy-aware services
 * @privacy     🛡️ Audit trail maintained through service interactions
 *
 * @dependency  🤝 App\Services\CollectionService - Collection lifecycle management
 * @dependency  🤝 WalletServiceInterface - Wallet operations and royalty management
 * @dependency  🤝 UserRoleServiceInterface - User role assignment
 * @dependency  🤝 ErrorManagerInterface - Centralized error handling
 * @dependency  🤝 UltraLogManager - Structured logging
 *
 * @testing     🧪 Service mocking enables isolated unit testing
 * @testing     🧪 Integration tests verify service interaction patterns
 * @testing     🧪 Error scenarios tested through service failure simulation
 *
 * @rationale   💡 Eliminates trait dependency for better architecture alignment.
 *                 Leverages existing service layer for consistency.
 *                 Improves testability and maintainability significantly.
 *                 Follows FlorenceEGI DI-first architectural principles.
 *
 * @changelog   2.0.0 - 2025-05-25: Complete refactor from trait-based to service-based
 *                                   architecture. Added proper DI for CollectionService,
 *                                   WalletService, UserRoleService. Enhanced error handling
 *                                   and documentation. Removed HasCreateDefaultCollectionWallets trait.
 */
class EgiUploadHandler {
    use HasValidation;
    use HasUtilitys;
    // REMOVED: HasCreateDefaultCollectionWallets trait

    /**
     * Log channel for internal logging within handlers
     * @var string
     */
    protected string $logChannel = 'egi_upload';

    /**
     * Error manager for standardized error handling
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * Ultra log manager for structured logging
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * Collection management service - REPLACES TRAIT FUNCTIONALITY
     * @var CollectionService
     */
    protected readonly CollectionService $collectionService;

    /**
     * Wallet operations service
     * @var WalletServiceInterface
     */
    protected readonly WalletServiceInterface $walletService;

    /**
     * User role management service
     * @var UserRoleServiceInterface
     */
    protected readonly UserRoleServiceInterface $userRoleService;

    /**
     * Image optimization service for multi-variant processing
     * @var ImageOptimizationManagerInterface
     */
    protected readonly ImageOptimizationManagerInterface $imageOptimizationManager;

    /**
     * IPFS pinning service for original image storage
     * @var IpfsServiceInterface
     */
    protected readonly IpfsServiceInterface $ipfsService;

    /**
     * Constructor: Injects all required services for clean DI pattern
     *
     * @param ErrorManagerInterface $errorManager UEM for error handling
     * @param UltraLogManager $logger ULM for structured logging
     * @param CollectionService $collectionService Collection management service
     * @param WalletServiceInterface $walletService Wallet operations service
     * @param UserRoleServiceInterface $userRoleService User role assignment service
     * @param ImageOptimizationManagerInterface $imageOptimizationManager Image optimization service
     * @param IpfsServiceInterface $ipfsService IPFS pinning service for original images
     *
     * @oracode-di-pattern Proper dependency injection replacing trait approach
     * @oracode-service-layer All business logic delegated to appropriate services
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        CollectionService $collectionService,
        WalletServiceInterface $walletService,
        UserRoleServiceInterface $userRoleService,
        ImageOptimizationManagerInterface $imageOptimizationManager,
        IpfsServiceInterface $ipfsService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->collectionService = $collectionService;
        $this->walletService = $walletService;
        $this->userRoleService = $userRoleService;
        $this->imageOptimizationManager = $imageOptimizationManager;
        $this->ipfsService = $ipfsService;
    }

    /**
     * Handles and persists EGI file upload using service-based architecture
     *
     * @purpose     🎯 Orchestrates complete EGI upload process through proper service layer:
     *              authenticate, validate, manage collection via CollectionService,
     *              create Egi record, store file, invalidate cache, return response.
     *
     * --- Logic ---
     * 1.  Generate Operation ID, Authenticate User (-> UEM on fail)
     * 2.  Start DB Transaction
     * 3.  Retrieve and Validate File Input (-> Exception -> UEM)
     * 4.  Core File Validation via HasValidation trait (-> Exception -> UEM)
     * 5.  Validate Request Metadata via Laravel validation (-> ValidationException -> UEM)
     * 6.  Find/Create Collection via CollectionService (-> Exception -> UEM)
     * 7.  Prepare EGI data (crypto filename, position, metadata, dimensions, hash)
     * 8.  Create and Save Egi model with complete data
     * 9.  Store physical file via saveToMultipleDisks (-> Exception -> UEM)
     * 10. Invalidate relevant caches
     * 11. Prepare success response payload
     * 12. Commit DB Transaction
     * 13. Return success JsonResponse (200)
     * 14. Catch ValidationException -> UEM (422)
     * 15. Catch other Throwable -> UEM (500 or mapped)
     * --- End Logic ---
     *
     * @param Request $request HTTP request with 'file' and metadata
     * @return JsonResponse Success (200) or Error (4xx/5xx via UEM)
     *
     * @throws Throwable Only if UEM itself fails (extremely rare)
     *
     * @sideEffect 💾 DB changes via service layer, file storage, cache invalidation
     * @sideEffect 📝 All logging delegated to UEM and service layer
     *
     * @oracode-service-integration Uses CollectionService instead of trait methods
     * @oracode-error-boundary All service failures properly handled via UEM
     * @oracode-transaction-safety DB transaction wraps all critical operations
     */
    public function handleEgiUpload(Request $request): JsonResponse {
        $file = null;
        $originalName = 'unknown';
        $logContext = ['handler' => static::class, 'operation_id' => Str::uuid()->toString()];
        $creatorUser = null;
        $egiId = null;

        try {
            // --- 0. Enhanced Authentication with Session Support ---
            $creatorUser = $this->authenticateUser();
            if (!$creatorUser instanceof User) {
                return $this->errorManager->handle('EGI_AUTH_REQUIRED', $logContext);
            }

            $creatorUserId = $creatorUser->id;
            $logContext['user_id'] = $creatorUserId;
            $logContext['auth_type'] = FegiAuth::check() ? 'full' : 'wallet_connected';

            $this->logger->info('[EGI HandleEgiUpload] User authenticated', $logContext);

            // --- Start DB Transaction ---
            $result = DB::transaction(function () use ($request, $creatorUser, &$file, &$originalName, &$logContext, &$egiId) {
                $creatorUserId = $creatorUser->id;

                // --- 1. File Input Validation ---
                $file = $this->validateFileInput($request);
                $originalName = $file->getClientOriginalName() ?? 'uploaded_file';
                $logContext['original_filename'] = $originalName;

                // --- 2. Enhanced File Validation (with MIME type support for HEIC/HEIF) ---
                $this->validateFileEnhanced($file);

                // --- 3. Request Metadata Validation ---
                $validatedData = $this->validateMetadata($request);

                // --- 4. Collection Management via Service (REPLACES TRAIT) ---
                $collection = $this->getOrCreateCollection($creatorUser, $logContext);
                $collectionId = $collection->id;
                $logContext['collection_id'] = $collectionId;

                // --- 5. EGI Data Preparation ---
                $egiData = $this->prepareEgiData($file, $validatedData, $collection, $creatorUser);

                // --- 6. Create EGI Database Record ---
                $egi = $this->createEgiRecord($egiData, $collectionId, $creatorUserId);
                $egiId = $egi->id;
                $logContext['egi_id'] = $egiId;

                // --- 7. Store Physical File ---
                $savedUrls = $this->storeEgiFile($file, $collection, $creatorUser, $egi);

                // --- 8. Cache Invalidation ---
                $this->invalidateRelevantCache($collectionId);

                // --- 9. Success Response Preparation ---
                return $this->prepareSuccessResponse($egi, $originalName, $savedUrls);
            });

            return response()->json($result, 200);
        } catch (ValidationException $e) {
            $logContext['validation_errors'] = $e->errors();
            return $this->errorManager->handle('EGI_VALIDATION_FAILED', $logContext, $e);
        } catch (Throwable $e) {
            $logContext['egi_id'] = $egiId;
            $errorCode = $this->mapExceptionToUemCode($e);
            return $this->errorManager->handle($errorCode, $logContext, $e);
        }
    }

    /**
     * Enhanced user authentication with session wallet support
     *
     * @return User|null
     * @privacy-safe Authentication without exposing credentials
     */
    protected function authenticateUser(): ?User {
        // Full Laravel authentication
        if (FegiAuth::check()) {
            return FegiAuth::user();
        }

        // Session-based wallet authentication
        if (session()->has('auth_status') && session()->get('auth_status') === 'connected') {
            $userId = session()->get('connected_user_id');
            if ($userId) {
                $user = User::find($userId);
                // Additional wallet verification (use getAttributes to bypass accessor)
                $sessionWallet = session()->get('connected_wallet');
                if ($user && ($user->getAttributes()['wallet'] ?? null) === $sessionWallet) {
                    return $user;
                }
            }
        }

        return null;
    }

    /**
     * Validate file input from request
     *
     * @param Request $request
     * @return UploadedFile
     * @throws Exception
     */
    protected function validateFileInput(Request $request): UploadedFile {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            $uploadErrorCode = $request->hasFile('file') ? $request->file('file')->getError() : UPLOAD_ERR_NO_FILE;
            throw new Exception("Invalid or missing 'file' input. Upload error code: {$uploadErrorCode}", 400);
        }

        return $request->file('file');
    }

    /**
     * Validate request metadata
     *
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    protected function validateMetadata(Request $request): array {
        return $request->validate([
            'egi-title' => ['nullable', 'string', 'max:60'],
            'egi-description' => ['nullable', 'string', 'max:5000'],
            'egi-floor-price' => ['nullable', 'numeric', 'min:0'],
            'egi-date' => ['nullable', 'date_format:Y-m-d'],
            'egi-position' => ['nullable', 'integer', 'min:1'],
            'egi-publish' => ['nullable', 'boolean'],
            'egi-type' => ['nullable', 'in:ASA,SmartContract'], // NEW: Dual Architecture support
        ]);
    }

    /**
     * Get or create collection using CollectionService (REPLACES TRAIT)
     *
     * @param User $creatorUser
     * @param array $logContext
     * @return Collection
     * @throws Exception
     *
     * @oracode-service-replacement Replaces findOrCreateDefaultCollection trait method
     * @oracode-error-delegation Service handles its own error cases
     */
    protected function getOrCreateCollection(User $creatorUser, array &$logContext): Collection {
        try {
            $this->logger->info('[EGI Upload] Delegating collection management to CollectionService', $logContext);

            // Use CollectionService instead of trait method
            $collectionResult = $this->collectionService->findOrCreateUserCollection($creatorUser, $logContext);

            // Handle potential JsonResponse error from service
            if ($collectionResult instanceof JsonResponse) {
                throw new Exception("Collection service returned error response", 500);
            }

            if (!$collectionResult instanceof Collection) {
                throw new Exception("Collection service returned invalid result type", 500);
            }

            $this->logger->info('[EGI Upload] Collection obtained successfully via service', [
                ...$logContext,
                'collection_id' => $collectionResult->id,
                'collection_name' => $collectionResult->collection_name
            ]);

            return $collectionResult;
        } catch (Throwable $e) {
            $this->logger->error('[EGI Upload] Collection service operation failed', [
                ...$logContext,
                'error' => $e->getMessage(),
                'service' => CollectionService::class
            ]);

            throw new Exception("Failed to obtain collection via service: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Prepare EGI data from validated inputs
     *
     * @param UploadedFile $file
     * @param array $validatedData
     * @param Collection $collection
     * @param User $creatorUser
     * @return array
     * @throws Exception
     */
    protected function prepareEgiData(UploadedFile $file, array $validatedData, Collection $collection, User $creatorUser): array {
        $tempPath = $file->getRealPath();
        if ($tempPath === false) {
            throw new Exception("Cannot access temporary file path for: {$file->getClientOriginalName()}");
        }

        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        if (empty($extension)) {
            throw new Exception("Could not determine file extension for: {$file->getClientOriginalName()}");
        }

        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $crypt_filename = $this->my_advanced_crypt($file->getClientOriginalName(), 'e');

        if ($crypt_filename === false) {
            throw new Exception("Failed to encrypt filename for: {$file->getClientOriginalName()}");
        }

        // Generate position using helper
        $egiPosition = isset($validatedData['egi-position']) && is_numeric($validatedData['egi-position'])
            ? (int) $validatedData['egi-position']
            : EgiHelper::generatePositionNumber($collection->id, $this->logChannel);

        // Generate title
        $egiTitle = !empty(trim($validatedData['egi-title'] ?? ''))
            ? trim($validatedData['egi-title'])
            : '#' . str_pad($egiPosition, 4, '0', STR_PAD_LEFT) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        // Floor price
        $egiFloorPrice = isset($validatedData['egi-floor-price']) && is_numeric($validatedData['egi-floor-price'])
            ? (float) $validatedData['egi-floor-price']
            : ($collection->floor_price ?: (float) Config::get('egi.default_floor_price', 0));

        // Publish settings
        $isPublished = $validatedData['egi-publish'] ?? false;
        $publishDate = $validatedData['egi-date'] ?? Carbon::now()->toDateTimeString();

        // NEW: Dual Architecture - EGI Type (defaults to ASA if not specified)
        $egiType = $validatedData['egi-type'] ?? 'ASA';

        // Image dimensions
        $dimensions = @getimagesize($tempPath);
        $dimensionString = ($dimensions !== false) ? 'w:' . $dimensions[0] . ' x h:' . $dimensions[1] : 'N/A';

        return [
            'temp_path' => $tempPath,
            'extension' => strtolower($extension),
            'mime_type' => $mimeType,
            'crypt_filename' => $crypt_filename,
            'title' => Str::limit($egiTitle, 60),
            'description' => $validatedData['egi-description'],
            'position' => $egiPosition,
            'floor_price' => $egiFloorPrice,
            'is_published' => $isPublished,
            'publish_date' => $publishDate,
            'size' => $this->formatSizeInMegabytes($file->getSize()),
            'dimensions' => $dimensionString,
            'file_hash' => hash_file('md5', $tempPath),
            'upload_id' => Str::uuid()->toString(),
            'egi_type' => $egiType, // NEW: Store intended EGI type for dual architecture
        ];
    }

    /**
     * Create EGI database record
     *
     * @param array $egiData
     * @param int $collectionId
     * @param int $creatorUserId
     * @return Egi
     * @throws Exception
     */
    protected function createEgiRecord(array $egiData, int $collectionId, int $creatorUserId): Egi {
        $creatorUser = User::find($creatorUserId);

        $egi = new Egi();
        $egi->collection_id = $collectionId;
        $egi->user_id = $creatorUserId;
        $egi->owner_id = $creatorUserId;
        // Use getAttributes to bypass the wallet accessor that returns Wallet object
        $egi->creator = $creatorUser->getAttributes()['wallet'] ?? 'WalletNotSet';
        $egi->owner_wallet = $creatorUser->getAttributes()['wallet'] ?? 'WalletNotSet';
        $egi->upload_id = $egiData['upload_id'];
        $egi->title = $egiData['title'];
        $egi->description = $egiData['description'];
        $egi->extension = $egiData['extension'];
        $egi->media = false;
        $egi->type = 'image';
        $egi->bind = 0;
        $egi->mint = 0;
        $egi->rebind = 0;
        $egi->paired = 0;
        $egi->price = $egiData['floor_price'];
        $egi->floorDropPrice = $egiData['floor_price'];
        $egi->position = $egiData['position'];
        $egi->creation_date = $egiData['publish_date'];
        $egi->size = $egiData['size'];
        $egi->dimension = $egiData['dimensions'];
        $egi->is_published = $egiData['is_published'];
        $egi->status = 'local';
        $egi->file_crypt = $egiData['crypt_filename'];
        $egi->file_hash = $egiData['file_hash'];
        $egi->file_mime = $egiData['mime_type'];

        // NEW: Dual Architecture - Set EGI type and PreMint mode
        $egi->egi_type = $egiData['egi_type']; // ASA or SmartContract
        $egi->pre_mint_mode = true; // Always starts as PreMint (not yet on blockchain)
        $egi->pre_mint_created_at = Carbon::now(); // Track PreMint creation time

        $egi->save(); // First save to get ID

        $egi->key_file = $egi->id;
        $egi->save(); // Second save with key_file

        // Update rarity percentages for all traits in this collection
        $this->updateRarityPercentages($collectionId);

        return $egi;
    }

    /**
     * Store EGI file using enhanced multi-disk strategy with image optimization
     *
     * @param UploadedFile $file
     * @param Collection $collection
     * @param User $creatorUser
     * @param Egi $egi
     * @return array
     * @throws Exception
     */
    protected function storeEgiFile(UploadedFile $file, Collection $collection, User $creatorUser, Egi $egi): array {
        $basePath = 'users_files/collections_' . $collection->id . '/creator_' . $creatorUser->id . '/';
        $finalPathKey = $basePath . $egi->key_file . '.' . $egi->extension;
        $ipfsCid = null;

        $this->logger->info('[EGI Upload] storeEgiFile called', [
            'egi_id' => $egi->id,
            'mime_type' => $file->getMimeType(),
            'is_image' => $this->isImageMimeType($file),
            'ipfs_enabled' => $this->ipfsService->isEnabled()
        ]);

        // Check if this is an image file that supports optimization
        if ($this->isImageMimeType($file) && $this->imageOptimizationManager->isOptimizationSupported($file->getMimeType())) {

            $this->logger->info('[EGI Upload] Starting image optimization process', [
                'egi_id' => $egi->id,
                'collection_id' => $collection->id,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ]);

            try {
                // Generate optimized variants using the ImageOptimizationManager
                $optimizedVariants = $this->imageOptimizationManager->optimizeImage(
                    $file,
                    $basePath,
                    $egi->key_file,
                    [], // Use default variants
                    ['local', 'public'] // Save to both disks
                );

                $this->logger->info('[EGI Upload] Image optimization completed successfully', [
                    'egi_id' => $egi->id,
                    'variants_created' => array_keys($optimizedVariants),
                    'total_variants' => count($optimizedVariants)
                ]);

                // Store the original file path as main path
                $mainStorageResult = $this->saveToMultipleDisks(
                    $finalPathKey,
                    $file->getRealPath(),
                    ['collection_id' => $collection->id, 'egi_id' => $egi->id]
                );

                // Upload original image to IPFS for permanent storage
                $ipfsCid = $this->uploadOriginalToIpfs($file, $egi);

                // Add variant paths and IPFS CID to the storage result
                return array_merge($mainStorageResult, [
                    'optimized_variants' => $optimizedVariants,
                    'optimization_enabled' => true,
                    'ipfs_cid' => $ipfsCid
                ]);
            } catch (\Exception $e) {
                // If optimization fails, log and continue with standard storage
                $this->logger->info('[EGI Upload] Image optimization failed, using standard storage', [
                    'egi_id' => $egi->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Standard file storage for non-images or when optimization fails
        $result = $this->saveToMultipleDisks(
            $finalPathKey,
            $file->getRealPath(),
            ['collection_id' => $collection->id, 'egi_id' => $egi->id]
        );

        // For image files, still attempt IPFS upload even without optimization
        if ($this->isImageMimeType($file)) {
            $ipfsCid = $this->uploadOriginalToIpfs($file, $egi);
            $result['ipfs_cid'] = $ipfsCid;
        }

        return $result;
    }

    /**
     * Upload original image to IPFS for permanent decentralized storage
     *
     * Solo le immagini originali vengono caricate su IPFS per:
     * - Garantire permanenza e immutabilità dell'opera originale
     * - Permettere zoom ad alta risoluzione via gateway IPFS
     * - Creare prova di esistenza con CID determinato dal contenuto
     *
     * @param UploadedFile $file Original uploaded file
     * @param Egi $egi EGI model to update with IPFS CID
     * @return string|null IPFS CID if successful, null otherwise
     *
     * @oracode-non-blocking IPFS failure does not block EGI creation
     * @oracode-resilience Graceful degradation to local storage
     */
    protected function uploadOriginalToIpfs(UploadedFile $file, Egi $egi): ?string
    {
        // Check if IPFS service is enabled
        if (!$this->ipfsService->isEnabled()) {
            $this->logger->info('[EGI Upload] IPFS service disabled, skipping upload', [
                'egi_id' => $egi->id
            ]);
            return null;
        }

        try {
            $this->logger->info('[EGI Upload] Starting IPFS upload for original image', [
                'egi_id' => $egi->id,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            // Generate metadata for IPFS pinning
            $metadata = [
                'egi_id' => (string) $egi->id,
                'collection_id' => (string) $egi->collection_id,
                'title' => $egi->title,
                'creator_id' => (string) $egi->user_id,
                'file_hash' => $egi->file_hash ?? hash_file('sha256', $file->getRealPath()),
                'uploaded_at' => now()->toIso8601String()
            ];

            // Upload to IPFS via Pinata
            $ipfsResult = $this->ipfsService->upload(
                $file->getRealPath(),
                $metadata
            );

            if (!$ipfsResult['success']) {
                $this->logger->warning('[EGI Upload] IPFS upload failed', [
                    'egi_id' => $egi->id,
                    'error' => $ipfsResult['error'] ?? 'Unknown error'
                ]);
                return null;
            }

            $ipfsCid = $ipfsResult['cid'];

            // Update EGI with IPFS CID
            $egi->update(['ipfs_cid' => $ipfsCid]);

            $this->logger->info('[EGI Upload] IPFS upload successful', [
                'egi_id' => $egi->id,
                'ipfs_cid' => $ipfsCid,
                'gateway_url' => $ipfsResult['gateway_url'] ?? null
            ]);

            return $ipfsCid;

        } catch (\Exception $e) {
            // Non-blocking: IPFS failure should not prevent EGI creation
            $this->logger->warning('[EGI Upload] IPFS upload exception (non-blocking)', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Invalidate relevant application caches
     *
     * @param int $collectionId
     * @return void
     */
    protected function invalidateRelevantCache(int $collectionId): void {
        $cacheKeys = [
            'collection_items-' . $collectionId,
            'collection_stats-' . $collectionId,
            'user_collections-' . auth()->id()
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Prepare success response payload
     *
     * @param Egi $egi
     * @param string $originalName
     * @param array $savedUrls
     * @return array
     */
    protected function prepareSuccessResponse(Egi $egi, string $originalName, array $savedUrls): array {
        $userMsgKey = 'uploadmanager::uploadmanager.file_saved_successfully';
        $userMsgFallback = "File '{$originalName}' (EGI ID: {$egi->id}) processed successfully.";
        $successUserMessage = trans($userMsgKey, ['fileCaricato' => $originalName]) ?: $userMsgFallback;

        $response = [
            'success' => true,
            'userMessage' => $successUserMessage,
            'egiData' => [
                'id' => $egi->id,
                'collection_id' => $egi->collection_id,
                'title' => $egi->title,
                'description' => $egi->description,
                'price' => $egi->price,
                'position' => $egi->position,
                'status' => $egi->status,
                'published_at' => $egi->published_at?->toIso8601String(),
                'fileName' => $originalName,
                'urls' => $savedUrls,
                'mime_type' => $egi->file_mime,
                'size_mb' => $egi->size,
                'dimensions' => $egi->dimension,
                'created_at' => $egi->created_at->toIso8601String(),
            ]
        ];

        // Dual Architecture: If SmartContract selected, add Living payment redirect
        if ($egi->egi_type === 'SmartContract') {
            $response['requires_living_payment'] = true;
            $response['living_payment_url'] = route('egi-living.payment.form', ['egiId' => $egi->id]);
            $response['userMessage'] = __('uploadmanager::uploadmanager.smart_contract_requires_payment');
        }

        return $response;
    }

    /**
     * Map exceptions to UEM error codes
     *
     * @param Throwable $e
     * @return string
     */
    protected function mapExceptionToUemCode(Throwable $e): string {
        if (str_contains($e->getMessage(), 'Collection service')) {
            return 'EGI_COLLECTION_SERVICE_ERROR';
        }
        if (str_contains($e->getMessage(), 'CRITICAL STORAGE FAILURE')) {
            return 'EGI_STORAGE_CRITICAL_FAILURE';
        }
        if ($e instanceof \Illuminate\Database\QueryException) {
            return 'EGI_DB_ERROR';
        }
        if ($e->getCode() === 400 && str_contains($e->getMessage(), 'file input')) {
            return 'EGI_FILE_INPUT_ERROR';
        }
        if (str_contains($e->getMessage(), 'encrypt filename')) {
            return 'EGI_CRYPTO_ERROR';
        }

        return 'EGI_UNEXPECTED_ERROR';
    }

    /**
     * Save file to multiple configured storage disks with enhanced fallback
     *
     * @param string $pathKey
     * @param string $tempPath
     * @param array $logContext
     * @return array
     * @throws Exception
     *
     * @oracode-storage-strategy Enhanced multi-disk with intelligent fallback
     */
    protected function saveToMultipleDisks(string $pathKey, string $tempPath, array $logContext): array {
        Log::channel($this->logChannel)->info('[EGI Upload] Starting enhanced storage process', $logContext);

        // Enhanced configuration with better fallback
        $storageDisksConfig = Config::get('egi.storage.disks', ['public']);
        $criticalDisksConfig = Config::get('egi.storage.critical_disks', []);

        if (empty($storageDisksConfig) || !is_array($storageDisksConfig)) {
            Log::channel($this->logChannel)->warning('[EGI Upload] Invalid storage config, using public disk fallback', $logContext);
            $storageDisks = ['public'];
            $criticalDisks = ['public'];
        } else {
            $storageDisks = $storageDisksConfig;
            $criticalDisks = array_intersect($criticalDisksConfig, $storageDisks);
        }

        Log::channel($this->logChannel)->info('[EGI Upload] Storage disks configured', [
            ...$logContext,
            'disks' => $storageDisks,
            'critical_disks' => $criticalDisks
        ]);

        $savedInfo = [];
        $errors = [];

        // Read file content once
        try {
            $contents = file_get_contents($tempPath);
            if ($contents === false) {
                throw new Exception("Failed to read content from temporary file: {$tempPath}");
            }
        } catch (Throwable $e) {
            Log::channel($this->logChannel)->error('[EGI Upload] Cannot read temporary file', array_merge($logContext, ['error' => $e->getMessage()]));
            throw new Exception("Cannot read temporary upload file content.", 500, $e);
        }

        // Attempt save to each disk
        foreach ($storageDisks as $disk) {
            $diskLogContext = array_merge($logContext, ['disk' => $disk]);

            try {
                // Verify disk configuration
                if (!Config::has("filesystems.disks.{$disk}")) {
                    throw new Exception("Storage disk '{$disk}' not configured");
                }

                // Attempt storage with appropriate visibility
                $visibility = Config::get("egi.storage.visibility.{$disk}", 'public');
                $success = Storage::disk($disk)->put($pathKey, $contents, $visibility);

                if (!$success) {
                    throw new Exception("Storage::put returned false for disk '{$disk}'");
                }

                // Get URL or path
                try {
                    $savedInfo[$disk] = Storage::disk($disk)->url($pathKey);
                } catch (Throwable $eUrl) {
                    $savedInfo[$disk] = $pathKey;
                    Log::channel($this->logChannel)->debug('[EGI Upload] Could not get URL, using path key', $diskLogContext);
                }

                Log::channel($this->logChannel)->info('[EGI Upload] File saved successfully', array_merge($diskLogContext, ['url' => $savedInfo[$disk]]));
            } catch (Throwable $e_store) {
                $errorMsg = "Failed to save to disk '{$disk}': " . $e_store->getMessage();
                Log::channel($this->logChannel)->error('[EGI Upload] ' . $errorMsg, $diskLogContext);
                $errors[$disk] = $errorMsg;
            }
        }

        // Check critical failures with enhanced fallback
        $criticalFailures = array_intersect_key($errors, array_flip($criticalDisks));

        Log::channel($this->logChannel)->debug('[EGI Upload] DEBUG SUMMARY', [
            'saved_info' => $savedInfo,
            'errors' => $errors,
            'critical_disks' => $criticalDisks,
            'critical_failures' => $criticalFailures
        ]);


        if (!empty($criticalFailures) && empty($savedInfo)) {
            // All disks failed including critical ones, try emergency fallback
            if (!isset($errors['public'])) {
                Log::channel($this->logChannel)->warning('[EGI Upload] Attempting emergency fallback to public disk', $logContext);

                try {
                    $success = Storage::disk('public')->put($pathKey, $contents, 'public');
                    if ($success) {
                        $savedInfo['public'] = Storage::disk('public')->url($pathKey);
                        Log::channel($this->logChannel)->info('[EGI Upload] Emergency fallback successful', $logContext);
                        return $savedInfo;
                    }
                } catch (Throwable $eFallback) {
                    Log::channel($this->logChannel)->error('[EGI Upload] Emergency fallback failed', [
                        ...$logContext,
                        'error' => $eFallback->getMessage()
                    ]);
                }
            }

            throw new Exception("CRITICAL STORAGE FAILURE: All configured disks failed including fallback");
        }

        // Log non-critical failures
        if (!empty($errors) && !empty($savedInfo)) {
            Log::channel($this->logChannel)->warning('[EGI Upload] Some storage operations failed but others succeeded', [
                ...$logContext,
                'successful_disks' => array_keys($savedInfo),
                'failed_disks' => array_keys($errors)
            ]);
        }

        Log::channel($this->logChannel)->info('[EGI Upload] Upload completed. Returning saved info.', [
            'saved_info' => $savedInfo,
            'errors' => $errors
        ]);

        return $savedInfo;
    }

    /**
     * 🎯 Enhanced file validation with MIME type support for HEIC/HEIF files.
     * Replaces the basic validateFile() method to properly handle HEIC/HEIF MIME types.
     *
     * @param UploadedFile $file The file to validate
     * @return void
     * @throws Exception If validation fails
     */
    protected function validateFileEnhanced(UploadedFile $file): void {
        $fileNameForLog = $file->getClientOriginalName();
        $detectedMimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        // Extra debug logging for HEIC/HEIF detection
        $isHeicHeifByExtension = in_array($extension, ['heic', 'heif']);
        $isHeicHeifByMime = $detectedMimeType && in_array($detectedMimeType, [
            'image/heic',
            'image/heif',
            'image/x-heic',
            'image/x-heif',
            'application/heic',
            'application/heif'
        ]);

        Log::channel($this->logChannel)->info('[EGI Upload] Enhanced file validation with HEIC/HEIF debug', [
            'fileName' => $fileNameForLog,
            'size' => $file->getSize(),
            'detectedMimeType' => $detectedMimeType,
            'extension' => $extension,
            'isHeicHeifByExtension' => $isHeicHeifByExtension,
            'isHeicHeifByMime' => $isHeicHeifByMime,
            'originalName' => $file->getClientOriginalName(),
            'tempPath' => $file->getRealPath(),
            'uploadError' => $file->getError()
        ]);

        // Get validation rules from config
        $allowedExtensions = config('AllowedFileType.collection.allowed_extensions', []);
        $allowedMimeTypes = config('AllowedFileType.collection.allowed_mime_types', []);
        $maxSizeInBytes = config('AllowedFileType.collection.post_max_size', 100 * 1024 * 1024);

        // 1. Extension validation
        if (!in_array($extension, $allowedExtensions)) {
            Log::channel($this->logChannel)->error('[EGI Upload] Extension validation failed', [
                'fileName' => $fileNameForLog,
                'extension' => $extension,
                'allowedExtensions' => $allowedExtensions
            ]);
            throw new Exception("File extension '{$extension}' is not allowed.");
        }

        // 2. Enhanced MIME type validation with HEIC/HEIF special handling
        if ($detectedMimeType) {
            $mimeAllowed = in_array($detectedMimeType, $allowedMimeTypes);

            // Special case: if file has HEIC/HEIF extension but wrong MIME type,
            // we'll be more permissive due to browser/system variations
            if (!$mimeAllowed && $isHeicHeifByExtension) {
                Log::channel($this->logChannel)->warning('[EGI Upload] HEIC/HEIF file with unexpected MIME type - allowing due to extension', [
                    'fileName' => $fileNameForLog,
                    'detectedMimeType' => $detectedMimeType,
                    'extension' => $extension,
                    'allowedMimeTypes' => $allowedMimeTypes
                ]);
                // Continue processing - don't throw exception
            } else if (!$mimeAllowed) {
                Log::channel($this->logChannel)->error('[EGI Upload] MIME type validation failed', [
                    'fileName' => $fileNameForLog,
                    'detectedMimeType' => $detectedMimeType,
                    'allowedMimeTypes' => $allowedMimeTypes
                ]);
                throw new Exception("File MIME type '{$detectedMimeType}' is not allowed.");
            }
        }

        // 3. Size validation
        $fileSize = $file->getSize();
        if ($fileSize > $maxSizeInBytes) {
            $maxSizeMB = round($maxSizeInBytes / (1024 * 1024), 2);
            $fileSizeMB = round($fileSize / (1024 * 1024), 2);

            Log::channel($this->logChannel)->error('[EGI Upload] File size validation failed', [
                'fileName' => $fileNameForLog,
                'fileSizeMB' => $fileSizeMB,
                'maxSizeMB' => $maxSizeMB
            ]);
            throw new Exception("File size ({$fileSizeMB}MB) exceeds maximum allowed size ({$maxSizeMB}MB).");
        }

        // 4. Image structure validation for image files (including HEIC/HEIF)
        if ($this->isImageMimeType($file)) {
            $this->validateImageStructureEnhanced($file);
        }

        Log::channel($this->logChannel)->info('[EGI Upload] Enhanced file validation completed successfully', [
            'fileName' => $fileNameForLog
        ]);
    }

    /**
     * 🎯 Enhanced image structure validation with better HEIC/HEIF support.
     *
     * @param UploadedFile $file The image file to validate
     * @return void
     * @throws Exception If validation fails
     */
    protected function validateImageStructureEnhanced(UploadedFile $file): void {
        $fileNameForLog = $file->getClientOriginalName();

        Log::channel($this->logChannel)->info('[EGI Upload] Starting enhanced image structure validation', [
            'fileName' => $fileNameForLog,
            'mimeType' => $file->getMimeType()
        ]);

        if (!extension_loaded('imagick') || !class_exists('\\Imagick')) {
            Log::channel($this->logChannel)->warning('[EGI Upload] Imagick not available, skipping structure validation', [
                'fileName' => $fileNameForLog
            ]);
            return; // Skip validation instead of failing
        }

        $filePath = $file->getRealPath();
        if ($filePath === false || !file_exists($filePath)) {
            Log::channel($this->logChannel)->error('[EGI Upload] File path invalid for structure validation', [
                'fileName' => $fileNameForLog,
                'path' => $filePath ?: 'N/A'
            ]);
            throw new Exception("Could not access file for structure validation.");
        }

        $imagick = null;
        try {
            if (!class_exists('Imagick')) {
                Log::channel($this->logChannel)->warning('[EGI Upload] Imagick class not available', [
                    'fileName' => $fileNameForLog
                ]);
                return;
            }

            /** @var \Imagick $imagick */
            $imagick = new \Imagick();

            // For HEIC/HEIF files, Imagick might fail even if they're valid
            // So we'll be more permissive
            $mimeType = $file->getMimeType();
            $isHeicHeif = in_array($mimeType, ['image/heic', 'image/heif', 'image/x-heic', 'image/x-heif']);

            if ($isHeicHeif) {
                Log::channel($this->logChannel)->info('[EGI Upload] Detected HEIC/HEIF file, using permissive validation', [
                    'fileName' => $fileNameForLog,
                    'mimeType' => $mimeType
                ]);

                try {
                    $imagick->pingImage($filePath);
                    Log::channel($this->logChannel)->info('[EGI Upload] HEIC/HEIF structure validation passed', [
                        'fileName' => $fileNameForLog
                    ]);
                } catch (Exception $e) {
                    Log::channel($this->logChannel)->warning('[EGI Upload] HEIC/HEIF Imagick validation failed, but allowing file', [
                        'fileName' => $fileNameForLog,
                        'error' => $e->getMessage()
                    ]);
                    // Don't throw exception for HEIC/HEIF files as Imagick support varies
                }
            } else {
                // Standard validation for other image types
                try {
                    if (!$imagick->pingImage($filePath)) {
                        throw new Exception("Imagick::pingImage returned false");
                    }
                    Log::channel($this->logChannel)->info('[EGI Upload] Standard image structure validation passed', [
                        'fileName' => $fileNameForLog
                    ]);
                } catch (Exception $e) {
                    Log::channel($this->logChannel)->error('[EGI Upload] Image structure validation failed', [
                        'fileName' => $fileNameForLog,
                        'error' => $e->getMessage()
                    ]);
                    throw new Exception("Invalid image structure detected: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            if (!isset($isHeicHeif) || !$isHeicHeif) {
                Log::channel($this->logChannel)->error('[EGI Upload] Image structure validation failed', [
                    'fileName' => $fileNameForLog,
                    'error' => $e->getMessage()
                ]);
                throw new Exception("Invalid image structure detected: " . $e->getMessage());
            }
        } catch (Throwable $e) {
            Log::channel($this->logChannel)->error('[EGI Upload] Unexpected error during image validation', [
                'fileName' => $fileNameForLog,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Unexpected error during image validation: " . $e->getMessage());
        } finally {
            if ($imagick && is_object($imagick) && method_exists($imagick, 'clear')) {
                $imagick->clear();
                $imagick->destroy();
            }
        }
    }

    /**
     * Check if uploaded file is an image based on MIME type
     *
     * @param UploadedFile $file
     * @return bool
     *
     * @oracode-helper Simple MIME type check for image optimization eligibility
     */
    protected function isImageMimeType(UploadedFile $file): bool {
        $imageMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/heic',
            'image/heif',
            'image/bmp',
            'image/tiff'
        ];

        return in_array(strtolower($file->getMimeType()), $imageMimeTypes);
    }

    /**
     * Update rarity percentages for all traits in a collection
     *
     * @param int $collectionId
     * @return void
     */
    private function updateRarityPercentages(int $collectionId): void {
        try {
            Log::info('Updating rarity percentages for collection', ['collection_id' => $collectionId]);

            // Get total EGIs in collection
            $totalEgis = Egi::where('collection_id', $collectionId)->count();

            if ($totalEgis === 0) {
                Log::info('No EGIs in collection, skipping rarity update', ['collection_id' => $collectionId]);
                return;
            }

            // Get all unique trait combinations (trait_type_id + value) in this collection
            $uniqueTraits = \App\Models\EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->select('egi_traits.trait_type_id', 'egi_traits.value')
                ->distinct()
                ->get();

            Log::info('Found unique traits', ['count' => $uniqueTraits->count()]);

            // Calculate and update rarity for each unique trait combination
            foreach ($uniqueTraits as $uniqueTrait) {
                // Count how many EGIs have this trait
                $egisWithTrait = \App\Models\EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->count();

                // Calculate percentage
                $percentage = round(($egisWithTrait / $totalEgis) * 100, 2);

                // Update all traits with this combination
                $updatedCount = \App\Models\EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->update(['egi_traits.rarity_percentage' => $percentage]);

                Log::info('Updated rarity percentage', [
                    'trait_type_id' => $uniqueTrait->trait_type_id,
                    'value' => $uniqueTrait->value,
                    'percentage' => $percentage,
                    'egis_with_trait' => $egisWithTrait,
                    'total_egis' => $totalEgis,
                    'updated_count' => $updatedCount
                ]);
            }

            Log::info('Rarity percentages updated successfully for collection', ['collection_id' => $collectionId]);
        } catch (\Exception $e) {
            Log::error('Failed to update rarity percentages', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear traits rarity cache for a specific collection
     *
     * @param int $collectionId
     * @return void
     */
    private function clearTraitsRarityCache(int $collectionId): void {
        try {
            // Get all cache keys that match the pattern for this collection
            $pattern = "trait_rarity_{$collectionId}_*";

            // Use Cache::flush() to clear all cache or implement more specific clearing
            // For now, we'll use a simple approach and clear all cache
            Cache::flush();

            $this->logger->info('Traits rarity cache cleared for collection after EGI creation', [
                'collection_id' => $collectionId,
                'pattern' => $pattern
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            $this->logger->error('Failed to clear traits rarity cache after EGI creation', [
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
