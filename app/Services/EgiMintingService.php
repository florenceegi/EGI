<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\User;
use App\Services\AlgorandService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\ConsentService;
use App\Enums\Gdpr\GdprActivityCategory;

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

    /**
     * Constructor with GDPR/Ultra compliance
     *
     * @param UltraLogManager $logger Ultra logging manager for audit trails
     * @param ErrorManagerInterface $errorManager Ultra error manager for error handling
     * @param AuditLogService $auditService GDPR audit logging service
     * @param ConsentService $consentService GDPR consent management service
     * @param AlgorandService $algorandService Algorand blockchain service
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        ConsentService $consentService,
        AlgorandService $algorandService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
        $this->consentService = $consentService;
        $this->algorandService = $algorandService;
    }

    /**
     * Mint EGI su blockchain Algorand - GDPR COMPLIANT
     * @param Egi $egi EGI model instance
     * @param User $user User requesting mint operation
     * @param array $metadata Additional metadata
     * @return EgiBlockchain Created blockchain record
     * @throws \Exception
     * @privacy-safe Full GDPR compliance with consent check and audit trail
     */
    public function mintEgi(Egi $egi, User $user, array $metadata = []): EgiBlockchain {
        try {
            // 1. ULM: Log start
            $this->logger->info('EGI minting process initiated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'log_category' => 'EGI_MINTING_START'
            ]);

            // 2. GDPR: Check consent
            if (!$this->consentService->hasConsent($user, 'allow-blockchain-operations')) {
                throw new \Exception('Missing blockchain operations consent');
            }

            // 3. Verifica se EGI è già mintato
            if ($egi->egiBlockchain && $egi->egiBlockchain->mint_status === 'minted') {
                throw new \Exception("EGI #{$egi->id} è già stato mintato");
            }

            // 4. Crea o aggiorna record blockchain
            $egiBlockchain = $this->createOrUpdateBlockchainRecord($egi, 'minting', $user);

            // 5. Prepara metadata per blockchain
            $blockchainMetadata = $this->prepareMetadata($egi, $metadata);

            // 6. Mint su Algorand (con User per GDPR)
            $algorandResult = $this->algorandService->mintEgi($egi->id, $blockchainMetadata, $user);

            // 7. Aggiorna record con dati blockchain
            $egiBlockchain->update([
                'asa_id' => $algorandResult['asaId'],
                'blockchain_tx_id' => $algorandResult['txId'],
                'anchor_hash' => $algorandResult['certificate_number'] ?? null,
                'platform_wallet' => $algorandResult['treasury_address'] ?? null,
                'mint_status' => 'minted',
                'minted_at' => now(),
                'mint_error' => null
            ]);

            // 8. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'egi_minting_completed',
                [
                    'egi_id' => $egi->id,
                    'egi_title' => $egi->title,
                    'asa_id' => $algorandResult['asaId'],
                    'tx_id' => $algorandResult['txId'],
                    'blockchain_record_id' => $egiBlockchain->id
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 9. ULM: Log success
            $this->logger->info('EGI minting process completed successfully', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'asa_id' => $algorandResult['asaId'],
                'tx_id' => $algorandResult['txId'],
                'blockchain_record_id' => $egiBlockchain->id,
                'log_category' => 'EGI_MINTING_SUCCESS'
            ]);

            return $egiBlockchain->fresh();
        } catch (\Exception $e) {
            // 10. UEM: Error handling
            $this->errorManager->handle('EGI_MINTING_FAILED', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'egi_title' => $egi->title,
                'error_message' => $e->getMessage()
            ], $e);

            throw new \Exception("EGI minting failed: {$e->getMessage()}");
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

            // Trasferisce su blockchain
            $transferTxId = $this->algorandService->transferEgiAsset(
                $buyerWallet,
                $egiBlockchain->asa_id
            );

            // Aggiorna ownership
            $egiBlockchain->update([
                'ownership_type' => 'wallet',
                'buyer_wallet' => $buyerWallet,
                'buyer_user_id' => $buyerUserId,
                'blockchain_tx_id' => $transferTxId // Ultimo transaction ID
            ]);

            $this->logger->info('EGI_TRANSFER_SUCCESS', [
                'egi_id' => $egiBlockchain->egi_id,
                'transfer_tx_id' => $transferTxId
            ]);

            return $transferTxId;
        } catch (\Exception $e) {
            $this->logger->error('EGI_TRANSFER_FAILED', [
                'egi_id' => $egiBlockchain->egi_id,
                'error' => $e->getMessage()
            ]);
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
        return array_merge([
            'title' => $egi->title,
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

        $this->logger->error('EGI_MINTING_FAILED', [
            'egi_id' => $egi->id,
            'error' => $error->getMessage(),
            'trace' => $error->getTraceAsString()
        ]);
    }
}
