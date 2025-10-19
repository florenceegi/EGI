<?php

namespace App\Services;

use App\Models\Egi;
use App\Models\EgiBlockchain;
use App\Models\EgiSmartContract;
use App\Models\User;
use App\Enums\EgiType;
use App\Enums\SmartContractStatus;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Service for deploying and managing EGI SmartContracts on Algorand
 */
class EgiSmartContractService
{
    private UltraLogManager $logger;
    private ErrorManagerInterface $errorManager;
    private AuditLogService $auditService;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Deploy EGI SmartContract on Algorand blockchain
     *
     * @param Egi $egi EGI model instance
     * @param User $user User deploying the SmartContract
     * @param array $options Deployment options (trigger_interval, metadata_hash, etc.)
     * @return EgiSmartContract Created SmartContract record
     * @throws \Exception
     */
    public function deploySmartContract(Egi $egi, User $user, array $options = []): EgiSmartContract
    {
        DB::beginTransaction();

        try {
            // 1. ULM: Log start
            $this->logger->info('SmartContract deployment initiated', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'log_category' => 'SMART_CONTRACT_DEPLOY_START'
            ]);

            // 2. Validate EGI is eligible for SmartContract deployment
            $this->validateEgiForSmartContract($egi);

            // 3. Prepare deployment parameters
            $triggerInterval = $options['trigger_interval'] ?? config('egi_living.ai_trigger_intervals.standard');
            $metadataHash = $options['metadata_hash'] ?? $this->generateMetadataHash($egi);
            $oracleAddress = config('egi_living.oracle.wallet_address');

            if (!$oracleAddress) {
                throw new \Exception('Oracle wallet address not configured');
            }

            // 4. Call microservice to deploy SmartContract
            $deploymentResult = $this->callDeploymentMicroservice([
                'egi_id' => $egi->id,
                'creator_address' => $user->wallet_address ?? config('algorand.treasury_address'),
                'oracle_address' => $oracleAddress,
                'trigger_interval' => $triggerInterval,
                'metadata_hash' => $metadataHash,
            ]);

            if (!$deploymentResult['success']) {
                throw new \Exception($deploymentResult['error'] ?? 'SmartContract deployment failed');
            }

            $data = $deploymentResult['data'];

            // 5. Create EgiSmartContract record
            $smartContract = EgiSmartContract::create([
                'egi_id' => $egi->id,
                'app_id' => $data['app_id'],
                'creator_address' => $data['creator_address'],
                'authorized_agent_address' => $oracleAddress,
                'deployment_tx_id' => $data['tx_id'],
                'deployed_at' => now(),
                'sc_status' => SmartContractStatus::ACTIVE->value,
                'trigger_interval' => $triggerInterval,
                'next_trigger_at' => now()->addSeconds($triggerInterval),
                'metadata_hash' => $metadataHash,
                'total_triggers_count' => 0,
                'ai_executions_success' => 0,
                'ai_executions_failed' => 0,
            ]);

            // 6. Update EGI record
            $egi->update([
                'egi_type' => EgiType::SMART_CONTRACT->value,
                'smart_contract_app_id' => $data['app_id'],
            ]);

            // 7. GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'smart_contract_deployed',
                [
                    'egi_id' => $egi->id,
                    'app_id' => $data['app_id'],
                    'tx_id' => $data['tx_id'],
                ],
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );

            // 8. ULM: Log success
            $this->logger->info('SmartContract deployment completed', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'app_id' => $data['app_id'],
                'log_category' => 'SMART_CONTRACT_DEPLOY_SUCCESS'
            ]);

            DB::commit();
            return $smartContract;
        } catch (\Exception $e) {
            DB::rollBack();

            // UEM: Error handling
            $this->errorManager->handle('SMART_CONTRACT_DEPLOY_FAILED', [
                'user_id' => $user->id,
                'egi_id' => $egi->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Validate EGI is eligible for SmartContract deployment
     *
     * @param Egi $egi
     * @throws \Exception
     */
    private function validateEgiForSmartContract(Egi $egi): void
    {
        // Check if EGI already has a SmartContract
        if ($egi->smart_contract_app_id) {
            throw new \Exception('EGI already has a SmartContract deployed');
        }

        // Check if EGI is already minted as ASA
        if ($egi->egi_type === EgiType::ASA->value && $egi->token_EGI) {
            throw new \Exception('EGI is already minted as ASA. Cannot convert to SmartContract');
        }

        // Check if EGI has valid metadata
        if (empty($egi->title)) {
            throw new \Exception('EGI must have a title to deploy SmartContract');
        }
    }

    /**
     * Generate IPFS metadata hash for EGI
     *
     * @param Egi $egi
     * @return string
     */
    private function generateMetadataHash(Egi $egi): string
    {
        // TODO: In production, upload metadata to IPFS and return actual hash
        // For now, return a placeholder
        $metadata = [
            'egi_id' => $egi->id,
            'title' => $egi->title,
            'description' => $egi->description,
            'created_at' => $egi->created_at->toIso8601String(),
        ];

        return 'Qm' . hash('sha256', json_encode($metadata));
    }

    /**
     * Call deployment microservice to deploy SmartContract
     *
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private function callDeploymentMicroservice(array $params): array
    {
        try {
            $microserviceUrl = config('algorand.microservice_url', 'http://localhost:3010');
            $endpoint = $microserviceUrl . '/deploy-smart-contract';

            $this->logger->info('Calling SmartContract deployment microservice', [
                'endpoint' => $endpoint,
                'egi_id' => $params['egi_id'],
                'log_category' => 'MICROSERVICE_CALL'
            ]);

            $response = Http::timeout(60)->post($endpoint, $params);

            if (!$response->successful()) {
                throw new \Exception('Microservice returned error: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            $this->logger->error('SmartContract deployment microservice call failed', [
                'error' => $e->getMessage(),
                'log_category' => 'MICROSERVICE_ERROR'
            ]);

            throw new \Exception('Failed to call deployment microservice: ' . $e->getMessage());
        }
    }

    /**
     * Trigger AI analysis for SmartContract
     *
     * @param EgiSmartContract $smartContract
     * @return bool
     */
    public function triggerAIAnalysis(EgiSmartContract $smartContract): bool
    {
        try {
            $this->logger->info('Triggering AI analysis for SmartContract', [
                'smart_contract_id' => $smartContract->id,
                'app_id' => $smartContract->app_id,
                'log_category' => 'AI_TRIGGER'
            ]);

            // TODO: Implement AI analysis trigger via Oracle service
            // For now, just update next trigger time

            $smartContract->update([
                'last_trigger_at' => now(),
                'next_trigger_at' => now()->addSeconds($smartContract->trigger_interval),
                'total_triggers_count' => $smartContract->total_triggers_count + 1,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('AI analysis trigger failed', [
                'smart_contract_id' => $smartContract->id,
                'error' => $e->getMessage(),
                'log_category' => 'AI_TRIGGER_ERROR'
            ]);

            return false;
        }
    }
}
