<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\StatisticsService;
use App\Models\Like;
use App\Models\User;
use App\Models\Egi;

echo "=== TEST QUERY LIKE ANALYTICS ===\n\n";

// Get a random user with some likes
$userWithLikes = User::whereHas('likes')->first();

if (!$userWithLikes) {
    echo "❌ No users found with likes. Creating test data...\n";
    exit(1);
}

echo "🧪 Testing with user: {$userWithLikes->nickname} (ID: {$userWithLikes->id})\n\n";

// Test basic Like query
echo "1. Testing basic Like model relationships...\n";
$likes = Like::where('user_id', $userWithLikes->id)
    ->where('likeable_type', 'App\Models\Egi')
    ->limit(3)
    ->get();

echo "   Found {$likes->count()} likes\n";

foreach ($likes as $like) {
    echo "   - Like ID: {$like->id}, Likeable Type: {$like->likeable_type}, Likeable ID: {$like->likeable_id}\n";

    // Test relationship
    $egi = $like->likeable;
    if ($egi) {
        echo "     ✅ EGI found: {$egi->title}\n";

        $collection = $egi->collection;
        if ($collection) {
            echo "     ✅ Collection found: {$collection->collection_name}\n";

            $creator = $collection->creator;
            if ($creator) {
                echo "     ✅ Creator found: {$creator->nickname}\n";
            } else {
                echo "     ❌ Creator NOT found\n";
            }
        } else {
            echo "     ❌ Collection NOT found\n";
        }
    } else {
        echo "     ❌ EGI NOT found\n";
    }
}

echo "\n2. Testing StatisticsService::getLikesReceivedStats...\n";
try {
    $receivedStats = StatisticsService::getLikesReceivedStats($userWithLikes->id);
    echo "   ✅ Received stats: Total = {$receivedStats['total_received']}, Top EGIs = " . count($receivedStats['top_egis']) . "\n";

    if (count($receivedStats['top_egis']) > 0) {
        $firstEgi = $receivedStats['top_egis'][0];
        echo "   Top EGI: {$firstEgi['title']} with {$firstEgi['likes_count']} likes\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error in getLikesReceivedStats: " . $e->getMessage() . "\n";
}

echo "\n3. Testing StatisticsService::getLikesGivenByUserStats...\n";
try {
    $givenStats = StatisticsService::getLikesGivenByUserStats($userWithLikes->id);
    echo "   ✅ Given stats: Total = {$givenStats['total_given']}, Liked EGIs = " . count($givenStats['liked_egis']) . ", Owners = " . count($givenStats['owners']) . "\n";

    if (count($givenStats['liked_egis']) > 0) {
        $firstEgi = $givenStats['liked_egis'][0];
        echo "   First liked EGI: {$firstEgi['title']} by {$firstEgi['owner_name']}\n";
    }

    if (count($givenStats['owners']) > 0) {
        $firstOwner = $givenStats['owners'][0];
        echo "   Top owner: {$firstOwner['nickname']} with {$firstOwner['likes_count']} likes received\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error in getLikesGivenByUserStats: " . $e->getMessage() . "\n";
}

echo "\n4. Testing EGI image accessors...\n";
$egi = Egi::first();
if ($egi) {
    echo "   Testing EGI: {$egi->title}\n";
    echo "   Main image: " . ($egi->getMainImageUrlAttribute() ?? 'NULL') . "\n";
    echo "   Thumbnail: " . ($egi->getThumbnailImageUrlAttribute() ?? 'NULL') . "\n";
    echo "   Avatar: " . ($egi->getAvatarImageUrlAttribute() ?? 'NULL') . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
