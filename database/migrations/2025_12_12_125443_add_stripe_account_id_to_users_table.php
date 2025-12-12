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
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable()->index()->after('wallet_balance')
                ->comment('Stripe Connect Account ID associated with the user');
        });

        // Data Migration: Copy existing IDs from Wallets
        // We prioritize the most recently updated wallet for each user
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $wallet = DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->whereNotNull('stripe_account_id')
                    ->where('stripe_account_id', '!=', '')
                    ->orderByDesc('updated_at')
                    ->first();

                if ($wallet) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['stripe_account_id' => $wallet->stripe_account_id]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_account_id');
        });
    }
};
