<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Key Management Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for envelope encryption and key management.
    | Supports development mode with mock KMS and production integration.
    |
    | KMS_ENVIRONMENT Controls which KMS to use:
    | - 'production' = Use cloud KMS (AWS, Azure, GCP, Vault)
    | - 'local'/'development' = Use mock KMS (local encryption)
    |
    | This is separate from APP_ENV to allow testing with real KMS
    | in local environment (APP_ENV=local + KMS_ENVIRONMENT=production)
    |
    */

    /**
     * Production KMS provider
     * Supported: aws, azure, vault, gcp
     */
    'provider' => env('KMS_PROVIDER', 'aws'),

    /**
     * Key Encryption Key (KEK) identifier
     * This is the master key ID used to encrypt Data Encryption Keys (DEKs)
     */
    'kek_id' => env('KMS_KEK_ID', 'egi-wallet-master-key'),

    /**
     * Development mode mock KEK (base64 encoded)
     * Generated automatically if not set
     * IMPORTANT: This is for development only - never use in production
     */
    'mock_kek' => env('KMS_MOCK_KEK', null),

    /**
     * AWS KMS Configuration
     */
    'aws' => [
        'region' => env('AWS_DEFAULT_REGION', 'eu-west-1'),
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'token' => env('AWS_SESSION_TOKEN'),
        'kek_arn' => env('AWS_KMS_KEK_ARN'),
    ],

    /**
     * Azure Key Vault Configuration
     */
    'azure' => [
        'vault_url' => env('AZURE_KEY_VAULT_URL'),
        'tenant_id' => env('AZURE_TENANT_ID'),
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
    ],

    /**
     * HashiCorp Vault Configuration
     */
    'vault' => [
        'address' => env('VAULT_ADDR'),
        'token' => env('VAULT_TOKEN'),
        'mount' => env('VAULT_MOUNT', 'transit'),
        'key_name' => env('VAULT_KEY_NAME', 'egi-master-key'),
    ],

    /**
     * Google Cloud KMS Configuration
     */
    'gcp' => [
        'project_id' => env('GCP_PROJECT_ID'),
        'location_id' => env('GCP_LOCATION_ID', 'global'),
        'key_ring_id' => env('GCP_KEY_RING_ID'),
        'key_id' => env('GCP_KEY_ID'),
        'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS'),
    ],

    /**
     * Encryption algorithm settings
     */
    'encryption' => [
        'algorithm' => 'xchacha20poly1305',
        'dek_size' => 32, // 256-bit DEK
        'nonce_size' => 24, // XChaCha20-Poly1305 nonce size
    ],

    /**
     * Security settings
     */
    'security' => [
        'secure_memory_cleanup' => true, // Use sodium_memzero()
        'audit_key_operations' => true,  // Log all key operations
        'require_user_context' => false, // Require user for audit logging
    ],

    /**
     * Development settings
     */
    'development' => [
        'log_mock_warnings' => true,     // Warn about mock KMS usage
        'generate_mock_kek' => true,     // Auto-generate mock KEK
        'expose_debug_info' => true,     // Include debug info in responses
    ],
];
