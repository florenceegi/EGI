
// debug_availability.php

$userId = 34; // Test User
$masterId = 2; // Master EGI

// 1. Simulate Auth
Auth::loginUsingId($userId);
$user = Auth::user();

echo "Debug session for User: {$user->id}\n";

// 2. Load Master
$master = \App\Models\Egi::find($masterId);
if (!$master) die("Master $masterId not found\n");

echo "Master found: {$master->title} (ID: {$master->id})\n";

// 3. Clone
try {
    echo "Cloning...\n";
    $cloneAction = app(\App\Actions\Egi\CloneEgiFromMasterAction::class);
    $clone = $cloneAction->execute($master, $user, false);
    echo "Cloned! New ID: {$clone->id}\n";
    echo "Clone Status: {$clone->status}\n";
    echo "Clone Owner: {$clone->owner_id}\n";
    echo "Clone IS_PUBLISHED: " . ($clone->is_published ? 'Yes' : 'No') . "\n";
} catch (\Exception $e) {
    die("Cloning failed: " . $e->getMessage() . "\n");
}

// 4. Check Availability
$service = app(\App\Services\EgiAvailabilityService::class);
$result = $service->checkAvailability($clone, $user);

echo "\n--- Availability Result ---\n";
echo "Can Mint: " . ($result['can_mint'] ? 'YES' : 'NO') . "\n";
echo "Reason: " . ($result['mint_reason'] ?? 'N/A') . "\n";
print_r($result);

// 5. Cleanup
echo "\nCleaning up clone {$clone->id}...\n";
$clone->forceDelete();
echo "Done.\n";
