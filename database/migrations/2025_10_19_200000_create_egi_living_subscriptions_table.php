<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Dual Architecture)
 * @date 2025-10-19
 * @purpose Create egi_living_subscriptions table for premium EGI Vivente subscriptions
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates table to track premium EGI Vivente subscriptions.
     * Each subscription enables AI-powered features for one EGI.
     * MiCA-SAFE: FIAT payments only, no crypto custody.
     */
    public function up(): void
    {
        Schema::create('egi_living_subscriptions', function (Blueprint $table) {
            $table->id();

            // === CORE RELATIONSHIP ===
            $table->foreignId('egi_id')
                ->constrained('egis')
                ->onDelete('cascade')
                ->comment('EGI this subscription applies to');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who owns/purchased the subscription');

            // === SUBSCRIPTION STATUS ===
            $table->enum('status', [
                'pending_payment',
                'active',
                'suspended',
                'cancelled',
                'expired'
            ])->default('pending_payment')
                ->comment('Current subscription status');

            // === SUBSCRIPTION TYPE ===
            $table->enum('plan_type', [
                'one_time',      // Single activation payment
                'monthly',       // Monthly recurring (future)
                'yearly',        // Yearly recurring (future)
                'lifetime'       // Lifetime access (premium)
            ])->default('one_time')
                ->comment('Subscription plan type');

            // === PAYMENT DATA (FIAT ONLY - MiCA-SAFE) ===
            $table->decimal('paid_amount', 10, 2)
                ->comment('Amount paid in FIAT currency');
            $table->string('paid_currency', 3)->default('EUR')
                ->comment('FIAT currency code (EUR, USD, etc.)');
            $table->enum('payment_method', ['stripe', 'paypal', 'bank_transfer'])
                ->comment('FIAT payment method used');
            $table->string('payment_reference')->nullable()
                ->comment('PSP transaction reference');
            $table->timestamp('paid_at')->nullable()
                ->comment('When payment was completed');

            // === SUBSCRIPTION VALIDITY ===
            $table->timestamp('activated_at')->nullable()
                ->comment('When subscription was activated');
            $table->timestamp('expires_at')->nullable()
                ->comment('When subscription expires (NULL = lifetime)');
            $table->timestamp('cancelled_at')->nullable()
                ->comment('When subscription was cancelled by user');

            // === AI FEATURES CONFIGURATION ===
            $table->json('enabled_features')->nullable()
                ->comment('JSON array of enabled AI features (curator, promoter, etc.)');

            $table->integer('ai_analysis_interval')->default(86400)
                ->comment('Seconds between AI analysis triggers (default 24h)');

            $table->integer('ai_executions_count')->default(0)
                ->comment('Total number of AI executions performed');

            $table->timestamp('last_ai_execution_at')->nullable()
                ->comment('Last time AI analysis was executed');

            // === METADATA ===
            $table->text('cancellation_reason')->nullable()
                ->comment('User-provided reason for cancellation');

            $table->json('subscription_metadata')->nullable()
                ->comment('Additional metadata (promo codes, discounts, etc.)');

            $table->timestamps();

            // === INDEXES FOR PERFORMANCE ===
            $table->index('egi_id', 'idx_egi_living_sub_egi_id');
            $table->index('user_id', 'idx_egi_living_sub_user_id');
            $table->index('status', 'idx_egi_living_sub_status');
            $table->index(['egi_id', 'status'], 'idx_egi_living_sub_egi_status');
            $table->index(['user_id', 'status'], 'idx_egi_living_sub_user_status');
            $table->index('expires_at', 'idx_egi_living_sub_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egi_living_subscriptions');
    }
};
