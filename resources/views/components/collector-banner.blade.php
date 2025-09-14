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
    'title' => 'COLLECTORS',
    'totalWorks' => 12847,
    'totalArtists' => 342,
    'totalReservations' => \App\Models\Reservation::where('is_current', true)->where('status', 'active')->where('is_highest', true)->sum('offer_amount_fiat'),
    'subtitle' => 'Dove passione e raffinatezza creano collezioni immortali',
    'floatingArtworks' => \App\Models\Egi::whereNotNull('key_file')
        ->whereNotNull('collection_id')
        ->whereNotNull('user_id')
        ->whereNotNull('extension')
        ->where('is_published', true)
        ->inRandomOrder()
        ->take(60)
        ->get()
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

    {{-- Floating Artwork --}}
    <div class="artwork-container">
        @foreach($floatingArtworks->take(4) as $index => $artwork)
            <div class="artwork-piece artwork-piece-{{ $index + 1 }}" data-artwork-id="{{ $artwork->id }}">
                @if($artwork->avatar_image_url)
                    <img src="{{ $artwork->avatar_image_url }}"
                         alt="{{ $artwork->title ?? 'Opera d\'arte' }}"
                         class="artwork-image"
                         loading="lazy">
                @endif
                <div class="artwork-frame"></div>
            </div>
        @endforeach

        {{-- Backup empty frames if not enough artworks --}}
        @for($i = $floatingArtworks->count(); $i < 4; $i++)
            <div class="artwork-piece artwork-piece-{{ $i + 1 }}">
                <div class="artwork-frame"></div>
            </div>
        @endfor
    </div>

    {{-- Golden Particles --}}
    <div class="particles-overlay" id="particlesOverlay-{{ $componentId }}"></div>

    {{-- Main Content --}}
    <div class="content-overlay">
        <div class="collector-title-wrapper">
            <h1 class="collector-title title-shadow">{{ $title }}</h1>
            <h1 class="collector-title title-glow">{{ $title }}</h1>
            <h1 class="collector-title title-main">{{ $title }}</h1>
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

            <div class="collection-counter">
                <div class="counter-label">Volume Prenotazioni</div>
                <div class="counter-number" id="reservationCounter-{{ $componentId }}">0</div>
            </div>

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
        pointer-events: auto;
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
        opacity: 0;
        overflow: hidden;
        border-radius: 4px;
        box-shadow:
            0 10px 40px rgba(212, 175, 55, 0.1),
            0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .artwork-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 2px;
        transition: transform 0.3s ease;
    }

    .artwork-frame {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid rgba(212, 175, 55, 0.4);
        border-radius: 4px;
        background: linear-gradient(45deg,
            transparent 0%,
            rgba(212, 175, 55, 0.05) 50%,
            transparent 100%);
        pointer-events: none;
    }

    .artwork-piece:hover .artwork-image {
        transform: scale(1.05);
    }

    .artwork-frame::before {
        content: '';
        position: absolute;
        top: 6px;
        left: 6px;
        right: 6px;
        bottom: 6px;
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 2px;
        background: radial-gradient(ellipse at center,
            rgba(212, 175, 55, 0.1) 0%,
            transparent 70%);
        animation: artworkShine 5s ease-in-out infinite;
    }

    .artwork-piece-1 {
        top: 10%;
        left: 5%;
        animation: floatArtwork1 15s ease-in-out infinite;
    }

    .artwork-piece-2 {
        top: 60%;
        left: 10%;
        width: 60px;
        height: 80px;
        animation: floatArtwork2 18s ease-in-out infinite;
        animation-delay: 2s;
    }

    .artwork-piece-3 {
        top: 20%;
        right: 8%;
        width: 90px;
        height: 110px;
        animation: floatArtwork3 20s ease-in-out infinite;
        animation-delay: 4s;
    }

    .artwork-piece-4 {
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
        font-size: clamp(3rem, 8vw, 8rem);
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
        flex-wrap: wrap;
    }

    .collection-counter {
        padding: 20px 40px;
        background: rgba(8, 6, 5, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.3);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        min-width: 180px;
        text-align: center;
    }

    .collection-counter:hover {
        transform: translateY(-5px) scale(1.05);
        border-color: rgba(212, 175, 55, 0.6);
        box-shadow:
            0 10px 30px rgba(212, 175, 55, 0.2),
            0 5px 15px rgba(0, 0, 0, 0.3);
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
        /* VECCHIO STILE - NON PIU' USATO */
        display: none;
    }

    .floating-artwork-particle {
        position: absolute;
        width: 40px;
        height: 50px;
        pointer-events: none;
        z-index: 5;
        opacity: 0.8;
    }

    .particle-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid rgba(212, 175, 55, 0.4);
        box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
    }

    /* Animations */
    @keyframes gridRotate {
        0% { transform: perspective(1000px) rotateX(45deg) translateX(0); }
        100% { transform: perspective(1000px) rotateX(45deg) translateX(-50%); }
    }

    @keyframes goldShimmer {
        0%, 100% { opacity: 0.15; }
        50% { opacity: 0.3; }
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

    /* Responsive */
    @media (max-width: 768px) {
        .collector-universe {
            height: 500px;
        }

        .collector-title-wrapper {
            height: 100px;
            margin-bottom: 20px;
        }

        .collector-title {
            font-size: clamp(2.2rem, 8vw, 3.5rem);
        }

        .collector-subtitle {
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .subtitle-container {
            height: 30px;
            margin-bottom: 25px;
        }

        .interactive-elements {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
            padding: 0 15px;
        }

        .collection-counter {
            min-width: 120px;
            max-width: 140px;
            padding: 12px 16px;
            flex: 0 1 auto;
        }

        .counter-label {
            font-size: 0.7rem;
            margin-bottom: 6px;
        }

        .counter-number {
            font-size: 1.4rem;
        }

        .gallery-grid {
            opacity: 0.6;
        }

        .gallery-artwork-image {
            opacity: 0.7;
        }

        .artwork-piece {        .artwork-frame {
            border-width: 1px;
        }

        .artwork-frame::before {
            border-width: 0.5px;
        }

        .content-overlay {
            padding: 0 10px;
        }
    }

    @media (max-width: 480px) {
        .collector-universe {
            height: 450px;
        }

        .collector-title-wrapper {
            height: 80px;
            margin-bottom: 15px;
        }

        .collector-title {
            font-size: clamp(1.8rem, 9vw, 2.8rem);
        }

        .collector-subtitle {
            font-size: 0.9rem;
            padding: 0 10px;
        }

        .subtitle-container {
            height: 25px;
            margin-bottom: 20px;
        }

        .content-overlay {
            width: 98%;
            padding: 0 5px;
        }

        .interactive-elements {
            gap: 8px;
            margin-top: 15px;
            padding: 0 10px;
        }

        .collection-counter {
            min-width: 100px;
            max-width: 110px;
            padding: 10px 12px;
        }

        .counter-label {
            font-size: 0.65rem;
            margin-bottom: 4px;
        }

        .counter-number {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 360px) {
        .collector-universe {
            height: 400px;
        }

        .collector-title {
            font-size: clamp(1.5rem, 10vw, 2.2rem);
        }

        .collector-title-wrapper {
            height: 70px;
            margin-bottom: 10px;
        }

        .interactive-elements {
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .collection-counter {
            min-width: 200px;
            max-width: 250px;
            padding: 8px 15px;
        }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
        .collection-counter:hover {
            transform: none;
        }

        .collection-counter:active {
            transform: translateY(-2px) scale(1.02);
            border-color: rgba(212, 175, 55, 0.7);
        }

        .artwork-piece {
            opacity: 0.15;
        }

        .artwork-frame {
            border-width: 1px;
        }

        .artwork-frame::before {
            border-width: 0.5px;
        }
    }

    /* High DPI displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .collector-title {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }
</style>

@php
    $floatingArtworksData = $floatingArtworks->map(function($artwork) {
        return [
            'id' => $artwork->id,
            'title' => $artwork->title,
            'avatar_url' => $artwork->avatar_image_url,
            'thumbnail_url' => $artwork->thumbnail_image_url
        ];
    })->toArray();
@endphp

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
    const totalReservations = {{ $totalReservations }};
    const subtitle = "{{ $subtitle }}";

    // Floating artworks data
    const floatingArtworks = @json($floatingArtworksData);

    // Device detection
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    // Three.js Scene Setup
    let scene, camera, renderer;
    let particles, frames;
    let mouseX = 0, mouseY = 0;
    let targetX = 0, targetY = 0;
    let lastTouch = { x: 0, y: 0 };

    function initThreeJS() {
        // Scene
        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x0a0908, 0.002);

        // Camera
        const containerHeight = isMobile ? (window.innerWidth < 480 ? 450 : 500) : 600;
        camera = new THREE.PerspectiveCamera(
            75,
            window.innerWidth / containerHeight,
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
            antialias: !isMobile, // Disable antialiasing on mobile for better performance
            powerPreference: 'high-performance'
        });
        renderer.setSize(window.innerWidth, containerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)); // Limit pixel ratio for better performance

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

        // Touch events for mobile
        if (isTouchDevice) {
            document.addEventListener('touchstart', onTouchStart, { passive: false });
            document.addEventListener('touchmove', onTouchMove, { passive: false });
            document.addEventListener('touchend', onTouchEnd, { passive: true });
        }

        // Start animation
        animate();
    }

    function createFloatingFrames() {
        frames = new THREE.Group();

        // Increase frames to replace golden cubes with artworks
        const frameCount = isMobile ? 20 : 40;

        for (let i = 0; i < frameCount; i++) {
            // Use PlaneGeometry instead of BoxGeometry for better texture display
            const geometry = new THREE.PlaneGeometry(1.2, 1.5);
            let material;

            // Use artwork texture if available
            if (floatingArtworks && floatingArtworks.length > 0) {
                const artwork = floatingArtworks[i % floatingArtworks.length];
                if (artwork && artwork.avatar_url) {
                    const textureLoader = new THREE.TextureLoader();

                    // Create material with artwork texture
                    material = new THREE.MeshBasicMaterial({
                        transparent: true,
                        opacity: 0.8,
                        side: THREE.DoubleSide
                    });

                    // Load texture
                    textureLoader.load(
                        artwork.avatar_url,
                        function(texture) {
                            material.map = texture;
                            material.needsUpdate = true;
                        },
                        undefined,
                        function(error) {
                            // Fallback to golden material on error
                            material.color.setHex(0xd4af37);
                        }
                    );
                } else {
                    // Fallback golden material
                    material = new THREE.MeshBasicMaterial({
                        color: 0xd4af37,
                        transparent: true,
                        opacity: 0.6,
                        side: THREE.DoubleSide
                    });
                }
            } else {
                // Fallback golden material
                material = new THREE.MeshBasicMaterial({
                    color: 0xd4af37,
                    transparent: true,
                    opacity: 0.6,
                    side: THREE.DoubleSide
                });
            }

            const frame = new THREE.Mesh(geometry, material);
            frame.position.x = (Math.random() - 0.5) * 100;
            frame.position.y = (Math.random() - 0.5) * 50;
            frame.position.z = (Math.random() - 0.5) * 50;
            frame.rotation.x = Math.random() * Math.PI;
            frame.rotation.y = Math.random() * Math.PI;
            frame.userData = {
                rotationSpeed: {
                    x: (Math.random() - 0.5) * (isMobile ? 0.005 : 0.01),
                    y: (Math.random() - 0.5) * (isMobile ? 0.005 : 0.01),
                    z: (Math.random() - 0.5) * (isMobile ? 0.005 : 0.01)
                },
                floatSpeed: Math.random() * 0.5 + 0.5,
                floatOffset: Math.random() * Math.PI * 2
            };
            frames.add(frame);
        }

        scene.add(frames);
    }

    function createParticleSystem() {
        // SOSTITUISCO LE PARTICELLE DORATE CON IMMAGINI DELLE OPERE
        // Non creo più particelle Three.js, uso elementi DOM con immagini

        const container = document.getElementById(`particlesOverlay-${componentId}`);
        if (!container) return;

        // Numero di opere volanti
        const particleCount = isMobile ? 30 : 60;

        for (let i = 0; i < particleCount; i++) {
            // Creo un div per ogni opera
            const particle = document.createElement('div');
            particle.className = 'floating-artwork-particle';

            // Posizione iniziale casuale
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';

            // Aggiungo l'immagine dell'opera
            if (floatingArtworks && floatingArtworks.length > 0) {
                const artwork = floatingArtworks[i % floatingArtworks.length];
                if (artwork && artwork.avatar_url) {
                    const img = document.createElement('img');
                    img.src = artwork.avatar_url;
                    img.alt = artwork.title || 'Opera';
                    img.className = 'particle-image';
                    particle.appendChild(img);
                }
            }

            container.appendChild(particle);

            // Animazione con GSAP
            if (typeof gsap !== 'undefined') {
                gsap.set(particle, {
                    rotation: Math.random() * 360,
                    scale: 0.3 + Math.random() * 0.4
                });

                gsap.to(particle, {
                    y: -200,
                    x: (Math.random() - 0.5) * 300,
                    rotation: '+=360',
                    opacity: 0,
                    duration: Math.random() * 8 + 6,
                    repeat: -1,
                    delay: Math.random() * 8,
                    ease: "power1.out"
                });
            }
        }
    }

    function onMouseMove(event) {
        const containerHeight = isMobile ? (window.innerWidth < 480 ? 450 : 500) : 600;
        mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        mouseY = -(event.clientY / containerHeight) * 2 + 1;
    }

    function onTouchStart(event) {
        if (event.touches.length === 1) {
            lastTouch.x = event.touches[0].clientX;
            lastTouch.y = event.touches[0].clientY;
        }
    }

    function onTouchMove(event) {
        event.preventDefault();
        if (event.touches.length === 1) {
            const touch = event.touches[0];
            const containerHeight = isMobile ? (window.innerWidth < 480 ? 450 : 500) : 600;
            mouseX = (touch.clientX / window.innerWidth) * 2 - 1;
            mouseY = -(touch.clientY / containerHeight) * 2 + 1;

            lastTouch.x = touch.clientX;
            lastTouch.y = touch.clientY;
        }
    }

    function onTouchEnd(event) {
        // Mantieni l'ultima posizione per continuità
    }

    function animate() {
        requestAnimationFrame(animate);

        // Smooth mouse follow
        targetX += (mouseX - targetX) * 0.05;
        targetY += (mouseY - targetY) * 0.05;

        // Animate frames (Three.js frames with artwork textures)
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

        gsap.to(`#reservationCounter-${componentId}`, {
            innerHTML: totalReservations,
            duration: 3,
            ease: "power2.out",
            delay: 4,
            snap: { innerHTML: 1 },
            onUpdate: function() {
                const element = document.getElementById(`reservationCounter-${componentId}`);
                if (element) {
                    const value = Math.floor(this.targets()[0].innerHTML);
                    element.innerHTML = '€' + value.toLocaleString();
                }
            }
        });
    }

    // Create DOM particles with artwork images - DISABLED
    function createDOMParticles() {
        // Disabled - we only want the floating artworks and Three.js frames
        return;
    }

    createDOMParticles();

    // Enhanced interaction for collection counters
    const counters = document.querySelectorAll('.collection-counter');
    counters.forEach(counter => {
        if (isTouchDevice) {
            counter.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
                this.style.borderColor = 'rgba(212, 175, 55, 0.7)';
            });

            counter.addEventListener('touchend', function() {
                this.style.transform = '';
                this.style.borderColor = '';
            });
        } else {
            counter.addEventListener('mouseenter', function() {
                if (typeof gsap !== 'undefined') {
                    gsap.to(this, {
                        y: -5,
                        scale: 1.05,
                        duration: 0.3,
                        ease: "power2.out"
                    });
                }
            });

            counter.addEventListener('mouseleave', function() {
                if (typeof gsap !== 'undefined') {
                    gsap.to(this, {
                        y: 0,
                        scale: 1,
                        duration: 0.3,
                        ease: "power2.out"
                    });
                }
            });
        }
    });

    // Resize handler
    window.addEventListener('resize', () => {
        if (camera && renderer) {
            const containerHeight = isMobile ? (window.innerWidth < 480 ? 450 : 500) : 600;
            camera.aspect = window.innerWidth / containerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, containerHeight);
        }
    });
});
</script>
@endpush
@endonce
