{{-- Collector Portfolio Content --}}
@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Egi> $currentEgis */
    $currentEgis = $purchasedEgis ?? $egis ?? collect();
    $viewMode = $view ?? 'grid';
@endphp

<div class="min-h-screen bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Collector Summary --}}
        <div class="mb-8 grid grid-cols-1 gap-4 text-center sm:grid-cols-3">
            <div>
                <span class="block text-xl font-bold text-oro-fiorentino">{{ $stats['total_owned_egis'] ?? 0 }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.home.owned_egis') }}</span>
            </div>
            <div>
                <span class="block text-xl font-bold text-oro-fiorentino">{{ $stats['collections_represented'] ?? 0 }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.collections_represented') }}</span>
            </div>
            <div>
                <span class="block text-xl font-bold text-oro-fiorentino">€{{ number_format($stats['total_spent_eur'] ?? 0, 2) }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.home.total_spent') }}</span>
            </div>
        </div>

        {{-- Controls --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-4 py-2 text-sm font-semibold text-purple-200">
                <span class="flex h-2 w-2 rounded-full bg-purple-400"></span>
                {{ __('collector.portfolio.owned') }}
            </div>
            <div class="flex space-x-1">
                <a href="{{ route('collector.portfolio', $collector->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
                    class="{{ $viewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-300' }} rounded-l-lg border border-gray-700 p-2 transition-colors hover:bg-purple-500">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zm6-6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </a>
                <a href="{{ route('collector.portfolio', $collector->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
                    class="{{ $viewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-300' }} rounded-r-lg border border-gray-700 p-2 transition-colors hover:bg-purple-500">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Portfolio Grid/List --}}
        @if ($currentEgis->count() > 0)
            @if ($viewMode === 'grid')
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true"
                            :portfolioOwner="$collector" :hideReserveButton="false" />
                    @endforeach
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($currentEgis as $egi)
                        <x-egi-card-list :egi="$egi" context="collector" :portfolioOwner="$collector"
                            :showPurchasePrice="true" :showOwnershipBadge="true" />
                    @endforeach
                </div>
            @endif
        @else
            <div class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-12 text-center">
                <svg class="mx-auto mb-6 h-16 w-16 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-2 text-2xl font-bold text-white">
                    {{ __('collector.portfolio.empty_title') }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ __('collector.portfolio.empty_description') }}
                </p>
                <a href="{{ route('home.collections.index') }}"
                    class="inline-flex items-center rounded-full bg-purple-600 px-6 py-3 font-semibold text-white transition-colors duration-200 hover:bg-purple-700">
                    {{ __('collector.portfolio.discover_button') }}
                </a>
            </div>
        @endif
    </div>
</div>
