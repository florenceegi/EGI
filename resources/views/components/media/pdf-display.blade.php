@props(['egi'])

@php
    $pdfUrl = $egi->main_image_url;
    // Classi default per visualizzazione PDF
    $defaultClasses = "w-full h-full pointer-events-none object-cover bg-white";
@endphp

<div class="relative w-full h-full bg-gray-100 group overflow-hidden">
    @if($pdfUrl)
        {{-- 
            📄 PDF Native Embedding 
            Usiamo <object> con pointer-events-none per evitare che il viewer PDF 
            catturi i click mouse, permettendo alla card padre di rimanere cliccabile.
        --}}
        <object 
            data="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=0&view=Fit" 
            type="application/pdf" 
            {{-- Supporto per classi custom (es. bordi, fit diversi) --}}
            {{ $attributes->merge(['class' => $defaultClasses]) }}
            style="pointer-events: none;"
        >
            {{-- Fallback: Icona PDF se il browser non supporta embed --}}
            <div class="flex flex-col items-center justify-center w-full h-full bg-gray-100 text-gray-500">
                <svg class="w-8 h-8 mb-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"></path>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-600">PDF</span>
            </div>
        </object>
        
        {{-- Overlay trasparente assoluto per garantire clickabilità dell'intera card --}}
        <div class="absolute inset-0 z-10 bg-transparent cursor-pointer"></div>
    @else
        {{-- Fallback generico se URL nullo --}}
        <div class="flex flex-col items-center justify-center w-full h-full bg-gray-200 text-gray-500">
             <svg class="w-8 h-8 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-[10px]">N/A</span>
        </div>
    @endif
</div>
