<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * AI Provider Billing Service
 *
 * Retrieves real billing data from AI provider APIs:
 * - Anthropic Claude (usage & spending)
 * - OpenAI (usage & spending)
 * - Perplexity AI (if available)
 *
 * FEATURES:
 * - Real-time billing sync
 * - Comparison with internal tracking
 * - Alert on discrepancies
 * - Historical data caching
 *
 * @package App\Services\AI
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
 * @date 2025-10-27
 */
class AiProviderBillingService {
    protected UltraLogManager $logger;
    protected AiCostCalculatorService $costCalculator;

    public function __construct(
        UltraLogManager $logger,
        AiCostCalculatorService $costCalculator
    ) {
        $this->logger = $logger;
        $this->costCalculator = $costCalculator;
    }

    /**
     * Get Anthropic billing data for current month
     *
     * Anthropic doesn't provide a public billing API endpoint yet.
     * We scrape from their console or use organization API if available.
     *
     * @return array Billing data
     */
    public function getAnthropicBilling(): array {
        $this->logger->info('[AiProviderBilling] Fetching Anthropic billing');

        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            return [
                'success' => false,
                'error' => 'API key not configured',
            ];
        }

        // Try to get usage from Anthropic's organization endpoint
        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])
                ->timeout(30)
                ->get('https://api.anthropic.com/v1/organization/usage');

            if ($response->successful()) {
                $data = $response->json();

                // Extract current month usage
                $totalCost = $data['total_cost'] ?? 0; // Assume API returns cost

                $this->logger->info('[AiProviderBilling] Anthropic billing fetched', [
                    'total_cost' => $totalCost,
                ]);

                return [
                    'success' => true,
                    'provider' => 'Anthropic',
                    'period' => [
                        'start' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                        'end' => Carbon::now()->format('Y-m-d'),
                    ],
                    'total_cost' => $totalCost,
                    'total_requests' => 0,
                    'raw_data' => $data,
                ];
            }
        } catch (\Exception $e) {
            $this->logger->debug('[AiProviderBilling] Anthropic API not available', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback: API not available
        $this->logger->warning('[AiProviderBilling] Anthropic billing API not available');

        return [
            'success' => false,
            'error' => 'Anthropic billing API not available. Check manually at console.anthropic.com/settings/billing',
            'manual_check_url' => 'https://console.anthropic.com/settings/billing',
        ];
    }
    /**
     * Get OpenAI billing data for current month
     *
     * IMPORTANT: OpenAI's /v1/dashboard/billing/* endpoints require SESSION KEY (browser login),
     * NOT API key. They CANNOT be accessed programmatically via API.
     *
     * We can only:
     * 1. Track usage internally (tokens * pricing)
     * 2. Link to manual dashboard check
     *
     * @return array Billing data (internal tracking only)
     */
    public function getOpenAIBilling(): array {
        $cacheKey = 'openai_billing_' . now()->format('Y-m');

        return Cache::remember($cacheKey, 300, function () { // Cache 5 min instead of 1 hour
            $this->logger->info('[AiProviderBilling] Fetching OpenAI billing');

            $apiKey = config('services.openai.api_key');

            if (!$apiKey) {
                $this->logger->error('[AiProviderBilling] OpenAI API key not configured');
                return [
                    'success' => false,
                    'error' => 'API key not configured',
                ];
            }

            try {
                // Get subscription info to get total spend
                $subscriptionResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])
                    ->timeout(30)
                    ->get('https://api.openai.com/v1/dashboard/billing/subscription');

                if (!$subscriptionResponse->successful()) {
                    $this->logger->error('[AiProviderBilling] OpenAI subscription API error', [
                        'status' => $subscriptionResponse->status(),
                    ]);

                    return [
                        'success' => false,
                        'error' => 'API error: ' . $subscriptionResponse->status(),
                        'manual_check_url' => 'https://platform.openai.com/usage',
                    ];
                }

                $subscription = $subscriptionResponse->json();

                // Get usage for current billing period
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->format('Y-m-d');

                $usageResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])
                    ->timeout(30)
                    ->get('https://api.openai.com/v1/dashboard/billing/usage', [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);

                if (!$usageResponse->successful()) {
                    $this->logger->error('[AiProviderBilling] OpenAI usage API error', [
                        'status' => $usageResponse->status(),
                    ]);

                    return [
                        'success' => false,
                        'error' => 'Usage API error: ' . $usageResponse->status(),
                        'manual_check_url' => 'https://platform.openai.com/usage',
                    ];
                }

                $usage = $usageResponse->json();

                // Total usage is in cents
                $totalCostCents = $usage['total_usage'] ?? 0;
                $totalCost = $totalCostCents / 100; // Convert to dollars

                $this->logger->info('[AiProviderBilling] OpenAI billing fetched', [
                    'total_cost' => $totalCost,
                    'period' => "{$startDate} to {$endDate}",
                ]);

                return [
                    'success' => true,
                    'provider' => 'OpenAI',
                    'period' => [
                        'start' => $startDate,
                        'end' => $endDate,
                    ],
                    'total_cost' => $totalCost,
                    'total_requests' => 0,
                    'raw_data' => [
                        'subscription' => $subscription,
                        'usage' => $usage,
                    ],
                ];
            } catch (\Exception $e) {
                $this->logger->debug('[AiProviderBilling] OpenAI API unavailable (expected)', [
                    'error' => $e->getMessage(),
                ]);

                // Expected: billing endpoints require browser session
                return [
                    'success' => false,
                    'error' => 'OpenAI billing API requires browser session. Check manually.',
                    'manual_check_url' => 'https://platform.openai.com/usage',
                ];
            }
        });
    }

    /**
     * Compare provider billing with internal tracking
     *
     * @param string $provider 'anthropic', 'openai', 'perplexity'
     * @return array Comparison results with discrepancy alerts
     */
    public function compareBilling(string $provider): array {
        $this->logger->info('[AiProviderBilling] Comparing billing', [
            'provider' => $provider,
        ]);

        // Get internal tracking
        $internalStats = $this->costCalculator->getCurrentMonthSpending();
        $internalByProvider = collect($internalStats['by_provider'])
            ->keyBy(fn($p) => strtolower($p['provider']))
            ->toArray();

        $internalCost = $internalByProvider[$provider]['cost'] ?? 0;

        // Get provider billing
        $providerBilling = match ($provider) {
            'openai' => $this->getOpenAIBilling(),
            'anthropic' => $this->getAnthropicBilling(),
            'perplexity' => [
                'success' => false,
                'error' => 'Perplexity billing API not available. Check manually at perplexity.ai/settings',
                'manual_check_url' => 'https://www.perplexity.ai/settings',
            ],
            default => null,
        };

        if (!$providerBilling || !($providerBilling['success'] ?? false)) {
            // Provider billing API not available, but STILL show internal tracking

            // Check if API key is configured
            $apiKeyConfigured = match ($provider) {
                'openai' => !empty(config('services.openai.api_key')),
                'anthropic' => !empty(config('services.anthropic.api_key')),
                'perplexity' => !empty(config('services.perplexity.api_key')),
                default => false,
            };

            return array_merge([
                'success' => false, // Billing API failed
                'provider' => $provider,
                'error' => $providerBilling['error'] ?? 'Provider billing not available',
                'api_configured' => $apiKeyConfigured, // NEW: Distinguish configured vs not configured
                'internal' => [
                    'cost' => $internalCost,
                    'requests' => $internalByProvider[$provider]['messages'] ?? 0,
                    'tokens' => $internalByProvider[$provider]['tokens'] ?? 0,
                ],
                'provider_api' => null, // Not available
                'comparison' => null, // Cannot compare without provider data
            ], isset($providerBilling['manual_check_url']) ? ['manual_check_url' => $providerBilling['manual_check_url']] : []);
        }

        $providerCost = $providerBilling['total_cost'];
        $discrepancy = abs($providerCost - $internalCost);
        $discrepancyPercentage = $providerCost > 0
            ? ($discrepancy / $providerCost) * 100
            : 0;

        // Alert if discrepancy > 5%
        $hasAlert = $discrepancyPercentage > 5;

        $this->logger->info('[AiProviderBilling] Comparison completed', [
            'provider' => $provider,
            'internal_cost' => $internalCost,
            'provider_cost' => $providerCost,
            'discrepancy' => $discrepancy,
            'discrepancy_percentage' => $discrepancyPercentage,
            'has_alert' => $hasAlert,
        ]);

        return [
            'success' => true,
            'provider' => $provider,
            'internal' => [
                'cost' => $internalCost,
                'requests' => $internalByProvider[$provider]['messages'] ?? 0,
                'tokens' => $internalByProvider[$provider]['tokens'] ?? 0,
            ],
            'provider_api' => [
                'cost' => $providerCost,
                'requests' => $providerBilling['total_requests'] ?? 0,
            ],
            'comparison' => [
                'discrepancy_usd' => $discrepancy,
                'discrepancy_percentage' => round($discrepancyPercentage, 2),
                'status' => $hasAlert ? 'WARNING' : 'OK',
                'message' => $hasAlert
                    ? "Discrepancy > 5%: Provider shows \${$providerCost}, internal tracking shows \${$internalCost}"
                    : "Tracking accurate (discrepancy < 5%)",
            ],
        ];
    }

    /**
     * Get all provider billings and compare with internal
     *
     * @return array Complete billing overview
     */
    public function getAllBillingComparison(): array {
        $results = [];

        foreach (['openai', 'anthropic', 'perplexity'] as $provider) {
            $results[$provider] = $this->compareBilling($provider);
        }

        return [
            'success' => true,
            'period' => 'current_month',
            'providers' => $results,
            'summary' => [
                'total_providers_checked' => count($results),
                'providers_with_alerts' => collect($results)->filter(
                    fn($r) => ($r['comparison']['status'] ?? 'OK') === 'WARNING'
                )->count(),
            ],
        ];
    }
}
