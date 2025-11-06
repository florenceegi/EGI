{{-- resources/views/components/egi/traits-viewer.blade.php --}}
{{--
    EGI Traits Viewer Component - with EDIT MODE support
    Visualizzazione dei traits esistenti renderizzati con PHP
    + Edit mode per owner dell'EGI (add/remove immediate)
--}}
@props([
    'egi' => null,
    'canManage' => false,
])

@php
    use App\Helpers\FegiAuth;
    // Controllo autorizzazione: solo il proprietario dell'EGI può editare E solo se NON è pubblicato E NON è mintato
$canEdit =
    $egi &&
    $canManage &&
    FegiAuth::check() &&
    FegiAuth::id() === $egi->user_id &&
    !$egi->is_published &&
    !$egi->token_EGI;
    
// Determina se l'utente è il creator (indipendentemente da pubblicazione)
$isCreator = $egi && FegiAuth::check() && FegiAuth::id() === $egi->user_id;

// Determina se mostrare la sezione: solo se ci sono traits o se l'utente può editare
    $hasTraits = $egi && $egi->traits && $egi->traits->count() > 0;
    $shouldShow = $hasTraits || $canEdit;
@endphp

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

{{-- Include JavaScript integrato per traits e image management --}}
@vite(['resources/js/traits-viewer-integrated.js'])

{{-- Funzioni globali per drag & drop e delete - DEVONO essere definite prima del rendering --}}
<script>
    // Delete trait image function - GLOBALE
    window.deleteTraitImage = async function(traitId) {
        try {
            const response = await fetch(`/traits/${traitId}/delete-image`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
            
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Errore durante la cancellazione');
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Errore durante la cancellazione');
        }
    };

    // Handle drag and drop - GLOBALE
    window.handleTraitImageDrop = function(event, traitId) {
        console.log('🎯 File droppato per trait', traitId);
        
        const files = event.dataTransfer.files;
        if (!files || files.length === 0) {
            console.error('❌ Nessun file droppato');
            return;
        }
        
        const file = files[0];
        console.log('📁 File:', file.name, file.type, file.size);
        
        // Valida che sia un'immagine
        if (!file.type.startsWith('image/')) {
            alert('Per favore seleziona un file immagine valido');
            return;
        }
        
        // Valida dimensione (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Il file è troppo grande. Massimo 5MB');
            return;
        }
        
        // Triggera il file input per attivare il sistema esistente
        const fileInput = document.getElementById(`trait-image-input-${traitId}`);
        if (fileInput) {
            console.log('✅ File input trovato, assegno files...');
            
            // Crea un DataTransfer per simulare la selezione file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            console.log('📦 Files assegnati:', fileInput.files.length, 'file(s)');
            
            // Triggera più eventi per compatibilità
            const changeEvent = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(changeEvent);
            console.log('🔔 Evento change triggerato');
            
            const inputEvent = new Event('input', { bubbles: true });
            fileInput.dispatchEvent(inputEvent);
            console.log('🔔 Evento input triggerato');
            
            // Se dopo 500ms non è partito nulla, usa il listener manuale
            setTimeout(() => {
                console.log('⏱️ Verifico se l\'upload è partito...');
                if (fileInput.files.length > 0) {
                    console.log('⚠️ Upload non partito automaticamente, triggero manualmente');
                    // Cerca il form e triggera direttamente
                    const form = fileInput.closest('form');
                    if (form) {
                        const traitIdInput = form.querySelector('input[name="trait_id"]');
                        if (traitIdInput) {
                            // Trigga direttamente il TraitImageManager se esiste
                            if (window.TraitImageManagerInstance) {
                                console.log('🚀 Usando TraitImageManagerInstance diretto');
                                window.TraitImageManagerInstance.handleImageUpload({ target: fileInput });
                            } else {
                                console.log('⚠️ TraitImageManagerInstance non trovato, uso fallback');
                            }
                        }
                    }
                } else {
                    console.log('✅ Upload già partito!');
                }
            }, 500);
            
        } else {
            console.error('❌ Input file non trovato');
        }
    };
