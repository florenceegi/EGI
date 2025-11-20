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

                {{-- 🌱 EPP PROJECT IMAGE & BADGE --}}
                @if($eppProject)
                    <div class="absolute top-3 right-3">
                        @if($eppProjectImageUrl)
                            {{-- EPP Project Image --}}
                            <div class="relative group/epp">
                                <img src="{{ $eppProjectImageUrl }}" 
                                     alt="{{ $eppProject->name }}"
                                     class="w-16 h-16 rounded-lg object-cover border-2 border-[#2D5016] shadow-lg transition-transform duration-300 group-hover/epp:scale-110"
                                     title="{{ __('egi.epp.supports_project', ['project' => $eppProject->name]) }}">
                                <div class="absolute inset-0 rounded-lg bg-[#2D5016]/20 opacity-0 group-hover/epp:opacity-100 transition-opacity"></div>
                            </div>
                        @else
                            {{-- EPP Project Icon Badge --}}
                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-[#2D5016] border-2 border-[#2D5016]/50 shadow-lg"
                                 title="{{ __('egi.epp.supports_project', ['project' => $eppProject->name]) }}">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.632 3.533A2 2 0 016.577 2h6.846a2 2 0 011.945 1.533l1.976 8.234A3.489 3.489 0 0016 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234z" clip-rule="evenodd"/>
                                    <path d="M4 19a2 2 0 100-4 2 2 0 000 4zM16 19a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- FIX: Questo 'absolute' ora è contenuto dal 'relative' sopra --}}
                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                    {{-- 🌱 EPP PROJECT INFO BAR --}}
                    @if($eppProject)
                        <div class="mb-3 p-2 rounded-lg bg-[#2D5016]/80 backdrop-blur-sm border border-[#2D5016]/50">
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 p-1 rounded bg-white/20">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.632 3.533A2 2 0 016.577 2h6.846a2 2 0 011.945 1.533l1.976 8.234A3.489 3.489 0 0016 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234z" clip-rule="evenodd"/>
                                        <path d="M4 19a2 2 0 100-4 2 2 0 000 4zM16 19a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-white/80 truncate">{{ __('egi.epp.supports') }}</p>
                                    <p class="text-sm font-semibold text-white truncate">{{ $eppProject->name }}</p>
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
