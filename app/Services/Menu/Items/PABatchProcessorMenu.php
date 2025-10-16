<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItemInterface;

/**
 * @Oracode Menu Item: PA Batch Processor
 * 🎯 Purpose: Menu item for PA batch processing management
 * 🛡️ Privacy: PA-only access
 * 🧱 Core Logic: Link to batch sources dashboard
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch System)
 * @date 2025-10-16
 */
class PABatchProcessorMenu implements MenuItemInterface
{
    /**
     * Get menu item configuration
     *
     * @return array{
     *     translationKey: string,
     *     route: string,
     *     icon: string,
     *     badge: string|null,
     *     submenu: array|null,
     *     permissions: array|null
     * }
     */
    public function getConfig(): array
    {
        return [
            'translationKey' => 'menu.pa_batch_processor',
            'route' => 'pa.batch.index',
            'icon' => 'auto_mode', // Material icon for automated processing
            'badge' => null,
            'submenu' => null,
            'permissions' => ['pa_entity'], // Only PA entities can access
        ];
    }
}

