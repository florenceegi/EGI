{{-- Monthly Aggregations Tab --}}
<div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
    
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('invoices.aggregations.title') }}
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('invoices.info.monthly_aggregation_info') }}
        </p>
    </div>

    {{-- Aggregations List --}}
    @if ($aggregations->isEmpty())
        <div class="rounded-lg bg-gray-50 p-8 text-center dark:bg-gray-900">
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('invoices.messages.no_aggregations') }}
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($aggregations as $aggregation)
                <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                    
                    {{-- Header Row --}}
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    📅 {{ $aggregation->period_start->format('F Y') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $aggregation->period_start->format('d/m/Y') }} - {{ $aggregation->period_end->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                {{ $aggregation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                {{ $aggregation->status === 'invoiced' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                {{ $aggregation->status === 'exported' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                {{ $aggregation->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}
                            ">
                                {{ __('invoices.aggregations.status.' . $aggregation->status) }}
                            </span>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                € {{ number_format($aggregation->total_sales_eur, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('invoices.aggregations.total_sales') }}
                            </div>
                        </div>
                    </div>

                    {{-- Stats Row --}}
                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        
                        {{-- Items Sold Box --}}
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('invoices.aggregations.total_items') }}
                                    </div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ $aggregation->total_items }}
                                    </div>
                                </div>
                                <button 
                                    onclick="toggleDetails('items-{{ $aggregation->id }}')"
                                    class="text-purple-600 hover:text-purple-700 dark:text-purple-400"
                                >
                                    <span id="icon-items-{{ $aggregation->id }}">▼</span>
                                </button>
                            </div>
                            
                            {{-- Items List --}}
                            <div id="items-{{ $aggregation->id }}" class="hidden space-y-2 border-t border-gray-300 pt-3 dark:border-gray-600">
                                @php
                                    $distributionIds = $aggregation->metadata['distribution_ids'] ?? [];
                                    $distributions = \App\Models\PaymentDistribution::whereIn('id', $distributionIds)
                                        ->with('egi')
                                        ->get();
                                @endphp
                                
                                @foreach($distributions as $dist)
                                    @if($dist->egi)
                                        <a href="{{ route('mint.show', $dist->egi_id) }}" 
                                           class="flex items-center justify-between rounded-lg bg-white p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700">
                                            <div class="flex items-center space-x-3">
                                                @if($dist->egi->getFirstMediaUrl('images'))
                                                    <img src="{{ $dist->egi->getFirstMediaUrl('images') }}" 
                                                         alt="{{ $dist->egi->title }}"
                                                         class="h-10 w-10 rounded object-cover">
                                                @else
                                                    <div class="flex h-10 w-10 items-center justify-center rounded bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                                                        🎨
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $dist->egi->title }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        #{{ str_pad($dist->egi_id, 7, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                                € {{ number_format($dist->amount_eur, 2, ',', '.') }}
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        {{-- Buyers Box --}}
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('invoices.aggregations.total_buyers') }}
                                    </div>
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ $aggregation->total_buyers }}
                                    </div>
                                </div>
                                <button 
                                    onclick="toggleDetails('buyers-{{ $aggregation->id }}')"
                                    class="text-purple-600 hover:text-purple-700 dark:text-purple-400"
                                >
                                    <span id="icon-buyers-{{ $aggregation->id }}">▼</span>
                                </button>
                            </div>
                            
                            {{-- Buyers List --}}
                            <div id="buyers-{{ $aggregation->id }}" class="hidden space-y-2 border-t border-gray-300 pt-3 dark:border-gray-600">
                                @php
                                    $distributionIds = $aggregation->metadata['distribution_ids'] ?? [];
                                    $distributions = \App\Models\PaymentDistribution::whereIn('id', $distributionIds)
                                        ->with('egi.blockchain.buyer')
                                        ->get();
                                    
                                    // Group by buyer
                                    $buyerData = [];
                                    foreach($distributions as $dist) {
                                        if($dist->egi && $dist->egi->blockchain && $dist->egi->blockchain->buyer) {
                                            $buyer = $dist->egi->blockchain->buyer;
                                            $buyerId = $buyer->id;
                                            
                                            if(!isset($buyerData[$buyerId])) {
                                                $buyerData[$buyerId] = [
                                                    'user' => $buyer,
                                                    'count' => 0,
                                                    'total' => 0
                                                ];
                                            }
                                            
                                            $buyerData[$buyerId]['count']++;
                                            $buyerData[$buyerId]['total'] += $dist->amount_eur;
                                        }
                                    }
                                @endphp
                                
                                @forelse($buyerData as $data)
                                    <div class="flex items-center justify-between rounded-lg bg-white p-2 dark:bg-gray-800">
                                        <div class="flex items-center space-x-3">
                                            @if($data['user']->profile_image)
                                                <img src="{{ asset('storage/' . $data['user']->profile_image) }}" 
                                                     alt="{{ $data['user']->name }}"
                                                     class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                                                    {{ strtoupper(substr($data['user']->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $data['user']->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $data['count'] }} {{ $data['count'] === 1 ? 'acquisto' : 'acquisti' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">
                                            € {{ number_format($data['total'], 2, ',', '.') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-lg bg-white p-3 text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                        {{ __('invoices.aggregations.no_buyers_data') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        {{-- Invoice Code (if invoiced) --}}
                        @if ($aggregation->isInvoiced() && $aggregation->invoice)
                            <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('invoices.fields.invoice_code') }}
                                </div>
                                <div class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                    <a href="{{ route('account.invoices.show', $aggregation->invoice->id) }}" 
                                       class="hover:underline">
                                        {{ $aggregation->invoice->invoice_code }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Actions Row --}}
                    <div class="flex space-x-3">
                        @if ($aggregation->isPending())
                            <form method="POST" action="{{ route('account.invoices.aggregation.generate', $aggregation->id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">
                                    📄 {{ __('invoices.actions.generate_from_aggregation') }}
                                </button>
                            </form>
                            
                            <a href="{{ route('account.invoices.aggregation.export', ['aggregationId' => $aggregation->id, 'format' => 'csv']) }}" 
                               class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                📊 {{ __('invoices.actions.export_aggregation') }} (CSV)
                            </a>
                            
                            <a href="{{ route('account.invoices.aggregation.export', ['aggregationId' => $aggregation->id, 'format' => 'json']) }}" 
                               class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                📋 {{ __('invoices.actions.export_aggregation') }} (JSON)
                            </a>
                        @endif
                        
                        @if ($aggregation->isInvoiced() && $aggregation->invoice)
                            <a href="{{ route('account.invoices.show', $aggregation->invoice->id) }}" 
                               class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                👁️ {{ __('invoices.actions.view') }} {{ __('invoices.invoice') }}
                            </a>
                        @endif
                        
                        @if ($aggregation->isExported())
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                ✓ {{ __('invoices.aggregations.status.exported') }} ({{ strtoupper($aggregation->export_format) }})
                            </span>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
function toggleDetails(elementId) {
    const element = document.getElementById(elementId);
    const icon = document.getElementById('icon-' + elementId);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.textContent = '▲';
    } else {
        element.classList.add('hidden');
        icon.textContent = '▼';
    }
}
</script>

