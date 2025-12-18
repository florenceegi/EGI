<?php

namespace App\View\Components;

use App\Contracts\GoldPriceServiceInterface;
use App\Models\Egi;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

/**
 * GoldBarInfo Component
 *
 * Autonomous Blade component for displaying gold bar information and indicative value.
 * Shows real-time gold price quotation with automatic refresh.
 *
 * @package App\View\Components
 * @author Fabio Cherici
 * @version 1.0.0
 * @date 2025-12-08
 */
class GoldBarInfo extends Component {
    /**
     * The EGI model (can be null if loading)
     */
    public ?Egi $egi;

    /**
     * Currency for gold quotation
     */
    public string $currency;

    /**
     * Calculated gold bar value data
     */
    public ?array $goldValue = null;

    /**
     * Whether to show detailed breakdown
     */
    public bool $showDetails;

    /**
     * Component size: 'compact', 'normal', 'large'
     */
    public string $size;

    /**
     * Whether this is a gold bar
     */
    public bool $isGoldBar = false;

    /**
     * Error message if any
     */
    public ?string $error = null;

    /**
     * Create a new component instance.
     *
     * @param Egi|null $egi The EGI model
     * @param string $currency Currency code (EUR, USD, GBP)
     * @param bool $showDetails Whether to show detailed breakdown
     * @param string $size Component size variant
     */
    public function __construct(
        ?Egi $egi = null,
        string $currency = 'EUR',
        bool $showDetails = true,
        string $size = 'normal'
    ) {
        $this->egi = $egi;
        $this->currency = strtoupper($currency);
        $this->showDetails = $showDetails;
        $this->size = $size;

        if ($egi) {
            $this->calculateGoldValue();
        }
    }

    /**
     * Calculate gold value using GoldPriceService
     */
    protected function calculateGoldValue(): void {
        if (!$this->egi) {
            return;
        }

        $this->isGoldBar = $this->egi->isGoldBar();

        if (!$this->isGoldBar) {
            return;
        }

        try {
            /** @var GoldPriceServiceInterface $goldService */ // Changed type hint
            $goldService = app(GoldPriceServiceInterface::class); // Changed class reference
            $this->goldValue = $goldService->calculateFromEgi($this->egi, $this->currency);

            if (!$this->goldValue) {
                $this->error = __('gold_bar.error');
            }
        } catch (\Exception $e) {
            $this->error = __('gold_bar.error');
        }
    }

    /**
     * Get purity description
     */
    public function getPurityDescription(): string {
        if (!$this->egi) {
            return '';
        }

        $purity = $this->egi->getGoldPurity();
        return match ($purity) {
            '999' => __('gold_bar.purity_999'),
            '995' => __('gold_bar.purity_995'),
            '990' => __('gold_bar.purity_990'),
            '916' => __('gold_bar.purity_916'),
            '750' => __('gold_bar.purity_750'),
            default => $purity ?? '',
        };
    }

    /**
     * Get weight unit translation
     */
    public function getWeightUnitLabel(): string {
        if (!$this->egi) {
            return '';
        }

        $unit = $this->egi->getGoldWeightUnit();
        return match ($unit) {
            'Grams' => __('gold_bar.unit_grams'),
            'Ounces' => __('gold_bar.unit_ounces'),
            'Troy Ounces' => __('gold_bar.unit_troy_ounces'),
            default => $unit ?? '',
        };
    }

    /**
     * Format currency value
     */
    public function formatCurrency(float $value): string {
        return number_format($value, 2, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Get size classes for the component
     */
    public function getSizeClasses(): string {
        return match ($this->size) {
            'compact' => 'p-3 text-sm',
            'large' => 'p-6 text-lg',
            default => 'p-4 text-base',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string {
        return view('components.gold-bar-info');
    }
}
