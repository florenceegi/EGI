<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Extend payment_method enum with Egili option
 * 🎯 Purpose: Allow recording Egili-based payments in egi_blockchain table
 * 🛡️ Compliance: Egili is a platform utility token (MiCA-safe)
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE egi_blockchain
            MODIFY COLUMN payment_method ENUM('stripe','paypal','bank_transfer','mock','egili')
            DEFAULT 'mock'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE egi_blockchain
            MODIFY COLUMN payment_method ENUM('stripe','paypal','bank_transfer','mock')
            DEFAULT 'mock'
        ");
    }
};

