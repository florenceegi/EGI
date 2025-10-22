<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI EGI Complete Analysis)
 * @date 2025-10-22
 * @purpose Create table for comprehensive AI analysis of EGIs (pricing, marketing, optimization)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per analisi AI complete degli EGI:
     * - Market positioning
     * - Competitive analysis
     * - Target audience identification
     * - Pricing strategy
     * - Marketing recommendations
     * - Growth opportunities
     */
    public function up(): void
    {
        Schema::create('ai_egi_analyses', function (Blueprint $table) {
            $table->id();

            // === RELAZIONI ===
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('EGI analizzato');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Utente che ha richiesto l\'analisi');

            // === TIPO E SCOPE ANALISI ===
            $table->enum('analysis_type', [
                'full',           // Analisi completa (pricing + marketing + optimization)
                'pricing_only',   // Solo suggerimenti pricing
                'marketing_only', // Solo azioni marketing
                'quick_scan'      // Scan veloce (overview generale)
            ])->default('full')
                ->comment('Tipo di analisi richiesta');

            $table->json('analysis_scope')
                ->nullable()
                ->comment('Parametri specifici dell\'analisi (JSON)');

            // === AI RESPONSE ===
            $table->json('ai_raw_response')
                ->nullable()
                ->comment('Response JSON completa da AI');

            $table->text('market_positioning')
                ->nullable()
                ->comment('Posizionamento mercato suggerito da AI');

            $table->text('target_audience')
                ->nullable()
                ->comment('Pubblico target identificato');

            $table->text('competitive_analysis')
                ->nullable()
                ->comment('Analisi competitiva');

            $table->text('unique_selling_points')
                ->nullable()
                ->comment('USP identificati dall\'AI (JSON array)');

            $table->text('growth_opportunities')
                ->nullable()
                ->comment('Opportunità di crescita');

            $table->text('risk_factors')
                ->nullable()
                ->comment('Fattori di rischio identificati');

            // === CONFIDENCE SCORES ===
            $table->unsignedTinyInteger('overall_confidence')
                ->nullable()
                ->comment('Confidence complessiva dell\'analisi (0-100)');

            $table->unsignedTinyInteger('pricing_confidence')
                ->nullable()
                ->comment('Confidence sui suggerimenti pricing (0-100)');

            $table->unsignedTinyInteger('marketing_confidence')
                ->nullable()
                ->comment('Confidence sulle azioni marketing (0-100)');

            // === RELAZIONI CONNESSE ===
            $table->unsignedSmallInteger('pricing_suggestions_count')
                ->default(0)
                ->comment('Numero di suggerimenti pricing generati');

            $table->unsignedSmallInteger('marketing_actions_count')
                ->default(0)
                ->comment('Numero di azioni marketing generate');

            // === STATUS WORKFLOW ===
            $table->enum('status', [
                'pending',      // Analisi in corso
                'completed',    // Analisi completata
                'review',       // In revisione utente
                'implemented',  // Suggerimenti implementati
                'archived',     // Archiviata
                'failed'        // Errore durante analisi
            ])->default('pending')
                ->comment('Stato analisi');

            $table->timestamp('completed_at')
                ->nullable()
                ->comment('Quando analisi è stata completata');

            $table->timestamp('reviewed_at')
                ->nullable()
                ->comment('Quando utente ha revisionato');

            $table->timestamp('implemented_at')
                ->nullable()
                ->comment('Quando suggerimenti sono stati implementati');

            // === AI CREDITS TRACKING ===
            $table->unsignedInteger('credits_used')
                ->default(0)
                ->comment('Crediti AI consumati per questa analisi');

            $table->string('ai_model_used', 100)
                ->nullable()
                ->comment('Modello AI utilizzato (es: claude-3-opus-20240229)');

            $table->unsignedInteger('processing_time_ms')
                ->nullable()
                ->comment('Tempo elaborazione AI in millisecondi');

            $table->unsignedInteger('tokens_used')
                ->nullable()
                ->comment('Token consumati (input + output)');

            // === ERROR TRACKING ===
            $table->text('error_message')
                ->nullable()
                ->comment('Messaggio errore se status=failed');

            $table->string('error_code', 50)
                ->nullable()
                ->comment('Codice errore UEM');

            // === AUDIT TRAIL ===
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('IP utente per audit');

            $table->string('user_agent', 500)
                ->nullable()
                ->comment('User agent per audit');

            // === VERSIONING ===
            $table->unsignedSmallInteger('version')
                ->default(1)
                ->comment('Versione analisi (se rieseguita)');

            $table->foreignId('previous_analysis_id')
                ->nullable()
                ->constrained('ai_egi_analyses')
                ->onDelete('set null')
                ->comment('Link a versione precedente (se re-analizzato)');

            $table->timestamps();
            $table->softDeletes();

            // === INDEXES ===
            $table->index('egi_id', 'idx_ai_analysis_egi');
            $table->index('user_id', 'idx_ai_analysis_user');
            $table->index('status', 'idx_ai_analysis_status');
            $table->index('analysis_type', 'idx_ai_analysis_type');
            $table->index(['egi_id', 'status'], 'idx_ai_analysis_egi_status');
            $table->index('created_at', 'idx_ai_analysis_created');
            $table->index('version', 'idx_ai_analysis_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_egi_analyses');
    }
};
