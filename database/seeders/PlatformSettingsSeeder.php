<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Platform Settings)
 * @date 2026-02-25
 * @purpose Popola platform_settings con i valori tecnici del sistema AI Credits.
 *          ATTENZIONE: i valori qui sono defaults tecnici, NON prezzi commerciali.
 *          I prezzi dei pacchetti sono nel seeder AiServicePackagesSeeder.
 */
class PlatformSettingsSeeder extends Seeder {
    public function run(): void {
        $settings = [
            // ── Exchange Rate ─────────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'usd_to_eur_rate',
                'value'       => '0.92',
                'value_type'  => 'decimal',
                'label'       => 'Tasso USD → EUR (fallback)',
                'description' => 'Tasso di cambio USD/EUR usato come fallback se la API (exchangerate.host) non risponde. Aggiornato dal comando ai-credits:update-exchange-rate.',
                'is_editable' => true,
            ],

            // ── Credits per EUR ───────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'credits_per_eur',
                'value'       => '100',
                'value_type'  => 'integer',
                'label'       => 'Egili per EUR',
                'description' => '1 EUR = N Egili. Default 100 (€0.01 = 1 Egili).',
                'is_editable' => true,
            ],

            // ── Egili Credit Ratio ────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'egili_credit_ratio',
                'value'       => '0.80',
                'value_type'  => 'decimal',
                'label'       => 'Ratio Egili accreditati',
                'description' => 'Percentuale Egili accreditati rispetto ai token AI acquistati. Default 0.80 = 80% (margine piattaforma 20%).',
                'is_editable' => true,
            ],

            // ── Claude Pricing (Anthropic) ─────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'claude_sonnet_input_price_usd',
                'value'       => '3.00',
                'value_type'  => 'decimal',
                'label'       => 'Claude Sonnet — prezzo input ($/1M token)',
                'description' => 'Prezzo USD per 1M token di input. Fonte: anthropic.com/pricing. NON modificare senza verificare il listino ufficiale.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'claude_sonnet_output_price_usd',
                'value'       => '15.00',
                'value_type'  => 'decimal',
                'label'       => 'Claude Sonnet — prezzo output ($/1M token)',
                'description' => 'Prezzo USD per 1M token di output. Fonte: anthropic.com/pricing. NON modificare senza verificare il listino ufficiale.',
                'is_editable' => true,
            ],

            // ── Free Tier ─────────────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'free_tier_initial_credits',
                'value'       => '1000',
                'value_type'  => 'integer',
                'label'       => 'Free tier — Egili iniziali',
                'description' => 'Egili accreditati al nuovo utente alla registrazione.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'free_tier_monthly_reset_credits',
                'value'       => '500',
                'value_type'  => 'integer',
                'label'       => 'Free tier — Egili reset mensile',
                'description' => 'Egili aggiunti ogni mese agli utenti free (non cumulabili oltre il limite).',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'free_tier_max_analysis_size',
                'value'       => '1000',
                'value_type'  => 'integer',
                'label'       => 'Free tier — max atti per analisi',
                'description' => 'Numero massimo di atti analizzabili per singola analisi nel tier free.',
                'is_editable' => true,
            ],

            // ── Premium Tier ──────────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'premium_tier_monthly_credits',
                'value'       => '5000',
                'value_type'  => 'integer',
                'label'       => 'Premium tier — Egili mensili',
                'description' => 'Egili mensili inclusi nel tier premium.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'premium_tier_discount_percentage',
                'value'       => '10',
                'value_type'  => 'integer',
                'label'       => 'Premium tier — sconto acquisti (%)',
                'description' => 'Percentuale di sconto applicata agli acquisti di pacchetti per gli utenti premium.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'premium_tier_max_analysis_size',
                'value'       => '5000',
                'value_type'  => 'integer',
                'label'       => 'Premium tier — max atti per analisi',
                'description' => 'Numero massimo di atti analizzabili per singola analisi nel tier premium.',
                'is_editable' => true,
            ],

            // ── Enterprise Tier ───────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'enterprise_tier_discount_percentage',
                'value'       => '20',
                'value_type'  => 'integer',
                'label'       => 'Enterprise tier — sconto acquisti (%)',
                'description' => 'Percentuale di sconto per gli utenti enterprise.',
                'is_editable' => true,
            ],

            // ── Refund Policy ─────────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'auto_refund_on_job_failure',
                'value'       => 'true',
                'value_type'  => 'boolean',
                'label'       => 'Rimborso automatico su job fallito',
                'description' => 'Se true, rimborsa automaticamente gli Egili in caso di job AI fallito.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'refund_processing_fee_percentage',
                'value'       => '0',
                'value_type'  => 'integer',
                'label'       => 'Commissione rimborso (%)',
                'description' => 'Percentuale trattenuta sul rimborso. 0 = rimborso totale.',
                'is_editable' => true,
            ],

            // ── Alerts ────────────────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'alert_low_balance_credits',
                'value'       => '100',
                'value_type'  => 'integer',
                'label'       => 'Alert saldo basso (Egili)',
                'description' => 'Notifica utente quando il saldo scende sotto questa soglia.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'alert_admin_high_spending_daily',
                'value'       => '10000',
                'value_type'  => 'integer',
                'label'       => 'Alert admin spesa giornaliera (Egili)',
                'description' => 'Notifica admin se un utente spende più di N Egili in un giorno.',
                'is_editable' => true,
            ],

            // ── Exchange Rate API ─────────────────────────────────────────────
            [
                'group'       => 'ai_credits',
                'key'         => 'exchange_rate_cache_ttl_hours',
                'value'       => '24',
                'value_type'  => 'integer',
                'label'       => 'Cache tasso cambio (ore)',
                'description' => 'Ore di validità della cache per il tasso di cambio USD/EUR.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'exchange_rate_sanity_min',
                'value'       => '0.70',
                'value_type'  => 'decimal',
                'label'       => 'Sanity check tasso min',
                'description' => 'Tasso USD/EUR minimo accettabile. Sotto questa soglia il valore API viene scartato.',
                'is_editable' => true,
            ],
            [
                'group'       => 'ai_credits',
                'key'         => 'exchange_rate_sanity_max',
                'value'       => '1.30',
                'value_type'  => 'decimal',
                'label'       => 'Sanity check tasso max',
                'description' => 'Tasso USD/EUR massimo accettabile. Sopra questa soglia il valore API viene scartato.',
                'is_editable' => true,
            ],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }

        PlatformSetting::invalidateCache();
    }
}
