<?php

/**
 * AI Credits Configuration — STUB
 *
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - AI Credits System)
 * @date 2026-02-25
 * @purpose File mantenuto per retrocompatibilità del nome `ai-credits:update-exchange-rate`.
 *
 * ⚠️  TUTTI I VALORI SONO STATI SPOSTATI NEL DATABASE.
 *     Tabelle:
 *       - platform_settings  (group='ai_credits') — exchange rate, tier limits, egili_credit_ratio, ecc.
 *       - ai_feature_pricing (bundle_type='credit_package') — pacchetti AI con prezzi e Egili inclusi.
 *
 *     Gestione via: Superadmin → Impostazioni Piattaforma / Prezzi Features
 *
 *     Seeder: php artisan db:seed --class=PlatformSettingsSeeder
 *             php artisan db:seed --class=AiServicePackagesSeeder
 */

return [];
