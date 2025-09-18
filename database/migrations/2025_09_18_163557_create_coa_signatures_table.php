<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration per tabella coa_signatures (tracce di firma)
     */
    public function up(): void {
        Schema::create('coa_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('coa_id')->constrained('coa')->onDelete('cascade');
            $table->enum('kind', ['qes', 'autograph_scan', 'wallet']);
            $table->string('provider', 120)->nullable()->comment('es. Namirial/InfoCert per QES');
            $table->json('payload')->nullable()->comment('manifest QES o metadati');
            $table->string('pubkey', 128)->nullable()->comment('per wallet signature');
            $table->text('signature_base64')->nullable()->comment('firma del digest');
            $table->timestamp('created_at');

            // Indici per performance
            $table->index(['coa_id', 'kind']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa_signatures');
    }
};
