<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Auction System)
 * @date 2025-10-19
 * @purpose Add auction configuration fields to egis table
 * 
 * AUCTION SYSTEM - NON-INVASIVE EXTENSION:
 * - Mantiene model Reservation intatto
 * - Aggiunge campi configurazione vendita su tabella egis
 * - Creator/Owner configura modalità vendita (fixed_price/auction/not_for_sale)
 * - Backward compatible: default = not_for_sale
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // ===== SALE MODE CONFIGURATION =====
            $table->enum('sale_mode', ['fixed_price', 'auction', 'not_for_sale'])
                ->default('not_for_sale')
                ->after('price')
                ->comment('Sale mode: fixed price, auction, or not for sale');

            // ===== AUCTION CONFIGURATION FIELDS =====
            $table->decimal('auction_minimum_price', 10, 2)
                ->nullable()
                ->after('sale_mode')
                ->comment('Minimum starting price for auction (EUR)');

            $table->timestamp('auction_start')
                ->nullable()
                ->after('auction_minimum_price')
                ->comment('Auction start date and time');

            $table->timestamp('auction_end')
                ->nullable()
                ->after('auction_start')
                ->comment('Auction end date and time');

            $table->boolean('auto_mint_highest')
                ->default(false)
                ->after('auction_end')
                ->comment('Auto-mint to highest bidder when auction ends');

            // ===== INDEXES FOR PERFORMANCE =====
            $table->index('sale_mode', 'idx_egis_sale_mode');
            $table->index(['sale_mode', 'auction_start', 'auction_end'], 'idx_egis_active_auctions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_egis_sale_mode');
            $table->dropIndex('idx_egis_active_auctions');

            // Drop columns
            $table->dropColumn([
                'sale_mode',
                'auction_minimum_price',
                'auction_start',
                'auction_end',
                'auto_mint_highest'
            ]);
        });
    }
};
