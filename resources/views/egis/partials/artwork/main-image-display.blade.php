{{-- resources/views/egis/partials/artwork/main-image-display.blade.php --}}
{{-- 
    Main Image Display per EGI
    ORIGINE: righe ~118-145 di show.blade.php
    DIPENDENZE: $egi (oggetto con main_image_url, original_image_url, title)
--}}

{{-- Main Image Display - Ottimizzata per visibilità completa --}}
<div class="relative w-full mx-auto">
    @if($egi->main_image_url)
    {{-- Trigger per lo zoom --}}
    <div id="zoom-container" class="overflow-hidden rounded-sm md:rounded-md lg:rounded-lg">
        <img id="zoom-image-trigger"
            src="{{ $egi->main_image_url }}"
            data-zoom-src="{{ $egi->original_image_url ?? $egi->main_image_url }}"
            alt="{{ $egi->title ?? __('egi.image_alt_default') }}"
            class="w-full h-auto max-h-[60vh] md:max-h-[70vh] lg:max-h-[75vh] object-contain cursor-zoom-in bg-black/20"
            loading="lazy"
            />
    </div>
    @else
    {{-- Placeholder quando non c'è immagine --}}
    <div
        class="flex items-center justify-center w-full shadow-2xl h-96 lg:h-auto bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl">
        <div class="text-center">
            <svg class="w-24 h-24 mb-4" viewBox="0 0 24 24" stroke-width="1"
                stroke="currentColor" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3l18 18M21 3L3 21" />
            </svg>
            <p class="text-lg text-gray-400">{{ __('egi.artwork_loading') }}</p>
        </div>
    </div>
    @endif
</div>
