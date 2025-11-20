@props(['egi'])

@php
    // 📷 OTTIMIZZAZIONE IMMAGINI
    $optimizedImageUrl = $egi->main_image_url;
    
    // Classi di base che possono essere sovrascritte/estese
    // Nota: object-contain è il default per le card, ma può essere sovrascritto con object-cover per le liste
    $defaultClasses = "object-contain object-center w-full h-full transition-transform duration-300 ease-in-out bg-gray-800 group-hover:scale-105";
@endphp

{{-- 🎯 Immagine Principale o Placeholder --}}
@if ($optimizedImageUrl)
    <img src="{{ $optimizedImageUrl }}" 
        {{-- Merge attributes permette di passare classi custom (es. object-cover per le liste) --}}
        {{ $attributes->merge(['class' => $defaultClasses]) }}
        alt="{{ $egi->title ?? 'EGI Image' }}"
        loading="lazy"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
        
    {{-- Fallback visivo in caso di errore caricamento immagine --}}
    <div class="items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 hidden">
        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
@else
    {{-- Placeholder --}}
    <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900">
        <svg class="w-8 h-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
@endif
