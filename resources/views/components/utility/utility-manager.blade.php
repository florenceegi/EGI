{{-- Component Utility Manager per EGI con localizzazione multilingua --}}
@if ($canEdit)
    <div class="utility-manager-component mt-6 rounded-lg bg-white p-6 shadow-lg" style="position: relative;">

        {{-- 🔒 LOCK OVERLAY se EGI è mintato --}}
        @if ($egi->token_EGI)
            <div
                class="absolute inset-0 z-50 flex flex-col items-center justify-center rounded-lg bg-gray-900/95 backdrop-blur-sm">
                <div class="max-w-md p-6 text-center">
                    <div class="mb-4 flex justify-center">
                        <svg class="h-16 w-16 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-amber-300">🔒 Utility Immutabile</h3>
                    <p class="mb-4 text-sm text-gray-300">
                        Questo EGI è stato mintato su blockchain (ASA #{{ $egi->token_EGI }}).
                    </p>
                    <p class="text-xs text-gray-400">
                        L'utility è ora certificata e non può più essere modificata per garantire l'integrità della
                        blockchain.
                    </p>
                    <a href="https://testnet.explorer.perawallet.app/asset/{{ $egi->token_EGI }}" target="_blank"
                        class="mt-4 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                        Verifica su Blockchain
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <div class="utility-header mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">
                <span class="mr-2">⚡</span>
                {{ __('utility.title') }}
            </h3>

            @if ($utility)
                <span class="rounded-full bg-green-100 px-3 py-1 text-sm text-green-800">
                    {{ __('utility.status_configured') }}
                </span>
            @else
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600">
                    {{ __('utility.status_none') }}
                </span>
            @endif
        </div>

        {{-- Alert informativo --}}
        <div class="alert alert-info mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
            <p class="text-sm text-blue-800">
                <strong>ℹ️ {{ __('utility.note') }}:</strong>
                {{ __('utility.info_edit_before_publish') }}
            </p>
        </div>

        {{-- Errori di validazione --}}
        @if ($errors->any())
            <div class="alert-danger alert mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="text-red-400">❌</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ __('utility.validation.errors_found') }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Utility --}}
        <form id="utility-form" action="{{ $utility ? route('utilities.update', $utility) : route('utilities.store') }}"
            method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateFormBeforeSubmit()">

            @csrf
            @if ($utility)
                @method('PUT')
            @endif

            <input type="hidden" name="egi_id" value="{{ $egi->id }}">

            {{-- Selezione Tipo --}}
            <div class="form-group">
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    {{ __('utility.types.label') }}
                </label>

                <div class="grid grid-cols-2 gap-4">
                    @foreach ($utilityTypes as $type => $info)
                        <label class="utility-type-option cursor-pointer">
                            <input type="radio" name="type" value="{{ $type }}"
                                {{ $utility && $utility->type === $type ? 'checked' : '' }} class="peer hidden"
                                onchange="toggleUtilitySections('{{ $type }}')">

                            <div
                                class="peer-checked:border-primary-500 peer-checked:bg-primary-50 rounded-lg border-2 border-gray-200 p-4 transition hover:border-gray-300">
                                <div class="mb-2 flex items-center">
                                    <span class="mr-2 text-2xl">{{ $info['icon'] }}</span>
                                    <span class="font-semibold">{{ $info['label'] }}</span>
                                </div>
                                <p class="text-xs text-gray-600">{{ $info['description'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Sezione Dettagli Base (sempre visibile se type selezionato) --}}
            <div id="utility-base-section" style="display: {{ $utility ? 'block' : 'none' }}">
                {{-- Titolo --}}
                <div class="form-group">
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('utility.fields.title') }} *
                    </label>
                    <input type="text" name="title" value="{{ $utility?->title }}"
                        class="focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @else border-gray-300 @enderror w-full rounded-lg border px-3 py-2"
                        placeholder="{{ __('utility.fields.title_placeholder') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descrizione --}}
                <div class="form-group">
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('utility.fields.description') }}
                    </label>
                    <textarea name="description" rows="4"
                        class="focus:ring-primary-500 focus:border-primary-500 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="{{ __('utility.fields.description_placeholder') }}">{{ $utility?->description }}</textarea>
                </div>
            </div>

            {{-- Sezione PHYSICAL (mostrata solo se type = physical/hybrid) --}}
            <div id="utility-physical-section" style="display: none" class="rounded-lg bg-gray-50 p-4">

                <h4 class="mb-4 font-semibold text-gray-800">
                    <span class="mr-2">🚚</span>
                    {{ __('utility.shipping.title') }}
                </h4>

                {{-- Info Escrow basato sul prezzo --}}
                <div
                    class="escrow-info bg-{{ $escrowTiers['tier'] === 'immediate' ? 'green' : ($escrowTiers['tier'] === 'standard' ? 'yellow' : 'orange') }}-50 border-{{ $escrowTiers['tier'] === 'immediate' ? 'green' : ($escrowTiers['tier'] === 'standard' ? 'yellow' : 'orange') }}-200 mb-4 rounded border p-3">
                    <p class="mb-1 text-sm font-semibold">
                        {{ $escrowTiers['label'] }}
                    </p>
                    <p class="text-xs text-gray-700">
                        {{ $escrowTiers['description'] }}
                    </p>
                    @if (count($escrowTiers['requirements']) > 0)
                        <ul class="mt-2 text-xs text-gray-600">
                            @foreach ($escrowTiers['requirements'] as $req)
                                <li>• {{ $req }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Peso --}}
                    <div class="form-group">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ __('utility.shipping.weight') }}
                        </label>
                        <input type="number" name="weight" step="0.1" value="{{ $utility?->weight }}"
                            class="@error('weight') border-red-500 bg-red-50 @else border-gray-300 @enderror w-full rounded-lg border px-3 py-2">
                        @error('weight')
                            <p class="mt-1 flex items-center text-sm text-red-600">
                                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('utility.shipping.weight_help') ?? 'Peso necessario per calcolare i costi di spedizione' }}
                            </p>
                        @enderror
                    </div>

                    {{-- Giorni spedizione --}}
                    <div class="form-group">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ __('utility.shipping.days') }}
                        </label>
                        <input type="number" name="estimated_shipping_days"
                            value="{{ $utility?->estimated_shipping_days ?? 5 }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>

                {{-- Dimensioni --}}
                <div class="form-group">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('utility.shipping.dimensions') }}
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="number" name="dimensions[length]"
                            placeholder="{{ __('utility.shipping.length') }}"
                            value="{{ $utility?->dimensions['length'] ?? '' }}"
                            class="rounded-lg border border-gray-300 px-3 py-2">
                        <input type="number" name="dimensions[width]"
                            placeholder="{{ __('utility.shipping.width') }}"
                            value="{{ $utility?->dimensions['width'] ?? '' }}"
                            class="rounded-lg border border-gray-300 px-3 py-2">
                        <input type="number" name="dimensions[height]"
                            placeholder="{{ __('utility.shipping.height') }}"
                            value="{{ $utility?->dimensions['height'] ?? '' }}"
                            class="rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>

                {{-- Checkbox opzioni --}}
                <div class="mt-4 flex items-center space-x-6">
                    <label class="flex cursor-pointer items-center">
                        <input type="checkbox" name="fragile" value="1"
                            {{ $utility?->fragile ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">{{ __('utility.shipping.fragile') }}</span>
                    </label>

                    <label class="flex cursor-pointer items-center">
                        <input type="checkbox" name="insurance_recommended" value="1"
                            {{ $utility?->insurance_recommended ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">{{ __('utility.shipping.insurance') }}</span>
                    </label>
                </div>

                {{-- Note spedizione --}}
                <div class="form-group mt-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('utility.shipping.notes') }}
                    </label>
                    <textarea name="shipping_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="{{ __('utility.shipping.notes_placeholder') }}">{{ $utility?->shipping_notes }}</textarea>
                </div>
            </div>

            {{-- Sezione SERVICE (mostrata solo se type = service/hybrid) --}}
            <div id="utility-service-section" style="display: none" class="rounded-lg bg-gray-50 p-4">

                <h4 class="mb-4 font-semibold text-gray-800">
                    <span class="mr-2">🎯</span>
                    {{ __('utility.service.title') }}
                </h4>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Validità --}}
                    <div class="form-group">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ __('utility.service.valid_from') }}
                        </label>
                        <input type="date" name="valid_from" value="{{ $utility?->valid_from }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div class="form-group">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ __('utility.service.valid_until') }}
                        </label>
                        <input type="date" name="valid_until" value="{{ $utility?->valid_until }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>

                {{-- Numero utilizzi --}}
                <div class="form-group">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('utility.service.max_uses') }}
                    </label>
                    <input type="number" name="max_uses" value="{{ $utility?->max_uses }}"
                        placeholder="{{ __('utility.service.max_uses_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>

                {{-- Istruzioni attivazione --}}
                <div class="form-group">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('utility.service.instructions') }}
                    </label>
                    <textarea name="activation_instructions" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="{{ __('utility.service.instructions_placeholder') }}">{{ $utility?->activation_instructions }}</textarea>
                </div>
            </div>

            {{-- Upload Media Gallery --}}
            <div id="utility-media-section" style="display: {{ $utility ? 'block' : 'none' }}"
                class="rounded-lg bg-gray-800/30 p-4">

                <h4 class="mb-4 font-semibold text-white">
                    <span class="mr-2">📸</span>
                    {{ __('utility.media.title') }}
                </h4>

                <p class="mb-4 text-sm text-gray-400">
                    {{ __('utility.media.description') }}
                </p>

                {{-- Drag & Drop Area --}}
                <div class="upload-area rounded-lg border-2 border-dashed border-gray-600 p-6 text-center transition-colors hover:border-emerald-500"
                    ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"
                    ondragenter="dragEnterHandler(event);" ondragleave="dragLeaveHandler(event);">
                    <input type="file" name="gallery[]" multiple accept="image/*" class="hidden"
                        id="gallery-upload" onchange="fileSelectHandler(event);">

                    <label for="gallery-upload" class="block cursor-pointer">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-2 text-sm">
                                {{ __('utility.media.upload_prompt') }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('utility.js.drag_drop_text') }}
                            </p>
                        </div>
                    </label>
                </div>

                {{-- Preview area per nuove immagini --}}
                <div id="image-preview" class="mt-4 grid grid-cols-4 gap-2" style="display: none;">
                    <!-- Qui verranno mostrate le anteprime delle immagini selezionate -->
                </div>

                {{-- Preview immagini esistenti --}}
                @if ($utility && $utility->getMedia('utility_gallery')->count() > 0)
                    <div class="existing-images mt-4">
                        <p class="mb-2 text-sm font-medium text-gray-300">{{ __('utility.media.current_images') }}</p>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($utility->getMedia('utility_gallery') as $media)
                                <div class="group relative">
                                    <img src="{{ $media->getUrl('thumb') }}"
                                        class="h-24 w-full rounded border border-gray-600 object-cover">
                                    <button type="button" onclick="removeMedia({{ $media->id }})"
                                        class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 p-1 text-xs text-white opacity-0 transition group-hover:opacity-100">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Pulsanti Azione --}}
            <div class="flex items-center justify-between border-t pt-6">
                <button type="button" onclick="resetUtilityForm()" class="text-gray-600 hover:text-gray-800">
                    {{ __('label.cancel') }}
                </button>

                <div class="flex items-center gap-3">
                    @if ($utility)
                        <button type="button" id="delete-utility-btn" onclick="confirmDeleteUtility()"
                            class="rounded-lg border border-red-700 bg-red-600 px-4 py-2 text-white transition-all duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span class="flex items-center gap-2">
                                <span>🗑️</span>
                                {{ __('utility.actions.delete') }}
                            </span>
                        </button>
                    @endif

                    <button type="submit" id="utility-submit-btn"
                        class="rounded-lg bg-emerald-600 px-6 py-2 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:bg-gray-400">
                        {{ $utility ? __('label.update') : __('label.save') }} {{ __('utility.title') }}
                    </button>
                </div>
            </div>

            {{-- Toast Container --}}
            <div class="toast-container" id="toast-container-utility"></div>
        </form>
    </div>

    {{-- JavaScript per gestione form con testi localizzati --}}
    <script>
        // Traduzioni per JavaScript
        const utilityTranslations = {
            waitUploadCompletion: '{{ __('utility.validation.wait_upload_completion') }}',
            waitBeforeSave: '{{ __('utility.validation.wait_before_save') }}',
            uploadInProgress: '{{ __('utility.validation.upload_in_progress') }}',
            selectImagesOnly: '{{ __('utility.validation.select_images_only') }}',
            imagesTooLarge: '{{ __('utility.validation.images_too_large') }}',
            uploading: '{{ __('utility.js.uploading') }}',
            selectType: '{{ __('utility.validation.select_type') }}',
            titleRequired: '{{ __('utility.validation.title_required') }}',
            weightRequiredPhysical: '{{ __('utility.validation.weight_required_physical') }}',
            correctErrors: '{{ __('utility.validation.correct_errors') }}',
            confirmDeleteTitle: '{{ __('utility.actions.confirm_delete_title') }}',
            confirmDeleteMessage: '{{ __('utility.actions.confirm_delete_message') }}',
            confirmDeleteButton: '{{ __('utility.actions.delete') }}',
            cancelButton: '{{ __('traits.cancel') }}',
            deleteSuccess: '{{ __('utility.actions.delete_success') }}',
            deleteError: '{{ __('utility.actions.delete_error') }}'
        };

        // Variabili globali
        let selectedFiles = [];
        let isUploading = false;

        // Toast Notification System (copied from traits-viewer)
        window.ToastManager = {
            container: null,

            init() {
                this.container = document.getElementById('toast-container-utility');
                if (!this.container) {
                    this.container = document.createElement('div');
                    this.container.className = 'toast-container';
                    this.container.id = 'toast-container-utility';
                    document.body.appendChild(this.container);
                }
            },

            show(message, type = 'info', title = null, duration = 4000) {
                this.init();

                const toast = document.createElement('div');
                toast.className = `toast ${type}`;

                const icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                const content = `
            <div class="toast-content">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <div class="toast-text">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
                    <div class="toast-message">${message}</div>
                </div>
            </div>
            <button class="toast-close" onclick="ToastManager.close(this.parentNode)">×</button>
            <div class="toast-progress animate"></div>
        `;

                toast.innerHTML = content;
                this.container.appendChild(toast);

                // Trigger animation
                setTimeout(() => toast.classList.add('show'), 10);

                // Auto remove
                setTimeout(() => this.close(toast), duration);

                return toast;
            },

            close(toast) {
                if (!toast || !toast.parentNode) return;

                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            },

            success(message, title = null) {
                return this.show(message, 'success', title);
            },

            error(message, title = null) {
                return this.show(message, 'error', title);
            },

            warning(message, title = null) {
                return this.show(message, 'warning', title);
            },

            info(message, title = null) {
                return this.show(message, 'info', title);
            }
        };

        // Definizione delle funzioni prima del loro utilizzo
        function toggleUtilitySections(type) {
            // Mostra sezione base
            document.getElementById('utility-base-section').style.display = type ? 'block' : 'none';
            document.getElementById('utility-media-section').style.display = type ? 'block' : 'none';

            // Mostra/nascondi sezioni specifiche
            const showPhysical = ['physical', 'hybrid'].includes(type);
            const showService = ['service', 'hybrid'].includes(type);

            document.getElementById('utility-physical-section').style.display = showPhysical ? 'block' : 'none';
            document.getElementById('utility-service-section').style.display = showService ? 'block' : 'none';

            // Peso non è più required - rimosso per permettere utility senza peso
        }

        function checkUploadStatus() {
            if (isUploading) {
                console.log('Upload in corso'); // Debug temporaneo
                // ToastManager.warning(utilityTranslations.waitBeforeSave, utilityTranslations.waitUploadCompletion);
                return false;
            }
            return true;
        }

        // Funzione semplificata per controlli upload (mantenuta per compatibilità)
        function validateFormBeforeSubmit() {
            return checkUploadStatus();
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('utility-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = isUploading;
                if (isUploading) {
                    submitBtn.title = utilityTranslations.waitUploadCompletion;
                } else {
                    submitBtn.title = '';
                }
            }
        }

        // Funzioni di supporto per file upload

        function resetUtilityForm() {
            if (confirm('{{ __('utility.confirm_reset') }}')) {
                document.getElementById('utility-form').reset();
                selectedFiles = [];
                isUploading = false;
                updateSubmitButton();
                document.getElementById('image-preview').style.display = 'none';
                document.getElementById('image-preview').innerHTML = '';
                toggleUtilitySections('');
            }
        }

        function removeMedia(mediaId) {
            if (confirm('{{ __('utility.confirm_remove_image') }}')) {
                // Aggiungi input nascosto per rimozione
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_media[]';
                input.value = mediaId;
                document.getElementById('utility-form').appendChild(input);

                // Nascondi visivamente l'immagine
                event.target.closest('.relative').style.display = 'none';
            }
        }

        // Drag & Drop handlers
        function dragOverHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.add('border-emerald-500', 'bg-gray-700/30');
        }

        function dragEnterHandler(ev) {
            ev.preventDefault();
        }

        function dragLeaveHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('border-emerald-500', 'bg-gray-700/30');
        }

        function dropHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('border-emerald-500', 'bg-gray-700/30');

            const files = ev.dataTransfer.files;
            handleFiles(files);
        }

        function fileSelectHandler(ev) {
            const files = ev.target.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));

            if (imageFiles.length === 0) {
                console.log('File non validi'); // Debug temporaneo
                // ToastManager.error(utilityTranslations.selectImagesOnly, '🚫 File non validi');
                return;
            }

            // Verifica dimensione massima (10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB
            const oversizedFiles = imageFiles.filter(file => file.size > maxSize);

            if (oversizedFiles.length > 0) {
                console.log('File troppo grandi'); // Debug temporaneo
                // ToastManager.error(utilityTranslations.imagesTooLarge, '📏 File troppo grandi');
                return;
            }

            // Inizio upload - disabilita pulsante
            isUploading = true;
            updateSubmitButton();

            // Aggiungi file all'array
            selectedFiles.push(...imageFiles);

            // Aggiorna l'input file con tutti i file selezionati
            updateFileInput();

            // Mostra preview
            showImagePreviews();

            // Simula completamento upload dopo breve delay
            setTimeout(() => {
                isUploading = false;
                updateSubmitButton();
                console.log('Upload completato'); // Debug temporaneo
                // ToastManager.success('{{ __('utility.upload_completed') }}', '✅ Upload completato');
            }, 1500);
        }

        function updateFileInput() {
            const input = document.getElementById('gallery-upload');
            const dt = new DataTransfer();

            selectedFiles.forEach(file => {
                dt.items.add(file);
            });

            input.files = dt.files;
        }

        function showImagePreviews() {
            const previewContainer = document.getElementById('image-preview');

            if (selectedFiles.length === 0) {
                previewContainer.style.display = 'none';
                return;
            }

            previewContainer.style.display = 'grid';
            previewContainer.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                <img src="${e.target.result}" class="object-cover w-full h-24 border border-gray-600 rounded">
                <button type="button" onclick="removeSelectedFile(${index})"
                        class="absolute flex items-center justify-center w-6 h-6 p-1 text-xs text-white transition bg-red-500 rounded-full opacity-0 top-1 right-1 group-hover:opacity-100">
                    ×
                </button>
                <div class="absolute px-1 text-xs text-white rounded bottom-1 left-1 bg-black/50">
                    ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                </div>
            `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeSelectedFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            showImagePreviews();
        }

        // Inizializza stato form se utility esistente
        @if ($utility)
            toggleUtilitySections('{{ $utility->type }}');
        @endif

        // Inizializza stato pulsante al caricamento
        document.addEventListener('DOMContentLoaded', function() {
            updateSubmitButton();

            // Aggiungi listener per il campo peso
            const weightInputField = document.querySelector('input[name="weight"]');
            if (weightInputField) {
                weightInputField.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-500',
                            'bg-red-50');
                        this.setAttribute('placeholder', '');
                    }
                });
            }

            // Aggiungi listener per il campo titolo
            const titleInput = document.querySelector('input[name="title"]');
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500', 'bg-red-50');
                    }
                });
            }

            // Aggiungi listener per i radio button del tipo
            const typeInputs = document.querySelectorAll('input[name="type"]');
            typeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Rimuovi eventuali messaggi di errore quando viene selezionato un tipo
                    const errorAlerts = document.querySelectorAll('.alert-danger');
                    errorAlerts.forEach(alert => {
                        if (alert.textContent.includes('Seleziona un tipo')) {
                            alert.style.display = 'none';
                        }
                    });

                    // Pulisci anche gli errori visuali da tutti i campi quando cambia il tipo
                    const fieldsToClean = ['title', 'weight'];
                    fieldsToClean.forEach(fieldName => {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            field.classList.remove('border-red-500', 'bg-red-50',
                                'border-yellow-400', 'bg-yellow-50');
                        }
                    });
                });
            });

            // Event listener per il bottone di submit per validazione
            const submitBtn = document.getElementById('utility-submit-btn');
            if (submitBtn) {
                let isValidating = false; // Protezione contro click multipli

                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Previeni il submit diretto

                    // Evita click multipli durante la validazione
                    if (isValidating) {
                        return;
                    }

                    // Controlla upload in corso
                    if (!checkUploadStatus()) {
                        return;
                    }

                    isValidating = true;

                    // Feedback visivo durante la validazione
                    const originalText = submitBtn.textContent;
                    submitBtn.textContent = '...';
                    submitBtn.disabled = true;

                    // Validazione immediata (senza timeout per debug)
                    let errors = [];
                    let hasErrors = false;

                    // Controlla se è stato selezionato un tipo di utility
                    const typeInputs = document.querySelectorAll('input[name="type"]:checked');
                    if (typeInputs.length === 0) {
                        errors.push(utilityTranslations.selectType);
                        hasErrors = true;
                    }

                    const selectedType = typeInputs[0]?.value;

                    // Controlla titolo obbligatorio
                    const titleInput = document.querySelector('input[name="title"]');
                    if (!titleInput.value.trim()) {
                        errors.push(utilityTranslations.titleRequired);
                        titleInput.classList.add('border-red-500', 'bg-red-50');
                        // titleInput.focus(); // Rimosso temporaneamente per debug
                        hasErrors = true;
                    } else {
                        titleInput.classList.remove('border-red-500', 'bg-red-50');
                    }

                    // Peso non più required - rimosso per permettere utility senza peso
                    // La validazione server-side gestirà eventuali requisiti specifici

                    // Ripristina il bottone
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    isValidating = false; // Reset protezione

                    // Se ci sono errori, mostra toast
                    if (hasErrors) {
                        let errorList = errors.map((error, index) => `${index + 1}. ${error}`).join('<br>');

                        ToastManager.error(errorList, '❌ ' + utilityTranslations.correctErrors, 6000);
                    } else {
                        // Se la validazione passa, submita il form
                        document.getElementById('utility-form').submit();
                    }
                });
            }

            // Inizializzazione se esiste già una utility
            @if ($utility && $utility->type)
                toggleUtilitySections('{{ $utility->type }}');
            @endif
        });

        // Funzione per confermare ed eliminare l'utility
        async function confirmDeleteUtility() {
            // Usa SweetAlert2 per conferma
            const result = await Swal.fire({
                title: utilityTranslations.confirmDeleteTitle,
                text: utilityTranslations.confirmDeleteMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: utilityTranslations.confirmDeleteButton || 'Elimina',
                cancelButtonText: utilityTranslations.cancelButton || 'Annulla',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                deleteUtility();
            }
        }

        // Funzione per eliminare l'utility
        async function deleteUtility() {
            const deleteBtn = document.getElementById('delete-utility-btn');
            const originalText = deleteBtn.innerHTML;

            try {
                // Feedback visivo
                deleteBtn.innerHTML = '<span class="flex items-center gap-2"><span>⏳</span>Eliminazione...</span>';
                deleteBtn.disabled = true;

                // Costruisci l'URL della route
                const utilityId = {{ $utility ? $utility->id : 'null' }};
                if (!utilityId) {
                    throw new Error('ID utility non disponibile');
                }

                const deleteUrl = `{{ url('/utilities') }}/${utilityId}`;

                // Creiamo un URL assoluto garantito
                const absoluteUrl = new URL(deleteUrl, window.location.origin);

                // Chiamata AJAX per eliminare
                const response = await fetch(absoluteUrl.href, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    // Successo
                    ToastManager.success(utilityTranslations.deleteSuccess, '✅ Eliminazione Completata', 3000);

                    // Reindirizza dopo un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error('Errore HTTP: ' + response.status);
                }

            } catch (error) {
                console.error('Error deleting utility:', error);

                // Ripristina il bottone
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;

                // Mostra errore
                ToastManager.error(utilityTranslations.deleteError, '❌ Errore', 5000);
            }
        }
    </script>

    {{-- CSS per Toast Notifications --}}
    <style>
        /* Toast Container */
        .toast-container {
            position: relative;
            margin-bottom: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 100%;
        }

        /* Base Toast Styles */
        .toast {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border-left: 4px solid #e5e7eb;
            padding: 16px;
            min-height: 60px;
            display: flex;
            align-items: center;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateY(100%);
            opacity: 0;
        }

        /* Toast Types */
        .toast.success {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .toast.error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        }

        .toast.warning {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
        }

        .toast.info {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        }

        /* Toast Content */
        .toast-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1;
        }

        .toast-icon {
            font-size: 20px;
            line-height: 1;
            margin-top: 2px;
        }

        .toast-text {
            flex: 1;
            line-height: 1.4;
        }

        .toast-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .toast-message {
            color: #6b7280;
            font-size: 13px;
        }

        .toast-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #9ca3af;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .toast-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #6b7280;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .toast-progress.animate {
            animation: toast-progress 4s linear forwards;
        }

        @keyframes toast-progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Toast Animations */
        @keyframes toast-slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast {
            animation: toast-slide-in 0.3s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .toast-container {
                top: -80px;
                right: -5px;
                left: -5px;
                max-width: none;
            }

            .toast {
                margin-bottom: 0;
            }
        }
    </style>

@endif {{-- Fine if $canEdit --}}
