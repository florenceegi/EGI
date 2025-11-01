@props(['featureCode'])

@php
    // Pricing dal catalog (SINGLE SOURCE OF TRUTH)
    $pricing = DB::table('ai_feature_pricing')
        ->where('feature_code', $featureCode)
        ->where('is_active', true)
        ->first();
    
    // User Egili balance
    $user = Auth::user();
    $egiliBalance = $user?->wallet->egili_balance ?? 0;
    $canAfford = $pricing && $egiliBalance >= ($pricing->cost_egili ?? 0);
    $deficit = $pricing ? max(0, $pricing->cost_egili - $egiliBalance) : 0;
@endphp

{{-- Feature Purchase Modal --}}
<div id="feature-purchase-modal-{{ $featureCode }}" 
     class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center hidden"
     onclick="if(event.target === this) closeFeaturePurchaseModal('{{ $featureCode }}')">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()">
        
        @if($pricing)
            {{-- Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($pricing->icon_name)
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <span class="text-2xl">{{ $pricing->icon_name }}</span>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-bold">{{ $pricing->feature_name }}</h2>
                            <p class="text-purple-100 text-sm">{{ $pricing->feature_description }}</p>
                        </div>
                    </div>
                    <button onclick="closeFeaturePurchaseModal('{{ $featureCode }}')" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                {{-- Benefits --}}
                @if($pricing->benefits)
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-3" style="color: #8E44AD;">
                            {{ __('features.what_you_get') }}
                        </h3>
                        <ul class="space-y-2">
                            @foreach(json_decode($pricing->benefits, true) as $benefit)
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Pricing Section --}}
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-3xl font-bold" style="color: #8E44AD;">
                                {{ number_format($pricing->cost_egili) }} Egili
                            </div>
                            <div class="text-sm text-gray-600">
                                ≈ €{{ number_format($pricing->cost_egili * 0.1, 2) }}
                            </div>
                        </div>
                        
                        @if(!$pricing->is_recurring)
                            <div class="bg-white px-3 py-1 rounded-full text-sm font-semibold text-purple-700">
                                ♾️ {{ __('payment.lifetime') }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- User Balance --}}
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600">{{ __('payment.your_egili_balance') }}:</span>
                            <span class="font-bold {{ $canAfford ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($egiliBalance) }} Egili
                            </span>
                        </div>
                        
                        @if(!$canAfford)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-red-800 font-semibold text-sm">
                                            {{ __('payment.insufficient_egili_title') }}
                                        </p>
                                        <p class="text-red-700 text-xs mt-1">
                                            {{ __('payment.need_more_egili', ['amount' => number_format($deficit)]) }}
                                        </p>
                                    </div>
                                </div>
                                
                                {{-- Link per comprare Egili (futuro) --}}
                                <a href="{{ route('egili.purchase') ?? '#' }}" 
                                   class="block w-full mt-3 bg-red-600 hover:bg-red-700 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition-colors">
                                    {{ __('egili.buy_more') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Form --}}
                <form id="feature-purchase-form-{{ $featureCode }}" 
                      action="{{ route('features.purchase.process') }}" 
                      method="POST">
                    @csrf
                    <input type="hidden" name="feature_code" value="{{ $featureCode }}">
                    <input type="hidden" name="payment_method" value="egili">
                    
                    {{-- Action Buttons --}}
                    <div class="flex gap-3">
                        <button type="submit" 
                                {{ !$canAfford ? 'disabled' : '' }}
                                class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-lg transition-all">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('features.unlock_with_egili') }}
                            </span>
                        </button>
                        
                        <button type="button" 
                                onclick="closeFeaturePurchaseModal('{{ $featureCode }}')"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition-colors">
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
            
        @else
            {{-- Pricing non trovato --}}
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-red-800">{{ __('errors.feature_not_configured') }}</p>
                </div>
                <button onclick="closeFeaturePurchaseModal('{{ $featureCode }}')" 
                        class="w-full mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg">
                    {{ __('common.close') }}
                </button>
            </div>
        @endif
    </div>
</div>

<script>
// Open modal
window.openFeaturePurchaseModal = function(featureCode) {
    const modal = document.getElementById(`feature-purchase-modal-${featureCode}`);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
};

// Close modal
window.closeFeaturePurchaseModal = function(featureCode) {
    const modal = document.getElementById(`feature-purchase-modal-${featureCode}`);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
};
</script>

