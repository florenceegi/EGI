@props(['egi'])

<div class="flex flex-col items-center justify-center w-full h-full bg-gray-100 text-gray-500 group-hover:bg-gray-200 transition-colors duration-300">
    {{-- Icona PDF --}}
    <svg class="w-16 h-16 mb-2 text-red-500 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"></path>
    </svg>
    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">PDF Document</span>
    @if($egi->title)
        <span class="text-[10px] mt-1 px-2 text-center truncate max-w-full text-gray-500">{{ Str::limit($egi->title, 30) }}</span>
    @endif
</div>

