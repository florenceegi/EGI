<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Add platform_role to payment_distributions
 * 🎯 Purpose: Store wallet platform_role for accurate payment distribution display
 * 🛡️ Context: platform_role comes from wallets table, NOT from users.usertype
 * 
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Payment Distribution Fix)
 * @date 2025-10-17
 * 
 * Why: 
 * - Collection wallets have platform_role (Natan, EPP, Frangette, Creator)
 * - This is the CORRECT role for payment distribution UI
 * - user_type (from users table) is account type, NOT payment role
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Add platform_role field (from wallets table)
            $table->string('platform_role', 50)
                ->nullable()
                ->after('user_type')
                ->comment('Wallet platform role (Natan, EPP, Frangette, Creator) - SOURCE OF TRUTH for display');
            
            // Add wallet_id foreign key
            $table->foreignId('wallet_id')
                ->nullable()
                ->after('user_id')
                ->constrained('wallets')
                ->nullOnDelete()
                ->comment('Reference to wallet that receives this distribution');
            
            // Add index for performance
            $table->index('platform_role', 'idx_payment_dist_platform_role');
            $table->index('wallet_id', 'idx_payment_dist_wallet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_distributions', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_payment_dist_platform_role');
            $table->dropIndex('idx_payment_dist_wallet_id');
            
            // Drop foreign key
            $table->dropForeign(['wallet_id']);
            
            // Drop columns
            $table->dropColumn(['platform_role', 'wallet_id']);
        });
    }
};
