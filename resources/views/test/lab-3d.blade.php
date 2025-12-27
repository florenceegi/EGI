<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Laboratory | Florence EGI</title>
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
            --glass-light: rgba(20, 30, 40, 0.85);
            --border-light: rgba(255, 255, 255, 0.1);
            --border-active: rgba(0, 255, 221, 0.4);
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

        /* Canvas container */
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
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 6px;
            color: var(--primary);
            margin-bottom: 20px;
            font-family: 'Share Tech Mono';
        }

        .bar-c {
            width: 250px;
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

        /* Top bar */
        #top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: var(--glass-dark);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        #top-bar h1 {
            font-family: 'Share Tech Mono';
            font-size: 16px;
            letter-spacing: 3px;
            color: white;
        }

        #top-bar h1 span {
            color: var(--primary);
        }

        #top-bar .status {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Left sidebar - Controls */
        #sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            bottom: 0;
            width: 320px;
            background: var(--glass-dark);
            border-right: 1px solid var(--border-light);
            z-index: 100;
            overflow-y: auto;
            backdrop-filter: blur(10px);
        }

        .panel {
            border-bottom: 1px solid var(--border-light);
            padding: 15px;
        }

        .panel-header {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-header i {
            font-size: 14px;
        }

        /* Buttons grid */
        .btn-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .lab-btn {
            padding: 12px 10px;
            font-family: 'Share Tech Mono';
            font-size: 10px;
            letter-spacing: 0.5px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 6px;
            text-align: center;
        }

        .lab-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0, 255, 221, 0.05);
        }

        .lab-btn.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        .lab-btn .icon {
            font-size: 20px;
            display: block;
            margin-bottom: 5px;
        }

        /* Slider control */
        .slider-control {
            margin-bottom: 12px;
        }

        .slider-control label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .slider-control .value {
            font-family: 'Share Tech Mono';
            color: white;
        }

        .slider-control input[type="range"] {
            width: 100%;
            height: 4px;
            -webkit-appearance: none;
            background: var(--border-light);
            border-radius: 2px;
            outline: none;
        }

        .slider-control input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
        }

        /* Color picker */
        .color-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .color-control label {
            font-size: 12px;
            color: var(--text-muted);
            min-width: 80px;
        }

        .color-control input[type="color"] {
            width: 40px;
            height: 28px;
            border: 1px solid var(--border-light);
            border-radius: 4px;
            background: transparent;
            cursor: pointer;
        }

        /* Toggle switch */
        .toggle-control {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .toggle-control label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 22px;
        }

        .toggle-switch input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .toggle-switch .slider {
            position: absolute;
            inset: 0;
            background: var(--border-light);
            border-radius: 11px;
            transition: 0.3s;
        }

        .toggle-switch .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
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
            transform: translateX(18px);
        }

        /* Console output */
        #console {
            position: fixed;
            bottom: 0;
            left: 320px;
            right: 0;
            height: 120px;
            background: rgba(0, 0, 0, 0.9);
            border-top: 1px solid var(--border-light);
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--text-muted);
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        #console-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 15px;
            border-bottom: 1px solid var(--border-light);
            background: rgba(20, 30, 40, 0.5);
        }

        #console-header span {
            color: var(--primary);
            letter-spacing: 2px;
        }

        #console-output {
            flex: 1;
            overflow-y: auto;
            padding: 10px 15px;
        }

        .log-entry {
            margin-bottom: 4px;
        }

        .log-entry .time {
            color: #555;
            margin-right: 10px;
        }

        .log-entry.info {
            color: var(--primary);
        }

        .log-entry.warn {
            color: var(--gold);
        }

        .log-entry.error {
            color: var(--danger);
        }

        /* Info panel */
        #info-panel {
            position: fixed;
            top: 60px;
            right: 20px;
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 15px 20px;
            z-index: 100;
            min-width: 200px;
        }

        #info-panel h3 {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: var(--primary);
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        #info-panel .stat {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }

        #info-panel .stat .label {
            color: var(--text-muted);
        }

        #info-panel .stat .value {
            font-family: 'Share Tech Mono';
            color: white;
        }

        /* Full width btn */
        .btn-full {
            width: 100%;
            padding: 12px;
            font-family: 'Share Tech Mono';
            font-size: 11px;
            letter-spacing: 1px;
            border: 1px solid var(--primary);
            background: transparent;
            color: var(--primary);
            cursor: pointer;
            border-radius: 6px;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-full:hover {
            background: var(--primary);
            color: #000;
        }

        /* Code snippet */
        .code-snippet {
            background: #111;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 10px;
            font-family: 'Share Tech Mono';
            font-size: 10px;
            color: var(--text-muted);
            overflow-x: auto;
            white-space: pre-wrap;
            margin-top: 10px;
        }

        /* Object Settings Modal */
        #object-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 500;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        #object-modal.active {
            display: flex;
        }

        #object-modal .modal-content {
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            width: 360px;
            max-height: 80vh;
            overflow-y: auto;
        }

        #object-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-light);
            background: rgba(0, 255, 221, 0.05);
        }

        #object-modal .modal-header h2 {
            font-family: 'Share Tech Mono';
            font-size: 14px;
            letter-spacing: 2px;
            color: var(--primary);
        }

        #object-modal .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
        }

        #object-modal .modal-body {
            padding: 20px;
        }

        #object-modal .setting-group {
            margin-bottom: 18px;
        }

        #object-modal .setting-label {
            font-family: 'Share Tech Mono';
            font-size: 11px;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 10px;
            display: block;
        }

        #object-modal .color-row {
            display: flex;
            gap: 15px;
        }

        #object-modal .color-picker-wrap {
            flex: 1;
            text-align: center;
        }

        #object-modal .color-picker-wrap label {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        #object-modal .color-picker-wrap input[type="color"] {
            width: 50px;
            height: 35px;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            cursor: pointer;
            background: transparent;
        }

        #object-modal .slider-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #object-modal .slider-wrap label {
            min-width: 80px;
            font-size: 12px;
            color: var(--text-muted);
        }

        #object-modal .slider-wrap input[type="range"] {
            flex: 1;
            height: 4px;
            -webkit-appearance: none;
            background: var(--border-light);
            border-radius: 2px;
        }

        #object-modal .slider-wrap input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
        }

        #object-modal .slider-wrap .value {
            min-width: 45px;
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: white;
            text-align: right;
        }

        #object-modal .modal-footer {
            display: flex;
            gap: 10px;
            padding: 15px 20px;
            border-top: 1px solid var(--border-light);
        }

        #object-modal .modal-footer .btn {
            flex: 1;
            padding: 10px;
            font-family: 'Share Tech Mono';
            font-size: 11px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        #object-modal .modal-footer .btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        #object-modal .modal-footer .btn-confirm {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }
    </style>

    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
            }
        }
    </script>
