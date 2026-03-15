{{-- Image Carousel - Stile Collection Navigator --}}
@if($hasImages())
<div class="w-full border-t utility-carousel border-gray-700/30">
    <div class="relative px-2 py-2">
        <!-- Carousel Container -->
        <div class="relative w-full">
            <!-- Scrollable Container -->
            <div class="flex gap-2 py-1 overflow-x-auto scrollbar-hide scroll-smooth"
                 style="scrollbar-width: none; -ms-overflow-style: none;">
                
                @foreach($getImages() as $index => $media)
                    <button type="button"
                            class="flex-shrink-0 w-12 h-12 overflow-hidden transition-all duration-200 rounded-lg md:w-14 md:h-14 hover:scale-105 hover:shadow-lg hover:ring-2 hover:ring-white/50"
                            onclick="openUtilityImageModal('{{ $media->getUrl('large') }}', '{{ $utility->title }}', {{ $index }})">
                        <img src="{{ $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl() }}"
                             onerror="this.src='/images/no-image.jpg'" 
                             alt="{{ $utility->title }} - Image {{ $index + 1 }}"
                             class="object-cover w-full h-full transition-opacity duration-200 opacity-80 hover:opacity-100"
                             loading="lazy">
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endif