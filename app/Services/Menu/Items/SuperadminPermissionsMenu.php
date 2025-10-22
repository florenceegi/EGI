<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Permissions Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminPermissionsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_permissions',
            route: 'superadmin.permissions.index',
            icon: 'superadmin-dashboard',
            permission: null // No permission check - protected by superadmin role via middleware
        );
    }
}
