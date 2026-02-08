<?php

namespace App\Services\RagNatan;

use App\Models\RagNatan\QueryCache;
use App\Models\RagNatan\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Cache Service
 *
 * Manages query result caching for RAG performance optimization.
 * Handles cache lookup, storage, invalidation, and cleanup.
 * Adheres to Ultra Standards for logging and error handling.
 *
 * @package App\Services\RagNatan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 */
class CacheService
{
    private const DEFAULT_TTL_HOURS = 24;
    private const STALE_THRESHOLD_DAYS = 7;

    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Generate cache key from question and context.
     */
    public function generateCacheKey(string $question, string $language, ?array $context = null): string
    {
        $questionHash = hash('sha256', trim(strtolower($question)));
        $contextHash = $context ? hash('sha256', json_encode($context)) : null;

        return implode(':', array_filter([
            'rag',
            $language,
            $questionHash,
            $contextHash,
        ]));
    }

    /**
     * Generate question hash for deduplication.
     */
    public function generateQuestionHash(string $question): string
    {
        return hash('sha256', trim(strtolower($question)));
    }

    /**
     * Look up cached response.
     */
    public function lookup(string $question, string $language, ?array $context = null): ?Response
    {
        $cacheKey = $this->generateCacheKey($question, $language, $context);

        $cache = QueryCache::valid()
            ->where('cache_key', $cacheKey)
            ->first();

        if (!$cache) {
            return null;
        }

        // Record cache hit
        $cache->recordHit();

        // Load and return response
        return $cache->response;
    }

    /**
     * Store response in cache.
     */
    public function store(
        string $question,
        string $language,
        Response $response,
        ?array $context = null,
        ?int $ttlHours = null
    ): QueryCache {
        $cacheKey = $this->generateCacheKey($question, $language, $context);
        $questionHash = $this->generateQuestionHash($question);
        $contextHash = $context ? hash('sha256', json_encode($context)) : null;
        $ttlHours = $ttlHours ?? self::DEFAULT_TTL_HOURS;

        return QueryCache::create([
            'cache_key' => $cacheKey,
            'question_hash' => $questionHash,
            'response_id' => $response->id,
            'language' => $language,
            'context_hash' => $contextHash,
            'hit_count' => 0,
            'last_hit_at' => null,
            'expires_at' => now()->addHours($ttlHours),
            'is_stale' => false,
        ]);
    }

    /**
     * Invalidate cache for a specific question.
     */
    public function invalidate(string $question, string $language): int
    {
        $questionHash = $this->generateQuestionHash($question);

        return QueryCache::where('question_hash', $questionHash)
            ->where('language', $language)
            ->update(['is_stale' => true]);
    }

    /**
     * Invalidate all cache entries for a document.
     */
    public function invalidateByDocument(int $documentId): int
    {
        // Mark as stale all cache entries that reference chunks from this document
        return DB::statement("
            UPDATE rag_natan.query_cache
            SET is_stale = true
            WHERE response_id IN (
                SELECT DISTINCT r.id
                FROM rag_natan.responses r
                JOIN rag_natan.sources s ON s.response_id = r.id
                JOIN rag_natan.chunks c ON c.id = s.chunk_id
                WHERE c.document_id = ?
            )
        ", [$documentId]);
    }

    /**
     * Invalidate all cache entries for a category.
     */
    public function invalidateByCategory(int $categoryId): int
    {
        return DB::statement("
            UPDATE rag_natan.query_cache
            SET is_stale = true
            WHERE response_id IN (
                SELECT DISTINCT r.id
                FROM rag_natan.responses r
                JOIN rag_natan.sources s ON s.response_id = r.id
                JOIN rag_natan.chunks c ON c.id = s.chunk_id
                JOIN rag_natan.documents d ON d.id = c.document_id
                WHERE d.category_id = ?
            )
        ", [$categoryId]);
    }

    /**
     * Clean up expired cache entries.
     */
    public function cleanupExpired(): int
    {
        return QueryCache::expired()->delete();
    }

    /**
     * Mark old cache entries as stale.
     */
    public function markStale(?int $days = null): int
    {
        $days = $days ?? self::STALE_THRESHOLD_DAYS;
        $threshold = now()->subDays($days);

        return QueryCache::where('created_at', '<', $threshold)
            ->where('is_stale', false)
            ->update(['is_stale' => true]);
    }

    /**
     * Get cache statistics.
     */
    public function getStats(): array
    {
        $total = QueryCache::count();
        $valid = QueryCache::valid()->count();
        $expired = QueryCache::expired()->count();
        $stale = QueryCache::where('is_stale', true)->count();

        $avgHitCount = QueryCache::avg('hit_count');
        $topHits = QueryCache::orderByDesc('hit_count')
            ->limit(10)
            ->get(['cache_key', 'hit_count', 'last_hit_at', 'created_at']);

        return [
            'total_entries' => $total,
            'valid_entries' => $valid,
            'expired_entries' => $expired,
            'stale_entries' => $stale,
            'avg_hit_count' => round($avgHitCount, 2),
            'top_hits' => $topHits,
            'hit_rate' => $total > 0 ? round(($valid / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Extend cache TTL for frequently accessed entries.
     */
    public function extendFrequentlyUsed(int $minHits = 10, int $extendHours = 48): int
    {
        return QueryCache::valid()
            ->where('hit_count', '>=', $minHits)
            ->update([
                'expires_at' => DB::raw("expires_at + interval '{$extendHours} hours'"),
            ]);
    }

    /**
     * Prune low-value cache entries.
     */
    public function pruneLowValue(int $maxHits = 2, int $minAgeHours = 24): int
    {
        return QueryCache::where('hit_count', '<=', $maxHits)
            ->where('created_at', '<', now()->subHours($minAgeHours))
            ->delete();
    }

    /**
     * Warm cache with common questions.
     */
    public function warmCache(array $questions, string $language, callable $generateResponse): int
    {
        $count = 0;

        foreach ($questions as $question) {
            // Check if already cached
            if ($this->lookup($question, $language)) {
                continue;
            }

            // Generate and cache response
            $response = $generateResponse($question, $language);
            if ($response instanceof Response) {
                $this->store($question, $language, $response);
                $count++;
            }
        }

        return $count;
    }
}
