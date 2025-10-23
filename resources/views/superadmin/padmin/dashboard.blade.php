<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Padmin Analyzer'">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-base-content">🧠 Padmin Analyzer</h1>
            <p class="mt-2 text-base text-base-content/70">Monitoraggio regole OS3.0, simboli e violazioni in tempo
                reale.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="{{ $healthStatus['redis_stack'] ? 'badge-success' : 'badge-error' }} badge gap-2">
                <div
                    class="{{ $healthStatus['redis_stack'] ? 'bg-green-300' : 'bg-red-300' }} h-2 w-2 animate-pulse rounded-full">
                </div>
                Redis Stack
            </div>
            <div class="{{ $healthStatus['node_cli'] ? 'badge-success' : 'badge-error' }} badge gap-2">
                <div
                    class="{{ $healthStatus['node_cli'] ? 'bg-green-300' : 'bg-red-300' }} h-2 w-2 animate-pulse rounded-full">
                </div>
                Node CLI
            </div>
        </div>
    </div>

    {{-- KPI Cards Grid --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Violations --}}
        <div
            class="card bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold opacity-80">Violazioni Totali</p>
                    <svg class="h-6 w-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-2 text-4xl font-bold" data-countup="{{ $stats['total'] }}">0</h3>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="badge badge-sm border-0 bg-white/20">{{ $stats['unfixed'] }} attive</span>
                    <span class="badge badge-sm border-0 bg-white/20">{{ $stats['fixed'] }} risolte</span>
                </div>
            </div>
        </div>

        {{-- P0 Critical Violations --}}
        <div
            class="card bg-gradient-to-br from-purple-500 to-violet-600 text-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold opacity-80">Violazioni P0</p>
                    <svg class="h-6 w-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="mt-2 text-4xl font-bold" data-countup="{{ $stats['byPriority']['P0'] }}">0</h3>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="badge badge-sm border-0 bg-white/20">BLOCKING</span>
                    <a href="{{ route('superadmin.padmin.violations', ['priority' => 'P0']) }}"
                        class="link-hover link opacity-80">Vedi tutte →</a>
                </div>
            </div>
        </div>

        {{-- Symbols Indexed --}}
        <div
            class="card bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold opacity-80">Simboli Indicizzati</p>
                    <svg class="h-6 w-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <h3 class="mt-2 text-4xl font-bold" data-countup="{{ $symbolCount }}">0</h3>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="badge badge-sm border-0 bg-white/20">Classes, Methods, Functions</span>
                </div>
            </div>
        </div>

        {{-- Health Score --}}
        <div
            class="card bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold opacity-80">Indice Salute</p>
                    <svg class="h-6 w-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @php
                    $healthScore = $stats['total'] > 0 ? round(($stats['fixed'] / $stats['total']) * 100) : 100;
                @endphp
                <h3 class="mt-2 text-4xl font-bold"><span data-countup="{{ $healthScore }}">0</span>%</h3>
                <div class="mt-3">
                    <progress class="progress progress-success h-2 bg-white/20" value="{{ $healthScore }}"
                        max="100"></progress>
                </div>
            </div>
        </div>
    </div>

    {{-- Priority Breakdown --}}
    <div class="card mb-8 bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">📊 Distribuzione per Priorità</h2>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="stat rounded-lg bg-purple-50">
                    <div class="stat-figure text-purple-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="stat-title text-purple-900">P0 - Blocking</div>
                    <div class="stat-value text-purple-600">{{ $stats['byPriority']['P0'] }}</div>
                    <div class="stat-desc text-purple-700">Richiede fix immediato</div>
                </div>
                <div class="stat rounded-lg bg-orange-50">
                    <div class="stat-figure text-orange-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="stat-title text-orange-900">P1 - High</div>
                    <div class="stat-value text-orange-600">{{ $stats['byPriority']['P1'] }}</div>
                    <div class="stat-desc text-orange-700">Fix entro 24h</div>
                </div>
                <div class="stat rounded-lg bg-yellow-50">
                    <div class="stat-figure text-yellow-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-title text-yellow-900">P2 - Medium</div>
                    <div class="stat-value text-yellow-600">{{ $stats['byPriority']['P2'] }}</div>
                    <div class="stat-desc text-yellow-700">Fix entro settimana</div>
                </div>
                <div class="stat rounded-lg bg-blue-50">
                    <div class="stat-figure text-blue-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-title text-blue-900">P3 - Low</div>
                    <div class="stat-value text-blue-600">{{ $stats['byPriority']['P3'] }}</div>
                    <div class="stat-desc text-blue-700">Fix opzionale</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <a href="{{ route('superadmin.padmin.violations') }}"
            class="card border-l-4 border-rose-500 bg-base-100 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-rose-100 p-3">
                        <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="card-title text-base">Violazioni recenti</h2>
                        <p class="text-sm text-base-content/70">Analizza e risolvi violazioni</p>
                    </div>
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('superadmin.padmin.symbols') }}"
            class="card border-l-4 border-blue-500 bg-base-100 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-blue-100 p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="card-title text-base">Registro simboli</h2>
                        <p class="text-sm text-base-content/70">Esplora simboli indicizzati</p>
                    </div>
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('superadmin.padmin.search') }}"
            class="card border-l-4 border-emerald-500 bg-base-100 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-emerald-100 p-3">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="card-title text-base">Ricerca semantica</h2>
                        <p class="text-sm text-base-content/70">Search codice intelligente</p>
                    </div>
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
    </div>

    {{-- CountUp Animation Script --}}
    @push('scripts')
        <script>
            // Vanilla JS CountUp Animation
            document.addEventListener('DOMContentLoaded', function() {
                const countUpElements = document.querySelectorAll('[data-countup]');

                countUpElements.forEach(el => {
                    const target = parseInt(el.dataset.countup);
                    const duration = 1500; // 1.5s animation
                    const start = 0;
                    const increment = target / (duration / 16); // 60fps
                    let current = start;

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            el.textContent = target;
                            clearInterval(timer);
                        } else {
                            el.textContent = Math.floor(current);
                        }
                    }, 16);
                });
            });
        </script>
    @endpush
</x-layouts.superadmin>
