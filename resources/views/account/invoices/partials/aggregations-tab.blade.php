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
                                    onclick="loadAndToggleDetails('items-{{ $aggregation->id }}', {{ $aggregation->id }}, 'items')"
                                    class="text-purple-600 hover:text-purple-700 dark:text-purple-400"
                                >
                                    <span id="icon-items-{{ $aggregation->id }}">▼</span>
                                </button>
                            </div>
                            
                            {{-- Items List - Loaded dynamically --}}
                            <div id="items-{{ $aggregation->id }}" class="hidden space-y-2 border-t border-gray-300 pt-3 dark:border-gray-600">
                                {{-- Loading spinner --}}
                                <div class="flex items-center justify-center py-4">
                                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-purple-200 border-t-purple-600"></div>
                                </div>
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
                                    onclick="loadAndToggleDetails('buyers-{{ $aggregation->id }}', {{ $aggregation->id }}, 'buyers')"
                                    class="text-purple-600 hover:text-purple-700 dark:text-purple-400"
                                >
                                    <span id="icon-buyers-{{ $aggregation->id }}">▼</span>
                                </button>
                            </div>
                            
                            {{-- Buyers List - Loaded dynamically --}}
                            <div id="buyers-{{ $aggregation->id }}" class="hidden space-y-2 border-t border-gray-300 pt-3 dark:border-gray-600">
                                {{-- Loading spinner --}}
                                <div class="flex items-center justify-center py-4">
                                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-purple-200 border-t-purple-600"></div>
                                </div>
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

