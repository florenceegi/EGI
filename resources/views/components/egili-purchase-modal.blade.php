{{-- Egili Purchase Modal (Coming Soon) --}}
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
                        <h2 class="text-3xl font-bold">{{ __('egili.purchase_modal.title') }}</h2>
                        <p class="text-purple-100 text-sm">{{ __('egili.purchase_modal.subtitle') }}</p>
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
        
        <div class="p-8">
            {{-- Coming Soon Badge --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-800 font-semibold">
                            🚧 {{ __('egili.purchase_modal.coming_soon_badge') }}
                        </p>
                        <p class="text-xs text-yellow-700 mt-1">
                            {{ __('egili.purchase_modal.coming_soon_text') }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Features Preview --}}
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-4" style="color: #8E44AD;">
                    📋 {{ __('egili.purchase_modal.features_title') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-white text-xl">💳</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">{{ __('egili.purchase_modal.payment_fiat') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('egili.purchase_modal.payment_fiat_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-white text-xl">₿</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">{{ __('egili.purchase_modal.payment_crypto') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('egili.purchase_modal.payment_crypto_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-white text-xl">📦</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">{{ __('egili.purchase_modal.bulk_discounts') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('egili.purchase_modal.bulk_discounts_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-white text-xl">📊</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">{{ __('egili.purchase_modal.history') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('egili.purchase_modal.history_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Info Box --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-800 font-semibold mb-1">
                            💡 {{ __('egili.purchase_modal.what_is_egili_title') }}
                        </p>
                        <p class="text-sm text-blue-700">
                            {!! __('egili.purchase_modal.what_is_egili_text') !!}
                        </p>
                        <p class="text-sm text-blue-700 mt-2">
                            <strong>{{ __('egili.purchase_modal.value') }}:</strong> 1 Egili = €0.10 EUR
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Temporary Solution Box --}}
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-purple-800 font-semibold mb-1">
                            🔔 {{ __('egili.purchase_modal.temporary_solution_title') }}
                        </p>
                        <p class="text-sm text-purple-700">
                            {!! __('egili.purchase_modal.temporary_solution_text', ['email' => '<a href="mailto:support@florenceegi.com" class="underline font-semibold">support@florenceegi.com</a>']) !!}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Close Button --}}
            <div class="flex justify-center">
                <button onclick="closeEgiliPurchaseModal()" 
                        class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    ← {{ __('common.back') }}
                </button>
            </div>
            
            {{-- Footer Note --}}
            <p class="text-xs text-center text-gray-500 mt-6">
                {{ __('egili.purchase_modal.footer_note') }}
            </p>
        </div>
    </div>
</div>

<script>
// Open Egili Purchase Modal
window.openEgiliPurchaseModal = function() {
    const modal = document.getElementById('egili-purchase-modal');
    if (modal) {
        modal.classList.remove('hidden');
        // z-index 60 > feature-purchase-modal z-index 50
    }
};

// Close Egili Purchase Modal
window.closeEgiliPurchaseModal = function() {
    const modal = document.getElementById('egili-purchase-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};
</script>

