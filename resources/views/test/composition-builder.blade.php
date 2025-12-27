<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Composition Builder | Florence EGI</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Share+Tech+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #00ffdd;
            --secondary: #0088ff;
            --danger: #ff0044;
            --gold: #ffaa00;
            --purple: #aa00ff;
            --glass-dark: rgba(5, 8, 12, 0.95);
            --border-light: rgba(255, 255, 255, 0.1);
            --border-active: rgba(0, 255, 221, 0.5);
            --text-main: #e0e0e0;
            --text-muted: #8090a0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            min-height: 100vh;
            font-family: 'Rajdhani', sans-serif;
            color: var(--text-main);
            overflow: hidden;
        }

        /* Three.js canvas */
        #canvas-container {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        canvas {
            display: block;
            cursor: grab;
        }

        canvas:active {
            cursor: grabbing;
        }

        /* Main Layout */
        #app {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 1fr 280px;
            grid-template-rows: 70px 1fr 60px;
            height: 100vh;
            pointer-events: none;
        }

        .ui-panel {
            pointer-events: auto;
            background: var(--glass-dark);
            backdrop-filter: blur(12px);
        }

        /* Top Bar */
        #top-bar {
            grid-column: 1 / -1;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .brand {
            font-family: 'Share Tech Mono';
            font-size: 20px;
            letter-spacing: 2px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand span {
            color: var(--primary);
        }

        .page-title {
            font-size: 14px;
            color: var(--text-muted);
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* Sidebar - EGI List */
        #sidebar {
            grid-row: 2 / 3;
            grid-column: 2 / 3;
            border-left: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-light);
        }

        .sidebar-header h3 {
            font-family: 'Share Tech Mono';
            font-size: 12px;
            color: var(--primary);
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 11px;
            color: var(--text-muted);
        }

        #egi-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .egi-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            cursor: grab;
            transition: all 0.2s;
        }

        .egi-item:hover {
            background: rgba(0, 255, 221, 0.08);
            border-color: var(--border-active);
            transform: translateX(-3px);
        }

        .egi-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .egi-item.in-scene {
            opacity: 0.4;
            border-color: var(--gold);
        }

        .egi-item.in-scene::after {
            content: '✓';
            position: absolute;
            right: 10px;
            color: var(--gold);
        }

        .egi-thumb {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-light);
        }

        .egi-info {
            flex: 1;
            min-width: 0;
        }

        .egi-title {
            font-size: 13px;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .egi-id {
            font-size: 10px;
            color: var(--text-muted);
            font-family: 'Share Tech Mono';
        }

        /* Drop Zone Indicator */
        #drop-zone {
            position: fixed;
            top: 70px;
            left: 0;
            right: 280px;
            bottom: 60px;
            z-index: 5;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        #drop-zone.active {
            opacity: 1;
            pointer-events: auto;
            background: rgba(0, 255, 221, 0.05);
            border: 2px dashed var(--primary);
        }

        .drop-hint {
            font-family: 'Share Tech Mono';
            font-size: 18px;
            color: var(--primary);
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Composition List */
        #composition-list {
            position: fixed;
            bottom: 80px;
            left: 20px;
            right: 300px;
            z-index: 20;
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            min-height: 80px;
            pointer-events: auto;
        }

        .composition-label {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--primary);
            letter-spacing: 1px;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
        }

        #selected-spheres {
            flex: 1;
            display: flex;
            gap: 10px;
            min-height: 50px;
            align-items: center;
            flex-wrap: wrap;
        }

        .selected-item {
            position: relative;
            width: 50px;
            height: 50px;
        }

        .selected-item img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }

        .selected-item .remove-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--danger);
            border: none;
            color: white;
            font-size: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-hint {
            color: var(--text-muted);
            font-size: 13px;
            font-style: italic;
        }

        /* Bottom Bar */
        #bottom-bar {
            grid-column: 1 / -1;
            border-top: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .info-text {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--text-muted);
        }

        .btn {
            padding: 12px 30px;
            font-family: 'Share Tech Mono';
            font-size: 13px;
            letter-spacing: 2px;
            border: 1px solid var(--primary);
            background: transparent;
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn:hover {
            background: var(--primary);
            color: #000;
            box-shadow: 0 0 20px var(--primary);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn:disabled:hover {
            background: transparent;
            color: var(--primary);
            box-shadow: none;
        }

        .btn-confirm {
            border-color: var(--gold);
            color: var(--gold);
        }

        .btn-confirm:hover {
            background: var(--gold);
            box-shadow: 0 0 20px var(--gold);
        }

        /* Global Orbit Controls */
        .global-orbit-controls {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 0 20px;
            border-left: 1px solid var(--border-light);
            border-right: 1px solid var(--border-light);
            height: 100%;
            pointer-events: auto;
            position: relative;
            z-index: 50;
        }

        .global-orbit-controls .orbit-label {
            font-family: 'Share Tech Mono';
            font-size: 10px;
            color: var(--primary);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .global-orbit-controls .orbit-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .global-orbit-controls .mini-toggle {
            width: 36px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 100;
            pointer-events: auto;
        }

        .global-orbit-controls .mini-toggle::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            background: var(--text-muted);
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: all 0.3s;
        }

        .global-orbit-controls .mini-toggle.active {
            background: rgba(0, 255, 221, 0.3);
        }

        .global-orbit-controls .mini-toggle.active::before {
            background: var(--primary);
            left: 18px;
            box-shadow: 0 0 8px var(--primary);
        }

        .global-orbit-controls .orbit-speed-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .global-orbit-controls .orbit-speed-slider {
            width: 80px;
            height: 4px;
            -webkit-appearance: none;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            outline: none;
        }

        .global-orbit-controls .orbit-speed-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 12px;
            height: 12px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
        }

        .global-orbit-controls .orbit-speed-value {
            font-family: 'Share Tech Mono';
            font-size: 10px;
            color: var(--text-main);
            min-width: 30px;
        }

        .global-orbit-controls .direction-btn {
            padding: 4px 10px;
            font-family: 'Share Tech Mono';
            font-size: 10px;
            letter-spacing: 1px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
            z-index: 100;
            pointer-events: auto;
        }

        .global-orbit-controls .direction-btn.active {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0, 255, 221, 0.1);
        }

        /* Loader */
        #loader {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s;
        }

        .loader-t {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 6px;
            color: white;
            margin-bottom: 20px;
            font-family: 'Share Tech Mono';
        }

        .bar-c {
            width: 200px;
            height: 2px;
            background: #222;
        }

        .bar-f {
            width: 0%;
            height: 100%;
            background: var(--primary);
            animation: load 1.5s ease-out forwards;
        }

        @keyframes load {
            to {
                width: 100%;
            }
        }

        /* Sphere Settings Modal */
        #sphere-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 500;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }

        #sphere-modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--glass-dark);
            border: 1px solid var(--border-active);
            border-radius: 16px;
            width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 0 40px rgba(0, 255, 221, 0.2);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid var(--border-light);
        }

        .modal-header h2 {
            font-family: 'Share Tech Mono';
            font-size: 16px;
            color: white;
            letter-spacing: 2px;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            color: var(--danger);
        }

        .modal-body {
            padding: 20px;
        }

        .setting-group {
            margin-bottom: 24px;
        }

        .setting-group:last-child {
            margin-bottom: 0;
        }

        .setting-label {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--primary);
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: block;
            text-transform: uppercase;
        }

        .color-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .color-picker-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-picker-wrap label {
            font-size: 12px;
            color: var(--text-muted);
            min-width: 60px;
        }

        .color-picker-wrap input[type="color"] {
            width: 50px;
            height: 32px;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            background: transparent;
            cursor: pointer;
            padding: 2px;
        }

        .slider-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .slider-wrap label {
            font-size: 12px;
            color: var(--text-muted);
            min-width: 100px;
        }

        .slider-wrap input[type="range"] {
            flex: 1;
            height: 4px;
            -webkit-appearance: none;
            background: var(--border-light);
            border-radius: 2px;
            outline: none;
        }

        .slider-wrap input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
        }

        .slider-wrap .value {
            font-family: 'Share Tech Mono';
            font-size: 12px;
            color: white;
            min-width: 40px;
            text-align: right;
        }

        .toggle-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
        }

        .toggle-wrap label {
            font-size: 13px;
            color: var(--text-main);
        }

        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
            margin: 0;
        }

        .toggle-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--border-light);
            border-radius: 12px;
            transition: 0.3s;
        }

        .toggle-switch .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        .toggle-switch input:checked+.slider {
            background: var(--primary);
        }

        .toggle-switch input:checked+.slider:before {
            transform: translateX(20px);
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .sphere-preview {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
        }

        .sphere-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        .sphere-preview .title {
            margin-top: 10px;
            font-size: 14px;
            color: white;
            font-weight: 600;
        }

        /* Shape selector */
        .shape-selector {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .shape-option {
            padding: 10px 8px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            font-family: 'Share Tech Mono';
            font-size: 9px;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 6px;
            text-align: center;
        }

        .shape-option:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .shape-option.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        .shape-option .icon {
            font-size: 18px;
            display: block;
            margin-bottom: 4px;
        }
    </style>

    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/",
                "@tweenjs/tween.js": "https://unpkg.com/@tweenjs/tween.js@23.1.1/dist/tween.esm.js"
            }
        }
    </script>
