<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the orders table for unified payment tracking.
 *
 * The Order model serves as the central entity for tracking the complete
 * purchase lifecycle: payment → split → mint → delivery.
 *
 * @see /docs/architecture/Contratto Narrativo EGI Claiming.md
 * @see /docs/architecture/PAYMENT_SYSTEM_ARCHITECTURE_v2_4_2.md
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Buyer relationship
            $table->foreignId('buyer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // EGI being purchased (nullable until minted)
            $table->foreignId('egi_id')
                  ->nullable()
                  ->constrained('egis')
                  ->nullOnDelete();
            
            // Collection for the EGI
            $table->foreignId('collection_id')
                  ->constrained('collections')
                  ->cascadeOnDelete();
            
            // Transaction type: purchase, rebind, claim
            $table->string('tx_kind', 20);
            
            // Order status with state machine
            $table->string('status', 20)->default('pending');
            
            // Payment details
            $table->string('payment_type', 20);  // stripe, paypal, bank_transfer, algorand
            $table->string('payment_intent_id')->nullable()->index();
            $table->string('currency', 3)->default('EUR');
            $table->unsignedBigInteger('amount_cents');  // Total amount in cents
            $table->decimal('amount_eur', 10, 2);  // Total amount in EUR
            
            // Split tracking
            $table->boolean('split_executed')->default(false);
            $table->timestamp('split_executed_at')->nullable();
            
            // Minting tracking
            $table->boolean('minted')->default(false);
            $table->timestamp('minted_at')->nullable();
            $table->string('mint_tx_id')->nullable();  // Algorand transaction ID
            
            // Claiming tracking (for claim transactions)
            $table->boolean('claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->string('claim_tx_id')->nullable();  // Algorand claim transaction ID
            
            // Error tracking
            $table->text('failure_reason')->nullable();
            $table->unsignedSmallInteger('retry_count')->default(0);
            
            // Refund/dispute tracking
            $table->boolean('refunded')->default(false);
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_id')->nullable();
            $table->boolean('disputed')->default(false);
            $table->timestamp('disputed_at')->nullable();
            $table->string('dispute_id')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('status', 'orders_status_index');
            $table->index('tx_kind', 'orders_tx_kind_index');
            $table->index(['buyer_id', 'status'], 'orders_buyer_status_index');
            $table->index(['collection_id', 'status'], 'orders_collection_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
