{{--
/**
 * PA Web Scrapers Index View
 *
 * ============================================================================
 * CONTESTO - LISTA SCRAPERS WEB
 * ============================================================================
 *
 * View per la gestione degli agenti di web scraping configurati per acquisizione automatica
 * di atti pubblici da fonti web esterne (API, Albo Pretorio, Portali Trasparenza).
 *
 * TARGET USER: PA entities (authenticate, role:pa_entity)
 * ACCESS: Authenticated only (middleware: auth, role:pa_entity)
 *
 * PURPOSE:
 * - Lista scraper configurati (Firenze Delibere, Albo Pretorio, ecc.)
 * - Overview status: attivo/inattivo, ultima esecuzione, totale atti estratti
 * - Azioni: Test API, Esegui manualmente, Modifica config, Elimina
 *
 * ============================================================================
 * FEATURES
 * ============================================================================
 *
 * STATS CARDS:
 * - Total scraper configurati
 * - Scraper attivi
 * - Tot atti estratti (somma globale)
 *
 * TABLE COLUMNS:
 * - Nome scraper + badge tipo (API / HTML)
 * - Fonte (Comune di Firenze, ecc.)
 * - Status (attivo/inattivo)
 * - Ultima esecuzione + frequenza schedule
 * - Totale atti estratti
 * - Azioni: Test, Run, Edit, Delete
 *
 * ============================================================================
 * PA BRAND DESIGN
 * ============================================================================
 *
 * COLORS:
 * - Primary: #1B365D (Blu Algoritmo)
 * - Accent: #D4A574 (Oro Fiorentino - CTA)
 * - Success: #2D5016 (Verde Rinascita)
 * - Info: #3498DB (Blu Info)
 *
 * ============================================================================
 *
 * @package Resources\Views\Pa\Scrapers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N. Web Scraping Agent)
 * @date 2025-10-23
 * @purpose PA web scrapers management index view
 * @accessibility WCAG 2.1 AA compliant
 */
--}}

