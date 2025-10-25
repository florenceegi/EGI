{{-- resources/views/components/desktop-egi-carousel.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Desktop EGI Carousel Only)
* @date 2025-01-18
* @purpose Desktop-only EGI carousel with navigation
--}}

@props([
    'egis' => collect(),
    'id' => null,
])

@php
    // Generate unique ID for multiple carousels on same page
    $carouselId = $id ?? 'carousel-' . uniqid();
@endphp

@if ($egis->count() > 0)
    <section class="hidden py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black lg:block">
        <div class="px-4 mx-auto sm:px-6 lg:px-8">

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
                <button id="prev-btn-{{ $carouselId }}"
                    class="absolute left-0 z-10 flex items-center justify-center w-10 h-10 text-white transition-all duration-300 -translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg group top-1/2 hover:border-gray-400 hover:bg-gray-700"
                    aria-label="{{ __('egi.desktop_carousel.navigation.previous') }}">
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="next-btn-{{ $carouselId }}"
                    class="absolute right-0 z-10 flex items-center justify-center w-10 h-10 text-white transition-all duration-300 translate-x-4 -translate-y-1/2 bg-gray-800 border border-gray-600 rounded-full shadow-lg group top-1/2 hover:border-gray-400 hover:bg-gray-700"
                    aria-label="{{ __('egi.desktop_carousel.navigation.next') }}">
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Carousel Track --}}
                <div class="flex pb-4 space-x-4 overflow-x-auto snap-x snap-mandatory scroll-smooth"
                    id="track-{{ $carouselId }}">
                    @foreach ($egis as $egi)
                        <div class="flex-shrink-0 snap-start" style="width: 280px;">
                            <x-egi-card :egi="$egi" :showPurchasePrice="true" />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Indicators --}}
            <div class="flex justify-center mt-6 space-x-2">
                @for ($i = 0; $i < min(5, ceil($egis->count() / 4)); $i++)
                    <button
                        class="carousel-indicator-{{ $carouselId }} h-2 w-2 rounded-full bg-gray-600 transition-colors duration-300 hover:bg-purple-500"
                        data-slide="{{ $i }}"
                        aria-label="{{ __('egi.desktop_carousel.navigation.slide', ['number' => $i + 1]) }}">
                    </button>
                @endfor
            </div>
        </div>
    </section>

    {{-- Desktop Carousel JavaScript --}}
    <script>
        (function() {
            const carouselId = '{{ $carouselId }}';
            console.log('🎠 Desktop Carousel Init:', carouselId);

            const carousel = document.getElementById('track-' + carouselId);
            const prevBtn = document.getElementById('prev-btn-' + carouselId);
            const nextBtn = document.getElementById('next-btn-' + carouselId);
            const indicators = document.querySelectorAll('.carousel-indicator-' + carouselId);

            console.log('🎠 Elements found:', {
                carousel: !!carousel,
                prevBtn: !!prevBtn,
                nextBtn: !!nextBtn,
                indicators: indicators.length
            });

            if (carousel && prevBtn && nextBtn) {
                console.log('🎠 Carousel dimensions:', {
                    scrollWidth: carousel.scrollWidth,
                    clientWidth: carousel.clientWidth,
                    scrollLeft: carousel.scrollLeft,
                    maxScroll: carousel.scrollWidth - carousel.clientWidth
                });

                const cardWidth = 296; // 280px + 16px gap
                let isScrolling = false;

                // Update UI based on current scroll position
                function updateUI() {
                    const currentScroll = carousel.scrollLeft;
                    const maxScroll = carousel.scrollWidth - carousel.clientWidth;

                    // Update indicators
                    const currentSlide = Math.round(currentScroll / cardWidth);
                    indicators.forEach((indicator, index) => {
                        if (index === currentSlide) {
                            indicator.classList.add('bg-purple-500', 'scale-125');
                            indicator.classList.remove('bg-gray-600');
                        } else {
                            indicator.classList.remove('bg-purple-500', 'scale-125');
                            indicator.classList.add('bg-gray-600');
                        }
                    });

                    // Update button states
                    prevBtn.style.opacity = currentScroll > 10 ? '1' : '0.5';
                    prevBtn.style.cursor = currentScroll > 10 ? 'pointer' : 'not-allowed';
                    nextBtn.style.opacity = currentScroll < maxScroll - 10 ? '1' : '0.5';
                    nextBtn.style.cursor = currentScroll < maxScroll - 10 ? 'pointer' : 'not-allowed';
                }

                // Listen to scroll events (for mouse/touch scrolling)
                carousel.addEventListener('scroll', function() {
                    console.log('🎠 Scroll event:', carousel.scrollLeft);
                    if (!isScrolling) {
                        updateUI();
                    }
                });

                // Previous button
                prevBtn.addEventListener('click', function(e) {
                    console.log('🎠 Prev button clicked');
                    e.preventDefault();

                    const currentScroll = carousel.scrollLeft;
                    console.log('🎠 Current scroll:', currentScroll);

                    if (isScrolling || currentScroll <= 10) {
                        console.log('🎠 Blocked:', {
                            isScrolling,
                            atStart: currentScroll <= 10
                        });
                        return;
                    }

                    isScrolling = true;
                    const newPosition = Math.max(0, currentScroll - cardWidth);
                    console.log('🎠 Scrolling to:', newPosition);

                    carousel.scrollTo({
                        left: newPosition,
                        behavior: 'smooth'
                    });

                    setTimeout(() => {
                        isScrolling = false;
                        updateUI();
                        console.log('🎠 Scroll complete, new position:', carousel.scrollLeft);
                    }, 400);
                });

                // Next button
                nextBtn.addEventListener('click', function(e) {
                    console.log('🎠 Next button clicked');
                    e.preventDefault();

                    const currentScroll = carousel.scrollLeft;
                    const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                    console.log('🎠 Current scroll:', currentScroll, 'Max:', maxScroll);

                    if (isScrolling || currentScroll >= maxScroll - 10) {
                        console.log('🎠 Blocked:', {
                            isScrolling,
                            atEnd: currentScroll >= maxScroll - 10
                        });
                        return;
                    }

                    isScrolling = true;
                    const newPosition = Math.min(maxScroll, currentScroll + cardWidth);
                    console.log('🎠 Scrolling to:', newPosition);

                    carousel.scrollTo({
                        left: newPosition,
                        behavior: 'smooth'
                    });

                    setTimeout(() => {
                        isScrolling = false;
                        updateUI();
                        console.log('🎠 Scroll complete, new position:', carousel.scrollLeft);
                    }, 400);
                });

                // Indicator clicks
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isScrolling) return;

                        isScrolling = true;
                        const targetPosition = index * cardWidth;
                        carousel.scrollTo({
                            left: targetPosition,
                            behavior: 'smooth'
                        });

                        setTimeout(() => {
                            isScrolling = false;
                            updateUI();
                        }, 400);
                    });
                });

                // Initialize
                updateUI();

                // Handle window resize
                window.addEventListener('resize', function() {
                    updateUI();
                });
            }
        })();
    </script>

    {{-- Custom Styles --}}
    <style>
        #track-{{ $carouselId }} {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
            scroll-behavior: smooth;
            cursor: grab;
        }

        #track-{{ $carouselId }}::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        #track-{{ $carouselId }}:active {
            cursor: grabbing;
        }

        .carousel-indicator-{{ $carouselId }} {
            transition: all 0.3s ease;
        }

        .carousel-indicator-{{ $carouselId }}:hover {
            transform: scale(1.3);
        }

        /* Smooth scroll for desktop */
        @media (prefers-reduced-motion: no-preference) {
            #desktop-egi-carousel-track {
                scroll-behavior: smooth;
            }
        }
    </style>
@endif
