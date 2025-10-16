{{-- Comparison Statistics Panel (Phase 2) --}}
<div class="mb-8">
    <h2 class="mb-6 flex items-center text-2xl font-bold text-white">
        <svg class="mr-3 h-8 w-8 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        {{ __('statistics.forecast_vs_reality') }}
    </h2>

    {{-- Summary Comparison Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Conversion Rate --}}
        <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-oro-fiorentino to-orange-600 p-6 backdrop-blur-sm">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-white opacity-90">{{ __('statistics.conversion_rate') }}</span>
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="comparison-conversion-rate">0%</div>
            <div class="mt-1 text-xs text-white opacity-75">{{ __('statistics.conversion_rate_description') }}</div>
        </div>

        {{-- Forecast EUR --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-verde-rinascita">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.forecast_eur') }}</span>
                <svg class="h-5 w-5 text-verde-rinascita" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="comparison-forecast">€0.00</div>
            <div class="mt-1 text-xs text-gray-500">Reservations</div>
        </div>

        {{-- Reality EUR --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-blu-algoritmo">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.reality_eur') }}</span>
                <svg class="h-5 w-5 text-blu-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-white" id="comparison-reality">€0.00</div>
            <div class="mt-1 text-xs text-gray-500">Mints</div>
        </div>

        {{-- Delta EUR --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm transition-all hover:border-viola-innovazione">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm text-gray-400">{{ __('statistics.delta_eur') }}</span>
                <svg class="h-5 w-5 text-viola-innovazione" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </div>
            <div class="text-3xl font-bold" id="comparison-delta">€0.00</div>
            <div class="mt-1 text-xs text-gray-500" id="comparison-delta-percentage">0%</div>
        </div>
    </div>

    {{-- Comparison by Collection --}}
    <div class="mb-8">
        <div class="rounded-xl border border-gray-700 bg-gray-800 bg-opacity-50 p-6 backdrop-blur-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">{{ __('statistics.comparison_by_collection') }}</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="comparison-by-collection-table">
                    <thead class="border-b border-gray-700 text-xs uppercase text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Collection</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.reservations_count') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.mints_count_short') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.forecast_eur') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.reality_eur') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.delta_eur') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('statistics.delta_percentage') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 text-gray-300">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                {{ __('statistics.no_data') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Insights & Recommendations --}}
    <div class="rounded-xl border border-yellow-700 bg-yellow-900 bg-opacity-30 p-6 backdrop-blur-sm">
        <div class="flex items-start space-x-3">
            <svg class="mt-1 h-6 w-6 flex-shrink-0 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="mb-2 font-medium text-yellow-200">Insights</h4>
                <ul class="space-y-1 text-sm text-yellow-300" id="comparison-insights">
                    <li>• Confronto tra prenotazioni (forecast) e mint reali (revenue certificata blockchain)</li>
                    <li>• Conversion rate indica quante prenotazioni si sono convertite in acquisti</li>
                    <li>• Delta positivo = revenue superiore alle aspettative</li>
                </ul>
            </div>
        </div>
    </div>
</div>
