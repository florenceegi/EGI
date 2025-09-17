@props([
    'creatorId' => null,
    'collectionPerformance' => null,
    'limit' => 5,
    'size' => 'normal',
    'period' => 'month'
])

@php
use App\Services\StatisticsService;

// Se non vengono passate le performance, le calcola usando il periodo temporale
if (!$collectionPerformance && $creatorId) {
    $statisticsService = app(StatisticsService::class);
    $collectionPerformance = $statisticsService->getCreatorCollectionPerformance($creatorId, $period, $limit);
}

// Fallback se non ci sono dati
$collectionPerformance = $collectionPerformance ?? [];
@endphp

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center space-x-2">
            <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>{{ __('creator.portfolio.collections.title') }}</span>
        </h2>
        <div class="text-sm text-gray-400">
            {{ __('creator.portfolio.collections.subtitle') }}
        </div>
    </div>

    @if(count($collectionPerformance) > 0)
        {{-- Collections List --}}
        <div class="space-y-3">
            @foreach($collectionPerformance as $collection)
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 hover:border-gray-600 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-oro-fiorentino rounded-full"></div>
                            <h3 class="font-semibold text-white">{{ $collection['collection_name'] }}</h3>
                            @if($collection['floor_price'] > 0)
                                <span class="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded">
                                    Floor: €{{ number_format($collection['floor_price'], 2) }}
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-400">€{{ number_format($collection['total_earnings'], 2) }}</p>
                            <p class="text-xs text-gray-400">{{ __('creator.portfolio.collections.total_earned') }}</p>
                        </div>
                    </div>

                    {{-- Performance Metrics --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div class="text-center">
                            <p class="font-semibold text-white">{{ number_format($collection['sales_count']) }}</p>
                            <p class="text-gray-400">{{ __('creator.portfolio.collections.sales') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-white">{{ number_format($collection['egis_sold']) }}</p>
                            <p class="text-gray-400">{{ __('creator.portfolio.collections.egis_sold') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-white">€{{ number_format($collection['avg_earnings'], 2) }}</p>
                            <p class="text-gray-400">{{ __('creator.portfolio.collections.avg_earnings') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-white">€{{ number_format($collection['best_sale'], 2) }}</p>
                            <p class="text-gray-400">{{ __('creator.portfolio.collections.best_sale') }}</p>
                        </div>
                    </div>

                    {{-- Conversion Rate Bar --}}
                    @if($collection['conversion_rate'] > 0)
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                                <span>{{ __('creator.portfolio.collections.conversion_rate') }}</span>
                                <span>{{ number_format($collection['conversion_rate'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                     style="width: {{ min($collection['conversion_rate'], 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Summary Stats --}}
        @php
            $totalEarnings = collect($collectionPerformance)->sum('total_earnings');
            $totalSales = collect($collectionPerformance)->sum('sales_count');
            $avgConversion = collect($collectionPerformance)->avg('conversion_rate');
        @endphp

        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-lg font-bold text-oro-fiorentino">€{{ number_format($totalEarnings, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ __('creator.portfolio.collections.total_from_top') }}</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-oro-fiorentino">{{ number_format($totalSales) }}</p>
                    <p class="text-xs text-gray-400">{{ __('creator.portfolio.collections.total_sales') }}</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-oro-fiorentino">{{ number_format($avgConversion, 1) }}%</p>
                    <p class="text-xs text-gray-400">{{ __('creator.portfolio.collections.avg_conversion') }}</p>
                </div>
            </div>
        </div>

    @else
        {{-- No Collections Message --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center">
            <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">{{ __('creator.portfolio.collections.no_sales_title') }}</h3>
            <p class="text-gray-400 mb-4">{{ __('creator.portfolio.collections.no_sales_description') }}</p>
            <a href="{{ route('home.collections.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('creator.portfolio.collections.create_collection') }}
            </a>
        </div>
    @endif
</div>
