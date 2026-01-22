{{-- resources/views/creators/index.blade.php --}}
<x-platform-layout :title="__('creator.index.page_title')" :metaDescription="__('creator.index.meta_description')">

<x-slot name="platformHeaderBanner">
    <x-collector-banner
        title="CREATORS"
        subtitle="Scopri gli artisti visionari che plasmano l'arte del futuro"
        :total-works="App\Models\User::whereHas('createdEgis')->count()"
        :total-artists="App\Models\Egi::distinct('user_id')->count()"
        :total-reservations="App\Models\Reservation::where('is_current', true)->where('status', 'active')->where('is_highest', true)->sum('offer_amount_fiat')"
    />
</x-slot>

<x-slot name="heroFullWidth">
    <div class="relative py-16 bg-gray-900 sm:py-24 lg:py-32">
        <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl font-display">
                    {{ __('creator.index.main_title') }}
                </h1>
                <p class="max-w-3xl mx-auto mt-4 text-xl text-gray-300">
                    {{ __('creator.index.subtitle') }}
                </p>
            </div>

            {{-- 🔍 Bottone Filtri Mobile (hamburger) --}}
            <div class="flex justify-center mt-8 lg:hidden">
                <button 
                    type="button" 
                    id="mobileFiltersToggle"
                    onclick="document.getElementById('mobileFiltersPanel').classList.toggle('hidden')"
                    class="inline-flex items-center gap-2 px-6 py-3 text-white transition-all duration-200 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-florence-gold focus:ring-offset-2 focus:ring-offset-gray-900"
                    aria-expanded="false"
                    aria-controls="mobileFiltersPanel"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('creator.index.filter_button') }}
                    @if(request()->hasAny(['query', 'category', 'sort']))
                        <span class="flex items-center justify-center w-5 h-5 text-xs font-bold text-gray-900 rounded-full bg-florence-gold">
                            {{ collect(['query', 'category'])->filter(fn($k) => request()->filled($k))->count() + (request('sort', 'latest') !== 'latest' ? 1 : 0) }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- 🔍 Pannello Filtri Mobile (collapsibile) --}}
            <div id="mobileFiltersPanel" class="hidden mt-6 lg:hidden">
                <div class="p-4 bg-gray-800 shadow-lg rounded-xl">
                    <form action="{{ route('creator.index') }}" method="GET" class="space-y-4">
                        {{-- Campo di Ricerca --}}
                        <div>
                            <label for="query_mobile" class="block mb-2 text-sm font-medium text-gray-300">
                                {{ __('creator.index.search_label') }}
                            </label>
                            <div class="relative">
                                <input
                                    type="search"
                                    name="query"
                                    id="query_mobile"
                                    value="{{ $filters['query'] ?? '' }}"
                                    placeholder="{{ __('creator.index.search_placeholder') }}"
                                    class="block w-full px-4 py-3 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-400 material-symbols-outlined">search</span>
                                </div>
                            </div>
                        </div>

                        {{-- Filtro Categoria --}}
                        <div>
                            <label for="category_mobile" class="block mb-2 text-sm font-medium text-gray-300">
                                {{ __('creator.index.filter_category') }}
                            </label>
                            <select
                                id="category_mobile"
                                name="category"
                                class="block w-full px-4 py-3 text-white bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                            >
                                <option value="">{{ __('creator.index.all_categories') }}</option>
                                <option value="art" @selected(($filters['category'] ?? '') == 'art')>Art</option>
                                <option value="music" @selected(($filters['category'] ?? '') == 'music')>Music</option>
                                <option value="photography" @selected(($filters['category'] ?? '') == 'photography')>Photography</option>
                            </select>
                        </div>

                        {{-- Ordinamento --}}
                        <div>
                            <label for="sort_mobile" class="block mb-2 text-sm font-medium text-gray-300">
                                {{ __('creator.index.sort_by') }}
                            </label>
                            <select
                                id="sort_mobile"
                                name="sort"
                                class="block w-full px-4 py-3 text-white bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                            >
                                <option value="latest" @selected(($filters['sort'] ?? '') == 'latest')>{{ __('creator.index.sort_latest') }}</option>
                                <option value="oldest" @selected(($filters['sort'] ?? '') == 'oldest')>{{ __('creator.index.sort_oldest') }}</option>
                                <option value="name_asc" @selected(($filters['sort'] ?? '') == 'name_asc')>{{ __('creator.index.sort_name_asc') }}</option>
                                <option value="name_desc" @selected(($filters['sort'] ?? '') == 'name_desc')>{{ __('creator.index.sort_name_desc') }}</option>
                                <option value="random" @selected(($filters['sort'] ?? '') == 'random')>{{ __('creator.index.sort_random') }}</option>
                            </select>
                        </div>

                        {{-- Pulsanti Azione --}}
                        <div class="flex gap-3 pt-2">
                            <a href="{{ route('creator.index') }}" 
                               class="flex-1 px-4 py-3 text-center text-white transition-colors duration-200 bg-gray-700 rounded-lg hover:bg-gray-600">
                                {{ __('creator.index.reset_filters') }}
                            </a>
                            <button type="submit" 
                                    class="flex-1 px-4 py-3 text-white transition-colors duration-200 rounded-lg bg-verde-rinascita hover:bg-verde-rinascita-dark">
                                {{ __('creator.index.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 🔍 Sezione Filtri Desktop (invariata) --}}
            <div class="hidden p-6 mt-12 bg-gray-800 shadow-lg lg:block rounded-xl">
                <form action="{{ route('creator.index') }}" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-4">
                    <div class="md:col-span-2 lg:col-span-2">
                        <label for="query" class="block text-sm font-medium text-gray-300 sr-only">
                            {{ __('creator.index.search_placeholder') }}
                        </label>
                        <div class="relative">
                            <input
                                type="search"
                                name="query"
                                id="query"
                                value="{{ $filters['query'] ?? '' }}"
                                placeholder="{{ __('creator.index.search_placeholder') }}"
                                class="block w-full px-4 py-3 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400 material-symbols-outlined">search</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-300 sr-only">
                            {{ __('creator.index.filter_category') }}
                        </label>
                        <select
                            id="category"
                            name="category"
                            class="block w-full px-4 py-3 text-white bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                        >
                            <option value="">{{ __('creator.index.all_categories') }}</option>
                            <option value="art" @selected(($filters['category'] ?? '') == 'art')>Art</option>
                            <option value="music" @selected(($filters['category'] ?? '') == 'music')>Music</option>
                            <option value="photography" @selected(($filters['category'] ?? '') == 'photography')>Photography</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-300 sr-only">
                            {{ __('creator.index.sort_by') }}
                        </label>
                        <select
                            id="sort"
                            name="sort"
                            class="block w-full px-4 py-3 text-white bg-gray-700 border border-gray-600 rounded-lg focus:ring-verde-rinascita focus:border-verde-rinascita"
                        >
                            <option value="latest" @selected(($filters['sort'] ?? '') == 'latest')>{{ __('creator.index.sort_latest') }}</option>
                            <option value="oldest" @selected(($filters['sort'] ?? '') == 'oldest')>{{ __('creator.index.sort_oldest') }}</option>
                            <option value="name_asc" @selected(($filters['sort'] ?? '') == 'name_asc')>{{ __('creator.index.sort_name_asc') }}</option>
                            <option value="name_desc" @selected(($filters['sort'] ?? '') == 'name_desc')>{{ __('creator.index.sort_name_desc') }}</option>
                            <option value="random" @selected(($filters['sort'] ?? '') == 'random')>{{ __('creator.index.sort_random') }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end md:col-span-3 lg:col-span-4">
                        <a href="{{ route('creator.index') }}" class="px-6 py-3 text-white transition-colors duration-200 rounded-lg btn btn-secondary hover:bg-gray-700">
                            {{ __('creator.index.reset_filters') }}
                        </a>
                        <button type="submit" class="px-6 py-3 ml-4 text-white transition-colors duration-200 rounded-lg btn btn-primary bg-verde-rinascita hover:bg-verde-rinascita-dark">
                            {{ __('creator.index.apply_filters') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- 🖼️ Griglia dei Creator - iPhone First con multi-colonna --}}
            <div class="grid grid-cols-2 gap-3 px-2 mt-12 sm:gap-4 sm:px-0 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 lg:gap-8">
                @forelse($creators as $creator)
                    <x-creator-card :creator="$creator" />
                @empty
                    <div class="py-12 text-center text-gray-400 col-span-full">
                        <p>{{ __('creator.index.no_creators_found') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $creators->links() }}
            </div>
        </div>
    </div>
</x-slot>

</x-platform-layout>
