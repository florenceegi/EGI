<?php

/**
 * @Oracode Config: IPFS Integration Configuration
 * 🎯 Purpose: Configure IPFS pinning service (Pinata) for original EGI images
 * 🧱 Core Logic: Environment-based configuration for IPFS provider settings
 * 
 * @package FlorenceEGI\Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-03
 */

return [

    /*
    |--------------------------------------------------------------------------
    | IPFS Provider
    |--------------------------------------------------------------------------
    |
    | The IPFS pinning service provider to use. Currently supports:
    | - 'pinata': Pinata Cloud (https://pinata.cloud)
    | - 'disabled': Disable IPFS uploads (development mode)
    |
    */
    'provider' => env('IPFS_PROVIDER', 'pinata'),

    /*
    |--------------------------------------------------------------------------
    | Enable IPFS Uploads
    |--------------------------------------------------------------------------
    |
    | Master switch to enable/disable IPFS uploads. When disabled, EGI images
    | will only be stored locally. Useful for development environments.
    |
    */
    'enabled' => env('IPFS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Pinata Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Pinata IPFS pinning service.
    | Get your JWT from: https://app.pinata.cloud/developers/api-keys
    | Get your Gateway from: https://app.pinata.cloud/gateway
    |
    */
    'pinata' => [
        // JWT token for API authentication (Admin privileges recommended)
        'jwt' => env('PINATA_JWT', ''),
        
        // Your dedicated gateway domain (e.g., "your-gateway.mypinata.cloud")
        'gateway' => env('PINATA_GATEWAY', ''),
        
        // API endpoints
        'api_url' => env('PINATA_API_URL', 'https://api.pinata.cloud'),
        'upload_url' => env('PINATA_UPLOAD_URL', 'https://api.pinata.cloud'),
        
        // Default pin options
        'pin_options' => [
            // CIDv1 is more modern and recommended
            'cidVersion' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for IPFS gateway URL construction.
    |
    */
    'gateway' => [
        // Public IPFS gateway fallback (if dedicated gateway fails)
        'public_fallback' => env('IPFS_PUBLIC_GATEWAY', 'https://ipfs.io/ipfs/'),
        
        // Timeout for gateway requests (seconds)
        'timeout' => env('IPFS_GATEWAY_TIMEOUT', 30),
        
        // Use dedicated gateway (true) or public gateway (false)
        'use_dedicated' => env('IPFS_USE_DEDICATED_GATEWAY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    |
    | Settings for IPFS uploads.
    |
    */
    'upload' => [
        // Maximum file size in MB for IPFS uploads (0 = no limit)
        'max_size_mb' => env('IPFS_MAX_SIZE_MB', 50),
        
        // Allowed MIME types for IPFS upload
        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/heic',
            'image/heif',
        ],
        
        // Upload timeout in seconds
        'timeout' => env('IPFS_UPLOAD_TIMEOUT', 120),
        
        // Retry attempts on failure
        'retry_attempts' => env('IPFS_RETRY_ATTEMPTS', 3),
        
        // Delay between retries in seconds
        'retry_delay' => env('IPFS_RETRY_DELAY', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Metadata Settings
    |--------------------------------------------------------------------------
    |
    | Metadata to attach to pinned files.
    |
    */
    'metadata' => [
        // Prefix for pin names (e.g., "florenceegi-egi-123")
        'name_prefix' => env('IPFS_NAME_PREFIX', 'florenceegi'),
        
        // Include EGI ID in metadata
        'include_egi_id' => true,
        
        // Include collection ID in metadata
        'include_collection_id' => true,
        
        // Include creator wallet in metadata
        'include_creator' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Logging configuration for IPFS operations.
    |
    */
    'logging' => [
        // Log channel for IPFS operations
        'channel' => env('IPFS_LOG_CHANNEL', 'egi_upload'),
        
        // Log successful uploads
        'log_success' => true,
        
        // Log failed uploads
        'log_failures' => true,
    ],

];
