<?php

namespace App\Services\WebSearch;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Web Search Service - External Web Search Integration for N.A.T.A.N.
 *
 * This service provides web search capabilities to augment N.A.T.A.N. responses with:
 * - Global best practices and case studies
 * - Real-time normative updates
 * - Funding opportunities
 * - International benchmarking data
 *
 * SUPPORTED PROVIDERS:
 * - Perplexity AI: AI-powered search with citations (recommended)
 * - Google Custom Search: Traditional search engine (fallback)
 *
 * GDPR COMPLIANCE:
 * - Only sanitized keywords sent to external APIs
 * - Full audit trail of data sent
 * - No internal document content exposed
 * - Results cached to minimize external calls
 *
 * ARCHITECTURE:
 * User Query → KeywordSanitizer → WebSearch (Provider) → Results → Cache
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Search)
 * @date 2025-10-26
 * @purpose External web search integration with GDPR compliance
 */
class WebSearchService {
    protected KeywordSanitizerService $sanitizer;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected array $config;

    public function __construct(
        KeywordSanitizerService $sanitizer,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->sanitizer = $sanitizer;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->config = config('services.web_search', []);
    }

    /**
     * Search the web with GDPR-safe keywords
     *
     * WORKFLOW:
     * 1. Sanitize query (remove internal references, PII)
     * 2. Check cache for recent results
     * 3. Call selected provider (Perplexity or Google)
     * 4. Parse and normalize results
     * 5. Cache results
     * 6. Return structured response with sources
     *
     * @param string $userQuery Original user query
     * @param string|null $personaId Persona for context-aware search
     * @param int|null $maxResults Max results to return (default from config)
     * @return array ['success' => bool, 'results' => array, 'metadata' => array]
     */
    public function search(
        string $userQuery,
        ?string $personaId = null,
        ?int $maxResults = null
    ): array {
        $startTime = microtime(true);

        // Check if web search is enabled
        if (!($this->config['enabled'] ?? true)) {
            return [
                'success' => false,
                'error' => 'web_search_disabled',
                'results' => [],
            ];
        }

        $maxResults = $maxResults ?? ($this->config['max_results'] ?? 5);

        $logContext = [
            'service' => 'WebSearchService',
            'persona_id' => $personaId,
            'max_results' => $maxResults,
            'query_length' => strlen($userQuery),
        ];

        $this->logger->info('[WebSearchService] Starting web search', $logContext);

        try {
            // STEP 1: Sanitize query (GDPR protection)
            $sanitized = $this->sanitizer->sanitize($userQuery, $personaId);

            // Validate sanitization
            $validation = $this->sanitizer->validate($sanitized['keywords']);
            if (!$validation['is_safe']) {
                // UEM: Critical error - sanitization failed (security issue)
                $this->errorManager->handle('WEB_SEARCH_SANITIZATION_FAILED', [
                    ...$logContext,
                    'violations' => $validation['violations'],
                    'query' => $userQuery,
                ], new \RuntimeException('GDPR sanitization validation failed'));

                return [
                    'success' => false,
                    'error' => 'sanitization_failed',
                    'results' => [],
                ];
            }

            $sanitizedQuery = $sanitized['sanitized_query'];

            // STEP 2: Check cache
            $cacheKey = $this->getCacheKey($sanitizedQuery, $personaId, $maxResults);
            $cachedResults = Cache::get($cacheKey);

            if ($cachedResults) {
                $this->logger->info('[WebSearchService] Cache hit', [
                    ...$logContext,
                    'cache_key' => $cacheKey,
                ]);

                return [
                    'success' => true,
                    'results' => $cachedResults['results'],
                    'metadata' => [
                        ...$cachedResults['metadata'],
                        'from_cache' => true,
                    ],
                ];
            }

            // STEP 3: Call provider
            $provider = $this->config['default_provider'] ?? 'perplexity';
            $results = $this->callProvider($provider, $sanitizedQuery, $personaId, $maxResults);

            // STEP 4: Calculate response time
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            // STEP 5: Build response metadata
            $metadata = [
                'provider' => $provider,
                'query_original' => $userQuery,
                'query_sanitized' => $sanitizedQuery,
                'keywords_removed' => $sanitized['removed'],
                'persona_id' => $personaId,
                'results_count' => count($results),
                'response_time_ms' => $responseTime,
                'from_cache' => false,
                'timestamp' => now()->toIso8601String(),
            ];

            // STEP 6: Cache results
            $cacheTtl = $this->config['cache_ttl'] ?? 3600; // 1 hour default
            Cache::put($cacheKey, [
                'results' => $results,
                'metadata' => $metadata,
            ], $cacheTtl);

            // STEP 7: GDPR Audit Log
            $this->logger->info('[WebSearchService][GDPR] Web search completed', [
                ...$logContext,
                'sanitized_query' => $sanitizedQuery,
                'provider' => $provider,
                'results_count' => count($results),
                'response_time_ms' => $responseTime,
            ]);

            return [
                'success' => true,
                'results' => $results,
                'metadata' => $metadata,
            ];
        } catch (\Throwable $e) {
            // UEM: Error handling (NO logger->error, solo UEM!)
            $this->errorManager->handle('WEB_SEARCH_FAILED', $logContext, $e);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => [],
            ];
        }
    }

    /**
     * Call search provider (Perplexity or Google)
     *
     * @param string $provider Provider name (perplexity|google)
     * @param string $query Sanitized query
     * @param string|null $personaId Persona for domain prioritization
     * @param int $maxResults Max results
     * @return array Normalized results
     */
    protected function callProvider(
        string $provider,
        string $query,
        ?string $personaId,
        int $maxResults
    ): array {
        switch ($provider) {
            case 'perplexity':
                return $this->searchPerplexity($query, $personaId, $maxResults);

            case 'google':
                return $this->searchGoogle($query, $personaId, $maxResults);

            default:
                throw new InvalidArgumentException("Unsupported provider: {$provider}");
        }
    }

    /**
     * Search using Perplexity AI (recommended)
     *
     * Perplexity provides AI-powered search with citations and summaries.
     *
     * @param string $query Sanitized query
     * @param string|null $personaId Persona for domain filtering
     * @param int $maxResults Max results
     * @return array Normalized results
     */
    protected function searchPerplexity(string $query, ?string $personaId, int $maxResults): array {
        $config = $this->config['perplexity'] ?? [];
        $apiKey = $config['api_key'] ?? null;

        if (!$apiKey) {
            throw new RuntimeException('Perplexity API key not configured');
        }

        // Add domain hints from persona preferences
        $domainHints = $this->getPersonaDomains($personaId);
        if (!empty($domainHints)) {
            $query .= ' site:' . implode(' OR site:', array_slice($domainHints, 0, 3));
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])
            ->timeout($config['timeout'] ?? 30)
            ->post($config['base_url'] . '/chat/completions', [
                'model' => $config['model'] ?? 'llama-3.1-sonar-large-128k-online',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $query,
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.2,
                'top_p' => 0.9,
                'return_citations' => true,
                'search_domain_filter' => $domainHints,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException("Perplexity API error: " . $response->status());
        }

        $data = $response->json();

        // Parse Perplexity response
        return $this->parsePerplexityResponse($data, $maxResults);
    }

    /**
     * Parse Perplexity API response to normalized format
     */
    protected function parsePerplexityResponse(array $data, int $maxResults): array {
        $results = [];

        // Extract main response
        $content = $data['choices'][0]['message']['content'] ?? '';

        // Extract citations
        $citations = $data['citations'] ?? [];

        foreach (array_slice($citations, 0, $maxResults) as $index => $citation) {
            $results[] = [
                'title' => $citation['title'] ?? "Result " . ($index + 1),
                'url' => $citation['url'] ?? '',
                'snippet' => $citation['text'] ?? '',
                'source' => 'perplexity',
                'relevance_score' => 1.0 - ($index * 0.1), // Decreasing relevance
            ];
        }

        // If no citations, create a single result from content
        if (empty($results) && !empty($content)) {
            $results[] = [
                'title' => 'AI Summary',
                'url' => '',
                'snippet' => substr($content, 0, 500),
                'source' => 'perplexity',
                'relevance_score' => 1.0,
            ];
        }

        return $results;
    }

    /**
     * Search using Google Custom Search API (fallback)
     *
     * @param string $query Sanitized query
     * @param string|null $personaId Persona for domain filtering
     * @param int $maxResults Max results
     * @return array Normalized results
     */
    protected function searchGoogle(string $query, ?string $personaId, int $maxResults): array {
        $config = $this->config['google'] ?? [];
        $apiKey = $config['api_key'] ?? null;
        $searchEngineId = $config['search_engine_id'] ?? null;

        if (!$apiKey || !$searchEngineId) {
            throw new RuntimeException('Google Custom Search not configured');
        }

        // Add site restrictions from persona preferences
        $domainHints = $this->getPersonaDomains($personaId);
        if (!empty($domainHints)) {
            $siteRestrictions = implode(' OR ', array_map(fn($d) => "site:{$d}", array_slice($domainHints, 0, 3)));
            $query = "({$query}) AND ({$siteRestrictions})";
        }

        $response = Http::timeout($config['timeout'] ?? 10)
            ->get($config['base_url'], [
                'key' => $apiKey,
                'cx' => $searchEngineId,
                'q' => $query,
                'num' => min($maxResults, 10), // Google max 10 per request
                'lr' => 'lang_it|lang_en', // Italian and English
            ]);

        if (!$response->successful()) {
            throw new RuntimeException("Google Search API error: " . $response->status());
        }

        $data = $response->json();

        return $this->parseGoogleResponse($data, $maxResults);
    }

    /**
     * Parse Google Custom Search response to normalized format
     */
    protected function parseGoogleResponse(array $data, int $maxResults): array {
        $results = [];
        $items = $data['items'] ?? [];

        foreach (array_slice($items, 0, $maxResults) as $index => $item) {
            $results[] = [
                'title' => $item['title'] ?? '',
                'url' => $item['link'] ?? '',
                'snippet' => $item['snippet'] ?? '',
                'source' => 'google',
                'relevance_score' => 1.0 - ($index * 0.1),
            ];
        }

        return $results;
    }

    /**
     * Get persona-specific priority domains
     */
    protected function getPersonaDomains(?string $personaId): array {
        if (!$personaId) {
            return [];
        }

        $personaPrefs = $this->config['persona_preferences'][$personaId] ?? [];
        return $personaPrefs['domains_priority'] ?? [];
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $query, ?string $personaId, int $maxResults): string {
        return 'web_search:' . md5($query . $personaId . $maxResults);
    }

    /**
     * Clear cache for specific query or all
     */
    public function clearCache(?string $query = null): void {
        if ($query) {
            $cacheKey = $this->getCacheKey($query, null, 5);
            Cache::forget($cacheKey);
        } else {
            // Clear all web search cache (pattern matching)
            Cache::flush(); // TODO: Implement pattern-based cache clearing
        }
    }
}
