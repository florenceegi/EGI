<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Rebind/Secondary Market)
 * @date 2025-12-11
 * @purpose Extend payment_distributions for rebind (secondary market) tracking
 *
 * This migration adds fields to track ownership transfers in the secondary market:
 * - seller_user_id: The user selling the EGI (NULL for primary mint)
 * - buyer_user_id: The user purchasing the EGI
 * - sale_price: Total sale price (for reconstructing ownership history)
 *
 * The source_type enum is extended to include 'rebind' for secondary market sales.
 * This allows payment_distributions to be the SINGLE SOURCE OF TRUTH for:
 * - Payment distribution tracking
 * - Ownership transfer history
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // === OWNERSHIP TRANSFER TRACKING ===
            $table->foreignId('seller_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User selling the EGI (NULL for primary mint)');

            $table->foreignId('buyer_user_id')
                ->nullable()
                ->after('seller_user_id')
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User purchasing the EGI');

            $table->decimal('sale_price', 10, 2)
                ->nullable()
                ->after('buyer_user_id')
                ->comment('Total sale price for this transaction (for ownership history)');

            // === INDEXES FOR PERFORMANCE ===
            $table->index(['seller_user_id'], 'idx_payment_dist_seller');
            $table->index(['buyer_user_id'], 'idx_payment_dist_buyer');
            // Note: idx_payment_dist_egi_source already exists from previous migration
        });

        // Note: source_type is stored as VARCHAR in PostgreSQL, not a true ENUM type.
        // The 'rebind' value is handled at application level (PaymentDistribution model).
        // No database-level enum modification needed.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_payment_dist_seller');
            $table->dropIndex('idx_payment_dist_buyer');

            // Drop foreign keys and columns
            $table->dropForeign(['seller_user_id']);
            $table->dropForeign(['buyer_user_id']);
            $table->dropColumn([
                'seller_user_id',
                'buyer_user_id',
                'sale_price',
            ]);
        });

        // Note: No enum rollback needed - source_type is VARCHAR
    }
};
