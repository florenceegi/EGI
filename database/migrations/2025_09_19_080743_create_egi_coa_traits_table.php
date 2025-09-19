<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for EGI CoA-Specific Traits
 *
 * Tabella di relazione che collega ogni EGI ai suoi traits specifici per CoA:
 * - Selezioni multiple per tecnica/materiali/supporto
 * - Campi free-text per voci "Altro" personalizzate
 * - JSON structured per flessibilità e performance
 *
 * Sistema multilingua: i slug vengono tradotti via Laravel i18n
 *
 * @package FlorenceEGI\Database\Migrations
 * @author AI Assistant for FlorenceEGI CoA System
 * @version 1.0.0 (CoA Traits System)
 * @date 2025-09-19
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('egi_coa_traits', function (Blueprint $table) {
            $table->id();

            // Relazione con EGI (one-to-one)
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->unique(); // Un solo record per EGI

            // Selezioni structured (arrays di slugs dal vocabolario)
            $table->json('technique_slugs')->nullable(); // ["painting-oil", "mixed-media"]
            $table->json('materials_slugs')->nullable(); // ["paint-oil", "canvas-cotton"]
            $table->json('support_slugs')->nullable();   // ["support-canvas-stretched-cotton"]

            // Free text per voci "Altro" personalizzate
            $table->json('technique_free_text')->nullable(); // ["tecnica particolare", "altra tecnica"]
            $table->json('materials_free_text')->nullable(); // ["materiale speciale"]
            $table->json('support_free_text')->nullable();   // ["supporto particolare"]

            // Metadati per audit e versioning
            $table->timestamp('last_updated_at')->nullable(); // Ultimo aggiornamento traits
            $table->foreignId('updated_by_user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete(); // Chi ha fatto l'ultimo update

            $table->timestamps();

            // Indexes per performance
            $table->index(['egi_id']); // lookup principale
            $table->index(['last_updated_at']); // ordinamento per freshness
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('egi_coa_traits');
    }
};