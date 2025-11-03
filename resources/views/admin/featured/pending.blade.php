<x-layouts.superadmin pageTitle="{{ __('admin.featured.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">⭐ {{ __('admin.featured.title') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.featured.pending_subtitle') }}</p>
    </div>

    <div class="space-y-6">
            
            @forelse($pendingRequests as $request)
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="card-title">
                                    {{ __('admin.featured.egi_title') }}: 
                                    @php
                                        $egi = \App\Models\Egi::find($request->metadata['egi_id'] ?? null);
                                    @endphp
                                    {{ $egi->title ?? 'N/A' }}
                                </h3>
                                <div class="text-sm text-base-content/60 mt-1">
                                    {{ __('admin.featured.creator') }}: {{ $request->user->name ?? 'N/A' }}
                                </div>
                            </div>
                            
                            <span class="badge badge-lg badge-warning">
                                {{ strtoupper($request->metadata['feature_type'] ?? 'featured') }}
                            </span>
                        </div>
                        
                        {{-- Request Details --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <div class="text-xs text-base-content/60">{{ __('admin.featured.requested_period') }}</div>
                                <div class="font-medium">
                                    {{ $request->scheduled_slot_start?->format('d/m/Y') }} - 
                                    {{ $request->scheduled_slot_end?->format('d/m/Y') }}
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-base-content/60">{{ __('admin.featured.egili_reserved') }}</div>
                                <div class="font-semibold text-warning">
                                    {{ number_format($request->egili_reserved) }} Egili
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-base-content/60">{{ __('admin.featured.requested_at') }}</div>
                                <div class="text-sm">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="card-actions justify-end mt-4">
                            <button onclick="openApproveModal({{ $request->id }})" class="btn btn-success btn-sm">
                                {{ __('admin.featured.approve') }}
                            </button>
                            <button onclick="openRejectModal({{ $request->id }})" class="btn btn-error btn-sm">
                                {{ __('admin.featured.reject') }}
                            </button>
                        </div>
                        
                    </div>
                </div>
            @empty
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center py-12 text-base-content/60">
                        {{ __('admin.featured.no_pending') }}
                    </div>
                </div>
            @endforelse
            

    </div>
</x-layouts.superadmin>

<script>
function openApproveModal(requestId) {
    alert('{{ __('admin.featured.approve_todo') }} ' + requestId);
}

function openRejectModal(requestId) {
    alert('{{ __('admin.featured.reject_todo') }} ' + requestId);
}
</script>



