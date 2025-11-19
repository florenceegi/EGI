<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">📊 {{ __('admin.consumption.title') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.consumption.subtitle') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Filters Card --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg mb-4">🔍 {{ __('common.filters') }}</h3>
                
                <form method="GET" action="{{ route('admin.consumption.summary') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    {{-- Date From --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('common.date_from') }}</span>
                        </label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="input input-bordered input-sm">
                    </div>
                    
                    {{-- Date To --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('common.date_to') }}</span>
                        </label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="input input-bordered input-sm">
                    </div>
                    
                    {{-- Billing Status --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('admin.consumption.billing_status') }}</span>
                        </label>
                        <select name="billing_status" class="select select-bordered select-sm">
                            <option value="all" {{ $billingStatus === 'all' ? 'selected' : '' }}>{{ __('common.all') }}</option>
                            <option value="pending" {{ $billingStatus === 'pending' ? 'selected' : '' }}>{{ __('admin.consumption.pending') }}</option>
                            <option value="batched" {{ $billingStatus === 'batched' ? 'selected' : '' }}>{{ __('admin.consumption.batched') }}</option>
                            <option value="charged" {{ $billingStatus === 'charged' ? 'selected' : '' }}>{{ __('admin.consumption.charged') }}</option>
                        </select>
                    </div>
                    
                    {{-- Apply Filters --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text opacity-0">-</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">
                            {{ __('common.apply_filters') }}
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>

        {{-- Summary Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Total Entries --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="stat-title">{{ __('admin.consumption.total_entries') }}</div>
                    <div class="stat-value text-primary">{{ number_format($stats['total_entries']) }}</div>
                    <div class="stat-desc">{{ __('admin.consumption.consumption_records') }}</div>
                </div>
            </div>
            
            {{-- Total Cost --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">{{ __('admin.consumption.total_cost') }}</div>
                    <div class="stat-value text-secondary">{{ number_format($stats['total_cost'], 2) }}</div>
                    <div class="stat-desc">Egili (frazionali)</div>
                </div>
            </div>
            
            {{-- Pending Debt --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">{{ __('admin.consumption.pending_debt') }}</div>
                    <div class="stat-value text-warning">{{ number_format($stats['pending_cost'], 2) }}</div>
                    <div class="stat-desc">{{ __('admin.consumption.not_charged_yet') }}</div>
                </div>
            </div>
            
            {{-- Unique Users --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.unique_users') }}</div>
                    <div class="stat-value">{{ number_format($stats['unique_users']) }}</div>
                </div>
            </div>
            
            {{-- Unique Features --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">{{ __('admin.consumption.unique_features') }}</div>
                    <div class="stat-value">{{ number_format($stats['unique_features']) }}</div>
                </div>
            </div>
            
            {{-- Charged Cost --}}
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-8 h-8 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-title">{{ __('admin.consumption.charged_cost') }}</div>
                    <div class="stat-value text-success">{{ number_format($stats['charged_cost'], 2) }}</div>
                    <div class="stat-desc">Egili (già addebitati)</div>
                </div>
            </div>
            
        </div>

        {{-- Top Features --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">🏆 {{ __('admin.consumption.top_features') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.feature') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.total_cost') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topFeatures as $feature)
                                <tr class="hover">
                                    <td>
                                        <code class="badge badge-ghost font-mono text-xs">{{ $feature->feature_code }}</code>
                                    </td>
                                    <td>{{ number_format($feature->uses) }}</td>
                                    <td>
                                        <span class="font-mono text-sm">{{ number_format($feature->total_cost, 4) }}</span>
                                        <span class="text-xs text-base-content/60">Egili</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-feature', $feature->feature_code) }}" class="btn btn-ghost btn-xs">
                                            {{ __('admin.consumption.drill_down') }} →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/60">
                                        {{ __('admin.consumption.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Users --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">👥 {{ __('admin.consumption.top_users') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.user') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.total_cost') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUsers as $userStat)
                                <tr class="hover">
                                    <td>
                                        <div class="font-medium">{{ $userStat->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-base-content/60">{{ $userStat->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ number_format($userStat->uses) }}</td>
                                    <td>
                                        <span class="font-mono text-sm">{{ number_format($userStat->total_cost, 4) }}</span>
                                        <span class="text-xs text-base-content/60">Egili</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-user', $userStat->user_id) }}" class="btn btn-ghost btn-xs">
                                            {{ __('admin.consumption.drill_down') }} →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-base-content/60">
                                        {{ __('admin.consumption.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Daily Trend Chart (Placeholder) --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">📈 {{ __('admin.consumption.daily_trend') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('common.date') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.cost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyTrend as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($day->uses) }}</td>
                                    <td class="font-mono">{{ number_format($day->cost, 4) }} Egili</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-base-content/60">
                                        {{ __('admin.consumption.no_trend_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="flex gap-4">
            <a href="{{ route('admin.consumption.by-feature') }}" class="btn btn-primary">
                📦 {{ __('admin.consumption.view_by_feature') }}
            </a>
            <a href="{{ route('admin.consumption.by-user') }}" class="btn btn-secondary">
                👥 {{ __('admin.consumption.view_by_user') }}
            </a>
        </div>

    </div>

</x-layouts.superadmin>


















