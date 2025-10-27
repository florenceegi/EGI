<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing AI Provider Billing Comparison\n";
echo str_repeat('=', 60) . "\n\n";

$service = app(\App\Services\AI\AiProviderBillingService::class);
$comparison = $service->getAllBillingComparison();

echo "✓ Success: " . ($comparison['success'] ? 'YES' : 'NO') . "\n";
echo "✓ Providers checked: " . ($comparison['summary']['total_providers_checked'] ?? 0) . "\n\n";

foreach ($comparison['providers'] ?? [] as $provider => $data) {
    echo strtoupper($provider) . ":\n";
    echo "  Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
    echo "  Internal cost: $" . ($data['internal']['cost'] ?? 0) . "\n";
    echo "  Internal requests: " . ($data['internal']['requests'] ?? 0) . "\n";

    if (!$data['success']) {
        echo "  Provider API: N/D (not available)\n";
        echo "  Error: " . ($data['error'] ?? 'Unknown') . "\n";
        if (isset($data['manual_check_url'])) {
            echo "  Manual check URL: " . $data['manual_check_url'] . "\n";
        }
    } else {
        echo "  Provider cost: $" . ($data['provider_api']['cost'] ?? 0) . "\n";
        echo "  Status: " . ($data['comparison']['status'] ?? 'UNKNOWN') . "\n";
        echo "  Discrepancy: $" . abs($data['comparison']['discrepancy_usd'] ?? 0) . " (" . ($data['comparison']['discrepancy_percentage'] ?? 0) . "%)\n";
    }
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
echo "✅ Test completed\n";
