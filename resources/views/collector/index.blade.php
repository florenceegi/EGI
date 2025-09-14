{{-- resources/views/collector/index.blade.php --}}
<x-platform-layout :title="__('collector.index.page_title')" :metaDescription="__('collector.index.meta_description')">

    <x-slot name="platformHeaderBanner">
        <x-collector-banner
            :total-works="$totalReservedWorks"
            :total-artists="$totalArtistsWithReservations"
            cta-text="Scopri le Opere"
            cta-link=""
            subtitle="L'eccellenza del collezionismo rinascimentale"
        />
    </x-slot>

    <x-slot name="heroFullWidth">
        <div class="relative py-6 bg-gray-900 sm:py-8 lg:py-10">
            <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Search and Platform Stats Toggle Buttons -->
                <div class="flex justify-center mb-4 space-x-4">
                    <!-- Search Toggle Button -->
                    <button id="searchToggle" type="button"
                        class="flex items-center justify-center p-3 text-white transition-all duration-300 rounded-full bg-verde-rinascita hover:bg-verde-rinascita-dark focus:outline-none focus:ring-2 focus:ring-verde-rinascita focus:ring-offset-2 focus:ring-offset-gray-900"
                        aria-label="{{ __('collector.index.toggle_search') }}"
                        aria-expanded="false">
                        <span class="text-2xl material-symbols-outlined">search</span>
                    </button>

                    <!-- Platform Holders Toggle Button -->
                    <button id="holdersToggle" type="button"
                        class="flex items-center justify-center p-3 text-white transition-all duration-300 rounded-full bg-blu-algoritmo hover:bg-blu-algoritmo-dark focus:outline-none focus:ring-2 focus:ring-blu-algoritmo focus:ring-offset-2 focus:ring-offset-gray-900"
                        aria-label="{{ __('collector.index.toggle_holders') }}"
                        aria-expanded="false">
                        <span class="text-2xl material-symbols-outlined">leaderboard</span>
                    </button>
                </div>

                <!-- Collapsible Search Area -->
                <div id="searchArea" class="hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 bg-gray-800 shadow-lg rounded-xl">
                        <form action="{{ route('collector.index') }}" method="GET"
                            class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-4">
                            <div class="md:col-span-2 lg:col-span-2">
                                <label for="query" class="block text-sm font-medium text-gray-300 sr-only">
                                    {{ __('collector.index.search_placeholder') }}
                                </label>
                                <div class="relative">
                                    <input type="search" name="query" id="query" value="{{ $query ?? '' }}"
                                        placeholder="{{ __('collector.index.search_placeholder') }}"
                                        class="block w-full px-4 py-3 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-400 material-symbols-outlined">search</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-300 sr-only">
                                    {{ __('collector.index.sort_by') }}
                                </label>
                                <select id="sort" name="sort"
                                    class="block w-full px-4 py-3 text-white bg-gray-700 border border-gray-600 rounded-lg focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <option value="latest" @selected($sort == 'latest')>
                                        {{ __('collector.index.sort_latest') }}</option>
                                    <option value="most_egis" @selected($sort == 'most_egis')>
                                        {{ __('collector.index.sort_most_egis') }}</option>
                                    <option value="most_spent" @selected($sort == 'most_spent')>
                                        {{ __('collector.index.sort_most_spent') }}</option>
                                </select>
                            </div>

                            <div class="flex justify-end md:col-span-3 lg:col-span-4">
                                <a href="{{ route('collector.index') }}"
                                    class="px-6 py-3 text-white transition-colors duration-200 rounded-lg btn btn-secondary hover:bg-gray-700">
                                    {{ __('collector.index.reset_filters') }}
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 ml-4 text-white transition-colors duration-200 rounded-lg btn btn-primary bg-verde-rinascita hover:bg-verde-rinascita-dark">
                                    {{ __('collector.index.apply_filters') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Collapsible Platform Holders Area -->
                <div id="holdersArea" class="hidden mt-6 transition-all duration-300 ease-in-out">
                    <x-stats.platform-holders-summary :limit="10" />
                </div>

                <div class="grid grid-cols-1 gap-8 mt-12 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($collectors as $collector)
                        <x-collector-card :collector="$collector" />
                    @empty
                        <div class="py-12 text-center text-gray-400 col-span-full">
                            <p>{{ __('collector.index.no_collectors_found') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $collectors->links() }}
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search Toggle Logic
            const searchToggle = document.getElementById('searchToggle');
            const searchArea = document.getElementById('searchArea');
            const searchIcon = searchToggle.querySelector('.material-symbols-outlined');
            let isSearchExpanded = false;

            // Holders Toggle Logic
            const holdersToggle = document.getElementById('holdersToggle');
            const holdersArea = document.getElementById('holdersArea');
            const holdersIcon = holdersToggle.querySelector('.material-symbols-outlined');
            let isHoldersExpanded = false;

            // Controlla se ci sono parametri di ricerca attivi per mostrare l'area aperta
            const urlParams = new URLSearchParams(window.location.search);
            const hasSearchParams = urlParams.has('query') || urlParams.has('sort');

            if (hasSearchParams) {
                toggleSearchArea(true);
            }

            searchToggle.addEventListener('click', function() {
                toggleSearchArea(!isSearchExpanded);
            });

            holdersToggle.addEventListener('click', function() {
                toggleHoldersArea(!isHoldersExpanded);
            });

            function toggleSearchArea(expand) {
                isSearchExpanded = expand;

                if (isSearchExpanded) {
                    // Mostra l'area di ricerca
                    searchArea.classList.remove('hidden');
                    searchArea.classList.add('animate-fadeInDown');
                    searchToggle.setAttribute('aria-expanded', 'true');

                    // Cambia icona e stile del bottone
                    searchIcon.textContent = 'expand_less';
                    searchToggle.classList.add('bg-verde-rinascita-dark');

                    // Focus sul campo di ricerca
                    setTimeout(() => {
                        document.getElementById('query').focus();
                    }, 100);
                } else {
                    // Nascondi l'area di ricerca
                    searchArea.classList.add('animate-fadeOutUp');

                    setTimeout(() => {
                        searchArea.classList.add('hidden');
                        searchArea.classList.remove('animate-fadeInDown', 'animate-fadeOutUp');
                    }, 200);

                    searchToggle.setAttribute('aria-expanded', 'false');

                    // Ripristina icona e stile del bottone
                    searchIcon.textContent = 'search';
                    searchToggle.classList.remove('bg-verde-rinascita-dark');
                }
            }

            function toggleHoldersArea(expand) {
                isHoldersExpanded = expand;

                if (isHoldersExpanded) {
                    // Mostra l'area holders
                    holdersArea.classList.remove('hidden');
                    holdersArea.classList.add('animate-fadeInDown');
                    holdersToggle.setAttribute('aria-expanded', 'true');

                    // Cambia icona e stile del bottone
                    holdersIcon.textContent = 'expand_less';
                    holdersToggle.classList.add('bg-blu-algoritmo-dark');
                } else {
                    // Nascondi l'area holders
                    holdersArea.classList.add('animate-fadeOutUp');

                    setTimeout(() => {
                        holdersArea.classList.add('hidden');
                        holdersArea.classList.remove('animate-fadeInDown', 'animate-fadeOutUp');
                    }, 200);

                    holdersToggle.setAttribute('aria-expanded', 'false');

                    // Ripristina icona e stile del bottone
                    holdersIcon.textContent = 'leaderboard';
                    holdersToggle.classList.remove('bg-blu-algoritmo-dark');
                }
            }
        });
    </script>

    <style>
        .animate-fadeInDown {
            animation: fadeInDown 0.3s ease-out forwards;
        }

        .animate-fadeOutUp {
            animation: fadeOutUp 0.2s ease-in forwards;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    </style>
    @endpush

</x-platform-layout>
