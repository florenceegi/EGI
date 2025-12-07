@props(['egi'])

@php
    // 📷 OTTIMIZZAZIONE IMMAGINI: usa l'accessor del modello con fallback interno
    // getMainImageUrlAttribute() restituisce la variante 'card' se presente,
    // altrimenti fa fallback all'originale su disco pubblico.
    $optimizedImageUrl = $egi->main_image_url;
@endphp

{{-- 🎯 Immagine Principale o Placeholder --}}
@if ($optimizedImageUrl)
    <img src="{{ $optimizedImageUrl }}" {{-- Usa l'URL ottimizzato con fallback --}} alt="{{ $egi->title ?? 'EGI Image' }}"
        class="h-auto max-h-full w-full object-contain object-center transition-transform duration-300 ease-in-out group-hover:scale-105"
        loading="lazy" {{-- Supporto WebP con fallback automatico del browser --}}
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
    {{-- Fallback visivo in caso di errore caricamento immagine --}}
    <div class="items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 hidden">
        <svg class="w-16 h-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
@else
    {{-- Placeholder --}}
    <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900">
        <svg class="w-16 h-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
@endif

