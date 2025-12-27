<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polyhedron Composer | Florence EGI</title>
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
            top: 50px;
            left: 350px;
            right: 0;
            bottom: 0;
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
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 4px;
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

        /* Left sidebar */
        #sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            bottom: 0;
            width: 350px;
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

        /* Shape selector */
        .shape-selector {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .shape-btn {
            padding: 12px 8px;
            font-family: 'Share Tech Mono';
            font-size: 10px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 8px;
            text-align: center;
        }

        .shape-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .shape-btn.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        .shape-btn .faces {
            display: block;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Face grid */
        .face-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .face-slot {
            aspect-ratio: 1;
            border: 2px dashed var(--border-light);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.3);
        }

        .face-slot:hover {
            border-color: var(--primary);
            background: rgba(0, 255, 221, 0.05);
        }

        .face-slot.active {
            border-color: var(--gold);
            border-style: solid;
        }

        .face-slot.filled {
            border-style: solid;
            border-color: var(--primary);
        }

        .face-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            inset: 0;
        }

        .face-slot .face-label {
            font-family: 'Share Tech Mono';
            font-size: 10px;
            color: var(--text-muted);
            z-index: 1;
            background: rgba(0, 0, 0, 0.7);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .face-slot .face-number {
            font-size: 20px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .face-slot .remove-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 20px;
            height: 20px;
            background: var(--danger);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 12px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .face-slot.filled:hover .remove-btn {
            display: flex;
        }

        /* EGI Gallery */
        .egi-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            max-height: 200px;
            overflow-y: auto;
            padding: 5px;
        }

        .egi-thumb {
            aspect-ratio: 1;
            border-radius: 6px;
            overflow: hidden;
            cursor: grab;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .egi-thumb:hover {
            border-color: var(--primary);
            transform: scale(1.05);
        }

        .egi-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Controls */
        .slider-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .slider-wrap label {
            min-width: 80px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .slider-wrap input[type="range"] {
            flex: 1;
            height: 4px;
            -webkit-appearance: none;
            background: var(--border-light);
            border-radius: 2px;
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
            min-width: 40px;
            font-family: 'Share Tech Mono';
            font-size: 11px;
            color: white;
            text-align: right;
        }

        .color-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .color-wrap {
            flex: 1;
            text-align: center;
        }

        .color-wrap label {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .color-wrap input[type="color"] {
            width: 45px;
            height: 30px;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            cursor: pointer;
            background: transparent;
        }

        .toggle-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .toggle-wrap label {
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

        .btn-action {
            width: 100%;
            padding: 12px;
            font-family: 'Share Tech Mono';
            font-size: 12px;
            letter-spacing: 1px;
            border: 1px solid var(--primary);
            background: var(--primary);
            color: #000;
            cursor: pointer;
            border-radius: 8px;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            filter: brightness(1.2);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary);
        }

        /* Info display */
        .info-box {
            background: rgba(0, 255, 221, 0.1);
            border: 1px solid rgba(0, 255, 221, 0.3);
            border-radius: 8px;
            padding: 10px;
            font-size: 11px;
            color: var(--primary);
            margin-top: 10px;
            text-align: center;
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
        <div class="loader-t">POLYHEDRON COMPOSER</div>
        <div class="bar-c">
            <div class="bar-f"></div>
        </div>
    </div>

    <div id="top-bar">
        <h1><i class="fa-solid fa-gem"></i> POLYHEDRON <span>COMPOSER</span></h1>
        <div class="status" id="status-text">Seleziona una forma</div>
    </div>

    <div id="sidebar">
        <!-- Shape Selector -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-shapes"></i> SCEGLI POLIEDRO</div>
            <div class="shape-selector">
                <button class="shape-btn active" data-shape="cube" data-faces="6">
                    <span class="faces">6</span>CUBO
                </button>
                <button class="shape-btn" data-shape="octahedron" data-faces="8">
                    <span class="faces">8</span>OTTAEDRO
                </button>
                <button class="shape-btn" data-shape="dodecahedron" data-faces="12">
                    <span class="faces">12</span>DODECA
                </button>
                <button class="shape-btn" data-shape="icosahedron" data-faces="20">
                    <span class="faces">20</span>ICOSA
                </button>
            </div>
        </div>

        <!-- Face Assignment -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-image"></i> ASSEGNA IMMAGINI ALLE FACCE</div>
            <div class="face-grid" id="face-grid">
                <!-- Dynamically populated -->
            </div>
            <div class="info-box" id="faces-info">
                Trascina un'immagine dalla galleria su una faccia
            </div>
        </div>

        <!-- EGI Gallery -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-photo-film"></i> GALLERIA EGI</div>
            <div class="egi-gallery" id="egi-gallery">
                @foreach ($egis as $egi)
                    <div class="egi-thumb" draggable="true" data-egi-id="{{ $egi->id }}"
                        data-thumb-url="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp">
                        <img src="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp"
                            alt="{{ $egi->title }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Material Controls -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-palette"></i> MATERIALE</div>
            <div class="color-row">
                <div class="color-wrap">
                    <label>Bordo</label>
                    <input type="color" id="edge-color" value="#00ffdd">
                </div>
                <div class="color-wrap">
                    <label>Emissivo</label>
                    <input type="color" id="emissive-color" value="#111111">
                </div>
            </div>
            <div class="slider-wrap">
                <label>Luminosità</label>
                <input type="range" id="emissive-intensity" min="0" max="100" value="30">
                <span class="value" id="emissive-val">0.30</span>
            </div>
            <div class="slider-wrap">
                <label>Metalness</label>
                <input type="range" id="metalness" min="0" max="100" value="10">
                <span class="value" id="metalness-val">0.10</span>
            </div>
        </div>

        <!-- Rotation Controls -->
        <div class="panel">
            <div class="panel-header"><i class="fa-solid fa-rotate"></i> ROTAZIONE</div>
            <div class="toggle-wrap">
                <label>Rotazione automatica</label>
                <div class="toggle-switch">
                    <input type="checkbox" id="auto-rotate" checked>
                    <span class="slider"></span>
                </div>
            </div>
            <div class="slider-wrap">
                <label>Velocità</label>
                <input type="range" id="rotation-speed" min="1" max="100" value="30">
                <span class="value" id="speed-val">30</span>
            </div>
            <div class="slider-wrap">
                <label>Angolo X</label>
                <input type="range" id="angle-x" min="0" max="360" value="0">
                <span class="value" id="angle-x-val">0°</span>
            </div>
            <div class="slider-wrap">
                <label>Angolo Y</label>
                <input type="range" id="angle-y" min="0" max="360" value="0">
                <span class="value" id="angle-y-val">0°</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="panel">
            <button class="btn-action" onclick="resetComposition()">
                <i class="fa-solid fa-rotate-left"></i> RESET
            </button>
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

        // =====================================================
        // CONFIGURATION
        // =====================================================
        let currentShape = 'cube';
        let faceCount = 6;
        const faceTextures = new Map(); // faceIndex -> texture
        const textureLoader = new THREE.TextureLoader();

        // =====================================================
        // SCENE SETUP
        // =====================================================
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 2000);
        camera.position.set(0, 0, 200);

        const renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
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
            new THREE.Vector2(container.clientWidth, container.clientHeight),
            1.5, 0.4, 0.85
        );
        bloomPass.threshold = 0.8;
        bloomPass.strength = 0.5;
        bloomPass.radius = 0.3;
        composer.addPass(bloomPass);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;

        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1);
        dirLight.position.set(50, 100, 50);
        scene.add(dirLight);

        // =====================================================
        // POLYHEDRON CREATION
        // =====================================================
        let polyhedronMesh = null;
        const size = 60;

        function getGeometry(shape) {
            switch (shape) {
                case 'cube':
                    return new THREE.BoxGeometry(size, size, size);
                case 'octahedron':
                    return new THREE.OctahedronGeometry(size * 0.7, 0);
                case 'dodecahedron':
                    return new THREE.DodecahedronGeometry(size * 0.7, 0);
                case 'icosahedron':
                    return new THREE.IcosahedronGeometry(size * 0.7, 0);
                default:
                    return new THREE.BoxGeometry(size, size, size);
            }
        }

        function getFaceCount(shape) {
            switch (shape) {
                case 'cube':
                    return 6;
                case 'octahedron':
                    return 8;
                case 'dodecahedron':
                    return 12;
                case 'icosahedron':
                    return 20;
                default:
                    return 6;
            }
        }

        // Assign material groups to geometry faces
        // This maps triangles to actual polyhedron faces
        function setupFaceGroups(geometry, shape) {
            geometry.clearGroups();
            const positionAttribute = geometry.getAttribute('position');
            const vertexCount = positionAttribute.count;
            const triangleCount = vertexCount / 3;

            if (shape === 'cube') {
                // BoxGeometry already has 6 groups (one per face, each with 2 triangles)
                // Each face = 6 vertices = 2 triangles
                for (let i = 0; i < 6; i++) {
                    geometry.addGroup(i * 6, 6, i);
                }
            } else if (shape === 'octahedron') {
                // Octahedron: 8 triangular faces, each is 1 triangle (3 vertices)
                for (let i = 0; i < 8; i++) {
                    geometry.addGroup(i * 3, 3, i);
                }
            } else if (shape === 'icosahedron') {
                // Icosahedron: 20 triangular faces, each is 1 triangle (3 vertices)
                for (let i = 0; i < 20; i++) {
                    geometry.addGroup(i * 3, 3, i);
                }
            } else if (shape === 'dodecahedron') {
                // Dodecahedron: 12 pentagonal faces
                // Each pentagon is tessellated into 3 triangles = 9 vertices per face
                for (let i = 0; i < 12; i++) {
                    geometry.addGroup(i * 9, 9, i);
                }
            }

            return geometry;
        }

        function createMaterials(count) {
            const materials = [];
            const baseColor = document.getElementById('edge-color').value;
            const emissiveColor = document.getElementById('emissive-color').value;
            const emissiveIntensity = parseInt(document.getElementById('emissive-intensity').value) / 100;
            const metalness = parseInt(document.getElementById('metalness').value) / 100;

            for (let i = 0; i < count; i++) {
                const texture = faceTextures.get(i);

                const mat = new THREE.MeshStandardMaterial({
                    color: texture ? 0xffffff : baseColor,
                    map: texture || null,
                    emissive: emissiveColor,
                    emissiveIntensity: texture ? emissiveIntensity : 0,
                    metalness: metalness,
                    roughness: 0.3,
                    side: THREE.FrontSide
                });
                materials.push(mat);
            }

            return materials;
        }

        function createPolyhedron() {
            // Remove old mesh
            if (polyhedronMesh) {
                scene.remove(polyhedronMesh);
                if (polyhedronMesh.geometry) polyhedronMesh.geometry.dispose();
                if (Array.isArray(polyhedronMesh.material)) {
                    polyhedronMesh.material.forEach(m => m.dispose());
                } else if (polyhedronMesh.material) {
                    polyhedronMesh.material.dispose();
                }
            }

            let geometry = getGeometry(currentShape);

            // Setup face groups for proper material assignment
            geometry = setupFaceGroups(geometry, currentShape);

            // Create materials array
            const materials = createMaterials(faceCount);

            // Create mesh with multi-material
            polyhedronMesh = new THREE.Mesh(geometry, materials);

            scene.add(polyhedronMesh);
            updateStatus();
        }

        // =====================================================
        // FACE GRID UI
        // =====================================================
        function updateFaceGrid() {
            const grid = document.getElementById('face-grid');
            grid.innerHTML = '';

            for (let i = 0; i < faceCount; i++) {
                const slot = document.createElement('div');
                slot.className = 'face-slot';
                slot.dataset.faceIndex = i;

                const texture = faceTextures.get(i);
                if (texture && texture.userData && texture.userData.thumbUrl) {
                    slot.classList.add('filled');
                    slot.innerHTML = `
                        <img src="${texture.userData.thumbUrl}" alt="Face ${i+1}">
                        <span class="face-label">F${i+1}</span>
                        <button class="remove-btn" onclick="removeFaceTexture(${i})">×</button>
                    `;
                } else {
                    slot.innerHTML = `
                        <span class="face-number">${i+1}</span>
                        <span class="face-label">FACCIA ${i+1}</span>
                    `;
                }

                // Drop zone
                slot.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    slot.classList.add('active');
                });

                slot.addEventListener('dragleave', () => {
                    slot.classList.remove('active');
                });

                slot.addEventListener('drop', (e) => {
                    e.preventDefault();
                    slot.classList.remove('active');

                    const thumbUrl = e.dataTransfer.getData('thumbUrl');
                    if (thumbUrl) {
                        loadTextureForFace(i, thumbUrl);
                    }
                });

                grid.appendChild(slot);
            }
        }

        function loadTextureForFace(faceIndex, thumbUrl) {
            textureLoader.load(thumbUrl, (texture) => {
                texture.colorSpace = THREE.SRGBColorSpace;
                texture.userData = {
                    thumbUrl
                };
                faceTextures.set(faceIndex, texture);
                updateFaceGrid();
                createPolyhedron();
            });
        }

        window.removeFaceTexture = function(faceIndex) {
            faceTextures.delete(faceIndex);
            updateFaceGrid();
            createPolyhedron();
        };

        // =====================================================
        // DRAG & DROP FROM GALLERY
        // =====================================================
        document.querySelectorAll('.egi-thumb').forEach(thumb => {
            thumb.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('thumbUrl', thumb.dataset.thumbUrl);
                e.dataTransfer.setData('egiId', thumb.dataset.egiId);
            });
        });

        // =====================================================
        // SHAPE SELECTOR
        // =====================================================
        document.querySelectorAll('.shape-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.shape-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                currentShape = btn.dataset.shape;
                faceCount = parseInt(btn.dataset.faces);
                faceTextures.clear();

                updateFaceGrid();
                createPolyhedron();
            });
        });

        // =====================================================
        // CONTROLS
        // =====================================================
        document.getElementById('edge-color').addEventListener('input', createPolyhedron);
        document.getElementById('emissive-color').addEventListener('input', createPolyhedron);

        document.getElementById('emissive-intensity').addEventListener('input', (e) => {
            document.getElementById('emissive-val').textContent = (e.target.value / 100).toFixed(2);
            createPolyhedron();
        });

        document.getElementById('metalness').addEventListener('input', (e) => {
            document.getElementById('metalness-val').textContent = (e.target.value / 100).toFixed(2);
            createPolyhedron();
        });

        document.getElementById('rotation-speed').addEventListener('input', (e) => {
            document.getElementById('speed-val').textContent = e.target.value;
        });

        document.getElementById('angle-x').addEventListener('input', (e) => {
            document.getElementById('angle-x-val').textContent = e.target.value + '°';
            if (polyhedronMesh && !document.getElementById('auto-rotate').checked) {
                polyhedronMesh.rotation.x = e.target.value * (Math.PI / 180);
            }
        });

        document.getElementById('angle-y').addEventListener('input', (e) => {
            document.getElementById('angle-y-val').textContent = e.target.value + '°';
            if (polyhedronMesh && !document.getElementById('auto-rotate').checked) {
                polyhedronMesh.rotation.y = e.target.value * (Math.PI / 180);
            }
        });

        window.resetComposition = function() {
            faceTextures.clear();
            document.getElementById('edge-color').value = '#00ffdd';
            document.getElementById('emissive-color').value = '#111111';
            document.getElementById('emissive-intensity').value = 30;
            document.getElementById('metalness').value = 10;
            document.getElementById('auto-rotate').checked = true;
            document.getElementById('rotation-speed').value = 30;
            document.getElementById('angle-x').value = 0;
            document.getElementById('angle-y').value = 0;

            updateFaceGrid();
            createPolyhedron();
        };

        function updateStatus() {
            const filled = faceTextures.size;
            document.getElementById('status-text').textContent =
                `${currentShape.toUpperCase()} - ${filled}/${faceCount} facce assegnate`;
            document.getElementById('faces-info').textContent =
                filled === faceCount ?
                '✅ Tutte le facce hanno un\'immagine!' :
                `Trascina immagini sulle ${faceCount - filled} facce rimanenti`;
        }

        // =====================================================
        // ANIMATION
        // =====================================================
        const clock = new THREE.Clock();

        function animate() {
            requestAnimationFrame(animate);

            const time = clock.getElapsedTime();
            controls.update();

            if (polyhedronMesh && document.getElementById('auto-rotate').checked) {
                const speed = parseInt(document.getElementById('rotation-speed').value) / 1000;
                polyhedronMesh.rotation.y += speed;
                polyhedronMesh.rotation.x += speed * 0.3;
            }

            composer.render();
        }

        animate();

        // =====================================================
        // RESIZE
        // =====================================================
        window.addEventListener('resize', () => {
            const width = container.clientWidth;
            const height = container.clientHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
            composer.setSize(width, height);
        });

        // =====================================================
        // INIT
        // =====================================================
        updateFaceGrid();
        createPolyhedron();

        window.onload = () => {
            setTimeout(() => document.getElementById('loader').style.opacity = 0, 800);
            setTimeout(() => document.getElementById('loader').remove(), 1300);
        };
    </script>
</body>

</html>
