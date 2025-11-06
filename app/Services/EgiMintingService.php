<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Services\AlgorandService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

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
class EgiMintingService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AlgorandService $algorandService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AlgorandService $algorandService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->algorandService = $algorandService;
    }

    /**
     * Mint EGI su blockchain Algorand
     * @param Egi $egi EGI model instance
     * @param array $metadata Additional metadata
     * @return EgiBlockchain Created blockchain record
     * @throws \Exception
     */
    public function mintEgi(Egi $egi, array $metadata = []): EgiBlockchain
    {
        $this->logger->info('EGI_MINTING_START', [
            'egi_id' => $egi->id,
            'egi_title' => $egi->title
        ]);

        try {
            // Verifica se EGI è già mintato
            if ($egi->egiBlockchain && $egi->egiBlockchain->mint_status === 'minted') {
                throw new \Exception("EGI #{$egi->id} è già stato mintato");
            }

            // Crea o aggiorna record blockchain
            $egiBlockchain = $this->createOrUpdateBlockchainRecord($egi, 'minting');

            // Prepara metadata per blockchain
            $blockchainMetadata = $this->prepareMetadata($egi, $metadata);

            // Mint su Algorand
            $algorandResult = $this->algorandService->mintEgi($egi->id, $blockchainMetadata);

            // Aggiorna record con dati blockchain
            $egiBlockchain->update([
                'asa_id' => $algorandResult['asaId'],
                'blockchain_tx_id' => $algorandResult['txId'],
                'anchor_hash' => $algorandResult['certificate_number'] ?? null,
                'platform_wallet' => $algorandResult['treasury_address'] ?? null,
                'mint_status' => 'minted',
                'minted_at' => now(),
                'mint_error' => null
            ]);

            // CRITICAL: Sync egis table per consistenza (isMinted(), token_EGI, status)
            // Stesso pattern di MintEgiJob.php
            $egi->update([
                'owner_id' => $egiBlockchain->buyer_user_id, // Owner = buyer (creator self-mint)
                'mint' => true,                               // Flag per isMinted() method
                'token_EGI' => $algorandResult['asaId'],     // Algorand ASA ID
                'status' => 'minted',                        // Status diventa 'minted'
            ]);

            // CRITICAL: Generate certificate (anche per creator self-mint gratuito)
            try {
                $certificateService = app(\App\Services\CertificateGeneratorService::class);
                $certificate = $certificateService->generateBlockchainCertificate($egi->fresh(), $egiBlockchain->fresh());
                
                $this->logger->info('EGI_MINTING_SUCCESS - Certificate generated', [
                    'egi_id' => $egi->id,
                    'certificate_uuid' => $certificate->certificate_uuid,
                    'certificate_path' => $certificate->pdf_path,
                ]);
            } catch (\Exception $certError) {
                // Certificate generation failed - log but don't block mint
                $this->logger->error('Certificate generation failed after mint', [
                    'egi_id' => $egi->id,
                    'blockchain_id' => $egiBlockchain->id,
                    'error' => $certError->getMessage(),
                ]);
                // Don't throw - mint was successful, certificate can be generated later
            }

            $this->logger->info('EGI_MINTING_SUCCESS - Tables synced', [
                'egi_id' => $egi->id,
                'asa_id' => $algorandResult['asaId'],
                'tx_id' => $algorandResult['txId'],
                'egis_table_synced' => true,
                'owner_id' => $egiBlockchain->buyer_user_id,
            ]);

            return $egiBlockchain->fresh();
        } catch (\Exception $e) {
            $this->handleMintingError($egi, $e);
            throw $e;
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
    public function transferEgiOwnership(EgiBlockchain $egiBlockchain, string $buyerWallet, ?int $buyerUserId = null): string
    {
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
     * Crea o aggiorna record blockchain
     * @param Egi $egi EGI instance
     * @param string $status Initial status
     * @return EgiBlockchain
     */
    private function createOrUpdateBlockchainRecord(Egi $egi, string $status): EgiBlockchain
    {
        return EgiBlockchain::updateOrCreate(
            ['egi_id' => $egi->id],
            [
                'mint_status' => $status,
                'ownership_type' => 'treasury',
                'platform_wallet' => config('algorand.algorand.treasury_address'),
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
    private function prepareMetadata(Egi $egi, array $additionalMetadata = []): array
    {
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
    private function handleMintingError(Egi $egi, \Exception $error): void
    {
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
