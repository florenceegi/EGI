<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use App\Services\Menu\ContextMenus;
use App\Services\Menu\MenuConditionEvaluator;
use App\Repositories\IconRepository;
use App\Services\Menu\Items\OpenCollectionMenu;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Sidebar Component - OS1 Enhanced
 * 🎯 Purpose: Context-aware navigation with modal action support
 *
 * @seo-purpose Primary navigation for FlorenceEGI dashboard and contexts
 * @accessibility-trait Full ARIA navigation and modal trigger support
 *
 * @version 3.0 - OS1 Modal Integration
 */
class Sidebar extends Component {
    public $menus = [];
    public $contextTitle = '';
    protected $iconRepo;

    /**
     * Mount component with OS1 enhanced menu processing
     *
     * @oracular-purpose Validates menu items for both route and modal action consistency
     */
    public function mount() {
        $evaluator = new MenuConditionEvaluator();
        $this->iconRepo = app(\App\Repositories\IconRepository::class);

        // Determina il contesto dalla rotta corrente
        $currentRouteName = Route::currentRouteName();
        $context = explode('.', $currentRouteName)[0] ?? 'dashboard';

        // Imposta il titolo del contesto
        $this->contextTitle = __('menu.' . $context);

        // Ottieni i menu per il contesto corrente
        $allMenus = ContextMenus::getMenusForContext($context);

        Log::channel('upload')->info('🔍 SIDEBAR MOUNT - START', [
            'route' => $currentRouteName,
            'context' => $context,
            'menu_groups_received' => count($allMenus),
            'menu_groups_names' => array_map(fn($m) => $m->name, $allMenus),
        ]);

        // Filtra i menu in base ai permessi dell'utente
        foreach ($allMenus as $menu) {
            Log::channel('upload')->info('🔍 PROCESSING MenuGroup', [
                'group_name' => $menu->name,
                'items_count' => count($menu->items),
                'items_names' => array_map(fn($i) => $i->name, $menu->items),
            ]);

            $filteredItems = array_filter($menu->items, function ($item) use ($evaluator) {
                $shouldDisplay = $evaluator->shouldDisplay($item);
                Log::channel('upload')->info('🔍 EVALUATING MenuItem', [
                    'item_name' => $item->name,
                    'item_permission' => $item->permission ?? 'NULL',
                    'should_display' => $shouldDisplay,
                ]);
                return $shouldDisplay;
            });

            Log::channel('upload')->info('🔍 FILTERED RESULT', [
                'group_name' => $menu->name,
                'filtered_count' => count($filteredItems),
            ]);

            if (!empty($filteredItems)) {
                // Converti il MenuGroup in un array associativo
                $menuArray = [
                    'name' => $menu->name,
                    'icon' => $menu->icon ? $this->iconRepo->getDefaultIcon($menu->icon) : null,
                    'permission' => $menu->permission ?? null,
                    'items' => [],
                ];

                foreach ($filteredItems as $item) {
                    // Gestione icona: se inizia con '<' è HTML diretto, altrimenti è icon name da DB
                    $iconHtml = null;
                    if ($item->icon) {
                        if (str_starts_with(trim($item->icon), '<')) {
                            // HTML diretto (Font Awesome, custom HTML)
                            $iconHtml = $item->icon;
                        } else {
                            // Icon name da database
                            $iconHtml = $this->iconRepo->getDefaultIcon($item->icon);
                        }
                    }

                    $menuItemArray = [
                        'name' => $item->name,
                        'route' => $item->route,
                        'icon' => $iconHtml,
                        'permission' => $item->permission ?? null,
                        'children' => $item->children ?? [],
                        // OS1 Enhancement: Modal action support
                        'is_modal_action' => $item->isModalAction ?? false,
                        'modal_action' => $item->modalAction ?? null,
                        'href' => $item->getHref(),
                        'html_attributes' => $item->getHtmlAttributes(),
                    ];

                    $menuArray['items'][] = $menuItemArray;
                }

                Log::channel('upload')->info('✅ MenuGroup ADDED to sidebar', [
                    'group_name' => $menu->name,
                    'items_added' => count($menuArray['items']),
                ]);

                $this->menus[] = $menuArray;
            } else {
                Log::channel('upload')->warning('⚠️ MenuGroup SKIPPED (no items passed filter)', [
                    'group_name' => $menu->name,
                ]);
            }
        }

        Log::channel('upload')->info('🎯 SIDEBAR MOUNT - COMPLETE', [
            'total_menu_groups_added' => count($this->menus),
        ]);
    }

    /**
     * Render the sidebar component
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render() {
        return view('livewire.sidebar');
    }
}