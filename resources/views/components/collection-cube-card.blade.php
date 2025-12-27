{{-- resources/views/components/collection-cube-card.blade.php --}}
{{--
    Collection Cube Card Component
    
    Displays a 3D cube with EGI images on 6 faces.
    Includes management modal for customization.
    
    Props:
    - collection: Collection model (with egis relation loaded)
    - showManageButton: bool - Show gear button for management modal (default: true)
--}}

@props(['collection', 'showManageButton' => true])

@php
    $cubeId = 'cube-' . ($collection->id ?? uniqid());
    $egis = $collection->egis ?? collect();
    $logo = config('app.logo');

    // Get first 6 EGI images for cube faces
    $faceImages = [];
    foreach ($egis->take(6) as $egi) {
        $faceImages[] = "/users_files/collections_{$egi->collection_id}/creator_{$egi->user_id}/{$egi->id}_thumbnail.webp";
    }
    // Fill remaining faces with null
    while (count($faceImages) < 6) {
        $faceImages[] = null;
    }

    // Collection info
    $collectionName = $collection->collection_name ?? 'Collection';
    $creatorName = $collection->creator->name ?? 'Unknown Creator';
    $egiCount = $egis->count();
@endphp

{{-- Load CSS once --}}
@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/collection-cube.css') }}?v={{ time() }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @endpush
@endonce

{{-- Cube Card --}}
<div class="collection-cube-card" id="{{ $cubeId }}-card" data-collection-id="{{ $collection->id ?? '' }}"
    onclick="window.location.href='{{ route('home.collections.show', $collection->id ?? 0) }}'">

    {{-- 3D Canvas Container --}}
    <div class="collection-cube-canvas" id="{{ $cubeId }}-container"></div>

    {{-- Info Overlay --}}
    <div class="collection-cube-info">
        <h3 class="collection-cube-title">{{ $collectionName }}</h3>
        <p class="collection-cube-creator">{{ __('by') }} {{ $creatorName }}</p>
        <span class="collection-cube-count">
            {{ $egiCount }} {{ trans_choice('EGI|EGIs', $egiCount) }}
        </span>
    </div>

    {{-- Manage Button --}}
    @if ($showManageButton)
        <button class="collection-cube-manage-btn"
            onclick="event.stopPropagation(); openCubeManager('{{ $cubeId }}')"
            title="{{ __('Gestisci Cubo') }}">
            <i class="fas fa-cog"></i>
        </button>
    @endif
</div>

