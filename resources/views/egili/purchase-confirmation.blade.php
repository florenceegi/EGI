<x-platform-layout :title="__('egili.confirmation.title')">
    
    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50 py-12">
        <div class="container mx-auto max-w-5xl px-4">
            
            {{-- Success Header --}}
            <div class="mb-12 text-center">
                <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg animate-bounce">
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
                
                @if(config('egili.notifications.send_purchase_confirmation', true))
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('egili.confirmation.email_sent', ['email' => Auth::user()->email]) }}
                </p>
                @endif
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left Column: Order Summary --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Order Reference --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ __('egili.confirmation.order_reference') }}
                            </h2>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                                @if($purchase->isCompleted()) bg-green-100 text-green-800
                                @elseif($purchase->isPending()) bg-yellow-100 text-yellow-800
                                @elseif($purchase->isFailed()) bg-red-100 text-red-800
                                @endif">
                                @if($purchase->isCompleted()) {{ __('egili.confirmation.status_completed') }}
                                @elseif($purchase->isPending()) {{ __('egili.confirmation.status_pending') }}
                                @elseif($purchase->isFailed()) {{ __('egili.confirmation.status_failed') }}
                                @endif
                            </span>
                        </div>
                        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-4">
                            <p class="text-3xl font-mono font-bold text-purple-700 tracking-wide">
                                {{ $purchase->order_reference }}
                            </p>
                        </div>
                    </div>
                    
                    {{-- Purchase Details --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            {{ __('egili.confirmation.order_summary') }}
                        </h3>
                        
                        <div class="space-y-4">
                            {{-- Egili Amount --}}
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <div class="flex items-center">
                                    <span class="text-3xl mr-3">💎</span>
                                    <span class="text-gray-700">{{ __('egili.confirmation.egili_purchased') }}</span>
                                </div>
                                <span class="text-2xl font-bold text-purple-700">
                                    {{ number_format($purchase->egili_amount) }} Egili
                                </span>
                            </div>
                            
                            {{-- Unit Price --}}
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <span class="text-gray-700">{{ __('egili.confirmation.unit_price') }}</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $purchase->formatted_unit_price }}
                                </span>
                            </div>
                            
                            {{-- Total --}}
                            <div class="flex items-center justify-between py-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg px-4">
                                <span class="text-lg font-bold text-gray-900">{{ __('egili.confirmation.total_paid') }}</span>
                                <span class="text-3xl font-bold text-purple-700">
                                    {{ $purchase->formatted_total }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Payment Details --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            {{ __('egili.confirmation.payment_method') }}
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.payment_method') }}</span>
                                <span class="font-semibold text-gray-900">
                                    @if($purchase->isFiatPayment())
                                        💳 FIAT (EUR)
                                    @else
                                        ₿ Crypto
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.payment_provider') }}</span>
                                <span class="font-semibold text-gray-900 capitalize">
                                    {{ ucfirst(str_replace('_', ' ', $purchase->payment_provider)) }}
                                </span>
                            </div>
                            
                            @if($purchase->payment_external_id)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('egili.confirmation.payment_id') }}</span>
                                <span class="font-mono text-sm text-gray-600 truncate max-w-xs">
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
                    
                    {{-- Crypto Details (if crypto payment) --}}
                    @if($purchase->isCryptoPayment() && $purchase->crypto_tx_hash)
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
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
                                    {{ number_format($purchase->crypto_amount, 8) }} {{ strtoupper($purchase->crypto_currency) }}
                                </span>
                            </div>
                            
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">{{ __('egili.confirmation.crypto_tx_hash') }}</p>
                                <div class="bg-gray-50 rounded-lg p-3 break-all font-mono text-xs text-gray-700">
                                    {{ $purchase->crypto_tx_hash }}
                                </div>
                                {{-- TODO: Add blockchain explorer link based on crypto_currency --}}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>
                
                {{-- Right Column: Wallet Info & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Wallet Balance --}}
                    <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-4">
                            {{ __('egili.confirmation.wallet_info') }}
                        </h3>
                        <div class="text-center py-4">
                            <p class="text-sm text-purple-200 mb-2">{{ __('egili.confirmation.new_balance') }}</p>
                            <p class="text-5xl font-bold mb-1">{{ number_format($currentBalance) }}</p>
                            <p class="text-lg text-purple-200">Egili</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-purple-400/30">
                            <p class="text-xs text-purple-200">
                                +{{ number_format($purchase->egili_amount) }} Egili {{ __('egili.transaction_types.purchase') }}
                            </p>
                        </div>
                    </div>
                    
                    {{-- Invoice Info --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            📄 {{ __('egili.confirmation.invoice') }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('egili.confirmation.invoice_will_be_sent') }}
                        </p>
                        @if($purchase->hasInvoice())
                        <a href="#" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg text-center transition-colors">
                            ⬇️ {{ __('egili.confirmation.download_receipt') }}
                        </a>
                        @else
                        <div class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                            {{ __('egili.confirmation.invoice_will_be_sent') }}
                        </div>
                        @endif
                    </div>
                    
                    {{-- Actions --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            🎯 {{ __('common.actions') }}
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('dashboard') }}" 
                               class="block w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition-all shadow-lg">
                                {{ __('egili.confirmation.back_to_dashboard') }}
                            </a>
                            
                            @if(request()->has('feature_code'))
                            <a href="{{ route('features.purchase', request('feature_code')) }}" 
                               class="block w-full bg-white hover:bg-gray-50 border-2 border-purple-600 text-purple-700 font-semibold py-3 px-4 rounded-lg text-center transition-all">
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

