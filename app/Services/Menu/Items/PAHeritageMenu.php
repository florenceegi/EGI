<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Heritage/Documents Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class PAHeritageMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_heritage',
            route: 'pa.heritage.index',
            icon: 'pa-heritage',
            permission: 'manage_institutional_collections'
        );
    }
}
