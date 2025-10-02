<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA CoA Certificates Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class PACoAMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_coa',
            route: '#',
            icon: 'pa-coa',
            permission: 'institutional_certification',
            children: null,
            modalAction: 'pa-coa-coming-soon' // TODO: FASE 2 - Change to real route
        );
    }
}
