<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\StatisticsService;
use App\Models\User;

echo "=== TEST CORRECTED LIKE ANALYTICS ===\n\n";

// Get a user with some activity
$user = User::whereHas('collections')->first();

if (!$user) {
    echo "❌ No users found with collections\n";
    exit(1);
}

echo "🧪 Testing with user: {$user->nickname} (ID: {$user->id})\n\n";

// Test 1: getLikesReceivedStats
echo "1. Testing getLikesReceivedStats...\n";
try {
    $receivedStats = StatisticsService::getLikesReceivedStats($user->id);
    echo "   ✅ Success: Total received = {$receivedStats['total_received']}, Top EGIs = " . count($receivedStats['top_egis']) . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: getWhoLikedUserEgisStats
echo "\n2. Testing getWhoLikedUserEgisStats...\n";
try {
    $whoLikedStats = StatisticsService::getWhoLikedUserEgisStats($user->id);
    echo "   ✅ Success: Total given to user = {$whoLikedStats['total_given']}, Top users = " . count($whoLikedStats['top_users']) . "\n";

    if (count($whoLikedStats['top_users']) > 0) {
        $firstUser = $whoLikedStats['top_users'][0];
        echo "   First user: {$firstUser['nickname']} with {$firstUser['likes_given']} likes\n";
        echo "   Avatar URL: " . ($firstUser['profile_photo_url'] ?? 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: getLikesGivenByUserStats
echo "\n3. Testing getLikesGivenByUserStats...\n";
try {
    $givenStats = StatisticsService::getLikesGivenByUserStats($user->id);
    echo "   ✅ Success: Total given by user = {$givenStats['total_given']}, Liked EGIs = " . count($givenStats['liked_egis']) . ", Owners = " . count($givenStats['owners']) . "\n";

    if (count($givenStats['owners']) > 0) {
        $firstOwner = $givenStats['owners'][0];
        echo "   First owner: {$firstOwner['nickname']} with {$firstOwner['likes_count']} likes received\n";
        echo "   Avatar URL: " . ($firstOwner['profile_photo_url'] ?? 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 4: User profile_photo_url
echo "\n4. Testing User profile_photo_url accessor...\n";
try {
    echo "   User profile photo URL: " . ($user->profile_photo_url ?? 'NULL') . "\n";
    echo "   ✅ Success: profile_photo_url accessible\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";
