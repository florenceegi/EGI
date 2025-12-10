<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: GoldPriceService
 * 🎯 Purpose: Handles gold price quotations for Gold Bar EGIs
 * 🧱 Core Logic: Fetches gold spot price, calculates indicative values based on weight/purity
 * 🛡️ Error Management: Integrated UEM for robust error handling
 * 💰 Multi-Currency: Supports EUR, USD, GBP for gold quotations
 *
 * @package App\Services
 * @author Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-08
 */
class GoldPriceService {
    private const CACHE_KEY_PREFIX = 'gold_price_';
    private const CACHE_TTL_SECONDS = 21600; // 6 hours - gold prices are stable, saves API calls (free tier: 300/month = ~120 calls with 6h cache)

    /**
     * Cost in Egili for manual price refresh
     */
    public const REFRESH_COST_EGILI = 1;

    /**
     * Throttle settings: max refreshes per time window
     */
    public const REFRESH_THROTTLE_MAX = 3;
    public const REFRESH_THROTTLE_WINDOW_SECONDS = 21600; // 6 hours

    /**
     * Conversion factors to grams
     */
    private const WEIGHT_TO_GRAMS = [
        'Grams' => 1,
        'Ounces' => 28.3495,
        'Troy Ounces' => 31.1035,
    ];

    /**
     * Purity percentages
     */
    private const PURITY_PERCENT = [
        '999' => 0.999,
        '995' => 0.995,
        '990' => 0.990,
        '916' => 0.916,
        '750' => 0.750,
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param EgiliService $egiliService
     */
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager,
        protected EgiliService $egiliService
    ) {
    }

