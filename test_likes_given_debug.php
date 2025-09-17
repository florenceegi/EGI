<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\StatisticsService;
use App\Models\User;

// Setup Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$userId = 23; // sancris@gmail.com
$user = User::find($userId);

if (!$user) {
    echo "User not found!\n";
    exit;
}

$service = new StatisticsService($user, app('ultra.log.manager'));

echo "=== Testing getLikesGivenByUserStatsByPeriod ===\n";
echo "User: {$user->email} (ID: {$userId})\n\n";

$periods = ['day', 'week', 'month', 'year', 'all'];

foreach ($periods as $period) {
    echo "--- Period: $period ---\n";
    try {
        $result = $service->getLikesGivenByUserStatsByPeriod($userId, $period);
        echo "Total given: {$result['total_given']}\n";
        echo "Liked EGIs count: " . count($result['liked_egis']) . "\n";
        echo "Owners count: " . count($result['owners']) . "\n";

        if (!empty($result['owners'])) {
            echo "Owners:\n";
            foreach ($result['owners'] as $owner) {
                echo "  - {$owner['nickname']}: {$owner['likes_count']} likes\n";
            }
        }

        if (!empty($result['liked_egis'])) {
            echo "First 5 EGIs:\n";
            foreach (array_slice($result['liked_egis'], 0, 5) as $index => $egi) {
                echo "  " . ($index + 1) . ". {$egi['title']} - {$egi['owner_name']}\n";
            }
        }

        echo "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
