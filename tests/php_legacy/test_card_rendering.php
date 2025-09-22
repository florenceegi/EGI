<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\Gdpr\DataExportService;
use ReflectionClass;
use App\Models\User;

// Test data con chiavi stringa (che causavano l'errore)
$testData = [
    'consent_1' => [
        'id' => 1,
        'type' => 'Marketing',
        'status' => 'Granted',
        'created_at' => '2025-01-01',
        'updated_at' => '2025-01-02'
    ],
    'consent_2' => [
        'id' => 2,
        'type' => 'Analytics',
        'status' => 'Denied',
        'created_at' => '2025-01-01',
        'updated_at' => '2025-01-02'
    ]
];

try {
    $exportService = new DataExportService();

    // Usa reflection per testare il metodo privato
    $reflection = new ReflectionClass($exportService);
    $method = $reflection->getMethod('renderAsCards');
    $method->setAccessible(true);

    $result = $method->invoke($exportService, $testData);

    echo "✅ Test renderAsCards completato con successo!\n";
    echo "HTML generato:\n";
    echo substr($result, 0, 500) . "...\n";
} catch (Exception $e) {
    echo "❌ Errore durante il test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
