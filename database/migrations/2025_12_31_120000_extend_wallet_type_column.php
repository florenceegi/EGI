<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Extend wallet_type column to support multiple wallet types.
 * 
 * Supported types:
 * - algorand: Custodial Algorand wallet (KMS encrypted)
 * - algorand_external: Non-custodial external Algorand wallet
 * - stripe: Stripe Connect account for fiat payments
 * - paypal: PayPal Merchant account
 * - iban: Bank transfer (SEPA)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ensure wallet_type column exists and has correct type
        if (!Schema::hasColumn('wallets', 'wallet_type')) {
            // Column doesn't exist - add it
            Schema::table('wallets', function (Blueprint $table) {
                $table->string('wallet_type', 50)->default('algorand')->after('wallet');
            });
        }

        // Update existing NULL or empty values to 'algorand' (legacy custodial wallets)
        DB::table('wallets')
            ->whereNull('wallet_type')
            ->orWhere('wallet_type', '')
            ->update(['wallet_type' => 'algorand']);

        // Set default for column (PostgreSQL way)
        DB::statement("ALTER TABLE wallets ALTER COLUMN wallet_type SET DEFAULT 'algorand'");

        // Add index for faster filtering by wallet type
        // Using raw SQL to handle index existence check properly
        $indexExists = DB::select("
            SELECT 1 FROM pg_indexes 
            WHERE tablename = 'wallets' 
            AND indexname = 'wallets_wallet_type_index'
        ");

        if (empty($indexExists)) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->index('wallet_type', 'wallets_wallet_type_index');
            });
        }
    }

    public function down(): void
    {
        // Remove default
        DB::statement("ALTER TABLE wallets ALTER COLUMN wallet_type DROP DEFAULT");
        
        // Drop index if exists
        $indexExists = DB::select("
            SELECT 1 FROM pg_indexes 
            WHERE tablename = 'wallets' 
            AND indexname = 'wallets_wallet_type_index'
        ");

        if (!empty($indexExists)) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropIndex('wallets_wallet_type_index');
            });
        }
    }
};
