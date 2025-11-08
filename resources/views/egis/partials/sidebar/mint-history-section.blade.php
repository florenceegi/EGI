{{-- resources/views/egis/partials/sidebar/mint-history-section.blade.php --}}
{{--
    Sezione cronologia mint/rebind blockchain dell'EGI
    Mostra lo storico acquisti blockchain (mint e mercato secondario)
    VARIABILI: $egi
--}}

{{-- Mint/Rebind History --}}
@php
    $mintCertificates = $egi->mintCertificates instanceof \Illuminate\Support\Collection
        ? $egi->mintCertificates->unique('egi_blockchain_id')->values()
        : collect();
@endphp

@if ($mintCertificates->isNotEmpty())
    <x-egi-mint-history :egi="$egi" :certificates="$mintCertificates" />
@endif
