<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Add 'pending_checkout' to payment_status enum
 *
 * Purpose: Support Stripe Checkout flow where payment is pending
 * until user completes checkout on Stripe's hosted page
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: Drop and recreate the CHECK constraint with new value
            DB::statement("ALTER TABLE egili_merchant_purchases DROP CONSTRAINT IF EXISTS egili_merchant_purchases_payment_status_check");
            DB::statement("ALTER TABLE egili_merchant_purchases ADD CONSTRAINT egili_merchant_purchases_payment_status_check CHECK (payment_status::text = ANY (ARRAY['pending'::text, 'pending_checkout'::text, 'completed'::text, 'failed'::text, 'refunded'::text]))");
        } elseif (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            // MySQL/MariaDB: Update ENUM definition
            DB::statement("ALTER TABLE egili_merchant_purchases MODIFY COLUMN payment_status ENUM('pending', 'pending_checkout', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        if (DB::getDriverName() === 'pgsql') {
            // Revert to original constraint (without pending_checkout)
            DB::statement("ALTER TABLE egili_merchant_purchases DROP CONSTRAINT IF EXISTS egili_merchant_purchases_payment_status_check");
            DB::statement("ALTER TABLE egili_merchant_purchases ADD CONSTRAINT egili_merchant_purchases_payment_status_check CHECK (payment_status::text = ANY (ARRAY['pending'::text, 'completed'::text, 'failed'::text, 'refunded'::text]))");
        } elseif (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            // Revert ENUM definition
            DB::statement("ALTER TABLE egili_merchant_purchases MODIFY COLUMN payment_status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
