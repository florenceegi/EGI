<?php

namespace App\Services;

use App\Models\User;
use App\Models\Collection;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\JsonResponse;
use Exception;
use Throwable;

/**
 * @Oracode Service: Enhanced Collection Management for EgiUploadHandler Integration
 * 🎯 Purpose: Manages collection lifecycle with enhanced error handling and service integration
 * 🧱 Core Logic: Updated constructor signature and enhanced error handling for EgiUploadHandler compatibility
 * 🛡️ GDPR: Maintains user-collection associations with minimal data exposure
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.1.0
 * @date 2025-05-25
 * @changelog 2.1.0 - Enhanced constructor for EgiUploadHandler DI compatibility
 *                     Added better error handling and logging integration
 *
 * @core-responsibilities
 * 1. Creates default collections for new users
 * 2. Finds or creates current user collection with enhanced fallback strategies
 * 3. Manages wallet and role assignments through service interfaces
 * 4. Maintains consistency between user collections and current state
 * 5. Provides enhanced error handling for upload handler integration
 *
 * @privacy-considerations
 * - Associates only necessary identifiers (creator_id, collection_id)
 * - Does not expose or log personal data beyond user ID
 * - Sanitizes user names when creating collection names
 * - Enhanced audit trail for upload handler operations
 *
 * @signature [CollectionService::v2.1] florence-egi-enhanced-collection-manager
 */
class CollectionService {
    /** @var UltraLogManager Enhanced logging for service operations */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface UEM interface for standardized error handling */
    private ErrorManagerInterface $errorManager;

