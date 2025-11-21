{{-- EUR Statement Tab Content --}}

<div class="space-y-6">

    {{-- Filters Section (same as EGILI) --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                📅 {{ __('statements.filters.title') }}
            </h3>
        </div>
        
        <form method="GET" action="{{ route('account.statements') }}" class="p-6">
            <input type="hidden" name="tab" value="eur">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-5">
                
                {{-- Quick Filters --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('statements.filters.title') }}
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="filter" value="today" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                       {{ $filter === 'today' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            {{ __('statements.filters.today') }}
                        </button>
                        <button type="submit" name="filter" value="week" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                       {{ $filter === 'week' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            {{ __('statements.filters.week') }}
                        </button>
                        <button type="submit" name="filter" value="month" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                       {{ $filter === 'month' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            {{ __('statements.filters.month') }}
                        </button>
                        <button type="submit" name="filter" value="year" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                       {{ $filter === 'year' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            {{ __('statements.filters.year') }}
                        </button>
                    </div>
                </div>

                {{-- Custom Date Range --}}
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('statements.filters.date_from') }}
                    </label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('statements.filters.date_to') }}
                    </label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" name="filter" value="custom"
                            class="flex-1 rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 px-4 py-2 text-sm font-medium text-white transition-all hover:from-purple-700 hover:to-blue-700 hover:shadow-lg">
                        {{ __('statements.filters.apply') }}
                    </button>
                    <a href="{{ route('account.statements') }}?tab=eur"
                       class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        {{ __('statements.filters.reset') }}
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Summary Section (Bank-style) --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-gradient-to-r from-green-600 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        💰 {{ __('statements.eur.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-green-100">
                        {{ __('statements.pdf.account_holder') }}: <span class="font-semibold">{{ $user->name }}</span>
                    </p>
                </div>
                <a href="{{ route('account.statements.eur.pdf', ['filter' => $filter, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   target="_blank"
                   class="inline-flex items-center rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/30">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('statements.eur.download_pdf') }}
                </a>
            </div>
            <p class="mt-1 text-sm text-green-100">
                {{ __('statements.eur.period', [
                    'from' => $startDate->format('d/m/Y'),
                    'to' => $endDate->format('d/m/Y')
                ]) }}
            </p>
        </div>

        <div class="grid grid-cols-2 gap-px bg-gray-200 dark:bg-gray-700 md:grid-cols-4">
            {{-- Total Expenses --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.eur.summary.total_expenses') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">
                    €{{ number_format($eurSummary['total_expenses'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $eurSummary['expense_count'] }} transazioni</p>
            </div>

            {{-- Total Income --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.eur.summary.total_income') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                    €{{ number_format($eurSummary['total_income'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $eurSummary['income_count'] }} transazioni</p>
            </div>

            {{-- Net Balance --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.eur.summary.net_balance') }}
                </p>
                <p class="mt-2 text-2xl font-bold {{ $eurSummary['net_balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    €{{ number_format($eurSummary['net_balance'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Saldo netto</p>
            </div>

            {{-- Transaction Count --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.eur.summary.transaction_count') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $eurSummary['transaction_count'] }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Movimenti totali</p>
            </div>
        </div>
    </div>

    {{-- Transactions Table (Bank-style with Merchant) --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        @if($eurTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.description') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.merchant') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.beneficiary') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.expenses') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.income') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.eur.table.reference') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach($eurTransactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $transaction['transaction_date']->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $transaction['transaction_date']->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if(isset($transaction['metadata']['egi_id']))
                                        <a href="{{ route('egis.show', $transaction['metadata']['egi_id']) }}" 
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline"
                                           target="_blank">
                                            {{ $transaction['description'] }}
                                            <svg class="inline-block ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @else
                                        {{ $transaction['description'] }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('statements.eur.types.' . $transaction['type']) }}
                                </div>
                                @if(isset($transaction['metadata']['is_split_payment']) && $transaction['metadata']['is_split_payment'])
                                    <div class="mt-1 text-xs italic text-blue-600 dark:text-blue-400">
                                        ↳ {{ $transaction['metadata']['split_breakdown'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $transaction['merchant'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                    {{ $transaction['beneficiary'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if($transaction['operation'] === 'expense')
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        -€{{ number_format($transaction['amount_eur'], 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if($transaction['operation'] === 'income')
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                        +€{{ number_format($transaction['amount_eur'], 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                    {{ $transaction['reference'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('statements.eur.no_transactions') }}
                </p>
            </div>
        @endif
    </div>

</div>

