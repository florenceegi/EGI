<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoldPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Gold Price API Controller
 * 
 * Handles gold price quotation requests and paid refreshes
 */
class GoldPriceController extends Controller
{
    public function __construct(
        protected GoldPriceService $goldPriceService
    ) {}

    /**
     * Get current gold price (from cache or API)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPrice(Request $request): JsonResponse
    {
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
    public function forceRefresh(Request $request): JsonResponse
    {
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
            $statusCode = match($result['error']) {
                'insufficient_egili' => 402, // Payment Required
                'api_failure' => 503,
                default => 500,
            };

            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'message' => match($result['error']) {
                    'insufficient_egili' => __('gold_bar.insufficient_egili', [
                        'required' => $result['required'],
                        'current' => $result['current_balance'],
                    ]),
                    'api_failure' => __('gold_bar.error'),
                    default => __('gold_bar.error'),
                },
                'required_egili' => $result['required'] ?? null,
                'current_balance' => $result['current_balance'] ?? null,
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
     * Get refresh info (cost, time until next refresh)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getRefreshInfo(Request $request): JsonResponse
    {
        $currency = strtoupper($request->input('currency', 'EUR'));
        $timeUntilRefresh = $this->goldPriceService->getTimeUntilRefresh($currency);
        
        $user = $request->user();
        $canAffordRefresh = $user 
            ? $user->egili_balance >= $this->goldPriceService->getRefreshCost()
            : false;

        return response()->json([
            'success' => true,
            'data' => [
                'refresh_cost_egili' => $this->goldPriceService->getRefreshCost(),
                'next_auto_refresh' => $timeUntilRefresh['formatted'],
                'next_refresh_at' => $timeUntilRefresh['next_refresh_at']->toIso8601String(),
                'seconds_until_refresh' => $timeUntilRefresh['seconds'],
                'can_afford_refresh' => $canAffordRefresh,
                'user_egili_balance' => $user?->egili_balance,
            ],
        ]);
    }
}
