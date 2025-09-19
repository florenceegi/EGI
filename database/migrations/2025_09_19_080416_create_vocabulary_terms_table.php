<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for CoA Vocabulary Terms (Controlled Vocabulary)
 *
 * Tabella per vocabolario controllato per certificati di autenticità:
 * - Tecniche artistiche
 * - Materiali
 * - Supporti
 *
 * Sistema multilingua: le traduzioni sono gestite via Laravel i18n (lang/[locale]/traits.php)
 * Gli aliases sono multilingua per tutte le 6 lingue supportate: it, en, fr, pt, es, de
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
        Schema::create('vocabulary_terms', function (Blueprint $table) {
            $table->id();

            // Identificatori univoci
            $table->string('slug', 100)->unique(); // es: 'painting-oil', 'metal-bronze'
            $table->enum('category', ['technique', 'materials', 'support']); // 3 categorie principali

            // Raggruppamento UI (per sidebar modale)
            $table->string('ui_group', 50)->nullable(); // es: 'Pittura', 'Metalli', 'Tele'

            // Search aliases (JSON multilingua per tutte le 6 lingue supportate)
            $table->json('aliases')->nullable(); // {"it": ["olio"], "en": ["oil"], "fr": ["huile"], "pt": ["óleo"], "es": ["aceite"], "de": ["öl"]}

            // Metadati opzionali
            $table->string('aat_id', 50)->nullable(); // Getty Art & Architecture Thesaurus ID
            $table->text('description')->nullable(); // descrizione tecnica dettagliata

            // Ordinamento e visibilità
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes ottimizzati per performance
            $table->index(['category', 'is_active', 'sort_order']); // listing per categoria
            $table->index(['ui_group', 'sort_order']); // grouping per UI
            $table->index(['slug']); // unique lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('vocabulary_terms');
    }
};
