<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Platform Settings)
 * @date 2026-02-25
 * @purpose Tabella key-value per impostazioni di piattaforma gestite dal DB
 *          Sostituisce config/ai-credits.php e simili file di config hardcoded.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();

            $table->string('group', 50)->index()
                ->comment('Gruppo logico: ai_credits, payments, platform, ecc.');

            $table->string('key', 100)
                ->comment('Chiave setting (es: usd_to_eur_rate)');

            $table->text('value')->nullable()
                ->comment('Valore (sempre stringa, castato dal Model)');

            $table->enum('value_type', ['string', 'integer', 'decimal', 'boolean', 'json'])
                ->default('string')
                ->comment('Tipo valore per il cast automatico');

            $table->string('label', 255)->nullable()
                ->comment('Etichetta leggibile per la UI admin');

            $table->text('description')->nullable()
                ->comment('Descrizione del setting per admin');

            $table->boolean('is_editable')->default(true)
                ->comment('Se il superadmin può modificarlo dalla UI');

            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('platform_settings');
    }
};
