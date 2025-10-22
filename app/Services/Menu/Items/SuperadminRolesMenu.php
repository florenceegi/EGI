<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @Oracode MenuItem: SuperAdmin Roles & Permissions
 * 🎯 Purpose: Navigation to enterprise roles management center
 * 
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminRolesMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_roles',
            route: 'superadmin.roles.index',
            icon: 'superadmin-dashboard',
            permission: null // Protected by superadmin middleware
        );
    }
}



