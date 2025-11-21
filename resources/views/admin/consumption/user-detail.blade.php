<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">👤 {{ $user->name }}</h1>
        <p class="text-base-content/60 mt-2">{{ $user->email }} - {{ __('admin.consumption.user_consumption_detail') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Breadcrumb --}}
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.consumption.summary') }}">Summary</a></li>
                <li><a href="{{ route('admin.consumption.by-user') }}">Users</a></li>
                <li>{{ $user->name }}</li>
            </ul>
        </div>

        {{-- User Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.uses') }}</div>
                    <div class="stat-value text-primary">{{ number_format($userStats['total_uses']) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.total_cost') }}</div>
                    <div class="stat-value text-secondary text-lg">{{ number_format($userStats['total_cost'], 2) }}</div>
                    <div class="stat-desc">Egili</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.pending_debt') }}</div>
                    <div class="stat-value text-warning text-lg">{{ number_format($userStats['pending_cost'], 2) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.avg_cost') }}</div>
                    <div class="stat-value text-info text-lg">{{ number_format($userStats['avg_cost_per_use'], 4) }}</div>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.features_used') }}</div>
                    <div class="stat-value">{{ number_format($userStats['features_used']) }}</div>
                </div>
            </div>
        </div>

        {{-- Features Breakdown --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">📦 {{ __('admin.consumption.features_for_user') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.feature') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.cost') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($featureBreakdown as $featureStat)
                                <tr class="hover">
                                    <td>
                                        <code class="badge badge-ghost font-mono text-xs">{{ $featureStat->feature_code }}</code>
                                    </td>
                                    <td>{{ number_format($featureStat->uses) }}</td>
                                    <td class="font-mono">{{ number_format($featureStat->cost, 4) }} Egili</td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-feature', $featureStat->feature_code) }}" class="btn btn-ghost btn-xs">
                                            {{ __('admin.consumption.view_feature') }} →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/60">{{ __('admin.consumption.no_features') }}</td>
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
                                <th>{{ __('admin.consumption.feature') }}</th>
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
                                    <td><code class="text-xs">{{ $entry->feature_code }}</code></td>
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
























