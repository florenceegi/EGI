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
        // MySQL: Use MODIFY COLUMN for enum
        // SQLite: Column already created as string, no need to modify
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE egi_blockchain
                MODIFY COLUMN payment_method ENUM('stripe','paypal','bank_transfer','mock','egili')
                DEFAULT 'mock'
            ");
        }
        // For SQLite, the column is already a string and accepts any value
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE egi_blockchain
                MODIFY COLUMN payment_method ENUM('stripe','paypal','bank_transfer','mock')
                DEFAULT 'mock'
            ");
        }
    }
};

