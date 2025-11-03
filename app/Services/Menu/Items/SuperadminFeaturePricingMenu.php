<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * SuperAdmin Feature Pricing Management Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 */
class SuperadminFeaturePricingMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_feature_pricing',
            route: 'admin.pricing.index', // Fixed: admin.* instead of superadmin.*
            icon: 'superadmin-egili-token',
            permission: null // Admin + SuperAdmin access
        );
    }
}