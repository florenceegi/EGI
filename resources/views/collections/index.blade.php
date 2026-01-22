{{-- resources/views/collections/index.blade.php --}}
{{-- 📜 Oracode View: Collections Grid --}}
{{-- Displays a paginated and filterable grid of EGI Collections. --}}
{{-- Uses Tailwind CSS for styling and layout. --}}
{{-- Expects $collections (Paginator instance) and $epps (Collection of Epp) from the controller. --}}

<x-platform-layout :title="__('collection.index.page_title')" :metaDescription="__('collection.index.meta_description')">

    {{-- Aggiungiamo Schema.org per la pagina elenco --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "CollectionPage", // Indica che questa è una pagina elenco
          "name": "{{ __('collection.index.main_title') }}",
          "description": "{{ __('collection.index.meta_description') }}",
          "url": "{{ route('home.collections.index') }}",
          "mainEntity": {
            "@type": "ItemList",
            "itemListElement": [
              @foreach ($collections as $index => $collection)
                {
                  "@type": "ListItem",
                  "position": {{ $collections->firstItem() + $index }},
                  "item": {
                    "@type": "CreativeWork", // O tipo più specifico se conosciuto
                    "name": "{{ $collection->collection_name }}",
                    "url": "{{ route('home.collections.show', $collection->id) }}",
                    "image": "{{ $collection->image_card ? Storage::url($collection->image_card) : asset('images/logo/logo.png') }}",
                    "author": {
                      "@type": "Person",
                      "name": "{{ $collection->creator->name ?? 'Unknown Creator' }}"
                    }
                  }
                }{{ !$loop->last ? ',' : '' }}
              @endforeach
            ]
          }
        }
        </script>
    </x-slot>

    <x-slot name="platformHeaderBanner">
        <x-collector-banner title="COLLECTIONS" subtitle="Esplora collezioni uniche e rare del mondo dell'arte digitale"
            :total-works="App\Models\Collection::count()" :total-artists="App\Models\Collection::distinct('creator_id')->count()" />
    </x-slot>

    <x-slot name="heroFullWidth">
        <div class="relative bg-gray-900 py-16 sm:py-24 lg:py-32"> {{-- Contenitore principale come per i Creator --}}
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="font-display text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">
                        {{ __('collection.index.main_title') }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-xl text-gray-300">
                        {{ __('collection.index.subtitle') }}
                    </p>
                </div>

                {{-- 🔍 Bottone Filtri Mobile (hamburger) --}}
                <div class="mt-8 flex justify-center lg:hidden">
                    <button type="button" id="mobileFiltersToggle"
                        onclick="document.getElementById('mobileFiltersPanel').classList.toggle('hidden')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-600 bg-gray-700 px-6 py-3 text-white transition-all duration-200 hover:bg-gray-600 focus:ring-2 focus:ring-florence-gold focus:ring-offset-2 focus:ring-offset-gray-900"
                        aria-expanded="false" aria-controls="mobileFiltersPanel">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('collection.index.filter_button') }}
                        @if (request()->hasAny(['query', 'epp', 'sort', 'status']))
                            <span
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-florence-gold text-xs font-bold text-gray-900">
                                {{ collect(['query', 'epp', 'status'])->filter(fn($k) => request()->filled($k))->count() + (request('sort', 'newest') !== 'newest' ? 1 : 0) }}
                            </span>
                        @endif
                    </button>
                </div>

                {{-- 🔍 Pannello Filtri Mobile (collapsibile) --}}
                <div id="mobileFiltersPanel" class="mt-6 hidden lg:hidden">
                    <div class="rounded-xl bg-gray-800 p-4 shadow-lg">
                        <form action="{{ route('home.collections.index') }}" method="GET" class="space-y-4">
                            {{-- Campo di Ricerca --}}
                            <div>
                                <label for="query_mobile" class="mb-2 block text-sm font-medium text-gray-300">
                                    {{ __('collection.index.search_label') }}
                                </label>
                                <div class="relative">
                                    <input type="search" name="query" id="query_mobile"
                                        value="{{ request('query') }}"
                                        placeholder="{{ __('collection.index.search_placeholder') }}"
                                        class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="material-symbols-outlined text-gray-400">search</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Filtro EPP --}}
                            <div>
                                <label for="epp_mobile" class="mb-2 block text-sm font-medium text-gray-300">
                                    {{ __('collection.index.filter_epp') }}
                                </label>
                                <select name="epp" id="epp_mobile"
                                    class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <option value="">{{ __('collection.index.all_epps') }}</option>
                                    @foreach ($epps as $epp)
                                        <option value="{{ $epp->id }}"
                                            {{ request('epp') == $epp->id ? 'selected' : '' }}>{{ $epp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Ordinamento --}}
                            <div>
                                <label for="sort_mobile" class="mb-2 block text-sm font-medium text-gray-300">
                                    {{ __('collection.index.sort_by') }}
                                </label>
                                <select name="sort" id="sort_mobile"
                                    class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <option value="newest"
                                        {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                                        {{ __('collection.index.sort_newest') }}</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                        {{ __('collection.index.sort_oldest') }}</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                        {{ __('collection.index.sort_name_asc') }}</option>
                                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>
                                        {{ __('collection.index.sort_popularity') }}</option>
                                </select>
                            </div>

                            {{-- Stato (solo autorizzati) --}}
                            @can('view_draft_collections')
                                <div>
                                    <label for="status_mobile" class="mb-2 block text-sm font-medium text-gray-300">
                                        {{ __('collection.index.filter_status') }}
                                    </label>
                                    <select name="status" id="status_mobile"
                                        class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                        <option value="">{{ __('collection.index.all_statuses') }}</option>
                                        <option value="published"
                                            {{ request('status', 'published') == 'published' ? 'selected' : '' }}>
                                            {{ __('collection.index.status_published') }}</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                            {{ __('collection.index.status_draft') }}</option>
                                    </select>
                                </div>
                            @endcan

                            @if (request()->filled('creator'))
                                <input type="hidden" name="creator" value="{{ request('creator') }}">
                            @endif

                            {{-- Pulsanti Azione --}}
                            <div class="flex gap-3 pt-2">
                                <a href="{{ route('home.collections.index', array_filter(request()->only('creator'))) }}"
                                    class="flex-1 rounded-lg bg-gray-700 px-4 py-3 text-center text-white transition-colors duration-200 hover:bg-gray-600">
                                    {{ __('collection.index.reset_filters') }}
                                </a>
                                <button type="submit"
                                    class="flex-1 rounded-lg bg-verde-rinascita px-4 py-3 text-white transition-colors duration-200 hover:bg-verde-rinascita-dark">
                                    {{ __('collection.index.apply_filters') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 🔍 Sezione Filtri Desktop (invariata) --}}
                <div class="mt-12 hidden rounded-xl bg-gray-800 p-6 shadow-lg lg:block">
                    <form action="{{ route('home.collections.index') }}" method="GET"
                        class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-4">
                        {{-- Campo di Ricerca --}}
                        <div class="md:col-span-2 lg:col-span-2">
                            <label for="query" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('collection.index.search_placeholder') }}
                            </label>
                            <div class="relative">
                                <input type="search" name="query" id="query" value="{{ request('query') }}"
                                    placeholder="{{ __('collection.index.search_placeholder') }}"
                                    class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 focus:border-verde-rinascita focus:ring-verde-rinascita">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="material-symbols-outlined text-gray-400">search</span>
                                </div>
                            </div>
                        </div>

                        {{-- 🌳 Gruppo Filtro: EPP --}}
                        <div>
                            <label for="epp" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('collection.index.filter_epp') }}
                            </label>
                            <select name="epp" id="epp"
                                class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                <option value="">{{ __('collection.index.all_epps') }}</option>
                                @foreach ($epps as $epp)
                                    <option value="{{ $epp->id }}"
                                        {{ request('epp') == $epp->id ? 'selected' : '' }}>{{ $epp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 🔢 Gruppo Filtro: Ordinamento --}}
                        <div>
                            <label for="sort" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('collection.index.sort_by') }}
                            </label>
                            <select name="sort" id="sort"
                                class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                                    {{ __('collection.index.sort_newest') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                    {{ __('collection.index.sort_oldest') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                    {{ __('collection.index.sort_name_asc') }}</option>
                                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>
                                    {{ __('collection.index.sort_popularity') }}</option>
                            </select>
                        </div>

                        {{-- 🏷️ Gruppo Filtro: Stato (solo per utenti autorizzati) --}}
                        @can('view_draft_collections')
                            <div>
                                <label for="status" class="sr-only block text-sm font-medium text-gray-300">
                                    {{ __('collection.index.filter_status') }}
                                </label>
                                <select name="status" id="status"
                                    class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                    <option value="">{{ __('collection.index.all_statuses') }}</option>
                                    <option value="published"
                                        {{ request('status', 'published') == 'published' ? 'selected' : '' }}>
                                        {{ __('collection.index.status_published') }}</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                        {{ __('collection.index.status_draft') }}</option>
                                </select>
                            </div>
                        @endcan

                        {{-- Campo nascosto per il creator_id se presente --}}
                        @if (request()->filled('creator'))
                            <input type="hidden" name="creator" value="{{ request('creator') }}">
                        @endif

                        {{-- Pulsanti di Azione --}}
                        <div class="flex justify-end md:col-span-3 lg:col-span-4">
                            {{-- Modificato per mantenere il parametro 'creator' se presente --}}
                            <a href="{{ route('home.collections.index', array_filter(request()->only('creator'))) }}"
                                class="btn btn-secondary rounded-lg px-6 py-3 text-white transition-colors duration-200 hover:bg-gray-700">
                                {{ __('collection.index.reset_filters') }}
                            </a>
                            <button type="submit"
                                class="btn btn-primary ml-4 rounded-lg bg-verde-rinascita px-6 py-3 text-white transition-colors duration-200 hover:bg-verde-rinascita-dark">
                                {{ __('collection.index.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="belowHeroContent_2"> {{-- Manteniamo nello slot per consistenza --}}
        {{-- 🖼️ Griglia delle Collezioni - iPhone First con multi-colonna --}}
        <div
            class="grid grid-cols-2 gap-3 px-2 sm:gap-4 sm:px-0 md:grid-cols-3 lg:grid-cols-3 lg:gap-8 xl:grid-cols-4">
            @forelse ($collections as $collection)
                <x-home-collection-card :id="$collection->id" :editable="false" imageType="card" :collection="$collection"
                    displayType="{{ request()->is('*mobile*') ? 'avatar' : 'default' }}" />
            @empty
                {{-- 💨 Stato Vuoto: Nessuna collezione trovata --}}
                <div class="col-span-full rounded-lg bg-gray-800 px-4 py-16 text-center text-gray-400 shadow-inner">
                    <div class="mb-5 inline-block rounded-full bg-gray-700 p-5">
                        <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-white">
                        {{ __('collection.index.no_collections_found') }}</h3>
                    <p class="mx-auto mb-6 max-w-md">{{ __('collection.index.no_collections_criteria_message') }}</p>
                    @can('create_collection')
                        <button type="button" data-action="open-create-collection-modal"
                            class="inline-flex items-center rounded-md border border-transparent bg-verde-rinascita px-6 py-3 text-sm font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-verde-rinascita-dark focus:outline-none focus:ring-2 focus:ring-verde-rinascita focus:ring-offset-2">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path
                                    d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                            </svg>
                            {{ __('collection.index.create_collection_button') }}
                            </a>
                        @endcan
                </div>
            @endforelse
        </div>
    </x-slot>

    {{-- 🔢 Paginazione --}}
    <div class="mt-10 flex justify-center md:mt-16">
        {{ $collections->appends(request()->query())->links() }}
    </div>

</x-platform-layout>
