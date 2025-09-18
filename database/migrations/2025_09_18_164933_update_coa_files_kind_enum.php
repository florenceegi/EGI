<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: aggiorna enum `coa_files.kind` per aggiungere tipi CoA Pro secondo l'Addendum
     */
    public function up(): void {
        // Prima rimuoviamo il constraint esistente
        Schema::table('coa_files', function (Blueprint $table) {
            $table->dropColumn('kind');
        });

        // Poi ricreiamo la colonna con i nuovi valori
        Schema::table('coa_files', function (Blueprint $table) {
            $table->enum('kind', [
                'pdf',
                'scan_signed',
                'image_front',
                'image_back',
                'signature_detail',
                // Nuovi tipi CoA Pro
                'core_pdf',      // PDF Core (1 pagina)
                'bundle_pdf',    // PDF completo Core + indice annessi
                'annex_pack'     // ZIP con materiale pesante per annessi
            ])->after('coa_id')->comment('Tipologia file CoA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Ripristiniamo l'enum originale
        Schema::table('coa_files', function (Blueprint $table) {
            $table->dropColumn('kind');
        });

        Schema::table('coa_files', function (Blueprint $table) {
            $table->enum('kind', ['pdf', 'scan_signed', 'image_front', 'image_back', 'signature_detail'])
                ->after('coa_id');
        });
    }
};
