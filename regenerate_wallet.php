<?php

/**
 * Script per rigenerare il wallet di un utente con il nuovo mock KEK
 *
 * ATTENZIONE: Questo script:
 * 1. Elimina il wallet esistente (seed irrecuperabile)
 * 2. Crea un nuovo wallet con la nuova KEK
 *
 * Usare SOLO in ambiente di test!
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\WalletProvisioningService;

$email = $argv[1] ?? 'fabiocherici+yuri@gmail.com';

echo "=== WALLET REGENERATION SCRIPT ===\n\n";
echo "User: $email\n";

// Get user
$user = User::where('email', $email)->first();
if (!$user) {
    die("User not found: $email\n");
}

echo "User ID: {$user->id}\n";

// Get existing wallet
$wallet = Wallet::where('user_id', $user->id)->first();
if ($wallet) {
    echo "Existing wallet ID: {$wallet->id}\n";
    echo "Existing address: {$wallet->wallet}\n";
    echo "Has encrypted seed: " . ($wallet->secret_ciphertext ? 'YES' : 'NO') . "\n";

    // Backup info
    $oldAddress = $wallet->wallet;
    $collectionId = $wallet->collection_id;

    // Delete old wallet
    echo "\nDeleting old wallet...\n";
    $wallet->delete();
    echo "Old wallet deleted.\n";
} else {
    echo "No existing wallet found.\n";
    $collectionId = null;
}

// Verify new mock KEK is loaded
$mockKek = config('kms.mock_kek');
if (!$mockKek) {
    die("ERROR: KMS mock_kek not configured! Add KMS_MOCK_KEK to .env\n");
}
echo "\nMock KEK configured: YES (" . strlen($mockKek) . " chars)\n";

// Create new wallet
echo "\nCreating new wallet...\n";

try {
    $walletService = app(WalletProvisioningService::class);
    $newWallet = $walletService->provisionWallet($user->id, $collectionId);

    echo "\n=== NEW WALLET CREATED ===\n";
    echo "Wallet ID: {$newWallet->id}\n";
    echo "Address: {$newWallet->wallet}\n";
    echo "Has encrypted seed: " . ($newWallet->secret_ciphertext ? 'YES' : 'NO') . "\n";

    // Test decryption
    echo "\nTesting decryption...\n";
    try {
        $mnemonic = $walletService->retrieveMnemonic($newWallet, $user);
        $wordCount = count(explode(' ', $mnemonic));
        echo "SUCCESS! Mnemonic decrypted: $wordCount words\n";
        echo "First 3 words: " . implode(' ', array_slice(explode(' ', $mnemonic), 0, 3)) . "...\n";
    } catch (\Exception $e) {
        echo "DECRYPTION FAILED: " . $e->getMessage() . "\n";
    }

    echo "\n=== IMPORTANT ===\n";
    echo "New wallet address: {$newWallet->wallet}\n";
    $oldAddrDisplay = isset($oldAddress) ? $oldAddress : 'N/A';
    echo "The old wallet ({$oldAddrDisplay}) is no longer accessible!\n";
    echo "Fund the new wallet on testnet if needed.\n";
} catch (\Exception $e) {
    echo "WALLET CREATION FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
