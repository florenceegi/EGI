{{--
/**
 * PA Web Scraper Show/Detail View
 *
 * @package Resources\Views\Pa\Scrapers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0
 * @date 2025-10-23
 * @purpose Detailed view for single scraper configuration with execution results
 */
--}}

<x-pa-layout title="Dettagli Scraper">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.scrapers.index') }}" class="text-[#D4A574] hover:text-[#C39463]">Web Scraping Agent</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">{{ $scraper->name }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ $scraper->name }}</x-slot:pageTitle>

    {{-- Actions Bar --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            {{-- Status Badge --}}
            @if ($scraper->is_active)
                <span
                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-800">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Attivo
                </span>
            @else
                <span
                    class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600">
                    <span class="material-symbols-outlined text-base">pause_circle</span>
                    Inattivo
                </span>
            @endif

            {{-- Type Badge --}}
            <span class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800">
                {{ strtoupper($scraper->type) }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Test Connection --}}
            <form method="POST" action="{{ route('pa.scrapers.test', $scraper) }}" onsubmit="showLoadingModal('test')">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition-colors hover:bg-blue-700">
                    <span class="material-symbols-outlined">electrical_services</span>
                    Test Connessione
                </button>
            </form>

            {{-- Run Manually --}}
            <form method="POST" action="{{ route('pa.scrapers.run', $scraper) }}"
                onsubmit="sessionStorage.setItem('scraperRunning', 'true'); sessionStorage.setItem('scraperId', '{{ $scraper->id }}'); showLoadingModal('run'); return true;">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2D5016] px-4 py-2 font-semibold text-white transition-colors hover:bg-[#1F3810]">
                    <span class="material-symbols-outlined">play_arrow</span>
                    Esegui Manualmente
                </button>
            </form>

            {{-- Toggle Active Status --}}
            <form method="POST" action="{{ route('pa.scrapers.toggle', $scraper) }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 font-semibold text-white transition-colors hover:bg-gray-700">
                    <span class="material-symbols-outlined">{{ $scraper->is_active ? 'pause' : 'play_circle' }}</span>
                    {{ $scraper->is_active ? 'Disattiva' : 'Attiva' }}
                </button>
            </form>

            {{-- Edit --}}
            <a href="{{ route('pa.scrapers.edit', $scraper) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#D4A574] px-4 py-2 font-semibold text-white transition-colors hover:bg-[#C39563]">
                <span class="material-symbols-outlined">edit</span>
                Modifica
            </a>
        </div>
    </div>

    {{-- Test/Preview Section --}}
    <div class="mb-8 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50 p-6">
        <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-[#1B365D]">
            <span class="material-symbols-outlined">science</span>
            Test Scraper - Anteprima Risultati
        </h3>
        <p class="mb-4 text-sm text-gray-700">
            Testa lo scraper per vedere quanti atti trova <strong>SENZA importarli</strong> nel database.
            Utile per verificare la configurazione prima di eseguire l'importazione completa.
        </p>

        <form id="previewForm" class="flex items-end gap-4">
            <div class="flex-1">
                <label for="preview_year" class="mb-2 block text-sm font-semibold text-gray-700">Anno da testare</label>
                <input type="number" id="preview_year" name="year" value="{{ date('Y') }}" min="2000"
                    max="{{ date('Y') + 1 }}"
                    class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    placeholder="Es: 2024">
            </div>
            <button type="submit" id="previewBtn"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-blue-700">
                <span class="material-symbols-outlined">search</span>
                Testa
            </button>
        </form>

        {{-- Preview Results --}}
        <div id="previewResults" class="mt-4 hidden">
            <div class="rounded-lg border border-blue-200 bg-white p-4">
                <div class="mb-3 flex items-center justify-between border-b border-gray-200 pb-3">
                    <h4 class="text-lg font-bold text-[#1B365D]">
                        <span id="previewCount">0</span> atti trovati per l'anno <span id="previewYear">-</span>
                    </h4>
                    <button id="importBtn" data-year="" data-scraper-id="{{ $scraper->id }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2D5016] px-4 py-2 font-semibold text-white transition-colors hover:bg-[#1F3810]">
                        <span class="material-symbols-outlined">download</span>
                        Importa Questi Atti
                    </button>
                </div>

                <div id="previewActsInfo" class="space-y-2 text-sm">
                    {{-- First act example --}}
                    <div id="firstActInfo" class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="mb-1 font-semibold text-gray-700">Primo atto:</p>
                        <div class="text-gray-600">
                            <p><strong>N°:</strong> <span id="firstActNumero">-</span></p>
                            <p><strong>Data:</strong> <span id="firstActData">-</span></p>
                            <p><strong>Tipo:</strong> <span id="firstActTipo">-</span></p>
                            <p><strong>Oggetto:</strong> <span id="firstActOggetto">-</span></p>
                        </div>
                    </div>

                    {{-- Last act example --}}
                    <div id="lastActInfo" class="rounded border border-gray-200 bg-gray-50 p-3">
                        <p class="mb-1 font-semibold text-gray-700">Ultimo atto:</p>
                        <div class="text-gray-600">
                            <p><strong>N°:</strong> <span id="lastActNumero">-</span></p>
                            <p><strong>Data:</strong> <span id="lastActData">-</span></p>
                            <p><strong>Tipo:</strong> <span id="lastActTipo">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Loading --}}
        <div id="previewLoading" class="mt-4 hidden text-center">
            <div class="inline-flex items-center gap-3 rounded-lg bg-white px-6 py-3 shadow">
                <div class="h-5 w-5 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                <span class="text-sm font-medium text-gray-700">Test in corso...</span>
            </div>
        </div>

        {{-- Preview Error --}}
        <div id="previewError" class="mt-4 hidden rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-800">Errore durante il test:</p>
            <p id="previewErrorMessage" class="mt-1 text-sm text-red-700"></p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Totale Atti Estratti</p>
            <p class="mt-2 text-3xl font-bold text-[#D4A574]">{{ number_format($scraper->total_items_scraped) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Ultima Esecuzione</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">
                {{ $scraper->last_run_at ? $scraper->last_run_at->format('d/m/Y H:i') : 'Mai' }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Prossima Esecuzione</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">
                {{ $scraper->next_run_at ? $scraper->next_run_at->format('d/m/Y H:i') : 'N/A' }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Frequenza</p>
            <p class="mt-2 text-lg font-semibold capitalize text-gray-900">
                {{ $scraper->schedule_frequency ?? 'N/A' }}
            </p>
        </div>
    </div>

    {{-- Main Info Grid --}}
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Configuration Card --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-xl font-bold text-[#1B365D]">
                <span class="material-symbols-outlined">settings</span>
                Configurazione
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-semibold text-gray-600">Fonte</dt>
                    <dd class="text-base text-gray-900">{{ $scraper->source_entity }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-600">Descrizione</dt>
                    <dd class="text-base text-gray-900">{{ $scraper->description ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-600">Base URL</dt>
                    <dd class="break-all text-sm text-blue-600">{{ $scraper->base_url }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-600">API Endpoint</dt>
                    <dd class="break-all text-sm text-gray-700">{{ $scraper->api_endpoint ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-600">Metodo HTTP</dt>
                    <dd class="font-mono text-base text-gray-900">{{ $scraper->method }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-600">Tipo Paginazione</dt>
                    <dd class="text-base text-gray-900">{{ $scraper->pagination_type ?? 'Nessuna' }}</dd>
                </div>
            </dl>
        </div>

        {{-- GDPR Compliance Card --}}
        <div class="rounded-xl border border-green-200 bg-green-50 p-6 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-xl font-bold text-green-900">
                <span class="material-symbols-outlined">verified_user</span>
                GDPR Compliance
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-semibold text-green-700">Tipo Fonte Dati</dt>
                    <dd class="text-base font-semibold uppercase text-green-900">{{ $scraper->data_source_type }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-green-700">Base Giuridica</dt>
                    <dd class="text-sm text-green-800">{{ $scraper->legal_basis ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-green-700">Politica Conservazione</dt>
                    <dd class="text-sm text-green-800">{{ $scraper->data_retention_policy ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-green-700">Campi PII Esclusi</dt>
                    <dd class="text-sm text-green-800">
                        @if ($scraper->pii_fields_to_exclude && count($scraper->pii_fields_to_exclude) > 0)
                            <ul class="ml-4 list-disc">
                                @foreach ($scraper->pii_fields_to_exclude as $field)
                                    <li>{{ $field }}</li>
                                @endforeach
                            </ul>
                        @else
                            Nessuno
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-green-700">Ultimo Audit GDPR</dt>
                    <dd class="text-sm text-green-800">
                        {{ $scraper->last_gdpr_audit_at ? $scraper->last_gdpr_audit_at->format('d/m/Y H:i') : 'Mai eseguito' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-green-700">Status GDPR</dt>
                    <dd>
                        @if ($scraper->gdpr_compliant)
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-green-600 px-3 py-1 text-xs font-semibold text-white">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Conforme
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white">
                                <span class="material-symbols-outlined text-sm">warning</span>
                                Non Conforme
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Last Execution Results (if available from session) --}}
    @if (session('scraper_results'))
        @php
            $results = session('scraper_results');
        @endphp
        <div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-xl font-bold text-[#1B365D]">
                <span class="material-symbols-outlined">assignment_turned_in</span>
                Risultati Ultima Esecuzione
            </h3>
            <div class="mb-4 flex items-center gap-4">
                <div>
                    <span class="text-sm text-gray-600">Atti Estratti:</span>
                    <span class="ml-2 text-xl font-bold text-[#D4A574]">{{ $results['stats']['acts_count'] }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Tempo Esecuzione:</span>
                    <span
                        class="ml-2 text-xl font-bold text-gray-900">{{ $results['stats']['execution_time'] }}s</span>
                </div>
            </div>

            @if (session('scraper_data') && count(session('scraper_data')) > 0)
                <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <h4 class="mb-3 font-semibold text-gray-700">Atti Estratti (Preview primi 10):</h4>
                    <ul class="space-y-2">
                        @foreach (array_slice(session('scraper_data'), 0, 10) as $act)
                            <li class="rounded-lg bg-white p-3 shadow-sm">
                                <p class="font-semibold text-gray-900">{{ $act['title'] ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">Protocollo: {{ $act['protocol_number'] ?? 'N/A' }} |
                                    Data: {{ $act['protocol_date'] ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Tipo: {{ $act['doc_type'] ?? 'N/A' }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Last Error (if any) --}}
    @if ($scraper->last_error)
        <div class="mb-8 rounded-xl border border-red-200 bg-red-50 p-6">
            <h3 class="mb-2 flex items-center gap-2 text-lg font-bold text-red-900">
                <span class="material-symbols-outlined">error</span>
                Ultimo Errore
            </h3>
            <p class="font-mono text-sm text-red-700">{{ $scraper->last_error }}</p>
            <p class="mt-2 text-xs text-red-600">
                Verificare la configurazione e riprovare. Se il problema persiste, contattare il supporto.
            </p>
        </div>
    @endif

    {{-- Advanced Config (collapsible) --}}
    <details class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-700 hover:bg-gray-50">
            <span class="material-symbols-outlined mr-2 align-middle">code</span>
            Configurazione Avanzata (JSON)
        </summary>
        <div class="border-t border-gray-200 p-6">
            <h4 class="mb-3 font-semibold text-gray-700">Headers:</h4>
            <pre class="mb-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-sm text-green-400">{{ json_encode($scraper->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <h4 class="mb-3 font-semibold text-gray-700">Payload Template:</h4>
            <pre class="mb-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-sm text-green-400">{{ json_encode($scraper->payload_template, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <h4 class="mb-3 font-semibold text-gray-700">Query Parameters:</h4>
            <pre class="mb-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-sm text-green-400">{{ json_encode($scraper->query_params, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <h4 class="mb-3 font-semibold text-gray-700">Data Mapping:</h4>
            <pre class="mb-4 overflow-x-auto rounded-lg bg-gray-900 p-4 text-sm text-green-400">{{ json_encode($scraper->data_mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <h4 class="mb-3 font-semibold text-gray-700">Pagination Config:</h4>
            <pre class="overflow-x-auto rounded-lg bg-gray-900 p-4 text-sm text-green-400">{{ json_encode($scraper->pagination_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </details>

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
                Connessione in corso...
            </h3>

            {{-- Message --}}
            <p class="mb-6 text-center text-gray-600" id="modalMessage">
                Stiamo verificando la connessione con <strong>{{ $scraper->source_entity }}</strong>.
                L'operazione potrebbe richiedere alcuni secondi.
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

        function showLoadingModal(type) {
            const modal = document.getElementById('loadingModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalIcon = document.getElementById('modalIcon');

            if (type === 'test') {
                modalTitle.textContent = 'Test Connessione in corso...';
                modalMessage.innerHTML =
                    'Stiamo verificando la connessione con <strong>{{ $scraper->source_entity }}</strong>. L\'operazione potrebbe richiedere alcuni secondi.';
                modalIcon.textContent = 'electrical_services';
            } else if (type === 'run') {
                modalTitle.textContent = 'Esecuzione Scraper in corso...';
                modalMessage.innerHTML =
                    'Stiamo estraendo gli atti da <strong>{{ $scraper->source_entity }}</strong>. L\'operazione potrebbe richiedere alcuni minuti a seconda del volume di dati.';
                modalIcon.textContent = 'play_arrow';

                // Start polling for progress updates
                startProgressPolling();
            }

            // Show modal with fade-in
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Prevent accidental double-submit
            return true;
        }

        function startProgressPolling() {
            console.log('[SCRAPER] Starting progress polling...');

            // Poll every 1.5 seconds
            progressInterval = setInterval(async () => {
                try {
                    const response = await fetch('{{ route('pa.scrapers.progress', $scraper) }}');
                    const data = await response.json();

                    console.log('[SCRAPER] Progress data:', data);

                    if (data.status === 'running') {
                        console.log('[SCRAPER] Status: RUNNING - Updating UI...');
                        updateProgress(data);
                    } else if (data.status === 'completed') {
                        console.log('[SCRAPER] Status: COMPLETED - Stopping polling');
                        clearInterval(progressInterval);
                        // Let the page reload show final results
                    } else {
                        console.log('[SCRAPER] Status: NOT RUNNING');
                    }
                } catch (error) {
                    console.error('[SCRAPER] Progress polling error:', error);
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

        // Check if scraper was running before page reload
        window.addEventListener('load', function() {
            const scraperRunning = sessionStorage.getItem('scraperRunning');
            const scraperId = sessionStorage.getItem('scraperId');

            console.log('[SCRAPER] Page loaded. scraperRunning:', scraperRunning, 'scraperId:', scraperId);

            if (scraperRunning === 'true' && scraperId === '{{ $scraper->id }}') {
                console.log('[SCRAPER] Scraper was running before reload - reactivating modal and polling...');

                // Scraper is running, show modal and start polling
                const modal = document.getElementById('loadingModal');
                const modalTitle = document.getElementById('modalTitle');
                const modalMessage = document.getElementById('modalMessage');
                const modalIcon = document.getElementById('modalIcon');

                modalTitle.textContent = 'Esecuzione Scraper in corso...';
                modalMessage.innerHTML =
                    'Stiamo estraendo gli atti da <strong>{{ $scraper->source_entity }}</strong>. L\'operazione potrebbe richiedere alcuni minuti a seconda del volume di dati.';
                modalIcon.textContent = 'play_arrow';

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                console.log('[SCRAPER] Modal shown, starting polling...');

                // Start polling immediately
                startProgressPolling();
            } else {
                console.log('[SCRAPER] No active scraper detected or different scraper ID');
            }

            // Hide modal if page loads with errors (form will not have been processed)
            setTimeout(function() {
                const modal = document.getElementById('loadingModal');
                if (modal && !modal.classList.contains('hidden')) {
                    // Check if there are any success/error messages (meaning scraping completed)
                    const hasMessages = document.querySelector('.alert-success, .alert-error');
                    if (hasMessages) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        // Clear sessionStorage flags
                        sessionStorage.removeItem('scraperRunning');
                        sessionStorage.removeItem('scraperId');
                    }
                }
            }, 100);
        });

        // ============================================
        // PREVIEW/TEST FUNCTIONALITY
        // ============================================
        const previewForm = document.getElementById('previewForm');
        const previewLoading = document.getElementById('previewLoading');
        const previewResults = document.getElementById('previewResults');
        const previewError = document.getElementById('previewError');
        const importBtn = document.getElementById('importBtn');

        previewForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const year = document.getElementById('preview_year').value;

            // Hide previous results/errors
            previewResults.classList.add('hidden');
            previewError.classList.add('hidden');
            previewLoading.classList.remove('hidden');

            try {
                const response = await fetch('{{ route('pa.scrapers.preview', $scraper) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        year: year
                    })
                });

                const data = await response.json();

                previewLoading.classList.add('hidden');

                if (data.success) {
                    // Show results
                    document.getElementById('previewCount').textContent = data.count;
                    document.getElementById('previewYear').textContent = data.year;
                    importBtn.dataset.year = data.year;

                    // First act
                    if (data.first_act) {
                        document.getElementById('firstActNumero').textContent = data.first_act.numero || '-';
                        document.getElementById('firstActData').textContent = data.first_act.data || '-';
                        document.getElementById('firstActTipo').textContent = data.first_act.tipo || '-';
                        document.getElementById('firstActOggetto').textContent = data.first_act.oggetto || '-';
                    }

                    // Last act
                    if (data.last_act) {
                        document.getElementById('lastActNumero').textContent = data.last_act.numero || '-';
                        document.getElementById('lastActData').textContent = data.last_act.data || '-';
                        document.getElementById('lastActTipo').textContent = data.last_act.tipo || '-';
                    }

                    previewResults.classList.remove('hidden');
                } else {
                    // Show error
                    document.getElementById('previewErrorMessage').textContent = data.error ||
                        'Errore sconosciuto';
                    previewError.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Preview error:', error);
                previewLoading.classList.add('hidden');
                document.getElementById('previewErrorMessage').textContent = 'Errore di rete: ' + error.message;
                previewError.classList.remove('hidden');
            }
        });

        // Import button - trigger scraping with specific year
        importBtn.addEventListener('click', function() {
            const year = this.dataset.year;
            const scraperId = this.dataset.scraperId;

            // Set flag in sessionStorage to show modal after page reload
            sessionStorage.setItem('scraperRunning', 'true');
            sessionStorage.setItem('scraperId', scraperId);

            // Create a hidden form to submit with year parameter
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('pa.scrapers.run', $scraper) }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            const yearInput = document.createElement('input');
            yearInput.type = 'hidden';
            yearInput.name = 'year';
            yearInput.value = year;

            form.appendChild(csrfInput);
            form.appendChild(yearInput);

            // Show loading modal before submit
            showLoadingModal('run');

            document.body.appendChild(form);
            form.submit();
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
