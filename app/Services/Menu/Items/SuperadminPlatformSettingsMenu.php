<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Platform Settings Menu)
 * @date 2026-02-25
 * @purpose Menu item superadmin per la gestione impostazioni di piattaforma.
 */
class SuperadminPlatformSettingsMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_platform_settings',
            route: 'superadmin.platform-settings.index',
            icon: 'superadmin-dashboard',
            permission: null
        );
    }
}
