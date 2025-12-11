<?php

use App\Models\User;
use App\Models\Egi;
use App\Models\Collection;
use App\Models\Wallet;
use App\Services\EgiliService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Enums\Egi\EgiTypeEnum;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- REBIND VERIFICATION SCRIPT (SAFE MODE) ---\n";

try {
    // 1. Create Users
    $creator = User::create([
        'name' => 'Creator ' . Str::random(5),
        'email' => 'creator_' . Str::random(5) . '@test.com',
        'password' => bcrypt('password'),
    ]);
    
    $seller = User::create([
        'name' => 'Seller ' . Str::random(5),
        'email' => 'seller_' . Str::random(5) . '@test.com',
        'password' => bcrypt('password'),
    ]);
    
    $buyer = User::create([
        'name' => 'Buyer ' . Str::random(5),
        'email' => 'buyer_' . Str::random(5) . '@test.com',
        'password' => bcrypt('password'),
    ]);

    echo "Users created: Creator={$creator->id}, Seller={$seller->id}, Buyer={$buyer->id}\n";

    // 2. Create Collection
    $collection = new Collection();
    $collection->creator_id = $creator->id; // Owner of collection
    $collection->collection_name = 'Rebind Test ' . Str::random(5);
    $collection->description = 'Test Desc';
    $collection->status = 'active';
    // Fill required fields based on Schema guess or usage
    // Assuming minimal fields
    $collection->save();
    
    echo "Collection created: {$collection->id}\n";

    // 4. Create Wallets (Creator, EPP, Natan)
    $creatorWallet = Wallet::create([
        'user_id' => $creator->id,
        'collection_id' => $collection->id,
        'platform_role' => 'Creator',
        'wallet' => 'CREATOR_WALLET_' . Str::random(10),
        'royalty_mint' => 100,
        'royalty_rebind' => 4.5, // Using env default manually for test
        'percentage' => 100
    ]);
    
    $eppWallet = Wallet::create([
        'user_id' => 2, // Default EPP ID
        'collection_id' => $collection->id,
        'platform_role' => 'EPP',
        'wallet' => 'EPP_WALLET_' . Str::random(10),
        'royalty_mint' => 20,
        'royalty_rebind' => 0.8,
        'percentage' => 100
    ]);

    $natanWallet = Wallet::create([
        'user_id' => 1, // Default Natan ID
        'collection_id' => $collection->id,
        'platform_role' => 'Natan',
        'wallet' => 'NATAN_WALLET_' . Str::random(10),
        'royalty_mint' => 10,
        'royalty_rebind' => 0.7,
        'percentage' => 100
    ]);
    
    echo "Wallets created: Creator={$creatorWallet->id}, EPP={$eppWallet->id}, Natan={$natanWallet->id}\n";

    // 4. Create EGI
    $egi = new Egi();
    $egi->user_id = $creator->id; // Creator
    $egi->owner_id = $seller->id; // Current Owner
    $egi->collection_id = $collection->id;
    $egi->title = 'Test EGI';
    $egi->description = 'Test EGI Desc';
    $egi->price = 100.00;
    $egi->status = 'active'; // Or whatever is valid
    // Assuming 'type' or other fields might be needed
    // $egi->type = EgiTypeEnum::STANDARD; // If Enum exists
    $egi->payment_by_egili = true;
    $egi->mint = true; // Required for Rebind
    $egi->is_published = true; // Required
    $egi->sale_mode = 'fixed_price'; // Required
    $egi->save();

    echo "EGI created: {$egi->id}\n";

    // 5. Fund Buyer
    // Create Buyer Wallet first
    $buyerWallet = Wallet::create([
        'user_id' => $buyer->id,
        'collection_id' => null, // User wallet
        'platform_role' => 'Collector', // Assuming role
        'wallet' => 'BUYER_WALLET_' . Str::random(5),
        'egili_balance' => 50000, // Direct funding
        'egili_lifetime_earned' => 50000
    ]);

    \DB::table('egili_transactions')->insert([
        'user_id' => $buyer->id,
        'wallet_id' => $buyerWallet->id, // Link to wallet
        'amount' => 50000,
        'balance_after' => 50000,
        'balance_before' => 0,
        'transaction_type' => 'admin_grant',
        'operation' => 'add',
        'reason' => 'Test Funding',
        'created_at' => now(),
        'updated_at' => now(),
        'metadata' => json_encode(['reference' => 'TEST_FUND_' . Str::random(5)])
    ]);
    
    // Update user balance cache if needed?
    // EgiliService usually calculates from DB, so insert should be enough if getBalance query sums it.
    // Or update user table if there is a cached column.
    
    echo "Buyer funded.\n";

    // 5b. Seed Consent
    $cv = \App\Models\ConsentVersion::firstOrCreate(
        ['version' => '1.0'],
        ['effective_date' => now(), 'created_by' => $creator->id, 'consent_types' => []]
    );

    \App\Models\UserConsent::create([
        'user_id' => $buyer->id,
        'consent_type' => 'platform-services',
        'granted' => true,
        'consent_version_id' => $cv->id,
        'ip_address' => '127.0.0.1',
        'legal_basis' => 'contract',
        'metadata' => []
    ]);
    echo "Consent seeded.\n";

    // 6. Execute Rebind
    Auth::login($buyer);
    $controller = app(\App\Http\Controllers\RebindController::class);
    $request = Request::create('/egi/'.$egi->id.'/rebind', 'POST', [
        'payment_method' => 'egili'
    ]);
    
    $response = $controller->process($egi->id, $request);
    
    // 7. Check Results
    $egi->refresh();
    echo "--- RESULTS ---\n";
    echo "Owner ID: {$egi->owner_id} (Expected: {$buyer->id})\n";
    
    if ($egi->owner_id != $buyer->id) {
        throw new Exception("Ownership transfer failed.");
    }
    
    $dists = \App\Models\PaymentDistribution::where('egi_id', $egi->id)->where('source_type', 'rebind')->get();
    echo "Distributions: " . $dists->count() . "\n";
    foreach ($dists as $d) {
        echo " - Payee: {$d->user_id} ({$d->user_type->value}) | Amount: {$d->amount_eur}\n";
    }
    
    // Check for beneficiaries
    $beneficiaries = ['creator', 'epp', 'frangette'];
    foreach ($beneficiaries as $role) {
        $found = $dists->filter(fn($d) => $d->user_type->value === $role)->count();
        if ($found > 0) {
            echo "[SUCCESS] {$role} received payment.\n";
        } else {
             echo "[WARNING] {$role} did NOT receive payment.\n";
        }
    }

} catch (\Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    // echo $e->getTraceAsString();
}
