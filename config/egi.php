<?php

/**
 * 📜 Oracode Configuration File: EGI Module
 * Central configuration for the Ecological Goods Invent (EGI) functionalities.
 *
 * @package     Config
 * @version     1.0.0
 * @author      Fabio Cherici & Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     Proprietary // Or your application's license
 *
 * @purpose     Defines default values, system entity IDs, default wallet royalty structures,
 *              and storage configurations essential for EGI creation, management, and processing.
 *              Values are primarily read from environment variables (.env) with sensible fallbacks.
 *              This file acts as the single source of truth for EGI-specific settings within the application,
 *              intended to be used via Laravel's `config('egi.*')` helper by components like EgiUploadHandler.
 *
 * @context     Loaded by Laravel during application bootstrap. Relies on corresponding
 *              environment variables being set in the `.env` file.
 *
 * @signal      Provides configuration arrays accessed via `config('egi.key')`.
 *
 * @privacy     Contains potentially sensitive default royalty percentages and system user IDs.
 *              Ensure environment variables referenced (e.g., NATAN_ID, EPP_ID) are managed securely.
 *
 * @dependency  Laravel Framework (`env()` helper). Relies on `.env` file setup.
 */
return [

    /**
     * 🏷️ Default floor price for newly created EGI records in euros (€).
     * This value is used when creating an Egi record if no specific price is provided
     * in the upload request. Referenced in EgiUploadHandler.
     * @env EGI_DEFAULT_FLOOR_PRICE
     * @type float
     */
    'default_floor_price' => (float) env('EGI_DEFAULT_FLOOR_PRICE', 600.00),

    /*
    |--------------------------------------------------------------------------
    | Default Roles for Application Logic
    |--------------------------------------------------------------------------
    |
    | Define default role names used in specific application contexts.
    | These are typically role *names* (strings) as used by Spatie's API.
    | Ensure these roles exist in your 'roles' database table.
    |
    */
    'default_roles' => [

        /**
         * The default role name assigned to the user who creates a Collection.
         * Use the exact string name of the role defined in your roles table.
         */
        'collection_owner' => 'creator', // <-- Esempio: Usa 'creator' come ruolo owner di default
    ],

    'default_type' => 'image',

    /**
     * 🏷️ System User IDs for predefined platform roles (Natan, EPP).
     * These IDs MUST correspond to valid records in the `users` table.
     * Used for associating default wallets in new collections.
     * Referenced in EgiUploadHandler::createDefaultWalletsForCollection.
     */
    'default_ids' => [
        /**
         * User ID representing the 'Natan' entity (Frangette Platform).
         * @env NATAN_ID
         * @type int
         */
        'natan_user_id' => (int) env('NATAN_ID', 1), // Cast to int for safety

        /**
         * User ID representing the default Environmental Protection Program (EPP) entity.
         * @env EPP_ID
         * @type int
         */
        'epp_user_id'   => (int) env('EPP_ID', 2),     // Cast to int for safety

        /**
         * User ID representing the 'Frangette' entity (Ecosystem Association).
         * @env FRANGETTE_ID
         * @type int
         */
        'frangette_user_id' => (int) env('FRANGETTE_ID', 3),
    ],

    /**
     * 🏷️ Default Wallet configurations for new 'single_egi_default' collections.
     * Defines roles, royalty percentages (read from ENV), and anonymity.
     * The keys ('Creator', 'EPP', 'Natan') MUST match `platform_role` values in the `wallets` table.
     * Referenced in EgiUploadHandler::createDefaultWalletsForCollection.
     * @type array
     */
    'default_wallets' => [
        // --- Creator Wallet Configuration ---
        'Creator' => [
            /**
             * Default royalty percentage for the EGI creator on the first sale (Mint).
             * @env CREATOR_ROYALTY_MINT
             * @type float
             */
            'mint_royalty'   => (float) env('CREATOR_ROYALTY_MINT', 70.0),

            /**
             * Default royalty percentage for the EGI creator on secondary market sales (Rebind).
             * @env CREATOR_ROYALTY_REBIND
             * @type float
             */
            'rebind_royalty' => (float) env('CREATOR_ROYALTY_REBIND', 4.5),

            /**
             * Indicates if the creator's wallet entry should be marked as anonymous. (Typically false)
             * @type bool
             */
            'is_anonymous'   => false,
            // 'user_id' is determined dynamically based on the collection creator.
        ],

        // --- EPP Wallet Configuration ---
        'EPP' => [
            /**
             * Default royalty percentage for the EPP on the first sale (Mint).
             * Note: ENV key 'EPP_ROYALTY_MINT' used, assuming it replaces 'EPP_ROYALTY_BIND'.
             * @env EPP_ROYALTY_MINT
             * @type float
             */
            'mint_royalty'   => (float) env('EPP_ROYALTY_MINT', 20.0), // Corrected ENV key assumption

            /**
             * Default royalty percentage for the EPP on secondary market sales (Rebind).
             * @env EPP_ROYALTY_REBIND
             * @type float
             */
            'rebind_royalty' => (float) env('EPP_ROYALTY_REBIND', 0.8),

            /**
             * Indicates if the EPP wallet entry should be marked as anonymous. (Typically true)
             * @type bool
             */
            'is_anonymous'   => true,

            /**
             * Configuration key path to retrieve the EPP User ID from within this config file.
             * @type string
             */
            'user_id_config_key' => 'egi.default_ids.epp_user_id',
        ],

        // --- Natan (Platform) Wallet Configuration ---
        'Natan' => [ // Assumes Natan represents the platform share (using FRANGETTE keys from ENV)
            /**
             * Default royalty percentage for the Platform (Natan) on the first sale (Mint).
             * @env NATAN_ROYALTY_MINT
             * @type float
             */
            'mint_royalty'   => (float) env('NATAN_ROYALTY_MINT', 10.0),

            /**
             * Default royalty percentage for the Platform (Natan) on secondary market sales (Rebind).
             * @env NATAN_ROYALTY_REBIND
             * @type float
             */
            'rebind_royalty' => (float) env('NATAN_ROYALTY_REBIND', 0.7),

            /**
             * Indicates if the Platform wallet entry should be marked as anonymous. (Confirm?)
             * @type bool
             */
            'is_anonymous'   => true, // Defaulting to true, adjust if needed

            /**
             * Configuration key path to retrieve the Natan User ID from within this config file.
             * @type string
             */
            'user_id_config_key' => 'egi.default_ids.natan_user_id',
        ],

        // --- Natan (Platform) Wallet Configuration ---
        'Ass_Frangette' => [ // Assumes Natan represents the platform share (using FRANGETTE keys from ENV)
            /**
             * Default royalty percentage for the Platform (Natan) on the first sale (Mint).
             * @env FRANGETTE_ROYALTY_MINT
             * @type float
             */
            'mint_royalty'   => (float) env('FRANGETTE_ROYALTY_MINT', 1.0),

            /**
             * Default royalty percentage for the Platform (Natan) on secondary market sales (Rebind).
             * @env FRANGETTE_ROYALTY_REBIND
             * @type float
             */
            'rebind_royalty' => (float) env('FRANGETTE_ROYALTY_REBIND', 0.1),

            /**
             * Indicates if the Platform wallet entry should be marked as anonymous. (Confirm?)
             * @type bool
             */
            'is_anonymous'   => true, // Defaulting to true, adjust if needed

            /**
             * Configuration key path to retrieve the Natan User ID from within this config file.
             * @type string
             */
            'user_id_config_key' => 'egi.default_ids.frangette_user_id',
        ],
    ],

    /**
     * 🏷️ Configuration for EGI file storage.
     * Defines the target filesystem disks and identifies which are critical.
     * Referenced in EgiUploadHandler::saveToMultipleDisks.
     */
    'storage' => [
        /**
         * Array of filesystem disk names (must be defined in `config/filesystems.php`).
         * EGI files will be saved to *all* disks listed here.
         * @env EGI_STORAGE_DISKS (comma-separated string, e.g., "do,local_backup")
         * @type array
         */
        'disks' => explode(',', env('EGI_STORAGE_DISKS', 's3')),

        /**
         * Array of disk names from the 'disks' list that are considered critical.
         * Failure to save to any critical disk will cause the entire upload to fail and rollback.
         * @env EGI_CRITICAL_DISKS (comma-separated string, e.g., "s3")
         * @type array
         */
        'critical_disks' => explode(',', env('EGI_CRITICAL_DISKS', 's3')),

        /**
         * Visibility per disk. Per S3 con CloudFront, usare 'private' perché
         * CloudFront serve i file tramite Origin Access Control.
         * @type array
         */
        'visibility' => [
            's3' => env('EGI_S3_VISIBILITY', 'private'),
            'public' => 'public',
            'local' => 'public',
            'do' => 'public',
        ],
    ],

    /**
     * 🏷️ Configuration related to EGI position generation within a collection.
     * Referenced in EgiHelper::generatePositionNumber.
     */
    'position' => [
        /**
         * Enable pessimistic locking (`SELECT ... FOR UPDATE`) when generating the next position number.
         * Helps prevent race conditions during concurrent uploads to the same collection. Recommended `true`.
         * @env EGI_POSITION_USE_LOCKING
         * @type bool
         */
        'use_locking' => env('EGI_POSITION_USE_LOCKING', true),
    ],

    /**
     * 🏷️ Platform Fee Configuration.
     * Centralized settings for splits and commissions.
     */
    'fees' => [
        /**
         * Percentage of the Net Margin taken by the Platform on Commodity Sales.
         * Default: 10% (0.10)
         * @env PLATFORM_MARGIN_PERCENTAGE
         */
        'platform_margin_percentage' => (float) env('PLATFORM_MARGIN_PERCENTAGE', 10.0),

        /**
         * Additional Platform Fee taken from Gross Amount (Transaction Fee).
         * Default: 0.5% (0.5)
         * @env PLATFORM_FEE_PERCENTAGE
         */
        'platform_fee_percentage' => (float) env('PLATFORM_FEE_PERCENTAGE', 0.5),
    ],

    /**
     * 💳 Payment System Name (PSP abstraction layer)
     * Used in UI copy, wizard messages and chip labels.
     * Override via EGI_PSP_NAME to change the display name without code changes.
     * @env EGI_PSP_NAME
     */
    'payment' => [
        'psp_name' => env('EGI_PSP_NAME', 'FlorenceEGI Payment System'),
    ],

];
