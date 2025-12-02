<?php

namespace App\Http\Controllers\Api\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AiTraitGeneration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode API Controller: SuperAdmin AI Management
 * 🎯 Purpose: API per gestione AI consultations, credits, features, statistics
 */
class AiApiController extends Controller
{
    /**
     * Check if a table exists and has a specific column
     */
    private function hasTableColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    /**
     * Get AI consultations list
     */
    public function consultations(Request $request): JsonResponse
    {
        try {
            if (!Schema::hasTable('ai_trait_generations')) {
                return response()->json([
                    'data' => [],
                    'meta' => ['total' => 0, 'today' => 0, 'week' => 0],
                ]);
            }

            $consultations = AiTraitGeneration::latest()->paginate(20);

            $meta = [
                'total' => AiTraitGeneration::count(),
                'today' => AiTraitGeneration::whereDate('created_at', today())->count(),
                'week' => AiTraitGeneration::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            ];

            return response()->json([
                'data' => $consultations->map(fn($c) => [
                    'id' => $c->id,
                    'user_id' => $c->user_id ?? null,
                    'user_name' => $c->user?->name ?? 'N/A',
                    'egi_id' => $c->egi_id ?? null,
                    'egi_name' => $c->egi?->name ?? 'N/A',
                    'prompt' => $c->prompt ?? '',
                    'response' => $c->response ?? '',
                    'tokens_used' => $c->tokens_used ?? 0,
                    'model' => $c->model ?? 'gpt-4',
                    'created_at' => $c->created_at,
                ]),
                'meta' => $meta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0, 'today' => 0, 'week' => 0],
            ]);
        }
    }

    /**
     * Get AI credits management data
     */
    public function credits(Request $request): JsonResponse
    {
        try {
            $hasAiCredits = $this->hasTableColumn('users', 'ai_credits');
            $hasAiCreditsTotal = $this->hasTableColumn('users', 'ai_credits_total');
            $hasAiCreditsUsed = $this->hasTableColumn('users', 'ai_credits_used');
            
            $usersWithCredits = $hasAiCredits ? User::where('ai_credits', '>', 0)->count() : 0;
            $totalCreditsIssued = $hasAiCreditsTotal ? (int) User::sum('ai_credits_total') : 0;
            $totalCreditsUsed = $hasAiCreditsUsed ? (int) User::sum('ai_credits_used') : 0;
            $totalCreditsAvailable = $hasAiCredits ? (int) User::sum('ai_credits') : 0;

            return response()->json([
                'stats' => [
                    'total_credits_issued' => $totalCreditsIssued,
                    'total_credits_used' => $totalCreditsUsed,
                    'total_credits_available' => $totalCreditsAvailable,
                    'users_with_credits' => $usersWithCredits,
                ],
                'transactions' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'stats' => [
                    'total_credits_issued' => 0,
                    'total_credits_used' => 0,
                    'total_credits_available' => 0,
                    'users_with_credits' => 0,
                ],
                'transactions' => [],
            ]);
        }
    }

    /**
     * Get AI features configuration
     */
    public function features(Request $request): JsonResponse
    {
        try {
            $traitUsage = 0;
            if (Schema::hasTable('ai_trait_generations')) {
                $traitUsage = AiTraitGeneration::whereDate('created_at', today())->count();
            }

            $features = [
                [
                    'id' => 'ai_trait_generation',
                    'name' => 'Generazione Traits AI',
                    'description' => 'Genera automaticamente traits per EGI usando AI',
                    'enabled' => config('ai.features.trait_generation', true),
                    'limit' => config('ai.limits.traits_per_day', 100),
                    'usage' => $traitUsage,
                ],
                [
                    'id' => 'ai_description',
                    'name' => 'Descrizione AI',
                    'description' => 'Genera descrizioni intelligenti per EGI',
                    'enabled' => config('ai.features.description', true),
                    'limit' => null,
                    'usage' => 0,
                ],
                [
                    'id' => 'ai_curator',
                    'name' => 'AI Curator',
                    'description' => 'Suggerimenti curatoriali basati su AI',
                    'enabled' => config('ai.features.curator', false),
                    'limit' => null,
                    'usage' => 0,
                ],
                [
                    'id' => 'natan_chat',
                    'name' => 'N.A.T.A.N. Chat',
                    'description' => 'Assistente AI conversazionale',
                    'enabled' => true,
                    'limit' => config('ai.limits.natan_daily', 500),
                    'usage' => 0,
                ],
            ];

            return response()->json([
                'features' => $features,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'features' => [],
            ]);
        }
    }

    /**
     * Get AI statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            if (!Schema::hasTable('ai_trait_generations')) {
                return response()->json([
                    'total_requests' => 0,
                    'total_tokens' => 0,
                    'total_cost' => 0,
                    'avg_response_time' => 0,
                    'requests_by_day' => [],
                    'requests_by_model' => [],
                ]);
            }

            $totalRequests = AiTraitGeneration::count();
            
            // Verifica se la colonna tokens_used esiste
            $hasTokensUsed = $this->hasTableColumn('ai_trait_generations', 'tokens_used');
            $totalTokens = $hasTokensUsed ? (int) (AiTraitGeneration::sum('tokens_used') ?? 0) : 0;
            
            // Costo stimato (assumendo $0.03 per 1000 tokens per GPT-4)
            $totalCost = ($totalTokens / 1000) * 0.03;
            
            $avgResponseTime = 850; // ms placeholder

            $requestsByDay = AiTraitGeneration::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn($r) => ['date' => $r->date, 'count' => $r->count]);

            // Verifica se la colonna model esiste
            $hasModel = $this->hasTableColumn('ai_trait_generations', 'model');
            $requestsByModel = [];
            
            if ($hasModel && $hasTokensUsed) {
                $requestsByModel = AiTraitGeneration::selectRaw('COALESCE(model, "gpt-4") as model, COUNT(*) as count, SUM(tokens_used) as tokens')
                    ->groupBy('model')
                    ->get()
                    ->map(fn($r) => [
                        'model' => $r->model,
                        'count' => $r->count,
                        'tokens' => (int) $r->tokens,
                    ]);
            } elseif ($hasModel) {
                $requestsByModel = AiTraitGeneration::selectRaw('COALESCE(model, "gpt-4") as model, COUNT(*) as count')
                    ->groupBy('model')
                    ->get()
                    ->map(fn($r) => [
                        'model' => $r->model,
                        'count' => $r->count,
                        'tokens' => 0,
                    ]);
            }

            return response()->json([
                'total_requests' => $totalRequests,
                'total_tokens' => $totalTokens,
                'total_cost' => round($totalCost, 2),
                'avg_response_time' => $avgResponseTime,
                'requests_by_day' => $requestsByDay,
                'requests_by_model' => $requestsByModel,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'total_requests' => 0,
                'total_tokens' => 0,
                'total_cost' => 0,
                'avg_response_time' => 0,
                'requests_by_day' => [],
                'requests_by_model' => [],
            ]);
        }
    }
}
