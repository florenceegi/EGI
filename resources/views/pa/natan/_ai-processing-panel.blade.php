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
<div id="aiProcessingPanel" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="w-full max-w-3xl mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-4xl animate-pulse">
                            psychology
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-white text-2xl font-bold" id="aiPanelTitle">
                        🤖 N.A.T.A.N. AI sta elaborando la tua richiesta...
                    </h3>
                    <p class="text-white/80 text-sm mt-1" id="aiPanelSubtitle">
                        Analisi semantica in corso su database atti amministrativi
                    </p>
                </div>
            </div>
        </div>

        {{-- Progress Section --}}
        <div class="px-8 py-6 space-y-6">
            
            {{-- Main Progress Bar --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Avanzamento elaborazione</span>
                    <span class="text-sm font-semibold text-[#1B365D]" id="aiProgressPercentage">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div id="aiProgressBar" 
                         class="h-full bg-gradient-to-r from-[#1B365D] via-[#2D5016] to-[#D4A574] rounded-full transition-all duration-500 ease-out"
                         style="width: 0%">
                    </div>
                </div>
            </div>

            {{-- Processing Stages --}}
            <div class="space-y-3" id="aiProcessingStages">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-green-50 border border-green-200" id="stage-search">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Ricerca semantica completata</p>
                        <p class="text-xs text-gray-600" id="stage-search-detail">0 atti trovati nel database</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 border border-blue-200" id="stage-context">
                    <div class="animate-spin">
                        <span class="material-symbols-outlined text-blue-600">sync</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Selezione contestuale in corso...</p>
                        <p class="text-xs text-gray-600" id="stage-context-detail">Ottimizzazione carico AI</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-200" id="stage-ai">
                    <span class="material-symbols-outlined text-gray-400">pending</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Analisi AI con Claude Sonnet 4.5</p>
                        <p class="text-xs text-gray-600" id="stage-ai-detail">In attesa...</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-200" id="stage-response">
                    <span class="material-symbols-outlined text-gray-400">pending</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Generazione risposta strutturata</p>
                        <p class="text-xs text-gray-600" id="stage-response-detail">In attesa...</p>
                    </div>
                </div>
            </div>

            {{-- Live Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl">
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Atti analizzati</p>
                    <p class="text-2xl font-bold text-[#1B365D]" id="stat-acts">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Rilevanza</p>
                    <p class="text-2xl font-bold text-[#2D5016]" id="stat-relevance">0%</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Tempo</p>
                    <p class="text-2xl font-bold text-[#D4A574]" id="stat-time">00:00</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-600 mb-1">Modello AI</p>
                    <p class="text-xs font-semibold text-gray-700" id="stat-model">Claude<br>Sonnet 4.5</p>
                </div>
            </div>

            {{-- Adaptive Retry Info (hidden by default, shown when retry occurs) --}}
            <div id="aiRetryInfo" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-yellow-600 mt-0.5">info</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 mb-1">⚡ Ottimizzazione carico AI in corso</p>
                        <p class="text-xs text-gray-700" id="aiRetryDetail">
                            Riduzione contesto per compliance rate limits...
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 bg-yellow-200 rounded-full h-2 overflow-hidden">
                                <div id="aiRetryProgress" class="h-full bg-yellow-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700" id="aiRetryCountdown">0s</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-200">
            <p class="text-xs text-gray-600 text-center">
                💡 <strong>Enterprise AI Processing:</strong> Il sistema sta elaborando grandi volumi di dati strutturati per fornire risposte accurate e contestualizzate.
            </p>
        </div>

    </div>
</div>
