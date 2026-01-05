<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * P0.5 GAP FIX: Complete payment_distributions with proper atomicity and reversal tracking
     */
    public function up(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Add missing fields for atomic transfers
            $table->string('idempotency_key')->nullable()->after('payment_intent_id')
                ->comment('Stripe idempotency key for safe retries');
            $table->timestamp('completed_at')->nullable()->after('updated_at')
                ->comment('When transfer completed successfully');
            $table->string('transfer_id')->nullable()->after('idempotency_key')
                ->comment('Stripe Transfer ID');
            $table->string('reversal_id')->nullable()->after('transfer_id')
                ->comment('Stripe Reversal ID');
            $table->timestamp('reversed_at')->nullable()->after('reversal_id')
                ->comment('When reversal occurred');
            $table->text('failure_reason')->nullable()->after('reversed_at')
                ->comment('Why transfer/reversal failed');
            $table->integer('retry_count')->default(0)->after('failure_reason')
                ->comment('Number of retry attempts');
            $table->string('stripe_account_id')->nullable()->after('retry_count')
                ->comment('Destination Stripe Connected Account');
            $table->enum('destination_type', ['stripe_transfer', 'platform_retained'])
                ->default('stripe_transfer')->after('stripe_account_id');
            $table->integer('amount_cents')->nullable()->after('destination_type')
                ->comment('Amount in cents');

            // Add indexes for performance and failure tracking
            $table->index(['distribution_status', 'retry_count'], 'idx_failed_reversals');
            $table->index('idempotency_key', 'idx_idempotency');
            $table->index('transfer_id', 'idx_transfer');
            $table->index('reversal_id', 'idx_reversal');

            // CRITICAL: Prevent duplicate distributions
            $table->unique(['payment_intent_id', 'wallet_id'], 'unique_payment_wallet');
        });

        // Update existing data: map 'confirmed' to 'completed'
        DB::statement("UPDATE payment_distributions SET distribution_status = 'completed' WHERE distribution_status = 'confirmed'");

        // PostgreSQL-compatible enum modification for distribution_status
        DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_distribution_status_check");
        DB::statement("ALTER TABLE payment_distributions ADD CONSTRAINT payment_distributions_distribution_status_check CHECK (distribution_status IN ('pending', 'completed', 'failed', 'reversed', 'reversal_failed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('payment_distributions', function (Blueprint $table) {
            $table->dropUnique('unique_payment_wallet');
            $table->dropIndex('idx_failed_reversals');
            $table->dropIndex('idx_idempotency');
            $table->dropIndex('idx_transfer');
            $table->dropIndex('idx_reversal');

            $table->dropColumn([
                'idempotency_key',
                'completed_at',
                'transfer_id',
                'reversal_id',
                'reversed_at',
                'failure_reason',
                'retry_count',
                'stripe_account_id',
                'destination_type',
                'amount_cents'
            ]);
        });

        // Revert status constraint to original values
        DB::statement("ALTER TABLE payment_distributions DROP CONSTRAINT IF EXISTS payment_distributions_distribution_status_check");
    }
};
