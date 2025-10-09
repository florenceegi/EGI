<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * N.A.T.A.N. Intelligence Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. AI Document Intelligence)
 * @date 2025-01-10
 * @purpose Menu item for N.A.T.A.N. Intelligence Center - AI-powered document analysis and blockchain verification
 */
class PAActsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.pa_acts',
            route: 'pa.acts.index',
            icon: 'smart_toy',
            permission: 'access_pa_dashboard'
        );
    }
}
