<x-layouts.superadmin pageTitle="{{ __('admin.pricing.title') }}">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-4xl font-bold text-base-content">💰 {{ __('admin.pricing.title') }}</h1>
            <button 
                onclick="openCreateModal()"
                class="btn btn-primary btn-sm gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('admin.pricing.create_new') }}
            </button>
        </div>
    </div>

    <div class="space-y-6">
            
            {{-- Filters --}}
            <div class="bg-base-100 shadow-xl rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.pricing.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        {{-- Search --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">{{ __('admin.pricing.search') }}</span>
                            </label>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="{{ __('admin.pricing.search_placeholder') }}"
                                class="input input-bordered"
                            >
                        </div>
                        
                        {{-- Category Filter --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">{{ __('admin.pricing.category') }}</span>
                            </label>
                            <select name="category" class="select select-bordered">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Feature Type Filter --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">{{ __('admin.pricing.feature_type') }}</span>
                            </label>
                            <select name="feature_type" class="select select-bordered">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="lifetime" {{ request('feature_type') == 'lifetime' ? 'selected' : '' }}>
                                    {{ __('admin.pricing.types.lifetime') }}
                                </option>
                                <option value="consumable" {{ request('feature_type') == 'consumable' ? 'selected' : '' }}>
                                    {{ __('admin.pricing.types.consumable') }}
                                </option>
                                <option value="temporal" {{ request('feature_type') == 'temporal' ? 'selected' : '' }}>
                                    {{ __('admin.pricing.types.temporal') }}
                                </option>
                            </select>
                        </div>
                        
                        {{-- Status Filter --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">{{ __('admin.pricing.status') }}</span>
                            </label>
                            <select name="status" class="select select-bordered">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>
                                    {{ __('common.all') }}
                                </option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                    {{ __('common.active') }}
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('common.inactive') }}
                                </option>
                            </select>
                        </div>
                        
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-neutral">
                            {{ __('common.apply_filters') }}
                        </button>
                    </div>
                    
                </form>
            </div>
            
            {{-- Features Table --}}
            <div class="bg-base-100 shadow-xl rounded-lg overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead class="bg-base-200 text-base-content">
                            <tr>
                                <th class="bg-base-200">{{ __('admin.pricing.feature_code') }}</th>
                                <th class="bg-base-200">{{ __('admin.pricing.name') }}</th>
                                <th class="bg-base-200">{{ __('admin.pricing.type') }}</th>
                                <th class="bg-base-200">{{ __('admin.pricing.cost_egili') }}</th>
                                <th class="bg-base-200">{{ __('admin.pricing.category') }}</th>
                                <th class="bg-base-200">{{ __('common.status') }}</th>
                                <th class="bg-base-200 w-32">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($features as $feature)
                                <tr class="hover">
                                    <td>
                                        <code class="badge badge-ghost font-mono text-xs">{{ $feature->feature_code }}</code>
                                    </td>
                                    <td>
                                        <div class="font-medium">{{ $feature->feature_name }}</div>
                                        @if($feature->feature_description)
                                            <div class="text-xs text-base-content/60 mt-1">
                                                {{ Str::limit($feature->feature_description, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($feature->feature_type === 'lifetime') badge-success
                                            @elseif($feature->feature_type === 'consumable') badge-info
                                            @else badge-secondary
                                            @endif
                                        ">
                                            {{ __('admin.pricing.types.' . $feature->feature_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-semibold text-warning">
                                            {{ number_format($feature->cost_egili) }} Egili
                                        </span>
                                        @if($feature->discount_percentage > 0)
                                            <span class="badge badge-sm badge-success ml-2">
                                                -{{ $feature->discount_percentage }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-outline">{{ ucfirst($feature->category ?? '-') }}</span>
                                    </td>
                                    <td>
                                        @if($feature->is_active)
                                            <span class="badge badge-success">{{ __('common.active') }}</span>
                                        @else
                                            <span class="badge badge-ghost">{{ __('common.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button 
                                                onclick="openEditModal({{ $feature->id }})"
                                                class="btn btn-ghost btn-sm"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </button>
                                            
                                            <form 
                                                method="POST" 
                                                action="{{ route('admin.pricing.toggle', $feature->id) }}"
                                                class="inline"
                                                onsubmit="return confirm('{{ __('admin.pricing.confirm_toggle') }}')"
                                            >
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-sm">
                                                    @if($feature->is_active)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-base-content/60">
                                        {{ __('admin.pricing.no_features') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="p-4 border-t border-base-300">
                    {{ $features->links() }}
                </div>
                
            </div>

    </div>

</x-layouts.superadmin>

<script>
function openCreateModal() {
    alert('{{ __('admin.pricing.create_feature_todo') }}');
    // TODO: Implement create modal in next iteration
}

function openEditModal(featureId) {
    alert('{{ __('admin.pricing.edit_feature_todo') }} ' + featureId);
    // TODO: Load feature data and show modal in next iteration
}
</script>
