<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Co-Creator Semantic Clarity)
 * @date 2025-11-28
 * @purpose Add co_creator_id field to egis table for clear role semantics
 *
 * I TRE RUOLI INSCINDIBILI:
 * - user_id: Creator (l'autore originale - IMMUTABILE)
 * - co_creator_id: Co-Creator (chi ha mintato - IMMUTABILE dopo mint)
 * - owner_id: Owner (proprietario commerciale - VARIABILE con vendite)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Step 1: Add co_creator_id column
        Schema::table('egis', function (Blueprint $table) {
            $table->unsignedBigInteger('co_creator_id')
                ->nullable()
                ->after('owner_id')
                ->comment('Co-Creator: chi ha mintato l\'EGI (immutabile dopo mint)');

            $table->index('co_creator_id', 'idx_egis_co_creator_id');
        });

        // Step 2: Add foreign key separately (safer for existing data)
        Schema::table('egis', function (Blueprint $table) {
            $table->foreign('co_creator_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Step 3: Sync existing data from egi_blockchain.buyer_user_id
        // Per gli EGI già mintati, il co_creator è il buyer_user_id
        if (DatabaseHelper::isMysql()) {
            // MySQL syntax with JOIN
            DB::statement("
                UPDATE egis e
                INNER JOIN egi_blockchain eb ON e.id = eb.egi_id
                SET e.co_creator_id = eb.buyer_user_id
                WHERE eb.buyer_user_id IS NOT NULL
                  AND eb.mint_status = 'minted'
            ");
        } else {
            // PostgreSQL/SQLite: use subquery
            DB::statement("
                UPDATE egis
                SET co_creator_id = (
                    SELECT eb.buyer_user_id
                    FROM egi_blockchain eb
                    WHERE eb.egi_id = egis.id
                      AND eb.buyer_user_id IS NOT NULL
                      AND eb.mint_status = 'minted'
                    LIMIT 1
                )
                WHERE EXISTS (
                    SELECT 1 FROM egi_blockchain eb
                    WHERE eb.egi_id = egis.id
                      AND eb.buyer_user_id IS NOT NULL
                      AND eb.mint_status = 'minted'
                )
            ");
        }

        // Log sync results
        $syncedCount = DB::table('egis')
            ->whereNotNull('co_creator_id')
            ->count();

        echo "\n✅ Synced co_creator_id for {$syncedCount} minted EGIs\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropForeign(['co_creator_id']);
            $table->dropIndex('idx_egis_co_creator_id');
            $table->dropColumn('co_creator_id');
        });
    }
};
