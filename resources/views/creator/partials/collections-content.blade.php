{{-- Collections Content - Partial View --}}
<div class="min-h-screen bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="mb-4 text-3xl font-bold text-white">{{ __('creator.collections.title') }}</h2>
            <p class="text-lg text-gray-400">{{ __('creator.collections.subtitle') }}</p>
        </div>

        {{-- Stats - Compact horizontal on mobile --}}
        <div class="mb-6 flex items-center justify-center gap-4 rounded-xl border border-gray-700/50 bg-gray-900/40 px-4 py-3 text-center md:mb-8 md:gap-8 md:px-6 md:py-4">
            <div>
                <span class="text-oro-fiorentino block text-lg font-bold md:text-2xl">{{ $stats['total_collections'] ?? 0 }}</span>
                <span class="text-[10px] text-gray-300 md:text-sm">{{ __('creator.collections.total_collections') }}</span>
            </div>
            <div class="h-10 w-px bg-gray-700"></div>
            <div>
                <span class="text-oro-fiorentino block text-lg font-bold md:text-2xl">{{ $stats['total_egis'] ?? 0 }}</span>
                <span class="text-[10px] text-gray-300 md:text-sm">{{ __('creator.collections.total_artworks') }}</span>
            </div>
            <div class="h-10 w-px bg-gray-700"></div>
            <div>
                <span class="text-oro-fiorentino block text-lg font-bold md:text-2xl">{{ $stats['total_supporters'] ?? 0 }}</span>
                <span class="text-[10px] text-gray-300 md:text-sm">{{ __('creator.collections.total_supporters') }}</span>
            </div>
        </div>

        @if ($collections && $collections->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($collections as $collection)
                    <x-home-collection-card :id="$collection->id" :collection="$collection" :editable="false" :show_save_button="false" />
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="mx-auto mb-6 h-24 w-24 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mb-4 text-2xl font-bold text-white">{{ __('creator.collections.empty_title') }}</h3>
                <p class="text-gray-400">{{ __('creator.collections.empty_description') }}</p>
            </div>
        @endif

    </div>
</div>
