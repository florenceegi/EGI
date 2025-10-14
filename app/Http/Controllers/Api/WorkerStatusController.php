<?php

/**
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Worker Status API)
 * @date 2025-10-13
 * @purpose API endpoint to check queue worker status for frontend progress bar
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\QueueWorkerService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class WorkerStatusController extends Controller {
    protected QueueWorkerService $workerService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        QueueWorkerService $workerService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        // NO auth middleware - getStatus() is public for frontend progress bar
        // attemptStart() requires auth and is protected by route middleware
        $this->workerService = $workerService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get current worker status for frontend progress bar
     *
     * @return JsonResponse
     */
    public function getStatus(): JsonResponse {
        try {
            $status = $this->workerService->getWorkerStatus();

            // Determine user-friendly status
            if ($status['is_running'] && $status['process_count'] > 0) {
                $userStatus = 'ready';
                $message = __('mint.worker.api_ready');
                $canProceed = true;
            } elseif (!$status['is_running']) {
                $userStatus = 'starting';
                $message = __('mint.worker.api_starting');
                $canProceed = false;
            } else {
                $userStatus = 'checking';
                $message = __('mint.worker.api_checking');
                $canProceed = false;
            }

            return response()->json([
                'status' => $userStatus,
                'message' => $message,
                'can_proceed' => $canProceed,
                'technical_details' => [
                    'is_running' => $status['is_running'],
                    'process_count' => $status['process_count'],
                    'queue' => $status['queue'],
                ]
            ]);
        } catch (\Exception $e) {
            $this->errorManager->handle('WORKER_STATUS_CHECK_FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], $e);

            return response()->json([
                'status' => 'error',
                'message' => __('mint.worker.api_error'),
                'can_proceed' => false,
            ], 500);
        }
    }

    /**
     * Attempt to start worker (for admin/debug purposes)
     *
     * @return JsonResponse
     */
    public function attemptStart(): JsonResponse {
        try {
            $this->logger->info('Manual worker start requested', [
                'user_id' => auth()->id()
            ]);

            $success = $this->workerService->ensureWorkerRunning();

            if ($success) {
                return response()->json([
                    'status' => 'started',
                    'message' => __('mint.worker.api_start_success')
                ]);
            }

            return response()->json([
                'status' => 'failed',
                'message' => __('mint.worker.api_start_failed')
            ], 503);
        } catch (\Exception $e) {
            $this->errorManager->handle('WORKER_MANUAL_START_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ], $e);

            return response()->json([
                'status' => 'error',
                'message' => __('mint.worker.api_start_error')
            ], 500);
        }
    }
}
