<?php

/**
 * @Oracode Legal Metadata: Version 2.0.0
 * 🎯 Purpose: Centralized metadata for legal document version management
 * 🛡️ Security: Immutable version tracking with cryptographic integrity
 *
 * @package FlorenceEGI\Legal
 * @version 2.0.0 - Complete GDPR/AI/Blockchain Update
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'version' => '2.0.0',
    'release_date' => '2025-02-15',
    'effective_date' => '2025-02-15',
    'created_by' => 'legal@florenceegi.com',
    'approved_by' => 'fabio.cherici@florenceegi.com',

    'summary_of_changes' => 'Major update: AI integration (N.A.T.A.N., Claude, OpenAI), Algorand blockchain, AWS KMS custody, complete Terms of Service for all user types',

    'change_details' => [
        'major_additions' => [
            'N.A.T.A.N. AI assistant integration',
            'Claude/Anthropic AI processing disclosure',
            'OpenAI services integration',
            'Algorand blockchain tokenization',
            'AWS KMS custodial wallet service',
            'PeraWallet self-custody option',
            'Complete EPP (Environmental Protection Projects) framework'
        ],
        'new_user_type_terms' => [
            'collector.php - Complete collector terms with AI/blockchain',
            'patron.php - Patronage and governance terms',
            'epp.php - Environmental project organization terms',
            'company.php - Enterprise B2B terms',
            'trader_pro.php - Professional trading terms'
        ],
        'updated_documents' => [
            'creator.php - v2.0.0 with AI/blockchain sections (Articles 9-10)',
            'Privacy Policy IT/EN - AI processing, blockchain data, AWS KMS',
            'Cookie Policy IT/EN - Detailed cookie inventory and categories'
        ],
        'compliance_updates' => [
            'EU AI Act 2024 preparation',
            'MiCA (Markets in Crypto-Assets) Regulation alignment',
            'Enhanced GDPR Article 22 automated decision-making disclosure',
            'DAC7 fiscal reporting improvements'
        ]
    ],

    'available_user_types' => [
        'creator' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'high',
            'notes' => 'Updated with AI (Art. 9) and Blockchain (Art. 10) sections'
        ],
        'collector' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'high',
            'notes' => 'Complete collector terms - wallet options, EGI purchasing, Egili program'
        ],
        'patron' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'high',
            'notes' => 'Patronage models, benefits, governance rights'
        ],
        'epp' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'high',
            'notes' => 'Environmental Protection Projects - KYB, fund management, impact reporting'
        ],
        'company' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'medium',
            'notes' => 'Enterprise B2B terms - KYB, multi-user, SLA, white-label'
        ],
        'trader_pro' => [
            'status' => 'ready',
            'version' => '2.0.0',
            'priority' => 'medium',
            'notes' => 'Professional trading - enhanced KYC, limits, market conduct'
        ]
    ],

    'available_locales' => [
        'it' => [
            'status' => 'primary',
            'completion' => '100%',
            'notes' => 'All user types complete - source language'
        ],
        'en' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'es' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'pt' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'fr' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ],
        'de' => [
            'status' => 'pending_translation',
            'completion' => '0%',
            'notes' => 'AI translation + legal review required'
        ]
    ],

    'content_hashes' => [
        'it' => [
            'creator' => 'pending_calculation',
            'collector' => 'pending_calculation',
            'patron' => 'pending_calculation',
            'epp' => 'pending_calculation',
            'company' => 'pending_calculation',
            'trader_pro' => 'pending_calculation',
        ]
    ],

    'technology_disclosures' => [
        'ai_services' => [
            'N.A.T.A.N.' => 'Internal AI assistant powered by Claude (Anthropic)',
            'Claude' => 'Anthropic Claude for conversation and analysis',
            'OpenAI' => 'GPT models for content processing',
            'Processing' => 'EU-based where available, US with SCCs'
        ],
        'blockchain' => [
            'Network' => 'Algorand MainNet',
            'Token_Standard' => 'ARC-3, ARC-19, ARC-69',
            'Smart_Contracts' => 'PyTeal/Beaker',
            'Explorer' => 'algoexplorer.io'
        ],
        'custody' => [
            'Provider' => 'AWS Key Management Service',
            'Security' => 'HSM-backed, FIPS 140-2 Level 3',
            'Regions' => 'eu-west-1 (Ireland), eu-central-1 (Frankfurt)',
            'Insurance' => 'Covered under platform insurance policy'
        ]
    ],

    'legal_review' => [
        'reviewed_by' => 'legal@florenceegi.com',
        'review_date' => '2025-02-15',
        'review_status' => 'approved',
        'review_notes' => 'Complete v2.0.0 update with AI/blockchain integration. All user types now have comprehensive terms.',
        'next_review_due' => '2025-08-15',
        'ai_act_review' => 'Q2 2025'
    ],

    'technical_notes' => [
        'file_structure' => '/resources/legal/terms/versions/current/',
        'symlink_target' => '2.0.0',
        'integration_status' => 'ready',
        'consent_type_slug' => 'terms-of-service',
        'required_permissions' => ['legal.terms.edit', 'legal.terms.create_version'],
        'deployment_requirements' => [
            'Run FlorenceEgiPrivacyPolicySeeder for Privacy/Cookie updates',
            'Clear view cache: php artisan view:clear',
            'Clear config cache: php artisan config:clear',
            'Notify users of updated terms via email'
        ]
    ],

    'compliance_checklist' => [
        'gdpr_article_7' => true,   // Consent documentation
        'gdpr_article_13' => true,  // Information to be provided
        'gdpr_article_22' => true,  // Automated decision-making
        'dsa_2022_2065' => true,    // Digital Services Act
        'italian_privacy_code' => true, // D.Lgs. 196/2003
        'consumer_code' => true,    // D.Lgs. 206/2005
        'dac7_reporting' => true,   // Fiscal transparency
        'eu_ai_act' => 'in_progress', // Regulation 2024/1689
        'mica_regulation' => 'partial' // Markets in Crypto-Assets
    ],

    'version_history' => [
        '1.0.0' => [
            'date' => '2025-06-22',
            'summary' => 'Initial versioned system - Creator terms only'
        ],
        '2.0.0' => [
            'date' => '2025-02-15',
            'summary' => 'Complete update - All user types, AI/blockchain integration'
        ]
    ]
];
