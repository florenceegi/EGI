{{-- resources/views/pa/dashboard.blade.php --}}
{{-- PA Dashboard - STUB VIEW per TASK 3.2 testing --}}
{{-- Full implementation in TASK 4.2 (5h) --}}

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PA Dashboard - FlorenceEGI</title>
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1B365D;
            margin-bottom: 10px;
        }
        .badge {
            background: #10B981;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1B365D;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .stat-value {
            color: #1B365D;
            font-size: 32px;
            font-weight: 700;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            color: #1B365D;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .data-list {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        .data-item {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-item:last-child {
            border-bottom: none;
        }
        .note {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .debug-info {
            margin-top: 40px;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>🏛️ Dashboard PA Entity</h1>
            <span class="badge">✅ CONTROLLER ATTIVO</span>
        </div>

        <p style="color: #64748b; margin-top: 10px;">
            Dashboard per {{ Auth::user()->name ?? 'PA Entity' }}
        </p>

        <div class="note">
            <strong>📋 TASK 3.2 COMPLETATO</strong><br>
            Controller implementato con ULM logging, PAStatisticsService (MOCK), error handling.<br>
            View completa sarà implementata in TASK 4.2 (5h)
        </div>

        {{-- KPI Statistics Grid --}}
        <div class="section">
            <div class="section-title">📊 Statistiche Patrimonio</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Patrimonio Totale</div>
                    <div class="stat-value">{{ $stats['total_heritage'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">CoA Emessi</div>
                    <div class="stat-value">{{ $stats['coa_issued'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Ispezioni Attive</div>
                    <div class="stat-value">{{ $stats['inspections_active'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Verifiche Blockchain</div>
                    <div class="stat-value">{{ $stats['blockchain_verifications'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Recent Heritage --}}
        <div class="section">
            <div class="section-title">🏛️ Patrimonio Recente ({{ $recentHeritage->count() }} items)</div>
            <div class="data-list">
                @forelse($recentHeritage as $egi)
                    <div class="data-item">
                        <strong>{{ $egi->title }}</strong>
                        <br>
                        <small style="color: #64748b;">
                            ID: {{ $egi->id }} |
                            Creato: {{ $egi->created_at->format('d/m/Y') }} |
                            CoA: {{ $egi->coa ? '✅ Emesso' : '⏳ Pending' }}
                        </small>
                    </div>
                @empty
                    <div class="data-item">
                        <em style="color: #94a3b8;">Nessun item nel patrimonio</em>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Actions --}}
        <div class="section">
            <div class="section-title">⚠️ Azioni Pendenti</div>
            <div class="data-list">
                @foreach($pendingActions as $action => $count)
                    <div class="data-item">
                        {{ ucfirst(str_replace('_', ' ', $action)) }}:
                        <strong style="color: #F59E0B;">{{ $count }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CoA Status Distribution --}}
        <div class="section">
            <div class="section-title">📈 Distribuzione Stato CoA</div>
            <div class="data-list">
                @php
                $coaStatusDistribution = [
                    'issued' => [
                        'count' => $stats['coa_issued'] ?? 0,
                        'percentage' => $stats['total_heritage'] > 0
                            ? round(($stats['coa_issued'] / $stats['total_heritage']) * 100, 1)
                            : 0
                    ],
                    'pending' => [
                        'count' => $stats['coa_pending'] ?? 0,
                        'percentage' => $stats['total_heritage'] > 0
                            ? round(($stats['coa_pending'] / $stats['total_heritage']) * 100, 1)
                            : 0
                    ],
                    'draft' => [
                        'count' => $stats['coa_draft'] ?? 0,
                        'percentage' => $stats['total_heritage'] > 0
                            ? round(($stats['coa_draft'] / $stats['total_heritage']) * 100, 1)
                            : 0
                    ],
                ];
                @endphp
                @foreach($coaStatusDistribution as $status => $data)
                    <div class="data-item">
                        <strong>{{ ucfirst($status) }}:</strong>
                        {{ $data['count'] }} ({{ $data['percentage'] }}%)
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Debug Info --}}
        <div class="debug-info">
            <strong>🔧 Debug Info:</strong><br>
            Controller: PADashboardController@index ✅<br>
            Service: PAStatisticsService (MOCK mode) ✅<br>
            ULM Logging: Active ✅<br>
            Error Handling: Active ✅<br>
            User: {{ Auth::user()->email }}<br>
            User ID: {{ Auth::id() }}<br>
            Timestamp: {{ now()->toDateTimeString() }}<br>
            <br>
            <strong>Next Steps:</strong><br>
            - TASK 4.1: pa-layout.blade.php (4h)<br>
            - TASK 4.2: Full dashboard.blade.php implementation (5h)<br>
            - TASK 4.5: PA UI Components (6h)
        </div>
    </div>
</body>
</html>
