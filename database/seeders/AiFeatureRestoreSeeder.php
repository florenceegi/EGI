<?php

namespace Database\Seeders;

use App\Models\AiFeaturePricing;
use Illuminate\Database\Seeder;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - AI Feature Pricing Restore)
 * @date 2026-03-03
 * @purpose Ripristina i 7 record AI cancellati accidentalmente dal truncate()
 *          in AiFeaturePricingSeederV2Real (commit 38ec67a5, sessione precedente).
 *          Idempotente: usa firstOrCreate — sicuro da eseguire più volte.
 *          Corregge anche collection_subscription_starter (is_active=false → true).
 */
class AiFeatureRestoreSeeder extends Seeder {
    public function run(): void {
        // ── Fix: collection_subscription_starter era is_active=false ──────────
        AiFeaturePricing::where('feature_code', 'collection_subscription_starter')
            ->update(['is_active' => true]);

        $features = [

            // 1. EGI LIVING SUBSCRIPTION (platform_services → lifetime)
            [
                'feature_code'             => 'egi_living_subscription',
                'feature_name'             => 'EGI Living Subscription',
                'feature_description'      => 'Abbonamento completo per gestione EGI con aggiornamenti automatici CoA e blockchain monitoring',
                'feature_category'         => 'platform_services',
                'cost_fiat_eur'            => 9.90,
                'cost_egili'               => 500,
                'is_free'                  => false,
                'feature_type'             => 'lifetime',
                'lifetime_cost'            => 500,
                'cost_per_use'             => null,
                'requires_admin_approval'  => false,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 10,
                'is_featured'              => true,
                'icon_name'                => 'superadmin-egili-token',
                'badge_color'              => '#D4A574',
                'benefits'                 => [
                    'Aggiornamenti automatici CoA',
                    'Blockchain monitoring',
                    'Notifiche cambio stato',
                    'Per sempre (acquisto una volta)',
                ],
            ],

            // 2. AI TRAIT GENERATION (ai_services → consumable)
            [
                'feature_code'             => 'ai_trait_generation',
                'feature_name'             => 'Generazione Traits AI',
                'feature_description'      => 'Analisi immagine con AI Vision per generare 1-10 traits artistici con fuzzy matching sul vocabulary',
                'feature_category'         => 'ai_services',
                'cost_fiat_eur'            => null,
                'cost_egili'               => 50,
                'is_free'                  => false,
                'feature_type'             => 'consumable',
                'cost_per_use'             => 50,
                'lifetime_cost'            => null,
                'requires_admin_approval'  => false,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 20,
                'is_featured'              => true,
                'icon_name'                => 'superadmin-ai-brain',
                'badge_color'              => '#1B365D',
                'benefits'                 => [
                    'AI Vision analysis (Claude)',
                    '1-10 traits suggeriti',
                    'Fuzzy matching con vocabulary',
                    'Proposta + approvazione utente',
                ],
                'feature_parameters'       => [
                    'min_traits'          => 1,
                    'max_traits'          => 10,
                    'uses_claude_vision'  => true,
                    'uses_fuzzy_matching' => true,
                ],
            ],

            // 3. AI DESCRIPTION GENERATION (ai_services → consumable)
            [
                'feature_code'             => 'ai_description_generation',
                'feature_name'             => 'Generazione Descrizione AI',
                'feature_description'      => "Analisi immagine con AI per generare descrizione professionale dell'opera con supporto linee guida utente",
                'feature_category'         => 'ai_services',
                'cost_fiat_eur'            => null,
                'cost_egili'               => 30,
                'is_free'                  => false,
                'feature_type'             => 'consumable',
                'cost_per_use'             => 30,
                'lifetime_cost'            => null,
                'requires_admin_approval'  => false,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 25,
                'is_featured'              => true,
                'icon_name'                => 'superadmin-ai-brain',
                'badge_color'              => '#8E44AD',
                'benefits'                 => [
                    'AI analysis (N.A.T.A.N)',
                    'Descrizione professionale',
                    'Supporto linee guida custom',
                    'Generazione istantanea',
                ],
                'feature_parameters'       => [
                    'supports_guidelines'     => true,
                    'max_description_length'  => 2000,
                    'uses_anthropic_claude'   => true,
                ],
            ],

            // 4. AI COLLECTION STRATEGY (ai_services → consumable)
            [
                'feature_code'             => 'ai_collection_strategy',
                'feature_name'             => 'Consulenza Strategica Collection',
                'feature_description'      => 'Analisi AI completa della collection con suggerimenti su descrizione, social, lancio e mantenimento',
                'feature_category'         => 'ai_services',
                'cost_fiat_eur'            => null,
                'cost_egili'               => 300,
                'is_free'                  => false,
                'feature_type'             => 'consumable',
                'cost_per_use'             => 300,
                'lifetime_cost'            => null,
                'requires_admin_approval'  => false,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 35,
                'is_featured'              => true,
                'icon_name'                => 'superadmin-ai-statistics',
                'badge_color'              => '#2D5016',
                'benefits'                 => [
                    'Analisi AI di tutti gli EGI della collection',
                    'Strategia descrizione collection',
                    'Piano social media',
                    'Suggerimenti lancio e aggiornamenti',
                ],
                'feature_parameters'       => [
                    'analyzes_all_collection_images' => true,
                    'generates_description'          => true,
                    'generates_social_plan'          => true,
                    'generates_launch_strategy'      => true,
                ],
            ],

            // 5. FEATURED EGI 7 GIORNI (premium_visibility → temporal + approval)
            [
                'feature_code'             => 'featured_egi_7d',
                'feature_name'             => 'Featured EGI - 7 giorni',
                'feature_description'      => 'EGI in evidenza in homepage per 7 giorni (soggetto ad approvazione admin e disponibilità slot)',
                'feature_category'         => 'premium_visibility',
                'cost_fiat_eur'            => null,
                'cost_egili'               => 500,
                'is_free'                  => false,
                'feature_type'             => 'temporal',
                'cost_per_use'             => 500,
                'lifetime_cost'            => null,
                'duration_hours'           => 168,
                'expires'                  => true,
                'requires_admin_approval'  => true,
                'max_concurrent_slots'     => 3,
                'is_active'                => true,
                'display_order'            => 40,
                'is_featured'              => false,
                'icon_name'                => 'superadmin-dashboard',
                'badge_color'              => '#D4A574',
                'benefits'                 => [
                    'Homepage spotlight 7 giorni',
                    'Visibilità premium',
                    'Scheduling admin garantito',
                    'Max 3 slot disponibili',
                ],
            ],

            // 6. HYPER MODE 7 GIORNI (premium_visibility → temporal + approval)
            [
                'feature_code'             => 'hyper_mode_7d',
                'feature_name'             => 'Hyper Mode - 7 giorni',
                'feature_description'      => 'Modalità Hyper con boost visibilità e badge speciale per 7 giorni (soggetto ad approvazione admin)',
                'feature_category'         => 'premium_visibility',
                'cost_fiat_eur'            => null,
                'cost_egili'               => 300,
                'is_free'                  => false,
                'feature_type'             => 'temporal',
                'cost_per_use'             => 300,
                'lifetime_cost'            => null,
                'duration_hours'           => 168,
                'expires'                  => true,
                'requires_admin_approval'  => true,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 50,
                'is_featured'              => false,
                'icon_name'                => 'superadmin-egili-token',
                'badge_color'              => '#C13120',
                'benefits'                 => [
                    'Badge Hyper Mode',
                    'Boost algoritmo +50%',
                    'Visibilità aumentata 7 giorni',
                    'Approvazione admin richiesta',
                ],
            ],

            // 7. AI CHAT ASSISTANT (ai_services → consumable token-based)
            [
                'feature_code'             => 'ai_chat_assistant',
                'feature_name'             => 'Assistente AI - Art Advisor',
                'feature_description'      => 'Chat illimitata con Art Advisor AI esperto in arte, NFT e strategie marketplace (costo basato su token Claude consumati)',
                'feature_category'         => 'ai_services',
                'cost_fiat_eur'            => null,
                'cost_egili'               => null,
                'is_free'                  => false,
                'feature_type'             => 'consumable',
                'cost_per_use'             => null,
                'lifetime_cost'            => null,
                'requires_admin_approval'  => false,
                'max_concurrent_slots'     => null,
                'is_active'                => true,
                'display_order'            => 60,
                'is_featured'              => true,
                'icon_name'                => 'superadmin-ai-brain',
                'badge_color'              => '#8E44AD',
                'benefits'                 => [
                    'Chat illimitata con Art Advisor AI',
                    'Analisi opere, strategie marketing, pricing',
                    'Costo trasparente basato su token',
                    'Batch charging (paghi solo quando accumuli 10 Egili)',
                ],
                'feature_parameters'       => [
                    'charging_model'            => 'token_based',
                    'egili_per_million_tokens'  => 1,
                    'batch_threshold_egili'     => 10,
                    'cost_per_token'            => 0.000001,
                ],
            ],

        ];

        $created = 0;
        $skipped = 0;

        foreach ($features as $f) {
            $params   = $f['feature_parameters'] ?? null;
            $benefits = $f['benefits'] ?? [];
            unset($f['feature_parameters'], $f['benefits']);

            $exists = AiFeaturePricing::where('feature_code', $f['feature_code'])->exists();

            if (!$exists) {
                AiFeaturePricing::create(array_merge($f, [
                    'benefits'           => $benefits,
                    'feature_parameters' => $params,
                ]));
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✅ AiFeatureRestoreSeeder: {$created} record ripristinati, {$skipped} già esistenti.");
        $this->command->info('   collection_subscription_starter: is_active → true');
    }
}
