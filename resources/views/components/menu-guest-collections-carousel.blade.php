{{-- Menu Guest Collections Carousel Component - Collections where user is collaborator/guest --}}
@props([
    'collections' => []
])

@php
    // Se non sono passate collezioni, recuperale dalle collaborazioni dell'utente autenticato
    if (is_null($collections)) {
        $collections = Auth::check() 
            ? Auth::user()->collaborations()->get() 
            : collect();
    }
@endphp

{{-- Mostra il componente solo se ci sono collezioni condivise --}}
@if($collections && count($collections) > 0)

    <div class="p-4 border bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl border-emerald-200/30 dark:border-emerald-800/30 mega-card">
        
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('collection.shared_collections') }}</h4>
                
                <!-- Badge con numero collezioni -->
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium border rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700">
                    {{ count($collections) }}
                </span>
            </div>
            
            @if(count($collections) > 1)
            <div class="flex items-center space-x-1">
                <button type="button" 
                        onclick="document.querySelector('.carousel-container').scrollBy({left: -320, behavior: 'smooth'})"
                        class="p-1 text-gray-400 transition-colors rounded hover:text-emerald-600 dark:hover:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button" 
                        onclick="document.querySelector('.carousel-container').scrollBy({left: 320, behavior: 'smooth'})"
                        class="p-1 text-gray-400 transition-colors rounded hover:text-emerald-600 dark:hover:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            @endif
        </div>

        <div class="relative menu-guest-collections-carousel">
            <!-- Carousel without visible scrollbar but with manual scrolling -->
            <div class="pb-2 overflow-x-auto overflow-y-hidden carousel-container">
                
                <!-- Flex container with proper spacing -->
                <div class="flex gap-3 w-max">
                    @foreach($collections as $collection)
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
                                        <div class="flex items-center justify-center w-16 h-16 text-white rounded-lg bg-gradient-to-br from-emerald-400 to-teal-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Collection Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <h5 class="font-medium text-gray-900 truncate transition-colors dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                            {{ $collection->collection_name }}
                                        </h5>
                                        <!-- Guest Role Badge -->
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            {{ ucfirst($collection->pivot->role ?? 'guest') }}
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
                                            <span class="flex items-center text-emerald-600 dark:text-emerald-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ $collection->creator->name }}
                                            </span>

                                            <!-- Join date -->
                                            {{-- @if($collection->pivot->created_at)
                                                <span class="flex items-center text-gray-500 dark:text-gray-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4M3 21h18M5 21V6a1 1 0 011-1h12a1 1 0 011 1v15"/>
                                                    </svg>
                                                    {{ __('collection.joined_since') }} {{ $collection->pivot->created_at->format('M Y') }}
                                                </span>
                                            @endif --}}
                                        </div>
                                    </div>

                                    <!-- Collection Description Preview (if available) -->
                                    @if($collection->description)
                                        <p class="mt-1 text-xs text-gray-600 truncate dark:text-gray-400">
                                            {{ Str::limit($collection->description, 80) }}
                                        </p>
                                    @endif

                                    <!-- Quick Actions for Collaborators -->
                                    <div class="flex items-center mt-2 space-x-2">
                                        @if($collection->pivot->role === 'editor' || $collection->pivot->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-md dark:bg-blue-900/30 dark:text-blue-300">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                {{ __('collection.can_edit') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md dark:bg-gray-800 dark:text-gray-300">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ __('collection.view_only') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>


    <style>
    /* Hide scrollbar but keep functionality */
    .carousel-container {
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
        scrollbar-width: none;  /* Firefox */
    }

    .carousel-container::-webkit-scrollbar { 
        display: none;  /* Safari and Chrome */
    }

    .carousel-container {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('.carousel-container');
        
        if (carousel) {
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
                touchStartX = e.touches[0].pageX - carousel.offsetLeft;
                touchScrollLeft = carousel.scrollLeft;
            });
            
            carousel.addEventListener('touchmove', function(e) {
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
        }
    });
    </script>

@endif
