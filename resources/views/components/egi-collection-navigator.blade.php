{{-- resources/views/components/egi-collection-navigator.blade.php --}}
{{-- 🎯 OpenSea-style EGI Collection Navigator - Images Only --}}
{{-- Horizontal carousel with thumbnails for navigating between EGIs in the same collection --}}

@props([
    'collectionEgis' => collect(),
    'currentEgi' => null,
])

@if ($collectionEgis->count() > 1)
    <div class="w-full border-b border-white/10 bg-white/5 backdrop-blur-sm">
        <div class="relative px-2 py-2 sm:px-3 md:px-4 md:py-3">
            <!-- Navigation Title (Screen readers only) -->
            <h2 class="sr-only">{{ __('label.collection_navigation.navigate_collection') }}</h2>

            <!-- Carousel Container - Full Width -->
            <div class="relative w-full">
                <!-- Scrollable Container - Responsive gap e padding -->
                <div id="carousel-track"
                    class="scrollbar-hide flex gap-1.5 overflow-x-auto scroll-smooth py-1 sm:gap-2 md:gap-3"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach ($collectionEgis as $egi)
                        <a href="{{ route('egis.show', $egi->id) }}"
                            class="{{ $currentEgi && $currentEgi->id === $egi->id ? 'ring-2 ring-blue-500 scale-105' : 'hover:ring-2 hover:ring-white/50' }} group carousel-item relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-md transition-all duration-200 hover:scale-105 hover:shadow-lg sm:h-12 sm:w-12 md:h-14 md:w-14 md:rounded-lg lg:h-16 lg:w-16 xl:h-20 xl:w-20"
                            aria-label="Visualizza EGI {{ $egi->name ?? '#' . $egi->id }}">
                            @if ($egi->main_image_url)
                                <img src="{{ $egi->main_image_url }}" alt="EGI {{ $egi->name ?? '#' . $egi->id }}"
                                    class="{{ $currentEgi && $currentEgi->id === $egi->id ? 'opacity-100' : 'opacity-80 group-hover:opacity-100' }} h-full w-full object-cover transition-opacity duration-200"
                                    loading="lazy">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                                    <svg class="h-4 w-4 text-gray-400 sm:h-5 sm:w-5 md:h-6 md:w-6" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Current item indicator - Responsive size -->
                            @if ($currentEgi && $currentEgi->id === $egi->id)
                                <div class="absolute inset-0 flex items-center justify-center bg-blue-500/20">
                                    <div class="h-1.5 w-1.5 rounded-full bg-blue-500 sm:h-2 sm:w-2 md:h-2.5 md:w-2.5">
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('carousel-track');

            if (!track) return;

            // Auto-scroll to current item on load
            const currentItem = track.querySelector('.ring-2.ring-blue-500');
            if (currentItem) {
                setTimeout(() => {
                    currentItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }, 100);
            }
        });
    </script>
@endif
