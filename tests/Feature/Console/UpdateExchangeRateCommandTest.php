<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use App\Services\AiCreditsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

/**
 * UpdateExchangeRateCommand Test
 *
 * Tests for exchange rate update console command.
 *
 * @package Tests\Feature\Console
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Testing)
 * @date 2025-10-28
 */
class UpdateExchangeRateCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_updates_exchange_rate_from_api_successfully()
    {
        // Arrange
        Http::fake([
            'api.exchangerate.host/*' => Http::response([
                'success' => true,
                'base' => 'USD',
                'rates' => ['EUR' => 0.9234]
            ], 200)
        ]);

        // Act
        $this->artisan('ai-credits:update-exchange-rate')
            ->expectsOutput('🔄 Updating USD to EUR exchange rate...')
            ->expectsOutput('✅ Exchange rate updated successfully')
            ->assertExitCode(0);

        // Assert
        $this->assertEquals(0.9234, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_sets_manual_exchange_rate()
    {
        // Act
        $this->artisan('ai-credits:update-exchange-rate', ['--manual-rate' => '0.95'])
            ->expectsConfirmation('⚠️  Set manual exchange rate to 0.95? This will override API.', 'yes')
            ->expectsOutput('✅ Exchange rate manually set to 0.95')
            ->assertExitCode(0);

        // Assert
        $this->assertEquals(0.95, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_rejects_invalid_manual_rate()
    {
        // Act
        $this->artisan('ai-credits:update-exchange-rate', ['--manual-rate' => '-0.5'])
            ->expectsOutput('❌ Invalid manual rate. Must be between 0 and 2.0')
            ->assertExitCode(1);

        // Assert - Cache should not be set
        $this->assertNull(Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_rejects_unrealistic_manual_rate()
    {
        // Act
        $this->artisan('ai-credits:update-exchange-rate', ['--manual-rate' => '5.0'])
            ->expectsOutput('❌ Invalid manual rate. Must be between 0 and 2.0')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_skips_update_when_cache_is_fresh_without_force()
    {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        // Act
        $this->artisan('ai-credits:update-exchange-rate')
            ->expectsOutput('ℹ️  Exchange rate already cached. Use --force to update anyway.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_forces_update_when_force_flag_provided()
    {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        Http::fake([
            'api.exchangerate.host/*' => Http::response([
                'success' => true,
                'base' => 'USD',
                'rates' => ['EUR' => 0.9234]
            ], 200)
        ]);

        // Act
        $this->artisan('ai-credits:update-exchange-rate', ['--force' => true])
            ->expectsOutput('✅ Exchange rate updated successfully')
            ->assertExitCode(0);

        // Assert - Rate should be updated
        $this->assertEquals(0.9234, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_falls_back_to_config_when_api_fails()
    {
        // Arrange
        config(['ai-credits.usd_to_eur_rate' => 0.92]);

        Http::fake([
            'api.exchangerate.host/*' => Http::response([], 500)
        ]);

        // Act
        $this->artisan('ai-credits:update-exchange-rate')
            ->expectsOutput('❌ Failed to update exchange rate from API')
            ->expectsOutput('Using fallback rate from config')
            ->assertExitCode(1);

        // Assert - Should use config fallback
        $this->assertEquals(0.92, Cache::get('exchange_rate_usd_to_eur'));
    }

    /** @test */
    public function it_displays_exchange_rate_info()
    {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.9234, now()->addDay());

        // Act & Assert
        $this->artisan('ai-credits:update-exchange-rate')
            ->expectsOutput('ℹ️  Exchange rate already cached. Use --force to update anyway.')
            ->expectsOutputToContain('📊 Current Exchange Rate Info:')
            ->expectsOutputToContain('Rate: 0.9234 (USD to EUR)')
            ->expectsOutputToContain('Source: cache')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_cost_calculation_examples()
    {
        // Arrange
        Cache::put('exchange_rate_usd_to_eur', 0.92, now()->addDay());

        // Act & Assert
        $this->artisan('ai-credits:update-exchange-rate')
            ->expectsOutputToContain('💱 Conversion Examples:')
            ->expectsOutputToContain('💰 Example Cost (100k input + 100k output tokens):')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_cancel_manual_rate_update()
    {
        // Act
        $this->artisan('ai-credits:update-exchange-rate', ['--manual-rate' => '0.95'])
            ->expectsConfirmation('⚠️  Set manual exchange rate to 0.95? This will override API.', 'no')
            ->expectsOutput('Cancelled.')
            ->assertExitCode(0);

        // Assert - Cache should not be set
        $this->assertNull(Cache::get('exchange_rate_usd_to_eur'));
    }
}
