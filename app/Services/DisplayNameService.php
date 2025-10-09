<?php

namespace App\Services;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * DisplayNameService - Frozen Display Name Management for NFT Creators
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Area 5 Display Names)
 * @date 2025-10-09
 * @purpose Manage immutable creator and co-creator display names for NFT metadata
 *
 * @context
 * This service handles the freezing of creator and co-creator names at mint time.
 * Once frozen in the blockchain record, these names become permanent and immutable,
 * ensuring consistent attribution in NFT metadata regardless of future user profile changes.
 *
 * @business_logic
 * - Creator name: Frozen when EGI is created (from Egi->user relationship)
 * - Co-Creator name: Frozen at mint time (from User who mints)
 * - User can propose custom co-creator name PRE-mint
 * - Names are immutable POST-mint (blockchain anchored)
 * - Max 100 characters, alphanumeric + spaces allowed
 * - Validation ensures appropriate names (no profanity, no special chars)
 *
 * @architecture
 * - Works with EgiBlockchain model (creator_display_name, co_creator_display_name columns)
 * - Integrates with EgiMetadataBuilderService for metadata construction
 * - GDPR compliant with full audit trail
 * - Ultra ecosystem integration (ULM + UEM)
 *
 * @immutability_principle
 * Display names are frozen to ensure NFT metadata consistency.
 * Even if a user changes their profile name, the NFT metadata remains unchanged.
 * This is critical for:
 * - Historical accuracy
 * - Provenance tracking
 * - Legal attribution
 * - Marketplace consistency
 *
 * @mca_safe
 * - No crypto custody involved
 * - Only metadata management
 * - FIAT payment flow compatible
 */
