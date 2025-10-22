<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Feature Pricing)
 * @date 2025-10-22
 * @purpose Create ai_feature_pricing table for dynamic pricing management
 * @source docs/ai/TOKENOMICS_EGILI_EQUILIBRIUM.md lines 454-531
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per gestire prezzi dinamici di tutte le features AI e premium.
     * Supporta sia pagamento FIAT che Egili, con tier differenziati.
     */
    public function up(): void
    {
        Schema::create('ai_feature_pricing', function (Blueprint $table) {
            $table->id();

            // === FEATURE IDENTIFICATION ===
            $table->string('feature_code', 100)->unique()
                ->comment('Codice univoco feature (es: ai_egi_deep_analysis)');

            $table->string('feature_name', 255)
                ->comment('Nome leggibile feature');

            $table->text('feature_description')->nullable()
                ->comment('Descrizione dettagliata cosa include');

            $table->enum('feature_category', [
                'ai_services',          // Servizi AI (analysis, pricing, marketing)
                'premium_visibility',   // Featured placement, boost visibility
                'premium_profile',      // Custom badge, verified, themes
                'premium_analytics',    // Advanced analytics, export, API
                'exclusive_access',     // Events, drops, contests
                'platform_services'     // Altri servizi piattaforma
            ])->default('ai_services')
                ->comment('Categoria feature per raggruppamento UI');

            // === PRICING ===
            $table->decimal('cost_fiat_eur', 10, 2)->nullable()
                ->comment('Prezzo in EUR (NULL = non disponibile in FIAT)');

            $table->unsignedInteger('cost_egili')->nullable()
                ->comment('Prezzo in Egili (NULL = non disponibile in Egili)');

            $table->boolean('is_free')->default(false)
                ->comment('Se feature è gratuita');

            $table->unsignedSmallInteger('free_monthly_limit')->nullable()
                ->comment('Limite mensile gratuito (es: 3 Quick Checks/mese)');

            // === SUBSCRIPTION TIERS ===
            $table->json('tier_pricing')->nullable()
                ->comment('Pricing differenziato per tier: {free: X, starter: Y, pro: Z}');

            $table->enum('min_tier_required', [
                'free',
                'starter',
                'pro',
                'business',
                'enterprise'
            ])->default('free')
                ->comment('Tier minimo richiesto per accesso');

            // === BUNDLE & DISCOUNTS ===
            $table->boolean('is_bundle')->default(false)
                ->comment('Se è un bundle di features');

            $table->json('bundle_features')->nullable()
                ->comment('Array feature codes incluse nel bundle');

            $table->unsignedTinyInteger('discount_percentage')->default(0)
                ->comment('Percentuale sconto applicata (0-100)');

            $table->string('bundle_type', 50)->nullable()
                ->comment('Tipo bundle (es: 3_egi, collection_sample, monthly)');

            // === DURATION & EXPIRY ===
            $table->boolean('is_recurring')->default(false)
                ->comment('Se è abbonamento ricorrente');

            $table->enum('recurrence_period', [
                'daily',
                'weekly',
                'monthly',
                'yearly',
                'one_time'
            ])->default('one_time')
                ->comment('Frequenza ricorrenza');

            $table->unsignedInteger('duration_hours')->nullable()
                ->comment('Durata in ore (es: 24h spotlight, 7gg boost)');

            $table->boolean('expires')->default(false)
                ->comment('Se l\'acquisto ha scadenza');

            // === LIMITS & QUOTAS ===
            $table->unsignedInteger('max_uses_per_purchase')->nullable()
                ->comment('Max utilizzi per singolo acquisto (NULL = illimitato)');

            $table->unsignedInteger('monthly_quota')->nullable()
                ->comment('Quota mensile per tier subscription');

            $table->boolean('stackable')->default(true)
                ->comment('Se acquisti multipli si sommano');

            // === BUSINESS LOGIC ===
            $table->json('feature_parameters')->nullable()
                ->comment('Parametri specifici feature (es: num_egis_analyzed, action_items_count)');

            $table->json('benefits')->nullable()
                ->comment('Lista benefici inclusi (es: image analysis, 3 action items)');

            $table->decimal('expected_roi_multiplier', 5, 2)->nullable()
                ->comment('ROI atteso (es: Featured = 3x views avg)');

            // === AVAILABILITY & STATUS ===
            $table->boolean('is_active')->default(true)->index()
                ->comment('Se feature è disponibile per acquisto');

            $table->timestamp('available_from')->nullable()
                ->comment('Data inizio disponibilità');

            $table->timestamp('available_until')->nullable()
                ->comment('Data fine disponibilità (per limited time offers)');

            $table->boolean('is_beta')->default(false)
                ->comment('Se feature è in beta (accesso limitato)');

            $table->boolean('requires_approval')->default(false)
                ->comment('Se richiede approvazione manuale');

            // === SORTING & DISPLAY ===
            $table->unsignedSmallInteger('display_order')->default(0)
                ->comment('Ordine visualizzazione in lista features');

            $table->boolean('is_featured')->default(false)
                ->comment('Se mostrare in evidenza nella UI');

            $table->string('icon_name', 100)->nullable()
                ->comment('Nome icona da config/icons.php');

            $table->string('badge_color', 50)->nullable()
                ->comment('Colore badge UI (es: gold, blue, green)');

            // === ANALYTICS & TRACKING ===
            $table->unsignedInteger('total_purchases')->default(0)
                ->comment('Conteggio acquisti totali');

            $table->unsignedInteger('total_egili_spent')->default(0)
                ->comment('Totale Egili spesi per questa feature');

            $table->decimal('total_fiat_revenue', 12, 2)->default(0)
                ->comment('Totale revenue FIAT per questa feature');

            $table->timestamp('last_purchased_at')->nullable()
                ->comment('Data ultimo acquisto');

            // === METADATA ===
            $table->json('metadata')->nullable()
                ->comment('Metadata aggiuntivi JSON');

            $table->text('admin_notes')->nullable()
                ->comment('Note admin per gestione pricing');

            $table->timestamps();
            $table->softDeletes();

            // === INDEXES ===
            $table->index(['feature_category', 'is_active']);
            $table->index(['min_tier_required', 'is_active']);
            $table->index(['is_bundle', 'is_active']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feature_pricing');
    }
};
