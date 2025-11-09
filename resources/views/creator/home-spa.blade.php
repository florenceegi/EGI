@vite(['resources/css/creator-home.css'])

<x-guest-layout :title="$creator->name . ' - Portfolio'" :metaDescription="__('creator.home.meta_description', ['name' => $creator->name])">

    {{-- BANNER + AVATAR + STATS --}}
    <x-slot name="platformInfoButtons">
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

</x-guest-layout>
