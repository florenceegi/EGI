{{-- EGILI Statement Tab Content --}}

<div class="space-y-6">

    {{-- Filters Section --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                📅 {{ __('statements.filters.title') }}
            </h3>
        </div>
        
        <form method="GET" action="{{ route('account.statements') }}" class="p-6">
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
                    <a href="{{ route('account.statements') }}"
                       class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        {{ __('statements.filters.reset') }}
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Summary Section (Bank-style) --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">
                    💎 {{ __('statements.egili.title') }}
                </h3>
                <a href="{{ route('account.statements.egili.pdf', ['filter' => $filter, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   target="_blank"
                   class="inline-flex items-center rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/30">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('statements.egili.download_pdf') }}
                </a>
            </div>
            <p class="mt-1 text-sm text-purple-100">
                {{ __('statements.egili.period', [
                    'from' => $startDate->format('d/m/Y'),
                    'to' => $endDate->format('d/m/Y')
                ]) }}
            </p>
        </div>

        <div class="grid grid-cols-2 gap-px bg-gray-200 dark:bg-gray-700 md:grid-cols-5">
            {{-- Starting Balance --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.egili.summary.starting_balance') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($egiliSummary['starting_balance']) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Egili</p>
            </div>

            {{-- Total Income --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.egili.summary.total_income') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                    +{{ number_format($egiliSummary['total_income']) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Egili</p>
            </div>

            {{-- Total Expenses --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.egili.summary.total_expenses') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">
                    -{{ number_format($egiliSummary['total_expenses']) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Egili</p>
            </div>

            {{-- Ending Balance --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.egili.summary.ending_balance') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">
                    {{ number_format($egiliSummary['ending_balance']) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Egili</p>
            </div>

            {{-- Transaction Count --}}
            <div class="bg-white px-6 py-4 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('statements.egili.summary.transaction_count') }}
                </p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $egiliSummary['transaction_count'] }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('statements.egili.summary.transaction_count') }}</p>
            </div>
        </div>
    </div>

    {{-- Transactions Table (Bank-style) --}}
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        @if($egiliTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.egili.table.date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.egili.table.description') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.egili.table.income') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.egili.table.expenses') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __('statements.egili.table.balance') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach($egiliTransactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $transaction->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $transaction->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $transaction->description ?? __('statements.egili.types.' . $transaction->type) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('statements.egili.types.' . $transaction->type) }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if($transaction->operation === 'add')
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                        +{{ number_format($transaction->amount) }} Egili
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if($transaction->operation === 'subtract')
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        -{{ number_format($transaction->amount) }} Egili
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ number_format($transaction->balance_after) }} Egili
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('statements.egili.no_transactions') }}
                </p>
            </div>
        @endif
    </div>

</div>

