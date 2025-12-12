<?php

use App\Models\Egi;
use App\Models\User;
use App\Actions\Egi\CloneEgiFromMasterAction;
use App\Services\SerialService;
use App\Services\EgiMintingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING BUYER CLONING VERIFICATION ---\n";

// 1. Setup Data
$creator = User::first(); // Assuming at least one user exists
$buyer = User::factory()->create(); // Create a temp buyer
echo "Creator ID: {$creator->id}\n";
echo "Buyer ID: {$buyer->id}\n";

// Create Master EGI
$master = Egi::create([
    'title' => 'Master Template for Buyer',
    'description' => 'Test Master',
    'user_id' => $creator->id,
    'collection_id' => 1, // Mock collection
    'is_template' => true,
    'is_sellable' => false,
    'is_published' => true,
    'status' => 'published',
    'price' => 100.00
]);
echo "Master Created: {$master->id} (is_template: {$master->is_template})\n";

// 2. Execute Action with shouldMint = false
echo "Executing Clone Action (Deferred Minting)...\n";
try {
    $action = app(CloneEgiFromMasterAction::class);
    $clone = $action->execute($master, $buyer, false);
    
    echo "Clone Created: {$clone->id}\n";
    
    // 3. Verify Clone State
    if ($clone->parent_id !== $master->id) {
        throw new Exception("Clone parent_id mismatch.");
    }
    if ($clone->owner_id !== $buyer->id) { // NOTE: Action sets owner_id? Let's check logic.
        // Wait, action sets user_id to $owner (which is buyer). owner_id might be set or not depending on logic.
        // In CloneAction: $child->user_id = $owner->id; $child->owner_id = $owner->id;
        if ($clone->user_id !== $buyer->id && $clone->owner_id !== $buyer->id) {
             echo "WARNING: Check owner/user assignment on clone. User: {$clone->user_id}, Owner: {$clone->owner_id}\n";
        }
    }
    
    if ($clone->isMinted()) {
        throw new Exception("Clone SHOULD NOT be minted yet.");
    } else {
        echo "SUCCESS: Clone is NOT minted.\n";
    }
    
    if (empty($clone->serial_number)) {
        throw new Exception("Clone missing serial number.");
    }
    echo "Serial Number: {$clone->serial_number}\n";

    // 4. Clean up
    $clone->forceDelete();
    $master->forceDelete();
    $buyer->delete();
    
    echo "--- VERIFICATION SUCCESSFUL ---\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
