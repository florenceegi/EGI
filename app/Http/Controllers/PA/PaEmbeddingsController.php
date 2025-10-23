<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\PaActEmbedding;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * PA Embeddings Controller
 * 
 * Gestisce la generazione e monitoring dei vector embeddings per semantic search.
 * 
 * Features:
 * - Dashboard embeddings con statistiche
 * - Generazione batch via UI con progress
 * - Status monitoring real-time
 * 
 * @package App\Http\Controllers\PA
 * @author Claude Sonnet 4.5 for Fabio Cherici
 * @date 2025-10-23
 */
class PaEmbeddingsController extends Controller
{
    protected EmbeddingService $embeddingService;
    protected UltraLogManager $logger;

    public function __construct(
        EmbeddingService $embeddingService,
        UltraLogManager $logger
    ) {
        $this->middleware(['auth']);
        $this->embeddingService = $embeddingService;
        $this->logger = $logger;
    }

    /**
     * Show embeddings dashboard
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasRole('pa_entity')) {
            abort(403, 'Accesso negato. Ruolo PA Entity richiesto.');
        }

        $this->logger->info('[PA Embeddings] Dashboard accessed', [
            'user_id' => $user->id,
        ]);

        // Get statistics
        $stats = $this->getStats($user);

        return view('pa.embeddings.index', compact('stats'));
    }

    /**
     * Get embeddings statistics (API)
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();
        $stats = $this->getStats($user);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Generate embeddings for acts without embeddings
     */
    public function generate(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('pa_entity')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $limit = $request->input('limit', 100);
        $force = $request->boolean('force', false);

        $this->logger->info('[PA Embeddings] Generation started', [
            'user_id' => $user->id,
            'limit' => $limit,
            'force' => $force,
        ]);

        try {
            // Get acts without embeddings
            $query = Egi::where('user_id', $user->id)
                ->whereNotNull('pa_protocol_number');

            if (!$force) {
                $query->doesntHave('embedding');
            }

            $acts = $query->limit($limit)->get();

            $success = 0;
            $failed = 0;
            $errors = [];

            foreach ($acts as $act) {
                try {
                    $result = $this->embeddingService->generateForAct($act, $force);
                    if ($result) {
                        $success++;
                    } else {
                        $failed++;
                        $errors[] = "EGI #{$act->id}: Generation failed";
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "EGI #{$act->id}: {$e->getMessage()}";
                }
            }

            $this->logger->info('[PA Embeddings] Generation completed', [
                'user_id' => $user->id,
                'total' => $acts->count(),
                'success' => $success,
                'failed' => $failed,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $acts->count(),
                    'success' => $success,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 10), // Max 10 errors
                ],
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[PA Embeddings] Generation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all embeddings for user
     */
    public function deleteAll(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('pa_entity')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        try {
            $deleted = PaActEmbedding::whereHas('egi', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->delete();

            $this->logger->info('[PA Embeddings] All embeddings deleted', [
                'user_id' => $user->id,
                'count' => $deleted,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'deleted' => $deleted,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics helper
     */
    private function getStats($user): array
    {
        $totalActs = Egi::where('user_id', $user->id)
            ->whereNotNull('pa_protocol_number')
            ->count();

        $withEmbeddings = PaActEmbedding::whereHas('egi', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        $withoutEmbeddings = $totalActs - $withEmbeddings;
        $coverage = $totalActs > 0 ? ($withEmbeddings / $totalActs) * 100 : 0;

        // Estimate cost
        $estimatedTokens = $withoutEmbeddings * 200; // avg 200 tokens per act
        $estimatedCost = ($estimatedTokens / 1000) * 0.0001;

        return [
            'total_acts' => $totalActs,
            'with_embeddings' => $withEmbeddings,
            'without_embeddings' => $withoutEmbeddings,
            'coverage_percentage' => round($coverage, 1),
            'estimated_cost' => number_format($estimatedCost, 4),
        ];
    }
}
