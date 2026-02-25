<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (FlorenceEGI - Egili Credit System)
 * @date 2026-02-25
 * @purpose Add Egili balance tracking to wallets table
 * 
 * RATIONALE (ToS v3.0.0):
 * Egili are internal AI service credits (NOT cryptocurrency, NOT utility tokens).
 * Sourced exclusively via AI Package purchase (FIAT) or merit reward.
 * Stored in wallets table as they are account-bound, non-transferable, MiCA-safe.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Adds Egili balance tracking to wallets:
     * - egili_balance: Current available balance
     * - egili_lifetime_earned: Total earned (all time)
     * - egili_lifetime_spent: Total spent (all time)
     */
    public function up(): void {
        Schema::table('wallets', function (Blueprint $table) {
            // === EGILI BALANCE ===
            $table->unsignedBigInteger('egili_balance')
                ->default(0)
                ->after('metadata')
                ->comment('Saldo Egili disponibili (token utility piattaforma)');

            $table->unsignedBigInteger('egili_lifetime_earned')
                ->default(0)
                ->after('egili_balance')
                ->comment('Totale Egili guadagnati lifetime (immutabile)');

            $table->unsignedBigInteger('egili_lifetime_spent')
                ->default(0)
                ->after('egili_lifetime_earned')
                ->comment('Totale Egili spesi lifetime (immutabile)');

            // === PERFORMANCE INDEXES ===
            $table->index('egili_balance', 'idx_wallets_egili_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('wallets', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('idx_wallets_egili_balance');

            // Drop columns
            $table->dropColumn([
                'egili_balance',
                'egili_lifetime_earned',
                'egili_lifetime_spent',
            ]);
        });
    }
};
