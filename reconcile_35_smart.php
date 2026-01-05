<?php
use App\Models\PaymentDistribution;
use App\Models\Wallet;
use App\Models\EgiBlockchain;
use App\Enums\PaymentDistribution\DistributionStatusEnum;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// CONFIGURATION FOR MINT 35
$egiBlockchainId = 35;
// Metadata from previous step will give us PI and Transfer ID (if available?)
// Wait, we don't have Transfer ID if verify failed?
// But user said "Stripe log says ok". So Transfer EXISTS in Stripe.
// I need transfer ID. 
// I'll generic search stripe for the PI to get Transfer ID.
// Or just let the script find it?
// Let's use Stripe Client to find the transfer.

$blockchain = EgiBlockchain::find($egiBlockchainId);
if (!$blockchain) die("Blockchain 35 NOT FOUND\n");

$meta = $blockchain->metadata ?? [];
$pi = $meta['payment_reference'] ?? $meta['client_secret'] ?? null;
if (strpos($pi, '_secret_')) $pi = explode('_secret_', $pi)[0];

echo "PI: $pi\n";

$stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
$intent = $stripe->paymentIntents->retrieve($pi);
$charges = $intent->charges->data;
$transferId = null;
$dest = null;

// Find transfer in charges? No, transfer is separate.
// Transfer is linked to PI via transfer_group usually?
// Or we check `transfers` endpoint filtering by transfer_group = 'group_'.$pi?

$transfers = $stripe->transfers->all(['transfer_group' => "group_{$pi}"]);
if (count($transfers->data) > 0) {
    $t = $transfers->data[0];
    $transferId = $t->id;
    $dest = $t->destination;
    $amountCents = $t->amount;
    $amountEur = $amountCents / 100;
    echo "Found Transfer: $transferId ($amountEur EUR)\n";
} else {
    echo "No Transfer found in Stripe. Maybe execution failed before transfer?\n";
    // If no transfer, we should call the Split Service to EXECUTE it.
    // Use the SplitService logic.
    echo "Calling SplitService...\n";
    $service = $app->make(\App\Services\Payment\StripeRealPaymentService::class); // Wrong service?
    $split = $app->make(\App\Services\Payment\StripePaymentSplitService::class);
    $split->evaluateAndSplitPayment($intent);
    echo "Service Execution Finished.\n";
    exit;
}

// If Transfer Exists, Create Record
$egi = $blockchain->egi;
$collection = $egi->collection;
$ownerId = $egi->user_id;
$wallet = Wallet::where('user_id', $ownerId)->first();

PaymentDistribution::create([
    'source_type' => 'mint',
    'egi_id' => $egi->id,
    'egi_blockchain_id' => $egiBlockchainId,
    'collection_id' => $collection->id,
    'payment_intent_id' => $pi,
    'user_id' => $ownerId,
    'wallet_id' => $wallet->id,
    'user_type' => 'creator',
    'platform_role' => 'company',
    'percentage' => 100.00,
    'amount_eur' => $amountEur,
    'amount_cents' => $amountCents,
    'distribution_status' => DistributionStatusEnum::COMPLETED,
    'transfer_id' => $transferId,
    'stripe_destination' => $dest,
    'status' => 'completed',
    'completed_at' => now(),
    'processed_at' => now(),
    'metadata' => ['reconciled' => true]
]);
echo "Record Created.\n";
