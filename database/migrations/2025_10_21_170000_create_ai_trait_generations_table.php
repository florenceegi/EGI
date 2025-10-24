<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Traits Generation)
 * @date 2025-10-21
 * @purpose Create table for AI trait generation sessions
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per tracciare le sessioni di generazione AI traits
     * Una sessione rappresenta una richiesta dell'utente di generare N traits per un EGI
     */
    public function up(): void
    {
        Schema::create('ai_trait_generations', function (Blueprint $table) {
            $table->id();

            // === RELAZIONI ===
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('EGI per cui sono stati generati i traits');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Utente che ha richiesto la generazione');

            // === PARAMETRI RICHIESTA ===
            $table->unsignedTinyInteger('requested_count')
                ->comment('Numero di traits richiesti (1-10)');

            $table->string('image_url', 500)
                ->comment('URL immagine analizzata da Claude Vision');

            // === RISULTATI AI ===
            $table->json('ai_raw_response')
                ->nullable()
                ->comment('Response JSON completa da Anthropic API');

            $table->unsignedTinyInteger('total_confidence')
                ->nullable()
                ->comment('Confidence media dei traits proposti (0-100)');

            $table->text('analysis_notes')
                ->nullable()
                ->comment('Note generali dell\'analisi AI');

            // === MATCHING RESULTS ===
            $table->unsignedTinyInteger('exact_matches_count')
                ->default(0)
                ->comment('Numero di traits con match esatto');

            $table->unsignedTinyInteger('fuzzy_matches_count')
                ->default(0)
                ->comment('Numero di traits con match fuzzy');

            $table->unsignedTinyInteger('new_proposals_count')
                ->default(0)
                ->comment('Numero di nuove proposte (categorie/tipi/valori nuovi)');

            // === STATUS WORKFLOW ===
            $table->enum('status', [
                'pending',      // AI generazione in corso
                'analyzed',     // AI completata, in attesa di review utente
                'approved',     // Utente ha approvato
                'rejected',     // Utente ha rifiutato
                'partial',      // Utente ha approvato solo alcuni traits
                'applied',      // Traits applicati all'EGI
                'failed'        // Errore durante generazione/applicazione
            ])->default('pending')
                ->comment('Stato workflow generazione');

            $table->timestamp('analyzed_at')
                ->nullable()
                ->comment('Quando AI ha completato l\'analisi');

            $table->timestamp('reviewed_at')
                ->nullable()
                ->comment('Quando utente ha fatto review');

            $table->timestamp('applied_at')
                ->nullable()
                ->comment('Quando traits sono stati applicati all\'EGI');

            // === ERROR TRACKING ===
            $table->text('error_message')
                ->nullable()
                ->comment('Messaggio errore se status=failed');

            $table->string('error_code', 50)
                ->nullable()
                ->comment('Codice errore UEM');

            // === METADATA ===
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('IP utente per audit');

            $table->string('user_agent', 500)
                ->nullable()
                ->comment('User agent per audit');

            $table->timestamps();

            // === INDEXES ===
            $table->index('egi_id', 'idx_trait_gen_egi');
            $table->index('user_id', 'idx_trait_gen_user');
            $table->index('status', 'idx_trait_gen_status');
            $table->index(['egi_id', 'status'], 'idx_trait_gen_egi_status');
            $table->index('created_at', 'idx_trait_gen_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_trait_generations');
    }
};










