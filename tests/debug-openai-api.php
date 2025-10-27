<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔍 OpenAI Billing API Debug\n";
echo str_repeat('=', 60) . "\n\n";

$apiKey = config('services.openai.api_key');

if (!$apiKey) {
    echo "❌ No OpenAI API key configured\n";
    exit(1);
}

echo "API Key: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -5) . "\n\n";

// TEST 1: Subscription endpoint
echo "📋 TEST 1: /v1/dashboard/billing/subscription\n";
echo str_repeat('-', 60) . "\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])
        ->timeout(30)
        ->get('https://api.openai.com/v1/dashboard/billing/subscription');

    echo "Status: " . $response->status() . "\n";
    echo "Response:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// TEST 2: Usage endpoint
echo "📊 TEST 2: /v1/dashboard/billing/usage\n";
echo str_repeat('-', 60) . "\n";

$startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
$endDate = Carbon::now()->format('Y-m-d');

echo "Start: $startDate\n";
echo "End: $endDate\n\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])
        ->timeout(30)
        ->get('https://api.openai.com/v1/dashboard/billing/usage', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

    echo "Status: " . $response->status() . "\n";
    echo "Response:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// TEST 3: Try organization endpoint (if exists)
echo "🏢 TEST 3: /v1/organization/usage\n";
echo str_repeat('-', 60) . "\n";

try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])
        ->timeout(30)
        ->get('https://api.openai.com/v1/organization/usage');

    echo "Status: " . $response->status() . "\n";
    echo "Response:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

echo str_repeat('=', 60) . "\n";
echo "✅ Debug completed\n";
