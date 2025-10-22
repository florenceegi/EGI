<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Marketing Actions)
 * @date 2025-10-22
 * @purpose Create table for AI-generated marketing action recommendations
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per azioni marketing suggerite da AI:
     * - Social media strategies
     * - Content recommendations
     * - Advertising suggestions
     * - Engagement tactics
     */
    public function up(): void
    {
        Schema::create('ai_marketing_actions', function (Blueprint $table) {
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

            // === ACTION TYPE & CATEGORY ===
            $table->enum('action_type', [
                'social_media',      // Azione social media
                'content_creation',  // Creazione contenuto
                'advertising',       // Campagna pubblicitaria
                'seo_optimization',  // Ottimizzazione SEO
                'email_marketing',   // Email marketing
                'influencer',        // Collaborazione influencer
                'pr',                // Public Relations
                'community',         // Community engagement
                'event',             // Evento/mostra
                'partnership'        // Partnership strategica
            ])->comment('Tipo azione marketing');

            $table->enum('priority', [
                'critical',   // Azione critica (da fare subito)
                'high',       // Alta priorità
                'medium',     // Media priorità
                'low'         // Bassa priorità
            ])->default('medium')
                ->comment('Priorità azione');

            $table->enum('effort_level', [
                'quick_win',  // Quick win (< 1 ora)
                'low',        // Sforzo basso (1-4 ore)
                'medium',     // Sforzo medio (1-2 giorni)
                'high',       // Sforzo alto (1 settimana+)
                'ongoing'     // Attività continuativa
            ])->comment('Livello di sforzo richiesto');

            // === ACTION DETAILS ===
            $table->string('title', 255)
                ->comment('Titolo azione');

            $table->text('description')
                ->comment('Descrizione dettagliata azione');

            $table->text('rationale')
                ->nullable()
                ->comment('Motivazione/logica dietro azione');

            $table->text('expected_outcome')
                ->nullable()
                ->comment('Risultati attesi');

            $table->json('step_by_step_guide')
                ->nullable()
                ->comment('Guida step-by-step (JSON array)');

            $table->json('required_resources')
                ->nullable()
                ->comment('Risorse necessarie (JSON)');

            // === TARGETING ===
            $table->json('target_audience')
                ->nullable()
                ->comment('Pubblico target per questa azione');

            $table->json('target_platforms')
                ->nullable()
                ->comment('Piattaforme target (Instagram, LinkedIn, etc.)');

            $table->json('recommended_hashtags')
                ->nullable()
                ->comment('Hashtag consigliati (se applicabile)');

            $table->json('recommended_keywords')
                ->nullable()
                ->comment('Keywords consigliate (per SEO)');

            // === TIMING & SCHEDULING ===
            $table->date('suggested_start_date')
                ->nullable()
                ->comment('Data inizio suggerita');

            $table->date('suggested_end_date')
                ->nullable()
                ->comment('Data fine suggerita (se applicabile)');

            $table->unsignedSmallInteger('estimated_duration_days')
                ->nullable()
                ->comment('Durata stimata in giorni');

            $table->enum('frequency', [
                'one_time',    // Una tantum
                'daily',       // Giornaliera
                'weekly',      // Settimanale
                'bi_weekly',   // Bi-settimanale
                'monthly',     // Mensile
                'seasonal'     // Stagionale
            ])->nullable()
                ->comment('Frequenza azione');

            // === BUDGET & COST ===
            $table->decimal('estimated_cost_min', 10, 2)
                ->nullable()
                ->comment('Costo minimo stimato (EUR)');

            $table->decimal('estimated_cost_max', 10, 2)
                ->nullable()
                ->comment('Costo massimo stimato (EUR)');

            $table->text('cost_breakdown')
                ->nullable()
                ->comment('Breakdown costi (JSON)');

            // === EXPECTED IMPACT ===
            $table->enum('expected_impact', [
                'high',      // Alto impatto
                'medium',    // Medio impatto
                'low'        // Basso impatto
            ])->nullable()
                ->comment('Impatto atteso');

            $table->json('kpi_targets')
                ->nullable()
                ->comment('KPI target da raggiungere (JSON)');

            $table->unsignedInteger('estimated_reach')
                ->nullable()
                ->comment('Reach stimato (persone)');

            $table->unsignedInteger('estimated_engagement')
                ->nullable()
                ->comment('Engagement stimato');

            $table->decimal('estimated_roi_percentage', 7, 2)
                ->nullable()
                ->comment('ROI stimato %');

            // === CONFIDENCE & VALIDATION ===
            $table->unsignedTinyInteger('confidence_score')
                ->nullable()
                ->comment('Confidence AI su questa azione (0-100)');

            $table->json('success_probability_factors')
                ->nullable()
                ->comment('Fattori di probabilità successo');

            // === CONTENT SUGGESTIONS ===
            $table->text('suggested_content_title')
                ->nullable()
                ->comment('Titolo contenuto suggerito');

            $table->text('suggested_content_body')
                ->nullable()
                ->comment('Corpo contenuto suggerito');

            $table->json('suggested_visuals')
                ->nullable()
                ->comment('Suggerimenti visuali (stile, colori, mood)');

            $table->json('call_to_action')
                ->nullable()
                ->comment('CTA suggerite');

            // === USER DECISION & EXECUTION ===
            $table->enum('user_decision', [
                'pending',       // In attesa di decisione
                'accepted',      // Accettata
                'in_progress',   // In esecuzione
                'completed',     // Completata
                'rejected',      // Rifiutata
                'postponed',     // Posticipata
                'modified'       // Modificata
            ])->default('pending')
                ->comment('Stato decisione/esecuzione');

            $table->text('user_notes')
                ->nullable()
                ->comment('Note utente');

            $table->timestamp('decided_at')
                ->nullable()
                ->comment('Quando utente ha deciso');

            $table->timestamp('started_at')
                ->nullable()
                ->comment('Quando azione è iniziata');

            $table->timestamp('completed_at')
                ->nullable()
                ->comment('Quando azione è stata completata');

            // === RESULTS TRACKING ===
            $table->json('actual_results')
                ->nullable()
                ->comment('Risultati effettivi raggiunti (JSON)');

            $table->unsignedInteger('actual_reach')
                ->nullable()
                ->comment('Reach effettivo');

            $table->unsignedInteger('actual_engagement')
                ->nullable()
                ->comment('Engagement effettivo');

            $table->decimal('actual_cost', 10, 2)
                ->nullable()
                ->comment('Costo effettivo sostenuto');

            $table->decimal('actual_roi_percentage', 7, 2)
                ->nullable()
                ->comment('ROI effettivo %');

            $table->boolean('was_successful')
                ->nullable()
                ->comment('Se azione è stata considerata successo');

            $table->text('lessons_learned')
                ->nullable()
                ->comment('Lezioni apprese (per future analisi AI)');

            // === RELATED ACTIONS ===
            $table->foreignId('parent_action_id')
                ->nullable()
                ->constrained('ai_marketing_actions')
                ->onDelete('set null')
                ->comment('Azione parent (se parte di campagna)');

            $table->json('related_actions_ids')
                ->nullable()
                ->comment('IDs azioni correlate (JSON array)');

            // === METADATA ===
            $table->json('ai_metadata')
                ->nullable()
                ->comment('Metadata aggiuntivi da AI');

            $table->timestamps();
            $table->softDeletes();

            // === INDEXES ===
            $table->index('analysis_id', 'idx_marketing_analysis');
            $table->index('egi_id', 'idx_marketing_egi');
            $table->index('user_id', 'idx_marketing_user');
            $table->index('action_type', 'idx_marketing_type');
            $table->index('priority', 'idx_marketing_priority');
            $table->index('user_decision', 'idx_marketing_decision');
            $table->index(['egi_id', 'user_decision'], 'idx_marketing_egi_decision');
            $table->index('suggested_start_date', 'idx_marketing_start_date');
            $table->index('created_at', 'idx_marketing_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_marketing_actions');
    }
};
