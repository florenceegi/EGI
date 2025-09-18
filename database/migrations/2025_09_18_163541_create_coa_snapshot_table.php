<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration per tabella coa_snapshot (immutabile, 1:1 con coa)
     */
    public function up(): void {
        Schema::create('coa_snapshot', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('coa_id')->unique()->constrained('coa')->onDelete('cascade');
            $table->json('snapshot_json')->comment('Frozen snapshot of EGI traits at CoA issue time');
            $table->timestamp('created_at');

            // Indice per performance su ricerche di snapshot
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa_snapshot');
    }
};
