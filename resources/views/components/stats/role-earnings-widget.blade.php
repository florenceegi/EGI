@props([
    'userId' => null,
    'totalEarnings' => null,
    'nonCreatorEarnings' => null,
    'size' => 'normal'
])

@php
use App\Models\PaymentDistribution;

// Se non vengono passate le statistiche, le calcola
if (!$totalEarnings && $userId) {
    $totalEarnings = PaymentDistribution::getUserTotalEarnings($userId);
}

if (!$nonCreatorEarnings && $userId) {
    $nonCreatorEarnings = PaymentDistribution::getUserNonCreatorEarnings($userId);
}

// Fallback se non ci sono dati
$totalEarnings = $totalEarnings ?? [
    'total_earnings' => 0,
    'total_distributions' => 0,
    'avg_earning_per_distribution' => 0,
    'collections_involved' => 0,
    'reservations_involved' => 0,
];

$nonCreatorEarnings = $nonCreatorEarnings ?? [
    'total_earnings' => 0,
    'total_distributions' => 0,
    'avg_earning_per_distribution' => 0,
    'collections_involved' => 0,
    'reservations_involved' => 0,
    'roles_held' => [],
    'collection_breakdown' => [],
];

// Calcola la percentuale di entrate da ruoli non-creator
$nonCreatorPercentage = $totalEarnings['total_earnings'] > 0
    ? round(($nonCreatorEarnings['total_earnings'] / $totalEarnings['total_earnings']) * 100, 1)
    : 0;
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center space-x-2">
            <svg class="w-6 h-6 text-verde-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ __('Statistiche Entrate per Ruolo') }}</span>
        </h2>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Total Earnings Card --}}
        <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ __('Entrate Totali') }}</h3>
                <div class="p-2 bg-oro-fiorentino/20 rounded-lg">
                    <svg class="w-5 h-5 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-3xl font-bold text-oro-fiorentino">
                    <x-currency-price :price="$totalEarnings['total_earnings']" />
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">{{ __('Distribuzioni') }}</span>
                        <div class="text-white font-medium">{{ number_format($totalEarnings['total_distributions']) }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">{{ __('Collezioni') }}</span>
                        <div class="text-white font-medium">{{ number_format($totalEarnings['collections_involved']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Non-Creator Earnings Card --}}
        <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ __('Entrate da Ruoli') }}</h3>
                <div class="p-2 bg-verde-algoritmo/20 rounded-lg">
                    <svg class="w-5 h-5 text-verde-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-3xl font-bold text-verde-algoritmo">
                    <x-currency-price :price="$nonCreatorEarnings['total_earnings']" />
                </div>

                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-gray-400">{{ __('del totale') }}</span>
                    <span class="px-2 py-1 bg-verde-algoritmo/20 text-verde-algoritmo rounded-full font-medium">
                        {{ $nonCreatorPercentage }}%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">{{ __('Distribuzioni') }}</span>
                        <div class="text-white font-medium">{{ number_format($nonCreatorEarnings['total_distributions']) }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">{{ __('Collezioni') }}</span>
                        <div class="text-white font-medium">{{ number_format($nonCreatorEarnings['collections_involved']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Held --}}
    @if(!empty($nonCreatorEarnings['roles_held']))
    <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-blu-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>{{ __('Ruoli Attivi') }}</span>
        </h3>

        <div class="flex flex-wrap gap-2">
            @foreach($nonCreatorEarnings['roles_held'] as $role)
            <span class="px-3 py-1 bg-blu-algoritmo/20 text-blu-algoritmo rounded-full text-sm font-medium">
                {{ ucfirst($role) }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Collection Breakdown --}}
    @if(!empty($nonCreatorEarnings['collection_breakdown']))
    <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-giallo-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>{{ __('Dettaglio per Collezione') }}</span>
        </h3>

        <div class="space-y-3">
            @foreach($nonCreatorEarnings['collection_breakdown'] as $collection)
            <div class="flex items-center justify-between p-4 bg-gray-900/50 rounded-lg border border-gray-600">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <h4 class="text-white font-medium">{{ $collection['collection_name'] }}</h4>
                        <span class="px-2 py-1 bg-{{ $collection['role'] === 'admin' ? 'red' : ($collection['role'] === 'editor' ? 'yellow' : 'blue') }}-500/20 text-{{ $collection['role'] === 'admin' ? 'red' : ($collection['role'] === 'editor' ? 'yellow' : 'blue') }}-400 rounded text-xs font-medium">
                            {{ ucfirst($collection['role']) }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-400">
                        <span>{{ number_format($collection['distributions_count']) }} distribuzioni</span>
                        <span>Media: <x-currency-price :price="$collection['avg_earning']" /></span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-verde-algoritmo">
                        <x-currency-price :price="$collection['earnings_from_collection']" />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if($nonCreatorEarnings['total_earnings'] == 0)
    <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2">{{ __('Nessuna Entrata da Ruoli') }}</h3>
        <p class="text-gray-400">{{ __('Non hai ancora ricevuto entrate da collezioni dove hai un ruolo collaborativo.') }}</p>
    </div>
    @endif
</div>