</head>

<body>
    <div id="loader">
        <div class="loader-t">3D LABORATORY</div>
        <div class="bar-c">
            <div class="bar-f"></div>
        </div>
    </div>

    <div id="top-bar">
        <h1><i class="fa-solid fa-flask"></i> 3D <span>LABORATORY</span></h1>
        <div class="status" id="fps-counter">FPS: --</div>
    </div>

    <div id="sidebar">
        <!-- Primitive Shapes -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-shapes"></i> PRIMITIVE</div>
            <div class="btn-grid">
                <button class="lab-btn" onclick="spawnShape('sphere')">
                    <span class="icon">🔮</span>SFERA
                </button>
                <button class="lab-btn" onclick="spawnShape('box')">
                    <span class="icon">📦</span>CUBO
                </button>
                <button class="lab-btn" onclick="spawnShape('cone')">
                    <span class="icon">⛰️</span>CONO
                </button>
                <button class="lab-btn" onclick="spawnShape('torus')">
                    <span class="icon">🍩</span>TORUS
                </button>
                <button class="lab-btn" onclick="spawnShape('cylinder')">
                    <span class="icon">🥫</span>CILINDRO
                </button>
                <button class="lab-btn" onclick="spawnShape('icosa')">
                    <span class="icon">💎</span>ICOSAED
                </button>
            </div>
        </div>

        <!-- Custom Shapes -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-wand-magic-sparkles"></i> CUSTOM</div>
            <div class="btn-grid">
                <button class="lab-btn" onclick="spawnShape('spaceship')">
                    <span class="icon">🚀</span>NAVE
                </button>
                <button class="lab-btn" onclick="spawnShape('star')">
                    <span class="icon">⭐</span>STELLA
                </button>
                <button class="lab-btn" onclick="spawnShape('heart')">
                    <span class="icon">❤️</span>CUORE
                </button>
                <button class="lab-btn" onclick="spawnShape('ring')">
                    <span class="icon">💍</span>ANELLO
                </button>
            </div>
        </div>

        <!-- Model Loader -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-file-import"></i> IMPORT MODELLO</div>
            <input type="file" id="model-input" accept=".glb,.gltf" style="display: none;">
            <button class="btn-full" onclick="document.getElementById('model-input').click()">
                <i class="fa-solid fa-upload"></i> CARICA .GLB/.GLTF
            </button>
            <div class="code-snippet" id="model-status">Nessun modello caricato</div>
        </div>

        <!-- Material Settings -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-palette"></i> MATERIALE</div>

            <div class="color-control">
                <label>Colore</label>
                <input type="color" id="mat-color" value="#00ffdd">
            </div>

            <div class="slider-control">
                <label>Metalness <span class="value" id="metal-val">0.1</span></label>
                <input type="range" id="mat-metal" min="0" max="100" value="10">
            </div>

            <div class="slider-control">
                <label>Roughness <span class="value" id="rough-val">0.2</span></label>
                <input type="range" id="mat-rough" min="0" max="100" value="20">
            </div>

            <div class="slider-control">
                <label>Opacity <span class="value" id="opacity-val">0.3</span></label>
                <input type="range" id="mat-opacity" min="0" max="100" value="30">
            </div>

            <div class="toggle-control">
                <label>Wireframe</label>
                <div class="toggle-switch">
                    <input type="checkbox" id="mat-wireframe">
                    <span class="slider"></span>
                </div>
            </div>
        </div>

        <!-- Scene Controls -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-sun"></i> SCENA</div>

            <div class="slider-control">
                <label>Bloom Intensity <span class="value" id="bloom-val">0.4</span></label>
                <input type="range" id="bloom-intensity" min="0" max="100" value="40">
            </div>

            <div class="toggle-control">
                <label>Mostra griglia</label>
                <div class="toggle-switch">
                    <input type="checkbox" id="show-grid" checked>
                    <span class="slider"></span>
                </div>
            </div>

            <div class="toggle-control">
                <label>Auto-rotazione</label>
                <div class="toggle-switch">
                    <input type="checkbox" id="auto-rotate">
                    <span class="slider"></span>
                </div>
            </div>

            <button class="btn-full" onclick="clearScene()">
                <i class="fa-solid fa-trash"></i> PULISCI SCENA
            </button>
        </div>

        <!-- Transform Controls -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-arrows-up-down-left-right"></i> TRASFORMA</div>
            <div class="btn-grid">
                <button class="lab-btn" onclick="setTransformMode('translate')">
                    <span class="icon">↔️</span>SPOSTA (T)
                </button>
                <button class="lab-btn" onclick="setTransformMode('rotate')">
                    <span class="icon">🔄</span>RUOTA (R)
                </button>
                <button class="lab-btn" onclick="setTransformMode('scale')">
                    <span class="icon">📐</span>SCALA (S)
                </button>
                <button class="lab-btn" onclick="openObjectModal()">
                    <span class="icon">🎨</span>COLORI
                </button>
                <button class="lab-btn" onclick="deleteSelected()">
                    <span class="icon">🗑️</span>ELIMINA
                </button>
            </div>
            <div style="margin-top: 12px;">
                <div class="panel-header" style="margin-bottom: 8px;"><i class="fa-solid fa-rotate"></i> ROTAZIONE
                    RAPIDA</div>
                <div class="btn-grid">
                    <button class="lab-btn" onclick="quickRotate('x', 90)">
                        <span class="icon">⬆️</span>+90° X
                    </button>
                    <button class="lab-btn" onclick="quickRotate('x', -90)">
                        <span class="icon">⬇️</span>-90° X
                    </button>
                    <button class="lab-btn" onclick="quickRotate('z', 90)">
                        <span class="icon">↩️</span>+90° Z
                    </button>
                    <button class="lab-btn" onclick="quickRotate('z', -90)">
                        <span class="icon">↪️</span>-90° Z
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="info-panel">
        <h3>STATISTICHE</h3>
        <div class="stat">
            <span class="label">Oggetti</span>
            <span class="value" id="obj-count">0</span>
        </div>
        <div class="stat">
            <span class="label">Triangoli</span>
            <span class="value" id="tri-count">0</span>
        </div>
        <div class="stat">
            <span class="label">Draw calls</span>
            <span class="value" id="draw-count">0</span>
        </div>
    </div>

    <div id="console">
        <div id="console-header">
            <span><i class="fa-solid fa-terminal"></i> CONSOLE</span>
            <button onclick="clearConsole()"
                style="background:none;border:none;color:var(--text-muted);cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div id="console-output"></div>
    </div>

    <!-- Object Settings Modal -->
    <div id="object-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-palette"></i> MATERIALE OGGETTO</h2>
                <button class="modal-close" onclick="closeObjectModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="setting-group">
                    <span class="setting-label">Colore</span>
                    <div class="color-row">
                        <div class="color-picker-wrap">
                            <label>Colore Base</label>
                            <input type="color" id="obj-color" value="#00ffdd">
                        </div>
                        <div class="color-picker-wrap">
                            <label>Emissivo</label>
                            <input type="color" id="obj-emissive" value="#000000">
                        </div>
                    </div>
                </div>

                <div class="setting-group">
                    <span class="setting-label">Proprietà Materiale</span>
                    <div class="slider-wrap">
                        <label>Metalness</label>
                        <input type="range" id="obj-metalness" min="0" max="100" value="10">
                        <span class="value" id="obj-metalness-val">0.10</span>
                    </div>
                    <div class="slider-wrap" style="margin-top: 8px;">
                        <label>Roughness</label>
                        <input type="range" id="obj-roughness" min="0" max="100" value="20">
                        <span class="value" id="obj-roughness-val">0.20</span>
                    </div>
                    <div class="slider-wrap" style="margin-top: 8px;">
                        <label>Opacità</label>
                        <input type="range" id="obj-opacity" min="0" max="100" value="100">
                        <span class="value" id="obj-opacity-val">1.00</span>
                    </div>
                    <div class="slider-wrap" style="margin-top: 8px;">
                        <label>Emissive Int.</label>
                        <input type="range" id="obj-emissive-int" min="0" max="100" value="0">
                        <span class="value" id="obj-emissive-int-val">0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeObjectModal()">CHIUDI</button>
                <button class="btn-confirm btn" onclick="applyObjectMaterial()">APPLICA</button>
            </div>
        </div>
    </div>

    <div id="canvas-container"></div>

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
        import {
            GLTFLoader
        } from 'three/addons/loaders/GLTFLoader.js';
        import {
            TransformControls
        } from 'three/addons/controls/TransformControls.js';

        // =====================================================
        // CONSOLE LOGGING
        // =====================================================
        function log(message, type = 'info') {
            const output = document.getElementById('console-output');
            const time = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.innerHTML = `<span class="time">${time}</span>${message}`;
            output.appendChild(entry);
            output.scrollTop = output.scrollHeight;
        }

        window.clearConsole = function() {
            document.getElementById('console-output').innerHTML = '';
        };

        log('🚀 Laboratory initialized');
        log('Three.js r' + THREE.REVISION + ' loaded');

        // =====================================================
        // SCENE SETUP
        // =====================================================
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(80, 60, 120);

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
        scene.background = new THREE.Color(0x050810);

        // Post-processing
        const composer = new EffectComposer(renderer);
        composer.addPass(new RenderPass(scene, camera));

        const bloomPass = new UnrealBloomPass(
            new THREE.Vector2(window.innerWidth, window.innerHeight),
            1.5, 0.4, 0.85
        );
        bloomPass.threshold = 0.8;
        bloomPass.strength = 0.4;
        bloomPass.radius = 0.3;
        composer.addPass(bloomPass);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;

        // Grid
        const gridHelper = new THREE.GridHelper(200, 40, 0x444444, 0x222222);
        scene.add(gridHelper);

        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.3);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1);
        dirLight.position.set(50, 100, 50);
        scene.add(dirLight);

        log('Scene created with post-processing');

        // =====================================================
        // TRANSFORM CONTROLS (for moving objects)
        // =====================================================
        const transformControls = new TransformControls(camera, renderer.domElement);
        transformControls.setMode('translate');
        scene.add(transformControls);

        // Disable orbit controls while transforming
        transformControls.addEventListener('dragging-changed', (event) => {
            controls.enabled = !event.value;
        });

        // Raycaster for selection
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();
        let selectedObject = null;

        function onCanvasClick(event) {
            // Ignore clicks on UI
            if (event.target !== renderer.domElement) return;

            // Calculate mouse position
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);

            // Check intersections with lab objects
            const intersects = raycaster.intersectObjects(labObjects, true);

            if (intersects.length > 0) {
                // Find the root lab object
                let target = intersects[0].object;
                while (target.parent && !labObjects.includes(target)) {
                    target = target.parent;
                }
                if (labObjects.includes(target)) {
                    selectObject(target);
                }
            } else {
                deselectObject();
            }
        }

        function selectObject(obj) {
            if (selectedObject === obj) return;

            selectedObject = obj;
            transformControls.attach(obj);
            log(`🎯 Selected object`, 'info');
        }

        function deselectObject() {
            if (!selectedObject) return;

            selectedObject = null;
            transformControls.detach();
            log(`📤 Deselected`, 'info');
        }

        renderer.domElement.addEventListener('click', onCanvasClick);

        // Keyboard shortcuts for transform modes
        document.addEventListener('keydown', (e) => {
            switch (e.key.toLowerCase()) {
                case 't':
                    transformControls.setMode('translate');
                    log('Mode: TRANSLATE (T)', 'info');
                    break;
                case 'r':
                    transformControls.setMode('rotate');
                    log('Mode: ROTATE (R)', 'info');
                    break;
                case 's':
                    if (!e.ctrlKey && !e.metaKey) {
                        transformControls.setMode('scale');
                        log('Mode: SCALE (S)', 'info');
                    }
                    break;
                case 'delete':
                case 'backspace':
                    if (selectedObject && e.target.tagName !== 'INPUT') {
                        e.preventDefault();
                        deleteSelectedObject();
                    }
                    break;
                case 'escape':
                    deselectObject();
                    break;
            }
        });

        function deleteSelectedObject() {
            if (!selectedObject) return;

            const index = labObjects.indexOf(selectedObject);
            if (index > -1) {
                labObjects.splice(index, 1);
            }

            scene.remove(selectedObject);
            if (selectedObject.geometry) selectedObject.geometry.dispose();
            if (selectedObject.material) selectedObject.material.dispose();

            transformControls.detach();
            log('🗑️ Deleted selected object', 'warn');
            selectedObject = null;
            updateStats();
        }

        // Expose functions to window for button clicks
        window.setTransformMode = function(mode) {
            transformControls.setMode(mode);
            log(`Mode: ${mode.toUpperCase()}`, 'info');
        };

        window.deleteSelected = function() {
            deleteSelectedObject();
        };

        window.quickRotate = function(axis, degrees) {
            if (!selectedObject) {
                log('⚠️ Nessun oggetto selezionato', 'warn');
                return;
            }

            const radians = degrees * (Math.PI / 180);

            switch (axis) {
                case 'x':
                    selectedObject.rotation.x += radians;
                    break;
                case 'y':
                    selectedObject.rotation.y += radians;
                    break;
                case 'z':
                    selectedObject.rotation.z += radians;
                    break;
            }

            log(`🔄 Rotated ${degrees}° on ${axis.toUpperCase()} axis`, 'info');
        };

        // =====================================================
        // OBJECT MATERIAL MODAL
        // =====================================================
        window.openObjectModal = function() {
            if (!selectedObject) {
                log('⚠️ Nessun oggetto selezionato', 'warn');
                return;
            }

            // Read current material properties
            const mat = selectedObject.material;
            if (mat) {
                document.getElementById('obj-color').value = '#' + mat.color.getHexString();
                document.getElementById('obj-emissive').value = mat.emissive ? '#' + mat.emissive.getHexString() :
                    '#000000';
                document.getElementById('obj-metalness').value = (mat.metalness || 0) * 100;
                document.getElementById('obj-roughness').value = (mat.roughness || 0) * 100;
                document.getElementById('obj-opacity').value = (mat.opacity || 1) * 100;
                document.getElementById('obj-emissive-int').value = (mat.emissiveIntensity || 0) * 100;

                // Update display values
                document.getElementById('obj-metalness-val').textContent = (mat.metalness || 0).toFixed(2);
                document.getElementById('obj-roughness-val').textContent = (mat.roughness || 0).toFixed(2);
                document.getElementById('obj-opacity-val').textContent = (mat.opacity || 1).toFixed(2);
                document.getElementById('obj-emissive-int-val').textContent = (mat.emissiveIntensity || 0).toFixed(2);
            }

            document.getElementById('object-modal').classList.add('active');
            controls.enabled = false;
            log('🎨 Material editor opened', 'info');
        };

        window.closeObjectModal = function() {
            document.getElementById('object-modal').classList.remove('active');
            controls.enabled = true;
        };

        window.applyObjectMaterial = function() {
            if (!selectedObject) return;

            const color = document.getElementById('obj-color').value;
            const emissive = document.getElementById('obj-emissive').value;
            const metalness = parseInt(document.getElementById('obj-metalness').value) / 100;
            const roughness = parseInt(document.getElementById('obj-roughness').value) / 100;
            const opacity = parseInt(document.getElementById('obj-opacity').value) / 100;
            const emissiveIntensity = parseInt(document.getElementById('obj-emissive-int').value) / 100;

            const newMat = new THREE.MeshPhysicalMaterial({
                color: color,
                emissive: emissive,
                emissiveIntensity: emissiveIntensity,
                metalness: metalness,
                roughness: roughness,
                transparent: true,
                opacity: opacity,
                envMapIntensity: 1.0,
                clearcoat: 0.5,
                side: THREE.DoubleSide
            });

            // Dispose old material
            if (selectedObject.material) {
                selectedObject.material.dispose();
            }
            selectedObject.material = newMat;

            closeObjectModal();
            log('✅ Material applied', 'info');
        };

        // Live preview on slider change
        document.querySelectorAll('#object-modal input[type="range"]').forEach(slider => {
            slider.addEventListener('input', (e) => {
                const id = e.target.id;
                const val = parseInt(e.target.value) / 100;
                document.getElementById(id + '-val').textContent = val.toFixed(2);

                // Live update material
                if (selectedObject && selectedObject.material) {
                    const mat = selectedObject.material;
                    switch (id) {
                        case 'obj-metalness':
                            mat.metalness = val;
                            break;
                        case 'obj-roughness':
                            mat.roughness = val;
                            break;
                        case 'obj-opacity':
                            mat.opacity = val;
                            break;
                        case 'obj-emissive-int':
                            mat.emissiveIntensity = val;
                            break;
                    }
                }
            });
        });

        // Live preview on color change
        document.querySelectorAll('#object-modal input[type="color"]').forEach(picker => {
            picker.addEventListener('input', (e) => {
                if (selectedObject && selectedObject.material) {
                    const mat = selectedObject.material;
                    if (e.target.id === 'obj-color') {
                        mat.color.set(e.target.value);
                    } else if (e.target.id === 'obj-emissive') {
                        mat.emissive.set(e.target.value);
                    }
                }
            });
        });

        // Double-click to open modal
        renderer.domElement.addEventListener('dblclick', (e) => {
            if (selectedObject) {
                openObjectModal();
            }
        });

        // Close modal on outside click
        document.getElementById('object-modal').addEventListener('click', (e) => {
            if (e.target.id === 'object-modal') {
                closeObjectModal();
            }
        });

        log('🛠️ Controls: T=Translate, R=Rotate, S=Scale, DEL=Delete, DBLCLICK=Colors');

        // =====================================================
        // OBJECT MANAGEMENT
        // =====================================================
        const labObjects = [];

        function getCurrentMaterial() {
            const color = document.getElementById('mat-color').value;
            const metalness = parseInt(document.getElementById('mat-metal').value) / 100;
            const roughness = parseInt(document.getElementById('mat-rough').value) / 100;
            const opacity = parseInt(document.getElementById('mat-opacity').value) / 100;
            const wireframe = document.getElementById('mat-wireframe').checked;

            return new THREE.MeshPhysicalMaterial({
                color: color,
                metalness: metalness,
                roughness: roughness,
                transparent: true,
                opacity: opacity,
                envMapIntensity: 1.0,
                clearcoat: 0.5,
                wireframe: wireframe,
                side: THREE.DoubleSide
            });
        }

        function getGeometry(type) {
            switch (type) {
                case 'sphere':
                    return new THREE.SphereGeometry(20, 64, 32);
                case 'box':
                    return new THREE.BoxGeometry(30, 30, 30);
                case 'cone':
                    return new THREE.ConeGeometry(15, 40, 32);
                case 'torus':
                    return new THREE.TorusGeometry(18, 6, 32, 100);
                case 'cylinder':
                    return new THREE.CylinderGeometry(12, 12, 35, 32);
                case 'icosa':
                    return new THREE.IcosahedronGeometry(20, 0);
                case 'spaceship': {
                    const points = [];
                    const s = 20;
                    points.push(new THREE.Vector2(0, s * 1.5));
                    points.push(new THREE.Vector2(s * 0.15, s * 1.2));
                    points.push(new THREE.Vector2(s * 0.25, s * 0.9));
                    points.push(new THREE.Vector2(s * 0.4, s * 0.6));
                    points.push(new THREE.Vector2(s * 0.5, s * 0.3));
                    points.push(new THREE.Vector2(s * 0.55, s * 0));
                    points.push(new THREE.Vector2(s * 0.5, s * -0.4));
                    points.push(new THREE.Vector2(s * 0.6, s * -0.8));
                    points.push(new THREE.Vector2(s * 0.7, s * -1.0));
                    points.push(new THREE.Vector2(s * 0.5, s * -1.1));
                    points.push(new THREE.Vector2(s * 0.3, s * -1.2));
                    points.push(new THREE.Vector2(0, s * -1.2));
                    return new THREE.LatheGeometry(points, 32);
                }
                case 'star': {
                    const shape = new THREE.Shape();
                    const outerRadius = 25;
                    const innerRadius = 10;
                    const spikes = 5;
                    for (let i = 0; i < spikes * 2; i++) {
                        const radius = i % 2 === 0 ? outerRadius : innerRadius;
                        const angle = (i / (spikes * 2)) * Math.PI * 2 - Math.PI / 2;
                        const x = Math.cos(angle) * radius;
                        const y = Math.sin(angle) * radius;
                        if (i === 0) shape.moveTo(x, y);
                        else shape.lineTo(x, y);
                    }
                    shape.closePath();
                    return new THREE.ExtrudeGeometry(shape, {
                        depth: 8,
                        bevelEnabled: false
                    });
                }
                case 'heart': {
                    const shape = new THREE.Shape();
                    const x = 0,
                        y = 0;
                    shape.moveTo(x + 5, y + 5);
                    shape.bezierCurveTo(x + 5, y + 5, x + 4, y, x, y);
                    shape.bezierCurveTo(x - 6, y, x - 6, y + 7, x - 6, y + 7);
                    shape.bezierCurveTo(x - 6, y + 11, x - 3, y + 15.4, x + 5, y + 19);
                    shape.bezierCurveTo(x + 12, y + 15.4, x + 16, y + 11, x + 16, y + 7);
                    shape.bezierCurveTo(x + 16, y + 7, x + 16, y, x + 10, y);
                    shape.bezierCurveTo(x + 7, y, x + 5, y + 5, x + 5, y + 5);
                    const geometry = new THREE.ExtrudeGeometry(shape, {
                        depth: 4,
                        bevelEnabled: true,
                        bevelThickness: 1,
                        bevelSize: 1
                    });
                    geometry.center();
                    geometry.scale(1.5, 1.5, 1.5);
                    return geometry;
                }
                case 'ring': {
                    return new THREE.TorusGeometry(22, 4, 16, 100);
                }
                default:
                    return new THREE.SphereGeometry(20, 64, 32);
            }
        }

        window.spawnShape = function(type) {
            const geometry = getGeometry(type);
            const material = getCurrentMaterial();
            const mesh = new THREE.Mesh(geometry, material);

            // Random position
            mesh.position.set(
                (Math.random() - 0.5) * 80,
                20 + Math.random() * 30,
                (Math.random() - 0.5) * 80
            );
            mesh.rotation.set(
                Math.random() * Math.PI,
                Math.random() * Math.PI,
                0
            );

            mesh.userData.labObject = true;
            scene.add(mesh);
            labObjects.push(mesh);

            log(`✨ Spawned ${type.toUpperCase()}`, 'info');
            updateStats();
        };

        window.clearScene = function() {
            labObjects.forEach(obj => {
                scene.remove(obj);
                if (obj.geometry) obj.geometry.dispose();
                if (obj.material) obj.material.dispose();
            });
            labObjects.length = 0;
            log('🗑️ Scene cleared', 'warn');
            updateStats();
        };

        // =====================================================
        // MODEL LOADER
        // =====================================================
        const gltfLoader = new GLTFLoader();

        document.getElementById('model-input').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            log(`📂 Loading model: ${file.name}`, 'info');
            document.getElementById('model-status').textContent = `Caricamento: ${file.name}...`;

            gltfLoader.load(url,
                (gltf) => {
                    const model = gltf.scene;

                    // Center and scale
                    const box = new THREE.Box3().setFromObject(model);
                    const size = box.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const scale = 50 / maxDim;
                    model.scale.setScalar(scale);

                    const center = box.getCenter(new THREE.Vector3());
                    model.position.sub(center.multiplyScalar(scale));
                    model.position.y = 25;

                    model.userData.labObject = true;
                    scene.add(model);
                    labObjects.push(model);

                    log(`✅ Model loaded: ${file.name}`, 'info');
                    document.getElementById('model-status').textContent = `Caricato: ${file.name}`;
                    updateStats();
                    URL.revokeObjectURL(url);
                },
                (progress) => {
                    const percent = (progress.loaded / progress.total * 100).toFixed(0);
                    document.getElementById('model-status').textContent = `Caricamento: ${percent}%`;
                },
                (error) => {
                    log(`❌ Error loading model: ${error.message}`, 'error');
                    document.getElementById('model-status').textContent = 'Errore caricamento';
                }
            );
        });

        // =====================================================
        // UI BINDINGS
        // =====================================================
        document.getElementById('mat-metal').addEventListener('input', (e) => {
            document.getElementById('metal-val').textContent = (e.target.value / 100).toFixed(2);
        });
        document.getElementById('mat-rough').addEventListener('input', (e) => {
            document.getElementById('rough-val').textContent = (e.target.value / 100).toFixed(2);
        });
        document.getElementById('mat-opacity').addEventListener('input', (e) => {
            document.getElementById('opacity-val').textContent = (e.target.value / 100).toFixed(2);
        });
        document.getElementById('bloom-intensity').addEventListener('input', (e) => {
            const val = e.target.value / 100;
            document.getElementById('bloom-val').textContent = val.toFixed(2);
            bloomPass.strength = val;
        });
        document.getElementById('show-grid').addEventListener('change', (e) => {
            gridHelper.visible = e.target.checked;
        });
        document.getElementById('auto-rotate').addEventListener('change', (e) => {
            controls.autoRotate = e.target.checked;
        });

        // =====================================================
        // STATS
        // =====================================================
        function updateStats() {
            document.getElementById('obj-count').textContent = labObjects.length;

            let triangles = 0;
            labObjects.forEach(obj => {
                obj.traverse((child) => {
                    if (child.isMesh && child.geometry) {
                        const geo = child.geometry;
                        if (geo.index) {
                            triangles += geo.index.count / 3;
                        } else if (geo.attributes.position) {
                            triangles += geo.attributes.position.count / 3;
                        }
                    }
                });
            });
            document.getElementById('tri-count').textContent = Math.round(triangles).toLocaleString();
        }

        // =====================================================
        // ANIMATION
        // =====================================================
        const clock = new THREE.Clock();
        let frameCount = 0;
        let lastFpsUpdate = 0;

        function animate() {
            requestAnimationFrame(animate);

            const time = clock.getElapsedTime();
            controls.update();

            // FPS counter
            frameCount++;
            if (time - lastFpsUpdate > 0.5) {
                document.getElementById('fps-counter').textContent =
                    `FPS: ${Math.round(frameCount / (time - lastFpsUpdate))}`;
                document.getElementById('draw-count').textContent = renderer.info.render.calls;
                frameCount = 0;
                lastFpsUpdate = time;
            }

            composer.render();
        }

        animate();
        log('🎬 Animation loop started');

        // =====================================================
        // RESIZE
        // =====================================================
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            composer.setSize(window.innerWidth, window.innerHeight);
        });

        // =====================================================
        // HIDE LOADER
        // =====================================================
        window.onload = () => {
            setTimeout(() => document.getElementById('loader').style.opacity = 0, 800);
            setTimeout(() => document.getElementById('loader').remove(), 1300);
        };
    </script>
</body>

</html>
