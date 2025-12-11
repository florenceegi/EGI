<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_subscriptions', function (Blueprint $table) {
            $table->id();
            
            // User owning the subscription
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Polymerphic relation to the subscribed entity (Collection, Tool, etc.)
            $table->nullableMorphs('subscribable');
            
            // Service type identifier (e.g., 'collection_subscription', 'saas_tool_pro')
            $table->string('service_type');
            
            // Subscription status
            $table->enum('status', ['active', 'cancelled', 'payment_failed', 'suspended'])
                  ->default('active');
            
            // Renewal scheduling
            $table->dateTime('next_renewal_at');
            $table->dateTime('last_renewal_at')->nullable();
            
            // Stats & Retry logic
            $table->integer('renewal_count')->default(0);
            $table->integer('failed_attempts')->default(0);
            $table->dateTime('last_failed_at')->nullable();
            
            // Metadata for pricing snapshot or options
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['status', 'next_renewal_at']);
            // $table->index(['subscribable_type', 'subscribable_id']); // Automatically created by nullableMorphs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_subscriptions');
    }
};
