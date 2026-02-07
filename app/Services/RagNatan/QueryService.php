<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\Query;
use App\Models\RagNatan\Response;
use App\Models\User;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Query Service
 *
 * Handles user query management, logging, and analytics.
 * Manages the full query-response lifecycle.
 * Adheres to Ultra Standards for logging, error handling, and GDPR compliance.
 *
 * @package App\Services\RagNatan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 */
class QueryService
{
    public function __construct(
        private CacheService $cacheService,
        private ResponseGenerationService $responseService,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditService
    ) {}

    /**
     * Process a user query with full RAG pipeline.
     *
     * @throws \Exception
     */
    public function processQuery(
        string $question,
        ?User $user = null,
        string $language = 'it',
        ?array $context = [],
        ?array $options = []
    ): array {
        try {
            $startTime = microtime(true);

            $this->logger->info('rag.query.processing_started', [
                'user_id' => $user?->id,
                'language' => $language,
                'question_length' => strlen($question)
            ]);

            // Check cache first
            $cachedResponse = $this->cacheService->lookup($question, $language, $context);

            if ($cachedResponse) {
                $this->logger->info('rag.query.cache_hit', [
                    'user_id' => $user?->id,
                    'response_id' => $cachedResponse->id
                ]);

                $query = $this->logQuery($question, $user, $language, $context, $cachedResponse);

                // GDPR Audit: Log query processing (cached)
                if ($user) {
                    $this->auditService->logUserAction(
                        $user,
                        'rag_query_processed_cached',
                        [
                            'query_id' => $query->id,
                            'response_id' => $cachedResponse->id,
                            'language' => $language
                        ],
                        GdprActivityCategory::AI_PROCESSING
                    );
                }

                return [
                    'query_id' => $query->id,
                    'query_uuid' => $query->uuid,
                    'response_id' => $cachedResponse->id,
                    'response_uuid' => $cachedResponse->uuid,
                    'answer' => $cachedResponse->answer,
                    'answer_html' => $cachedResponse->answer_html,
                    'urs_score' => $cachedResponse->urs_score,
                    'sources' => $cachedResponse->sources,
                    'cached' => true,
                    'response_time_ms' => (int) ((microtime(true) - $startTime) * 1000),
                ];
            }

            // Not in cache, generate new response
            $this->logger->info('rag.query.cache_miss', [
                'user_id' => $user?->id,
                'generating_new_response' => true
            ]);

            $responseData = $this->responseService->generateResponse(
                $question,
                $language,
                $context,
                $options
            );

            // Create response record
            $response = Response::create([
                'uuid' => Str::uuid()->toString(),
                'query_id' => null, // Will be set after query creation
                'answer' => $responseData['answer'],
                'answer_html' => $responseData['answer_html'] ?? null,
                'urs_score' => $responseData['urs_score'] ?? null,
                'urs_explanation' => $responseData['urs_explanation'] ?? null,
                'claims_used' => $responseData['claims_used'] ?? [],
                'gaps_detected' => $responseData['gaps_detected'] ?? [],
                'hallucinations' => $responseData['hallucinations'] ?? [],
                'sources_used' => $responseData['sources_used'] ?? [],
                'processing_time_ms' => $responseData['processing_time_ms'],
                'tokens_input' => $responseData['tokens_input'] ?? null,
                'tokens_output' => $responseData['tokens_output'] ?? null,
                'cost_usd' => $responseData['cost_usd'] ?? null,
                'model_used' => $responseData['model_used'] ?? null,
                'stage_timings' => $responseData['stage_timings'] ?? [],
                'is_cached' => false,
            ]);

            $this->logger->info('rag.query.response_created', [
                'response_id' => $response->id,
                'urs_score' => $response->urs_score
            ]);

            // Log query
            $query = $this->logQuery($question, $user, $language, $context, $response);

            // Update response with query_id
            $response->update(['query_id' => $query->id]);

            // Store sources
            if (isset($responseData['sources'])) {
                $this->responseService->storeSources($response, $responseData['sources']);
            }

            // Cache the response
            $this->cacheService->store($question, $language, $response, $context);

            $totalTime = (int) ((microtime(true) - $startTime) * 1000);

            // GDPR Audit: Log query processing (new)
            if ($user) {
                $this->auditService->logUserAction(
                    $user,
                    'rag_query_processed_new',
                    [
                        'query_id' => $query->id,
                        'response_id' => $response->id,
                        'language' => $language,
                        'urs_score' => $response->urs_score,
                        'response_time_ms' => $totalTime
                    ],
                    GdprActivityCategory::AI_PROCESSING
                );
            }

            $this->logger->info('rag.query.processing_completed', [
                'query_id' => $query->id,
                'response_id' => $response->id,
                'total_time_ms' => $totalTime,
                'urs_score' => $response->urs_score
            ]);

            return [
                'query_id' => $query->id,
                'query_uuid' => $query->uuid,
                'response_id' => $response->id,
                'response_uuid' => $response->uuid,
                'answer' => $response->answer,
                'answer_html' => $response->answer_html,
                'urs_score' => $response->urs_score,
                'sources' => $response->sources,
                'cached' => false,
                'response_time_ms' => $totalTime,
                'processing_breakdown' => $responseData['stage_timings'] ?? [],
            ];
        } catch (\Exception $e) {
            $this->logger->error('rag.query.processing_failed', [
                'user_id' => $user?->id,
                'language' => $language,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_QUERY_PROCESSING_FAILED', [
                'user_id' => $user?->id,
                'language' => $language,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Log a query.
     */
    private function logQuery(
        string $question,
        ?User $user,
        string $language,
        ?array $context,
        ?Response $response
    ): Query {
        $questionHash = $this->cacheService->generateQuestionHash($question);

        return Query::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => $user?->id,
            'response_id' => $response?->id,
            'question' => $question,
            'question_hash' => $questionHash,
            'language' => $language,
            'context' => $context ?? [],
            'urs_score' => $response?->urs_score,
            'answer_length' => $response ? strlen($response->answer) : null,
            'chunks_used' => $response ? $response->sources()->count() : null,
            'response_time_ms' => $response?->processing_time_ms,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'responded_at' => $response ? now() : null,
        ]);
    }

    /**
     * Record user feedback on a query.
     *
     * @throws \Exception
     */
    public function recordFeedback(
        int $queryId,
        bool $wasHelpful,
        ?string $feedbackText = null
    ): Query {
        try {
            $query = Query::findOrFail($queryId);

            $this->logger->info('rag.query.recording_feedback', [
                'query_id' => $queryId,
                'was_helpful' => $wasHelpful,
                'has_text' => !empty($feedbackText)
            ]);

            $query->update([
                'was_helpful' => $wasHelpful,
                'feedback_text' => $feedbackText,
            ]);

            // GDPR Audit: Log feedback recording
            if ($query->user_id) {
                $user = User::find($query->user_id);
                if ($user) {
                    $this->auditService->logUserAction(
                        $user,
                        'rag_query_feedback_recorded',
                        [
                            'query_id' => $queryId,
                            'was_helpful' => $wasHelpful,
                            'has_feedback_text' => !empty($feedbackText)
                        ],
                        GdprActivityCategory::AI_PROCESSING
                    );
                }
            }

            $this->logger->info('rag.query.feedback_recorded', [
                'query_id' => $queryId,
                'was_helpful' => $wasHelpful
            ]);

            return $query;
        } catch (\Exception $e) {
            $this->logger->error('rag.query.feedback_recording_failed', [
                'query_id' => $queryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_QUERY_FEEDBACK_FAILED', [
                'query_id' => $queryId,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get query by UUID.
     */
    public function getByUuid(string $uuid): ?Query
    {
        return Query::where('uuid', $uuid)->first();
    }

    /**
     * Get user query history.
     *
     * @throws \Exception
     */
    public function getUserHistory(User $user, int $limit = 50): \Illuminate\Support\Collection
    {
        try {
            $this->logger->debug('rag.query.fetching_user_history', [
                'user_id' => $user->id,
                'limit' => $limit
            ]);

            $history = Query::where('user_id', $user->id)
                ->with('response')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            $this->logger->debug('rag.query.user_history_fetched', [
                'user_id' => $user->id,
                'queries_count' => $history->count()
            ]);

            return $history;
        } catch (\Exception $e) {
            $this->logger->error('rag.query.user_history_failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            $this->errorManager->handle('RAG_QUERY_HISTORY_FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Get popular queries.
     */
    public function getPopularQueries(int $limit = 20, int $days = 30): array
    {
        $since = now()->subDays($days);

        return DB::table('rag_natan.queries')
            ->select('question', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $since)
            ->groupBy('question')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get query analytics.
     *
     * @throws \Exception
     */
    public function getAnalytics(int $days = 30): array
    {
        try {
            $this->logger->info('rag.query.fetching_analytics', ['days' => $days]);

            $since = now()->subDays($days);

            $totalQueries = Query::where('created_at', '>=', $since)->count();
            $uniqueUsers = Query::where('created_at', '>=', $since)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');

            $avgResponseTime = Query::where('created_at', '>=', $since)
                ->whereNotNull('response_time_ms')
                ->avg('response_time_ms');

            $avgUrsScore = Query::where('created_at', '>=', $since)
                ->whereNotNull('urs_score')
                ->avg('urs_score');

            $helpfulRate = Query::where('created_at', '>=', $since)
                ->whereNotNull('was_helpful')
                ->selectRaw('
                    COUNT(*) as total_feedback,
                    SUM(CASE WHEN was_helpful THEN 1 ELSE 0 END) as helpful_count
                ')
                ->first();

            $languageDistribution = Query::where('created_at', '>=', $since)
                ->select('language', DB::raw('COUNT(*) as count'))
                ->groupBy('language')
                ->get();

            $analytics = [
                'period_days' => $days,
                'total_queries' => $totalQueries,
                'unique_users' => $uniqueUsers,
                'avg_response_time_ms' => round($avgResponseTime ?? 0, 2),
                'avg_urs_score' => round($avgUrsScore ?? 0, 2),
                'feedback' => [
                    'total' => $helpfulRate->total_feedback ?? 0,
                    'helpful' => $helpfulRate->helpful_count ?? 0,
                    'helpful_rate' => $helpfulRate->total_feedback > 0
                        ? round(($helpfulRate->helpful_count / $helpfulRate->total_feedback) * 100, 2)
                        : 0,
                ],
                'language_distribution' => $languageDistribution,
            ];

            $this->logger->info('rag.query.analytics_fetched', [
                'days' => $days,
                'total_queries' => $totalQueries,
                'unique_users' => $uniqueUsers
            ]);

            return $analytics;
        } catch (\Exception $e) {
            $this->logger->error('rag.query.analytics_failed', [
                'days' => $days,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->errorManager->handle('RAG_QUERY_ANALYTICS_FAILED', [
                'days' => $days,
                'error' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Find similar queries.
     */
    public function findSimilarQueries(string $question, int $limit = 10): \Illuminate\Support\Collection
    {
        $questionHash = $this->cacheService->generateQuestionHash($question);

        // Find exact matches first
        $exactMatches = Query::where('question_hash', $questionHash)
            ->with('response')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($exactMatches->isNotEmpty()) {
            return $exactMatches;
        }

        // Fall back to fuzzy matching (could use trigram similarity in future)
        // For now, just return recent queries
        return Query::orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
