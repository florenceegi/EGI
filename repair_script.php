<?php

use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$egiBlockchainId = 35; // Target ID set dynamically
$blockchain = \App\Models\EgiBlockchain::find($egiBlockchainId);

if (!$blockchain) {
    echo "Blockchain record $egiBlockchainId not found.\n";
    exit;
}

// Find related PaymentIntent from metadata
$metadata = $blockchain->metadata ?? [];
$paymentIntentId = $metadata['payment_reference'] ?? $metadata['client_secret'] ?? null;

// Clean secret if needed
if ($paymentIntentId && strpos($paymentIntentId, '_secret_') !== false) {
    $paymentIntentId = explode('_secret_', $paymentIntentId)[0];
}

if (!$paymentIntentId) {
    echo "No PaymentIntent ID found in metadata.\n";
    exit;
}

echo "Found PaymentIntent: $paymentIntentId\n";

// Manually trigger the service
$stripeService = $app->make(\App\Services\Payment\StripeRealPaymentService::class);
$splitService = $app->make(\App\Services\Payment\StripePaymentSplitService::class);

try {
    $metadata['platform_fee'] = $platformFee;
    $metadata['net_available_stripe'] = $netAmount;
    $metadata['egi_blockchain_id'] = $egiBlockchainId; // Ensure linked

    echo "Triggering split...\n";

    $splitService = app(\App\Services\Payment\StripePaymentSplitService::class);
    $splitService->splitPaymentToWallets(
        $paymentIntentId,
        $collection,
        $distributableAmount,
        $metadata
    );

    echo "Split completed successfully.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
