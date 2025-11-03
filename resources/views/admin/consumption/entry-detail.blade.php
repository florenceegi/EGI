<x-layouts.superadmin pageTitle="{{ __('admin.consumption.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">🔍 {{ __('admin.consumption.entry_detail') }} #{{ $entry->id }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.consumption.single_consumption_record') }}</p>
    </div>

    <div class="space-y-6">
        
        {{-- Breadcrumb --}}
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.consumption.summary') }}">Summary</a></li>
                <li><a href="{{ route('admin.consumption.by-user', $entry->user_id) }}">{{ $entry->user->name ?? 'User' }}</a></li>
                <li>Entry #{{ $entry->id }}</li>
            </ul>
        </div>

        {{-- Entry Details Card --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">📊 {{ __('admin.consumption.consumption_details') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- Left Column --}}
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.user') }}</div>
                            <div class="font-medium">{{ $entry->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-base-content/60">{{ $entry->user->email ?? 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.feature') }}</div>
                            <code class="badge badge-ghost font-mono">{{ $entry->feature_code }}</code>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.consumption_type') }}</div>
                            <span class="badge badge-primary">{{ $entry->consumption_type }}</span>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.units_consumed') }}</div>
                            <div class="font-mono text-lg">{{ number_format($entry->units_consumed, 4) }} {{ $entry->unit_type }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.cost_per_unit') }}</div>
                            <div class="font-mono text-sm">{{ number_format($entry->cost_per_unit, 8) }} Egili</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.total_cost') }}</div>
                            <div class="font-mono text-xl font-bold text-secondary">{{ number_format($entry->total_cost_egili, 6) }} Egili</div>
                        </div>
                    </div>
                    
                    {{-- Right Column --}}
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.billing_status') }}</div>
                            <span class="badge 
                                @if($entry->billing_status === 'pending') badge-warning badge-lg
                                @elseif($entry->billing_status === 'batched') badge-info badge-lg
                                @else badge-success badge-lg
                                @endif">
                                {{ $entry->billing_status }}
                            </span>
                        </div>
                        
                        @if($entry->batched_in_transaction_id)
                            <div>
                                <div class="text-sm text-base-content/60">{{ __('admin.consumption.transaction_id') }}</div>
                                <a href="#" class="link link-primary font-mono">
                                    #{{ $entry->batched_in_transaction_id }}
                                </a>
                            </div>
                        @endif
                        
                        @if($entry->charged_at)
                            <div>
                                <div class="text-sm text-base-content/60">{{ __('admin.consumption.charged_at') }}</div>
                                <div class="text-sm">{{ $entry->charged_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        @endif
                        
                        <div>
                            <div class="text-sm text-base-content/60">{{ __('admin.consumption.consumed_at') }}</div>
                            <div class="text-sm">{{ $entry->consumed_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">IP Address</div>
                            <div class="font-mono text-xs">{{ $entry->ip_address ?? 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-base-content/60">User Agent</div>
                            <div class="text-xs text-base-content/70 break-all">{{ Str::limit($entry->user_agent ?? 'N/A', 100) }}</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- Request Metadata (JSON) --}}
        @if($entry->request_metadata)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title">🔧 {{ __('admin.consumption.request_metadata') }}</h3>
                    
                    <div class="bg-base-200 rounded-lg p-4">
                        <pre class="text-xs overflow-x-auto">{{ json_encode($entry->request_metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif>

    </div>

</x-layouts.superadmin>


