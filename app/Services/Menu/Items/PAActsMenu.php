<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Acts Tokenization Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization System)
 * @date 2025-01-10
 * @purpose Menu item for PA Acts tokenization and blockchain verification system
 */
class PAActsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_acts',
            route: 'pa.acts.index',
            icon: 'pa-acts',
            permission: 'access_pa_dashboard'
        );
    }
}
