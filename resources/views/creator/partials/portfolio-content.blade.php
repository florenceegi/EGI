{{-- Portfolio Content - Partial View --}}
@php
    $currentMode = $portfolioMode ?? 'created';
    $canSwitchModes = $canSwitchPortfolioMode ?? false;
    $createdSet = $createdEgis ?? ($egis ?? collect());
    $ownedSet = $ownedEgis ?? collect();
    $currentEgis = $currentMode === 'owned' ? $ownedSet : $createdSet;
    $currentStats = $portfolioStats ?? ($stats ?? []);
    $summaryLabels = $currentMode === 'owned'
        ? [
            'collections' => __('creator.portfolio.owned_collections'),
            'egis' => __('creator.portfolio.owned_artworks'),
        ]
        : [
            'collections' => __('creator.portfolio.public_collections'),
            'egis' => __('creator.portfolio.public_artworks'),
        ];
    $emptyTitle = $currentMode === 'owned'
        ? __('creator.portfolio.empty_owned_title')
        : __('creator.portfolio.empty_title');
    $emptyDescription = $currentMode === 'owned'
        ? __('creator.portfolio.empty_owned_description')
        : __('creator.portfolio.empty_description');
@endphp

<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Portfolio Summary --}}
        <div class="mb-8 text-center">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <span class="text-oro-fiorentino block text-xl font-bold">{{ $currentStats['total_collections'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ $summaryLabels['collections'] }}</span>
                </div>
                <div>
                    <span class="text-oro-fiorentino block text-xl font-bold">{{ $currentStats['total_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ $summaryLabels['egis'] }}</span>
                </div>
            </div>
            @if (Auth::check() && Auth::id() === $creator->id)
                <div class="mt-4 rounded-lg border border-blue-600/30 bg-blue-900/50 p-3">
                    <p class="text-sm text-blue-200">
                        <svg class="mr-1 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('creator.portfolio.private_stats_info') }}
                        <a href="{{ route('statistics.index') }}"
                            class="ml-1 text-blue-300 underline hover:text-blue-100">
                            {{ __('creator.portfolio.view_detailed_stats') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        {{-- Mode & View Toggles --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            @if ($canSwitchModes)
                <div class="inline-flex rounded-lg border border-purple-600/40 bg-gray-900/60 p-1 text-sm shadow-sm">
                    <button data-mode="created"
                        class="portfolio-mode-toggle {{ $currentMode === 'created' ? 'bg-purple-600 text-white shadow' : 'text-gray-300' }} rounded-l-md px-3 py-2 transition-colors hover:bg-purple-500 hover:text-white"
                        type="button" aria-pressed="{{ $currentMode === 'created' ? 'true' : 'false' }}">
                        {{ __('creator.portfolio.modes.created') }}
                    </button>
                    <button data-mode="owned"
                        class="portfolio-mode-toggle {{ $currentMode === 'owned' ? 'bg-purple-600 text-white shadow' : 'text-gray-300' }} rounded-r-md px-3 py-2 transition-colors hover:bg-purple-500 hover:text-white"
                        type="button" aria-pressed="{{ $currentMode === 'owned' ? 'true' : 'false' }}">
                        {{ __('creator.portfolio.modes.owned') }}
                    </button>
                </div>
            @else
                <div class="rounded-lg border border-gray-700 bg-gray-900/60 px-4 py-2 text-sm text-gray-300">
                    {{ $currentMode === 'owned'
                        ? __('creator.portfolio.modes.owned')
                        : __('creator.portfolio.modes.created') }}
                </div>
            @endif

            <div class="hidden justify-end md:flex">
                <div class="flex space-x-1">
                    <button data-view="grid"
                        class="view-toggle {{ ($view ?? 'grid') == 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-l-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500"
                        type="button" aria-pressed="{{ ($view ?? 'grid') == 'grid' ? 'true' : 'false' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="sr-only">{{ __('creator.portfolio.view_grid') }}</span>
                    </button>
                    <button data-view="list"
                        class="view-toggle {{ ($view ?? 'grid') == 'list' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-r-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500"
                        type="button" aria-pressed="{{ ($view ?? 'grid') == 'list' ? 'true' : 'false' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">{{ __('creator.portfolio.view_list') }}</span>
                    </button>
                </div>
            </div>
        </div>

        @if ($currentEgis->count() > 0)
            {{-- MOBILE: Sempre List View (iOS-first) --}}
            <div class="space-y-3 md:hidden">
                @foreach ($currentEgis as $egi)
                    <x-egi-card-list :egi="$egi" context="creator" :portfolioOwner="$creator" :showPurchasePrice="false"
                        :showOwnershipBadge="true" />
                @endforeach
            </div>

            {{-- DESKTOP: Grid/List basato su toggle --}}
            @if (($view ?? 'grid') == 'grid')
                <div class="hidden gap-6 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true" :portfolioOwner="$creator"
                            :creatorPortfolioContext="true" :hideReserveButton="false" />
                    @endforeach
                </div>
            @else
                {{-- Desktop List View --}}
                <div class="hidden space-y-3 md:block">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card-list :egi="$egi" context="creator" :portfolioOwner="$creator" :showPurchasePrice="false"
                            :showOwnershipBadge="true" />
                    @endforeach
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <svg class="mx-auto mb-6 h-24 w-24 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-4 text-2xl font-bold text-white">
                    {{ $emptyTitle }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ $emptyDescription }}
                </p>
                <a href="{{ route('home.collections.index') }}"
                    class="rounded-lg bg-purple-600 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-purple-700">
                    {{ __('creator.portfolio.discover_button') }}
                </a>
            </div>
        @endif
    </div>
</div>
