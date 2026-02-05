@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$creator->name . ' - Portfolio'" :metaDescription="__('creator.home.meta_description', ['name' => $creator->name])">

    {{-- BANNER + AVATAR + STATS --}}
    <x-slot name="platformInfoButtons">
        {{-- Payment Settings (Owner Only) --}}
        @if (auth()->check() && auth()->id() === $creator->id)
            {{-- DESKTOP: Bottone completo con carta di credito --}}
            <div class="absolute bottom-4 right-4 z-30 hidden md:block">
                <button onclick="window.paymentModal.open()"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-amber-600 via-yellow-500 to-orange-600 p-[2px] shadow-xl shadow-amber-500/40 transition-all duration-500 hover:scale-[1.02] hover:shadow-[0_0_40px_rgba(251,191,36,0.6)]">
                    <div
                        class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white to-transparent opacity-30 transition-transform duration-700 group-hover:translate-x-full">
                    </div>
                    <div class="relative rounded-xl bg-gradient-to-br from-gray-900 to-black px-4 py-2.5">
                        <div class="flex items-center gap-3">
                            <!-- CARTA REALISTICA COMPATTA -->
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

            {{-- MOBILE: FAB compatto - Vanilla JS (NO Alpine!) --}}
            <div id="payment-fab-mobile" class="fixed bottom-20 right-4 z-50 md:hidden">
                <button onclick="window.paymentModal.open()"
                    class="group flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-500 shadow-lg shadow-amber-500/50 transition-all duration-300 active:scale-95">
                    {{-- Icona carta stilizzata --}}
                    <div
                        class="relative h-7 w-10 rounded bg-gradient-to-br from-amber-300 via-yellow-400 to-amber-500 shadow-inner">
                        <div
                            class="absolute left-1 top-1 h-2 w-2.5 rounded-sm bg-gradient-to-br from-yellow-200 to-amber-400">
                        </div>
                        <div class="absolute bottom-1 left-1 text-[5px] font-bold text-white/80">••••</div>
                    </div>
                    {{-- Indicatore attivo --}}
                    <div
                        class="absolute -right-1 -top-1 h-3 w-3 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/80">
                    </div>
                </button>
            </div>
        @endif

        <div class="absolute inset-0 opacity-50" aria-hidden="true">
            <div class="absolute inset-0">
                @php
                    $bannerUrl = method_exists($creator, 'getCreatorBannerUrl')
                        ? $creator->getCreatorBannerUrl('banner')
                        : null;
                @endphp
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="Banner for {{ $creator->name }}"
                        class="h-full w-full object-cover">
                @else
                    {{-- Mobile: sfondo più scuro per contrasto con card --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 md:hidden">
                    </div>
                    <div class="absolute inset-0 hidden md:block"
                        style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
            </div>

            {{-- Edit Banner Button - Owner Only --}}
            @if (auth()->check() && auth()->id() === $creator->id)
                <button type="button" onclick="openImageModal('creator-banner-modal')" id="edit-banner-btn"
                    class="absolute left-4 top-4 z-20 flex touch-manipulation items-center gap-2 rounded-lg bg-black/60 px-3 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:bg-black/80"
                    title="{{ __('creator.profile.edit_banner') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('creator.profile.edit_banner') }}</span>
                </button>
            @endif
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
                    {{-- Avatar iOS size (80pt) - Circular per creator --}}
                    <div class="relative flex-shrink-0">
                        <div class="ring-oro-fiorentino/40 h-20 w-20 overflow-hidden rounded-full ring-2">
                            <img src="{{ $creator->profile_photo_url }}" alt="{{ $creator->name }}"
                                class="h-full w-full object-cover" loading="lazy">
                        </div>
                        {{-- Edit Avatar Button - Owner Only --}}
                        @if (auth()->check() && auth()->id() === $creator->id)
                            <button type="button" onclick="openImageModal('creator-avatar-modal')"
                                id="edit-avatar-btn-mobile"
                                class="bg-oro-fiorentino absolute -bottom-1 -left-1 touch-manipulation rounded-full p-1.5 shadow-lg ring-2 ring-gray-900 transition-all hover:bg-opacity-90"
                                title="{{ __('creator.profile.edit_avatar') }}">
                                <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        @endif
                        @if ($creator->is_verified)
                            <div class="absolute -bottom-1 -right-1 rounded-full bg-verde-rinascita p-1.5 shadow-lg">
                                <svg class="h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-xl font-bold text-white">{{ $creator->name }}</h1>
                        @if ($creator->tagline)
                            <p class="text-oro-fiorentino/80 truncate text-sm italic">"{{ $creator->tagline }}"</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-400">
                            {{ __('creator.home.member_since', ['year' => $creator->created_at->format('Y')]) }}
                        </p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-4 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                {{-- Row 2: Stats iOS-style (equal width columns) --}}
                <div class="grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-white/5 px-3 py-2.5 text-center">
                        <p class="text-lg font-bold text-white">{{ number_format($stats['total_egis'] ?? 0) }}</p>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">
                            {{ __('creator.home.stats.works') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/5 px-3 py-2.5 text-center">
                        <p class="text-lg font-bold text-white">{{ number_format($stats['total_collections'] ?? 0) }}
                        </p>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">
                            {{ __('creator.home.stats.collections') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/5 px-3 py-2.5 text-center">
                        <p class="text-lg font-bold text-white">{{ number_format($stats['total_supporters'] ?? 0) }}
                        </p>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">
                            {{ __('creator.home.stats.patrons') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             DESKTOP: Layout originale (md+)
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="relative z-10 mx-auto hidden max-w-7xl px-4 py-16 sm:px-6 md:block md:py-24 lg:px-8">
            <div class="grid grid-cols-1 items-end gap-12 md:grid-cols-12 md:gap-8">
                {{-- Avatar + Info --}}
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end sm:gap-8 md:col-span-8">
                    <div class="group relative flex-shrink-0">
                        <div
                            class="ring-oro-fiorentino/30 h-32 w-32 overflow-hidden rounded-full shadow-2xl ring-4 md:h-40 md:w-40">
                            <img src="{{ $creator->profile_photo_url }}" alt="{{ $creator->name }}"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy">
                        </div>
                        {{-- Edit Avatar Button - Owner Only --}}
                        @if (auth()->check() && auth()->id() === $creator->id)
                            <button type="button" onclick="openImageModal('creator-avatar-modal')"
                                id="edit-avatar-btn-desktop"
                                class="bg-oro-fiorentino absolute -bottom-2 -right-2 touch-manipulation rounded-full p-2 shadow-lg ring-2 ring-gray-900 transition-all hover:bg-opacity-90"
                                title="{{ __('creator.profile.edit_avatar') }}">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        @endif
                        @if ($creator->is_verified)
                            <div class="absolute -bottom-2 -right-2 rounded-full bg-verde-rinascita p-2 shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col text-center sm:text-left">
                        <h1 class="font-playfair mb-1 text-3xl font-bold text-white md:text-5xl">{{ $creator->name }}
                        </h1>
                        @if ($creator->tagline)
                            <p class="text-oro-fiorentino font-source-sans text-lg italic md:text-xl">
                                "{{ $creator->tagline }}"</p>
                        @endif
                        <div class="mt-3">
                            <p class="text-gray-400">{{ $creator->name }} &middot;
                                {{ __('creator.home.member_since', ['year' => $creator->created_at->format('Y')]) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    <div class="flex gap-8 text-center">
                        <div>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['total_egis'] ?? 0) }}
                            </p>
                            <p class="text-sm uppercase tracking-wider text-gray-400">
                                {{ __('creator.home.stats.works') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">
                                {{ number_format($stats['total_collections'] ?? 0) }}</p>
                            <p class="text-sm uppercase tracking-wider text-gray-400">
                                {{ __('creator.home.stats.collections') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">
                                {{ number_format($stats['total_supporters'] ?? 0) }}</p>
                            <p class="text-sm uppercase tracking-wider text-gray-400">
                                {{ __('creator.home.stats.patrons') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- NAVIGATION TABS --}}
    <x-slot name="platformStats">
        <nav class="sticky top-0 z-40 border-b border-gray-800 bg-gray-900/95 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex overflow-x-auto" id="creator-tabs">
                    <button data-tab="portfolio"
                        data-url="{{ route('creator.portfolio', $creator->nick_name ?? $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? 'portfolio') === 'portfolio' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.portfolio') }}
                    </button>
                    <button data-tab="collections"
                        data-url="{{ route('creator.collections', $creator->nick_name ?? $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'collections' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.collections') }}
                    </button>
                    <button data-tab="biography"
                        data-url="{{ route('creator.biography', $creator->nick_name ?? $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'biography' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.biography') }}
                    </button>
                    <button data-tab="impact"
                        data-url="{{ route('creator.impact', $creator->nick_name ?? $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'impact' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.impact') }}
                    </button>
                    <button data-tab="community"
                        data-url="{{ route('creator.community', $creator->nick_name ?? $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'community' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.community') }}
                    </button>
                </div>
            </div>
        </nav>
    </x-slot>

    {{-- CONTENUTO DINAMICO --}}
    <x-slot name="heroFullWidth">
        <div id="content-loader" class="hidden py-12 text-center">
            <div
                class="border-oro-fiorentino inline-block h-8 w-8 animate-spin rounded-full border-4 border-t-transparent">
            </div>
            <p class="mt-4 text-gray-400">{{ __('common.loading') }}</p>
        </div>

        <div id="content-container">
            @if (isset($activeTab))
                @if ($activeTab === 'portfolio')
                    @include('creator.partials.portfolio-content')
                @elseif($activeTab === 'collections')
                    @include('creator.partials.collections-content')
                @elseif($activeTab === 'biography')
                    @include('creator.partials.biography-content')
                @elseif($activeTab === 'impact')
                    @include('creator.partials.impact-content')
                @elseif($activeTab === 'community')
                    @include('creator.partials.community-content')
                @endif
            @else
                @include('creator.partials.portfolio-content')
            @endif
        </div>
    </x-slot>

    {{-- VANILLA JAVASCRIPT --}}
    <script>
        (function() {
            const tabs = document.querySelectorAll('.creator-tab');
            const contentContainer = document.getElementById('content-container');
            const loader = document.getElementById('content-loader');

            initializePortfolioViewToggle();

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    loadContent(url, this);
                });
            });

            function showLoader() {
                if (!contentContainer || !loader) {
                    return;
                }
                contentContainer.classList.add('hidden');
                loader.classList.remove('hidden');
            }

            function hideLoader() {
                if (!contentContainer || !loader) {
                    return;
                }
                loader.classList.add('hidden');
                contentContainer.classList.remove('hidden');
            }

            function loadContent(url, activeButton) {
                tabs.forEach(t => {
                    t.classList.remove('active', 'text-oro-fiorentino', 'border-oro-fiorentino');
                    t.classList.add('text-gray-300', 'border-transparent');
                });
                activeButton.classList.add('active', 'text-oro-fiorentino', 'border-oro-fiorentino');
                activeButton.classList.remove('text-gray-300', 'border-transparent');

                showLoader();

                fetch(url + '?partial=1', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        contentContainer.innerHTML = html;
                        hideLoader();
                        history.pushState({
                            url: url
                        }, '', url);
                        initializePortfolioViewToggle();
                    })
                    .catch(error => {
                        console.error('Error loading content:', error);
                        contentContainer.innerHTML =
                            '<p class="text-red-500 text-center py-12">Error loading content</p>';
                        hideLoader();
                    });
            }

            function initializePortfolioViewToggle() {
                if (!contentContainer || contentContainer.dataset.viewToggleBound === 'true') {
                    return;
                }

                contentContainer.addEventListener('click', function(event) {
                    const modeToggle = event.target.closest('.portfolio-mode-toggle');
                    if (modeToggle) {
                        event.preventDefault();

                        const desiredMode = modeToggle.getAttribute('data-mode');
                        if (!desiredMode) {
                            return;
                        }

                        const modeUrl = new URL(window.location.href);
                        modeUrl.searchParams.set('mode', desiredMode);
                        modeUrl.searchParams.delete('page');

                        const modeFetchUrl = new URL(modeUrl.toString());
                        modeFetchUrl.searchParams.set('partial', '1');

                        showLoader();

                        fetch(modeFetchUrl.toString(), {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'text/html'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                contentContainer.innerHTML = html;
                                hideLoader();
                                history.pushState({
                                    url: modeUrl.toString()
                                }, '', modeUrl.toString());
                            })
                            .catch(error => {
                                console.error('Error loading portfolio mode:', error);
                                contentContainer.innerHTML =
                                    '<p class="text-red-500 text-center py-12">Error loading content</p>';
                                hideLoader();
                            });
                        return;
                    }

                    const viewToggle = event.target.closest('.view-toggle');
                    if (!viewToggle) {
                        return;
                    }

                    event.preventDefault();

                    const desiredView = viewToggle.getAttribute('data-view');
                    if (!desiredView) {
                        return;
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('view', desiredView);
                    url.searchParams.delete('page');

                    const fetchUrl = new URL(url.toString());
                    fetchUrl.searchParams.set('partial', '1');

                    showLoader();

                    fetch(fetchUrl.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            contentContainer.innerHTML = html;
                            hideLoader();
                            history.pushState({
                                url: url.toString()
                            }, '', url.toString());
                        })
                        .catch(error => {
                            console.error('Error loading view:', error);
                            contentContainer.innerHTML =
                                '<p class="text-red-500 text-center py-12">Error loading content</p>';
                            hideLoader();
                        });
                });

                contentContainer.dataset.viewToggleBound = 'true';
            }

            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    location.reload();
                }
            });
        })();
    </script>


    {{-- Payment Settings Modal --}}
    <x-payment-settings-modal />

    {{-- AI Sidebar - Onboarding Assistant (Owner Only) --}}
    @if (!empty($onboardingChecklist))
        <x-ai-sidebar :user="$creator" :userType="'creator'" :checklist="$onboardingChecklist" />
    @endif

    {{-- Image Upload Modals (only for owner) --}}
    @if (\App\Helpers\FegiAuth::check() && \App\Helpers\FegiAuth::id() === $creator->id)
        {{-- Banner Upload Modal --}}
        <x-modals.image-upload-modal modalId="creator-banner-modal" type="banner" collection="creator_banners"
            uploadRoute="{{ route('creator.upload-banner') }}"
            setCurrentRoute="{{ route('creator.set-current-banner') }}"
            deleteRoute="{{ route('creator.delete-banner') }}" :currentImage="auth()->user()->getCurrentCreatorBanner()" :allImages="auth()->user()->getAllCreatorBanners()"
            title="{{ __('profile.upload_new_banner') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Avatar Upload Modal --}}
        <x-modals.image-upload-modal modalId="creator-avatar-modal" type="avatar" collection="profile_image"
            uploadRoute="{{ route('profile.upload-image') }}"
            setCurrentRoute="{{ route('profile.set-current-image') }}"
            deleteRoute="{{ route('profile.delete-image') }}" :currentImage="auth()->user()->getCurrentProfileImage()" :allImages="auth()->user()->getAllProfileImages()"
            title="{{ __('profile.upload_new_avatar') }}"
            helpText="{{ __('profile.supported_formats_with_size') }}" />

        {{-- Include JS Manager --}}
        <script src="{{ asset('js/home-page-image-manager.js') }}" defer></script>
    @endif

</x-guest-layout>
