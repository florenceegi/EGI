<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration to add fiat wallet types (stripe, paypal) to the wallet_type column.
 * 
 * The wallets table originally had an ENUM for wallet_type which creates a CHECK constraint
 * in PostgreSQL. This migration drops the old constraint and creates a new one with 
 * additional wallet types.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing check constraint (if any)
        DB::statement("
            DO $$ 
            BEGIN
                -- Drop the check constraint if it exists
                IF EXISTS (
                    SELECT 1 FROM pg_constraint 
                    WHERE conname = 'wallets_wallet_type_check'
                ) THEN
                    ALTER TABLE wallets DROP CONSTRAINT wallets_wallet_type_check;
                END IF;
            END $$;
        ");

        // Create a new check constraint with all wallet types
        DB::statement("
            ALTER TABLE wallets ADD CONSTRAINT wallets_wallet_type_check 
            CHECK (wallet_type IN ('algorand', 'algorand_external', 'iban', 'stripe', 'paypal', 'both'))
        ");
    }

    public function down(): void
    {
        // Drop the new constraint
        DB::statement("
            DO $$ 
            BEGIN
                IF EXISTS (
                    SELECT 1 FROM pg_constraint 
                    WHERE conname = 'wallets_wallet_type_check'
                ) THEN
                    ALTER TABLE wallets DROP CONSTRAINT wallets_wallet_type_check;
                END IF;
            END $$;
        ");

        // Restore original constraint (only original types)
        DB::statement("
            ALTER TABLE wallets ADD CONSTRAINT wallets_wallet_type_check 
            CHECK (wallet_type IN ('algorand', 'iban', 'both'))
        ");
    }
};
