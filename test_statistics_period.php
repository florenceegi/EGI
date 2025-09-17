<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\StatisticsService;
use App\Models\User;
use Ultra\UltraLogManager\UltraLogManager;

// Bootstrap Laravel
$app = new Application(__DIR__);
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

try {
    // Get a test user (assuming user ID 1 exists)
    $user = User::find(1);
    if (!$user) {
        echo "User not found!\n";
        exit(1);
    }

    echo "Testing statistics with different periods for user: {$user->email}\n\n";

    $logger = new UltraLogManager();
    $statisticsService = new StatisticsService($user, $logger);

    // Test different periods
    $periods = ['day', 'week', 'month', 'year', 'all'];

    foreach ($periods as $period) {
        echo "=== Period: $period ===\n";

        try {
            $stats = $statisticsService->getComprehensiveStats(true, $period); // Force refresh

            echo "Total likes: " . $stats['likes']['total'] . "\n";
            echo "Collection likes: " . $stats['likes']['collections_total'] . "\n";
            echo "EGI likes: " . $stats['likes']['egis_total'] . "\n";
            echo "Generated at: " . $stats['generated_at'] . "\n";
            echo "\n";

        } catch (Exception $e) {
            echo "Error for period $period: " . $e->getMessage() . "\n\n";
        }
    }

} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
