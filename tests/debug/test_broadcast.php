<?php

// Test rapido broadcasting - da eseguire via browser
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Events\PriceUpdated;

echo "🚀 Test Broadcasting PriceUpdated\n";

try {
    // Emette un evento di test
    PriceUpdated::dispatch(
        999, // EGI ID di test
        '123.45', // Amount
        'EUR', // Currency  
        now()->toISOString(), // Timestamp
        [
            'is_first_reservation' => true,
            'reservation_count' => 1,
            'activator' => [
                'name' => 'Test User',
                'avatar' => null,
                'is_commissioner' => false,
                'wallet_address' => null
            ],
            'button_state' => 'rilancia'
        ]
    );

    echo "✅ Evento PriceUpdated emesso per EGI ID 999\n";
    echo "💡 Controlla il browser su canale price.999\n";
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
