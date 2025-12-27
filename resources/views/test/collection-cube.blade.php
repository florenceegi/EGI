<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Cube Test | Florence EGI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/collection-cube.css') }}?v={{ time() }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #0f0f23 100%);
            min-height: 100vh;
            color: #fff;
            padding: 40px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(90deg, #64b5f6, #ce93d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
        }

        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .test-card-wrapper {
            position: relative;
        }

        .test-label {
            position: absolute;
            top: -24px;
            left: 0;
            font-size: 0.75rem;
            color: #ce93d8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            margin-bottom: 30px;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .info-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .info-box h3 {
            color: #ce93d8;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul {
            color: rgba(255, 255, 255, 0.7);
            padding-left: 20px;
        }

        .info-box li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <a href="/" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Torna alla Home
    </a>

    <div class="page-header">
        <h1><i class="fas fa-cube"></i> Collection Cube Test</h1>
        <p>Test del componente Collection Cube Card con modale di gestione</p>
    </div>

    <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Come Usare</h3>
        <ul>
            <li><strong>Trascina</strong> il mouse sul cubo per ruotarlo manualmente</li>
            <li><strong>Clicca</strong> sull'icona <i class="fas fa-cog"></i> in alto a destra per aprire la modale di
                gestione</li>
            <li>Nella modale puoi assegnare EGI alle facce, cambiare colori, trasparenza, ecc.</li>
            <li>La configurazione viene salvata in localStorage</li>
        </ul>
    </div>

    <div class="test-grid">
        @php
            // Get some collections with EGIs for testing
            $collections = \App\Models\Collection::with(['egis', 'creator'])
                ->whereHas('egis')
                ->take(4)
                ->get();
        @endphp

        @forelse($collections as $collection)
            <div class="test-card-wrapper">
                <span class="test-label">{{ $collection->collection_name }}</span>
                <x-collection-cube-card :collection="$collection" :showManageButton="true" />
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: rgba(255,255,255,0.5);">
                <i class="fas fa-database" style="font-size: 3rem; margin-bottom: 16px;"></i>
                <p>Nessuna collezione con EGI trovata nel database.</p>
                <p style="font-size: 0.85rem; margin-top: 8px;">Crea alcune collezioni con EGI per testare il
                    componente.</p>
            </div>
        @endforelse
    </div>

    {{-- Load JS --}}
    <script type="module">
        import {
            CollectionCube
        } from '/js/collection-cube.js';
        window.CollectionCube = CollectionCube;
    </script>

    @stack('scripts')
</body>

</html>
