<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * P0.5 GAP FIX: DB-based webhook idempotency
     * Replaces cache-only approach with persistent storage
     */
    public function up(): void {
        Schema::create('psp_webhook_events', function (Blueprint $table) {
            $table->id();

            // Provider and event identification
            $table->enum('provider', ['stripe', 'paypal'])->index();
            $table->string('event_id')->comment('Stripe evt_xxx or PayPal event ID');
            $table->string('event_type', 100)->index()->comment('payment_intent.succeeded, etc');

            // Processing state
            $table->enum('status', ['processing', 'processed', 'failed'])
                ->default('processing')
                ->index();

            // Full webhook payload for debugging/replay
            $table->json('payload')->nullable();

            // Processing result
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);

            // Timestamps
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // CRITICAL: Prevent duplicate processing
            $table->unique(['provider', 'event_id'], 'unique_provider_event');

            // Performance indexes
            $table->index(['status', 'received_at'], 'idx_status_received');
            $table->index(['status', 'received_at'], 'idx_processing_timeout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('psp_webhook_events');
    }
};
