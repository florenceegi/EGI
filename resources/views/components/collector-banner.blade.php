{{--
    Renaissance Collector Banner Component
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 3.0.0 (FlorenceEGI - Collector Banner)
    @date 2025-01-03
    @purpose Museum-grade collector banner Blade component with Three.js

    Usage: <x-collector-banner />
    Or with props: <x-collector-banner :total-works="15000" :artists="400" />
--}}

@props([
    'totalWorks' => 12847,
    'totalArtists' => 342,
    'ctaText' => 'Esplora la Collezione',
    'ctaLink' => '#collectors',
    'subtitle' => 'Dove passione e raffinatezza creano collezioni immortali'
])

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,700;0,6..96,900;1,6..96,400&family=Crimson+Pro:ital,wght@0,200;0,400;0,600;1,300&display=swap" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
@endpush

<div class="collector-universe" id="collectorBanner-{{ Str::random(8) }}">
    {{-- Three.js Canvas --}}
    <canvas id="three-canvas-{{ $componentId = Str::random(8) }}"></canvas>

    {{-- Gallery Grid --}}
    <div class="gallery-grid">
        @for($i = 0; $i < 48; $i++)
            <div class="gallery-frame"></div>
        @endfor
    </div>

    {{-- Floating Artwork --}}
    <div class="artwork-container">
        @for($i = 0; $i < 4; $i++)
            <div class="artwork-piece"></div>
        @endfor
    </div>

    {{-- Golden Particles --}}
    <div class="particles-overlay" id="particlesOverlay-{{ $componentId }}"></div>

    {{-- Main Content --}}
    <div class="content-overlay">
        <div class="collector-title-wrapper">
            <h1 class="collector-title title-shadow">COLLECTORS</h1>
            <h1 class="collector-title title-glow">COLLECTORS</h1>
            <h1 class="collector-title title-main">COLLECTORS</h1>
        </div>

        <div class="subtitle-container">
            <p class="collector-subtitle" id="subtitle-{{ $componentId }}">
                <span id="subtitleText-{{ $componentId }}"></span>
                <span class="subtitle-cursor"></span>
            </p>
        </div>

        <div class="interactive-elements">
            <div class="collection-counter">
                <div class="counter-label">Opere Curate</div>
                <div class="counter-number" id="counterNumber-{{ $componentId }}">0</div>
            </div>

            <a href="{{ $ctaLink }}" class="premium-cta">
                {{ $ctaText }}
            </a>

            <div class="collection-counter">
                <div class="counter-label">Artisti Rari</div>
                <div class="counter-number" id="artistCounter-{{ $componentId }}">0</div>
            </div>
        </div>
    </div>
</div>

