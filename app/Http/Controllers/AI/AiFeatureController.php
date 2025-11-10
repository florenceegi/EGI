<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\Features\AiFeatureOrchestrator;
use App\Services\EgiliService;
use App\Models\AiFeaturePricing;
use App\Helpers\FegiAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package App\Http\Controllers\AI
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Orchestration)
 * @date 2025-11-04
 * @purpose Unified Controller for all AI Feature requests
 * 
 * UNIFIED ROUTE:
 * POST /api/ai/features/execute
 * 
 * REQUEST BODY:
 * {
 *   "feature_code": "ai_trait_generation",
 *   "egi_id": 123,
 *   "params": {
 *     "requested_count": 5
 *   }
 * }
 * 
 * RESPONSE:
 * {
 *   "success": true,
 *   "message": "...",
 *   "data": {...},
 *   "feature_code": "...",
 *   "egi_id": 123,
 *   "user_id": 456
 * }
 */
class AiFeatureController extends Controller
{
    public function __construct(
        private AiFeatureOrchestrator $orchestrator,
        private EgiliService $egiliService,
        private UltraLogManager $logger
    ) {
        // Auth middleware gestito dalla route group in routes/api.php
        // Non serve qui perché FegiAuth::user() controlla già
    }
    
    /**
     * Get pricing info for AI Feature (for confirmation dialog)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPricing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'feature_code' => 'required|string|max:100',
        ]);

        $featureCode = $validated['feature_code'];
        $user = FegiAuth::user();

        // Get pricing from DB
        $pricing = AiFeaturePricing::where('feature_code', $featureCode)->first();

        if (!$pricing) {
            $this->logger->warning('[AiFeatureController] Pricing missing', [
                'feature_code' => $featureCode,
                'user_id' => $user?->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('ai.features.errors.pricing_missing'),
            ], 422);
        }

        if (!$pricing->is_active) {
            $this->logger->warning('[AiFeatureController] Feature inactive', [
                'feature_code' => $featureCode,
                'user_id' => $user?->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('ai.features.errors.feature_inactive'),
            ], 422);
        }

        // Get user balance
        $balance = $this->egiliService->getBalance($user);
        $userBalance = is_array($balance) ? ($balance['egili'] ?? 0) : (int) $balance;
        $requiredEgili = $pricing->cost_egili ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'feature_code' => $pricing->feature_code,
                'feature_name' => $pricing->feature_name,
                'cost_egili' => $pricing->cost_egili ?? 0,
                'is_free' => $pricing->is_free,
                'user_balance' => $userBalance,
                'has_sufficient_credits' => $pricing->is_free ? true : $userBalance >= $requiredEgili,
            ]
        ]);
    }

    /**
     * Execute AI Feature (Unified Entry Point)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'feature_code' => 'required|string|max:100',
            'egi_id' => 'required|integer|exists:egis,id',
            'params' => 'sometimes|array',
        ]);

        $featureCode = $validated['feature_code'];
        $egiId = $validated['egi_id'];
        $params = $validated['params'] ?? [];

        // Add request metadata
        $params['ip_address'] = $request->ip();
        $params['user_agent'] = $request->userAgent();

        // Get authenticated user
        $userId = FegiAuth::id();

        $this->logger->info('[AiFeatureController] Feature request received', [
            'feature_code' => $featureCode,
            'egi_id' => $egiId,
            'user_id' => $userId,
        ]);

        // Process via Orchestrator
        $result = $this->orchestrator->process(
            featureCode: $featureCode,
            egiId: $egiId,
            userId: $userId,
            params: $params
        );

        // Return JSON response
        $statusCode = $result->success ? 200 : 400;
        
        return response()->json($result->toArray(), $statusCode);
    }
}

