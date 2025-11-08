{{-- resources/views/egis/partials/sidebar/reservation-history-section.blade.php --}}
{{--
    Sezione cronologia prenotazioni dell'EGI
    ORIGINE: righe 152-159 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Reservation History --}}
@php
    $reservationCertificates = $egi->reservationCertificates instanceof \Illuminate\Support\Collection
        ? $egi->reservationCertificates
        : collect();
@endphp

@if ($egi->price && $egi->price > 0 && $reservationCertificates->isNotEmpty())
    {{-- Ha offerte - mostra cronologia completa --}}
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-white">{{ __('reservation.history.purchases_offers_title') }}</h3>
        <x-egi-reservation-history :egi="$egi" :certificates="$reservationCertificates" />
    </div>
@endif
