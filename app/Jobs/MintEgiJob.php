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
                $egiBlockchain->buyer,
                [
                    'payment_reference' => $egiBlockchain->payment_reference,
                    'buyer_wallet' => $egiBlockchain->buyer_wallet,
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

            // 5. Generate blockchain certificate (NUOVO)
            try {
                $certificatePath = $certificateService->generateBlockchainCertificate($egiBlockchain->fresh());

                $logger->info('Blockchain certificate generated', [
                    'egi_blockchain_id' => $this->egiBlockchainId,
                    'certificate_path' => $certificatePath,
                    'certificate_uuid' => $egiBlockchain->certificate_uuid
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
     */
    public function failed(\Throwable $exception): void {
        $egiBlockchain = EgiBlockchain::find($this->egiBlockchainId);
        if ($egiBlockchain) {
            $egiBlockchain->update([
                'mint_status' => 'failed',
                'mint_error' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage()
            ]);
        }
    }
}
