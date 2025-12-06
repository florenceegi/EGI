<?php

namespace Database\Seeders;

use App\Models\AiFeaturePricing;
use Illuminate\Database\Seeder;
use App\Helpers\DatabaseHelper;

/**
 * AI Feature Pricing Seeder V2 - REAL FEATURES ONLY
 *
 * Popola ai_feature_pricing con SOLO features effettivamente implementate
 * 
 * FEATURES REALI:
 * 1. EGI Living Subscription (lifetime - implementato da mesi)
 * 2. AI Trait Generation (consumable - AiTraitGenerationService)
 * 3. AI Collection Strategy (consumable - future AnthropicService consultation)
 * 4. Featured EGI 7 giorni (temporal + approval - FeaturedSchedulingService)
 * 5. Hyper Mode 7 giorni (temporal + approval - FeaturedSchedulingService)
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (Real Features Only)
 * @date 2025-11-03
 * @purpose Seed ONLY implemented features with correct feature_type logic
 */
class AiFeaturePricingSeederV2Real extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing features (including soft deleted)
        // Disable FK checks in database-agnostic way
        if (DatabaseHelper::isMysql()) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \DB::table('ai_feature_pricing')->truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            // PostgreSQL: use TRUNCATE ... CASCADE or DELETE
            \DB::table('ai_feature_pricing')->delete();
        }

        $features = [
            
            // ============================================
            // 1. EGI LIVING (LIFETIME - Già implementato)
            // ============================================
            [
                'feature_code' => 'egi_living_subscription',
                'feature_name' => 'EGI Living Subscription',
                'feature_description' => 'Abbonamento completo per gestione EGI con aggiornamenti automatici CoA e blockchain monitoring',
                'feature_category' => 'platform_services',
                
                // PRICING (placeholder - Fabio rivedrà)
                'cost_fiat_eur' => 9.90,
                'cost_egili' => 500,
                'is_free' => false,
                
                // FEATURE TYPE: LIFETIME
                'feature_type' => 'lifetime',
                'lifetime_cost' => 500, // Costo una-tantum per acquisto lifetime
                'cost_per_use' => null, // Non applicabile per lifetime
                'requires_admin_approval' => false,
                'max_concurrent_slots' => null,
                
                // METADATA
                'is_active' => true,
                'display_order' => 10,
                'is_featured' => true,
                'icon_name' => 'superadmin-egili-token',
                'badge_color' => '#D4A574', // Oro Fiorentino
                'benefits' => [
                    'Aggiornamenti automatici CoA',
                    'Blockchain monitoring',
                    'Notifiche cambio stato',
                    'Per sempre (acquisto una volta)'
                ],
            ],
            
            // ============================================
            // 2. AI TRAIT GENERATION (CONSUMABLE)
            // ============================================
            [
                'feature_code' => 'ai_trait_generation',
                'feature_name' => 'Generazione Traits AI',
                'feature_description' => 'Analisi immagine con AI Vision per generare 1-10 traits artistici con fuzzy matching sul vocabulary',
                'feature_category' => 'ai_services',
                
                // PRICING (placeholder)
                'cost_fiat_eur' => null, // Solo Egili per AI services
                'cost_egili' => 50,
                'is_free' => false,
                
                // FEATURE TYPE: CONSUMABLE
                'feature_type' => 'consumable',
                'cost_per_use' => 50, // 50 Egili per ogni generazione
                'lifetime_cost' => null, // Non applicabile per consumable
                'requires_admin_approval' => false,
                'max_concurrent_slots' => null,
                
                // METADATA
                'is_active' => true,
                'display_order' => 20,
                'is_featured' => true,
                'icon_name' => 'superadmin-ai-brain',
                'badge_color' => '#1B365D', // Blu Algoritmo
                'benefits' => [
                    'AI Vision analysis (Claude)',
                    '1-10 traits suggeriti',
                    'Fuzzy matching con vocabulary',
                    'Proposta + approvazione utente'
                ],
                'feature_parameters' => [
                    'min_traits' => 1,
                    'max_traits' => 10,
                    'uses_claude_vision' => true,
                    'uses_fuzzy_matching' => true,
                ],
            ],
            
            // ============================================
            // 3. AI DESCRIPTION GENERATION (CONSUMABLE)
            // ============================================
            [
                'feature_code' => 'ai_description_generation',
                'feature_name' => 'Generazione Descrizione AI',
                'feature_description' => 'Analisi immagine con AI per generare descrizione professionale dell\'opera con supporto linee guida utente',
                'feature_category' => 'ai_services',
                
                // PRICING
                'cost_fiat_eur' => null, // Solo Egili per AI services
                'cost_egili' => 30,
                'is_free' => false,
                
                // FEATURE TYPE: CONSUMABLE
                'feature_type' => 'consumable',
                'cost_per_use' => 30, // 30 Egili per ogni generazione
                'lifetime_cost' => null, // Non applicabile per consumable
                'requires_admin_approval' => false,
                'max_concurrent_slots' => null,
                
                // METADATA
                'is_active' => true,
                'display_order' => 25,
                'is_featured' => true,
                'icon_name' => 'superadmin-ai-brain',
                'badge_color' => '#8E44AD', // Viola Innovazione
                'benefits' => [
                    'AI analysis (N.A.T.A.N)',
                    'Descrizione professionale',
                    'Supporto linee guida custom',
                    'Generazione istantanea'
                ],
                'feature_parameters' => [
                    'supports_guidelines' => true,
                    'max_description_length' => 2000,
                    'uses_anthropic_claude' => true,
                ],
            ],
            
            // ============================================
            // 4. AI COLLECTION STRATEGY (CONSUMABLE)
            // ============================================
            [
                'feature_code' => 'ai_collection_strategy',
                'feature_name' => 'Consulenza Strategica Collection',
                'feature_description' => 'Analisi AI completa della collection con suggerimenti su descrizione, social, lancio e mantenimento',
                'feature_category' => 'ai_services',
                
                // PRICING (placeholder)
                'cost_fiat_eur' => null,
                'cost_egili' => 300,
                'is_free' => false,
                
                // FEATURE TYPE: CONSUMABLE
                'feature_type' => 'consumable',
                'cost_per_use' => 300, // 300 Egili per consulenza completa
                'lifetime_cost' => null,
                'requires_admin_approval' => false,
                'max_concurrent_slots' => null,
                
                // METADATA
                'is_active' => true,
                'display_order' => 35,
                'is_featured' => true,
                'icon_name' => 'superadmin-ai-statistics',
                'badge_color' => '#2D5016', // Verde Rinascita
                'benefits' => [
                    'Analisi AI di tutti gli EGI della collection',
                    'Strategia descrizione collection',
                    'Piano social media',
                    'Suggerimenti lancio e aggiornamenti',
                ],
                'feature_parameters' => [
                    'analyzes_all_collection_images' => true,
                    'generates_description' => true,
                    'generates_social_plan' => true,
                    'generates_launch_strategy' => true,
                ],
            ],
            
            // ============================================
            // 5. FEATURED EGI 7 GIORNI (TEMPORAL + APPROVAL)
            // ============================================
            [
                'feature_code' => 'featured_egi_7d',
                'feature_name' => 'Featured EGI - 7 giorni',
                'feature_description' => 'EGI in evidenza in homepage per 7 giorni (soggetto ad approvazione admin e disponibilità slot)',
                'feature_category' => 'premium_visibility',
                
                // PRICING (placeholder)
                'cost_fiat_eur' => null,
                'cost_egili' => 500,
                'is_free' => false,
                
                // FEATURE TYPE: TEMPORAL
                'feature_type' => 'temporal',
                'cost_per_use' => 500, // 500 Egili per 7 giorni
                'lifetime_cost' => null,
                'duration_hours' => 168, // 7 giorni
                'expires' => true,
                
                // ADMIN APPROVAL (Limited Slots)
                'requires_admin_approval' => true, // Admin decide slot scheduling
                'max_concurrent_slots' => 3, // Max 3 Featured EGI in homepage
                
                // METADATA
                'is_active' => true,
                'display_order' => 40,
                'is_featured' => false,
                'icon_name' => 'superadmin-dashboard',
                'badge_color' => '#D4A574', // Oro Fiorentino
                'benefits' => [
                    'Homepage spotlight 7 giorni',
                    'Visibilità premium',
                    'Scheduling admin garantito',
                    'Max 3 slot disponibili'
                ],
                'expected_roi_multiplier' => 3.00, // +200% views estimated
            ],
            
            // ============================================
            // 5. HYPER MODE 7 GIORNI (TEMPORAL + APPROVAL)
            // ============================================
            [
                'feature_code' => 'hyper_mode_7d',
                'feature_name' => 'Hyper Mode - 7 giorni',
                'feature_description' => 'Modalità Hyper con boost visibilità e badge speciale per 7 giorni (soggetto ad approvazione admin)',
                'feature_category' => 'premium_visibility',
                
                // PRICING (placeholder)
                'cost_fiat_eur' => null,
                'cost_egili' => 300,
                'is_free' => false,
                
                // FEATURE TYPE: TEMPORAL
                'feature_type' => 'temporal',
                'cost_per_use' => 300, // 300 Egili per 7 giorni Hyper
                'lifetime_cost' => null,
                'duration_hours' => 168, // 7 giorni
                'expires' => true,
                
                // ADMIN APPROVAL (Admin discretion)
                'requires_admin_approval' => true,
                'max_concurrent_slots' => null, // No hard limit, admin decides
                
                // METADATA
                'is_active' => true,
                'display_order' => 50,
                'is_featured' => false,
                'icon_name' => 'superadmin-egili-token',
                'badge_color' => '#C13120', // Rosso Urgenza
                'benefits' => [
                    'Badge Hyper Mode',
                    'Boost algoritmo +50%',
                    'Visibilità aumentata 7 giorni',
                    'Approvazione admin richiesta'
                ],
                'expected_roi_multiplier' => 1.50,
            ],
            
            // ============================================
            // 6. AI CHAT ASSISTANT (CONSUMABLE - Token-Based)
            // ============================================
            [
                'feature_code' => 'ai_chat_assistant',
                'feature_name' => 'Assistente AI - Art Advisor',
                'feature_description' => 'Chat illimitata con Art Advisor AI esperto in arte, NFT e strategie marketplace (costo basato su token Claude consumati)',
                'feature_category' => 'ai_services',
                
                // PRICING (token-based - fractional)
                'cost_fiat_eur' => null, // Solo Egili
                'cost_egili' => null, // Costo dinamico basato su token usage
                'is_free' => false,
                
                // FEATURE TYPE: CONSUMABLE (token-based)
                'feature_type' => 'consumable',
                'cost_per_use' => null, // Variable (calculated per token)
                'lifetime_cost' => null,
                'requires_admin_approval' => false,
                'max_concurrent_slots' => null,
                
                // TOKEN-BASED PRICING (stored in metadata)
                'feature_parameters' => [
                    'charging_model' => 'token_based',
                    'egili_per_million_tokens' => 1, // 1 Egili = 1M tokens
                    'batch_threshold_egili' => 10, // Charge when pending >= 10 Egili
                    'cost_per_token' => 0.000001, // 1 Egili / 1M tokens
                ],
                
                // METADATA
                'is_active' => true,
                'display_order' => 60,
                'is_featured' => true,
                'icon_name' => 'superadmin-ai-brain',
                'badge_color' => '#8E44AD', // Viola Innovazione
                'benefits' => [
                    'Chat illimitata con Art Advisor AI',
                    'Analisi opere, strategie marketing, pricing',
                    'Costo trasparente basato su token',
                    'Batch charging (paghi solo quando accumuli 10 Egili)',
                ],
                'admin_notes' => 'Token-based pricing: accumula debt frazionale, addebita batch quando >= 10 Egili. Remainder sempre preservato.',
            ],
            
        ];

        foreach ($features as $feature) {
            AiFeaturePricing::create($feature);
        }

        $this->command->info('✅ AI Feature Pricing seeded successfully (7 REAL features)');
        $this->command->info('📊 Features created:');
        $this->command->info('  1. egi_living_subscription (lifetime)');
        $this->command->info('  2. ai_trait_generation (consumable - per use)');
        $this->command->info('  3. ai_description_generation (consumable - per use) ⭐ NEW');
        $this->command->info('  4. ai_collection_strategy (consumable - per use)');
        $this->command->info('  5. featured_egi_7d (temporal + approval)');
        $this->command->info('  6. hyper_mode_7d (temporal + approval)');
        $this->command->info('  7. ai_chat_assistant (consumable - token-based)');
    }
}

