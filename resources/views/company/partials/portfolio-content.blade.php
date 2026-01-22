{{-- Portfolio Content - Partial View for Company --}}
@php
    $currentMode = $portfolioMode ?? 'created';
    $canSwitchModes = $canSwitchPortfolioMode ?? false;
    $createdSet = $createdEgis ?? ($egis ?? collect());
    $ownedSet = $ownedEgis ?? collect();
    $currentEgis = $currentMode === 'owned' ? $ownedSet : $createdSet;
    $currentStats = $portfolioStats ?? ($stats ?? []);
    $summaryLabels =
        $currentMode === 'owned'
            ? [
                'collections' => __('company.portfolio.owned_collections'),
                'egis' => __('company.portfolio.owned_egis'),
            ]
            : [
                'collections' => __('company.portfolio.collections'),
                'egis' => __('company.portfolio.egis'),
            ];
    $emptyTitle =
        $currentMode === 'owned' ? __('company.portfolio.empty_owned_title') : __('company.portfolio.empty_title');
    $emptyDescription =
        $currentMode === 'owned'
            ? __('company.portfolio.empty_owned_description')
            : __('company.portfolio.empty_description');
@endphp

<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Mode & View Toggles --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            @if ($canSwitchModes)
                <div class="inline-flex rounded-lg border border-[#1E3A5F]/40 bg-gray-900/60 p-1 text-sm shadow-sm">
                    <button data-mode="created"
                        class="portfolio-mode-toggle {{ $currentMode === 'created' ? 'bg-[#1E3A5F] text-[#C9A227] shadow' : 'text-gray-300' }} rounded-l-md px-3 py-2 transition-colors hover:bg-[#1E3A5F]/80 hover:text-white"
                        type="button" aria-pressed="{{ $currentMode === 'created' ? 'true' : 'false' }}">
                        {{ __('company.portfolio.modes.created') }}
                    </button>
                    <button data-mode="owned"
                        class="portfolio-mode-toggle {{ $currentMode === 'owned' ? 'bg-[#1E3A5F] text-[#C9A227] shadow' : 'text-gray-300' }} rounded-r-md px-3 py-2 transition-colors hover:bg-[#1E3A5F]/80 hover:text-white"
                        type="button" aria-pressed="{{ $currentMode === 'owned' ? 'true' : 'false' }}">
                        {{ __('company.portfolio.modes.owned') }}
                    </button>
                </div>
            @else
                <div class="rounded-lg border border-gray-700 bg-gray-900/60 px-4 py-2 text-sm text-gray-300">
                    {{ $currentMode === 'owned' ? __('company.portfolio.modes.owned') : __('company.portfolio.modes.created') }}
                </div>
            @endif

            <div class="hidden justify-end md:flex">
                <div class="flex space-x-1">
                    <button data-view="grid"
                        class="view-toggle {{ ($view ?? 'grid') == 'grid' ? 'bg-[#1E3A5F] text-[#C9A227]' : 'bg-gray-700 text-gray-300' }} rounded-l-lg border border-gray-600 p-2 transition-colors hover:bg-[#1E3A5F]/80"
                        type="button" aria-pressed="{{ ($view ?? 'grid') == 'grid' ? 'true' : 'false' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button data-view="list"
                        class="view-toggle {{ ($view ?? 'grid') == 'list' ? 'bg-[#1E3A5F] text-[#C9A227]' : 'bg-gray-700 text-gray-300' }} rounded-r-lg border border-gray-600 p-2 transition-colors hover:bg-[#1E3A5F]/80"
                        type="button" aria-pressed="{{ ($view ?? 'grid') == 'list' ? 'true' : 'false' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        @if ($currentEgis->count() > 0)
            {{-- MOBILE: Sempre List View (iOS-first) --}}
            <div class="space-y-3 md:hidden">
                @foreach ($currentEgis as $egi)
                    <x-egi-card-list :egi="$egi" context="company" :portfolioOwner="$company" :showPurchasePrice="false"
                        :showOwnershipBadge="true" />
                @endforeach
            </div>

            {{-- DESKTOP: Grid/List basato su toggle --}}
            @if (($view ?? 'grid') == 'grid')
                <div class="hidden gap-6 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true" :portfolioOwner="$company"
                            :creatorPortfolioContext="true" :hideReserveButton="false" />
                    @endforeach
                </div>
            @else
                {{-- Desktop List View --}}
                <div class="hidden space-y-3 md:block">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card-list :egi="$egi" context="company" :portfolioOwner="$company" :showPurchasePrice="false"
                            :showOwnershipBadge="true" />
                    @endforeach
                </div>
            @endif
        @else
            <div
                class="rounded-2xl border border-[#1E3A5F]/30 bg-gradient-to-br from-gray-800/50 to-transparent p-12 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#1E3A5F]/20">
                    <svg class="h-10 w-10 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold text-white">{{ $emptyTitle }}</h3>
                <p class="text-gray-400">{{ $emptyDescription }}</p>
            </div>
        @endif
    </div>
</div>
