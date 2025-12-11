{{-- Collector Collections Content --}}
@php
    $currentCollections = $collectorCollections ?? collect();
@endphp

<div class="min-h-screen bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Collector Summary --}}
        <div class="mb-8 grid grid-cols-1 gap-4 text-center sm:grid-cols-3">
            <div>
                <span class="text-oro-fiorentino block text-xl font-bold">{{ $stats['total_owned_egis'] ?? 0 }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.home.owned_egis') }}</span>
            </div>
            <div>
                <span
                    class="text-oro-fiorentino block text-xl font-bold">{{ $stats['collections_represented'] ?? 0 }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.collections_represented') }}</span>
            </div>
            <div>
                <span
                    class="text-oro-fiorentino block text-xl font-bold">€{{ number_format($stats['total_spent_eur'] ?? 0, 2) }}</span>
                <span class="text-sm text-gray-300">{{ __('collector.home.total_spent') }}</span>
            </div>
        </div>

        {{-- Controls --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div
                class="inline-flex items-center gap-2 rounded-full border border-purple-500/30 bg-purple-500/10 px-4 py-2 text-sm font-semibold text-purple-200">
                <span class="flex h-2 w-2 rounded-full bg-purple-400"></span>
                {{ __('collector.collections.badge_label') }}
            </div>
        </div>

        {{-- Collections Grid --}}
        @if ($currentCollections->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($currentCollections as $collection)
                    <article
                        class="hover:border-oro-fiorentino/60 overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 transition-all duration-300 hover:shadow-2xl">
                        {{-- Collection Cover --}}
                        @if ($collection->cover_image)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ $collection->cover_image }}" alt="{{ $collection->title }}"
                                    class="h-full w-full object-cover transition-transform duration-300 hover:scale-105">
                            </div>
                        @else
                            <div
                                class="flex aspect-video items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                <svg class="h-16 w-16 text-gray-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="mb-4 flex items-center">
                                @if ($collection->creator && $collection->creator->profile_photo_url)
                                    <img src="{{ $collection->creator->profile_photo_url }}"
                                        alt="{{ $collection->creator->name }}"
                                        class="mr-4 h-12 w-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="from-oro-fiorentino mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br to-verde-rinascita">
                                        <span
                                            class="text-lg font-bold text-white">{{ substr($collection->creator->name ?? 'C', 0, 1) }}</span>
                                    </div>
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
                                            <a href="{{ route('creator.home', $collection->creator->id) }}"
                                                class="hover:text-oro-fiorentino font-medium text-gray-200 transition-colors">
                                                {{ $collection->creator->name }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if ($collection->description)
                                <p class="mb-4 line-clamp-2 text-sm text-gray-400">
                                    {{ $collection->description }}
                                </p>
                            @endif

                            <div
                                class="flex items-center justify-between border-t border-gray-700 pt-4 text-sm text-gray-300">
                                <span class="flex items-center gap-2">
                                    <svg class="text-oro-fiorentino h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                                        <path fill-rule="evenodd"
                                            d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $collection->egis_count ?? 0 }} {{ __('collector.home.owned_in_collection') }}
                                </span>
                                @if ($collection->total_value)
                                    <span class="text-oro-fiorentino font-medium">
                                        €{{ number_format($collection->total_value, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-12 text-center">
                <svg class="mx-auto mb-6 h-16 w-16 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-2 text-2xl font-bold text-white">
                    {{ __('collector.collections.empty_title') }}
                </h3>
                <p class="mb-6 text-gray-400">
                    {{ __('collector.collections.empty_description') }}
                </p>
                <a href="{{ route('egis.index') }}"
                    class="inline-flex items-center rounded-full bg-purple-600 px-6 py-3 font-semibold text-white transition-colors duration-200 hover:bg-purple-700">
                    {{ __('collector.collections.explore_button') }}
                </a>
            </div>
        @endif
    </div>
</div>
