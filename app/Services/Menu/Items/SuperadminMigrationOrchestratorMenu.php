<?php

declare(strict_types=1);

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI)
 * @date 2025-11-03
 * @purpose Menu item per interfaccia admin Migration Orchestrator
 * 
 * CONTESTO: Gestione centralizzata migration database condiviso EGI + NATAN_LOC
 * PERCORSO FILE: app/Services/Menu/Items/SuperadminMigrationOrchestratorMenu.php
 */
class SuperadminMigrationOrchestratorMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.superadmin_migration_orchestrator',
            route: 'superadmin.migration-orchestrator.index',
            icon: 'database-cog',
            permission: null // Protected by superadmin middleware
        );
    }
}


