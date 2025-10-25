{{-- resources/views/egis/partials/sidebar/description-section.blade.php --}}
{{--
    Sezione descrizione dell'EGI
    ORIGINE: righe 149-156 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Description Section - Compatto --}}
<div class="space-y-2 sm:space-y-2.5 md:space-y-2.5 lg:space-y-3">
    <h3 class="text-sm font-semibold text-white sm:text-sm md:text-base lg:text-base">{{ __('egi.about_this_piece') }}</h3>
    <div class="prose prose-sm prose-invert max-w-none text-xs leading-relaxed text-gray-300 sm:text-xs md:text-sm lg:text-sm">
        {!! nl2br(e($egi->description ?? __('egi.default_description'))) !!}
    </div>
</div>
