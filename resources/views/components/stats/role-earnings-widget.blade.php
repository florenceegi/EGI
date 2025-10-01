@props([
    'userId' => null,
    'totalEarnings' => null,
    'nonCreatorEarnings' => null,
    'size' => 'normal',
    'period' => 'month',
])

@php
    use App\Services\StatisticsService;

    // Se non vengono passate le statistiche, le calcola usando il periodo temporale
    if (!$totalEarnings && $userId) {
        $statisticsService = app(StatisticsService::class);
        $totalEarnings = $statisticsService->getUserTotalEarnings($userId, $period);
    }

    if (!$nonCreatorEarnings && $userId) {
        $statisticsService = app(StatisticsService::class);
        $nonCreatorEarnings = $statisticsService->getUserNonCreatorEarnings($userId, $period);
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
    $nonCreatorPercentage =
        $totalEarnings['total_earnings'] > 0
            ? round(($nonCreatorEarnings['total_earnings'] / $totalEarnings['total_earnings']) * 100, 1)
            : 0;
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="flex items-center space-x-2 text-xl font-bold text-white">
            <svg class="text-verde-algoritmo h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ __('statistics.role_earnings_stats') }}</span>
        </h2>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Total Earnings Card --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">{{ __('statistics.total_earnings') }}</h3>
                <div class="bg-oro-fiorentino/20 rounded-lg p-2">
                    <svg class="text-oro-fiorentino h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-oro-fiorentino text-3xl font-bold">
                    <x-currency-price :price="$totalEarnings['total_earnings']" />
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">{{ __('statistics.distributions') }}</span>
                        <div class="font-medium text-white">{{ number_format($totalEarnings['total_distributions']) }}
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400">{{ __('statistics.collections') }}</span>
                        <div class="font-medium text-white">{{ number_format($totalEarnings['collections_involved']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Non-Creator Earnings Card --}}
        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">{{ __('statistics.role_earnings') }}</h3>
                <div class="bg-verde-algoritmo/20 rounded-lg p-2">
                    <svg class="text-verde-algoritmo h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-verde-algoritmo text-3xl font-bold">
                    <x-currency-price :price="$nonCreatorEarnings['total_earnings']" />
                </div>

                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-gray-400">{{ __('statistics.of_total') }}</span>
                    <span class="bg-verde-algoritmo/20 text-verde-algoritmo rounded-full px-2 py-1 font-medium">
                        {{ $nonCreatorPercentage }}%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">{{ __('statistics.distributions') }}</span>
                        <div class="font-medium text-white">
                            {{ number_format($nonCreatorEarnings['total_distributions']) }}</div>
                    </div>
                    <div>
                        <span class="text-gray-400">{{ __('statistics.collections') }}</span>
                        <div class="font-medium text-white">
                            {{ number_format($nonCreatorEarnings['collections_involved']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Held --}}
    @if (!empty($nonCreatorEarnings['roles_held']))
        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
            <h3 class="mb-4 flex items-center space-x-2 text-lg font-semibold text-white">
                <svg class="h-5 w-5 text-blu-algoritmo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>{{ __('statistics.active_roles') }}</span>
            </h3>

            <div class="flex flex-wrap gap-2">
                @foreach ($nonCreatorEarnings['roles_held'] as $role)
                    <span class="rounded-full bg-blu-algoritmo/20 px-3 py-1 text-sm font-medium text-blu-algoritmo">
                        {{ ucfirst($role) }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Collection Breakdown --}}
    @if (!empty($nonCreatorEarnings['collection_breakdown']))
        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-6">
            <h3 class="mb-4 flex items-center space-x-2 text-lg font-semibold text-white">
                <svg class="text-giallo-algoritmo h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>{{ __('statistics.collection_breakdown') }}</span>
            </h3>

            <div class="space-y-3">
                @foreach ($nonCreatorEarnings['collection_breakdown'] as $collection)
                    <div class="flex items-center justify-between rounded-lg border border-gray-600 bg-gray-900/50 p-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h4 class="font-medium text-white">{{ $collection['collection_name'] }}</h4>
                                <span
                                    class="bg-{{ $collection['role'] === 'admin' ? 'red' : ($collection['role'] === 'editor' ? 'yellow' : 'blue') }}-500/20 text-{{ $collection['role'] === 'admin' ? 'red' : ($collection['role'] === 'editor' ? 'yellow' : 'blue') }}-400 rounded px-2 py-1 text-xs font-medium">
                                    {{ ucfirst($collection['role']) }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-400">
                                <span>{{ number_format($collection['distributions_count']) }}
                                    {{ __('statistics.distributions_count') }}</span>
                                <span>{{ __('statistics.average') }}: <x-currency-price :price="$collection['avg_earning']" /></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-verde-algoritmo text-lg font-bold">
                                <x-currency-price :price="$collection['earnings_from_collection']" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if ($nonCreatorEarnings['total_earnings'] == 0)
        <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-700">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-white">{{ __('statistics.no_role_earnings') }}</h3>
            <p class="text-gray-400">{{ __('statistics.no_role_earnings_message') }}</p>
        </div>
    @endif
</div>
