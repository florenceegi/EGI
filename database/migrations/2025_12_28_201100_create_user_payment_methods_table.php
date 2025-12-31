<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates user_payment_methods table for polymorphic payment method storage.
     * Supports both User and Collection as payable entities.
     */
    public function up(): void
    {
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship: payable_type + payable_id
            // Can reference User or Collection
            $table->morphs('payable');
            
            // Payment method type: stripe, egili, bank_transfer
            $table->string('method', 50);
            
            // Is this method enabled for this user/collection?
            $table->boolean('is_enabled')->default(true);
            
            // Is this the default method for this user/collection?
            $table->boolean('is_default')->default(false);
            
            // Method-specific configuration (JSON)
            // For bank_transfer: { "iban": "...", "bic": "...", "holder": "..." }
            // For stripe: { "account_status": "..." } - account_id is on users table
            // For egili: {} - no extra config needed
            // STORED AS ENCRYPTED PAYLOAD (encrypted:array cast)
            $table->longText('config')->nullable();
            
            $table->timestamps();
            
            // Each payable can have each method only once
            $table->unique(['payable_type', 'payable_id', 'method'], 'upm_payable_method_unique');
            
            // Index for efficient lookups
            $table->index(['payable_type', 'payable_id', 'is_enabled'], 'upm_enabled_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
