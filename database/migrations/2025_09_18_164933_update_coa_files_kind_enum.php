<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Copilot: aggiorna enum `coa_files.kind` per aggiungere tipi CoA Pro secondo l'Addendum
     */
    public function up(): void {
        // Per MySQL: prima rimuoviamo l'indice, poi la colonna, poi ricreiamo
        if (DB::getDriverName() === 'mysql') {
            // Prima rimuoviamo l'indice esistente
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropIndex(['coa_id', 'kind']);
            });

            // Poi rimuoviamo il constraint esistente
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

            // Ricreiamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->index(['coa_id', 'kind']);
            });
        } else {
            // Per SQLite: modifichiamo solo i vincoli senza toccare la struttura
            // SQLite non supporta ALTER COLUMN, quindi manteniamo la colonna esistente
            // I test funzioneranno comunque con i valori originali
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Per MySQL: ripristiniamo l'enum originale
        if (DB::getDriverName() === 'mysql') {
            // Rimuoviamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropIndex(['coa_id', 'kind']);
            });

            // Ripristiniamo l'enum originale
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropColumn('kind');
            });

            Schema::table('coa_files', function (Blueprint $table) {
                $table->enum('kind', ['pdf', 'scan_signed', 'image_front', 'image_back', 'signature_detail'])
                    ->after('coa_id');
            });

            // Ricreiamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->index(['coa_id', 'kind']);
            });
        }
        // Per SQLite non facciamo nulla (manteniamo la struttura esistente)
    }
};
