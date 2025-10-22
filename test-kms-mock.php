<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== KMS Provider Test ===\n\n";

try {
    $kms = app(App\Services\Security\KmsClient::class);

    // Show runtime config to avoid ambiguity
    $env = config('app.env');
    $provider = config('kms.provider');
    $kekId = config('kms.kek_id');
    echo "Environment: {$env}\n";
    echo "KMS Provider: {$provider}\n";
    echo "KEK ID: {$kekId}\n\n";

    // Test encryption/decryption cycle
    $plaintext = 'test wallet mnemonic phrase for development';
    echo "Original text: {$plaintext}\n\n";

    echo "🔐 Testing encryption...\n";
    $encrypted = $kms->secureEncrypt($plaintext);
    echo "✅ Encrypted successfully\n";
    echo "   Provider: {$encrypted['encrypted_dek']['provider']}\n";
    echo "   KEK ID: {$encrypted['encrypted_dek']['kek_id']}\n\n";

    echo "🔓 Testing decryption...\n";
    $decrypted = $kms->secureDecrypt($encrypted);
    echo "✅ Decrypted successfully\n";
    echo "   Decrypted text: {$decrypted}\n\n";

    if ($decrypted === $plaintext) {
        echo "✅ ✅ ✅ KMS TEST PASSED! ✅ ✅ ✅\n";
        echo "Envelope encryption working correctly.\n";
    } else {
        echo "❌ TEST FAILED: Decrypted text doesn't match original\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "❌ ERROR (user message): {$e->getMessage()}\n";
    $prev = $e->getPrevious();
    if ($prev) {
        echo "⚙️  Root cause: ".$prev->getMessage()."\n";
        echo "Stack trace (root):\n".$prev->getTraceAsString()."\n";
    } else {
        echo "Stack trace:\n{$e->getTraceAsString()}\n";
    }
    exit(1);
}
