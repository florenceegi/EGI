<?php
// Test temporal filters with a simple script

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

echo "=== Testing Temporal Filters ===\n\n";

$user = App\Models\User::first();
if (!$user) {
    echo "No user found\n";
    exit;
}

echo "Testing for user: {$user->email}\n\n";

try {
    $logger = app('App\Supports\UltraLogManager');
    $service = new App\Services\StatisticsService($user, $logger);

    // Test ALL period
    echo "=== PERIOD: ALL ===\n";
    $statsAll = $service->getComprehensiveStats(true, 'all');
    echo "Reservations total: " . $statsAll['reservations']['total'] . "\n";
    echo "Amount total: " . ($statsAll['amounts']['total_eur'] ?? 0) . "\n\n";

    // Test TODAY period
    echo "=== PERIOD: TODAY ===\n";
    $statsToday = $service->getComprehensiveStats(true, 'day');
    echo "Reservations total: " . $statsToday['reservations']['total'] . "\n";
    echo "Amount total: " . ($statsToday['amounts']['total_eur'] ?? 0) . "\n\n";

    // Test MONTH period
    echo "=== PERIOD: MONTH ===\n";
    $statsMonth = $service->getComprehensiveStats(true, 'month');
    echo "Reservations total: " . $statsMonth['reservations']['total'] . "\n";
    echo "Amount total: " . ($statsMonth['amounts']['total_eur'] ?? 0) . "\n\n";

    echo "If TODAY and MONTH show 0 and ALL shows higher numbers, the filters are working!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
