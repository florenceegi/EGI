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
                    'co_creator_display_name' => $egiBlockchain->co_creator_display_name, // User-provided name (optional)
                ]
            );

            // 4. Get fresh instance with updated data from service
            // (service already updated: metadata, names, blockchain data, status)
            $egiBlockchain = $result; // $result is already fresh() from service

            // 4.5. CRITICAL: Sync egis.owner_id with buyer_user_id
            // This ensures Policy checks and secondary market work correctly
            // IMPORTANT: Use fresh() to avoid stale relationship cache
            $egi = \App\Models\Egi::find($egiBlockchain->egi_id);

            $egi->update([
                'owner_id' => $egiBlockchain->buyer_user_id
            ]);

            $logger->info('REAL blockchain minting completed successfully', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'asa_id' => $result->asa_id,
                'tx_id' => $result->blockchain_tx_id,
                'owner_id_synced' => $egiBlockchain->buyer_user_id,
                'egi_owner_updated' => $egi->owner_id
            ]);

            // 4.8. PAYMENT DISTRIBUTIONS: Create after successful mint (AREA 2.2.2)
            if ($egiBlockchain->paid_amount && $egiBlockchain->paid_amount > 0) {
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
