@props([
    'creatorId' => null,
    'earnings' => null,
    'size' => 'normal',
    'period' => 'month'
])

@php
use App\Services\StatisticsService;

// Se non vengono passate le earnings, le calcola usando il periodo
if (!$earnings && $creatorId) {
    $statisticsService = app(StatisticsService::class);
    $earnings = $statisticsService->getCreatorEarnings($creatorId, $period);
}

// Fallback se non ci sono dati
$earnings = $earnings ?? [
    'total_earnings' => 0,
    'total_sales' => 0,
    'avg_earnings_per_sale' => 0,
    'collections_with_sales' => 0,
];
@endphp

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="flex items-center space-x-2 text-xl font-bold text-white">
            <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
            <span>{{ __('creator.portfolio.earnings.title') }}</span>
        </h2>
        <div class="text-sm text-gray-400">
            {{ __('creator.portfolio.earnings.subtitle') }}
        </div>
    </div>

    {{-- Earnings Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Earnings --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.earnings.total') }}"
            :value="$earnings['total_earnings']"
            :formatted_value="'€' . number_format($earnings['total_earnings'], 2)"
            icon="currency-euro"
            color="green"
            :size="$size">
            @if($earnings['total_sales'] > 0)
                {{ trans_choice('creator.portfolio.earnings.from_sales', $earnings['total_sales'], ['count' => $earnings['total_sales']]) }}
            @else
                {{ __('creator.portfolio.earnings.no_sales_yet') }}
            @endif
        </x-stats.stat-card>

        {{-- Average per Sale --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.earnings.avg_per_sale') }}"
            :value="$earnings['avg_earnings_per_sale']"
            :formatted_value="'€' . number_format($earnings['avg_earnings_per_sale'], 2)"
            icon="trending-up"
            color="blue"
            :size="$size">
            {{ __('creator.portfolio.earnings.per_transaction') }}
        </x-stats.stat-card>

        {{-- Total Sales --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.earnings.total_sales') }}"
            :value="$earnings['total_sales']"
            :formatted_value="number_format($earnings['total_sales'])"
            icon="chart-bar"
            color="purple"
            :size="$size">
            {{ __('creator.portfolio.earnings.completed_transactions') }}
        </x-stats.stat-card>

        {{-- Collections with Sales --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.earnings.collections_earning') }}"
            :value="$earnings['collections_with_sales']"
            :formatted_value="number_format($earnings['collections_with_sales'])"
            icon="collection"
            color="orange"
            :size="$size">
            {{ __('creator.portfolio.earnings.generating_revenue') }}
        </x-stats.stat-card>
    </div>

    {{-- Performance Indicators --}}
    @if($earnings['total_earnings'] > 0)
        <div class="p-4 bg-gray-800 border border-gray-700 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-gray-300">{{ __('creator.portfolio.earnings.status_active') }}</span>
                </div>
                <div class="text-gray-400">
                    {{ __('creator.portfolio.earnings.range') }}:
                    €{{ number_format($earnings['min_earnings'] ?? 0, 2) }} - €{{ number_format($earnings['max_earnings'] ?? 0, 2) }}
                </div>
            </div>
        </div>
    @else
        <div class="p-4 bg-gray-800 border rounded-lg border-yellow-500/20">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <p class="font-medium text-yellow-400">{{ __('creator.portfolio.earnings.no_earnings_title') }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ __('creator.portfolio.earnings.no_earnings_description') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
