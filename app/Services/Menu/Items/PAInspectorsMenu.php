<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Inspectors Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class PAInspectorsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_inspectors',
            route: '#',
            icon: 'pa-inspectors',
            permission: 'cultural_heritage_management',
            children: null,
            modalAction: 'pa-inspectors-coming-soon' // TODO: FASE 3 - Change to real route
        );
    }
}
