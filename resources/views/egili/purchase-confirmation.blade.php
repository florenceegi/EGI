<x-platform-layout :title="__('egili.confirmation.title')">

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50 py-12">
        <div class="container mx-auto max-w-5xl px-4">

            {{-- Success Header --}}
            <div class="mb-12 text-center">
                <div
                    class="mb-6 inline-flex h-20 w-20 animate-bounce items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="mb-3 text-4xl font-bold text-gray-900">
                    {{ __('egili.confirmation.title') }}
                </h1>
                <p class="text-lg text-gray-600">
                    {{ __('egili.confirmation.thank_you') }}
                </p>

                @if (config('egili.notifications.send_purchase_confirmation', true))
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('egili.confirmation.email_sent', ['email' => Auth::user()->email]) }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                {{-- Left Column: Order Summary --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Order Reference --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ __('egili.confirmation.order_reference') }}
                            </h2>
                            <span
                                class="@if ($purchase->isCompleted()) bg-green-100 text-green-800
                                @elseif($purchase->isPending()) bg-yellow-100 text-yellow-800
                                @elseif($purchase->isFailed()) bg-red-100 text-red-800 @endif inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold">
                                @if ($purchase->isCompleted())
                                    {{ __('egili.confirmation.status_completed') }}
                                @elseif($purchase->isPending())
                                    {{ __('egili.confirmation.status_pending') }}
                                @elseif($purchase->isFailed())
                                    {{ __('egili.confirmation.status_failed') }}
                                @endif
                            </span>
                        </div>
                        <div class="rounded-xl bg-gradient-to-r from-purple-50 to-blue-50 p-4">
                            <p class="font-mono text-3xl font-bold tracking-wide text-purple-700">
                                {{ $purchase->order_reference }}
                            </p>
                        </div>
                    </div>

                    {{-- Purchase Details --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        <h3 class="mb-2 text-xl font-bold text-gray-900">
                            {{ __('egili.confirmation.order_summary') }}
                        </h3>
                        {{-- Nota Pacchetto AI — ToS v3.0.0 --}}
                        <div
                            class="mb-4 flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                            <span class="text-base">🤖</span>
                            <span>{{ __('egili.purchase.egili_model_note') }}</span>
                        </div>

                        <div class="space-y-4">
                            {{-- Egili Amount --}}
                            <div class="flex items-center justify-between border-b border-gray-200 py-3">
                                <div class="flex items-center">
                                    <span class="mr-3 text-3xl">💎</span>
                                    <span class="text-gray-700">{{ __('egili.confirmation.egili_purchased') }}</span>
                                </div>
                                <span class="text-2xl font-bold text-purple-700">
                                    {{ number_format($purchase->egili_amount) }} Egili
                                </span>
                            </div>

                            {{-- Unit Price --}}
                            <div class="flex items-center justify-between border-b border-gray-200 py-3">
                                <span class="text-gray-700">{{ __('egili.confirmation.unit_price') }}</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $purchase->formatted_unit_price }}
                                </span>
                            </div>

                            {{-- Total --}}
                            <div
                                class="flex items-center justify-between rounded-lg bg-gradient-to-r from-purple-50 to-blue-50 px-4 py-4">
                                <span
                                    class="text-lg font-bold text-gray-900">{{ __('egili.confirmation.total_paid') }}</span>
                                <span class="text-3xl font-bold text-purple-700">
                                    {{ $purchase->formatted_total }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        <h3 class="mb-6 text-xl font-bold text-gray-900">
                            {{ __('egili.confirmation.payment_method') }}
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.payment_method') }}</span>
                                <span class="font-semibold text-gray-900">
                                    @if ($purchase->isFiatPayment())
                                        💳 FIAT (EUR)
                                    @else
                                        ₿ Crypto
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.payment_provider') }}</span>
                                <span class="font-semibold capitalize text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $purchase->payment_provider)) }}
                                </span>
                            </div>

                            @if ($purchase->payment_external_id)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700">{{ __('egili.confirmation.payment_id') }}</span>
                                    <span class="max-w-xs truncate font-mono text-sm text-gray-600">
                                        {{ $purchase->payment_external_id }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.purchased_at') }}</span>
                                <span class="text-gray-900">
                                    {{ $purchase->purchased_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Crypto Details (legacy — solo ordini precedenti ToS v3.0.0) --}}
                    @if ($purchase->isCryptoPayment() && $purchase->crypto_tx_hash)
                        <div class="rounded-2xl bg-white p-6 shadow-xl">
                            <h3 class="mb-6 text-xl font-bold text-gray-900">
                                ₿ {{ __('egili.confirmation.crypto_details') }}
                            </h3>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700">{{ __('egili.confirmation.crypto_currency') }}</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ strtoupper($purchase->crypto_currency) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700">{{ __('egili.confirmation.crypto_amount') }}</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ number_format($purchase->crypto_amount, 8) }}
                                        {{ strtoupper($purchase->crypto_currency) }}
                                    </span>
                                </div>

                                <div class="border-t border-gray-200 pt-3">
                                    <p class="mb-2 text-sm text-gray-600">{{ __('egili.confirmation.crypto_tx_hash') }}
                                    </p>
                                    <div class="break-all rounded-lg bg-gray-50 p-3 font-mono text-xs text-gray-700">
                                        {{ $purchase->crypto_tx_hash }}
                                    </div>
                                    {{-- TODO: Add blockchain explorer link based on crypto_currency --}}
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Right Column: Wallet Info & Actions --}}
                <div class="space-y-6 lg:col-span-1">

                    {{-- Wallet Balance --}}
                    <div class="rounded-2xl bg-gradient-to-br from-purple-600 to-blue-600 p-6 text-white shadow-xl">
                        <h3 class="mb-4 text-xl font-bold">
                            {{ __('egili.confirmation.wallet_info') }}
                        </h3>
                        <div class="py-4 text-center">
                            <p class="mb-2 text-sm text-purple-200">{{ __('egili.confirmation.new_balance') }}</p>
                            <p class="mb-1 text-5xl font-bold">{{ number_format($currentBalance) }}</p>
                            <p class="text-lg text-purple-200">Egili</p>
                        </div>
                        <div class="mt-4 border-t border-purple-400/30 pt-4">
                            <p class="text-xs text-purple-200">
                                +{{ number_format($purchase->egili_amount) }} Egili
                                {{ __('egili.transaction_types.purchase') }}
                            </p>
                        </div>
                    </div>

                    {{-- Invoice Info --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        <h3 class="mb-3 text-lg font-bold text-gray-900">
                            📄 {{ __('egili.confirmation.invoice') }}
                        </h3>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ __('egili.confirmation.invoice_will_be_sent') }}
                        </p>
                        @if ($purchase->hasInvoice())
                            <a href="#"
                                class="block w-full rounded-lg bg-gray-100 px-4 py-2 text-center font-semibold text-gray-800 transition-colors hover:bg-gray-200">
                                ⬇️ {{ __('egili.confirmation.download_receipt') }}
                            </a>
                        @else
                            <div class="rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                                {{ __('egili.confirmation.invoice_will_be_sent') }}
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">
                            🎯 {{ __('common.actions') }}
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ $purchase->return_url ?? route('dashboard') }}"
                                class="block w-full rounded-lg bg-gradient-to-r from-purple-600 to-blue-600 px-4 py-3 text-center font-semibold text-white shadow-lg transition-all hover:from-purple-700 hover:to-blue-700">
                                @if ($purchase->return_url)
                                    ← {{ __('common.back') }}
                                @else
                                    {{ __('egili.confirmation.back_to_dashboard') }}
                                @endif
                            </a>

                            @if (request()->has('feature_code'))
                                <a href="{{ route('features.purchase', request('feature_code')) }}"
                                    class="block w-full rounded-lg border-2 border-purple-600 bg-white px-4 py-3 text-center font-semibold text-purple-700 transition-all hover:bg-gray-50">
                                    {{ __('features.continue_purchase') }}
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</x-platform-layout>
