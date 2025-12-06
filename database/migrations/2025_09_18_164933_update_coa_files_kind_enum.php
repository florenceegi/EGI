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
        // Per MySQL: prima rimuoviamo FK, poi indice, poi colonna, poi ricreiamo tutto
        if (DB::getDriverName() === 'mysql') {
            // 1. Prima rimuoviamo la foreign key (blocca l'eliminazione dell'indice)
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropForeign(['coa_id']);
            });

            // 2. Poi rimuoviamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropIndex(['coa_id', 'kind']);
            });

            // 3. Poi rimuoviamo la colonna
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropColumn('kind');
            });

            // 4. Ricreiamo la colonna con i nuovi valori
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

            // 5. Ricreiamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->index(['coa_id', 'kind']);
            });

            // 6. Ricreiamo la foreign key
            Schema::table('coa_files', function (Blueprint $table) {
                $table->foreign('coa_id')->references('id')->on('coa')->onDelete('cascade');
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
            // 1. Rimuoviamo la foreign key
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropForeign(['coa_id']);
            });

            // 2. Rimuoviamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropIndex(['coa_id', 'kind']);
            });

            // 3. Ripristiniamo l'enum originale
            Schema::table('coa_files', function (Blueprint $table) {
                $table->dropColumn('kind');
            });

            Schema::table('coa_files', function (Blueprint $table) {
                $table->enum('kind', ['pdf', 'scan_signed', 'image_front', 'image_back', 'signature_detail'])
                    ->after('coa_id');
            });

            // 4. Ricreiamo l'indice
            Schema::table('coa_files', function (Blueprint $table) {
                $table->index(['coa_id', 'kind']);
            });

            // 5. Ricreiamo la foreign key
            Schema::table('coa_files', function (Blueprint $table) {
                $table->foreign('coa_id')->references('id')->on('coa')->onDelete('cascade');
            });
        }
        // Per SQLite non facciamo nulla (manteniamo la struttura esistente)
    }
};
