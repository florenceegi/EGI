<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Padmin Analyzer - Statistics Menu Item
 */
class SuperadminPadminStatisticsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_padmin_statistics',
            route: 'superadmin.padmin.statistics',
            icon: 'superadmin-dashboard',
            permission: null
        );
    }
}
