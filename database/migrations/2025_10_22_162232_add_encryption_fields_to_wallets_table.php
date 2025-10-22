<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds encryption fields to wallets table for secure mnemonic storage
     * and IBAN management (for wallets that need FIAT payouts).
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // ═══ ALGORAND WALLET ENCRYPTION ═══
            $table->binary('secret_ciphertext')->nullable()->after('wallet')
                ->comment('Encrypted mnemonic using XChaCha20-Poly1305');

            $table->binary('secret_nonce')->nullable()->after('secret_ciphertext')
                ->comment('Nonce for XChaCha20-Poly1305 encryption');

            $table->binary('secret_tag')->nullable()->after('secret_nonce')
                ->comment('Authentication tag (optional for AES-GCM)');

            $table->text('dek_encrypted')->nullable()->after('secret_tag')
                ->comment('Data Encryption Key encrypted with KMS KEK (JSON)');

            $table->string('cipher_algo', 32)->nullable()->after('dek_encrypted')
                ->comment('Encryption algorithm used (xchacha20poly1305)');

            // ═══ IBAN FIELDS (for FIAT payouts) ═══
            $table->text('iban_encrypted')->nullable()->after('cipher_algo')
                ->comment('Encrypted IBAN for FIAT payouts (Laravel encrypted cast)');

            $table->string('iban_hash', 64)->nullable()->index()->after('iban_encrypted')
                ->comment('SHA-256 hash of IBAN with pepper for uniqueness check');

            $table->string('iban_last4', 8)->nullable()->after('iban_hash')
                ->comment('Last 4 digits of IBAN for UI display');

            // ═══ VERSIONING & TYPE ═══
            $table->integer('version')->default(1)->after('iban_last4')
                ->comment('Wallet schema version for future migrations');

            $table->enum('wallet_type', ['algorand', 'iban', 'both'])->default('algorand')->after('version')
                ->comment('Wallet type: algorand only, iban only, or both');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'secret_ciphertext',
                'secret_nonce',
                'secret_tag',
                'dek_encrypted',
                'cipher_algo',
                'iban_encrypted',
                'iban_hash',
                'iban_last4',
                'version',
                'wallet_type',
            ]);
        });
    }
};
