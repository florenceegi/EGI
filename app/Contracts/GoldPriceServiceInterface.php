<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Egi;

/**
 * Interface GoldPriceServiceInterface
 * 
 * Defines the contract for gold price services.
 */
interface GoldPriceServiceInterface
{
    /**
     * Get current gold spot price per gram in specified currency.
     *
     * @param string $currency Currency code (USD, EUR, GBP)
     * @return array|null
     */
    public function getGoldPrice(string $currency = 'EUR'): ?array;

    /**
     * Calculate indicative gold value for an EGI Gold Bar.
     *
     * @param float $weight
     * @param string $weightUnit
     * @param string $purity
     * @param float|null $marginPercent
     * @param float|null $marginFixed
     * @param string $currency
     * @return array|null
     */
    public function calculateGoldBarValue(
        float $weight,
        string $weightUnit = 'Grams',
        string $purity = '999',
        ?float $marginPercent = null,
        ?float $marginFixed = null,
        string $currency = 'EUR'
    ): ?array;

    /**
     * Calculate gold bar value from Egi model traits.
     *
     * @param Egi|mixed $egi
     * @param string $currency
     * @return array|null
     */
    public function calculateFromEgi($egi, string $currency = 'EUR'): ?array;

    /**
     * Get weight conversion factors.
     *
     * @return array
     */
    public function getWeightUnits(): array;

    /**
     * Get available purity levels.
     *
     * @return array
     */
    public function getPurityLevels(): array;

    /**
     * Clear cached gold prices.
     *
     * @param string|null $currency
     * @return void
     */
    public function clearCache(?string $currency = null): void;

    /**
     * Force refresh gold price (paid feature).
     *
     * @param User $user
     * @param string $currency
     * @return array
     */
    public function forceRefresh(User $user, string $currency = 'EUR'): array;

    /**
     * Force refresh gold price without charging (pre-mint).
     *
     * @param string $currency
     * @param Egi|null $egi
     * @return array
     */
    public function forceRefreshFree(string $currency = 'EUR', ?Egi $egi = null): array;

    /**
     * Get time until next automatic refresh.
     *
     * @param string $currency
     * @return array
     */
    public function getTimeUntilRefresh(string $currency = 'EUR'): array;

    /**
     * Get the cost for manual refresh.
     *
     * @return int
     */
    public function getRefreshCost(): int;

    /**
     * Check if user can perform a refresh.
     *
     * @param User $user
     * @return array
     */
    public function checkRefreshThrottle(User $user): array;

    /**
     * Get throttle info for display in UI.
     *
     * @param User $user
     * @return array
     */
    public function getThrottleInfo(User $user): array;
}
