<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Egili Token Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminEgiliManagementMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.superadmin_egili_management',
            route: 'admin.egili.index', // Fixed: admin.* instead of superadmin.*
            icon: 'superadmin-egili-credits',
            permission: 'role:superadmin' // SuperAdmin ONLY
        );
    }
}
