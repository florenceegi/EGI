{{-- Menu Collections Carousel Component --}}
@props([
    'collections' => []
])

<div class="p-4 border bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-purple-200/30 dark:border-purple-800/30 mega-card">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('collection.my_collections') }}</h4>
            
            <!-- Badge con numero collezioni -->
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-800 bg-purple-100 border border-purple-200 rounded-full dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700">
                {{ count($collections) }}
            </span>
        </div>

        @if(count($collections) > 1)
        <div class="flex items-center space-x-1">
            <button type="button" 
                    data-scroll-direction="left"
                    id="btn-left-main-carousel"
                    class="p-1 text-gray-400 transition-colors rounded hover:text-purple-600 dark:hover:text-purple-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button type="button" 
                    data-scroll-direction="right"
                    id="btn-right-main-carousel"
                    class="p-1 text-gray-400 transition-colors rounded hover:text-purple-600 dark:hover:text-purple-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
        @endif
    </div>

    <div class="relative menu-collections-carousel">
        <!-- Carousel without visible scrollbar but with manual scrolling -->
        <div class="pb-2 overflow-x-auto overflow-y-hidden carousel-container-menu">
            
            <!-- Flex container with proper spacing -->
            <div class="flex gap-3 w-max">
                @forelse($collections as $collection)
                    <div class="flex-shrink-0 w-80">
                        <a href="{{ route('home.collections.show', $collection->id) }}"
                           class="block p-3 transition-all duration-200 rounded-lg bg-white/50 dark:bg-black/20 hover:bg-white/70 dark:hover:bg-black/30 hover:scale-105 group">

                        <div class="flex items-center space-x-3">
                            <!-- Collection Image/Icon -->
                            <div class="flex-shrink-0">
                                @if($collection->getFirstMediaUrl('head', 'thumb'))
                                    <img src="{{ $collection->getFirstMediaUrl('head', 'thumb') }}"
                                         alt="{{ $collection->collection_name }}"
                                         class="object-cover w-16 h-16 transition-transform duration-200 rounded-lg group-hover:scale-110">
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 text-white rounded-lg bg-gradient-to-br from-purple-400 to-pink-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Collection Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <h5 class="font-medium text-gray-900 truncate transition-colors dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                        {{ $collection->collection_name }}
                                    </h5>
                                    <!-- Owner Badge -->
                                    <span class="px-2 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full dark:bg-purple-900/30 dark:text-purple-300">
                                        Owner
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m3 0H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                                            </svg>
                                            {{ $collection->egis()->count() }} {{ __('label.egis') }}
                                        </span>

                                        @php
                                            $avgPrice = $collection->egis()->whereNotNull('price')->avg('price') ?? 0;
                                            $totalValue = $collection->egis()->whereNotNull('price')->sum('price') ?? 0;
                                            $soldCount = $collection->egis()->whereHas('reservations', function($q) {
                                                $q->where('sub_status', 'highest');
                                            })->count();
                                        @endphp

                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            @if($avgPrice > 0)
                                                €{{ number_format($avgPrice, 0) }} med
                                            @else
                                                {{ __('collection.no_price') }}
                                            @endif
                                        </span>

                                        <!-- Creator info -->
                                        {{-- <span class="flex items-center text-purple-600 dark:text-purple-400">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $collection->creator->name }}
                                        </span> --}}

                                        @if($soldCount > 0)
                                            <span class="flex items-center text-green-700 dark:text-green-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $soldCount }} {{ __('collection.sold') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($collection->description)
                                    <p class="mt-1 mb-2 text-xs text-gray-600 truncate dark:text-gray-400">
                                        {{ Str::limit($collection->description, 80) }}
                                    </p>
                                @endif

                                @if($collection->type)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full mt-2 {{
                                        $collection->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                        ($collection->status === 'local' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' :
                                        'bg-gray-200 text-gray-900 dark:bg-gray-600 dark:text-gray-100')
                                    }}">
                                        {{ __('collection.type_' . $collection->type) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="w-full py-4 text-center">
                    <div class="mb-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ __('collection.no_collections') }}
                    </div>
                    <button type="button"
                            data-action="open-create-collection-modal"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-600 transition-colors duration-200 bg-purple-100 rounded-lg hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50"
                            aria-label="{{ __('collection.create_collection') }}">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('collection.create_collection') }}
                    </button>
                </div>
            @endforelse
            </div>
        </div>
    </div>
