<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Egi;
use App\Models\EgiliTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @Oracode Test: EgiliTransaction Model Unit Tests
 * 🎯 Purpose: Test EgiliTransaction model relationships and methods
 * 🧱 Core Logic: Validate relationships, scopes, accessors
 * 🛡️ Coverage: Model behavior, polymorphic relations, query scopes
 * 
 * @package Tests\Unit\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Egili Token System)
 * @date 2025-11-01
 */
class EgiliTransactionTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_belongs_to_wallet()
    {
        $wallet = Wallet::factory()->create();
        $transaction = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
        ]);
        
        $this->assertInstanceOf(Wallet::class, $transaction->wallet);
        $this->assertEquals($wallet->id, $transaction->wallet->id);
    }
    
    /** @test */
    public function it_belongs_to_user()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $transaction = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertEquals($user->id, $transaction->user->id);
    }
    
    /** @test */
    public function it_has_polymorphic_source_relationship()
    {
        $egi = Egi::factory()->create();
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        
        $transaction = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'source_type' => Egi::class,
            'source_id' => $egi->id,
        ]);
        
        $this->assertInstanceOf(Egi::class, $transaction->source);
        $this->assertEquals($egi->id, $transaction->source->id);
    }
    
    /** @test */
    public function it_scopes_earned_transactions()
    {
        $wallet = Wallet::factory()->create();
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'add',
        ]);
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'subtract',
        ]);
        
        $earned = EgiliTransaction::earned()->get();
        
        $this->assertEquals(1, $earned->count());
        $this->assertEquals('add', $earned->first()->operation);
    }
    
    /** @test */
    public function it_scopes_spent_transactions()
    {
        $wallet = Wallet::factory()->create();
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'add',
        ]);
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'subtract',
        ]);
        
        $spent = EgiliTransaction::spent()->get();
        
        $this->assertEquals(1, $spent->count());
        $this->assertEquals('subtract', $spent->first()->operation);
    }
    
    /** @test */
    public function it_scopes_completed_transactions()
    {
        $wallet = Wallet::factory()->create();
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'status' => 'completed',
        ]);
        
        EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'status' => 'pending',
        ]);
        
        $completed = EgiliTransaction::completed()->get();
        
        $this->assertEquals(1, $completed->count());
        $this->assertEquals('completed', $completed->first()->status);
    }
    
    /** @test */
    public function it_returns_signed_amount_accessor()
    {
        $wallet = Wallet::factory()->create();
        
        $earnTransaction = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'add',
            'amount' => 100,
        ]);
        
        $spendTransaction = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'operation' => 'subtract',
            'amount' => 50,
        ]);
        
        $this->assertEquals('+100', $earnTransaction->signed_amount);
        $this->assertEquals('-50', $spendTransaction->signed_amount);
    }
    
    /** @test */
    public function it_checks_if_transaction_is_reversible()
    {
        $wallet = Wallet::factory()->create();
        
        // Reversible: completed + earned/spent/purchase
        $reversible = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'status' => 'completed',
            'transaction_type' => 'earned',
        ]);
        
        // Non-reversible: admin_grant
        $nonReversible = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'status' => 'completed',
            'transaction_type' => 'admin_grant',
        ]);
        
        // Non-reversible: failed status
        $failed = EgiliTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'status' => 'failed',
            'transaction_type' => 'earned',
        ]);
        
        $this->assertTrue($reversible->isReversible());
        $this->assertFalse($nonReversible->isReversible());
        $this->assertFalse($failed->isReversible());
    }
}





