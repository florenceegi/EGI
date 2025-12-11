<?php

use App\Models\User;
use App\Models\Collection;
use App\Services\EgiliService;
use App\Services\CollectionSubscriptionService;
use App\Helpers\FegiAuth;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 15;
$collectionId = 5;

$user = User::find($userId);
$collection = Collection::find($collectionId);

if (!$user) {
    echo "User $userId not found.\n";
    exit;
}
if (!$collection) {
    echo "Collection $collectionId not found.\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Collection: {$collection->collection_name} (ID: {$collection->id})\n";
echo "Monetization Type: " . $collection->monetization_type . "\n";
echo "Info from Subscription Accessors:\n";
echo " - Status: " . $collection->subscription_status . "\n";
echo " - Tier: " . ($collection->subscription_tier ?? 'NULL') . "\n";
echo " - Expires At: " . ($collection->subscription_expires_at ?? 'NULL') . "\n";
echo " - Started At: " . ($collection->subscription_started_at ?? 'NULL') . "\n";
echo " - Stripe ID: " . ($collection->subscription_stripe_id ?? 'NULL') . "\n";
