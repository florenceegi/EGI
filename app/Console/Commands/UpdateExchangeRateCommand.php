<?php

namespace App\Console\Commands;

use App\Services\AiCreditsService;
use Illuminate\Console\Command;

/**
 * Update Exchange Rate Command
 *
 * Updates USD to EUR exchange rate from external API.
 * Should be scheduled to run daily.
 *
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Exchange Rate)
 * @date 2025-10-28
 * @purpose Keep exchange rates up-to-date for accurate credit pricing
 */
class UpdateExchangeRateCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-credits:update-exchange-rate
                            {--manual-rate= : Manually set exchange rate (USD to EUR)}
                            {--force : Force update even if cache is fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update USD to EUR exchange rate for AI credits pricing';

    /**
     * Execute the console command.
     *
     * @param AiCreditsService $creditsService
     * @return int
     */
    public function handle(AiCreditsService $creditsService): int {
        $this->info('🔄 Updating USD to EUR exchange rate...');

        // Check if manual rate provided
        $manualRate = $this->option('manual-rate');

        if ($manualRate) {
            // Validate manual rate
            $rate = (float) $manualRate;

            if ($rate <= 0 || $rate > 2.0) {
                $this->error('❌ Invalid manual rate. Must be between 0 and 2.0');
                return Command::FAILURE;
            }

            if (!$this->confirm("⚠️  Set manual exchange rate to {$rate}? This will override API.", true)) {
                $this->info('Cancelled.');
                return Command::SUCCESS;
            }

            $success = $creditsService->updateExchangeRate($rate);

            if ($success) {
                $this->info("✅ Exchange rate manually set to {$rate}");
                $this->displayCurrentRate($creditsService);
                return Command::SUCCESS;
            } else {
                $this->error('❌ Failed to update exchange rate');
                return Command::FAILURE;
            }
        }

        // Check if force update
        if (!$this->option('force') && \Cache::has('exchange_rate_usd_to_eur')) {
            $this->info('ℹ️  Exchange rate already cached. Use --force to update anyway.');
            $this->displayCurrentRate($creditsService);
            return Command::SUCCESS;
        }

        // Update from API
        $success = $creditsService->updateExchangeRate();

        if ($success) {
            $this->info('✅ Exchange rate updated successfully');
            $this->displayCurrentRate($creditsService);
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to update exchange rate from API');
            $this->warn('Using fallback rate from config');
            $this->displayCurrentRate($creditsService);
            return Command::FAILURE;
        }
    }

    /**
     * Display current exchange rate info
     *
     * @param AiCreditsService $creditsService
     * @return void
     */
    protected function displayCurrentRate(AiCreditsService $creditsService): void {
        $info = $creditsService->getExchangeRateInfo();

        $this->newLine();
        $this->line('📊 Current Exchange Rate Info:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("Rate: {$info['rate']} (USD to EUR)");
        $this->line("Source: {$info['source']}");

        if ($info['last_updated']) {
            $this->line("Last Updated: {$info['last_updated']}");
        }

        $this->newLine();
        $this->line('💱 Conversion Examples:');
        $this->line("  • $1.00 USD = €{$info['1_usd_in_eur']}");
        $this->line("  • €1.00 EUR = ${$info['1_eur_in_usd']} USD");
        $this->newLine();

        // Example cost calculation
        $exampleTokens = 100000; // 100k tokens
        $exampleInputCost = ($exampleTokens / 1_000_000) * 3.00; // $0.30
        $exampleOutputCost = ($exampleTokens / 1_000_000) * 15.00; // $1.50
        $exampleTotalUSD = $exampleInputCost + $exampleOutputCost; // $1.80
        $exampleTotalEUR = $exampleTotalUSD * $info['rate'];
        $exampleCredits = (int) ceil($exampleTotalEUR * 100);

        $this->line('💰 Example Cost (100k input + 100k output tokens):');
        $this->line("  • USD: $" . number_format($exampleTotalUSD, 2));
        $this->line("  • EUR: €" . number_format($exampleTotalEUR, 2));
        $this->line("  • Credits: {$exampleCredits}");
        $this->newLine();
    }
}
