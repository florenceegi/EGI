<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\{EgiBlockchain, User};
use App\Services\EgiMintingService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{DB, Mail, Notification};
use Carbon\Carbon;
use Exception;

/**
 * @Oracode Job: Process EGI Minting (Async)
 * 🎯 Purpose: Async job for EGI blockchain minting with retry logic and notifications
 * 🧱 Core Logic: Coordinate minting service, handle failures, notify users
 * 🛡️ MiCA-SAFE: Blockchain operations only, no crypto custody
 *
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Async EGI minting with retry, progress tracking, and user notifications
 */
class ProcessEgiMintingJob implements ShouldQueue {
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Job configuration
     */
    public int $tries = 3;
    public int $timeout = 300; // 5 minutes
    public int $backoff = 60; // 1 minute between retries
    public bool $failOnTimeout = true;

    /**
     * Job data
     */
    private int $egiBlockchainId;
    private ?string $webhookId;
    private array $jobMetadata;

    /**
     * Create a new job instance
     *
     * @param int $egiBlockchainId EgiBlockchain record ID
     * @param string|null $webhookId Optional webhook tracking ID
     * @param array $jobMetadata Additional job metadata
     */
    public function __construct(
        int $egiBlockchainId,
        ?string $webhookId = null,
        array $jobMetadata = []
    ) {
        $this->egiBlockchainId = $egiBlockchainId;
        $this->webhookId = $webhookId;
        $this->jobMetadata = $jobMetadata;

        // Configure job queue
        $this->onQueue('blockchain');

        // Add delay if specified in metadata
        if (isset($jobMetadata['delay_seconds'])) {
            $this->delay($jobMetadata['delay_seconds']);
        }
    }

