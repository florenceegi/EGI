{{--
/**
 * AI Processing Panel Component
 *
 * @package Resources\Views\Pa\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-27
 * @purpose Professional AI processing visualization for enterprise demos
 *
 * FEATURES:
 * - Multi-stage progress visualization
 * - Live stats counter
 * - Adaptive retry status display
 * - Professional animations
 * - WOW factor for PA presentations
 */
--}}

{{-- AI Processing Panel (hidden by default, shown via JavaScript) --}}
<div id="aiProcessingPanel" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm"
    style="display: none;">
    <div class="mx-4 w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/10 backdrop-blur-sm">
                        <span class="material-symbols-outlined animate-pulse text-4xl text-white" id="aiPanelIcon">
                            psychology
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-white" id="aiPanelTitle">
                        🤖 N.A.T.A.N. AI sta elaborando la tua richiesta...
                    </h3>
                    <p class="mt-1 text-sm text-white/80" id="aiPanelSubtitle">
                        Analisi semantica in corso su database atti amministrativi
                    </p>
                </div>
            </div>
        </div>

        {{-- Progress Section --}}
        <div class="space-y-6 px-8 py-6">

            {{-- Main Progress Bar --}}
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Avanzamento elaborazione</span>
                    <span class="text-sm font-semibold text-[#1B365D]" id="aiProgressPercentage">0%</span>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
                    <div id="aiProgressBar"
                        class="h-full rounded-full bg-gradient-to-r from-[#1B365D] via-[#2D5016] to-[#D4A574] transition-all duration-500 ease-out"
                        style="width: 0%">
                    </div>
                </div>
            </div>

            {{-- Processing Stages --}}
            <div class="space-y-3" id="aiProcessingStages">
                <div class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-3"
                    id="stage-search">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Ricerca semantica completata</p>
                        <p class="text-xs text-gray-600" id="stage-search-detail">0 atti trovati nel database</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3"
                    id="stage-context">
                    <div class="animate-spin">
                        <span class="material-symbols-outlined text-blue-600">sync</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Selezione contestuale in corso...</p>
                        <p class="text-xs text-gray-600" id="stage-context-detail">Ottimizzazione carico AI</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3" id="stage-ai">
                    <span class="material-symbols-outlined text-gray-400">pending</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Analisi AI con Claude Sonnet 4.5</p>
                        <p class="text-xs text-gray-600" id="stage-ai-detail">In attesa...</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3"
                    id="stage-response">
                    <span class="material-symbols-outlined text-gray-400">pending</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Generazione risposta strutturata</p>
                        <p class="text-xs text-gray-600" id="stage-response-detail">In attesa...</p>
                    </div>
                </div>
            </div>

            {{-- Live Stats --}}
            <div class="grid grid-cols-2 gap-4 rounded-xl bg-gray-50 p-4 md:grid-cols-4">
                <div class="text-center">
                    <p class="mb-1 text-xs text-gray-600">Atti analizzati</p>
                    <p class="text-2xl font-bold text-[#1B365D]" id="stat-acts">0</p>
                </div>
                <div class="text-center">
                    <p class="mb-1 text-xs text-gray-600">Rilevanza</p>
                    <p class="text-2xl font-bold text-[#2D5016]" id="stat-relevance">0%</p>
                </div>
                <div class="text-center">
                    <p class="mb-1 text-xs text-gray-600">Tempo</p>
                    <p class="text-2xl font-bold text-[#D4A574]" id="stat-time">00:00</p>
                </div>
                <div class="text-center">
                    <p class="mb-1 text-xs text-gray-600">Modello AI</p>
                    <p class="text-xs font-semibold text-gray-700" id="stat-model">Claude<br>Sonnet 4.5</p>
                </div>
            </div>

            {{-- ✨ NEW v5.0 - Real-Time Cost Tracking --}}
            <div id="aiCostTracking"
                class="hidden rounded-xl border border-[#E67E22]/30 bg-gradient-to-br from-[#E67E22]/5 to-white p-6 shadow-sm">
                {{-- Cost Header --}}
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E67E22]/20">
                            <span class="material-symbols-outlined text-[#E67E22]">payments</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#1B365D]">{{ __('ai_credits.realtime.panel_title') }}</h4>
                            <p class="text-xs text-gray-600">{{ __('ai_credits.realtime.tokens_used') }}</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-[#E67E22] px-3 py-1">
                        <span class="text-sm font-bold text-white" id="costCurrentCredits">0</span>
                        <span class="text-xs text-white/80">{{ __('ai_credits.balance.credits') }}</span>
                    </div>
                </div>

                {{-- Cost Breakdown --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-lg bg-blue-50 p-3 text-center">
                        <p class="text-xs text-gray-600">{{ __('ai_credits.realtime.input') }}</p>
                        <p class="text-lg font-bold text-blue-600" id="costInputTokens">0</p>
                        <p class="text-xs text-gray-500">tokens</p>
                    </div>
                    <div class="rounded-lg bg-purple-50 p-3 text-center">
                        <p class="text-xs text-gray-600">{{ __('ai_credits.realtime.output') }}</p>
                        <p class="text-lg font-bold text-purple-600" id="costOutputTokens">0</p>
                        <p class="text-xs text-gray-500">tokens</p>
                    </div>
                    <div class="rounded-lg bg-orange-50 p-3 text-center">
                        <p class="text-xs text-gray-600">{{ __('ai_credits.realtime.current_cost') }}</p>
                        <p class="text-lg font-bold text-orange-600" id="costCurrentTotal">0.00</p>
                        <p class="text-xs text-gray-500">{{ __('ai_credits.balance.credits') }}</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3 text-center">
                        <p class="text-xs text-gray-600">{{ __('ai_credits.realtime.estimated_final') }}</p>
                        <p class="text-lg font-bold text-green-600" id="costEstimatedFinal">0.00</p>
                        <p class="text-xs text-gray-500">{{ __('ai_credits.balance.credits') }}</p>
                    </div>
                </div>

                {{-- Per-Chunk Costs (for chunking mode) --}}
                <div id="costPerChunk" class="mt-3 hidden space-y-2">
                    <p class="text-xs font-medium text-gray-700">{{ __('ai_credits.summary.cost_per_chunk') }}:</p>
                    <div class="max-h-32 space-y-1 overflow-y-auto" id="costChunksList">
                        {{-- Dynamically populated by JavaScript --}}
                    </div>
                </div>
            </div>

            {{-- ✨ NEW v4.0 - Chunking Progress (hidden by default, shown when chunking active) --}}
            <div id="aiChunkingProgress"
                class="hidden rounded-xl border border-[#D4A574]/30 bg-gradient-to-br from-[#D4A574]/5 to-white p-6 shadow-sm">
                {{-- Chunking Header --}}
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#D4A574]/20">
                            <span class="material-symbols-outlined text-[#D4A574]">splitscreen</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#1B365D]">Elaborazione Dataset Grande</h4>
                            <p class="text-xs text-gray-600" id="chunkingSubtitle">Dividendo in chunk per ottimizzare
                                carico AI...</p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-[#D4A574] px-3 py-1">
                        <span class="text-sm font-bold text-white" id="chunkCurrentCount">0/0</span>
                    </div>
                </div>

                {{-- Chunks Visual Grid --}}
                <div class="mb-4 grid grid-cols-5 gap-2 sm:grid-cols-10" id="chunksGrid">
                    {{-- Populated dynamically by JavaScript --}}
                </div>

                {{-- Current Chunk Details --}}
                <div class="space-y-3">
                    {{-- Chunk Progress Bar --}}
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="font-medium text-gray-700" id="chunkProgressLabel">Chunk 1: Analizzando 180
                                atti...</span>
                            <span class="font-semibold text-[#D4A574]" id="chunkProgressPercentage">0%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                            <div id="chunkProgressBar"
                                class="h-full rounded-full bg-gradient-to-r from-[#D4A574] to-[#E67E22] transition-all duration-500"
                                style="width: 0%">
                            </div>
                        </div>
                    </div>

                    {{-- Chunk Results Summary --}}
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg bg-green-50 p-2">
                            <p class="text-xs text-gray-600">✅ Completati</p>
                            <p class="text-lg font-bold text-green-600" id="chunksCompleted">0</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-2">
                            <p class="text-xs text-gray-600">⚙️ In corso</p>
                            <p class="text-lg font-bold text-blue-600" id="chunksProcessing">0</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-2">
                            <p class="text-xs text-gray-600">⏳ In attesa</p>
                            <p class="text-lg font-bold text-gray-600" id="chunksPending">0</p>
                        </div>
                    </div>

                    {{-- Real-time Results Preview --}}
                    <div id="chunkResultsPreview" class="hidden rounded-lg border border-green-200 bg-green-50 p-3">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-sm text-green-600">checklist</span>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-900">Risultati parziali:</p>
                                <p class="mt-1 text-xs text-gray-700" id="chunkResultsText">
                                    Chunk 1: 23 atti rilevanti trovati<br>
                                    Chunk 2: 17 atti rilevanti trovati
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Adaptive Retry Info (hidden by default, shown when retry occurs) --}}
            <div id="aiRetryInfo" class="hidden rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-0.5 text-yellow-600">info</span>
                    <div class="flex-1">
                        <p class="mb-1 text-sm font-medium text-gray-900">⚡ Ottimizzazione carico AI in corso</p>
                        <p class="text-xs text-gray-700" id="aiRetryDetail">
                            Riduzione contesto per compliance rate limits...
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-yellow-200">
                                <div id="aiRetryProgress"
                                    class="h-full rounded-full bg-yellow-600 transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700" id="aiRetryCountdown">0s</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 bg-gray-50 px-8 py-4">
            <p class="text-center text-xs text-gray-600">
                💡 <strong>Enterprise AI Processing:</strong> Il sistema sta elaborando grandi volumi di dati
                strutturati per fornire risposte accurate e contestualizzate.
            </p>
        </div>

    </div>
</div>
