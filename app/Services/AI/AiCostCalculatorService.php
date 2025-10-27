<?php

namespace App\Services\AI;

use App\Models\NatanChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * AI Cost Calculator Service
 * 
 * Calculates costs for AI API usage across multiple providers:
 * - Anthropic Claude (Sonnet, Opus, Haiku)
 * - OpenAI (GPT-4, GPT-3.5, Embeddings)
 * - Perplexity AI (Search)
 * 
 * FEATURES:
 * - Real-time cost calculation based on tokens
 * - Budget tracking per provider
 * - Monthly spending reports
 * - Cost optimization suggestions
 * - Alert system for budget limits
 * 
 * @package App\Services\AI
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
 * @date 2025-10-27
 */
class AiCostCalculatorService {
    protected UltraLogManager $logger;

    /**
     * Pricing per 1M tokens (in USD)
     * Updated: 2025-10-27
     * 
     * Source: Official provider pricing pages
     */
    protected array $pricing = [
        // Anthropic Claude 3.5 Sonnet
        'claude-3-5-sonnet-20241022' => [
            'input' => 3.00,   // $3 per 1M input tokens
            'output' => 15.00, // $15 per 1M output tokens
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'Anthropic',
        ],

        // Anthropic Claude 3 Opus
        'claude-3-opus-20240229' => [
            'input' => 15.00,
            'output' => 75.00,
            'name' => 'Claude 3 Opus',
            'provider' => 'Anthropic',
        ],

        // Anthropic Claude 3 Haiku
        'claude-3-haiku-20240307' => [
            'input' => 0.25,
            'output' => 1.25,
            'name' => 'Claude 3 Haiku',
            'provider' => 'Anthropic',
        ],

        // OpenAI GPT-4 Turbo
        'gpt-4-turbo' => [
            'input' => 10.00,
            'output' => 30.00,
            'name' => 'GPT-4 Turbo',
            'provider' => 'OpenAI',
        ],

        // OpenAI GPT-3.5 Turbo
        'gpt-3.5-turbo' => [
            'input' => 0.50,
            'output' => 1.50,
            'name' => 'GPT-3.5 Turbo',
            'provider' => 'OpenAI',
        ],

        // OpenAI Embeddings (text-embedding-ada-002)
        'text-embedding-ada-002' => [
            'input' => 0.10,
            'output' => 0.00, // Embeddings have no output cost
            'name' => 'Ada Embeddings v2',
            'provider' => 'OpenAI',
        ],

        // Perplexity AI (search queries)
        'perplexity-sonar' => [
            'input' => 1.00,  // Estimated per 1M tokens
            'output' => 1.00,
            'name' => 'Perplexity Sonar',
            'provider' => 'Perplexity',
        ],
    ];

    public function __construct(UltraLogManager $logger) {
        $this->logger = $logger;
    }

    /**
     * Calculate cost for a single message
     * 
     * @param string $model AI model identifier
     * @param int $tokensInput Input tokens
     * @param int $tokensOutput Output tokens
     * @return array Cost breakdown
     */
    public function calculateMessageCost(string $model, int $tokensInput, int $tokensOutput): array {
        if (!isset($this->pricing[$model])) {
            $this->logger->warning('[AiCostCalculator] Unknown model', ['model' => $model]);

            return [
                'model' => $model,
                'provider' => 'Unknown',
                'input_cost' => 0,
                'output_cost' => 0,
                'total_cost' => 0,
                'tokens_input' => $tokensInput,
                'tokens_output' => $tokensOutput,
                'is_estimated' => true,
            ];
        }

        $pricing = $this->pricing[$model];

        // Calculate costs (pricing is per 1M tokens)
        $inputCost = ($tokensInput / 1_000_000) * $pricing['input'];
        $outputCost = ($tokensOutput / 1_000_000) * $pricing['output'];
        $totalCost = $inputCost + $outputCost;

        return [
            'model' => $model,
            'model_name' => $pricing['name'],
            'provider' => $pricing['provider'],
            'input_cost' => round($inputCost, 6),
            'output_cost' => round($outputCost, 6),
            'total_cost' => round($totalCost, 6),
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'tokens_total' => $tokensInput + $tokensOutput,
            'is_estimated' => false,
        ];
    }

