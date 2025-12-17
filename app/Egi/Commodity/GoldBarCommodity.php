<?php

namespace App\Egi\Commodity;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GoldBarCommodity implements CommodityContract
{
    public function name(): string
    {
        return 'Gold Bar';
    }

    public function fields(): array
    {
        return [
            'weight' => 'required|numeric|min:0.01',
            'unit' => 'required|string|in:g,oz',
            'purity' => 'required|string', // e.g., '999.9'
            'markup' => 'required|numeric|min:0',
            // 'margin_fixed' => 'nullable|numeric', // Optional based on old seeder, can remain if needed
        ];
    }

    public function validate(array $input): void
    {
        $validator = Validator::make($input, $this->fields());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function calculateMetrics(array $data): array
    {
        // Example logic: Calculate price based on weight * current gold fixing (mocked for now) * markup
        // In OS3, we might fetch real fixing here or rely on passed data.
        // For now, we just pass through or calculate standard derivatives.
        
        return [
            'calculated_price_eur' => 0, // Placeholder for real logic
        ];
    }
}
