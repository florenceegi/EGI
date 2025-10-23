<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Violazioni Padmin'">
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-base-content">⚠️ Violazioni Regole OS3.0</h1>
            <p class="mt-1 text-sm text-base-content/70">{{ count($violations) }} violazioni trovate</p>
        </div>
        <div class="flex gap-2">
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
                    <select name="priority" class="select select-bordered select-sm">
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
                    <select name="severity" class="select select-bordered select-sm">
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
                    <select name="isFixed" class="select select-bordered select-sm">
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
                        <select name="limit" class="select select-bordered select-sm flex-1">
                            <option value="50" {{ request('limit', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('limit') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Filtra</button>
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
                                        @if (!($violation['isFixed'] ?? false))
                                            <button onclick="markViolationFixed('{{ $violation['id'] }}')"
                                                class="btn btn-success btn-xs gap-1" title="Marca come risolta">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Fix
                                            </button>
                                        @else
                                            <span class="text-xs text-base-content/40">-</span>
                                        @endif
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
        </script>
    @endpush
</x-layouts.superadmin>
