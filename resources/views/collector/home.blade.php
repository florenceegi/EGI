@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$collector->name . ' - ' . __('collector.home.title_suffix')" :metaDescription="__('collector.home.meta_description', ['name' => $collector->name])">

    @push('head')
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "ProfilePage",
                "mainEntity": {
                    "@type": "Person",
                    "@id": "{{ url('/collector/' . $collector->id) }}",
                    "name": "{{ $collector->name }}",
                    "url": "{{ url('/collector/' . $collector->id) }}",
                    "description": "{{ $collector->bio ?? __('collector.home.default_bio') }}",
                    "image": "{{ $collector->profile_photo_url }}"
                }
            }
        </script>
    @endpush

    <x-slot name="platformInfoButtons">
        {{-- Payment Settings (Owner Only) --}}
        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
            <div class="absolute bottom-4 right-4 z-30 hidden md:block">
                <button onclick="window.paymentModal.open()"
                    aria-label="{{ __('payment.settings_title') }}"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-amber-600 via-yellow-500 to-orange-600 p-[2px] shadow-xl shadow-amber-500/40 transition-all duration-500 hover:scale-[1.02] hover:shadow-[0_0_40px_rgba(251,191,36,0.6)]">
                    <div
                        class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white to-transparent opacity-30 transition-transform duration-700 group-hover:translate-x-full">
                    </div>
                    <div class="relative rounded-xl bg-gradient-to-br from-gray-900 to-black px-4 py-2.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="relative h-8 w-12 overflow-hidden rounded-md bg-gradient-to-br from-amber-400 via-yellow-500 to-amber-600 shadow-lg shadow-amber-500/40 transition-all duration-500 group-hover:rotate-6 group-hover:scale-110">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-white/30 via-transparent to-black/20">
                                </div>
                                <div
                                    class="absolute left-1 top-1 h-2.5 w-3 rounded-sm bg-gradient-to-br from-yellow-200 via-amber-300 to-yellow-600 shadow-inner">
                                    <div class="grid h-full w-full grid-cols-3 gap-[0.5px] p-[1px]">
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                    </div>
                                </div>
                                <div
                                    class="absolute bottom-1 left-1 text-[6px] font-bold tracking-wide text-white/90 drop-shadow">
                                    •••• 4242</div>
                                <div class="absolute bottom-1 right-1 flex gap-[1px]">
                                    <div class="h-1 w-1 rounded-full bg-red-500/80"></div>
                                    <div class="h-1 w-1 rounded-full bg-yellow-500/80"></div>
                                </div>
                                <div
                                    class="absolute left-0 right-0 top-1/2 h-1 -translate-y-1/2 bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800">
                                </div>
                            </div>
                            <span
                                class="bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-400 bg-clip-text text-sm font-bold text-transparent">{{ __('payment.settings_title') }}</span>
                            <div
                                class="ml-1 h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400 shadow-lg shadow-amber-400/80">
                            </div>
                        </div>
                    </div>
                </button>
            </div>

            <div id="payment-fab-mobile" class="fixed bottom-20 right-4 z-50 md:hidden">
                <button onclick="window.paymentModal.open()"
                    aria-label="{{ __('payment.settings_title') }}"
                    class="group flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-500 shadow-lg shadow-amber-500/50 transition-all duration-300 active:scale-95">
                    <div
                        class="relative h-7 w-10 rounded bg-gradient-to-br from-amber-300 via-yellow-400 to-amber-500 shadow-inner">
                        <div
                            class="absolute left-1 top-1 h-2 w-2.5 rounded-sm bg-gradient-to-br from-yellow-200 to-amber-400">
                        </div>
                        <div class="absolute bottom-1 left-1 text-[5px] font-bold text-white/80">••••</div>
                    </div>
                    <div
                        class="absolute -right-1 -top-1 h-3 w-3 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/80">
                    </div>
                </button>
            </div>
        @endif
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
                    {{-- Mobile: sfondo più scuro per contrasto con card --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 md:hidden">
                    </div>
                    <div class="absolute inset-0 hidden md:block"
                        style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>

                {{-- Edit Banner Button (only for owner) --}}
                @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
                    <div class="absolute right-4 top-4 z-10">
                        <button onclick="openImageModal('collector-banner-modal')"
                            class="hover:bg-oro-fiorentino rounded-lg bg-gray-900/80 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:text-gray-900">
                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('profile.upload_banner') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             MOBILE: iOS-style Profile Header (iPhone-first)
             Seguendo iOS Human Interface Guidelines
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="relative z-10 px-4 pb-4 pt-6 md:hidden">
            {{-- Profile Card con glassmorphism iOS --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
                {{-- Row 1: Avatar + Info --}}
                <div class="flex items-center gap-4">
                    {{-- Avatar iOS size (80pt) - Circular per collector --}}
                    <div class="group relative flex-shrink-0">
                        <div class="ring-oro-fiorentino/40 h-20 w-20 overflow-hidden rounded-full ring-2">
                            <img src="{{ $collector->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($collector->name) . '&size=160&background=D4A574&color=2D5016' }}"
                                alt="{{ __('collector.home.avatar_alt', ['name' => $collector->name]) }}"
                                class="h-full w-full object-cover" loading="lazy">
                        </div>

                        {{-- Edit Avatar Button Mobile (only for owner) --}}
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
                            <button onclick="openImageModal('collector-avatar-modal')"
                                class="bg-oro-fiorentino absolute bottom-0 right-0 rounded-full p-1.5 text-gray-900 opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        @endif
                        <div class="absolute -bottom-1 -right-1 rounded-full bg-blu-algoritmo p-1.5 shadow-lg"
                            title="{{ __('collector.home.collector_badge_title') }}">
                            <svg class="h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-xl font-bold text-white">{{ $collector->name }}</h1>
                        @if ($collector->tagline)
                            <p class="text-oro-fiorentino/80 truncate text-sm italic">"{{ $collector->tagline }}"</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-400">
                            {{ __('collector.home.collector_title') }} ·
                            {{ __('collector.home.member_since', ['year' => $collector->created_at->format('Y')]) }}
                        </p>
                    </div>
                </div>

                {{-- Action Buttons (Mobile) --}}
                @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() !== $collector->id)
                    <div class="mt-4 flex gap-2">
                        <button type="button"
                            class="bg-oro-fiorentino flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-lg transition-all active:scale-95">
                            <span class="flex items-center justify-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('collector.home.follow_button') }}
                            </span>
                        </button>
                        <button type="button"
                            class="flex-1 rounded-xl bg-verde-rinascita px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition-all active:scale-95">
                            {{ __('collector.home.message_button') }}
                        </button>
                    </div>
                @elseif (\App\Helpers\FegiAuth::guest())
                    <div class="mt-4">
                        <button type="button" onclick="window.location.href='{{ route('login') }}'"
                            class="bg-oro-fiorentino w-full rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-lg transition-all active:scale-95">
                            {{ __('collector.home.login_to_follow') }}
                        </button>
                    </div>
                @endif

                {{-- Divider --}}
                <div class="my-4 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                {{-- Row 2: Stats iOS-style (equal width columns) --}}
                <div class="grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-white/5 px-2 py-2.5 text-center">
                        <p class="text-oro-fiorentino text-lg font-bold">{{ $stats['total_owned_egis'] ?? 0 }}</p>
                        <p class="text-[9px] uppercase tracking-wider text-gray-400">
                            {{ __('collector.home.owned_egis') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/5 px-2 py-2.5 text-center">
                        <p class="text-oro-fiorentino text-lg font-bold">{{ $stats['collections_represented'] ?? 0 }}
                        </p>
                        <p class="text-[9px] uppercase tracking-wider text-gray-400">
                            {{ __('collector.collections_represented') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/5 px-2 py-2.5 text-center">
                        <p class="text-oro-fiorentino text-base font-bold">
                            €{{ number_format($stats['total_spent_eur'] ?? 0, 0) }}</p>
                        <p class="text-[9px] uppercase tracking-wider text-gray-400">
                            {{ __('collector.home.total_spent') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             DESKTOP: Layout originale (md+)
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="relative z-10 mx-auto hidden max-w-7xl px-4 py-16 sm:px-6 md:block md:py-24 lg:px-8">
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

                        {{-- Edit Avatar Button Desktop (only for owner) --}}
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
                            <button onclick="openImageModal('collector-avatar-modal')"
                                class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 absolute bottom-0 right-0 rounded-full p-2 text-gray-900 opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        @endif
                        <div class="absolute -bottom-2 -right-2 rounded-full bg-blu-algoritmo p-2 shadow-lg"
                            title="{{ __('collector.home.collector_badge_title') }}">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
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
                    <div class="flex gap-3">
                        @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() !== $collector->id)
                            <button type="button"
                                class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl"
                                aria-label="{{ __('collector.home.follow_aria', ['name' => $collector->name]) }}">
                                <span class="flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        @elseif (\App\Helpers\FegiAuth::guest())
                            <button type="button" onclick="window.location.href='{{ route('login') }}'"
                                class="bg-oro-fiorentino hover:bg-oro-fiorentino/90 rounded-full px-6 py-2.5 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:shadow-xl">
                                {{ __('collector.home.login_to_follow') }}
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div class="flex flex-col">
                            <span class="text-oro-fiorentino text-2xl font-bold md:text-3xl">
                                {{ $stats['total_owned_egis'] ?? 0 }}
                            </span>
                            <span
                                class="text-xs text-gray-300 md:text-sm">{{ __('collector.home.owned_egis') }}</span>
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
        @php
            $activeTab = $activeTab ?? 'overview';
        @endphp
        <nav class="flex overflow-x-auto border-b border-gray-800 bg-gray-900/95"
            aria-label="{{ __('collector.home.navigation_aria') }}">
            <div class="mx-auto flex w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex w-full space-x-6">
                    <a href="{{ route('collector.home', $collector->id) }}"
                        class="{{ $activeTab === 'overview' ? 'border-oro-fiorentino text-oro-fiorentino' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium">
                        {{ __('collector.home.overview_tab') }}
                    </a>
                    <a href="{{ route('collector.portfolio', $collector->id) }}"
                        class="{{ $activeTab === 'portfolio' ? 'border-oro-fiorentino text-oro-fiorentino' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium">
                        {{ __('collector.home.portfolio_tab') }}
                    </a>
                    <a href="{{ route('collector.collections', $collector->id) }}"
                        class="{{ $activeTab === 'collections' ? 'border-oro-fiorentino text-oro-fiorentino' : 'border-transparent text-gray-300 hover:text-white' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium">
                        {{ __('collector.home.collections_tab') }}
                    </a>
                </div>
            </div>
        </nav>
    </x-slot>

    <x-slot name="heroFullWidth">
        <section class="bg-gray-900">
            <div class="mx-auto max-w-7xl space-y-12 px-4 py-12 sm:px-6 lg:px-8">
                {{-- Portfolio Preview --}}
                @if ($featuredEgis->count() > 0)
                    <div>
                        <div class="mb-8 flex items-center justify-between">
                            <h2 class="font-playfair text-3xl font-bold text-white">
                                {{ __('collector.home.portfolio_preview_title') }}
                            </h2>
                            <a href="{{ route('collector.portfolio', $collector->id) }}"
                                class="text-oro-fiorentino hover:text-oro-fiorentino/80 flex items-center font-medium">
                                {{ __('collector.home.view_all_portfolio') }}
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($featuredEgis->take(8) as $egi)
                                <x-egi-card :egi="$egi" :collection="$egi->collection" :portfolioContext="true" :portfolioOwner="$collector" />
                            @endforeach
                        </div>
                    </div>
                @else
                    <div
                        class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-12 text-center">
                        <svg class="mx-auto mb-6 h-16 w-16 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="mb-2 text-2xl font-bold text-white">
                            {{ __('collector.home.empty_portfolio_title') }}
                        </h3>
                        <p class="mb-6 text-gray-400">
                            {{ __('collector.home.empty_portfolio_description') }}
                        </p>
                        <a href="{{ route('home.collections.index') }}"
                            class="inline-flex items-center rounded-full bg-purple-600 px-6 py-3 font-semibold text-white transition-colors duration-200 hover:bg-purple-700">
                            {{ __('collector.home.discover_egis_button') }}
                        </a>
                    </div>
                @endif

                {{-- Collections Preview --}}
                @if ($collectorCollections->count() > 0)
                    <div>
                        <div class="mb-8 flex items-center justify-between">
                            <h2 class="font-playfair text-3xl font-bold text-white">
                                {{ __('collector.home.collections_preview_title') }}
                            </h2>
                            <a href="{{ route('collector.collections', $collector->id) }}"
                                class="text-oro-fiorentino hover:text-oro-fiorentino/80 flex items-center font-medium">
                                {{ __('collector.home.view_all_collections') }}
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($collectorCollections->take(6) as $collection)
                                <article
                                    class="hover:border-oro-fiorentino/60 overflow-hidden rounded-xl border border-gray-800 bg-gray-900/70 transition-all duration-300 hover:shadow-2xl">
                                    <div class="p-6">
                                        <div class="mb-4 flex items-center">
                                            @if ($collection->creator && $collection->creator->profile_photo_url)
                                                <img src="{{ $collection->creator->profile_photo_url }}"
                                                    alt="{{ $collection->creator->name }}"
                                                    class="mr-4 h-12 w-12 rounded-full object-cover">
                                            @endif
                                            <div>
                                                <h3 class="text-xl font-semibold text-white">
                                                    <a href="{{ route('collector.collection.show', [$collector->id, $collection->id]) }}"
                                                        class="hover:text-oro-fiorentino transition-colors duration-200">
                                                        {{ $collection->title }}
                                                    </a>
                                                </h3>
                                                @if ($collection->creator)
                                                    <p class="text-sm text-gray-400">
                                                        {{ __('collector.home.by_creator') }}
                                                        <span
                                                            class="font-medium text-gray-200">{{ $collection->creator->name }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between text-sm text-gray-300">
                                            <span>{{ $collection->egis_count }}
                                                {{ __('collector.home.owned_in_collection') }}</span>
                                            @if ($collection->total_value)
                                                <span
                                                    class="text-oro-fiorentino font-medium">€{{ number_format($collection->total_value, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </x-slot>

    @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
        @include('components.payment-settings-modal')
    @endif

    {{-- Image Upload Modals (only for owner) --}}
    @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $collector->id)
        {{-- Banner Upload Modal --}}
        <x-modals.image-upload-modal modalId="collector-banner-modal" type="banner" collection="creator_banners"
            uploadRoute="{{ route('creator.upload-banner') }}"
            setCurrentRoute="{{ route('creator.set-current-banner') }}"
            deleteRoute="{{ route('creator.delete-banner') }}" :currentImage="\App\Helpers\FegiAuth::user()->getCurrentCreatorBanner()" :allImages="\App\Helpers\FegiAuth::user()->getAllCreatorBanners()"
            title="{{ __('profile.upload_new_banner') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Avatar Upload Modal --}}
        <x-modals.image-upload-modal modalId="collector-avatar-modal" type="avatar" collection="profile_image"
            uploadRoute="{{ route('profile.upload-image') }}"
            setCurrentRoute="{{ route('profile.set-current-image') }}"
            deleteRoute="{{ route('profile.delete-image') }}" :currentImage="\App\Helpers\FegiAuth::user()->getCurrentProfileImage()" :allImages="\App\Helpers\FegiAuth::user()->getAllProfileImages()"
            title="{{ __('profile.upload_new_avatar') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Include JS Manager --}}
        <script src="{{ asset('js/home-page-image-manager.js') }}" defer></script>
    @endif
</x-guest-layout>
