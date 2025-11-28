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
     * Mint EGI with payment processing and split distribution (COMPLETE WORKFLOW)
     * 
     * This method orchestrates the complete mint workflow:
     * 1. Validates payment result
     * 2. Mints EGI on Algorand blockchain
     * 3. Updates database records
     * 4. Generates certificate
     * 
     * The split payment is already handled by StripeRealPaymentService before calling this method.
     * 
     * @param Egi $egi EGI model instance
     * @param array $paymentResult Payment result from payment service (includes split_result)
     * @param array $metadata Additional metadata (buyer_user_id, buyer_wallet, payment_method, etc.)
     * @return EgiBlockchain Created blockchain record
     * @throws \Exception
     */
    public function mintEgiWithPayment(Egi $egi, array $paymentResult, array $metadata = []): EgiBlockchain
    {
        $this->logger->info('EGI_MINT_WITH_PAYMENT_START', [
            'egi_id' => $egi->id,
            'egi_title' => $egi->title,
            'buyer_user_id' => $metadata['buyer_user_id'] ?? null,
            'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
            'has_split_result' => isset($paymentResult['split_result']),
        ]);

        try {
            // Verifica che il pagamento sia stato completato con successo
            if (($paymentResult['status'] ?? null) !== 'succeeded') {
                throw new \Exception('Payment not succeeded - cannot mint EGI');
            }

            // Prepara metadata completo includendo dati pagamento
            $mintMetadata = array_merge($metadata, [
                'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
                'payment_method' => $paymentResult['payment_method'] ?? 'stripe',
                'payment_amount_eur' => $paymentResult['amount'] ?? null,
                'payment_currency' => $paymentResult['currency'] ?? 'eur',
                'merchant_psp_config' => $paymentResult['metadata']['merchant_psp_config'] ?? null,
            ]);

            // Se è presente split_result, aggiungi informazioni sulla distribuzione
            if (isset($paymentResult['split_result'])) {
                $splitResult = $paymentResult['split_result'];
                
                $this->logger->info('EGI_MINT_WITH_SPLIT_PAYMENT', [
                    'egi_id' => $egi->id,
                    'total_wallets' => $splitResult['total_wallets'],
                    'successful_transfers' => $splitResult['successful_transfers'],
                    'failed_transfers' => $splitResult['failed_transfers'],
                ]);

                $mintMetadata['split_payment'] = [
                    'total_wallets' => $splitResult['total_wallets'],
                    'successful_transfers' => $splitResult['successful_transfers'],
                    'failed_transfers' => $splitResult['failed_transfers'],
                    'transfers' => array_map(function ($transfer) {
                        return [
                            'wallet_id' => $transfer['wallet_id'],
                            'platform_role' => $transfer['platform_role'],
                            'amount_eur' => $transfer['amount_eur'],
                            'percentage' => $transfer['percentage'],
                            'transfer_id' => $transfer['transfer_id'],
                            'status' => $transfer['status'],
                        ];
                    }, $splitResult['transfers'] ?? []),
                ];
            }

            // Esegui mint standard con metadata arricchito
            $egiBlockchain = $this->mintEgi($egi, $mintMetadata);

            $this->logger->info('EGI_MINT_WITH_PAYMENT_SUCCESS', [
                'egi_id' => $egi->id,
                'blockchain_id' => $egiBlockchain->id,
                'asa_id' => $egiBlockchain->asa_id,
                'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
            ]);

            return $egiBlockchain;

        } catch (\Exception $e) {
            $this->logger->error('EGI_MINT_WITH_PAYMENT_FAILED', [
                'egi_id' => $egi->id,
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
            ]);

            // Mark blockchain record as failed
            $this->handleMintingError($egi, $e);

            throw $e;
        }
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

            // Determina buyer/wallet/co-creator name
            $buyerUserId = $metadata['buyer_user_id']
                ?? $egiBlockchain->buyer_user_id
                ?? ($egi->owner_id ?: $egi->user_id);

            $buyerWallet = $metadata['buyer_wallet'] ?? $egiBlockchain->buyer_wallet;

            $creatorName = $metadata['creator_display_name']
                ?? $egiBlockchain->creator_display_name
                ?? ($egi->creator ?? optional($egi->user)->name);

            $coCreatorName = $metadata['co_creator_display_name']
                ?? $egiBlockchain->co_creator_display_name;

            // Mint su Algorand
            $algorandResult = $this->algorandService->mintEgi($egi->id, $blockchainMetadata);

            // Aggiorna record con dati blockchain
            $updateData = [
                'asa_id' => $algorandResult['asaId'],
                'blockchain_tx_id' => $algorandResult['txId'],
                'anchor_hash' => $algorandResult['certificate_number'] ?? null,
                'platform_wallet' => $algorandResult['treasury_address'] ?? null,
                'mint_status' => 'minted',
                'minted_at' => now(),
                'mint_error' => null,
                'metadata' => $blockchainMetadata,
                'metadata_last_updated_at' => now(),
            ];

            if ($buyerUserId) {
                $updateData['buyer_user_id'] = $buyerUserId;
            }

            if ($buyerWallet) {
                $updateData['buyer_wallet'] = $buyerWallet;
                $updateData['ownership_type'] = 'wallet';
            }

            if ($creatorName) {
                $updateData['creator_display_name'] = $creatorName;
            }

            if ($coCreatorName) {
                $updateData['co_creator_display_name'] = $coCreatorName;
            }

            $egiBlockchain->update($updateData);

            // CRITICAL: Sync egis table per consistenza (isMinted(), token_EGI, status)
            // Stesso pattern di MintEgiJob.php
            $ownerUpdate = [
                'mint' => true,
                'token_EGI' => $algorandResult['asaId'],
                'status' => 'minted',
            ];

            if ($buyerUserId) {
                $ownerUpdate['owner_id'] = $buyerUserId;
                $ownerUpdate['co_creator_id'] = $buyerUserId; // Co-Creator: chi ha mintato (immutabile)
            }

            $egi->update($ownerUpdate);

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
