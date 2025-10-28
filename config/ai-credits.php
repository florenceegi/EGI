<?php

/**
 * AI Credits Configuration
 *
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits System)
 * @date 2025-10-28
 * @purpose Configuration for AI credits pricing, exchange rates, and limits
 */

return [
    /*
    |--------------------------------------------------------------------------
    | USD to EUR Exchange Rate (Fallback)
    |--------------------------------------------------------------------------
    |
    | Fallback exchange rate if API is unavailable.
    | Updated periodically by reviewing https://www.ecb.europa.eu/stats/policy_and_exchange_rates/
    |
    | Priority:
    | 1. Cache (updated daily by scheduled task via exchangerate.host API)
    | 2. This config value
    | 3. Safe fallback 1.0 (no conversion, conservative pricing)
    |
    | Last updated: 2025-10-28
    | Source: European Central Bank
    |
    */
    'usd_to_eur_rate' => env('AI_CREDITS_USD_TO_EUR', 0.92),

    /*
    |--------------------------------------------------------------------------
    | Credits per EUR
    |--------------------------------------------------------------------------
    |
    | Conversion rate between EUR and internal credits.
    | 1 EUR = 100 credits (default)
    |
    | This means:
    | - €0.01 = 1 credit
    | - €1.00 = 100 credits
    | - €10.00 = 1000 credits
    |
    */
    'credits_per_eur' => 100,

    /*
    |--------------------------------------------------------------------------
    | Claude Sonnet 3.5 Pricing (USD per 1M tokens)
    |--------------------------------------------------------------------------
    |
    | Official Anthropic pricing as of 2025-10-28.
    | Source: https://www.anthropic.com/pricing
    |
    | DO NOT modify unless Anthropic changes pricing.
    |
    */
    'claude_sonnet_35_input_price' => 3.00,   // $3.00 per 1M input tokens
    'claude_sonnet_35_output_price' => 15.00, // $15.00 per 1M output tokens

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Update Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic exchange rate updates.
    |
    */
    'exchange_rate_api_url' => env('EXCHANGE_RATE_API_URL', 'https://api.exchangerate.host/latest'),
    'exchange_rate_cache_ttl_hours' => 24, // Cache for 24 hours
    'exchange_rate_sanity_check_min' => 0.70, // Min valid rate (70 cents per dollar)
    'exchange_rate_sanity_check_max' => 1.30, // Max valid rate (1.30 EUR per dollar)

    /*
    |--------------------------------------------------------------------------
    | Free Tier Limits
    |--------------------------------------------------------------------------
    |
    | Default credits and limits for free tier users.
    |
    */
    'free_tier_initial_credits' => env('AI_CREDITS_FREE_TIER_INITIAL', 1000), // 1000 credits = €10
    'free_tier_monthly_reset_credits' => env('AI_CREDITS_FREE_TIER_MONTHLY', 500), // 500 credits/month = €5
    'free_tier_max_analysis_size' => 1000, // Max 1000 acts per analysis for free tier

    /*
    |--------------------------------------------------------------------------
    | Premium Tier Limits
    |--------------------------------------------------------------------------
    |
    | Limits for premium subscribers.
    |
    */
    'premium_tier_monthly_credits' => 5000, // 5000 credits/month = €50
    'premium_tier_discount_percentage' => 10, // 10% discount on purchases
    'premium_tier_max_analysis_size' => 5000, // Max 5000 acts per analysis

    /*
    |--------------------------------------------------------------------------
    | Enterprise Tier Limits
    |--------------------------------------------------------------------------
    |
    | Limits for enterprise customers (PA).
    |
    */
    'enterprise_tier_unlimited' => true,
    'enterprise_tier_monthly_credits' => null, // Unlimited
    'enterprise_tier_discount_percentage' => 20, // 20% discount on purchases

    /*
    |--------------------------------------------------------------------------
    | Purchase Options
    |--------------------------------------------------------------------------
    |
    | Available credit packages for purchase.
    |
    */
    'purchase_packages' => [
        'starter' => [
            'credits' => 1000,
            'price_eur' => 10.00,
            'label' => 'Starter Pack',
        ],
        'professional' => [
            'credits' => 5000,
            'price_eur' => 45.00, // 10% discount
            'label' => 'Professional Pack',
        ],
        'business' => [
            'credits' => 10000,
            'price_eur' => 85.00, // 15% discount
            'label' => 'Business Pack',
        ],
        'enterprise' => [
            'credits' => 50000,
            'price_eur' => 400.00, // 20% discount
            'label' => 'Enterprise Pack',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Policy
    |--------------------------------------------------------------------------
    |
    | Automatic refund settings for failed jobs.
    |
    */
    'auto_refund_on_job_failure' => true,
    'refund_processing_fee_percentage' => 0, // No fee on refunds (full refund)
    'refund_admin_notification_threshold' => 1000, // Notify admin if refund > 1000 credits

    /*
    |--------------------------------------------------------------------------
    | Logging & Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for credits system logging and alerts.
    |
    */
    'log_all_transactions' => true,
    'alert_on_low_balance_credits' => 100, // Alert user when balance < 100 credits
    'alert_admin_on_high_spending_daily' => 10000, // Alert admin if user spends >10k credits/day
];
