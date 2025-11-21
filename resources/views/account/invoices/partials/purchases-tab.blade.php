{{-- Purchase Invoices Tab --}}
<div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
    
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('invoices.tabs.purchases') }}
        </h3>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('account.invoices') }}" class="mb-6">
        <input type="hidden" name="tab" value="purchases">
        
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('invoices.fields.invoice_status') }}
                </label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('common.all') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('invoices.status.pending') }}</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('invoices.status.paid') }}</option>
                </select>
            </div>
            
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('common.from_date') }}
                </label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('common.to_date') }}
                </label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-lg bg-purple-600 px-4 py-2 text-white hover:bg-purple-700">
                    {{ __('common.filter') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Invoices List --}}
    @if ($invoices->isEmpty())
        <div class="rounded-lg bg-gray-50 p-8 text-center dark:bg-gray-900">
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('invoices.messages.no_invoices') }}
            </p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('invoices.fields.invoice_code') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('invoices.fields.seller') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('invoices.fields.issue_date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('invoices.fields.total') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('invoices.fields.invoice_status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @foreach ($invoices as $invoice)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $invoice->invoice_code }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $invoice->seller->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $invoice->issue_date->format('d/m/Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                € {{ number_format($invoice->total_eur, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5
                                    {{ $invoice->invoice_status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                    {{ $invoice->invoice_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                ">
                                    {{ __('invoices.status.' . $invoice->invoice_status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('account.invoices.show', $invoice->id) }}" 
                                   class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                    {{ __('invoices.actions.view') }}
                                </a>
                                
                                @if ($invoice->pdf_path)
                                    <span class="mx-1 text-gray-300">|</span>
                                    <a href="{{ route('account.invoices.download.pdf', $invoice->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('invoices.actions.download_pdf') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

