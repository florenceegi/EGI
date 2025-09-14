<x-creator-layout :title="$collector->name . ' - ' . __('collector.home.title_suffix')"
    :metaDescription="__('collector.home.meta_description', ['name' => $collector->name])">

    {{-- Schema.org Markup --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "ProfilePage",
            "mainEntity": {
                "@type": "Person",
                "@id": "{{ url('/collector/' . $collector->id) }}",
                "name": "{{ $collector->name }}",
                "url": "{{ url('/collector/' . $collector->id) }}",
                "description": "{{ $collector->bio ?? __('collector.home.default_bio', ['name' => $collector->name]) }}",
                "image": "{{ $collector->profile_photo_url }}",
                @if($collector->social_links)
                "sameAs": {!! json_encode(json_decode($collector->social_links, true)) !!},
                @endif
                "interactionStatistic": [
                    {
                        "@type": "InteractionCounter",
                        "interactionType": "https://schema.org/AcquireAction",
                        "userInteractionCount": {{ $stats['total_owned_egis'] ?? 0 }}
                    }
                ]
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "{{ __('breadcrumb.home') }}",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ __('breadcrumb.collectors') }}",
                        "item": "{{ url('/collector') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $collector->name }}"
                    }
                ]
            }
        }
        </script>
    </x-slot>

    {{-- Hero Section --}}
    <section class="relative min-h-[60vh] overflow-hidden bg-gradient-to-br from-gray-900 via-blu-algoritmo to-gray-900"
        role="banner" aria-label="{{ __('collector.home.hero_aria_label', ['name' => $collector->name]) }}">

        {{-- Renaissance Pattern --}}
        <div class="absolute inset-0 opacity-50" aria-hidden="true">
            {{-- Dynamic Creator Banner with Spatie Media Support --}}
            <div class="absolute inset-0">
                @php
                    // Prova ad usare Spatie Media se disponibile per il banner del creator
                    $bannerUrl = method_exists($collector, 'getCreatorBannerUrl')
                        ? $collector->getCreatorBannerUrl('banner')
                        : null;
                @endphp
                @if($bannerUrl)
                <img src="{{ $bannerUrl }}" alt="Banner for {{ $collector->name }}"
                    class="object-cover w-full h-full">
                @else
                {{-- Fallback: Random background or gradient --}}
                <div class="absolute inset-0"
                    style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                </div>
                @endif
                {{-- Overlay gradiente potenziato per leggibilità --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
            </div>
        </div>


        {{-- Hero Content --}}
        <div class="relative z-10 px-4 py-16 mx-auto max-w-7xl sm:px-6 md:py-24 lg:px-8">
            {{-- Padmin: Aumentato il gap verticale su mobile per dare più respiro tra il blocco profilo e quello delle
            azioni/statistiche --}}
            <div class="grid items-end grid-cols-1 gap-12 md:grid-cols-12 md:gap-8">

                {{-- Profile Section --}}
                {{-- Padmin: Aumentato il gap e aggiunto `items-center` su mobile per un migliore allineamento verticale
                --}}
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    {{-- Avatar --}}
                    {{-- Padmin: Aggiunto flex-shrink-0 per evitare che l'avatar si restringa su schermi stretti --}}
                    <div class="relative flex-shrink-0 group">
                        <div
                            class="w-32 h-32 overflow-hidden rounded-full shadow-2xl ring-oro-fiorentino/30 ring-4 md:h-40 md:w-40">
                            <img src="{{ $collector->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($collector->name) . '&size=160&background=D4A574&color=2D5016' }}"
                                alt="{{ __('collector.home.avatar_alt', ['name' => $collector->name]) }}"
                                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                loading="lazy">
                        </div>
                        {{-- Collector Badge --}}
                        <div class="absolute p-2 rounded-full shadow-lg -bottom-2 -right-2 bg-blu-algoritmo"
                            title="{{ __('collector.home.collector_badge_title') }}">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sr-only">{{ __('collector.home.collector_sr') }}</span>
                        </div>
                    </div>

                    {{-- Collector Info --}}
                    {{-- Padmin: Aggiunto un wrapper per dare più struttura e controllo all'allineamento. --}}
                    <div class="flex flex-col text-center sm:text-left">
                        <h1 class="mb-1 text-3xl font-bold text-white font-playfair md:text-5xl">
                            {{ $collector->name }}
                        </h1>
                        @if ($collector->tagline)
                        <p class="text-lg italic text-oro-fiorentino font-source-sans md:text-xl">
                            "{{ $collector->tagline }}"
                        </p>
                        @endif
                        {{-- Padmin: Separato in un div per un miglior controllo del layout e aggiunto un margine
                        superiore. --}}
                        <div class="mt-3">
                            <p class="text-gray-400">
                                {{ __('collector.home.collector_title') }} &middot;
                                {{ __('collector.home.member_since', ['year' => $collector->created_at->format('Y')]) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions & Stats --}}
                {{-- Padmin: Aggiunto un margine superiore su mobile (mt-8) che scompare su schermi medi (md:mt-0)
                quando il layout cambia. --}}
                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    {{-- CTA Buttons --}}
                    <div class="flex gap-3">
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() !== $collector->id)
                        <button type="button"
                            class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl"
                            aria-label="{{ __('collector.home.follow_aria', ['name' => $collector->name]) }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('collector.home.follow_button') }}
                            </span>
                        </button>
                        <button type="button"
                            class="rounded-full bg-verde-rinascita px-6 py-2.5 font-semibold text-white shadow-lg transition-all duration-300 hover:bg-verde-rinascita/90 hover:shadow-xl"
                            aria-label="{{ __('collector.home.message_aria', ['name' => $collector->name]) }}">
                            {{ __('collector.home.message_button') }}
                        </button>
                        @elseif(\App\Helpers\FegiAuth::guest())
                        <button type="button" onclick="window.location.href='{{ route('login') }}'"
                            class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl">
                            {{ __('collector.home.login_to_follow') }}
                        </button>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">{{
                                $stats['total_owned_egis'] ?? 0 }}</span>
                            <span class="text-xs text-gray-400 md:text-sm">{{ __('collector.home.owned_egis') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">{{
                                $stats['collections_represented'] ?? 0 }}</span>
                            <span class="text-xs text-gray-400 md:text-sm">{{ __('collector.collections_represented')
                                }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">€{{
                                number_format($stats['total_spent_eur'] ?? 0, 2) }}</span>
                            <span class="text-xs text-gray-400 md:text-sm">{{ __('collector.home.total_spent') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Navigation Tabs --}}
    <section class="sticky top-0 z-20 bg-white shadow-md">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <nav class="flex space-x-8" aria-label="{{ __('collector.home.navigation_aria') }}">
                <a href="{{ route('collector.home', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium border-b-2 border-blu-algoritmo text-blu-algoritmo"
                    aria-current="page">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4m8-4v4" />
                    </svg>
                    {{ __('collector.home.overview_tab') }}
                </a>
                <a href="{{ route('collector.portfolio', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('collector.home.portfolio_tab') }}
                </a>
                <a href="{{ route('collector.collections', $collector->id) }}"
                    class="flex items-center px-1 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('collector.home.collections_tab') }}
                </a>
            </nav>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="min-h-screen bg-gray-800">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Portfolio Preview --}}
            @if ($featuredEgis->count() > 0)
            <section class="mb-12">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-white font-playfair">
                        {{ __('collector.home.portfolio_preview_title') }}
                    </h2>
                    <a href="{{ route('collector.portfolio', $collector->id) }}"
                        class="flex items-center font-medium text-purple-400 hover:text-purple-300">
                        {{ __('collector.home.view_all_portfolio') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredEgis->take(8) as $egi)
                    <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true"
                        :portfolioOwner="$collector" />
                    @endforeach
                </div>
            </section>
            @else
            {{-- Empty State --}}
            <section class="py-12 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-24 h-24 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mb-4 text-2xl font-bold text-white">
                        {{ __('collector.home.empty_portfolio_title') }}
                    </h3>
                    <p class="mb-6 text-gray-400">
                        {{ __('collector.home.empty_portfolio_description') }}
                    </p>
                    <a href="{{ route('home.collections.index') }}"
                        class="px-6 py-3 font-medium text-white transition-colors duration-200 bg-purple-600 rounded-lg hover:bg-purple-700">
                        {{ __('collector.home.discover_egis_button') }}
                    </a>
                </div>
            </section>
            @endif

            {{-- Collections Preview --}}
            @if ($collectorCollections->count() > 0)
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-white font-playfair">
                        {{ __('collector.home.collections_preview_title') }}
                    </h2>
                    <a href="{{ route('collector.collections', $collector->id) }}"
                        class="flex items-center font-medium text-purple-400 hover:text-purple-300">
                        {{ __('collector.home.view_all_collections') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($collectorCollections->take(6) as $collection)
                    <article
                        class="overflow-hidden transition-all duration-300 bg-white shadow-lg rounded-xl hover:shadow-2xl">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @if ($collection->creator && $collection->creator->profile_photo_url)
                                <img src="{{ $collection->creator->profile_photo_url }}"
                                    alt="{{ $collection->creator->name }}" class="w-12 h-12 mr-4 rounded-full">
                                @endif
                                <div>
                                    <h3 class="text-xl font-bold text-grigio-pietra">
                                        <a href="{{ route('collector.collection.show', [$collector->id, $collection->id]) }}"
                                            class="transition-colors duration-200 hover:text-blu-algoritmo">
                                            {{ $collection->title }}
                                        </a>
                                    </h3>
                                    @if ($collection->creator)
                                    <p class="text-sm text-gray-600">
                                        {{ __('collector.home.by_creator') }}
                                        <span class="font-medium">{{ $collection->creator->name }}</span>
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>{{ $collection->egis_count }}
                                    {{ __('collector.home.owned_in_collection') }}</span>
                                @if ($collection->total_value)
                                <span class="font-medium text-blu-algoritmo">€{{ number_format($collection->total_value,
                                    2) }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </div>

</x-creator-layout>
