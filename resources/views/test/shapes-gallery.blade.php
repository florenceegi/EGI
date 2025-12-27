<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shapes Gallery | Florence EGI</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Share+Tech+Mono&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #00ffdd;
            --secondary: #0088ff;
            --danger: #ff0044;
            --gold: #ffaa00;
            --purple: #aa00ff;
            --glass-dark: rgba(5, 8, 12, 0.95);
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
            background: radial-gradient(circle at center, #0a1520 0%, #000 100%);
            min-height: 100vh;
            font-family: 'Rajdhani', sans-serif;
            color: var(--text-main);
            overflow: hidden;
        }

        canvas {
            display: block;
            position: fixed;
            inset: 0;
            z-index: 0;
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
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 6px;
            color: white;
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

        /* UI Overlay */
        #ui-overlay {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            padding: 20px 30px;
            backdrop-filter: blur(10px);
            border-left: 4px solid var(--primary);
        }

        #ui-overlay h1 {
            font-family: 'Share Tech Mono';
            font-size: 22px;
            color: white;
            margin-bottom: 10px;
        }

        #ui-overlay h1 span {
            color: var(--primary);
        }

        #ui-overlay p {
            color: var(--text-muted);
            font-size: 13px;
        }

        /* Shape selector */
        #shape-selector {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 15px 25px;
            backdrop-filter: blur(10px);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .shape-btn {
            padding: 10px 20px;
            font-family: 'Share Tech Mono';
            font-size: 11px;
            letter-spacing: 1px;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 6px;
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

        /* Shape name display */
        #shape-name {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, 200px);
            z-index: 100;
            font-family: 'Share Tech Mono';
            font-size: 28px;
            color: white;
            letter-spacing: 4px;
            text-shadow: 0 0 20px var(--primary);
        }

        /* Navigation arrows */
        .nav-arrow {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
            background: var(--glass-dark);
            border: 1px solid var(--border-light);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-arrow:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 20px rgba(0, 255, 221, 0.3);
        }

        #prev-shape {
            left: 30px;
        }

        #next-shape {
            right: 30px;
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
        <div class="loader-t">SHAPES GALLERY</div>
        <div class="bar-c">
            <div class="bar-f"></div>
        </div>
    </div>

    <div id="ui-overlay">
        <h1>SHAPES <span>GALLERY</span></h1>
        <p>Esplora le diverse forme 3D con effetti premium</p>
    </div>

    <div id="shape-name">SPHERE</div>

    <button class="nav-arrow" id="prev-shape">←</button>
    <button class="nav-arrow" id="next-shape">→</button>

    <div id="shape-selector">
        <button class="shape-btn active" data-shape="sphere">SFERA</button>
        <button class="shape-btn" data-shape="crystal">CRISTALLO</button>
        <button class="shape-btn" data-shape="cube">CUBO</button>
        <button class="shape-btn" data-shape="pyramid">PIRAMIDE</button>
        <button class="shape-btn" data-shape="dodeca">DODECA</button>
        <button class="shape-btn" data-shape="torus">TORUS</button>
        <button class="shape-btn" data-shape="torusknot">TORUS KNOT</button>
        <button class="shape-btn" data-shape="capsule">CAPSULA</button>
        <button class="shape-btn" data-shape="prism">PRISMA</button>
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
        import {
            RoundedBoxGeometry
        } from 'three/addons/geometries/RoundedBoxGeometry.js';

        // =====================================================
        // CONFIGURATION
        // =====================================================
        const SHAPES = {
            sphere: {
                name: 'SFERA',
                color: 0x00ffdd
            },
            crystal: {
                name: 'CRISTALLO',
                color: 0x00aaff
            },
            cube: {
                name: 'CUBO',
                color: 0xffaa00
            },
            pyramid: {
                name: 'PIRAMIDE',
                color: 0xff0066
            },
            dodeca: {
                name: 'DODECAEDRO',
                color: 0xaa00ff
            },
            torus: {
                name: 'TORUS',
                color: 0x00ff88
            },
            torusknot: {
                name: 'TORUS KNOT',
                color: 0xff6600
            },
            capsule: {
                name: 'CAPSULA',
                color: 0x0088ff
            },
            prism: {
                name: 'PRISMA',
                color: 0x00ddff
            }
        };

        const shapeOrder = ['sphere', 'crystal', 'cube', 'pyramid', 'dodeca', 'torus', 'torusknot', 'capsule', 'prism'];
        let currentShapeIndex = 0;

        const IMAGE_URL = "/users_files/collections_7/creator_26/36_thumbnail.webp";

        // =====================================================
        // ENGINE SETUP
        // =====================================================
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(0, 0, 250);

        const renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        document.body.appendChild(renderer.domElement);

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
        bloomPass.threshold = 0.85;
        bloomPass.strength = 0.4;
        bloomPass.radius = 0.2;
        composer.addPass(bloomPass);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.maxDistance = 500;
        controls.minDistance = 100;

        // =====================================================
        // STARS
        // =====================================================
        const starsGeo = new THREE.BufferGeometry();
        const starsCount = 2000;
        const starPos = new Float32Array(starsCount * 3);
        for (let i = 0; i < starsCount * 3; i++) {
            starPos[i] = (Math.random() - 0.5) * 1200;
        }
        starsGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3));
        const stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({
            color: 0x888888,
            size: 0.7,
            transparent: true,
            opacity: 0.8
        }));
        scene.add(stars);

        // =====================================================
        // SHAPE GEOMETRIES
        // =====================================================
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
                case 'prism': {
                    // Rectangular prism - PORTRAIT: short side as base, tall standing
                    const width = size * 1.1; // lato corto = base
                    const height = size * 1.8; // lato lungo = in piedi
                    const depth = size * 0.25;
                    const radius = size * 0.08;
                    return new RoundedBoxGeometry(width, height, depth, 4, radius);
                }
                default:
                    return new THREE.SphereGeometry(size, 64, 32);
            }
        }

        function getInnerGeometry(shapeKey, size) {
            // Inner geometry for image - slightly smaller
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
                case 'prism': {
                    // Flat plane for image - PORTRAIT aspect ratio
                    const width = innerSize * 1.0;
                    const height = innerSize * 1.65;
                    return new THREE.PlaneGeometry(width, height);
                }
                default:
                    return new THREE.SphereGeometry(innerSize, 64, 32);
            }
        }

        // =====================================================
        // CREATE TEXT TEXTURE (for prism back side)
        // =====================================================
        function createTextTexture(title, lines) {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 800;
            const ctx = canvas.getContext('2d');

            // DARK background - proper contrast
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, '#0a1828');
            gradient.addColorStop(0.5, '#0d1f2d');
            gradient.addColorStop(1, '#071018');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Outer border - gold accent
            ctx.strokeStyle = '#ffaa00';
            ctx.lineWidth = 6;
            ctx.strokeRect(16, 16, canvas.width - 32, canvas.height - 32);

            // Inner border - subtle
            ctx.strokeStyle = 'rgba(255, 170, 0, 0.4)';
            ctx.lineWidth = 2;
            ctx.strokeRect(28, 28, canvas.width - 56, canvas.height - 56);

            // Title - GOLD, bold, large
            ctx.fillStyle = '#ffaa00';
            ctx.font = 'bold 42px "Rajdhani", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(title, canvas.width / 2, 110);

            // Title underline
            ctx.strokeStyle = '#ffaa00';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(80, 135);
            ctx.lineTo(canvas.width - 80, 135);
            ctx.stroke();

            // Description lines - WHITE for max contrast
            ctx.fillStyle = '#ffffff';
            ctx.font = '26px "Rajdhani", sans-serif';
            let yPos = 210;
            const lineHeight = 55;

            lines.forEach((line) => {
                if (line === '---') {
                    // Draw separator - subtle gold
                    ctx.strokeStyle = 'rgba(255, 170, 0, 0.5)';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(100, yPos - 15);
                    ctx.lineTo(canvas.width - 100, yPos - 15);
                    ctx.stroke();
                } else {
                    ctx.fillText(line, canvas.width / 2, yPos);
                }
                yPos += lineHeight;
            });

            // Bottom decorative element - cyan accent
            ctx.fillStyle = '#00ddff';
            ctx.font = 'bold 20px "Share Tech Mono", monospace';
            ctx.fillText('◆ FLORENCE EGI ◆', canvas.width / 2, canvas.height - 55);

            const texture = new THREE.CanvasTexture(canvas);
            texture.colorSpace = THREE.SRGBColorSpace;
            return texture;
        }

        // =====================================================
        // CREATE SHAPE
        // =====================================================
        let currentShape = null;
        let imageTexture = null;

        // Preload texture
        const textureLoader = new THREE.TextureLoader();
        textureLoader.load(IMAGE_URL, (texture) => {
            texture.colorSpace = THREE.SRGBColorSpace;
            texture.anisotropy = 16;
            imageTexture = texture;
            createShape('sphere');
        });

        function createShape(shapeKey) {
            // Remove old shape
            if (currentShape) {
                scene.remove(currentShape);
                currentShape.traverse((child) => {
                    if (child.geometry) child.geometry.dispose();
                    if (child.material) {
                        if (Array.isArray(child.material)) {
                            child.material.forEach(m => m.dispose());
                        } else {
                            child.material.dispose();
                        }
                    }
                });
            }

            const shapeData = SHAPES[shapeKey];
            const root = new THREE.Group();
            const size = 60;

            // 1. GLASS SHELL
            const glassGeo = getGeometry(shapeKey, size);
            const glassMat = new THREE.MeshPhysicalMaterial({
                color: shapeData.color,
                metalness: 0.05,
                roughness: 0.02,
                transparent: true,
                opacity: 0.2,
                envMapIntensity: 0.8,
                clearcoat: 0.8,
                clearcoatRoughness: 0.1,
                side: THREE.FrontSide,
                depthWrite: false // Prevent z-fighting with inner content
            });
            const glassMesh = new THREE.Mesh(glassGeo, glassMat);
            glassMesh.renderOrder = 2;
            root.add(glassMesh);

            // 2. IMAGE INNER (or front/back for prism)
            if (imageTexture) {
                if (shapeKey === 'prism') {
                    // PRISM: Create inner box with image on front, text on back
                    // This puts the content INSIDE the glass like a showcase
                    const innerScale = 0.9; // Fill most of the prism with minimal padding
                    const innerWidth = size * innerScale;
                    const innerHeight = size * 1.55 * innerScale; // Taller to match prism aspect
                    const innerDepth = size * 0.15 * innerScale; // Thin slab inside

                    // Create the text texture for back face
                    const textTexture = createTextTexture('EGI Digital Art', [
                        'Unique generative artwork',
                        'Minted on Polygon Network',
                        'Collection: Florence EGI',
                        '---',
                        'Artist: Creator #26',
                        'Token ID: 36'
                    ]);

                    // Create materials array for box: [+X, -X, +Y, -Y, +Z (front), -Z (back)]
                    const sideMat = new THREE.MeshStandardMaterial({
                        color: 0x050810,
                        roughness: 1.0,
                        metalness: 0.0,
                        transparent: true,
                        opacity: 0.8
                    });

                    // Front face - Image
                    const frontMat = new THREE.MeshStandardMaterial({
                        map: imageTexture,
                        side: THREE.FrontSide,
                        emissive: 0xffffff,
                        emissiveMap: imageTexture,
                        emissiveIntensity: 0.5,
                        roughness: 1.0,
                        metalness: 0.0
                    });

                    // Back face - Text
                    const backMat = new THREE.MeshStandardMaterial({
                        map: textTexture,
                        side: THREE.FrontSide,
                        emissive: 0xffffff,
                        emissiveMap: textTexture,
                        emissiveIntensity: 0.3,
                        roughness: 1.0,
                        metalness: 0.0
                    });

                    const materials = [
                        sideMat, // +X (right)
                        sideMat, // -X (left)
                        sideMat, // +Y (top)
                        sideMat, // -Y (bottom)
                        frontMat, // +Z (front - image)
                        backMat // -Z (back - text)
                    ];

                    const innerGeo = new THREE.BoxGeometry(innerWidth, innerHeight, innerDepth);
                    const innerMesh = new THREE.Mesh(innerGeo, materials);
                    innerMesh.renderOrder = 1;
                    root.add(innerMesh);

                    root.userData.innerMesh = innerMesh;
                    root.userData.frontMat = frontMat;
                    root.userData.backMat = backMat;
                } else {
                    // Other shapes - standard inner mesh
                    const innerGeo = getInnerGeometry(shapeKey, size);
                    const innerMat = new THREE.MeshStandardMaterial({
                        map: imageTexture,
                        side: THREE.DoubleSide,
                        emissive: 0xffffff,
                        emissiveMap: imageTexture,
                        emissiveIntensity: 0.4,
                        roughness: 1.0,
                        metalness: 0.0
                    });
                    const innerMesh = new THREE.Mesh(innerGeo, innerMat);
                    innerMesh.renderOrder = 1;
                    root.add(innerMesh);
                    root.userData.innerMesh = innerMesh;
                }
            }

            // 3. RINGS (adapted per shape)
            const ringMat = new THREE.MeshStandardMaterial({
                color: 0xffffff,
                metalness: 0.8,
                roughness: 0.2,
                transparent: true,
                opacity: 0.5
            });

            // Different ring configurations per shape
            if (shapeKey === 'torus' || shapeKey === 'torusknot') {
                // No rings for torus shapes
            } else {
                const ring1 = new THREE.Mesh(
                    new THREE.TorusGeometry(size * 1.3, 0.4, 16, 100),
                    ringMat
                );
                ring1.rotation.x = Math.PI / 1.7;
                root.add(ring1);
                root.userData.ring1 = ring1;

                const ring2 = new THREE.Mesh(
                    new THREE.TorusGeometry(size * 1.5, 0.4, 16, 100),
                    ringMat
                );
                ring2.rotation.y = Math.PI / 2;
                root.add(ring2);
                root.userData.ring2 = ring2;
            }

            root.userData.glassMesh = glassMesh;
            scene.add(root);
            currentShape = root;

            // Update UI
            document.getElementById('shape-name').textContent = shapeData.name;
            document.querySelectorAll('.shape-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.shape === shapeKey);
            });
        }

        // =====================================================
        // NAVIGATION
        // =====================================================
        function nextShape() {
            currentShapeIndex = (currentShapeIndex + 1) % shapeOrder.length;
            createShape(shapeOrder[currentShapeIndex]);
        }

        function prevShape() {
            currentShapeIndex = (currentShapeIndex - 1 + shapeOrder.length) % shapeOrder.length;
            createShape(shapeOrder[currentShapeIndex]);
        }

        document.getElementById('next-shape').addEventListener('click', nextShape);
        document.getElementById('prev-shape').addEventListener('click', prevShape);

        document.querySelectorAll('.shape-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const shapeKey = btn.dataset.shape;
                currentShapeIndex = shapeOrder.indexOf(shapeKey);
                createShape(shapeKey);
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') nextShape();
            if (e.key === 'ArrowLeft') prevShape();
        });

        // =====================================================
        // ANIMATION
        // =====================================================
        const clock = new THREE.Clock();

        function animate() {
            requestAnimationFrame(animate);

            const time = clock.getElapsedTime();
            controls.update();

            if (currentShape) {
                // Rotate shape
                currentShape.rotation.y += 0.003;

                // Animate rings
                if (currentShape.userData.ring1) {
                    currentShape.userData.ring1.rotation.z += 0.008;
                }
                if (currentShape.userData.ring2) {
                    currentShape.userData.ring2.rotation.y -= 0.01;
                }
            }

            composer.render();
        }

        animate();

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
            setTimeout(() => document.getElementById('loader').style.opacity = 0, 1000);
            setTimeout(() => document.getElementById('loader').remove(), 1500);
        };
    </script>
</body>

</html>
