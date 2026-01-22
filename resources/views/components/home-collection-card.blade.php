{{-- resources/views/components/home-collection-card.blade.php --}}
{{-- 📜 FIX: Aggiungere 'relative' al contenitore card --}}

@props([
    'collection',
    'imageType' => 'card',
    'displayType' => 'default'
])

@php
    $isAvatarDisplay = ($displayType === 'avatar');
    $logo = config('app.logo');
    $imageUrl = '';

    // 🌱 EPP PROJECT INFO
    $eppProject = null;
    $eppProjectImageUrl = '';
    if ($collection && $collection->eppProject) {
        $eppProject = $collection->eppProject;
        // Usa direttamente getFirstMediaUrl - ritorna stringa vuota se non c'è media
        $eppProjectImageUrl = $eppProject->getFirstMediaUrl('project_images');
    }

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
            <div class="relative w-full {{ $imageType === 'cover' ? 'aspect-[3/4]' : 'aspect-[4/5]' }} overflow-hidden">

                <img src="{{ $imageUrl }}"
                     alt="{{ $collection->collection_name }}"
                     class="object-cover w-full h-full transition-transform duration-300 ease-in-out group-hover:scale-105"
                     loading="lazy" decoding="async">
                <div class="absolute inset-0 transition-opacity bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-hover:opacity-95"></div>

                {{-- Content Section --}}
                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                    {{-- 🌱 EPP PROJECT INFO BAR con Banner + Avatar --}}
                    @if($eppProject)
                        <div class="relative mb-3 p-2 rounded-lg overflow-hidden backdrop-blur-sm border border-white/20 shadow-lg">
                            {{-- Background Image Banner --}}
                            @if($eppProject->getFirstMediaUrl('project_images'))
                                <div class="absolute inset-0">
                                    <img src="{{ $eppProject->getFirstMediaUrl('project_images') }}" 
                                         alt="{{ $eppProject->name }}"
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-r from-[#2D5016]/60 via-[#2D5016]/50 to-[#1B365D]/60"></div>
                                </div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-r from-[#2D5016]/80 to-[#1B365D]/80"></div>
                            @endif
                            
                            <div class="relative flex items-center gap-2">
                                {{-- Avatar del progetto --}}
                                @if($eppProject->getFirstMediaUrl('project_avatar'))
                                    <img src="{{ $eppProject->getFirstMediaUrl('project_avatar') }}" 
                                         alt="{{ $eppProject->name }}"
                                         class="flex-shrink-0 w-10 h-10 rounded-full object-cover ring-2 ring-white/40">
                                @else
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white/40">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.632 3.533A2 2 0 016.577 2h6.846a2 2 0 011.945 1.533l1.976 8.234A3.489 3.489 0 0016 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234z" clip-rule="evenodd"/>
                                            <path d="M4 19a2 2 0 100-4 2 2 0 000 4zM16 19a2 2 0 100-4 2 2 0 000 4z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white/90 truncate drop-shadow">{{ __('egi.epp.supports') }}</p>
                                    <p class="text-sm font-bold text-white truncate drop-shadow">{{ $eppProject->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                @php
                                    // Usa original_egis_count se disponibile (esclude cloni), altrimenti egis_count
                                    $egiCount = $collection->original_egis_count ?? $collection->egis_count ?? 0;
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-florence-gold/20 text-florence-gold font-semibold">
                                    {{ $egiCount }} {{ trans_choice('EGI|EGIs', $egiCount) }}
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
