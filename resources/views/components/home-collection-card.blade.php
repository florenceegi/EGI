{{-- resources/views/components/home-collection-card.blade.php --}}
{{-- 📜 FIX: Aggiungere 'relative' al contenitore card --}}

@props([
    'collection',
    'imageType' => 'card',
    'displayType' => 'default'
])

@php
    $isAvatarDisplay = ($displayType === 'avatar');
    $logo = config('app.logo_01');
    $imageUrl = '';
    
    
    // Prova ad usare Spatie Media se disponibile
    if ($collection) {
        if (method_exists($collection, 'getFirstMediaUrl')) {
            $imageUrl = $collection->getFirstMediaUrl('head', 'card');
            if ($imageUrl != '') {
                // OK, abbiamo un'immagine
            } else {
                // Nessuna immagine, usa il logo di default
                $imageUrl = asset($logo);
            }
        } else {
            $imageUrl = asset($logo);
        }
    }
    
    
@endphp

@if($collection)
    <a href="{{ route('home.collections.show', $collection->id) }}"
       class="block w-full h-full group focus:outline-none focus:ring-2 focus:ring-florence-gold focus:ring-offset-2 focus:ring-offset-gray-800 {{ $isAvatarDisplay ? 'p-2' : 'relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 bg-gray-800' }}"
       aria-label="{{ sprintf(__('View collection %s by %s'), $collection->collection_name, $collection->creator?->name) }}">

        @if($isAvatarDisplay)
            {{-- Visualizzazione AVATAR (Mobile) --}}
            <div class="flex flex-col items-center text-center">
                <div class="relative w-24 h-24 mb-3 md:w-28 md:h-28">
                    <img src="{{ $imageUrl }}"
                         alt="{{ $collection->collection_name }}"
                         class="object-cover w-full h-full transition-colors border-2 border-gray-700 rounded-full shadow-md group-hover:border-florence-gold"
                         loading="lazy" decoding="async"
                         width="96" height="96">
                </div>
                <h3 class="text-sm font-semibold text-white truncate font-body group-hover:text-florence-gold" title="{{ $collection->collection_name }}">
                    {{ Str::limit($collection->collection_name, 25) }}
                </h3>
                @if($collection->creator)
                    <p class="text-xs text-gray-400 truncate font-body group-hover:text-gray-300" title="{{ $collection->creator->name }}">
                        {{ __('by') }} {{ Str::limit($collection->creator->name, 20) }}
                    </p>
                @endif
            </div>
        @else
            {{-- Visualizzazione CARD (Desktop/Tablet) --}}
            {{-- FIX: Il contenitore ora ha 'relative' per contenere gli 'absolute' --}}
            <div class="relative w-full {{ $imageType === 'cover' ? 'aspect-[3/4]' : 'aspect-square' }} overflow-hidden">
                
                <img src="{{ $imageUrl }}"
                     alt="{{ $collection->collection_name }}"
                     class="object-cover w-full h-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                     loading="lazy" decoding="async">
                <div class="absolute inset-0 transition-opacity bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-80 group-hover:opacity-90"></div>

                {{-- FIX: Questo 'absolute' ora è contenuto dal 'relative' sopra --}}
                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white truncate transition-colors md:text-xl font-display group-hover:text-florence-gold" title="{{ $collection->collection_name }}">
                                {{ $collection->collection_name }}
                            </h3>
                            @if($collection->creator)
                                <p class="mt-1 text-sm text-gray-300 truncate transition-colors font-body group-hover:text-gray-100">
                                    {{ __('by') }} {{ $collection->creator->name }}
                                </p>
                            @endif
                            <div class="mt-3 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-florence-gold/20 text-florence-gold font-semibold">
                                    {{ $collection->egis_count ?? 0 }} {{ trans_choice('EGI|EGIs', $collection->egis_count ?? 0) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Like Button -->
                        <div class="flex-shrink-0 ml-2">
                            <x-like-button
                                :resourceType="'collection'"
                                :resourceId="$collection->id"
                                :isLiked="$collection->is_liked ?? false"
                                :likesCount="$collection->likes_count ?? 0"
                                size="small"
                            />
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </a>
@else
    <div class="flex items-center justify-center w-full h-full p-4 text-center text-gray-500 bg-gray-800 rounded-xl">
        {{ __('Collection data not available.') }}
    </div>
@endif
