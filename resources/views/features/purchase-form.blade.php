@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-4xl px-4 py-8">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-4xl font-bold" style="color: #8E44AD;">
                {{ $pricing->feature_name }}
            </h1>
            <p class="text-gray-600">
                {{ $pricing->feature_description }}
            </p>
        </div>

        {{-- Features/Benefits Card --}}
        @if ($pricing->benefits)
            <div class="mb-8 rounded-lg bg-gradient-to-r from-purple-50 to-blue-50 p-6">
                <h3 class="mb-4 text-lg font-semibold" style="color: #8E44AD;">
                    {{ __('features.benefits_included') }}
                </h3>
                <ul class="space-y-2">
                    @foreach (json_decode($pricing->benefits, true) as $benefit)
                        <li class="flex items-center">
                            <svg class="mr-2 h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $benefit }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Payment Form --}}
        <form id="feature-purchase-form" action="{{ route('features.purchase.process') }}" method="POST"
            class="rounded-lg bg-white p-6 shadow-lg">
            @csrf
            <input type="hidden" name="feature_code" value="{{ $pricing->feature_code }}">

            {{-- Payment Method Selection --}}
            <div class="mb-6">
                <label class="mb-3 block text-sm font-medium text-gray-700">
                    {{ __('payment.select_method') }}
                </label>

                <div class="space-y-3">

                    {{-- EGILI Payment (if available) --}}
                    @if ($pricing->cost_egili)
                        <label
                            class="{{ $canPayWithEgili ? '' : 'opacity-50 cursor-not-allowed' }} flex cursor-pointer items-center justify-between rounded-lg border-2 p-4 transition-all hover:border-purple-400"
                            :class="paymentMethod === 'egili' ? 'border-purple-500 bg-purple-50' : 'border-gray-300'">
                            <div class="flex flex-1 items-center">
                                <input type="radio" name="payment_method" value="egili"
                                    {{ $canPayWithEgili ? '' : 'disabled' }}
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500">
                                <div class="ml-3">
                                    <div class="font-semibold" style="color: #8E44AD;">
                                        💎 {{ __('payment.method_egili') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ __('payment.your_balance') }}: {{ number_format($egiliBalance) }} Egili
                                    </div>
                                    @if (!$canPayWithEgili)
                                        <div class="text-sm text-red-600">
                                            {{ __('payment.insufficient_egili') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold" style="color: #8E44AD;">
                                    {{ number_format($pricing->cost_egili) }} Egili
                                </div>
                                <div class="text-sm text-gray-500">
                                    ≈ €{{ number_format($pricing->cost_egili * 0.01, 2) }}
                                </div>
                            </div>
                        </label>
                    @endif

                    {{-- FIAT Payment (if available) --}}
                    @if ($pricing->cost_fiat_eur)
                        <label
                            class="flex cursor-pointer items-center justify-between rounded-lg border-2 p-4 transition-all hover:border-blue-400"
                            :class="paymentMethod === 'fiat' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <div class="flex flex-1 items-center">
                                <input type="radio" name="payment_method" value="fiat"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <div class="font-semibold text-blue-900">
                                        💳 {{ __('payment.method_fiat') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Stripe, PayPal
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-900">
                                    €{{ number_format($pricing->cost_fiat_eur, 2) }}
                                </div>
                            </div>
                        </label>
                    @endif

                    {{-- CRYPTO Payment (if FIAT available, assume crypto too) --}}
                    @if ($pricing->cost_fiat_eur)
                        <label
                            class="flex cursor-pointer items-center justify-between rounded-lg border-2 p-4 transition-all hover:border-orange-400"
                            :class="paymentMethod === 'crypto' ? 'border-orange-500 bg-orange-50' : 'border-gray-300'">
                            <div class="flex flex-1 items-center">
                                <input type="radio" name="payment_method" value="crypto"
                                    class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <div class="ml-3">
                                    <div class="font-semibold text-orange-900">
                                        ₿ {{ __('payment.method_crypto') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        BTC, ETH, USDC, ALGO
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-orange-900">
                                    €{{ number_format($pricing->cost_fiat_eur, 2) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ __('payment.crypto_dynamic') }}
                                </div>
                            </div>
                        </label>
                    @endif
                </div>
            </div>

            {{-- FIAT Provider Selection (conditional) --}}
            <div id="fiat-providers" class="mb-6 hidden">
                <label class="mb-3 block text-sm font-medium text-gray-700">
                    {{ __('payment.select_provider') }}
                </label>
                <div class="space-y-2">
                    <label class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50">
                        <input type="radio" name="fiat_provider" value="stripe" class="h-4 w-4 text-blue-600">
                        <span class="ml-3">💳 {{ __('payment.provider_stripe') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50">
                        <input type="radio" name="fiat_provider" value="paypal" class="h-4 w-4 text-blue-600">
                        <span class="ml-3">💙 {{ __('payment.provider_paypal') }}</span>
                    </label>
                </div>
            </div>

            {{-- CRYPTO Provider Selection (conditional) --}}
            <div id="crypto-providers" class="mb-6 hidden">
                <label class="mb-3 block text-sm font-medium text-gray-700">
                    {{ __('payment.select_crypto_provider') }}
                </label>
                <div class="space-y-2">
                    <label class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50">
                        <input type="radio" name="crypto_provider" value="coinbase_commerce"
                            class="h-4 w-4 text-orange-600">
                        <span class="ml-3">{{ __('payment.crypto_coinbase') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50">
                        <input type="radio" name="crypto_provider" value="bitpay" class="h-4 w-4 text-orange-600">
                        <span class="ml-3">{{ __('payment.crypto_bitpay') }}</span>
                    </label>
                    <label class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50">
                        <input type="radio" name="crypto_provider" value="nowpayments"
                            class="h-4 w-4 text-orange-600">
                        <span class="ml-3">{{ __('payment.crypto_nowpayments') }}</span>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex space-x-4">
                <button type="submit"
                    class="flex-1 rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3 font-bold text-white transition-all hover:from-purple-700 hover:to-purple-800">
                    {{ __('payment.pay_now') }}
                </button>
                <a href="{{ url()->previous() }}"
                    class="flex-1 rounded-lg bg-gray-200 px-6 py-3 text-center font-bold text-gray-700 transition-all hover:bg-gray-300">
                    {{ __('payment.cancel') }}
                </a>
            </div>
        </form>

    </div>

    @push('scripts')
        <script>
            // Show/hide provider selection based on payment method
            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const fiatProviders = document.getElementById('fiat-providers');
                    const cryptoProviders = document.getElementById('crypto-providers');

                    fiatProviders.classList.add('hidden');
                    cryptoProviders.classList.add('hidden');

                    if (this.value === 'fiat') {
                        fiatProviders.classList.remove('hidden');
                        document.querySelector('input[name="fiat_provider"][value="stripe"]').checked = true;
                    } else if (this.value === 'crypto') {
                        cryptoProviders.classList.remove('hidden');
                        document.querySelector('input[name="crypto_provider"][value="coinbase_commerce"]')
                            .checked = true;
                    }
                });
            });
        </script>
    @endpush
@endsection
