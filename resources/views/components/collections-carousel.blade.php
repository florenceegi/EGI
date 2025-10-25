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
    'marginClass' => 'mb-12'
])

{{-- Debug temporaneo: verifica che i dati arrivino correttamente --}}
@if(config('app.debug'))
    {{-- <div class="p-2 text-xs text-gray-500">DEBUG: {{ count($collections) }} collezioni caricate</div> --}}
@endif

{{-- Carousel Collezioni in Evidenza --}}

<div class="w-full py-8 {{ $marginClass }} {{ $bgClass }} md:py-10 lg:py-12">
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
        @if($title)
            <h2 class="mb-6 text-2xl font-bold text-white md:text-3xl lg:text-4xl {{ $titleClass }} font-display">
                {{ $title }}
            </h2>
        @endif

        <div class="relative overflow-hidden featured-collections-carousel">
            <div class="flex gap-4 pb-4 overflow-x-auto md:gap-6 snap-x snap-mandatory scrollbar-hide">
                @forelse($collections as $index => $collection)
                    {{-- VERSIONE DESKTOP: Card completa --}}
                    <div class="flex-shrink-0 hidden w-72 md:w-80 lg:w-96 snap-start md:block collection-card-desktop" data-collection-id="{{ $collection->id ?? $index }}">
                        <div class="h-full group">
                            <x-home-collection-card 
                                :collection="$collection" 
                                imageType="card" 
                                displayType="default" 
                            />
                        </div>
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-gray-400">
                        {{ __('guest_home.no_collections_available') }}
                    </div>
                @endforelse
            </div>

            {{-- Controlli Carousel (solo se ci sono collezioni multiple) --}}
            @if(count($collections) > 1)
                <button type="button" 
                        class="absolute left-0 z-10 items-center justify-center hidden w-10 h-10 -ml-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-prev" 
                        aria-label="{{ __('guest_home.previous_collections') }}">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button" 
                        class="absolute right-0 z-10 items-center justify-center hidden w-10 h-10 -mr-5 text-white transition-all transform -translate-y-1/2 bg-black rounded-full opacity-70 md:flex hover:opacity-100 top-1/2 carousel-next" 
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
                    
                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    
                    scrollTimeout = setTimeout(() => {
                        scrollTimeout = null;
                    }, 300);
                });

                nextButton.addEventListener('click', () => {
                    if (scrollTimeout) return;
                    
                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    
                    scrollTimeout = setTimeout(() => {
                        scrollTimeout = null;
                    }, 300);
                });
            });
        });
    </script>
    @endpush
@endonce