{{-- resources/views/components/creators-carousel.blade.php --}}
@props([
    'creators' => [],
    'title' => __('guest_home.featured_creators_title'),
    'titleClass' => '',
    'bgClass' => 'bg-gray-900',
    'marginClass' => 'mb-12',
])

<div class="{{ $marginClass }} {{ $bgClass }} w-full py-8 md:py-10 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @if ($title)
            <h2 class="{{ $titleClass }} mb-6 font-display text-2xl font-bold text-white md:text-3xl lg:text-4xl">
                {{ $title }}
            </h2>
        @endif

        <div class="featured-creators-carousel relative overflow-hidden">
            <div class="scrollbar-hide flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 md:gap-6">
                @forelse($creators as $index => $creator)
                    {{-- NFT-Style Creator Card (uguale per mobile e desktop) --}}
                    <div class="creator-card w-48 flex-shrink-0 snap-start md:w-56 lg:w-64"
                        data-creator-id="{{ $creator->id ?? $index }}">
                        <x-creator-card :creator="$creator" imageType="card" displayType="nft-style" />
                    </div>
                @empty
                    <div class="w-full py-8 text-center text-gray-400">
                        {{ __('guest_home.no_creators_available') }}
                    </div>
                @endforelse
            </div>

            {{-- Controlli Carousel (solo se ci sono creator multipli) --}}
            @if (count($creators) > 1)
                <button type="button"
                    class="carousel-prev absolute left-0 top-1/2 z-10 -ml-5 hidden h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full bg-black text-white opacity-70 transition-all hover:opacity-100 md:flex"
                    aria-label="{{ __('guest_home.previous_creators') }}">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button type="button"
                    class="carousel-next absolute right-0 top-1/2 z-10 -mr-5 hidden h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full bg-black text-white opacity-70 transition-all hover:opacity-100 md:flex"
                    aria-label="{{ __('guest_home.next_creators') }}">
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
                const carousels = document.querySelectorAll('.featured-creators-carousel'); // Selettore aggiornato

                carousels.forEach(carousel => {
                    const container = carousel.querySelector('.snap-x');
                    const prevButton = carousel.querySelector('.carousel-prev');
                    const nextButton = carousel.querySelector('.carousel-next');

                    if (!container || !prevButton || !nextButton) {
                        console.warn('FlorenceEGI: Carousel elements for Creators missing');
                        return;
                    }

                    // Calcolo dinamico scroll amount basato su viewport
                    function getScrollAmount() {
                        const isMobile = window.innerWidth < 768;
                        if (isMobile) {
                            // Mobile: w-48 (192px) + gap-4 (16px)
                            return 208;
                        } else {
                            // Desktop: calcola basandosi sulla prima card visibile
                            const firstCard = container.querySelector('.creator-card');
                            if (firstCard) {
                                return firstCard.offsetWidth + 24; // + gap-6 (24px)
                            }
                            return 280; // Fallback
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