    /** @var WalletServiceInterface Service for managing user wallets */
    private WalletServiceInterface $walletService;

    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    /**
     * Enhanced Constructor with proper DI order for EgiUploadHandler compatibility
     * 🎯 Purpose: Initialize service with dependencies in correct injection order
     * 📥 Input: Logger, error manager, wallet service, role service
     * ✅ Dependencies: All services properly injected with UEM/ULM integration
     *
     * @param UltraLogManager $logger Logger for operation traceability
     * @param ErrorManagerInterface $errorManager Error handler interface
     * @param WalletServiceInterface $walletService Service for wallet management
     * @param UserRoleServiceInterface $roleService Service for role assignment
     *
     * @oracode-di-pattern Enhanced dependency injection for upload handler integration
     * @oracode-service-integration Proper service interface usage
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        WalletServiceInterface $walletService,
        UserRoleServiceInterface $roleService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->walletService = $walletService;
        $this->roleService = $roleService;
    }

    /**
     * Enhanced collection creation with improved error handling
     * 🎯 Purpose: Generate new collection with enhanced validation and error handling
     * 📥 Input: User instance
     * 📤 Output: Newly created Collection instance or JsonResponse on error
     * 🛡️ GDPR: Uses minimal user data (ID, sanitized name)
     *
     * @param User $user The user for whom to create the collection
     * @return Collection|JsonResponse The created collection or error response
     *
     * @throws Exception If errors occur during creation process
     *
     * @oracode-enhanced-error-handling Improved error context and UEM integration
     * @oracode-upload-handler-compatible Designed for seamless EgiUploadHandler integration
     *
     * @changelog 2025-12-05: Added company usertype handling - EPP voluntary, subscription required
     */
    public function createDefaultCollection(User $user, ?bool $isDefault = true, ?string $collectionName = ''): Collection|JsonResponse {
        // Enhanced name sanitization
        $firstName = $this->sanitizeUserName($user->name);

        if (empty($collectionName)) {
            // Use sanitized first name if no collection name provided
            $collectionName = "{$firstName}'s Collection";
        }

        // Check if user is company - EPP is voluntary for company users
        $isCompanyUser = $user->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;

        $logContext = [
            'creator_id' => $user->id,
            'collection_name' => $collectionName,
            'operation' => 'create_default_collection',
            'is_company_user' => $isCompanyUser
        ];

        $this->logger->info('[CollectionService] Starting default collection creation', $logContext);

        try {
            // Enhanced validation
            $this->validateUserForCollectionCreation($user);

            // Prepare EPP configuration based on usertype
            // Company: EPP is voluntary (null by default), is_epp_voluntary = true
            // Others: EPP is mandatory (use default config)
            $eppProjectId = $isCompanyUser ? null : config('app.epp_id');
            $isEppVoluntary = $isCompanyUser;

            // Create collection with enhanced data
            $collection = Collection::create([
                'creator_id'      => $user->id,
                'owner_id'        => $user->id,
                'epp_id'          => $eppProjectId,
                'epp_project_id'  => $eppProjectId,
                'is_epp_voluntary' => $isEppVoluntary,
                'epp_donation_percentage' => null, // Company can set this later if desired
                'is_default'      => $isDefault,
                'collection_name' => $collectionName,
                'description'     => trans('collection.collection_description_placeholder', [], 'en') ?: 'Default collection automatically created for single EGI uploads.',
                'type'            => config('egi.default_type', 'image'),
                'position'        => $this->calculateCollectionPosition($user),
                'EGI_number'      => 1,
                'floor_price'     => (float) config('egi.default_floor_price', 0.0),
                'is_published'    => true,
                'status'          => 'local',
                'created_via'     => 'collection_service' // Enhanced tracking
            ]);

            $enhancedLogContext = array_merge($logContext, [
                'collection_id' => $collection->id,
                'collection_position' => $collection->position,
                'epp_project_id' => $eppProjectId,
                'is_epp_voluntary' => $isEppVoluntary
            ]);

            $this->logger->info('[CollectionService] Collection created successfully', $enhancedLogContext);

            // Enhanced pivot relationship setup
            $this->setupCollectionRelationships($collection, $user, $enhancedLogContext);

            // Enhanced wallet attachment with error handling
            $this->attachDefaultWalletsWithErrorHandling($collection, $user, $enhancedLogContext);

            // Enhanced role assignment with error handling
            $this->assignCreatorRoleWithErrorHandling($user, $enhancedLogContext);

            // Enhanced current collection setup
            $this->updateCurrentCollectionSafely($user, $collection, $enhancedLogContext);

            $this->logger->info('[CollectionService] New collection created and set as current', [
                ...$enhancedLogContext,
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'created_fresh' => true
            ]);

            return $collection;
        } catch (Throwable $e) {
            $errorContext = array_merge($logContext, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            $this->logger->error('[CollectionService] Failed to create default collection', $errorContext);

            return $this->errorManager->handle('COLLECTION_CREATION_FAILED', $errorContext, $e);
        }
    }

    /**
     * Enhanced collection finding with improved fallback strategy
     * 🎯 Purpose: Ensure user has an active collection with enhanced error handling
     * 📥 Input: User instance and optional log context
     * 📤 Output: Found or created Collection instance or JsonResponse on error
     * 📡 Enhanced Fallback: Current -> Default -> Create New (with validation)
     *
     * @param User $user The user for whom to find or create collection
     * @param array $logContext Optional logging context from calling handler
     * @return Collection|JsonResponse The found/created collection or error response
     *
     * @throws Exception If unable to find or create collection
     *
     * @oracode-enhanced-fallback-strategy Multi-level fallback with validation
     * @oracode-upload-handler-integration Designed for EgiUploadHandler context preservation
     */
    public function findOrCreateUserCollection(User $user, array $logContext = []): Collection|JsonResponse {
        $enhancedLogContext = array_merge($logContext, [
            'creator_id' => $user->id,
            'operation' => 'find_or_create_user_collection',
            'service' => static::class
        ]);

        $this->logger->info('[CollectionService] Starting collection lookup with enhanced strategy', $enhancedLogContext);

        try {
            // Enhanced Step 1: Try to find current collection with validation
            $currentCollection = $this->findCurrentCollectionWithValidation($user, $enhancedLogContext);
            if ($currentCollection) {
                $this->logger->info('[CollectionService] Current collection found and validated', [
                    ...$enhancedLogContext,
                    'collection_id' => $currentCollection->id,
                    'collection_name' => $currentCollection->collection_name
                ]);
                return $currentCollection;
            }

            // Enhanced Step 2: Try to find default collection with validation
            $defaultCollection = $this->findDefaultCollectionWithValidation($user, $enhancedLogContext);
            if ($defaultCollection) {
                $this->logger->info('[CollectionService] Default collection found', [
                    ...$enhancedLogContext,
                    'collection_id' => $defaultCollection->id,
                    'collection_name' => $defaultCollection->collection_name
                ]);

                return $defaultCollection;
            }

            // Enhanced Step 3: Create new collection with comprehensive setup
            $this->logger->info('[CollectionService] No existing collection found, creating new default collection', $enhancedLogContext);

            $newCollection = $this->createDefaultCollection($user);

            // Handle potential error response from creation
            if ($newCollection instanceof JsonResponse) {
                $this->logger->error('[CollectionService] Collection creation returned error response', $enhancedLogContext);
                return $newCollection;
            }

            return $newCollection;
        } catch (Throwable $e) {
            $errorContext = array_merge($enhancedLogContext, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]);

            $this->logger->error('[CollectionService] Error during collection find/create operation', $errorContext);

            return $this->errorManager->handle('COLLECTION_FIND_CREATE_FAILED', $errorContext, $e);
        }
    }

