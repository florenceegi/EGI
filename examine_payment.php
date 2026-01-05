<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blockchainId = 26;
$blockchain = \App\Models\EgiBlockchain::find($blockchainId);

if (!$blockchain) {
    echo "Blockchain record $blockchainId not found.\n";
    exit(1);
}

$paymentIntentId = $blockchain->payment_reference;
echo "Examining Blockchain #$blockchainId - Payment Intent: $paymentIntentId\n";

try {
    $stripe = new \Stripe\StripeClient(config('algorand.payments.stripe.secret_key'));
    $intent = $stripe->paymentIntents->retrieve($paymentIntentId);
    
    $amountCents = $intent->amount;
    $amountEur = $amountCents / 100;
    
    echo "--- STRIPE DATA ---\n";
    echo "Status: " . $intent->status . "\n";
    echo "Amount Gross: " . $amountEur . " " . strtoupper($intent->currency) . "\n";
    
    $chargeId = $intent->latest_charge;
    $fee = 0;
    $net = $amountEur;
    
    if ($chargeId) {
        $charge = $stripe->charges->retrieve($chargeId);
        if ($charge->balance_transaction) {
            $bt = $stripe->balanceTransactions->retrieve($charge->balance_transaction);
            $fee = ($bt->fee / 100);
            $net = ($bt->net / 100);
            echo "Stripe Fee: " . $fee . " EUR\n";
            echo "Net from Stripe: " . $net . " EUR\n";
        } else {
             echo "Balance Transaction not found yet (might be pending)\n";
        }
    }
    
    $platformFeeConfig = config('egi.fees.platform_fee_percentage', 0.5);
    $platformFee = $amountEur * ($platformFeeConfig / 100);
    $distributable = $net - $platformFee;
    
    echo "--- CALCULATION ---\n";
    echo "Platform Fee ($platformFeeConfig%): " . $platformFee . " EUR\n";
    echo "Distributable to Split: " . $distributable . " EUR\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
