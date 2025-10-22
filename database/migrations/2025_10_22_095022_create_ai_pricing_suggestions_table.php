<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Pricing Suggestions)
 * @date 2025-10-22
 * @purpose Create table for AI-generated pricing recommendations
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per suggerimenti pricing AI:
     * - Price range recommendations
     * - Dynamic pricing strategies
     * - Market comparisons
     * - Revenue optimization
     */
    public function up(): void
    {
        Schema::create('ai_pricing_suggestions', function (Blueprint $table) {
            $table->id();

            // === RELAZIONI ===
            $table->foreignId('analysis_id')
                ->constrained('ai_egi_analyses')
                ->onDelete('cascade')
                ->comment('Analisi di appartenenza');

            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('EGI target');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Utente destinatario');

            // === PRICING STRATEGY ===
            $table->enum('strategy_type', [
                'competitive',      // Basato su analisi competitor
                'value_based',      // Basato su valore percepito
                'premium',          // Strategia premium/luxury
                'penetration',      // Prezzo penetrazione mercato
                'dynamic',          // Prezzo dinamico
                'psychological'     // Pricing psicologico
            ])->comment('Tipo di strategia pricing suggerita');

            $table->text('strategy_rationale')
                ->nullable()
                ->comment('Motivazione strategia scelta');

            // === PRICE RECOMMENDATIONS ===
            $table->decimal('suggested_price_min', 12, 2)
                ->nullable()
                ->comment('Prezzo minimo suggerito (EUR)');

            $table->decimal('suggested_price_optimal', 12, 2)
                ->nullable()
                ->comment('Prezzo ottimale suggerito (EUR)');

            $table->decimal('suggested_price_max', 12, 2)
                ->nullable()
                ->comment('Prezzo massimo suggerito (EUR)');

            $table->decimal('current_price', 12, 2)
                ->nullable()
                ->comment('Prezzo corrente EGI al momento analisi');

            $table->decimal('price_adjustment_percentage', 7, 2)
                ->nullable()
                ->comment('% variazione suggerita vs prezzo corrente');

            // === MARKET ANALYSIS ===
            $table->decimal('market_average_price', 12, 2)
                ->nullable()
                ->comment('Prezzo medio di mercato');

            $table->decimal('competitor_price_low', 12, 2)
                ->nullable()
                ->comment('Prezzo più basso competitor');

            $table->decimal('competitor_price_high', 12, 2)
                ->nullable()
                ->comment('Prezzo più alto competitor');

            $table->text('market_position')
                ->nullable()
                ->comment('Posizione nel mercato (JSON: below/at/above market)');

            // === REVENUE PROJECTIONS ===
            $table->decimal('estimated_revenue_low', 12, 2)
                ->nullable()
                ->comment('Revenue stimato scenario conservativo');

            $table->decimal('estimated_revenue_optimal', 12, 2)
                ->nullable()
                ->comment('Revenue stimato scenario ottimale');

            $table->decimal('estimated_revenue_high', 12, 2)
                ->nullable()
                ->comment('Revenue stimato scenario ottimistico');

            $table->unsignedSmallInteger('estimated_sales_volume')
                ->nullable()
                ->comment('Volume vendite stimato (unità)');

            $table->unsignedSmallInteger('estimated_time_to_sell_days')
                ->nullable()
                ->comment('Tempo stimato per vendita (giorni)');

            // === CONFIDENCE & VALIDATION ===
            $table->unsignedTinyInteger('confidence_score')
                ->nullable()
                ->comment('Confidence AI su questo suggerimento (0-100)');

            $table->json('confidence_factors')
                ->nullable()
                ->comment('Fattori che influenzano confidence (JSON)');

            $table->text('validation_notes')
                ->nullable()
                ->comment('Note di validazione AI');

            // === SEASONALITY & TIMING ===
            $table->json('seasonal_factors')
                ->nullable()
                ->comment('Fattori stagionali da considerare');

            $table->date('optimal_listing_date')
                ->nullable()
                ->comment('Data ottimale per listing');

            $table->text('timing_recommendations')
                ->nullable()
                ->comment('Raccomandazioni timing');

            // === USER DECISION ===
            $table->enum('user_decision', [
                'pending',      // In attesa di decisione
                'accepted',     // Accettato e implementato
                'rejected',     // Rifiutato
                'modified',     // Modificato dall\'utente
                'ignored'       // Ignorato
            ])->default('pending')
                ->comment('Decisione utente');

            $table->decimal('user_final_price', 12, 2)
                ->nullable()
                ->comment('Prezzo finale scelto dall\'utente');

            $table->text('user_notes')
                ->nullable()
                ->comment('Note utente sulla decisione');

            $table->timestamp('decided_at')
                ->nullable()
                ->comment('Quando utente ha deciso');

            // === TRACKING & RESULTS ===
            $table->boolean('was_implemented')
                ->default(false)
                ->comment('Se suggerimento è stato implementato');

            $table->timestamp('implemented_at')
                ->nullable()
                ->comment('Quando implementato');

            $table->decimal('actual_sale_price', 12, 2)
                ->nullable()
                ->comment('Prezzo vendita effettivo (se venduto)');

            $table->unsignedSmallInteger('actual_days_to_sell')
                ->nullable()
                ->comment('Giorni effettivi per vendita');

            $table->boolean('was_accurate')
                ->nullable()
                ->comment('Se previsione era accurata (valutato post-vendita)');

            // === METADATA ===
            $table->json('ai_metadata')
                ->nullable()
                ->comment('Metadata aggiuntivi da AI');

            $table->timestamps();
            $table->softDeletes();

            // === INDEXES ===
            $table->index('analysis_id', 'idx_pricing_analysis');
            $table->index('egi_id', 'idx_pricing_egi');
            $table->index('user_id', 'idx_pricing_user');
            $table->index('strategy_type', 'idx_pricing_strategy');
            $table->index('user_decision', 'idx_pricing_decision');
            $table->index('was_implemented', 'idx_pricing_implemented');
            $table->index(['egi_id', 'user_decision'], 'idx_pricing_egi_decision');
            $table->index('created_at', 'idx_pricing_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_pricing_suggestions');
    }
};
