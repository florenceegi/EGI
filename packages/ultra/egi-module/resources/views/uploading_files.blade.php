<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ trans('uploadmanager::uploadmanager.first_template_title') }}</title>

    @vite(['resources/css/app.css', 'resources/css/modal-fix.css', 'resources/js/app.js', 'vendor/ultra/ultra-upload-manager/resources/css/app.css', 'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'])

    {{-- Icone di MaterialIcons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />

    <!-- Styles -->
    @livewireStyles

    <style>
        body {
            /* background-color: #0f1018; */
            /* min-height: 100vh; Assicura che il body copra almeno l'intera altezza della viewport */
            /* margin: 0; */
            /* padding: 0; */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            /* overflow-x: hidden; */
        }

        /* .content {
        position: relative;
        z-index: 1;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        } */

        /* h1 {
        color: #0f0;
        text-shadow: 0 0 5px #0f0;
        }

        p {
        line-height: 1.6;
        }

        /* Rendere i link più visibili sullo sfondo Matrix */
        /* a {
        color: #0ff;
        text-decoration: none;
        font-weight: bold;
        }

        a:hover {
        text-decoration: underline;
        text-shadow: 0 0 5px #0ff;
        } */
        */
    </style>

</head>

<body id="uploading_files">

    {{-- Feature Purchase Modal (Egili Living) - MUST be loaded BEFORE radio listener --}}
    <x-feature-purchase-modal featureCode="egi_living_subscription" />

    <div class="drawer lg:drawer-open">
        <!-- Questo checkbox controlla lo stato del drawer -->
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-screen flex-col">

            <livewire:navigation-menu />

            <div class="three-column-layout mt-24">

                <!-- Left Column (Matrix) -->
                {{-- <div class="left-column">
                    @include('egimodule::partials.matrix_animation')
                </div> --}}

                <div class="left-column mt-2">
                    @include('egimodule::partials.logo3d')
                </div>

                <!-- Center Column (Main Form) -->
                <div class="center-column mt-2">
                    <div class="nft-background relative rounded-2xl border border-purple-500/30 bg-gradient-to-br from-gray-800 via-purple-900 to-blue-900 p-8 shadow-2xl"
                        id="upload-container">
                        <!-- Title with EGI style -->
                        <h2
                            class="nft-title mb-6 text-center text-4xl font-extrabold tracking-wide text-white drop-shadow-lg">
                            💎 {{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}
                        </h2>

                        <!-- Enterprise Features Badges -->
                        <div class="mb-6 flex flex-wrap justify-center gap-3">
                            <div class="flex items-center rounded-lg bg-blue-900/60 px-3 py-1.5 text-sm font-medium text-blue-200 shadow-md"
                                title="{{ trans('uploadmanager::uploadmanager.secure_storage_tooltip') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                {{ trans('uploadmanager::uploadmanager.secure_storage') }}
                            </div>
                            <div class="flex items-center rounded-lg bg-purple-900/60 px-3 py-1.5 text-sm font-medium text-purple-200 shadow-md"
                                title="{{ trans('uploadmanager::uploadmanager.virus_scan_tooltip') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ trans('uploadmanager::uploadmanager.virus_scan_feature') }}
                            </div>
                            <div class="flex items-center rounded-lg bg-green-900/60 px-3 py-1.5 text-sm font-medium text-green-200 shadow-md"
                                title="{{ trans('uploadmanager::uploadmanager.advanced_validation_tooltip') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ trans('uploadmanager::uploadmanager.advanced_validation') }}
                            </div>
                            <div class="flex items-center rounded-lg bg-indigo-900/60 px-3 py-1.5 text-sm font-medium text-indigo-200 shadow-md"
                                title="{{ trans('uploadmanager::uploadmanager.storage_space_tooltip') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>
                                    <span id="storage-used">2.4</span>/<span id="storage-total">50</span>
                                    {{ trans('uploadmanager::uploadmanager.storage_space_unit') }}
                                </span>
                            </div>
                        </div>

                        <!-- Enhanced drag & drop upload area -->
                        <div class="group mb-6 flex h-64 w-full flex-col items-center justify-center rounded-2xl border-4 border-dashed border-blue-400/50 bg-purple-800/20 p-8 transition-all duration-300 hover:bg-purple-800/30"
                            id="upload-drop-zone">
                            <!-- Drag & drop icon/illustration -->
                            <div
                                class="mb-4 text-5xl text-blue-400 transition-transform duration-300 group-hover:scale-110">
                                📤
                            </div>
                            <!-- Instructions with improved contrast (Punto 5) -->
                            <p class="mb-6 text-center text-xl text-white">
                                {{ trans('uploadmanager::uploadmanager.drag_files_here') }} <br>
                                <span
                                    class="text-sm text-blue-200">{{ trans('uploadmanager::uploadmanager.or') }}</span>
                            </p>
                            <!-- Button styled with tooltip (Punto 5) -->
                            <label for="files" id="file-label"
                                class="nft-button group relative flex cursor-pointer items-center justify-center rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-4 text-lg font-semibold text-white transition-all duration-300 ease-in-out hover:from-purple-500 hover:to-blue-500 hover:shadow-xl"
                                aria-label="{{ trans('uploadmanager::uploadmanager.select_files_aria') }}">
                                {{ trans('uploadmanager::uploadmanager.select_files') }}
                                <input type="file" id="files" multiple
                                    class="absolute left-0 top-0 h-full w-full cursor-pointer opacity-0">
                                <!-- Tooltip (Punto 5) -->
                                <span
                                    class="absolute -top-12 left-1/2 w-48 -translate-x-1/2 transform rounded bg-gray-800 px-2 py-1 text-center text-xs text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ trans('uploadmanager::uploadmanager.select_files_tooltip') }}
                                </span>
                            </label>
                            <div class="upload-dropzone mt-2 text-center text-sm text-gray-200">
                                <!-- About upload size -->
                            </div>
                        </div>

                        {{-- Metadata --}}
                        @include('egimodule::partials.metadata')

                        <!-- Progress bar and virus switch -->
                        <div class="mt-6 space-y-6">
                            <div class="h-3 w-full overflow-hidden rounded-full bg-gray-700">
                                <div class="h-3 rounded-full bg-gradient-to-r from-green-400 to-blue-500 transition-all duration-500"
                                    id="progress-bar"></div>
                            </div>
                            <p class="text-center text-sm text-gray-200"><span id="progress-text"></span></p>

                            <div class="flex items-center justify-center gap-3">
                                <input
                                    class="me-2 h-4 w-8 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-4 before:w-4 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:shadow-md after:transition-all checked:bg-purple-600 checked:after:ms-4 checked:after:bg-purple-400 checked:after:shadow-md hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                                    type="checkbox" role="switch" id="scanvirus"
                                    title="{{ trans('uploadmanager::uploadmanager.toggle_virus_scan') }}" />
                                <label class="font-medium text-red-400 hover:pointer-events-none" id="scanvirus_label"
                                    for="scanvirus">{{ trans('uploadmanager::uploadmanager.virus_scan_disabled') }}</label>
                            </div>
                            <p class="text-center text-sm text-gray-200"><span id="virus-advise"></span></p>
                        </div>

                        <!-- Action buttons with EGI style and tooltips -->
                        <div class="mt-10 flex justify-center space-x-6">
                            <button type="button" id="uploadBtn"
                                class="nft-button group relative cursor-not-allowed rounded-full bg-green-500 px-8 py-4 text-lg font-semibold text-white opacity-50 disabled:hover:bg-green-500 disabled:hover:shadow-none"
                                aria-label="{{ trans('uploadmanager::uploadmanager.save_aria') }}">
                                💾 {{ trans('uploadmanager::uploadmanager.save_the_files') }}
                                <span
                                    class="pointer-events-none absolute -top-12 left-1/2 w-48 -translate-x-1/2 transform rounded bg-gray-800 px-2 py-1 text-center text-xs text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ trans('uploadmanager::uploadmanager.save_tooltip') }}
                                </span>
                            </button>
                            <button type="button" onclick="cancelUpload()" id="cancelUpload"
                                class="nft-button group relative cursor-not-allowed rounded-full bg-red-500 px-8 py-4 text-lg font-semibold text-white opacity-50 disabled:hover:bg-red-500 disabled:hover:shadow-none"
                                aria-label="{{ trans('uploadmanager::uploadmanager.cancel_aria') }}">
                                ❌ {{ trans('uploadmanager::uploadmanager.cancel') }}
                                <span
                                    class="pointer-events-none absolute -top-12 left-1/2 w-48 -translate-x-1/2 transform rounded bg-gray-800 px-2 py-1 text-center text-xs text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ trans('uploadmanager::uploadmanager.cancel_tooltip') }}
                                </span>
                            </button>
                        </div>

                        <!-- Previews grid -->
                        <div id="collection" class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                            <!-- Previews will be loaded dynamically via JS -->
                        </div>

                        <!-- Return to collection button with tooltip -->
                        <div class="mt-6 flex justify-center">
                            <button type="button" onclick="redirectToCollection()" id="returnToCollection"
                                class="nft-button group relative rounded-full bg-gray-700 px-8 py-4 text-lg font-semibold text-white hover:bg-gray-600"
                                aria-label="{{ trans('uploadmanager::uploadmanager.return_aria') }}">
                                🔙 {{ trans('uploadmanager::uploadmanager.return_to_collection') }}
                                <span
                                    class="pointer-events-none absolute -top-12 left-1/2 w-48 -translate-x-1/2 transform rounded bg-gray-800 px-2 py-1 text-center text-xs text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ trans('uploadmanager::uploadmanager.return_tooltip') }}
                                </span>
                            </button>
                        </div>

                        <!-- Scan progress with improved contrast -->
                        <div class="mt-10 text-center">
                            <p class="text-sm text-gray-200"><span id="scan-progress-text"></span></p>
                        </div>

                        <!-- Status showEmoji-->
                        <div id="status" class="mx-auto mt-6 w-48 p-4 text-center text-sm text-gray-200"></div>

                        <!-- Upload status -->
                        <div id="upload-status" class="mt-8 text-center text-gray-200">
                            <p id="status-message">{{ trans('uploadmanager::uploadmanager.preparing_to_mint') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column (3D Animation) -->
                <div id="post-upload-card-container" class="right-column mt-2">
                    @include('egimodule::partials.tunnel')
                </div>

            </div>
        </div>
        <livewire:sidebar />
        @stack('modals')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Crea il canvas per l'effetto Matrix
            const matrixCanvas = document.createElement('canvas');
            const matrixCtx = matrixCanvas.getContext('2d');

            // Stile del canvas per coprire l'intero body come sfondo
            matrixCanvas.style.position = 'fixed';
            matrixCanvas.style.top = '0';
            matrixCanvas.style.left = '0';
            matrixCanvas.style.width = '100%';
            matrixCanvas.style.height = '100%';
            matrixCanvas.style.zIndex = '-1'; // Dietro tutti gli altri elementi
            matrixCanvas.style.opacity = '0.7'; // Leggermente trasparente per leggibilità

            // Aggiungi il canvas al body
            document.body.prepend(matrixCanvas);

            // Imposta dimensioni del canvas
            function resizeMatrixCanvas() {
                matrixCanvas.width = window.innerWidth;
                matrixCanvas.height = window.innerHeight;
            }

            window.addEventListener('resize', resizeMatrixCanvas);
            resizeMatrixCanvas();

            // Caratteri da utilizzare (stile Matrix)
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';

            // Dimensione del font e spaziatura
            const fontSize = 14;
            const columnWidth = fontSize * 1.05; // Spaziatura orizzontale
            const rowHeight = fontSize * 1.1; // Spaziatura verticale

            // Calcola il numero di colonne basato sulla larghezza
            const columns = Math.floor(matrixCanvas.width / columnWidth);

            // Array per tenere traccia della posizione Y di ogni colonna
            const drops = [];

            // Inizializza tutte le colonne
            for (let i = 0; i < columns; i++) {
                // Inizia ogni colonna a una posizione Y casuale sopra il canvas (valori negativi)
                drops[i] = -Math.random() * 50; // Inizia sopra il canvas con diversi offset
            }

            // Variabili per controllare la velocità
            let lastTime = 0;
            const frameRate = 45; // Come hai trovato ottimale

            // Funzione di disegno principale
            function drawMatrix(timestamp) {
                // Controlla se è il momento di aggiornare il frame
                if (timestamp - lastTime < 1000 / frameRate) {
                    requestAnimationFrame(drawMatrix);
                    return;
                }
                lastTime = timestamp;

                // Effetto scia semi-trasparente - ridotto per far durare più a lungo le scie
                matrixCtx.fillStyle = 'rgba(0, 0, 0, 0.02)'; // Ridotto da 0.05 a 0.02
                matrixCtx.fillRect(0, 0, matrixCanvas.width, matrixCanvas.height);

                // Imposta il font
                matrixCtx.font = `${fontSize}px monospace`;

                for (let i = 0; i < drops.length; i++) {
                    // Scegli un carattere casuale
                    const char = chars[Math.floor(Math.random() * chars.length)];

                    // Posizione attuale della testa della colonna
                    const yPos = drops[i] * rowHeight;

                    // Disegna solo se la posizione è visibile (all'interno del canvas)
                    if (yPos >= 0 && yPos < matrixCanvas.height) {
                        // Colori diversi in base alla posizione nella colonna
                        if (drops[i] < 1) {
                            matrixCtx.fillStyle = '#fff'; // Testa bianca per le prime posizioni
                        } else if (drops[i] < 5) {
                            matrixCtx.fillStyle = '#0f0'; // Verde brillante per i primi caratteri
                        } else {
                            // Gradazioni di verde più scure per il resto della colonna
                            // Modificata la formula per far durare più a lungo il colore
                            const factor = Math.min(1, drops[i] / (matrixCanvas.height / rowHeight * 0.9));
                            const greenIntensity = Math.max(50, 255 - (factor * 200));
                            matrixCtx.fillStyle = `rgba(0, ${greenIntensity}, 0, 0.8)`;
                        }

                        // Disegna il carattere
                        const x = i * columnWidth;
                        matrixCtx.fillText(char, x, yPos);
                    }

                    // Muovi la goccia verso il basso
                    drops[i] += 0.3;

                    // Ripristina la posizione quando la goccia esce dallo schermo (in basso)
                    if (yPos > matrixCanvas.height) {
                        drops[i] = -Math.random() * 10; // Riparti da sopra il canvas
                    }
                }

                // Continua l'animazione
                requestAnimationFrame(drawMatrix);
            }

            // Avvia l'animazione
            requestAnimationFrame(drawMatrix);
        });
    </script>

    {{-- EGI Channel: Cross-tab refresh for EGI creation (OS3 Vanilla JS) --}}
    @auth
        <script src="{{ asset('js/egi-channel.js') }}"></script>
    @endauth

</body>

</html>
