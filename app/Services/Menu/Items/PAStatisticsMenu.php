<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Statistics Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - PA Statistics Dashboard)
 * @date 2025-10-15
 * @purpose Menu item for PA Statistics - Analytics and KPIs for administrative acts
 */
class PAStatisticsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.pa_statistics',
            route: 'pa.acts.statistics',
            icon: 'bar_chart',
            permission: 'manage_institutional_collections'
        );
    }
}
