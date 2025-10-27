<?php

namespace App\Services\Menu\Items;

use App\Services\Menu\MenuItem;

/**
 * PA Embeddings Menu Item
 *
 * Menu per gestione vector embeddings per semantic search
 *
 * @package App\Services\Menu\Items
 * @author Claude Sonnet 4.5 for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Vector Embeddings RAG)
 * @date 2025-10-23
 */
class PAEmbeddingsMenu extends MenuItem {
    public function __construct() {
        parent::__construct(
            translationKey: 'menu.pa_embeddings',
            route: 'pa.embeddings.index',
            icon: 'search',
            permission: 'access_pa_dashboard'
        );
    }
}
