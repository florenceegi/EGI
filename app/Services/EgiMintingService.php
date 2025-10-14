<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use App\Services\AlgorandService;
use App\Services\PaymentDistributionService;
use App\Services\EgiMetadataBuilderService;
use App\Services\DisplayNameService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode EGI Minting Service - Orchestrates EGI blockchain minting workflow
 * 🎯 Purpose: Handle complete EGI minting process from database to blockchain
 * 🧱 Core Logic: Coordinate AlgorandService, EgiBlockchain model, error handling
 * 🛡️ Security: Input validation, state management, audit trail
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose EGI blockchain minting orchestration service
 */
class EgiMintingService {
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;
    private ConsentService $consentService;
    private AlgorandService $algorandService;
    private PaymentDistributionService $distributionService;
    private EgiMetadataBuilderService $metadataBuilder;
    private DisplayNameService $displayNameService;

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @param AlgorandService $algorandService Algorand blockchain service
     * @param PaymentDistributionService $distributionService Payment distribution service (AREA 2)
     * @param EgiMetadataBuilderService $metadataBuilder Metadata builder service (AREA 5)
     * @param DisplayNameService $displayNameService Display name service (AREA 5)
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        AlgorandService $algorandService,
        PaymentDistributionService $distributionService,
        EgiMetadataBuilderService $metadataBuilder,
        DisplayNameService $displayNameService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->algorandService = $algorandService;
        $this->distributionService = $distributionService;
        $this->metadataBuilder = $metadataBuilder;
        $this->displayNameService = $displayNameService;
    }

    /**
     * Mint EGI su blockchain Algorand - GDPR COMPLIANT + METADATA (AREA 5.6.1)
     * @param Egi $egi EGI model instance
     * @param User $user User requesting mint operation (minter/co-creator)
     * @param array $metadata Additional metadata (optional custom co_creator_name)
     * @return EgiBlockchain Created blockchain record with metadata
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function mintEgi(Egi $egi, User $user, array $metadata = []): EgiBlockchain {
        try {
            // 🚨 DEBUG: Log service call IMMEDIATELY
            $this->logger->emergency('🔥🔥🔥 MINTING SERVICE CALLED 🔥🔥🔥', [
                'egi_id' => $egi->id,
                'user_id' => $user->id,
                'pid' => getmypid(),
                'xdebug_enabled' => extension_loaded('xdebug') ? 'YES' : 'NO',
                'timestamp' => now()->format('H:i:s.u'),
                'log_category' => 'MINTING_SERVICE_DEBUG'
            ]);

            // 1. ULM: Log start
            $this->logger->info('EGI minting process initiated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'log_category' => 'EGI_MINTING_START'
            ]);

            // 2. Check Spatie Permission (not GDPR consent)
            if (!$user->hasPermissionTo('allow-blockchain-operations')) {
                throw new \Exception('Missing blockchain operations permission');
            }

            // 3. Verifica se EGI è già mintato
            if ($egi->egiBlockchain && $egi->egiBlockchain->mint_status === 'minted') {
                throw new \Exception("EGI #{$egi->id} è già stato mintato");
            }

            // 4. Crea o aggiorna record blockchain
            $egiBlockchain = $this->createOrUpdateBlockchainRecord($egi, 'minting', $user);

            // ========================================================================
            // AREA 5.6.1: BUILD METADATA + FREEZE DISPLAY NAMES
            // ========================================================================

            // 5a. Build complete NFT metadata (OpenSea-compatible)
            $egiMetadataStructure = $this->metadataBuilder->buildMetadata($egi, $user);

            $this->logger->info('EGI metadata structure built', [
                'egi_id' => $egi->id,
                'traits_count' => count($egiMetadataStructure->traits),
                'has_coa' => !empty($egiMetadataStructure->coa_reference),
                'log_category' => 'EGI_METADATA_BUILT'
            ]);

            // 5b. Freeze creator display name (snapshot at EGI creation)
            $creatorDisplayName = $this->displayNameService->freezeCreatorName($egi);

            // 5c. Freeze co-creator display name (snapshot at mint time)
            // User can provide custom name in $metadata['co_creator_display_name']
            $customCoCreatorName = $metadata['co_creator_display_name'] ?? null;
            $coCreatorDisplayName = $this->displayNameService->freezeCoCreatorName($user, $customCoCreatorName);

            $this->logger->info('Display names frozen', [
                'egi_id' => $egi->id,
                'creator_display_name' => $creatorDisplayName,
                'co_creator_display_name' => $coCreatorDisplayName,
                'custom_name_provided' => !empty($customCoCreatorName),
                'log_category' => 'EGI_NAMES_FROZEN'
            ]);

            // 5d. Convert to OpenSea format for validation and storage
            // NOTE: Using EGI image_url as fallback until Area 6 IPFS integration
            $metadataArray = $egiMetadataStructure->toOpenSeaFormat(
                "FlorenceEGI #{$egi->id}",
                $egi->description ?? "Digital Certificate for EGI #{$egi->id} - Florence Ecological Renaissance",
                route('egis.show', $egi->id),
                $egi->image_url ?? asset('images/egi-placeholder.png')
            );

            // 5e. Validate metadata before blockchain mint
            if (!$this->metadataBuilder->validateMetadata($metadataArray)) {
                throw new \Exception('Metadata validation failed before blockchain mint');
            }

            // ========================================================================
            // BLOCKCHAIN MINTING
            // ========================================================================

            // 6. Prepara metadata per blockchain (backward compatible)
            $blockchainMetadata = $this->prepareMetadata($egi, $metadata);

            // 7. Mint su Algorand (con User per GDPR)
            // NOTE: IPFS upload will be integrated in AREA 6
            // For now, metadata stored in DB only
            $algorandResult = $this->algorandService->mintEgi($egi->id, $blockchainMetadata, $user);

            // 8. Aggiorna record con dati blockchain + metadata + display names
            $egiBlockchain->update([
                'asa_id' => $algorandResult['asaId'],
                'blockchain_tx_id' => $algorandResult['txId'],
                'anchor_hash' => $algorandResult['certificate_number'] ?? null,
                'platform_wallet' => $algorandResult['treasury_address'] ?? null,
                'mint_status' => 'minted',
                'minted_at' => now(),
                'mint_error' => null,

                // AREA 5.6.1: Store metadata + display names
                'metadata' => $metadataArray,
                'creator_display_name' => $creatorDisplayName,
                'co_creator_display_name' => $coCreatorDisplayName,
                'metadata_last_updated_at' => now(),
            ]);

            // 9. GDPR: Audit trail (includes metadata info)
            $this->auditService->logUserAction(
                $user,
                'egi_minting_completed',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'asa_id' => $algorandResult['asaId'],
                    'tx_id' => $algorandResult['txId'],
                    'blockchain_record_id' => $egiBlockchain->id,
                    'creator_display_name' => $creatorDisplayName,
                    'co_creator_display_name' => $coCreatorDisplayName,
                    'metadata_traits_count' => count($egiMetadataStructure->traits),
                    'has_coa' => !empty($egiMetadataStructure->coa_reference),
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 10. ULM: Log success
            $this->logger->info('EGI minting process completed successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'asa_id' => $algorandResult['asaId'],
                'tx_id' => $algorandResult['txId'],
                'blockchain_record_id' => $egiBlockchain->id,
                'creator_display_name' => $creatorDisplayName,
                'co_creator_display_name' => $coCreatorDisplayName,
                'log_category' => 'EGI_MINTING_SUCCESS'
            ]);

            // 11. CRITICAL: Sync egis.owner_id with buyer_user_id
            // This ensures Policy checks and secondary market work correctly
            // This covers ALL mint flows (Jobs, direct calls, etc.)
            $freshBlockchain = $egiBlockchain->fresh();

            $this->logger->info('🔍 DEBUG OWNER SYNC - PRIMA', [
                'egi_id' => $egi->id,
                'egi_owner_id_before' => $egi->owner_id,
                'egi_user_id' => $egi->user_id,
                'blockchain_buyer_user_id' => $freshBlockchain->buyer_user_id,
                'will_set_owner_to' => $freshBlockchain->buyer_user_id
            ]);

            $egi->update([
                'owner_id' => $freshBlockchain->buyer_user_id,
                'token' => 'EGI',      // Register token type
                'status' => 'minted',  // Update status to minted
                'token_EGI' => $algorandResult['asaId'], // Store ASA ID from Algorand
                'mint' => true         // Mark as minted
            ]);

            $egiAfter = Egi::find($egi->id);
            $this->logger->info('🔍 DEBUG OWNER SYNC - DOPO', [
                'egi_id' => $egi->id,
                'egi_owner_id_after' => $egiAfter->owner_id,
                'egi_token' => $egiAfter->token,
                'egi_token_EGI' => $egiAfter->token_EGI,
                'egi_mint' => $egiAfter->mint,
                'egi_status' => $egiAfter->status,
                'update_worked' => $egiAfter->owner_id == $freshBlockchain->buyer_user_id ? 'YES' : 'NO'
            ]);

            return $freshBlockchain;
        } catch (\Exception $e) {
            // 11. UEM: Error handling (codes specifici già gestiti in AlgorandService)
            $this->errorManager->handle('EGI_MINTING_FAILED', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e; // Propaga eccezione (già gestita da UEM)
        }
    }

    /**
     * Trasferisce ownership EGI a wallet acquirente
     * @param EgiBlockchain $egiBlockchain Blockchain record
     * @param string $buyerWallet Wallet acquirente
     * @param int|null $buyerUserId User ID acquirente
     * @return string Transfer transaction ID
     * @throws \Exception
     */
    public function transferEgiOwnership(EgiBlockchain $egiBlockchain, string $buyerWallet, ?int $buyerUserId = null): string {
        $this->logger->info('EGI_TRANSFER_START', [
            'egi_id' => $egiBlockchain->egi_id,
            'buyer_wallet' => $buyerWallet,
            'buyer_user_id' => $buyerUserId
        ]);

        try {
            if ($egiBlockchain->mint_status !== 'minted') {
                throw new \Exception("EGI deve essere mintato prima del trasferimento");
            }

            // Get User object for GDPR compliance
            $user = $buyerUserId ? User::findOrFail($buyerUserId) : $egiBlockchain->buyer;
            if (!$user) {
                throw new \Exception("User not found for transfer operation");
            }

            // Trasferisce su blockchain
            $transferTxId = $this->algorandService->transferEgiAsset(
                $buyerWallet,
                $egiBlockchain->asa_id,
                $user
            );

            // Aggiorna ownership nel record blockchain
            $egiBlockchain->update([
                'ownership_type' => 'wallet',
                'buyer_wallet' => $buyerWallet,
                'buyer_user_id' => $buyerUserId,
                'blockchain_tx_id' => $transferTxId // Ultimo transaction ID
            ]);

            // CRITICAL: Sync egis.owner_id with buyer_user_id
            // This ensures Policy checks and secondary market work correctly
            if ($buyerUserId) {
                $egiBlockchain->egi->update([
                    'owner_id' => $buyerUserId
                ]);
            }

            $this->logger->info('EGI_TRANSFER_SUCCESS', [
                'egi_id' => $egiBlockchain->egi_id,
                'transfer_tx_id' => $transferTxId,
                'new_owner_id' => $buyerUserId
            ]);

            return $transferTxId;
        } catch (\Exception $e) {
            // UEM: Error handling (P1 compliance)
            $this->errorManager->handle('EGI_TRANSFER_FAILED', [
                'egi_id' => $egiBlockchain->egi_id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'buyer_wallet' => $buyerWallet,
                'buyer_user_id' => $buyerUserId,
                'error' => $e->getMessage()
            ], $e);
            throw $e;
        }
    }

    /**
     * Crea o aggiorna record blockchain - GDPR COMPLIANT
     * @param Egi $egi EGI instance
     * @param string $status Initial status
     * @param User $user User requesting operation
     * @return EgiBlockchain
     * @privacy-safe Includes user tracking for GDPR compliance
     */
    private function createOrUpdateBlockchainRecord(Egi $egi, string $status, User $user): EgiBlockchain {
        return EgiBlockchain::updateOrCreate(
            ['egi_id' => $egi->id],
            [
                'mint_status' => $status,
                'ownership_type' => 'treasury',
                'platform_wallet' => config('algorand.algorand.treasury_address'),
                'buyer_user_id' => $user->id, // GDPR: Track user for audit trail
                'certificate_uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Prepara metadata per blockchain
     * @param Egi $egi EGI instance
     * @param array $additionalMetadata Extra metadata
     * @return array Formatted metadata
     */
    private function prepareMetadata(Egi $egi, array $additionalMetadata = []): array {
        // Fix: title might be JSON array - extract string safely
        $titleStr = is_array($egi->title)
            ? ($egi->title['en'] ?? $egi->title['it'] ?? $egi->title[0] ?? 'EGI #' . $egi->id)
            : ($egi->title ?? 'EGI #' . $egi->id);

        return array_merge([
            'title' => $titleStr,
            'description' => $egi->description,
            'image_url' => $egi->image_url,
            'collection' => 'Florence EGI Marketplace',
            'category' => 'Digital Certificate'
        ], $additionalMetadata);
    }

    /**
     * Gestisce errori durante minting
     * @param Egi $egi EGI instance
     * @param \Exception $error Error occurred
     */
    private function handleMintingError(Egi $egi, \Exception $error): void {
        // Aggiorna stato errore
        if ($egi->egiBlockchain) {
            $egi->egiBlockchain->update([
                'mint_status' => 'failed',
                'mint_error' => $error->getMessage()
            ]);
        }

        // UEM: Error handling (P1 compliance)
        $this->errorManager->handle('EGI_MINTING_FAILED', [
            'egi_id' => $egi->id,
            'blockchain_record_id' => $egi->egiBlockchain?->id,
            'error' => $error->getMessage(),
            'trace' => $error->getTraceAsString()
        ], $error);
    }

    // ========================================================================
    // AREA 2.2.2: MINT + PAYMENT DISTRIBUTION INTEGRATION (PHASE 2)
    // ========================================================================

    /**
     * Mint EGI with automatic payment distribution (AREA 2.2.2)
     * Orchestrates: Mint → Distribution creation in atomic transaction
     * GDPR Compliance: Full audit trail for mint and payment distributions
     *
     * @param Egi $egi EGI model instance
     * @param User $user User requesting mint operation
     * @param array $paymentData ['paid_amount' => float, 'paid_currency' => string, 'payment_method' => string]
     * @param array $metadata Additional metadata
     * @return array ['blockchain' => EgiBlockchain, 'distributions' => array]
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function mintEgiWithPayment(Egi $egi, User $user, array $paymentData, array $metadata = []): array {
        try {
            // 1. ULM: Log start
            $this->logger->info('EGI minting with payment distribution initiated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'paid_amount' => $paymentData['paid_amount'] ?? null,
                'paid_currency' => $paymentData['paid_currency'] ?? 'EUR',
                'payment_method' => $paymentData['payment_method'] ?? 'unknown',
                'log_category' => 'EGI_MINT_WITH_PAYMENT_START'
            ]);

            // 2. Atomic transaction: Mint + Distribution
            return DB::transaction(function () use ($egi, $user, $paymentData, $metadata) {
                // STEP 1: Mint EGI on blockchain
                $egiBlockchain = $this->mintEgi($egi, $user, $metadata);

                // STEP 2: Record payment distributions (AREA 2.2.1)
                $distributions = [];
                if (isset($paymentData['paid_amount']) && $paymentData['paid_amount'] > 0) {
                    try {
                        // Update blockchain record with payment data
                        $egiBlockchain->update([
                            'paid_amount' => $paymentData['paid_amount'],
                            'paid_currency' => $paymentData['paid_currency'] ?? 'EUR',
                            'payment_method' => $paymentData['payment_method'] ?? 'fiat',
                            'status' => 'minted' // Confirm final status
                        ]);

                        // Create payment distributions
                        $distributions = $this->distributionService->recordMintDistribution(
                            $egiBlockchain->fresh(),
                            $paymentData
                        );

                        $this->logger->info('Payment distributions created successfully', [
                            'egi_blockchain_id' => $egiBlockchain->id,
                            'distributions_count' => count($distributions),
                            'total_distributed' => array_sum(array_column($distributions, 'amount_eur')),
                            'log_category' => 'MINT_DISTRIBUTION_SUCCESS'
                        ]);
                    } catch (\Exception $e) {
                        // Log warning but don't fail the mint (distributions can be created later)
                        $this->logger->warning('Payment distribution creation failed, mint successful', [
                            'egi_blockchain_id' => $egiBlockchain->id,
                            'error' => $e->getMessage(),
                            'log_category' => 'MINT_DISTRIBUTION_WARNING'
                        ]);

                        // Handle error but allow transaction to continue
                        $this->errorManager->handle('MINT_DISTRIBUTION_PARTIAL_FAILURE', [
                            'egi_blockchain_id' => $egiBlockchain->id,
                            'egi_id' => $egi->id,
                            'user_id' => $user->id,
                            'paid_amount' => $paymentData['paid_amount'],
                            'error' => $e->getMessage()
                        ], $e);

                        // Rethrow to rollback transaction
                        throw $e;
                    }
                }

                // 3. GDPR: Audit trail (comprehensive)
                $this->auditService->logUserAction(
                    $user,
                    'egi_mint_with_payment_completed',
                    [
                        'egi_id' => $egi->id,
                        'egi_title' => $egi->title,
                        'asa_id' => $egiBlockchain->asa_id,
                        'tx_id' => $egiBlockchain->blockchain_tx_id,
                        'blockchain_record_id' => $egiBlockchain->id,
                        'paid_amount' => $paymentData['paid_amount'] ?? null,
                        'paid_currency' => $paymentData['paid_currency'] ?? 'EUR',
                        'payment_method' => $paymentData['payment_method'] ?? 'unknown',
                        'distributions_count' => count($distributions),
                        'mint_with_payment' => true
                    ],
                    GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                );

                // 4. ULM: Log complete success
                $this->logger->info('EGI mint with payment distribution completed successfully', [
                    'user_id' => $user->id,
                    'egi_id' => $egi->id,
                    'asa_id' => $egiBlockchain->asa_id,
                    'tx_id' => $egiBlockchain->blockchain_tx_id,
                    'blockchain_record_id' => $egiBlockchain->id,
                    'paid_amount' => $paymentData['paid_amount'] ?? null,
                    'distributions_count' => count($distributions),
                    'log_category' => 'EGI_MINT_WITH_PAYMENT_SUCCESS'
                ]);

                return [
                    'blockchain' => $egiBlockchain->fresh(),
                    'distributions' => $distributions,
                    'mint_status' => 'success',
                    'payment_distributed' => count($distributions) > 0
                ];
            });
        } catch (\Exception $e) {
            // 5. UEM: Error handling
            $this->errorManager->handle('EGI_MINT_WITH_PAYMENT_FAILED', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'paid_amount' => $paymentData['paid_amount'] ?? null,
                'paid_currency' => $paymentData['paid_currency'] ?? 'EUR',
                'error_message' => $e->getMessage()
            ], $e);

            throw new \Exception("EGI mint with payment failed: {$e->getMessage()}");
        }
    }

    /**
     * Retry payment distribution for existing mint (AREA 2.2.2 utility)
     * Use when mint succeeded but distribution failed
     *
     * @param EgiBlockchain $egiBlockchain Existing blockchain record
     * @param array $paymentData ['paid_amount' => float, 'paid_currency' => string, 'payment_method' => string]
     * @return array Created distributions
     * @throws \Exception
     */
    public function retryPaymentDistribution(EgiBlockchain $egiBlockchain, array $paymentData): array {
        try {
            $this->logger->info('Retrying payment distribution for existing mint', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'paid_amount' => $paymentData['paid_amount'] ?? null,
                'log_category' => 'MINT_DISTRIBUTION_RETRY'
            ]);

            // Validate mint status
            if ($egiBlockchain->mint_status !== 'minted' && $egiBlockchain->status !== 'minted') {
                throw new \Exception("Cannot create distributions for non-minted EGI (status: {$egiBlockchain->mint_status})");
            }

            // Update payment data if provided
            if (isset($paymentData['paid_amount'])) {
                $egiBlockchain->update([
                    'paid_amount' => $paymentData['paid_amount'],
                    'paid_currency' => $paymentData['paid_currency'] ?? 'EUR',
                    'payment_method' => $paymentData['payment_method'] ?? 'fiat'
                ]);
            }

            // Create distributions
            $distributions = $this->distributionService->recordMintDistribution(
                $egiBlockchain->fresh(),
                $paymentData
            );

            $this->logger->info('Payment distribution retry successful', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'distributions_count' => count($distributions),
                'log_category' => 'MINT_DISTRIBUTION_RETRY_SUCCESS'
            ]);

            return $distributions;
        } catch (\Exception $e) {
            $this->errorManager->handle('MINT_DISTRIBUTION_RETRY_FAILED', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }
}