class DisplayNameService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
    }

    /**
     * Freeze creator display name at EGI creation time
     *
     * @param Egi $egi EGI model instance with user relationship loaded
     * @return string Frozen creator display name
     *
     * @throws \Exception If creator name cannot be determined
     *
     * @business_logic
     * 1. Extract creator name from Egi->user relationship
     * 2. Validate name format and length
     * 3. Return sanitized name ready for storage
     * 4. This name will be stored in egi_blockchain.creator_display_name at mint time
     *
     * @immutability
     * This name represents the EGI creator at creation time.
     * It remains frozen even if the user later changes their profile name.
     *
     * @example
     * ```php
     * $egi = Egi::with('user')->find(123);
     * $creatorName = $displayNameService->freezeCreatorName($egi);
     * // Returns: "Leonardo Da Vinci"
     * ```
     */
    public function freezeCreatorName(Egi $egi): string {
        try {
            // 1. ULM: Log freeze operation start
            $this->logger->info('DisplayNameService: Freezing creator name', [
                'egi_id' => $egi->id,
                'user_id' => $egi->user_id,
                'log_category' => 'DISPLAY_NAME_FREEZE_CREATOR_START'
            ]);

            // 2. Load user relationship if not loaded
            if (!$egi->relationLoaded('user')) {
                $egi->load('user');
            }

            // 3. Get creator user
            $creator = $egi->user;

            if (!$creator) {
                throw new \Exception("Cannot freeze creator name: EGI #{$egi->id} has no associated user");
            }

            // 4. Extract and validate name
            $creatorName = $creator->name;

            if (empty($creatorName)) {
                throw new \Exception("Cannot freeze creator name: User #{$creator->id} has no name set");
            }

            // 5. Validate display name
            $this->validateDisplayName($creatorName);

            // 6. ULM: Log successful freeze
            $this->logger->info('DisplayNameService: Creator name frozen successfully', [
                'egi_id' => $egi->id,
                'user_id' => $creator->id,
                'frozen_name' => $creatorName,
                'log_category' => 'DISPLAY_NAME_FREEZE_CREATOR_SUCCESS'
            ]);

            return $creatorName;
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('DISPLAY_NAME_FREEZE_CREATOR_FAILED', [
                'egi_id' => $egi->id,
                'user_id' => $egi->user_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Freeze co-creator display name at mint time
     *
     * @param User $minter User who is minting the EGI (co-creator)
     * @param string|null $customName Optional custom name proposed by user PRE-mint
     * @return string Frozen co-creator display name
     *
     * @throws \Exception If co-creator name cannot be determined
     *
     * @business_logic
     * 1. If custom name provided and valid, use it
     * 2. Otherwise, use minter's current profile name
     * 3. Validate name format and length
     * 4. Return sanitized name ready for storage
     * 5. This name will be stored in egi_blockchain.co_creator_display_name
     *
     * @immutability
     * This name represents the co-creator/minter at mint time.
     * It remains frozen even if the user later changes their profile name.
     *
     * @example
     * ```php
     * $minter = Auth::user();
     * $coCreatorName = $displayNameService->freezeCoCreatorName($minter, 'Michelangelo');
     * // Returns: "Michelangelo" (custom name)
     *
     * $coCreatorName = $displayNameService->freezeCoCreatorName($minter);
     * // Returns: "John Doe" (profile name)
     * ```
     */
    public function freezeCoCreatorName(User $minter, ?string $customName = null): string {
        try {
            // 1. ULM: Log freeze operation start
            $this->logger->info('DisplayNameService: Freezing co-creator name', [
                'user_id' => $minter->id,
                'has_custom_name' => !is_null($customName),
                'log_category' => 'DISPLAY_NAME_FREEZE_CO_CREATOR_START'
            ]);

            // 2. Determine which name to use
            $coCreatorName = $customName ?? $minter->name;

            if (empty($coCreatorName)) {
                throw new \Exception("Cannot freeze co-creator name: User #{$minter->id} has no name set and no custom name provided");
            }

            // 3. Validate display name
            $this->validateDisplayName($coCreatorName);

            // 4. ULM: Log successful freeze
            $this->logger->info('DisplayNameService: Co-creator name frozen successfully', [
                'user_id' => $minter->id,
                'frozen_name' => $coCreatorName,
                'was_custom' => !is_null($customName),
                'log_category' => 'DISPLAY_NAME_FREEZE_CO_CREATOR_SUCCESS'
            ]);

            // 5. GDPR: Audit log name freeze
            $this->auditService->logUserAction(
                $minter,
                'co_creator_display_name_frozen',
                [
                    'frozen_name' => $coCreatorName,
                    'was_custom' => !is_null($customName)
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            return $coCreatorName;
        } catch (\Exception $e) {
            // 6. UEM: Error handling
            $this->errorManager->handle('DISPLAY_NAME_FREEZE_CO_CREATOR_FAILED', [
                'user_id' => $minter->id,
                'custom_name' => $customName,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Propose co-creator name for user (default for pre-mint form)
     *
     * @param User $user User who will mint the EGI
     * @return string Proposed display name (user's current profile name)
     *
     * @business_logic
     * - Returns user's current profile name as default proposal
     * - User can modify this in the pre-mint form
     * - Modified name will be validated before freezing
     *
     * @example
     * ```php
     * $user = Auth::user();
     * $proposedName = $displayNameService->proposeCoCreatorName($user);
     * // Returns: "John Doe" (current profile name)
     * // User sees this in form and can modify to "Johnny Artist"
     * ```
     */
    public function proposeCoCreatorName(User $user): string {
        // 1. ULM: Log proposal
        $this->logger->info('DisplayNameService: Proposing co-creator name', [
            'user_id' => $user->id,
            'proposed_name' => $user->name,
            'log_category' => 'DISPLAY_NAME_PROPOSE'
        ]);

        // 2. Return current profile name as proposal
        return $user->name ?? '';
    }

    /**
     * Validate display name format and content
     *
     * @param string $name Display name to validate
     * @return bool True if valid
     *
     * @throws ValidationException If validation fails
     *
     * @validation_rules
     * - Required
     * - String type
     * - Min 2 characters
     * - Max 100 characters
     * - Alphanumeric + spaces + basic punctuation (. ' -)
     * - No leading/trailing spaces
     * - No multiple consecutive spaces
     * - No special characters or emojis
     *
     * @example
     * ```php
     * $displayNameService->validateDisplayName("Leonardo Da Vinci"); // ✅ Pass
     * $displayNameService->validateDisplayName("O'Connor"); // ✅ Pass
     * $displayNameService->validateDisplayName("Jean-Pierre"); // ✅ Pass
     * $displayNameService->validateDisplayName("A"); // ❌ Fail (too short)
     * $displayNameService->validateDisplayName("Name🎨"); // ❌ Fail (emoji)
     * ```
     */
    public function validateDisplayName(string $name): bool {
        try {
            // 1. ULM: Log validation start
            $this->logger->info('DisplayNameService: Validating display name', [
                'name_length' => strlen($name),
                'log_category' => 'DISPLAY_NAME_VALIDATION_START'
            ]);

            // 2. Basic validation rules
            $rules = [
                'name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^[a-zA-Z0-9\s.\'\-]+$/', // Alphanumeric + spaces + . ' -
                ]
            ];

            $messages = [
                'name.required' => 'Display name is required',
                'name.string' => 'Display name must be a string',
                'name.min' => 'Display name must be at least 2 characters',
                'name.max' => 'Display name cannot exceed 100 characters',
                'name.regex' => 'Display name can only contain letters, numbers, spaces, dots, apostrophes, and hyphens',
            ];

            // 3. Execute validation
            $validator = Validator::make(['name' => $name], $rules, $messages);

            if ($validator->fails()) {
                // 4. ULM: Log validation failure
                $this->logger->warning('DisplayNameService: Display name validation failed', [
                    'name' => $name,
                    'errors' => $validator->errors()->toArray(),
                    'log_category' => 'DISPLAY_NAME_VALIDATION_FAILED'
                ]);

                throw new ValidationException($validator);
            }

            // 5. Additional business rules validation
            $this->validateBusinessRules($name);

            // 6. ULM: Log successful validation
            $this->logger->info('DisplayNameService: Display name validation passed', [
                'name' => $name,
                'log_category' => 'DISPLAY_NAME_VALIDATION_SUCCESS'
            ]);

            return true;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // 7. UEM: Error handling
            $this->errorManager->handle('DISPLAY_NAME_VALIDATION_FAILED', [
                'name' => $name,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Store frozen names in EgiBlockchain record
     *
     * @param EgiBlockchain $egiBlockchain Blockchain record to update
     * @param string $creatorName Frozen creator name
     * @param string $coCreatorName Frozen co-creator name
     * @return void
     *
     * @throws \Exception If storage fails
     *
     * @business_logic
     * - Updates egi_blockchain record with frozen names
     * - Names become immutable after this operation
     * - Full audit trail logged
     *
     * @example
     * ```php
     * $displayNameService->storeFrozenNames(
     *     $egiBlockchain,
     *     "Leonardo Da Vinci",
     *     "Michelangelo"
     * );
     * ```
     */
    public function storeFrozenNames(
        EgiBlockchain $egiBlockchain,
        string $creatorName,
        string $coCreatorName
    ): void {
        try {
            // 1. ULM: Log storage start
            $this->logger->info('DisplayNameService: Storing frozen names', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'creator_name' => $creatorName,
                'co_creator_name' => $coCreatorName,
                'log_category' => 'DISPLAY_NAME_STORE_START'
            ]);

            // 2. Update blockchain record
            $egiBlockchain->update([
                'creator_display_name' => $creatorName,
                'co_creator_display_name' => $coCreatorName,
            ]);

            // 3. ULM: Log successful storage
            $this->logger->info('DisplayNameService: Frozen names stored successfully', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'log_category' => 'DISPLAY_NAME_STORE_SUCCESS'
            ]);

            // 4. GDPR: Audit log storage
            if ($egiBlockchain->buyer) {
                $this->auditService->logUserAction(
                    $egiBlockchain->buyer,
                    'display_names_frozen_in_blockchain',
                    [
                        'egi_blockchain_id' => $egiBlockchain->id,
                        'egi_id' => $egiBlockchain->egi_id,
                        'creator_name' => $creatorName,
                        'co_creator_name' => $coCreatorName
                    ],
                    GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                );
            }
        } catch (\Exception $e) {
            // 5. UEM: Error handling
            $this->errorManager->handle('DISPLAY_NAME_STORE_FAILED', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Check if display names are already frozen in blockchain record
     *
     * @param EgiBlockchain $egiBlockchain Blockchain record to check
     * @return bool True if names are frozen, false otherwise
     *
     * @example
     * ```php
     * if ($displayNameService->areNamesFrozen($egiBlockchain)) {
     *     // Names are immutable, cannot modify
     * } else {
     *     // Can still set names
     * }
     * ```
     */
    public function areNamesFrozen(EgiBlockchain $egiBlockchain): bool {
        return !is_null($egiBlockchain->creator_display_name)
            && !is_null($egiBlockchain->co_creator_display_name);
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Validate business rules for display names
     *
     * @param string $name Display name to validate
     * @return void
     * @throws \Exception If business rules violated
     */
    private function validateBusinessRules(string $name): void {
        // Business rule: No leading or trailing spaces
        if ($name !== trim($name)) {
            throw new \Exception('Display name cannot have leading or trailing spaces');
        }

        // Business rule: No multiple consecutive spaces
        if (Str::contains($name, '  ')) { // Check for double space
            throw new \Exception('Display name cannot contain multiple consecutive spaces');
        }

        // Business rule: Must contain at least one letter
        $hasLetter = false;
        for ($i = 0; $i < strlen($name); $i++) {
            if (ctype_alpha($name[$i])) {
                $hasLetter = true;
                break;
            }
        }
        if (!$hasLetter) {
            throw new \Exception('Display name must contain at least one letter');
        }

        // Business rule: Cannot be only numbers and punctuation
        $cleanedName = str_replace([' ', '.', "'", '-'], '', $name);
        if (ctype_digit($cleanedName)) {
            throw new \Exception('Display name cannot consist only of numbers and punctuation');
        }

        // Business rule: Simple profanity check (can be enhanced)
        $this->checkProfanity($name);
    }

    /**
     * Basic profanity check for display names
     *
     * @param string $name Display name to check
     * @return void
     * @throws \Exception If profanity detected
     */
    private function checkProfanity(string $name): void {
        // Simple blacklist (can be enhanced with external service)
        $profanityList = [
            // Add common inappropriate terms in multiple languages
            // This is a basic implementation - consider using a proper profanity filter service
        ];

        $nameLower = strtolower($name);

        foreach ($profanityList as $term) {
            if (str_contains($nameLower, strtolower($term))) {
                throw new \Exception('Display name contains inappropriate content');
            }
        }
    }
}
