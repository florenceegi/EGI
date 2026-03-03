<?php

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Token Recharge Packages)
 * @date 2026-02-27
 * @purpose Crea i 4 pacchetti di ricarica AI Token configurabili dall'admin
 *
 * MODELLO ECONOMICO:
 *   Cliente acquista FIAT → accreditati ai_tokens_included Egili (rapporto 1:1)
 *   Consumo query AI: token_consumati × 1.20 scalati dal saldo Egili
 *   Margine piattaforma: 20% applicato solo al consumo
 *
 * IMPORTANTE: cost_fiat_eur = NULL → prezzi da impostare via admin EGI-HUB.
 *             I pacchetti vengono creati con struttura, non con prezzi fissi.
 */

namespace Database\Seeders;

use App\Models\AiFeaturePricing;
use Illuminate\Database\Seeder;

class AiTokenPackageSeeder extends Seeder {
    public function run(): void {
        $packages = [
            [
                'feature_code'              => 'ai_token_pack_starter',
                'feature_name'              => 'Pacchetto AI Starter',
                'feature_description'       => 'Pacchetto di ricarica AI Token per utenti occasionali. I token vengono accreditati 1:1 come Egili e scalati ad ogni utilizzo AI.',
                'feature_category'          => 'ai_services',
                'cost_fiat_eur'             => null,      // Da impostare via admin EGI-HUB
                'cost_egili'                => null,
                'ai_tokens_included'        => null,      // Da impostare via admin EGI-HUB
                'ai_tokens_bonus_percentage' => 0,
                'is_bundle'                 => true,
                'bundle_type'               => 'credit_package',
                'is_recurring'              => false,
                'recurrence_period'         => 'one_time',
                'is_active'                 => false,     // Inattivo finché admin non configura prezzi
                'display_order'             => 10,
                'is_featured'               => false,
                'badge_color'               => '#6B7280',
                'admin_notes'              => 'Tier entry-level. Impostare: cost_fiat_eur, ai_tokens_included. Attivare dopo configurazione.',
                'benefits'                  => [
                    'Accesso a tutti i servizi AI della piattaforma',
                    'Token accreditati 1:1 come Egili',
                    'Saldo non scade',
                ],
            ],
            [
                'feature_code'              => 'ai_token_pack_professional',
                'feature_name'              => 'Pacchetto AI Professional',
                'feature_description'       => 'Pacchetto di ricarica AI Token per utenti professionali con volume maggiore e bonus token.',
                'feature_category'          => 'ai_services',
                'cost_fiat_eur'             => null,
                'cost_egili'                => null,
                'ai_tokens_included'        => null,
                'ai_tokens_bonus_percentage' => 0,         // Es. 10 = +10% token bonus
                'is_bundle'                 => true,
                'bundle_type'               => 'credit_package',
                'is_recurring'              => false,
                'recurrence_period'         => 'one_time',
                'is_active'                 => false,
                'display_order'             => 20,
                'is_featured'               => true,
                'badge_color'               => '#3B82F6',
                'admin_notes'              => 'Tier mid-range. Impostare: cost_fiat_eur, ai_tokens_included, ai_tokens_bonus_percentage. Attivare dopo configurazione.',
                'benefits'                  => [
                    'Accesso a tutti i servizi AI della piattaforma',
                    'Token accreditati 1:1 come Egili',
                    'Bonus token su volume',
                    'Saldo non scade',
                ],
            ],
            [
                'feature_code'              => 'ai_token_pack_business',
                'feature_name'              => 'Pacchetto AI Business',
                'feature_description'       => 'Pacchetto di ricarica AI Token per aziende e creator ad alto volume con bonus significativi.',
                'feature_category'          => 'ai_services',
                'cost_fiat_eur'             => null,
                'cost_egili'                => null,
                'ai_tokens_included'        => null,
                'ai_tokens_bonus_percentage' => 0,
                'is_bundle'                 => true,
                'bundle_type'               => 'credit_package',
                'is_recurring'              => false,
                'recurrence_period'         => 'one_time',
                'is_active'                 => false,
                'display_order'             => 30,
                'is_featured'               => false,
                'badge_color'               => '#8B5CF6',
                'admin_notes'              => 'Tier business. Impostare: cost_fiat_eur, ai_tokens_included, ai_tokens_bonus_percentage. Attivare dopo configurazione.',
                'benefits'                  => [
                    'Accesso prioritario a tutti i servizi AI',
                    'Token accreditati 1:1 come Egili',
                    'Bonus token su volume elevato',
                    'Saldo non scade',
                    'Supporto dedicato',
                ],
            ],
            [
                'feature_code'              => 'ai_token_pack_enterprise',
                'feature_name'              => 'Pacchetto AI Enterprise',
                'feature_description'       => 'Pacchetto di ricarica AI Token per grandi volumi enterprise con massimo bonus e condizioni personalizzabili.',
                'feature_category'          => 'ai_services',
                'cost_fiat_eur'             => null,
                'cost_egili'                => null,
                'ai_tokens_included'        => null,
                'ai_tokens_bonus_percentage' => 0,
                'is_bundle'                 => true,
                'bundle_type'               => 'credit_package',
                'is_recurring'              => false,
                'recurrence_period'         => 'one_time',
                'is_active'                 => false,
                'display_order'             => 40,
                'is_featured'               => false,
                'badge_color'               => '#D97706',
                'admin_notes'              => 'Tier enterprise. Impostare: cost_fiat_eur, ai_tokens_included, ai_tokens_bonus_percentage. Attivare dopo configurazione. Valutare offerta personalizzata.',
                'benefits'                  => [
                    'Accesso prioritario a tutti i servizi AI',
                    'Token accreditati 1:1 come Egili',
                    'Massimo bonus token su volume',
                    'Saldo non scade',
                    'Account manager dedicato',
                    'SLA garantito',
                ],
            ],
        ];

        foreach ($packages as $package) {
            AiFeaturePricing::updateOrCreate(
                ['feature_code' => $package['feature_code']],
                $package
            );
        }

        $this->command->info('✅ AI Token Package records creati (4 pacchetti - prezzi da configurare via admin EGI-HUB)');
        $this->command->warn('⚠️  Tutti i pacchetti sono INATTIVI. Impostare prezzi e token via admin prima di attivare.');
    }
}
