<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds 'pending_checkout' to mint_status enum for EgiBlockchain.
     * This status is used when a user initiates a Stripe Checkout payment
     * but hasn't completed it yet (redirected to Stripe).
     */
    public function up(): void {
        // For PostgreSQL: ALTER TYPE to add new enum value
        // The CHECK constraint needs to be updated
        DB::statement("ALTER TABLE egi_blockchain DROP CONSTRAINT IF EXISTS egi_blockchain_mint_status_check");

        DB::statement("ALTER TABLE egi_blockchain ADD CONSTRAINT egi_blockchain_mint_status_check CHECK (mint_status IN ('unminted', 'minting_queued', 'minting', 'minted', 'failed', 'pending_checkout'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // First update any pending_checkout records to unminted
        DB::table('egi_blockchain')
            ->where('mint_status', 'pending_checkout')
            ->update(['mint_status' => 'unminted']);

        // Remove and recreate constraint without pending_checkout
        DB::statement("ALTER TABLE egi_blockchain DROP CONSTRAINT IF EXISTS egi_blockchain_mint_status_check");

        DB::statement("ALTER TABLE egi_blockchain ADD CONSTRAINT egi_blockchain_mint_status_check CHECK (mint_status IN ('unminted', 'minting_queued', 'minting', 'minted', 'failed'))");
    }
};
