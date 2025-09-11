<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class EgiCard extends Component {
    public $egi;
    public $collection;
    public $showPurchasePrice;
    public $hideReserveButton;
    public $portfolioContext;
    public $portfolioOwner;
    public $creatorPortfolioContext;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $egi = null,
        $collection = null,
        $showPurchasePrice = false,
        $hideReserveButton = false,
        $portfolioContext = false,
        $portfolioOwner = null,
        $creatorPortfolioContext = false
    ) {
        $this->egi = $egi;
        $this->collection = $collection;
        $this->showPurchasePrice = $showPurchasePrice;
        $this->hideReserveButton = $hideReserveButton;
        $this->portfolioContext = $portfolioContext;
        $this->portfolioOwner = $portfolioOwner;
        $this->creatorPortfolioContext = $creatorPortfolioContext;
    }

    /**
     * Get optimized image URL for card display
     * Uses 'card' variant (400x400) with fallback to original
     *
     * @return string|null
     */
    // Placeholder per eventuale futura ottimizzazione immagine (rimosso helper non presente)
    public function getOptimizedImageUrl(): ?string {
        return null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string {
        return view('components.egi-card');
    }
}
