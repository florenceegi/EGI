<?php

namespace App\Jobs;

use App\Models\Egi;
use App\Services\AlgorandService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Tokenize PA Act Job - Blockchain Anchoring
 *
 * ============================================================================
 * CONTESTO - TOKENIZZAZIONE ASINCRONA ATTI PA
 * ============================================================================
 *
 * Job che ancora l'hash del documento PA su blockchain Algorand usando
 * il microservizio AlgoKit esistente.
 *
 * WORKFLOW:
 * 1. Riceve Egi model (atto PA già salvato in DB)
 * 2. Estrae hash documento da jsonMetadata
 * 3. Chiama AlgorandService->anchorDocument() [REAL, non mock]
 * 4. Aggiorna colonne pa_anchored, pa_anchored_at, jsonMetadata con TXID
 * 5. Log ULM + ErrorManager per audit trail
 *
 * QUEUE:
 * - Queue: 'pa_blockchain' (dedicata PA, separata da merchant 'blockchain')
 * - Timeout: 300 secondi (5 min)
 * - Tries: 3 (retry automatico se fallisce)
 * - Backoff: 60 secondi tra retry
 *
 * ESEMPIO METADATA DOPO TOKENIZZAZIONE:
 * ```json
 * {
 *   "doc_hash": "a3f7d9e2c1b8f4a6...",
 *   "protocol_number": "12345/2025",
 *   "protocol_date": "2025-09-15",
 *   "doc_type": "delibera",
 *   "signature_validation": {...},
 *   "public_code": "VER-ABC123XYZ",
 *   "anchor_txid": "ALGO-TX-20250915143022-A1B2C3D4",
 *   "anchored_at": "2025-09-15T14:35:00Z"
 * }
 * ```
 *
 * ============================================================================
 *
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Tokenization)
 * @date 2025-10-09
 * @purpose Asynchronous blockchain anchoring for PA administrative acts
 *
 * @architecture Job Layer (Queue Worker)
 * @dependencies AlgorandService, UltraLogManager, ErrorManager
 * @queue pa_blockchain
 * @timeout 300
 */
class TokenizePaActJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job timeout in seconds (5 minutes)
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Number of times the job may be attempted
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Number of seconds to wait before retrying
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The Egi model instance
     *
     * @var Egi
     */
    protected Egi $egi;

    /**
     * Create a new job instance
     *
     * @param Egi $egi The PA act to tokenize
     */
    public function __construct(Egi $egi)
    {
        $this->egi = $egi;
        $this->onQueue('pa_blockchain'); // Dedicated PA queue (separate from merchant 'blockchain')
    }

    /**
     * Execute the job
     *
     * @param AlgorandService $algorandService
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @return void
     *
     * @throws \Exception
     */
    public function handle(
        AlgorandService $algorandService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ): void {
        try {
            // 0. Update status to 'processing' and increment attempts
            $this->egi->increment('pa_tokenization_attempts');
            $this->egi->update([
                'pa_tokenization_status' => 'processing'
            ]);

            $logger->info('[TokenizePaActJob] Starting tokenization', [
                'egi_id' => $this->egi->id,
                'pa_protocol_number' => $this->egi->pa_protocol_number,
                'job_id' => $this->job->getJobId(),
                'attempt' => $this->egi->pa_tokenization_attempts
            ]);

            // 1. Validate: Check if already anchored
            if ($this->egi->pa_anchored) {
                $logger->warning('[TokenizePaActJob] Act already anchored, skipping', [
                    'egi_id' => $this->egi->id,
                    'pa_anchored_at' => $this->egi->pa_anchored_at
                ]);

                // Reset to completed status
                $this->egi->update([
                    'pa_tokenization_status' => 'completed',
                    'pa_tokenization_error' => null
                ]);
                return;
            }

            // 2. Extract document hash from jsonMetadata
            $metadata = $this->egi->jsonMetadata ?? [];
            $docHash = $metadata['doc_hash'] ?? null;

            if (!$docHash) {
                throw new \Exception('Document hash not found in jsonMetadata');
            }

            // 3. Prepare metadata for blockchain anchoring
            $anchorMetadata = [
                'egi_id' => $this->egi->id,
                'protocol_number' => $this->egi->pa_protocol_number,
                'protocol_date' => $this->egi->pa_protocol_date?->format('Y-m-d'),
                'doc_type' => $this->egi->pa_act_type,
                'public_code' => $this->egi->pa_public_code,
                'title' => $this->egi->title,
                'user_id' => $this->egi->user_id
            ];

            $logger->info('[TokenizePaActJob] Calling AlgorandService to anchor document', [
                'egi_id' => $this->egi->id,
                'doc_hash' => substr($docHash, 0, 16) . '...',
                'metadata_keys' => array_keys($anchorMetadata)
            ]);

            // 4. Anchor on Algorand blockchain via microservice (GDPR compliant)
            // CRITICAL: This uses REAL AlgorandService (App\Services\AlgorandService)
            // which calls AlgoKit microservice on port 3000
            // Pass the EGI owner (PA user) for GDPR audit trail
            $anchorResult = $algorandService->anchorDocument($docHash, $anchorMetadata, $this->egi->user);

            if (!$anchorResult['success']) {
                throw new \Exception('Blockchain anchoring failed: ' . ($anchorResult['error'] ?? 'Unknown error'));
            }

            // 5. Update Egi record with blockchain data + standard EGI fields
            $updatedMetadata = $metadata;
            $updatedMetadata['anchor_txid'] = $anchorResult['txid'];
            $updatedMetadata['anchored_at'] = now()->toIso8601String();
            $updatedMetadata['anchor_block'] = $anchorResult['block'] ?? null;
            $updatedMetadata['anchor_network'] = $anchorResult['network'] ?? 'algorand-testnet';

            $this->egi->update([
                // PA-specific fields
                'pa_anchored' => true,
                'pa_anchored_at' => now(),
                'pa_tokenization_status' => 'completed',
                'pa_tokenization_error' => null, // Clear any previous error

                // Standard EGI fields (consistency with Merchant flow)
                'mint' => 1,                     // Mark as minted/tokenized (boolean stored as tinyint)
                'status' => 'minted',            // Status minted (not published)
                'token_EGI' => $anchorResult['txid'], // Store TXID as token reference

                // Metadata
                'jsonMetadata' => $updatedMetadata
            ]);

            // Refresh model to get updated values
            $this->egi->refresh();

            $logger->info('[TokenizePaActJob] Tokenization completed successfully', [
                'egi_id' => $this->egi->id,
                'txid' => $anchorResult['txid'],
                'block' => $anchorResult['block'] ?? null,
                'pa_anchored_at' => $this->egi->pa_anchored_at,
                'total_attempts' => $this->egi->pa_tokenization_attempts,
                // Standard EGI fields verification
                'mint' => $this->egi->mint ? 'YES' : 'NO',
                'status' => $this->egi->status,
                'token_EGI' => $this->egi->token_EGI,
                'pa_tokenization_status' => $this->egi->pa_tokenization_status
            ]);
        } catch (\Exception $e) {
            // Sanitize error message (remove sensitive data for GDPR)
            $sanitizedError = $this->sanitizeErrorMessage($e->getMessage());

            // Update EGI with error details
            $this->egi->update([
                'pa_tokenization_status' => 'failed',
                'pa_tokenization_error' => $sanitizedError
            ]);

            $logger->error('[TokenizePaActJob] Tokenization failed', [
                'egi_id' => $this->egi->id,
                'error' => $sanitizedError,
                'attempt' => $this->egi->pa_tokenization_attempts,
                'will_retry' => $this->attempts() < $this->tries
            ]);

            // Use ErrorManager for structured error handling
            $errorManager->handle('PA_ACT_TOKENIZATION_FAILED', [
                'egi_id' => $this->egi->id,
                'pa_protocol_number' => $this->egi->pa_protocol_number,
                'error_message' => $sanitizedError,
                'job_id' => $this->job->getJobId(),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries
            ], $e);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Sanitize error message for GDPR compliance
     *
     * Removes sensitive data like file paths, API keys, wallet addresses
     *
     * @param string $message Raw error message
     * @return string Sanitized error message safe for storage
     * @privacy-safe Removes PII and sensitive system information
     */
    private function sanitizeErrorMessage(string $message): string
    {
        // Remove file paths
        $message = preg_replace('/\/[^\s]+/', '[PATH]', $message);

        // Remove potential API keys (alphanumeric strings > 20 chars)
        $message = preg_replace('/[a-zA-Z0-9]{20,}/', '[KEY]', $message);

        // Remove wallet addresses (Algorand format)
        $message = preg_replace('/[A-Z2-7]{58}/', '[WALLET]', $message);

        // Truncate if too long (max 500 chars for UI display)
        if (strlen($message) > 500) {
            $message = substr($message, 0, 497) . '...';
        }

        return $message;
    }

    /**
     * Handle a job failure after all retries exhausted
     *
     * Updates EGI with permanent failure status and sanitized error message.
     * Logs critical error for admin monitoring.
     *
     * @param \Throwable $exception Exception that caused the failure
     * @return void
     * @privacy-safe Error message sanitized before storage
     */
    public function failed(\Throwable $exception): void
    {
        // Sanitize error for GDPR compliance
        $sanitizedError = $this->sanitizeErrorMessage($exception->getMessage());

        // Update EGI with permanent failure status
        $this->egi->update([
            'pa_tokenization_status' => 'failed',
            'pa_tokenization_error' => $sanitizedError
        ]);

        // Log permanent failure after all retries exhausted
        app(UltraLogManager::class)->critical('[TokenizePaActJob] Job failed permanently after retries', [
            'egi_id' => $this->egi->id,
            'pa_protocol_number' => $this->egi->pa_protocol_number,
            'exception' => $sanitizedError,
            'total_attempts' => $this->egi->pa_tokenization_attempts,
            'max_tries' => $this->tries
        ]);

        // Optional: Send notification to admin or PA user
        // Notification::send(User::find($this->egi->user_id), new TokenizationFailedNotification($this->egi));
    }
}