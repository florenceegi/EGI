{{-- 
    CoA Annex Management Modal Component
    🎯 Purpose: Manage CoA Pro annexes (Provenance, Condition, Exhibitions, Photos)
    🛡️ Privacy: GDPR-compliant annex management
    📍 Location: Modal overlay for annex management
    
    @param App\Models\Coa $coa
--}}

@props(['coa'])

{{-- Annex Management Modal --}}
<div id="coaAnnexModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Background overlay --}}
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true"></div>

        {{-- Modal container --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            {{-- Modal header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-amber-800 to-yellow-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold leading-6 text-white" id="modal-title">
                        {{ __('egi.coa.manage_annexes_title') }}
                    </h3>
                    <button type="button" onclick="closeCoaAnnexModal()" class="transition-colors text-amber-200 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-sm text-amber-200">
                    {{ __('egi.coa.annexes_description') }}
                </p>
            </div>

            {{-- Modal content --}}
            <div class="px-6 py-6 bg-gray-800">
                {{-- Annex Type Tabs --}}
                <div class="mb-6 border-b border-gray-700">
                    <nav class="flex -mb-px space-x-8" aria-label="Tabs">
                        <button onclick="switchAnnexTab('provenance')" 
                                class="px-1 py-2 text-sm font-medium transition-colors border-b-2 annex-tab whitespace-nowrap border-amber-500 text-amber-400" 
                                data-tab="provenance">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('egi.coa.provenance_tab') }}
                        </button>
                        <button onclick="switchAnnexTab('condition')" 
                                class="px-1 py-2 text-sm font-medium text-gray-400 transition-colors border-b-2 border-transparent annex-tab whitespace-nowrap hover:text-gray-300 hover:border-gray-300" 
                                data-tab="condition">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h2m0 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3m0 2V1"/>
                            </svg>
                            {{ __('egi.coa.condition_tab') }}
                        </button>
                        <button onclick="switchAnnexTab('exhibitions')" 
                                class="px-1 py-2 text-sm font-medium text-gray-400 transition-colors border-b-2 border-transparent annex-tab whitespace-nowrap hover:text-gray-300 hover:border-gray-300" 
                                data-tab="exhibitions">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ __('egi.coa.exhibitions_tab') }}
                        </button>
                        <button onclick="switchAnnexTab('photos')" 
                                class="px-1 py-2 text-sm font-medium text-gray-400 transition-colors border-b-2 border-transparent annex-tab whitespace-nowrap hover:text-gray-300 hover:border-gray-300" 
                                data-tab="photos">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('egi.coa.photos_tab') }}
                        </button>
                    </nav>
                </div>

                {{-- Annex Content Panels --}}
                
                {{-- A_PROVENANCE Panel --}}
                <div id="provenance-panel" class="annex-panel">
                    <div class="space-y-4">
                        <div class="p-4 border rounded-lg bg-blue-900/20 border-blue-500/30">
                            <h4 class="mb-2 font-medium text-white">{{ __('egi.coa.provenance_title') }}</h4>
                            <p class="mb-4 text-sm text-gray-300">
                                {{ __('egi.coa.ownership_history_description') }}
                            </p>
                            
                            <form id="provenanceForm" class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.previous_owners') }}</label>
                                    <textarea name="previous_owners" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.previous_owners_placeholder') }}"></textarea>
                                </div>
                                
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.acquisition_details') }}</label>
                                    <textarea name="acquisition_details" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.acquisition_details_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.authenticity_sources') }}</label>
                                    <textarea name="authenticity_sources" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.authenticity_sources_placeholder') }}"></textarea>
                                </div>

                                <button type="submit" class="w-full px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                    {{ __('egi.coa.save_provenance_data') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- B_CONDITION Panel --}}
                <div id="condition-panel" class="hidden annex-panel">
                    <div class="space-y-4">
                        <div class="p-4 border rounded-lg bg-green-900/20 border-green-500/30">
                            <h4 class="mb-2 font-medium text-white">{{ __('egi.coa.condition_title') }}</h4>
                            <p class="mb-4 text-sm text-gray-300">
                                {{ __('egi.coa.condition_assessment_description') }}
                            </p>
                            
                            <form id="conditionForm" class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.overall_condition') }}</label>
                                    <select name="overall_condition" 
                                            class="w-full px-3 py-2 text-white bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                        <option value="">{{ __('egi.coa.select_condition') }}</option>
                                        <option value="excellent">{{ __('egi.coa.condition_excellent') }}</option>
                                        <option value="very_good">{{ __('egi.coa.condition_very_good') }}</option>
                                        <option value="good">{{ __('egi.coa.condition_good') }}</option>
                                        <option value="fair">{{ __('egi.coa.condition_fair') }}</option>
                                        <option value="poor">{{ __('egi.coa.condition_poor') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.detailed_assessment') }}</label>
                                    <textarea name="detailed_assessment" 
                                              rows="4" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.detailed_assessment_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.conservation_history') }}</label>
                                    <textarea name="conservation_history" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.conservation_history_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.assessor_information') }}</label>
                                    <input type="text" 
                                           name="assessor_name" 
                                           class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                           placeholder="{{ __('egi.coa.assessor_placeholder') }}">
                                </div>

                                <button type="submit" class="w-full px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                                    {{ __('egi.coa.save_condition_report') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- C_EXHIBITIONS Panel --}}
                <div id="exhibitions-panel" class="hidden annex-panel">
                    <div class="space-y-4">
                        <div class="p-4 border rounded-lg bg-purple-900/20 border-purple-500/30">
                            <h4 class="mb-2 font-medium text-white">{{ __('egi.coa.exhibitions_title') }}</h4>
                            <p class="mb-4 text-sm text-gray-300">
                                {{ __('egi.coa.exhibition_history_description') }}
                            </p>
                            
                            <form id="exhibitionsForm" class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.major_exhibitions') }}</label>
                                    <textarea name="major_exhibitions" 
                                              rows="4" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.major_exhibitions_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.publications_catalogues') }}</label>
                                    <textarea name="publications" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.publications_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.awards_recognition') }}</label>
                                    <textarea name="awards" 
                                              rows="2" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.awards_placeholder') }}"></textarea>
                                </div>

                                <button type="submit" class="w-full px-4 py-2 font-medium text-white transition-colors bg-purple-600 rounded-lg hover:bg-purple-700">
                                    {{ __('egi.coa.save_exhibition_history') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- D_PHOTOS Panel --}}
                <div id="photos-panel" class="hidden annex-panel">
                    <div class="space-y-4">
                        <div class="p-4 border rounded-lg bg-rose-900/20 border-rose-500/30">
                            <h4 class="mb-2 font-medium text-white">{{ __('egi.coa.photos_title') }}</h4>
                            <p class="mb-4 text-sm text-gray-300">
                                {{ __('egi.coa.photo_documentation_description') }}
                            </p>
                            
                            <form id="photosForm" class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.upload_files') }}</label>
                                    <div class="p-6 text-center border-2 border-gray-600 border-dashed rounded-lg">
                                        <svg class="w-12 h-12 mx-auto text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="mt-4">
                                            <label for="photo-upload" class="cursor-pointer">
                                                <span class="block mt-2 text-sm font-medium text-gray-300">
                                                    {{ __('egi.coa.click_upload_images') }}
                                                </span>
                                                <input id="photo-upload" name="photos[]" type="file" multiple accept="image/*" class="sr-only">
                                            </label>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ __('egi.coa.png_jpg_webp') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.photo_descriptions') }}</label>
                                    <textarea name="photo_descriptions" 
                                              rows="3" 
                                              class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                              placeholder="{{ __('egi.coa.photo_descriptions_placeholder') }}"></textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">{{ __('egi.coa.photographer_credits') }}</label>
                                    <input type="text" 
                                           name="photographer" 
                                           class="w-full px-3 py-2 text-white placeholder-gray-400 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                           placeholder="{{ __('egi.coa.photographer_placeholder') }}">
                                </div>

                                <button type="submit" class="w-full px-4 py-2 font-medium text-white transition-colors rounded-lg bg-rose-600 hover:bg-rose-700">
                                    {{ __('egi.coa.save_photo_documentation') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="flex justify-end px-6 py-3 space-x-3 bg-gray-700">
                <button type="button" 
                        onclick="closeCoaAnnexModal()"
                        class="px-4 py-2 font-medium text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-500">
                    {{ __('egi.coa.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Annex Modal JavaScript --}}
@push('scripts')
<script>
let currentCoaId = null;

function openCoaAnnexModal(coaId) {
    currentCoaId = coaId;
    document.getElementById('coaAnnexModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCoaAnnexModal() {
    document.getElementById('coaAnnexModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentCoaId = null;
}

function switchAnnexTab(tabName) {
    // Hide all panels
    document.querySelectorAll('.annex-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    // Reset all tabs
    document.querySelectorAll('.annex-tab').forEach(tab => {
        tab.classList.remove('border-amber-500', 'text-amber-400');
        tab.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Show selected panel
    document.getElementById(tabName + '-panel').classList.remove('hidden');
    
    // Activate selected tab
    const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-amber-500', 'text-amber-400');
}

// Form submission handlers
document.getElementById('provenanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitAnnexData('A_PROVENANCE', new FormData(this));
});

document.getElementById('conditionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitAnnexData('B_CONDITION', new FormData(this));
});

document.getElementById('exhibitionsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitAnnexData('C_EXHIBITIONS', new FormData(this));
});

document.getElementById('photosForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitAnnexData('D_PHOTOS', new FormData(this));
});

function submitAnnexData(type, formData) {
    if (!currentCoaId) {
        alert('{{ __("egi.coa.error_no_certificate") }}');
        return;
    }
    
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = '{{ __("egi.coa.saving") }}';
    submitButton.disabled = true;
    
    // Convert FormData to JSON
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    fetch(`{{ route('coa.annexes.store', ':id') }}`.replace(':id', currentCoaId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type,
            data: data
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('{{ __("egi.coa.annex_saved_success") }}');
            closeCoaAnnexModal();
            // Optionally reload the page to show updated annex count
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_saving_annex") }}: ' + (result.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_saving_annex") }}');
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}

// Close modal when clicking outside
document.getElementById('coaAnnexModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCoaAnnexModal();
    }
});
</script>
@endpush