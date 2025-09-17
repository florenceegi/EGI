<?php
// Simple test to check if temporal filters work

require_once 'bootstrap/app.php';

$app = new Illuminate\Foundation\Application(dirname(__FILE__));

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing temporal filters ===\n";

// Test with different periods to see if values change
$periods = ['day', 'week', 'month', 'all'];

$user = App\Models\User::first();
if (!$user) {
    echo "No users found!\n";
    exit;
}

echo "Testing for user: {$user->email}\n\n";

// Test reservations directly
foreach ($periods as $period) {
    echo "Period: $period\n";

    // Test the date range helper
    $service = new ReflectionClass('App\Services\StatisticsService');
    $method = $service->getMethod('getDateRangeForPeriod');
    $method->setAccessible(true);

    $serviceInstance = new App\Services\StatisticsService($user, app('App\Supports\UltraLogManager'));
    $dateRange = $method->invoke($serviceInstance, $period);

    if ($dateRange) {
        echo "  Date range: {$dateRange['start']} to {$dateRange['end']}\n";

        // Count reservations in this period
        $count = App\Models\Reservation::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'active')
            ->where('is_current', 1)
            ->count();
        echo "  Reservations in period: $count\n";
    } else {
        echo "  No date filter (all time)\n";
        $count = App\Models\Reservation::where('status', 'active')
            ->where('is_current', 1)
            ->count();
        echo "  All reservations: $count\n";
    }
    echo "\n";
}
