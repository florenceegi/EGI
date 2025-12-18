<?php

namespace App\Egi\Commodity;

interface CommodityContract
{
    /**
     * Define the specific fields required for this commodity.
     * Returns an array validation rules or field definitions.
     */
    public function fields(): array;

    /**
     * Validate the input data for this commodity.
     * Throws ValidationException on failure.
     */
    public function validate(array $input): void;

    /**
     * Get the display name for this commodity type.
     */
    public function name(): string;

    /**
     * Calculate price or other derived metrics specifically for this commodity.
     */
    public function calculateMetrics(array $data): array;

    /**
     * Calculate the monetary value of the commodity based on its traits and current market price.
     *
     * @param \App\Models\Egi|mixed $egi The EGI instance (or data object)
     * @param string $currency Target currency
     * @return array|null The calculation result or null on failure
     */
    public function calculateValue($egi, string $currency = 'EUR'): ?array;

    /**
     * Force a refresh of the commodity price (paid or free depending on context).
     *
     * @param string $currency Target currency
     * @param \App\Models\Egi|null $egi Optional EGI context
     * @return array Result with success status and price data
     */
    public function forceRefresh(string $currency = 'EUR', $egi = null): array;
}
