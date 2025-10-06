<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Wallet;

/**
 * Migration to fix duplicate 'pending_wallet_address' entries in wallets table.
 * 
 * @package     Database\Migrations
 * @author      Padmin D. Curtis (AI Partner OS3.0)
 * @version     1.0.0 (FlorenceEGI - Wallet Uniqueness Fix)
 * @date        2025-10-06
 * @purpose     Resolve duplicate pending_wallet_address entries by generating unique placeholders
 * 
 * @context     During user registration, multiple users were assigned the same 'pending_wallet_address'
 *              placeholder, causing duplicate wallet violations. This migration fixes existing duplicates
 *              by generating unique addresses for each wallet record.
 * 
 * @rationale   The WalletService now generates unique placeholders (pending_wallet_{user_id}_{collection_id}_{timestamp})
 *              instead of using a fixed 'pending_wallet_address' value. This migration ensures database
 *              consistency by applying the same logic to existing duplicate records.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all wallets with 'pending_wallet_address' placeholder
        $duplicateWallets = Wallet::where('wallet', 'pending_wallet_address')->get();

        if ($duplicateWallets->count() > 0) {
            echo "\n🔧 Found {$duplicateWallets->count()} wallet(s) with 'pending_wallet_address' placeholder.\n";
            
            foreach ($duplicateWallets as $wallet) {
                // Generate unique placeholder using same format as WalletService
                $uniqueAddress = sprintf(
                    'pending_wallet_%d_%d_%s',
                    $wallet->user_id,
                    $wallet->collection_id,
                    microtime(true)
                );

                // Update wallet with unique address
                $wallet->update(['wallet' => $uniqueAddress]);

                echo "✅ Updated Wallet ID {$wallet->id} (User: {$wallet->user_id}, Collection: {$wallet->collection_id})\n";
                echo "   Old: pending_wallet_address → New: {$uniqueAddress}\n";
            }

            echo "\n✨ All duplicate wallet addresses fixed!\n\n";
        } else {
            echo "\n✅ No duplicate 'pending_wallet_address' entries found. Database is clean.\n\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration as we don't track original duplicate state
        echo "\n⚠️  WARNING: This migration cannot be reversed.\n";
        echo "   Original duplicate 'pending_wallet_address' entries were intentionally fixed.\n";
        echo "   Manual intervention required if you need to restore previous state.\n\n";
    }
};
