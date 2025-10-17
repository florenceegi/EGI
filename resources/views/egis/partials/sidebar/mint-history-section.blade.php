{{-- resources/views/egis/partials/sidebar/mint-history-section.blade.php --}}
{{-- 
    Sezione cronologia mint/rebind blockchain dell'EGI
    Mostra lo storico acquisti blockchain (mint e mercato secondario)
    VARIABILI: $egi
--}}

{{-- Mint/Rebind History --}}
@if($egi->mintCertificates && $egi->mintCertificates->isNotEmpty())
<div class="space-y-4">
    <h3 class="text-lg font-semibold text-white">{{ __('certificate.mint_history.section_title') }}</h3>
    <x-egi-mint-history :egi="$egi" :certificates="$egi->mintCertificates" />
</div>
@endif
