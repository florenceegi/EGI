<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'fabiocherici+yuri@gmail.com')->first();
$wallet = \App\Models\Wallet::where('user_id', $user->id)->first();

echo "=== WALLET ENCRYPTION FIELDS ===\n\n";
echo "User ID: " . $user->id . "\n";
echo "Wallet ID: " . $wallet->id . "\n";
echo "Address: " . $wallet->address . "\n\n";

echo "secret_ciphertext: " . ($wallet->secret_ciphertext ? 'presente (' . strlen($wallet->secret_ciphertext) . ' chars)' : 'VUOTO') . "\n";
echo "secret_nonce: " . ($wallet->secret_nonce ? 'presente (' . strlen($wallet->secret_nonce) . ' chars)' : 'VUOTO') . "\n";
echo "secret_tag: " . ($wallet->secret_tag ? 'presente (' . strlen($wallet->secret_tag) . ' chars)' : 'VUOTO') . "\n";
echo "dek_encrypted: " . ($wallet->dek_encrypted ? 'presente (' . strlen($wallet->dek_encrypted) . ' chars)' : 'VUOTO') . "\n";

// Check all columns in wallet
echo "\n=== ALL WALLET COLUMNS ===\n";
$attributes = $wallet->getAttributes();
foreach ($attributes as $key => $value) {
    if ($value !== null && $key !== 'address') {
        $display = is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
        echo "$key: $display\n";
    }
}
