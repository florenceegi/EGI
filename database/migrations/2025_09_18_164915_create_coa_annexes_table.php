<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration `coa_annexes` secondo l'Addendum - versioning degli annessi collegati a un CoA
     */
    public function up(): void {
        Schema::create('coa_annexes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('coa_id')->constrained('coa')->onDelete('cascade');
            $table->enum('code', ['A_PROVENANCE', 'B_CONDITION', 'C_EXHIBITIONS', 'D_PHOTOS'])->comment('Tipologia annesso');
            $table->integer('version')->default(1)->comment('Versione dell\'annesso per questo CoA');
            $table->string('path', 255)->comment('File singolo (PDF) o ZIP');
            $table->string('mime', 127);
            $table->bigInteger('bytes')->nullable()->comment('Dimensione file in bytes');
            $table->char('sha256', 64)->comment('SHA-256 hash del file in hex');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('created_at');

            // Indici e constraint di unicità
            $table->unique(['coa_id', 'code', 'version'], 'unique_coa_annex_version');
            $table->index(['coa_id', 'code']);
            $table->index('sha256');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa_annexes');
    }
};
