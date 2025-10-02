{{-- resources/views/pa/heritage/show.blade.php --}}
{{-- PA Heritage Detail - STUB VIEW per TASK 3.3 testing --}}
{{-- Full implementation in TASK 4.4 (4h) --}}

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $egi->title }} - FlorenceEGI PA</title>
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1B365D;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .section-title {
            color: #1B365D;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            padding: 15px;
            background: white;
            border-radius: 6px;
        }

        .info-label {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #1B365D;
            font-size: 16px;
            font-weight: 600;
        }

        .coa-section {
            background: linear-gradient(135deg, #1B365D 0%, #2D5016 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .coa-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .coa-status {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
        }

        .coa-details {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 6px;
        }

        .back-btn {
            background: #64748b;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #475569;
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
        <a href="{{ route('pa.heritage.index') }}" class="back-btn">← Torna alla lista</a>

        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h1>{{ $egi->title }}</h1>
                <p style="color: #64748b; margin-top: 5px;">
                    @if ($egi->artist)
                        Artista: <strong>{{ $egi->artist }}</strong>
                    @endif
                </p>
            </div>
            @if ($egi->coa)
                <span class="badge badge-success">✅ CoA Emesso</span>
            @else
                <span class="badge badge-warning">⏳ CoA Non Emesso</span>
            @endif
        </div>

        <div class="note">
            <strong>📋 TASK 3.3 COMPLETATO</strong><br>
            Controller implementato con authorization check, eager loading, ULM logging.<br>
            View completa sarà implementata in TASK 4.4 (4h)
        </div>

        {{-- EGI Basic Info --}}
        <div class="section">
            <div class="section-title">📋 Informazioni Bene Patrimoniale</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">ID EGI</div>
                    <div class="info-value">#{{ $egi->id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Data Creazione</div>
                    <div class="info-value">{{ $egi->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ultimo Aggiornamento</div>
                    <div class="info-value">{{ $egi->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Collection</div>
                    <div class="info-value">{{ $egi->collection->name ?? 'N/A' }}</div>
                </div>
            </div>

            @if ($egi->description)
                <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 6px;">
                    <strong style="color: #1B365D;">Descrizione:</strong><br>
                    <p style="color: #64748b; margin-top: 10px; line-height: 1.6;">
                        {{ $egi->description }}
                    </p>
                </div>
            @endif
        </div>

        {{-- CoA Section --}}
        @if ($egi->coa)
            <div class="coa-section">
                <div class="coa-header">
                    <h2 style="margin: 0;">📜 Certificate of Authenticity</h2>
                    <div class="coa-status">
                        Status: {{ strtoupper($egi->coa->status) }}
                    </div>
                </div>

                <div class="coa-details">
                    <div class="info-grid">
                        <div>
                            <div style="opacity: 0.8; font-size: 12px;">CoA ID</div>
                            <div style="font-weight: 600; margin-top: 5px;">#{{ $egi->coa->id }}</div>
                        </div>
                        <div>
                            <div style="opacity: 0.8; font-size: 12px;">Data Emissione</div>
                            <div style="font-weight: 600; margin-top: 5px;">
                                {{ $egi->coa->issued_at ? $egi->coa->issued_at->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <div style="opacity: 0.8; font-size: 12px;">Issuer Type</div>
                            <div style="font-weight: 600; margin-top: 5px;">
                                {{ ucfirst($egi->coa->issuer_type) }}
                            </div>
                        </div>
                        <div>
                            <div style="opacity: 0.8; font-size: 12px;">Verification Hash</div>
                            <div style="font-weight: 600; margin-top: 5px; font-size: 11px; font-family: monospace;">
                                {{ $egi->coa->verification_hash ? substr($egi->coa->verification_hash, 0, 16) . '...' : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    {{-- CoA Files --}}
                    @if ($egi->coa->files && $egi->coa->files->count() > 0)
                        <div style="margin-top: 20px;">
                            <strong>📎 Files Allegati ({{ $egi->coa->files->count() }}):</strong>
                            <ul style="margin-top: 10px; list-style: none; padding: 0;">
                                @foreach ($egi->coa->files as $file)
                                    <li
                                        style="padding: 8px; background: rgba(255,255,255,0.1); margin-bottom: 5px; border-radius: 4px;">
                                        📄 {{ $file->kind ?? 'document' }} - {{ $file->filename ?? 'file.pdf' }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- CoA Signatures --}}
                    @if ($egi->coa->signatures && $egi->coa->signatures->count() > 0)
                        <div style="margin-top: 20px;">
                            <strong>✍️ Firme ({{ $egi->coa->signatures->count() }}):</strong>
                            <ul style="margin-top: 10px; list-style: none; padding: 0;">
                                @foreach ($egi->coa->signatures as $signature)
                                    <li
                                        style="padding: 8px; background: rgba(255,255,255,0.1); margin-bottom: 5px; border-radius: 4px;">
                                        ✅ {{ $signature->signer->name ?? 'N/A' }} -
                                        {{ $signature->created_at->format('d/m/Y H:i') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="section">
                <div class="section-title">⚠️ Certificate of Authenticity</div>
                <p style="color: #64748b; text-align: center; padding: 40px;">
                    <em>Nessun CoA emesso per questo bene patrimoniale.</em>
                </p>
            </div>
        @endif

        {{-- CoA Traits --}}
        @if ($egi->coaTraits && $egi->coaTraits->count() > 0)
            <div class="section">
                <div class="section-title">🎨 Caratteristiche CoA ({{ $egi->coaTraits->count() }} traits)</div>
                <div class="info-grid">
                    @foreach ($egi->coaTraits as $trait)
                        <div class="info-item">
                            <div class="info-label">{{ ucfirst($trait->category) }}</div>
                            <div class="info-value">{{ $trait->term }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Debug Info --}}
        <div class="debug-info">
            <strong>🔧 Debug Info:</strong><br>
            Controller: PAHeritageController@show ✅<br>
            Authorization: Ownership verified ✅<br>
            Eager Loading: coa, files, signatures, traits, media ✅<br>
            ULM Logging: Active ✅<br>
            Error Handling: Active ✅<br>
            User: {{ Auth::user()->email }}<br>
            EGI ID: {{ $egi->id }}<br>
            Has CoA: {{ $egi->coa ? 'Yes' : 'No' }}<br>
            Collection: {{ $egi->collection->name ?? 'N/A' }}<br>
            Timestamp: {{ now()->toDateTimeString() }}<br>
            <br>
            <strong>Next Steps:</strong><br>
            - TASK 4.1: pa-layout.blade.php (4h)<br>
            - TASK 4.4: Full heritage/show.blade.php implementation (4h)<br>
            - TASK 4.5: PA UI Components (6h)
        </div>
    </div>
</body>

</html>
