{{-- 
    resources/views/components/egi-prism-3d.blade.php
    Standalone 3D Prism viewer component for EGI cards
--}}

@props(['egi', 'isCreatorOrOwner' => false])

@php
    use App\Helpers\FegiAuth;

    $egiId = $egi->id;
    $prismConfig = $egi->prism_config ?? [];
    $displayMode = $egi->display_mode ?? '2d';

    // Image URL
    $imageUrl = "/users_files/collections_{$egi->collection_id}/creator_{$egi->user_id}/{$egi->id}_thumbnail.webp";

    // EGI Info for back panel
    $egiInfo = [
        'id' => $egi->id,
        'title' => $egi->title ?? 'EGI #' . $egi->id,
        'collection' => $egi->collection->collection_name ?? 'Collection',
        'description' => $egi->description ?? '',
    ];
@endphp

<div class="egi-prism-container" id="prism-container-{{ $egiId }}" data-egi-id="{{ $egiId }}"
    data-image-url="{{ $imageUrl }}" data-egi-info="{{ json_encode($egiInfo) }}"
    data-prism-config="{{ json_encode($prismConfig) }}">

    <canvas class="egi-prism-canvas" id="prism-canvas-{{ $egiId }}"></canvas>

    {{-- Loading indicator --}}
    <div class="egi-prism-loading" id="prism-loading-{{ $egiId }}">
        <i class="fas fa-spinner fa-spin"></i>
    </div>

    {{-- Enlarge Button (Visible to everyone) --}}
    <button class="egi-prism-enlarge-btn" onclick="openPrismViewer({{ $egiId }})" title="Ingrandisci">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
        </svg>
    </button>

    {{-- Settings button (only for creator/owner) --}}
    @if ($isCreatorOrOwner)
        <button class="egi-prism-settings-btn" onclick="openPrismSettings({{ $egiId }})" title="Impostazioni 3D">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    @endif
</div>

