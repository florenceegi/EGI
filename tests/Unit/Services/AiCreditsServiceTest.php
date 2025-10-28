<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AiCreditsService;
use App\Models\User;
use App\Models\AiCreditsTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use ReflectionClass;

/**
 * AiCreditsService Test
 *
 * Tests for AI credits system including:
 * - Exchange rate management
 * - Credits calculation from tokens
 * - Credits deduction and refund
 * - Cost estimation
 *
 * @package Tests\Unit\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Testing)
 * @date 2025-10-28
 */
class AiCreditsServiceTest extends TestCase {
    use RefreshDatabase;

    protected AiCreditsService $service;
    protected User $user;

    protected function setUp(): void {
        parent::setUp();

        // Create service instance
        $this->service = app(AiCreditsService::class);

        // Create test user with initial credits
        $this->user = User::factory()->create([
            'ai_credits_balance' => 1000,
            'ai_credits_lifetime_earned' => 1000,
            'ai_credits_lifetime_used' => 0,
        ]);

        // Clear cache before each test
        Cache::flush();
    }

    /** @test */
    public function it_gets_exchange_rate_from_cache() {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.95, now()->addDay());

        // Act
        $rate = $this->invokeMethod($this->service, 'getExchangeRate');

        // Assert
        $this->assertEquals(0.95, $rate);
    }

    /** @test */
    public function it_gets_exchange_rate_from_config_when_cache_missing() {
        // Arrange
        config(['ai-credits.usd_to_eur_rate' => 0.93]);
        Cache::forget('exchange_rate_usd_to_eur');

        // Act
        $rate = $this->invokeMethod($this->service, 'getExchangeRate');

        // Assert
        $this->assertEquals(0.93, $rate);
    }

    /** @test */
    public function it_uses_safe_fallback_when_cache_and_config_missing() {
        // Arrange
        Cache::forget('exchange_rate_usd_to_eur');
        config(['ai-credits.usd_to_eur_rate' => null]);

        // Act
        $rate = $this->invokeMethod($this->service, 'getExchangeRate');

        // Assert
        $this->assertEquals(1.0, $rate, 'Should use safe fallback 1.0');
    }

    /** @test */
    public function it_calculates_credits_from_tokens_correctly() {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        // Input: 50,000 tokens @ $3/1M = $0.15
        // Output: 20,000 tokens @ $15/1M = $0.30
        // Total: $0.45 USD → €0.414 → 42 credits (rounded up)

        // Act
        $credits = $this->service->calculateCreditsFromTokens(50000, 20000);

        // Assert
        $this->assertEquals(42, $credits);
    }

    /** @test */
    public function it_rounds_up_credits_for_pa_safety() {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        // Very small cost should still be at least 1 credit
        // Input: 100 tokens @ $3/1M = $0.0003
        // Output: 100 tokens @ $15/1M = $0.0015
        // Total: $0.0018 USD → €0.001656 → 1 credit (rounded up from 0.1656)

        // Act
        $credits = $this->service->calculateCreditsFromTokens(100, 100);

        // Assert
        $this->assertGreaterThanOrEqual(1, $credits, 'Should round up to at least 1 credit');
    }

    /** @test */
    public function it_checks_if_user_has_enough_credits() {
        // Act & Assert
        $this->assertTrue($this->service->hasEnoughCredits($this->user, 500));
        $this->assertTrue($this->service->hasEnoughCredits($this->user, 1000));
        $this->assertFalse($this->service->hasEnoughCredits($this->user, 1001));
        $this->assertFalse($this->service->hasEnoughCredits($this->user, 2000));
    }

    /** @test */
    public function it_deducts_credits_successfully() {
        // Arrange
        $initialBalance = $this->user->ai_credits_balance;

        // Act
        $transaction = $this->service->deductCredits(
            user: $this->user,
            credits: 250,
            sourceType: 'ai_pa_analysis_chunked',
            sourceId: null,
            metadata: [
                'tokens_consumed' => 50000,
                'input_tokens' => 30000,
                'output_tokens' => 20000,
            ]
        );

        // Assert
        $this->assertInstanceOf(AiCreditsTransaction::class, $transaction);
        $this->assertEquals('usage', $transaction->transaction_type);
        $this->assertEquals('subtract', $transaction->operation);
        $this->assertEquals(250, $transaction->amount);
        $this->assertEquals($initialBalance, $transaction->balance_before);
        $this->assertEquals($initialBalance - 250, $transaction->balance_after);
        $this->assertEquals('completed', $transaction->status);

        // Verify user balance updated
        $this->user->refresh();
        $this->assertEquals($initialBalance - 250, $this->user->ai_credits_balance);
        $this->assertEquals(250, $this->user->ai_credits_lifetime_used);
    }

    /** @test */
    public function it_throws_exception_when_insufficient_credits() {
        // Arrange
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient credits');

        // Act
        $this->service->deductCredits(
            user: $this->user,
            credits: 1500, // More than user has
            sourceType: 'ai_pa_analysis_chunked'
        );
    }

    /** @test */
    public function it_refunds_credits_successfully() {
        // Arrange
        $initialBalance = $this->user->ai_credits_balance;

        // First deduct some credits
        $originalTransaction = $this->service->deductCredits(
            user: $this->user,
            credits: 250,
            sourceType: 'ai_pa_analysis_chunked'
        );

        $this->user->refresh();
        $balanceAfterDeduct = $this->user->ai_credits_balance;

        // Act - Refund
        $refundTransaction = $this->service->refundCredits(
            user: $this->user,
            credits: 250,
            reason: 'Job failed: timeout',
            originalTransactionId: $originalTransaction->id
        );

        // Assert
        $this->assertInstanceOf(AiCreditsTransaction::class, $refundTransaction);
        $this->assertEquals('refund', $refundTransaction->transaction_type);
        $this->assertEquals('add', $refundTransaction->operation);
        $this->assertEquals(250, $refundTransaction->amount);
        $this->assertEquals($balanceAfterDeduct, $refundTransaction->balance_before);
        $this->assertEquals($initialBalance, $refundTransaction->balance_after);
        $this->assertEquals('completed', $refundTransaction->status);

        // Verify user balance restored
        $this->user->refresh();
        $this->assertEquals($initialBalance, $this->user->ai_credits_balance);
        $this->assertEquals(0, $this->user->ai_credits_lifetime_used);
    }

    /** @test */
    public function it_estimates_cost_correctly() {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        // Act
        $estimation = $this->service->getEstimatedCost(500, 100);

        // Assert
        $this->assertIsArray($estimation);
        $this->assertArrayHasKey('estimated_credits', $estimation);
        $this->assertArrayHasKey('estimated_cost_eur', $estimation);
        $this->assertArrayHasKey('total_chunks', $estimation);
        $this->assertArrayHasKey('total_input_tokens', $estimation);
        $this->assertArrayHasKey('total_output_tokens', $estimation);

        // 500 acts → 5 chunks
        $this->assertEquals(5, $estimation['total_chunks']);

        // Credits should be positive
        $this->assertGreaterThan(0, $estimation['estimated_credits']);
        $this->assertGreaterThan(0, $estimation['estimated_cost_eur']);
    }

    /** @test */
    public function it_updates_exchange_rate_manually() {
        // Act
        $success = $this->service->updateExchangeRate(0.95);

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(0.95, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_rejects_invalid_manual_exchange_rate() {
        // Arrange
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Exchange rate must be positive');

        // Act
        $this->service->updateExchangeRate(-0.5);
    }

    /** @test */
    public function it_updates_exchange_rate_from_api() {
        // Arrange - Mock HTTP response
        Http::fake([
            'api.exchangerate.host/*' => Http::response([
                'success' => true,
                'base' => 'USD',
                'rates' => [
                    'EUR' => 0.9234
                ]
            ], 200)
        ]);

        // Act
        $success = $this->service->updateExchangeRate();

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(0.9234, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_falls_back_to_config_when_api_fails() {
        // Arrange
        config(['ai-credits.usd_to_eur_rate' => 0.92]);

        Http::fake([
            'api.exchangerate.host/*' => Http::response([], 500) // API fails
        ]);

        // Act
        $success = $this->service->updateExchangeRate();

        // Assert
        $this->assertTrue($success);
        $this->assertEquals(0.92, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_validates_api_rate_with_sanity_check() {
        // Arrange - Mock unrealistic rate (should be rejected)
        config(['ai-credits.usd_to_eur_rate' => 0.92]);

        Http::fake([
            'api.exchangerate.host/*' => Http::response([
                'success' => true,
                'base' => 'USD',
                'rates' => [
                    'EUR' => 5.0 // Unrealistic rate
                ]
            ], 200)
        ]);

        // Act
        $success = $this->service->updateExchangeRate();

        // Assert
        $this->assertTrue($success);
        // Should use config fallback instead of invalid API rate
        $this->assertEquals(0.92, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_returns_exchange_rate_info() {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.9234, now()->addDay());

        // Act
        $info = $this->service->getExchangeRateInfo();

        // Assert
        $this->assertIsArray($info);
        $this->assertArrayHasKey('rate', $info);
        $this->assertArrayHasKey('source', $info);
        $this->assertArrayHasKey('1_usd_in_eur', $info);
        $this->assertArrayHasKey('1_eur_in_usd', $info);

        $this->assertEquals(0.9234, $info['rate']);
        $this->assertEquals('cache', $info['source']);
        $this->assertEquals(0.9234, $info['1_usd_in_eur']);
        $this->assertEquals(round(1 / 0.9234, 4), $info['1_eur_in_usd']);
    }

    /** @test */
    public function it_handles_zero_tokens_gracefully() {
        // Act
        $credits = $this->service->calculateCreditsFromTokens(0, 0);

        // Assert
        $this->assertEquals(0, $credits, 'Zero tokens should result in zero credits');
    }

    /** @test */
    public function it_stores_metadata_in_transaction() {
        // Act
        $transaction = $this->service->deductCredits(
            user: $this->user,
            credits: 50,
            sourceType: 'ai_pa_analysis_chunked',
            sourceId: 123,
            metadata: [
                'tokens_consumed' => 25000,
                'input_tokens' => 15000,
                'output_tokens' => 10000,
                'ai_model' => 'claude-3-5-sonnet-20241022',
                'feature_parameters' => [
                    'session_id' => 'test_session_123',
                    'query' => 'test query',
                ],
            ]
        );

        // Assert
        $this->assertEquals(25000, $transaction->tokens_consumed);
        $this->assertEquals('claude-3-5-sonnet-20241022', $transaction->ai_model);
        $this->assertIsArray($transaction->metadata);
        $this->assertEquals(15000, $transaction->metadata['input_tokens']);
        $this->assertEquals(10000, $transaction->metadata['output_tokens']);
    }

    /** @test */
    public function it_uses_atomic_transaction_for_deduction() {
        // This test verifies DB transaction works correctly
        // If deduction fails, user balance should NOT change

        try {
            // Force an error by passing invalid data
            $this->service->deductCredits(
                user: $this->user,
                credits: -50, // Invalid negative amount
                sourceType: 'test'
            );
        } catch (\Exception $e) {
            // Exception expected
        }

        // Assert user balance unchanged
        $this->user->refresh();
        $this->assertEquals(1000, $this->user->ai_credits_balance);
    }

    /**
     * Helper method to invoke protected methods for testing
     */
    protected function invokeMethod($object, $methodName, array $parameters = []) {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
