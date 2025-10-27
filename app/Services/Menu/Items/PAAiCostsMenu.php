<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * AI Costs Dashboard Menu Item
 * 
 * Dashboard per monitoring costi AI (Anthropic Claude, OpenAI, Perplexity)
 * 
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Cost Monitor)
 * @date 2025-10-27
 */
class PAAiCostsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_ai_costs',
            route: 'pa.ai-costs.dashboard',
            icon: 'pa-ai-costs', // Will fallback to default if custom icon not found
            permission: null // Accessible to all PA entities
        );
    }
}
