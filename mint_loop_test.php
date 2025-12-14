
// mint_loop_test.php

// Params
$masterId = 2;
$userId = 34;

echo "--- STARTING MINT LOOP TEST ---\n";
echo "User: $userId | Master: $masterId\n";

// 1. Setup Auth
\Illuminate\Support\Facades\Auth::loginUsingId($userId);
echo "Logged in as User " . \Illuminate\Support\Facades\Auth::id() . "\n";

// 2. Clean previous clones (optional but good for repeated tests)
$clones = \App\Models\Egi::where('parent_id', $masterId)->where('user_id', $userId)->get();
if ($clones->count() > 0) {
    echo "Found {$clones->count()} old clones. Deleting...\n";
    foreach ($clones as $c) {
        $c->forceDelete(); 
    }
}

// 3. Restore Master (if owner was changed by bug)
$master = \App\Models\Egi::find($masterId);
if (!$master) {
    die("Master EGI $masterId not found!\n");
}

if ($master->owner_id !== $master->user_id) {
    echo "⚠️ Master owner is wrong ({$master->owner_id}). Restoring to {$master->user_id}...\n";
    $master->owner_id = $master->user_id;
    $master->mint = false;
    $master->save();
    echo "Master restored.\n";
} else {
    echo "Master state appears correct.\n";
}

// 4. Construct Request for Direct Mint
// We simulate a Stripe payment logic match or just enough to pass validation
// Note: processDirectMint expects 'payment_method'.
$baseRequest = \Illuminate\Http\Request::create("/egi/{$masterId}/mint-direct", 'POST', [
    'egi_id' => $masterId, // Required by MintDirectRequest rules
    'payment_method' => 'stripe', 
    'co_creator_display_name' => 'Test User 34',
]);

// Convert to FormRequest
$request = \App\Http\Requests\MintDirectRequest::createFromBase($baseRequest);

// Bind container (needed for validator resolution)
$request->setContainer(app());

// Manually validate to ensure $request->validated() works in Controller
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), $request->rules());
$request->setValidator($validator);

if ($validator->fails()) {
    echo "❌ TEST SETUP ERROR: Validation failed.\n";
    print_r($validator->errors()->toArray());
    exit;
}

// Headers to simulate AJAX vs Browser
// Test 1: Browser Request (wantsJson = false by default for Request::create unless Accept header set)

echo "\n--- TEST 1: Browser Request (Expect Redirect) ---\n";
try {
    $controller = app(\App\Http\Controllers\MintController::class);
    // Bind request to container
    app()->instance('request', $request);

    // CALL THE CONTROLLER
    // method signature: processDirectMint(int $id, MintDirectRequest $request)
    $response = $controller->processDirectMint($masterId, $request);

    echo "Response Type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✅ SUCCESS: Received RedirectResponse.\n";
        echo "Target URL: " . $response->getTargetUrl() . "\n";
    } elseif ($response instanceof \Illuminate\Http\JsonResponse) {
        echo "❌ FAILURE: Received JsonResponse (Expected Redirect).\n";
        print_r($response->getData());
    } else {
        echo "⚠️ UNEXPECTED: " . get_class($response) . "\n";
    }

} catch (\Exception $e) {
    echo "❌ CRASHED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "\n--- END TEST ---\n";
