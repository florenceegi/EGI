<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Padmin Analyzer - Symbols Menu Item
 */
class SuperadminPadminSymbolsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_padmin_symbols',
            route: 'superadmin.padmin.symbols',
            icon: 'superadmin-padmin-symbols',
            permission: null
        );
    }
}
