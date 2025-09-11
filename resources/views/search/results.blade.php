<x-app-layout>
<div class="px-4 mx-auto mt-8 max-w-7xl">
    <h1 class="mb-6 text-2xl font-bold text-white">{{ __('search.results.title') }}</h1>
    {{-- Barra / Info ricerca --}}
    <div class="flex flex-col gap-3 mb-6 md:flex-row md:items-center md:justify-between">
        <form method="GET" action="{{ url('/search/results') }}" class="flex flex-1 gap-2" aria-label="{{ __('search.results.form_aria') }}">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('search.results.placeholder') }}"
                   class="w-full px-3 py-2 text-sm text-white placeholder-gray-400 border border-gray-700 rounded-lg bg-gray-800/60 focus:outline-none focus:ring-2 focus:ring-purple-600" />
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500">{{ __('search.results.search_button') }}</button>
        </form>
        <button type="button" onclick="window.UniversalSearch && window.UniversalSearch.open()" class="px-4 py-2 text-sm font-medium text-gray-300 border border-gray-700 rounded-lg bg-gray-800/70 hover:bg-gray-700 hover:text-white">{{ __('search.results.open_advanced') }}</button>
    </div>

    @if($q)
    <p class="mb-4 text-sm text-gray-400">{{ __('search.results.query_label') }} <span class="font-semibold text-purple-300">"{{ $q }}"</span></p>
    @endif

    @php
        $totalAll = 0;
        if(isset($egiResults)) $totalAll += $egiResults?->total() ?? 0;
        if(isset($collectionResults)) $totalAll += $collectionResults?->total() ?? 0;
        if(isset($creatorResults)) $totalAll += $creatorResults?->total() ?? 0;
    @endphp
    <p class="mb-10 text-xs tracking-wide text-gray-400 uppercase">{{ __('search.results.total_all') }} <span class="font-semibold text-emerald-400">{{ $totalAll }}</span></p>

    {{-- EGIs --}}
    @if($egiResults)
        <div class="mt-8">
            <h2 class="mb-3 text-lg font-semibold text-purple-300">{{ __('search.results.egis_heading', ['count' => $egiResults->total()]) }}</h2>
            @if($egiResults->count())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($egiResults as $egi)
                        @include('components.egi-card-list', ['egi' => $egi])
                    @endforeach
                </div>
                <div class="mt-4">{{ $egiResults->withQueryString()->links() }}</div>
            @else
                <p class="text-sm text-gray-500">{{ __('search.results.none_egis') }}</p>
            @endif
        </div>
    @endif

    {{-- Collections --}}
    @if($collectionResults)
        <div class="mt-12">
            <h2 class="mb-3 text-lg font-semibold text-amber-300">{{ __('search.results.collections_heading', ['count' => $collectionResults->total()]) }}</h2>
            @if($collectionResults->count())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($collectionResults as $collection)
                        @include('components.collection-card-list', ['collection' => $collection, 'id' => $collection->id])
                    @endforeach
                </div>
                <div class="mt-4">{{ $collectionResults->withQueryString()->links() }}</div>
            @else
                <p class="text-sm text-gray-500">{{ __('search.results.none_collections') }}</p>
            @endif
        </div>
    @endif

    {{-- Creators --}}
    @if($creatorResults)
        <div class="mt-12">
            <h2 class="mb-3 text-lg font-semibold text-cyan-300">{{ __('search.results.creators_heading', ['count' => $creatorResults->total()]) }}</h2>
            @if($creatorResults->count())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($creatorResults as $creator)
                        @include('components.creator-card-list', ['creator' => $creator])
                    @endforeach
                </div>
                <div class="mt-4">{{ $creatorResults->withQueryString()->links() }}</div>
            @else
                <p class="text-sm text-gray-500">{{ __('search.results.none_creators') }}</p>
            @endif
        </div>
    @endif
</div>
</x-app-layout>
