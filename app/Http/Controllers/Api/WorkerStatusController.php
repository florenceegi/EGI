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

class WorkerStatusController extends Controller
{
    protected QueueWorkerService $workerService;
    protected UltraLogManager $logger;

    public function __construct(QueueWorkerService $workerService, UltraLogManager $logger)
    {
        $this->middleware('auth');
        $this->workerService = $workerService;
        $this->logger = $logger;
    }

    /**
     * Get current worker status for frontend progress bar
     *
     * @return JsonResponse
     */
    public function getStatus(): JsonResponse
    {
        try {
            $status = $this->workerService->getWorkerStatus();

            // Determine user-friendly status
            if ($status['is_running'] && $status['process_count'] > 0) {
                $userStatus = 'ready';
                $message = 'Sistema pronto per elaborare il mint';
                $canProceed = true;
            } elseif (!$status['is_running']) {
                $userStatus = 'starting';
                $message = 'Avvio sistema di elaborazione in corso...';
                $canProceed = false;
            } else {
                $userStatus = 'checking';
                $message = 'Verifica disponibilità sistema...';
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
            $this->logger->error('Worker status check failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Impossibile verificare lo stato del sistema',
                'can_proceed' => false,
            ], 500);
        }
    }

    /**
     * Attempt to start worker (for admin/debug purposes)
     *
     * @return JsonResponse
     */
    public function attemptStart(): JsonResponse
    {
        try {
            $this->logger->info('Manual worker start requested', [
                'user_id' => auth()->id()
            ]);

            $success = $this->workerService->ensureWorkerRunning();

            if ($success) {
                return response()->json([
                    'status' => 'started',
                    'message' => 'Worker avviato con successo'
                ]);
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'Impossibile avviare il worker. Contattare l\'amministratore.'
            ], 503);

        } catch (\Exception $e) {
            $this->logger->error('Manual worker start failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Errore durante avvio worker'
            ], 500);
        }
    }
}
