<x-layouts.superadmin pageTitle="{{ __('admin.egili.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">💎 {{ __('admin.egili.title') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.egili.subtitle') }}</p>

    <div class="space-y-6">
            
            {{-- Grant Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Grant Lifetime --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">{{ __('admin.egili.grant_lifetime') }}</h3>
                        <p class="text-sm text-base-content/60 mb-4">
                            {{ __('admin.egili.grant_lifetime_desc') }}
                        </p>
                        <button onclick="openGrantLifetimeModal()" class="btn btn-primary">
                            {{ __('admin.egili.grant') }}
                        </button>
                    </div>
                </div>
                
                {{-- Grant Gift --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">{{ __('admin.egili.grant_gift') }}</h3>
                        <p class="text-sm text-base-content/60 mb-4">
                            {{ __('admin.egili.grant_gift_desc') }}
                        </p>
                        <button onclick="openGrantGiftModal()" class="btn btn-secondary">
                            {{ __('admin.egili.grant') }}
                        </button>
                    </div>
                </div>
                
            </div>
            
            {{-- Recent Transactions --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title mb-4">{{ __('admin.egili.recent_transactions') }}</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>{{ __('admin.egili.user') }}</th>
                                    <th>{{ __('admin.egili.type') }}</th>
                                    <th>{{ __('admin.egili.amount') }}</th>
                                    <th>{{ __('admin.egili.reason') }}</th>
                                    <th>{{ __('admin.egili.expires') }}</th>
                                    <th>{{ __('admin.egili.granted_by') }}</th>
                                    <th>{{ __('admin.egili.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ $tx->user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-base-content/60">{{ $tx->user->email ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $tx->egili_type === 'gift' ? 'badge-secondary' : 'badge-primary' }}">
                                                {{ ucfirst($tx->egili_type) }}
                                            </span>
                                        </td>
                                        <td class="font-semibold {{ $tx->operation === 'add' ? 'text-success' : 'text-error' }}">
                                            {{ $tx->signed_amount }} Egili
                                        </td>
                                        <td class="text-sm">{{ $tx->reason }}</td>
                                        <td class="text-sm">
                                            @if($tx->expires_at)
                                                {{ $tx->expires_at->format('d/m/Y') }}
                                                @if($tx->is_expired)
                                                    <span class="badge badge-error badge-xs">{{ __('admin.egili.expired') }}</span>
                                                @endif
                                            @else
                                                <span class="text-base-content/40">{{ __('admin.egili.never') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-sm">{{ $tx->grantedByAdmin->name ?? '-' }}</td>
                                        <td class="text-xs text-base-content/60">
                                            {{ $tx->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/60">
                                            {{ __('admin.egili.no_transactions') }}
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

<script>
function openGrantLifetimeModal() {
    alert('{{ __('admin.egili.grant_lifetime_todo') }}');
}

function openGrantGiftModal() {
    alert('{{ __('admin.egili.grant_gift_todo') }}');
}
</script>



