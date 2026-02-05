@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$collector->name . ' - ' . __('collector.portfolio.title')" :metaDescription="__('collector.portfolio.meta_description', ['name' => $collector->name])">

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
                        style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
            </div>

            {{-- Edit Banner Button - Owner Only --}}
            @if (auth()->check() && auth()->id() === $collector->id)
                <button type="button" onclick="openImageModal('collector-banner-modal')" id="edit-banner-btn"
                    class="absolute left-4 top-4 z-20 flex touch-manipulation items-center gap-2 rounded-lg bg-black/60 px-3 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:bg-black/80"
                    title="{{ __('collector.profile.edit_banner') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('collector.profile.edit_banner') }}</span>
                </button>
            @endif
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
            <div class="grid grid-cols-1 items-end gap-12 md:grid-cols-12 md:gap-8">
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    <div class="group relative flex-shrink-0">
                        <div
                            class="ring-oro-fiorentino/40 h-32 w-32 overflow-hidden rounded-full shadow-2xl ring-4 md:h-40 md:w-40">
                            <img src="{{ $collector->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($collector->name) . '&size=160&background=D4A574&color=2D5016' }}"
                                alt="{{ __('collector.home.avatar_alt', ['name' => $collector->name]) }}"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy">
                        </div>
                        {{-- Edit Avatar Button - Owner Only --}}
                        @if (auth()->check() && auth()->id() === $collector->id)
                            <button type="button" onclick="openImageModal('collector-avatar-modal')"
                                id="edit-avatar-btn"
                                class="absolute -bottom-2 -left-2 touch-manipulation rounded-full bg-oro-fiorentino p-2 shadow-lg ring-2 ring-gray-900 transition-all hover:bg-opacity-90"
                                title="{{ __('collector.profile.edit_avatar') }}">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        @endif
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
                            <p class="font-source-sans text-oro-fiorentino text-lg italic md:text-xl">
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
                            <span class="text-oro-fiorentino text-2xl font-bold md:text-3xl">
                                {{ $stats['total_owned_egis'] ?? 0 }}
                            </span>
                            <span class="text-xs text-gray-300 md:text-sm">{{ __('collector.home.owned_egis') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-oro-fiorentino text-2xl font-bold md:text-3xl">
                                {{ $stats['collections_represented'] ?? 0 }}
                            </span>
                            <span
                                class="text-xs text-gray-300 md:text-sm">{{ __('collector.collections_represented') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-oro-fiorentino text-2xl font-bold md:text-3xl">
                                €{{ number_format($stats['total_spent_eur'] ?? 0, 2) }}
                            </span>
                            <span
                                class="text-xs text-gray-300 md:text-sm">{{ __('collector.home.total_spent') }}</span>
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
                        class="border-oro-fiorentino text-oro-fiorentino border-b-2 px-6 py-4 text-sm font-medium">
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
        <x-ai-sidebar :user="$collector" :userType="'collector'" :checklist="$onboardingChecklist" />
    @endif

    {{-- Image Upload Modals (only for owner) --}}
    @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
        {{-- Banner Upload Modal --}}
        <x-modals.image-upload-modal modalId="collector-banner-modal" type="banner" collection="creator_banners"
            uploadRoute="{{ route('creator.upload-banner') }}"
            setCurrentRoute="{{ route('creator.set-current-banner') }}"
            deleteRoute="{{ route('creator.delete-banner') }}" :currentImage="auth()->user()->getCurrentCreatorBanner()" :allImages="auth()->user()->getAllCreatorBanners()"
            title="{{ __('profile.upload_new_banner') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Avatar Upload Modal --}}
        <x-modals.image-upload-modal modalId="collector-avatar-modal" type="avatar" collection="profile_image"
            uploadRoute="{{ route('profile.upload-image') }}"
            setCurrentRoute="{{ route('profile.set-current-image') }}"
            deleteRoute="{{ route('profile.delete-image') }}" :currentImage="auth()->user()->getCurrentProfileImage()" :allImages="auth()->user()->getAllProfileImages()"
            title="{{ __('profile.upload_new_avatar') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Include JS Manager --}}
        <script src="{{ asset('js/home-page-image-manager.js') }}" defer></script>
    @endif
</x-guest-layout>
