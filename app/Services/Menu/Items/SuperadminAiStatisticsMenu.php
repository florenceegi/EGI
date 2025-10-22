<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin AI Statistics Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminAiStatisticsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_ai_statistics',
            route: 'superadmin.ai.statistics.index',
            icon: 'superadmin-ai-stats',
            permission: null // No permission check - protected by superadmin role via middleware
        );
    }
}
