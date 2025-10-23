<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Ricerca Semantica'">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">🔍 Ricerca Semantica</h1>
            <p class="text-sm text-base-content/70 mt-1">Cerca simboli nel codebase con query avanzate</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.padmin.symbols') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Simboli
            </a>
            <a href="{{ route('superadmin.padmin.dashboard') }}" class="btn btn-primary btn-sm">
                📊 Dashboard
            </a>
        </div>
    </div>

    {{-- Advanced Search Form --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">🎯 Ricerca Avanzata</h3>
            <form method="GET" action="{{ route('superadmin.padmin.search') }}" class="space-y-4">
                {{-- Main Search Input --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Query di ricerca</span>
                        <span class="label-text-alt text-base-content/60">Usa * per wildcard</span>
                    </label>
                    <div class="join w-full">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ request('q') }}" 
                            placeholder="es. ConsentService, hasConsent, User*..."
                            class="input input-bordered join-item flex-1"
                            autofocus />
                        <button type="submit" class="btn btn-primary join-item">
                            🔍 Cerca
                        </button>
                    </div>
                </div>

                {{-- Advanced Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search Field --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Campo</span>
                        </label>
                        <select name="searchField" class="select select-bordered select-sm">
                            <option value="">Nome (default)</option>
                            <option value="name" {{ request('searchField') === 'name' ? 'selected' : '' }}>Nome esatto</option>
                            <option value="namespace" {{ request('searchField') === 'namespace' ? 'selected' : '' }}>Namespace</option>
                            <option value="filePath" {{ request('searchField') === 'filePath' ? 'selected' : '' }}>File Path</option>
                        </select>
                    </div>

                    {{-- Type Filter --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tipo</span>
                        </label>
                        <select name="type" class="select select-bordered select-sm">
                            <option value="">Tutti</option>
                            <option value="class" {{ request('type') === 'class' ? 'selected' : '' }}>Class</option>
                            <option value="interface" {{ request('type') === 'interface' ? 'selected' : '' }}>Interface</option>
                            <option value="trait" {{ request('type') === 'trait' ? 'selected' : '' }}>Trait</option>
                            <option value="method" {{ request('type') === 'method' ? 'selected' : '' }}>Method</option>
                            <option value="function" {{ request('type') === 'function' ? 'selected' : '' }}>Function</option>
                            <option value="const" {{ request('type') === 'const' ? 'selected' : '' }}>Constant</option>
                        </select>
                    </div>

                    {{-- Namespace Filter --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Namespace</span>
                        </label>
                        <input 
                            type="text" 
                            name="namespace" 
                            value="{{ request('namespace') }}" 
                            placeholder="App\Services\..."
                            class="input input-bordered input-sm" />
                    </div>

                    {{-- Limit --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Risultati</span>
                        </label>
                        <select name="limit" class="select select-bordered select-sm">
                            <option value="50" {{ request('limit', 100) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('limit', 100) == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('limit') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                    </div>
                </div>

                {{-- Quick Search Examples --}}
                <div class="bg-base-200 p-3 rounded-lg">
                    <p class="text-xs text-base-content/70 font-semibold mb-2">💡 Esempi rapidi:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="document.querySelector('input[name=q]').value='Consent*'; this.form.submit();" class="badge badge-outline badge-sm hover:badge-primary cursor-pointer">
                            Consent*
                        </button>
                        <button type="button" onclick="document.querySelector('input[name=q]').value='Audit*'; this.form.submit();" class="badge badge-outline badge-sm hover:badge-primary cursor-pointer">
                            Audit*
                        </button>
                        <button type="button" onclick="document.querySelector('input[name=q]').value='User'; this.form.submit();" class="badge badge-outline badge-sm hover:badge-primary cursor-pointer">
                            User
                        </button>
                        <button type="button" onclick="document.querySelector('input[name=q]').value='Service'; this.form.submit();" class="badge badge-outline badge-sm hover:badge-primary cursor-pointer">
                            *Service
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Search Results --}}
    @if($searchPerformed)
        @if(count($results) > 0)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title text-base">
                            📋 {{ count($results) }} risultati trovati
                        </h3>
                        <div class="text-xs text-base-content/60">
                            Query: <code class="bg-base-200 px-2 py-1 rounded">{{ request('q') }}</code>
                        </div>
                    </div>

                    {{-- Results Table --}}
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nome</th>
                                    <th>File</th>
                                    <th>Linea</th>
                                    <th>Namespace</th>
                                    <th>Visibilità</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    <tr class="hover">
                                        <td>
                                            <span class="badge badge-sm
                                                {{ $result['type'] === 'class' ? 'badge-info' : '' }}
                                                {{ $result['type'] === 'interface' ? 'badge-secondary' : '' }}
                                                {{ $result['type'] === 'trait' ? 'badge-accent' : '' }}
                                                {{ $result['type'] === 'method' ? 'badge-success' : '' }}
                                                {{ $result['type'] === 'function' ? 'badge-warning' : '' }}
                                                {{ $result['type'] === 'const' ? 'badge-error' : '' }}">
                                                {{ ucfirst($result['type']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code class="font-mono font-bold text-sm">{{ $result['name'] }}</code>
                                            @if(isset($result['signature']) && $result['signature'])
                                                <div class="text-xs text-base-content/60 mt-1">
                                                    <code>{{ Str::limit($result['signature'], 60) }}</code>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-1">
                                                <svg class="h-3 w-3 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <code class="text-xs" title="{{ $result['filePath'] ?? 'N/A' }}">
                                                    {{ basename($result['filePath'] ?? 'N/A') }}
                                                </code>
                                            </div>
                                        </td>
                                        <td class="text-xs text-base-content/60">
                                            {{ $result['line'] ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <code class="text-xs text-base-content/60">
                                                {{ $result['namespace'] ?? '-' }}
                                            </code>
                                        </td>
                                        <td>
                                            @if(isset($result['visibility']))
                                                <span class="badge badge-xs badge-ghost">{{ $result['visibility'] }}</span>
                                            @else
                                                <span class="text-xs text-base-content/40">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Export Actions --}}
                    <div class="mt-4 flex justify-end gap-2">
                        <button onclick="copyResultsToClipboard()" class="btn btn-outline btn-sm">
                            📋 Copia risultati
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold">Nessun risultato trovato</h3>
                    <p class="text-base-content/70 mt-2">
                        La query <code class="bg-base-200 px-2 py-1 rounded">{{ request('q') }}</code> non ha prodotto risultati.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-base-content/60">
                        <p>💡 Suggerimenti:</p>
                        <ul class="list-disc list-inside text-left inline-block">
                            <li>Verifica l'ortografia</li>
                            <li>Usa wildcard (*): es. <code>User*</code> per trovare User, UserService, etc.</li>
                            <li>Rimuovi filtri troppo restrittivi</li>
                            <li>Prova ricerca per namespace o file path</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty State - No Search Performed --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center py-12">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-xl font-bold">Ricerca Semantica OS3 Guardian</h3>
                <p class="text-base-content/70 mt-2 max-w-lg mx-auto">
                    Cerca simboli nel codebase utilizzando query avanzate. Supporta wildcard (*), filtri per tipo, namespace e file path.
                </p>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
                    <div class="bg-base-200 p-4 rounded-lg text-left">
                        <div class="text-2xl mb-2">💻</div>
                        <h4 class="font-bold text-sm mb-1">Cerca per nome</h4>
                        <p class="text-xs text-base-content/60">Trova classi, metodi, funzioni per nome esatto o con wildcard</p>
                    </div>
                    <div class="bg-base-200 p-4 rounded-lg text-left">
                        <div class="text-2xl mb-2">📁</div>
                        <h4 class="font-bold text-sm mb-1">Filtra per tipo</h4>
                        <p class="text-xs text-base-content/60">Restringe la ricerca a class, interface, trait, method, function</p>
                    </div>
                    <div class="bg-base-200 p-4 rounded-lg text-left">
                        <div class="text-2xl mb-2">🎯</div>
                        <h4 class="font-bold text-sm mb-1">Namespace preciso</h4>
                        <p class="text-xs text-base-content/60">Cerca all'interno di un namespace specifico del progetto</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Copy to Clipboard Script --}}
    @push('scripts')
    <script>
        function copyResultsToClipboard() {
            const table = document.querySelector('table tbody');
            if (!table) return;

            let text = 'PADMIN SEARCH RESULTS\n';
            text += '='.repeat(80) + '\n\n';

            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 3) {
                    const type = cells[0].textContent.trim();
                    const name = cells[1].textContent.trim();
                    const file = cells[2].textContent.trim();
                    const line = cells[3]?.textContent.trim() || 'N/A';
                    text += `[${type}] ${name} → ${file}:${line}\n`;
                }
            });

            navigator.clipboard.writeText(text).then(() => {
                showToast('Risultati copiati negli appunti!', 'success');
            }).catch(() => {
                showToast('Errore durante la copia', 'error');
            });
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} fixed top-4 right-4 w-auto max-w-sm shadow-lg z-50`;
            toast.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
    @endpush
</x-layouts.superadmin>
