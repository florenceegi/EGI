<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AI Budget Setting Model
 * 
 * Stores budget configurations per AI provider
 * 
 * @property int $id
 * @property string $provider Provider name (anthropic, openai, perplexity)
 * @property float $monthly_budget Monthly budget in USD
 * @property float $alert_threshold Alert threshold percentage (e.g., 75%)
 * @property bool $alerts_enabled Enable/disable alerts
 * @property string|null $alert_email Email for budget alerts
 * @property string|null $notes Optional notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
 * @date 2025-10-27
 */
class AiBudgetSetting extends Model
{
    protected $table = 'ai_budget_settings';

    protected $fillable = [
        'provider',
        'monthly_budget',
        'alert_threshold',
        'alerts_enabled',
        'alert_email',
        'notes',
    ];

    protected $casts = [
        'monthly_budget' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'alerts_enabled' => 'boolean',
    ];

    /**
     * Get budget setting for a provider
     */
    public static function getForProvider(string $provider): ?self
    {
        return self::where('provider', $provider)->first();
    }

    /**
     * Get all provider budgets as array
     */
    public static function getAllBudgets(): array
    {
        return self::all()->keyBy('provider')->toArray();
    }

    /**
     * Check if budget alert should be triggered
     */
    public function shouldAlert(float $currentSpending): bool
    {
        if (!$this->alerts_enabled || $this->monthly_budget <= 0) {
            return false;
        }

        $percentage = ($currentSpending / $this->monthly_budget) * 100;
        return $percentage >= $this->alert_threshold;
    }
}

