<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGI Prism Viewer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            overflow: hidden;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
            font-weight: 600;
            background: linear-gradient(90deg, #64b5f6, #ce93d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title i {
            background: linear-gradient(135deg, #64b5f6, #ce93d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Main Layout */
        .main-container {
            display: flex;
            height: 100vh;
            padding-top: 60px;
        }

        /* Canvas Container */
        .canvas-container {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #three-canvas {
            width: 100%;
            height: 100%;
        }

        /* Control Panel */
        .control-panel {
            width: 380px;
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            overflow-y: auto;
            padding: 20px;
        }

        .panel-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #ce93d8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            font-size: 0.9rem;
        }

        /* Form Controls */
        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .control-row:last-child {
            margin-bottom: 0;
        }

        .control-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .control-value {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            min-width: 40px;
            text-align: right;
        }

        /* Sliders */
        .slider-container {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 12px;
        }

        input[type="range"] {
            flex: 1;
            height: 6px;
            -webkit-appearance: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.5);
            transition: transform 0.2s;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        /* Color Picker */
        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="color"] {
            width: 36px;
            height: 36px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            background: transparent;
            padding: 2px;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 6px;
        }

        .color-hex {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            font-family: monospace;
        }

        /* Toggle Switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch {
            position: relative;
            width: 48px;
            height: 26px;
        }

        .toggle-switch input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .toggle-slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 13px;
            transition: all 0.3s;
        }

        .toggle-slider:before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            left: 3px;
            top: 3px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .toggle-switch input:checked+.toggle-slider {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .toggle-switch input:checked+.toggle-slider:before {
            transform: translateX(22px);
        }

        /* Image Selector */
        .image-selector {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 12px;
        }

        .image-thumb {
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
            position: relative;
        }

        .image-thumb:hover {
            border-color: rgba(102, 126, 234, 0.5);
            transform: scale(1.05);
        }

        .image-thumb.active {
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.5);
        }

        .image-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-thumb .thumb-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
            padding: 20px 6px 6px 6px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .image-thumb:hover .thumb-info {
            opacity: 1;
        }

        .thumb-info .thumb-title {
            font-size: 0.65rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .thumb-info .thumb-collection {
            font-size: 0.55rem;
            color: #ce93d8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .image-thumb .thumb-desc-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            background: rgba(102, 126, 234, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
            z-index: 5;
        }

        .image-thumb:hover .thumb-desc-btn {
            opacity: 1;
        }

        .thumb-desc-btn:hover {
            background: rgba(102, 126, 234, 1);
            transform: scale(1.1);
        }

        /* Description Modal */
        .desc-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .desc-modal.active {
            display: flex;
        }

        .desc-modal-content {
            background: rgba(15, 15, 35, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .desc-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .desc-modal-header h3 {
            font-size: 1.2rem;
            color: #fff;
            margin: 0;
        }

        .desc-modal-header .collection-badge {
            font-size: 0.75rem;
            color: #ce93d8;
            margin-top: 4px;
        }

        .desc-modal-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 4px;
        }

        .desc-modal-close:hover {
            color: #fff;
        }

        .desc-modal-body {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .desc-modal-body.empty {
            color: rgba(255, 255, 255, 0.4);
            font-style: italic;
        }

        /* Selected EGI Info */
        .selected-egi-info {
            margin-top: 12px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
        }

        .egi-info-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
        }

        .egi-info-collection {
            font-size: 0.75rem;
            color: #ce93d8;
            margin-bottom: 10px;
        }

        .egi-info-description {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
            max-height: 120px;
            overflow-y: auto;
        }

        .egi-info-description em {
            color: rgba(255, 255, 255, 0.4);
        }

        /* Upload Zone */
        .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 12px;
        }

        .upload-zone:hover {
            border-color: rgba(102, 126, 234, 0.5);
            background: rgba(102, 126, 234, 0.05);
        }

        .upload-zone i {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 8px;
        }

        .upload-zone p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Preset Buttons */
        .preset-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .preset-btn {
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .preset-btn:hover {
            background: rgba(102, 126, 234, 0.2);
            border-color: rgba(102, 126, 234, 0.5);
        }

        .preset-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: transparent;
        }

        /* Stats Display */
        .stats-display {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-family: monospace;
            color: #64b5f6;
            display: flex;
            gap: 20px;
        }

        /* Scrollbar */
        .control-panel::-webkit-scrollbar {
            width: 6px;
        }

        .control-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .control-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .control-panel::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            margin: 16px 0;
        }

        /* Export Section */
        .export-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .export-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .export-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .export-btn.secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: none;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">
            <i class="fas fa-gem"></i>
            EGI Prism Viewer
        </div>
        <div class="header-actions">
            <a href="/test/composition-builder" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Torna al Composer
            </a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Three.js Canvas -->
        <div class="canvas-container">
            <canvas id="three-canvas"></canvas>
        </div>

        <!-- Control Panel -->
        <div class="control-panel">
            <!-- Image Selection -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-image"></i>
                    Immagine EGI
                </div>
                <div class="image-selector">
                    @foreach ($egis as $index => $egi)
                        <div class="image-thumb {{ $index === 0 ? 'active' : '' }}"
                            data-image="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp"
                            data-title="{{ $egi->title ?? 'EGI #' . $egi->id }}" data-egi-id="{{ $egi->id }}"
                            data-collection="{{ $egi->collection->collection_name ?? 'Collection #' . $egi->collection_id }}"
                            data-description="{{ htmlspecialchars($egi->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                            onclick="selectImage(this)">
                            <img src="/users_files/collections_{{ $egi->collection_id }}/creator_{{ $egi->user_id }}/{{ $egi->id }}_thumbnail.webp"
                                alt="{{ $egi->title ?? 'EGI' }}">
                            <div class="thumb-info">
                                <div class="thumb-title">{{ $egi->title ?? 'EGI #' . $egi->id }}</div>
                                <div class="thumb-collection">
                                    {{ $egi->collection->collection_name ?? 'Collection #' . $egi->collection_id }}
                                </div>
                            </div>
                            @if ($egi->description)
                                <div class="thumb-desc-btn"
                                    onclick="event.stopPropagation(); showDescription(this.parentElement)"
                                    title="Mostra descrizione">
                                    <i class="fas fa-info"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="upload-zone" onclick="document.getElementById('image-upload').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Trascina o clicca per caricare</p>
                </div>
                <input type="file" id="image-upload" hidden accept="image/*" onchange="uploadImage(event)">

                <!-- Selected EGI Info -->
                <div class="divider"></div>
                <div class="selected-egi-info">
                    <div class="egi-info-title" id="selected-egi-title">Seleziona un EGI</div>
                    <div class="egi-info-collection" id="selected-egi-collection"></div>
                    <div class="egi-info-description" id="selected-egi-description">
                        <em>Nessuna descrizione disponibile</em>
                    </div>
                </div>
            </div>

            <!-- Glass Material -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-glass-whiskey"></i>
                    Materiale Vetro
                </div>

                <div class="control-row">
                    <span class="control-label">Colore Vetro</span>
                    <div class="color-picker-wrapper">
                        <input type="color" id="glass-color" value="#88ccff" onchange="updateMaterial()">
                        <span class="color-hex">#88ccff</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Opacità</span>
                    <div class="slider-container">
                        <input type="range" id="glass-opacity" min="0" max="1" step="0.01"
                            value="0.15" oninput="updateMaterial()">
                        <span class="control-value" id="glass-opacity-val">0.15</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Metalness</span>
                    <div class="slider-container">
                        <input type="range" id="glass-metalness" min="0" max="1" step="0.01"
                            value="0.9" oninput="updateMaterial()">
                        <span class="control-value" id="glass-metalness-val">0.9</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Roughness</span>
                    <div class="slider-container">
                        <input type="range" id="glass-roughness" min="0" max="1" step="0.01"
                            value="0.05" oninput="updateMaterial()">
                        <span class="control-value" id="glass-roughness-val">0.05</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Transmission</span>
                    <div class="slider-container">
                        <input type="range" id="glass-transmission" min="0" max="1" step="0.01"
                            value="0.95" oninput="updateMaterial()">
                        <span class="control-value" id="glass-transmission-val">0.95</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">IOR (Rifrazione)</span>
                    <div class="slider-container">
                        <input type="range" id="glass-ior" min="1" max="2.5" step="0.01"
                            value="1.5" oninput="updateMaterial()">
                        <span class="control-value" id="glass-ior-val">1.5</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Thickness</span>
                    <div class="slider-container">
                        <input type="range" id="glass-thickness" min="0" max="5" step="0.1"
                            value="0.5" oninput="updateMaterial()">
                        <span class="control-value" id="glass-thickness-val">0.5</span>
                    </div>
                </div>
            </div>

            <!-- Emissive Settings -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-lightbulb"></i>
                    Emissione Luce
                </div>

                <div class="control-row">
                    <span class="control-label">Colore Emissivo</span>
                    <div class="color-picker-wrapper">
                        <input type="color" id="emissive-color" value="#4488ff" onchange="updateMaterial()">
                        <span class="color-hex">#4488ff</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Intensità</span>
                    <div class="slider-container">
                        <input type="range" id="emissive-intensity" min="0" max="3" step="0.1"
                            value="0.3" oninput="updateMaterial()">
                        <span class="control-value" id="emissive-intensity-val">0.3</span>
                    </div>
                </div>
            </div>

            <!-- Edges Settings -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-vector-square"></i>
                    Bordi / Edges
                </div>

                <div class="control-row">
                    <span class="control-label">Mostra Bordi</span>
                    <div class="toggle-container">
                        <label class="toggle-switch">
                            <input type="checkbox" id="show-edges" checked onchange="updateEdges()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Colore Bordi</span>
                    <div class="color-picker-wrapper">
                        <input type="color" id="edge-color" value="#ffffff" onchange="updateEdges()">
                        <span class="color-hex">#ffffff</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Opacità Bordi</span>
                    <div class="slider-container">
                        <input type="range" id="edge-opacity" min="0" max="1" step="0.01"
                            value="0.5" oninput="updateEdges()">
                        <span class="control-value" id="edge-opacity-val">0.5</span>
                    </div>
                </div>
            </div>

            <!-- Bloom Settings -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-star"></i>
                    Bloom / Glow
                </div>

                <div class="control-row">
                    <span class="control-label">Abilita Bloom</span>
                    <div class="toggle-container">
                        <label class="toggle-switch">
                            <input type="checkbox" id="enable-bloom" checked onchange="updateBloom()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Intensità</span>
                    <div class="slider-container">
                        <input type="range" id="bloom-strength" min="0" max="3" step="0.1"
                            value="1.2" oninput="updateBloom()">
                        <span class="control-value" id="bloom-strength-val">1.2</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Radius</span>
                    <div class="slider-container">
                        <input type="range" id="bloom-radius" min="0" max="2" step="0.1"
                            value="0.5" oninput="updateBloom()">
                        <span class="control-value" id="bloom-radius-val">0.5</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Threshold</span>
                    <div class="slider-container">
                        <input type="range" id="bloom-threshold" min="0" max="1" step="0.01"
                            value="0.2" oninput="updateBloom()">
                        <span class="control-value" id="bloom-threshold-val">0.2</span>
                    </div>
                </div>
            </div>

            <!-- Rotation Settings -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-sync-alt"></i>
                    Rotazione
                </div>

                <div class="control-row">
                    <span class="control-label">Auto Rotazione</span>
                    <div class="toggle-container">
                        <label class="toggle-switch">
                            <input type="checkbox" id="auto-rotate" checked onchange="updateRotation()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Velocità</span>
                    <div class="slider-container">
                        <input type="range" id="rotation-speed" min="0.1" max="5" step="0.1"
                            value="1" oninput="updateRotation()">
                        <span class="control-value" id="rotation-speed-val">1.0</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Angolo X</span>
                    <div class="slider-container">
                        <input type="range" id="angle-x" min="0" max="360" step="1"
                            value="0" oninput="updateRotation()">
                        <span class="control-value" id="angle-x-val">0°</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Angolo Y</span>
                    <div class="slider-container">
                        <input type="range" id="angle-y" min="0" max="360" step="1"
                            value="0" oninput="updateRotation()">
                        <span class="control-value" id="angle-y-val">0°</span>
                    </div>
                </div>
            </div>

            <!-- Dimension Settings -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-expand-arrows-alt"></i>
                    Dimensioni
                </div>

                <div class="control-row">
                    <span class="control-label">Scala</span>
                    <div class="slider-container">
                        <input type="range" id="prism-scale" min="0.5" max="3" step="0.1"
                            value="1" oninput="updateScale()">
                        <span class="control-value" id="prism-scale-val">1.0</span>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Profondità</span>
                    <div class="slider-container">
                        <input type="range" id="prism-depth" min="0.05" max="1" step="0.05"
                            value="0.15" oninput="updateDimensions()">
                        <span class="control-value" id="prism-depth-val">0.15</span>
                    </div>
                </div>
            </div>

            <!-- Presets -->
            <div class="panel-section">
                <div class="section-title">
                    <i class="fas fa-magic"></i>
                    Preset Stili
                </div>
                <div class="preset-grid">
                    <button class="preset-btn active" onclick="applyPreset('crystal')">💎 Crystal</button>
                    <button class="preset-btn" onclick="applyPreset('neon')">🌈 Neon</button>
                    <button class="preset-btn" onclick="applyPreset('ice')">❄️ Ice</button>
                    <button class="preset-btn" onclick="applyPreset('gold')">🏆 Gold</button>
                    <button class="preset-btn" onclick="applyPreset('hologram')">📡 Holo</button>
                    <button class="preset-btn" onclick="applyPreset('ember')">🔥 Ember</button>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Export -->
            <div class="export-section">
                <button class="export-btn" onclick="exportConfig()">
                    <i class="fas fa-save"></i>
                    Salva Configurazione
                </button>
                <button class="export-btn secondary" onclick="exportImage()">
                    <i class="fas fa-camera"></i>
                    Screenshot PNG
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-display">
        <span id="fps">FPS: 60</span>
        <span id="objects">Objects: 3</span>
    </div>

    <!-- Description Modal -->
    <div class="desc-modal" id="desc-modal">
        <div class="desc-modal-content">
            <div class="desc-modal-header">
                <div>
                    <h3 id="desc-modal-title">EGI Title</h3>
                    <div class="collection-badge" id="desc-modal-collection">Collection Name</div>
                </div>
                <button class="desc-modal-close" onclick="closeDescriptionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="desc-modal-body" id="desc-modal-body">
                Descrizione...
            </div>
        </div>
    </div>

    <!-- Three.js -->
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
        }
    }
    </script>

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

        // Scene Setup
        const canvas = document.getElementById('three-canvas');
        const scene = new THREE.Scene();

        const camera = new THREE.PerspectiveCamera(50, 1, 0.1, 1000);
        camera.position.set(0, 0, 4);

        const renderer = new THREE.WebGLRenderer({
            canvas,
            antialias: true,
            alpha: true,
            preserveDrawingBuffer: true
        });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;

        // Environment
        const pmremGenerator = new THREE.PMREMGenerator(renderer);
        const roomEnv = new RoomEnvironment();
        const envTexture = pmremGenerator.fromScene(roomEnv).texture;
        scene.environment = envTexture;
        scene.background = new THREE.Color(0x0a0a0f);

        // Controls
        const controls = new OrbitControls(camera, canvas);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.minDistance = 2;
        controls.maxDistance = 10;

        // Post-processing
        const composer = new EffectComposer(renderer);
        const renderPass = new RenderPass(scene, camera);
        composer.addPass(renderPass);

        const bloomPass = new UnrealBloomPass(
            new THREE.Vector2(window.innerWidth, window.innerHeight),
            1.2,
            0.5,
            0.2
        );
        composer.addPass(bloomPass);

        // Prism Group
        const prismGroup = new THREE.Group();
        scene.add(prismGroup);

        // Texture Loader
        const textureLoader = new THREE.TextureLoader();
        let currentTexture = null;

        // Create Prism
        let glassMesh, imageMesh, backMesh, edgesMesh;
        let currentEgiInfo = {
            title: 'EGI',
            id: '',
            collection: ''
        };

        function createPrism(imagePath, egiInfo = null) {
            // Store EGI info for back text
            if (egiInfo) {
                currentEgiInfo = egiInfo;
            }

            // Clear existing
            while (prismGroup.children.length > 0) {
                const child = prismGroup.children[0];
                if (child.geometry) child.geometry.dispose();
                if (child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach(m => m.dispose());
                    } else {
                        child.material.dispose();
                    }
                }
                prismGroup.remove(child);
            }

            const depth = parseFloat(document.getElementById('prism-depth').value);

            // Load texture
            textureLoader.load(imagePath, (texture) => {
                currentTexture = texture;
                texture.colorSpace = THREE.SRGBColorSpace;

                // Fixed prism dimensions
                const prismWidth = 2;
                const prismHeight = 2;

                // Calculate image size to fit inside prism while preserving aspect ratio
                const imgAspect = texture.image.width / texture.image.height;
                let imgWidth, imgHeight;
                if (imgAspect > 1) {
                    // Landscape image
                    imgWidth = prismWidth - 0.1;
                    imgHeight = imgWidth / imgAspect;
                } else {
                    // Portrait or square image
                    imgHeight = prismHeight - 0.1;
                    imgWidth = imgHeight * imgAspect;
                }

                // Glass Box - fixed size
                const glassGeometry = new THREE.BoxGeometry(prismWidth, prismHeight, depth);
                const glassMaterial = new THREE.MeshPhysicalMaterial({
                    color: new THREE.Color(document.getElementById('glass-color').value),
                    metalness: parseFloat(document.getElementById('glass-metalness').value),
                    roughness: parseFloat(document.getElementById('glass-roughness').value),
                    transmission: parseFloat(document.getElementById('glass-transmission').value),
                    thickness: parseFloat(document.getElementById('glass-thickness').value),
                    ior: parseFloat(document.getElementById('glass-ior').value),
                    opacity: parseFloat(document.getElementById('glass-opacity').value),
                    transparent: true,
                    emissive: new THREE.Color(document.getElementById('emissive-color').value),
                    emissiveIntensity: parseFloat(document.getElementById('emissive-intensity').value),
                    envMapIntensity: 1.5,
                    clearcoat: 1,
                    clearcoatRoughness: 0.1,
                    side: THREE.DoubleSide
                });

                glassMesh = new THREE.Mesh(glassGeometry, glassMaterial);
                prismGroup.add(glassMesh);

                // Front Image Plane - scaled to fit inside prism
                const imageGeometry = new THREE.PlaneGeometry(imgWidth, imgHeight);
                const imageMaterial = new THREE.MeshBasicMaterial({
                    map: texture,
                    side: THREE.FrontSide
                });
                imageMesh = new THREE.Mesh(imageGeometry, imageMaterial);
                imageMesh.position.z = depth / 2 - 0.02;
                prismGroup.add(imageMesh);

                // Back Panel with Text (Canvas Texture) - fixed size
                const backCanvas = document.createElement('canvas');
                const backCtx = backCanvas.getContext('2d');
                backCanvas.width = 512;
                backCanvas.height = 512;

                // Background gradient
                const gradient = backCtx.createLinearGradient(0, 0, backCanvas.width, backCanvas.height);
                gradient.addColorStop(0, '#1a1a2e');
                gradient.addColorStop(0.5, '#16213e');
                gradient.addColorStop(1, '#0f0f23');
                backCtx.fillStyle = gradient;
                backCtx.fillRect(0, 0, backCanvas.width, backCanvas.height);

                // Border glow effect
                backCtx.strokeStyle = '#667eea';
                backCtx.lineWidth = 4;
                backCtx.strokeRect(10, 10, backCanvas.width - 20, backCanvas.height - 20);

                // Inner border
                backCtx.strokeStyle = 'rgba(206, 147, 216, 0.5)';
                backCtx.lineWidth = 2;
                backCtx.strokeRect(20, 20, backCanvas.width - 40, backCanvas.height - 40);

                // EGI Logo/Icon area
                backCtx.fillStyle = 'rgba(102, 126, 234, 0.3)';
                backCtx.beginPath();
                backCtx.arc(backCanvas.width / 2, 80, 40, 0, Math.PI * 2);
                backCtx.fill();

                // EGI Text
                backCtx.fillStyle = '#ce93d8';
                backCtx.font = 'bold 24px Segoe UI, sans-serif';
                backCtx.textAlign = 'center';
                backCtx.fillText('EGI', backCanvas.width / 2, 88);

                // Title
                backCtx.fillStyle = '#ffffff';
                backCtx.font = 'bold 28px Segoe UI, sans-serif';
                const titleText = currentEgiInfo.title || 'EGI Asset';
                const maxTitleWidth = backCanvas.width - 60;
                let displayTitle = titleText;
                if (backCtx.measureText(titleText).width > maxTitleWidth) {
                    while (backCtx.measureText(displayTitle + '...').width > maxTitleWidth && displayTitle.length >
                        0) {
                        displayTitle = displayTitle.slice(0, -1);
                    }
                    displayTitle += '...';
                }
                backCtx.fillText(displayTitle, backCanvas.width / 2, 160);

                // Separator line
                backCtx.strokeStyle = 'rgba(102, 126, 234, 0.5)';
                backCtx.lineWidth = 2;
                backCtx.beginPath();
                backCtx.moveTo(60, 190);
                backCtx.lineTo(backCanvas.width - 60, 190);
                backCtx.stroke();

                // Info fields
                backCtx.font = '18px Segoe UI, sans-serif';
                backCtx.textAlign = 'left';
                const startY = 230;
                const lineHeight = 35;

                // ID
                backCtx.fillStyle = '#64b5f6';
                backCtx.fillText('ID:', 60, startY);
                backCtx.fillStyle = '#ffffff';
                backCtx.fillText(`#${currentEgiInfo.id || 'N/A'}`, 120, startY);

                // Collection
                backCtx.fillStyle = '#64b5f6';
                backCtx.fillText('Collection:', 60, startY + lineHeight);
                backCtx.fillStyle = '#ffffff';
                backCtx.fillText(`${currentEgiInfo.collection || 'N/A'}`, 180, startY + lineHeight);

                // Description
                let descY = startY + lineHeight * 2;
                let rawDescription = currentEgiInfo.description || '';
                // Decode HTML entities
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = rawDescription;
                const description = tempDiv.textContent || tempDiv.innerText || '';

                if (description.trim()) {
                    backCtx.fillStyle = '#64b5f6';
                    backCtx.font = '16px Segoe UI, sans-serif';
                    backCtx.fillText('Descrizione:', 60, descY);

                    // Word wrap description
                    backCtx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                    backCtx.font = '14px Segoe UI, sans-serif';
                    const maxWidth = backCanvas.width - 120;
                    const words = description.split(' ');
                    let line = '';
                    let lineY = descY + 22;
                    const maxLines = 4;
                    let lineCount = 0;
                    let wordIndex = 0;

                    for (let i = 0; i < words.length && lineCount < maxLines; i++) {
                        const testLine = line + words[i] + ' ';
                        const metrics = backCtx.measureText(testLine);
                        if (metrics.width > maxWidth && line !== '') {
                            if (lineCount === maxLines - 1) {
                                // Last line - add ellipsis
                                backCtx.fillText(line.trim() + '...', 60, lineY);
                            } else {
                                backCtx.fillText(line.trim(), 60, lineY);
                            }
                            line = words[i] + ' ';
                            lineY += 18;
                            lineCount++;
                        } else {
                            line = testLine;
                        }
                        wordIndex = i;
                    }
                    // Check if there's remaining text and if we reached the limit
                    const hasMoreText = wordIndex < words.length - 1;
                    if (lineCount < maxLines && line.trim()) {
                        let finalLine = line.trim();
                        if (hasMoreText) {
                            finalLine += '...';
                        }
                        backCtx.fillText(finalLine, 60, lineY);
                    }
                    descY = lineY + 20;
                }

                // Status badge
                backCtx.fillStyle = 'rgba(76, 175, 80, 0.3)';
                const badgeY = descY + 15;
                backCtx.beginPath();
                backCtx.roundRect(backCanvas.width / 2 - 60, badgeY, 120, 30, 15);
                backCtx.fill();
                backCtx.fillStyle = '#4caf50';
                backCtx.font = 'bold 14px Segoe UI, sans-serif';
                backCtx.textAlign = 'center';
                backCtx.fillText('✓ VERIFIED', backCanvas.width / 2, badgeY + 20);

                // Bottom decoration
                backCtx.fillStyle = 'rgba(206, 147, 216, 0.2)';
                backCtx.fillRect(30, backCanvas.height - 50, backCanvas.width - 60, 2);

                backCtx.font = '12px Segoe UI, sans-serif';
                backCtx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                backCtx.fillText('Powered by Blockchain', backCanvas.width / 2, backCanvas.height - 25);

                // Create texture from canvas
                const backTexture = new THREE.CanvasTexture(backCanvas);
                backTexture.colorSpace = THREE.SRGBColorSpace;

                const backGeometry = new THREE.PlaneGeometry(prismWidth - 0.1, prismHeight - 0.1);
                const backMaterial = new THREE.MeshBasicMaterial({
                    map: backTexture,
                    side: THREE.FrontSide
                });
                backMesh = new THREE.Mesh(backGeometry, backMaterial);
                backMesh.position.z = -(depth / 2 - 0.02);
                backMesh.rotation.y = Math.PI; // Flip to face outward
                prismGroup.add(backMesh);

                // Edges
                const edgesGeometry = new THREE.EdgesGeometry(glassGeometry);
                const edgesMaterial = new THREE.LineBasicMaterial({
                    color: new THREE.Color(document.getElementById('edge-color').value),
                    opacity: parseFloat(document.getElementById('edge-opacity').value),
                    transparent: true
                });
                edgesMesh = new THREE.LineSegments(edgesGeometry, edgesMaterial);
                edgesMesh.visible = document.getElementById('show-edges').checked;
                prismGroup.add(edgesMesh);

                updateScale();
            });
        }

        // Initialize with first EGI
        @if ($egis->count() > 0)
            @php $firstEgi = $egis->first(); @endphp
            createPrism(
                "/users_files/collections_{{ $firstEgi->collection_id }}/creator_{{ $firstEgi->user_id }}/{{ $firstEgi->id }}_thumbnail.webp", {
                    title: "{{ $firstEgi->title ?? 'EGI #' . $firstEgi->id }}",
                    id: "{{ $firstEgi->id }}",
                    collection: "{{ $firstEgi->collection_id }}"
                }
            );
        @endif

        // Update Functions
        window.updateMaterial = function() {
            if (!glassMesh) return;

            const material = glassMesh.material;
            material.color.set(document.getElementById('glass-color').value);
            material.opacity = parseFloat(document.getElementById('glass-opacity').value);
            material.metalness = parseFloat(document.getElementById('glass-metalness').value);
            material.roughness = parseFloat(document.getElementById('glass-roughness').value);
            material.transmission = parseFloat(document.getElementById('glass-transmission').value);
            material.thickness = parseFloat(document.getElementById('glass-thickness').value);
            material.ior = parseFloat(document.getElementById('glass-ior').value);
            material.emissive.set(document.getElementById('emissive-color').value);
            material.emissiveIntensity = parseFloat(document.getElementById('emissive-intensity').value);
            material.needsUpdate = true;

            // Update display values
            document.getElementById('glass-opacity-val').textContent = document.getElementById('glass-opacity').value;
            document.getElementById('glass-metalness-val').textContent = document.getElementById('glass-metalness')
                .value;
            document.getElementById('glass-roughness-val').textContent = document.getElementById('glass-roughness')
                .value;
            document.getElementById('glass-transmission-val').textContent = document.getElementById(
                'glass-transmission').value;
            document.getElementById('glass-ior-val').textContent = document.getElementById('glass-ior').value;
            document.getElementById('glass-thickness-val').textContent = document.getElementById('glass-thickness')
                .value;
            document.getElementById('emissive-intensity-val').textContent = document.getElementById(
                'emissive-intensity').value;

            // Update color hex displays
            document.querySelector('#glass-color + .color-hex').textContent = document.getElementById('glass-color')
                .value;
            document.querySelector('#emissive-color + .color-hex').textContent = document.getElementById(
                'emissive-color').value;
        };

        window.updateEdges = function() {
            if (!edgesMesh) return;

            edgesMesh.visible = document.getElementById('show-edges').checked;
            edgesMesh.material.color.set(document.getElementById('edge-color').value);
            edgesMesh.material.opacity = parseFloat(document.getElementById('edge-opacity').value);

            document.getElementById('edge-opacity-val').textContent = document.getElementById('edge-opacity').value;
            document.querySelector('#edge-color + .color-hex').textContent = document.getElementById('edge-color')
                .value;
        };

        window.updateBloom = function() {
            const enabled = document.getElementById('enable-bloom').checked;
            bloomPass.enabled = enabled;
            bloomPass.strength = parseFloat(document.getElementById('bloom-strength').value);
            bloomPass.radius = parseFloat(document.getElementById('bloom-radius').value);
            bloomPass.threshold = parseFloat(document.getElementById('bloom-threshold').value);

            document.getElementById('bloom-strength-val').textContent = document.getElementById('bloom-strength').value;
            document.getElementById('bloom-radius-val').textContent = document.getElementById('bloom-radius').value;
            document.getElementById('bloom-threshold-val').textContent = document.getElementById('bloom-threshold')
                .value;
        };

        window.updateRotation = function() {
            document.getElementById('rotation-speed-val').textContent = parseFloat(document.getElementById(
                'rotation-speed').value).toFixed(1);
            document.getElementById('angle-x-val').textContent = document.getElementById('angle-x').value + '°';
            document.getElementById('angle-y-val').textContent = document.getElementById('angle-y').value + '°';
        };

        window.updateScale = function() {
            const scale = parseFloat(document.getElementById('prism-scale').value);
            prismGroup.scale.set(scale, scale, scale);
            document.getElementById('prism-scale-val').textContent = scale.toFixed(1);
        };

        window.updateDimensions = function() {
            document.getElementById('prism-depth-val').textContent = document.getElementById('prism-depth').value;
            // Rebuild prism with new depth
            if (currentTexture) {
                const activeThumb = document.querySelector('.image-thumb.active');
                if (activeThumb) {
                    const imagePath = activeThumb.querySelector('img').src;
                    createPrism(imagePath);
                }
            }
        };

        window.selectImage = function(element) {
            document.querySelectorAll('.image-thumb').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
            const imagePath = element.querySelector('img').src;
            const egiInfo = {
                title: element.dataset.title || 'EGI Asset',
                id: element.dataset.egiId || '',
                collection: element.dataset.collection || '',
                description: element.dataset.description || ''
            };

            // Update EGI info panel
            document.getElementById('selected-egi-title').textContent = egiInfo.title;
            document.getElementById('selected-egi-collection').textContent = egiInfo.collection;

            const descEl = document.getElementById('selected-egi-description');
            if (egiInfo.description && egiInfo.description.trim()) {
                descEl.innerHTML = egiInfo.description;
            } else {
                descEl.innerHTML = '<em>Nessuna descrizione disponibile</em>';
            }

            createPrism(imagePath, egiInfo);
        };

        window.uploadImage = function(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                createPrism(e.target.result);
            };
            reader.readAsDataURL(file);
        };

        // Description Modal Functions
        window.showDescription = function(thumbElement) {
            const title = thumbElement.dataset.title || 'EGI';
            const collection = thumbElement.dataset.collection || '';
            const description = thumbElement.dataset.description || '';

            document.getElementById('desc-modal-title').textContent = title;
            document.getElementById('desc-modal-collection').textContent = collection;

            const bodyEl = document.getElementById('desc-modal-body');
            if (description.trim()) {
                bodyEl.textContent = description;
                bodyEl.classList.remove('empty');
            } else {
                bodyEl.textContent = 'Nessuna descrizione disponibile per questo EGI.';
                bodyEl.classList.add('empty');
            }

            document.getElementById('desc-modal').classList.add('active');
        };

        window.closeDescriptionModal = function() {
            document.getElementById('desc-modal').classList.remove('active');
        };

        // Close modal on backdrop click
        document.getElementById('desc-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDescriptionModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('desc-modal').classList.contains('active')) {
                closeDescriptionModal();
            }
        });

        // Presets
        const presets = {
            crystal: {
                glassColor: '#88ccff',
                opacity: 0.15,
                metalness: 0.9,
                roughness: 0.05,
                transmission: 0.95,
                ior: 1.5,
                thickness: 0.5,
                emissiveColor: '#4488ff',
                emissiveIntensity: 0.3,
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
                emissiveColor: '#ff00ff',
                emissiveIntensity: 1.5,
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
                emissiveColor: '#88eeff',
                emissiveIntensity: 0.2,
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
                emissiveColor: '#ffaa00',
                emissiveIntensity: 0.5,
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
                emissiveColor: '#00ffcc',
                emissiveIntensity: 0.8,
                bloomStrength: 1.8,
                bloomRadius: 1.0,
                bloomThreshold: 0.05
            },
            ember: {
                glassColor: '#ff4400',
                opacity: 0.25,
                metalness: 0.6,
                roughness: 0.3,
                transmission: 0.7,
                ior: 1.6,
                thickness: 0.6,
                emissiveColor: '#ff6600',
                emissiveIntensity: 1.0,
                bloomStrength: 1.5,
                bloomRadius: 0.6,
                bloomThreshold: 0.15
            }
        };

        window.applyPreset = function(name) {
            const preset = presets[name];
            if (!preset) return;

            document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');

            document.getElementById('glass-color').value = preset.glassColor;
            document.getElementById('glass-opacity').value = preset.opacity;
            document.getElementById('glass-metalness').value = preset.metalness;
            document.getElementById('glass-roughness').value = preset.roughness;
            document.getElementById('glass-transmission').value = preset.transmission;
            document.getElementById('glass-ior').value = preset.ior;
            document.getElementById('glass-thickness').value = preset.thickness;
            document.getElementById('emissive-color').value = preset.emissiveColor;
            document.getElementById('emissive-intensity').value = preset.emissiveIntensity;
            document.getElementById('bloom-strength').value = preset.bloomStrength;
            document.getElementById('bloom-radius').value = preset.bloomRadius;
            document.getElementById('bloom-threshold').value = preset.bloomThreshold;

            updateMaterial();
            updateBloom();
        };

        window.exportConfig = function() {
            const config = {
                glass: {
                    color: document.getElementById('glass-color').value,
                    opacity: parseFloat(document.getElementById('glass-opacity').value),
                    metalness: parseFloat(document.getElementById('glass-metalness').value),
                    roughness: parseFloat(document.getElementById('glass-roughness').value),
                    transmission: parseFloat(document.getElementById('glass-transmission').value),
                    ior: parseFloat(document.getElementById('glass-ior').value),
                    thickness: parseFloat(document.getElementById('glass-thickness').value)
                },
                emissive: {
                    color: document.getElementById('emissive-color').value,
                    intensity: parseFloat(document.getElementById('emissive-intensity').value)
                },
                edges: {
                    show: document.getElementById('show-edges').checked,
                    color: document.getElementById('edge-color').value,
                    opacity: parseFloat(document.getElementById('edge-opacity').value)
                },
                bloom: {
                    enabled: document.getElementById('enable-bloom').checked,
                    strength: parseFloat(document.getElementById('bloom-strength').value),
                    radius: parseFloat(document.getElementById('bloom-radius').value),
                    threshold: parseFloat(document.getElementById('bloom-threshold').value)
                },
                dimensions: {
                    scale: parseFloat(document.getElementById('prism-scale').value),
                    depth: parseFloat(document.getElementById('prism-depth').value)
                },
                rotation: {
                    auto: document.getElementById('auto-rotate').checked,
                    speed: parseFloat(document.getElementById('rotation-speed').value),
                    angleX: parseFloat(document.getElementById('angle-x').value),
                    angleY: parseFloat(document.getElementById('angle-y').value)
                }
            };

            const blob = new Blob([JSON.stringify(config, null, 2)], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'prism-config.json';
            a.click();
            URL.revokeObjectURL(url);
        };

        window.exportImage = function() {
            renderer.render(scene, camera);
            const dataUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'egi-prism.png';
            a.click();
        };

        // Animation
        let lastTime = 0;
        let frameCount = 0;

        function animate(time) {
            requestAnimationFrame(animate);

            // FPS counter
            frameCount++;
            if (time - lastTime >= 1000) {
                document.getElementById('fps').textContent = `FPS: ${frameCount}`;
                frameCount = 0;
                lastTime = time;
            }

            // Auto rotation
            if (document.getElementById('auto-rotate').checked && prismGroup) {
                const speed = parseFloat(document.getElementById('rotation-speed').value) * 0.01;
                prismGroup.rotation.y += speed;
            } else if (prismGroup) {
                prismGroup.rotation.x = THREE.MathUtils.degToRad(parseFloat(document.getElementById('angle-x').value));
                prismGroup.rotation.y = THREE.MathUtils.degToRad(parseFloat(document.getElementById('angle-y').value));
            }

            controls.update();
            composer.render();
        }

        // Resize handler
        function onResize() {
            const container = canvas.parentElement;
            const width = container.clientWidth;
            const height = container.clientHeight;

            camera.aspect = width / height;
            camera.updateProjectionMatrix();

            renderer.setSize(width, height);
            composer.setSize(width, height);
        }

        window.addEventListener('resize', onResize);
        onResize();

        // Start
        animate(0);

        // Update object count
        document.getElementById('objects').textContent = `Objects: ${scene.children.length}`;
    </script>
</body>

</html>