{{-- Settings Modal (only rendered once per page) --}}
@once
    {{-- Viewer Modal Responsive Styles --}}
    <style>
        #prism-viewer-dialog {
            width: 95vw;
            max-width: 900px;
            max-height: 90vh;
        }

        #prism-viewer-body {
            height: 50vh;
            /* Responsive height based on viewport */
            min-height: 250px;
            max-height: 500px;
        }

        #viewer-info-section {
            max-height: 30vh;
            min-height: 80px;
        }

        @media (max-width: 768px) {

            /* Hide enlarge button on mobile - modal not useful on small screens */
            .egi-prism-enlarge-btn {
                display: none !important;
            }

            #prism-viewer-dialog {
                width: 100vw;
                max-width: 100vw;
                max-height: 100vh;
                border-radius: 0;
                margin: 0;
            }

            #prism-viewer-body {
                height: 55vh;
                min-height: 200px;
            }

            #viewer-info-section {
                max-height: 35vh;
                padding: 16px;
            }

            #viewer-title {
                font-size: 1rem !important;
            }

            #viewer-description {
                font-size: 0.85rem !important;
            }
        }
    </style>

    {{-- Viewer Modal: Elegant Centered Dialog --}}
    <div id="prism-viewer-overlay"
        style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        z-index: 999999;
        display: none;
        justify-content: center;
        align-items: center;
    ">
        {{-- Centered Modal Dialog --}}
        <div id="prism-viewer-dialog"
            style="
            background: linear-gradient(180deg, #1a1a2e 0%, #0f0f1a 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.6);
            width: 95vw;
            max-width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        ">
            {{-- Close Button --}}
            <button id="prism-viewer-close" onclick="closePrismViewer()"
                style="
                position: absolute;
                top: 12px;
                right: 12px;
                z-index: 10;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                font-size: 18px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            ">×</button>

            {{-- Canvas Container --}}
            <div id="prism-viewer-body"
                style="
                width: 100%;
                height: 450px;
                background: radial-gradient(circle at center, #1a1a2e 0%, #0a0a15 100%);
                position: relative;
                overflow: hidden;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            ">
                {{-- Canvas will be moved here by JS --}}
                {{-- Rotation Toggle Button --}}
                <button id="viewer-rotation-btn" onclick="toggleViewerRotation()"
                    style="
                    position: absolute;
                    bottom: 12px;
                    right: 12px;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.15);
                    border: 1px solid rgba(255, 255, 255, 0.25);
                    color: white;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 5;
                "
                    title="Toggle Rotation">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            {{-- Info Section --}}
            <div id="viewer-info-section"
                style="
                padding: 20px;
                color: white;
                overflow-y: auto;
                max-height: 200px;
            ">
                <h4 id="viewer-title"
                    style="
                    margin: 0 0 12px 0;
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #ffaa00;
                    letter-spacing: 0.5px;
                ">
                    Title</h4>
                <p id="viewer-description"
                    style="
                    margin: 0;
                    font-size: 0.9rem;
                    line-height: 1.6;
                    color: rgba(255, 255, 255, 0.85);
                ">
                    Description...</p>
            </div>
        </div>
    </div>

    <div class="egi-prism-modal-overlay" id="prism-settings-modal">
        <div class="egi-prism-modal">
            <div class="egi-prism-modal-header">
                <h3><i class="fas fa-cube"></i> Impostazioni Prisma 3D</h3>
                <button class="egi-prism-modal-close" onclick="closePrismSettings()">×</button>
            </div>

            <div class="egi-prism-modal-content-grid">
                {{-- Left Column: Controls --}}
                <div class="egi-prism-settings-panel">
                    <input type="hidden" id="prism-settings-egi-id" value="">
                    <input type="hidden" id="prism-settings-egi-image" value="">

                    {{-- Glass Material --}}
                    <div class="prism-section-title">
                        <i class="fas fa-glass-whiskey"></i> Materiale Vetro
                    </div>

                    <div class="prism-control-group">
                        <label>Colore Vetro</label>
                        <div class="prism-control-row">
                            <input type="color" id="prism-glass-color" value="#ffdd44">
                            <span class="prism-control-value" id="prism-glass-color-val">#ffdd44</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Opacità</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-opacity" min="0" max="1" step="0.01"
                                value="0.7">
                            <span class="prism-control-value" id="prism-opacity-val">0.70</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Metallicità</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-metalness" min="0" max="1" step="0.01"
                                value="0.9">
                            <span class="prism-control-value" id="prism-metalness-val">0.90</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Ruvidità</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-roughness" min="0" max="1" step="0.01"
                                value="0.05">
                            <span class="prism-control-value" id="prism-roughness-val">0.05</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Trasmissione</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-transmission" min="0" max="1" step="0.01"
                                value="1.0">
                            <span class="prism-control-value" id="prism-transmission-val">1.00</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>IOR (Rifrazione)</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-ior" min="1" max="2.33" step="0.01"
                                value="1.5">
                            <span class="prism-control-value" id="prism-ior-val">1.50</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Spessore (Thickness)</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-thickness" min="0" max="5" step="0.1"
                                value="0.5">
                            <span class="prism-control-value" id="prism-thickness-val">0.5</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Clearcoat</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-clearcoat" min="0" max="1" step="0.01"
                                value="1.0">
                            <span class="prism-control-value" id="prism-clearcoat-val">1.00</span>
                        </div>
                    </div>

                    {{-- Edges Settings --}}
                    <div class="prism-section-title">
                        <i class="fas fa-vector-square"></i> Bordi / Edges
                    </div>

                    <div class="prism-control-group">
                        <label> <input type="checkbox" id="prism-show-edges"> Mostra Bordi </label>
                    </div>

                    <div class="prism-control-group">
                        <label>Colore Bordi</label>
                        <div class="prism-control-row">
                            <input type="color" id="prism-edge-color" value="#ffffff">
                            <span class="prism-control-value" id="prism-edge-color-val">#ffffff</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Opacità Bordi</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-edge-opacity" min="0" max="1" step="0.01"
                                value="0.05">
                            <span class="prism-control-value" id="prism-edge-opacity-val">0.05</span>
                        </div>
                    </div>

                    {{-- Bloom Settings --}}
                    <div class="prism-section-title">
                        <i class="fas fa-star"></i> Bloom / Glow
                    </div>

                    <div class="prism-control-group">
                        <label>Intensità</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-bloom-strength" min="0" max="3"
                                step="0.1" value="0">
                            <span class="prism-control-value" id="prism-bloom-strength-val">0</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Raggio</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-bloom-radius" min="0" max="2" step="0.1"
                                value="0.4">
                            <span class="prism-control-value" id="prism-bloom-radius-val">0.4</span>
                        </div>
                    </div>

                    <div class="prism-control-group">
                        <label>Soglia (Threshold)</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-bloom-threshold" min="0" max="1"
                                step="0.01" value="0.85">
                            <span class="prism-control-value" id="prism-bloom-threshold-val">0.85</span>
                        </div>
                    </div>

                    {{-- Dimensions & Rotation --}}
                    <div class="prism-section-title">
                        <i class="fas fa-sync-alt"></i> Rotazione & Extra
                    </div>

                    <div class="prism-control-group">
                        <label> <input type="checkbox" id="prism-auto-rotate" checked> Rotazione Automatica </label>
                    </div>

                    <div class="prism-control-group">
                        <label>Velocità Rotazione</label>
                        <div class="prism-control-row">
                            <input type="range" id="prism-rotation-speed" min="0.001" max="0.02"
                                step="0.001" value="0.02">
                            <span class="prism-control-value" id="prism-rotation-speed-val">0.02</span>
                        </div>
                    </div>

                    {{-- Presets --}}
                    <div class="prism-section-title">
                        <i class="fas fa-magic"></i> Preset Stili
                    </div>
                    <div class="prism-control-group prism-full-width">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(70px, 1fr)); gap: 8px;">
                            <button class="prism-preset-btn" onclick="applyPrismPreset('standard')">⭐ Default</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('crystal')">💎 Crystal</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('neon')">🌈 Neon</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('ice')">❄️ Ice</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('gold')">🏆 Gold</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('hologram')">📡 Holo</button>
                            <button class="prism-preset-btn" onclick="applyPrismPreset('ember')">🔥 Ember</button>
                        </div>
                    </div>

                    <div class="prism-full-width" style="display: flex; gap: 10px; margin-top: 20px;">
                        <button class="prism-save-btn" id="prism-save-btn" style="flex: 1;">
                            <i class="fas fa-save"></i> Salva
                        </button>
                        <button class="prism-save-btn" id="prism-save-collection-btn"
                            style="flex: 1; background: linear-gradient(135deg, #FF9800 0%, #F44336 100%);">
                            <i class="fas fa-layer-group"></i> Applica a Tutti
                        </button>
                    </div>

                    <div id="prism-save-status" class="prism-full-width"
                        style="text-align: center; margin-top: 10px; visibility: hidden; height: 1.2em;"></div>
                </div>

                {{-- Right Column: Preview --}}
                <div class="egi-prism-preview-panel">
                    <span class="egi-prism-preview-label">LIVE PREVIEW</span>
                    <div id="prism-preview-container" style="width: 100%; height: 100%;" data-image="">
                        <canvas></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endonce

