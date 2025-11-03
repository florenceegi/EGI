<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Feature Purchase System)
 * @date 2025-11-01
 * @purpose Create user_feature_purchases table for tracking feature purchases
 * 
 * HYBRID APPROACH:
 * - Links to ai_feature_pricing (pricing catalog)
 * - Links to permissions (auto-grant after purchase)
 * - Tracks payment history and expiration
 * - GDPR compliant audit trail
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_feature_purchases', function (Blueprint $table) {
            $table->id();
            
            // === RELATIONSHIPS ===
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who purchased the feature');
            
            // === FEATURE REFERENCE ===
            $table->string('feature_code', 100)
                ->comment('Feature code from ai_feature_pricing table');
            
            $table->foreign('feature_code')
                ->references('feature_code')
                ->on('ai_feature_pricing')
                ->onDelete('cascade');
            
            // === PERMISSION AUTO-GRANT ===
            $table->string('granted_permission')->nullable()
                ->comment('Permission name auto-granted after purchase (Spatie)');
            
            // === PAYMENT DETAILS ===
            $table->enum('payment_method', [
                'fiat',      // Stripe/PayPal
                'crypto',    // Coinbase Commerce/BitPay/NOWPayments
                'egili',     // Platform utility token
                'free',      // Promotional/Admin grant
            ])->comment('Payment method used');
            
            $table->string('payment_provider')->nullable()
                ->comment('Specific provider (stripe, coinbase_commerce, etc)');
            
            $table->string('payment_transaction_id')->nullable()
                ->comment('External payment ID for reference');
            
            $table->decimal('amount_paid_eur', 10, 2)->nullable()
                ->comment('Amount paid in EUR (if FIAT/Crypto)');
            
            $table->unsignedBigInteger('amount_paid_egili')->nullable()
                ->comment('Amount paid in Egili (if Egili payment)');
            
            // === PURCHASE METADATA ===
            $table->timestamp('purchased_at')
                ->useCurrent()
                ->comment('When feature was purchased');
            
            $table->timestamp('activated_at')->nullable()
                ->comment('When feature was activated (after payment confirmation)');
            
            $table->timestamp('expires_at')->nullable()
                ->comment('Expiration date (NULL = lifetime)');
            
            $table->boolean('is_active')
                ->default(true)
                ->comment('Feature currently active (auto-revoked on expiry)');
            
            $table->boolean('auto_renew')
                ->default(false)
                ->comment('Auto-renew recurring subscriptions');
            
            // === QUANTITY/USAGE (for quota-based features) ===
            $table->unsignedInteger('quantity_purchased')->nullable()
                ->comment('Quantity purchased (e.g., 100 AI credits)');
            
            $table->unsignedInteger('quantity_used')
                ->default(0)
                ->comment('Quantity already consumed');
            
            // === SOURCE TRACKING ===
            $table->string('source_type')->nullable()
                ->comment('Entity that triggered purchase (Egi, Collection, etc)');
            
            $table->unsignedBigInteger('source_id')->nullable()
                ->comment('Entity ID (polymorphic)');
            
            // === ADMIN & PROMO ===
            $table->foreignId('admin_user_id')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Admin who granted free access');
            
            $table->string('promo_code')->nullable()
                ->comment('Promotional code used');
            
            $table->text('admin_notes')->nullable()
                ->comment('Admin notes for manual grants');
            
            // === STATUS & ERROR ===
            $table->enum('status', [
                'active',     // Purchase active and valid
                'expired',    // Expired (time-based)
                'cancelled',  // Cancelled by user
                'refunded',   // Refunded
                'pending',    // Payment pending confirmation
                'failed',     // Payment failed
            ])->default('active')
                ->comment('Purchase status');
            
            $table->text('error_message')->nullable()
                ->comment('Error message if failed');
            
            // === GDPR & AUDIT ===
            $table->string('ip_address', 45)->nullable()
                ->comment('IP address at purchase time');
            
            $table->text('user_agent')->nullable()
                ->comment('User-Agent at purchase time');
            
            $table->json('metadata')->nullable()
                ->comment('Additional context (egi_id, etc)');
            
            $table->timestamps();
            $table->softDeletes();
            
            // === INDEXES ===
            $table->index('user_id', 'idx_user_feature_purchases_user');
            $table->index('feature_code', 'idx_user_feature_purchases_code');
            $table->index(['user_id', 'feature_code'], 'idx_user_feature_user_code');
            $table->index('status', 'idx_user_feature_purchases_status');
            $table->index('expires_at', 'idx_user_feature_purchases_expires');
            $table->index(['source_type', 'source_id'], 'idx_user_feature_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feature_purchases');
    }
};





