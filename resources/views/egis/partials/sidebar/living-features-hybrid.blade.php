{{--
    EGI Living Features - HYBRID APPROACH
    🎯 Purpose: Show Living features with @can() permission check
    🛡️ Payment: Auto-unlock after purchase via Spatie permissions
    📍 Location: egis/show.blade.php sidebar

    Required variables:
    - $egi: EGI model instance
    - $isCreator: Boolean if current user is creator
--}}

@php
    // Pricing dal catalog (SINGLE SOURCE OF TRUTH)
    $livingPricing = DB::table('ai_feature_pricing')
        ->where('feature_code', 'egi_living_subscription')
        ->where('is_active', true)
        ->first();
    
    // User Egili balance
    $egiliBalance = Auth::user()->primaryWallet->egili_balance ?? 0;
    $canPayWithEgili = $livingPricing && $egiliBalance >= ($livingPricing->cost_egili ?? 0);
@endphp

@if($isCreator && $egi->egi_type === 'SmartContract')
    
    {{-- SPATIE PERMISSION CHECK (Hybrid Approach) --}}
    @can('use-egi-living')
        
        {{-- ✅ USER HA LA PERMISSION = HA GIÀ PAGATO --}}
        <div class="bg-gradient-to-br from-green-900/20 to-emerald-900/20 rounded-xl border border-green-500/30 backdrop-blur-sm p-4 mb-6">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-green-800">
                        {{ __('egi_living.status.active') }}
                    </h3>
                    <p class="text-xs text-gray-600">
                        {{ __('egi_living.status.active_since') }} {{ $egi->egi_living_activated_at?->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            {{-- Active Features Buttons --}}
            <div class="space-y-2">
                <button onclick="runAICurator({{ $egi->id }})" 
                        class="w-full bg-white hover:bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm text-left transition-colors">
                    <span class="font-semibold">🤖 {{ __('egi_living.curator.title') }}</span>
                    <p class="text-xs text-gray-600">{{ __('egi_living.curator.description_short') }}</p>
                </button>
                
                <button onclick="runAIPromoter({{ $egi->id }})" 
                        class="w-full bg-white hover:bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm text-left transition-colors">
                    <span class="font-semibold">📢 {{ __('egi_living.promoter.title') }}</span>
                    <p class="text-xs text-gray-600">{{ __('egi_living.promoter.description_short') }}</p>
                </button>
                
                <button onclick="viewProvenanceGraph({{ $egi->id }})" 
                        class="w-full bg-white hover:bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm text-left transition-colors">
                    <span class="font-semibold">📊 {{ __('egi_living.provenance.title') }}</span>
                    <p class="text-xs text-gray-600">{{ __('egi_living.provenance.description_short') }}</p>
                </button>
            </div>
        </div>
    
    @else
        
        {{-- ❌ USER NON HA PERMISSION = DEVE PAGARE --}}
        @if($livingPricing)
            <div class="bg-gradient-to-br from-purple-900/20 to-blue-900/20 rounded-xl border-2 border-purple-500/40 backdrop-blur-sm p-5 mb-6 animate-pulse-slow">
                
                {{-- Lock Icon + Title --}}
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-blue-600 flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg" style="color: #8E44AD;">
                            {{ $livingPricing->feature_name }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $livingPricing->feature_description }}
                        </p>
                    </div>
                </div>
                
                {{-- Benefits (da JSON in ai_feature_pricing) --}}
                @if($livingPricing->benefits)
                    <div class="bg-white/50 rounded-lg p-3 mb-4 space-y-1 text-sm">
                        @foreach(json_decode($livingPricing->benefits, true) as $benefit)
                            <div class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- Pricing (DA PREZZARIO DB - NO HARDCODED!) --}}
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-2xl font-bold" style="color: #8E44AD;">
                            €{{ number_format($livingPricing->cost_fiat_eur, 2) }}
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ __('common.or') }} {{ $livingPricing->cost_egili }} Egili
                        </div>
                    </div>
                    
                    {{-- User Egili Balance --}}
                    <div class="text-right text-sm">
                        <div class="text-gray-600">{{ __('payment.your_balance') }}:</div>
                        <div class="font-bold {{ $canPayWithEgili ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($egiliBalance) }} Egili
                        </div>
                    </div>
                </div>
                
                {{-- CTA Button (Apre modale generica) --}}
                <button onclick="openFeaturePurchaseModal('{{ $livingPricing->feature_code }}')" 
                        class="block w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition-all transform hover:scale-105 shadow-lg">
                    {{ __('features.unlock_now') }}
                </button>
                
                {{-- One-time/Recurring badge --}}
                @if(!$livingPricing->is_recurring)
                    <p class="text-xs text-gray-500 text-center mt-2">
                        ♾️ {{ __('payment.one_time_lifetime') }}
                    </p>
                @else
                    <p class="text-xs text-gray-500 text-center mt-2">
                        🔄 {{ __('payment.recurring_' . $livingPricing->recurrence_period) }}
                    </p>
                @endif
            </div>
        @else
            {{-- Pricing non configurato --}}
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800 text-sm">
                    {{ __('errors.feature_pricing_not_configured') }}
                </p>
            </div>
        @endif
    
    @endcan

@endif

