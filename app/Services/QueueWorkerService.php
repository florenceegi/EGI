<?php

declare(strict_types=1);

namespace App\Services;

use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\Artisan;

/**
 * Queue Worker Service - Auto-Start & Monitoring
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-13
 * @purpose Garantisce che il queue worker sia sempre attivo per processare tokenizzazioni
 *
 * WORKFLOW:
 * 1. Check se worker è attivo (cerca processo php artisan queue:*)
 * 2. Se non attivo → avvia automaticamente in background
 * 3. Log ULM per audit trail
 * 4. UEM alert se avvio fallisce
 *
 * USAGE:
 * - Chiamato automaticamente prima di dispatch job tokenizzazione
 * - Chiamato da health check endpoint
 * - Chiamato da cronjob ogni minuto (monitoring)
 */
class QueueWorkerService
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    /**
     * Queue name to monitor
     */
    protected string $queueName = 'blockchain';

    /**
     * Constructor
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Ensure queue worker is running for blockchain queue
     *
     * @return bool True if worker is running or started successfully
     */
    public function ensureWorkerRunning(): bool
    {
        try {
            if ($this->isWorkerRunning()) {
                $this->logger->debug('Queue worker already running', [
                    'queue' => $this->queueName,
                    'status' => 'healthy'
                ]);
                return true;
            }

            // Worker not running - attempt auto-start
            $this->logger->warning('Queue worker not running, attempting auto-start', [
                'queue' => $this->queueName
            ]);

            return $this->attemptWorkerAutoStart();
        } catch (\Exception $e) {
            $this->errorManager->handle('QUEUE_WORKER_CHECK_FAILED', [
                'queue' => $this->queueName,
                'error' => $e->getMessage()
            ], $e);

            return false;
        }
    }

    /**
     * Check if queue worker is currently running
     *
     * @return bool True if worker process found
     */
    protected function isWorkerRunning(): bool
    {
        try {
            // Check for queue:work or queue:listen process for blockchain queue
            $command = "ps aux | grep -E 'queue:(work|listen).*blockchain' | grep -v grep";
            $output = [];
            $returnVar = 0;

            exec($command, $output, $returnVar);

            $isRunning = !empty($output);

            $this->logger->debug('Queue worker status check', [
                'queue' => $this->queueName,
                'is_running' => $isRunning,
                'processes_found' => count($output)
            ]);

            return $isRunning;
        } catch (\Exception $e) {
            $this->logger->error('Failed to check worker status', [
                'queue' => $this->queueName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Attempt to auto-start queue worker
     *
     * @return bool True if start successful
     */
    protected function attemptWorkerAutoStart(): bool
    {
        try {
            $this->logger->info('Attempting to start queue worker', [
                'queue' => $this->queueName,
                'method' => 'background_process'
            ]);

            // Get base path
            $basePath = base_path();

            // Start worker in background using nohup
            $command = sprintf(
                'cd %s && nohup php artisan queue:work --queue=%s --tries=3 --timeout=60 > /dev/null 2>&1 &',
                escapeshellarg($basePath),
                escapeshellarg($this->queueName)
            );

            exec($command);

            // Wait a moment for process to start
            sleep(2);

            // Verify worker started
            if ($this->isWorkerRunning()) {
                $this->logger->info('Queue worker auto-start successful', [
                    'queue' => $this->queueName,
                    'verification' => 'passed'
                ]);

                return true;
            }

            $this->errorManager->handle('QUEUE_WORKER_AUTOSTART_FAILED', [
                'queue' => $this->queueName,
                'reason' => 'Worker not found after start command'
            ], new \Exception('Worker auto-start failed'));

            return false;
        } catch (\Exception $e) {
            $this->errorManager->handle('QUEUE_WORKER_AUTOSTART_EXCEPTION', [
                'queue' => $this->queueName,
                'error' => $e->getMessage()
            ], $e);

            return false;
        }
    }

    /**
     * Get worker status info for monitoring/debugging
     *
     * @return array Status information
     */
    public function getWorkerStatus(): array
    {
        $isRunning = $this->isWorkerRunning();

        // Get process details if running
        $processes = [];
        if ($isRunning) {
            $command = "ps aux | grep -E 'queue:(work|listen).*blockchain' | grep -v grep";
            exec($command, $processes);
        }

        return [
            'queue' => $this->queueName,
            'is_running' => $isRunning,
            'process_count' => count($processes),
            'processes' => $processes,
            'checked_at' => now()->toIso8601String()
        ];
    }

    /**
     * Force restart worker (kill existing + start new)
     *
     * @return bool True if restart successful
     */
    public function forceRestartWorker(): bool
    {
        try {
            $this->logger->info('Force restarting queue worker', [
                'queue' => $this->queueName
            ]);

            // Kill existing workers
            $killCommand = "pkill -f 'queue:(work|listen).*blockchain'";
            exec($killCommand);

            sleep(1);

            // Start new worker
            return $this->attemptWorkerAutoStart();
        } catch (\Exception $e) {
            $this->errorManager->handle('QUEUE_WORKER_RESTART_FAILED', [
                'queue' => $this->queueName,
                'error' => $e->getMessage()
            ], $e);

            return false;
        }
    }
}
