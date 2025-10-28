<?php

namespace App\Services\Natan;

use Illuminate\Support\Collection;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Models\Egi;

/**
 * NATAN Intelligent Chunking Service
 *
 * @package App\Services\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - NATAN Intelligent Chunking System)
 * @date 2025-10-27
 * @purpose Intelligent chunking system for processing large datasets without rate limits
 *
 * STRATEGY:
 * - Works with ANY dataset size (100 to 100,000+ acts)
 * - Number of acts only affects processing TIME, not SUCCESS
 * - User controls how many acts to analyze via slider
 * - All parameters configurable from superadmin panel
 *
 * NOTE: PA Acts are stored in `egis` table with `pa_act_type` discrimination
 */
class NatanIntelligentChunkingService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Estimate how many acts can fit in a single Claude call
     *
     * @return array{available_tokens: int, estimated_acts_per_chunk: int}
     */
    public function calculateChunkCapacity(): array {
        $maxTokens = config('natan.max_tokens_per_call', 180000);
        $reservedSystem = config('natan.reserved_tokens_system', 2000);
        $reservedOutput = config('natan.reserved_tokens_output', 8000);

        $availableTokens = $maxTokens - $reservedSystem - $reservedOutput;

        // Average act size estimation (empirical)
        $avgCharsPerAct = 2000; // Title + summary + partial content
        $avgTokensPerChar = config('natan.avg_tokens_per_char', 0.25);
        $avgTokensPerAct = (int) ($avgCharsPerAct * $avgTokensPerChar);

        $estimatedActsPerChunk = (int) floor($availableTokens / $avgTokensPerAct);

        return [
            'available_tokens' => $availableTokens,
            'estimated_acts_per_chunk' => max(10, $estimatedActsPerChunk), // Min 10 acts
        ];
    }

    /**
     * Chunk acts by token budget (STRATEGY 1 - Token-Based)
     *
     * @param Collection $acts Collection of Egi models (PA acts)
     * @return Collection<Collection<Egi>>
     */
    public function chunkByTokenBudget(Collection $acts): Collection {
        $capacity = $this->calculateChunkCapacity();
        $availableTokens = $capacity['available_tokens'];

        $chunks = collect();
        $currentChunk = collect();
        $currentTokens = 0;

        foreach ($acts as $act) {
            $actTokens = $this->estimateTokens($act);

            if ($currentTokens + $actTokens > $availableTokens && $currentChunk->isNotEmpty()) {
                // Current chunk full, save and start new one
                $chunks->push($currentChunk);
                $currentChunk = collect([$act]);
                $currentTokens = $actTokens;
            } else {
                $currentChunk->push($act);
                $currentTokens += $actTokens;
            }
        }

        // Add last chunk if not empty
        if ($currentChunk->isNotEmpty()) {
            $chunks->push($currentChunk);
        }

        $this->logger->info('[NATAN Chunking] Token-based chunking completed', [
            'total_acts' => $acts->count(),
            'num_chunks' => $chunks->count(),
            'avg_acts_per_chunk' => $chunks->count() > 0 ? round($acts->count() / $chunks->count(), 1) : 0,
        ]);

        return $chunks;
    }

    /**
     * Chunk acts by relevance score (STRATEGY 2 - Relevance-Based)
     *
     * @param Collection $acts
     * @param string $query
     * @return array{high: Collection, medium: Collection, low: Collection}
     */
    public function chunkByRelevance(Collection $acts, string $query): array {
        // Sort by relevance score (assumes semantic search already scored them)
        $sortedActs = $acts->sortByDesc('relevance_score');

        $total = $sortedActs->count();

        $highPriorityCount = (int) ($total * 0.2); // Top 20%
        $mediumPriorityCount = (int) ($total * 0.4); // Next 40%

        $chunks = [
            'high' => $sortedActs->take($highPriorityCount),
            'medium' => $sortedActs->slice($highPriorityCount, $mediumPriorityCount),
            'low' => $sortedActs->slice($highPriorityCount + $mediumPriorityCount),
        ];

        $this->logger->info('[NATAN Chunking] Relevance-based chunking completed', [
            'total_acts' => $total,
            'high_priority' => $chunks['high']->count(),
            'medium_priority' => $chunks['medium']->count(),
            'low_priority' => $chunks['low']->count(),
        ]);

        return $chunks;
    }

    /**
     * Adaptive chunking based on historical performance (STRATEGY 3 - Adaptive/ML)
     *
     * @param Collection $acts Collection of Egi models (PA acts)
     * @return Collection<Collection<Egi>>
     */
    public function adaptiveChunk(Collection $acts): Collection {
        // TODO: Implement ML-based optimization using historical data
        // For now, fallback to token-based

        $this->logger->info('[NATAN Chunking] Adaptive strategy requested, using token-based fallback');

        return $this->chunkByTokenBudget($acts);
    }

    /**
     * Estimate token count for a single PA act
     *
     * @param Egi $act
     * @return int
     */
    private function estimateTokens(Egi $act): int {
        $text = implode(' ', [
            $act->title ?? '',
            $act->description ?? '',
            $act->jsonMetadata['pa_act']['doc_summary'] ?? '', // PA act summary from AI extraction
        ]);

        $chars = mb_strlen($text);
        $avgTokensPerChar = config('natan.avg_tokens_per_char', 0.25);

        return (int) ceil($chars * $avgTokensPerChar);
    }

    /**
     * Calculate cost and time estimates for user
     *
     * @param int $totalActs
     * @param int $selectedActs
     * @return array{chunks: int, estimated_time_seconds: int, estimated_time_human: string, estimated_cost_eur: float}
     */
    public function estimateProcessing(int $totalActs, int $selectedActs): array {
        $capacity = $this->calculateChunkCapacity();
        $actsPerChunk = $capacity['estimated_acts_per_chunk'];

        $numChunks = (int) ceil($selectedActs / $actsPerChunk);

        // Cost calculation
        $costPerChunk = config('natan.cost_per_chunk', 0.09);
        $costAggregation = config('natan.cost_aggregation', 0.03);
        $totalCost = ($numChunks * $costPerChunk) + $costAggregation;

        // Time calculation
        $timePerChunk = config('natan.time_per_chunk_seconds', 10);
        $timeAggregation = config('natan.time_aggregation_seconds', 15);
        $totalTimeSeconds = ($numChunks * $timePerChunk) + $timeAggregation;

        // Human-readable time
        $minutes = (int) floor($totalTimeSeconds / 60);
        $seconds = $totalTimeSeconds % 60;
        $humanTime = $minutes > 0
            ? "{$minutes} min {$seconds} sec"
            : "{$seconds} sec";

        return [
            'chunks' => $numChunks,
            'estimated_time_seconds' => $totalTimeSeconds,
            'estimated_time_human' => $humanTime,
            'estimated_cost_eur' => round($totalCost, 2),
        ];
    }

    /**
     * Pre-filter acts by relevance score before chunking
     *
     * @param Collection $acts
     * @return Collection
     */
    public function preFilterByRelevance(Collection $acts): Collection {
        $minScore = config('natan.min_relevance_score', 0.3);

        $filtered = $acts->filter(function ($act) use ($minScore) {
            return ($act->relevance_score ?? 0) >= $minScore;
        });

        $this->logger->info('[NATAN Chunking] Pre-filtering by relevance', [
            'original_count' => $acts->count(),
            'filtered_count' => $filtered->count(),
            'min_score' => $minScore,
            'removed' => $acts->count() - $filtered->count(),
        ]);

        return $filtered;
    }

    /**
     * Get slider configuration for frontend
     *
     * @return array{min: int, max: int, default: int}
     */
    public function getSliderConfig(): array {
        return [
            'min' => config('natan.slider_min_acts', 50),
            'max' => config('natan.slider_max_acts', 5000),
            'default' => config('natan.slider_default_acts', 500),
        ];
    }

    /**
     * Determine optimal chunking strategy based on dataset characteristics
     *
     * @param Collection $acts
     * @param string $query
     * @return string
     */
    public function determineOptimalStrategy(Collection $acts, string $query): string {
        $configured = config('natan.chunking_strategy', 'token-based');

        // If adaptive, analyze dataset and choose best strategy
        if ($configured === 'adaptive') {
            $hasRelevanceScores = $acts->first()?->relevance_score !== null;

            if ($hasRelevanceScores) {
                return 'relevance-based';
            }

            return 'token-based';
        }

        return $configured;
    }

    /**
     * Process chunks with exponential backoff on rate limit
     *
     * @param Collection $chunks
     * @param callable $processor Function to process each chunk
     * @return Collection Results from each chunk
     */
    public function processChunksWithRetry(Collection $chunks, callable $processor): Collection {
        $maxRetries = config('natan.rate_limit_max_retries', 3);
        $initialDelay = config('natan.rate_limit_initial_delay_seconds', 2);

        $results = collect();

        foreach ($chunks as $index => $chunk) {
            $attempt = 0;
            $success = false;

            while ($attempt < $maxRetries && !$success) {
                try {
                    $result = $processor($chunk, $index);
                    $results->push($result);
                    $success = true;
                } catch (\Exception $e) {
                    $attempt++;

                    if ($attempt >= $maxRetries) {
                        $this->errorManager->handle('NATAN_CHUNKING_MAX_RETRIES', [
                            'chunk_index' => $index,
                            'max_retries' => $maxRetries,
                        ], $e);
                        throw $e;
                    }

                    // Exponential backoff
                    $delay = $initialDelay * pow(2, $attempt - 1);

                    $this->logger->warning('[NATAN Chunking] Retry after rate limit', [
                        'chunk_index' => $index,
                        'attempt' => $attempt,
                        'delay_seconds' => $delay,
                    ]);

                    sleep($delay);
                }
            }
        }

        return $results;
    }
}
