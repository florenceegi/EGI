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

<div class="overflow-hidden rounded-2xl border-2 border-purple-200 bg-white shadow-lg">
    {{-- Header with Viola Innovazione gradient --}}
    <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-2xl text-white"></i>
                <div>
                    <h3 class="text-xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        EGI Vivente
                    </h3>
                    <p class="text-sm text-purple-100">SmartContract Attivo su Algorand</p>
                </div>
            </div>

            @if ($isActive)
                <span
                    class="flex items-center gap-2 rounded-full bg-green-500 px-3 py-1 text-sm font-semibold text-white">
                    <i class="fas fa-check-circle"></i>
                    Attivo
                </span>
            @else
                <span class="rounded-full bg-gray-500 px-3 py-1 text-sm font-semibold text-white">
                    {{ ucfirst($smartContract->sc_status) }}
                </span>
            @endif
        </div>
    </div>

    <div class="space-y-6 p-6">
        {{-- AI Analytics Grid --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Success Rate --}}
            <div class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-green-100 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-green-800">Successo AI</span>
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
                <div class="text-3xl font-bold text-green-900" style="font-family: 'Playfair Display', serif;">
                    {{ number_format($successRate, 1) }}%
                </div>
                <p class="mt-1 text-xs text-green-700">
                    {{ $smartContract->ai_executions_success }} /
                    {{ $smartContract->ai_executions_success + $smartContract->ai_executions_failed }} analisi
                </p>
            </div>

            {{-- Next Trigger --}}
            <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-800">Prossima Analisi</span>
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
                <div class="text-lg font-bold text-blue-900">
                    @if ($triggerReady)
                        <span class="flex items-center gap-2 text-green-600">
                            <i class="fas fa-check-circle"></i>
                            Pronta ora
                        </span>
                    @else
                        {{ $smartContract->next_trigger_at->diffForHumans() }}
                    @endif
                </div>
                <p class="mt-1 text-xs text-blue-700">
                    Intervallo: {{ $smartContract->trigger_interval / 3600 }}h
                </p>
            </div>

            {{-- Total Triggers --}}
            <div class="rounded-xl border border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-purple-800">Analisi Totali</span>
                    <i class="fas fa-history text-purple-600"></i>
                </div>
                <div class="text-3xl font-bold text-purple-900" style="font-family: 'Playfair Display', serif;">
                    {{ $smartContract->total_triggers_count }}
                </div>
                <p class="mt-1 text-xs text-purple-700">
                    Ultimo: {{ $smartContract->last_trigger_at?->diffForHumans() ?? 'Mai' }}
                </p>
            </div>
        </div>

        {{-- SmartContract Info --}}
        <div class="border-t border-gray-200 pt-4">
            <h4 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                <i class="fas fa-link text-blue-600"></i>
                Informazioni SmartContract
            </h4>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between border-b border-gray-100 py-2">
                    <span class="text-gray-600">App ID:</span>
                    <code class="rounded bg-blue-50 px-2 py-1 font-mono text-blue-800">
                        {{ $smartContract->app_id }}
                    </code>
                </div>

                <div class="flex items-center justify-between border-b border-gray-100 py-2">
                    <span class="text-gray-600">Deploy:</span>
                    <span class="text-gray-900">{{ $smartContract->deployed_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-600">Metadata Hash:</span>
                    <code class="rounded bg-gray-100 px-2 py-1 font-mono text-xs text-gray-700">
                        {{ Str::limit($smartContract->metadata_hash ?? 'N/A', 20) }}
                    </code>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 border-t border-gray-200 pt-4">
            @if ($isActive && $triggerReady)
                <button wire:click="triggerAIAnalysis"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-500 px-4 py-3 font-semibold text-white shadow-md transition-all duration-200 hover:from-green-700 hover:to-green-600 hover:shadow-lg">
                    <i class="fas fa-play-circle"></i>
                    Avvia Analisi AI
                </button>
            @endif

            <a href="{{ $smartContract->getExplorerUrl() }}" target="_blank"
                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-3 font-semibold text-white shadow-md transition-colors duration-200 hover:bg-blue-800 hover:shadow-lg">
                <i class="fas fa-external-link-alt"></i>
                Vedi su Algorand
            </a>
        </div>

        {{-- Subscription Info --}}
        @if ($subscription)
            <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-4">
                <div class="mb-2 flex items-center gap-3">
                    <i class="fas fa-crown text-xl text-amber-600"></i>
                    <h4 class="text-sm font-semibold text-amber-900">
                        Abbonamento {{ ucfirst($subscription->plan_type) }}
                    </h4>
                </div>
                <div class="space-y-1 text-xs text-amber-800">
                    <p><strong>Attivato:</strong> {{ $subscription->activated_at->format('d/m/Y') }}</p>
                    @if ($subscription->expires_at)
                        <p><strong>Scadenza:</strong> {{ $subscription->expires_at->format('d/m/Y') }}</p>
                    @else
                        <p class="font-semibold text-green-700">✓ Lifetime Access</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

