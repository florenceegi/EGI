<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Creator' }} - FlorenceEGI</title>
    @vite(['resources/css/app.css', 'resources/css/creator-home.css'])
</head>

<body class="bg-gray-900 text-white">

    {{-- HEADER FISSO --}}
    <header class="fixed left-0 right-0 top-0 z-50 bg-gray-900">
        {{-- Banner + Avatar + Stats --}}
        <div class="relative overflow-hidden">
            {{-- Background Banner --}}
            <div class="absolute inset-0 opacity-50">
                @php
                    $bannerUrl = method_exists($creator, 'getCreatorBannerUrl')
                        ? $creator->getCreatorBannerUrl('banner')
                        : null;
                @endphp
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="Banner for {{ $creator->name }}"
                        class="h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
            </div>

            {{-- Hero Content --}}
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 items-end gap-8 md:grid-cols-12">
                    {{-- Profile Section --}}
                    <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-end md:col-span-8">
                        <div class="relative flex-shrink-0">
                            <div
                                class="ring-oro-fiorentino/30 h-24 w-24 overflow-hidden rounded-full shadow-2xl ring-4 md:h-32 md:w-32">
                                <img src="{{ $creator->profile_photo_url }}" alt="{{ $creator->name }}"
                                    class="h-full w-full object-cover">
                            </div>
                        </div>
                        <div class="flex flex-col text-center sm:text-left">
                            <h1 class="mb-1 text-2xl font-bold text-white md:text-4xl">{{ $creator->name }}</h1>
                            @if ($creator->tagline)
                                <p class="text-oro-fiorentino text-lg italic">"{{ $creator->tagline }}"</p>
                            @endif
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex gap-6 text-center md:col-span-4 md:justify-end">
                        <div>
                            <p class="text-xl font-bold text-white">{{ number_format($stats['total_egis']) }}</p>
                            <p class="text-xs uppercase text-gray-400">{{ __('creator.home.stats.works') }}</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-white">{{ number_format($stats['total_collections']) }}
                            </p>
                            <p class="text-xs uppercase text-gray-400">{{ __('creator.home.stats.collections') }}</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-white">
                                {{ number_format($stats['total_supporters'] ?? 0) }}</p>
                            <p class="text-xs uppercase text-gray-400">{{ __('creator.home.stats.patrons') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <nav class="border-b border-gray-800 bg-gray-900/95 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="scrollbar-hide flex overflow-x-auto" id="creator-tabs">
                    <button data-tab="portfolio"
                        class="creator-tab text-oro-fiorentino border-oro-fiorentino active whitespace-nowrap border-b-2 px-6 py-4 text-sm font-medium">
                        {{ __('creator.home.nav.portfolio') }}
                    </button>
                    <button data-tab="collections"
                        class="creator-tab whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 hover:border-gray-600 hover:text-white">
                        {{ __('creator.home.nav.collections') }}
                    </button>
                    <button data-tab="biography"
                        class="creator-tab whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 hover:border-gray-600 hover:text-white">
                        {{ __('creator.home.nav.biography') }}
                    </button>
                    <button data-tab="impact"
                        class="creator-tab whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 hover:border-gray-600 hover:text-white">
                        {{ __('creator.home.nav.impact') }}
                    </button>
                    <button data-tab="community"
                        class="creator-tab whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-300 hover:border-gray-600 hover:text-white">
                        {{ __('creator.home.nav.community') }}
                    </button>
                </div>
            </div>
        </nav>
    </header>

    {{-- CONTENT DINAMICO --}}
    <main class="pb-12 pt-64" id="creator-content">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div id="content-loader" class="hidden py-12 text-center">
                <div
                    class="border-oro-fiorentino inline-block h-8 w-8 animate-spin rounded-full border-4 border-t-transparent">
                </div>
                <p class="mt-4 text-gray-400">{{ __('common.loading') }}</p>
            </div>
            <div id="content-container">
                @yield('content')
            </div>
        </div>
    </main>

    {{-- Vanilla JavaScript per gestione tab --}}
    <script>
        (function() {
            const creatorId = {{ $creator->id }};
            const tabs = document.querySelectorAll('.creator-tab');
            const contentContainer = document.getElementById('content-container');
            const loader = document.getElementById('content-loader');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabName = this.getAttribute('data-tab');
                    loadContent(tabName);
                });
            });

            function loadContent(tabName) {
                // Update active tab
                tabs.forEach(t => {
                    t.classList.remove('active', 'text-oro-fiorentino', 'border-oro-fiorentino');
                    t.classList.add('text-gray-300', 'border-transparent');
                });
                const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
                activeTab.classList.add('active', 'text-oro-fiorentino', 'border-oro-fiorentino');
                activeTab.classList.remove('text-gray-300', 'border-transparent');

                // Show loader
                contentContainer.classList.add('hidden');
                loader.classList.remove('hidden');

                // Fetch content
                fetch(`/creator/${creatorId}/${tabName}?partial=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        contentContainer.innerHTML = html;
                        contentContainer.classList.remove('hidden');
                        loader.classList.add('hidden');

                        // Update URL without reload
                        history.pushState({
                            tab: tabName
                        }, '', `/creator/${creatorId}/${tabName}`);
                    })
                    .catch(error => {
                        console.error('Error loading content:', error);
                        contentContainer.innerHTML =
                        '<p class="text-red-500 text-center">Error loading content</p>';
                        contentContainer.classList.remove('hidden');
                        loader.classList.add('hidden');
                    });
            }

            // Handle browser back/forward
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.tab) {
                    loadContent(e.state.tab);
                }
            });
        })();
    </script>

    @vite(['resources/js/app.js'])

    {{-- 🎩 Natan Assistant - Sempre Visibile --}}
    <div id="natan-global-assistant" class="fixed bottom-6 right-6 z-[9999]" role="region"
        aria-label="Natan Assistant">
        @include('components.natan-assistant', ['suffix' => '-global'])
    </div>

</body>

</html>
