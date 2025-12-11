{{--
    REBIND CHECKOUT PAGE
    🎯 Purpose: Secondary market purchase - buy EGI from current owner
    📍 Route: GET /egi/{id}/rebind
    ➡️ Submit: POST /egi/{id}/rebind → Process purchase
    🔒 Auth: Required
--}}
<x-platform-layout :title="__('rebind.checkout.title') . ' - ' . $egi->title">
    <div class="container mx-auto max-w-4xl px-4 py-8">

        {{-- Header --}}
        <div class="mb-8 rounded-xl bg-gradient-to-br from-cyan-600 to-teal-700 p-6 text-center shadow-2xl">
            <h1 class="mb-2 text-3xl font-bold text-white drop-shadow-lg">
                {{ __('rebind.title') }}
            </h1>
            <p class="text-lg font-semibold text-cyan-100">
                {{ __('rebind.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

            {{-- COLONNA 1: EGI Preview --}}
            <div class="space-y-6">

                {{-- Immagine EGI --}}
                <div class="overflow-hidden rounded-lg bg-white shadow-lg">
                    @if ($egi->main_image_url)
                        <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}" class="h-64 w-full object-cover">
                    @else
                        <div class="flex h-64 w-full items-center justify-center bg-gray-200">
                            <span class="text-4xl text-gray-400">🎨</span>
                        </div>
                    @endif

                    <div class="p-6">
                        <h2 class="mb-2 text-xl font-bold text-gray-900">{{ $egi->title }}</h2>
                        <p class="mb-4 text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}
                        </p>

                        @if ($egi->description)
                            <p class="text-sm text-gray-700">{{ Str::limit($egi->description, 150) }}</p>
                        @endif

                        {{-- Blockchain Badge --}}
                        @if ($egi->blockchain && $egi->blockchain->isMinted())
                            <div
                                class="mt-4 flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span class="font-medium">{{ __('egi.badge.minted') }} -
                                    {{ __('rebind.info.blockchain_transfer') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Secondary Market Info --}}
                <div class="rounded-lg border-l-4 border-cyan-500 bg-cyan-50 p-4">
                    <h4 class="mb-2 flex items-center gap-2 text-sm font-semibold text-cyan-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('rebind.info.secondary_market') }}
                    </h4>
                    <p class="text-xs text-cyan-700">
                        {{ __('rebind.info.secondary_market_desc') }}
                    </p>
                </div>

                {{-- Current Owner Card --}}
                <div class="rounded-lg bg-white p-6 shadow-lg">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">{{ __('rebind.checkout.current_owner') }}</h3>
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 overflow-hidden rounded-full bg-gray-100">
                            @if ($owner->profile_photo_url)
                                <img src="{{ $owner->profile_photo_url }}" alt="{{ $owner->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-200 to-teal-300 text-xl font-bold text-cyan-700">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $owner->name }}</p>
                            <p class="text-sm text-gray-500">{{ __('collector.role') ?? 'Collector' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Prezzo --}}
                <div class="rounded-lg bg-cyan-50 p-6">
                    <h3 class="mb-2 font-semibold text-cyan-900">
                        {{ __('rebind.checkout.price_label') }}
                    </h3>
                    <div class="text-3xl font-bold text-cyan-600">
                        €{{ number_format($paymentAmountEur ?? 0, 2, ',', '.') }}
                    </div>
                    <p class="mt-2 text-sm text-cyan-700">
                        {{ __('rebind.info.blockchain_transfer_desc') }}
                    </p>
                </div>

            </div>

            {{-- COLONNA 2: Payment Form --}}
            <div class="space-y-6">

                {{-- MiCA Compliance Notice --}}
                <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
                    <h4 class="mb-1 text-sm font-semibold text-blue-900">
                        {{ __('mint.compliance.mica_title') }}
                    </h4>
                    <p class="text-xs text-blue-700">
                        {{ __('mint.compliance.mica_description') }}
                    </p>
                </div>

                {{-- Form Pagamento --}}
                <form id="rebind-payment-form" action="{{ route('egi.rebind.process', $egi->id) }}" method="POST"
                    class="rounded-lg bg-white p-6 shadow-lg">
                    @csrf

                    @php
                        $showEgiliOption = $showEgiliOption ?? false;
                        $canPayWithEgili = $canPayWithEgili ?? false;
                        $egiliBalance = $egiliBalance ?? 0;
                        $requiredEgili = $requiredEgili ?? 0;
                        $selectedPaymentMethod = old(
                            'payment_method',
                            $showEgiliOption && $canPayWithEgili ? 'egili' : 'stripe',
                        );
                    @endphp

                    <input type="hidden" name="egi_id" value="{{ $egi->id }}">
                    <input type="hidden" name="seller_id" value="{{ $owner->id }}">

                    {{-- Payment Method --}}
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-medium text-gray-700">
                            {{ __('mint.payment.payment_method_label') }}
                        </label>
                        <div class="space-y-3">
                            @php
                                $stripeMerchantAvailable = $stripeMerchantAvailable ?? false;
                                $stripeMerchantError =
                                    $stripeMerchantError ?? __('payment.errors.merchant_account_incomplete');
                            @endphp

                            {{-- Stripe / Credit Card --}}
                            <label
                                class="{{ $stripeMerchantAvailable ? 'cursor-pointer border-gray-300 hover:bg-gray-50' : 'cursor-not-allowed border-red-300 bg-red-50 opacity-60' }} flex items-center rounded-lg border p-3 transition-colors">
                                <input type="radio" name="payment_method" value="stripe"
                                    {{ $selectedPaymentMethod === 'stripe' && $stripeMerchantAvailable ? 'checked' : '' }}
                                    {{ !$stripeMerchantAvailable ? 'disabled' : '' }}
                                    class="{{ !$stripeMerchantAvailable ? 'cursor-not-allowed opacity-50' : '' }} h-4 w-4 border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                <div class="ml-3 flex-1">
                                    <span
                                        class="{{ $stripeMerchantAvailable ? 'text-gray-900' : 'text-red-700' }} text-sm font-medium">
                                        💳 {{ __('mint.payment.credit_card') }}
                                    </span>
                                    @if (!$stripeMerchantAvailable)
                                        <p class="mt-1 text-xs text-red-600">
                                            ⚠️ {{ $stripeMerchantError }}
                                        </p>
                                    @endif
                                </div>
                            </label>

                            {{-- PayPal --}}
                            @php
                                $paypalAvailable = $paypalAvailable ?? false;
                                $paypalError = $paypalError ?? __('payment.errors.paypal_not_implemented');
                            @endphp
                            <label
                                class="{{ $paypalAvailable ? 'cursor-pointer border-gray-300 hover:bg-gray-50' : 'cursor-not-allowed border-red-300 bg-red-50 opacity-60' }} flex items-center rounded-lg border p-3 transition-colors">
                                <input type="radio" name="payment_method" value="paypal"
                                    {{ $selectedPaymentMethod === 'paypal' && $paypalAvailable ? 'checked' : '' }}
                                    {{ !$paypalAvailable ? 'disabled' : '' }}
                                    class="{{ !$paypalAvailable ? 'cursor-not-allowed opacity-50' : '' }} h-4 w-4 border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                <div class="ml-3 flex-1">
                                    <span
                                        class="{{ $paypalAvailable ? 'text-gray-900' : 'text-red-700' }} text-sm font-medium">
                                        💙 {{ __('mint.payment.paypal') }}
                                    </span>
                                    @if (!$paypalAvailable)
                                        <p class="mt-1 text-xs text-red-600">
                                            ⚠️ {{ $paypalError }}
                                        </p>
                                    @endif
                                </div>
                            </label>

                            {{-- EGILI Token --}}
                            @if ($showEgiliOption)
                                <label
                                    class="{{ $canPayWithEgili ? 'cursor-pointer hover:bg-emerald-50' : 'cursor-not-allowed bg-emerald-50/60' }} flex items-start rounded-lg border border-emerald-400/60 p-3 transition-colors">
                                    <div class="pt-1">
                                        <input type="radio" name="payment_method" value="egili"
                                            {{ $selectedPaymentMethod === 'egili' && $canPayWithEgili ? 'checked' : '' }}
                                            {{ $canPayWithEgili ? '' : 'disabled' }}
                                            class="h-4 w-4 border-emerald-400 text-emerald-600 focus:ring-emerald-600">
                                    </div>
                                    <div class="ml-3 space-y-1 text-sm">
                                        <p class="font-semibold text-emerald-900">
                                            🪙 {{ __('mint.payment.payment_method_egili') }}
                                        </p>
                                        <p class="text-xs text-emerald-700">
                                            {{ __('mint.payment.egili_balance_label', ['balance' => number_format($egiliBalance)]) }}
                                        </p>
                                        <p class="text-xs text-emerald-700">
                                            {{ __('mint.payment.egili_required_label', ['required' => number_format($requiredEgili)]) }}
                                        </p>
                                        @unless ($canPayWithEgili)
                                            <p class="text-xs font-semibold text-red-600">
                                                {{ __('mint.payment.egili_insufficient') }}
                                            </p>
                                        @endunless
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- Wallet Destinazione (Opzionale) --}}
                    <div class="mb-6">
                        <div class="mb-3 flex items-center">
                            <input type="checkbox" id="has_wallet_toggle"
                                class="h-4 w-4 rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                            <label for="has_wallet_toggle" class="ml-2 text-sm font-medium text-gray-700">
                                {{ __('mint.buyer_info.has_wallet') }}
                            </label>
                        </div>

                        <div id="wallet_input_container" class="hidden">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ __('mint.buyer_info.wallet_label') }}
                            </label>
                            <input type="text" name="buyer_wallet" id="buyer_wallet"
                                placeholder="{{ __('mint.buyer_info.wallet_placeholder') }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('mint.buyer_info.wallet_help') }}
                            </p>
                        </div>
                    </div>

                    {{-- Ownership Transfer Info --}}
                    <div class="mb-6 rounded-lg border border-cyan-200 bg-cyan-50 p-4">
                        <h4 class="mb-2 flex items-center gap-2 text-sm font-semibold text-cyan-900">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            {{ __('rebind.info.blockchain_transfer') }}
                        </h4>
                        <p class="text-xs text-cyan-700">
                            {{ __('rebind.info.blockchain_transfer_desc') }}
                        </p>
                    </div>

                    {{-- Total --}}
                    <div class="mb-6 border-t pt-4">
                        <div class="flex items-center justify-between text-lg font-semibold">
                            <span class="text-gray-900">{{ __('rebind.checkout.total') }}</span>
                            <span class="text-2xl font-bold text-cyan-700">
                                €{{ number_format($paymentAmountEur, 2, ',', '.') }}
                            </span>
                        </div>
                        @if ($showEgiliOption)
                            <div class="mt-3 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-800">
                                <p class="font-semibold">
                                    {{ __('mint.payment.egili_summary_title') }}
                                </p>
                                <p>
                                    {{ __('mint.payment.egili_summary', ['required' => number_format($requiredEgili)]) }}
                                </p>
                                <p>
                                    {{ __('mint.payment.egili_balance_label', ['balance' => number_format($egiliBalance)]) }}
                                </p>
                                @unless ($canPayWithEgili)
                                    <p class="font-semibold text-red-600">
                                        {{ __('mint.payment.egili_insufficient') }}
                                    </p>
                                @endunless
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" id="submit-rebind-btn"
                        class="w-full rounded-lg bg-gradient-to-r from-cyan-500 to-teal-600 px-6 py-3 font-bold text-white transition-all hover:from-cyan-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('egi.actions.rebind') ?? 'Rebind' }} -
                            €{{ number_format($paymentAmountEur, 2, ',', '.') }}
                        </span>
                    </button>

                    {{-- Back Link --}}
                    <div class="mt-4 text-center">
                        <a href="{{ route('egis.show', $egi->id) }}"
                            class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                            ← {{ __('common.back_to_egi') ?? 'Torna all\'EGI' }}
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- JavaScript --}}
    @push('scripts')
        <script>
            // Toggle wallet input
            document.getElementById('has_wallet_toggle').addEventListener('change', function(e) {
                const container = document.getElementById('wallet_input_container');
                const input = document.getElementById('buyer_wallet');

                if (e.target.checked) {
                    container.classList.remove('hidden');
                    input.required = true;
                } else {
                    container.classList.add('hidden');
                    input.required = false;
                    input.value = '';
                }
            });

            // Check if page was reloaded after error (flash messages present)
            document.addEventListener('DOMContentLoaded', function() {
                const hasErrors = document.querySelector('.alert-danger') ||
                    document.querySelector('[role="alert"]') ||
                    @json($errors->any());

                if (hasErrors && window.Swal) {
                    Swal.close();
                }
            });

            // Form submission con MODALE DI PROGRESS
            document.getElementById('rebind-payment-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const btn = document.getElementById('submit-rebind-btn');

                // Disabilita button e mostra spinner
                btn.disabled = true;
                btn.innerHTML = `
                    <span class="flex items-center justify-center gap-2">
                        <svg class="inline w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('rebind.process.processing') }}
                    </span>
                `;

                // Mostra modale di progress
                if (window.Swal) {
                    Swal.fire({
                        title: '⏳ {{ __('rebind.process.initiated') }}',
                        html: `
                            <div class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <svg class="w-16 h-16 text-cyan-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-700">{{ __('rebind.process.processing') }}</p>
                                <p class="text-sm text-cyan-600">{{ __('rebind.process.transferring') }}</p>
                                <p class="text-sm text-gray-500">⚠️ Non chiudere questa finestra</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });
        </script>
    @endpush

</x-platform-layout>
