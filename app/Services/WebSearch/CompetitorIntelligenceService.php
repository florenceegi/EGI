<?php

namespace App\Services\WebSearch;

use App\Models\User;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * Competitor Intelligence Service - Benchmark against other Italian municipalities
 *
 * Analyzes public data from other Comuni to provide competitive insights.
 * GDPR-compliant: uses ONLY public information.
 *
 * MONITORED DATA (all public):
 * - Transparency portals (Amministrazione Trasparente)
 * - Published acts and deliberations
 * - Public performance indicators
 * - Digital transformation initiatives
 * - Best practices documentation
 *
 * GDPR COMPLIANCE (P1 MANDATORY):
 * - Uses ONLY public information
 * - Consent check before personalized reports
 * - Audit trail for benchmark requests
 * - UEM error handling
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Competitor Intelligence GDPR-compliant)
 * @date 2025-10-26
 * @purpose Benchmark PA against other municipalities using public data only
 */
class CompetitorIntelligenceService {
    protected WebSearchService $webSearch;
    protected ConsentService $consentService;
    protected AuditLogService $auditService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        WebSearchService $webSearch,
        ConsentService $consentService,
        AuditLogService $auditService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->webSearch = $webSearch;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get benchmark data for specific topic (GDPR-compliant)
     *
     * @param User $user PA user requesting benchmark
     * @param string $topic Topic to benchmark (waste_management, digital_services, etc.)
     * @param array $competitorCities Cities to compare with
     * @return array ['benchmark' => array, 'insights' => array]
     */
    public function getBenchmark(User $user, string $topic, array $competitorCities = []): array {
        // ULM: Log start
        $this->logger->info('[CompetitorIntelligence] Benchmark requested', [
            'user_id' => $user->id,
            'topic' => $topic,
            'competitors' => $competitorCities,
        ]);

        try {
            // GDPR: Check consent
            if (!$this->consentService->hasConsent($user, 'allow-competitor-analysis')) {
                return [
                    'benchmark' => [],
                    'insights' => [],
                    'consent_required' => true,
                ];
            }

            // Default competitors (top Italian cities)
            if (empty($competitorCities)) {
                $competitorCities = ['Milano', 'Roma', 'Bologna', 'Torino'];
            }

            $benchmarkData = [];

            foreach ($competitorCities as $city) {
                $query = "{$city} comune {$topic} best practices";
                $results = $this->webSearch->search($query, 'strategic', 5);

                if ($results['success']) {
                    $benchmarkData[$city] = $results['results'];
                }
            }

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'competitor_benchmark_generated',
                [
                    'topic' => $topic,
                    'competitors' => $competitorCities,
                    'results_count' => count($benchmarkData),
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            // ULM: Log success
            $this->logger->info('[CompetitorIntelligence] Benchmark completed', [
                'user_id' => $user->id,
                'cities_analyzed' => count($benchmarkData),
            ]);

            return [
                'benchmark' => $benchmarkData,
                'insights' => $this->generateInsights($benchmarkData),
                'consent_required' => false,
            ];
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('COMPETITOR_BENCHMARK_FAILED', [
                'user_id' => $user->id,
                'topic' => $topic,
            ], $e);

            return [
                'benchmark' => [],
                'insights' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate actionable insights from benchmark data
     */
    protected function generateInsights(array $benchmarkData): array {
        // Basic insights (can be enhanced with AI analysis)
        return [
            'cities_analyzed' => count($benchmarkData),
            'total_sources' => array_sum(array_map('count', $benchmarkData)),
            'summary' => 'Benchmark data available for ' . count($benchmarkData) . ' municipalities',
        ];
    }
}
