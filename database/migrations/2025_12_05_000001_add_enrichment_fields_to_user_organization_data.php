<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add business enrichment fields to user_organization_data
 *
 * New fields for multi-source business data extraction:
 * - pec: Posta Elettronica Certificata
 * - ateco_code: Codice ATECO (e.g., 62.01)
 * - ateco_description: Description of ATECO activity
 * - enrichment_source: Last enrichment source(s)
 * - enriched_at: When data was last enriched
 */
return new class extends Migration {
    public function up(): void {
        Schema::table('user_organization_data', function (Blueprint $table) {
            // PEC (Italian certified email)
            $table->string('pec', 255)->nullable()->after('org_email');

            // ATECO code (Italian business activity classification)
            $table->string('ateco_code', 20)->nullable()->after('rea');

            // ATECO description
            $table->string('ateco_description', 500)->nullable()->after('ateco_code');

            // Track enrichment metadata
            $table->json('enrichment_sources')->nullable()->after('ateco_description');
            $table->timestamp('enriched_at')->nullable()->after('enrichment_sources');

            // Index for ATECO searches
            $table->index('ateco_code');
        });
    }

    public function down(): void {
        Schema::table('user_organization_data', function (Blueprint $table) {
            $table->dropIndex(['ateco_code']);
            $table->dropColumn([
                'pec',
                'ateco_code',
                'ateco_description',
                'enrichment_sources',
                'enriched_at',
            ]);
        });
    }
};
