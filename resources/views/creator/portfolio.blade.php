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

            {{-- Public Portfolio Info - Only basic public information --}}
            <div class="mt-6 text-center">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <span class="block text-xl font-bold text-oro-fiorentino">{{ $stats['total_collections'] ?? 0 }}</span>
                        <span class="text-sm text-gray-300">{{ __('creator.portfolio.public_collections') }}</span>
                    </div>
                    <div>
                        <span class="block text-xl font-bold text-oro-fiorentino">{{ $stats['total_egis'] ?? 0 }}</span>
                        <span class="text-sm text-gray-300">{{ __('creator.portfolio.public_artworks') }}</span>
                    </div>
                </div>
                @if(Auth::check() && Auth::id() === $creator->id)
                    <div class="mt-4 p-3 bg-blue-900/50 rounded-lg border border-blue-600/30">
                        <p class="text-sm text-blue-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('creator.portfolio.private_stats_info') }}
                            <a href="{{ route('statistics.index') }}" class="text-blue-300 hover:text-blue-100 underline ml-1">
                                {{ __('creator.portfolio.view_detailed_stats') }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- EGI Grid/List --}}
    <div class="min-h-screen bg-gray-800">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- View Toggle --}}
            <div class="flex justify-end mb-8">
                <div class="flex space-x-1">
                    <a href="{{ route('creator.portfolio', $creator->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'grid'])) }}"
                        class="{{ $view == 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-l-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </a>
                    <a href="{{ route('creator.portfolio', $creator->id) }}?{{ http_build_query(array_merge(request()->query(), ['view' => 'list'])) }}"
                        class="{{ $view == 'list' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300' }} rounded-r-lg border border-gray-600 p-2 transition-colors hover:bg-purple-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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
