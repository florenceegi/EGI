{{--
    Enterprise Sidebar Component - Universal Contextual Navigation

    @props
    - logo: string - Logo text (default: 'FlorenceEGI')
    - badge: string|null - Badge text below logo (e.g., 'Ente PA', 'Ispettore')
    - theme: string - Theme identifier (pa|inspector|company|dashboard)
--}}

@props([
    'logo' => 'FlorenceEGI',
    'badge' => null,
    'theme' => 'pa',
])

@php
    use App\Services\Menu\ContextMenus;
    use App\Services\Menu\MenuConditionEvaluator;
    use Illuminate\Support\Facades\Route;
    use App\Repositories\IconRepository;

    $evaluator = new MenuConditionEvaluator();
    $iconRepo = app(IconRepository::class);

    // Estrai context dalla route
    $currentRouteName = Route::currentRouteName();
    $context = explode('.', $currentRouteName)[0] ?? 'dashboard';
    $contextTitle = __('menu.' . $context);

    // Theme colors mapping
    $themeColors = [
        'pa' => 'bg-gradient-to-b from-[#1B365D] to-[#0F2342]', // Blu Algoritmo
        'inspector' => 'bg-gradient-to-b from-[#2D5016] to-[#1F3810]', // Verde Rinascita
        'company' => 'bg-gradient-to-b from-[#8E44AD] to-[#6C3483]', // Viola Innovazione
        'dashboard' => 'bg-neutral', // Default DaisyUI
    ];

    $sidebarBgClass = $themeColors[$theme] ?? $themeColors['dashboard'];

    // Ottieni menu per context
    $allMenus = ContextMenus::getMenusForContext($context);

    // Filtra menu per permessi
    $menus = [];
    foreach ($allMenus as $menuGroup) {
        $filteredItems = array_filter($menuGroup->items, function ($item) use ($evaluator) {
            return $evaluator->shouldDisplay($item);
        });

        if (!empty($filteredItems)) {
            $menuData = [
                'name' => $menuGroup->name,
                'icon' => $menuGroup->icon ? $iconRepo->getDefaultIcon($menuGroup->icon) : null,
                'items' => [],
            ];

            foreach ($filteredItems as $item) {
                // Gestione icona
                $iconHtml = null;
                if ($item->icon) {
                    if (str_starts_with(trim($item->icon), '<')) {
                        $iconHtml = $item->icon;
                    } else {
                        $iconHtml = $iconRepo->getDefaultIcon($item->icon);
                    }
                }

                $menuData['items'][] = [
                    'name' => $item->name,
                    'route' => $item->route,
                    'icon' => $iconHtml,
                    'is_modal_action' => $item->isModalAction ?? false,
                    'modal_action' => $item->modalAction ?? null,
                    'href' => $item->getHref(),
                    'html_attributes' => $item->getHtmlAttributes(),
                ];
            }

            $menus[] = $menuData;
        }
    }
@endphp

<aside class="{{ $sidebarBgClass }} flex min-h-screen w-80 flex-col text-neutral-content">
    <!-- Logo & Badge -->
    <div class="p-6 text-center border-b border-neutral-focus">
        <h1 class="text-2xl font-bold text-white">{{ $logo }}</h1>
        @if ($badge)
            <span class="inline-block px-3 py-1 mt-2 text-xs font-semibold rounded-full bg-white/10 text-white/90">
                {{ $badge }}
            </span>
        @else
            <span class="inline-block mt-2 text-sm text-white/70">{{ $contextTitle }}</span>
        @endif
    </div>

    <!-- Back Button -->
    <div class="px-4 py-6">
        <x-back-button />
    </div>

    <!-- Menu Navigation -->
    <div class="flex-1 px-4 py-2 space-y-3 overflow-y-auto">
        @if (!empty($menus))
            @foreach ($menus as $menu)
                @php
                    // Check se gruppo o item è attivo
                    $isGroupActive = false;
                    foreach ($menu['items'] as $item) {
                        if (!$item['is_modal_action'] && $currentRouteName == $item['route']) {
                            $isGroupActive = true;
                            break;
                        }
                    }
                @endphp

                <details class="bg-transparent group collapse collapse-arrow"
                    @if ($isGroupActive) open @endif>
                    <summary
                        class="{{ $isGroupActive ? 'bg-primary text-primary-content shadow-sm rounded-md' : 'hover:bg-base-content hover:bg-opacity-10 rounded-md' }} cursor-pointer list-none transition-colors duration-150 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        <div class="flex items-center gap-3 px-3 py-3 text-base font-medium collapse-title">
                            @if (!empty($menu['icon']))
                                <span
                                    class="{{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }} flex-shrink-0">
                                    {!! $menu['icon'] !!}
                                </span>
                            @endif
                            <span class="flex-grow truncate">{{ $menu['name'] }}</span>
                        </div>
                    </summary>

                    <!-- Submenu Items -->
                    <div class="pt-2 pb-1 pl-6 pr-2 space-y-1 collapse-content">
                        @foreach ($menu['items'] as $item)
                            @php
                                $isItemActive = !$item['is_modal_action'] && $currentRouteName == $item['route'];
                            @endphp

                            @if ($item['is_modal_action'])
                                <!-- Modal Action Button -->
                                <button type="button"
                                    @foreach ($item['html_attributes'] as $attr => $value)
                                           {{ $attr }}="{{ $value }}" @endforeach
                                    class="flex w-full items-center justify-start gap-3 rounded-md px-3 py-2.5 text-left text-sm transition-colors duration-150 ease-in-out hover:bg-base-content hover:bg-opacity-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                    @if (!empty($item['icon']))
                                        <span
                                            class="flex-shrink-0 transition-opacity opacity-60 group-hover:opacity-100">
                                            {!! $item['icon'] !!}
                                        </span>
                                    @else
                                        <span class="w-5 h-5"></span>
                                    @endif
                                    <span class="flex-grow truncate">{{ $item['name'] }}</span>
                                    <span class="text-xs material-symbols-outlined opacity-40">open_in_new</span>
                                </button>
                            @else
                                <!-- Route Link -->
                                <a href="{{ $item['href'] }}"
                                    class="{{ $isItemActive ? 'bg-primary/80 text-primary-content font-semibold shadow-sm' : 'hover:bg-base-content hover:bg-opacity-10' }} flex w-full items-center justify-start gap-3 rounded-md px-3 py-2.5 text-sm transition-colors duration-150 ease-in-out">
                                    @if (!empty($item['icon']))
                                        <span
                                            class="{{ $isItemActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }} flex-shrink-0">
                                            {!! $item['icon'] !!}
                                        </span>
                                    @else
                                        <span class="w-5 h-5"></span>
                                    @endif
                                    <span class="flex-grow truncate">{{ $item['name'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </details>
            @endforeach
        @else
            <!-- No menu available -->
            <div class="px-3 py-6 text-sm text-center opacity-60">
                <p>Nessun menu disponibile</p>
            </div>
        @endif
    </div>

    <!-- Footer (optional) -->
    <div class="p-4 text-xs text-center border-t border-neutral-focus opacity-60">
        <p>FlorenceEGI PA Enterprise</p>
        <p class="mt-1">© 2025 FlorenceEGI</p>
    </div>
</aside>
