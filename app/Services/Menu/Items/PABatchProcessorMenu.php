<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Batch Processor Menu Item
 *
 * @package App\Services\Menu\Items
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - NATAN Batch System)
 * @date 2025-10-16
 * @purpose Menu item for PA batch processing management - automated document processing
 */
class PABatchProcessorMenu extends MenuItem
{
    public function __construct()
    {
        parent::__construct(
            translationKey: 'menu.pa_batch_processor',
            route: 'pa.batch.index',
            icon: 'auto_mode',
            permission: 'access_pa_dashboard'
        );
    }
}