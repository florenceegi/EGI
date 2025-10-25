{{-- resources/views/egis/partials/sidebar/description-section.blade.php --}}
{{--
    Sezione descrizione dell'EGI
    ORIGINE: righe 149-156 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Description Section - Responsive --}}
<div class="space-y-3 sm:space-y-4">
    <h3 class="text-base font-semibold text-white sm:text-lg">{{ __('egi.about_this_piece') }}</h3>
    <div class="prose prose-sm prose-invert max-w-none text-sm leading-relaxed text-gray-300 sm:text-base">
        {!! nl2br(e($egi->description ?? __('egi.default_description'))) !!}
    </div>
</div>
