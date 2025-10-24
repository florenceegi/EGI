<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Traits Generation)
 * @date 2025-10-21
 * @purpose Create table for individual AI trait proposals
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabella per singole proposte di traits dall'AI
     * Ogni row rappresenta UNA proposta di trait (category + type + value)
     */
    public function up(): void
    {
        Schema::create('ai_trait_proposals', function (Blueprint $table) {
            $table->id();

            // === RELAZIONE SESSIONE ===
            $table->foreignId('generation_id')
                ->constrained('ai_trait_generations')
                ->onDelete('cascade')
                ->comment('Sessione di generazione a cui appartiene questa proposta');

            // === PROPOSTE AI ===
            $table->string('category_suggestion', 100)
                ->comment('Categoria proposta dall\'AI (es: "Materials")');

            $table->string('type_suggestion', 150)
                ->comment('Tipo proposto dall\'AI (es: "Primary Material")');

            $table->string('value_suggestion', 255)
                ->comment('Valore proposto dall\'AI (es: "Bronze")');

            $table->string('display_value_suggestion', 255)
                ->nullable()
                ->comment('Display value proposto (es: "Bronze Patina")');

            $table->unsignedTinyInteger('confidence')
                ->comment('Confidence AI per questa proposta (0-100)');

            $table->text('reasoning')
                ->nullable()
                ->comment('Motivazione AI per questa proposta');

            // === MATCHING RESULTS ===
            $table->enum('match_type', [
                'exact',            // Match esatto con esistente
                'fuzzy',            // Match fuzzy con esistente
                'new_value',        // Category + Type esistono, Value nuovo
                'new_type',         // Category esiste, Type nuovo
                'new_category'      // Category completamente nuova
            ])->comment('Tipo di match trovato');

            // === REFERENCES A ESISTENTI (se match) ===
            $table->foreignId('matched_category_id')
                ->nullable()
                ->constrained('trait_categories')
                ->onDelete('set null')
                ->comment('Category esistente matchata (se match_type != new_category)');

            $table->foreignId('matched_type_id')
                ->nullable()
                ->constrained('trait_types')
                ->onDelete('set null')
                ->comment('Type esistente matchato (se match_type = exact/fuzzy)');

            $table->string('matched_value', 255)
                ->nullable()
                ->comment('Value esistente matchato (se match_type = exact/fuzzy)');

            // === MATCH SCORES ===
            $table->unsignedTinyInteger('category_match_score')
                ->nullable()
                ->comment('Score match category (0-100)');

            $table->unsignedTinyInteger('type_match_score')
                ->nullable()
                ->comment('Score match type (0-100)');

            $table->unsignedTinyInteger('value_match_score')
                ->nullable()
                ->comment('Score match value (0-100)');

            // === USER DECISION ===
            $table->enum('user_decision', [
                'pending',      // In attesa di review
                'approved',     // Utente ha approvato
                'rejected',     // Utente ha rifiutato
                'modified'      // Utente ha modificato manualmente
            ])->default('pending')
                ->comment('Decisione utente su questa proposta');

            $table->json('user_modifications')
                ->nullable()
                ->comment('Modifiche manuali utente (se user_decision=modified)');

            $table->timestamp('reviewed_at')
                ->nullable()
                ->comment('Quando utente ha fatto review');

            // === CREATED REFERENCES (dopo approvazione) ===
            $table->foreignId('created_category_id')
                ->nullable()
                ->constrained('trait_categories')
                ->onDelete('set null')
                ->comment('Category creata se era nuova');

            $table->foreignId('created_type_id')
                ->nullable()
                ->constrained('trait_types')
                ->onDelete('set null')
                ->comment('Type creato se era nuovo');

            $table->foreignId('created_trait_id')
                ->nullable()
                ->constrained('egi_traits')
                ->onDelete('set null')
                ->comment('Trait EGI creato dopo approvazione');

            $table->timestamp('applied_at')
                ->nullable()
                ->comment('Quando trait è stato applicato all\'EGI');

            // === METADATA ===
            $table->unsignedTinyInteger('sort_order')
                ->default(0)
                ->comment('Ordine di presentazione nella UI');

            $table->timestamps();

            // === INDEXES ===
            $table->index('generation_id', 'idx_proposal_generation');
            $table->index('match_type', 'idx_proposal_match_type');
            $table->index('user_decision', 'idx_proposal_decision');
            $table->index(['generation_id', 'user_decision'], 'idx_proposal_gen_decision');
            $table->index('matched_category_id', 'idx_proposal_matched_cat');
            $table->index('matched_type_id', 'idx_proposal_matched_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_trait_proposals');
    }
};










