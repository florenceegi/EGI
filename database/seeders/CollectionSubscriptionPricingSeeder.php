<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiFeaturePricing;
use Illuminate\Database\Seeder;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - EGI Collection Subscription Pricing)
 * @date 2026-03-03
 * @purpose Seed idempotente dei 4 piani abbonamento Collection in ai_feature_pricing.
 *          Usato da CollectionSubscriptionFiatService::getActivePlans().
 *          updateOrCreate su feature_code: sicuro da rieseguire.
 */
class CollectionSubscriptionPricingSeeder extends Seeder {

    public function run(): void {
        $this->command->info('[CollectionSubscriptionPricingSeeder] Ensuring 4 collection subscription plans...');

        $plans = [
            [
                'feature_code'        => 'collection_subscription_starter',
                'feature_name'        => 'Collection Subscription - Starter',
                'feature_description' => 'Piano abbonamento mensile per collezioni fino a 19 EGI.',
                'feature_category'    => 'platform_services',
                'cost_fiat_eur'       => 4.90,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_recurring'        => true,
                'recurrence_period'   => 'monthly',
                'expires'             => true,
                'is_active'           => true,
                'display_order'       => 10,
                'feature_parameters'  => [
                    'max_egis'                    => 19,
                    'plan_tier'                   => 'starter',
                    'egili_discount_percent'      => 0,
                    'egili_required_for_discount' => 0,
                ],
                'benefits' => [
                    'Fino a 19 EGI per collezione',
                    'Analytics completo',
                    'Supporto prioritario',
                ],
            ],
            [
                'feature_code'        => 'collection_subscription_basic',
                'feature_name'        => 'Collection Subscription - Basic',
                'feature_description' => 'Piano abbonamento mensile per collezioni da 20 a 49 EGI.',
                'feature_category'    => 'platform_services',
                'cost_fiat_eur'       => 7.90,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_recurring'        => true,
                'recurrence_period'   => 'monthly',
                'expires'             => true,
                'is_active'           => true,
                'display_order'       => 20,
                'feature_parameters'  => [
                    'max_egis'                    => 49,
                    'plan_tier'                   => 'basic',
                    'egili_discount_percent'      => 0,
                    'egili_required_for_discount' => 0,
                ],
                'benefits' => [
                    'Fino a 49 EGI per collezione',
                    'Analytics completo',
                    'Supporto prioritario',
                ],
            ],
            [
                'feature_code'        => 'collection_subscription_professional',
                'feature_name'        => 'Collection Subscription - Professional',
                'feature_description' => 'Piano abbonamento mensile per collezioni da 50 a 99 EGI.',
                'feature_category'    => 'platform_services',
                'cost_fiat_eur'       => 9.90,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_recurring'        => true,
                'recurrence_period'   => 'monthly',
                'expires'             => true,
                'is_active'           => true,
                'display_order'       => 30,
                'feature_parameters'  => [
                    'max_egis'                    => 99,
                    'plan_tier'                   => 'professional',
                    'egili_discount_percent'      => 0,
                    'egili_required_for_discount' => 0,
                ],
                'benefits' => [
                    'Fino a 99 EGI per collezione',
                    'Analytics completo',
                    'Supporto prioritario',
                ],
            ],
            [
                'feature_code'        => 'collection_subscription_unlimited',
                'feature_name'        => 'Collection Subscription - Unlimited',
                'feature_description' => 'Piano abbonamento mensile per collezioni con 100+ EGI.',
                'feature_category'    => 'platform_services',
                'cost_fiat_eur'       => 19.90,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_recurring'        => true,
                'recurrence_period'   => 'monthly',
                'expires'             => true,
                'is_active'           => true,
                'display_order'       => 40,
                'feature_parameters'  => [
                    'max_egis'                    => null,
                    'plan_tier'                   => 'unlimited',
                    'egili_discount_percent'      => 0,
                    'egili_required_for_discount' => 0,
                ],
                'benefits' => [
                    'EGI illimitati per collezione',
                    'Analytics completo',
                    'Supporto prioritario',
                ],
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($plans as $plan) {
            $featureCode       = $plan['feature_code'];
            $featureParameters = json_encode($plan['feature_parameters']);
            $benefits          = json_encode($plan['benefits']);

            $existing = AiFeaturePricing::where('feature_code', $featureCode)->first();

            if ($existing) {
                $existing->update([
                    'feature_name'        => $plan['feature_name'],
                    'feature_description' => $plan['feature_description'],
                    'cost_fiat_eur'       => $plan['cost_fiat_eur'],
                    'is_recurring'        => $plan['is_recurring'],
                    'recurrence_period'   => $plan['recurrence_period'],
                    'is_active'           => true,              // forza active
                    'display_order'       => $plan['display_order'],
                    'feature_parameters'  => $featureParameters,
                    'benefits'            => $benefits,
                ]);
                $this->command->line("  [UPDATE] {$featureCode} → is_active=true, cost={$plan['cost_fiat_eur']}€");
                $updated++;
            } else {
                AiFeaturePricing::create([
                    'feature_code'        => $featureCode,
                    'feature_name'        => $plan['feature_name'],
                    'feature_description' => $plan['feature_description'],
                    'feature_category'    => $plan['feature_category'],
                    'cost_fiat_eur'       => $plan['cost_fiat_eur'],
                    'cost_egili'          => $plan['cost_egili'],
                    'is_free'             => $plan['is_free'],
                    'is_recurring'        => $plan['is_recurring'],
                    'recurrence_period'   => $plan['recurrence_period'],
                    'expires'             => $plan['expires'],
                    'is_active'           => $plan['is_active'],
                    'display_order'       => $plan['display_order'],
                    'feature_parameters'  => $featureParameters,
                    'benefits'            => $benefits,
                    'is_bundle'           => false,
                    'stackable'           => false,
                    'is_beta'             => false,
                    'requires_approval'   => false,
                    'is_featured'         => false,
                    'discount_percentage' => 0,
                ]);
                $this->command->line("  [CREATE] {$featureCode} → cost={$plan['cost_fiat_eur']}€");
                $created++;
            }
        }

        $this->command->info("[CollectionSubscriptionPricingSeeder] Done: {$created} creati, {$updated} aggiornati.");
    }
}