    /**
     * Enhanced current collection finder with validation
     * 🎯 Purpose: Locate and validate active collection from session or user record
     * 📥 Input: User instance and log context
     * 📤 Output: Validated Collection instance or null
     *
     * @param User $user The user
     * @param array $logContext Enhanced logging context
     * @return Collection|null The current collection or null if not found/invalid
     *
     * @oracode-validation-enhanced Adds collection existence and ownership validation
     */
    protected function findCurrentCollectionWithValidation(User $user, array $logContext): ?Collection {
        $currentCollectionId = session('current_collection_id') ?? $user->current_collection_id;

        if (!$currentCollectionId) {
            $this->logger->debug('[CollectionService] No current collection ID found', $logContext);
            return null;
        }

        $collection = Collection::find($currentCollectionId);

        // Handle Eloquent Collection vs Model instance
        if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
            $collection = $collection->first();
        }

        // Enhanced validation
        if (!$collection) {
            $this->logger->warning('[CollectionService] Current collection ID points to non-existent collection', [
                ...$logContext,
                'non_existent_collection_id' => $currentCollectionId
            ]);

            // Clean up invalid reference
            $this->cleanupInvalidCollectionReference($user, $currentCollectionId);
            return null;
        }

        // Validate ownership
        if ($collection->creator_id !== $user->id) {
            $this->logger->warning('[CollectionService] Current collection ownership mismatch', [
                ...$logContext,
                'collection_id' => $collection->id,
                'collection_owner_id' => $collection->creator_id,
                'expected_user_id' => $user->id
            ]);

            // Clean up ownership mismatch
            $this->cleanupInvalidCollectionReference($user, $currentCollectionId);
            return null;
        }

