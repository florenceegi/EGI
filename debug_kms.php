<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get wallet's encrypted DEK
$user = \App\Models\User::where('email', 'fabiocherici+yuri@gmail.com')->first();
$wallet = \App\Models\Wallet::where('user_id', $user->id)->first();

echo "=== WALLET DEK INFO ===\n\n";

$dekData = json_decode($wallet->dek_encrypted, true);
echo "DEK Encrypted data:\n";
print_r($dekData);

echo "\n=== CURRENT KMS CONFIG ===\n\n";
echo "KMS mock_kek from config: " . (config('kms.mock_kek') ? 'SET (' . strlen(config('kms.mock_kek')) . ' chars)' : 'NOT SET') . "\n";
echo "App environment: " . config('app.env') . "\n";
echo "KMS kek_id: " . config('kms.kek_id') . "\n";

// Check if kek_id matches
if (isset($dekData['kek_id'])) {
    echo "\nWallet was encrypted with kek_id: " . $dekData['kek_id'] . "\n";
    echo "Current kek_id: " . config('kms.kek_id') . "\n";
    echo "Match: " . ($dekData['kek_id'] === config('kms.kek_id') ? 'YES' : 'NO') . "\n";
}
