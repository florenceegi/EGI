<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Phase 2 Blockchain Integration)
 * @date 2025-10-09
 * @purpose Extend payment_distributions table for dual tracking (Mint + Reservation)
 *
 * AREA 2.1.1: Database Schema Extension
 * - Add source_type to distinguish mint vs reservation distributions
 * - Add blockchain tracking fields (egi_blockchain_id, blockchain_tx_id)
 * - Add indexes for performance on new fields
 * - Maintain backward compatibility with existing reservation-based distributions
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // ===== SOURCE TRACKING =====
            $table->enum('source_type', ['reservation', 'mint', 'transfer'])
                ->default('reservation')
                ->after('reservation_id')
                ->comment('Distribution source: reservation (legacy), mint (Phase 2), transfer (future)');

            // ===== BLOCKCHAIN TRACKING =====
            $table->foreignId('egi_blockchain_id')
                ->nullable()
                ->after('source_type')
                ->constrained('egi_blockchain')
                ->onDelete('set null')
                ->comment('Link to blockchain record for mint-based distributions');

            $table->string('blockchain_tx_id', 255)
                ->nullable()
                ->after('egi_blockchain_id')
                ->comment('Algorand transaction ID for on-chain verification');

            // ===== INDEXES FOR PERFORMANCE =====
            $table->index(['source_type'], 'idx_payment_dist_source_type');
            $table->index(['egi_blockchain_id'], 'idx_payment_dist_blockchain');
            $table->index(['blockchain_tx_id'], 'idx_payment_dist_tx_id');

            // Composite index for common queries (source + status)
            $table->index(['source_type', 'distribution_status'], 'idx_payment_dist_source_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_payment_dist_source_type');
            $table->dropIndex('idx_payment_dist_blockchain');
            $table->dropIndex('idx_payment_dist_tx_id');
            $table->dropIndex('idx_payment_dist_source_status');

            // Drop foreign key and columns
            $table->dropForeign(['egi_blockchain_id']);
            $table->dropColumn([
                'source_type',
                'egi_blockchain_id',
                'blockchain_tx_id'
            ]);
        });
    }
};
