{{-- resources/views/components/collections-carousel.blade.php --}}
{{--
    Fixed carousel component with Oracode System 2.0 principles
    
    ISSUE: Text accumulation on first card
    FIX: Proper component scoping, unique keys, CSS isolation
    
    Props:
    - collections: Collection[] - The collections to display
    - title: string - The title for the carousel section (optional)
    - titleClass: string - Additional classes for the title (optional)
    - bgClass: string - Background class for the container (optional)
    - marginClass: string - Margin class for the container (optional)
--}}

@props([
    'collections' => [],
    'title' => __('guest_home.featured_collections_title'),
    'titleClass' => '',
    'bgClass' => 'bg-gray-900',
    'marginClass' => 'mb-12',
])

{{-- Debug temporaneo: verifica che i dati arrivino correttamente --}}
@if (config('app.debug'))
    {{-- <div class="p-2 text-xs text-gray-500">DEBUG: {{ count($collections) }} collezioni caricate</div> --}}
@endif

{{-- Carousel Collezioni in Evidenza --}}

<div class="{{ $marginClass }} {{ $bgClass }} w-full py-8 md:py-10 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @if ($title)
            <h2 class="{{ $titleClass }} mb-6 font-display text-2xl font-bold text-white md:text-3xl lg:text-4xl">
                {{ $title }}
            </h2>
        @endif

        <div class="featured-collections-carousel relative overflow-hidden">
            <div class="scrollbar-hide flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 md:gap-6">
                @forelse($collections as $index => $collection)
                    {{-- VERSIONE DESKTOP: Card completa --}}
                    <div class="collection-card-desktop hidden w-72 flex-shrink-0 snap-start md:block md:w-80 lg:w-96"
                        data-collection-id="{{ $collection->id ?? $index }}">
                        <div class="group h-full">
                            <x-home-collection-card :collection="$collection" imageType="card" displayType="default" />
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-gray-400">
                        {{ __('guest_home.no_collections_available') }}
                    </div>
                @endforelse
            </div>

            {{-- Controlli Carousel (solo se ci sono collezioni multiple) --}}
            @if (count($collections) > 1)
                <button type="button"
                    class="carousel-prev absolute left-0 top-1/2 z-10 -ml-5 hidden h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full bg-black text-white opacity-70 transition-all hover:opacity-100 md:flex"
                    aria-label="{{ __('guest_home.previous_collections') }}">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button"
                    class="carousel-next absolute right-0 top-1/2 z-10 -mr-5 hidden h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full bg-black text-white opacity-70 transition-all hover:opacity-100 md:flex"
                    aria-label="{{ __('guest_home.next_collections') }}">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Script carousel con sicurezza proattiva --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const carousels = document.querySelectorAll('.featured-collections-carousel');

                carousels.forEach(carousel => {
                    const container = carousel.querySelector('.snap-x');
                    const prevButton = carousel.querySelector('.carousel-prev');
                    const nextButton = carousel.querySelector('.carousel-next');

                    if (!container || !prevButton || !nextButton) {
                        console.warn('FlorenceEGI: Carousel elements missing');
                        return;
                    }

                    // Calcolo dinamico scroll amount basato su viewport
                    function getScrollAmount() {
                        const isMobile = window.innerWidth < 768;
                        if (isMobile) {
                            // Mobile: w-32 (128px) + gap-4 (16px)
                            return 144;
                        } else {
                            // Desktop: calcola basandosi sulla prima card visibile
                            const firstCard = container.querySelector('.collection-card-desktop');
                            if (firstCard) {
                                return firstCard.offsetWidth + 24; // + gap-6 (24px)
                            }
                            return 320; // Fallback
                        }
                    }

                    let scrollAmount = getScrollAmount();

                    // Aggiorna scroll amount su resize
                    window.addEventListener('resize', function() {
                        scrollAmount = getScrollAmount();
                    });

                    // Event listeners con throttling per performance
                    let scrollTimeout;

                    prevButton.addEventListener('click', () => {
                        if (scrollTimeout) return;

                        container.scrollBy({
                            left: -scrollAmount,
                            behavior: 'smooth'
                        });

                        scrollTimeout = setTimeout(() => {
                            scrollTimeout = null;
                        }, 300);
                    });

                    nextButton.addEventListener('click', () => {
                        if (scrollTimeout) return;

                        container.scrollBy({
                            left: scrollAmount,
                            behavior: 'smooth'
                        });

                        scrollTimeout = setTimeout(() => {
                            scrollTimeout = null;
                        }, 300);
                    });
                });
            });
        </script>
    @endpush
@endonce
