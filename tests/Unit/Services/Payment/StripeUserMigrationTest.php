<?php

namespace Tests\Unit\Services\Payment;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Payment\MerchantAccountResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class StripeUserMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected MerchantAccountResolver $resolver;
    protected $mockLogger;
    protected $mockErrorManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockLogger->shouldReceive('info')->andReturnNull();
        $this->mockLogger->shouldReceive('warning')->andReturnNull();
        $this->mockLogger->shouldReceive('debug')->andReturnNull();
        $this->mockLogger->shouldReceive('error')->andReturnNull();

        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);

        $this->resolver = new MerchantAccountResolver(
            $this->mockLogger,
            $this->mockErrorManager
        );
        
        Config::set('algorand.payments.stripe_enabled', true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_prioritizes_user_stripe_account_id_over_wallet()
    {
        $user = User::factory()->create([
            'stripe_account_id' => 'acct_user_priority_123'
        ]);
        
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Wallet has a DIFFERENT ID (Legacy data)
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => 'acct_legacy_wallet_456', 
            'royalty_mint' => 100,
            'platform_role' => 'creator'
        ]);

        $result = $this->resolver->resolveForEgiAndProvider($egi, 'stripe');

        // MUST match User ID
        $this->assertEquals('acct_user_priority_123', $result['stripe_account_id']);
    }

    /** @test */
    public function it_falls_back_to_wallet_id_if_user_has_none()
    {
        $user = User::factory()->create([
            'stripe_account_id' => null // Missing on User
        ]);
        
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Wallet HAS ID
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => 'acct_legacy_wallet_456', 
            'royalty_mint' => 100,
            'platform_role' => 'creator'
        ]);

        $result = $this->resolver->resolveForEgiAndProvider($egi, 'stripe');

        // MUST return Wallet ID (Fallback)
        $this->assertEquals('acct_legacy_wallet_456', $result['stripe_account_id']);
    }

    /** @test */
    public function it_returns_user_id_even_if_wallet_is_empty()
    {
        $user = User::factory()->create([
            'stripe_account_id' => 'acct_user_only_789'
        ]);
        
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Wallet has NULL ID
        Wallet::factory()->create([
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'stripe_account_id' => null, 
            'royalty_mint' => 100,
            'platform_role' => 'creator'
        ]);

        $result = $this->resolver->resolveForEgiAndProvider($egi, 'stripe');

        $this->assertEquals('acct_user_only_789', $result['stripe_account_id']);
    }
}
