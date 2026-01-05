<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blockchainId = 24;
echo "Debugging Payment Breakdown for Blockchain ID: $blockchainId\n";

$countRaw = \Illuminate\Support\Facades\DB::table('payment_distributions')
    ->where('egi_blockchain_id', $blockchainId)
    ->count();
echo "Raw DB Count: $countRaw\n";

$distributions = \App\Models\PaymentDistribution::where('source_type', 'mint')
    ->where('egi_blockchain_id', $blockchainId)
    ->where('amount_eur', '>', 0)
    ->with('user')
    ->get();

echo "Eloquent Collection Count: " . $distributions->count() . "\n";

foreach ($distributions as $dist) {
    echo " - Record ID: " . $dist->id . "\n";
    echo "   Amount: " . $dist->amount_eur . "\n";
    echo "   User ID: " . $dist->user_id . "\n";
    echo "   User Relation: " . ($dist->user ? 'Loaded ('.$dist->user->name.')' : 'NULL') . "\n";
    echo "   Role: " . ($dist->platform_role ?? 'N/A') . "\n";
}

$certificate = \App\Models\EgiReservationCertificate::where('egi_blockchain_id', $blockchainId)->first();
echo "Certificate: " . ($certificate ? 'Exists' : 'NULL') . "\n";
