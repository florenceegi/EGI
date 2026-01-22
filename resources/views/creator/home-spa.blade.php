@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$creator->name . ' - Portfolio'" :metaDescription="__('creator.home.meta_description', ['name' => $creator->name])">

    {{-- BANNER + AVATAR + STATS --}}
    <x-slot name="platformInfoButtons">
        {{-- Payment Settings (Owner Only) --}}
        @if (auth()->check() && auth()->id() === $creator->id)
            {{-- DESKTOP: Bottone completo con carta di credito --}}
            <div class="absolute bottom-4 right-4 z-30 hidden md:block">
                <button onclick="window.paymentModal.open()" class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-amber-600 via-yellow-500 to-orange-600 p-[2px] shadow-xl shadow-amber-500/40 transition-all duration-500 hover:scale-[1.02] hover:shadow-[0_0_40px_rgba(251,191,36,0.6)]">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 opacity-30"></div>
                    <div class="relative rounded-xl bg-gradient-to-br from-gray-900 to-black px-4 py-2.5">
                        <div class="flex items-center gap-3">
                            <!-- CARTA REALISTICA COMPATTA -->
                            <div class="relative h-8 w-12 rounded-md bg-gradient-to-br from-amber-400 via-yellow-500 to-amber-600 shadow-lg shadow-amber-500/40 transition-all duration-500 group-hover:rotate-6 group-hover:scale-110 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-white/30 via-transparent to-black/20"></div>
                                <div class="absolute left-1 top-1 h-2.5 w-3 rounded-sm bg-gradient-to-br from-yellow-200 via-amber-300 to-yellow-600 shadow-inner">
                                    <div class="grid h-full w-full grid-cols-3 gap-[0.5px] p-[1px]">
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                        <div class="rounded-[0.5px] bg-amber-600/40"></div>
                                    </div>
                                </div>
                                <div class="absolute bottom-1 left-1 text-[6px] font-bold text-white/90 tracking-wide drop-shadow">•••• 4242</div>
                                <div class="absolute bottom-1 right-1 flex gap-[1px]">
                                    <div class="h-1 w-1 rounded-full bg-red-500/80"></div>
                                    <div class="h-1 w-1 rounded-full bg-yellow-500/80"></div>
                                </div>
                                <div class="absolute left-0 right-0 top-1/2 h-1 -translate-y-1/2 bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800"></div>
                            </div>
                            <span class="bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-400 bg-clip-text text-sm font-bold text-transparent">{{ __('payment.settings_title') }}</span>
                            <div class="ml-1 h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400 shadow-lg shadow-amber-400/80"></div>
                        </div>
                    </div>
                </button>
            </div>

            {{-- MOBILE: FAB compatto - Vanilla JS (NO Alpine!) --}}
            <div id="payment-fab-mobile" class="fixed bottom-20 right-4 z-50 md:hidden">
                <button onclick="window.paymentModal.open()" 
                    class="group flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-500 shadow-lg shadow-amber-500/50 transition-all duration-300 active:scale-95">
                    {{-- Icona carta stilizzata --}}
                    <div class="relative h-7 w-10 rounded bg-gradient-to-br from-amber-300 via-yellow-400 to-amber-500 shadow-inner">
                        <div class="absolute left-1 top-1 h-2 w-2.5 rounded-sm bg-gradient-to-br from-yellow-200 to-amber-400"></div>
                        <div class="absolute bottom-1 left-1 text-[5px] font-bold text-white/80">••••</div>
                    </div>
                    {{-- Indicatore attivo --}}
                    <div class="absolute -top-1 -right-1 h-3 w-3 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/80"></div>
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
                    <div class="absolute inset-0"
                        style="background-image: url('/images/default/random_background/7.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-24 lg:px-8">
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
                                {{ __('creator.home.member_since', ['year' => $creator->created_at->format('Y')]) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex flex-col items-center gap-6 md:col-span-4 md:items-end">
                    <div class="flex gap-8 text-center">
                        <div>
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['total_egis'] ?? 0) }}</p>
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
                    <button data-tab="portfolio" data-url="{{ route('creator.portfolio', $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? 'portfolio') === 'portfolio' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.portfolio') }}
                    </button>
                    <button data-tab="collections" data-url="{{ route('creator.collections', $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'collections' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.collections') }}
                    </button>
                    <button data-tab="biography" data-url="{{ route('creator.biography', $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'biography' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.biography') }}
                    </button>
                    <button data-tab="impact" data-url="{{ route('creator.impact', $creator->id) }}"
                        class="creator-tab {{ ($activeTab ?? '') === 'impact' ? 'active text-oro-fiorentino border-oro-fiorentino' : 'text-gray-300 border-transparent' }} whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium hover:text-white">
                        {{ __('creator.home.nav.impact') }}
                    </button>
                    <button data-tab="community" data-url="{{ route('creator.community', $creator->id) }}"
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

</x-guest-layout>
