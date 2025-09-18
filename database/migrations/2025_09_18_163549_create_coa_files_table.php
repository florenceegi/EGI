<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: crea migration per tabella coa_files (file collegati ai CoA)
     */
    public function up(): void {
        Schema::create('coa_files', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('coa_id')->constrained('coa')->onDelete('cascade');
            $table->enum('kind', ['pdf', 'scan_signed', 'image_front', 'image_back', 'signature_detail']);
            $table->string('path', 255);
            $table->char('sha256', 64)->comment('SHA-256 hash del file in hex');
            $table->bigInteger('bytes')->nullable()->comment('Dimensione file in bytes');
            $table->timestamp('created_at');

            // Indici per performance
            $table->index(['coa_id', 'kind']);
            $table->index('sha256');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coa_files');
    }
};
