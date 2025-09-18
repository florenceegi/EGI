<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration `coa_events` secondo l'Addendum - audit trail degli eventi CoA
     */
    public function up(): void {
        Schema::create('coa_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('coa_id')->constrained('coa')->onDelete('cascade');
            $table->enum('type', ['ISSUED', 'REVOKED', 'ANNEX_ADDED', 'ADDENDUM_ISSUED'])->comment('Tipo di evento CoA');
            $table->json('payload')->nullable()->comment('Esito, motivi, elenco file, hash coinvolti');
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null')->comment('Utente piattaforma');
            $table->datetime('created_at');

            // Indici per performance
            $table->index(['coa_id', 'type', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa_events');
    }
};