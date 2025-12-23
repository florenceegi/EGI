<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sphere Experiment | Florence EGI</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Share+Tech+Mono&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #00ffdd;
            --secondary: #0088ff;
            --danger: #ff0044;
            --gold: #ffaa00;
            --purple: #aa00ff;
            --white: #ffffff;
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
            font-size: 40px;
            font-weight: 700;
            letter-spacing: 8px;
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
            font-size: 24px;
            color: white;
            margin-bottom: 10px;
        }

        #ui-overlay h1 span {
            color: var(--primary);
        }

        #ui-overlay p {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Bottom info */
        #bottom-info {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: rgba(0, 0, 0, 0.8);
            padding: 10px 25px;
            border: 1px solid var(--border-light);
            font-family: 'Share Tech Mono';
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 2px;
        }

        #bottom-info i {
            color: var(--primary);
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
        <div class="loader-t">SPHERE TEST</div>
        <div class="bar-c">
            <div class="bar-f"></div>
        </div>
    </div>

    <div id="ui-overlay">
        <h1>SPHERE <span>EXPERIMENT</span></h1>
        <p>Test: Immagine texture su sfera Three.js</p>
    </div>

    <div id="bottom-info">
        <i>◈</i> DRAG TO ROTATE &nbsp; | &nbsp; SCROLL TO ZOOM
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

        // =====================================================
        // CONFIGURATION
        // =====================================================
        const CONFIG = {
            sphere: {
                radius: 80,
                color: 0x00ffdd // Cyan primary
            },
            camera: {
                fov: 45,
                position: [0, 0, 250]
            },
            bloom: {
                threshold: 0.9,
                strength: 0.3,
                radius: 0.1
            }
        };

        // Image URL from Laravel storage
        const IMAGE_URL = "/users_files/collections_5/creator_17/18_thumbnail.webp";

        // =====================================================
        // ENGINE SETUP
        // =====================================================
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(
            CONFIG.camera.fov,
            window.innerWidth / window.innerHeight,
            0.1,
            2000
        );
        camera.position.set(...CONFIG.camera.position);

        // Renderer
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
        bloomPass.threshold = CONFIG.bloom.threshold;
        bloomPass.strength = CONFIG.bloom.strength;
        bloomPass.radius = CONFIG.bloom.radius;
        composer.addPass(bloomPass);

        // Controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.maxDistance = 500;
        controls.minDistance = 100;

        // =====================================================
        // VOLUMETRIC SHADER (MAGMA CORE)
        // =====================================================
        const magmaVShader = `
            varying vec3 vNormal;
            varying vec3 vPosition;
            void main() {
                vNormal = normalize(normalMatrix * normal);
                vPosition = position;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `;

        const magmaFShader = `
            uniform vec3 uColor;
            uniform float uTime;
            varying vec3 vNormal;
            varying vec3 vPosition;
            
            // Simplex Noise
            vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
            vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
            vec4 permute(vec4 x) { return mod289(((x*34.0)+1.0)*x); }
            vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }
            
            float snoise(vec3 v) { 
                const vec2 C = vec2(1.0/6.0, 1.0/3.0);
                const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);
                vec3 i = floor(v + dot(v, C.yyy));
                vec3 x0 = v - i + dot(i, C.xxx);
                vec3 g = step(x0.yzx, x0.xyz);
                vec3 l = 1.0 - g;
                vec3 i1 = min(g.xyz, l.zxy);
                vec3 i2 = max(g.xyz, l.zxy);
                vec3 x1 = x0 - i1 + C.xxx;
                vec3 x2 = x0 - i2 + C.yyy;
                vec3 x3 = x0 - D.yyy;
                i = mod289(i);
                vec4 p = permute(permute(permute(
                    i.z + vec4(0.0, i1.z, i2.z, 1.0))
                    + i.y + vec4(0.0, i1.y, i2.y, 1.0))
                    + i.x + vec4(0.0, i1.x, i2.x, 1.0));
                float n_ = 0.142857142857;
                vec3 ns = n_ * D.wyz - D.xzx;
                vec4 j = p - 49.0 * floor(p * ns.z * ns.z);
                vec4 x_ = floor(j * ns.z);
                vec4 y_ = floor(j - 7.0 * x_);
                vec4 x = x_ * ns.x + ns.yyyy;
                vec4 y = y_ * ns.x + ns.yyyy;
                vec4 h = 1.0 - abs(x) - abs(y);
                vec4 b0 = vec4(x.xy, y.xy);
                vec4 b1 = vec4(x.zw, y.zw);
                vec4 s0 = floor(b0)*2.0 + 1.0;
                vec4 s1 = floor(b1)*2.0 + 1.0;
                vec4 sh = -step(h, vec4(0.0));
                vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy;
                vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww;
                vec3 p0 = vec3(a0.xy, h.x);
                vec3 p1 = vec3(a0.zw, h.y);
                vec3 p2 = vec3(a1.xy, h.z);
                vec3 p3 = vec3(a1.zw, h.w);
                vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2,p2), dot(p3,p3)));
                p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;
                vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
                m = m * m;
                return 42.0 * dot(m*m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
            }

            void main() {
                float noise = snoise(vPosition * 0.15 + uTime * 0.5);
                float noise2 = snoise(vPosition * 0.5 - uTime * 0.2);
                float intensity = (noise * 0.5 + 0.5) + (noise2 * 0.2);
                
                vec3 viewDir = vec3(0.0, 0.0, 1.0);
                float fresnel = pow(1.0 - dot(normalize(vNormal), viewDir), 2.0);
                
                vec3 coreColor = uColor * (0.3 + intensity * 0.2);
                gl_FragColor = vec4(coreColor + (vec3(0.3)*fresnel), 1.0);
            }
        `;

        // =====================================================
        // SPHERE BUILDER
        // =====================================================
        const commonUniforms = {
            uTime: {
                value: 0
            }
        };

        function createGlassMaterial(colorHex) {
            return new THREE.MeshPhysicalMaterial({
                color: colorHex,
                metalness: 0.05,
                roughness: 0.02,
                transparent: true,
                opacity: 0.15, // Reduced for better image visibility
                envMapIntensity: 0.5, // Reduced reflections
                clearcoat: 0.5,
                clearcoatRoughness: 0.1,
                side: THREE.FrontSide
            });
        }

        function createSphereWithImage(imageUrl, radius, color) {
            const root = new THREE.Group();

            // 1. MAGMA CORE (Inner volumetric glow - smaller to not obstruct image)
            const coreGeo = new THREE.IcosahedronGeometry(radius * 0.25, 5); // Smaller core
            const coreMat = new THREE.ShaderMaterial({
                uniforms: {
                    uColor: {
                        value: new THREE.Color(color)
                    },
                    uTime: commonUniforms.uTime
                },
                vertexShader: magmaVShader,
                fragmentShader: magmaFShader,
                transparent: true,
                opacity: 0.7 // Slightly transparent
            });
            const coreMesh = new THREE.Mesh(coreGeo, coreMat);
            coreMesh.renderOrder = 0;
            root.add(coreMesh);

            // 2. IMAGE SPHERE (Inside the glass shell - enhanced visibility)
            const textureLoader = new THREE.TextureLoader();
            textureLoader.load(imageUrl, (texture) => {
                texture.anisotropy = 16;
                texture.colorSpace = THREE.SRGBColorSpace; // Proper color handling

                const imageSphereGeo = new THREE.SphereGeometry(radius * 0.9, 64, 32); // Larger = more visible
                const imageSphereMat = new THREE.MeshStandardMaterial({
                    map: texture,
                    side: THREE.DoubleSide,
                    transparent: false,
                    depthWrite: true,
                    emissive: 0xffffff, // Self-illuminated
                    emissiveMap: texture,
                    emissiveIntensity: 0.4, // Brightness boost
                    roughness: 1.0,
                    metalness: 0.0
                });
                const imageSphereMesh = new THREE.Mesh(imageSphereGeo, imageSphereMat);
                imageSphereMesh.renderOrder = 1;
                root.add(imageSphereMesh);

                // Store reference for animation
                root.userData.imageMesh = imageSphereMesh;

                console.log('✅ Image texture loaded successfully');
            }, undefined, (error) => {
                console.error('❌ Error loading texture:', error);
            });

            // 3. GLASS SHELL (Outer transparent layer)
            const glassGeo = new THREE.IcosahedronGeometry(radius, 4);
            const glassMat = createGlassMaterial(color);
            const glassMesh = new THREE.Mesh(glassGeo, glassMat);
            glassMesh.renderOrder = 2;
            root.add(glassMesh);

            // 4. GYROSCOPIC RINGS
            const ringMat = new THREE.MeshStandardMaterial({
                color: 0xffffff,
                metalness: 0.8,
                roughness: 0.2,
                transparent: true,
                opacity: 0.6
            });

            const ring1 = new THREE.Mesh(
                new THREE.TorusGeometry(radius * 1.3, 0.5, 16, 100),
                ringMat
            );
            ring1.rotation.x = Math.PI / 1.7;
            root.add(ring1);

            const ring2 = new THREE.Mesh(
                new THREE.TorusGeometry(radius * 1.5, 0.5, 16, 100),
                ringMat
            );
            ring2.rotation.y = Math.PI / 0.5;
            root.add(ring2);

            // Store references
            root.userData.coreMesh = coreMesh;
            root.userData.ring1 = ring1;
            root.userData.ring2 = ring2;

            return root;
        }

        // =====================================================
        // CREATE THE SPHERE
        // =====================================================
        const sphere = createSphereWithImage(IMAGE_URL, CONFIG.sphere.radius, CONFIG.sphere.color);
        scene.add(sphere);

        // =====================================================
        // STARS BACKGROUND
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
        // ANIMATION LOOP
        // =====================================================
        const clock = new THREE.Clock();

        function animate() {
            requestAnimationFrame(animate);

            const time = clock.getElapsedTime();
            controls.update();
            TWEEN.update();

            // Update uniforms
            commonUniforms.uTime.value = time;

            // Animate sphere parts
            if (sphere.userData.coreMesh) {
                sphere.userData.coreMesh.rotation.y -= 0.005;
                sphere.userData.coreMesh.rotation.x += 0.002;
            }

            if (sphere.userData.ring1) {
                sphere.userData.ring1.rotation.z += 0.01;
                sphere.userData.ring1.rotation.x = Math.sin(time * 0.5) * 0.2;
            }

            if (sphere.userData.ring2) {
                sphere.userData.ring2.rotation.y -= 0.015;
                sphere.userData.ring2.rotation.z = Math.cos(time * 0.3) * 0.2;
            }

            // Billboard image sphere to face camera
            if (sphere.userData.imageMesh) {
                sphere.userData.imageMesh.quaternion.copy(camera.quaternion);
            }

            composer.render();
        }

        animate();

        // =====================================================
        // RESIZE HANDLER
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
            setTimeout(() => document.getElementById('loader').remove(), 2000);
        };
    </script>
</body>

</html>
