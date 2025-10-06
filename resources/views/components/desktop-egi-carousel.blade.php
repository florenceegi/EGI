{{-- resources/views/components/desktop-egi-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Desktop EGI Carousel Only)
* @date 2025-01-18
* @purpose Desktop-only EGI carousel with navigation
--}}

@props([
'egis' => collect()
])

@if($egis->count() > 0)
<section class="hidden py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black lg:block">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h2 class="mb-3 text-2xl font-bold text-white md:text-3xl">
                🎨 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                    {{ __('egi.desktop_carousel.title') }}
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-300">
                {{ __('egi.desktop_carousel.subtitle') }}
            </p>
        </div>

        {{-- Carousel Container --}}
        <div class="relative">
            {{-- Navigation Buttons --}}
            <button id="desktop-prev-btn"
                class="absolute left-0 z-10 flex items-center justify-center w-10 h-10 text-white transition-all duration-300 -translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg top-1/2 hover:bg-gray-700 group hover:border-gray-400"
                aria-label="{{ __('egi.desktop_carousel.navigation.previous') }}">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button id="desktop-next-btn"
                class="absolute right-0 z-10 flex items-center justify-center w-10 h-10 text-white transition-all duration-300 translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg top-1/2 hover:bg-gray-700 group hover:border-gray-400"
                aria-label="{{ __('egi.desktop_carousel.navigation.next') }}">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Carousel Track --}}
            <div class="flex pb-4 space-x-4 overflow-x-auto scrollbar-hide" id="desktop-egi-carousel-track">
                @foreach($egis as $egi)
                <div class="flex-shrink-0" style="width: 280px;">
                    <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                </div>
                @endforeach
            </div>
        </div>

        {{-- Indicators --}}
        <div class="flex justify-center mt-6 space-x-2">
            @for($i = 0; $i < min(5, ceil($egis->count() / 4)); $i++)
                <button
                    class="w-2 h-2 transition-colors duration-300 bg-gray-600 rounded-full hover:bg-purple-500 desktop-carousel-indicator"
                    data-slide="{{ $i }}"
                    aria-label="{{ __('egi.desktop_carousel.navigation.slide', ['number' => $i + 1]) }}">
                </button>
                @endfor
        </div>
    </div>
</section>

{{-- Desktop Carousel JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('desktop-egi-carousel-track');
    const prevBtn = document.getElementById('desktop-prev-btn');
    const nextBtn = document.getElementById('desktop-next-btn');
    const indicators = document.querySelectorAll('.desktop-carousel-indicator');

    if (carousel && prevBtn && nextBtn) {
        const cardWidth = 296; // 280px + 16px gap
        let currentPosition = 0;
        let isScrolling = false;

        function updateCarousel() {
            carousel.scrollTo({
                left: currentPosition,
                behavior: 'smooth'
            });

            // Update indicators
            const totalCards = {{ $egis->count() }};
            const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
            const currentSlide = Math.floor(currentPosition / cardWidth);

            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('bg-purple-500', index === currentSlide);
                indicator.classList.toggle('bg-gray-600', index !== currentSlide);
            });

            // Update button states
            const maxScroll = Math.max(0, (totalCards - visibleCards) * cardWidth);
            prevBtn.style.opacity = currentPosition > 0 ? '1' : '0.5';
            nextBtn.style.opacity = currentPosition < maxScroll ? '1' : '0.5';
        }

        prevBtn.addEventListener('click', function() {
            if (isScrolling) return;
            isScrolling = true;
            currentPosition = Math.max(0, currentPosition - cardWidth);
            updateCarousel();
            setTimeout(() => isScrolling = false, 300);
        });

        nextBtn.addEventListener('click', function() {
            if (isScrolling) return;
            isScrolling = true;
            const totalCards = {{ $egis->count() }};
            const visibleCards = Math.floor(carousel.offsetWidth / cardWidth);
            const maxScroll = Math.max(0, (totalCards - visibleCards) * cardWidth);
            currentPosition = Math.min(maxScroll, currentPosition + cardWidth);
            updateCarousel();
            setTimeout(() => isScrolling = false, 300);
        });

        // Indicator clicks
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function() {
                if (isScrolling) return;
                isScrolling = true;
                currentPosition = index * cardWidth;
                updateCarousel();
                setTimeout(() => isScrolling = false, 300);
            });
        });

        // Initialize
        updateCarousel();

        // Handle window resize
        window.addEventListener('resize', function() {
            updateCarousel();
        });
    }
});
</script>

{{-- Custom Styles --}}
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .desktop-carousel-indicator {
        transition: all 0.3s ease;
    }

    .desktop-carousel-indicator:hover {
        transform: scale(1.2);
    }
</style>
@endif
