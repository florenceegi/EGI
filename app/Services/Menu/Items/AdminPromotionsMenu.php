<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * Admin Promotions Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Admin Panel)
 * @date 2025-11-02
 */
class AdminPromotionsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.admin_promotions',
            route: 'admin.promotions.index',
            icon: 'superadmin-egili-token',
            permission: 'role:admin|superadmin'
        );
    }
}