@once
    <link rel="stylesheet" href="{{ asset('css/egi-prism-3d.css') }}?v=2">
    {{-- Import map MUST be declared before module scripts --}}
    <script type="importmap">
        {"imports": {"three": "https://unpkg.com/three@0.160.0/build/three.module.js", "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"}}
    </script>
@endonce

{{-- Initialize script --}}
<script type="module">
    import {
        EgiPrism3D
    } from '/js/egi-prism-3d.js?v=5';

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('prism-container-{{ $egiId }}');
        if (!container) return;

        const config = JSON.parse(container.dataset.prismConfig || '{}');
        const egiInfo = JSON.parse(container.dataset.egiInfo || '{}');
        const imageUrl = container.dataset.imageUrl;

        const prism = new EgiPrism3D(container, config);
        prism.loadImage(imageUrl, egiInfo);

        // Store reference
        window.egiPrisms = window.egiPrisms || {};
        window.egiPrisms[{{ $egiId }}] = prism;

        // Hide loading
        const loading = document.getElementById('prism-loading-{{ $egiId }}');
        if (loading) loading.style.display = 'none';
    });
</script>

{{-- Settings modal scripts (once) --}}
@once
    <script>
        // === GLOBAL HELPERS ===

        // VIEW MODAL HELPERS
        window.openPrismViewer = function(egiId) {
            // Skip modal on mobile - not useful since it wouldn't be larger than the EGI card
            if (window.innerWidth < 768) {
                return;
            }

            const originalContainer = document.getElementById(`prism-container-${egiId}`);
            const overlay = document.getElementById('prism-viewer-overlay');
            const canvasContainer = document.getElementById('prism-viewer-body');
            if (!originalContainer || !overlay || !canvasContainer) return;

            // CRITICAL: Move overlay to document body so it's a true full-page modal
            if (overlay.parentNode !== document.body) {
                document.body.appendChild(overlay);
            }

            // Find Canvas
            const canvas = originalContainer.querySelector('canvas');
            if (!canvas) return;

            // Move canvas to modal
            canvasContainer.insertBefore(canvas, canvasContainer.firstChild);

            // Update instance container ref
            if (window.egiPrisms && window.egiPrisms[egiId]) {
                window.egiPrisms[egiId].container = canvasContainer;

                // Store original parent ID on the canvas for return
                canvas.dataset.originalParent = `prism-container-${egiId}`;

                // Show Overlay
                overlay.style.display = 'flex';

                // Populate Description
                const titleEl = document.getElementById('viewer-title');
                const descEl = document.getElementById('viewer-description');

                // Parse Info
                let info = {};
                try {
                    info = JSON.parse(originalContainer.dataset.egiInfo || '{}');
                } catch (e) {}

                if (titleEl) titleEl.textContent = info.title || 'EGI';
                if (descEl) {
                    descEl.textContent = info.description || '';
                    descEl.style.display = (info.description) ? 'block' : 'none';
                }

                // Resize canvas for new container - multiple attempts for reliable sizing
                setTimeout(() => {
                    if (window.egiPrisms[egiId]) {
                        window.egiPrisms[egiId].resize();
                        // Second resize after layout settles
                        setTimeout(() => {
                            if (window.egiPrisms[egiId]) window.egiPrisms[egiId].resize();
                        }, 100);
                    }
                }, 50);
            }
        };

        window.toggleViewerRotation = function() {
            // Find active canvas
            const modalBody = document.getElementById('prism-viewer-body');
            const canvas = modalBody.querySelector('canvas');
            if (!canvas || !canvas.dataset.originalParent) return;

            const egiId = canvas.dataset.originalParent.replace('prism-container-', '');
            const prism = window.egiPrisms && window.egiPrisms[egiId];

            if (prism) {
                // Toggle Config
                const newState = !prism.config.autoRotate;
                prism.updateConfig({
                    autoRotate: newState
                });

                // Update Button UI
                const btn = document.getElementById('viewer-rotation-btn');
                if (btn) {
                    if (newState) btn.classList.add('active');
                    else btn.classList.remove('active');
                }
            }
        };

        window.closePrismViewer = function() {
            const overlay = document.getElementById('prism-viewer-overlay');
            const canvasContainer = document.getElementById('prism-viewer-body');
            if (!overlay) return;

            // Find canvas in modal
            const canvas = canvasContainer ? canvasContainer.querySelector('canvas') : null;
            if (canvas && canvas.dataset.originalParent) {
                const originalParent = document.getElementById(canvas.dataset.originalParent);
                if (originalParent) {
                    // Move canvas back to original container
                    originalParent.insertBefore(canvas, originalParent.firstChild);

                    // Reset style
                    canvas.style.width = '100%';
                    canvas.style.height = '100%';

                    // Update Instance
                    const egiId = canvas.dataset.originalParent.replace('prism-container-', '');
                    if (window.egiPrisms && window.egiPrisms[egiId]) {
                        window.egiPrisms[egiId].container = originalParent;
                        window.egiPrisms[egiId].resize();
                    }
                }
            }

            // Hide Overlay
            overlay.style.display = 'none';
        };

        // Helper to update DOM inputs silently or with event
        window.setControlValue = function(id, val) {
            const el = document.getElementById(id);
            if (!el) return;

            if (el.type === 'checkbox') {
                el.checked = !!val;
            } else {
                el.value = val !== undefined ? val : '';
            }
            // Dispatch input event to trigger preview update
            el.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        };

        // 1. OPEN SETTINGS: Populate Inputs from Data Attribute (DB)
        window.openPrismSettings = function(egiId) {
            const container = document.getElementById(`prism-container-${egiId}`);
            const modal = document.querySelector('.egi-prism-modal-overlay');
            if (!container || !modal) return;

            // Move modal to body to fix stacking
            document.body.appendChild(modal);

            // Read Config from Data Attribute (Source of Truth)
            let config = {};
            try {
                config = JSON.parse(container.dataset.prismConfig || '{}');
            } catch (e) {
                console.error("Invalid prism config JSON", e);
            }

            // Defaults
            const defaults = {
                glassColor: '#ffdd44',
                opacity: 0.7,
                metalness: 0.05,
                roughness: 0.02,
                transmission: 1.0,
                ior: 1.5,
                thickness: 0.5,
                clearcoat: 0.8,
                showEdges: false,
                edgeColor: '#ffffff',
                edgeOpacity: 0.05,
                bloomStrength: 0,
                bloomRadius: 0.2,
                bloomThreshold: 0.85,
                autoRotate: true,
                rotationSpeed: 0.02
            };

            const merged = {
                ...defaults,
                ...config
            };

            // POPULATE DOM INPUTS
            // Use setControlValue but suppress event if we want to avoid massive updates?
            // No, we want to update the Preview immediately.

            const setVal = (id, v) => {
                const el = document.getElementById(id);
                if (el) {
                    if (el.type === 'checkbox') el.checked = !!v;
                    else el.value = v;
                    // Manual Span update
                    const span = document.getElementById(id + '-val');
                    if (span) span.textContent = v;
                }
            };

            setVal('prism-glass-color', merged.glassColor);
            setVal('prism-opacity', merged.opacity);
            setVal('prism-metalness', merged.metalness);
            setVal('prism-roughness', merged.roughness);
            setVal('prism-transmission', merged.transmission);
            setVal('prism-ior', merged.ior);
            setVal('prism-thickness', merged.thickness);
            setVal('prism-clearcoat', merged.clearcoat);

            setVal('prism-show-edges', merged.showEdges);
            setVal('prism-edge-color', merged.edgeColor);
            setVal('prism-edge-opacity', merged.edgeOpacity);

            setVal('prism-bloom-strength', merged.bloomStrength);
            setVal('prism-bloom-radius', merged.bloomRadius);
            setVal('prism-bloom-threshold', merged.bloomThreshold);

            setVal('prism-auto-rotate', merged.autoRotate);
            setVal('prism-rotation-speed', merged.rotationSpeed);

            // Store Target EGI ID
            document.getElementById('prism-settings-egi-id').value = egiId;

            // Show Modal
            modal.classList.add('active');

            // Initialize Preview Prism
            if (window.initPreviewPrism) {
                window.initPreviewPrism(container.dataset.imageUrl, merged);
            }
        };

        window.closePrismSettings = function() {
            const modal = document.querySelector('.egi-prism-modal-overlay');
            if (modal) modal.classList.remove('active');
        };

        // 2. SAVE CONFIG: Read DIRECTLY from DOM Inputs (Source of Truth)
        window.savePrismConfig = async function(isCollection, btn) {
            const egiId = document.getElementById('prism-settings-egi-id').value;
            if (!egiId) return;

            // DOM READING HELPERS
            const getVal = (id) => document.getElementById(id)?.value;
            const getFloat = (id) => parseFloat(document.getElementById(id)?.value || 0);
            const getCheck = (id) => document.getElementById(id)?.checked || false;

            // BUILD CONFIG OBJECT STRICTLY FROM INPUTS
            const bloomStrength = getFloat('prism-bloom-strength');

            const configToSave = {
                glassColor: getVal('prism-glass-color'),
                opacity: getFloat('prism-opacity'),
                metalness: getFloat('prism-metalness'),
                roughness: getFloat('prism-roughness'),
                transmission: getFloat('prism-transmission'),
                ior: getFloat('prism-ior'),
                thickness: getFloat('prism-thickness'),
                clearcoat: getFloat('prism-clearcoat'),

                showEdges: getCheck('prism-show-edges'),
                edgeColor: getVal('prism-edge-color'),
                edgeOpacity: getFloat('prism-edge-opacity'),

                bloomStrength: bloomStrength,
                bloomRadius: getFloat('prism-bloom-radius'),
                bloomThreshold: getFloat('prism-bloom-threshold'),

                // CRITICAL: LINK EMISSIVE TO BLOOM FOR VISUAL CONSISTENCY
                emissiveIntensity: bloomStrength,

                autoRotate: getCheck('prism-auto-rotate'),
                rotationSpeed: getFloat('prism-rotation-speed')
            };

            // UI Feedback
            const statusEl = document.getElementById('prism-save-status');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
            statusEl.style.visibility = 'visible';
            statusEl.textContent = 'Salvataggio...';
            statusEl.style.color = '#fff';

            const url = isCollection ? `/api/egi/${egiId}/prism-config/collection` :
                `/api/egi/${egiId}/prism-config`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        prism_config: configToSave
                    })
                });

                const data = await response.json();

                if (data.success) {
                    statusEl.textContent = 'Salvato!';
                    statusEl.style.color = '#4caf50';
                    setTimeout(() => statusEl.textContent = '', 4000);

                    // Update Local Data Attribute so refreshing page isn't needed immediately
                    const container = document.getElementById(`prism-container-${egiId}`);
                    if (container) {
                        container.dataset.prismConfig = JSON.stringify(configToSave);
                        // Reload page is nice for full sync, but maybe intrusive. 
                        // User can refresh manually.
                    }

                } else {
                    throw new Error(data.error || 'Errore');
                }
            } catch (error) {
                console.error(error);
                statusEl.textContent = 'Errore';
                statusEl.style.color = '#f44336';
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        };

        // 3. LISTENERS: Bind Inputs to Preview (Visual Feedback Only)
        function initPrismListeners() {
            if (window.prismListenersInitialized) return;

            const updatePreview = () => {
                if (!window.previewPrism) return;

                // Read inputs
                const getVal = (id) => document.getElementById(id)?.value;
                const getFloat = (id) => parseFloat(document.getElementById(id)?.value || 0);
                const getCheck = (id) => document.getElementById(id)?.checked || false;

                const bloomStrength = getFloat('prism-bloom-strength');

                const currentConfig = {
                    glassColor: getVal('prism-glass-color'),
                    opacity: getFloat('prism-opacity'),
                    metalness: getFloat('prism-metalness'),
                    roughness: getFloat('prism-roughness'),
                    transmission: getFloat('prism-transmission'),
                    ior: getFloat('prism-ior'),
                    thickness: getFloat('prism-thickness'),
                    clearcoat: getFloat('prism-clearcoat'),
                    showEdges: getCheck('prism-show-edges'),
                    edgeColor: getVal('prism-edge-color'),
                    edgeOpacity: getFloat('prism-edge-opacity'),
                    bloomStrength: bloomStrength,
                    bloomRadius: getFloat('prism-bloom-radius'),
                    bloomThreshold: getFloat('prism-bloom-threshold'),
                    emissiveIntensity: bloomStrength, // Link
                    autoRotate: getCheck('prism-auto-rotate'),
                    rotationSpeed: getFloat('prism-rotation-speed')
                };

                window.previewPrism.updateConfig(currentConfig);

                // Update ID SPANS
                const updateSpan = (id, val) => {
                    const sp = document.getElementById(id + '-val');
                    if (sp) sp.textContent = val;
                };
                updateSpan('prism-glass-color', currentConfig.glassColor);
                updateSpan('prism-opacity', currentConfig.opacity);
                updateSpan('prism-metalness', currentConfig.metalness);
                updateSpan('prism-roughness', currentConfig.roughness);
                updateSpan('prism-transmission', currentConfig.transmission);
                updateSpan('prism-ior', currentConfig.ior);
                updateSpan('prism-thickness', currentConfig.thickness);
                updateSpan('prism-clearcoat', currentConfig.clearcoat);
                updateSpan('prism-edge-color', currentConfig.edgeColor);
                updateSpan('prism-edge-opacity', currentConfig.edgeOpacity);
                updateSpan('prism-bloom-strength', currentConfig.bloomStrength);
                updateSpan('prism-bloom-radius', currentConfig.bloomRadius);
                updateSpan('prism-bloom-threshold', currentConfig.bloomThreshold);
                updateSpan('prism-rotation-speed', currentConfig.rotationSpeed);
            };

            const inputs = document.querySelectorAll('.egi-prism-modal input');
            inputs.forEach(input => {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            // Save Buttons
            document.getElementById('prism-save-btn')?.addEventListener('click', function() {
                savePrismConfig(false, this);
            });
            document.getElementById('prism-save-collection-btn')?.addEventListener('click', function() {
                savePrismConfig(true, this);
            });

            // Close
            document.getElementById('prism-settings-modal')?.addEventListener('click', function(e) {
                if (e.target === this) closePrismSettings();
            });

            window.prismListenersInitialized = true;
        }

        // Draggable
        (function initDraggableModal() {
            const modal = document.querySelector('.egi-prism-modal');
            const header = document.querySelector('.egi-prism-modal-header');
            if (!modal || !header) return;
            let isDragging = false,
                startX, startY, initialX, initialY;
            header.style.cursor = 'grab';
            header.addEventListener('mousedown', function(e) {
                if (e.target.closest('.egi-prism-modal-close')) return;
                isDragging = true;
                header.style.cursor = 'grabbing';
                startX = e.clientX - modal.offsetLeft;
                startY = e.clientY - modal.offsetTop;
            });
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                e.preventDefault();
                modal.style.left = (e.clientX - startX) + 'px';
                modal.style.top = (e.clientY - startY) + 'px';
                modal.style.transform = 'none';
                modal.style.margin = '0';
            });
            document.addEventListener('mouseup', () => {
                isDragging = false;
                header.style.cursor = 'grab';
            });
        })
        ();

        // BOOTSTRAP
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPrismListeners);
        } else {
            initPrismListeners();
        }

        // PREVIEW INIT
        window.initPreviewPrism = function(imageUrl, config) {
            const container = document.getElementById('prism-preview-container'); // Correct ID
            if (!container) return;

            if (window.previewPrism) {
                window.previewPrism.updateConfig(config);
                window.previewPrism.loadImage(imageUrl);
            } else if (window.EgiPrism3D) {
                window.previewPrism = new window.EgiPrism3D(container, config);
                window.previewPrism.loadImage(imageUrl);
            }
        };

        const prismPresets = {
            standard: {
                glassColor: '#ffdd44',
                opacity: 0.7,
                metalness: 0.05,
                roughness: 0.02,
                transmission: 1.0,
                ior: 1.5,
                thickness: 0.5,
                clearcoat: 0.8,
                showEdges: false,
                edgeColor: '#ffffff',
                edgeOpacity: 0.05,
                emissiveIntensity: 0,
                bloomStrength: 0,
                bloomRadius: 0.2,
                bloomThreshold: 0.85
            },
            crystal: {
                glassColor: '#88ccff',
                opacity: 0.15,
                roughness: 0.05,
                transmission: 0.95,
                ior: 1.5,
                thickness: 0.5,
                clearcoat: 1.0,
                showEdges: true,
                edgeColor: '#ffffff',
                edgeOpacity: 0.4,
                emissiveIntensity: 0.6,
                bloomStrength: 1.2,
                bloomRadius: 0.5,
                bloomThreshold: 0.2
            },
            neon: {
                glassColor: '#ff00ff',
                opacity: 0.2,
                metalness: 0.7,
                roughness: 0.1,
                transmission: 0.8,
                ior: 1.8,
                thickness: 0.3,
                clearcoat: 1.0,
                showEdges: true,
                edgeColor: '#ff00ff',
                edgeOpacity: 0.8,
                emissiveIntensity: 1.0,
                bloomStrength: 2.0,
                bloomRadius: 0.8,
                bloomThreshold: 0.1
            },
            ice: {
                glassColor: '#aaddff',
                opacity: 0.1,
                metalness: 0.3,
                roughness: 0.2,
                transmission: 0.98,
                ior: 1.31,
                thickness: 0.8,
                clearcoat: 0.8,
                showEdges: true,
                edgeColor: '#aaddff',
                edgeOpacity: 0.3,
                emissiveIntensity: 0.5,
                bloomStrength: 0.8,
                bloomRadius: 0.3,
                bloomThreshold: 0.3
            },
            gold: {
                glassColor: '#ffdd44',
                opacity: 0.3,
                metalness: 1.0,
                roughness: 0.15,
                transmission: 0.6,
                ior: 1.45,
                thickness: 0.4,
                clearcoat: 1.0,
                showEdges: true,
                edgeColor: '#ffaa00',
                edgeOpacity: 0.6,
                emissiveIntensity: 0.6,
                bloomStrength: 1.0,
                bloomRadius: 0.4,
                bloomThreshold: 0.25
            },
            hologram: {
                glassColor: '#00ffaa',
                opacity: 0.05,
                metalness: 0.5,
                roughness: 0.0,
                transmission: 0.99,
                ior: 2.0,
                thickness: 0.2,
                clearcoat: 1.0,
                showEdges: true,
                edgeColor: '#00ffcc',
                edgeOpacity: 0.9,
                emissiveIntensity: 0.8,
                bloomStrength: 1.5,
                bloomRadius: 1.0,
                bloomThreshold: 0.05
            },
            ember: {
                glassColor: '#ff4400',
                opacity: 0.25,
                metalness: 0.8,
                roughness: 0.2,
                transmission: 0.9,
                ior: 1.6,
                thickness: 0.5,
                clearcoat: 1.0,
                showEdges: true,
                edgeColor: '#ff2200',
                edgeOpacity: 0.7,
                emissiveIntensity: 0.7,
                bloomStrength: 1.4,
                bloomRadius: 0.6,
                bloomThreshold: 0.4
            }
        };

        window.applyPrismPreset = function(name) {
            const preset = prismPresets[name];
            if (!preset) return;

            window.setControlValue('prism-glass-color', preset.glassColor);
            window.setControlValue('prism-opacity', preset.opacity);
            window.setControlValue('prism-metalness', preset.metalness);
            window.setControlValue('prism-roughness', preset.roughness);
            window.setControlValue('prism-transmission', preset.transmission);
            window.setControlValue('prism-ior', preset.ior);
            window.setControlValue('prism-thickness', preset.thickness);
            window.setControlValue('prism-clearcoat', preset.clearcoat);

            window.setControlValue('prism-show-edges', preset.showEdges);
            window.setControlValue('prism-edge-color', preset.edgeColor);
            window.setControlValue('prism-edge-opacity', preset.edgeOpacity);

            window.setControlValue('prism-bloom-strength', preset.bloomStrength);
            window.setControlValue('prism-bloom-radius', preset.bloomRadius);
            window.setControlValue('prism-bloom-threshold', preset.bloomThreshold);
        };
    </script>
@endonce
