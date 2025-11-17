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
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('stripe_account_id', 255)
                ->nullable()
                ->after('wallet')
                ->comment('Stripe Connect account ID for direct merchant settlements');

            $table->string('paypal_merchant_id', 255)
                ->nullable()
                ->after('stripe_account_id')
                ->comment('PayPal merchant ID for direct merchant settlements');

            $table->index('stripe_account_id', 'wallets_stripe_account_idx');
            $table->index('paypal_merchant_id', 'wallets_paypal_merchant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex('wallets_stripe_account_idx');
            $table->dropIndex('wallets_paypal_merchant_idx');
            $table->dropColumn(['stripe_account_id', 'paypal_merchant_id']);
        });
    }
};

