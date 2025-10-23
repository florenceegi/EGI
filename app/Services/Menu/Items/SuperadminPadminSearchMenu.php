<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Padmin Analyzer - Search Menu Item
 */
class SuperadminPadminSearchMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_padmin_search',
            route: 'superadmin.padmin.search',
            icon: 'superadmin-ai-brain',
            permission: null
        );
    }
}
