<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA N.A.T.A.N. Menu Item
 *
 * Menu item for N.A.T.A.N. AI document intelligence system
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Menu item for AI-powered administrative acts analysis and management
 */
class PANatanMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.pa_natan',
            route: 'pa.natan.dashboard',
            icon: 'pa-natan',
            permission: 'access_pa_dashboard'
        );
    }
}
