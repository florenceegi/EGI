<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('coa', function (Blueprint $table) {
            // Cryptographic signature fields
            $table->string('verification_hash', 64)->nullable()->comment('SHA-256 hash for public verification');
            $table->string('integrity_hash', 64)->nullable()->comment('SHA-256 hash for tamper detection');
            $table->json('signature_data')->nullable()->comment('Detailed signature information');

            // Certificate metadata
            $table->text('notes')->nullable()->comment('Optional notes about the certificate');
            $table->timestamp('expires_at')->nullable()->comment('Certificate expiration date');
            $table->json('metadata')->nullable()->comment('Additional certificate metadata');

            // QR code for quick verification
            $table->string('qr_code_data', 512)->nullable()->comment('QR code verification data');

            // Indexes for performance
            $table->index('verification_hash');
            $table->index('integrity_hash');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('coa', function (Blueprint $table) {
            $table->dropIndex(['verification_hash']);
            $table->dropIndex(['integrity_hash']);
            $table->dropIndex(['expires_at']);

            $table->dropColumn([
                'verification_hash',
                'integrity_hash',
                'signature_data',
                'notes',
                'expires_at',
                'metadata',
                'qr_code_data'
            ]);
        });
    }
};
