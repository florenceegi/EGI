<x-layouts.superadmin pageTitle="{{ __('admin.promotions.title') }}">

    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">🎁 {{ __('admin.promotions.title') }}</h1>
        <p class="text-base-content/60 mt-2">{{ __('admin.promotions.subtitle') }}</p>
    </div>

    <div class="space-y-6">
            
            {{-- Status Tabs --}}
            <div class="tabs tabs-boxed mb-6">
                <a href="{{ route('admin.promotions.index', ['status' => 'active']) }}" 
                   class="tab {{ request('status', 'active') === 'active' ? 'tab-active' : '' }}">
                    {{ __('admin.promotions.active') }}
                </a>
                <a href="{{ route('admin.promotions.index', ['status' => 'upcoming']) }}" 
                   class="tab {{ request('status') === 'upcoming' ? 'tab-active' : '' }}">
                    {{ __('admin.promotions.upcoming') }}
                </a>
                <a href="{{ route('admin.promotions.index', ['status' => 'expired']) }}" 
                   class="tab {{ request('status') === 'expired' ? 'tab-active' : '' }}">
                    {{ __('admin.promotions.expired') }}
                </a>
                <a href="{{ route('admin.promotions.index', ['status' => 'all']) }}" 
                   class="tab {{ request('status') === 'all' ? 'tab-active' : '' }}">
                    {{ __('common.all') }}
                </a>
            </div>
            
            {{-- Promotions Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @forelse($promotions as $promo)
                    <div class="card bg-base-100 shadow-xl {{ $promo->is_featured ? 'ring-2 ring-warning' : '' }}">
                        <div class="card-body">
                            
                            {{-- Header --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="card-title text-base">{{ $promo->promo_name }}</h3>
                                    <code class="text-xs text-base-content/60">{{ $promo->promo_code }}</code>
                                </div>
                                @if($promo->is_featured)
                                    <span class="badge badge-warning">{{ __('admin.promotions.featured') }}</span>
                                @endif
                            </div>
                            
                            {{-- Discount Badge --}}
                            <div class="text-center py-4 bg-gradient-to-r from-warning/10 to-error/10 rounded-lg">
                                <div class="text-3xl font-bold text-warning">
                                    {{ $promo->discount_display }}
                                </div>
                                <div class="text-xs text-base-content/60">
                                    {{ __('admin.promotions.discount') }}
                                </div>
                            </div>
                            
                            {{-- Details --}}
                            <div class="space-y-2 mt-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-base-content/60">{{ __('admin.promotions.scope') }}:</span>
                                    <span class="font-medium">
                                        @if($promo->is_global)
                                            <span class="badge badge-primary badge-sm">{{ __('admin.promotions.global') }}</span>
                                        @else
                                            <code class="text-xs">{{ $promo->feature_code }}</code>
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-base-content/60">{{ __('admin.promotions.period') }}:</span>
                                    <span class="text-xs">
                                        {{ $promo->start_at?->format('d/m/Y') }} - {{ $promo->end_at?->format('d/m/Y') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-base-content/60">{{ __('admin.promotions.uses') }}:</span>
                                    <span class="font-medium">
                                        {{ $promo->current_uses }} / {{ $promo->max_uses ?? '∞' }}
                                    </span>
                                </div>
                                
                                @if($promo->total_egili_saved > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-base-content/60">{{ __('admin.promotions.savings') }}:</span>
                                        <span class="text-success font-medium">
                                            {{ number_format($promo->total_egili_saved) }} Egili
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Actions --}}
                            <div class="card-actions justify-end mt-4">
                                @if($promo->is_active)
                                    <form method="POST" action="{{ route('admin.promotions.deactivate', $promo->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">
                                            {{ __('common.deactivate') }}
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.promotions.activate', $promo->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            {{ __('common.activate') }}
                                        </button>
                                    </form>
                                @endif
                                
                                <button onclick="viewPromoStats({{ $promo->id }})" class="btn btn-ghost btn-sm">
                                    {{ __('admin.promotions.view_stats') }}
                                </button>
                            </div>
                            
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-base-content/60">
                        {{ __('admin.promotions.no_promotions') }}
                    </div>
                @endforelse
                
            </div>
            
            {{-- Pagination --}}
            @if($promotions->hasPages())
                <div class="mt-6">
                    {{ $promotions->links() }}
                </div>
            @endif
            
        </div>
    </div>

    </div>
</x-layouts.superadmin>

<script>
function openCreatePromoModal() {
    alert('{{ __('admin.promotions.create_todo') }}');
}

function viewPromoStats(promoId) {
    alert('{{ __('admin.promotions.stats_todo') }} ' + promoId);
}
</script>



