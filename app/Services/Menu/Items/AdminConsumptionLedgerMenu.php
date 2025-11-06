<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * Admin Consumption Ledger Menu Item
 * 
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-11-03
 */
class AdminConsumptionLedgerMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.admin_consumption_ledger',
            route: 'admin.consumption.summary',
            icon: 'superadmin-ai-statistics',
            permission: null // Admin + SuperAdmin access
        );
    }
}





