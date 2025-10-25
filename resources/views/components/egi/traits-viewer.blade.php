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
// Determina se mostrare la sezione: solo se ci sono traits o se l'utente può editare
    $hasTraits = $egi && $egi->traits && $egi->traits->count() > 0;
    $shouldShow = $hasTraits || $canEdit;
@endphp

{{-- Include CSS con Vite --}}
@vite(['resources/css/traits-manager.css'])

{{-- Include JavaScript integrato per traits e image management --}}
@vite(['resources/js/traits-viewer-integrated.js'])

@if ($shouldShow)
    <div class="egi-traits-viewer" id="traits-viewer-{{ $egi ? $egi->id : 'new' }}"
        data-egi-id="{{ $egi ? $egi->id : '' }}" data-can-edit="{{ $canEdit ? 'true' : 'false' }}"
        style="position: relative !important; order: -1 !important; margin-top: 0 !important; margin-bottom: 2rem !important;">

        {{-- COLLAPSABILE BOX TRAITS --}}
        <details class="group" open>
            <summary class="flex cursor-pointer items-center justify-between rounded-lg bg-gradient-to-r from-purple-900/30 to-indigo-900/30 px-4 py-3 text-white transition-colors hover:from-purple-900/40 hover:to-indigo-900/40 border border-purple-700/30">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🎯</span>
                    <span class="text-sm font-semibold">{{ __('Tratti e Attributi') }}</span>
                    <span class="trait-counter ml-2">
                        <span class="traits-count">{{ $egi && $egi->traits ? $egi->traits->count() : 0 }}</span>/30
                    </span>
                </div>
                <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-3">
                @if ($canEdit)
            {{-- Add Trait Button --}}
            <div class="traits-editor-controls" style="margin-bottom: 1rem;">
                <button type="button" class="add-trait-btn"
                    style="background: transparent !important;
                           border: 2px dashed #d4af37 !important;
                           color: #d4af37 !important;
                           padding: 0.75rem 1.5rem !important;
                           border-radius: 0.5rem !important;
                           font-weight: 600 !important;
                           cursor: pointer !important;
                           width: 100% !important;
                           margin-bottom: 1rem !important;
                           font-size: 1rem !important;
                           transition: all 0.2s ease !important;"
                    onmouseover="this.style.backgroundColor='rgba(212, 175, 55, 0.1)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('traits.add_trait') }}
                </button>
            </div>
        @endif

        {{-- Traits Grid (readonly) renderizzato con PHP - 2 COLONNE --}}
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

                        <div class="trait-card readonly clickable-trait" data-category="{{ $trait->category_id }}"
                            data-trait-id="{{ $trait->id }}"
                            style="position: relative; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                            onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='';"
                            title="{{ __('traits.click_to_view_details') }}">
                            @if ($canEdit)
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
                            <div class="trait-header readonly">
                                <span class="trait-category-badge" style="background-color: {{ $categoryColor }}">
                                    {{ $categoryIcon }}
                                </span>
                                @if ($trait->getFirstMedia('trait_images'))
                                    @php
                                        $media = $trait->getFirstMedia('trait_images');
                                        $thumbnailUrl = $media ? $media->getUrl('thumb') : null;
                                    @endphp
                                    <span class="trait-image-indicator"
                                        style="position: absolute;
                                             top: 1.5rem;
                                             left: 1.8rem;
                                             background: rgba(34, 197, 94, 0.9);
                                             color: white;
                                             border-radius: 50%;
                                             width: 2rem;
                                             height: 2rem;
                                             display: flex;
                                             align-items: center;
                                             justify-content: center;
                                             font-size: 0.75rem;
                                             z-index: 100;
                                             overflow: hidden;
                                             border: 2px solid rgba(34, 197, 94, 0.9);"
                                        title="{{ __('traits.has_image') }}">
                                        @if ($thumbnailUrl)
                                            <img src="{{ $thumbnailUrl }}" alt="Trait image"
                                                style="width: 100%;
                                                    height: 100%;
                                                    object-fit: cover;
                                                    border-radius: 50%;">
                                        @else
                                            📷
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <div class="trait-content">
                                <div class="trait-type">
                                    @if ($trait->traitType)
                                        {{ __('trait_elements.types.' . $trait->traitType->name, [], null) ?: $trait->traitType->name }}
                                    @else
                                        Unknown
                                    @endif
                                </div>
                                <div class="trait-value">
                                    <span>{{ $trait->display_value ?? $trait->value }}</span>
                                    @if ($trait->traitType && $trait->traitType->unit)
                                        <span class="trait-unit">{{ $trait->traitType->unit }}</span>
                                    @endif
                                </div>

                                {{-- Barra di rarità --}}
                                @if (isset($trait->rarity_percentage) && $trait->rarity_percentage)
                                    @php
                                        // Determina la classe di rarità in base alla percentuale
                                        if ($trait->rarity_percentage >= 70) {
                                            $rarityClass = 'common';
                                        } elseif ($trait->rarity_percentage >= 40) {
                                            $rarityClass = 'uncommon';
                                        } elseif ($trait->rarity_percentage >= 20) {
                                            $rarityClass = 'rare';
                                        } elseif ($trait->rarity_percentage >= 10) {
                                            $rarityClass = 'epic';
                                        } elseif ($trait->rarity_percentage >= 5) {
                                            $rarityClass = 'legendary';
                                        } else {
                                            $rarityClass = 'mythic';
                                        }

                                        // Formula semplice e diretta: più è raro, più la barra è lunga
                                        // Invertiamo direttamente la percentuale per creare differenze evidenti
                                        if ($trait->rarity_percentage <= 5) {
                                            $barWidth = 90; // Leggendario/Mitico - barra quasi piena
                                        } elseif ($trait->rarity_percentage <= 10) {
                                            $barWidth = 75; // Epico
                                        } elseif ($trait->rarity_percentage <= 20) {
                                            $barWidth = 60; // Raro
                                        } elseif ($trait->rarity_percentage <= 40) {
                                            $barWidth = 40; // Poco comune
                                        } elseif ($trait->rarity_percentage <= 70) {
                                            $barWidth = 25; // Comune
                                        } else {
                                            $barWidth = 10; // Molto comune - barra quasi vuota
                                        }
                                    @endphp
                                    <div class="trait-rarity">
                                        <div class="rarity-bar">
                                            <div class="rarity-fill {{ $rarityClass }}"
                                                style="width: {{ number_format($barWidth, 1) }}%"></div>
                                        </div>
                                        <span class="rarity-text">{{ number_format($trait->rarity_percentage, 1) }}%
                                            have this</span>
                                    </div>
                                @endif
                            </div>
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
    {{-- Trait Modal --}}
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
     padding: 1rem !important;
     overflow-y: auto !important;">
        <div class="modal-content"
            style="background: #ffffff !important;
         border-radius: 0.75rem !important;
         max-width: 500px !important;
         width: 90% !important;
         max-height: 80vh !important;
         overflow-y: auto !important;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
         position: relative !important;
         margin: auto !important;">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('traits.modal_title') }}</h4>
                <button type="button" class="modal-close" onclick="TraitsViewer.closeModal()">
                    ×
                </button>
            </div>

            <div class="modal-body">
                {{-- Step 1: Select Category --}}
                <div class="form-group">
                    <label class="form-label">{{ __('traits.select_category') }}</label>
                    <div class="category-selector" id="category-selector-viewer">
                        {{-- Categories will be inserted here by JS --}}
                    </div>
                </div>

                {{-- Step 2: Select Trait Type --}}
                <div class="form-group" id="type-selector-group-viewer" style="display: none;">
                    <label class="form-label">{{ __('traits.select_type') }}</label>
                    <select class="form-select" id="trait-type-select-viewer" onchange="TraitsViewer.onTypeSelected()">
                        <option value="">{{ __('traits.choose_type') }}</option>
                    </select>
                </div>

                {{-- Step 3: Select/Input Value --}}
                <div class="form-group" id="value-selector-group-viewer" style="display: none;">
                    <label class="form-label">{{ __('traits.select_value') }}</label>
                    <div id="value-input-container-viewer">
                        {{-- Input will be inserted here based on type --}}
                    </div>
                </div>

                {{-- Preview --}}
                <div class="trait-preview" id="trait-preview-viewer" style="display: none;">
                    <div class="preview-label">{{ __('traits.preview') }}</div>
                    <div class="preview-card">
                        <span class="preview-type"></span>:
                        <span class="preview-value"></span>
                        <span class="preview-unit"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="TraitsViewer.closeModal()">
                    {{ __('traits.cancel') }}
                </button>
                <button type="button" class="btn-confirm" id="confirm-trait-btn-viewer"
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

    {{-- Include trait detail modals per tutti i trait visibili --}}
    @if ($egi && $egi->traits && $egi->traits->count() > 0)
        @foreach ($egi->traits as $trait)
            {{-- Modal di edit per proprietari --}}
            <x-trait.trait-detail-modal :trait="$trait" />
        @endforeach
    @endif

@endif
