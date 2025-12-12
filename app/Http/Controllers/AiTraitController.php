<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AiTraitGenerationService;
use App\Models\AiTraitGeneration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\FegiAuth;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * AiTraitController
 *
 * Controller per gestire AI Trait Generation endpoints
 *
 * Endpoints:
 * - POST /egis/{egi}/traits/generate - Richiedi generazione AI
 * - GET /traits/generations/{generation} - Vedi dettagli generation
 * - POST /traits/generations/{generation}/review - Review proposals
 * - POST /traits/generations/{generation}/apply - Applica traits approvati
 *
 * @package FlorenceEGI\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class AiTraitController extends Controller
{
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        private AiTraitGenerationService $aiTraitService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * POST /egis/{egi}/traits/generate
     *
     * Richiedi AI di generare N traits per un EGI
     *
     * @param int $egiId
     * @param Request $request
     * @return JsonResponse
     */
    public function generate(int $egiId, Request $request): JsonResponse
    {
        try {
            // Auth check
            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => __('egi_dual_arch.ai.unauthorized'),
                ], 401);
            }

            // Load EGI first to check constraints
            $egi = \App\Models\Egi::findOrFail($egiId);

            // Check if EGI is NOT minted (blockchain immutability)
            if (!is_null($egi->token_EGI)) {
                return response()->json([
                    'success' => false,
                    'error' => 'egi_minted',
                    'message' => 'Impossibile generare traits: questo EGI è già stato mintato su blockchain. I traits sono immutabili.',
                ], 403);
            }

            // Check ownership (only creator can generate traits)
            $userId = FegiAuth::id();
            if ($egi->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'error' => 'not_owner',
                    'message' => 'Solo il creator dell\'EGI può generare traits.',
                ], 403);
            }

            // Validation
            $validated = $request->validate([
                'requested_count' => 'required|integer|min:1|max:10',
            ]);
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Call service
            $generation = $this->aiTraitService->requestTraitGeneration(
                $egiId,
                $userId,
                $validated['requested_count'],
                $ipAddress,
                $userAgent
            );

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.ai.generation_started'),
                'data' => [
                    'generation' => $this->formatGeneration($generation),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'generation_failed',
                'message' => __('egi_dual_arch.ai.generation_failed'),
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /traits/generations/{generation}
     *
     * Recupera dettagli di una generation con proposals
     *
     * @param int $generationId
     * @return JsonResponse
     */
    public function show(int $generationId): JsonResponse
    {
        try {
            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => __('egi_dual_arch.ai.unauthorized'),
                ], 401);
            }

            $generation = AiTraitGeneration::with(['proposals', 'egi', 'user'])
                ->findOrFail($generationId);

            // Authorize: solo creator può vedere
            if ($generation->egi->user_id !== FegiAuth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'forbidden',
                    'message' => __('egi_dual_arch.ai.forbidden'),
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'generation' => $this->formatGeneration($generation),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'not_found',
                'message' => __('egi_dual_arch.ai.generation_not_found'),
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 404);
        }
    }

    /**
     * POST /traits/generations/{generation}/review
     *
     * User review proposals (approve/reject/modify)
     *
     * @param int $generationId
     * @param Request $request
     * @return JsonResponse
     */
    public function review(int $generationId, Request $request): JsonResponse
    {
        try {
            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => __('egi_dual_arch.ai.unauthorized'),
                ], 401);
            }

            // Validation
            $validated = $request->validate([
                'decisions' => 'required|array',
                'decisions.*.proposal_id' => 'required|integer|exists:ai_trait_proposals,id',
                'decisions.*.action' => 'required|in:approved,rejected,modified',
                'decisions.*.modifications' => 'nullable|array',
            ]);

            // Trasforma in formato atteso dal service
            $decisions = [];
            foreach ($validated['decisions'] as $decision) {
                $decisions[$decision['proposal_id']] = [
                    'action' => $decision['action'],
                    'modifications' => $decision['modifications'] ?? null,
                ];
            }

            $userId = FegiAuth::id();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Call service
            $generation = $this->aiTraitService->reviewProposals(
                $generationId,
                $userId,
                $decisions,
                $ipAddress,
                $userAgent
            );

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.ai.review_completed'),
                'data' => [
                    'generation' => $this->formatGeneration($generation),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'review_failed',
                'message' => __('egi_dual_arch.ai.review_failed'),
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /traits/generations/{generation}/apply
     *
     * Applica traits approvati all'EGI
     *
     * @param int $generationId
     * @param Request $request
     * @return JsonResponse
     */
    public function apply(int $generationId, Request $request): JsonResponse
    {
        try {
            if (!FegiAuth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthorized',
                    'message' => __('egi_dual_arch.ai.unauthorized'),
                ], 401);
            }

            $userId = FegiAuth::id();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Call service
            $result = $this->aiTraitService->applyTraits(
                $generationId,
                $userId,
                $ipAddress,
                $userAgent
            );

            return response()->json([
                'success' => true,
                'message' => __('egi_dual_arch.ai.traits_applied'),
                'data' => [
                    'generation' => $this->formatGeneration($result['generation']),
                    'created_traits_count' => count($result['created_traits']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'apply_failed',
                'message' => __('egi_dual_arch.ai.apply_failed'),
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Format generation per JSON response
     *
     * @param AiTraitGeneration $generation
     * @return array
     */
    private function formatGeneration(AiTraitGeneration $generation): array
    {
        return [
            'id' => $generation->id,
            'egi_id' => $generation->egi_id,
            'requested_count' => $generation->requested_count,
            'status' => $generation->status,
            'total_confidence' => $generation->total_confidence,
            'analysis_notes' => $generation->analysis_notes,
            'exact_matches_count' => $generation->exact_matches_count,
            'fuzzy_matches_count' => $generation->fuzzy_matches_count,
            'new_proposals_count' => $generation->new_proposals_count,
            'analyzed_at' => $generation->analyzed_at?->toIso8601String(),
            'reviewed_at' => $generation->reviewed_at?->toIso8601String(),
            'applied_at' => $generation->applied_at?->toIso8601String(),
            'success_rate' => $generation->success_rate,
            'proposals' => $generation->proposals->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'category_suggestion' => $proposal->category_suggestion,
                    'type_suggestion' => $proposal->type_suggestion,
                    'value_suggestion' => $proposal->value_suggestion,
                    'display_value_suggestion' => $proposal->display_value_suggestion,
                    'confidence' => $proposal->confidence,
                    'reasoning' => $proposal->reasoning,
                    'match_type' => $proposal->match_type,
                    'average_match_score' => $proposal->average_match_score,
                    'user_decision' => $proposal->user_decision,
                    'display_label' => $proposal->display_label,
                    'is_applied' => $proposal->isApplied(),
                ];
            }),
        ];
    }
}
