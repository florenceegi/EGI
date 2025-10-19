{{--
    Component: EGI Living Panel
    Dashboard panel for EGI Vivente (SmartContract) controls and analytics
    
    @package Components
    @author Padmin D. Curtis (AI Partner OS3.0)
    @version 1.0.0 (FlorenceEGI - Dual Architecture)
    @date 2025-10-19
    
    Props:
    - egi: Egi model instance
--}}

@props(['egi'])

@php
use App\Enums\SmartContractStatus;

$smartContract = $egi->smartContract;
$subscription = $egi->livingSubscription;

if (!$smartContract) {
    return;
}

$isActive = $smartContract->sc_status === SmartContractStatus::ACTIVE->value;
$triggerReady = $smartContract->isTriggerReady();
$successRate = $smartContract->getAISuccessRate();
@endphp

<div class="bg-white rounded-2xl shadow-lg border-2 border-purple-200 overflow-hidden">
    {{-- Header with Viola Innovazione gradient --}}
    <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        EGI Vivente
                    </h3>
                    <p class="text-purple-100 text-sm">SmartContract Attivo su Algorand</p>
                </div>
            </div>
            
            @if($isActive)
                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Attivo
                </span>
            @else
                <span class="px-3 py-1 bg-gray-500 text-white rounded-full text-sm font-semibold">
                    {{ ucfirst($smartContract->sc_status) }}
                </span>
            @endif
        </div>
    </div>

    <div class="p-6 space-y-6">
        {{-- AI Analytics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Success Rate --}}
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-green-800">Successo AI</span>
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <div class="text-3xl font-bold text-green-900" style="font-family: 'Playfair Display', serif;">
                    {{ number_format($successRate, 1) }}%
                </div>
                <p class="text-xs text-green-700 mt-1">
                    {{ $smartContract->ai_executions_success }} / {{ $smartContract->ai_executions_success + $smartContract->ai_executions_failed }} analisi
                </p>
            </div>

            {{-- Next Trigger --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-800">Prossima Analisi</span>
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
                <div class="text-lg font-bold text-blue-900">
                    @if($triggerReady)
                        <span class="text-green-600 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Pronta ora
                        </span>
                    @else
                        {{ $smartContract->next_trigger_at->diffForHumans() }}
                    @endif
                </div>
                <p class="text-xs text-blue-700 mt-1">
                    Intervallo: {{ $smartContract->trigger_interval / 3600 }}h
                </p>
            </div>

            {{-- Total Triggers --}}
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-purple-800">Analisi Totali</span>
                    <i class="fas fa-history text-purple-600"></i>
                </div>
                <div class="text-3xl font-bold text-purple-900" style="font-family: 'Playfair Display', serif;">
                    {{ $smartContract->total_triggers_count }}
                </div>
                <p class="text-xs text-purple-700 mt-1">
                    Ultimo: {{ $smartContract->last_trigger_at?->diffForHumans() ?? 'Mai' }}
                </p>
            </div>
        </div>

        {{-- SmartContract Info --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-link text-blue-600"></i>
                Informazioni SmartContract
            </h4>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">App ID:</span>
                    <code class="text-blue-800 font-mono bg-blue-50 px-2 py-1 rounded">
                        {{ $smartContract->app_id }}
                    </code>
                </div>
                
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Deploy:</span>
                    <span class="text-gray-900">{{ $smartContract->deployed_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-600">Metadata Hash:</span>
                    <code class="text-gray-700 font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                        {{ Str::limit($smartContract->metadata_hash ?? 'N/A', 20) }}
                    </code>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            @if($isActive && $triggerReady)
                <button 
                    wire:click="triggerAIAnalysis"
                    class="flex-1 bg-gradient-to-r from-green-600 to-green-500 text-white px-4 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-600 transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                >
                    <i class="fas fa-play-circle"></i>
                    Avvia Analisi AI
                </button>
            @endif
            
            <a 
                href="{{ $smartContract->getExplorerUrl() }}"
                target="_blank"
                class="flex-1 bg-blue-900 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-800 transition-colors duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
            >
                <i class="fas fa-external-link-alt"></i>
                Vedi su Algorand
            </a>
        </div>

        {{-- Subscription Info --}}
        @if($subscription)
            <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-crown text-amber-600 text-xl"></i>
                    <h4 class="text-sm font-semibold text-amber-900">
                        Abbonamento {{ ucfirst($subscription->plan_type) }}
                    </h4>
                </div>
                <div class="text-xs text-amber-800 space-y-1">
                    <p><strong>Attivato:</strong> {{ $subscription->activated_at->format('d/m/Y') }}</p>
                    @if($subscription->expires_at)
                        <p><strong>Scadenza:</strong> {{ $subscription->expires_at->format('d/m/Y') }}</p>
                    @else
                        <p class="text-green-700 font-semibold">✓ Lifetime Access</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

