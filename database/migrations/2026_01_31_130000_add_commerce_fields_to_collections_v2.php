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
        Schema::table('collections', function (Blueprint $table) {
            // P0 Commerce Fields
            $table->string('commercial_status')->default('draft')->after('status')->comment('Enum: draft, configured, commercial_enabled');
            $table->string('delivery_policy')->default('DIGITAL_ONLY')->after('commercial_status')->comment('Enum: DIGITAL_ONLY, PHYSICAL_ALLOWED, PHYSICAL_REQUIRED');
            $table->string('impact_mode')->nullable()->after('delivery_policy')->comment('Enum: EPP, SUBSCRIPTION');
            $table->foreignId('subscription_plan_id')->nullable()->after('impact_mode')->comment('FK for Subscription Mode');
            
            // Indexes for performance
            $table->index('commercial_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropIndex(['commercial_status']);
            $table->dropColumn(['commercial_status', 'delivery_policy', 'impact_mode', 'subscription_plan_id']);
        });
    }
};
