{{--
    Component: EGI Auto-Mint Panel
    Panel for Creator to self-mint their own EGI
    
    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-19
    
    Props:
    - egi: Egi model instance
    - isCreator: Boolean indicating if current user is the creator
--}}

@props(['egi', 'isCreator'])

@if(!$isCreator)
    @php return; @endphp
@endif

<div class="bg-white rounded-2xl shadow-lg border-2 border-green-200 overflow-hidden">
    {{-- Header with Verde Rinascita --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-hammer text-2xl text-white"></i>
            <div>
                <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    Auto-Mint Creator
                </h3>
                <p class="text-green-100 text-sm">Minta tu stesso la tua opera</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">
        @if(!$egi->auto_mint_enabled)
            {{-- Enable Auto-Mint Section --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200">
                <div class="flex items-start gap-4">
                    <i class="fas fa-lightbulb text-blue-600 text-2xl mt-1"></i>
                    <div>
                        <h4 class="text-lg font-bold text-blue-900 mb-2">
                            Vuoi mintare personalmente questo EGI?
                        </h4>
                        <p class="text-sm text-blue-800 mb-4">
                            Abilita l'Auto-Mint per riservare a te stesso il mint di questa opera. 
                            Potrai scegliere tra EGI Classico o EGI Vivente e diventerai automaticamente il proprietario.
                        </p>
                        <button 
                            wire:click="enableAutoMint"
                            class="bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-lg font-bold hover:from-green-700 hover:to-green-600 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-check-circle"></i>
                            Abilita Auto-Mint
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- Auto-Mint Enabled - Show Minting Options --}}
            <div class="space-y-4">
                {{-- Info --}}
                <div class="bg-green-50 rounded-xl p-4 border-2 border-green-200">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <div>
                            <p class="font-semibold text-green-900">Auto-Mint Abilitato</p>
                            <p class="text-sm text-green-700">Puoi mintare questa opera quando vuoi</p>
                        </div>
                    </div>
                </div>

                {{-- Mint Options --}}
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">
                        Scegli il tipo di mint:
                    </h4>

                    <div class="space-y-3">
                        {{-- Mint as ASA --}}
                        <button 
                            wire:click="openCreatorMintModal('ASA')"
                            class="w-full bg-gradient-to-r from-blue-900 to-blue-800 text-white px-6 py-5 rounded-xl font-bold hover:from-blue-950 hover:to-blue-900 transition-all duration-200 text-left shadow-lg hover:shadow-xl border-2 border-blue-700"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <i class="fas fa-shield-check text-3xl"></i>
                                    <div>
                                        <div class="text-lg">EGI Classico (ASA)</div>
                                        <div class="text-sm font-normal text-blue-200 mt-1">
                                            Asset statico su blockchain Algorand
                                        </div>
                                        <div class="text-xs font-normal text-blue-300 mt-2 flex items-center gap-2">
                                            <i class="fas fa-check-circle"></i>
                                            Gratuito • Permanente • Sicuro
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right text-2xl"></i>
                            </div>
                        </button>

                        {{-- Mint as SmartContract --}}
                        <button 
                            wire:click="openCreatorMintModal('SmartContract')"
                            class="w-full bg-gradient-to-r from-purple-700 to-purple-600 text-white px-6 py-5 rounded-xl font-bold hover:from-purple-800 hover:to-purple-700 transition-all duration-200 text-left shadow-lg hover:shadow-xl border-2 border-purple-400"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <i class="fas fa-brain text-3xl"></i>
                                    <div>
                                        <div class="text-lg flex items-center gap-2">
                                            EGI Vivente (SmartContract)
                                            <span class="px-2 py-1 bg-amber-500 text-xs rounded-full">PREMIUM</span>
                                        </div>
                                        <div class="text-sm font-normal text-purple-200 mt-1">
                                            Asset intelligente con AI Curator integrata
                                        </div>
                                        <div class="text-xs font-normal text-purple-300 mt-2 space-y-1">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-check-circle"></i>
                                                Analisi AI automatiche
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-check-circle"></i>
                                                Promozione intelligente
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-check-circle"></i>
                                                Memoria evolutiva
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold text-amber-300 mt-3">
                                            Da €{{ config('egi_living.subscription_plans.one_time.price_eur') }}
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right text-2xl"></i>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Disable Auto-Mint --}}
                <div class="border-t border-gray-200 pt-4">
                    <button 
                        wire:click="disableAutoMint"
                        class="text-sm text-gray-600 hover:text-red-600 transition-colors duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-times-circle"></i>
                        Disabilita Auto-Mint
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