        return $collection;
    }

    /**
     * Enhanced default collection finder with validation
     * 🎯 Purpose: Query and validate collection marked as default for user
     * 📥 Input: User instance and log context
     * 📤 Output: Validated Collection instance or null
     *
     * @param User $user The user
     * @param array $logContext Enhanced logging context
     * @return Collection|null The default collection or null if not found/invalid
     */
    protected function findDefaultCollectionWithValidation(User $user, array $logContext): ?Collection {
        $collection = Collection::where('creator_id', $user->id)
            ->where('is_default', true)
            ->first();

        if ($collection) {
            $this->logger->debug('[CollectionService] Default collection found', [
                ...$logContext,
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name
            ]);
        } else {
            $this->logger->debug('[CollectionService] No default collection found for user', $logContext);
        }

        return $collection;
    }

    /**
     * Enhanced wallet attachment with comprehensive error handling
     * 🎯 Purpose: Attach default wallets with detailed error handling and recovery
     *
     * @param Collection $collection The collection
     * @param User $user The owner user
     * @param array $logContext Enhanced logging context
     * @return void
     *
     * @oracode-error-recovery Enhanced error handling with recovery strategies
     */
    protected function attachDefaultWalletsWithErrorHandling(Collection $collection, User $user, array $logContext): void {
        try {
            $this->logger->debug('[CollectionService] Attaching default wallets via WalletService', $logContext);

            $this->walletService->attachDefaultWalletsToCollection($collection, $user);

            $this->logger->info('[CollectionService] Default wallets attached successfully via WalletService', $logContext);
        } catch (Throwable $eWallet) {
            $walletErrorContext = array_merge($logContext, [
                'wallet_service_error' => $eWallet->getMessage(),
                'wallet_service_class' => get_class($eWallet)
            ]);

            $this->logger->error('[CollectionService] WalletService operation failed', $walletErrorContext);

            // Enhanced error handling - don't fail collection creation for wallet errors
            $this->errorManager->handle('COLLECTION_WALLET_ATTACHMENT_FAILED', $walletErrorContext, $eWallet, false);
        }
    }

    /**
     * Enhanced role assignment with comprehensive error handling
     * 🎯 Purpose: Assign creator role with detailed error handling and recovery
     *
     * @param User $user The user
     * @param array $logContext Enhanced logging context
     * @return void
     *
     * @oracode-error-recovery Enhanced error handling with recovery strategies
     */
    protected function assignCreatorRoleWithErrorHandling(User $user, array $logContext): void {
        try {
            $this->logger->debug('[CollectionService] Assigning creator role via UserRoleService', $logContext);

            $this->roleService->assignCreatorRole($user->id);

            $this->logger->info('[CollectionService] Creator role assigned successfully via UserRoleService', $logContext);
        } catch (Throwable $eRole) {
            $roleErrorContext = array_merge($logContext, [
                'role_service_error' => $eRole->getMessage(),
                'role_service_class' => get_class($eRole)
            ]);

            $this->logger->error('[CollectionService] UserRoleService operation failed', $roleErrorContext);

            // Enhanced error handling - don't fail collection creation for role errors
            $this->errorManager->handle('COLLECTION_ROLE_ASSIGNMENT_FAILED', $roleErrorContext, $eRole, false);
        }
    }

    /**
     * Enhanced collection relationships setup - VERSIONE SEMPLIFICATA
     * 🎯 Purpose: Setup pivot relationships with enhanced error handling
     *
     * @param Collection $collection The collection
     * @param User $user The user
     * @param array $logContext Enhanced logging context
     * @return void
     */
    protected function setupCollectionRelationships(Collection $collection, User $user, array $logContext): void {
        try {
            $this->logger->debug('[CollectionService] Setting up collection relationships via UserRoleService', $logContext);

            // 🎯 USA IL NUOVO METODO UserRoleService invece del vecchio attach()
            $success = $this->roleService->createCollectionUserRecord(
                $user->id,
                $collection->id,
                'creator'
            );

            if ($success) {
                $this->logger->debug('[CollectionService] Collection relationships setup completed successfully', $logContext);
            } else {
                $this->logger->warning('[CollectionService] UserRoleService returned false, trying fallback', $logContext);

                // Fallback al vecchio metodo se necessario
                $this->fallbackToDirectAttach($collection, $user, $logContext);
            }
        } catch (Throwable $e) {
            $relationshipErrorContext = array_merge($logContext, [
                'relationship_error' => $e->getMessage()
            ]);

            $this->logger->warning('[CollectionService] UserRoleService failed, trying fallback', $relationshipErrorContext);

            // Fallback al vecchio metodo
            $this->fallbackToDirectAttach($collection, $user, $relationshipErrorContext);
        }
    }

    /**
     * Enhanced user validation for collection creation
     * 🎯 Purpose: Validate user before collection creation with comprehensive checks
     *
     * @param User $user The user to validate
     * @throws Exception If user validation fails
     */
    protected function validateUserForCollectionCreation(User $user): void {
        if (!$user->id) {
            throw new Exception("User must have a valid ID for collection creation");
        }

        if (empty(trim($user->name))) {
            throw new Exception("User must have a valid name for collection creation");
        }

        // Check if user already has too many collections
        $maxCollections = config('egi.max_collections_per_user', 10);
        $currentCollectionCount = Collection::where('creator_id', $user->id)->count();

        if ($currentCollectionCount >= $maxCollections) {
            throw new Exception("User has reached maximum collection limit: {$maxCollections}");
        }
    }

    /**
     * 🚨 Fallback method using direct attach if UserRoleService fails
     */
    protected function fallbackToDirectAttach(Collection $collection, User $user, array $logContext): void {
        try {
            // Vecchio metodo come fallback
            $collection->users()->attach($user->id, [
                'role' => 'creator',
                'joined_at' => now(),
                'permissions' => json_encode(['upload', 'edit', 'delete'])
            ]);

            $this->logger->info('[CollectionService] Fallback attach method successful', $logContext);
        } catch (Throwable $e) {
            $this->logger->error('[CollectionService] Even fallback method failed', array_merge($logContext, [
                'fallback_error' => $e->getMessage()
            ]));

            // Non-critical error, continue execution
        }
    }

    /**
     * Enhanced user name sanitization
     * 🎯 Purpose: Safely extract and sanitize user name for collection naming
     *
     * @param string|null $userName The user's full name
     * @return string Sanitized first name
     */
    protected function sanitizeUserName(?string $userName): string {
        if (empty($userName)) {
            return 'User';
        }

        // Extract first name and sanitize
        $firstName = explode(' ', trim($userName), 2)[0];
        $firstName = preg_replace('/[^a-zA-Z0-9\s]/', '', $firstName);
        $firstName = trim($firstName);

        return !empty($firstName) ? $firstName : 'User';
    }

    /**
     * Enhanced collection position calculation
     * 🎯 Purpose: Calculate next available position for user's collections
     *
     * @param User $user The user
     * @return int Next available position
     */
    protected function calculateCollectionPosition(User $user): int {
        $maxPosition = Collection::where('creator_id', $user->id)->max('position') ?? 0;
        return $maxPosition + 1;
    }

    /**
     * Enhanced current collection update with safety checks and proper service delegation
     * 🎯 Purpose: Safely update user's current collection reference via UserRoleService
     *
     * @param User $user The user
     * @param Collection $collection The collection to set as current
     * @param array $logContext Enhanced logging context
     * @return void
     *
     * @oracode-service-delegation Delegates user updates to UserRoleService for proper separation
     * @oracode-session-management Maintains session state as CollectionService responsibility
     */
    protected function updateCurrentCollectionSafely(User $user, Collection $collection, array $logContext): void {
        try {
            $enhancedLogContext = array_merge($logContext, [
                'method' => 'updateCurrentCollectionSafely',
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'operation' => 'update_current_collection_safely'
            ]);

            $this->logger->debug('[CollectionService] Starting safe current collection update', $enhancedLogContext);

            // STEP 1: Delegate user database update to UserRoleService (proper separation of concerns)
            $this->logger->debug('[CollectionService] Delegating user current collection update to UserRoleService', $enhancedLogContext);

            $updateSuccess = $this->roleService->updateUserCurrentCollection(
                $user->id,
                $collection->id,
                $enhancedLogContext
            );

            // NOTE: If UserRoleService fails, UEM will handle the BLOCKING error and stop execution
            // This point should only be reached if the update was successful
            if (!$updateSuccess) {
                // This is a fallback scenario that should rarely occur
                $this->logger->error('[CollectionService] UserRoleService returned false unexpectedly', $enhancedLogContext);

                throw new \Exception("UserRoleService updateUserCurrentCollection returned false for user {$user->id}");
            }

            // STEP 2: Update session state (CollectionService responsibility)
            $this->logger->debug('[CollectionService] Updating session current collection reference', $enhancedLogContext);

            session(['current_collection_id' => $collection->id]);

            // STEP 3: Success logging
            $successContext = array_merge($enhancedLogContext, [
                'database_update_successful' => true,
                'session_update_successful' => true,
                'previous_collection_id' => $user->getOriginal('current_collection_id'),
                'new_collection_id' => $collection->id
            ]);

            $this->logger->info('[CollectionService] Current collection updated safely via service delegation', $successContext);
        } catch (\Throwable $e) {
            // Enhanced error context for this specific operation
            $updateErrorContext = array_merge($logContext, [
                'update_error' => $e->getMessage(),
                'update_error_class' => get_class($e),
                'collection_id' => $collection->id,
                'user_id' => $user->id,
                'method' => 'updateCurrentCollectionSafely'
            ]);

            $this->logger->error('[CollectionService] Failed to update user current collection safely', $updateErrorContext);

            // NOTE: If the error came from UserRoleService, it will already be handled by UEM as BLOCKING
            // If it's a different error (like session issues), we handle it here as CRITICAL
            $this->errorManager->handle(
                'COLLECTION_CURRENT_UPDATE_ERROR',
                $updateErrorContext,
                $e,
                true // This is critical for consistency, so throw
            );
        }
    }

    /**
     * Enhanced cleanup of invalid collection references
     * 🎯 Purpose: Clean up invalid collection references with proper logging
     *
     * @param User $user The user
     * @param int $invalidCollectionId The invalid collection ID
     * @return void
     */
    protected function cleanupInvalidCollectionReference(User $user, int $invalidCollectionId): void {
        try {
            $user->current_collection_id = null;
            $user->save();

            // Also clear from session
            session()->forget('current_collection_id');

            $this->logger->info('[CollectionService] Cleaned up invalid collection reference', [
                'creator_id' => $user->id,
                'invalid_collection_id' => $invalidCollectionId,
                'operation' => 'cleanup_invalid_reference'
            ]);
        } catch (Throwable $e) {
            $this->logger->warning('[CollectionService] Failed to cleanup invalid collection reference', [
                'creator_id' => $user->id,
                'invalid_collection_id' => $invalidCollectionId,
                'cleanup_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * LEGACY COMPATIBILITY METHOD - Maintains backward compatibility
     * 🎯 Purpose: Provide legacy method name for existing code compatibility
     *
     * @param User $user
     * @param array $logContext
     * @return Collection|JsonResponse
     * @deprecated Use findOrCreateUserCollection() instead
     */
    public function findOrCreateDefaultCollection(User $user, array $logContext = []): Collection|JsonResponse {
        $this->logger->warning('[CollectionService] Legacy method called', [
            'method' => 'findOrCreateDefaultCollection',
            'creator_id' => $user->id,
            'recommended_method' => 'findOrCreateUserCollection'
        ]);

        return $this->findOrCreateUserCollection($user, $logContext);
    }
}
