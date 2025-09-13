@vite(['resources/css/creator-home.css', 'resources/js/creator-home.js'])

<x-creator-layout>
    <x-slot name="title">{{ $creator->name }} - {{ __('creator.portfolio.title') }}</x-slot>
    <x-slot name="description">{{ __('creator.portfolio.meta_description', ['name' => $creator->name]) }}</x-slot>
    {{-- Schema.org Markup opzionale --}}
    <x-slot name="schema">
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "mainEntity": {
                "@type": "Person",
                "@id": "{{ url('/creator/' . $creator->id) }}",
                "name": "{{ $creator->name }}",
                "owns": {
                    "@type": "Collection",
                    "name": "{{ $creator->name }}'s EGI Portfolio",
                    "numberOfItems": {{ $egis->count() }}
                }
            }
        }
        </script>
    </x-slot>

    {{-- Header --}}
    <section class="py-12 bg-gradient-to-br from-gray-900 via-blu-algoritmo to-gray-900">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('creator.home', $creator->id) }}"
                    class="text-oro-fiorentino hover:text-oro-fiorentino/80">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white font-playfair">{{ $creator->name }}</h1>
                    <p class="text-oro-fiorentino">{{ __('creator.portfolio.subtitle') }}</p>
                </div>
            </div>

            {{-- Stats Bar - Enhanced per Creator Portfolio --}}
            <div class="grid grid-cols-2 gap-6 mt-6 text-center md:grid-cols-4">
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.total_egis') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['total_collections'] ?? 0
                        }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.total_collections') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">{{ $stats['reserved_egis'] ?? 0 }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.reserved_egis') }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-oro-fiorentino">€{{ number_format($stats['highest_offer']
                        ?? 0, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-300">{{ __('creator.portfolio.highest_offer') }}</span>
                </div>
            </div>

            {{-- Secondary Stats Row --}}
            <div class="grid grid-cols-2 gap-6 mt-4 text-center">
                <div>
                    <span class="block text-lg font-semibold text-white">{{ $stats['available_egis'] ?? 0 }}</span>
                    <span class="text-xs text-gray-400">{{ __('creator.portfolio.available_egis') }}</span>
                </div>
                <div>
                    <span class="block text-lg font-semibold text-white">€{{ number_format($stats['total_value_eur'] ??
                        0, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-400">{{ __('creator.portfolio.total_value') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Advanced Statistics Section --}}
    @if($advancedStats && $advancedStats['earnings']['total_earnings'] > 0)
        <section class="py-8 bg-gray-900">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="space-y-8">
                    {{-- Row 1: Earnings & Monthly Trends --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <x-stats.earnings-widget :creatorId="$creator->id" :earnings="$advancedStats['earnings']" />
                        <x-stats.monthly-trend-chart :creatorId="$creator->id" :monthlyTrend="$advancedStats['monthly_trend']" />
                    </div>

                    {{-- Row 2: Collection Performance & Engagement --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <x-stats.collection-performance-widget :creatorId="$creator->id" :collectionPerformance="$advancedStats['collection_performance']" />
                        <x-stats.engagement-widget :creatorId="$creator->id" :engagement="$advancedStats['engagement']" />
                    </div>

                    {{-- Row 3: Role-based Earnings --}}
                    <div class="grid grid-cols-1 gap-8">
                        <x-stats.role-earnings-widget :user-id="$creator->id" />
                    </div>
                </div>
            </div>
        </section>
    @elseif($advancedStats)
        {{-- No Sales Yet - Motivational Section --}}
        <section class="py-8 bg-gray-900">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-purple-900/50 to-blue-900/50 border border-purple-500/20 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 text-oro-fiorentino mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white mb-4">{{ __('creator.portfolio.earnings.no_earnings_title') }}</h3>
                    <p class="text-gray-300 mb-6 max-w-2xl mx-auto">
                        {{ __('creator.portfolio.earnings.no_earnings_description') }}
                    </p>
                    <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <span>Guadagna dalle vendite</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>Crea impatto ambientale</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <span>Costruisci la tua audience</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- EGI Grid/List --}}
    <div class="min-h-screen bg-gray-800">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- View Toggle --}}
            <div class="mb-8 flex justify-end">
                <div class="flex space-x-1">
                    <a href="{{ route('creator.portfolio', $creator->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
                        class="{{ $view == 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-l-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </a>
                    <a href="{{ route('creator.portfolio', $creator->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
                        class="{{ $view == 'list' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-r-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            @if ($egis->count() > 0)
            @if ($view == 'grid')
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($egis as $egi)
                <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true"
                    :portfolioOwner="$creator" :creatorPortfolioContext="true" :hideReserveButton="false" />
                @endforeach
            </div>
            @else
            {{-- List View --}}
            <div class="space-y-3">
                @foreach ($egis as $egi)
                <x-egi-card-list :egi="$egi" context="creator" :portfolioOwner="$creator" :showPurchasePrice="false"
                    :showOwnershipBadge="true" />
                @endforeach
            </div>
            @endif
            @else
            <div class="py-12 text-center">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-4 text-2xl font-bold text-white">
                    {{ __('creator.portfolio.empty_title') }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ __('creator.portfolio.empty_description') }}
                </p>
                <a href="{{ route('home.collections.index') }}"
                    class="px-6 py-3 font-medium text-white transition-colors duration-200 bg-purple-600 rounded-lg hover:bg-purple-700">
                    {{ __('creator.portfolio.discover_button') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</x-creator-layout>
