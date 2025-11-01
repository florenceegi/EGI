@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold mb-2" style="color: #8E44AD;">
            {{ $pricing->feature_name }}
        </h1>
        <p class="text-gray-600">
            {{ $pricing->feature_description }}
        </p>
    </div>

    {{-- Features/Benefits Card --}}
    @if($pricing->benefits)
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4" style="color: #8E44AD;">
                {{ __('features.benefits_included') }}
            </h3>
            <ul class="space-y-2">
                @foreach(json_decode($pricing->benefits, true) as $benefit)
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $benefit }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Payment Form --}}
    <form id="feature-purchase-form" action="{{ route('features.purchase.process') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
        @csrf
        <input type="hidden" name="feature_code" value="{{ $pricing->feature_code }}">

        {{-- Payment Method Selection --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ __('payment.select_method') }}
            </label>

            <div class="space-y-3">
                
                {{-- EGILI Payment (if available) --}}
                @if($pricing->cost_egili)
                    <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-purple-400 {{ $canPayWithEgili ? '' : 'opacity-50 cursor-not-allowed' }}"
                           :class="paymentMethod === 'egili' ? 'border-purple-500 bg-purple-50' : 'border-gray-300'">
                        <div class="flex items-center flex-1">
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
                                @if(!$canPayWithEgili)
                                    <div class="text-sm text-red-600">
                                        {{ __('payment.insufficient_egili') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-lg" style="color: #8E44AD;">
                                {{ number_format($pricing->cost_egili) }} Egili
                            </div>
                            <div class="text-sm text-gray-500">
                                ≈ €{{ number_format($pricing->cost_egili * 0.1, 2) }}
                            </div>
                        </div>
                    </label>
                @endif

                {{-- FIAT Payment (if available) --}}
                @if($pricing->cost_fiat_eur)
                    <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-400"
                           :class="paymentMethod === 'fiat' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <div class="flex items-center flex-1">
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
                            <div class="font-bold text-lg text-blue-900">
                                €{{ number_format($pricing->cost_fiat_eur, 2) }}
                            </div>
                        </div>
                    </label>
                @endif

                {{-- CRYPTO Payment (if FIAT available, assume crypto too) --}}
                @if($pricing->cost_fiat_eur)
                    <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-orange-400"
                           :class="paymentMethod === 'crypto' ? 'border-orange-500 bg-orange-50' : 'border-gray-300'">
                        <div class="flex items-center flex-1">
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
                            <div class="font-bold text-lg text-orange-900">
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
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ __('payment.select_provider') }}
            </label>
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="fiat_provider" value="stripe" class="h-4 w-4 text-blue-600">
                    <span class="ml-3">💳 {{ __('payment.provider_stripe') }}</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="fiat_provider" value="paypal" class="h-4 w-4 text-blue-600">
                    <span class="ml-3">💙 {{ __('payment.provider_paypal') }}</span>
                </label>
            </div>
        </div>

        {{-- CRYPTO Provider Selection (conditional) --}}
        <div id="crypto-providers" class="mb-6 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ __('payment.select_crypto_provider') }}
            </label>
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="crypto_provider" value="coinbase_commerce" class="h-4 w-4 text-orange-600">
                    <span class="ml-3">{{ __('payment.crypto_coinbase') }}</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="crypto_provider" value="bitpay" class="h-4 w-4 text-orange-600">
                    <span class="ml-3">{{ __('payment.crypto_bitpay') }}</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="crypto_provider" value="nowpayments" class="h-4 w-4 text-orange-600">
                    <span class="ml-3">{{ __('payment.crypto_nowpayments') }}</span>
                </label>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex space-x-4">
            <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all">
                {{ __('payment.pay_now') }}
            </button>
            <a href="{{ url()->previous() }}" 
               class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-lg hover:bg-gray-300 transition-all text-center">
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
            document.querySelector('input[name="crypto_provider"][value="coinbase_commerce"]').checked = true;
        }
    });
});
</script>
@endpush
@endsection

