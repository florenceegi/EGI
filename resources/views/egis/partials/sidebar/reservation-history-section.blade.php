{{-- resources/views/egis/partials/sidebar/reservation-history-section.blade.php --}}
{{-- 
    Sezione cronologia prenotazioni dell'EGI
    ORIGINE: righe 152-159 di show.blade.php
    VARIABILI: $egi
--}}

{{-- Reservation History --}}
@if($egi->price && $egi->price > 0)
    @if($egi->reservationCertificates && $egi->reservationCertificates->isNotEmpty())
        {{-- Ha offerte - mostra cronologia completa --}}
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-white">{{ __('reservation.history.purchases_offers_title') }}</h3>
            <x-egi-reservation-history :egi="$egi" :certificates="$egi->reservationCertificates" />
        </div>
    @else
        {{-- Nessuna offerta - Badge visibile --}}
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-white">{{ __('reservation.history.purchases_offers_title') }}</h3>
            <div class="flex items-center space-x-2 rounded-lg bg-indigo-900/20 px-3 py-2.5 border border-indigo-500/30">
                <svg class="h-4 w-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-sm text-indigo-200">{{ __('reservation.history.no_entries') }}</span>
            </div>
        </div>
    @endif
@endif