</head>

<body>
    <div id="loader">
        <div class="loader-t">COMPOSITION BUILDER</div>
        <div class="bar-c">
            <div class="bar-f"></div>
        </div>
    </div>

    <div id="canvas-container"></div>

    <div id="drop-zone">
        <div class="drop-hint">↓ Rilascia qui per aggiungere ↓</div>
    </div>

    <div id="app">
        <div id="top-bar" class="ui-panel">
            <div class="brand">
                F<span>EGI</span> | COMPOSITION
            </div>
            <div class="page-title">Costruttore Composizione 3D</div>
        </div>

        <div id="sidebar" class="ui-panel">
            <div class="sidebar-header">
                <h3>EGI DISPONIBILI</h3>
                <p>Trascina le sfere nell'area centrale</p>
            </div>
            <div id="egi-list">
                @foreach ($egis as $egi)
                    <div class="egi-item" draggable="true" data-egi-id="{{ $egi->id }}"
                        data-egi-title="{{ $egi->title }}"
                        data-egi-thumb="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp">
                        <img class="egi-thumb"
                            src="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp"
                            alt="{{ $egi->title }}">
                        <div class="egi-info">
                            <div class="egi-title">{{ $egi->title }}</div>
                            <div class="egi-id">ID: {{ $egi->id }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="bottom-bar" class="ui-panel">
            <div class="info-text">
                <i class="fa-solid fa-cube"></i>
                Sfere selezionate: <span id="count">0</span>
            </div>

            <!-- Global Orbit Controls -->
            <div class="global-orbit-controls">
                <span class="orbit-label"><i class="fa-solid fa-sync-alt"></i> Orbita Globale</span>
                <div class="orbit-toggle">
                    <div class="mini-toggle" id="global-orbit-toggle" onclick="toggleGlobalOrbit()"></div>
                </div>
                <div class="orbit-speed-wrapper">
                    <span style="font-size: 10px; color: var(--text-muted);">Vel:</span>
                    <input type="range" class="orbit-speed-slider" id="global-orbit-speed" min="1"
                        max="100" value="30" oninput="updateGlobalOrbitSpeed(this.value)">
                    <span class="orbit-speed-value" id="global-orbit-speed-value">30</span>
                </div>
                <button class="direction-btn active" id="orbit-cw" onclick="setGlobalOrbitDirection('cw')">⟳
                    CW</button>
                <button class="direction-btn" id="orbit-ccw" onclick="setGlobalOrbitDirection('ccw')">⟲ CCW</button>
            </div>

            <div>
                <button class="btn" onclick="clearSelection()">RESET</button>
                <button class="btn-confirm btn" id="confirm-btn" disabled onclick="confirmComposition()">
                    CONFERMA COMPOSIZIONE
                </button>
            </div>
        </div>
    </div>

    <div id="composition-list">
        <div class="composition-label">COMPOSIZIONE</div>
        <div id="selected-spheres">
            <span class="empty-hint">Nessun EGI selezionato. Trascina dalla sidebar →</span>
        </div>
    </div>

    <!-- Sphere Settings Modal -->
    <div id="sphere-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-sliders"></i> IMPOSTAZIONI SFERA</h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="sphere-preview">
                    <img id="modal-thumb" src="" alt="">
                    <div class="title" id="modal-title">-</div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Forma</span>
                    <div class="shape-selector">
                        <button class="shape-option active" data-shape="sphere">
                            <span class="icon">🔮</span>SFERA
                        </button>
                        <button class="shape-option" data-shape="crystal">
                            <span class="icon">💎</span>CRISTALLO
                        </button>
                        <button class="shape-option" data-shape="cube">
                            <span class="icon">📦</span>CUBO
                        </button>
                        <button class="shape-option" data-shape="pyramid">
                            <span class="icon">⛰️</span>PIRAMIDE
                        </button>
                        <button class="shape-option" data-shape="dodeca">
                            <span class="icon">🔷</span>DODECA
                        </button>
                        <button class="shape-option" data-shape="torus">
                            <span class="icon">🍩</span>TORUS
                        </button>
                        <button class="shape-option" data-shape="torusknot">
                            <span class="icon">🌀</span>KNOT
                        </button>
                        <button class="shape-option" data-shape="capsule">
                            <span class="icon">💊</span>CAPSULA
                        </button>
                        <button class="shape-option" data-shape="spaceship">
                            <span class="icon">🚀</span>NAVE
                        </button>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Colori</span>
                    <div class="color-row">
                        <div class="color-picker-wrap">
                            <label>Vetro</label>
                            <input type="color" id="color-glass" value="#00ffdd">
                        </div>
                        <div class="color-picker-wrap">
                            <label>Anelli</label>
                            <input type="color" id="color-rings" value="#ffffff">
                        </div>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Opacità Vetro</span>
                    <div class="slider-wrap">
                        <label>Trasparenza</label>
                        <input type="range" id="glass-opacity" min="0" max="100" value="15">
                        <span class="value" id="glass-opacity-val">15%</span>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Luminosità Immagine</span>
                    <div class="slider-wrap">
                        <label>Emissive</label>
                        <input type="range" id="emissive-intensity" min="0" max="100" value="40">
                        <span class="value" id="emissive-val">0.4</span>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Opacità Anelli</span>
                    <div class="slider-wrap">
                        <label>Visibilità</label>
                        <input type="range" id="rings-opacity" min="0" max="100" value="50">
                        <span class="value" id="rings-opacity-val">50%</span>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Elementi</span>
                    <div class="toggle-wrap">
                        <label>Mostra Anello 1</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="show-ring1" checked>
                            <span class="slider"></span>
                        </div>
                    </div>
                    <div class="toggle-wrap">
                        <label>Mostra Anello 2</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="show-ring2" checked>
                            <span class="slider"></span>
                        </div>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Rotazione Sfera</span>
                    <div class="toggle-wrap">
                        <label>Rotazione automatica</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="enable-rotation">
                            <span class="slider"></span>
                        </div>
                    </div>
                    <div class="slider-wrap">
                        <label>Velocità auto</label>
                        <input type="range" id="rotation-speed" min="1" max="100" value="30">
                        <span class="value" id="rotation-speed-val">30%</span>
                    </div>
                    <div class="slider-wrap">
                        <label>Angolo manuale</label>
                        <input type="range" id="rotation-angle" min="0" max="360" value="0">
                        <span class="value" id="rotation-angle-val">0°</span>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Orbita attorno al centro</span>
                    <div class="toggle-wrap">
                        <label>Abilita orbita</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="enable-orbit">
                            <span class="slider"></span>
                        </div>
                    </div>
                    <div class="slider-wrap">
                        <label>Velocità orbita</label>
                        <input type="range" id="orbit-speed" min="1" max="100" value="20">
                        <span class="value" id="orbit-speed-val">20%</span>
                    </div>
                    <div class="toggle-wrap">
                        <label>Senso orario</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="orbit-clockwise" checked>
                            <span class="slider"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="resetSphereSettings()">RESET</button>
                <button class="btn-confirm btn" onclick="applySphereSettings()">APPLICA</button>
            </div>
        </div>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import {
            OrbitControls
        } from 'three/addons/controls/OrbitControls.js';
        import {
            EffectComposer
        } from 'three/addons/postprocessing/EffectComposer.js';
        import {
            RenderPass
        } from 'three/addons/postprocessing/RenderPass.js';
        import {
            UnrealBloomPass
        } from 'three/addons/postprocessing/UnrealBloomPass.js';
        import {
            RoomEnvironment
        } from 'three/addons/environments/RoomEnvironment.js';
        import TWEEN from '@tweenjs/tween.js';

        // ===========================================
        // STATE
        // ===========================================
        const selectedEgis = new Map(); // id -> { data, mesh }

        // Global orbit state
        const globalOrbitState = {
            enabled: false,
            speed: 30,
            clockwise: true
        };

        // ===========================================
        // THREE.JS SETUP
        // ===========================================
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(0, 0, 400);

        const renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        container.appendChild(renderer.domElement);

        // Environment
        const pmremGenerator = new THREE.PMREMGenerator(renderer);
        scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
        scene.background = new THREE.Color(0x020202);

        // Post-processing
        const composer = new EffectComposer(renderer);
        composer.addPass(new RenderPass(scene, camera));

        const bloomPass = new UnrealBloomPass(
            new THREE.Vector2(window.innerWidth, window.innerHeight),
            1.5, 0.4, 0.85
        );
        bloomPass.threshold = 0.9;
        bloomPass.strength = 0.3;
        bloomPass.radius = 0.1;
        composer.addPass(bloomPass);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.maxDistance = 800;
        controls.minDistance = 150;

        // ===========================================
        // STARS BACKGROUND
        // ===========================================
        const starsGeo = new THREE.BufferGeometry();
        const starsCount = 3000;
        const starPos = new Float32Array(starsCount * 3);
        for (let i = 0; i < starsCount * 3; i++) {
            starPos[i] = (Math.random() - 0.5) * 1500;
        }
        starsGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3));
        const stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({
            color: 0x888888,
            size: 0.7,
            transparent: true,
            opacity: 0.8
        }));
        scene.add(stars);

        // ===========================================
        // GEOMETRY HELPERS
        // ===========================================
        function getGeometry(shapeKey, size) {
            switch (shapeKey) {
                case 'sphere':
                    return new THREE.SphereGeometry(size, 64, 32);
                case 'crystal':
                    return new THREE.OctahedronGeometry(size, 0);
                case 'cube':
                    return new THREE.BoxGeometry(size * 1.5, size * 1.5, size * 1.5, 2, 2, 2);
                case 'pyramid':
                    return new THREE.ConeGeometry(size, size * 1.8, 4);
                case 'dodeca':
                    return new THREE.DodecahedronGeometry(size, 0);
                case 'torus':
                    return new THREE.TorusGeometry(size * 0.8, size * 0.35, 32, 100);
                case 'torusknot':
                    return new THREE.TorusKnotGeometry(size * 0.6, size * 0.2, 100, 16);
                case 'capsule':
                    return new THREE.CapsuleGeometry(size * 0.5, size * 1.2, 16, 32);
                case 'spaceship': {
                    // Sleek spaceship shape using LatheGeometry
                    const points = [];
                    // Nose cone
                    points.push(new THREE.Vector2(0, size * 1.5));
                    points.push(new THREE.Vector2(size * 0.15, size * 1.2));
                    points.push(new THREE.Vector2(size * 0.25, size * 0.9));
                    // Cockpit bulge
                    points.push(new THREE.Vector2(size * 0.4, size * 0.6));
                    points.push(new THREE.Vector2(size * 0.5, size * 0.3));
                    // Body
                    points.push(new THREE.Vector2(size * 0.55, size * 0));
                    points.push(new THREE.Vector2(size * 0.5, size * -0.4));
                    // Engine section
                    points.push(new THREE.Vector2(size * 0.6, size * -0.8));
                    points.push(new THREE.Vector2(size * 0.7, size * -1.0));
                    points.push(new THREE.Vector2(size * 0.5, size * -1.1));
                    points.push(new THREE.Vector2(size * 0.3, size * -1.2));
                    points.push(new THREE.Vector2(0, size * -1.2));
                    return new THREE.LatheGeometry(points, 32);
                }
                default:
                    return new THREE.SphereGeometry(size, 64, 32);
            }
        }

        function getInnerGeometry(shapeKey, size) {
            const innerSize = size * 0.8;
            switch (shapeKey) {
                case 'sphere':
                    return new THREE.SphereGeometry(innerSize, 64, 32);
                case 'crystal':
                    return new THREE.OctahedronGeometry(innerSize * 0.7, 2);
                case 'cube':
                    return new THREE.BoxGeometry(innerSize * 1.3, innerSize * 1.3, innerSize * 1.3);
                case 'pyramid':
                    return new THREE.ConeGeometry(innerSize * 0.8, innerSize * 1.4, 4);
                case 'dodeca':
                    return new THREE.DodecahedronGeometry(innerSize * 0.85, 1);
                case 'torus':
                    return new THREE.TorusGeometry(innerSize * 0.65, innerSize * 0.25, 32, 100);
                case 'torusknot':
                    return new THREE.TorusKnotGeometry(innerSize * 0.5, innerSize * 0.15, 100, 16);
                case 'capsule':
                    return new THREE.CapsuleGeometry(innerSize * 0.4, innerSize, 16, 32);
                case 'spaceship': {
                    const points = [];
                    const s = innerSize * 0.85;
                    points.push(new THREE.Vector2(0, s * 1.4));
                    points.push(new THREE.Vector2(s * 0.12, s * 1.1));
                    points.push(new THREE.Vector2(s * 0.22, s * 0.8));
                    points.push(new THREE.Vector2(s * 0.35, s * 0.5));
                    points.push(new THREE.Vector2(s * 0.42, s * 0.2));
                    points.push(new THREE.Vector2(s * 0.45, s * 0));
                    points.push(new THREE.Vector2(s * 0.4, s * -0.35));
                    points.push(new THREE.Vector2(s * 0.5, s * -0.7));
                    points.push(new THREE.Vector2(s * 0.55, s * -0.9));
                    points.push(new THREE.Vector2(s * 0.4, s * -1.0));
                    points.push(new THREE.Vector2(s * 0.25, s * -1.1));
                    points.push(new THREE.Vector2(0, s * -1.1));
                    return new THREE.LatheGeometry(points, 32);
                }
                default:
                    return new THREE.SphereGeometry(innerSize, 64, 32);
            }
        }

        // ===========================================
        // SHAPE CREATION
        // ===========================================
        function createShape(imageUrl, position, radius = 40, shapeType = 'sphere') {
            const group = new THREE.Group();
            group.userData.shapeType = shapeType;

            // Glass material
            const glassMat = new THREE.MeshPhysicalMaterial({
                color: 0x00ffdd,
                metalness: 0.05,
                roughness: 0.02,
                transparent: true,
                opacity: 0.15,
                envMapIntensity: 0.5,
                clearcoat: 0.5,
                side: THREE.FrontSide
            });

            // Glass shell
            const glassGeo = getGeometry(shapeType, radius);
            const glassMesh = new THREE.Mesh(glassGeo, glassMat);
            group.add(glassMesh);
            group.userData.glassMesh = glassMesh;

            // Image inner mesh
            const textureLoader = new THREE.TextureLoader();
            textureLoader.load(imageUrl, (texture) => {
                texture.colorSpace = THREE.SRGBColorSpace;

                const innerGeo = getInnerGeometry(shapeType, radius);
                const innerMat = new THREE.MeshStandardMaterial({
                    map: texture,
                    side: THREE.DoubleSide,
                    emissive: 0xffffff,
                    emissiveMap: texture,
                    emissiveIntensity: 0.4,
                    roughness: 1.0,
                    metalness: 0.0
                });
                const imageMesh = new THREE.Mesh(innerGeo, innerMat);
                group.add(imageMesh);
                group.userData.imageMesh = imageMesh;
                group.userData.imageTexture = texture;
            });

            // Rings (skip for torus shapes)
            if (shapeType !== 'torus' && shapeType !== 'torusknot') {
                const ringMat = new THREE.MeshStandardMaterial({
                    color: 0xffffff,
                    metalness: 0.8,
                    roughness: 0.2,
                    transparent: true,
                    opacity: 0.5
                });

                const ring1 = new THREE.Mesh(
                    new THREE.TorusGeometry(radius * 1.2, 0.3, 16, 100),
                    ringMat.clone()
                );
                ring1.rotation.x = Math.PI / 1.7;
                group.add(ring1);
                group.userData.ring1 = ring1;

                const ring2 = new THREE.Mesh(
                    new THREE.TorusGeometry(radius * 1.4, 0.3, 16, 100),
                    ringMat.clone()
                );
                ring2.rotation.y = Math.PI / 2;
                group.add(ring2);
                group.userData.ring2 = ring2;
            }

            group.position.set(position.x, position.y, position.z);

            return group;
        }

        // ===========================================
        // LAYOUT SPHERES
        // ===========================================
        function updateLayout() {
            const count = selectedEgis.size;
            const radius = 120 + count * 10; // Dynamic radius based on count

            let index = 0;
            selectedEgis.forEach((data, id) => {
                const angle = (index / count) * Math.PI * 2;
                const x = Math.cos(angle) * radius;
                const y = Math.sin(angle) * radius * 0.5; // Flattened ellipse
                const z = Math.sin(angle) * 30;

                // Animate to new position
                new TWEEN.Tween(data.mesh.position)
                    .to({
                        x,
                        y,
                        z
                    }, 500)
                    .easing(TWEEN.Easing.Quadratic.Out)
                    .start();

                index++;
            });
        }

        // ===========================================
        // ADD/REMOVE SPHERES
        // ===========================================
        window.addSphereToScene = function(egiId, thumbUrl, title) {
            if (selectedEgis.has(egiId)) return;

            const position = {
                x: (Math.random() - 0.5) * 100,
                y: (Math.random() - 0.5) * 100,
                z: 0
            };
            const mesh = createShape(thumbUrl, position, 35, 'sphere');
            scene.add(mesh);

            selectedEgis.set(egiId, {
                id: egiId,
                title,
                thumbUrl,
                mesh,
                settings: {
                    ...defaultSettings
                }
            });

            updateLayout();
            updateUI();
        };

        window.removeSphereFromScene = function(egiId) {
            const data = selectedEgis.get(egiId);
            if (data) {
                scene.remove(data.mesh);
                selectedEgis.delete(egiId);
                updateLayout();
                updateUI();
            }
        };

        window.clearSelection = function() {
            selectedEgis.forEach((data) => {
                scene.remove(data.mesh);
            });
            selectedEgis.clear();
            updateUI();
        };

        function updateUI() {
            const count = selectedEgis.size;
            document.getElementById('count').textContent = count;
            document.getElementById('confirm-btn').disabled = count < 2;

            // Update selected spheres display
            const container = document.getElementById('selected-spheres');
            if (count === 0) {
                container.innerHTML = '<span class="empty-hint">Nessun EGI selezionato. Trascina dalla sidebar →</span>';
            } else {
                container.innerHTML = '';
                selectedEgis.forEach((data, id) => {
                    const item = document.createElement('div');
                    item.className = 'selected-item';
                    item.innerHTML = `
                        <img src="${data.thumbUrl}" alt="${data.title}">
                        <button class="remove-btn" onclick="removeSphereFromScene(${id})">×</button>
                    `;
                    container.appendChild(item);
                });
            }

            // Update sidebar items
            document.querySelectorAll('.egi-item').forEach(item => {
                const id = parseInt(item.dataset.egiId);
                item.classList.toggle('in-scene', selectedEgis.has(id));
            });
        }

        window.confirmComposition = function() {
            const ids = Array.from(selectedEgis.keys());
            console.log('Composizione confermata:', ids);
            alert('Composizione con ' + ids.length + ' elementi confermata!\nIDs: ' + ids.join(', '));
            // TODO: Navigate to composition result page
        };

        // ===========================================
        // DRAG & DROP
        // ===========================================
        const dropZone = document.getElementById('drop-zone');

        document.querySelectorAll('.egi-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    id: parseInt(item.dataset.egiId),
                    title: item.dataset.egiTitle,
                    thumb: item.dataset.egiThumb
                }));
                item.classList.add('dragging');
                dropZone.classList.add('active');
            });

            item.addEventListener('dragend', () => {
                item.classList.remove('dragging');
                dropZone.classList.remove('active');
            });
        });

        // Drop zone events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.background = 'rgba(0,255,221,0.1)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.background = 'rgba(0,255,221,0.05)';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('active');

            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                addSphereToScene(data.id, data.thumb, data.title);
            } catch (err) {
                console.error('Drop error:', err);
            }
        });

        // Also allow dropping on canvas
        container.addEventListener('dragover', (e) => e.preventDefault());
        container.addEventListener('drop', (e) => {
            e.preventDefault();
            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                addSphereToScene(data.id, data.thumb, data.title);
            } catch (err) {}
        });

        // ===========================================
        // ANIMATION LOOP
        // ===========================================
        const clock = new THREE.Clock();

        function animate() {
            requestAnimationFrame(animate);

            const time = clock.getElapsedTime();
            controls.update();
            TWEEN.update();

            // Animate all spheres
            selectedEgis.forEach((data) => {
                const mesh = data.mesh;
                const settings = data.settings || defaultSettings;

                if (mesh.userData.ring1) {
                    mesh.userData.ring1.rotation.z += 0.008;
                }
                if (mesh.userData.ring2) {
                    mesh.userData.ring2.rotation.y -= 0.01;
                }

                // Sphere rotation or billboard
                if (mesh.userData.imageMesh) {
                    if (settings.enableRotation) {
                        // Auto-rotate the image sphere on Y axis
                        const speed = settings.rotationSpeed / 1000;
                        mesh.userData.imageMesh.rotation.y += speed;
                    } else {
                        // Manual angle mode - set fixed rotation
                        const angle = (settings.rotationAngle || 0) * (Math.PI / 180);
                        mesh.userData.imageMesh.rotation.y = angle;
                    }
                }

                // Orbital rotation around center (0,0,0)
                if (settings.enableOrbit) {
                    const orbitSpeed = (settings.orbitSpeed / 5000) * (settings.orbitClockwise ? -1 : 1);
                    const pos = mesh.position;
                    const radius = Math.sqrt(pos.x * pos.x + pos.z * pos.z);

                    if (radius > 0.1) { // Avoid division by zero
                        const currentAngle = Math.atan2(pos.z, pos.x);
                        const newAngle = currentAngle + orbitSpeed;

                        pos.x = Math.cos(newAngle) * radius;
                        pos.z = Math.sin(newAngle) * radius;
                    }
                }
            });

            // Global orbit animation - rotates ALL spheres on X-Y plane (frontal view)
            // Like clock hands: objects orbit around the Z-axis (camera facing axis)
            if (globalOrbitState.enabled) {
                const orbitSpeed = (globalOrbitState.speed / 3000) * (globalOrbitState.clockwise ? -1 : 1);

                selectedEgis.forEach((data) => {
                    const mesh = data.mesh;
                    const pos = mesh.position;

                    // Calculate radius on X-Y plane (frontal plane - like a clock face)
                    const radius = Math.sqrt(pos.x * pos.x + pos.y * pos.y);

                    if (radius > 0.1) { // Avoid division by zero for objects at center
                        // Get current angle on X-Y plane (clockwise from top = 12 o'clock)
                        const currentAngle = Math.atan2(pos.x, pos.y);
                        const newAngle = currentAngle + orbitSpeed;

                        // Apply new position (rotating around Z-axis, like clock hands)
                        pos.x = Math.sin(newAngle) * radius;
                        pos.y = Math.cos(newAngle) * radius;
                        // Z remains unchanged - depth position stays the same
                    }
                });
            }

            composer.render();
        }

        animate();

        // ===========================================
        // RESIZE
        // ===========================================
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            composer.setSize(window.innerWidth, window.innerHeight);
        });

        // ===========================================
        // HIDE LOADER
        // ===========================================
        window.onload = () => {
            setTimeout(() => document.getElementById('loader').style.opacity = 0, 800);
            setTimeout(() => document.getElementById('loader').remove(), 1300);
        };

        // ===========================================
        // GLOBAL ORBIT CONTROLS
        // ===========================================
        function toggleGlobalOrbit() {
            globalOrbitState.enabled = !globalOrbitState.enabled;
            const toggle = document.getElementById('global-orbit-toggle');
            toggle.classList.toggle('active', globalOrbitState.enabled);
        }

        function updateGlobalOrbitSpeed(value) {
            globalOrbitState.speed = parseInt(value);
            document.getElementById('global-orbit-speed-value').textContent = value;
        }

        function setGlobalOrbitDirection(dir) {
            globalOrbitState.clockwise = (dir === 'cw');
            document.getElementById('orbit-cw').classList.toggle('active', dir === 'cw');
            document.getElementById('orbit-ccw').classList.toggle('active', dir === 'ccw');
        }

        // Expose global orbit functions to window
        window.toggleGlobalOrbit = toggleGlobalOrbit;
        window.updateGlobalOrbitSpeed = updateGlobalOrbitSpeed;
        window.setGlobalOrbitDirection = setGlobalOrbitDirection;

        // ===========================================
        // RAYCASTING - CLICK ON SPHERES
        // ===========================================
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();
        let currentEditingEgiId = null;

        // Default settings template
        const defaultSettings = {
            shapeType: 'sphere',
            glassColor: '#00ffdd',
            ringsColor: '#ffffff',
            glassOpacity: 15,
            emissiveIntensity: 40,
            ringsOpacity: 50,
            showRing1: true,
            showRing2: true,
            enableRotation: false,
            rotationSpeed: 30,
            rotationAngle: 0,
            enableOrbit: false,
            orbitSpeed: 20,
            orbitClockwise: true
        };

        function onCanvasClick(event) {
            // Ignore if modal is open
            if (document.getElementById('sphere-modal').classList.contains('active')) return;

            // Calculate mouse position
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);

            // Get all sphere meshes for intersection
            const meshes = [];
            selectedEgis.forEach((data) => {
                data.mesh.traverse((child) => {
                    if (child.isMesh) meshes.push(child);
                });
            });

            const intersects = raycaster.intersectObjects(meshes);

            if (intersects.length > 0) {
                // Find which EGI this mesh belongs to
                const hitObject = intersects[0].object;
                let hitGroup = hitObject;
                while (hitGroup.parent && hitGroup.parent !== scene) {
                    hitGroup = hitGroup.parent;
                }

                // Find egiId by mesh reference
                for (const [egiId, data] of selectedEgis) {
                    if (data.mesh === hitGroup) {
                        openSphereModal(egiId);
                        break;
                    }
                }
            }
        }

        renderer.domElement.addEventListener('click', onCanvasClick);

        // ===========================================
        // MODAL FUNCTIONS
        // ===========================================
        window.openSphereModal = function(egiId) {
            const data = selectedEgis.get(egiId);
            if (!data) return;

            currentEditingEgiId = egiId;

            // Populate modal
            document.getElementById('modal-thumb').src = data.thumbUrl;
            document.getElementById('modal-title').textContent = data.title;

            // Load current settings or defaults
            const settings = data.settings || {
                ...defaultSettings
            };

            document.getElementById('color-glass').value = settings.glassColor;
            document.getElementById('color-rings').value = settings.ringsColor;
            document.getElementById('glass-opacity').value = settings.glassOpacity;
            document.getElementById('emissive-intensity').value = settings.emissiveIntensity;
            document.getElementById('rings-opacity').value = settings.ringsOpacity;
            document.getElementById('show-ring1').checked = settings.showRing1;
            document.getElementById('show-ring2').checked = settings.showRing2;

            // Update display values
            document.getElementById('glass-opacity-val').textContent = settings.glassOpacity + '%';
            document.getElementById('emissive-val').textContent = (settings.emissiveIntensity / 100).toFixed(2);
            document.getElementById('rings-opacity-val').textContent = settings.ringsOpacity + '%';

            // Rotation settings
            document.getElementById('enable-rotation').checked = settings.enableRotation || false;
            document.getElementById('rotation-speed').value = settings.rotationSpeed || 30;
            document.getElementById('rotation-speed-val').textContent = (settings.rotationSpeed || 30) + '%';
            document.getElementById('rotation-angle').value = settings.rotationAngle || 0;
            document.getElementById('rotation-angle-val').textContent = (settings.rotationAngle || 0) + '°';

            // Orbit settings
            document.getElementById('enable-orbit').checked = settings.enableOrbit || false;
            document.getElementById('orbit-speed').value = settings.orbitSpeed || 20;
            document.getElementById('orbit-speed-val').textContent = (settings.orbitSpeed || 20) + '%';
            document.getElementById('orbit-clockwise').checked = settings.orbitClockwise !== false;

            // Shape selector - highlight current shape
            const currentShape = settings.shapeType || 'sphere';
            document.querySelectorAll('.shape-option').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.shape === currentShape);
            });

            // Show modal
            document.getElementById('sphere-modal').classList.add('active');
            controls.enabled = false; // Disable camera controls
        };

        window.closeModal = function() {
            document.getElementById('sphere-modal').classList.remove('active');
            currentEditingEgiId = null;
            controls.enabled = true;
        };

        window.resetSphereSettings = function() {
            if (!currentEditingEgiId) return;

            document.getElementById('color-glass').value = defaultSettings.glassColor;
            document.getElementById('color-rings').value = defaultSettings.ringsColor;
            document.getElementById('glass-opacity').value = defaultSettings.glassOpacity;
            document.getElementById('emissive-intensity').value = defaultSettings.emissiveIntensity;
            document.getElementById('rings-opacity').value = defaultSettings.ringsOpacity;
            document.getElementById('show-ring1').checked = defaultSettings.showRing1;
            document.getElementById('show-ring2').checked = defaultSettings.showRing2;

            document.getElementById('glass-opacity-val').textContent = defaultSettings.glassOpacity + '%';
            document.getElementById('emissive-val').textContent = (defaultSettings.emissiveIntensity / 100).toFixed(2);
            document.getElementById('rings-opacity-val').textContent = defaultSettings.ringsOpacity + '%';

            // Rotation defaults
            document.getElementById('enable-rotation').checked = defaultSettings.enableRotation;
            document.getElementById('rotation-speed').value = defaultSettings.rotationSpeed;
            document.getElementById('rotation-speed-val').textContent = defaultSettings.rotationSpeed + '%';
            document.getElementById('rotation-angle').value = defaultSettings.rotationAngle;
            document.getElementById('rotation-angle-val').textContent = defaultSettings.rotationAngle + '°';

            // Orbit defaults
            document.getElementById('enable-orbit').checked = defaultSettings.enableOrbit;
            document.getElementById('orbit-speed').value = defaultSettings.orbitSpeed;
            document.getElementById('orbit-speed-val').textContent = defaultSettings.orbitSpeed + '%';
            document.getElementById('orbit-clockwise').checked = defaultSettings.orbitClockwise;

            applySettingsToSphere(currentEditingEgiId, defaultSettings);
        };

        window.applySphereSettings = function() {
            if (!currentEditingEgiId) return;

            const settings = {
                glassColor: document.getElementById('color-glass').value,
                ringsColor: document.getElementById('color-rings').value,
                glassOpacity: parseInt(document.getElementById('glass-opacity').value),
                emissiveIntensity: parseInt(document.getElementById('emissive-intensity').value),
                ringsOpacity: parseInt(document.getElementById('rings-opacity').value),
                showRing1: document.getElementById('show-ring1').checked,
                showRing2: document.getElementById('show-ring2').checked,
                enableRotation: document.getElementById('enable-rotation').checked,
                rotationSpeed: parseInt(document.getElementById('rotation-speed').value),
                rotationAngle: parseInt(document.getElementById('rotation-angle').value),
                enableOrbit: document.getElementById('enable-orbit').checked,
                orbitSpeed: parseInt(document.getElementById('orbit-speed').value),
                orbitClockwise: document.getElementById('orbit-clockwise').checked
            };

            // Save settings
            const data = selectedEgis.get(currentEditingEgiId);
            if (data) {
                data.settings = settings;
            }

            applySettingsToSphere(currentEditingEgiId, settings);
            closeModal();
        };

        function applySettingsToSphere(egiId, settings) {
            const data = selectedEgis.get(egiId);
            if (!data) return;

            const mesh = data.mesh;

            // Apply glass color and opacity
            mesh.traverse((child) => {
                if (child.isMesh && child.material) {
                    // Glass material (first mesh added)
                    if (child.material.type === 'MeshPhysicalMaterial') {
                        child.material.color.setStyle(settings.glassColor);
                        child.material.opacity = settings.glassOpacity / 100;
                        child.material.needsUpdate = true;
                    }
                }
            });

            // Apply rings settings
            if (mesh.userData.ring1) {
                mesh.userData.ring1.visible = settings.showRing1;
                mesh.userData.ring1.material.color.setStyle(settings.ringsColor);
                mesh.userData.ring1.material.opacity = settings.ringsOpacity / 100;
            }
            if (mesh.userData.ring2) {
                mesh.userData.ring2.visible = settings.showRing2;
                mesh.userData.ring2.material.color.setStyle(settings.ringsColor);
                mesh.userData.ring2.material.opacity = settings.ringsOpacity / 100;
            }

            // Apply emissive intensity
            if (mesh.userData.imageMesh && mesh.userData.imageMesh.material) {
                mesh.userData.imageMesh.material.emissiveIntensity = settings.emissiveIntensity / 100;
                mesh.userData.imageMesh.material.needsUpdate = true;
            }
        }

        // Live preview on slider change
        document.querySelectorAll('#sphere-modal input[type="range"]').forEach(slider => {
            slider.addEventListener('input', () => {
                document.getElementById('glass-opacity-val').textContent =
                    document.getElementById('glass-opacity').value + '%';
                document.getElementById('emissive-val').textContent =
                    (document.getElementById('emissive-intensity').value / 100).toFixed(2);
                document.getElementById('rings-opacity-val').textContent =
                    document.getElementById('rings-opacity').value + '%';
                document.getElementById('rotation-speed-val').textContent =
                    document.getElementById('rotation-speed').value + '%';
                document.getElementById('rotation-angle-val').textContent =
                    document.getElementById('rotation-angle').value + '°';
                document.getElementById('orbit-speed-val').textContent =
                    document.getElementById('orbit-speed').value + '%';

                // Live preview
                if (currentEditingEgiId) {
                    const data = selectedEgis.get(currentEditingEgiId);
                    if (data) {
                        data.settings = {
                            glassColor: document.getElementById('color-glass').value,
                            ringsColor: document.getElementById('color-rings').value,
                            glassOpacity: parseInt(document.getElementById('glass-opacity').value),
                            emissiveIntensity: parseInt(document.getElementById('emissive-intensity')
                                .value),
                            ringsOpacity: parseInt(document.getElementById('rings-opacity').value),
                            showRing1: document.getElementById('show-ring1').checked,
                            showRing2: document.getElementById('show-ring2').checked,
                            enableRotation: document.getElementById('enable-rotation').checked,
                            rotationSpeed: parseInt(document.getElementById('rotation-speed').value),
                            rotationAngle: parseInt(document.getElementById('rotation-angle').value),
                            enableOrbit: document.getElementById('enable-orbit').checked,
                            orbitSpeed: parseInt(document.getElementById('orbit-speed').value),
                            orbitClockwise: document.getElementById('orbit-clockwise').checked
                        };
                        applySettingsToSphere(currentEditingEgiId, data.settings);
                    }
                }
            });
        });

        // Live preview on color change
        document.querySelectorAll('#sphere-modal input[type="color"]').forEach(picker => {
            picker.addEventListener('input', () => {
                if (currentEditingEgiId) {
                    const data = selectedEgis.get(currentEditingEgiId);
                    if (data) {
                        data.settings = {
                            glassColor: document.getElementById('color-glass').value,
                            ringsColor: document.getElementById('color-rings').value,
                            glassOpacity: parseInt(document.getElementById('glass-opacity').value),
                            emissiveIntensity: parseInt(document.getElementById('emissive-intensity')
                                .value),
                            ringsOpacity: parseInt(document.getElementById('rings-opacity').value),
                            showRing1: document.getElementById('show-ring1').checked,
                            showRing2: document.getElementById('show-ring2').checked,
                            enableRotation: document.getElementById('enable-rotation').checked,
                            rotationSpeed: parseInt(document.getElementById('rotation-speed').value),
                            rotationAngle: parseInt(document.getElementById('rotation-angle').value),
                            enableOrbit: document.getElementById('enable-orbit').checked,
                            orbitSpeed: parseInt(document.getElementById('orbit-speed').value),
                            orbitClockwise: document.getElementById('orbit-clockwise').checked
                        };
                        applySettingsToSphere(currentEditingEgiId, data.settings);
                    }
                }
            });
        });

        // Live preview on toggle change
        document.querySelectorAll('#sphere-modal input[type="checkbox"]').forEach(toggle => {
            toggle.addEventListener('change', () => {
                if (currentEditingEgiId) {
                    const data = selectedEgis.get(currentEditingEgiId);
                    if (data) {
                        data.settings = {
                            glassColor: document.getElementById('color-glass').value,
                            ringsColor: document.getElementById('color-rings').value,
                            glassOpacity: parseInt(document.getElementById('glass-opacity').value),
                            emissiveIntensity: parseInt(document.getElementById('emissive-intensity')
                                .value),
                            ringsOpacity: parseInt(document.getElementById('rings-opacity').value),
                            showRing1: document.getElementById('show-ring1').checked,
                            showRing2: document.getElementById('show-ring2').checked,
                            enableRotation: document.getElementById('enable-rotation').checked,
                            rotationSpeed: parseInt(document.getElementById('rotation-speed').value),
                            rotationAngle: parseInt(document.getElementById('rotation-angle').value),
                            enableOrbit: document.getElementById('enable-orbit').checked,
                            orbitSpeed: parseInt(document.getElementById('orbit-speed').value),
                            orbitClockwise: document.getElementById('orbit-clockwise').checked
                        };
                        applySettingsToSphere(currentEditingEgiId, data.settings);
                    }
                }
            });
        });

        // Close modal on outside click
        document.getElementById('sphere-modal').addEventListener('click', (e) => {
            if (e.target.id === 'sphere-modal') {
                closeModal();
            }
        });

        // Close modal on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.getElementById('sphere-modal').classList.contains('active')) {
                closeModal();
            }
        });

        // ===========================================
        // RESHAPE ELEMENT
        // ===========================================
        function reshapeElement(egiId, newShapeType) {
            const data = selectedEgis.get(egiId);
            if (!data) return;

            const oldMesh = data.mesh;
            const position = oldMesh.position.clone();

            // Remove old mesh
            scene.remove(oldMesh);
            oldMesh.traverse((child) => {
                if (child.geometry) child.geometry.dispose();
                if (child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach(m => m.dispose());
                    } else {
                        child.material.dispose();
                    }
                }
            });

            // Create new shape
            const newMesh = createShape(data.thumbUrl, position, 35, newShapeType);
            scene.add(newMesh);

            // Update data
            data.mesh = newMesh;
            data.settings.shapeType = newShapeType;

            // Apply current settings to new mesh (after texture loads)
            setTimeout(() => {
                applySettingsToSphere(egiId, data.settings);
            }, 100);
        }

        // Shape button click handlers
        document.querySelectorAll('.shape-option').forEach(btn => {
            btn.addEventListener('click', () => {
                const newShape = btn.dataset.shape;

                // Update UI
                document.querySelectorAll('.shape-option').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Apply shape change
                if (currentEditingEgiId) {
                    const data = selectedEgis.get(currentEditingEgiId);
                    if (data && data.settings.shapeType !== newShape) {
                        data.settings.shapeType = newShape;
                        reshapeElement(currentEditingEgiId, newShape);
                    }
                }
            });
        });
    </script>
</body>

</html>
