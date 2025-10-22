<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Padmin Analyzer - Violations Menu Item
 */
class SuperadminPadminViolationsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_padmin_violations',
            route: 'superadmin.padmin.violations',
            icon: 'superadmin-padmin-violations',
            permission: null
        );
    }
}
