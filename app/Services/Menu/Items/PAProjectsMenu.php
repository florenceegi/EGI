<?php

declare(strict_types=1);

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * N.A.T.A.N. Projects Menu Item
 *
 * Opens projects modal in NATAN chat - NO separate page
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.1.0 (FlorenceEGI - Projects Modal Integration)
 * @date 2025-10-27
 * @purpose Menu item for Projects modal in N.A.T.A.N. chat - document upload and priority RAG
 */
class PAProjectsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_projects',
            route: '#',
            icon: 'pa-projects',
            permission: 'access_pa_dashboard',
            modalAction: 'open-projects-modal'
        );
    }
}
