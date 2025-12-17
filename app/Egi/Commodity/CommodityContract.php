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
}
