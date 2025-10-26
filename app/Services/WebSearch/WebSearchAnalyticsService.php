<?php

namespace App\Services\WebSearch;

use App\Models\NatanChatMessage;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Web Search Analytics Service - Usage metrics and insights
 *
 * Provides analytics on web search usage, costs, and effectiveness.
 * Read-only operations, no GDPR consent needed (legitimate interest).
 *
 * METRICS TRACKED:
 * - Web search usage rate
 * - Provider distribution (Perplexity vs Google)
 * - Cache hit rate
 * - Costs estimation
 * - Most searched topics
 * - Persona-specific usage
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search Analytics)
 * @date 2025-10-26
 * @purpose Analytics dashboard for web search feature monitoring
 */
class WebSearchAnalyticsService
{
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get web search usage statistics (NO GDPR consent needed - legitimate interest)
     *
     * @param int $days Period to analyze (default: 30)
     * @param ?int $limit Optional limit for results (REGOLA STATISTICS compliant)
     * @return array Analytics data
     */
    public function getUsageStats(int $days = 30, ?int $limit = null): array
    {
        $startDate = now()->subDays($days);

        // ULM: Log analytics request
        $this->logger->info('[WebSearchAnalytics] Generating usage stats', [
            'days' => $days,
            'start_date' => $startDate->toDateString(),
            'limit' => $limit,
        ]);

        // Total messages with web search
        $totalWebSearchMessages = NatanChatMessage::where('web_search_enabled', true)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Total messages (for percentage)
        $totalMessages = NatanChatMessage::where('created_at', '>=', $startDate)
            ->count();

        // Provider distribution
        $providerStats = NatanChatMessage::where('web_search_enabled', true)
            ->where('created_at', '>=', $startDate)
            ->select('web_search_provider', DB::raw('count(*) as count'))
            ->groupBy('web_search_provider')
            ->get();

        // Cache hit rate
        $cacheHits = NatanChatMessage::where('web_search_enabled', true)
            ->where('web_search_from_cache', true)
            ->where('created_at', '>=', $startDate)
            ->count();

        $cacheHitRate = $totalWebSearchMessages > 0
            ? round(($cacheHits / $totalWebSearchMessages) * 100, 2)
            : 0;

        // Usage rate
        $usageRate = $totalMessages > 0
            ? round(($totalWebSearchMessages / $totalMessages) * 100, 2)
            : 0;

        return [
            'period_days' => $days,
            'total_messages' => $totalMessages,
            'web_search_messages' => $totalWebSearchMessages,
            'usage_rate_percent' => $usageRate,
            'provider_distribution' => $providerStats->pluck('count', 'web_search_provider')->toArray(),
            'cache_hit_rate_percent' => $cacheHitRate,
            'cache_hits' => $cacheHits,
        ];
    }
}

