<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Equilibrium (EQUI) Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminEquilibriumManagementMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_equilibrium_management',
            route: 'superadmin.equilibrium.index',
            icon: 'superadmin-equilibrium',
            permission: null // No permission check - protected by superadmin role via middleware
        );
    }
}
