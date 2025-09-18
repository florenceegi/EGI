<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Copilot: crea migration per tabella egi_traits_version (audit dei traits "vivi")
     */
    public function up(): void
    {
        Schema::create('egi_traits_version', function (Blueprint $table) {
            $table->id();
            $table->foreignId('egi_id')->constrained('egis')->onDelete('cascade');
            $table->integer('version')->default(1);
            $table->json('traits_json')->comment('Snapshot dei traits per versioning');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at');

            // Indici per performance e unicità
            $table->unique(['egi_id', 'version']);
            $table->index(['egi_id', 'created_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_traits_version');
    }
};
