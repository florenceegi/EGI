<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Blockchain Integration)
 * @date 2025-10-07
 * @purpose Create egi_blockchain table for NFT/ASA blockchain data (1:1 with egis)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates egi_blockchain table for storing blockchain-related data
     * for EGI tokens. Maintains 1:1 relationship with egis table.
     * MiCA-SAFE compliant: no crypto custody, only minting/anchoring.
     */
    public function up(): void {
        Schema::create('egi_blockchain', function (Blueprint $table) {
            $table->id();

            // === CORE RELATIONSHIP ===
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('Link to EGI record (1:1 relationship)');

            // === BLOCKCHAIN DATA ===
            $table->string('asa_id')->nullable()
                ->comment('Algorand Standard Asset ID after minting');
            $table->string('anchor_hash')->nullable()
                ->comment('Blockchain anchor hash for certificate verification');
            $table->string('blockchain_tx_id')->nullable()
                ->comment('Blockchain transaction ID for minting');
            $table->string('platform_wallet')
                ->comment('Treasury wallet address holding the asset');

            // === PAYMENT DATA (FIAT ONLY - MiCA-SAFE) ===
            $table->enum('payment_method', ['stripe', 'paypal', 'bank_transfer', 'mock'])
                ->default('mock')
                ->comment('FIAT payment method used');
            $table->string('psp_provider')->nullable()
                ->comment('Payment Service Provider identifier');
            $table->string('payment_reference')->nullable()
                ->comment('PSP transaction reference');
            $table->decimal('paid_amount', 10, 2)->nullable()
                ->comment('Amount paid in FIAT currency');
            $table->string('paid_currency', 3)->default('EUR')
                ->comment('FIAT currency code (EUR, USD, etc.)');

            // === OWNERSHIP DATA ===
            $table->enum('ownership_type', ['treasury', 'wallet'])
                ->default('treasury')
                ->comment('Asset location: treasury or transferred to user wallet');
            $table->string('buyer_wallet')->nullable()
                ->comment('User wallet address (if transferred)');
            $table->foreignId('buyer_user_id')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who purchased the EGI');

            // === CERTIFICATE DATA ===
            $table->string('certificate_uuid', 36)->unique()
                ->comment('Unique certificate identifier for verification');
            $table->string('certificate_path')->nullable()
                ->comment('File storage path for generated certificate PDF');
            $table->string('verification_url')->nullable()
                ->comment('Public URL for certificate verification');

            // === RESERVATION LINK ===
            $table->foreignId('reservation_id')->nullable()
                ->constrained('reservations')
                ->onDelete('set null')
                ->comment('Link to original reservation (if came from reservation)');

            // === MINTING STATUS ===
            $table->enum('mint_status', [
                'unminted',
                'minting_queued',
                'minting',
                'minted',
                'failed'
            ])->default('unminted')
                ->comment('Current minting status');
            $table->timestamp('minted_at')->nullable()
                ->comment('Timestamp when successfully minted');
            $table->text('mint_error')->nullable()
                ->comment('Error message if minting failed');

            // === V2 CRYPTO PAYMENTS (FUTURE - CASP/EMI LICENSED) ===
            $table->json('merchant_psp_config')->nullable()
                ->comment('Merchant PSP configuration for crypto payments (V2)');
            $table->string('crypto_payment_reference')->nullable()
                ->comment('Crypto payment reference (V2)');
            $table->boolean('supports_crypto_payments')->default(false)
                ->comment('Flag for crypto payment support (V2)');

            $table->timestamps();

            // === INDEXES FOR PERFORMANCE ===
            $table->index('egi_id', 'idx_egi_blockchain_egi_id');
            $table->index('mint_status', 'idx_egi_blockchain_mint_status');
            $table->index('buyer_user_id', 'idx_egi_blockchain_buyer_user_id');
            $table->index('reservation_id', 'idx_egi_blockchain_reservation_id');
            $table->index('certificate_uuid', 'idx_egi_blockchain_certificate_uuid');
            $table->index(['mint_status', 'minted_at'], 'idx_egi_blockchain_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('egi_blockchain');
    }
};
