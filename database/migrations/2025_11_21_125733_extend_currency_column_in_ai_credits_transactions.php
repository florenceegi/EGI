<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Collection Subscription)
 * @date 2025-11-21
 * @purpose Extend currency column to support 'EGILI' (5 chars) instead of only 3-char codes
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Estende la colonna currency da VARCHAR(3) a VARCHAR(10)
     * per supportare 'EGILI' e altre valute future
     */
    public function up(): void {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("
                ALTER TABLE ai_credits_transactions 
                MODIFY COLUMN currency VARCHAR(10) DEFAULT 'EUR' COMMENT 'Valuta pagamento'
            ");
        } elseif ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE ai_credits_transactions 
                ALTER COLUMN currency TYPE VARCHAR(10),
                ALTER COLUMN currency SET DEFAULT 'EUR'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("
                ALTER TABLE ai_credits_transactions 
                MODIFY COLUMN currency VARCHAR(3) DEFAULT 'EUR' COMMENT 'Valuta pagamento'
            ");
        } elseif ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE ai_credits_transactions 
                ALTER COLUMN currency TYPE VARCHAR(3),
                ALTER COLUMN currency SET DEFAULT 'EUR'
            ");
        }
    }
};
