<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Natan Tutor Pricing)
 * @date 2025-11-25
 * @purpose Seed Natan Tutor pricing into ai_feature_pricing table
 */
class NatanTutorPricingSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $this->command->info('🎩 Seeding Natan Tutor pricing...');

        $natanPricing = config('natan-tutor.pricing', []);

        if (empty($natanPricing)) {
            $this->command->warn('⚠️  No Natan Tutor pricing configuration found.');
            return;
        }

        $inserted = 0;
        $updated = 0;

        foreach ($natanPricing as $key => $item) {
            $data = [
                'feature_code' => $item['code'],
                'feature_name' => $item['name'],
                'feature_description' => $item['description'] ?? null,
                'feature_category' => $this->mapCategory($item['category'] ?? 'platform_services'),
                'cost_egili' => $item['cost_egili'] ?? 0,
                'cost_fiat_eur' => null, // Solo Egili per Natan Tutor
                'is_free' => ($item['cost_egili'] ?? 0) === 0,
                'min_tier_required' => 'free',
                'is_active' => true,
                'updated_at' => now(),
            ];

            $existing = DB::table('ai_feature_pricing')
                ->where('feature_code', $item['code'])
                ->first();

            if ($existing) {
                DB::table('ai_feature_pricing')
                    ->where('feature_code', $item['code'])
                    ->update($data);
                $updated++;
            } else {
                $data['created_at'] = now();
                DB::table('ai_feature_pricing')->insert($data);
                $inserted++;
            }
        }

        $this->command->info("✅ Natan Tutor pricing seeded: {$inserted} inserted, {$updated} updated.");

        // Log per audit
        Log::info('NatanTutorPricingSeeder executed', [
            'inserted' => $inserted,
            'updated' => $updated,
            'total_items' => count($natanPricing),
        ]);
    }

    /**
     * Map Natan Tutor categories to ai_feature_pricing categories
     */
    private function mapCategory(string $category): string {
        $mapping = [
            'creator_actions' => 'platform_services',
            'collector_actions' => 'platform_services',
            'ai_services' => 'ai_services',
            'tutoring' => 'platform_services',
            'navigation' => 'platform_services',
            'info' => 'platform_services',
        ];

        return $mapping[$category] ?? 'platform_services';
    }
}
