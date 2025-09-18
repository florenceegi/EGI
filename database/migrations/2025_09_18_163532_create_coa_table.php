<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration per tabella `coa` con le colonne descritte nel piano tecnico, FK su `egi` e indice univoco su `serial`.
     */
    public function up(): void {
        Schema::create('coa', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade');
            $table->string('serial', 64)->unique()->comment('Format: COA-EGI-YYYY-000###');
            $table->enum('status', ['valid', 'revoked'])->default('valid');
            $table->enum('issuer_type', ['author', 'archive', 'platform'])->default('author');
            $table->string('issuer_name', 190);
            $table->string('issuer_location', 190)->nullable();
            $table->datetime('issued_at');
            $table->datetime('revoked_at')->nullable();
            $table->string('revoke_reason', 255)->nullable();
            $table->timestamps();

            // Indici per performance
            $table->index(['egi_id', 'status']);
            $table->index(['issued_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa');
    }
};
