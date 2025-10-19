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

@if (!$isCreator)
    @php return; @endphp
@endif

<div class="overflow-hidden rounded-2xl border-2 border-green-200 bg-white shadow-lg">
    {{-- Header with Verde Rinascita --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-hammer text-2xl text-white"></i>
            <div>
                <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    Auto-Mint Creator
                </h3>
                <p class="text-sm text-green-100">Minta tu stesso la tua opera</p>
            </div>
        </div>
    </div>

    <div class="space-y-6 p-6">
        @if (!$egi->auto_mint_enabled)
            {{-- Enable Auto-Mint Section --}}
            <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-5">
                <div class="flex items-start gap-4">
                    <i class="fas fa-lightbulb mt-1 text-2xl text-blue-600"></i>
                    <div>
                        <h4 class="mb-2 text-lg font-bold text-blue-900">
                            Vuoi mintare personalmente questo EGI?
                        </h4>
                        <p class="mb-4 text-sm text-blue-800">
                            Abilita l'Auto-Mint per riservare a te stesso il mint di questa opera.
                            Potrai scegliere tra EGI Classico o EGI Vivente e diventerai automaticamente il
                            proprietario.
                        </p>
                        <button wire:click="enableAutoMint"
                            class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-6 py-3 font-bold text-white shadow-md transition-all duration-200 hover:from-green-700 hover:to-green-600 hover:shadow-lg">
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
                <div class="rounded-xl border-2 border-green-200 bg-green-50 p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                        <div>
                            <p class="font-semibold text-green-900">Auto-Mint Abilitato</p>
                            <p class="text-sm text-green-700">Puoi mintare questa opera quando vuoi</p>
                        </div>
                    </div>
                </div>

                {{-- Mint Options --}}
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="mb-4 text-lg font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">
                        Scegli il tipo di mint:
                    </h4>

                    <div class="space-y-3">
                        {{-- Mint as ASA --}}
                        <button wire:click="openCreatorMintModal('ASA')"
                            class="w-full rounded-xl border-2 border-blue-700 bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-5 text-left font-bold text-white shadow-lg transition-all duration-200 hover:from-blue-950 hover:to-blue-900 hover:shadow-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <i class="fas fa-shield-check text-3xl"></i>
                                    <div>
                                        <div class="text-lg">EGI Classico (ASA)</div>
                                        <div class="mt-1 text-sm font-normal text-blue-200">
                                            Asset statico su blockchain Algorand
                                        </div>
                                        <div class="mt-2 flex items-center gap-2 text-xs font-normal text-blue-300">
                                            <i class="fas fa-check-circle"></i>
                                            Gratuito • Permanente • Sicuro
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right text-2xl"></i>
                            </div>
                        </button>

                        {{-- Mint as SmartContract --}}
                        <button wire:click="openCreatorMintModal('SmartContract')"
                            class="w-full rounded-xl border-2 border-purple-400 bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-5 text-left font-bold text-white shadow-lg transition-all duration-200 hover:from-purple-800 hover:to-purple-700 hover:shadow-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <i class="fas fa-brain text-3xl"></i>
                                    <div>
                                        <div class="flex items-center gap-2 text-lg">
                                            EGI Vivente (SmartContract)
                                            <span class="rounded-full bg-amber-500 px-2 py-1 text-xs">PREMIUM</span>
                                        </div>
                                        <div class="mt-1 text-sm font-normal text-purple-200">
                                            Asset intelligente con AI Curator integrata
                                        </div>
                                        <div class="mt-2 space-y-1 text-xs font-normal text-purple-300">
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
                                        <div class="mt-3 text-sm font-semibold text-amber-300">
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
                    <button wire:click="disableAutoMint"
                        class="flex items-center gap-2 text-sm text-gray-600 transition-colors duration-200 hover:text-red-600">
                        <i class="fas fa-times-circle"></i>
                        Disabilita Auto-Mint
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
