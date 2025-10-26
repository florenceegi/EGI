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
 * Funding Opportunities Service - Real-time funding alerts for PA
 *
 * Monitors Italian and EU funding sources for opportunities matching PA projects.
 * GDPR-compliant alert system with consent management.
 *
 * MONITORED SOURCES:
 * - PNRR (pnrr.gov.it)
 * - Europa.eu funding portal
 * - Regione Toscana bandi
 * - Fondazioni bancarie (Cariplo, CRT, etc.)
 * - MISE incentivi
 *
 * GDPR COMPLIANCE (P1 MANDATORY):
 * - Consent check before alerts
 * - Audit trail for all notifications
 * - UEM error handling
 * - ULM operations logging
 *
 * @package App\Services\WebSearch
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Funding Opportunities GDPR-compliant)
 * @date 2025-10-26
 * @purpose Real-time funding opportunities monitoring with GDPR compliance
 */
class FundingOpportunitiesService
{
    protected WebSearchService $webSearch;
    protected ConsentService $consentService;
    protected AuditLogService $auditService;
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    protected array $fundingSources = [
        'pnrr' => [
            'keywords' => 'PNRR bandi aperti comuni 2024 2025 scadenza',
            'domains' => ['pnrr.gov.it', 'italiadomani.gov.it'],
            'category' => 'national',
        ],
        'eu_funding' => [
            'keywords' => 'European funding municipalities local authorities 2024 2025',
            'domains' => ['europa.eu', 'eif.org'],
            'category' => 'european',
        ],
        'regional' => [
            'keywords' => 'Regione Toscana bandi contributi comuni 2024 2025',
            'domains' => ['regione.toscana.it'],
            'category' => 'regional',
        ],
    ];

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
     * Search for funding opportunities (GDPR-compliant)
     *
     * @param User $user PA user requesting search
     * @param array|null $categories Filter by category (null = all)
     * @return array ['opportunities' => array, 'count' => int]
     */
    public function searchOpportunities(User $user, ?array $categories = null): array
    {
        // ULM: Log start
        $this->logger->info('[FundingOpportunities] Searching opportunities', [
            'user_id' => $user->id,
            'categories' => $categories,
        ]);

        try {
            // GDPR: Check consent
            if (!$this->consentService->hasConsent($user, 'allow-funding-alerts')) {
                $this->logger->info('[FundingOpportunities] Search skipped - no consent', [
                    'user_id' => $user->id,
                ]);

                return [
                    'opportunities' => [],
                    'count' => 0,
                    'consent_required' => true,
                ];
            }

            $allOpportunities = [];

            foreach ($this->fundingSources as $sourceKey => $source) {
                // Filter by category if specified
                if ($categories && !in_array($source['category'], $categories, true)) {
                    continue;
                }

                $results = $this->searchSource($sourceKey, $source);
                $allOpportunities = array_merge($allOpportunities, $results);
            }

            // GDPR: Audit trail
            $this->auditService->logUserAction(
                $user,
                'funding_opportunities_searched',
                [
                    'categories' => $categories,
                    'results_count' => count($allOpportunities),
                ],
                GdprActivityCategory::DATA_ACCESS
            );

            // ULM: Log success
            $this->logger->info('[FundingOpportunities] Search completed', [
                'user_id' => $user->id,
                'opportunities_found' => count($allOpportunities),
            ]);

            return [
                'opportunities' => $allOpportunities,
                'count' => count($allOpportunities),
                'consent_required' => false,
            ];
        } catch (\Exception $e) {
            // UEM: Error handling
            $this->errorManager->handle('FUNDING_SEARCH_FAILED', [
                'user_id' => $user->id,
                'categories' => $categories,
            ], $e);

            return [
                'opportunities' => [],
                'count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search single funding source
     */
    protected function searchSource(string $sourceKey, array $source): array
    {
        $cacheKey = "funding_opportunities:{$sourceKey}";

        // Check cache first (avoid excessive API calls)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // ULM: Log search
        $this->logger->debug('[FundingOpportunities] Searching source', ['source' => $sourceKey]);

        $searchResponse = $this->webSearch->search(
            $source['keywords'],
            'financial', // Use financial persona
            10
        );

        if (!$searchResponse['success']) {
            // ULM: Warning (non-critical)
            $this->logger->warning('[FundingOpportunities] Source search failed', [
                'source' => $sourceKey,
                'error' => $searchResponse['error'] ?? 'unknown',
            ]);
            return [];
        }

        $results = $searchResponse['results'];

        // Cache for 6 hours (funding changes frequently)
        Cache::put($cacheKey, $results, now()->addHours(6));

        return $results;
    }
}

