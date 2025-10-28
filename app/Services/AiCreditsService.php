<?php

namespace App\Services;

use App\Models\User;
use App\Models\AiCreditsTransaction;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * AI Credits Service
 *
 * Gestisce crediti AI:
 * - Token tracking e conversione a crediti
 * - Deduct credits per usage
 * - Cost calculation (Claude pricing)
 * - Balance management
 * - Refunds per failed jobs
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - NATAN Chunking Cost Tracking)
 * @date 2025-10-28
 * @purpose Implement cost tracking for Claude AI usage with credit deduction
 */
class AiCreditsService {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;
    protected AuditLogService $auditService;

    /**
     * Claude Sonnet 3.5 Pricing (USD per 1M tokens)
     * Source: Anthropic pricing page (2025-10-28)
     */
    private const CLAUDE_SONNET_35_INPUT_PRICE = 3.00;   // $3.00 per 1M input tokens
    private const CLAUDE_SONNET_35_OUTPUT_PRICE = 15.00; // $15.00 per 1M output tokens

    /**
     * Credits conversion rate
     * 1 EUR = 100 credits (configurable)
     */
    private const CREDITS_PER_EUR = 100;

    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->auditService = $auditService;
    }

    /**
     * Get current USD to EUR exchange rate
     *
     * Priority:
     * 1. Cache (refreshed daily)
     * 2. Config fallback
     * 3. Hardcoded safe fallback (1.0 = no conversion)
     *
     * @return float Exchange rate (USD to EUR)
     */
    protected function getExchangeRate(): float {
        // Try cache first (updated daily by scheduled task)
        $cachedRate = \Cache::get('exchange_rate_usd_to_eur');

        if ($cachedRate && is_numeric($cachedRate) && $cachedRate > 0) {
            return (float) $cachedRate;
        }

        // Fallback to config
        $configRate = config('ai-credits.usd_to_eur_rate');

        if ($configRate && is_numeric($configRate) && $configRate > 0) {
            return (float) $configRate;
        }

        // Ultimate fallback: 1.0 (no conversion, charges in USD equivalent)
        // This is SAFE for PA: if exchange APIs fail, we charge 1:1 (slightly more conservative)
        $this->logger->warning('Exchange rate not available, using safe fallback 1.0', [
            'log_category' => 'AI_CREDITS_EXCHANGE_RATE_FALLBACK'
        ]);

        return 1.0;
    }

    /**
     * Calculate cost in credits from Claude usage tokens
     *
     * @param int $inputTokens Input tokens consumed
     * @param int $outputTokens Output tokens generated
     * @return int Credits to deduct (rounded up)
     */
    public function calculateCreditsFromTokens(int $inputTokens, int $outputTokens): int {
        try {
            // Cost in USD
            $inputCostUSD = ($inputTokens / 1_000_000) * self::CLAUDE_SONNET_35_INPUT_PRICE;
            $outputCostUSD = ($outputTokens / 1_000_000) * self::CLAUDE_SONNET_35_OUTPUT_PRICE;
            $totalCostUSD = $inputCostUSD + $outputCostUSD;

            // Get current exchange rate (dynamic, updated daily)
            $exchangeRate = $this->getExchangeRate();

            // Convert USD to EUR
            $totalCostEUR = $totalCostUSD * $exchangeRate;

            // Convert EUR to credits (rounded up for PA safety)
            $credits = (int) ceil($totalCostEUR * self::CREDITS_PER_EUR);

            $this->logger->debug('Credits calculated from tokens', [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'cost_usd' => round($totalCostUSD, 6),
                'exchange_rate' => $exchangeRate,
                'cost_eur' => round($totalCostEUR, 6),
                'credits' => $credits,
                'log_category' => 'AI_CREDITS_CALCULATION'
            ]);

            return $credits;
        } catch (\Exception $e) {
            // UEM: Credits calculation failed
            $this->errorManager->handle('AI_CREDITS_CALCULATION_FAILED', [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ], $e);

            // Fallback sicuro per PA: se calcolo fallisce, assume 0 (no charge)
            return 0;
        }
    }

    /**
     * Check if user has sufficient credits
     *
     * @param User $user User to check
     * @param int $requiredCredits Credits needed
     * @return bool True if user has enough
     */
    public function hasEnoughCredits(User $user, int $requiredCredits): bool {
        $currentBalance = $user->ai_credits_balance ?? 0;
        $hasEnough = $currentBalance >= $requiredCredits;

        $this->logger->debug('Credits sufficiency check', [
            'user_id' => $user->id,
            'current_balance' => $currentBalance,
            'required_credits' => $requiredCredits,
            'has_enough' => $hasEnough,
            'log_category' => 'AI_CREDITS_CHECK'
        ]);

        return $hasEnough;
    }

    /**
     * Deduct credits from user (usage transaction)
     *
     * @param User $user User to deduct from
     * @param int $credits Credits to deduct
     * @param string $sourceType Feature used (ai_pa_analysis, ai_trait_generation, etc)
     * @param int|null $sourceId ID of source model
     * @param array $metadata Additional tracking data (tokens, feature_parameters, etc)
     * @return AiCreditsTransaction Created transaction
     * @throws \Exception If insufficient credits
     */
    public function deductCredits(
        User $user,
        int $credits,
        string $sourceType,
        ?int $sourceId = null,
        array $metadata = []
    ): AiCreditsTransaction {
        try {
            DB::beginTransaction();

            // 1. Check balance
            $balanceBefore = $user->ai_credits_balance ?? 0;

            if ($balanceBefore < $credits) {
                // UEM: Insufficient credits error
                $this->errorManager->handle('AI_CREDITS_INSUFFICIENT', [
                    'user_id' => $user->id,
                    'balance' => $balanceBefore,
                    'required' => $credits,
                    'source_type' => $sourceType,
                ]);

                throw new \Exception("Insufficient credits: has {$balanceBefore}, needs {$credits}");
            }

            // 2. Calculate new balance
            $balanceAfter = $balanceBefore - $credits;

            // 3. Create transaction
            $transaction = AiCreditsTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'usage',
                'operation' => 'subtract',
                'amount' => $credits,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'feature_used' => $sourceType,
                'tokens_consumed' => $metadata['tokens_consumed'] ?? 0,
                'ai_model' => $metadata['ai_model'] ?? 'claude-3-5-sonnet-20241022',
                'feature_parameters' => $metadata['feature_parameters'] ?? null,
                'subscription_tier' => $user->ai_subscription_tier ?? 'free',
                'was_free_tier' => ($user->ai_subscription_tier ?? 'free') === 'free',
                'status' => 'completed',
                'metadata' => $metadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 4. Update user balance
            $user->update([
                'ai_credits_balance' => $balanceAfter,
                'ai_credits_lifetime_used' => ($user->ai_credits_lifetime_used ?? 0) + $credits,
            ]);

            // 5. GDPR: Audit trail per modifica dato finanziario personale
            $this->auditService->logUserAction(
                $user,
                'ai_credits_deducted',
                [
                    'credits_deducted' => $credits,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'source_type' => $sourceType,
                    'transaction_id' => $transaction->id,
                    'tokens_consumed' => $metadata['tokens_consumed'] ?? 0,
                ],
                GdprActivityCategory::AI_CREDITS_USAGE
            );

            DB::commit();

            $this->logger->info('Credits deducted successfully', [
                'user_id' => $user->id,
                'credits_deducted' => $credits,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $sourceType,
                'transaction_id' => $transaction->id,
                'log_category' => 'AI_CREDITS_DEDUCT_SUCCESS'
            ]);

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();

            // UEM: Credits deduction failed
            $this->errorManager->handle('AI_CREDITS_DEDUCT_FAILED', [
                'user_id' => $user->id,
                'credits' => $credits,
                'source_type' => $sourceType,
                'balance' => $user->ai_credits_balance ?? 0,
            ], $e);

            throw $e;
        }
    }

    /**
     * Refund credits (for failed jobs)
     *
     * @param User $user User to refund
     * @param int $credits Credits to refund
     * @param string $reason Refund reason
     * @param int|null $originalTransactionId ID of original deduct transaction
     * @return AiCreditsTransaction Created refund transaction
     */
    public function refundCredits(
        User $user,
        int $credits,
        string $reason,
        ?int $originalTransactionId = null
    ): AiCreditsTransaction {
        try {
            DB::beginTransaction();

            // 1. Get current balance
            $balanceBefore = $user->ai_credits_balance ?? 0;
            $balanceAfter = $balanceBefore + $credits;

            // 2. Create refund transaction
            $transaction = AiCreditsTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'refund',
                'operation' => 'add',
                'amount' => $credits,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => 'refund',
                'admin_notes' => $reason,
                'status' => 'completed',
                'metadata' => [
                    'original_transaction_id' => $originalTransactionId,
                    'refund_reason' => $reason,
                    'refunded_at' => now()->toIso8601String(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 3. Update user balance
            $user->update([
                'ai_credits_balance' => $balanceAfter,
                'ai_credits_lifetime_used' => max(0, ($user->ai_credits_lifetime_used ?? 0) - $credits),
            ]);

            // 4. GDPR: Audit trail per rimborso crediti
            $this->auditService->logUserAction(
                $user,
                'ai_credits_refunded',
                [
                    'credits_refunded' => $credits,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reason' => $reason,
                    'transaction_id' => $transaction->id,
                    'original_transaction_id' => $originalTransactionId,
                ],
                GdprActivityCategory::AI_CREDITS_USAGE
            );

            DB::commit();

            $this->logger->info('Credits refunded successfully', [
                'user_id' => $user->id,
                'credits_refunded' => $credits,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reason' => $reason,
                'transaction_id' => $transaction->id,
                'log_category' => 'AI_CREDITS_REFUND_SUCCESS'
            ]);

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();

            // UEM: Credits refund failed
            $this->errorManager->handle('AI_CREDITS_REFUND_FAILED', [
                'user_id' => $user->id,
                'credits' => $credits,
                'reason' => $reason,
            ], $e);

            throw $e;
        }
    }

    /**
     * Get estimated cost for analysis
     * (per preview prima di eseguire chunking)
     *
     * @param int $totalActs Number of PA acts to analyze
     * @param int $chunkSize Chunk size
     * @return array ['estimated_credits' => int, 'estimated_cost_eur' => float]
     */
    public function getEstimatedCost(int $totalActs, int $chunkSize): array {
        try {
            // Stima conservativa (assumiamo worst-case token usage)
            // Chunk processing: ~1000 input + ~500 output tokens per act (media)
            $tokensPerAct = 1500;
            $totalChunks = (int) ceil($totalActs / $chunkSize);

            // Chunk processing tokens
            $chunkInputTokens = $totalActs * 1000; // ~1000 input per act
            $chunkOutputTokens = $totalActs * 500;  // ~500 output per act

            // Aggregation tokens (basato su numero chunk)
            $aggregationInputTokens = $totalChunks * 800;  // ~800 per chunk summary
            $aggregationOutputTokens = 1500;                // ~1500 final output

            $totalInputTokens = $chunkInputTokens + $aggregationInputTokens;
            $totalOutputTokens = $chunkOutputTokens + $aggregationOutputTokens;

            // Calculate credits
            $estimatedCredits = $this->calculateCreditsFromTokens($totalInputTokens, $totalOutputTokens);

            // Calculate EUR
            $estimatedCostEUR = $estimatedCredits / self::CREDITS_PER_EUR;

            $this->logger->debug('Cost estimation calculated', [
                'total_acts' => $totalActs,
                'chunk_size' => $chunkSize,
                'total_chunks' => $totalChunks,
                'estimated_input_tokens' => $totalInputTokens,
                'estimated_output_tokens' => $totalOutputTokens,
                'estimated_credits' => $estimatedCredits,
                'estimated_cost_eur' => round($estimatedCostEUR, 2),
                'log_category' => 'AI_CREDITS_ESTIMATION'
            ]);

            return [
                'estimated_credits' => $estimatedCredits,
                'estimated_cost_eur' => round($estimatedCostEUR, 2),
                'total_chunks' => $totalChunks,
                'total_input_tokens' => $totalInputTokens,
                'total_output_tokens' => $totalOutputTokens,
            ];
        } catch (\Exception $e) {
            // UEM: Cost estimation failed
            $this->errorManager->handle('AI_CREDITS_ESTIMATION_FAILED', [
                'total_acts' => $totalActs,
                'chunk_size' => $chunkSize,
            ], $e);

            // Fallback sicuro
            return [
                'estimated_credits' => 0,
                'estimated_cost_eur' => 0.00,
                'total_chunks' => 0,
                'total_input_tokens' => 0,
                'total_output_tokens' => 0,
            ];
        }
    }

    /**
     * Update exchange rate in cache
     *
     * Should be called daily by scheduled task.
     * Can use multiple sources with fallback.
     *
     * @param float|null $manualRate Optional manual rate (for admin override)
     * @return bool Success status
     */
    public function updateExchangeRate(?float $manualRate = null): bool {
        // Validate manual rate BEFORE try-catch (must throw)
        if ($manualRate !== null && $manualRate <= 0) {
            throw new \Exception('Exchange rate must be positive');
        }

        try {
            if ($manualRate !== null) {
                // Manual override by admin
                \Cache::put('exchange_rate_usd_to_eur', $manualRate, now()->addDays(7));

                $this->logger->info('Exchange rate updated manually', [
                    'rate' => $manualRate,
                    'expires_at' => now()->addDays(7)->toIso8601String(),
                    'log_category' => 'AI_CREDITS_EXCHANGE_RATE_UPDATE'
                ]);

                return true;
            }

            // Try European Central Bank (ECB) API (free, no key required)
            try {
                $response = \Http::timeout(10)->get('https://api.exchangerate.host/latest', [
                    'base' => 'USD',
                    'symbols' => 'EUR',
                ]);

                if ($response->successful() && isset($response->json()['rates']['EUR'])) {
                    $rate = (float) $response->json()['rates']['EUR'];

                    if ($rate > 0 && $rate < 2.0) { // Sanity check (rate should be ~0.85-0.95)
                        \Cache::put('exchange_rate_usd_to_eur', $rate, now()->addDays(1));

                        $this->logger->info('Exchange rate updated from ECB API', [
                            'rate' => $rate,
                            'source' => 'exchangerate.host',
                            'expires_at' => now()->addDays(1)->toIso8601String(),
                            'log_category' => 'AI_CREDITS_EXCHANGE_RATE_UPDATE'
                        ]);

                        return true;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->warning('ECB exchange rate API failed', [
                    'error' => $e->getMessage(),
                    'log_category' => 'AI_CREDITS_EXCHANGE_RATE_API_ERROR'
                ]);
            }

            // Fallback: Use config rate if API fails
            $configRate = config('ai-credits.usd_to_eur_rate', 0.92);
            \Cache::put('exchange_rate_usd_to_eur', $configRate, now()->addDays(1));

            $this->logger->warning('Exchange rate updated from config fallback', [
                'rate' => $configRate,
                'reason' => 'API unavailable',
                'log_category' => 'AI_CREDITS_EXCHANGE_RATE_FALLBACK'
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Exchange rate update failed', [
                'error' => $e->getMessage(),
                'log_category' => 'AI_CREDITS_EXCHANGE_RATE_ERROR'
            ]);

            return false;
        }
    }

    /**
     * Get current exchange rate info (for admin display)
     *
     * @return array
     */
    public function getExchangeRateInfo(): array {
        $rate = $this->getExchangeRate();
        $source = 'fallback';
        $lastUpdated = null;

        if (\Cache::has('exchange_rate_usd_to_eur')) {
            $source = 'cache';
            // Cache doesn't expose TTL easily, but we know it's updated daily
            $lastUpdated = now()->startOfDay()->toIso8601String();
        } elseif (config('ai-credits.usd_to_eur_rate')) {
            $source = 'config';
        }

        return [
            'rate' => $rate,
            'source' => $source,
            'last_updated' => $lastUpdated,
            '1_usd_in_eur' => round($rate, 4),
            '1_eur_in_usd' => round(1 / $rate, 4),
        ];
    }
}
