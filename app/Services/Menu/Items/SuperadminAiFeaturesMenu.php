<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin AI Features Configuration Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminAiFeaturesMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_ai_features',
            route: 'superadmin.ai.features.index',
            icon: 'superadmin-ai-config',
            permission: null // No permission check - protected by superadmin role via middleware
        );
    }
}
