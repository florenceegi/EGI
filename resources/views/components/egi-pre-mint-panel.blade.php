{{--
    Component: EGI Pre-Mint Panel
    Panel for Pre-Mint EGI with promotion options and AI analysis
    
    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-19
    
    Props:
    - egi: Egi model instance
--}}

@props(['egi'])

@php
$daysRemaining = $egi->pre_mint_created_at ? 
    config('egi_living.pre_mint.max_duration_days') - now()->diffInDays($egi->pre_mint_created_at) : 
    null;
@endphp

<div class="bg-white rounded-2xl shadow-lg border-2 border-amber-200 overflow-hidden">
    {{-- Header with Oro Fiorentino gradient --}}
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-seedling text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        EGI Pre-Mint
                    </h3>
                    <p class="text-amber-100 text-sm">Asset virtuale in crescita</p>
                </div>
            </div>
            
            @if($daysRemaining !== null && $daysRemaining > 0)
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    {{ $daysRemaining }} giorni
                </span>
            @endif
        </div>
    </div>

    <div class="p-6 space-y-6">
        {{-- Info Box --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border-2 border-blue-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                <div class="text-sm text-blue-900">
                    <p class="font-semibold mb-2">Cos'è il Pre-Mint?</p>
                    <p class="text-blue-800">
                        Il tuo EGI è in modalità virtuale. Puoi testarlo, promuoverlo e far analizzare 
                        i metadati dall'AI prima di mintarlo sulla blockchain.
                    </p>
                </div>
            </div>
        </div>

        {{-- Status Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Creato il --}}
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Creato il</span>
                    <i class="fas fa-calendar text-gray-500"></i>
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $egi->pre_mint_created_at?->format('d/m/Y H:i') ?? $egi->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- Tempo rimanente --}}
            @if($daysRemaining !== null)
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-amber-800">Scadenza</span>
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                    </div>
                    <div class="text-lg font-semibold {{ $daysRemaining <= 7 ? 'text-red-600' : 'text-amber-900' }}">
                        @if($daysRemaining <= 0)
                            Scaduto
                        @elseif($daysRemaining <= 7)
                            <span class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $daysRemaining }} giorni
                            </span>
                        @else
                            {{ $daysRemaining }} giorni
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- AI Analysis Section --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-robot text-purple-600"></i>
                Analisi AI N.A.T.A.N
            </h4>
            
            <div class="space-y-3">
                <button 
                    wire:click="requestAIDescription"
                    class="w-full bg-gradient-to-r from-purple-100 to-purple-50 text-purple-900 px-4 py-3 rounded-lg font-medium hover:from-purple-200 hover:to-purple-100 transition-all duration-200 flex items-center justify-between border border-purple-200"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        Genera Descrizione AI
                    </span>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button 
                    wire:click="requestAITraits"
                    class="w-full bg-gradient-to-r from-blue-100 to-blue-50 text-blue-900 px-4 py-3 rounded-lg font-medium hover:from-blue-200 hover:to-blue-100 transition-all duration-200 flex items-center justify-between border border-blue-200"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-tags"></i>
                        Estrai Traits con AI
                    </span>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button 
                    wire:click="requestAIPromotion"
                    class="w-full bg-gradient-to-r from-green-100 to-green-50 text-green-900 px-4 py-3 rounded-lg font-medium hover:from-green-200 hover:to-green-100 transition-all duration-200 flex items-center justify-between border border-green-200"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-bullhorn"></i>
                        Strategia Promozione AI
                    </span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- Promotion Actions --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">
                Pronto per il mint su blockchain?
            </h4>
            
            <div class="space-y-3">
                <button 
                    wire:click="openPromoteModal('ASA')"
                    class="w-full bg-gradient-to-r from-blue-900 to-blue-800 text-white px-6 py-4 rounded-xl font-bold hover:from-blue-950 hover:to-blue-900 transition-all duration-200 flex items-center justify-between shadow-lg hover:shadow-xl"
                >
                    <span class="flex items-center gap-3">
                        <i class="fas fa-shield-check text-xl"></i>
                        <div class="text-left">
                            <div>Promuovi a EGI Classico</div>
                            <div class="text-xs font-normal text-blue-200">Asset statico su blockchain</div>
                        </div>
                    </span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <button 
                    wire:click="openPromoteModal('SmartContract')"
                    class="w-full bg-gradient-to-r from-purple-700 to-purple-600 text-white px-6 py-4 rounded-xl font-bold hover:from-purple-800 hover:to-purple-700 transition-all duration-200 flex items-center justify-between shadow-lg hover:shadow-xl border-2 border-purple-400"
                >
                    <span class="flex items-center gap-3">
                        <i class="fas fa-brain text-xl"></i>
                        <div class="text-left">
                            <div class="flex items-center gap-2">
                                Promuovi a EGI Vivente
                                <span class="px-2 py-0.5 bg-amber-500 text-xs rounded-full">PREMIUM</span>
                            </div>
                            <div class="text-xs font-normal text-purple-200">Con AI Curator e funzioni intelligenti</div>
                        </div>
                    </span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

