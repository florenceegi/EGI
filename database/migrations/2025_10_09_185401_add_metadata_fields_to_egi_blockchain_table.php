<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Phase 2 Area 5)
 * @date 2025-10-09
 * @purpose Add metadata fields to egi_blockchain table for NFT metadata + display names
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds:
     * - metadata JSON field for OpenSea-compatible metadata
     * - creator_display_name / co_creator_display_name for frozen user names
     * - metadata_ipfs_cid for IPFS metadata storage
     * - metadata_last_updated_at for tracking changes
     */
    public function up(): void {
        Schema::table('egi_blockchain', function (Blueprint $table) {
            // === METADATA JSON ===
            $table->json('metadata')->nullable()->after('anchor_hash')
                ->comment('OpenSea-compatible NFT metadata (traits, properties, attributes)');

            // === DISPLAY NAMES (FROZEN) ===
            $table->string('creator_display_name', 100)->nullable()->after('metadata')
                ->comment('Frozen creator name at EGI creation (immutable)');
            $table->string('co_creator_display_name', 100)->nullable()->after('creator_display_name')
                ->comment('Frozen co-creator/minter name at mint time (immutable)');

            // === IPFS METADATA REFERENCE ===
            $table->string('metadata_ipfs_cid', 255)->nullable()->after('co_creator_display_name')
                ->comment('IPFS CID for uploaded metadata JSON (Area 6 integration)');

            // === METADATA TRACKING ===
            $table->timestamp('metadata_last_updated_at')->nullable()->after('metadata_ipfs_cid')
                ->comment('Timestamp of last metadata update (PRE-mint only)');

            // === INDEXES FOR PERFORMANCE ===
            $table->index('creator_display_name', 'idx_egi_blockchain_creator_display');
            $table->index('co_creator_display_name', 'idx_egi_blockchain_co_creator_display');
            $table->index('metadata_ipfs_cid', 'idx_egi_blockchain_metadata_ipfs_cid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('egi_blockchain', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_egi_blockchain_creator_display');
            $table->dropIndex('idx_egi_blockchain_co_creator_display');
            $table->dropIndex('idx_egi_blockchain_metadata_ipfs_cid');

            // Drop columns
            $table->dropColumn([
                'metadata',
                'creator_display_name',
                'co_creator_display_name',
                'metadata_ipfs_cid',
                'metadata_last_updated_at'
            ]);
        });
    }
};
