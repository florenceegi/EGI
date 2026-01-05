<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the wallet_destinations table for scalable payment type management.
 *
 * This table implements the "one-to-many" relationship between wallets and payment
 * destinations, allowing each wallet to have multiple payment methods without
 * fixed columns.
 *
 * @see /docs/architecture/Contratto Operativo (PaymentTypes, Wallet Split, Resolver).md
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')
                  ->constrained('wallets')
                  ->cascadeOnDelete();
            
            // Payment type using enum values
            $table->string('payment_type', 20);  // stripe, paypal, bank_transfer, algorand
            
            // Destination identifier (account ID, address, IBAN, etc.)
            // Encrypted for sensitive data (IBAN)
            $table->text('destination_value');
            
            // Whether this destination is verified/active
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Whether this is the primary destination for this payment type
            $table->boolean('is_primary')->default(false);
            
            // Metadata for additional info (e.g., bank name, account holder)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Each wallet can only have one destination per payment type
            $table->unique(['wallet_id', 'payment_type'], 'wallet_payment_type_unique');
            
            // Index for lookups by payment type
            $table->index('payment_type', 'wallet_destinations_payment_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_destinations');
    }
};
