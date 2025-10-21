<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture Hotfix)
 * @date 2025-10-21
 * @purpose Fix egi_type ENUM - Remove 'PreMint' value (pre-mint is managed by pre_mint_mode boolean)
 * @context HOTFIX: egi_type dovrebbe essere NULL | 'ASA' | 'SmartContract'
 *                  Il valore 'PreMint' era erroneamente incluso nell'enum iniziale
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * STEP 1: Converte tutti i record con egi_type='PreMint' → NULL
     * STEP 2: Ricrea la colonna egi_type con enum corretto ['ASA', 'SmartContract']
     */
    public function up(): void
    {
        // === STEP 1: Data Migration ===
        // Converti tutti gli EGI con egi_type='PreMint' a NULL
        DB::table('egis')
            ->where('egi_type', 'PreMint')
            ->update(['egi_type' => null]);

        // === STEP 2: Schema Fix ===
        // Drop indici esistenti prima di droppare la colonna
        Schema::table('egis', function (Blueprint $table) {
            $table->dropIndex('idx_egis_egi_type');
            $table->dropIndex('idx_egis_type_status');
        });

        // Ricrea la colonna con l'enum corretto
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn('egi_type');
        });

        Schema::table('egis', function (Blueprint $table) {
            $table->enum('egi_type', ['ASA', 'SmartContract'])
                ->nullable()
                ->default(null)
                ->after('status')
                ->comment('EGI architecture type: NULL=not minted, ASA=classic, SmartContract=living');

            // Ricrea gli indici
            $table->index('egi_type', 'idx_egis_egi_type');
            $table->index(['egi_type', 'status'], 'idx_egis_type_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Riporta l'enum al valore precedente (con PreMint) per rollback
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropIndex('idx_egis_egi_type');
            $table->dropIndex('idx_egis_type_status');
            $table->dropColumn('egi_type');
        });

        Schema::table('egis', function (Blueprint $table) {
            $table->enum('egi_type', ['ASA', 'SmartContract', 'PreMint'])
                ->nullable()
                ->default(null)
                ->after('status')
                ->comment('EGI architecture type: NULL=not minted, ASA=classic, SmartContract=living, PreMint=legacy');

            $table->index('egi_type', 'idx_egis_egi_type');
            $table->index(['egi_type', 'status'], 'idx_egis_type_status');
        });
    }
};

