<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * Admin Featured EGI Calendar Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Panel)
 * @date 2025-11-02
 */
class AdminFeaturedCalendarMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.admin_featured',
            route: 'admin.featured.pending',
            icon: 'superadmin-dashboard',
            permission: null
        );
    }
}