    /**
     * Execute the job
     *
     * @param UltraLogManager $logger Ultra logging manager
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param AuditLogService $auditService GDPR audit service
     * @param EgiMintingService $mintingService EGI minting service
     * @return void
     * @throws Exception Job execution failed
     */
    public function handle(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        EgiMintingService $mintingService
    ): void {
        $jobId = $this->job->getJobId();
        $attempt = $this->attempts();

        try {
            // 1. ULM: Log job start
            $logger->info('EGI minting job started', [
                'job_id' => $jobId,
                'egi_blockchain_id' => $this->egiBlockchainId,
                'webhook_id' => $this->webhookId,
                'attempt' => $attempt,
                'max_tries' => $this->tries,
                'queue' => 'blockchain'
            ]);

            // 2. Load EgiBlockchain record
            $egiBlockchain = EgiBlockchain::with(['egi', 'buyerUser'])
                ->findOrFail($this->egiBlockchainId);

            // 3. Validate job can proceed
            $this->validateJobPreconditions($egiBlockchain, $logger);

            // 4. Update status to processing
            $egiBlockchain->update([
                'mint_status' => 'minting',
                'minting_started_at' => now(),
                'job_id' => $jobId,
                'last_attempt' => $attempt
            ]);

            // 5. Execute minting process
            $user = $egiBlockchain->buyerUser ?? User::find(1); // fallback to admin
            $mintResult = $mintingService->mintEgi($egiBlockchain->egi, $user, [
                'job_id' => $jobId,
                'webhook_id' => $this->webhookId,
                'payment_reference' => $egiBlockchain->payment_reference
            ]);

            // 6. Update blockchain record with results
            $egiBlockchain->update([
                'mint_status' => 'minted',
                'asa_id' => $mintResult->asa_id,
                'blockchain_tx_id' => $mintResult->blockchain_tx_id,
                'anchor_hash' => $mintResult->anchor_hash,
                'minted_at' => now(),
                'mint_error' => null,
                'job_completed_at' => now()
            ]);

            // 6.5. CRITICAL: Sync egis.owner_id with buyer_user_id
            // This ensures Policy checks and secondary market work correctly
            $egi = \App\Models\Egi::find($egiBlockchain->egi_id);
            $egi->update([
                'owner_id' => $egiBlockchain->buyer_user_id
            ]);

            // 7. GDPR: Audit trail
            if ($user) {
                $auditService->logUserAction(
                    $user,
                    'EGI minting completed via async job',
                    [
                        'egi_id' => $egiBlockchain->egi_id,
                        'asa_id' => $mintResult->asa_id,
                        'job_id' => $jobId,
                        'attempt' => $attempt,
                        'webhook_id' => $this->webhookId
                    ],
                    GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                );
            }

            // 8. Send success notification
            $this->sendSuccessNotification($egiBlockchain, $logger);

            // 9. ULM: Log success
            $logger->info('EGI minting job completed successfully', [
                'job_id' => $jobId,
                'egi_blockchain_id' => $this->egiBlockchainId,
                'asa_id' => $mintResult->asa_id,
                'processing_time' => now()->diffInSeconds($egiBlockchain->minting_started_at),
                'attempt' => $attempt
            ]);
        } catch (Exception $e) {
            // 10. Handle job failure
            $this->handleJobFailure($e, $logger, $errorManager, $auditService, $jobId, $attempt);

            // Re-throw to trigger Laravel's retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure
     *
     * @param Exception $exception The exception that caused failure
     * @param UltraLogManager $logger Ultra logging manager
     * @param ErrorManagerInterface $errorManager Ultra error manager
     * @param AuditLogService $auditService GDPR audit service
     * @param string $jobId Job identifier
     * @param int $attempt Current attempt number
     * @return void
     */
    private function handleJobFailure(
        Exception $exception,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService,
        string $jobId,
        int $attempt
    ): void {
        try {
            // Update EgiBlockchain with failure info
            $egiBlockchain = EgiBlockchain::find($this->egiBlockchainId);
            if ($egiBlockchain) {
                $status = $attempt >= $this->tries ? 'failed' : 'minting_queued';

                $egiBlockchain->update([
                    'mint_status' => $status,
                    'mint_error' => substr($exception->getMessage(), 0, 500),
                    'last_attempt' => $attempt,
                    'failed_at' => $attempt >= $this->tries ? now() : null
                ]);

                // GDPR: Audit trail for failure
                if ($egiBlockchain->buyerUser) {
                    $auditService->logUserAction(
                        $egiBlockchain->buyerUser,
                        'EGI minting job failed',
                        [
                            'job_id' => $jobId,
                            'attempt' => $attempt,
                            'error' => $exception->getMessage(),
                            'will_retry' => $attempt < $this->tries
                        ],
                        GdprActivityCategory::BLOCKCHAIN_ACTIVITY
                    );
                }

                // Send failure notification if final attempt
                if ($attempt >= $this->tries) {
                    $this->sendFailureNotification($egiBlockchain, $exception, $logger);
                }
            }

            // UEM: Error handling
            $errorManager->handle('EGI_MINTING_JOB_FAILED', [
                'job_id' => $jobId,
                'egi_blockchain_id' => $this->egiBlockchainId,
                'attempt' => $attempt,
                'max_tries' => $this->tries,
                'error' => $exception->getMessage(),
                'webhook_id' => $this->webhookId
            ], $exception);
        } catch (Exception $handlingException) {
            // Log handling failure but don't throw
            $logger->error('Failed to handle job failure', [
                'job_id' => $jobId,
                'original_error' => $exception->getMessage(),
                'handling_error' => $handlingException->getMessage()
            ]);
        }
    }

    /**
     * Validate job can proceed with minting
     *
     * @param EgiBlockchain $egiBlockchain Blockchain record
     * @param UltraLogManager $logger Ultra logging manager
     * @return void
     * @throws Exception Validation failed
     */
    private function validateJobPreconditions(EgiBlockchain $egiBlockchain, UltraLogManager $logger): void {
        // Check if already minted
        if ($egiBlockchain->mint_status === 'minted') {
            throw new Exception("EGI #{$egiBlockchain->egi_id} is already minted (ASA: {$egiBlockchain->asa_id})");
        }

        // Check if payment completed (for webhook-triggered jobs)
        if ($this->webhookId && $egiBlockchain->payment_status !== 'completed') {
            throw new Exception("Payment not completed for EGI #{$egiBlockchain->egi_id}");
        }

        // Check if EGI still exists
        if (!$egiBlockchain->egi) {
            throw new Exception("EGI #{$egiBlockchain->egi_id} not found");
        }

        $logger->info('Job precondition validation passed', [
            'egi_blockchain_id' => $this->egiBlockchainId,
            'mint_status' => $egiBlockchain->mint_status,
            'payment_status' => $egiBlockchain->payment_status
        ]);
    }

    /**
     * Send success notification to user
     *
     * @param EgiBlockchain $egiBlockchain Completed blockchain record
     * @param UltraLogManager $logger Ultra logging manager
     * @return void
     */
    private function sendSuccessNotification(EgiBlockchain $egiBlockchain, UltraLogManager $logger): void {
        try {
            if ($egiBlockchain->buyerUser) {
                // TODO: Implement actual notification sending
                // For now, just log the intent
                $logger->info('Success notification queued', [
                    'user_id' => $egiBlockchain->buyerUser->id,
                    'egi_id' => $egiBlockchain->egi_id,
                    'asa_id' => $egiBlockchain->asa_id,
                    'notification_type' => 'egi_minting_success'
                ]);
            }
        } catch (Exception $e) {
            $logger->warning('Failed to send success notification', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send failure notification to user and admin
     *
     * @param EgiBlockchain $egiBlockchain Failed blockchain record
     * @param Exception $exception Failure exception
     * @param UltraLogManager $logger Ultra logging manager
     * @return void
     */
    private function sendFailureNotification(EgiBlockchain $egiBlockchain, Exception $exception, UltraLogManager $logger): void {
        try {
            // TODO: Implement actual notification sending
            // For now, just log the intent
            $logger->error('Failure notification queued', [
                'user_id' => $egiBlockchain->buyerUser?->id,
                'egi_id' => $egiBlockchain->egi_id,
                'error' => $exception->getMessage(),
                'notification_type' => 'egi_minting_failure'
            ]);
        } catch (Exception $e) {
            $logger->warning('Failed to send failure notification', [
                'egi_blockchain_id' => $this->egiBlockchainId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job
     *
     * @return int Seconds to wait
     */
    public function backoff(): int {
        // Exponential backoff: 60s, 120s, 180s
        return $this->backoff * $this->attempts();
    }

    /**
     * Get the tags that should be assigned to the job
     *
     * @return array Job tags for monitoring
     */
    public function tags(): array {
        return [
            'egi-minting',
            'blockchain',
            "egi-blockchain:{$this->egiBlockchainId}",
            $this->webhookId ? "webhook:{$this->webhookId}" : 'direct-minting'
        ];
    }
}