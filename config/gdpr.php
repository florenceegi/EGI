<?php
// config/gdpr.php
// FlorenceEGI GDPR Configuration
return [
    /*
    |--------------------------------------------------------------------------
    | GDPR Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for the GDPR module.
    |
    */

    'current_policy_version' => '1.0.0',
    'policy_update_url' => '/gdpr/policy-update',

    /*
    |--------------------------------------------------------------------------
    | Default Consent Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for default consent values used during user registration
    | and consent management. These should NEVER be hardcoded in controllers!
    |
    */
    'default_consent_version' => '1.0',
    'default_consent_version_id' => 1, // Updated dynamically by seeder
    'fallback_consent_version_id' => null, // Used if default fails

    'activity_categories' => [
        'authentication' => [
            'name' => 'Authentication',
            'description' => 'Login, logout, password changes',
            'retention_period' => 365,
            'privacy_level' => 'standard'
        ],
        'admin_access' => [
            'name' => 'Administrative Access',
            'description' => 'Accesso al pannello amministrativo (superadmin/admin)',
            'retention_period' => 1095, // 3 anni
            'privacy_level' => 'high'
        ],
        'admin_action' => [
            'name' => 'Administrative Actions',
            'description' => 'Azioni e modifiche effettuate nel back-office (superadmin/admin)',
            'retention_period' => 1095, // 3 anni
            'privacy_level' => 'high'
        ],
        'gdpr_actions' => [
            'name' => 'GDPR Actions',
            'description' => 'Data requests, consent changes, deletions',
            'retention_period' => 2555, // 7 years
            'privacy_level' => 'high'
        ],
        'data_access' => [
            'name' => 'Data Access',
            'description' => 'Profile views, data exports, sensitive operations',
            'retention_period' => 1095, // 3 years
            'privacy_level' => 'high'
        ],
        'platform_usage' => [
            'name' => 'Platform Usage',
            'description' => 'Navigation, feature usage, interactions',
            'retention_period' => 730, // 2 years
            'privacy_level' => 'standard'
        ],
        'security_events' => [
            'name' => 'Security Events',
            'description' => 'Failed logins, suspicious activities, breaches',
            'retention_period' => 2555, // 7 years
            'privacy_level' => 'critical'
        ],
        'blockchain_activity' => [
            'name' => 'Blockchain Activity',
            'description' => 'NFT creation, wallet connections, transactions',
            'retention_period' => 2555, // 7 years (legal requirement)
            'privacy_level' => 'immutable'
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Consent Settings
    |--------------------------------------------------------------------------
    */
    'consent' => [
        // Default consent values for new users
        'defaults' => [
            'marketing' => false,
            'analytics' => false,
            'profiling' => false,
            'functional' => true,
            'essential' => true,
        ],

        // Whether consents can be updated via API
        'allow_api_updates' => true,

        // Whether to auto-save individual consent changes
        'auto_save' => true,

        // Categories that cannot be opted out of
        'required_categories' => [
            'essential',
        ],

        // Consent definitions
        'definitions' => [
            'essential' => [
                'label' => 'gdpr.consent.essential.label',
                'description' => 'gdpr.consent.essential.description',
                'required' => true,
            ],
            'functional' => [
                'label' => 'gdpr.consent.functional.label',
                'description' => 'gdpr.consent.functional.description',
                'required' => false,
            ],
            'analytics' => [
                'label' => 'gdpr.consent.analytics.label',
                'description' => 'gdpr.consent.analytics.description',
                'required' => false,
            ],
            'marketing' => [
                'label' => 'gdpr.consent.marketing.label',
                'description' => 'gdpr.consent.marketing.description',
                'required' => false,
            ],
            'profiling' => [
                'label' => 'gdpr.consent.profiling.label',
                'description' => 'gdpr.consent.profiling.description',
                'required' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Export Settings
    |--------------------------------------------------------------------------
    */
    'export' => [
        // Default export format
        'default_format' => 'json',

        // Available export formats
        'available_formats' => [
            'json' => [
                'extension' => 'json',
                'mime_type' => 'application/json',
            ],
            'csv' => [
                'extension' => 'csv',
                'mime_type' => 'text/csv',
            ],
            'pdf' => [
                'extension' => 'pdf',
                'mime_type' => 'application/pdf',
            ],
        ],

        // Maximum exports per user per day
        'max_exports_per_day' => 0, // 0 means no limit

        // Export timeout in minutes
        'timeout_minutes' => 30,

        // Whether to enable password protection option
        'enable_password_protection' => true,

        // Default inclusion settings
        'include_metadata' => true,
        'include_timestamps' => true,

        // Data categories available for export
        'data_categories' => [
            'profile' => 'gdpr.export.categories.profile',
            'account' => 'gdpr.export.categories.account',
            'preferences' => 'gdpr.export.categories.preferences',
            'activity' => 'gdpr.export.categories.activity',
            'consents' => 'gdpr.export.categories.consents',
            'collections' => 'gdpr.export.categories.collections',
            'purchases' => 'gdpr.export.categories.purchases',
            'comments' => 'gdpr.export.categories.comments',
            'messages' => 'gdpr.export.categories.messages',
            'biography' => 'gdpr.export.categories.biography',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Restriction Settings
    |--------------------------------------------------------------------------
    */
    'processing_restriction' => [
        // Maximum active restrictions per user
        'max_active_restrictions' => 5,

        // Auto-expiry time for restrictions in days (null = never expire)
        'auto_expiry_days' => null,

        // Whether to enable processing restriction notifications
        'enable_notifications' => true,

        // Data categories available for restriction
        'data_categories' => [
            'profile' => 'gdpr.restriction.categories.profile',
            'activity' => 'gdpr.restriction.categories.activity',
            'preferences' => 'gdpr.restriction.categories.preferences',
            'collections' => 'gdpr.restriction.categories.collections',
            'purchases' => 'gdpr.restriction.categories.purchases',
            'comments' => 'gdpr.restriction.categories.comments',
            'messages' => 'gdpr.restriction.categories.messages',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Restriction Settings
    |--------------------------------------------------------------------------
    */
    'processing_restriction' => [
        // Maximum active restrictions per user
        'max_active_restrictions' => 5,

        // Auto-expiry time for restrictions in days (null = never expire)
        'auto_expiry_days' => null, // Se vuoi un default, ad esempio 90 giorni, metti 90

        // Whether to enable processing restriction notifications
        'enable_notifications' => true,

        // Data categories available for restriction
        'data_categories' => [
            'profile' => 'gdpr.restriction.categories.profile',
            'activity' => 'gdpr.restriction.categories.activity',
            'preferences' => 'gdpr.restriction.categories.preferences',
            'collections' => 'gdpr.restriction.categories.collections',
            'purchases' => 'gdpr.restriction.categories.purchases',
            'comments' => 'gdpr.restriction.categories.comments',
            'messages' => 'gdpr.restriction.categories.messages',
        ],

        /*
        |----------------------------------------------------------------------
        | Processing Restriction Type Mapping
        |----------------------------------------------------------------------
        |
        | Maps specific ProcessingRestrictionType enum values to an array of
        | granular application-level processing activities (strings) that
        | should be restricted when that restriction type is active.
        |
        | The `ProcessingRestrictionType::ALL` (general)
        | is handled directly in the service and applies to all processing.
        |
        */
        'type_mapping' => [
            // Utilizzo dei case effettivi dell'Enum ProcessingRestrictionType
            \App\Enums\Gdpr\ProcessingRestrictionType::AUTOMATED_DECISIONS->value => [
                'automated_profile_scoring',
                'automated_credit_assessment',
                'algorithmic_content_ranking',
            ],
            \App\Enums\Gdpr\ProcessingRestrictionType::MARKETING->value => [
                'marketing_emails',
                'newsletter_subscriptions',
                'on_platform_targeted_ads',
                'sms_marketing_campaigns',
            ],
            \App\Enums\Gdpr\ProcessingRestrictionType::ANALYTICS->value => [
                'website_usage_tracking',
                'product_interaction_analysis',
                'user_behavior_monitoring',
                'heatmaps_and_session_recording',
            ],
            \App\Enums\Gdpr\ProcessingRestrictionType::THIRD_PARTY->value => [
                // Nota: Questo potrebbe sovrapporsi o essere più specifico di DATA_SHARING.
                // Valuta se mantenere entrambi o consolidare.
                'third_party_analytics_pixels',
                'external_ad_network_integration',
                // Se THIRD_PARTY implica anche una forma di condivisione, aggiungila qui
                // o assicurati che la logica di DATA_SHARING copra questi casi.
            ],
            \App\Enums\Gdpr\ProcessingRestrictionType::DATA_SHARING->value => [
                'data_sharing_with_partners', // Condivisione esplicita con partner
                'social_login_data_synchronization', // Sincronizzazione dati con login social
                // Aggiungere altri tipi specifici di data sharing
            ],
            // Nota: ProcessingRestrictionType::ALL è gestito come "blocca tutto" nel service
            // e non necessita di una mappatura qui, a meno di requisiti molto specifici.
            // Se ProcessingRestrictionType::PROFILING dovesse avere trattamenti specifici
            // non coperti da AUTOMATED_DECISIONS o MARKETING, andrebbe aggiunto qui.
            // Esempio se PROFILING fosse un tipo di restrizione a sé stante con trattamenti specifici:
            // \App\Enums\Gdpr\ProcessingRestrictionType::PROFILING->value => [
            //     'manual_user_segmentation_for_offers',
            //     'behavioral_analysis_for_content_personalization',
            // ],
        ],
        // ***** FINE SEZIONE CORRETTA *****
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Deletion Settings
    |--------------------------------------------------------------------------
    */
    'deletion' => [
        // Whether to use soft deletes or hard deletes
        'use_soft_delete' => true,

        // Delay before processing deletion request (in days)
        'processing_delay_days' => 7,

        // Whether to allow reactivation during the delay period
        'allow_reactivation' => true,

        // Whether to keep certain anonymized data
        'keep_anonymized_data' => true,

        // Whether to require password confirmation for deletion
        'require_password_confirmation' => true,

        // Whether to require reason for deletion
        'require_reason' => true,

        // Predefined deletion reasons
        'deletion_reasons' => [
            'no_longer_needed' => 'gdpr.deletion.reasons.no_longer_needed',
            'privacy_concerns' => 'gdpr.deletion.reasons.privacy_concerns',
            'moving_to_competitor' => 'gdpr.deletion.reasons.moving_to_competitor',
            'unhappy_with_service' => 'gdpr.deletion.reasons.unhappy_with_service',
            'other' => 'gdpr.deletion.reasons.other',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Breach Report Settings
    |--------------------------------------------------------------------------
    */
    'breach_report' => [
        // Whether to allow anonymous breach reports
        'allow_anonymous' => false,

        // File types allowed for evidence uploads
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'txt', 'doc', 'docx'],

        // Maximum file size for evidence uploads (in KB)
        'max_file_size_kb' => 10240, // 10MB

        // Email addresses to notify on new breach reports
        'notification_emails' => [
            'dpo@florenceegi.com',
            'security@florenceegi.com',
        ],

        // Whether to show a custom thank you message after submission
        'show_thank_you' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Settings
    |--------------------------------------------------------------------------
    */
    'activity_log' => [
        // Whether to enable GDPR activity logging
        'enabled' => true,

        // Log retention period in days
        'retention_days' => 365,

        // Activities to log
        'log_activities' => [
            'consent_updated' => true,
            'data_exported' => true,
            'processing_restricted' => true,
            'account_deletion_requested' => true,
            'account_deleted' => true,
            'breach_reported' => true,
        ],

        // Whether to include IP address in logs
        'log_ip_address' => true,

        // Whether to include user agent in logs
        'log_user_agent' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI/UX Settings
    |--------------------------------------------------------------------------
    */
    'ui' => [
        // Brand colors for GDPR UI (FlorenceEGI brand guidelines)
        'colors' => [
            'primary' => '#D4A574', // Oro Fiorentino
            'secondary' => '#2D5016', // Verde Rinascita
            'accent' => '#1B365D', // Blu Algoritmo
            'neutral' => '#6B6B6B', // Grigio Pietra
            'danger' => '#C13120', // Rosso Urgenza
            'warning' => '#E67E22', // Arancio Energia
            'success' => '#4ADE80', // Verde Successo
        ],

        // Whether to show breadcrumbs in GDPR pages
        'show_breadcrumbs' => true,

        // Whether to show help text throughout GDPR pages
        'show_help_text' => true,

        // Whether to show privacy policy links in context
        'show_privacy_policy_links' => true,

        // Whether to use glassmorphism design in UI components
        'use_glassmorphism' => true,

        // Path to privacy policy page
        'privacy_policy_path' => '/privacy-policy',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        // Whether to send email notifications for GDPR actions
        'send_emails' => true,

        // Whether to send Slack notifications for critical GDPR actions
        'send_slack' => true,

        // Slack webhook URL for notifications
        'slack_webhook_url' => env('GDPR_SLACK_WEBHOOK_URL', null),

        // Notification class to database mapping
        'classes' => [
            'consent_updated' => \App\Notifications\Gdpr\ConsentUpdatedNotification::class,
            'data_exported' => \App\Notifications\Gdpr\DataExportedNotification::class,
            'processing_restricted' => \App\Notifications\Gdpr\ProcessingRestrictedNotification::class,
            'account_deletion_requested' => \App\Notifications\Gdpr\AccountDeletionRequestedNotification::class,
            'account_deletion_processed' => \App\Notifications\Gdpr\AccountDeletionProcessedNotification::class,
            'breach_report_received' => \App\Notifications\Gdpr\BreachReportReceivedNotification::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Whether to rate limit GDPR requests
        'enable_rate_limiting' => true,

        // Rate limit attempts per minute
        'rate_limit_attempts' => 100,

        // Rate limit decay minutes
        'rate_limit_decay_minutes' => 1,

        // Whether to encrypt exports
        'encrypt_exports' => true,

        // Whether to encrypt breach reports
        'encrypt_breach_reports' => true,

        // Encryption cipher to use
        'encryption_cipher' => 'AES-256-CBC',
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Protection Officer (DPO) Settings - GDPR Art. 37-39
    |--------------------------------------------------------------------------
    |
    | Configuration for the Data Protection Officer contact and communication.
    | Even if not legally required, having a DPO contact improves trust and
    | demonstrates commitment to data protection.
    |
    */
    'dpo' => [
        // DPO name (can be a person or role title)
        'name' => env('GDPR_DPO_NAME', 'Data Protection Officer'),

        // DPO email address (required for GDPR compliance)
        'email' => env('GDPR_DPO_EMAIL', 'dpo@florenceegi.com'),

        // Optional: DPO phone number
        'phone' => env('GDPR_DPO_PHONE', null),

        // Optional: DPO physical address
        'address' => env('GDPR_DPO_ADDRESS', null),

        // Expected response time (GDPR requires response within 30 days max)
        'response_time' => '72 hours',

        // Maximum response time for complex requests
        'max_response_time' => '30 days',

        // Languages supported for DPO communication
        'supported_languages' => ['it', 'en'],

        // Office hours for DPO availability
        'office_hours' => 'Monday-Friday 9:00-17:00 CET',

        // Whether DPO is internal or external
        'is_external' => env('GDPR_DPO_IS_EXTERNAL', false),

        // External DPO company name (if applicable)
        'external_company' => env('GDPR_DPO_EXTERNAL_COMPANY', null),

        // Message priority levels
        'priority_levels' => [
            'low' => [
                'response_time' => '7 days',
                'label' => 'Low Priority',
            ],
            'normal' => [
                'response_time' => '72 hours',
                'label' => 'Normal Priority',
            ],
            'high' => [
                'response_time' => '48 hours',
                'label' => 'High Priority',
            ],
            'urgent' => [
                'response_time' => '24 hours',
                'label' => 'Urgent',
            ],
        ],

        // Request types that users can submit
        'request_types' => [
            'information' => 'Information Request',
            'complaint' => 'Complaint',
            'access_request' => 'Data Access Request',
            'rectification' => 'Data Rectification Request',
            'erasure' => 'Data Erasure Request (Right to be Forgotten)',
            'restriction' => 'Processing Restriction Request',
            'portability' => 'Data Portability Request',
            'objection' => 'Objection to Processing',
            'other' => 'Other',
        ],

        // Notification settings for DPO
        'notifications' => [
            'new_message' => true,
            'urgent_escalation' => true,
            'overdue_response' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy Contact Settings
    |--------------------------------------------------------------------------
    */
    'privacy_email' => env('GDPR_PRIVACY_EMAIL', 'privacy@florenceegi.com'),
];
