<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">📦 {{ __('admin.consumption.view_by_feature') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.consumption.select_feature_drill') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Back to Summary --}}
        <div>
            <a href="{{ route('admin.consumption.summary') }}" class="btn btn-ghost btn-sm">
                ← {{ __('common.back') }}
            </a>
        </div>

        {{-- Features List --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">{{ __('admin.consumption.all_features') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.feature') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.total_cost') }}</th>
                                <th>{{ __('admin.consumption.pending_debt') }}</th>
                                <th>{{ __('admin.consumption.unique_users') }}</th>
                                <th>{{ __('admin.consumption.avg_cost') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($features as $feature)
                                <tr class="hover">
                                    <td>
                                        <code class="badge badge-ghost font-mono text-xs">{{ $feature->feature_code }}</code>
                                    </td>
                                    <td>{{ number_format($feature->total_uses) }}</td>
                                    <td>
                                        <span class="font-mono">{{ number_format($feature->total_cost, 4) }}</span>
                                        <span class="text-xs text-base-content/60">Egili</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning font-mono text-xs">
                                            {{ number_format($feature->pending_cost, 4) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($feature->unique_users) }}</td>
                                    <td class="font-mono text-xs">{{ number_format($feature->avg_cost_per_use, 6) }}</td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-feature', $feature->feature_code) }}" class="btn btn-primary btn-xs">
                                            {{ __('admin.consumption.view_details') }} →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-base-content/60">
                                        {{ __('admin.consumption.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</x-layouts.superadmin>




