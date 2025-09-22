<?php

use App\Models\User;
use App\Services\StatisticsService;

// Test simple per verificare i filtri temporali
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit;
}

echo "Testing user: {$user->email}\n\n";

$logger = app('App\Supports\UltraLogManager');
$service = new StatisticsService($user, $logger);

// Test periods
$periods = ['day', 'month', 'all'];

foreach ($periods as $period) {
    echo "=== Period: $period ===\n";

    try {
        $stats = $service->getComprehensiveStats(true, $period);
        echo "Total likes: " . $stats['likes']['total'] . "\n";
        echo "Collection likes: " . $stats['likes']['collections_total'] . "\n";
        echo "EGI likes: " . $stats['likes']['egis_total'] . "\n\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