<x-pa-layout title="Agente Web Scraping">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Web Scraping Agent</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Agente Web Scraping</x-slot:pageTitle>

    {{-- Subtitle --}}
    <div class="mb-8 flex items-center justify-between">
        <p class="text-gray-600">
            Configura e gestisci l'acquisizione automatica di atti pubblici da fonti web esterne (API, Albo Pretorio,
            Portali Trasparenza).
        </p>
        <a href="{{ route('pa.scrapers.create') }}"
            class="inline-flex transform items-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:bg-[#C39563]"
            aria-label="Nuovo Scraper">
            <span class="material-symbols-outlined mr-2 text-xl">add</span>
            Nuovo Scraper
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Total Scrapers --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        Scraper Configurati
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#1B365D]">
                        {{ $scrapers->count() }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#1B365D] bg-opacity-10 p-3">
                    <span class="material-symbols-outlined text-3xl text-[#1B365D]">settings</span>
                </div>
            </div>
        </div>

        {{-- Active Scrapers --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        Scraper Attivi
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#2D5016]">
                        {{ $scrapers->where('is_active', true)->count() }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#2D5016] bg-opacity-10 p-3">
                    <span class="material-symbols-outlined text-3xl text-[#2D5016]">play_circle</span>
                </div>
            </div>
        </div>

        {{-- Total Acts Scraped --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        Totale Atti Estratti
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#D4A574]">
                        {{ number_format($scrapers->sum('total_items_scraped')) }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#D4A574] bg-opacity-10 p-3">
                    <span class="material-symbols-outlined text-3xl text-[#D4A574]">download</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Scrapers Table --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        @if ($scrapers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nome Scraper</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Fonte</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Ultima Esecuzione</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Atti Estratti</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($scrapers as $scraper)
                            <tr class="transition-colors hover:bg-gray-50">
                                {{-- Nome + Tipo --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#1B365D]">
                                            {{ $scraper->type === 'api' ? 'api' : 'language' }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $scraper->name }}</p>
                                            <p class="text-xs text-gray-500">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                                    {{ strtoupper($scraper->type) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Fonte --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700">{{ $scraper->source_entity }}</p>
                                    <p class="text-xs text-gray-500">{{ $scraper->schedule_frequency ?? 'N/A' }}</p>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($scraper->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            Attivo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                            <span class="material-symbols-outlined text-sm">pause_circle</span>
                                            Inattivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Ultima Esecuzione --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-700">
                                    @if ($scraper->last_run_at)
                                        <span title="{{ $scraper->last_run_at->format('Y-m-d H:i:s') }}">
                                            {{ $scraper->last_run_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Mai eseguito</span>
                                    @endif
                                </td>

                                {{-- Atti Estratti --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="font-semibold text-[#D4A574]">
                                        {{ number_format($scraper->total_items_scraped) }}
                                    </span>
                                </td>

                                {{-- Azioni --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View/Show --}}
                                        <a href="{{ route('pa.scrapers.show', $scraper) }}"
                                            class="inline-flex items-center rounded-lg bg-[#1B365D] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#0F2342]"
                                            title="Visualizza Dettagli">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </a>

                                        {{-- Run Manually --}}
                                        <form method="POST" action="{{ route('pa.scrapers.run', $scraper) }}"
                                            class="inline-block"
                                            onsubmit="showLoadingModal('run', '{{ $scraper->source_entity }}', {{ $scraper->id }})">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-[#2D5016] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#1F3810]"
                                                title="Esegui Manualmente">
                                                <span class="material-symbols-outlined text-sm">play_arrow</span>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a href="{{ route('pa.scrapers.edit', $scraper) }}"
                                            class="inline-flex items-center rounded-lg bg-[#D4A574] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#C39563]"
                                            title="Modifica">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('pa.scrapers.destroy', $scraper) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Sei sicuro di voler eliminare questo scraper?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-red-700"
                                                title="Elimina">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined mb-4 text-6xl text-gray-300">cloud_download</span>
                <p class="mb-2 text-lg font-semibold text-gray-600">Nessun Scraper Configurato</p>
                <p class="mb-6 text-sm text-gray-500">Inizia configurando il tuo primo scraper per acquisire
                    automaticamente atti pubblici dal web.</p>
                <a href="{{ route('pa.scrapers.create') }}"
                    class="inline-flex items-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white shadow-md transition-all duration-200 hover:bg-[#C39563]">
                    <span class="material-symbols-outlined mr-2">add</span>
                    Crea il Primo Scraper
                </a>
            </div>
        @endif
    </div>

    {{-- Info GDPR Compliance --}}
    <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-6">
        <div class="flex items-start gap-4">
            <span class="material-symbols-outlined text-3xl text-blue-600">info</span>
            <div>
                <h3 class="mb-2 text-lg font-bold text-blue-900">GDPR Compliance</h3>
                <p class="text-sm text-blue-800">
                    Tutti gli scraper configurati operano esclusivamente su dati <strong>pubblici</strong> resi
                    disponibili dalle PA
                    ai sensi del D.Lgs 33/2013 (Trasparenza Amministrativa) e art. 22 CAD (Documenti amministrativi
                    informatici).
                    I campi PII vengono automaticamente sanitizzati prima del salvataggio. Ogni esecuzione è tracciata
                    tramite
                    <strong>audit trail completo</strong> per conformità GDPR Art. 5 (responsabilità e tracciabilità).
                </p>
            </div>
        </div>
    </div>

    {{-- Loading Modal - Enterprise Style --}}
    <div id="loadingModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
        <div class="relative mx-4 w-full max-w-md transform rounded-2xl bg-white p-8 shadow-2xl transition-all">
            {{-- Logo/Icon Area --}}
            <div class="mb-6 flex justify-center">
                <div class="relative">
                    {{-- Animated Ring --}}
                    <div
                        class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-l-[#D4A574] border-t-[#1B365D]">
                    </div>
                    {{-- Icon --}}
                    <div
                        class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-[#1B365D] to-[#2D5016]">
                        <span class="material-symbols-outlined animate-pulse text-5xl text-white"
                            id="modalIcon">cloud_sync</span>
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <h3 class="mb-3 text-center text-2xl font-bold text-[#1B365D]" id="modalTitle">
                Esecuzione in corso...
            </h3>

            {{-- Message --}}
            <p class="mb-6 text-center text-gray-600" id="modalMessage">
                Stiamo estraendo gli atti pubblici. L'operazione potrebbe richiedere alcuni minuti.
            </p>

            {{-- Progress Indicators --}}
            <div id="progressStats" class="hidden space-y-3">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="rounded-lg bg-blue-50 p-3">
                        <div class="text-2xl font-bold text-[#1B365D]" id="processedCount">0</div>
                        <div class="text-xs text-gray-600">Atti elaborati</div>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3">
                        <div class="text-2xl font-bold text-[#2D5016]" id="savedCount">0</div>
                        <div class="text-xs text-gray-600">Atti salvati</div>
                    </div>
                </div>
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <div class="text-lg font-semibold text-gray-700" id="skippedCount">0</div>
                    <div class="text-xs text-gray-600">Atti già presenti (skipped)</div>
                </div>
                <div class="text-center text-xs text-gray-500" id="currentTitle">
                    <!-- Current act title will appear here -->
                </div>
            </div>

            <div id="staticProgress" class="space-y-2">
                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <span class="material-symbols-outlined animate-pulse text-[#1B365D]">check_circle</span>
                    <span>Preparazione richiesta...</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <span class="material-symbols-outlined animate-pulse text-[#1B365D]">cloud_upload</span>
                    <span>Invio dati all'API...</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <span class="material-symbols-outlined animate-pulse text-[#1B365D]">shield</span>
                    <span>Verifica GDPR compliance...</span>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mt-6 h-2 overflow-hidden rounded-full bg-gray-200">
                <div id="progressBar"
                    class="h-full rounded-full bg-gradient-to-r from-[#1B365D] via-[#D4A574] to-[#2D5016] transition-all duration-300"
                    style="width: 0%">
                </div>
            </div>
            <div id="progressPercentage" class="mt-2 hidden text-center text-sm text-gray-600">0%</div>

            {{-- Institutional Footer --}}
            <div class="mt-6 border-t border-gray-200 pt-4 text-center">
                <p class="text-xs text-gray-500">
                    <span class="material-symbols-outlined mr-1 inline-block align-middle text-sm">verified_user</span>
                    Sistema certificato N.A.T.A.N. - Conformità GDPR garantita
                </p>
            </div>
        </div>
    </div>

    {{-- JavaScript for Modal --}}
    <script>
        let progressInterval = null;
        let currentScraperId = null;

        function showLoadingModal(type, sourceEntity = '', scraperId = null) {
            const modal = document.getElementById('loadingModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalIcon = document.getElementById('modalIcon');

            if (type === 'test') {
                modalTitle.textContent = 'Test Connessione in corso...';
                modalMessage.innerHTML =
                    `Stiamo verificando la connessione con <strong>${sourceEntity}</strong>. L'operazione potrebbe richiedere alcuni secondi.`;
                modalIcon.textContent = 'electrical_services';
            } else if (type === 'run') {
                modalTitle.textContent = 'Esecuzione Scraper in corso...';
                modalMessage.innerHTML =
                    `Stiamo estraendo gli atti da <strong>${sourceEntity}</strong>. L'operazione potrebbe richiedere alcuni minuti a seconda del volume di dati.`;
                modalIcon.textContent = 'play_arrow';

                // Store scraper ID and start polling
                currentScraperId = scraperId;
                if (scraperId) {
                    startProgressPolling(scraperId);
                }
            }

            // Show modal with fade-in
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Prevent accidental double-submit
            return true;
        }

        function startProgressPolling(scraperId) {
            // Poll every 1.5 seconds
            progressInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/pa/scrapers/${scraperId}/progress`);
                    const data = await response.json();

                    if (data.status === 'running') {
                        updateProgress(data);
                    } else if (data.status === 'completed') {
                        clearInterval(progressInterval);
                        // Let the page reload show final results
                    }
                } catch (error) {
                    console.error('Progress polling error:', error);
                }
            }, 1500);
        }

        function updateProgress(data) {
            // Hide static progress, show dynamic stats
            document.getElementById('staticProgress').classList.add('hidden');
            document.getElementById('progressStats').classList.remove('hidden');
            document.getElementById('progressPercentage').classList.remove('hidden');

            // Update counters
            document.getElementById('processedCount').textContent = data.processed || 0;
            document.getElementById('savedCount').textContent = data.saved || 0;
            document.getElementById('skippedCount').textContent = data.skipped || 0;

            // Update progress bar
            const percentage = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('progressPercentage').textContent = percentage + '%';

            // Update current title (truncated)
            if (data.current_title) {
                const truncated = data.current_title.length > 80 ?
                    data.current_title.substring(0, 80) + '...' :
                    data.current_title;
                document.getElementById('currentTitle').textContent = '📄 ' + truncated;
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (progressInterval) {
                clearInterval(progressInterval);
            }
        });

        // Hide modal if page loads with errors (form will not have been processed)
        window.addEventListener('load', function() {
            setTimeout(function() {
                const modal = document.getElementById('loadingModal');
                if (modal && !modal.classList.contains('hidden')) {
                    // Check if there are any success/error messages (meaning page reloaded)
                    const hasMessages = document.querySelector('.alert-success, .alert-error');
                    if (hasMessages) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                }
            }, 100);
        });
    </script>

    {{-- CSS for Animations --}}
    <style>
        @keyframes progress {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        .animate-progress {
            animation: progress 3s ease-in-out infinite;
        }

        /* Backdrop blur support */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }

        /* Smooth transitions */
        #loadingModal {
            transition: opacity 0.3s ease-in-out;
        }

        /* Pulse animation for icons */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-pa-layout>
