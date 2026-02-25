<?php

namespace Database\Seeders;

use App\Models\AiFeaturePricing;
use Illuminate\Database\Seeder;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Service Packages)
 * @date 2026-02-25
 * @purpose Crea i pacchetti Servizi AI nella tabella ai_feature_pricing.
 *
 * ⚠️  PREZZI NON DEFINITI — is_active = false
 *     I prezzi (cost_fiat_eur) e gli Egili inclusi (feature_parameters.egili_amount)
 *     devono essere impostati dal superadmin da Superadmin → Prezzi Features.
 *     I pacchetti NON sono visibili agli utenti fino a quando is_active = true.
 */
class AiServicePackagesSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'feature_code'        => 'ai_package_starter',
                'feature_name'        => 'Starter Pack',
                'feature_description' => 'Pacchetto di accesso ai servizi AI — livello base. Ideale per singoli utenti o prima esperienza.',
                'feature_category'    => 'ai_services',
                'cost_fiat_eur'       => null,      // ⚠️ DA DEFINIRE via admin panel
                'cost_egili'          => null,
                'is_free'             => false,
                'is_bundle'           => true,
                'bundle_type'         => 'credit_package',
                'discount_percentage' => 0,
                'is_recurring'        => false,
                'recurrence_period'   => 'one_time',
                'expires'             => false,
                'stackable'           => true,
                'is_active'           => false,     // ⚠️ Attivare SOLO dopo aver impostato il prezzo
                'is_featured'         => false,
                'display_order'       => 10,
                'feature_parameters'  => json_encode([
                    'egili_amount' => null,         // ⚠️ DA DEFINIRE: quanti Egili include questo pacchetto
                ]),
                'benefits'            => json_encode([
                    'Egili accreditati istantaneamente',
                    'Pagamento sicuro Stripe/PayPal',
                    'Fattura aggregata mensile',
                ]),
            ],
            [
                'feature_code'        => 'ai_package_professional',
                'feature_name'        => 'Professional Pack',
                'feature_description' => 'Pacchetto professionale per uso intensivo dei servizi AI.',
                'feature_category'    => 'ai_services',
                'cost_fiat_eur'       => null,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_bundle'           => true,
                'bundle_type'         => 'credit_package',
                'discount_percentage' => 0,         // ⚠️ DA DEFINIRE: eventuale sconto %
                'is_recurring'        => false,
                'recurrence_period'   => 'one_time',
                'expires'             => false,
                'stackable'           => true,
                'is_active'           => false,
                'is_featured'         => false,
                'display_order'       => 20,
                'feature_parameters'  => json_encode([
                    'egili_amount' => null,
                ]),
                'benefits'            => json_encode([
                    'Egili accreditati istantaneamente',
                    'Pagamento sicuro Stripe/PayPal',
                    'Fattura aggregata mensile',
                ]),
            ],
            [
                'feature_code'        => 'ai_package_business',
                'feature_name'        => 'Business Pack',
                'feature_description' => 'Pacchetto business per team e agenzie con utilizzo continuativo.',
                'feature_category'    => 'ai_services',
                'cost_fiat_eur'       => null,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_bundle'           => true,
                'bundle_type'         => 'credit_package',
                'discount_percentage' => 0,
                'is_recurring'        => false,
                'recurrence_period'   => 'one_time',
                'expires'             => false,
                'stackable'           => true,
                'is_active'           => false,
                'is_featured'         => false,
                'display_order'       => 30,
                'feature_parameters'  => json_encode([
                    'egili_amount' => null,
                ]),
                'benefits'            => json_encode([
                    'Egili accreditati istantaneamente',
                    'Pagamento sicuro Stripe/PayPal',
                    'Fattura aggregata mensile',
                ]),
            ],
            [
                'feature_code'        => 'ai_package_enterprise',
                'feature_name'        => 'Enterprise Pack',
                'feature_description' => 'Pacchetto enterprise per grandi organizzazioni e Pubbliche Amministrazioni.',
                'feature_category'    => 'ai_services',
                'cost_fiat_eur'       => null,
                'cost_egili'          => null,
                'is_free'             => false,
                'is_bundle'           => true,
                'bundle_type'         => 'credit_package',
                'discount_percentage' => 0,
                'is_recurring'        => false,
                'recurrence_period'   => 'one_time',
                'expires'             => false,
                'stackable'           => true,
                'is_active'           => false,
                'is_featured'         => false,
                'display_order'       => 40,
                'feature_parameters'  => json_encode([
                    'egili_amount' => null,
                ]),
                'benefits'            => json_encode([
                    'Egili accreditati istantaneamente',
                    'Pagamento sicuro Stripe/PayPal',
                    'Fattura aggregata mensile',
                    'Account manager dedicato',
                ]),
            ],
        ];

        foreach ($packages as $package) {
            AiFeaturePricing::updateOrCreate(
                ['feature_code' => $package['feature_code']],
                $package
            );
        }
    }
}