</script>

@if ($shouldShow)
    <div class="egi-traits-viewer" id="traits-viewer-{{ $egi ? $egi->id : 'new' }}"
        data-egi-id="{{ $egi ? $egi->id : '' }}" 
        data-can-edit="{{ $canEdit ? 'true' : 'false' }}"
        data-is-creator="{{ $isCreator ? 'true' : 'false' }}">

        {{-- COLLAPSABILE BOX TRAITS - Semplificato senza bordo --}}
        <details class="group" open>
            <summary class="flex cursor-pointer items-center justify-between rounded-lg bg-gradient-to-r from-purple-900/20 to-indigo-900/20 px-3 py-2.5 md:px-4 md:py-3 text-white transition-colors hover:from-purple-900/30 hover:to-indigo-900/30">
                <div class="flex items-center gap-1.5 md:gap-2">
                    <span class="text-lg md:text-xl">🎯</span>
                    <span class="text-xs md:text-sm font-semibold">{{ __('Tratti e Attributi') }}</span>
                    <span class="trait-counter ml-1 md:ml-2">
                        <span class="traits-count">{{ $egi && $egi->traits ? $egi->traits->count() : 0 }}</span>/30
                    </span>
                </div>
                <svg class="h-3.5 w-3.5 md:h-4 md:w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-3">
                @if ($canEdit)
            {{-- Add Trait Buttons --}}
            <div class="traits-editor-controls mb-3 space-y-2">
                {{-- Manual Add Trait Button --}}
                <button type="button" class="add-trait-btn w-full flex items-center justify-center gap-2 bg-purple-900/20 hover:bg-purple-900/30 text-purple-300 hover:text-purple-200 px-3 py-2 md:px-4 md:py-2.5 rounded-lg font-semibold text-sm md:text-base transition-all duration-200"
                    onclick="if(window.TraitsViewer) { TraitsViewer.openModal(); }">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>{{ __('traits.add_trait_manual') }}</span>
                </button>
                
                {{-- AI Generate Traits Button --}}
                <button type="button" class="ai-traits-btn w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600/20 to-purple-600/20 hover:from-blue-600/30 hover:to-purple-600/30 text-blue-400 hover:text-blue-300 border border-blue-600/50 hover:border-blue-500 px-3 py-2 md:px-4 md:py-2.5 rounded-lg font-semibold text-sm md:text-base transition-all duration-200"
                    onclick="handleTraitsGenerate(event, {{ $egi->id }})">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <span>🤖 {{ __('traits.generate_traits_ai') }}</span>
                </button>
            </div>
        @endif

        {{-- Traits Grid (readonly) renderizzato con PHP - 1 COLONNA per migliore leggibilità --}}
        <div class="traits-list readonly">
            <div class="traits-grid" id="traits-grid-viewer">
                @if ($egi && $egi->traits && $egi->traits->count() > 0)
                    @foreach ($egi->traits as $trait)
                        @php
                            // Carica colore e icona dal database
                            $category = $trait->category;
                            $categoryColor = $category ? $category->color : '#6B6B6B';
                            $categoryIcon = $category ? $category->icon : '🏷️';
                        @endphp

                        <div class="trait-card readonly" data-category="{{ $trait->category_id }}"
                            data-trait-id="{{ $trait->id }}"
                            style="position: relative; 
                                   transition: all 0.2s ease; 
                                   background: rgba(255, 255, 255, 0.25) !important;
                                   border: 2px solid rgba(212, 165, 116, 0.6) !important;
                                   box-shadow: 0 3px 8px rgba(212, 165, 116, 0.3), 0 1px 3px rgba(0, 0, 0, 0.3) !important;
                                   border-radius: 0.5rem !important;">
                            @if ($isCreator)
                                <button type="button" class="trait-remove trait-action-button"
                                    onclick="console.log('Remove button clicked for trait:', {{ $trait->id }}); event.stopPropagation(); event.preventDefault(); if(window.TraitsViewer) { TraitsViewer.removeTrait({{ $trait->id }}); } else { console.error('TraitsViewer not found!'); } return false;"
                                    title="Rimuovi trait"
                                    style="position: absolute;
                                           right: 0.25rem;
                                           top: 0.25rem;
                                           background: rgba(220, 53, 69, 0.9) !important;
                                           color: white !important;
                                           border: none !important;
                                           border-radius: 50% !important;
                                           width: 1.25rem !important;
                                           height: 1.25rem !important;
                                           font-size: 0.875rem !important;
                                           line-height: 1 !important;
                                           cursor: pointer !important;
                                           display: flex !important;
                                           align-items: center !important;
                                           justify-content: center !important;
                                           z-index: 9999 !important;
                                           pointer-events: auto !important;
                                           transition: all 0.2s ease !important;"
                                    onmouseover="this.style.backgroundColor='rgba(220, 53, 69, 1)'; this.style.transform='scale(1.1)';"
                                    onmouseout="this.style.backgroundColor='rgba(220, 53, 69, 0.9)'; this.style.transform='scale(1)';">
                                    ×
                                </button>
                            @endif
                            {{-- Prima riga: Icona + Tipo + Valore --}}
                            <div class="trait-content">
                                <div class="flex items-center gap-2">
                                    {{-- Icona --}}
                                    <span class="trait-category-badge flex-shrink-0" style="background-color: {{ $categoryColor }}">
                                        {{ $categoryIcon }}
                                    </span>
                                    
                                    {{-- Tipo e Valore sulla stessa riga --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="trait-type">
                                                @if ($trait->traitType)
                                                    @php
                                                        $translationKey = \Illuminate\Support\Str::title(strtolower($trait->traitType->name));
                                                    @endphp
                                                    {{ __('trait_elements.types.' . $translationKey, [], null) ?: $trait->traitType->name }}:
                                                @else
                                                    Unknown:
                                                @endif
                                            </span>
                                            <span class="trait-value">
                                                {{ $trait->display_value ?? $trait->value }}
                                                @if ($trait->traitType && $trait->traitType->unit)
                                                    <span class="trait-unit">{{ $trait->traitType->unit }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Barra di rarità - VISIBILE CON STILI INLINE --}}
                                @if (isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                    @php
                                        // Calcola colore basato su rarità
                                        if ($trait->rarity_percentage >= 70) {
                                            $rarityColor = 'linear-gradient(90deg, #27ae60, #2ecc71)'; // Verde - comune
                                        } elseif ($trait->rarity_percentage >= 40) {
                                            $rarityColor = 'linear-gradient(90deg, #f39c12, #e67e22)'; // Arancione - poco comune
                                        } elseif ($trait->rarity_percentage >= 20) {
                                            $rarityColor = 'linear-gradient(90deg, #e74c3c, #c0392b)'; // Rosso - raro
                                        } elseif ($trait->rarity_percentage >= 10) {
                                            $rarityColor = 'linear-gradient(90deg, #9b59b6, #8e44ad)'; // Viola - epico
                                        } elseif ($trait->rarity_percentage >= 5) {
                                            $rarityColor = 'linear-gradient(90deg, #d4a574, #b8860b)'; // Oro - leggendario
                                        } else {
                                            $rarityColor = 'linear-gradient(90deg, #ff6b6b, #ffd700, #ff6b6b)'; // Mitico
                                        }
                                        
                                        // Calcola larghezza: più raro = barra più lunga
                                        if ($trait->rarity_percentage <= 5) {
                                            $barWidth = 90;
                                        } elseif ($trait->rarity_percentage <= 10) {
                                            $barWidth = 75;
                                        } elseif ($trait->rarity_percentage <= 20) {
                                            $barWidth = 60;
                                        } elseif ($trait->rarity_percentage <= 40) {
                                            $barWidth = 40;
                                        } elseif ($trait->rarity_percentage <= 70) {
                                            $barWidth = 25;
                                        } else {
                                            $barWidth = 10;
                                        }
                                    @endphp
                                    <div style="margin-top: 0.375rem; padding-left: 0.5rem; padding-right: 0.5rem;">
                                        <div style="height: 5px; background: rgba(55, 65, 81, 0.5); border-radius: 9999px; overflow: hidden; border: 1px solid rgba(75, 85, 99, 0.3);">
                                            <div style="height: 100%; width: {{ number_format($barWidth, 1) }}%; background: {{ $rarityColor }}; transition: width 0.5s ease;"></div>
                                        </div>
                                        <div style="font-size: 10px; color: #9ca3af; text-align: right; margin-top: 0.125rem;">{{ number_format($trait->rarity_percentage, 1) }}%</div>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Area upload VISIBILE --}}
                            @if ($isCreator)
                                <div class="px-2 pb-1.5 pt-0.5" onclick="event.stopPropagation();">
                                    <form id="trait-image-form-{{ $trait->id }}" class="trait-image-upload-form" onsubmit="event.preventDefault(); return false;">
                                        @csrf
                                        <input type="hidden" name="trait_id" value="{{ $trait->id }}">
                                        
                                        @if($trait->getFirstMedia('trait_images'))
                                            {{-- Immagine esistente con aspect ratio preservato --}}
                                            @php
                                                $media = $trait->getFirstMedia('trait_images');
                                                $thumbnailUrl = $media->getUrl('thumb');
                                            @endphp
                                            <div class="relative group flex items-center justify-center bg-gray-900/30 rounded border-2 border-green-500/60 p-1" style="min-height: 40px;" onclick="event.stopPropagation();">
                                                <img src="{{ $thumbnailUrl }}" alt="{{ $trait->display_value }}"
                                                     class="max-w-full max-h-16 object-contain rounded">
                                                <button type="button" 
                                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                                        onclick="event.stopPropagation(); if(confirm('{{ __('traits.confirm_delete') }}')) { deleteTraitImage({{ $trait->id }}); }">×</button>
                                            </div>
                                        @else
                                            {{-- Area upload con icona camera e drag&drop --}}
                                            <label for="trait-image-input-{{ $trait->id }}" 
                                                   id="trait-upload-area-{{ $trait->id }}"
                                                   onclick="event.stopPropagation();"
                                                   ondragover="event.preventDefault(); event.stopPropagation(); this.classList.add('!border-blue-300', '!bg-blue-500/30');"
                                                   ondragleave="event.preventDefault(); event.stopPropagation(); this.classList.remove('!border-blue-300', '!bg-blue-500/30');"
                                                   ondrop="event.preventDefault(); event.stopPropagation(); this.classList.remove('!border-blue-300', '!bg-blue-500/30'); handleTraitImageDrop(event, {{ $trait->id }});"
                                                   class="flex items-center justify-center gap-1.5 w-full h-8 border-2 border-dashed border-blue-400/60 rounded bg-blue-500/10 hover:bg-blue-500/20 hover:border-blue-300 transition-all cursor-pointer">
                                                <span class="text-xl">📷</span>
                                                <span class="text-[10px] text-blue-300 font-medium">{{ __('traits.add_image') }}</span>
                                                <input type="file" 
                                                       id="trait-image-input-{{ $trait->id }}"
                                                       name="trait_image"
                                                       class="hidden trait-image-input"
                                                       accept="image/jpeg,image/png,image/webp,image/gif">
                                            </label>
                                        @endif
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
            </div>{{-- Fine mt-3 content --}}
        </details>{{-- Fine details collapsabile --}}
    </div>
