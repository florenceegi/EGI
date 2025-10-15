<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add blockchain_txid to egis table
 * 🎯 Purpose: Neutral field for blockchain transaction IDs (PA + Creator/Merchant)
 * 🔧 Architecture: Separates ASA ID (token_EGI) from Transaction ID (blockchain_txid)
 *
 * SEMANTIC SEPARATION:
 * - token_EGI:       ASA ID (Algorand Standard Asset) - NFT minting (Creator/Merchant)
 * - blockchain_txid: Transaction ID - Document anchoring (PA) + Creation TX (Creator)
 *
 * USE CASES:
 * - PA Acts:     blockchain_txid stores anchoring transaction ID
 * - Creator NFT: blockchain_txid stores creation transaction ID (optional, for audit)
 * - Explorer:    /tx/{blockchain_txid} - Shows transaction details
 *
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Blockchain TXID Separation)
 * @date 2025-10-15
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // Add blockchain_txid field after pa_anchored_at
            $table->string('blockchain_txid', 52)
                ->nullable()
                ->after('pa_anchored_at')
                ->index()
                ->comment('Blockchain transaction ID (Algorand TXID) - Used by PA for anchoring, Creator for audit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn('blockchain_txid');
        });
    }
};
