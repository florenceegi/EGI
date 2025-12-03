<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add IPFS CID to EGIs table
 * 🎯 Purpose: Store IPFS Content Identifier for original images
 * 🧱 Core Logic: Nullable string to store CID returned by Pinata IPFS pinning
 * 
 * @package FlorenceEGI\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-03
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds ipfs_cid column to store the IPFS Content Identifier
     * for the original image uploaded to IPFS via Pinata.
     * Nullable because existing EGIs won't have IPFS uploads initially.
     */
    public function up(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            // IPFS Content Identifier (CID) - e.g., "bafkreidvbhs33ighmljlvr7zbv2ywwzcmp5adtf4kqvlly67cy56bdtmve"
            $table->string('ipfs_cid', 100)->nullable()->after('file_hash')
                ->comment('IPFS Content Identifier for original image (via Pinata)');
            
            // Index for potential lookups by CID
            $table->index('ipfs_cid', 'egis_ipfs_cid_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropIndex('egis_ipfs_cid_index');
            $table->dropColumn('ipfs_cid');
        });
    }
};
