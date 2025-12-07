<?php

namespace Ultra\EgiModule\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Collection;
use App\Enums\Wallet\WalletRoleEnum;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Http\RedirectResponse;
use Throwable;

/**
 * 📜 Oracode Service: WalletService
 * Service for managing wallet operations.
 *
 * @package     App\Services
 * @author      Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     2.0.0 // Refactored with UEM integration
 * @since       1.0.0
 *
 * @purpose     🎯 Handles wallet creation, quota validation, and royalty management
 *              for collections. Ensures proper allocation of mint and rebind royalties
 *              while maintaining quota integrity.
 *
 * @context     🧩 Used by collection management workflows to setup and validate wallet
 *              royalty distributions. Operates with high privileges for system wallets.
 *
 * @state       💾 Stateless. Relies on the injected dependencies (ULM, UEM).
 *
 * @feature     🗝️ Creates and attaches wallets to collections (creator, EPP, and platform wallets)
 * @feature     🗝️ Validates royalty quotas against thresholds
 * @feature     🗝️ Manages default wallet assignments with proper royalty distributions
 * @feature     🗝️ Uses UltraErrorManager for standardized error handling
 * @feature     🗝️ Uses UltraLogManager for consistent auditing
 *
 * @signal      🚦 Returns created Wallet models or boolean validation results
 * @signal      🚦 Throws standardized errors handled by UEM
 * @signal      🚦 Logs operation details via ULM
 *
 * @privacy     🛡️ `@privacy-internal`: Handles wallet addresses which may be considered personal financial data
 * @privacy     🛡️ `@privacy-data`: Uses minimal wallet identifiers when logging
 * @privacy     🛡️ `@privacy-purpose`: Logs essential information for auditing
 * @privacy     🛡️ `@privacy-consideration`: Ensures system accounts (EPP, Natan) are properly separated from user accounts
 *
 * @dependency  🤝 Ultra\ErrorManager\Interfaces\ErrorManagerInterface
 * @dependency  🤝 Ultra\UltraLogManager\UltraLogManager
 * @dependency  🤝 App\Models\Wallet
 * @dependency  🤝 App\Models\User
 * @dependency  🤝 App\Models\Collection
 *
 * @testing     🧪 Unit Test: Mock dependencies and verify wallet creation
 * @testing     🧪 Unit Test: Verify quota validation logic
 * @testing     🧪 Integration Test: Verify complete wallet attachment flow
 * @testing     🧪 Error Test: Verify proper error handling with UEM
 *
 * @rationale   💡 Centralizes wallet logic while providing standardized error handling and logging.
 *                 Integration with UEM ensures consistent error responses across the application.
 *
 * @changelog   2.0.0 - 2025-04-29: Refactored to use UltraErrorManager and UltraLogManager.
 *                                   Removed direct PSR-3 logger dependency.
 *                                   Added structured error handling with UEM error codes.
 *                                   Updated documentation to Oracode v1.5 standard.
 */
