<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Phase 2 Blockchain Integration)
 * @date 2025-10-09
 * @purpose Make reservation_id nullable for mint-based distributions
 *
 * AREA 2.1.2: Fix Constraints Issue
 * - Change reservation_id from NOT NULL to NULLABLE
 * - Enables mint-based distributions (no reservation required)
 * - Maintains backward compatibility (all existing records have reservation_id)
 * - Application-level validation ensures data integrity
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['reservation_id']);

            // Make reservation_id nullable
            $table->foreignId('reservation_id')
                ->nullable()
                ->change()
                ->comment('Reservation link (NULL for mint-based distributions)');

            // Re-add foreign key with same cascade behavior
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['reservation_id']);

            // Make reservation_id NOT NULL again
            $table->foreignId('reservation_id')
                ->nullable(false)
                ->change()
                ->comment('Collegamento alla prenotazione originale');

            // Re-add foreign key
            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->onDelete('cascade');
        });
    }
};
