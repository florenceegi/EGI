<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ProjectDocument;
use App\Services\Projects\DocumentProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Process Document Job
 * 
 * Async queue job to process uploaded documents:
 * - Extract text from PDF/DOCX/TXT/CSV
 * - Chunk content
 * - Generate embeddings
 * - Update document status
 * 
 * QUEUE: default
 * TIMEOUT: 300 seconds (5 minutes)
 * RETRIES: 3 attempts
 * 
 * @package App\Jobs
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Projects RAG System)
 * @date 2025-10-27
 * @purpose Async document processing for RAG
 */
class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 300;

    /**
     * Document to process
     *
     * @var ProjectDocument
     */
    protected ProjectDocument $document;

    /**
     * Create a new job instance.
     *
     * @param ProjectDocument $document
     */
    public function __construct(ProjectDocument $document)
    {
        $this->document = $document;
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @param DocumentProcessingService $processingService
     * @param UltraLogManager $logger
     * @return void
     */
    public function handle(
        DocumentProcessingService $processingService,
        UltraLogManager $logger
    ): void {
        $logContext = [
            'job' => 'ProcessDocumentJob',
            'document_id' => $this->document->id,
            'project_id' => $this->document->project_id,
            'filename' => $this->document->filename,
            'attempt' => $this->attempts(),
        ];

        $logger->info('[ProcessDocumentJob] Starting document processing', $logContext);

        try {
            $success = $processingService->processDocument($this->document);

            if ($success) {
                $logger->info('[ProcessDocumentJob] Document processed successfully', $logContext);
            } else {
                $logger->warning('[ProcessDocumentJob] Document processing returned false', $logContext);
            }

        } catch (\Throwable $e) {
            $logger->error('[ProcessDocumentJob] Job failed with exception', [
                ...$logContext,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        $logger = app(UltraLogManager::class);

        $logger->error('[ProcessDocumentJob] Job failed after all retries', [
            'job' => 'ProcessDocumentJob',
            'document_id' => $this->document->id,
            'project_id' => $this->document->project_id,
            'filename' => $this->document->filename,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark document as failed
        $this->document->markAsFailed("Job failed after {$this->tries} attempts: {$exception->getMessage()}");
    }
}
