<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\GoldPriceServiceInterface;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Gold Price API Controller
 *
 * Handles gold price quotation requests and paid refreshes
 */
class GoldPriceController extends Controller {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public function __construct(
        protected GoldPriceServiceInterface $goldPriceService,
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get current gold price (from cache or API)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPrice(Request $request): JsonResponse {
        $currency = strtoupper($request->input('currency', 'EUR'));

        $goldPrice = $this->goldPriceService->getGoldPrice($currency);
        $timeUntilRefresh = $this->goldPriceService->getTimeUntilRefresh($currency);

        if (!$goldPrice) {
            return response()->json([
                'success' => false,
                'error' => __('gold_bar.error'),
            ], 503);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'price_per_gram' => $goldPrice['price_per_gram'],
                'price_per_oz' => $goldPrice['price_per_oz'],
                'currency' => $goldPrice['currency'],
                'source' => $goldPrice['source'] ?? null,
                'cached_at' => $goldPrice['timestamp']->toIso8601String(),
            ],
            'refresh' => [
                'next_auto_refresh' => $timeUntilRefresh['formatted'],
                'next_refresh_at' => $timeUntilRefresh['next_refresh_at']->toIso8601String(),
                'manual_refresh_cost' => $this->goldPriceService->getRefreshCost(),
            ],
        ]);
    }

    /**
     * Force refresh gold price (paid - costs Egili)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forceRefresh(Request $request): JsonResponse {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthorized',
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        $currency = strtoupper($request->input('currency', 'EUR'));

        $result = $this->goldPriceService->forceRefresh($user, $currency);

        if (!$result['success']) {
            $statusCode = match ($result['error']) {
                'insufficient_egili' => 402, // Payment Required
                'throttle_exceeded' => 429, // Too Many Requests
                'api_failure' => 503,
                default => 500,
            };

            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'message' => match ($result['error']) {
                    'insufficient_egili' => __('gold_bar.insufficient_egili', [
                        'required' => $result['required'],
                        'current' => $result['current_balance'],
                    ]),
                    'throttle_exceeded' => __('gold_bar.throttle_exceeded'),
                    'api_failure' => __('gold_bar.error'),
                    default => __('gold_bar.error'),
                },
                'required_egili' => $result['required'] ?? null,
                'current_balance' => $result['current_balance'] ?? null,
                'reset_at' => $result['reset_at'] ?? null,
                'seconds_until_reset' => $result['seconds_until_reset'] ?? null,
            ], $statusCode);
        }

        $timeUntilRefresh = $this->goldPriceService->getTimeUntilRefresh($currency);

        return response()->json([
            'success' => true,
            'message' => __('gold_bar.refresh_success'),
            'data' => [
                'price_per_gram' => $result['gold_price']['price_per_gram'],
                'price_per_oz' => $result['gold_price']['price_per_oz'],
                'currency' => $result['gold_price']['currency'],
                'source' => $result['gold_price']['source'] ?? null,
                'refreshed_at' => $result['gold_price']['timestamp']->toIso8601String(),
            ],
            'cost' => [
                'egili_spent' => $result['cost'],
                'new_balance' => $result['new_balance'],
            ],
            'refresh' => [
                'next_auto_refresh' => $timeUntilRefresh['formatted'],
                'next_refresh_at' => $timeUntilRefresh['next_refresh_at']->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get refresh info (cost, time until next refresh, throttle status)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRefreshInfo(Request $request): JsonResponse {
        $currency = strtoupper($request->input('currency', 'EUR'));
        $timeUntilRefresh = $this->goldPriceService->getTimeUntilRefresh($currency);

        $user = $request->user();
        $canAffordRefresh = $user
            ? $user->egili_balance >= $this->goldPriceService->getRefreshCost()
            : false;

        // Get throttle info if user is authenticated
        $throttleInfo = $user
            ? $this->goldPriceService->getThrottleInfo($user)
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'refresh_cost_egili' => $this->goldPriceService->getRefreshCost(),
                'next_auto_refresh' => $timeUntilRefresh['formatted'],
                'next_refresh_at' => $timeUntilRefresh['next_refresh_at']->toIso8601String(),
                'seconds_until_refresh' => $timeUntilRefresh['seconds'],
                'can_afford_refresh' => $canAffordRefresh,
                'user_egili_balance' => $user?->egili_balance,
                'throttle' => $throttleInfo,
            ],
        ]);
    }

    /**
     * Refresh gold price for mint (FREE - no Egili cost)
     * Used before minting a Gold Bar EGI to get fresh price
     * Returns the calculated value for the specific EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function refreshForMint(Request $request, int $egiId): JsonResponse {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'unauthorized',
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        $egi = \App\Models\Egi::find($egiId);

        if (!$egi) {
            return response()->json([
                'success' => false,
                'error' => 'egi_not_found',
                'message' => 'EGI not found',
            ], 404);
        }

        // Check if EGI is a Gold Bar
        if (!$egi->isGoldBar()) {
            return response()->json([
                'success' => false,
                'error' => 'not_gold_bar',
                'message' => __('gold_bar.not_gold_bar'),
            ], 400);
        }

        // Check if user can mint this EGI (creator or has valid reservation)
        $canMint = $egi->user_id === $user->id ||
            $egi->reservations()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('is_current', true)
            ->exists();

        if (!$canMint) {
            return response()->json([
                'success' => false,
                'error' => 'unauthorized',
                'message' => __('mint.errors.unauthorized'),
            ], 403);
        }

        $currency = strtoupper($request->input('currency', 'EUR'));

        // Force refresh the gold price (FREE for mint)
        $refreshResult = $this->goldPriceService->forceRefreshFree($currency, $egi);

        if (!$refreshResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $refreshResult['error'] ?? 'api_failure',
                'message' => __('gold_bar.error'),
            ], 503);
        }

        // Calculate the EGI gold bar value with fresh price
        $goldValue = $this->goldPriceService->calculateFromEgi($egi, $currency);

        if (!$goldValue) {
            return response()->json([
                'success' => false,
                'error' => 'calculation_error',
                'message' => __('gold_bar.error'),
            ], 500);
        }

        // Return fresh price with 10-minute validity timestamp
        $validUntil = now()->addMinutes(10);


        // STAGING FIX: Persist Cache (Robustness over Session)
        // Key: gold_bar_mint_{userId}_{egiId}
        $cacheKey = 'gold_bar_mint_' . $user->id . '_' . $egi->id;
        
        Cache::put($cacheKey, [
            'refreshed_at' => now()->timestamp,
            'valid_until' => $validUntil->timestamp,
            'price' => (float) $goldValue['final_value'],
            'gold_data' => $goldValue,
        ], 600); // 10 minutes TTL

        $this->logger->info('Gold Bar price CACHED by API', [
            'egi_id' => $egi->id,
            'price' => $goldValue['final_value'],
            'cache_key' => $cacheKey
        ]);

        return response()->json([
            'success' => true,
            'message' => __('gold_bar.refresh_success'),
            'data' => [
                'egi_id' => $egi->id,
                'gold_weight' => $goldValue['weight_grams'],
                'gold_purity' => $egi->getGoldPurity(),
                'pure_gold_grams' => $goldValue['pure_gold_grams'],
                'gold_price_per_gram' => $goldValue['gold_price_per_gram'],
                'base_value' => $goldValue['base_value'],
                'margin_applied' => $goldValue['margin_applied'],
                'final_value' => $goldValue['final_value'],
                'currency' => $goldValue['currency'],
                'refreshed_at' => now()->toIso8601String(),
                'valid_until' => $validUntil->toIso8601String(),
                'valid_for_seconds' => 600, // 10 minutes
            ],
        ]);
    }
}
