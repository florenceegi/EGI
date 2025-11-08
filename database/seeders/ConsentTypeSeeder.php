<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsentType;
use App\Models\ConsentVersion;
use Illuminate\Support\Facades\DB;

class ConsentTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     * Popola la tabella dei tipi di consenso con la configurazione di base.
     * Il testo (nome/descrizione) viene gestito tramite i file di traduzione,
     * usando lo 'slug' come chiave.
     */
    public function run(): void {
        DB::transaction(function () {

            $items = [
                // CONSENSO FONDAMENTALE PER PROCESSING DATI PERSONALI
                [
                    'slug' => 'allow-personal-data-processing',
                    'legal_basis' => 'contract',
                    'data_categories' => [
                        'personal_information',
                        'contact_data',
                        'usage_data'
                    ],
                    'processing_purposes' => [
                        'platform_operation',
                        'service_delivery',
                        'account_management',
                        'legal_compliance'
                    ],
                    'recipients' => [
                        'internal_staff',
                        'service_providers'
                    ],
                    'international_transfers' => false,
                    'is_required' => true,
                    'is_granular' => false,
                    'can_withdraw' => true,
                    'withdrawal_effect_days' => 30,
                    'retention_period' => 'contract_duration',
                    'deletion_method' => 'hard_delete',
                    'priority_order' => 1,
                    'is_active' => true,
                    'requires_double_opt_in' => false,
                    'requires_age_verification' => false,
                ],

                // ACCETTAZIONI E CONSENSI OBBLIGATORI
                [
                    'slug' => 'platform-services',
                    'legal_basis' => 'contract',
                    'data_categories' => [
                        'account_data',
                        'usage_data',
                        'technical_data'
                    ],
                    'processing_purposes' => [
                        'account_management',
                        'service_delivery',
                        'legal_compliance',
                        'customer_support'
                    ],
                    'recipients' => [
                        'internal_staff',
                        'service_providers'
                    ],
                    'is_required' => true,
                    'is_active' => true,
                    'priority_order' => 2,
                ],
                [
                    'slug' => 'terms-of-service',
                    'legal_basis' => 'contract',
                    'data_categories' => [
                        'legal_acceptance_data'
                    ],
                    'processing_purposes' => ['legal_compliance'],
                    'recipients' => [
                        'internal_staff',
                        'legal_team'
                    ],
                    'is_active' => true,
                    'is_granular' => false,
                    'can_withdraw' => false,
                    'is_required' => true,
                    'priority_order' => 10,
                ],
                [
                    'slug' => 'privacy-policy',
                    'legal_basis' => 'legal_obligation',
                    'data_categories' => [
                        'legal_acceptance_data'
                    ],
                    'processing_purposes' => ['legal_compliance'],
                    'recipients' => [
                        'internal_staff',
                        'legal_team'
                    ],
                    'is_required' => true,
                    'priority_order' => 20,
                ],
                [
                    'slug' => 'age-confirmation',
                    'legal_basis' => 'contract',
                    'data_categories' => [
                        'age_verification_data'
                    ],
                    'processing_purposes' => ['legal_compliance'],
                    'recipients' => [
                        'internal_staff'
                    ],
                    'is_required' => true,
                    'priority_order' => 30,
                ],

                // CONSENSI GDPR OPZIONALI
                [
                    'slug' => 'analytics',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'usage_data',
                        'behavioral_data',
                        'technical_data'
                    ],
                    'processing_purposes' => ['analytics', 'performance_monitoring'],
                    'recipients' => [
                        'internal_staff',
                        'analytics_providers'
                    ],
                    'is_required' => false,
                    'priority_order' => 40,
                ],
                [
                    'slug' => 'marketing',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'contact_data',
                        'preference_data',
                        'behavioral_data'
                    ],
                    'processing_purposes' => ['marketing', 'promotional_communications'],
                    'recipients' => [
                        'internal_staff',
                        'marketing_providers'
                    ],
                    'is_required' => false,
                    'priority_order' => 50,
                ],
                [
                    'slug' => 'personalization',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'preference_data',
                        'behavioral_data',
                        'usage_data'
                    ],
                    'processing_purposes' => ['personalization', 'user_experience'],
                    'recipients' => [
                        'internal_staff'
                    ],
                    'is_required' => false,
                    'priority_order' => 60,
                ],

                // COOKIE CONSENT TYPES - Nuovi per sistema cookie GDPR
                [
                    'slug' => 'essential',
                    'legal_basis' => 'legitimate_interest',
                    'data_categories' => [
                        'session_data',
                        'security_data',
                        'technical_data'
                    ],
                    'processing_purposes' => [
                        'website_functionality',
                        'security',
                        'session_management'
                    ],
                    'recipients' => ['internal_staff'],
                    'is_required' => true,
                    'priority_order' => 5,
                    'is_active' => true,
                ],
                [
                    'slug' => 'functional',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'preference_data',
                        'functionality_data'
                    ],
                    'processing_purposes' => [
                        'enhanced_functionality',
                        'user_preferences'
                    ],
                    'recipients' => ['internal_staff'],
                    'is_required' => false,
                    'priority_order' => 15,
                    'is_active' => true,
                ],
                [
                    'slug' => 'profiling',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'behavioral_data',
                        'preference_data',
                        'usage_patterns'
                    ],
                    'processing_purposes' => [
                        'user_profiling',
                        'behavioral_analysis',
                        'content_personalization'
                    ],
                    'recipients' => [
                        'internal_staff',
                        'analytics_providers'
                    ],
                    'is_required' => false,
                    'priority_order' => 25,
                    'is_active' => true,
                ],

                // AI PROCESSING CONSENT (N.A.T.A.N. System)
                [
                    'slug' => 'allow-ai-processing',
                    'legal_basis' => 'consent',
                    'data_categories' => [
                        'query_data',
                        'document_analysis_data',
                        'ai_interaction_data'
                    ],
                    'processing_purposes' => [
                        'ai_assisted_analysis',
                        'intelligent_document_search',
                        'automated_consulting',
                        'strategic_recommendations'
                    ],
                    'recipients' => [
                        'internal_staff',
                        'ai_service_providers'
                    ],
                    'international_transfers' => true,
                    'is_required' => false,
                    'is_granular' => true,
                    'can_withdraw' => true,
                    'withdrawal_effect_days' => 0,
                    'retention_period' => '90_days',
                    'deletion_method' => 'anonymization',
                    'priority_order' => 8,
                    'is_active' => true,
                    'requires_double_opt_in' => false,
                    'requires_age_verification' => false,
                ]
            ];

            foreach ($items as $item) {
                ConsentType::updateOrCreate(
                    ['slug' => $item['slug']], // Cerca per slug
                    $item  // E crea/aggiorna con gli altri dati
                );
            }

            // Create initial consent version (FIX for consent_version_id FK)
            $consentVersion = \App\Models\ConsentVersion::updateOrCreate(
                ['version' => '1.0'],
                [
                    'version' => '1.0',
                    'consent_types' => json_encode([
                        'allow-personal-data-processing',
                        'allow-marketing-communications',
                        'allow-analytics-cookies',
                        'allow-personalization',
                        'allow-newsletter'
                    ]),
                    'changes' => json_encode(['initial_version' => 'Initial consent framework setup']),
                    'configuration' => json_encode(['default_version' => true]),
                    'effective_date' => now(),
                    'is_active' => true,
                    'created_by' => null, // System created
                    'notes' => 'Initial consent version created by seeder for FK compatibility'
                ]
            );

            // Update config cache with actual consent_version_id
            if ($consentVersion) {
                \Illuminate\Support\Facades\Config::set('gdpr.default_consent_version_id', $consentVersion->id);
                // Note: In production, this should trigger config:cache refresh
            }
        });
    }
}