</div>

<style>
/* Hide scrollbar but keep functionality for menu carousel */
.carousel-container-menu {
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
}

.carousel-container-menu::-webkit-scrollbar { 
    display: none;  /* Safari and Chrome */
}

.carousel-container-menu {
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel-container-menu');
    
    if (!carousel) {
        console.log('Main carousel container not found');
        return;
    }
    
    // Handle scroll buttons - check if they exist first
    const leftButton = document.getElementById('btn-left-main-carousel');
    const rightButton = document.getElementById('btn-right-main-carousel');
    
    console.log('Main carousel buttons found:', {
        left: !!leftButton,
        right: !!rightButton,
        collectionsCount: {{ count($collections) }}
    });
    
    // Only attach button events if buttons exist
    if (leftButton && rightButton) {
        console.log('Attaching events to main carousel buttons');
        
        // Left button events
        leftButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Main carousel left button clicked');
            carousel.scrollBy({left: -320, behavior: 'smooth'});
        });
        
        leftButton.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Main carousel left button touched');
            carousel.scrollBy({left: -320, behavior: 'smooth'});
        });
        
        leftButton.addEventListener('touchstart', function(e) {
            e.stopPropagation();
        });
        
        leftButton.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        });
        
        // Right button events
        rightButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Main carousel right button clicked');
            carousel.scrollBy({left: 320, behavior: 'smooth'});
        });
        
        rightButton.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Main carousel right button touched');
            carousel.scrollBy({left: 320, behavior: 'smooth'});
        });
        
        rightButton.addEventListener('touchstart', function(e) {
            e.stopPropagation();
        });
        
        rightButton.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        });
    } else {
        console.log('Main carousel buttons not found - skipping button events');
    }
    
    // Mouse wheel scrolling - convert vertical to horizontal
    carousel.addEventListener('wheel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.scrollLeft += e.deltaY;
    }, { passive: false });
    
    // Touch/drag scrolling
    let isDown = false;
    let startX;
    let scrollLeft;
    
    // Mouse events
    carousel.addEventListener('mousedown', function(e) {
        isDown = true;
        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
        carousel.style.cursor = 'grabbing';
        e.preventDefault();
    });
    
    carousel.addEventListener('mouseleave', function() {
        isDown = false;
        carousel.style.cursor = 'grab';
    });
    
    carousel.addEventListener('mouseup', function() {
        isDown = false;
        carousel.style.cursor = 'grab';
    });
    
    carousel.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2;
        carousel.scrollLeft = scrollLeft - walk;
    });
    
    // Touch events for mobile
    let touchStartX = 0;
    let touchScrollLeft = 0;
    
    carousel.addEventListener('touchstart', function(e) {
        // Ignore touch on buttons if they exist
        if (leftButton && e.target.closest('#btn-left-main-carousel')) return;
        if (rightButton && e.target.closest('#btn-right-main-carousel')) return;
        
        touchStartX = e.touches[0].pageX - carousel.offsetLeft;
        touchScrollLeft = carousel.scrollLeft;
    });
    
    carousel.addEventListener('touchmove', function(e) {
        // Ignore touch on buttons if they exist
        if (leftButton && e.target.closest('#btn-left-main-carousel')) return;
        if (rightButton && e.target.closest('#btn-right-main-carousel')) return;
        
        if (!touchStartX) return;
        e.preventDefault();
        const x = e.touches[0].pageX - carousel.offsetLeft;
        const walk = (x - touchStartX) * 2;
        carousel.scrollLeft = touchScrollLeft - walk;
    }, { passive: false });
    
    carousel.addEventListener('touchend', function() {
        touchStartX = 0;
    });
    
    // Set cursor
    carousel.style.cursor = 'grab';
});
</script>
