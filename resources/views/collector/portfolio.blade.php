@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$collector->name . ' - ' . __('collector.portfolio.title')"
    :metaDescription="__('collector.portfolio.meta_description', ['name' => $collector->name])">

    @push('head')
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "CollectionPage",
                "mainEntity": {
                    "@type": "Person",
                    "@id": "{{ url('/collector/' . $collector->id) }}",
                    "name": "{{ $collector->name }}",
                    "owns": {
                        "@type": "Collection",
                        "name": "{{ $collector->name }}'s EGI Portfolio",
                        "numberOfItems": {{ $purchasedEgis->count() }}
                    }
                }
            }
        </script>
    @endpush

    <x-slot name="platformInfoButtons">
        <div class="absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute inset-0">
                @php
                    $bannerUrl = method_exists($collector, 'getCreatorBannerUrl')
                        ? $collector->getCreatorBannerUrl('banner')
                        : null;
                @endphp
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="Banner for {{ $collector->name }}"
                        class="h-full w-full object-cover">
                @else
                    <div class="absolute inset-0"
                        style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
            <div class="grid grid-cols-1 items-end gap-12 md:grid-cols-12 md:gap-8">
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    <div class="group relative flex-shrink-0">
                        <div
                            class="h-32 w-32 overflow-hidden rounded-full shadow-2xl ring-4 ring-oro-fiorentino/40 md:h-40 md:w-40">
                            <img src="{{ $collector->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($collector->name) . '&size=160&background=D4A574&color=2D5016' }}"
                                alt="{{ __('collector.home.avatar_alt', ['name' => $collector->name]) }}"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy">
                        </div>
                        <div class="absolute -bottom-2 -right-2 rounded-full bg-blu-algoritmo p-2 shadow-lg"
                            title="{{ __('collector.home.collector_badge_title') }}">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sr-only">{{ __('collector.home.collector_sr') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col text-center sm:text-left">
                        <h1 class="font-playfair mb-1 text-3xl font-bold text-white md:text-5xl">
                            {{ $collector->name }}
                        </h1>
                        @if ($collector->tagline)
                            <p class="font-source-sans text-lg italic text-oro-fiorentino md:text-xl">
                                "{{ $collector->tagline }}"
                            </p>
                        @endif
                        <p class="mt-3 text-gray-300">
                            {{ __('collector.home.collector_title') }} &middot;
                            {{ __('collector.home.member_since', ['year' => $collector->created_at->format('Y')]) }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">
                                {{ $stats['total_owned_egis'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('collector.home.owned_egis') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">
                                {{ $stats['collections_represented'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('collector.collections_represented') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-oro-fiorentino md:text-3xl">
                                €{{ number_format($stats['total_spent_eur'] ?? 0, 2) }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('collector.home.total_spent') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="platformStats">
        <nav class="flex overflow-x-auto border-b border-gray-800 bg-gray-900/95"
            aria-label="{{ __('collector.home.navigation_aria') }}">
            <div class="mx-auto flex w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex w-full space-x-6">
                    <a href="{{ route('collector.portfolio', $collector->id) }}"
                        class="border-b-2 border-oro-fiorentino px-6 py-4 text-sm font-medium text-oro-fiorentino">
                        {{ __('collector.home.portfolio_tab') }}
                    </a>
                    <a href="{{ route('collector.collections', $collector->id) }}"
                        class="border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 hover:text-white">
                        {{ __('collector.home.collections_tab') }}
                    </a>
                </div>
            </div>
        </nav>
    </x-slot>

    <x-slot name="heroFullWidth">
        @include('collector.partials.portfolio-content', [
            'collector' => $collector,
            'purchasedEgis' => $purchasedEgis,
            'stats' => $stats,
            'view' => $view,
        ])
    </x-slot>

    {{-- AI Sidebar - Onboarding Assistant (Owner Only) --}}
    @if (!empty($onboardingChecklist))
        <x-ai-sidebar 
            :user="$collector" 
            :userType="'collector'" 
            :checklist="$onboardingChecklist" 
        />
    @endif
</x-guest-layout>
