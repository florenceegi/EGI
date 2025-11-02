{{-- Egili Purchase Modal (Functional) --}}
@php
    $unitPrice = config('egili.purchase.unit_price_eur', 0.01);
    $minPurchase = config('egili.purchase.min_amount', 5000);
    $maxPurchase = config('egili.purchase.max_amount', 1000000);
@endphp

<div id="egili-purchase-modal" 
     class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[60] flex items-center justify-center hidden"
     onclick="if(event.target === this) closeEgiliPurchaseModal()">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                        <span class="text-4xl">💎</span>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">{{ __('egili.purchase.title') }}</h2>
                        <p class="text-purple-100 text-sm">{{ __('egili.purchase.subtitle') }}</p>
                    </div>
                </div>
                <button onclick="closeEgiliPurchaseModal()" 
                        class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="egili-purchase-form" class="p-8">
            @csrf
            
            {{-- Step 1: Amount Selection --}}
            <div class="mb-6">
                <label for="egili-amount" class="block text-sm font-semibold text-gray-700 mb-2">
                    💎 {{ __('egili.purchase.how_many_label') }}
                </label>
                <input type="number" 
                       id="egili-amount" 
                       name="egili_amount"
                       min="{{ $minPurchase }}" 
                       max="{{ $maxPurchase }}" 
                       step="100"
                       placeholder="{{ __('egili.purchase.amount_placeholder') }}"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all text-lg"
                       required>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ __('egili.purchase.min_purchase', ['min' => number_format($minPurchase), 'eur' => '€' . number_format($minPurchase * $unitPrice, 2)]) }}</span>
                    <span>{{ __('egili.purchase.max_purchase', ['max' => number_format($maxPurchase), 'eur' => '€' . number_format($maxPurchase * $unitPrice, 2)]) }}</span>
                </div>
            </div>
            
            {{-- Price Summary --}}
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 mb-6 border-2 border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('egili.purchase.unit_price') }}</p>
                        <p class="text-xl font-bold text-purple-700">€{{ number_format($unitPrice, 4) }} / Egili</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">{{ __('egili.purchase.total_cost') }}</p>
                        <p id="total-price" class="text-3xl font-bold text-purple-700">€0.00</p>
                    </div>
                </div>
                <div id="price-breakdown" class="text-xs text-gray-600 hidden">
                    <span id="egili-qty">0</span> Egili × €{{ number_format($unitPrice, 4) }} = 
                    <span id="total-eur" class="font-semibold">€0.00</span>
                </div>
            </div>
            
            {{-- Step 2: Payment Method Selection --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    💳 {{ __('egili.purchase.select_payment_method') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="fiat" checked
                               class="peer sr-only" onchange="updateProviderOptions()">
                        <div class="peer-checked:border-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-4 transition-all hover:border-purple-400">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-2xl">💳</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ __('egili.purchase.payment_method_fiat') }}</p>
                                    <p class="text-xs text-gray-600">Stripe, PayPal</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="crypto"
                               class="peer sr-only" onchange="updateProviderOptions()">
                        <div class="peer-checked:border-blue-600 peer-checked:bg-blue-50 border-2 border-gray-300 rounded-lg p-4 transition-all hover:border-blue-400">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-2xl">₿</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ __('egili.purchase.payment_method_crypto') }}</p>
                                    <p class="text-xs text-gray-600">BTC, ETH, USDC...</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            
            {{-- Step 3: Provider Selection (FIAT) --}}
            <div id="fiat-providers" class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    🏦 {{ __('egili.purchase.select_provider') }}
                </label>
                <div class="space-y-2">
                    @if(config('egili.payment_providers.fiat.stripe.enabled', true))
                    <label class="relative cursor-pointer block">
                        <input type="radio" name="fiat_provider" value="stripe" checked
                               class="peer sr-only">
                        <div class="peer-checked:border-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-purple-400 flex items-center">
                            <span class="text-2xl mr-3">💳</span>
                            <span class="font-medium">{{ __('egili.purchase.fiat_provider_stripe') }}</span>
                        </div>
                    </label>
                    @endif
                    
                    @if(config('egili.payment_providers.fiat.paypal.enabled', true))
                    <label class="relative cursor-pointer block">
                        <input type="radio" name="fiat_provider" value="paypal"
                               class="peer sr-only">
                        <div class="peer-checked:border-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-purple-400 flex items-center">
                            <span class="text-2xl mr-3">🅿️</span>
                            <span class="font-medium">{{ __('egili.purchase.fiat_provider_paypal') }}</span>
                        </div>
                    </label>
                    @endif
                </div>
            </div>
            
            {{-- Step 3: Provider Selection (Crypto) - Hidden by default --}}
            <div id="crypto-providers" class="mb-6 hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    ₿ {{ __('egili.purchase.select_provider') }}
                </label>
                <div class="space-y-2">
                    @if(config('egili.payment_providers.crypto.coinbase_commerce.enabled', true))
                    <label class="relative cursor-pointer block">
                        <input type="radio" name="crypto_provider" value="coinbase_commerce" checked
                               class="peer sr-only">
                        <div class="peer-checked:border-blue-600 peer-checked:bg-blue-50 border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-blue-400 flex items-center">
                            <span class="text-2xl mr-3">🔷</span>
                            <span class="font-medium">{{ __('egili.purchase.crypto_provider_coinbase') }}</span>
                        </div>
                    </label>
                    @endif
                    
                    @if(config('egili.payment_providers.crypto.bitpay.enabled', false))
                    <label class="relative cursor-pointer block">
                        <input type="radio" name="crypto_provider" value="bitpay"
                               class="peer sr-only">
                        <div class="peer-checked:border-blue-600 peer-checked:bg-blue-50 border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-blue-400 flex items-center">
                            <span class="text-2xl mr-3">🔶</span>
                            <span class="font-medium">{{ __('egili.purchase.crypto_provider_bitpay') }}</span>
                        </div>
                    </label>
                    @endif
                    
                    @if(config('egili.payment_providers.crypto.nowpayments.enabled', false))
                    <label class="relative cursor-pointer block">
                        <input type="radio" name="crypto_provider" value="nowpayments"
                               class="peer sr-only">
                        <div class="peer-checked:border-blue-600 peer-checked:bg-blue-50 border-2 border-gray-300 rounded-lg p-3 transition-all hover:border-blue-400 flex items-center">
                            <span class="text-2xl mr-3">⚡</span>
                            <span class="font-medium">{{ __('egili.purchase.crypto_provider_nowpayments') }}</span>
                        </div>
                    </label>
                    @endif
                </div>
            </div>
            
            {{-- Error Message --}}
            <div id="purchase-error" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-700 font-semibold"></p>
            </div>
            
            {{-- Submit Button --}}
            <div class="flex justify-between items-center">
                <button type="button" 
                        onclick="closeEgiliPurchaseModal()" 
                        class="text-gray-600 hover:text-gray-800 font-semibold transition-colors">
                    ← {{ __('common.back') }}
                </button>
                
                <button type="submit" 
                        id="purchase-submit-btn"
                        class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="submit-btn-text">💎 {{ __('egili.purchase.purchase_now') }}</span>
                    <span id="submit-btn-loading" class="hidden">
                        <svg class="inline w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('egili.purchase.processing') }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Configuration from backend
const EGILI_CONFIG = {
    unitPrice: {{ $unitPrice }},
    minPurchase: {{ $minPurchase }},
    maxPurchase: {{ $maxPurchase }},
    csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
};

// Open Egili Purchase Modal
window.openEgiliPurchaseModal = function() {
    const modal = document.getElementById('egili-purchase-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.getElementById('egili-amount').focus();
    }
};

// Close Egili Purchase Modal
window.closeEgiliPurchaseModal = function() {
    const modal = document.getElementById('egili-purchase-modal');
    if (modal) {
        modal.classList.add('hidden');
        // Reset form
        document.getElementById('egili-purchase-form').reset();
        updateTotalPrice();
        hideError();
    }
};

// Update provider options based on payment method
window.updateProviderOptions = function() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const fiatDiv = document.getElementById('fiat-providers');
    const cryptoDiv = document.getElementById('crypto-providers');
    
    if (paymentMethod === 'fiat') {
        fiatDiv.classList.remove('hidden');
        cryptoDiv.classList.add('hidden');
    } else {
        fiatDiv.classList.add('hidden');
        cryptoDiv.classList.remove('hidden');
    }
};

// Update total price
function updateTotalPrice() {
    const amountInput = document.getElementById('egili-amount');
    const totalPriceEl = document.getElementById('total-price');
    const priceBreakdownEl = document.getElementById('price-breakdown');
    const egiliQtyEl = document.getElementById('egili-qty');
    const totalEurEl = document.getElementById('total-eur');
    
    const amount = parseInt(amountInput.value) || 0;
    
    if (amount > 0) {
        const total = (amount * EGILI_CONFIG.unitPrice).toFixed(2);
        totalPriceEl.textContent = '€' + total;
        egiliQtyEl.textContent = amount.toLocaleString();
        totalEurEl.textContent = '€' + total;
        priceBreakdownEl.classList.remove('hidden');
    } else {
        totalPriceEl.textContent = '€0.00';
        priceBreakdownEl.classList.add('hidden');
    }
}

// Show error message
function showError(message) {
    const errorDiv = document.getElementById('purchase-error');
    errorDiv.querySelector('p').textContent = message;
    errorDiv.classList.remove('hidden');
}

// Hide error message
function hideError() {
    document.getElementById('purchase-error').classList.add('hidden');
}

// Handle form submission
document.getElementById('egili-purchase-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideError();
    
    const submitBtn = document.getElementById('purchase-submit-btn');
    const submitBtnText = document.getElementById('submit-btn-text');
    const submitBtnLoading = document.getElementById('submit-btn-loading');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtnText.classList.add('hidden');
    submitBtnLoading.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        const paymentMethod = formData.get('payment_method');
        
        const response = await fetch('{{ route("egili.purchase.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': EGILI_CONFIG.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                egili_amount: parseInt(formData.get('egili_amount')),
                payment_method: paymentMethod,
                fiat_provider: paymentMethod === 'fiat' ? formData.get('fiat_provider') : null,
                crypto_provider: paymentMethod === 'crypto' ? formData.get('crypto_provider') : null,
                return_url: window.location.href, // Current page URL
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.redirect_url) {
            // Redirect to payment gateway or confirmation page
            window.location.href = result.redirect_url;
        } else {
            showError(result.message || '{{ __("egili.purchase.process_error") }}');
            submitBtn.disabled = false;
            submitBtnText.classList.remove('hidden');
            submitBtnLoading.classList.add('hidden');
        }
        
    } catch (error) {
        console.error('Purchase error:', error);
        showError('{{ __("egili.purchase.process_error") }}');
        submitBtn.disabled = false;
        submitBtnText.classList.remove('hidden');
        submitBtnLoading.classList.add('hidden');
    }
});

// Listen to amount input changes
document.getElementById('egili-amount').addEventListener('input', updateTotalPrice);

// Initialize
updateTotalPrice();
</script>
