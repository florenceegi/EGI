<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Padmin Analyzer - Dashboard Menu Item
 */
class SuperadminPadminDashboardMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_padmin_dashboard',
            route: 'superadmin.padmin.dashboard',
            icon: 'superadmin-ai-brain',
            permission: null
        );
    }
}
