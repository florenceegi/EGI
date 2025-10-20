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
    $daysRemaining = $egi->pre_mint_created_at
        ? config('egi_living.pre_mint.max_duration_days') - now()->diffInDays($egi->pre_mint_created_at)
        : null;
@endphp

<div class="overflow-hidden rounded-2xl border-2 border-amber-200 bg-white shadow-lg">
    {{-- Header with Oro Fiorentino gradient --}}
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-seedling text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        EGI Pre-Mint
                    </h3>
                    <p class="text-sm text-amber-100">Asset virtuale in crescita</p>
                </div>
            </div>

            @if ($daysRemaining !== null && $daysRemaining > 0)
                <span
                    class="flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-sm font-semibold text-white backdrop-blur-sm">
                    <i class="fas fa-clock"></i>
                    {{ $daysRemaining }} giorni
                </span>
            @endif
        </div>
    </div>

    <div class="space-y-6 p-6">
        {{-- Info Box --}}
        <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle mt-1 text-xl text-blue-600"></i>
                <div class="text-sm text-blue-900">
                    <p class="mb-2 font-semibold">Cos'è il Pre-Mint?</p>
                    <p class="text-blue-800">
                        Il tuo EGI è in modalità virtuale. Puoi testarlo, promuoverlo e far analizzare
                        i metadati dall'AI prima di mintarlo sulla blockchain.
                    </p>
                </div>
            </div>
        </div>

        {{-- Status Grid --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- Creato il --}}
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Creato il</span>
                    <i class="fas fa-calendar text-gray-500"></i>
                </div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $egi->pre_mint_created_at?->format('d/m/Y H:i') ?? $egi->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- Tempo rimanente --}}
            @if ($daysRemaining !== null)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-amber-800">Scadenza</span>
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                    </div>
                    <div class="{{ $daysRemaining <= 7 ? 'text-red-600' : 'text-amber-900' }} text-lg font-semibold">
                        @if ($daysRemaining <= 0)
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
            <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                <i class="fas fa-robot text-purple-600"></i>
                Analisi AI N.A.T.A.N
            </h4>

            <div class="space-y-3">
                <button wire:click="requestAIDescription"
                    class="flex w-full items-center justify-between rounded-lg border border-purple-200 bg-gradient-to-r from-purple-100 to-purple-50 px-4 py-3 font-medium text-purple-900 transition-all duration-200 hover:from-purple-200 hover:to-purple-100">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        Genera Descrizione AI
                    </span>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button wire:click="requestAITraits"
                    class="flex w-full items-center justify-between rounded-lg border border-blue-200 bg-gradient-to-r from-blue-100 to-blue-50 px-4 py-3 font-medium text-blue-900 transition-all duration-200 hover:from-blue-200 hover:to-blue-100">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-tags"></i>
                        Estrai Traits con AI
                    </span>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button wire:click="requestAIPromotion"
                    class="flex w-full items-center justify-between rounded-lg border border-green-200 bg-gradient-to-r from-green-100 to-green-50 px-4 py-3 font-medium text-green-900 transition-all duration-200 hover:from-green-200 hover:to-green-100">
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
            <h4 class="mb-4 text-lg font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">
                Pronto per il mint su blockchain?
            </h4>

            <div class="space-y-3">
                <button wire:click="openPromoteModal('ASA')"
                    class="flex w-full items-center justify-between rounded-xl bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-4 font-bold text-white shadow-lg transition-all duration-200 hover:from-blue-950 hover:to-blue-900 hover:shadow-xl">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-shield-check text-xl"></i>
                        <div class="text-left">
                            <div>Promuovi a EGI Classico</div>
                            <div class="text-xs font-normal text-blue-200">Asset statico su blockchain</div>
                        </div>
                    </span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <button wire:click="openPromoteModal('SmartContract')"
                    class="flex w-full items-center justify-between rounded-xl border-2 border-purple-400 bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 font-bold text-white shadow-lg transition-all duration-200 hover:from-purple-800 hover:to-purple-700 hover:shadow-xl">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-brain text-xl"></i>
                        <div class="text-left">
                            <div class="flex items-center gap-2">
                                Promuovi a EGI Vivente
                                <span class="rounded-full bg-amber-500 px-2 py-0.5 text-xs">PREMIUM</span>
                            </div>
                            <div class="text-xs font-normal text-purple-200">Con AI Curator e funzioni intelligenti
                            </div>
                        </div>
                    </span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

