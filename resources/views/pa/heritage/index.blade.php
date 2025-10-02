{{-- resources/views/pa/heritage/index.blade.php --}}
{{-- PA Heritage List - STUB VIEW per TASK 3.3 testing --}}
{{-- Full implementation in TASK 4.3 (3h) --}}

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patrimonio Culturale - FlorenceEGI PA</title>
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
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
        .filters {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .filters input, .filters select {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
        }
        .filters button {
            background: #1B365D;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        .filters button:hover {
            background: #2D5016;
        }
        .heritage-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .heritage-table th {
            background: #1B365D;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .heritage-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .heritage-table tbody tr:hover {
            background: #f8fafc;
        }
        .coa-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .coa-valid {
            background: #D1FAE5;
            color: #065F46;
        }
        .coa-pending {
            background: #FEF3C7;
            color: #92400E;
        }
        .coa-none {
            background: #F1F5F9;
            color: #475569;
        }
        .view-btn {
            background: #1B365D;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        .view-btn:hover {
            background: #2D5016;
        }
        .pagination {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
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
            <h1>🏛️ Patrimonio Culturale</h1>
            <span class="badge">✅ CONTROLLER ATTIVO</span>
        </div>

        <p style="color: #64748b; margin-top: 10px;">
            Gestione patrimonio per {{ Auth::user()->name ?? 'PA Entity' }}
        </p>

        <div class="note">
            <strong>📋 TASK 3.3 COMPLETATO</strong><br>
            Controller implementato con filtri, authorization, ULM logging, ErrorManager.<br>
            View completa sarà implementata in TASK 4.3 (3h)
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('pa.heritage.index') }}" class="filters">
            <input type="text" 
                   name="search" 
                   placeholder="Cerca per titolo, artista..." 
                   value="{{ request('search') }}"
                   style="flex: 1;">
            
            <select name="coa_status">
                <option value="">Tutti gli stati CoA</option>
                <option value="valid" {{ request('coa_status') === 'valid' ? 'selected' : '' }}>CoA Validi</option>
                <option value="revoked" {{ request('coa_status') === 'revoked' ? 'selected' : '' }}>CoA Revocati</option>
                <option value="no_coa" {{ request('coa_status') === 'no_coa' ? 'selected' : '' }}>Senza CoA</option>
            </select>
            
            <button type="submit">🔍 Filtra</button>
            @if(request('search') || request('coa_status'))
                <a href="{{ route('pa.heritage.index') }}" style="color: #64748b;">✕ Reset</a>
            @endif
        </form>

        {{-- Heritage Table --}}
        <table class="heritage-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titolo</th>
                    <th>Artista</th>
                    <th>Data Creazione</th>
                    <th>Stato CoA</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($heritage as $item)
                    <tr>
                        <td><strong>#{{ $item->id }}</strong></td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->artist ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($item->coa)
                                <span class="coa-badge coa-{{ $item->coa->status }}">
                                    ✅ {{ ucfirst($item->coa->status) }}
                                </span>
                            @else
                                <span class="coa-badge coa-none">⏳ Nessun CoA</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pa.heritage.show', $item) }}" class="view-btn">
                                👁️ Visualizza
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <em style="color: #94a3b8;">
                                @if(request('search') || request('coa_status'))
                                    Nessun risultato trovato per i filtri selezionati.
                                @else
                                    Nessun bene patrimoniale nel catalogo.
                                @endif
                            </em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($heritage->hasPages())
            <div class="pagination">
                {{ $heritage->links() }}
            </div>
        @endif

        {{-- Stats Summary --}}
        <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <strong style="color: #1B365D;">📊 Riepilogo:</strong><br>
            <div style="margin-top: 10px; color: #64748b;">
                Visualizzati: <strong>{{ $heritage->count() }}</strong> item |
                Totale patrimonio: <strong>{{ $heritage->total() }}</strong> item |
                Pagina: <strong>{{ $heritage->currentPage() }}</strong> di <strong>{{ $heritage->lastPage() }}</strong>
            </div>
        </div>

        {{-- Debug Info --}}
        <div class="debug-info">
            <strong>🔧 Debug Info:</strong><br>
            Controller: PAHeritageController@index ✅<br>
            Authorization: collection ownership check ✅<br>
            Filters: search="{{ request('search') }}", coa_status="{{ request('coa_status') }}" ✅<br>
            ULM Logging: Active ✅<br>
            Error Handling: Active ✅<br>
            User: {{ Auth::user()->email }}<br>
            Timestamp: {{ now()->toDateTimeString() }}<br>
            <br>
            <strong>Next Steps:</strong><br>
            - TASK 4.1: pa-layout.blade.php (4h)<br>
            - TASK 4.3: Full heritage/index.blade.php implementation (3h)<br>
            - TASK 4.4: heritage/show.blade.php implementation (4h)
        </div>
    </div>
</body>
</html>
