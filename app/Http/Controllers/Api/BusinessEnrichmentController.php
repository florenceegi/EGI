<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessEnrichment\BusinessDataEnrichmentService;
use App\DataTransferObjects\BusinessEnrichment\EnrichmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Ultra\UltraLogManager\UltraLogManager;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: Business Data Enrichment API
 * 🎯 Purpose: API endpoints for extracting business data from multiple sources
 * 🛡️ Privacy: Rate limited, authenticated, business data only
 * 🧱 Core Logic: Facade for BusinessDataEnrichmentService
 */
class BusinessEnrichmentController extends Controller {
    private BusinessDataEnrichmentService $enrichmentService;
    private UltraLogManager $logger;

    public function __construct(
        BusinessDataEnrichmentService $enrichmentService,
        UltraLogManager $logger
    ) {
        $this->enrichmentService = $enrichmentService;
        $this->logger = $logger;
    }

    /**
     * Enrich business data from website URL and/or VAT number
     *
     * POST /api/business/enrich
     * Body: { "website_url": "https://...", "vat_number": "12345678901" }
     *
     * At least one of website_url or vat_number must be provided.
     */
    public function enrich(Request $request): JsonResponse {
        $user = FegiAuth::user();

        // Rate limiting: 10 requests per minute per user
        $rateLimitKey = 'business_enrich:' . ($user?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            return response()->json([
                'success' => false,
                'error' => 'Too many requests. Please wait before trying again.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey),
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        // Validate input
        $validated = $request->validate([
            'website_url' => 'nullable|url|max:500',
            'vat_number' => 'nullable|string|max:20',
            'fiscal_code' => 'nullable|string|max:20',
        ]);

        if (empty($validated['website_url']) && empty($validated['vat_number'])) {
            return response()->json([
                'success' => false,
                'error' => 'At least website_url or vat_number must be provided',
            ], 422);
        }

        try {
            $enrichmentRequest = EnrichmentRequest::fromArray($validated);
            $result = $this->enrichmentService->enrich($enrichmentRequest);

            $this->logger->info('[BusinessEnrichment] Enrichment completed', [
                'user_id' => $user?->id,
                'sources_used' => $result->sources,
                'completeness' => $result->getCompletenessScore(),
                'has_errors' => !empty($result->errors),
            ]);

            return response()->json([
                'success' => true,
                'data' => $result->data,
                'metadata' => [
                    'sources' => $result->sources,
                    'confidence' => $result->confidence,
                    'completeness_score' => $result->getCompletenessScore(),
                    'fetched_at' => $result->fetchedAt->toIso8601String(),
                ],
                'errors' => $result->errors ?: null,
                'organization_data' => $result->toOrganizationData(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            $this->logger->error('[BusinessEnrichment] Enrichment failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while fetching business data',
            ], 500);
        }
    }

    /**
     * Validate VAT number only (quick check)
     *
     * GET /api/business/validate-vat/{vatNumber}
     */
    public function validateVat(string $vatNumber): JsonResponse {
        $user = FegiAuth::user();

        // Rate limiting: 20 requests per minute
        $rateLimitKey = 'vat_validate:' . ($user?->id ?? request()->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            return response()->json([
                'success' => false,
                'error' => 'Too many requests',
                'retry_after' => RateLimiter::availableIn($rateLimitKey),
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        // Clean VAT number
        $vatNumber = preg_replace('/[^0-9]/', '', $vatNumber);

        if (strlen($vatNumber) !== 11) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'error' => 'VAT number must be 11 digits',
            ], 422);
        }

        try {
            $result = $this->enrichmentService->validateVat($vatNumber);

            return response()->json([
                'success' => true,
                'vat_number' => $vatNumber,
                'valid' => $result['valid'],
                'status' => $result['status'],
                'start_date' => $result['start_date'] ?? null,
                'source' => $result['source'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation service unavailable',
            ], 503);
        }
    }

    /**
     * Scrape only from website (no VAT lookup)
     *
     * POST /api/business/scrape-website
     * Body: { "url": "https://..." }
     */
    public function scrapeWebsite(Request $request): JsonResponse {
        $user = FegiAuth::user();

        // Rate limiting: 5 requests per minute (more intensive)
        $rateLimitKey = 'website_scrape:' . ($user?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'error' => 'Too many requests. Website scraping is rate limited.',
                'retry_after' => RateLimiter::availableIn($rateLimitKey),
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $validated = $request->validate([
            'url' => 'required|url|max:500',
        ]);

        try {
            $enrichmentRequest = new EnrichmentRequest(
                websiteUrl: $validated['url']
            );
            $result = $this->enrichmentService->enrich($enrichmentRequest);

            return response()->json([
                'success' => true,
                'data' => $result->data,
                'completeness_score' => $result->getCompletenessScore(),
                'organization_data' => $result->toOrganizationData(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to scrape website: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear enrichment cache for specific request
     *
     * DELETE /api/business/cache
     * Body: { "website_url": "...", "vat_number": "..." }
     */
    public function clearCache(Request $request): JsonResponse {
        $validated = $request->validate([
            'website_url' => 'nullable|url',
            'vat_number' => 'nullable|string',
        ]);

        if (empty($validated['website_url']) && empty($validated['vat_number'])) {
            return response()->json([
                'success' => false,
                'error' => 'At least one parameter required',
            ], 422);
        }

        try {
            $enrichmentRequest = EnrichmentRequest::fromArray($validated);
            $this->enrichmentService->clearCache($enrichmentRequest);

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
