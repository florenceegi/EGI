<?php

namespace App\Services\BusinessEnrichment;

use App\Services\BusinessEnrichment\Sources\WebsiteScraperSource;
use App\Services\BusinessEnrichment\Sources\RegistroImpreseSource;
use App\Services\BusinessEnrichment\Sources\AgenziaEntrateSource;
use App\DataTransferObjects\BusinessEnrichment\EnrichedBusinessData;
use App\DataTransferObjects\BusinessEnrichment\EnrichmentRequest;
use App\DataTransferObjects\BusinessEnrichment\SourceResult;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\Cache;

/**
 * @Oracode Service: Business Data Enrichment Orchestrator
 * 🎯 Purpose: Aggregate business data from multiple free sources
 * 🛡️ Privacy: No PII stored from scraping, only business public data
 * 🧱 Core Logic: Multi-source orchestration with fallback and merging
 *
 * Sources:
 * 1. Website scraping → indirizzo, email, PEC, telefono, P.IVA
 * 2. Registro Imprese → ATECO, ragione sociale, sede legale, REA
 * 3. Agenzia Entrate → Validazione P.IVA, stato attività
 */
class BusinessDataEnrichmentService
{
    private UltraLogManager $logger;
    private WebsiteScraperSource $websiteSource;
    private RegistroImpreseSource $registroImpreseSource;
    private AgenziaEntrateSource $agenziaEntrateSource;

    private const CACHE_TTL_HOURS = 24;

    public function __construct(
        UltraLogManager $logger,
        WebsiteScraperSource $websiteSource,
        RegistroImpreseSource $registroImpreseSource,
        AgenziaEntrateSource $agenziaEntrateSource
    ) {
        $this->logger = $logger;
        $this->websiteSource = $websiteSource;
        $this->registroImpreseSource = $registroImpreseSource;
        $this->agenziaEntrateSource = $agenziaEntrateSource;
    }