    /**
     * Get spending statistics for a time period
     * 
     * @param Carbon $startDate Start date
     * @param Carbon $endDate End date
     * @param int|null $userId Optional user filter
     * @return array Statistics
     */
    public function getSpendingStats(Carbon $startDate, Carbon $endDate, ?int $userId = null): array {
        $cacheKey = "ai_costs_stats_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate, $userId) {
            $query = NatanChatMessage::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('ai_model')
                ->whereNotNull('tokens_input');

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $messages = $query->get();

            // Calculate costs per provider
            $byProvider = [];
            $byModel = [];
            $totalCost = 0;
            $totalTokens = 0;
            $totalMessages = $messages->count();

            foreach ($messages as $message) {
                $cost = $this->calculateMessageCost(
                    $message->ai_model,
                    $message->tokens_input ?? 0,
                    $message->tokens_output ?? 0
                );

                $provider = $cost['provider'];
                $model = $cost['model'];

                // Aggregate by provider
                if (!isset($byProvider[$provider])) {
                    $byProvider[$provider] = [
                        'provider' => $provider,
                        'cost' => 0,
                        'tokens' => 0,
                        'messages' => 0,
                    ];
                }
                $byProvider[$provider]['cost'] += $cost['total_cost'];
                $byProvider[$provider]['tokens'] += $cost['tokens_total'];
                $byProvider[$provider]['messages']++;

                // Aggregate by model
                if (!isset($byModel[$model])) {
                    $byModel[$model] = [
                        'model' => $model,
                        'model_name' => $cost['model_name'] ?? $model,
                        'provider' => $provider,
                        'cost' => 0,
                        'tokens' => 0,
                        'messages' => 0,
                    ];
                }
                $byModel[$model]['cost'] += $cost['total_cost'];
                $byModel[$model]['tokens'] += $cost['tokens_total'];
                $byModel[$model]['messages']++;

                $totalCost += $cost['total_cost'];
                $totalTokens += $cost['tokens_total'];
            }

            return [
                'period' => [
                    'start' => $startDate->toIso8601String(),
                    'end' => $endDate->toIso8601String(),
                    'days' => $startDate->diffInDays($endDate),
                ],
                'totals' => [
                    'cost' => round($totalCost, 2),
                    'tokens' => $totalTokens,
                    'messages' => $totalMessages,
                    'avg_cost_per_message' => $totalMessages > 0 ? round($totalCost / $totalMessages, 4) : 0,
                ],
                'by_provider' => array_values($byProvider),
                'by_model' => array_values($byModel),
            ];
        });
    }

    /**
     * Get current month spending
     */
    public function getCurrentMonthSpending(?int $userId = null): array {
        return $this->getSpendingStats(
            Carbon::now()->startOfMonth(),
            Carbon::now(),
            $userId
        );
    }

    /**
     * Get last 30 days spending
     */
    public function getLast30DaysSpending(?int $userId = null): array {
        return $this->getSpendingStats(
            Carbon::now()->subDays(30),
            Carbon::now(),
            $userId
        );
    }

    /**
     * Get daily spending trend (last 30 days)
     */
    public function getDailySpendingTrend(?int $userId = null): array {
        $cacheKey = "ai_costs_daily_trend_{$userId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $query = NatanChatMessage::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('ai_model')
                ->whereNotNull('tokens_input');

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $messages = $query->get()->groupBy(function ($message) {
                return $message->created_at->format('Y-m-d');
            });

            $trend = [];
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $dayMessages = $messages->get($dateKey, collect());

                $dayCost = 0;
                $dayTokens = 0;

                foreach ($dayMessages as $message) {
                    $cost = $this->calculateMessageCost(
                        $message->ai_model,
                        $message->tokens_input ?? 0,
                        $message->tokens_output ?? 0
                    );
                    $dayCost += $cost['total_cost'];
                    $dayTokens += $cost['tokens_total'];
                }

                $trend[] = [
                    'date' => $dateKey,
                    'cost' => round($dayCost, 2),
                    'tokens' => $dayTokens,
                    'messages' => $dayMessages->count(),
                ];

                $currentDate->addDay();
            }

            return $trend;
        });
    }

    /**
     * Get available models and their pricing
     */
    public function getAvailableModels(): array {
        $models = [];

        foreach ($this->pricing as $modelId => $pricing) {
            $models[] = [
                'id' => $modelId,
                'name' => $pricing['name'],
                'provider' => $pricing['provider'],
                'input_price_per_1m' => $pricing['input'],
                'output_price_per_1m' => $pricing['output'],
            ];
        }

        return $models;
    }

    /**
     * Check if spending exceeds budget
     */
    public function checkBudgetAlert(float $currentSpending, float $monthlyBudget): array {
        $percentage = ($currentSpending / $monthlyBudget) * 100;

        return [
            'current' => round($currentSpending, 2),
            'budget' => $monthlyBudget,
            'remaining' => round($monthlyBudget - $currentSpending, 2),
            'percentage' => round($percentage, 1),
            'alert_level' => $this->getAlertLevel($percentage),
            'is_exceeded' => $currentSpending > $monthlyBudget,
        ];
    }

    /**
     * Get alert level based on budget percentage
     */
    protected function getAlertLevel(float $percentage): string {
        if ($percentage >= 100) return 'critical';
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 75) return 'warning';
        return 'normal';
    }
}
