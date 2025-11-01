<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Egi;
use App\Models\EgiliTransaction;
use App\Services\EgiliService;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

/**
 * @Oracode Test: EgiliService Unit Tests
 * 🎯 Purpose: Test Egili token operations (earn, spend, balance)
 * 🧱 Core Logic: Validate atomic transactions, GDPR compliance, error handling
 * 🛡️ Coverage: Happy paths + edge cases + security
 * 
 * @package Tests\Unit\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Token System)
 * @date 2025-11-01
 */
class EgiliServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private EgiliService $service;
    private $mockLogger;
    private $mockErrorManager;
    private $mockAuditService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock dependencies (OS3 pattern)
        $this->mockLogger = Mockery::mock(UltraLogManager::class);
        $this->mockErrorManager = Mockery::mock(ErrorManagerInterface::class);
        $this->mockAuditService = Mockery::mock(AuditLogService::class);
        
        // Allow all logger calls (we're not testing logging)
        $this->mockLogger->shouldReceive('info')->andReturn(null);
        $this->mockLogger->shouldReceive('warning')->andReturn(null);
        $this->mockLogger->shouldReceive('error')->andReturn(null);
        
        // Instantiate service with mocks
        $this->service = new EgiliService(
            $this->mockLogger,
            $this->mockErrorManager,
            $this->mockAuditService
        );
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /** @test */
    public function it_returns_zero_balance_for_user_without_wallet()
    {
        $user = User::factory()->create();
        // No wallet created
        
        $balance = $this->service->getBalance($user);
        
        $this->assertEquals(0, $balance);
    }
    
    /** @test */
    public function it_returns_correct_balance_for_user_with_wallet()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 1000,
        ]);
        
        // Refresh user to load wallet relationship
        $user = $user->fresh();
        
        $balance = $this->service->getBalance($user);
        
        $this->assertEquals(1000, $balance);
    }
    
    /** @test */
    public function it_can_check_if_user_can_spend()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 500,
        ]);
        
        $user = $user->fresh();
        
        $this->assertTrue($this->service->canSpend($user, 400));
        $this->assertTrue($this->service->canSpend($user, 500));
        $this->assertFalse($this->service->canSpend($user, 501));
    }
    
    /** @test */
    public function it_converts_egili_to_eur_correctly()
    {
        $this->assertEquals(0.10, $this->service->toEur(1));
        $this->assertEquals(1.00, $this->service->toEur(10));
        $this->assertEquals(50.00, $this->service->toEur(500));
        $this->assertEquals(49.90, $this->service->toEur(499));
    }
    
    /** @test */
    public function it_converts_eur_to_egili_correctly()
    {
        $this->assertEquals(1, $this->service->fromEur(0.10));
        $this->assertEquals(10, $this->service->fromEur(1.00));
        $this->assertEquals(500, $this->service->fromEur(50.00));
        $this->assertEquals(499, $this->service->fromEur(49.90));
    }
    
    /** @test */
    public function it_earns_egili_successfully_with_atomic_transaction()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 100,
            'egili_lifetime_earned' => 100,
        ]);
        
        $egi = Egi::factory()->create();
        
        // Mock GDPR audit
        $this->mockAuditService->shouldReceive('logUserAction')
            ->once()
            ->with(
                $user,
                'egili_earned',
                Mockery::type('array'),
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
        
        $transaction = $this->service->earn(
            $user,
            50,
            'egi_sale_cashback',
            'trading',
            ['egi_id' => $egi->id],
            $egi
        );
        
        // Assert transaction created
        $this->assertInstanceOf(EgiliTransaction::class, $transaction);
        $this->assertEquals('earned', $transaction->transaction_type);
        $this->assertEquals('add', $transaction->operation);
        $this->assertEquals(50, $transaction->amount);
        $this->assertEquals(100, $transaction->balance_before);
        $this->assertEquals(150, $transaction->balance_after);
        $this->assertEquals('egi_sale_cashback', $transaction->reason);
        $this->assertEquals('trading', $transaction->category);
        
        // Assert wallet updated
        $wallet->refresh();
        $this->assertEquals(150, $wallet->egili_balance);
        $this->assertEquals(150, $wallet->egili_lifetime_earned);
    }
    
    /** @test */
    public function it_spends_egili_successfully_with_atomic_transaction()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 500,
            'egili_lifetime_spent' => 0,
        ]);
        
        // Mock GDPR audit
        $this->mockAuditService->shouldReceive('logUserAction')
            ->once()
            ->with(
                $user,
                'egili_spent',
                Mockery::type('array'),
                GdprActivityCategory::BLOCKCHAIN_ACTIVITY
            );
        
        $transaction = $this->service->spend(
            $user,
            500,
            'living_subscription_payment',
            'service',
            ['subscription_type' => 'one_time']
        );
        
        // Assert transaction created
        $this->assertInstanceOf(EgiliTransaction::class, $transaction);
        $this->assertEquals('spent', $transaction->transaction_type);
        $this->assertEquals('subtract', $transaction->operation);
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals(500, $transaction->balance_before);
        $this->assertEquals(0, $transaction->balance_after);
        
        // Assert wallet updated
        $wallet->refresh();
        $this->assertEquals(0, $wallet->egili_balance);
        $this->assertEquals(500, $wallet->egili_lifetime_spent);
    }
    
    /** @test */
    public function it_throws_exception_when_spending_more_than_balance()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 100,
        ]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo Egili insufficiente');
        
        $this->service->spend($user, 200, 'test_spend', 'service');
    }
    
    /** @test */
    public function it_throws_exception_when_earning_with_no_wallet()
    {
        $user = User::factory()->create();
        // No wallet
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User has no wallet');
        
        $this->service->earn($user, 100, 'test_earn', 'trading');
    }
    
    /** @test */
    public function it_throws_exception_when_spending_with_no_wallet()
    {
        $user = User::factory()->create();
        // No wallet
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User has no wallet');
        
        $this->service->spend($user, 100, 'test_spend', 'service');
    }
    
    /** @test */
    public function it_throws_exception_for_negative_earn_amount()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Egili earn amount must be positive');
        
        $this->service->earn($user, -10, 'test_earn', 'trading');
    }
    
    /** @test */
    public function it_throws_exception_for_negative_spend_amount()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'egili_balance' => 1000]);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Egili spend amount must be positive');
        
        $this->service->spend($user, -10, 'test_spend', 'service');
    }
    
    /** @test */
    public function it_tracks_audit_trail_with_ip_and_user_agent()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 0,
        ]);
        
        // Mock GDPR audit
        $this->mockAuditService->shouldReceive('logUserAction')->once();
        
        // Simulate request
        request()->merge(['REMOTE_ADDR' => '127.0.0.1']);
        request()->headers->set('User-Agent', 'Test Browser');
        
        $transaction = $this->service->earn($user, 100, 'test_earn', 'trading');
        
        $this->assertNotNull($transaction->ip_address);
        $this->assertNotNull($transaction->user_agent);
    }
    
    /** @test */
    public function it_maintains_atomic_transaction_on_earn()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'egili_balance' => 100,
        ]);
        
        // Mock GDPR to throw exception mid-transaction
        $this->mockAuditService->shouldReceive('logUserAction')
            ->once()
            ->andThrow(new \Exception('GDPR audit failed'));
        
        try {
            $this->service->earn($user, 50, 'test_earn', 'trading');
        } catch (\Exception $e) {
            // Transaction should rollback
            $wallet->refresh();
            $this->assertEquals(100, $wallet->egili_balance, 'Balance should not change on rollback');
            
            // No transaction record should exist
            $this->assertEquals(0, EgiliTransaction::where('wallet_id', $wallet->id)->count());
        }
    }
}

