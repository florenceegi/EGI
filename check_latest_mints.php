<?php

use App\Models\EgiBlockchain;
use App\Models\PaymentDistribution;
use App\Models\EgiReservationCertificate;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "--- LATEST 5 MINTS ---\n";

$mints = EgiBlockchain::orderBy('id', 'desc')->take(5)->get();

foreach ($mints as $mint) {
    echo "ID: {$mint->id} | Status: {$mint->mint_status} | Created: {$mint->created_at}\n";
    
    // Check Splits
    $splits = PaymentDistribution::where('source_type', 'mint')
        ->where('egi_blockchain_id', $mint->id)
        ->count();
    echo "  -> Splits Count: {$splits}\n";

    // Check Certificate
    $cert = EgiReservationCertificate::where('egi_blockchain_id', $mint->id)->first();
    echo "  -> Certificate: " . ($cert ? "YES ({$cert->certificate_uuid})" : "NO") . "\n";
    
    // Check Metadata (Payment ID)
    $paymentId = $mint->paymentRelease->payment_id ?? 'N/A';
    echo "  -> Payment ID: {$paymentId}\n";
    
    echo "------------------------\n";
}