{{-- Management Modal (one per cube) --}}
<div class="cube-modal-overlay" id="{{ $cubeId }}-modal">
    <div class="cube-modal">
        {{-- Left: 3D Preview --}}
        <div class="cube-modal-preview">
            <div class="cube-modal-preview-canvas" id="{{ $cubeId }}-modal-canvas"></div>
        </div>

        {{-- Right: Controls --}}
        <div class="cube-modal-controls">
            {{-- Header --}}
            <div class="cube-modal-header">
                <h2><i class="fas fa-cube"></i> {{ __('Gestione Cubo') }}</h2>
                <button class="cube-modal-close" onclick="closeCubeManager('{{ $cubeId }}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Face Assignment --}}
            <div class="cube-panel-section">
                <div class="cube-section-title">
                    <i class="fas fa-th-large"></i>
                    {{ __('Assegna EGI alle Facce') }}
                </div>

                <div class="cube-faces-grid" id="{{ $cubeId }}-faces">
                    @foreach (['Fronte', 'Retro', 'Destra', 'Sinistra', 'Sopra', 'Sotto'] as $index => $label)
                        <div class="cube-face-slot {{ $faceImages[$index] ? 'has-image' : '' }}"
                            data-face="{{ $index }}"
                            onclick="selectFaceSlot('{{ $cubeId }}', {{ $index }})">
                            @if ($faceImages[$index])
                                <img src="{{ $faceImages[$index] }}" alt="Face {{ $index }}">
                            @else
                                <i class="fas fa-plus placeholder-icon"></i>
                            @endif
                            <span class="cube-face-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- EGI Selector --}}
                <div class="cube-section-title" style="margin-top: 16px;">
                    <i class="fas fa-images"></i>
                    {{ __('Seleziona EGI') }}
                </div>
                <div class="cube-egi-selector" id="{{ $cubeId }}-egis">
                    @foreach ($egis as $egi)
                        @php
                            $imgUrl = "/users_files/collections_{$egi->collection_id}/creator_{$egi->user_id}/{$egi->id}_thumbnail.webp";
                        @endphp
                        <div class="cube-egi-thumb" data-egi-id="{{ $egi->id }}" data-image="{{ $imgUrl }}"
                            onclick="selectEgiForFace('{{ $cubeId }}', '{{ $imgUrl }}')">
                            <img src="{{ $imgUrl }}" alt="{{ $egi->title ?? 'EGI' }}">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Glass Material --}}
            <div class="cube-panel-section">
                <div class="cube-section-title">
                    <i class="fas fa-glass-whiskey"></i>
                    {{ __('Materiale Vetro') }}
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Colore') }}</span>
                    <div class="cube-color-picker">
                        <input type="color" id="{{ $cubeId }}-glass-color" value="#88ccff"
                            onchange="updateCubeGlass('{{ $cubeId }}')">
                        <span class="cube-color-hex">#88ccff</span>
                    </div>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Opacità') }}</span>
                    <div class="cube-slider-container">
                        <input type="range" id="{{ $cubeId }}-glass-opacity" min="0" max="0.5"
                            step="0.01" value="0.15" oninput="updateCubeGlass('{{ $cubeId }}')">
                        <span class="cube-control-value" id="{{ $cubeId }}-glass-opacity-val">0.15</span>
                    </div>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Metalness') }}</span>
                    <div class="cube-slider-container">
                        <input type="range" id="{{ $cubeId }}-glass-metalness" min="0" max="1"
                            step="0.05" value="0.9" oninput="updateCubeGlass('{{ $cubeId }}')">
                        <span class="cube-control-value" id="{{ $cubeId }}-glass-metalness-val">0.9</span>
                    </div>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Roughness') }}</span>
                    <div class="cube-slider-container">
                        <input type="range" id="{{ $cubeId }}-glass-roughness" min="0" max="1"
                            step="0.05" value="0.05" oninput="updateCubeGlass('{{ $cubeId }}')">
                        <span class="cube-control-value" id="{{ $cubeId }}-glass-roughness-val">0.05</span>
                    </div>
                </div>
            </div>

            {{-- Edges --}}
            <div class="cube-panel-section">
                <div class="cube-section-title">
                    <i class="fas fa-vector-square"></i>
                    {{ __('Bordi') }}
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Mostra') }}</span>
                    <label class="cube-toggle">
                        <input type="checkbox" id="{{ $cubeId }}-show-edges" checked
                            onchange="updateCubeEdges('{{ $cubeId }}')">
                        <span class="cube-toggle-slider"></span>
                    </label>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Colore') }}</span>
                    <div class="cube-color-picker">
                        <input type="color" id="{{ $cubeId }}-edge-color" value="#ffffff"
                            onchange="updateCubeEdges('{{ $cubeId }}')">
                        <span class="cube-color-hex">#ffffff</span>
                    </div>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Opacità') }}</span>
                    <div class="cube-slider-container">
                        <input type="range" id="{{ $cubeId }}-edge-opacity" min="0" max="1"
                            step="0.05" value="0.5" oninput="updateCubeEdges('{{ $cubeId }}')">
                        <span class="cube-control-value" id="{{ $cubeId }}-edge-opacity-val">0.5</span>
                    </div>
                </div>
            </div>

            {{-- Rotation --}}
            <div class="cube-panel-section">
                <div class="cube-section-title">
                    <i class="fas fa-sync-alt"></i>
                    {{ __('Rotazione') }}
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Auto Rotazione') }}</span>
                    <label class="cube-toggle">
                        <input type="checkbox" id="{{ $cubeId }}-auto-rotate" checked
                            onchange="updateCubeRotation('{{ $cubeId }}')">
                        <span class="cube-toggle-slider"></span>
                    </label>
                </div>

                <div class="cube-control-row">
                    <span class="cube-control-label">{{ __('Velocità') }}</span>
                    <div class="cube-slider-container">
                        <input type="range" id="{{ $cubeId }}-rotation-speed" min="0.1" max="3"
                            step="0.1" value="0.5" oninput="updateCubeRotation('{{ $cubeId }}')">
                        <span class="cube-control-value" id="{{ $cubeId }}-rotation-speed-val">0.5</span>
                    </div>
                </div>
            </div>

            {{-- Presets --}}
            <div class="cube-panel-section">
                <div class="cube-section-title">
                    <i class="fas fa-magic"></i>
                    {{ __('Preset Stili') }}
                </div>
                <div class="cube-preset-grid" id="{{ $cubeId }}-presets">
                    <button class="cube-preset-btn active" data-preset="crystal"
                        onclick="applyCubePreset('{{ $cubeId }}', 'crystal')">💎 Crystal</button>
                    <button class="cube-preset-btn" data-preset="neon"
                        onclick="applyCubePreset('{{ $cubeId }}', 'neon')">🌈 Neon</button>
                    <button class="cube-preset-btn" data-preset="ice"
                        onclick="applyCubePreset('{{ $cubeId }}', 'ice')">❄️ Ice</button>
                    <button class="cube-preset-btn" data-preset="gold"
                        onclick="applyCubePreset('{{ $cubeId }}', 'gold')">🏆 Gold</button>
                    <button class="cube-preset-btn" data-preset="hologram"
                        onclick="applyCubePreset('{{ $cubeId }}', 'hologram')">📡 Holo</button>
                    <button class="cube-preset-btn" data-preset="ember"
                        onclick="applyCubePreset('{{ $cubeId }}', 'ember')">🔥 Ember</button>
                </div>
            </div>

            {{-- Save Button --}}
            <button class="cube-save-btn" onclick="saveCubeConfig('{{ $cubeId }}')">
                <i class="fas fa-save"></i>
                {{ __('Salva Configurazione') }}
            </button>
        </div>
    </div>
