<?php

use App\Models\User;
use App\Models\Collection;
use App\Services\CollectionSubscriptionService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 15;
$collectionId = 5;

$user = User::findOrFail($userId);
$collection = Collection::findOrFail($collectionId);

echo "Attempting subscription for Collection {$collection->id} by User {$user->id}...\n";

$service = app(CollectionSubscriptionService::class);

try {
    $result = $service->processSubscription($user, $collection);

    if ($result['success']) {
        echo "SUCCESS! Subscription actived.\n";
        echo "Expires: " . $result['expires_at'] . "\n";
        echo "New Balance: " . $result['new_balance'] . "\n";
        
        // Update collection monetization status manually if service didn't (check service logic)
        // Service updates ai_credits_transactions but might relies on other listeners for collection table update
        // Let's check if we need to update collection table
        $collection->refresh();
        if ($collection->subscription_status !== 'active') {
             echo "Updating collection subscription_status to active...\n";
             $collection->subscription_status = 'active';
             $collection->save();
        }
        
    } else {
        echo "FAILED: " . $result['message'] . "\n";
        if (isset($result['missing_egili'])) {
            echo "Missing Egili: " . $result['missing_egili'] . "\n";
        }
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
