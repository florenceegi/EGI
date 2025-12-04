<?php

/**
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Real Blockchain Integration)
 * @date 2025-10-08
 * @purpose REAL blockchain minting job - NO MOCK
 */

namespace App\Jobs;

use App\Models\EgiBlockchain;
use App\Services\EgiMintingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class MintEgiJob implements ShouldQueue {
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Job configuration for blockchain operations
     */
    public int $tries = 3;
    public int $timeout = 300; // 5 minutes for blockchain calls
    public int $backoff = 60;  // 1 minute between retries

    private int $egiBlockchainId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $egiBlockchainId) {
        $this->egiBlockchainId = $egiBlockchainId;
        $this->onQueue('blockchain'); // Dedicated blockchain queue
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array {
        return [
            new WithoutOverlapping($this->egiBlockchainId),
        ];
    }

    /**
     * Execute the job - REAL BLOCKCHAIN MINTING
     */
    public function handle(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        EgiMintingService $mintingService,
        \App\Services\CertificateGeneratorService $certificateService
    ): void {
        try {
            // 🚨 DEBUG: Log IMMEDIATELY at start
            $logger->emergency('🚨🚨🚨 MINT JOB HANDLE STARTED 🚨🚨🚨', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'pid' => getmypid(),
                'xdebug_loaded' => extension_loaded('xdebug') ? 'YES ✅' : 'NO ❌',
                'timestamp' => now()->format('H:i:s.u'),
                'log_category' => 'MINT_JOB_DEBUG'
            ]);

            // 1. Load blockchain record
            $egiBlockchain = EgiBlockchain::with(['egi', 'buyer'])
                ->findOrFail($this->egiBlockchainId);

            $logger->info('REAL blockchain minting job started', [
                'job_id' => $this->job->getJobId(),
                'egi_blockchain_id' => $this->egiBlockchainId,
                'egi_id' => $egiBlockchain->egi_id,
                'attempt' => $this->attempts()
            ]);

            // 2. Update status to processing
            $egiBlockchain->update([
                'mint_status' => 'minting'
            ]);

            // 3. REAL BLOCKCHAIN MINT (not mock!)
            // AREA 5.5.1: Pass proposed co-creator name to service
            // NOTE: mintingService->mintEgi() already updates the record with ALL data:
            // - metadata, creator_display_name, co_creator_display_name
            // - asa_id, blockchain_tx_id, anchor_hash, mint_status='minted', minted_at
            // So we don't need to update again here! Just get fresh instance.
            $result = $mintingService->mintEgi(
                $egiBlockchain->egi,
                [
                    'payment_reference' => $egiBlockchain->payment_reference,
                    'buyer_wallet' => $egiBlockchain->buyer_wallet,
                    'buyer_user_id' => $egiBlockchain->buyer_user_id,
                    'creator_display_name' => $egiBlockchain->creator_display_name
                        ?? optional($egiBlockchain->egi->user)->name
                        ?? $egiBlockchain->egi->creator,
                    'co_creator_display_name' => $egiBlockchain->co_creator_display_name, // User-provided name (optional)
                ]
            );

            // 4. Get fresh instance with updated data from service
            // (service already updated: metadata, names, blockchain data, status)
            $egiBlockchain = $result; // $result is already fresh() from service

            // 4.5. CRITICAL: Sync egis table with minting results
            // This ensures Card EGI displays correctly and isMinted() works
            // IMPORTANT: Use fresh() to avoid stale relationship cache
            $egi = \App\Models\Egi::find($egiBlockchain->egi_id);

            if ($egi) {
                $egi->loadMissing('collection.creator', 'collection.owner', 'user');
            }

            $egi->update([
                'owner_id' => $egiBlockchain->buyer_user_id, // New owner (buyer)
                'mint' => true,                               // Flag for isMinted() method
                'token_EGI' => $result->asa_id,              // Algorand ASA ID
                'status' => 'minted',                        // Status becomes 'minted'
            ]);

            $logger->info('REAL blockchain minting completed - egis table synced', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'egi_id' => $egi->id,
                'asa_id' => $result->asa_id,
                'tx_id' => $result->blockchain_tx_id,
                'owner_id_synced' => $egiBlockchain->buyer_user_id,
                'mint_flag' => true,
                'status' => 'minted',
                'token_EGI' => $result->asa_id
            ]);

            // 4.8. PAYMENT DISTRIBUTIONS OR EGILI REWARDS
            if ($egiBlockchain->payment_method === 'egili') {
                $this->rewardCreatorForEgiliPayment($egi, $egiBlockchain, $logger);
            } elseif ($egiBlockchain->paid_amount && $egiBlockchain->paid_amount > 0) {
                try {
                    $distributionService = app(\App\Services\PaymentDistributionService::class);
                    $distributions = $distributionService->recordMintDistribution(
                        $egiBlockchain->fresh(),
                        [
                            'paid_amount' => $egiBlockchain->paid_amount,
                            'paid_currency' => $egiBlockchain->paid_currency ?? 'EUR',
                            'payment_method' => $egiBlockchain->payment_method ?? 'fiat',
                        ]
                    );

                    $logger->info('Payment distributions created successfully', [
                        'egi_blockchain_id' => $this->egiBlockchainId,
                        'distributions_count' => count($distributions),
                        'total_distributed' => array_sum(array_column($distributions, 'amount_eur')),
                        'log_category' => 'MINT_DISTRIBUTION_SUCCESS'
                    ]);
                } catch (\Exception $distException) {
                    // Log warning but don't fail the mint (distributions can be created later)
                    $logger->warning('Payment distribution creation failed, mint successful', [
                        'egi_blockchain_id' => $this->egiBlockchainId,
                        'error' => $distException->getMessage(),
                        'log_category' => 'MINT_DISTRIBUTION_WARNING'
                    ]);

                    $errorManager->handle('MINT_DISTRIBUTION_FAILED', [
                        'egi_blockchain_id' => $this->egiBlockchainId,
                        'asa_id' => $result->asa_id,
                        'error' => $distException->getMessage()
                    ], $distException);
                }
            }

            // 5. Generate blockchain certificate (NUOVO)
            try {
                $certificate = $certificateService->generateBlockchainCertificate($egi, $egiBlockchain->fresh());

                $logger->info('Blockchain certificate generated', [
                    'egi_blockchain_id' => $this->egiBlockchainId,
                    'certificate_uuid' => $certificate->certificate_uuid,
                    'certificate_path' => $certificate->pdf_path
                ]);
            } catch (\Exception $certException) {
                // Certificate generation failure is NOT blocking - mint already completed
                $logger->warning('Certificate generation failed (mint completed successfully)', [
                    'egi_blockchain_id' => $this->egiBlockchainId,
                    'error' => $certException->getMessage()
                ]);

                $errorManager->handle('CERTIFICATE_GENERATION_FAILED_POST_MINT', [
                    'egi_blockchain_id' => $this->egiBlockchainId,
                    'asa_id' => $result->asa_id,
                    'error' => $certException->getMessage()
                ], $certException);
            }
        } catch (\Exception $e) {
            // Update error status
            $egiBlockchain = EgiBlockchain::find($this->egiBlockchainId);
            if ($egiBlockchain) {
                $egiBlockchain->update([
                    'mint_status' => 'failed',
                    'mint_error' => $e->getMessage()
                ]);
            }

            $errorManager->handle('REAL_BLOCKCHAIN_MINT_FAILED', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ], $e);

            // Re-throw for retry mechanism
            throw $e;
        }
    }

    /**
     * Reward Creator with Gift Egili when EGI is purchased with Egili
     *
     * Business Logic:
     * - Buyer's Egili were already burned in handleEgiliPayment() (MintController)
     * - Creator receives equivalent amount as GIFT Egili (expire in 365 days)
     * - Gift Egili have priority spending (used before lifetime Egili)
     *
     * This creates a circular economy:
     * - Buyer spends Egili → burned from their wallet
     * - Creator earns Gift Egili → incentivizes platform usage
     * - Gift Egili expire → encourages spending within platform
     *
     * @param \App\Models\Egi $egi The EGI that was minted
     * @param EgiBlockchain $egiBlockchain The blockchain record with payment info
     * @param \Ultra\UltraLogManager\UltraLogManager $logger Logger instance
     */
    private function rewardCreatorForEgiliPayment(
        \App\Models\Egi $egi,
        EgiBlockchain $egiBlockchain,
        \Ultra\UltraLogManager\UltraLogManager $logger
    ): void {
        try {
            /** @var \App\Services\EgiliService $egiliService */
            $egiliService = app(\App\Services\EgiliService::class);

            // Get creator (original owner of EGI)
            $creator = $egi->user;
            if (!$creator) {
                $logger->warning('Cannot reward creator - no user associated with EGI', [
                    'egi_id' => $egi->id,
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'log_category' => 'EGILI_REWARD_NO_CREATOR'
                ]);
                return;
            }

            // Get amount paid in Egili
            $egiliAmount = (int) $egiBlockchain->paid_amount;
            if ($egiliAmount <= 0) {
                $logger->warning('Cannot reward creator - no Egili amount recorded', [
                    'egi_id' => $egi->id,
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'paid_amount' => $egiBlockchain->paid_amount,
                    'log_category' => 'EGILI_REWARD_NO_AMOUNT'
                ]);
                return;
            }

            // Grant Gift Egili to creator (365 days expiration)
            // Using grantGiftFromSystem() which doesn't require admin
            $transaction = $egiliService->grantGiftFromSystem(
                $creator,
                $egiliAmount,
                365, // 1 year expiration
                'egi_sale_reward',
                [
                    'egi_id' => $egi->id,
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'buyer_user_id' => $egiBlockchain->buyer_user_id,
                    'payment_reference' => $egiBlockchain->payment_reference,
                    'egi_title' => $egi->title,
                ]
            );

            $logger->info('Creator rewarded with Gift Egili for sale', [
                'creator_id' => $creator->id,
                'egi_id' => $egi->id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'egili_amount' => $egiliAmount,
                'transaction_id' => $transaction->id,
                'expires_in_days' => 365,
                'log_category' => 'EGILI_CREATOR_REWARD_SUCCESS'
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the mint (reward can be retried manually)
            $logger->error('Failed to reward creator with Gift Egili', [
                'egi_id' => $egi->id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'error' => $e->getMessage(),
                'log_category' => 'EGILI_CREATOR_REWARD_FAILED'
            ]);

            // Handle via ErrorManager but don't throw
            $errorManager = app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class);
            $errorManager->handle('EGILI_CREATOR_REWARD_FAILED', [
                'egi_id' => $egi->id,
                'egi_blockchain_id' => $egiBlockchain->id,
                'creator_id' => $egi->user_id,
                'egili_amount' => $egiBlockchain->paid_amount,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Handle job failure after all retries
     * CRITICAL: Rollback EGI state + cleanup orphan records
     */
    public function failed(\Throwable $exception): void {
        $logger = app(\Ultra\UltraLogManager\UltraLogManager::class);

        $egiBlockchain = EgiBlockchain::find($this->egiBlockchainId);

        if ($egiBlockchain) {
            // Update final error status
            $egiBlockchain->update([
                'mint_status' => 'failed',
                'mint_error' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage()
            ]);

            // ROLLBACK: Ripristina stato EGI originale
            if ($egiBlockchain->egi) {
                $logger->warning('Rolling back EGI fields to pre-mint state after final failure', [
                    'egi_blockchain_id' => $egiBlockchain->id,
                    'egi_id' => $egiBlockchain->egi_id,
                    'current_status' => $egiBlockchain->egi->status,
                    'current_mint' => $egiBlockchain->egi->mint,
                    'current_owner_id' => $egiBlockchain->egi->owner_id,
                    'will_restore_to_user_id' => $egiBlockchain->egi->user_id
                ]);

                $egiBlockchain->egi->update([
                    'owner_id' => $egiBlockchain->egi->user_id, // Ripristina owner originale (creator)
                    'token' => null,           // Reset token
                    'status' => 'published',   // Torna a published
                    'token_EGI' => null,       // Reset ASA ID
                    'mint' => false            // Reset flag mint
                ]);

                $logger->info('EGI fields rolled back successfully', [
                    'egi_id' => $egiBlockchain->egi_id,
                    'restored_owner_id' => $egiBlockchain->egi->user_id,
                    'restored_status' => 'published'
                ]);
            }

            // CLEANUP: Elimina record orphan dopo fallimento definitivo
            $logger->warning('Deleting orphan EgiBlockchain record after final failure', [
                'egi_blockchain_id' => $egiBlockchain->id,
                'egi_id' => $egiBlockchain->egi_id,
                'buyer_user_id' => $egiBlockchain->buyer_user_id,
                'mint_status' => $egiBlockchain->mint_status,
                'mint_error' => $egiBlockchain->mint_error,
                'attempts' => $this->tries
            ]);

            $egiBlockchain->delete();

            $logger->info('Orphan EgiBlockchain record deleted', [
                'egi_blockchain_id' => $this->egiBlockchainId
            ]);
        }
    }
}