class WalletService implements WalletServiceInterface {
    /**
     * 🧱 @dependency UltraErrorManager instance.
     * Used for standardized error handling.
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * 🧱 @dependency UltraLogManager instance.
     * Used for standardized logging and auditing.
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * 🎯 Constructor: Injects required dependencies.
     *
     * @param ErrorManagerInterface $errorManager UEM for standardized error handling
     * @param UltraLogManager $logger ULM for standardized logging
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;

        // Define custom error codes if not already defined in config
        $this->defineWalletErrorCodes();
    }

    /**
     * 🚀 Attaches default wallets to a collection.
     *
     * Creates four default wallets for each new collection:
     * 1. Creator wallet - belongs to the collection owner (68% mint, 4.5% rebind)
     * 2. EPP wallet - environmental impact partner (20% mint, 0.8% rebind)
     * 3. Natan wallet - platform infrastructure (10% mint, 0.7% rebind)
     * 4. Frangette wallet - ecosystem development (2% mint, 0.1% rebind)
     *
     * Royalty percentages are defined in WalletRoleEnum for type-safety and immutability.
     *
     * --- Logic ---
     * 1. Create CREATOR wallet with user-specific data (dynamic)
     * 2. Create PLATFORM wallets (EPP, Natan, Frangette) from enum definitions
     * 3. Validate total mint percentages sum to 100%
     * 4. Log successful operation or handle exceptions via UEM
     * --- End Logic ---
     *
     * @param Collection $collection The collection to attach wallets to
     * @param User $user The owner/creator of the collection
     *
     * @return void
     *
     * @throws Throwable When UEM handling results in thrown exception
     * @throws \Exception If wallet percentages validation fails
     *
     * @sideEffect Creates four new wallet records in the database
     * @sideEffect Logs the wallet creation operations via ULM
     *
     * @privacy-purpose Wallet creation for royalty management
     * @privacy-data Uses user IDs and wallet addresses
     *
     * @see WalletRoleEnum For immutable tokenomics definitions
     */
    public function attachDefaultWalletsToCollection(Collection $collection, User $user): void {
        // Create context for logging and error handling
        $context = [
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'collection_name' => $collection->name
        ];

        try {
            // Check if this is a company collection
            $isCompanyCollection = $user->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;

            // Determine the correct platform_role based on user type
            $ownerPlatformRole = $this->determineOwnerPlatformRole($user->usertype);

            // Calculate royalties based on user type
            // Company: 90% owner, 0% EPP, 0% Frangette, 10% Natan
            // Creator: 68% owner, 20% EPP, 2% Frangette, 10% Natan
            $ownerMintRoyalty = $isCompanyCollection 
                ? WalletRoleEnum::COMPANY->getMintRoyalty()  // 90%
                : WalletRoleEnum::CREATOR->getMintRoyalty(); // 68%
            
            $ownerRebindRoyalty = $isCompanyCollection
                ? WalletRoleEnum::COMPANY->getRebindRoyalty()  // 4.6%
                : WalletRoleEnum::CREATOR->getRebindRoyalty(); // 4.5%

            // 1. Create OWNER wallet (user-specific, dynamic)
            $creatorWallet = $this->createWallet(
                $collection->id,
                $user->id,
                $user->getAttributes()['wallet'] ?? null,
                $ownerMintRoyalty,
                $ownerRebindRoyalty,
                $ownerPlatformRole
            );

            // 2. Create PLATFORM wallets (EPP, Natan, Frangette)
            // For Company: EPP and Frangette are created with 0% (can be modified later)
            // For Creator: Standard percentages apply
            $platformWallets = [];
            foreach (WalletRoleEnum::platformRoles() as $role) {
                // Determine mint royalty
                $mintRoyalty = $role->getMintRoyalty();
                $rebindRoyalty = $role->getRebindRoyalty();
                
                if ($isCompanyCollection) {
                    // Company: EPP and Frangette at 0%, Natan stays at 10%
                    if ($role === WalletRoleEnum::EPP || $role === WalletRoleEnum::FRANGETTE) {
                        $mintRoyalty = 0.0;
                        $rebindRoyalty = 0.0;
                    }
                }

                $platformWallets[$role->value] = $this->createWallet(
                    $collection->id,
                    $role->getUserId(),
                    $role->getWalletAddress(),
                    $mintRoyalty,
                    $rebindRoyalty,
                    $role->value
                );
            }

            // Log success with all wallet IDs
            $this->logger->info('All default wallets attached to collection', array_merge($context, [
                'owner_wallet_id' => $creatorWallet->id ?? 'failed',
                'owner_platform_role' => $ownerPlatformRole,
                'epp_wallet_id' => $platformWallets['EPP']->id ?? 'failed',
                'natan_wallet_id' => $platformWallets['Natan']->id ?? 'failed',
                'frangette_wallet_id' => $platformWallets['Frangette']->id ?? 'failed',
                'is_company_collection' => $isCompanyCollection,
                'owner_mint_percentage' => $ownerMintRoyalty,
                'epp_mint_percentage' => $isCompanyCollection ? 0.0 : WalletRoleEnum::EPP->getMintRoyalty(),
                'frangette_mint_percentage' => $isCompanyCollection ? 0.0 : WalletRoleEnum::FRANGETTE->getMintRoyalty(),
            ]));
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Failed to attach default wallets to collection', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Let UEM handle the error, potentially throwing
            $this->errorManager->handle(
                'WALLET_CREATION_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode()
                ]),
                $e,
                true // Always throw to maintain backward compatibility with existing code
            );
        }
    }

    /**
     * 🚀 Attaches EPP-specific wallet to a collection (100% royalties)
     *
     * Creates a SINGLE wallet for EPP user with 100% mint and rebind royalties.
     * Unlike standard collections that split royalties among Creator, EPP, Natan, and Frangette,
     * EPP collections have ONLY the EPP user wallet at 100%.
     *
     * --- Logic ---
     * 1. Create ONLY EPP wallet with user-specific data
     * 2. Set 100% mint and 100% rebind royalties
     * 3. Set platform_role = 'EPP'
     * 4. Validate wallet creation succeeded
     * 5. Log successful operation or handle exceptions via UEM
     * --- End Logic ---
     *
     * @param Collection $collection The EPP collection to attach wallet to
     * @param User $user The EPP user (owner of the collection)
     *
     * @return void
     *
     * @throws Throwable When UEM handling results in thrown exception
     *
     * @sideEffect Creates one wallet record in the database
     * @sideEffect Logs the wallet creation operation via ULM
     *
     * @privacy-purpose Wallet creation for EPP-exclusive royalty management
     * @privacy-data Uses EPP user ID and wallet address
     *
     * @oracode-pillar Semplicità Potenziante - Single wallet, 100% control for EPP
     * @oracode-pillar Coerenza Semantica - EPP role maps to 100% royalties
     */
    public function attachEppWalletToCollection(Collection $collection, User $user): void {
        // Create context for logging and error handling
        $context = [
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'user_type' => $user->usertype,
            'collection_name' => $collection->collection_name ?? 'N/A'
        ];

        try {
            // 1. Create EPP wallet (user-specific, 100% royalties)
            // Use getAttributes to bypass the wallet accessor that returns Wallet object
            $eppWallet = $this->createWallet(
                $collection->id,
                $user->id,
                $user->getAttributes()['wallet'] ?? null,
                100.0,  // 100% mint royalty
                100.0,  // 100% rebind royalty
                'EPP'   // platform_role
            );

            // 2. Validate wallet creation
            if (!$eppWallet instanceof Wallet) {
                throw new \Exception('EPP wallet creation returned non-Wallet response');
            }

            // 3. Log success
            $this->logger->info('EPP wallet attached to collection', array_merge($context, [
                'epp_wallet_id' => $eppWallet->id,
                'mint_royalty' => 100.0,
                'rebind_royalty' => 100.0,
                'platform_role' => 'EPP'
            ]));
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Failed to attach EPP wallet to collection', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Let UEM handle the error, potentially throwing
            $this->errorManager->handle(
                'EPP_WALLET_CREATION_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode()
                ]),
                $e,
                true // Always throw to maintain backward compatibility with existing code
            );
        }
    }

    /**
     * 🔧 Creates a single wallet with specified parameters.
     *
     * Helper method to create individual wallets with the specified
     * configuration values. Used by attachDefaultWalletsToCollection
     * to create the three required wallets.
     *
     * --- Logic ---
     * 1. If wallet address is null, generates unique placeholder: pending_wallet_{user_id}_{collection_id}_{timestamp}
     * 2. Checks if wallet already exists for this collection/user/address combination
     * 3. If exists, updates existing wallet with new royalty values
     * 4. Validates wallet address is not already associated with different user/collection
     * 5. Creates new wallet record with provided parameters
     * 6. Logs operation via ULM
     * --- End Logic ---
     *
     * @param int $collectionId Collection ID to associate the wallet with
     * @param int $userId User ID to associate the wallet with
     * @param string|null $walletAddress Wallet address (blockchain address). If null, generates unique placeholder.
     * @param float $royaltyMint Mint royalty percentage value
     * @param float $royaltyRebind Rebind royalty percentage value
     * @param string $platform_role Role of the wallet (Creator, EPP, Natan)
     *
     * @return Wallet The created wallet
     * @return RedirectResponse If UEM handles the error and returns a response
     *
     * @throws Throwable If wallet creation fails and UEM throws
     * @throws \Exception If wallet address already associated with different user
     *
     * @internal Used by attachDefaultWalletsToCollection to avoid code duplication
     *
     * @privacy-purpose Create individual wallet record with assigned royalties
     * @privacy-data Processes blockchain wallet address or generates unique placeholder
     */
    public function createWallet(
        int $collectionId,
        int $userId,
        ?string $walletAddress,
        float $royaltyMint,
        float $royaltyRebind,
        string $platform_role

    ): Wallet|RedirectResponse {
        // Create context for logging and error handling
        $context = [
            'collection_id' => $collectionId,
            'user_id' => $userId,
            'royalty_mint' => $royaltyMint,
            'royalty_rebind' => $royaltyRebind,
            'platform_role' => $platform_role,
        ];

        try {
            // If no wallet address is provided, generate a unique placeholder
            if ($walletAddress === null) {
                // Generate unique placeholder: pending_wallet_{user_id}_{collection_id}_{timestamp}
                $address = sprintf(
                    'pending_wallet_%d_%d_%s',
                    $userId,
                    $collectionId,
                    microtime(true)
                );

                // Log placeholder generation
                $this->logger->info('Generated unique placeholder wallet address', array_merge($context, [
                    'generated_address' => $address,
                    'reason' => 'no_wallet_provided'
                ]));
            } else {
                $address = $walletAddress;
            }

            // Check if wallet already exists for this user/address combination
            // First check: exact match with collection_id
            $existingWallet = Wallet::where('collection_id', $collectionId)
                ->where('user_id', $userId)
                ->where('wallet', $address)
                ->first();

            // Second check: wallet exists for this user with NULL collection_id (from registration)
            if (!$existingWallet) {
                $existingWallet = Wallet::where('user_id', $userId)
                    ->where('wallet', $address)
                    ->whereNull('collection_id')
                    ->first();
            }

            if ($existingWallet) {
                // Update existing wallet with new values
                $existingWallet->update([
                    'collection_id' => $collectionId, // Set collection_id if it was NULL
                    'royalty_mint' => $royaltyMint,
                    'royalty_rebind' => $royaltyRebind,
                    'platform_role' => $platform_role,
                ]);

                // Log wallet update
                $this->logger->info('Existing wallet updated for collection', array_merge($context, [
                    'wallet_id' => $existingWallet->id,
                    'action' => 'updated_existing',
                    'was_null_collection' => $existingWallet->wasChanged('collection_id')
                ]));

                return $existingWallet;
            }

            // NOTE: We allow the same wallet address to be used across different collections
            // and by different users (e.g., NATAN and FRANGETTE share the same platform wallet).
            // The unique constraint should only be on (collection_id, user_id, platform_role).

            // Create the wallet
            $wallet = Wallet::create([
                'collection_id' => $collectionId,
                'user_id' => $userId,
                'wallet' => $address,
                'royalty_mint' => $royaltyMint,
                'royalty_rebind' => $royaltyRebind,
                'platform_role' => $platform_role,
            ]);

            // Log successful wallet creation
            $this->logger->info('New wallet created for collection', array_merge($context, [
                'wallet_id' => $wallet->id,
                'action' => 'created_new'
            ]));

            return $wallet;
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Failed to create wallet', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Let UEM handle the error, potentially throwing
            return $this->errorManager->handle(
                'WALLET_CREATION_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage(),
                    'wallet_address_provided' => !is_null($walletAddress)
                ]),
                $e,
                true // Always throw to maintain backward compatibility
            );
        }
    }

    /**
     * 🔍 Validates if a user has sufficient royalty quota.
     *
     * Checks if the user's wallet has enough mint and rebind royalties
     * to meet the specified requirements while maintaining minimum thresholds.
     *
     * --- Logic ---
     * 1. Retrieve the user's wallet for the specified collection
     * 2. Check if wallet exists
     * 3. Read minimum threshold values from configuration
     * 4. Verify both current quota and post-operation remaining quota
     * --- End Logic ---
     *
     * @param int $userId User ID to check quota for
     * @param int $collectionId Collection ID to find the wallet
     * @param float $requiredMintQuota Minimum mint quota required
     * @param float $requiredRebindQuota Minimum rebind quota required
     *
     * @return bool True if quota is sufficient, false otherwise
     *
     * @privacy-purpose Validate available royalty quota
     * @privacy-data Uses user ID and collection ID only
     */
    public function hasUserSufficientQuota(
        int $userId,
        int $collectionId,
        float $requiredMintQuota,
        float $requiredRebindQuota
    ): bool {
        // Create context for logging
        $context = [
            'user_id' => $userId,
            'collection_id' => $collectionId,
            'required_mint_quota' => $requiredMintQuota,
            'required_rebind_quota' => $requiredRebindQuota
        ];

        try {
            // Get user's wallet for this collection
            $wallet = Wallet::where('collection_id', $collectionId)
                ->where('user_id', $userId)
                ->first();

            if (!$wallet) {
                $this->logger->notice('Wallet not found during quota check', $context);
                return false;
            }

            // Read minimum thresholds
            $thresholdMint = config('app.creator_royalty_mint_threshold', 0);
            $thresholdRebind = config('app.creator_royalty_rebind_threshold', 0);

            // Enhanced context with current values
            $detailedContext = array_merge($context, [
                'wallet_id' => $wallet->id,
                'current_mint_quota' => $wallet->royalty_mint,
                'current_rebind_quota' => $wallet->royalty_rebind,
                'threshold_mint' => $thresholdMint,
                'threshold_rebind' => $thresholdRebind
            ]);

            // Check both available quota and resulting thresholds
            $hasQuota = (
                $wallet->royalty_mint >= $requiredMintQuota &&
                $wallet->royalty_rebind >= $requiredRebindQuota &&
                ($wallet->royalty_mint - $requiredMintQuota) >= $thresholdMint &&
                ($wallet->royalty_rebind - $requiredRebindQuota) >= $thresholdRebind
            );

            // Log the result with appropriate level
            if ($hasQuota) {
                $this->logger->debug('User has sufficient quota', $detailedContext);
            } else {
                $this->logger->notice('User has insufficient quota', $detailedContext);
            }

            return $hasQuota;
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Error during quota validation', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle error non-blocking via UEM (no throw)
            $this->errorManager->handle(
                'WALLET_QUOTA_CHECK_ERROR',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw - default to false
            );

            return false;
        }
    }

    /**
     * 🧱 Determine the correct platform_role for the collection owner based on user type.
     *
     * This ensures that the wallet's platform_role correctly reflects the user's type
     * (e.g., 'Company' for company users, 'Creator' for creators).
     *
     * @param string|null $userType The user's type (e.g., 'creator', 'company', 'epp')
     * @return string The platform_role to assign to the owner's wallet
     */
    protected function determineOwnerPlatformRole(?string $userType): string {
        // Map user types to platform roles
        $platformRoleMapping = [
            'creator' => 'Creator',
            'company' => 'Company',
            'epp' => 'EPP',
            'patron' => 'Creator', // Patron acts as creator
            'collector' => 'Collector',
            'commissioner' => 'Commissioner',
            'trader_pro' => 'Trader Pro',
            'pa_entity' => 'PA Entity',
        ];

        return $platformRoleMapping[$userType] ?? 'Creator';
    }

    /**
     * 🧱 Define custom error codes specific to wallet operations.
     * Registers these codes with UEM for consistent error handling.
     *
     * @return void
     */
    protected function defineWalletErrorCodes(): void {
        // Define error for wallet creation failure
        $this->errorManager->defineError('WALLET_CREATION_FAILED', [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message' => 'Failed to create wallet: :error_message',
            'user_message' => 'Unable to setup the wallet for this collection. Please try again later.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => true,
            'notify_slack' => true,
        ]);

        // Define error for quota check failures
        $this->errorManager->defineError('WALLET_QUOTA_CHECK_ERROR', [
            'type' => 'error',
            'blocking' => 'not', // Non-blocking, just log
            'dev_message' => 'Error checking wallet quota: :error_message',
            'user_message' => null, // No user-visible message needed
            'http_status_code' => 500,
            'msg_to' => 'log-only',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for quota insufficiency
        $this->errorManager->defineError('WALLET_INSUFFICIENT_QUOTA', [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Wallet has insufficient quota for requested operation',
            'user_message' => 'You do not have sufficient royalty quota for this operation.',
            'http_status_code' => 400,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);
    }
}