@endif

@if ($canEdit)
    {{-- Trait Modal - Ottimizzato per mobile con footer sticky --}}
    <div class="trait-modal" id="trait-modal-viewer"
        style="display: none;
     position: fixed !important;
     top: 0 !important;
     left: 0 !important;
     width: 100vw !important;
     height: 100vh !important;
     z-index: 10000 !important;
     background: rgba(0, 0, 0, 0.7) !important;
     backdrop-filter: blur(8px) !important;
     align-items: center !important;
     justify-content: center !important;
     padding: 0.75rem !important;
     overflow-y: auto !important;">
        <div class="modal-content"
            style="background: #ffffff !important;
         border-radius: 0.75rem !important;
         max-width: 500px !important;
         width: 100% !important;
         max-height: 92vh !important;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
         position: relative !important;
         margin: auto !important;
         display: flex !important;
         flex-direction: column !important;">
            <div class="modal-header" style="flex-shrink: 0 !important;">
                <h4 class="modal-title text-base md:text-lg">{{ __('traits.modal_title') }}</h4>
                <button type="button" class="modal-close" onclick="TraitsViewer.closeModal()">
                    ×
                </button>
            </div>

            <div class="modal-body" style="flex: 1 1 auto !important; overflow-y: auto !important; padding: 1rem !important;">
                {{-- Step 1: Select Category --}}
                <div class="form-group mb-4">
                    <label class="form-label text-sm md:text-base">{{ __('traits.select_category') }}</label>
                    <div class="category-selector" id="category-selector-viewer">
                        {{-- Categories will be inserted here by JS --}}
                    </div>
                </div>

                {{-- Step 2: Select Trait Type --}}
                <div class="form-group mb-4" id="type-selector-group-viewer" style="display: none;">
                    <label class="form-label text-sm md:text-base">{{ __('traits.select_type') }}</label>
                    <select class="form-select text-sm md:text-base" id="trait-type-select-viewer" onchange="TraitsViewer.onTypeSelected()">
                        <option value="">{{ __('traits.choose_type') }}</option>
                    </select>
                </div>

                {{-- Step 3: Select/Input Value --}}
                <div class="form-group mb-4" id="value-selector-group-viewer" style="display: none;">
                    <label class="form-label text-sm md:text-base">{{ __('traits.select_value') }}</label>
                    <div id="value-input-container-viewer">
                        {{-- Input will be inserted here based on type --}}
                    </div>
                </div>

                {{-- Preview --}}
                <div class="trait-preview" id="trait-preview-viewer" style="display: none;">
                    <div class="preview-label text-xs md:text-sm">{{ __('traits.preview') }}</div>
                    <div class="preview-card text-sm md:text-base">
                        <span class="preview-type"></span>:
                        <span class="preview-value"></span>
                        <span class="preview-unit"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="flex-shrink: 0 !important; position: sticky !important; bottom: 0 !important; background: #fafafa !important; border-top: 1px solid #e5e5e5 !important; padding: 0.875rem 1rem !important; display: flex !important; justify-content: flex-end !important; gap: 0.5rem !important;">
                <button type="button" class="btn-cancel text-sm md:text-base px-3 py-2 md:px-4 md:py-2.5" onclick="TraitsViewer.closeModal()">
                    {{ __('traits.cancel') }}
                </button>
                <button type="button" class="btn-confirm text-sm md:text-base px-3 py-2 md:px-4 md:py-2.5" id="confirm-trait-btn-viewer"
                    onclick="TraitsViewer.addTrait()" disabled>
                    {{ __('traits.add') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toast-container-viewer"></div>

    {{-- Translations for JavaScript --}}
    <script>
        // Translations for TraitsViewer (loaded from Laravel)
        window.TraitsTranslations = {
            remove_success: @json(__('traits.remove_success')),
            remove_error: @json(__('traits.remove_error')),
            network_error: @json(__('traits.network_error_js')),
            unauthorized: @json(__('traits.unauthorized')),
            confirm_remove: @json(__('traits.confirm_remove')),
            creator_only_modify: @json(__('traits.creator_only_modify')),
            modal_open_error: @json(__('traits.modal_open_error')),
            add_trait_error: @json(__('traits.add_trait_error')),
            unknown_error: @json(__('traits.unknown_error_js')),
            network_error_general: @json(__('traits.network_error_general')),
            add_success: @json(__('traits.add_success')),

            // SweetAlert2 translations
            confirm_delete_title: @json(__('traits.confirm_delete_title')),
            confirm_delete_text: @json(__('traits.confirm_delete_text')),
            confirm_delete_button: @json(__('traits.confirm_delete_button')),
            cancel_button: @json(__('traits.cancel_button')),
            delete_success_title: @json(__('traits.delete_success_title')),
            delete_success_text: @json(__('traits.delete_success_text')),
            delete_error_title: @json(__('traits.delete_error_title')),
            delete_error_text: @json(__('traits.delete_error_text'))
        };

        // Translations for Trait Image Manager
        window.traitTranslations = {
            upload_success: '{{ __('traits.upload_success') }}',
            upload_error: '{{ __('traits.upload_error') }}',
            delete_success: '{{ __('traits.delete_success') }}',
            delete_error: '{{ __('traits.delete_error') }}',
            confirm_delete: '{{ __('traits.confirm_delete') }}',
            uploading: '{{ __('traits.uploading') }}',
            file_too_large: '{{ __('traits.file_too_large') }}',
            invalid_file_type: '{{ __('traits.invalid_file_type') }}',
            preview_selected: '{{ __('traits.preview_selected') }}'
        };

        // Translations for Trait Elements (categories, types, values)
        window.traitElementTranslations = {
            categories: @json(__('trait_elements.categories')),
            types: @json(__('trait_elements.types')),
            values: @json(__('trait_elements.values'))
        };
    </script>

    {{-- MODALS SPOSTATI IN show.blade.php per evitare interferenze con grid --}}

    {{-- AI Traits Generation Script --}}
    @push('scripts')
        <script>
            /**
             * Handle AI Traits Generation Request (with cost confirmation)
             * Uses unified AI Feature Orchestrator
             * 
             * FLUSSO CORRETTO:
             * 1. Show cost confirmation dialog (CHECK EGILI PRIMA!)
             * 2. Chiede quanti traits generare (3-10)
             * 3. Execute via unified API
             * 4. Reload → Proposals modal opens automatically
             */
            async function handleTraitsGenerate(event, egiId) {
                event.preventDefault();
                
                console.log('[AI Traits] Generate clicked', { egiId });

                // Step 1: PRIMA controlla Egili e mostra costo
                // Questo mostrerà il costo PRIMA di chiedere quanti traits
                try {
                    const pricingUrl = `/api/ai/features/pricing?feature_code=ai_trait_generation`;
                    const pricingResponse = await fetch(pricingUrl, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });

                    if (!pricingResponse.ok) {
                        throw new Error('Failed to fetch pricing');
                    }

                    const pricingData = await pricingResponse.json();

                    if (!pricingData.success) {
                        throw new Error(pricingData.message || 'Pricing not available');
                    }

                    const pricing = pricingData.data;

                    // Check crediti PRIMA di tutto
                    if (!pricing.is_free && !pricing.has_sufficient_credits) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Crediti Insufficienti',
                            html: `
                                <p class="mb-3">Non hai abbastanza Egili per questa operazione.</p>
                                <div class="bg-red-50 border border-red-200 rounded p-3 text-left">
                                    <p class="text-sm"><strong>Richiesti:</strong> ${pricing.cost_egili} Egili</p>
                                    <p class="text-sm"><strong>Disponibili:</strong> ${pricing.user_balance} Egili</p>
                                    <p class="text-sm text-red-600"><strong>Mancanti:</strong> ${pricing.cost_egili - pricing.user_balance} Egili</p>
                                </div>
                                <p class="mt-3 text-xs text-gray-600">Acquista Egili per continuare.</p>
                            `,
                            confirmButtonText: 'Acquista Egili',
                            showCancelButton: true,
                            cancelButtonText: 'Chiudi',
                            confirmButtonColor: '#f97316',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Apri modale acquisto Egili
                                if (typeof openEgiliPurchaseModal === 'function') {
                                    openEgiliPurchaseModal();
                                } else {
                                    console.error('openEgiliPurchaseModal() not found');
                                    window.location.href = '/egili/purchase/pricing';
                                }
                            }
                        });
                        return false;
                    }

                    // Step 2: Se ha crediti, mostra conferma costo
                    const confirmCost = await Swal.fire({
                        title: pricing.feature_name,
                        html: `
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-400 p-4 mb-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm font-bold text-gray-800">Costo Operazione</p>
                                </div>
                                <p class="text-2xl font-bold text-orange-600 mb-2">${pricing.cost_egili} Egili</p>
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>Il tuo saldo: <strong>${pricing.user_balance} Egili</strong></span>
                                    <span>Dopo: <strong>${pricing.user_balance - pricing.cost_egili} Egili</strong></span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Gli Egili verranno scalati solo se l'operazione ha successo.</p>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#f97316',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: `Conferma e Scala ${pricing.cost_egili} Egili`,
                        cancelButtonText: 'Annulla'
                    });

                    if (!confirmCost.isConfirmed) {
                        return false;
                    }

                    // Step 3: DOPO conferma costo, chiede quanti traits
                    const countResult = await Swal.fire({
                        title: '🤖 Genera Traits con AI',
                        html: `
                            <div class="text-left">
                                <p class="mb-3">N.A.T.A.N analizzerà l'immagine e proporrà traits basati su:</p>
                                <ul class="mb-4 ml-4 space-y-1 text-sm text-gray-600">
                                    <li>✓ Elementi visivi identificati</li>
                                    <li>✓ Stile artistico</li>
                                    <li>✓ Caratteristiche tecniche</li>
                                    <li>✓ Mood e atmosfera</li>
                                </ul>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">Quanti traits vuoi generare?</label>
                                <input type="number" id="traits-count-input" class="swal2-input" 
                                       value="5" min="3" max="10" step="1"
                                       style="width: 100px; text-align: center; font-size: 18px; font-weight: bold;">
                                <p class="mt-2 text-xs text-gray-500">Min: 3 | Max: 10 | Consigliato: 5-7</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-wand-magic-sparkles mr-2"></i>Genera Ora',
                        cancelButtonText: 'Annulla',
                        confirmButtonColor: '#9333ea',
                        cancelButtonColor: '#6b7280',
                        preConfirm: () => {
                            const input = document.getElementById('traits-count-input');
                            const count = parseInt(input.value);
                            
                            if (isNaN(count) || count < 3 || count > 10) {
                                Swal.showValidationMessage('Inserisci un numero tra 3 e 10');
                                return false;
                            }
                            
                            return count;
                        }
                    });

                    if (!countResult.isConfirmed) return false;

                    const requestedCount = countResult.value;

                    // Step 4: Execute
                    Swal.fire({
                        title: 'Elaborazione in corso...',
                        html: '<p class="text-sm text-gray-600">N.A.T.A.N sta lavorando alla tua richiesta...</p>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const executeUrl = '/api/ai/features/execute';
                    const executeResponse = await fetch(executeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            feature_code: 'ai_trait_generation',
                            egi_id: egiId,
                            params: { requested_count: requestedCount }
                        })
                    });

                    const executeData = await executeResponse.json();

                    if (executeData.success) {
                        await Swal.fire({
                            title: '✨ Analisi Completata!',
                            html: `<p>${executeData.message || 'Traits generati con successo!'}</p>
                                   <p class="text-sm text-gray-600 mt-2">Tra un istante vedrai le proposte da approvare.</p>`,
                            icon: 'success',
                            confirmButtonText: 'Vedi Proposte',
                            confirmButtonColor: '#9333ea',
                        });
                        window.location.reload();
                    } else {
                        throw new Error(executeData.message || 'Execution failed');
                    }

                } catch (error) {
                    console.error('[AI Traits] Error:', error);
                    
                    await Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: error.message || 'Si è verificato un errore. Riprova.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        </script>
    @endpush

@endif
