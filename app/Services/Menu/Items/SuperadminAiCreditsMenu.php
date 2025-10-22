<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin AI Credits Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminAiCreditsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_ai_credits',
            route: 'superadmin.ai.credits.index',
            icon: 'superadmin-ai-credits',
            permission: null // No permission check - protected by superadmin role via middleware
        );
    }
}
