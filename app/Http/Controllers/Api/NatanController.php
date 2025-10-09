<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EgiAct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * N.A.T.A.N. API Controller
 *
 * Handles API endpoints for document analysis and acts management
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 */
class NatanController extends Controller
{
    /**
     * Upload document for AI analysis
     *
     * POST /api/natan/analyze
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,p7m|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');

            // Generate job ID
            $jobId = (string) Str::uuid();

            // Store file temporarily
            $filePath = $file->store('natan/pending', 'local');

            // TODO: Dispatch job to queue for async processing
            // ProcessDocument::dispatch($filePath, $jobId)->onQueue('natan-processing');

            // For now, cache job status
            cache()->put("natan:job:{$jobId}", [
                'status' => 'processing',
                'file' => $file->getClientOriginalName(),
                'created_at' => now()->toIso8601String()
            ], now()->addHours(24));

            Log::info('N.A.T.A.N. API: Document upload initiated', [
                'job_id' => $jobId,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);

            return response()->json([
                'job_id' => $jobId,
                'status' => 'processing',
                'estimated_time' => 30,
                'message' => 'Document uploaded successfully. Processing started.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated list of acts with filters
     *
     * GET /api/natan/acts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActs(Request $request): JsonResponse
    {
        try {
            $query = EgiAct::query()->completed();

            // Apply filters
            if ($request->has('tipo')) {
                $query->ofType($request->tipo);
            }

            if ($request->has('ente')) {
                $query->byEnte($request->ente);
            }

            if ($request->has('direzione')) {
                $query->byDirezione($request->direzione);
            }

            if ($request->has('data_from') || $request->has('data_to')) {
                $query->dateRange($request->data_from, $request->data_to);
            }

            if ($request->has('importo_min') || $request->has('importo_max')) {
                $query->importoRange($request->importo_min, $request->importo_max);
            }

            // Sorting
            $sortField = $request->input('sort', 'created_at');
            $sortDirection = $request->input('direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->input('per_page', 20);
            $acts = $query->paginate($perPage);

            return response()->json([
                'data' => $acts->items(),
                'meta' => [
                    'current_page' => $acts->currentPage(),
                    'total' => $acts->total(),
                    'per_page' => $acts->perPage(),
                    'last_page' => $acts->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Get acts failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve acts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single act by ID
     *
     * GET /api/natan/acts/{id}
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getAct(int $id): JsonResponse
    {
        try {
            $act = EgiAct::findOrFail($id);

            return response()->json([
                'data' => $act
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Act not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Get act failed', [
                'act_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve act',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search acts by full-text query
     *
     * GET /api/natan/search?q={query}
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchActs(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Query parameter required (min 3 characters)',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('q');

            $acts = EgiAct::query()
                ->completed()
                ->search($query)
                ->orderBy('data_atto', 'desc')
                ->paginate(20);

            return response()->json([
                'data' => $acts->items(),
                'meta' => [
                    'current_page' => $acts->currentPage(),
                    'total' => $acts->total(),
                    'per_page' => $acts->perPage(),
                    'query' => $query
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Search failed', [
                'query' => $request->input('q'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     *
     * GET /api/natan/stats
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = cache()->remember('natan:stats', now()->addMinutes(10), function () {
                // Total acts
                $totalActs = EgiAct::completed()->count();

                // By tipo_atto
                $byTipo = EgiAct::completed()
                    ->selectRaw('tipo_atto, COUNT(*) as count')
                    ->groupBy('tipo_atto')
                    ->pluck('count', 'tipo_atto')
                    ->toArray();

                // By month (last 12 months)
                $byMonth = EgiAct::completed()
                    ->where('data_atto', '>=', now()->subMonths(12))
                    ->selectRaw('DATE_FORMAT(data_atto, "%Y-%m") as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'month' => $item->month,
                            'count' => $item->count
                        ];
                    })
                    ->toArray();

                // Average importo
                $avgImporto = EgiAct::completed()
                    ->whereNotNull('importo')
                    ->avg('importo') ?? 0;

                // Total AI cost
                $totalAiCost = EgiAct::completed()->sum('ai_cost') ?? 0;

                // Recent acts (last 7 days)
                $recentActs = EgiAct::completed()
                    ->recent(7)
                    ->count();

                return [
                    'total_acts' => $totalActs,
                    'by_tipo' => $byTipo,
                    'by_month' => $byMonth,
                    'avg_importo' => round($avgImporto, 2),
                    'total_ai_cost' => round($totalAiCost, 2),
                    'recent_acts_7d' => $recentActs,
                ];
            });

            return response()->json($stats, 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Get stats failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job status
     *
     * GET /api/natan/jobs/{jobId}
     *
     * @param string $jobId
     * @return JsonResponse
     */
    public function getJobStatus(string $jobId): JsonResponse
    {
        try {
            $jobData = cache()->get("natan:job:{$jobId}");

            if (!$jobData) {
                return response()->json([
                    'message' => 'Job not found'
                ], 404);
            }

            return response()->json($jobData, 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Get job status failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve job status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available filters options
     *
     * GET /api/natan/filters
     *
     * @return JsonResponse
     */
    public function getFilters(): JsonResponse
    {
        try {
            $filters = cache()->remember('natan:filters', now()->addHour(), function () {
                return [
                    'tipi_atto' => EgiAct::completed()
                        ->distinct()
                        ->pluck('tipo_atto')
                        ->filter()
                        ->values()
                        ->toArray(),

                    'enti' => EgiAct::completed()
                        ->distinct()
                        ->whereNotNull('ente')
                        ->pluck('ente')
                        ->filter()
                        ->values()
                        ->toArray(),

                    'direzioni' => EgiAct::completed()
                        ->distinct()
                        ->whereNotNull('direzione')
                        ->pluck('direzione')
                        ->filter()
                        ->values()
                        ->toArray(),
                ];
            });

            return response()->json($filters, 200);
        } catch (\Exception $e) {
            Log::error('N.A.T.A.N. API: Get filters failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve filters',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
