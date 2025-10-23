<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Statistiche Padmin'">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">📊 Statistiche Padmin</h1>
            <p class="text-sm text-base-content/70 mt-1">Metriche aggregate e analisi qualità del codice</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.padmin.violations') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Violazioni
            </a>
            <a href="{{ route('superadmin.padmin.dashboard') }}" class="btn btn-primary btn-sm">
                📊 Dashboard
            </a>
        </div>
    </div>

    {{-- KPI Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Violations --}}
        <div class="card bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-xl">
            <div class="card-body p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Violazioni Totali</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $totalViolations }}</h3>
                    </div>
                    <div class="text-4xl opacity-80">⚠️</div>
                </div>
            </div>
        </div>

        {{-- Fixed Violations --}}
        <div class="card bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-xl">
            <div class="card-body p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Violazioni Risolte</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $fixedViolations }}</h3>
                    </div>
                    <div class="text-4xl opacity-80">✅</div>
                </div>
            </div>
        </div>

        {{-- Fix Rate --}}
        <div class="card bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-xl">
            <div class="card-body p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Tasso di Risoluzione</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $fixRate }}%</h3>
                    </div>
                    <div class="text-4xl opacity-80">📈</div>
                </div>
            </div>
        </div>

        {{-- Symbol Count --}}
        <div class="card bg-gradient-to-br from-purple-500 to-violet-600 text-white shadow-xl">
            <div class="card-body p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Simboli Indicizzati</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $symbolCount }}</h3>
                    </div>
                    <div class="text-4xl opacity-80">💻</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Priority Distribution --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">🎯 Distribuzione per Priorità</h3>
                <div class="space-y-3">
                    {{-- P0 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-error badge-sm">P0</span>
                                <span class="text-sm font-semibold">BLOCKING</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['byPriority']['P0'] }} ({{ $priorityPercentages['P0'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-error h-full rounded-full transition-all duration-500" style="width: {{ $priorityPercentages['P0'] }}%"></div>
                        </div>
                    </div>

                    {{-- P1 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-warning badge-sm">P1</span>
                                <span class="text-sm font-semibold">High</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['byPriority']['P1'] }} ({{ $priorityPercentages['P1'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-warning h-full rounded-full transition-all duration-500" style="width: {{ $priorityPercentages['P1'] }}%"></div>
                        </div>
                    </div>

                    {{-- P2 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-info badge-sm">P2</span>
                                <span class="text-sm font-semibold">Medium</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['byPriority']['P2'] }} ({{ $priorityPercentages['P2'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-info h-full rounded-full transition-all duration-500" style="width: {{ $priorityPercentages['P2'] }}%"></div>
                        </div>
                    </div>

                    {{-- P3 --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-neutral badge-sm">P3</span>
                                <span class="text-sm font-semibold">Low</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['byPriority']['P3'] }} ({{ $priorityPercentages['P3'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-neutral h-full rounded-full transition-all duration-500" style="width: {{ $priorityPercentages['P3'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Severity Distribution --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">🔥 Distribuzione per Severità</h3>
                <div class="space-y-3">
                    {{-- Critical --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-error badge-sm">Critical</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['bySeverity']['critical'] }} ({{ $severityPercentages['critical'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-600 to-red-400 h-full rounded-full transition-all duration-500" style="width: {{ $severityPercentages['critical'] }}%"></div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-warning badge-sm">Error</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['bySeverity']['error'] }} ({{ $severityPercentages['error'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-600 to-orange-400 h-full rounded-full transition-all duration-500" style="width: {{ $severityPercentages['error'] }}%"></div>
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-info badge-sm">Warning</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['bySeverity']['warning'] }} ({{ $severityPercentages['warning'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-yellow-600 to-yellow-400 h-full rounded-full transition-all duration-500" style="width: {{ $severityPercentages['warning'] }}%"></div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-neutral badge-sm">Info</span>
                            </div>
                            <span class="text-sm text-base-content/70">{{ $violationStats['bySeverity']['info'] }} ({{ $severityPercentages['info'] }}%)</span>
                        </div>
                        <div class="w-full bg-base-300 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-400 h-full rounded-full transition-all duration-500" style="width: {{ $severityPercentages['info'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Tables Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Violation Types --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">🏆 Top 5 Tipi di Violazione</h3>
                @if(count($topViolationTypes) > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>Tipo</th>
                                    <th class="text-right">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach($topViolationTypes as $type => $count)
                                    <tr class="hover">
                                        <td>
                                            <span class="badge badge-sm 
                                                {{ $rank === 1 ? 'badge-error' : '' }}
                                                {{ $rank === 2 ? 'badge-warning' : '' }}
                                                {{ $rank === 3 ? 'badge-info' : '' }}
                                                {{ $rank > 3 ? 'badge-neutral' : '' }}">
                                                {{ $rank }}
                                            </span>
                                        </td>
                                        <td>
                                            <code class="text-xs">{{ $type }}</code>
                                        </td>
                                        <td class="text-right font-bold">{{ $count }}</td>
                                    </tr>
                                    @php $rank++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-base-content/60">
                        <div class="text-3xl mb-2">🎉</div>
                        <p class="text-sm">Nessuna violazione registrata!</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Top Problematic Files --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-base mb-4">🔴 Top 5 File Problematici</h3>
                @if(count($topProblematicFiles) > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>File</th>
                                    <th class="text-right">Violazioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @foreach($topProblematicFiles as $file => $count)
                                    <tr class="hover">
                                        <td>
                                            <span class="badge badge-sm 
                                                {{ $rank === 1 ? 'badge-error' : '' }}
                                                {{ $rank === 2 ? 'badge-warning' : '' }}
                                                {{ $rank === 3 ? 'badge-info' : '' }}
                                                {{ $rank > 3 ? 'badge-neutral' : '' }}">
                                                {{ $rank }}
                                            </span>
                                        </td>
                                        <td>
                                            <code class="text-xs">{{ $file }}</code>
                                        </td>
                                        <td class="text-right font-bold">{{ $count }}</td>
                                    </tr>
                                    @php $rank++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-base-content/60">
                        <div class="text-3xl mb-2">🎉</div>
                        <p class="text-sm">Nessun file problematico!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Health Score Card --}}
    <div class="mt-6">
        <div class="card bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">🏥 Code Health Score</h3>
                        <p class="text-sm opacity-90">
                            Basato su: Fix Rate ({{ $fixRate }}%), P0 Violations ({{ $violationStats['byPriority']['P0'] }}), Critical Issues ({{ $violationStats['bySeverity']['critical'] }})
                        </p>
                    </div>
                    <div class="text-center">
                        @php
                            // Simple health score calculation
                            $healthScore = 100;
                            $healthScore -= ($violationStats['byPriority']['P0'] * 5); // -5 per P0
                            $healthScore -= ($violationStats['bySeverity']['critical'] * 3); // -3 per critical
                            $healthScore = max(0, min(100, $healthScore)); // Clamp between 0-100
                        @endphp
                        <div class="text-6xl font-bold">{{ round($healthScore) }}</div>
                        <div class="text-sm opacity-90">su 100</div>
                        <div class="mt-2">
                            @if($healthScore >= 80)
                                <span class="badge badge-success badge-lg">Eccellente</span>
                            @elseif($healthScore >= 60)
                                <span class="badge badge-warning badge-lg">Buono</span>
                            @elseif($healthScore >= 40)
                                <span class="badge badge-warning badge-lg">Migliorabile</span>
                            @else
                                <span class="badge badge-error badge-lg">Critico</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