    /**
     * Get current gold spot price per gram in specified currency.
     * Uses caching and implements fallback.
     *
     * @param string $currency Currency code (USD, EUR, GBP)
     * @return array|null ['price_per_gram' => float, 'price_per_oz' => float, 'currency' => string, 'timestamp' => Carbon]
     */
    public function getGoldPrice(string $currency = 'EUR'): ?array {
        $currency = strtoupper($currency);
        $cacheKey = self::CACHE_KEY_PREFIX . $currency;

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($currency) {
            try {
                // Primary: Gold-API.io (free tier allows 300 requests/month)
                $result = $this->fetchFromGoldApi($currency);

                if (!$result) {
                    // Fallback: MetalPriceAPI (free tier)
                    $result = $this->fetchFromMetalPriceApi($currency);
                }

                if (!$result) {
                    throw new \Exception('All gold price API sources failed.');
                }

                $this->logger->info('Gold price fetched successfully', [
                    'currency' => $currency,
                    'price_per_gram' => $result['price_per_gram'],
                    'source' => $result['source'] ?? 'unknown'
                ]);

                return $result;
            } catch (\Exception $e) {
                $this->errorManager->handle('GOLD_PRICE_SERVICE_FAILED', [
                    'currency' => $currency,
                    'error' => $e->getMessage(),
                ], $e);

                return null;
            }
        });
    }

    /**
     * Fetch gold price from Gold-API.io
     *
     * @param string $currency
     * @return array|null
     */
    private function fetchFromGoldApi(string $currency): ?array {
        try {
            $apiKey = config('services.gold_api.key');

            if (empty($apiKey)) {
                $this->logger->debug('Gold-API key not configured, skipping');
                return null;
            }

            $response = Http::withHeaders([
                'x-access-token' => $apiKey,
            ])->get('https://www.goldapi.io/api/XAU/' . $currency);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (!isset($data['price'])) {
                return null;
            }

            // price is per troy ounce
            $pricePerOz = (float) $data['price'];
            $pricePerGram = $pricePerOz / self::WEIGHT_TO_GRAMS['Troy Ounces'];

            return [
                'price_per_gram' => round($pricePerGram, 2),
                'price_per_oz' => round($pricePerOz, 2),
                'currency' => $currency,
                'timestamp' => now(),
                'source' => 'goldapi',
            ];
        } catch (\Exception $e) {
            $this->logger->warning('Gold-API request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch gold price from MetalPriceAPI
     *
     * @param string $currency
     * @return array|null
     */
    private function fetchFromMetalPriceApi(string $currency): ?array {
        try {
            $apiKey = config('services.metal_price_api.key');

            if (empty($apiKey)) {
                $this->logger->debug('MetalPriceAPI key not configured, skipping');
                return null;
            }

            $response = Http::withHeaders([
                'x-access-token' => $apiKey,
            ])->get('https://api.metalpriceapi.com/v1/latest', [
                'api_key' => $apiKey,
                'base' => $currency,
                'currencies' => 'XAU',
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (!isset($data['rates']['XAU'])) {
                return null;
            }

            // XAU rate is in troy ounces, inverted (how many oz per currency unit)
            $ozPerCurrency = (float) $data['rates']['XAU'];
            $pricePerOz = 1 / $ozPerCurrency;
            $pricePerGram = $pricePerOz / self::WEIGHT_TO_GRAMS['Troy Ounces'];

            return [
                'price_per_gram' => round($pricePerGram, 2),
                'price_per_oz' => round($pricePerOz, 2),
                'currency' => $currency,
                'timestamp' => now(),
                'source' => 'metalpriceapi',
            ];
        } catch (\Exception $e) {
            $this->logger->warning('MetalPriceAPI request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Calculate indicative gold value for an EGI Gold Bar.
     *
     * @param float $weight Weight of gold
     * @param string $weightUnit Weight unit (Grams, Ounces, Troy Ounces)
     * @param string $purity Purity code (999, 995, 990, 916, 750)
     * @param float|null $marginPercent Optional margin percentage (e.g., 5 for 5%)
     * @param float|null $marginFixed Optional fixed margin amount
     * @param string $currency Currency for the quotation
     * @return array|null ['base_value' => float, 'final_value' => float, 'margin_applied' => float, 'currency' => string, 'gold_price' => array]
     */
    public function calculateGoldBarValue(
        float $weight,
        string $weightUnit = 'Grams',
        string $purity = '999',
        ?float $marginPercent = null,
        ?float $marginFixed = null,
        string $currency = 'EUR'
    ): ?array {
        // Get current gold price
        $goldPrice = $this->getGoldPrice($currency);

        if (!$goldPrice) {
            return null;
        }

        // Convert weight to grams
        $conversionFactor = self::WEIGHT_TO_GRAMS[$weightUnit] ?? 1;
        $weightInGrams = $weight * $conversionFactor;

        // Apply purity
        $purityFactor = self::PURITY_PERCENT[$purity] ?? 0.999;
        $pureGoldGrams = $weightInGrams * $purityFactor;

        // Calculate base value
        $baseValue = $pureGoldGrams * $goldPrice['price_per_gram'];

        // Apply margin (priority: fixed > percent)
        $marginApplied = 0;
        $finalValue = $baseValue;

        if ($marginFixed !== null && $marginFixed > 0) {
            $marginApplied = $marginFixed;
            $finalValue = $baseValue + $marginFixed;
        } elseif ($marginPercent !== null && $marginPercent > 0) {
            $marginApplied = $baseValue * ($marginPercent / 100);
            $finalValue = $baseValue + $marginApplied;
        }

        return [
            'weight_grams' => round($weightInGrams, 2),
            'pure_gold_grams' => round($pureGoldGrams, 2),
            'base_value' => round($baseValue, 2),
            'margin_applied' => round($marginApplied, 2),
            'final_value' => round($finalValue, 2),
            'currency' => $currency,
            'gold_price_per_gram' => $goldPrice['price_per_gram'],
            'gold_price_per_oz' => $goldPrice['price_per_oz'],
            'price_timestamp' => $goldPrice['timestamp'],
            'price_source' => $goldPrice['source'] ?? 'unknown',
        ];
    }

    /**
     * Calculate gold bar value from Egi model traits.
     *
     * @param \App\Models\Egi $egi
     * @param string $currency
     * @return array|null
     */
    public function calculateFromEgi($egi, string $currency = 'EUR'): ?array {
        // Check if EGI has gold bar traits
        $weight = $egi->getGoldWeight();
        $weightUnit = $egi->getGoldWeightUnit();
        $purity = $egi->getGoldPurity();

        if (!$weight || !$weightUnit || !$purity) {
            return null;
        }

        $marginPercent = $egi->getGoldMarginPercent();
        $marginFixed = $egi->getGoldMarginFixed();

        return $this->calculateGoldBarValue(
            $weight,
            $weightUnit,
            $purity,
            $marginPercent,
            $marginFixed,
            $currency
        );
    }

    /**
     * Get weight conversion factors (useful for frontend)
     *
     * @return array
     */
    public function getWeightUnits(): array {
        return array_keys(self::WEIGHT_TO_GRAMS);
    }

    /**
     * Get available purity levels (useful for frontend)
     *
     * @return array
     */
    public function getPurityLevels(): array {
        return array_keys(self::PURITY_PERCENT);
    }

    /**
     * Clear cached gold prices
     *
     * @param string|null $currency If null, clears all currencies
     * @return void
     */
    public function clearCache(?string $currency = null): void {
        if ($currency) {
            Cache::forget(self::CACHE_KEY_PREFIX . strtoupper($currency));
        } else {
            // Clear common currencies
            foreach (['EUR', 'USD', 'GBP', 'CHF'] as $curr) {
                Cache::forget(self::CACHE_KEY_PREFIX . $curr);
            }
        }
    }

    /**
     * Force refresh gold price (paid feature - costs Egili)
     * This bypasses the cache and fetches fresh data from API
     * Throttled to max 3 refreshes per 6 hours per user
     *
     * @param \App\Models\User $user The user requesting the refresh
     * @param string $currency Currency to refresh
     * @return array ['success' => bool, 'gold_price' => array|null, 'error' => string|null]
     */
    public function forceRefresh(\App\Models\User $user, string $currency = 'EUR'): array {
        $currency = strtoupper($currency);

        // Check throttle: max 3 refreshes per 6 hours
        $throttleCheck = $this->checkRefreshThrottle($user);
        if (!$throttleCheck['allowed']) {
            return [
                'success' => false,
                'gold_price' => null,
                'error' => 'throttle_exceeded',
                'remaining_refreshes' => 0,
                'reset_at' => $throttleCheck['reset_at'],
                'seconds_until_reset' => $throttleCheck['seconds_until_reset'],
            ];
        }

        // Check if user has enough Egili via EgiliService
        $userBalance = $this->egiliService->getBalance($user);
        if ($userBalance < self::REFRESH_COST_EGILI) {
            return [
                'success' => false,
                'gold_price' => null,
                'error' => 'insufficient_egili',
                'required' => self::REFRESH_COST_EGILI,
                'current_balance' => $userBalance,
            ];
        }

        // Increment throttle counter BEFORE the operation
        $this->incrementRefreshThrottle($user);

        // Deduct Egili via EgiliService (creates transaction automatically)
        try {
            $this->egiliService->spend(
                $user,
                self::REFRESH_COST_EGILI,
                'gold_price_refresh',
                'service',
                [
                    'currency' => $currency,
                    'timestamp' => now()->toIso8601String(),
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Gold price refresh - Egili deduction failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'gold_price' => null,
                'error' => 'payment_failed',
            ];
        }

        // Clear cache for this currency
        $this->clearCache($currency);

        // Fetch fresh price
        $goldPrice = $this->getGoldPrice($currency);

        if (!$goldPrice) {
            // Refund on failure via EgiliService
            try {
                $this->egiliService->earn(
                    $user,
                    self::REFRESH_COST_EGILI,
                    'gold_price_refresh_refund',
                    'refund',
                    [
                        'currency' => $currency,
                        'reason' => 'api_failure',
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->error('Gold price refresh - Refund failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'gold_price' => null,
                'error' => 'api_failure',
            ];
        }

        $this->logger->info('Gold price force refreshed by user', [
            'user_id' => $user->id,
            'currency' => $currency,
            'cost_egili' => self::REFRESH_COST_EGILI,
            'new_price_per_gram' => $goldPrice['price_per_gram'],
        ]);

        return [
            'success' => true,
            'gold_price' => $goldPrice,
            'cost' => self::REFRESH_COST_EGILI,
            'new_balance' => $this->egiliService->getBalance($user),
        ];
    }

    /**
     * Force refresh gold price without charging Egili (for pre-mint operations only)
     * This method should only be called from the mint flow context.
     *
     * @param string $currency
     * @param \App\Models\Egi|null $egi Optional EGI for logging purposes
     * @return array
     */
    public function forceRefreshFree(string $currency = 'EUR', ?\App\Models\Egi $egi = null): array {
        $currency = strtoupper($currency);

        // Clear cache for this currency
        $this->clearCache($currency);

        // Fetch fresh price
        $goldPrice = $this->getGoldPrice($currency);

        if (!$goldPrice) {
            $this->logger->error('Pre-mint gold price refresh failed', [
                'currency' => $currency,
                'egi_id' => $egi?->id,
            ]);

            return [
                'success' => false,
                'gold_price' => null,
                'error' => 'api_failure',
            ];
        }

        $this->logger->info('Gold price refreshed for pre-mint (free)', [
            'currency' => $currency,
            'egi_id' => $egi?->id,
            'new_price_per_gram' => $goldPrice['price_per_gram'],
        ]);

        return [
            'success' => true,
            'gold_price' => $goldPrice,
            'refreshed_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get time until next automatic refresh
     *
     * @param string $currency
     * @return array ['seconds' => int, 'formatted' => string, 'next_refresh_at' => Carbon]
     */
    public function getTimeUntilRefresh(string $currency = 'EUR'): array {
        $cacheKey = self::CACHE_KEY_PREFIX . strtoupper($currency);

        // Try to get the cached timestamp
        $cached = Cache::get($cacheKey);

        if (!$cached || !isset($cached['timestamp'])) {
            return [
                'seconds' => 0,
                'formatted' => __('gold_bar.refresh_available_now'),
                'next_refresh_at' => now(),
            ];
        }

        $cachedAt = $cached['timestamp'];
        $expiresAt = $cachedAt->copy()->addSeconds(self::CACHE_TTL_SECONDS);
        $secondsRemaining = max(0, now()->diffInSeconds($expiresAt, false));

        // Format as HH:MM:SS
        $hours = floor($secondsRemaining / 3600);
        $minutes = floor(($secondsRemaining % 3600) / 60);
        $formatted = sprintf('%dh %02dm', $hours, $minutes);

        return [
            'seconds' => $secondsRemaining,
            'formatted' => $formatted,
            'next_refresh_at' => $expiresAt,
        ];
    }

    /**
     * Get the cost for manual refresh
     *
     * @return int
     */
    public function getRefreshCost(): int {
        return self::REFRESH_COST_EGILI;
    }

    /**
     * Check if user can perform a refresh (throttle check)
     *
     * @param \App\Models\User $user
     * @return array ['allowed' => bool, 'remaining' => int, 'reset_at' => Carbon|null]
     */
    public function checkRefreshThrottle(\App\Models\User $user): array {
        $cacheKey = "gold_refresh_throttle_{$user->id}";
        $throttleData = Cache::get($cacheKey);

        if (!$throttleData) {
            return [
                'allowed' => true,
                'remaining' => self::REFRESH_THROTTLE_MAX,
                'reset_at' => null,
                'seconds_until_reset' => 0,
            ];
        }

        $count = $throttleData['count'] ?? 0;
        $resetAt = $throttleData['reset_at'] ?? now();

        // If reset time has passed, allow
        if (now()->greaterThanOrEqualTo($resetAt)) {
            Cache::forget($cacheKey);
            return [
                'allowed' => true,
                'remaining' => self::REFRESH_THROTTLE_MAX,
                'reset_at' => null,
                'seconds_until_reset' => 0,
            ];
        }

        $remaining = max(0, self::REFRESH_THROTTLE_MAX - $count);

        return [
            'allowed' => $remaining > 0,
            'remaining' => $remaining,
            'reset_at' => $resetAt->toIso8601String(),
            'seconds_until_reset' => (int) now()->diffInSeconds($resetAt, false),
        ];
    }

    /**
     * Increment the refresh throttle counter for user
     *
     * @param \App\Models\User $user
     * @return void
     */
    protected function incrementRefreshThrottle(\App\Models\User $user): void {
        $cacheKey = "gold_refresh_throttle_{$user->id}";
        $throttleData = Cache::get($cacheKey);

        if (!$throttleData || now()->greaterThanOrEqualTo($throttleData['reset_at'] ?? now())) {
            // Start new throttle window
            Cache::put($cacheKey, [
                'count' => 1,
                'reset_at' => now()->addSeconds(self::REFRESH_THROTTLE_WINDOW_SECONDS),
            ], self::REFRESH_THROTTLE_WINDOW_SECONDS);
        } else {
            // Increment existing counter
            $throttleData['count'] = ($throttleData['count'] ?? 0) + 1;
            $remainingTtl = (int) now()->diffInSeconds($throttleData['reset_at'], false);
            Cache::put($cacheKey, $throttleData, max(1, $remainingTtl));
        }
    }

    /**
     * Get throttle info for display in UI
     *
     * @param \App\Models\User $user
     * @return array
     */
    public function getThrottleInfo(\App\Models\User $user): array {
        $check = $this->checkRefreshThrottle($user);

        return [
            'max_refreshes' => self::REFRESH_THROTTLE_MAX,
            'remaining_refreshes' => $check['remaining'],
            'can_refresh' => $check['allowed'],
            'reset_at' => $check['reset_at'],
            'seconds_until_reset' => $check['seconds_until_reset'],
            'window_hours' => self::REFRESH_THROTTLE_WINDOW_SECONDS / 3600,
        ];
    }
}
