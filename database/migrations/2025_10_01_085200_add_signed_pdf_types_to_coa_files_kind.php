<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Debug CoA Signature)
 * @date 2025-10-01
 * @purpose Aggiunge tipi PDF firmati mancanti all'enum coa_files.kind
 */
return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Aggiunge i tipi PDF firmati che mancavano nell'enum causando errore di troncamento
     */
    public function up(): void {
        // Prima rimuoviamo il constraint esistente
        Schema::table('coa_files', function (Blueprint $table) {
            $table->dropColumn('kind');
        });

        // Poi ricreiamo la colonna con tutti i valori necessari
        Schema::table('coa_files', function (Blueprint $table) {
            $table->enum('kind', [
                // Tipi originali
                'pdf',
                'scan_signed', 
                'image_front',
                'image_back',
                'signature_detail',
                // Tipi CoA Pro
                'core_pdf',
                'bundle_pdf',
                'annex_pack',
                // Tipi PDF firmati (MANCAVANO - CAUSA ERRORE)
                'pdf_signed_author',     // PDF firmato dall'autore
                'pdf_signed_inspector',  // PDF firmato dall'ispettore
                'pdf_signed_ts',         // PDF con timestamp
            ])->after('coa_id')->comment('Tipologia file CoA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Ripristiniamo l'enum precedente
        Schema::table('coa_files', function (Blueprint $table) {
            $table->dropColumn('kind');
        });

        Schema::table('coa_files', function (Blueprint $table) {
            $table->enum('kind', [
                'pdf',
                'scan_signed',
                'image_front', 
                'image_back',
                'signature_detail',
                'core_pdf',
                'bundle_pdf',
                'annex_pack'
            ])->after('coa_id')->comment('Tipologia file CoA');
        });
    }
};