<style>
    /**
     * Renaissance Collector Banner Component Styles
     * Scoped to component to avoid conflicts
     */

    .collector-universe {
        position: relative;
        width: 100%;
        height: 600px;
        background: linear-gradient(180deg,
            #0a0908 0%,
            #1a1512 20%,
            #0f0c09 40%,
            #080605 100%);
        overflow: hidden;
        cursor: crosshair;
    }

    .collector-universe canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .gallery-grid {
        position: absolute;
        top: 0;
        left: -50%;
        width: 200%;
        height: 100%;
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 2px;
        opacity: 0.03;
        transform: perspective(1000px) rotateX(45deg);
        animation: gridRotate 60s linear infinite;
        z-index: 2;
    }

    .gallery-frame {
        border: 1px solid rgba(212, 175, 55, 0.2);
        background: radial-gradient(circle at center,
            rgba(212, 175, 55, 0.05) 0%,
            transparent 70%);
        animation: frameGlow 4s ease-in-out infinite;
    }

    .gallery-frame:nth-child(odd) {
        animation-delay: 0.5s;
    }

    .gallery-frame:nth-child(even) {
        animation-delay: 1s;
    }

    .artwork-container {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 3;
        pointer-events: none;
    }

    .artwork-piece {
        position: absolute;
        width: 80px;
        height: 100px;
        background: linear-gradient(135deg,
            rgba(139, 69, 19, 0.1) 0%,
            rgba(212, 175, 55, 0.1) 50%,
            rgba(139, 69, 19, 0.1) 100%);
        border: 2px solid rgba(212, 175, 55, 0.2);
        box-shadow:
            0 10px 40px rgba(212, 175, 55, 0.1),
            inset 0 0 20px rgba(0, 0, 0, 0.5);
        opacity: 0;
    }

    .artwork-piece::before {
        content: '';
        position: absolute;
        top: 10%;
        left: 10%;
        right: 10%;
        bottom: 10%;
        background: radial-gradient(ellipse at center,
            rgba(212, 175, 55, 0.2) 0%,
            transparent 70%);
        animation: artworkShine 5s ease-in-out infinite;
    }

    .artwork-piece:nth-child(1) {
        top: 10%;
        left: 5%;
        animation: floatArtwork1 15s ease-in-out infinite;
    }

    .artwork-piece:nth-child(2) {
        top: 60%;
        left: 10%;
        width: 60px;
        height: 80px;
        animation: floatArtwork2 18s ease-in-out infinite;
        animation-delay: 2s;
    }

    .artwork-piece:nth-child(3) {
        top: 20%;
        right: 8%;
        width: 90px;
        height: 110px;
        animation: floatArtwork3 20s ease-in-out infinite;
        animation-delay: 4s;
    }

    .artwork-piece:nth-child(4) {
        top: 65%;
        right: 15%;
        width: 70px;
        height: 90px;
        animation: floatArtwork4 16s ease-in-out infinite;
        animation-delay: 6s;
    }

    .content-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 10;
        width: 90%;
        max-width: 1200px;
    }

    .collector-title-wrapper {
        position: relative;
        height: 150px;
        margin-bottom: 30px;
        perspective: 1000px;
    }

    .collector-title {
        position: absolute;
        width: 100%;
        font-family: 'Bodoni Moda', serif;
        font-size: clamp(4rem, 10vw, 8rem);
        font-weight: 900;
        font-style: italic;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        opacity: 0;
        transform-style: preserve-3d;
    }

    .title-main {
        color: transparent;
        background: linear-gradient(135deg,
            #d4af37 0%,
            #f4e4c1 20%,
            #d4af37 40%,
            #8b6914 60%,
            #d4af37 80%,
            #f4e4c1 100%);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        background-clip: text;
        animation:
            goldFlow 4s ease-in-out infinite,
            titleReveal 2s ease-out 0.5s forwards;
    }

    .title-shadow {
        color: transparent;
        -webkit-text-stroke: 1px rgba(212, 175, 55, 0.3);
        animation:
            titleShadow 2s ease-out 0.7s forwards,
            titleFloat 6s ease-in-out infinite;
    }

    .title-glow {
        color: transparent;
        text-shadow:
            0 0 40px rgba(212, 175, 55, 0.5),
            0 0 80px rgba(212, 175, 55, 0.3),
            0 0 120px rgba(212, 175, 55, 0.1);
        opacity: 0.5;
        animation:
            titleGlow 3s ease-in-out infinite alternate,
            titleReveal 2s ease-out 0.9s forwards;
    }

    .subtitle-container {
        height: 40px;
        margin-bottom: 40px;
        overflow: hidden;
    }

    .collector-subtitle {
        font-family: 'Crimson Pro', serif;
        font-size: 1.4rem;
        font-weight: 300;
        font-style: italic;
        color: rgba(244, 228, 193, 0.8);
        letter-spacing: 0.05em;
        opacity: 0;
        animation: subtitleReveal 1s ease-out 2s forwards;
    }

    .subtitle-cursor {
        display: inline-block;
        width: 2px;
        height: 1.4rem;
        background: rgba(212, 175, 55, 0.8);
        margin-left: 2px;
        animation: cursorBlink 1s step-end infinite;
    }

    .interactive-elements {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 50px;
        opacity: 0;
        animation: elementsReveal 1s ease-out 3s forwards;
    }

    .collection-counter {
        padding: 20px 40px;
        background: rgba(8, 6, 5, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.3);
        position: relative;
        overflow: hidden;
    }

    .collection-counter::before {
        content: '';
        position: absolute;
        top: -100%;
        left: -100%;
        width: 300%;
        height: 300%;
        background: radial-gradient(circle,
            rgba(212, 175, 55, 0.1) 0%,
            transparent 70%);
        animation: counterScan 4s linear infinite;
    }

    .counter-label {
        font-family: 'Crimson Pro', serif;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: rgba(212, 175, 55, 0.7);
        margin-bottom: 10px;
    }

    .counter-number {
        font-family: 'Bodoni Moda', serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #d4af37;
        text-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
    }

    .premium-cta {
        position: relative;
        display: inline-block;
        padding: 20px 60px;
        font-family: 'Crimson Pro', serif;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        text-decoration: none;
        color: #0a0908;
        background: linear-gradient(135deg,
            #d4af37 0%,
            #f4e4c1 50%,
            #d4af37 100%);
        background-size: 200% 200%;
        border: none;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: ctaPulse 2s ease-in-out infinite;
    }

    .premium-cta::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .premium-cta:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow:
            0 20px 40px rgba(212, 175, 55, 0.3),
            0 10px 20px rgba(0, 0, 0, 0.2);
        animation: none;
    }

    .premium-cta:hover::before {
        width: 300px;
        height: 300px;
    }

    .particles-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 8;
    }

    .golden-particle {
        position: absolute;
        width: 3px;
        height: 3px;
        background: radial-gradient(circle,
            rgba(212, 175, 55, 0.8) 0%,
            rgba(212, 175, 55, 0) 70%);
        border-radius: 50%;
        filter: blur(0.5px);
    }

    /* Animations */
    @keyframes gridRotate {
        0% { transform: perspective(1000px) rotateX(45deg) translateX(0); }
        100% { transform: perspective(1000px) rotateX(45deg) translateX(-50%); }
    }

    @keyframes frameGlow {
        0%, 100% {
            opacity: 0.03;
            transform: scale(1);
        }
        50% {
            opacity: 0.08;
            transform: scale(1.02);
        }
    }

    @keyframes floatArtwork1 {
        0%, 100% {
            opacity: 0.3;
            transform: translate(0, 0) rotate(5deg);
        }
        25% {
            opacity: 0.6;
            transform: translate(50px, -30px) rotate(-5deg);
        }
        50% {
            opacity: 0.4;
            transform: translate(-30px, 20px) rotate(3deg);
        }
        75% {
            opacity: 0.5;
            transform: translate(20px, -10px) rotate(-3deg);
        }
    }

    @keyframes floatArtwork2 {
        0%, 100% {
            opacity: 0.25;
            transform: translate(0, 0) rotate(-3deg) scale(1);
        }
        33% {
            opacity: 0.5;
            transform: translate(40px, -40px) rotate(5deg) scale(1.1);
        }
        66% {
            opacity: 0.35;
            transform: translate(-20px, 30px) rotate(-5deg) scale(0.95);
        }
    }

    @keyframes floatArtwork3 {
        0%, 100% {
            opacity: 0.35;
            transform: translate(0, 0) rotate(2deg);
        }
        50% {
            opacity: 0.55;
            transform: translate(-60px, 40px) rotate(-8deg);
        }
    }

    @keyframes floatArtwork4 {
        0%, 100% {
            opacity: 0.3;
            transform: translate(0, 0) rotate(-5deg) scale(1);
        }
        25% {
            opacity: 0.45;
            transform: translate(-30px, -20px) rotate(3deg) scale(1.05);
        }
        50% {
            opacity: 0.35;
            transform: translate(40px, 10px) rotate(-2deg) scale(0.98);
        }
        75% {
            opacity: 0.5;
            transform: translate(-10px, -30px) rotate(4deg) scale(1.02);
        }
    }

    @keyframes artworkShine {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 0.5; }
    }

    @keyframes goldFlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes titleReveal {
        0% {
            opacity: 0;
            transform: translateZ(-100px) rotateY(20deg);
        }
        100% {
            opacity: 1;
            transform: translateZ(0) rotateY(0);
        }
    }

    @keyframes titleShadow {
        0% {
            opacity: 0;
            transform: translateZ(-50px) translateX(10px) translateY(10px);
        }
        100% {
            opacity: 0.3;
            transform: translateZ(-20px) translateX(5px) translateY(5px);
        }
    }

    @keyframes titleFloat {
        0%, 100% { transform: translateZ(-20px) translateX(5px) translateY(5px); }
        50% { transform: translateZ(-20px) translateX(5px) translateY(0px); }
    }

    @keyframes titleGlow {
        0% { filter: brightness(1); }
        100% { filter: brightness(1.2); }
    }

    @keyframes subtitleReveal {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes cursorBlink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }

    @keyframes elementsReveal {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes counterScan {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes ctaPulse {
        0%, 100% {
            background-position: 0% 50%;
            transform: scale(1);
        }
        50% {
            background-position: 100% 50%;
            transform: scale(1.02);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .collector-universe {
            height: 500px;
        }

        .collector-title {
            font-size: clamp(3rem, 12vw, 5rem);
        }

        .interactive-elements {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Collector Banner Three.js Engine
     * Component ID: {{ $componentId }}
     */

    const componentId = '{{ $componentId }}';
    const totalWorks = {{ $totalWorks }};
    const totalArtists = {{ $totalArtists }};
    const subtitle = "{{ $subtitle }}";

    // Three.js Scene Setup
    let scene, camera, renderer;
    let particles, frames;
    let mouseX = 0, mouseY = 0;
    let targetX = 0, targetY = 0;

    function initThreeJS() {
        // Scene
        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x0a0908, 0.002);

        // Camera
        camera = new THREE.PerspectiveCamera(
            75,
            window.innerWidth / 600,
            0.1,
            1000
        );
        camera.position.z = 50;

        // Renderer
        const canvas = document.getElementById(`three-canvas-${componentId}`);
        if (!canvas) return;

        renderer = new THREE.WebGLRenderer({
            canvas: canvas,
            alpha: true,
            antialias: true
        });
        renderer.setSize(window.innerWidth, 600);
        renderer.setPixelRatio(window.devicePixelRatio);

        // Create floating golden frames
        createFloatingFrames();

        // Create particle system
        createParticleSystem();

        // Add lights
        const ambientLight = new THREE.AmbientLight(0xd4af37, 0.3);
        scene.add(ambientLight);

        const pointLight = new THREE.PointLight(0xd4af37, 1, 100);
        pointLight.position.set(0, 0, 30);
        scene.add(pointLight);

        // Mouse movement
        document.addEventListener('mousemove', onMouseMove);

        // Start animation
        animate();
    }

    function createFloatingFrames() {
        const geometry = new THREE.BoxGeometry(1, 1.3, 0.1);
        const material = new THREE.MeshPhongMaterial({
            color: 0xd4af37,
            emissive: 0x8b6914,
            emissiveIntensity: 0.2,
            shininess: 100,
            opacity: 0.8,
            transparent: true
        });

        frames = new THREE.Group();

        for (let i = 0; i < 20; i++) {
            const frame = new THREE.Mesh(geometry, material.clone());
            frame.position.x = (Math.random() - 0.5) * 100;
            frame.position.y = (Math.random() - 0.5) * 50;
            frame.position.z = (Math.random() - 0.5) * 50;
            frame.rotation.x = Math.random() * Math.PI;
            frame.rotation.y = Math.random() * Math.PI;
            frame.userData = {
                rotationSpeed: {
                    x: (Math.random() - 0.5) * 0.01,
                    y: (Math.random() - 0.5) * 0.01,
                    z: (Math.random() - 0.5) * 0.01
                },
                floatSpeed: Math.random() * 0.5 + 0.5,
                floatOffset: Math.random() * Math.PI * 2
            };
            frames.add(frame);
        }

        scene.add(frames);
    }

    function createParticleSystem() {
        const particleCount = 500;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);
        const sizes = new Float32Array(particleCount);

        for (let i = 0; i < particleCount; i++) {
            const i3 = i * 3;
            positions[i3] = (Math.random() - 0.5) * 100;
            positions[i3 + 1] = (Math.random() - 0.5) * 60;
            positions[i3 + 2] = (Math.random() - 0.5) * 50;

            // Golden colors
            colors[i3] = 0.83 + Math.random() * 0.17;
            colors[i3 + 1] = 0.69 + Math.random() * 0.1;
            colors[i3 + 2] = 0.22;

            sizes[i] = Math.random() * 2 + 0.5;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
        geometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

        const material = new THREE.PointsMaterial({
            size: 2,
            vertexColors: true,
            transparent: true,
            opacity: 0.6,
            blending: THREE.AdditiveBlending,
            depthWrite: false
        });

        particles = new THREE.Points(geometry, material);
        scene.add(particles);
    }

    function onMouseMove(event) {
        mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        mouseY = -(event.clientY / 600) * 2 + 1;
    }

    function animate() {
        requestAnimationFrame(animate);

        // Smooth mouse follow
        targetX += (mouseX - targetX) * 0.05;
        targetY += (mouseY - targetY) * 0.05;

        // Rotate particles
        if (particles) {
            particles.rotation.y += 0.0005;
            particles.rotation.x += 0.0003;

            // Mouse influence on particles
            particles.rotation.y += targetX * 0.01;
            particles.rotation.x += targetY * 0.01;
        }

        // Animate frames
        if (frames) {
            frames.children.forEach((frame, index) => {
                const time = Date.now() * 0.001;

                // Rotation
                frame.rotation.x += frame.userData.rotationSpeed.x;
                frame.rotation.y += frame.userData.rotationSpeed.y;
                frame.rotation.z += frame.userData.rotationSpeed.z;

                // Floating motion
                frame.position.y += Math.sin(time * frame.userData.floatSpeed + frame.userData.floatOffset) * 0.02;

                // Mouse influence
                frame.position.x += targetX * 0.5;
                frame.position.z += targetY * 0.5;
            });

            frames.rotation.y += 0.001;
        }

        // Camera movement
        camera.position.x = targetX * 10;
        camera.position.y = targetY * 10;
        camera.lookAt(scene.position);

        if (renderer) {
            renderer.render(scene, camera);
        }
    }

    // Initialize Three.js
    initThreeJS();

    // GSAP Animations
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(TextPlugin);

        // Typewriter effect for subtitle
        gsap.to(`#subtitleText-${componentId}`, {
            duration: 3,
            text: subtitle,
            ease: "none",
            delay: 2.5
        });

        // Counter animations
        gsap.to(`#counterNumber-${componentId}`, {
            innerHTML: totalWorks,
            duration: 3,
            ease: "power2.out",
            delay: 3,
            snap: { innerHTML: 1 },
            onUpdate: function() {
                const element = document.getElementById(`counterNumber-${componentId}`);
                if (element) {
                    element.innerHTML = Math.floor(this.targets()[0].innerHTML).toLocaleString();
                }
            }
        });

        gsap.to(`#artistCounter-${componentId}`, {
            innerHTML: totalArtists,
            duration: 3,
            ease: "power2.out",
            delay: 3.5,
            snap: { innerHTML: 1 }
        });
    }

    // Create DOM particles
    function createDOMParticles() {
        const container = document.getElementById(`particlesOverlay-${componentId}`);
        if (!container) return;

        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'golden-particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            container.appendChild(particle);

            // Animate particle
            if (typeof gsap !== 'undefined') {
                gsap.to(particle, {
                    y: -100,
                    x: (Math.random() - 0.5) * 100,
                    opacity: 0,
                    duration: Math.random() * 5 + 5,
                    repeat: -1,
                    delay: Math.random() * 5,
                    ease: "power1.out"
                });
            }
        }
    }

    createDOMParticles();

    // Advanced hover effects
    const cta = document.querySelector('.premium-cta');

    if (cta) {
        cta.addEventListener('mouseenter', () => {
            if (frames && typeof gsap !== 'undefined') {
                gsap.to(frames.children, {
                    duration: 1,
                    ease: "power2.out",
                    onUpdate: function() {
                        frames.children.forEach(frame => {
                            frame.material.emissiveIntensity = 0.5;
                        });
                    }
                });
            }
        });

        cta.addEventListener('mouseleave', () => {
            if (frames && typeof gsap !== 'undefined') {
                gsap.to(frames.children, {
                    duration: 1,
                    ease: "power2.out",
                    onUpdate: function() {
                        frames.children.forEach(frame => {
                            frame.material.emissiveIntensity = 0.2;
                        });
                    }
                });
            }
        });
    }

    // Resize handler
    window.addEventListener('resize', () => {
        if (camera && renderer) {
            camera.aspect = window.innerWidth / 600;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, 600);
        }
    });
});
</script>
@endpush
@endonce
