<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Violazioni Padmin'">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-base-content">⚠️ Violazioni Regole OS3.0</h1>
            <p class="mt-1 text-sm text-base-content/70">{{ count($violations) }} violazioni trovate</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openScanModal()" class="btn btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Run Scan
            </button>
            <a href="{{ route('superadmin.padmin.dashboard') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('superadmin.padmin.statistics') }}" class="btn btn-outline btn-sm">
                📊 Statistiche
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6 bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title mb-4 text-base">🔍 Filtri</h3>
            <form method="GET" action="{{ route('superadmin.padmin.violations') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-4">
                {{-- Priority Filter --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Priorità</span>
                    </label>
                    <select name="priority" class="select select-bordered h-10 min-h-10">
                        <option value="">Tutte</option>
                        <option value="P0" {{ request('priority') === 'P0' ? 'selected' : '' }}>P0 - Blocking
                        </option>
                        <option value="P1" {{ request('priority') === 'P1' ? 'selected' : '' }}>P1 - High</option>
                        <option value="P2" {{ request('priority') === 'P2' ? 'selected' : '' }}>P2 - Medium</option>
                        <option value="P3" {{ request('priority') === 'P3' ? 'selected' : '' }}>P3 - Low</option>
                    </select>
                </div>

                {{-- Severity Filter --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Severità</span>
                    </label>
                    <select name="severity" class="select select-bordered h-10 min-h-10">
                        <option value="">Tutte</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical
                        </option>
                        <option value="error" {{ request('severity') === 'error' ? 'selected' : '' }}>Error</option>
                        <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning
                        </option>
                        <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Stato</span>
                    </label>
                    <select name="isFixed" class="select select-bordered h-10 min-h-10">
                        <option value="">Tutte</option>
                        <option value="0" {{ request('isFixed') === '0' ? 'selected' : '' }}>Attive</option>
                        <option value="1" {{ request('isFixed') === '1' ? 'selected' : '' }}>Risolte</option>
                    </select>
                </div>

                {{-- Limit --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Risultati</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="limit" class="select select-bordered h-10 min-h-10 flex-1">
                            <option value="50" {{ request('limit', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('limit') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                        <button type="submit" class="btn btn-primary h-10 min-h-10">Filtra</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Violations Table --}}
    @if (count($violations) > 0)
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead class="bg-base-200">
                            <tr>
                                <th class="w-16">Priorità</th>
                                <th class="w-24">Severità</th>
                                <th>Tipo Violazione</th>
                                <th>File</th>
                                <th>Linea</th>
                                <th class="w-32">Data</th>
                                <th class="w-20">Stato</th>
                                <th class="w-28">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($violations as $violation)
                                <tr class="transition-colors hover:bg-base-200/50">
                                    {{-- Priority Badge --}}
                                    <td>
                                        <span
                                            class="{{ $violation['priority'] === 'P0' ? 'badge-error' : '' }} {{ $violation['priority'] === 'P1' ? 'badge-warning' : '' }} {{ $violation['priority'] === 'P2' ? 'badge-info' : '' }} {{ $violation['priority'] === 'P3' ? 'badge-neutral' : '' }} badge badge-sm font-mono">
                                            {{ $violation['priority'] }}
                                        </span>
                                    </td>

                                    {{-- Severity Badge --}}
                                    <td>
                                        <span
                                            class="{{ $violation['severity'] === 'critical' ? 'badge-error' : '' }} {{ $violation['severity'] === 'error' ? 'badge-warning' : '' }} {{ $violation['severity'] === 'warning' ? 'badge-info' : '' }} badge badge-outline badge-sm">
                                            {{ ucfirst($violation['severity']) }}
                                        </span>
                                    </td>

                                    {{-- Type --}}
                                    <td>
                                        <div class="text-sm font-semibold">{{ $violation['type'] }}</div>
                                        @if (isset($violation['message']))
                                            <div class="max-w-xs truncate text-xs text-base-content/60"
                                                title="{{ $violation['message'] }}">
                                                {{ $violation['message'] }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- File Path --}}
                                    <td>
                                        <code class="rounded bg-base-200 px-2 py-1 text-xs">
                                            {{ basename($violation['filePath'] ?? 'N/A') }}
                                        </code>
                                    </td>

                                    {{-- Line --}}
                                    <td class="text-center font-mono text-sm">
                                        {{ $violation['line'] ?? '-' }}
                                    </td>

                                    {{-- Timestamp --}}
                                    <td class="text-xs">
                                        @if (isset($violation['timestamp']))
                                            <div>{{ date('d/m/Y', $violation['timestamp']) }}</div>
                                            <div class="text-base-content/60">
                                                {{ date('H:i', $violation['timestamp']) }}</div>
                                        @else
                                            <span class="text-base-content/40">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if ($violation['isFixed'] ?? false)
                                            <span class="badge badge-success badge-sm gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Risolta
                                            </span>
                                        @else
                                            <span class="badge badge-error badge-sm gap-1">
                                                <svg class="h-3 w-3 animate-pulse" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="6" />
                                                </svg>
                                                Attiva
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="flex gap-1">
                                            @if (!($violation['isFixed'] ?? false))
                                                <button onclick="openAiFixModal('{{ $violation['id'] }}')"
                                                    class="btn btn-primary btn-xs gap-1" title="Fix with AI">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    AI
                                                </button>
                                                <button onclick="markViolationFixed('{{ $violation['id'] }}')"
                                                    class="btn btn-success btn-xs gap-1" title="Marca come risolta">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-xs text-base-content/40">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body py-12 text-center">
                <div class="mb-4 text-6xl">🎉</div>
                <h3 class="text-xl font-bold">Nessuna violazione trovata</h3>
                <p class="mt-2 text-base-content/70">Ottimo lavoro! Il codice rispetta tutte le regole OS3.0.</p>
                @if (request()->hasAny(['priority', 'severity', 'isFixed']))
                    <a href="{{ route('superadmin.padmin.violations') }}" class="btn btn-outline btn-sm mt-4">
                        Rimuovi filtri
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Mark as Fixed Script --}}
    @push('scripts')
        <script>
            // CSRF token setup
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            async function markViolationFixed(violationId) {
                if (!confirm('Confermi di voler marcare questa violazione come risolta?')) {
                    return;
                }

                const button = event.target.closest('button');
                button.disabled = true;
                button.classList.add('loading');

                try {
                    const response = await fetch(`/admin/padmin/violations/${violationId}/fix`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Success: reload page to show updated status
                        showToast('Violazione marcata come risolta!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Errore durante l\'aggiornamento', 'error');
                        button.disabled = false;
                        button.classList.remove('loading');
                    }
                } catch (error) {
                    console.error('Error marking violation as fixed:', error);
                    showToast('Errore di rete. Riprova più tardi.', 'error');
                    button.disabled = false;
                    button.classList.remove('loading');
                }
            }

            function showToast(message, type = 'info') {
                // Simple toast notification (using alert as fallback)
                // TODO: Integrate with UEM toast system
                const toast = document.createElement('div');
                toast.className = `alert alert-${type} fixed top-4 right-4 z-50 w-96 shadow-lg`;
                toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>${message}</span>
            `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            // AI Fix Modal Functions
            let currentAiPrompt = '';

            function openAiFixModal(violationId) {
                document.getElementById('aiFixModal').showModal();
                document.getElementById('aiFixLoading').classList.remove('hidden');
                document.getElementById('aiFixContent').classList.add('hidden');

                fetchAiFixPrompt(violationId);
            }

            function closeAiFixModal() {
                document.getElementById('aiFixModal').close();
                currentAiPrompt = '';
            }

            async function fetchAiFixPrompt(violationId) {
                try {
                    const response = await fetch(`/superadmin/padmin/violations/${violationId}/ai-fix`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    document.getElementById('aiFixLoading').classList.add('hidden');

                    if (response.ok && data.success) {
                        currentAiPrompt = data.ai_prompt;
                        document.getElementById('aiPromptText').value = data.ai_prompt;
                        document.getElementById('aiFixContent').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Errore generazione prompt AI', 'error');
                        closeAiFixModal();
                    }
                } catch (error) {
                    console.error('AI Fix error:', error);
                    showToast('Errore di rete durante generazione prompt AI', 'error');
                    closeAiFixModal();
                }
            }

            function copyAiPrompt() {
                const textarea = document.getElementById('aiPromptText');
                textarea.select();
                document.execCommand('copy');

                showToast('✅ Prompt copiato! Incollalo in GitHub Copilot Chat', 'success');
            }

            // Scan Modal Functions
            function openScanModal() {
                document.getElementById('scanModal').showModal();
            }

            function closeScanModal() {
                document.getElementById('scanModal').close();
            }

            async function runScan(event) {
                event.preventDefault();

                const form = event.target;
                const path = form.path.value.trim();
                const rulesCheckboxes = form.querySelectorAll('input[name="rules[]"]:checked');
                const selectedRules = Array.from(rulesCheckboxes).map(cb => cb.value);

                if (!path) {
                    showToast('Inserisci un path valido', 'error');
                    return;
                }

                if (selectedRules.length === 0) {
                    showToast('Seleziona almeno una regola', 'warning');
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"]');
                const resultsDiv = document.getElementById('scanResults');
                const loadingDiv = document.getElementById('scanLoading');

                // Show loading
                submitButton.disabled = true;
                submitButton.classList.add('loading');
                resultsDiv.innerHTML = '';
                loadingDiv.classList.remove('hidden');

                try {
                    const response = await fetch('/superadmin/padmin/scan/run', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            path: path,
                            rules: selectedRules,
                            store: true // Salva violazioni automaticamente
                        })
                    });

                    const data = await response.json();

                    loadingDiv.classList.add('hidden');

                    if (response.ok && data.success) {
                        showToast(`✅ ${data.violations.length} violazioni trovate e salvate!`, 'success');

                        // Close modal dopo 1.5 secondi
                        setTimeout(() => {
                            closeScanModal();
                            // Ricarica la pagina per mostrare nuove violations
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Errore durante la scansione', 'error');
                    }

                } catch (error) {
                    console.error('Scan error:', error);
                    showToast('Errore di rete durante la scansione', 'error');
                    loadingDiv.classList.add('hidden');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('loading');
                }
            }

            function displayScanResults(violations) {
                const resultsDiv = document.getElementById('scanResults');

                if (violations.length === 0) {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Nessuna violazione trovata! 🎉</span>
                        </div>
                    `;
                    return;
                }

                const violationsHtml = violations.map((v, idx) => `
                    <div class="card bg-base-200 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="badge badge-${v.severity === 'P0' ? 'error' : v.severity === 'P1' ? 'warning' : 'info'} badge-sm">
                                            ${v.severity || 'P2'}
                                        </span>
                                        <span class="badge badge-outline badge-sm">${v.rule || 'UNKNOWN'}</span>
                                    </div>
                                    <p class="text-sm font-medium mb-1">${v.message}</p>
                                    <p class="text-xs text-base-content/60">
                                        📄 ${v.file}:${v.line}
                                    </p>
                                    ${v.codeSnippet ? `
                                                        <details class="mt-2">
                                                            <summary class="cursor-pointer text-xs text-primary">Vedi codice</summary>
                                                            <pre class="mt-2 p-2 bg-base-300 rounded text-xs overflow-x-auto"><code>${escapeHtml(v.codeSnippet)}</code></pre>
                                                        </details>
                                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');

                resultsDiv.innerHTML = `
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-bold">Risultati Scansione (${violations.length})</h3>
                        <button onclick="location.reload()" class="btn btn-sm btn-ghost">🔄 Ricarica Pagina</button>
                    </div>
                    ${violationsHtml}
                `;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        </script>
    @endpush

    {{-- Scan Modal --}}
    <dialog id="scanModal" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="mb-4 text-lg font-bold">🔍 Avvia Nuova Scansione</h3>

            <form onsubmit="runScan(event)">
                {{-- Path Input --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Path da Scansionare</span>
                    </label>
                    <input type="text" name="path" placeholder="app/Livewire/Collections"
                        class="input input-bordered w-full" value="app/Livewire/Collections">
                    <label class="label">
                        <span class="label-text-alt">Directory o file PHP da analizzare</span>
                    </label>
                </div>

                {{-- Rules Selection --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Regole da Applicare</span>
                    </label>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="rules[]" value="REGOLA_ZERO" class="checkbox checkbox-sm"
                                checked>
                            <span class="label-text">🚫 REGOLA ZERO (P0)</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="rules[]" value="UEM_FIRST" class="checkbox checkbox-sm"
                                checked>
                            <span class="label-text">⚠️ UEM FIRST (P0)</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="rules[]" value="STATISTICS" class="checkbox checkbox-sm"
                                checked>
                            <span class="label-text">📊 STATISTICS (P0)</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="rules[]" value="MICA_SAFE" class="checkbox checkbox-sm"
                                checked>
                            <span class="label-text">🏛️ MiCA SAFE (P0)</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="rules[]" value="GDPR_COMPLIANCE"
                                class="checkbox checkbox-sm" checked>
                            <span class="label-text">🔒 GDPR COMPLIANCE (P0)</span>
                        </label>
                    </div>
                </div>

                {{-- Loading Indicator --}}
                <div id="scanLoading" class="mb-4 hidden">
                    <div class="alert alert-info">
                        <span class="loading loading-spinner loading-sm"></span>
                        <span>Scansione in corso...</span>
                    </div>
                </div>

                {{-- Results Container --}}
                <div id="scanResults" class="mb-4 max-h-96 overflow-y-auto"></div>

                {{-- Actions --}}
                <div class="modal-action">
                    <button type="button" onclick="closeScanModal()" class="btn btn-ghost">Chiudi</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Avvia Scansione
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="closeScanModal()">close</button>
        </form>
    </dialog>

    {{-- AI Fix Modal --}}
    <dialog id="aiFixModal" class="modal">
        <div class="modal-box max-w-4xl">
            <h3 class="mb-4 flex items-center gap-2 text-lg font-bold">
                <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Fix con AI Assistant
            </h3>

            <div id="aiFixLoading" class="mb-4 hidden">
                <div class="alert alert-info">
                    <span class="loading loading-spinner loading-sm"></span>
                    <span>Generazione prompt AI in corso...</span>
                </div>
            </div>

            <div id="aiFixContent" class="hidden">
                <div class="mb-4">
                    <label class="label">
                        <span class="label-text font-medium">📋 Prompt per GitHub Copilot Chat</span>
                        <button onclick="copyAiPrompt()" class="btn btn-ghost btn-xs gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Copia
                        </button>
                    </label>
                    <textarea id="aiPromptText" class="textarea textarea-bordered h-96 w-full font-mono text-sm" readonly></textarea>
                </div>

                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <strong>Come usare:</strong>
                        <ol class="mt-2 list-inside list-decimal text-sm">
                            <li>Copia il prompt con il pulsante sopra</li>
                            <li>Apri GitHub Copilot Chat in VS Code (Ctrl+Shift+I)</li>
                            <li>Incolla il prompt e premi Invio</li>
                            <li>Applica le modifiche suggerite</li>
                            <li>Torna qui e marca la violazione come risolta</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" onclick="closeAiFixModal()" class="btn btn-outline">Chiudi</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="closeAiFixModal()">close</button>
        </form>
    </dialog>
</x-layouts.superadmin>
