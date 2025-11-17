<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">📦 {{ $featureCode }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.consumption.feature_consumption_detail') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Breadcrumb --}}
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.consumption.summary') }}">Summary</a></li>
                <li><a href="{{ route('admin.consumption.by-feature') }}">Features</a></li>
                <li>{{ $featureCode }}</li>
            </ul>
        </div>

        {{-- Feature Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.uses') }}</div>
                    <div class="stat-value text-primary">{{ number_format($featureStats['total_uses']) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.total_cost') }}</div>
                    <div class="stat-value text-secondary text-lg">{{ number_format($featureStats['total_cost'], 2) }}</div>
                    <div class="stat-desc">Egili</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.pending_debt') }}</div>
                    <div class="stat-value text-warning text-lg">{{ number_format($featureStats['pending_cost'], 2) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.avg_cost') }}</div>
                    <div class="stat-value text-info text-lg">{{ number_format($featureStats['avg_cost'], 4) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.unique_users') }}</div>
                    <div class="stat-value">{{ number_format($featureStats['unique_users']) }}</div>
                </div>
            </div>
        </div>

        {{-- Users Breakdown --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">👥 {{ __('admin.consumption.users_for_feature') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.user') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.cost') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userBreakdown as $userStat)
                                <tr class="hover">
                                    <td>
                                        <div class="font-medium">{{ $userStat->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-base-content/60">{{ $userStat->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ number_format($userStat->uses) }}</td>
                                    <td class="font-mono">{{ number_format($userStat->cost, 4) }} Egili</td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-user', $userStat->user_id) }}" class="btn btn-ghost btn-xs">
                                            {{ __('admin.consumption.view_user') }} →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/60">{{ __('admin.consumption.no_users') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Entries --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">📋 {{ __('admin.consumption.recent_entries') }} ({{ __('admin.consumption.last_50') }})</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-xs">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('admin.consumption.user') }}</th>
                                <th>{{ __('admin.consumption.consumed_at') }}</th>
                                <th>{{ __('admin.consumption.units') }}</th>
                                <th>{{ __('admin.consumption.cost') }}</th>
                                <th>{{ __('admin.consumption.billing_status') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEntries as $entry)
                                <tr class="hover">
                                    <td class="font-mono text-xs">{{ $entry->id }}</td>
                                    <td class="text-xs">{{ $entry->user->name ?? 'N/A' }}</td>
                                    <td class="text-xs">{{ $entry->consumed_at->format('d/m/Y H:i') }}</td>
                                    <td class="font-mono text-xs">{{ number_format($entry->units_consumed, 2) }} {{ $entry->unit_type }}</td>
                                    <td class="font-mono text-xs">{{ number_format($entry->total_cost_egili, 6) }}</td>
                                    <td>
                                        <span class="badge badge-xs 
                                            @if($entry->billing_status === 'pending') badge-warning
                                            @elseif($entry->billing_status === 'batched') badge-info
                                            @else badge-success
                                            @endif">
                                            {{ $entry->billing_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.consumption.entry-detail', $entry->id) }}" class="btn btn-ghost btn-xs">
                                            🔍
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-base-content/60">{{ __('admin.consumption.no_entries') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</x-layouts.superadmin>

















