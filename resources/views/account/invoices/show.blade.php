<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('invoices.invoice') }} {{ $invoice->invoice_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            
            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('account.invoices') }}" 
                   class="inline-flex items-center text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                    ← {{ __('common.back') }}
                </a>
            </div>

            {{-- Invoice Header --}}
            <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $invoice->invoice_code }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.types.' . $invoice->invoice_type) }}
                        </p>
                    </div>
                    
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                        {{ $invoice->invoice_status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                        {{ $invoice->invoice_status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                        {{ $invoice->invoice_status === 'sent' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                        {{ $invoice->invoice_status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}
                    ">
                        {{ __('invoices.status.' . $invoice->invoice_status) }}
                    </span>
                </div>

                {{-- Parties --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('invoices.fields.seller') }}
                        </h3>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $invoice->seller->name }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $invoice->seller->email }}
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('invoices.fields.buyer') }}
                        </h3>
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                            @if ($invoice->buyer)
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $invoice->buyer->name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $invoice->buyer->email }}
                                </p>
                            @else
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ __('common.multiple') }} ({{ __('invoices.aggregations.title') }})
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('invoices.fields.issue_date') }}
                        </p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $invoice->issue_date->format('d/m/Y') }}
                        </p>
                    </div>
                    
                    @if ($invoice->due_date)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('invoices.fields.due_date') }}
                            </p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </p>
                        </div>
                    @endif
                    
                    @if ($invoice->payment_date)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('invoices.fields.payment_date') }}
                            </p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $invoice->payment_date->format('d/m/Y') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Invoice Items --}}
            <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('invoices.items_title') }}
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('common.description') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('common.quantity') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('common.unit_price') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('invoices.fields.tax_amount') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('invoices.fields.total') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        @if ($item->code)
                                            <span class="font-mono text-xs text-gray-500">{{ $item->code }}</span>
                                            <br>
                                        @endif
                                        {{ $item->description }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($item->quantity, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        € {{ number_format($item->unit_price_eur, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                        € {{ number_format($item->tax_amount_eur, 2, ',', '.') }}
                                        <span class="text-xs">({{ number_format($item->tax_rate, 0) }}%)</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        € {{ number_format($item->total_eur, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('invoices.fields.subtotal') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    € {{ number_format($invoice->subtotal_eur, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('invoices.fields.tax_amount') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    € {{ number_format($invoice->tax_amount_eur, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">
                                    {{ __('invoices.fields.total') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-lg font-bold text-purple-600 dark:text-purple-400">
                                    € {{ number_format($invoice->total_eur, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-3">
                @if ($invoice->pdf_path)
                    <a href="{{ route('account.invoices.download.pdf', $invoice->id) }}" 
                       class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
                        📄 {{ __('invoices.actions.download_pdf') }}
                    </a>
                @endif
                
                <a href="{{ route('account.invoices') }}" 
                   class="rounded-lg border border-gray-300 bg-white px-6 py-3 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('common.back') }}
                </a>
            </div>

        </div>
    </div>

</x-app-layout>

