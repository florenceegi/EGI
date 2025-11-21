<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">👥 {{ __('admin.consumption.view_by_user') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.consumption.select_user_drill') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Back to Summary --}}
        <div>
            <a href="{{ route('admin.consumption.summary') }}" class="btn btn-ghost btn-sm">
                ← {{ __('common.back') }}
            </a>
        </div>

        {{-- Users List --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">{{ __('admin.consumption.all_users') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.consumption.user') }}</th>
                                <th>{{ __('admin.consumption.uses') }}</th>
                                <th>{{ __('admin.consumption.total_cost') }}</th>
                                <th>{{ __('admin.consumption.pending_debt') }}</th>
                                <th>{{ __('admin.consumption.features_used') }}</th>
                                <th>{{ __('admin.consumption.last_consumption') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $userStat)
                                <tr class="hover">
                                    <td>
                                        <div class="font-medium">{{ $userStat->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-base-content/60">{{ $userStat->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ number_format($userStat->total_uses) }}</td>
                                    <td>
                                        <span class="font-mono">{{ number_format($userStat->total_cost, 4) }}</span>
                                        <span class="text-xs text-base-content/60">Egili</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning font-mono text-xs">
                                            {{ number_format($userStat->pending_cost, 4) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($userStat->features_used) }}</td>
                                    <td class="text-xs">{{ \Carbon\Carbon::parse($userStat->last_consumption)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.consumption.by-user', $userStat->user_id) }}" class="btn btn-primary btn-xs">
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























