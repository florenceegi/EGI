<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Dashboard Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class PADashboardMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_dashboard',
            route: 'pa.dashboard',
            icon: 'pa-dashboard',
            permission: 'access_pa_dashboard'
        );
    }
}
