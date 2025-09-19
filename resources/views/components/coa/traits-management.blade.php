{{-- CoA Traits Management Component for EGI Show Page --}}
@props(['egi'])

<div class="coa-traits-management bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-egi-id="{{ $egi->id }}">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                {{ __('coa_traits.management_title') }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('coa_traits.management_description') }}
            </p>
        </div>

        <div class="flex items-center space-x-3">
            {{-- Status Badge --}}
            @php
                $coaTraits = $egi->coaTraits;
                $hasTraits = $coaTraits && (
                    !empty($coaTraits->technique_slugs) ||
                    !empty($coaTraits->materials_slugs) ||
                    !empty($coaTraits->support_slugs) ||
                    !empty($coaTraits->technique_free_text) ||
                    !empty($coaTraits->materials_free_text) ||
                    !empty($coaTraits->support_free_text)
                );
            @endphp

            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hasTraits ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                @if($hasTraits)
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('coa_traits.status_configured') }}
                @else
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('coa_traits.status_not_configured') }}
                @endif
            </span>

            {{-- Edit Button --}}
            <button type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    onclick="CoaTraitsManager.openModal({{ $egi->id }})">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('coa_traits.edit_traits') }}
            </button>
        </div>
    </div>

    {{-- Current Traits Display --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Technique --}}
        <div class="space-y-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <h4 class="text-sm font-medium text-gray-900">{{ __('coa_traits.category_technique') }}</h4>
            </div>

            <div id="technique-display" class="min-h-[3rem]">
                @if($coaTraits && !empty($coaTraits->technique_slugs))
                    <div class="space-y-2">
                        @foreach($coaTraits->technique_slugs as $slug)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-md">
                                {{ __("vocabulary.{$slug}", [], null) ?: ucfirst(str_replace(['_', '-'], ' ', $slug)) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($coaTraits && !empty($coaTraits->technique_free_text))
                    <div class="space-y-2 mt-2">
                        @foreach($coaTraits->technique_free_text as $customText)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-md">
                                {{ $customText }} <span class="text-xs">({{ __('coa_traits.custom') }})</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                @if(!$coaTraits || (empty($coaTraits->technique_slugs) && empty($coaTraits->technique_free_text)))
                    <p class="text-sm text-gray-500 italic">{{ __('coa_traits.no_technique_selected') }}</p>
                @endif
            </div>
        </div>

        {{-- Materials --}}
        <div class="space-y-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.781 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <h4 class="text-sm font-medium text-gray-900">{{ __('coa_traits.category_materials') }}</h4>
            </div>

            <div id="materials-display" class="min-h-[3rem]">
                @if($coaTraits && !empty($coaTraits->materials_slugs))
                    <div class="space-y-2">
                        @foreach($coaTraits->materials_slugs as $slug)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-md">
                                {{ __("vocabulary.{$slug}", [], null) ?: ucfirst(str_replace(['_', '-'], ' ', $slug)) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($coaTraits && !empty($coaTraits->materials_free_text))
                    <div class="space-y-2 mt-2">
                        @foreach($coaTraits->materials_free_text as $customText)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-md">
                                {{ $customText }} <span class="text-xs">({{ __('coa_traits.custom') }})</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                @if(!$coaTraits || (empty($coaTraits->materials_slugs) && empty($coaTraits->materials_free_text)))
                    <p class="text-sm text-gray-500 italic">{{ __('coa_traits.no_materials_selected') }}</p>
                @endif
            </div>
        </div>

        {{-- Support --}}
        <div class="space-y-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                </svg>
                <h4 class="text-sm font-medium text-gray-900">{{ __('coa_traits.category_support') }}</h4>
            </div>

            <div id="support-display" class="min-h-[3rem]">
                @if($coaTraits && !empty($coaTraits->support_slugs))
                    <div class="space-y-2">
                        @foreach($coaTraits->support_slugs as $slug)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-md">
                                {{ __("vocabulary.{$slug}", [], null) ?: ucfirst(str_replace(['_', '-'], ' ', $slug)) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($coaTraits && !empty($coaTraits->support_free_text))
                    <div class="space-y-2 mt-2">
                        @foreach($coaTraits->support_free_text as $customText)
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-md">
                                {{ $customText }} <span class="text-xs">({{ __('coa_traits.custom') }})</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                @if(!$coaTraits || (empty($coaTraits->support_slugs) && empty($coaTraits->support_free_text)))
                    <p class="text-sm text-gray-500 italic">{{ __('coa_traits.no_support_selected') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            @if($coaTraits && $coaTraits->last_updated_at)
                {{ __('coa_traits.last_updated') }}:
                <time datetime="{{ $coaTraits->last_updated_at->toISOString() }}">
                    {{ $coaTraits->last_updated_at->format('d/m/Y H:i') }}
                </time>
            @else
                {{ __('coa_traits.never_configured') }}
            @endif
        </div>

        <div class="flex items-center space-x-3">
            {{-- Clear All Button --}}
            @if($hasTraits)
                <button type="button"
                        class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        onclick="CoaTraitsManager.clearAll({{ $egi->id }})">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('coa_traits.clear_all') }}
                </button>
            @endif

            {{-- Save Status (hidden initially) --}}
            <div id="save-status-{{ $egi->id }}" class="hidden items-center text-sm">
                <div class="flex items-center text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('coa_traits.saved') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Form for Data Persistence --}}
    <form id="coa-traits-form-{{ $egi->id }}" action="{{ route('egi.coa-traits.update', $egi) }}" method="POST" class="hidden">
        @csrf
        @method('PUT')

        {{-- Hidden inputs for vocabulary selections --}}
        <input type="hidden" name="technique_slugs" id="technique_slugs_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->technique_slugs ?? []) : '[]' }}">
        <input type="hidden" name="materials_slugs" id="materials_slugs_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->materials_slugs ?? []) : '[]' }}">
        <input type="hidden" name="support_slugs" id="support_slugs_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->support_slugs ?? []) : '[]' }}">

        {{-- Hidden inputs for custom/free text --}}
        <input type="hidden" name="technique_free_text" id="technique_free_text_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->technique_free_text ?? []) : '[]' }}">
        <input type="hidden" name="materials_free_text" id="materials_free_text_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->materials_free_text ?? []) : '[]' }}">
        <input type="hidden" name="support_free_text" id="support_free_text_{{ $egi->id }}"
               value="{{ $coaTraits ? json_encode($coaTraits->support_free_text ?? []) : '[]' }}">
    </form>
</div>

{{-- Include the vocabulary modal --}}
@include('components.coa.vocabulary-modal')

{{-- JavaScript Controller --}}
<script>
window.CoaTraitsManager = window.CoaTraitsManager || {};

// Define translations for JavaScript
window.coaTraitsTranslations = {
    no_technique_selected: @json(__('coa_traits.no_technique_selected')),
    no_materials_selected: @json(__('coa_traits.no_materials_selected')),
    no_support_selected: @json(__('coa_traits.no_support_selected')),
    custom: @json(__('coa_traits.custom'))
};

(function() {
    'use strict';

    // Initialize manager for this EGI
    CoaTraitsManager.openModal = function(egiId) {
        console.log('CoaTraitsManager: Opening modal for EGI', egiId);

        // Get current selections from hidden inputs
        const currentSelections = {
            technique: [],
            materials: [],
            support: []
        };

        // Parse vocabulary selections
        ['technique', 'materials', 'support'].forEach(category => {
            const slugsInput = document.getElementById(`${category}_slugs_${egiId}`);
            const freeTextInput = document.getElementById(`${category}_free_text_${egiId}`);

            if (slugsInput && slugsInput.value) {
                try {
                    const slugs = JSON.parse(slugsInput.value);
                    slugs.forEach(slug => {
                        currentSelections[category].push({
                            slug: slug,
                            name: slug.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase()), // Format slug as readable name
                            isCustom: false
                        });
                    });
                } catch (e) {
                    console.warn('Error parsing slugs for', category, e);
                }
            }

            if (freeTextInput && freeTextInput.value) {
                try {
                    const freeTexts = JSON.parse(freeTextInput.value);
                    freeTexts.forEach((text, index) => {
                        currentSelections[category].push({
                            slug: `custom_${category}_${index}_${Date.now()}`,
                            name: text,
                            isCustom: true
                        });
                    });
                } catch (e) {
                    console.warn('Error parsing free text for', category, e);
                }
            }
        });

        // Open vocabulary modal
        vocabularyModal.open({
            selections: currentSelections,
            onConfirm: function(selections) {
                CoaTraitsManager.saveSelections(egiId, selections);
            },
            onCancel: function() {
                console.log('CoaTraitsManager: User canceled selection');
            }
        });
    };

    CoaTraitsManager.saveSelections = function(egiId, selections) {
        console.log('CoaTraitsManager: Saving selections for EGI', egiId, selections);

        // Separate vocabulary terms from custom terms
        const vocabularySelections = { technique: [], materials: [], support: [] };
        const customSelections = { technique: [], materials: [], support: [] };

        Object.keys(selections).forEach(category => {
            selections[category].forEach(item => {
                if (item.isCustom) {
                    customSelections[category].push(item.name);
                } else {
                    vocabularySelections[category].push(item.slug);
                }
            });
        });

        // Update hidden form inputs
        Object.keys(vocabularySelections).forEach(category => {
            const slugsInput = document.getElementById(`${category}_slugs_${egiId}`);
            const freeTextInput = document.getElementById(`${category}_free_text_${egiId}`);

            if (slugsInput) {
                slugsInput.value = JSON.stringify(vocabularySelections[category]);
            }
            if (freeTextInput) {
                freeTextInput.value = JSON.stringify(customSelections[category]);
            }
        });

        // Submit form via AJAX
        const form = document.getElementById(`coa-traits-form-${egiId}`);
        if (!form) {
            console.error('CoaTraitsManager: Form not found for EGI', egiId);
            return;
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('CoaTraitsManager: Saved successfully');
                CoaTraitsManager.updateDisplay(egiId, selections);
                CoaTraitsManager.showSaveStatus(egiId);
            } else {
                console.error('CoaTraitsManager: Save failed', data);
                alert('Errore nel salvataggio. Riprova.');
            }
        })
        .catch(error => {
            console.error('CoaTraitsManager: Network error', error);
            alert('Errore di rete. Riprova.');
        });
    };

    CoaTraitsManager.updateDisplay = function(egiId, selections) {
        // Update the visual display with new selections
        Object.keys(selections).forEach(category => {
            const displayElement = document.getElementById(`${category}-display`);
            if (!displayElement) return;

            const items = selections[category];
            if (items.length === 0) {
                const messageKey = `no_${category}_selected`;
                const message = window.coaTraitsTranslations[messageKey] || `No ${category} selected`;
                displayElement.innerHTML = `<p class="text-sm text-gray-500 italic">${message}</p>`;
                return;
            }

            let html = '<div class="space-y-2">';

            // Regular vocabulary terms
            const regularItems = items.filter(item => !item.isCustom);
            if (regularItems.length > 0) {
                regularItems.forEach(item => {
                    const colorClass = category === 'technique' ? 'bg-blue-100 text-blue-800' :
                                     category === 'materials' ? 'bg-green-100 text-green-800' :
                                     'bg-amber-100 text-amber-800';
                    html += `<span class="inline-block px-2 py-1 text-xs font-medium ${colorClass} rounded-md">${item.name}</span>`;
                });
            }

            // Custom terms
            const customItems = items.filter(item => item.isCustom);
            if (customItems.length > 0) {
                html += customItems.length > 0 && regularItems.length > 0 ? '<div class="mt-2">' : '';
                customItems.forEach(item => {
                    const customLabel = window.coaTraitsTranslations.custom || 'custom';
                    html += `<span class="inline-block px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-md">${item.name} <span class="text-xs">(${customLabel})</span></span>`;
                });
                html += customItems.length > 0 && regularItems.length > 0 ? '</div>' : '';
            }

            html += '</div>';
            displayElement.innerHTML = html;
        });
    };

    CoaTraitsManager.showSaveStatus = function(egiId) {
        const statusElement = document.getElementById(`save-status-${egiId}`);
        if (statusElement) {
            statusElement.classList.remove('hidden');
            statusElement.classList.add('flex');

            // Hide after 3 seconds
            setTimeout(() => {
                statusElement.classList.add('hidden');
                statusElement.classList.remove('flex');
            }, 3000);
        }
    };

    CoaTraitsManager.clearAll = function(egiId) {
        if (!confirm('Sei sicuro di voler cancellare tutti i traits selezionati?')) {
            return;
        }

        const emptySelections = { technique: [], materials: [], support: [] };
        CoaTraitsManager.saveSelections(egiId, emptySelections);
    };

})();
</script>

{{-- Load the vocabulary modal JavaScript --}}
<script src="{{ asset('js/coa/vocabulary-modal.js') }}"></script>
