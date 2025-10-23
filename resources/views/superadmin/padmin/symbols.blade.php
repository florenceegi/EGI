<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Simboli Padmin'">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">💻 Registro Simboli</h1>
            <p class="text-sm text-base-content/70 mt-1">{{ $symbolCount }} simboli indicizzati - {{ count($symbols) }} mostrati</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.padmin.dashboard') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('superadmin.padmin.search') }}" class="btn btn-primary btn-sm">
                🔍 Ricerca Semantica
            </a>
        </div>
    </div>

    {{-- Search Filters --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <h3 class="card-title text-base mb-4">🔍 Cerca Simboli</h3>
            <form method="GET" action="{{ route('superadmin.padmin.symbols') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Text Search --}}
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Nome simbolo</span>
                    </label>
                    <input 
                        type="text" 
                        name="text" 
                        value="{{ request('text') }}" 
                        placeholder="es. ConsentService, hasConsent, User..."
                        class="input input-bordered input-sm w-full" />
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
                        <button type="submit" class="btn btn-primary btn-sm">Cerca</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Symbols Grid --}}
    @if(count($symbols) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($symbols as $symbol)
                <div class="card bg-base-100 shadow-md hover:shadow-xl transition-all duration-300 border-l-4 
                    {{ $symbol['type'] === 'class' ? 'border-blue-500' : '' }}
                    {{ $symbol['type'] === 'interface' ? 'border-purple-500' : '' }}
                    {{ $symbol['type'] === 'trait' ? 'border-pink-500' : '' }}
                    {{ $symbol['type'] === 'method' ? 'border-green-500' : '' }}
                    {{ $symbol['type'] === 'function' ? 'border-yellow-500' : '' }}
                    {{ $symbol['type'] === 'const' ? 'border-orange-500' : '' }}">
                    <div class="card-body p-4">
                        {{-- Header: Type badge + Name --}}
                        <div class="flex items-start gap-2 mb-2">
                            <span class="badge badge-sm
                                {{ $symbol['type'] === 'class' ? 'badge-info' : '' }}
                                {{ $symbol['type'] === 'interface' ? 'badge-secondary' : '' }}
                                {{ $symbol['type'] === 'trait' ? 'badge-accent' : '' }}
                                {{ $symbol['type'] === 'method' ? 'badge-success' : '' }}
                                {{ $symbol['type'] === 'function' ? 'badge-warning' : '' }}
                                {{ $symbol['type'] === 'const' ? 'badge-error' : '' }}">
                                {{ ucfirst($symbol['type']) }}
                            </span>
                            @if(isset($symbol['visibility']))
                                <span class="badge badge-sm badge-ghost">{{ $symbol['visibility'] }}</span>
                            @endif
                        </div>

                        {{-- Symbol Name --}}
                        <h3 class="font-mono font-bold text-base truncate" title="{{ $symbol['name'] }}">
                            {{ $symbol['name'] }}
                        </h3>

                        {{-- File Path --}}
                        <div class="mt-2 flex items-center gap-1 text-xs text-base-content/60">
                            <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <code class="truncate" title="{{ $symbol['filePath'] ?? 'N/A' }}">
                                {{ basename($symbol['filePath'] ?? 'N/A') }}
                            </code>
                        </div>

                        {{-- Line Number --}}
                        @if(isset($symbol['line']))
                            <div class="mt-1 text-xs text-base-content/60">
                                <span class="font-semibold">Line:</span> {{ $symbol['line'] }}
                            </div>
                        @endif

                        {{-- Signature (for methods/functions) --}}
                        @if(isset($symbol['signature']) && $symbol['signature'])
                            <div class="mt-3 pt-3 border-t border-base-300">
                                <code class="text-xs bg-base-200 px-2 py-1 rounded block overflow-x-auto whitespace-nowrap">
                                    {{ $symbol['signature'] }}
                                </code>
                            </div>
                        @endif

                        {{-- Namespace (for classes/interfaces/traits) --}}
                        @if(isset($symbol['namespace']) && $symbol['namespace'])
                            <div class="mt-2 text-xs text-base-content/50">
                                <span class="font-semibold">NS:</span> <code>{{ $symbol['namespace'] }}</code>
                            </div>
                        @endif

                        {{-- Metadata badges --}}
                        <div class="mt-3 flex flex-wrap gap-1">
                            @if(isset($symbol['isAbstract']) && $symbol['isAbstract'])
                                <span class="badge badge-xs badge-outline">abstract</span>
                            @endif
                            @if(isset($symbol['isStatic']) && $symbol['isStatic'])
                                <span class="badge badge-xs badge-outline">static</span>
                            @endif
                            @if(isset($symbol['isFinal']) && $symbol['isFinal'])
                                <span class="badge badge-xs badge-outline">final</span>
                            @endif
                            @if(isset($symbol['isAsync']) && $symbol['isAsync'])
                                <span class="badge badge-xs badge-outline">async</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-bold">Nessun simbolo trovato</h3>
                <p class="text-base-content/70 mt-2">
                    @if(request()->hasAny(['text', 'type', 'filePath']))
                        Prova a modificare i filtri di ricerca.
                    @else
                        L'indice simboli è vuoto. Esegui il parse del codebase per popolare.
                    @endif
                </p>
                @if(request()->hasAny(['text', 'type', 'filePath']))
                    <a href="{{ route('superadmin.padmin.symbols') }}" class="btn btn-outline btn-sm mt-4">
                        Rimuovi filtri
                    </a>
                @endif
            </div>
        </div>
    @endif
</x-layouts.superadmin>
