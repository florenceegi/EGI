<?php

/**
 * @Oracode FlorenceEGI Algorand Blockchain Configuration
 * 🎯 Purpose: Configuration for EGI blockchain integration with Algorand
 * 🧱 Core Logic: Blockchain settings, microservice integration, MiCA-SAFE compliance
 * 🛡️ Security: Environment-based secrets, production-ready settings
 *
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Configuration for EGI Algorand blockchain integration
 */

return [

    // ========================================
    // EGI BLOCKCHAIN CONFIGURATION
    // ========================================

    'egi_marketplace' => [
        'name' => env('EGI_MARKETPLACE_NAME', 'Florence EGI Marketplace'),
        'description' => env('EGI_MARKETPLACE_DESCRIPTION', 'Digital Certificates for Ecological Renaissance'),
        'base_url' => env('APP_URL', 'https://florenceegi.it'),
        'certificate_path' => env('EGI_CERTIFICATE_PATH', '/certificates'),
    ],

    // ========================================
    // ALGOKIT MICROSERVICE CONFIGURATION
    // ========================================

    'algokit_microservice' => [
        'url' => env('ALGOKIT_MICROSERVICE_URL', 'http://localhost:3000'),
        'timeout' => env('ALGOKIT_MICROSERVICE_TIMEOUT', 30),
        'retries' => env('ALGOKIT_MICROSERVICE_RETRIES', 3),
        'retry_delay' => env('ALGOKIT_MICROSERVICE_RETRY_DELAY', 1000), // milliseconds
    ],

    // ========================================
    // APPLICATION CONFIGURATION
    // ========================================

    'app' => [
        'https_port' => env('APP_PORT', 8443),
        'base_url' => env('APP_URL', 'https://localhost:8443'),
        'force_https' => env('APP_FORCE_HTTPS', true),
    ],

    // ========================================
    // ALGORAND NETWORK CONFIGURATION
    // ========================================

    'algorand' => [
        'network' => env('ALGORAND_NETWORK', 'sandbox'),

        // Generic API URLs (fallback for services that don't use microservice)
        'api_url' => env('ALGORAND_API_URL', 'https://testnet-api.algonode.cloud'),
        'indexer_url' => env('ALGORAND_INDEXER_URL', 'https://testnet-idx.algonode.cloud'),
        'api_key' => env('ALGORAND_API_KEY', ''),

        // Network configurations (used by microservice)
        'sandbox' => [
            'algod_url' => env('ALGOD_SANDBOX_URL', 'http://localhost:4001'),
            'indexer_url' => env('INDEXER_SANDBOX_URL', 'http://localhost:8980'),
            'explorer_url' => env('EXPLORER_SANDBOX_URL', 'https://testnet.explorer.perawallet.app'),
        ],

        'testnet' => [
            'algod_url' => env('ALGOD_TESTNET_URL', 'https://testnet-api.algonode.cloud'),
            'indexer_url' => env('INDEXER_TESTNET_URL', 'https://testnet-idx.algonode.cloud'),
            'explorer_url' => env('EXPLORER_TESTNET_URL', 'https://testnet.explorer.perawallet.app'),
        ],

        'mainnet' => [
            'algod_url' => env('ALGOD_MAINNET_URL', 'https://mainnet-api.algonode.cloud'),
            'indexer_url' => env('INDEXER_MAINNET_URL', 'https://mainnet-idx.algonode.cloud'),
            'explorer_url' => env('EXPLORER_MAINNET_URL', 'https://algoexplorer.io'),
        ],

        // Treasury configuration (handled by microservice)
        'treasury_address' => env('ALGORAND_TREASURY_ADDRESS', ''),
        'treasury_mnemonic' => env('ALGORAND_TREASURY_MNEMONIC', ''),

        // API settings
        'api_timeout' => env('ALGORAND_API_TIMEOUT', 30),
        'api_retries' => env('ALGORAND_API_RETRIES', 3),
        'api_retry_delay' => env('ALGORAND_API_RETRY_DELAY', 1000),
    ],

    // ========================================
    // ASA (ALGORAND STANDARD ASSET) CONFIGURATION
    // ========================================

    'asa_config' => [
        'total' => 1, // NFT standard
        'decimals' => 0, // NFT standard
        'default_frozen' => false,
        'unit_name' => 'EGI{id}', // Placeholder replaced with EGI ID
        'asset_name' => 'FlorenceEGI Certificate #{id}',
        'description' => 'Digital Certificate for Florence Ecological Renaissance',
        'metadata_template_url' => env('ASA_METADATA_URL', 'https://florenceegi.it/egis/{id}/metadata.json'),
        'image_url' => env('ASA_IMAGE_URL', 'https://florenceegi.it/images/egis/{id}.png'),

        // Metadata standards compliance
        'metadata_standard' => 'ARC-3', // Algorand NFT standard
        'external_url_template' => env('ASA_EXTERNAL_URL', 'https://florenceegi.it/egis/{id}'),

        // Certificate specific attributes
        'certificate_attributes' => [
            'issuer' => 'Florence EGI Foundation',
            'standard' => 'ISO 14001',
            'blockchain' => 'Algorand',
            'compliance' => 'MiCA-SAFE'
        ]
    ],

    // ========================================
    // MINTING CONFIGURATION
    // ========================================

    'minting' => [
        'batch_size' => env('ALGORAND_BATCH_SIZE', 10),
        'queue_name' => env('ALGORAND_QUEUE_NAME', 'algorand_minting'),
        'retry_attempts' => env('ALGORAND_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('ALGORAND_RETRY_DELAY', 60), // seconds

        // Status configurations
        'statuses' => [
            'unminted' => 'unminted',
            'minting_queued' => 'minting_queued',
            'minting' => 'minting',
            'minted' => 'minted',
            'failed' => 'failed',
            'transferred' => 'transferred'
        ]
    ],

    // ========================================
    // CERTIFICATE ANCHORING
    // ========================================

    'anchoring' => [
        'enabled' => env('ALGORAND_ANCHORING_ENABLED', true),
        'hash_algorithm' => env('ALGORAND_HASH_ALGORITHM', 'sha256'),
        'anchor_note_prefix' => env('ALGORAND_ANCHOR_PREFIX', 'EGI_CERT:'),
        'verification_url_template' => env('ALGORAND_VERIFICATION_URL', 'https://florenceegi.it/verify/{hash}')
    ],

    // ========================================
    // PAYMENT INTEGRATION
    // ========================================

    'payments' => [
        // MiCA-SAFE compliance: Only FIAT payments
        'supported_currencies' => ['EUR', 'USD'],
        'default_currency' => env('EGI_DEFAULT_CURRENCY', 'EUR'),

        // Mock payment settings (V1)
        'mock_mode' => env('EGI_MOCK_PAYMENTS', true),
        'mock_success_rate' => env('EGI_MOCK_SUCCESS_RATE', 0.95),
        'mock_processing_delay' => env('EGI_MOCK_DELAY', 2), // seconds

        // Real PSP settings (V2) 
        'stripe_enabled' => env('STRIPE_ENABLED', false),
        'paypal_enabled' => env('PAYPAL_ENABLED', false),
    ],

    // ========================================
    // SECURITY & COMPLIANCE
    // ========================================

    'security' => [
        // MiCA-SAFE compliance
        'wallet_custody' => false, // NO wallet custody for clients
        'kyc_required' => env('EGI_KYC_REQUIRED', false),
        'gdpr_compliance' => true,

        // Audit trail
        'audit_enabled' => env('EGI_AUDIT_ENABLED', true),
        'audit_retention_days' => env('EGI_AUDIT_RETENTION', 2555), // 7 years

        // Rate limiting
        'rate_limit_minting' => env('EGI_RATE_LIMIT_MINTING', 100), // per hour
        'rate_limit_transfers' => env('EGI_RATE_LIMIT_TRANSFERS', 50), // per hour
    ],

    // ========================================
    // DEVELOPMENT & TESTING
    // ========================================

    'development' => [
        'sandbox_enabled' => env('ALGORAND_SANDBOX_ENABLED', true),
        'test_wallet_enabled' => env('ALGORAND_TEST_WALLET', true),
        'debug_microservice' => env('ALGORAND_DEBUG_MICROSERVICE', false),
        'log_all_transactions' => env('ALGORAND_LOG_ALL_TX', true),
    ],

    // ========================================
    // PERFORMANCE & CACHING
    // ========================================

    'performance' => [
        'cache_ttl' => env('ALGORAND_CACHE_TTL', 300), // 5 minutes
        'cache_prefix' => 'algorand:',
        'parallel_minting' => env('ALGORAND_PARALLEL_MINTING', true),
        'max_concurrent_mints' => env('ALGORAND_MAX_CONCURRENT', 5),
    ]

];
