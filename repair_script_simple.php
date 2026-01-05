<?php
use App\Models\EgiBlockchain;
use App\Services\Payment\StripeRealPaymentService;
use App\Services\Payment\StripePaymentSplitService;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$egiBlockchainId = 35; 
$blockchain = EgiBlockchain::find($egiBlockchainId);

if (!$blockchain) {
    echo "Blockchain record $egiBlockchainId not found.\n";
    exit;
}

$metadata = $blockchain->metadata ?? [];
$paymentIntentId = $metadata['payment_reference'] ?? $metadata['client_secret'] ?? null;

if ($paymentIntentId && strpos($paymentIntentId, '_secret_') !== false) {
    $paymentIntentId = explode('_secret_', $paymentIntentId)[0];
}

if (!$paymentIntentId) {
    echo "No PaymentIntent ID found.\n";
    exit;
}

echo "Found PaymentIntent: $paymentIntentId\n";

try {
    // Service
    $splitService = $app->make(StripePaymentSplitService::class);
    
    // Stripe Client
    $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

    echo "Invoking Service...\n";
    // This method handles ALL calculations and DB updates
    $result = $splitService->evaluateAndSplitPayment($paymentIntent);
    
    print_r($result);
    echo "\nSUCCESS.\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
