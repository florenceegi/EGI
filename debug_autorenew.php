<?php

use App\Models\User;
use App\Models\Collection;
use App\Services\RecurringPaymentService;
use App\Services\CollectionSubscriptionService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 15;
$collectionId = 5;

echo "--- Auto-Renew Verification ---\n";

$user = User::find($userId);
$collection = Collection::find($collectionId);

if (!$user || !$collection) {
    echo "User or Collection not found.\n";
    exit;
}

echo "User Balance: " . $user->EgiliBalance . "\n";
echo "Granting 1000 Egili for test...\n";
app(\App\Services\EgiliService::class)->grantBonus($user, 1000, 'Test AutoRenew', $user);
echo "New Balance: " . $user->fresh()->EgiliBalance . "\n";

// 1. Manually create/register a subscription that expires NOW
echo "Creating expired subscription...\n";
$recurringService = app(RecurringPaymentService::class);
$sub = $recurringService->registerSubscription(
    $user,
    $collection,
    'collection_subscription',
    now()->subDay(), // Expired 24 hours ago
    ['cost_egili' => 100, 'duration_days' => 30] // Low cost for test
);

echo "Subscription Created. Status: {$sub->status}, Next Renewal: {$sub->next_renewal_at}\n";

// 2. Run Process
echo "Running Renewal Process...\n";
$recurringService->processDueRenewals();

// 3. Verify
$sub->refresh();
echo "Re-checking Subscription...\n";
echo "Status: {$sub->status}\n";
echo "Next Renewal: {$sub->next_renewal_at}\n";
echo "Renewal Count: {$sub->renewal_count}\n";
echo "User Balance After: " . $user->fresh()->EgiliBalance . "\n";

if ($sub->renewal_count > 0 && $sub->next_renewal_at > now()) {
    echo "SUCCESS: Subscription renewed!\n";
} else {
    echo "FAILURE: Subscription did not renew.\n";
}
