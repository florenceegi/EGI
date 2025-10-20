<?php

/**
 * EGI Living Configuration
 * Configuration for EGI Vivente (SmartContract) features and AI triggers
 *
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 */

return [

    /*
    |--------------------------------------------------------------------------
    | AI Trigger Intervals (in seconds)
    |--------------------------------------------------------------------------
    |
    | Define how frequently the AI Curator should analyze EGI Viventi.
    | These values are used as defaults when creating SmartContracts.
    |
    */

    'ai_trigger_intervals' => [
        // Default: 24 hours (daily analysis)
        'default' => 86400,

        // Quick: 1 hour (for testing/premium)
        'quick' => 3600,

        // Standard: 24 hours (default production)
        'standard' => 86400,

        // Extended: 7 days (weekly analysis)
        'extended' => 604800,

        // Minimal: 30 days (monthly check)
        'minimal' => 2592000,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Features Configuration
    |--------------------------------------------------------------------------
    |
    | Define which AI features are available for EGI Viventi
    |
    */

    'ai_features' => [
        'curator' => [
            'enabled' => true,
            'name' => 'AI Curator',
            'description' => 'Analisi automatica e storytelling dell\'opera',
            'icon' => 'fa-brain',
        ],
        'promoter' => [
            'enabled' => true,
            'name' => 'AI Promoter',
            'description' => 'Promozione intelligente e marketing automatico',
            'icon' => 'fa-megaphone',
        ],
        'provenance' => [
            'enabled' => true,
            'name' => 'Provenance Graph',
            'description' => 'Tracciamento completo della storia dell\'opera',
            'icon' => 'fa-project-diagram',
        ],
        'passport' => [
            'enabled' => true,
            'name' => 'Passaporto Espositivo',
            'description' => 'Registro digitale delle esposizioni e eventi',
            'icon' => 'fa-passport',
        ],
        'anchoring' => [
            'enabled' => true,
            'name' => 'Anchoring Automatico',
            'description' => 'Ancoraggio giornaliero su blockchain',
            'icon' => 'fa-anchor',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define available subscription plans for EGI Vivente
    |
    */

    'subscription_plans' => [
        'one_time' => [
            'name' => 'Attivazione Singola',
            'price_eur' => 49.99,
            'duration_days' => null, // lifetime
            'features' => ['curator', 'promoter', 'provenance', 'passport', 'anchoring'],
            'trigger_interval' => 'standard',
        ],
        'monthly' => [
            'name' => 'Mensile',
            'price_eur' => 9.99,
            'duration_days' => 30,
            'features' => ['curator', 'promoter', 'provenance'],
            'trigger_interval' => 'standard',
        ],
        'yearly' => [
            'name' => 'Annuale',
            'price_eur' => 99.99,
            'duration_days' => 365,
            'features' => ['curator', 'promoter', 'provenance', 'passport', 'anchoring'],
            'trigger_interval' => 'quick',
        ],
        'lifetime' => [
            'name' => 'Lifetime Premium',
            'price_eur' => 299.99,
            'duration_days' => null, // lifetime
            'features' => ['curator', 'promoter', 'provenance', 'passport', 'anchoring'],
            'trigger_interval' => 'quick',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SmartContract Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Algorand SmartContract deployment
    |
    */

    'smart_contract' => [
        // Algorand network (testnet/mainnet)
        'network' => env('ALGORAND_NETWORK', 'testnet'),

        // Default gas/fee parameters
        'deployment' => [
            'min_balance' => 0.1, // ALGO required for SC deployment
            'max_retries' => 3,
            'retry_delay_seconds' => 5,
        ],

        // Global state schema
        'global_state_schema' => [
            'num_uints' => 8,  // creator, authorized_agent, next_trigger, interval, license_id, epp_id
            'num_byte_slices' => 10,  // metadata_hash, terms_hash, exhibit_refs, anchoring_root, audit_log
        ],

        // Local state schema (if needed)
        'local_state_schema' => [
            'num_uints' => 0,
            'num_byte_slices' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Oracolo Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Oracle service (AI ↔ Blockchain bridge)
    |
    */

    'oracle' => [
        // Oracolo wallet address (authorized_agent in SmartContracts)
        'wallet_address' => env('ORACLE_WALLET_ADDRESS'),

        // Polling configuration for SC events
        'polling' => [
            'enabled' => true,
            'interval_seconds' => 60, // Check every minute
            'batch_size' => 10, // Process max 10 SCs per batch
        ],

        // AI execution timeouts
        'ai_timeouts' => [
            'analysis_seconds' => 120,  // 2 minutes for AI analysis
            'update_state_seconds' => 30,  // 30 seconds for blockchain update
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-Mint Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Pre-Mint EGI (virtual AI-managed assets)
    |
    */

    'pre_mint' => [
        // Maximum duration an EGI can stay in pre-mint mode (days)
        'max_duration_days' => 90,

        // Auto-promotion to mint after N days
        'auto_promote_after_days' => 30,

        // AI analysis frequency for pre-mint EGIs
        'ai_trigger_interval' => 'quick', // More frequent for testing

        // Notification when pre-mint expires
        'expiration_notification_days' => [7, 3, 1], // Notify 7, 3, and 1 day before
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits and Quotas
    |--------------------------------------------------------------------------
    |
    | Rate limits and quotas for EGI Vivente features
    |
    */

    'limits' => [
        // Max EGI Viventi per user (0 = unlimited)
        'max_living_egis_per_user' => 0,

        // Max AI executions per day per EGI
        'max_ai_executions_per_day' => 10,

        // Max SC state updates per hour
        'max_state_updates_per_hour' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable specific features for gradual rollout
    |
    */

    'feature_flags' => [
        // Enable SmartContract minting
        'smart_contract_mint_enabled' => env('FEATURE_SC_MINT', false),

        // Enable Pre-Mint system
        'pre_mint_enabled' => env('FEATURE_PRE_MINT', false),

        // Enable AI Curator
        'ai_curator_enabled' => env('FEATURE_AI_CURATOR', false),

        // Enable Oracle service
        'oracle_enabled' => env('FEATURE_ORACLE', false),
    ],

];

