<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PA Acts Metadata Migration
 * 
 * Adds columns to `egis` table for PA Acts tokenization system.
 * 
 * STRATEGY:
 * - Use existing `jsonMetadata` column for flexible PA metadata
 * - Add specific indexed columns for filtering/sorting
 * - Add `pa_act_type` enum for type discrimination
 * 
 * PA METADATA STRUCTURE (stored in jsonMetadata):
 * {
 *   "pa_act": {
 *     "protocol_number": "12345/2025",
 *     "protocol_date": "2025-10-04",
 *     "doc_type": "delibera|determina|ordinanza|decreto|atto",
 *     "doc_hash": "sha256_hash_of_pdf",
 *     "signature_validation": {
 *       "signer_name": "Nome Cognome",
 *       "signer_org": "Comune di Firenze",
 *       "cert_issuer": "InfoCert",
 *       "cert_serial": "...",
 *       "timestamp": "2025-10-04T12:00:00Z",
 *       "valid": true
 *     },
 *     "anchor_txid": "algorand_transaction_id",
 *     "anchor_root": "merkle_root_hash",
 *     "merkle_proof": [...],
 *     "public_code": "ABCD1234",
 *     "qr_code_path": "/storage/qr/ABCD1234.png",
 *     "anchored": true,
 *     "anchored_at": "2025-10-04T12:05:00Z"
 *   }
 * }
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('egis', function (Blueprint $table) {
            // PA Act type column (nullable for backward compatibility)
            $table->enum('pa_act_type', [
                'delibera',
                'determina',
                'ordinanza',
                'decreto',
                'atto'
            ])->nullable()->after('type')->index();

            // Protocol number (indexed for fast search)
            $table->string('pa_protocol_number', 50)->nullable()->after('pa_act_type')->index();

            // Protocol date (indexed for date range filters)
            $table->date('pa_protocol_date')->nullable()->after('pa_protocol_number')->index();

            // Public verification code (indexed for fast lookup)
            $table->string('pa_public_code', 20)->nullable()->after('pa_protocol_date')->unique();

            // Anchoring status (for filtering anchored vs pending)
            $table->boolean('pa_anchored')->default(false)->after('pa_public_code')->index();

            // Anchor timestamp
            $table->timestamp('pa_anchored_at')->nullable()->after('pa_anchored')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('egis', function (Blueprint $table) {
            $table->dropColumn([
                'pa_act_type',
                'pa_protocol_number',
                'pa_protocol_date',
                'pa_public_code',
                'pa_anchored',
                'pa_anchored_at'
            ]);
        });
    }
};
