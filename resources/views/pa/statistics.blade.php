<x-pa-layout title="Statistiche N.A.T.A.N.">
    <div class="container mx-auto px-4 py-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#1B365D] mb-2">
                📊 {{ __('pa_acts.statistics.page_title') }}
            </h1>
            <p class="text-gray-600">
                {{ __('pa_acts.statistics.page_subtitle') }} - {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- KPI Cards Row 1 - Contatori Base --}}
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
            {{-- Total Acts --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.total') }}</p>
                        <p class="mt-2 text-3xl font-bold text-[#1B365D]">
                            {{ $stats['total'] ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-[#1B365D] bg-opacity-10 p-3">
                        <span class="material-icons text-[#1B365D] text-3xl">description</span>
                    </div>
                </div>
            </div>

            {{-- Anchored --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.anchored') }}</p>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ $stats['anchored'] ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-green-100 p-3">
                        <span class="material-icons text-green-600 text-3xl">verified</span>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.pending') }}</p>
                        <p class="mt-2 text-3xl font-bold text-orange-600">
                            {{ $stats['pending'] ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-orange-100 p-3">
                        <span class="material-icons text-orange-600 text-3xl">schedule</span>
                    </div>
                </div>
            </div>

            {{-- Failed --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.failed') }}</p>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ $stats['failed'] ?? 0 }}
                        </p>
                        @if(($stats['failed'] ?? 0) > 0)
                            <p class="mt-1 text-xs text-red-500">{{ __('pa_acts.statistics.require_attention') }}</p>
                        @endif
                    </div>
                    <div class="rounded-lg bg-red-100 p-3">
                        <span class="material-icons text-red-600 text-3xl">error</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Cards Row 2 - Performance Metrics --}}
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
            {{-- Success Rate --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.success_rate') }}</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        {{ number_format($stats['success_rate'] ?? 0, 1) }}%
                    </p>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full bg-green-600 transition-all" 
                         style="width: {{ $stats['success_rate'] ?? 0 }}%"></div>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    {{ __('pa_acts.statistics.completed_of_total', [
                        'completed' => $stats['tokenization']['completed'] ?? 0, 
                        'total' => $stats['total'] ?? 0
                    ]) }}
                </p>
            </div>

            {{-- Avg Time --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.avg_time') }}</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600">
                        @if(isset($stats['avg_tokenization_time']) && $stats['avg_tokenization_time'])
                            {{ number_format($stats['avg_tokenization_time'], 0) }}<span class="text-xl">s</span>
                        @else
                            <span class="text-gray-400">--</span>
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ __('pa_acts.statistics.from_upload_to_anchor') }}</p>
                </div>
            </div>

            {{-- This Month --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('pa_acts.index.stats.this_month') }}</p>
                    <p class="mt-2 text-3xl font-bold text-purple-600">
                        {{ $stats['this_month']['uploaded'] ?? 0 }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('pa_acts.statistics.already_anchored', ['count' => $stats['this_month']['anchored'] ?? 0]) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
            {{-- Document Type Distribution --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    {{ __('pa_acts.statistics.doc_type_distribution') }}
                </h3>
                @if(!empty($docTypeDistribution))
                    <div class="space-y-3">
                        @foreach($docTypeDistribution as $type => $count)
                            @php
                                $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                                $colors = [
                                    'delibera' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-700'],
                                    'determina' => ['bg' => 'bg-green-500', 'text' => 'text-green-700'],
                                    'ordinanza' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-700'],
                                    'decreto' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-700'],
                                    'atto' => ['bg' => 'bg-gray-500', 'text' => 'text-gray-700'],
                                ];
                                $color = $colors[$type] ?? ['bg' => 'bg-gray-500', 'text' => 'text-gray-700'];
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium {{ $color['text'] }} capitalize">
                                        {{ ucfirst($type) }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ $count }} ({{ number_format($percentage, 1) }}%)
                                    </span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full {{ $color['bg'] }} transition-all" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">{{ __('pa_acts.statistics.no_data') }}</p>
                @endif
            </div>

            {{-- Monthly Trends --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    {{ __('pa_acts.statistics.monthly_trends') }}
                </h3>
                @if(!empty($monthlyTrends))
                    <div class="space-y-3">
                        @foreach(array_slice($monthlyTrends, -6) as $trend)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $trend['month_label'] }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ __('pa_acts.statistics.uploaded_anchored', [
                                            'uploaded' => $trend['uploaded'], 
                                            'anchored' => $trend['anchored']
                                        ]) }}
                                    </span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    @php
                                        $uploadPercentage = 100;
                                        $anchoredPercentage = $trend['uploaded'] > 0 
                                            ? ($trend['anchored'] / $trend['uploaded']) * 100 
                                            : 0;
                                    @endphp
                                    <div class="h-full bg-[#1B365D] relative" style="width: {{ $uploadPercentage }}%">
                                        <div class="h-full bg-green-500 absolute left-0 top-0" 
                                             style="width: {{ $anchoredPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-[#1B365D]"></div>
                            <span class="text-gray-600">{{ __('pa_acts.statistics.legend_uploaded') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-green-500"></div>
                            <span class="text-gray-600">{{ __('pa_acts.statistics.legend_anchored') }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">{{ __('pa_acts.statistics.no_data') }}</p>
                @endif
            </div>
        </div>

        {{-- Failed Acts Table (if any) --}}
        @if(!empty($recentFailed) && $recentFailed->count() > 0)
            <div class="bg-white rounded-xl border border-red-200 p-6 shadow-sm mb-8">
                <h3 class="text-lg font-semibold text-red-700 mb-4 flex items-center gap-2">
                    <span class="material-icons">error</span>
                    {{ __('pa_acts.statistics.failed_acts_title', ['count' => $recentFailed->count()]) }}
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('pa_acts.statistics.table.protocol') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('pa_acts.statistics.table.type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('pa_acts.statistics.table.attempts') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('pa_acts.statistics.table.error') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('pa_acts.statistics.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentFailed as $act)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $act->pa_protocol_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                        {{ $act->pa_act_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $act->pa_tokenization_attempts }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 max-w-md truncate">
                                        {{ Str::limit($act->pa_tokenization_error, 100) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('pa.acts.show', $act) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ __('pa_acts.statistics.table.view_details') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Info Footer --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <span class="material-icons text-blue-600 text-2xl">info</span>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">{{ __('pa_acts.statistics.info_title') }}</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• {{ __('pa_acts.statistics.info_realtime') }}</li>
                        <li>• {{ __('pa_acts.statistics.info_success_rate') }}</li>
                        <li>• {{ __('pa_acts.statistics.info_avg_time') }}</li>
                        <li>• {{ __('pa_acts.statistics.info_retry') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-pa-layout>
