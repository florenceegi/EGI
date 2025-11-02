<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create egili_merchant_purchases table
 * 
 * Purpose: Track all Egili purchases for merchant reporting and reconciliation
 * 
 * Key Features:
 * - Complete payment tracking (FIAT + Crypto)
 * - Invoice management (FASE 2)
 * - GDPR audit trail integration
 * - Merchant reconciliation data
 * 
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Purchase System)
 * @date 2025-11-02
 * @purpose Merchant purchase tracking for Egili sales
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egili_merchant_purchases', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // User & Order Info
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Buyer user ID');
            
            $table->string('order_reference', 50)
                ->unique()
                ->comment('Unique order reference (EGIL-YYYY-NNNNNN)');
            
            // Purchase Details
            $table->unsignedInteger('egili_amount')
                ->comment('Quantity of Egili purchased');
            
            $table->decimal('egili_unit_price_eur', 10, 4)
                ->comment('EUR price per Egili at purchase time');
            
            $table->decimal('total_price_eur', 10, 2)
                ->comment('Total price in EUR');
            
            // Payment Info
            $table->enum('payment_method', ['fiat', 'crypto'])
                ->comment('Payment method type');
            
            $table->string('payment_provider', 50)
                ->comment('Payment provider (stripe, paypal, coinbase, etc)');
            
            $table->string('payment_external_id', 255)
                ->nullable()
                ->comment('External payment ID from provider');
            
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('pending')
                ->comment('Payment processing status');
            
            // Crypto-specific fields (NULL if FIAT)
            $table->string('crypto_currency', 10)
                ->nullable()
                ->comment('Cryptocurrency used (BTC, ETH, USDC, etc)');
            
            $table->decimal('crypto_amount', 20, 8)
                ->nullable()
                ->comment('Amount of crypto paid');
            
            $table->string('crypto_tx_hash', 255)
                ->nullable()
                ->comment('Blockchain transaction hash');
            
            // Invoice Info (FASE 2)
            $table->string('invoice_number', 50)
                ->nullable()
                ->unique()
                ->comment('Electronic invoice number');
            
            $table->timestamp('invoice_issued_at')
                ->nullable()
                ->comment('Invoice generation timestamp');
            
            $table->string('invoice_pdf_path', 255)
                ->nullable()
                ->comment('Path to generated invoice PDF');
            
            // Metadata
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('Buyer IP address at purchase time');
            
            $table->text('user_agent')
                ->nullable()
                ->comment('Buyer user agent string');
            
            $table->text('notes')
                ->nullable()
                ->comment('Internal notes for merchant');
            
            // Timestamps
            $table->timestamp('purchased_at')
                ->useCurrent()
                ->comment('Purchase initiation timestamp');
            
            $table->timestamp('completed_at')
                ->nullable()
                ->comment('Payment completion timestamp');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'purchased_at'], 'idx_user_purchases');
            $table->index('order_reference', 'idx_order_reference');
            $table->index('payment_status', 'idx_payment_status');
            $table->index('invoice_number', 'idx_invoice_number');
            $table->index('payment_external_id', 'idx_payment_external_id');
            $table->index('purchased_at', 'idx_purchased_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egili_merchant_purchases');
    }
};

