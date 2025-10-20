<?php

namespace App\Services;

use App\Models\EgiSmartContract;
use App\Enums\SmartContractStatus;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Oracle service - bridge between AI (N.A.T.A.N) and Blockchain (SmartContracts)
 */
class EgiOracleService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Poll SmartContracts for pending AI triggers
     * This method should be called by a scheduled job (e.g., every minute)
     *
     * @return array Processing results
     */
    public function pollForTriggers(): array
    {
        try {
            // Check if oracle is enabled
            if (!config('egi_living.feature_flags.oracle_enabled')) {
                return ['status' => 'disabled', 'message' => 'Oracle service is disabled'];
            }

            $this->logger->info('Oracle polling for pending triggers', [
                'log_category' => 'ORACLE_POLL'
            ]);

            // Get SmartContracts ready for AI trigger
            $batchSize = config('egi_living.oracle.polling.batch_size', 10);
            $smartContracts = EgiSmartContract::readyForTrigger()
                ->limit($batchSize)
                ->get();

            if ($smartContracts->isEmpty()) {
                $this->logger->debug('No SmartContracts ready for trigger', [
                    'log_category' => 'ORACLE_POLL_EMPTY'
                ]);

                return ['status' => 'idle', 'processed' => 0];
            }

            $results = [];
            foreach ($smartContracts as $sc) {
                try {
                    $result = $this->processTrigger($sc);
                    $results[] = [
                        'smart_contract_id' => $sc->id,
                        'app_id' => $sc->app_id,
                        'success' => $result['success'],
                    ];
                } catch (\Exception $e) {
                    $this->logger->error('Trigger processing failed', [
                        'smart_contract_id' => $sc->id,
                        'error' => $e->getMessage(),
                        'log_category' => 'ORACLE_TRIGGER_ERROR'
                    ]);

                    $results[] = [
                        'smart_contract_id' => $sc->id,
                        'app_id' => $sc->app_id,
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $successCount = count(array_filter($results, fn($r) => $r['success']));

            $this->logger->info('Oracle polling completed', [
                'total' => count($results),
                'success' => $successCount,
                'failed' => count($results) - $successCount,
                'log_category' => 'ORACLE_POLL_COMPLETE'
            ]);

            return [
                'status' => 'completed',
                'processed' => count($results),
                'success' => $successCount,
                'results' => $results,
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('ORACLE_POLL_FAILED', [
                'error_message' => $e->getMessage()
            ], $e);

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Process AI trigger for a single SmartContract
     *
     * @param EgiSmartContract $smartContract
     * @return array Processing result
     * @throws \Exception
     */
    public function processTrigger(EgiSmartContract $smartContract): array
    {
        DB::beginTransaction();

        try {
            $this->logger->info('Processing AI trigger for SmartContract', [
                'smart_contract_id' => $smartContract->id,
                'app_id' => $smartContract->app_id,
                'log_category' => 'ORACLE_TRIGGER_PROCESS'
            ]);

            // 1. Execute AI analysis via N.A.T.A.N
            $aiResult = $this->executeAIAnalysis($smartContract);

            // 2. Update SmartContract state on blockchain
            $updateResult = $this->updateSmartContractState($smartContract, $aiResult);

            // 3. Update local database record
            $smartContract->update([
                'last_trigger_at' => now(),
                'next_trigger_at' => now()->addSeconds($smartContract->trigger_interval),
                'total_triggers_count' => $smartContract->total_triggers_count + 1,
                'ai_executions_success' => $smartContract->ai_executions_success + 1,
                'last_ai_result' => $aiResult,
                'last_ai_result_at' => now(),
            ]);

            DB::commit();

            $this->logger->info('AI trigger processed successfully', [
                'smart_contract_id' => $smartContract->id,
                'app_id' => $smartContract->app_id,
                'log_category' => 'ORACLE_TRIGGER_SUCCESS'
            ]);

            return [
                'success' => true,
                'ai_result' => $aiResult,
                'blockchain_update' => $updateResult,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            // Update error tracking
            $smartContract->update([
                'ai_executions_failed' => $smartContract->ai_executions_failed + 1,
                'last_error' => $e->getMessage(),
                'last_error_at' => now(),
            ]);

            $this->logger->error('AI trigger processing failed', [
                'smart_contract_id' => $smartContract->id,
                'error' => $e->getMessage(),
                'log_category' => 'ORACLE_TRIGGER_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Execute AI analysis for SmartContract via N.A.T.A.N
     *
     * @param EgiSmartContract $smartContract
     * @return array AI analysis result
     * @throws \Exception
     */
    private function executeAIAnalysis(EgiSmartContract $smartContract): array
    {
        try {
            $timeout = config('egi_living.oracle.ai_timeouts.analysis_seconds', 120);

            $this->logger->info('Executing AI analysis', [
                'smart_contract_id' => $smartContract->id,
                'egi_id' => $smartContract->egi_id,
                'log_category' => 'ORACLE_AI_ANALYSIS'
            ]);

            // TODO: Integrate with N.A.T.A.N AI service
            // For now, return mock data

            $result = [
                'timestamp' => now()->toIso8601String(),
                'egi_id' => $smartContract->egi_id,
                'analysis' => [
                    'sentiment' => 'positive',
                    'engagement_score' => 0.85,
                    'recommended_actions' => ['promote_on_social', 'update_description'],
                ],
                'metadata_hash' => 'Qm' . hash('sha256', json_encode([
                    'egi_id' => $smartContract->egi_id,
                    'timestamp' => now()->timestamp,
                ])),
            ];

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('AI analysis execution failed', [
                'smart_contract_id' => $smartContract->id,
                'error' => $e->getMessage(),
                'log_category' => 'ORACLE_AI_ANALYSIS_ERROR'
            ]);

            throw new \Exception('AI analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * Update SmartContract state on blockchain
     *
     * @param EgiSmartContract $smartContract
     * @param array $aiResult AI analysis result
     * @return array Update result
     * @throws \Exception
     */
    private function updateSmartContractState(EgiSmartContract $smartContract, array $aiResult): array
    {
        try {
            $timeout = config('egi_living.oracle.ai_timeouts.update_state_seconds', 30);
            $microserviceUrl = config('algorand.microservice_url', 'http://localhost:3010');

            $this->logger->info('Updating SmartContract state on blockchain', [
                'smart_contract_id' => $smartContract->id,
                'app_id' => $smartContract->app_id,
                'log_category' => 'ORACLE_STATE_UPDATE'
            ]);

            // Call microservice to update SmartContract state
            $response = Http::timeout($timeout)->post("{$microserviceUrl}/update-smart-contract-state", [
                'app_id' => $smartContract->app_id,
                'metadata_hash' => $aiResult['metadata_hash'],
                'oracle_private_key' => config('egi_living.oracle.private_key'), // TODO: Secure key management
            ]);

            if (!$response->successful()) {
                throw new \Exception('Blockchain update failed: ' . $response->body());
            }

            $result = $response->json();

            // Update local SmartContract record
            $smartContract->update([
                'metadata_hash' => $aiResult['metadata_hash'],
                'state_last_synced_at' => now(),
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('SmartContract state update failed', [
                'smart_contract_id' => $smartContract->id,
                'error' => $e->getMessage(),
                'log_category' => 'ORACLE_STATE_UPDATE_ERROR'
            ]);

            throw new \Exception('Blockchain update failed: ' . $e->getMessage());
        }
    }

    /**
     * Listen for SmartContract events from blockchain
     * This method can be called by a long-running listener process
     *
     * @param callable $callback Callback to handle each event
     * @return void
     */
    public function listenForEvents(callable $callback): void
    {
        // TODO: Implement blockchain event listener
        // This would use Algorand Indexer API to poll for application calls
        // and invoke the callback for each relevant event

        $this->logger->info('Event listener not yet implemented', [
            'log_category' => 'ORACLE_LISTENER'
        ]);
    }

    /**
     * Manually trigger AI analysis for a specific SmartContract
     *
     * @param EgiSmartContract $smartContract
     * @return array Trigger result
     */
    public function manualTrigger(EgiSmartContract $smartContract): array
    {
        $this->logger->info('Manual AI trigger requested', [
            'smart_contract_id' => $smartContract->id,
            'app_id' => $smartContract->app_id,
            'log_category' => 'ORACLE_MANUAL_TRIGGER'
        ]);

        return $this->processTrigger($smartContract);
    }
}

