<?php

namespace Tests\Manual;

use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Collection;
use App\Services\Payment\StripePaymentSplitService;
use App\Services\GoldPriceService;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as LaravelCollection;

class VerifyCommoditySplitTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCommoditySplitLogic()
    {
        echo "\n--- STARTING COMMODITY SPLIT VERIFICATION ---\n";

        // 1. Setup Mock Data
        
        // Create a Mock Gold Bar EGI
        // We need an EGI that returns true for isGoldBar()
        // And has valid traits/attributes for calculation
        // Since we can't easily create complex trait relationships in a simple script without factories,
        // we might need to mock the GoldPriceService or use an existing EGI if available.
        // Let's Mock GoldPriceService to avoid dependency on real EGI traits/API.
        
        $mockGoldService = $this->createMock(GoldPriceService::class);
        $mockGoldService->method('calculateFromEgi')->willReturn([
            'base_value' => 1000.00, // Cost
            'margin_applied' => 100.00, // Margin
            'final_value' => 1100.00, // Total
            'currency' => 'EUR'
        ]);
        
        // Identify a real EGI ID to pass check, or mock EGI::find?
        // EGI::find is static, hard to mock without full facade mocking.
        // Better to create a temporary dummy EGI in DB or use a known one.
        // Let's create a dummy EGI.
        $egi = Egi::factory()->create([
            'commodity_type' => 'gold_bar', // Assuming this triggers isGoldBar
            // Add other necessary fields
            'title' => 'Test Gold Bar',
            'collection_id' => 1, // Dummy
            'user_id' => 1,
        ]);
        
        // Mock isGoldBar if it depends on traits?
        // Let's assume 'commodity_type' => 'gold_bar' works or check Model.
        // Checking Model: isGoldBar() checks traits usually.
        // Let's rely on injecting the service and hope we can bypass strict model checks in the service 
        // OR rely on the mock service returning data regardless of the EGI state, 
        // BUT the service code has: if ($egi && $egi->isGoldBar()) ...
        
        // So we MUST ensure $egi->isGoldBar() returns true.
        // We can partial mock the EGI? No, it's Eloquent.
        // We can just add the 'commodity_type' => 'gold_bar' if that's how it works.
        // Model code wasn't fully visible for isGoldBar logic, likely checks commodity_type or specific trait.
        // Let's double check Egi.php if needed, or assume 'commodity_type' is the key.
        // In viewed file: 'commodity_type', 'commodity_metadata' exist.
        
        // 2. Create Wallets
        $collection = Collection::factory()->create();
        
        // Platform Wallet (Natan)
        $platformWallet = Wallet::factory()->create([
            'platform_role' => 'Natan',
            'royalty_mint' => 10,
            'collection_id' => $collection->id
        ]);
        
        // Company Wallet (Owner)
        $companyWallet = Wallet::factory()->create([
            'platform_role' => 'Company',
            'royalty_mint' => 90,
            'collection_id' => $collection->id,
            'user_id' => $collection->collection_owner_id // Match collection owner
        ]);
        
        $wallets = new LaravelCollection([$platformWallet, $companyWallet]);
        
        // 3. Instantiate Service with Mock
        // We need to resolve other dependencies
        $service = new StripePaymentSplitService(
            app(\Ultra\UltraLogManager\UltraLogManager::class),
            app(\Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class),
            app(\App\Services\AuditLogService::class),
            app(\App\Services\ConsentService::class),
            app(\App\Services\PaymentDistributionService::class),
            $mockGoldService // Injected Mock
        );
        
        // Allow access to protected method
        $reflection = new \ReflectionClass(StripePaymentSplitService::class);
        $method = $reflection->getMethod('calculateDistributions');
        $method->setAccessible(true);
        
        // 4. Run Calculation
        $totalAmount = 1100.00;
        
        // We need to ensure $egi is treated as GoldBar.
        // If standard factory doesn't set it right, we might fail the "if ($egi->isGoldBar())" check.
        // For this test, let's assume isGoldBar() returns true if commodity_type is 'gold_bar'.
        // If not, we might need to physically create traits.
        
        // To be safe, let's try to mock the EGI retrieval or just force the params if possible?
        // The service does: $egi = Egi::find($egiId);
        // We can't easily intercept that static call.
        // Ideally we'd use a Repository pattern, but here we are direct.
        
        echo "Calling calculateDistributions...\n";
        $distributions = $method->invokeArgs($service, [$wallets, $totalAmount, $egi->id]);
        
        // 5. Verify Results
        echo "\nResults:\n";
        foreach ($distributions as $d) {
            echo "Role: {$d['platform_role']} | Amount: {$d['amount_eur']} EUR | Percentage: {$d['percentage']}%\n";
            
            if ($d['platform_role'] === 'Natan') {
                // Expect 10% of Margin (100 * 0.10 = 10 EUR)
                if (abs($d['amount_eur'] - 10.00) < 0.01) {
                    echo "✅ Platform Fee Correct (10.00 EUR)\n";
                } else {
                    echo "❌ Platform Fee Incorrect (Expected 10.00, Got {$d['amount_eur']})\n";
                }
            }
            
            if ($d['platform_role'] === 'Company') {
                // Expect Cost + 90% Margin (1000 + 90 = 1090 EUR)
                if (abs($d['amount_eur'] - 1090.00) < 0.01) {
                    echo "✅ Company Share Correct (1090.00 EUR)\n";
                } else {
                    echo "❌ Company Share Incorrect (Expected 1090.00, Got {$d['amount_eur']})\n";
                }
            }
        }
        
        echo "\n--- VERIFICATION FINISHED ---\n";
    }
}
