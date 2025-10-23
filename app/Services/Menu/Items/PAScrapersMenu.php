<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Web Scrapers Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Scraping Agent)
 * @date 2025-10-23
 * @purpose Menu item for Web Scraping Agent - Automatic acquisition of public PA acts from web sources
 */
class PAScrapersMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.pa_scrapers',
            route: 'pa.scrapers.index',
            icon: 'cloud_download',
            permission: 'access_pa_dashboard'
        );
    }
}

