<?php

namespace App\Services\AI\Features;

use App\Models\Egi;
use App\Models\User;
use App\Models\AiFeaturePricing;
use App\Services\AI\Features\DTOs\AiFeatureResult;
use App\Services\AI\Features\AiFeatureFactory;
use App\Services\Gdpr\AuditLogService;
use App\Services\EgiliService;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Services\AI\Features
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Unified Entry Point for all AI Features with Pricing + Credits Check
 * 
 * ARCHITECTURE PATTERN:
 * - Strategy Pattern: Handlers implement AiFeatureInterface
 * - Factory Pattern: AiFeatureFactory creates correct handler
 * - Orchestrator: Centralized pricing/credits check + delegation
 * 
 * WORKFLOW:
 * 1. Check feature exists in ai_feature_pricing
 * 2. Check user credits/payment (TODO: integrate EgiliService)
 * 3. Validate handler requirements
 * 4. Execute handler
 * 5. Audit trail (GDPR)
 */
class AiFeatureOrchestrator
{
    public function __construct(
        private AiFeatureFactory $factory,
        private AuditLogService $auditService,
        private EgiliService $egiliService,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Process AI Feature Request
     * 
     * @param string $featureCode Feature code from ai_feature_pricing
     * @param int $egiId EGI ID
     * @param int $userId User ID
     * @param array $params Handler-specific parameters
     * @return AiFeatureResult
     */
    public function process(
        string $featureCode,
        int $egiId,
        int $userId,
        array $params = []
    ): AiFeatureResult {
        
        $this->logger->info('[AiFeatureOrchestrator] Processing feature request', [
            'feature_code' => $featureCode,
            'egi_id' => $egiId,
            'user_id' => $userId,
            'params_count' => count($params),
        ]);

        try {
            // STEP 1: Check Feature Exists in Pricing DB
            $pricing = $this->checkFeaturePricing($featureCode);
            
            if (!$pricing) {
                return AiFeatureResult::failure(
                    message: "Feature '{$featureCode}' not found in pricing table",
                    featureCode: $featureCode,
                    egiId: $egiId,
                    userId: $userId
                );
            }

            // STEP 2: Check Feature is Active
            if (!$pricing->is_active) {
                return AiFeatureResult::failure(
                    message: "Feature '{$featureCode}' is currently inactive",
                    featureCode: $featureCode,
                    egiId: $egiId,
                    userId: $userId
                );
            }

            // STEP 3: Load EGI + User
            $egi = Egi::findOrFail($egiId);
            $user = User::findOrFail($userId);

            // STEP 4: Check Credits/Payment (if not free)
            if (!$pricing->is_free) {
                $creditsCheck = $this->checkUserCredits($user, $pricing);
                if (!$creditsCheck['sufficient']) {
                    return AiFeatureResult::failure(
                        message: $creditsCheck['message'],
                        featureCode: $featureCode,
                        egiId: $egiId,
                        userId: $userId,
                        metadata: ['required_cost' => $pricing->cost_egili ?? $pricing->cost_fiat_eur]
                    );
                }
            }

            // STEP 5: Create Handler via Factory
            $handler = $this->factory->create($featureCode);

            // STEP 6: Validate Handler Requirements
            if (!$handler->validate($egi, $user, $params)) {
                return AiFeatureResult::failure(
                    message: $handler->getValidationError() ?? 'Validation failed',
                    featureCode: $featureCode,
                    egiId: $egiId,
                    userId: $userId
                );
            }

            // STEP 7: Execute Handler
            $result = $handler->execute($egi, $user, $params);

            // STEP 7.5: Debit Egili if execution successful and not free
            if ($result->success && !$pricing->is_free && $pricing->cost_egili > 0) {
                $debitResult = $this->egiliService->debitForFeature(
                    user: $user,
                    featureCode: $featureCode,
                    amount: $pricing->cost_egili,
                    metadata: [
                        'egi_id' => $egiId,
                        'feature_code' => $featureCode,
                        'execution_result' => 'success',
                    ]
                );

                if (!$debitResult['success']) {
                    $this->logger->warning('[AiFeatureOrchestrator] Egili debit failed after execution', [
                        'user_id' => $user->id,
                        'feature_code' => $featureCode,
                        'amount' => $pricing->cost_egili,
                        'error' => $debitResult['message'] ?? 'Unknown error',
                    ]);
                    // Continue anyway - execution was successful, debit failure should be investigated separately
                } else {
                    $this->logger->info('[AiFeatureOrchestrator] Egili debited successfully', [
                        'user_id' => $user->id,
                        'amount' => $pricing->cost_egili,
                        'transaction_id' => $debitResult['transaction_id'] ?? null,
                    ]);
                }
            }

            // STEP 8: Audit Trail (GDPR)
            $this->auditService->logUserAction(
                user: $user,
                action: 'ai_feature_executed',
                context: [
                    'feature_code' => $featureCode,
                    'egi_id' => $egiId,
                    'success' => $result->success,
                    'cost_egili' => $pricing->cost_egili,
                ],
                category: GdprActivityCategory::AI_INTERACTION
            );

            $this->logger->info('[AiFeatureOrchestrator] Feature executed successfully', [
                'feature_code' => $featureCode,
                'egi_id' => $egiId,
                'success' => $result->success,
            ]);

            return $result;

        } catch (\Exception $e) {
            $this->errorManager->handle('AI_FEATURE_ORCHESTRATION_ERROR', [
                'feature_code' => $featureCode,
                'egi_id' => $egiId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ], $e);

            return AiFeatureResult::failure(
                message: 'Internal error processing AI feature',
                featureCode: $featureCode,
                egiId: $egiId,
                userId: $userId,
                metadata: ['error_type' => get_class($e)]
            );
        }
    }

    /**
     * Check feature exists in pricing table
     * 
     * @param string $featureCode
     * @return AiFeaturePricing|null
     */
    private function checkFeaturePricing(string $featureCode): ?AiFeaturePricing
    {
        return AiFeaturePricing::where('feature_code', $featureCode)->first();
    }

    /**
     * Check if user has sufficient credits
     * 
     * @param User $user
     * @param AiFeaturePricing $pricing
     * @return array ['sufficient' => bool, 'message' => string]
     */
    private function checkUserCredits(User $user, AiFeaturePricing $pricing): array
    {
        $requiredEgili = $pricing->cost_egili ?? 0;
        
        $this->logger->info('[AiFeatureOrchestrator] Checking user credits', [
            'user_id' => $user->id,
            'required_egili' => $requiredEgili,
            'feature_code' => $pricing->feature_code,
        ]);

        // Get user balance via EgiliService
        $balance = $this->egiliService->getBalance($user);
        
        $this->logger->info('[AiFeatureOrchestrator] User balance retrieved', [
            'user_id' => $user->id,
            'available_egili' => $balance['egili'] ?? 0,
            'required_egili' => $requiredEgili,
        ]);

        // Check if user has enough Egili
        $availableEgili = $balance['egili'] ?? 0;
        
        if ($availableEgili < $requiredEgili) {
            return [
                'sufficient' => false,
                'message' => sprintf(
                    'Crediti insufficienti. Disponibili: %d Egili, Richiesti: %d Egili',
                    $availableEgili,
                    $requiredEgili
                )
            ];
        }

        return [
            'sufficient' => true,
            'message' => sprintf('Crediti sufficienti (%d Egili disponibili)', $availableEgili)
        ];
    }
}

