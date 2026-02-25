{{-- Egili Wallet Card for Navigation Menu --}}
@php
    use App\Services\EgiliService;

    $egiliService = app(EgiliService::class);
    $currentBalance = Auth::check() ? $egiliService->getBalance(Auth::user()) : 0;
    $recentTransactions = Auth::check() ? $egiliService->getTransactionHistory(Auth::user(), 5) : collect([]);
@endphp

<div
    class="mega-card rounded-2xl border border-purple-200/30 bg-gradient-to-br from-purple-50 to-blue-50 p-4 dark:border-purple-800/30 dark:from-purple-900/20 dark:to-blue-900/20">

    {{-- Header --}}
    <div class="mb-3 flex items-center space-x-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-blue-500">
            <span class="text-xl">💎</span>
        </div>
        <h4 class="font-semibold text-gray-100">{{ __('egili.wallet.title') }}</h4>
    </div>

    {{-- Current Balance --}}
    <div class="mb-4 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 p-4">
        <div class="text-center">
            <p class="mb-1 text-xs text-purple-200">{{ __('egili.wallet.current_balance') }}</p>
            <p class="text-4xl font-bold text-white">{{ number_format($currentBalance) }}</p>
            <p class="text-sm text-purple-200">Egili</p>
        </div>
    </div>

    {{-- ToS v3.0.0: bottone acquisto Egili RIMOSSO — gli Egili si ottengono solo via Pacchetti AI o merit reward --}}

    {{-- Recent Transactions (Collapsible) --}}
    @if ($recentTransactions->count() > 0)
        <details class="group">
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-2 py-1.5 text-sm text-gray-300 transition-colors hover:bg-black/20 hover:text-purple-400">
                <span>📊 {{ __('egili.wallet.recent_transactions') }}</span>
                <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-2 max-h-60 space-y-1.5 overflow-y-auto">
                @foreach ($recentTransactions as $transaction)
                    <div class="flex items-center justify-between rounded-lg bg-black/10 px-3 py-2 text-xs">
                        <div class="flex-1">
                            <p
                                class="{{ $transaction->operation === 'add' ? 'text-green-400' : 'text-red-400' }} font-medium">
                                {{ $transaction->signed_amount }} Egili
                            </p>
                            <p class="max-w-[120px] truncate text-[10px] text-gray-400">
                                {{ $transaction->type_description }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400">
                                {{ $transaction->created_at->format('d/m') }}
                            </p>
                            <p class="text-[10px] text-gray-500">
                                {{ $transaction->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All Link --}}
            @if ($recentTransactions->count() >= 5)
                <a href="{{ route('dashboard') }}#egili-transactions"
                    class="mt-2 block text-center text-xs text-purple-400 transition-colors hover:text-purple-300">
                    {{ __('egili.wallet.view_all') }} →
                </a>
            @endif
        </details>
    @else
        <div class="rounded-lg bg-black/10 px-3 py-2 text-center text-xs text-gray-400">
            {{ __('egili.wallet.no_transactions') }}
        </div>
    @endif

</div>