    /**
     * Enrich business data from all available sources
     *
     * @param EnrichmentRequest $request Contains vatNumber and/or websiteUrl
     * @return EnrichedBusinessData Aggregated data from all sources
     */
    public function enrich(EnrichmentRequest $request): EnrichedBusinessData
    {
        $cacheKey = $this->buildCacheKey($request);

        // Check cache first
        if ($cached = Cache::get($cacheKey)) {
            $this->logger->info('[BusinessEnrichment] Cache hit', [
                'vat_number' => $request->vatNumber,
                'website_url' => $request->websiteUrl,
            ]);
            return EnrichedBusinessData::fromArray($cached);
        }

        $this->logger->info('[BusinessEnrichment] Starting multi-source enrichment', [
            'vat_number' => $request->vatNumber,
            'website_url' => $request->websiteUrl,
        ]);

        $results = [];
        $errors = [];

        // 1. Website scraping (if URL provided)
        if ($request->websiteUrl) {
            try {
                $results['website'] = $this->websiteSource->fetch($request->websiteUrl);
                $this->logger->info('[BusinessEnrichment] Website scraping completed', [
                    'url' => $request->websiteUrl,
                    'fields_found' => count(array_filter($results['website']->data)),
                ]);
            } catch (\Exception $e) {
                $errors['website'] = $e->getMessage();
                $this->logger->warning('[BusinessEnrichment] Website scraping failed', [
                    'url' => $request->websiteUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2. Registro Imprese (if VAT provided)
        $vatNumber = $request->vatNumber ?? ($results['website']->data['vat_number'] ?? null);
        if ($vatNumber) {
            try {
                $results['registro_imprese'] = $this->registroImpreseSource->fetch($vatNumber);
                $this->logger->info('[BusinessEnrichment] Registro Imprese completed', [
                    'vat_number' => $vatNumber,
                    'fields_found' => count(array_filter($results['registro_imprese']->data)),
                ]);
            } catch (\Exception $e) {
                $errors['registro_imprese'] = $e->getMessage();
                $this->logger->warning('[BusinessEnrichment] Registro Imprese failed', [
                    'vat_number' => $vatNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 3. Agenzia Entrate validation (if VAT provided)
        if ($vatNumber) {
            try {
                $results['agenzia_entrate'] = $this->agenziaEntrateSource->fetch($vatNumber);
                $this->logger->info('[BusinessEnrichment] Agenzia Entrate completed', [
                    'vat_number' => $vatNumber,
                    'is_valid' => $results['agenzia_entrate']->data['is_valid'] ?? false,
                ]);
            } catch (\Exception $e) {
                $errors['agenzia_entrate'] = $e->getMessage();
                $this->logger->warning('[BusinessEnrichment] Agenzia Entrate failed', [
                    'vat_number' => $vatNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Merge results with priority: Registro Imprese > Agenzia Entrate > Website
        $enrichedData = $this->mergeResults($results, $errors);

        // Cache the result
        Cache::put($cacheKey, $enrichedData->toArray(), now()->addHours(self::CACHE_TTL_HOURS));

        return $enrichedData;
    }

    /**
     * Quick VAT validation only (faster, single source)
     */
    public function validateVat(string $vatNumber): array
    {
        try {
            $result = $this->agenziaEntrateSource->fetch($vatNumber);
            return [
                'valid' => $result->data['is_valid'] ?? false,
                'status' => $result->data['status'] ?? 'unknown',
                'start_date' => $result->data['start_date'] ?? null,
                'source' => 'agenzia_entrate',
            ];
        } catch (\Exception $e) {
            return [
                'valid' => null,
                'status' => 'error',
                'error' => $e->getMessage(),
                'source' => 'agenzia_entrate',
            ];
        }
    }

    /**
     * Merge results from multiple sources with priority handling
     */
    private function mergeResults(array $results, array $errors): EnrichedBusinessData
    {
        $merged = [
            'legal_name' => null,
            'trade_name' => null,
            'vat_number' => null,
            'fiscal_code' => null,
            'rea_number' => null,
            'ateco_code' => null,
            'ateco_description' => null,
            'legal_form' => null,
            'street' => null,
            'city' => null,
            'province' => null,
            'region' => null,
            'zip_code' => null,
            'country' => 'IT',
            'phone' => null,
            'fax' => null,
            'email' => null,
            'pec' => null,
            'website' => null,
            'vat_status' => null,
            'vat_start_date' => null,
            'is_vat_valid' => null,
        ];

        $sources = [];
        $confidence = [];

        // Priority: Registro Imprese (official) > Agenzia Entrate (official) > Website (scraped)
        $priority = ['registro_imprese', 'agenzia_entrate', 'website'];

        foreach ($priority as $source) {
            if (!isset($results[$source])) {
                continue;
            }

            $sourceData = $results[$source]->data;
            $sources[] = $source;

            foreach ($sourceData as $field => $value) {
                if ($value !== null && $merged[$field] === null) {
                    $merged[$field] = $value;
                    $confidence[$field] = $source;
                }
            }
        }

        return new EnrichedBusinessData(
            data: $merged,
            sources: $sources,
            errors: $errors,
            confidence: $confidence,
            fetchedAt: now()
        );
    }

    /**
     * Build cache key from request
     */
    private function buildCacheKey(EnrichmentRequest $request): string
    {
        $parts = ['business_enrichment'];

        if ($request->vatNumber) {
            $parts[] = 'vat_' . preg_replace('/[^0-9]/', '', $request->vatNumber);
        }

        if ($request->websiteUrl) {
            $parts[] = 'url_' . md5($request->websiteUrl);
        }

        return implode(':', $parts);
    }

    /**
     * Clear cache for specific request
     */
    public function clearCache(EnrichmentRequest $request): void
    {
        $cacheKey = $this->buildCacheKey($request);
        Cache::forget($cacheKey);
    }
}
