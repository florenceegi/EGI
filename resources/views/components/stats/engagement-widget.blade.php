@props([
    'creatorId' => null,
    'engagement' => null,
    'size' => 'normal',
    'period' => 'month'
])

@php
use App\Services\StatisticsService;

// Se non vengono passate le engagement stats, le calcola usando il periodo temporale
if (!$engagement && $creatorId) {
    $statisticsService = app(StatisticsService::class);
    $engagement = $statisticsService->getCreatorEngagementStats($creatorId, $period);
}

// Fallback se non ci sono dati
$engagement = $engagement ?? [
    'collectors_reached' => 0,
    'epp_impact_generated' => 0,
    'total_volume_generated' => 0,
    'avg_impact_per_collector' => 0,
    'epp_percentage' => 0,
];
@endphp

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center space-x-2">
            <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span>{{ __('creator.portfolio.engagement.title') }}</span>
        </h2>
        <div class="text-sm text-gray-400">
            {{ __('creator.portfolio.engagement.subtitle') }}
        </div>
    </div>

    {{-- Engagement Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Collectors Reached --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.engagement.collectors_reached') }}"
            :value="$engagement['collectors_reached']"
            :formatted_value="number_format($engagement['collectors_reached'])"
            icon="users"
            color="blue"
            :size="$size">
            {{ __('creator.portfolio.engagement.unique_buyers') }}
        </x-stats.stat-card>

        {{-- EPP Impact --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.engagement.epp_impact') }}"
            :value="$engagement['epp_impact_generated']"
            :formatted_value="'€' . number_format($engagement['epp_impact_generated'], 2)"
            icon="heart"
            color="green"
            :size="$size">
            {{ __('creator.portfolio.engagement.environmental_contribution') }}
        </x-stats.stat-card>

        {{-- Total Volume Generated --}}
        <x-stats.stat-card
            title="{{ __('creator.portfolio.engagement.total_volume') }}"
            :value="$engagement['total_volume_generated']"
            :formatted_value="'€' . number_format($engagement['total_volume_generated'], 2)"
            icon="trending-up"
            color="purple"
            :size="$size">
            {{ __('creator.portfolio.engagement.ecosystem_contribution') }}
        </x-stats.stat-card>
    </div>

    {{-- Impact Analysis --}}
    @if($engagement['total_volume_generated'] > 0)
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>{{ __('creator.portfolio.engagement.impact_analysis') }}</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- EPP Impact Percentage --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">{{ __('creator.portfolio.engagement.epp_percentage') }}</span>
                        <span class="text-white font-semibold">{{ number_format($engagement['epp_percentage'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-1000"
                             style="width: {{ min($engagement['epp_percentage'], 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400">{{ __('creator.portfolio.engagement.epp_percentage_description') }}</p>
                </div>

                {{-- Average Impact per Collector --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">{{ __('creator.portfolio.engagement.avg_impact_per_collector') }}</span>
                        <span class="text-white font-semibold">€{{ number_format($engagement['avg_impact_per_collector'], 2) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <p class="text-xs text-gray-400">{{ __('creator.portfolio.engagement.avg_impact_description') }}</p>
                    </div>
                </div>
            </div>

            {{-- Impact Milestones --}}
            <div class="mt-6 pt-4 border-t border-gray-700">
                <div class="flex items-center justify-between text-sm">
                    @php
                        $impactLevel = '';
                        $nextMilestone = 0;
                        if ($engagement['epp_impact_generated'] >= 1000) {
                            $impactLevel = __('creator.portfolio.engagement.impact_level_high');
                        } elseif ($engagement['epp_impact_generated'] >= 500) {
                            $impactLevel = __('creator.portfolio.engagement.impact_level_medium');
                            $nextMilestone = 1000;
                        } elseif ($engagement['epp_impact_generated'] >= 100) {
                            $impactLevel = __('creator.portfolio.engagement.impact_level_growing');
                            $nextMilestone = 500;
                        } else {
                            $impactLevel = __('creator.portfolio.engagement.impact_level_starting');
                            $nextMilestone = 100;
                        }
                    @endphp

                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <span class="text-gray-300">{{ $impactLevel }}</span>
                    </div>

                    @if($nextMilestone > 0)
                        <span class="text-gray-400">
                            {{ __('creator.portfolio.engagement.next_milestone') }}: €{{ number_format($nextMilestone) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

    @else
        {{-- No Engagement Yet --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">{{ __('creator.portfolio.engagement.no_engagement_title') }}</h3>
            <p class="text-gray-400 mb-4">{{ __('creator.portfolio.engagement.no_engagement_description') }}</p>
            <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span>{{ __('creator.portfolio.engagement.build_audience') }}</span>
                </div>
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span>{{ __('creator.portfolio.engagement.create_impact') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
