{{-- resources/views/components/egi-collection-navigator.blade.php --}}
{{-- 🎯 OpenSea-style EGI Collection Navigator - Images Only --}}
{{-- Horizontal carousel with thumbnails for navigating between EGIs in the same collection --}}

@props([
    'collectionEgis' => collect(),
    'currentEgi' => null,
])

@if ($collectionEgis->count() > 1)
    <div class="w-full border-b border-white/10 bg-white/5 backdrop-blur-sm">
        <div class="relative px-2 py-1.5 sm:px-2 md:px-3 md:py-2">
            <!-- Navigation Title (Screen readers only) -->
            <h2 class="sr-only">{{ __('label.collection_navigation.navigate_collection') }}</h2>

            <!-- Carousel Container - Full Width -->
            <div class="group relative w-full">
                <!-- Bottone Prev -->
                <button id="carousel-prev-btn"
                    class="absolute left-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-1 text-white opacity-0 transition-all hover:bg-black/70 disabled:opacity-30 group-hover:opacity-100 sm:p-1.5 md:p-2">
                    <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Bottone Next -->
                <button id="carousel-next-btn"
                    class="absolute right-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-1 text-white opacity-0 transition-all hover:bg-black/70 disabled:opacity-30 group-hover:opacity-100 sm:p-1.5 md:p-2">
                    <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Scrollable Container - Responsive gap e padding -->
                <div id="carousel-track"
                    class="scrollbar-hide flex gap-1 overflow-x-auto scroll-smooth py-0.5 sm:gap-1.5 md:gap-2 lg:gap-2.5"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach ($collectionEgis as $egi)
                        <a href="{{ route('egis.show', $egi->id) }}"
                            class="{{ $currentEgi && $currentEgi->id === $egi->id ? 'ring-2 ring-blue-500 scale-105' : 'hover:ring-2 hover:ring-white/50' }} group carousel-item relative h-9 w-9 flex-shrink-0 overflow-hidden rounded transition-all duration-200 hover:scale-105 hover:shadow-lg sm:h-10 sm:w-10 sm:rounded-md md:h-12 md:w-12 lg:h-14 lg:w-14 lg:rounded-md xl:h-16 xl:w-16"
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
            const prevBtn = document.getElementById('carousel-prev-btn');
            const nextBtn = document.getElementById('carousel-next-btn');

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

            // Funzione per aggiornare stato bottoni
            function updateButtons() {
                if (!prevBtn || !nextBtn) return;

                const scrollLeft = track.scrollLeft;
                const maxScroll = track.scrollWidth - track.clientWidth;

                prevBtn.disabled = scrollLeft <= 0;
                nextBtn.disabled = scrollLeft >= maxScroll - 1;
            }

            // Scroll con bottoni
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    const scrollAmount = track.clientWidth * 0.8;
                    track.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const scrollAmount = track.clientWidth * 0.8;
                    track.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });
            }

            // Update buttons on scroll
            track.addEventListener('scroll', updateButtons);
            updateButtons();

            // Touch/Swipe support
            let isDown = false;
            let startX;
            let scrollLeftStart;

            track.addEventListener('mousedown', (e) => {
                isDown = true;
                track.classList.add('cursor-grabbing');
                startX = e.pageX - track.offsetLeft;
                scrollLeftStart = track.scrollLeft;
            });

            track.addEventListener('mouseleave', () => {
                isDown = false;
                track.classList.remove('cursor-grabbing');
            });

            track.addEventListener('mouseup', () => {
                isDown = false;
                track.classList.remove('cursor-grabbing');
            });

            track.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 2;
                track.scrollLeft = scrollLeftStart - walk;
            });
        });
    </script>
@endif
