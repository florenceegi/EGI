<?php

namespace App\Http\Controllers\PA;

use App\Http\Controllers\Controller;
use App\Models\AiBudgetSetting;
use App\Services\AI\AiCostCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * AI Costs Dashboard Controller
 * 
 * Provides REST API and views for AI costs monitoring
 * 
 * ENDPOINTS:
 * - GET  /pa/ai-costs              → Dashboard view
 * - GET  /pa/ai-costs/api/stats    → Get spending statistics
 * - GET  /pa/ai-costs/api/trend    → Get daily spending trend
 * - POST /pa/ai-costs/api/budget   → Update budget settings
 * 
 * @package App\Http\Controllers\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
 * @date 2025-10-27
 */
class AiCostsDashboardController extends Controller
{
    protected AiCostCalculatorService $costCalculator;
    protected UltraLogManager $logger;

    public function __construct(
        AiCostCalculatorService $costCalculator,
        UltraLogManager $logger
    ) {
        $this->costCalculator = $costCalculator;
        $this->logger = $logger;
    }

    /**
     * Show AI costs dashboard
     */
    public function index()
    {
        $user = auth()->user();

        $this->logger->info('[AiCostsDashboard] Accessing dashboard', [
            'user_id' => $user->id,
        ]);

        // Get current month spending
        $currentMonth = $this->costCalculator->getCurrentMonthSpending();
        
        // Get budget settings
        $budgets = AiBudgetSetting::getAllBudgets();

        // Get available models
        $models = $this->costCalculator->getAvailableModels();

        return view('pa.ai-costs.dashboard', [
            'current_month' => $currentMonth,
            'budgets' => $budgets,
            'models' => $models,
        ]);
    }

    /**
     * Get spending statistics (API)
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $period = $request->input('period', 'current_month'); // current_month, last_30_days, custom

        $this->logger->info('[AiCostsDashboard] Fetching stats', [
            'user_id' => $user->id,
            'period' => $period,
        ]);

        try {
            if ($period === 'current_month') {
                $stats = $this->costCalculator->getCurrentMonthSpending();
            } elseif ($period === 'last_30_days') {
                $stats = $this->costCalculator->getLast30DaysSpending();
            } else {
                // Custom period
                $startDate = \Carbon\Carbon::parse($request->input('start_date'));
                $endDate = \Carbon\Carbon::parse($request->input('end_date'));
                $stats = $this->costCalculator->getSpendingStats($startDate, $endDate);
            }

            // Add budget alerts
            $budgets = AiBudgetSetting::all();
            $alerts = [];

            foreach ($stats['by_provider'] as &$providerStats) {
                $budget = $budgets->firstWhere('provider', strtolower($providerStats['provider']));
                
                if ($budget) {
                    $alert = $this->costCalculator->checkBudgetAlert(
                        $providerStats['cost'],
                        $budget->monthly_budget
                    );
                    $providerStats['budget'] = $alert;
                    
                    if ($alert['alert_level'] !== 'normal') {
                        $alerts[] = [
                            'provider' => $providerStats['provider'],
                            'level' => $alert['alert_level'],
                            'message' => "Budget al {$alert['percentage']}% per {$providerStats['provider']}",
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'alerts' => $alerts,
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('[AiCostsDashboard] Failed to fetch stats', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch statistics',
            ], 500);
        }
    }

    /**
     * Get daily spending trend (API)
     */
    public function getTrend(Request $request): JsonResponse
    {
        $user = auth()->user();

        $this->logger->info('[AiCostsDashboard] Fetching trend', [
            'user_id' => $user->id,
        ]);

        try {
            $trend = $this->costCalculator->getDailySpendingTrend();

            return response()->json([
                'success' => true,
                'trend' => $trend,
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('[AiCostsDashboard] Failed to fetch trend', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch trend',
            ], 500);
        }
    }

    /**
     * Update budget settings (API)
     */
    public function updateBudget(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'provider' => 'required|string|in:anthropic,openai,perplexity',
            'monthly_budget' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0|max:100',
            'alerts_enabled' => 'boolean',
            'alert_email' => 'nullable|email',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->logger->info('[AiCostsDashboard] Updating budget', [
            'user_id' => $user->id,
            'provider' => $validated['provider'],
            'budget' => $validated['monthly_budget'],
        ]);

        try {
            $budget = AiBudgetSetting::updateOrCreate(
                ['provider' => $validated['provider']],
                $validated
            );

            return response()->json([
                'success' => true,
                'budget' => $budget,
                'message' => 'Budget aggiornato con successo',
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('[AiCostsDashboard] Failed to update budget', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update budget',
            ], 500);
        }
    }
}