</div>

{{-- JavaScript --}}
@once
    @push('scripts')
        <script type="module">
            import {
                CollectionCube
            } from '/js/collection-cube.js';

            // Make it globally available
            window.CollectionCube = CollectionCube;
        </script>

        <script>
            // Storage for cube instances and state
            window.cubeInstances = window.cubeInstances || {};
            window.cubeConfigs = window.cubeConfigs || {};
            window.selectedFaceSlot = null;

            // Initialize cube when visible
            document.addEventListener('DOMContentLoaded', function() {
                initializeVisibleCubes();

                // Intersection observer for lazy loading
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const container = entry.target;
                            const cubeId = container.id.replace('-container', '');
                            if (!window.cubeInstances[cubeId]) {
                                initializeCube(cubeId);
                            }
                        }
                    });
                }, {
                    threshold: 0.1
                });

                document.querySelectorAll('.collection-cube-canvas').forEach(el => {
                    observer.observe(el);
                });
            });

            function initializeVisibleCubes() {
                document.querySelectorAll('.collection-cube-canvas').forEach(container => {
                    const rect = container.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom > 0) {
                        const cubeId = container.id.replace('-container', '');
                        initializeCube(cubeId);
                    }
                });
            }

            function initializeCube(cubeId) {
                const container = document.getElementById(cubeId + '-container');
                if (!container || window.cubeInstances[cubeId]) return;

                // Load saved config from localStorage
                const savedConfig = localStorage.getItem('cube-config-' + cubeId);
                const options = savedConfig ? JSON.parse(savedConfig) : {};

                // Wait for module to load
                setTimeout(() => {
                    if (window.initCollectionCube) {
                        const cube = window.initCollectionCube(cubeId, container, options);
                        window.cubeInstances[cubeId] = cube;

                        // Set initial face images from data
                        const faceSlots = document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot');
                        const images = [];
                        faceSlots.forEach(slot => {
                            const img = slot.querySelector('img');
                            images.push(img ? img.src : null);
                        });
                        cube.setFaceImages(images);
                    }
                }, 100);
            }

            function openCubeManager(cubeId) {
                const modal = document.getElementById(cubeId + '-modal');
                if (!modal) return;

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Initialize modal preview cube
                const modalContainer = document.getElementById(cubeId + '-modal-canvas');
                if (modalContainer && !window.cubeInstances[cubeId + '-modal']) {
                    setTimeout(() => {
                        if (window.initCollectionCube) {
                            const mainCube = window.cubeInstances[cubeId];
                            const config = mainCube ? mainCube.getConfig() : {};

                            const modalCube = window.initCollectionCube(cubeId + '-modal', modalContainer, config);
                            window.cubeInstances[cubeId + '-modal'] = modalCube;

                            // Copy face images
                            const faceSlots = document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot');
                            const images = [];
                            faceSlots.forEach(slot => {
                                const img = slot.querySelector('img');
                                images.push(img ? img.src : null);
                            });
                            modalCube.setFaceImages(images);
                        }
                    }, 100);
                }
            }

            function closeCubeManager(cubeId) {
                const modal = document.getElementById(cubeId + '-modal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            function selectFaceSlot(cubeId, faceIndex) {
                // Deselect all
                document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot').forEach(slot => {
                    slot.classList.remove('selected');
                });

                // Select this one
                const slots = document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot');
                if (slots[faceIndex]) {
                    slots[faceIndex].classList.add('selected');
                }

                window.selectedFaceSlot = {
                    cubeId,
                    faceIndex
                };
            }

            function selectEgiForFace(cubeId, imageUrl) {
                if (!window.selectedFaceSlot || window.selectedFaceSlot.cubeId !== cubeId) {
                    alert('Prima seleziona una faccia del cubo');
                    return;
                }

                const {
                    faceIndex
                } = window.selectedFaceSlot;
                const slots = document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot');
                const slot = slots[faceIndex];

                if (slot) {
                    // Update slot UI
                    slot.innerHTML =
                        `<img src="${imageUrl}" alt="Face"><span class="cube-face-label">${slot.querySelector('.cube-face-label')?.textContent || ''}</span>`;
                    slot.classList.add('has-image');

                    // Update cube
                    const images = [];
                    slots.forEach(s => {
                        const img = s.querySelector('img');
                        images.push(img ? img.src : null);
                    });

                    const modalCube = window.cubeInstances[cubeId + '-modal'];
                    if (modalCube) {
                        modalCube.setFaceImages(images);
                    }
                }
            }

            function updateCubeGlass(cubeId) {
                const color = document.getElementById(cubeId + '-glass-color').value;
                const opacity = parseFloat(document.getElementById(cubeId + '-glass-opacity').value);
                const metalness = parseFloat(document.getElementById(cubeId + '-glass-metalness').value);
                const roughness = parseFloat(document.getElementById(cubeId + '-glass-roughness').value);

                // Update value displays
                document.getElementById(cubeId + '-glass-opacity-val').textContent = opacity.toFixed(2);
                document.getElementById(cubeId + '-glass-metalness-val').textContent = metalness.toFixed(2);
                document.getElementById(cubeId + '-glass-roughness-val').textContent = roughness.toFixed(2);
                document.querySelector('#' + cubeId + '-glass-color + .cube-color-hex').textContent = color;

                const modalCube = window.cubeInstances[cubeId + '-modal'];
                if (modalCube) {
                    modalCube.updateGlass({
                        color: parseInt(color.replace('#', ''), 16),
                        opacity,
                        metalness,
                        roughness
                    });
                }
            }

            function updateCubeEdges(cubeId) {
                const visible = document.getElementById(cubeId + '-show-edges').checked;
                const color = document.getElementById(cubeId + '-edge-color').value;
                const opacity = parseFloat(document.getElementById(cubeId + '-edge-opacity').value);

                document.getElementById(cubeId + '-edge-opacity-val').textContent = opacity.toFixed(2);
                document.querySelector('#' + cubeId + '-edge-color + .cube-color-hex').textContent = color;

                const modalCube = window.cubeInstances[cubeId + '-modal'];
                if (modalCube) {
                    modalCube.updateEdges({
                        visible,
                        color: parseInt(color.replace('#', ''), 16),
                        opacity
                    });
                }
            }

            function updateCubeRotation(cubeId) {
                const autoRotate = document.getElementById(cubeId + '-auto-rotate').checked;
                const speed = parseFloat(document.getElementById(cubeId + '-rotation-speed').value);

                document.getElementById(cubeId + '-rotation-speed-val').textContent = speed.toFixed(1);

                const modalCube = window.cubeInstances[cubeId + '-modal'];
                if (modalCube) {
                    modalCube.updateRotation({
                        autoRotate,
                        speed
                    });
                }
            }

            function applyCubePreset(cubeId, presetName) {
                // Update UI
                document.querySelectorAll('#' + cubeId + '-presets .cube-preset-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.preset === presetName);
                });

                const modalCube = window.cubeInstances[cubeId + '-modal'];
                if (modalCube) {
                    modalCube.applyPreset(presetName);

                    // Update UI controls to match preset
                    // This would need preset values synced back to controls
                }
            }

            function saveCubeConfig(cubeId) {
                const modalCube = window.cubeInstances[cubeId + '-modal'];
                const mainCube = window.cubeInstances[cubeId];

                if (modalCube) {
                    const config = modalCube.getConfig();

                    // Save to localStorage
                    localStorage.setItem('cube-config-' + cubeId, JSON.stringify(config));

                    // Apply to main cube
                    if (mainCube) {
                        mainCube.loadConfig(config);

                        // Update face images
                        const faceSlots = document.querySelectorAll('#' + cubeId + '-faces .cube-face-slot');
                        const images = [];
                        faceSlots.forEach(slot => {
                            const img = slot.querySelector('img');
                            images.push(img ? img.src : null);
                        });
                        mainCube.setFaceImages(images);
                    }

                    // Close modal
                    closeCubeManager(cubeId);

                    // Show feedback
                    alert('Configurazione salvata!');
                }
            }

            // Close modal on escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.cube-modal-overlay.active').forEach(modal => {
                        const cubeId = modal.id.replace('-modal', '');
                        closeCubeManager(cubeId);
                    });
                }
            });

            // Close modal on backdrop click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('cube-modal-overlay')) {
                    const cubeId = e.target.id.replace('-modal', '');
                    closeCubeManager(cubeId);
                }
            });
        </script>
    @endpush
@endonce
