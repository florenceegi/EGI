{{-- Reservations Statistics Panel (Legacy) --}}
<div class="mb-8">
    <h2 class="mb-6 flex items-center text-2xl font-bold text-white">
        <svg class="text-oro-fiorentino mr-3 h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        {{ __('statistics.reservations_tab') }}
    </h2>

    {{-- Summary KPI Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Reservations --}}
        <div
            class="hover:border-oro-fiorentino rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.total_reservations') }}</span>
                <svg class="text-oro-fiorentino h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="reservation-total">0</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.total_reservations') }}</div>
        </div>

        {{-- Forecast Amount --}}
        <div
            class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-verde-rinascita">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.forecast_eur') }}</span>
                <svg class="h-5 w-5 text-verde-rinascita" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="reservation-forecast">€0.00</div>
            <div class="mt-1 text-xs text-gray-500">{{ __('statistics.total_amount') }}</div>
        </div>

        {{-- Strong Reservations --}}
        <div
            class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-blu-algoritmo">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">Strong</span>
                <svg class="h-5 w-5 text-blu-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="reservation-strong">0</div>
            <div class="mt-1 text-xs text-gray-500">Strong Reservations</div>
        </div>

        {{-- Weak Reservations --}}
        <div
            class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-viola-innovazione">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">Weak</span>
                <svg class="h-5 w-5 text-viola-innovazione" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="reservation-weak">0</div>
            <div class="mt-1 text-xs text-gray-500">Weak Reservations</div>
        </div>
    </div>

    {{-- Reservations by Collection --}}
    <div class="mb-8">
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('statistics.reservations_by_collection') }}</h3>

            <div id="reservation-by-collection-container" class="space-y-3">
                <div class="text-center text-gray-400">{{ __('statistics.no_reservations') }}</div>
            </div>
        </div>
    </div>

    {{-- EPP Potential --}}
    <div class="mb-8">
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('statistics.epp_breakdown') }}</h3>

            <div id="reservation-epp-container" class="space-y-3">
                <div class="text-center text-gray-400">{{ __('statistics.no_epp_data') }}</div>
            </div>
        </div>
    </div>
</div>
