<?php
use App\Models\PaymentDistribution;
use App\Models\Wallet;
use App\Models\EgiBlockchain;
use App\Enums\PaymentDistribution\DistributionStatusEnum;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// CONFIGURATION FOR MINT 34
$egiBlockchainId = 34;
$paymentIntentId = 'pi_3SmLfsP4QQlp6ZtM02ms36ts';
$transferId = 'tr_3SmLfsP4QQlp6ZtM0htLzSe0';
$amountEur = 30368.35;
$amountCents = 3036835;
$destinationAcct = 'acct_1SbPAs1Utx7G8oHh';

// Check if already exists
$count = PaymentDistribution::where('egi_blockchain_id', $egiBlockchainId)->count();
if ($count > 0) {
    echo "Record already exists. Skipping.\n";
    exit;
}

// Find Wallet for Company (Creator)
// We need to find the wallet that matches the destination account OR the company logic.
// Usually Owner/Creator.
$blockchain = EgiBlockchain::with('egi.collection')->find($egiBlockchainId);
if (!$blockchain) die("Blockchain record not found.\n");

$egi = $blockchain->egi;
$collection = $egi->collection;
$ownerId = $egi->user_id; // Creator

// Find wallet for this user
$wallet = Wallet::where('user_id', $ownerId)->first();
if (!$wallet) die("Wallet not found for user $ownerId\n");

echo "Creating Record for Mint 34...\n";

PaymentDistribution::create([
    'source_type' => 'mint',
    'egi_id' => $egi->id,
    'egi_blockchain_id' => $egiBlockchainId,
    'collection_id' => $collection->id,
    'payment_intent_id' => $paymentIntentId,
    'user_id' => $ownerId,
    'wallet_id' => $wallet->id,
    'user_type' => 'creator', // fallback
    'platform_role' => 'company', // Assuming company share
    'percentage' => 100.00, // Approx
    'amount_eur' => $amountEur,
    'amount_cents' => $amountCents,
    'distribution_status' => DistributionStatusEnum::COMPLETED,
    'transfer_id' => $transferId,
    'stripe_destination' => $destinationAcct,
    'status' => 'completed', // Legacy
    'completed_at' => now(),
    'processed_at' => now(),
    'metadata' => [
        'reconciled_manually' => true,
        'original_error' => 'Rollback after transfer',
    ]
]);

echo "Success.\n";
