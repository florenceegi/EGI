{{-- CoA Vocabulary Modal - Main Container for Traits Selection --}}
<div id="vocabularyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="vocabularyModal.close()"></div>

        {{-- Modal Content --}}
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            {{-- Header --}}
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                        {{ __('coa_traits.modal_title') }}
                    </h3>
                    <button type="button"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors"
                            onclick="vocabularyModal.close()"
                            aria-label="Chiudi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Tabs and Search Bar --}}
                <div class="mt-4">
                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button id="tabTechnique" 
                                    class="vocabulary-tab py-2 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active"
                                    onclick="vocabularyModal.switchTab('technique')"
                                    data-tab="technique">
                                {{ __('coa_traits.technique') }}
                                <span id="techniqueCount" class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs font-medium">0</span>
                            </button>
                            <button id="tabMaterials" 
                                    class="vocabulary-tab py-2 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    onclick="vocabularyModal.switchTab('materials')"
                                    data-tab="materials">
                                {{ __('coa_traits.materials') }}
                                <span id="materialsCount" class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium">0</span>
                            </button>
                            <button id="tabSupport" 
                                    class="vocabulary-tab py-2 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    onclick="vocabularyModal.switchTab('support')"
                                    data-tab="support">
                                {{ __('coa_traits.support') }}
                                <span id="supportCount" class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium">0</span>
                            </button>
                        </nav>
                    </div>

                    {{-- Search Box --}}
                    <div class="flex items-center justify-end pt-3">
                        <div class="relative">
                            <input id="vocabularySearch"
                                   type="search"
                                   placeholder="{{ __('coa_traits.search_placeholder') }}"
                                   class="block w-64 pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button id="clearSearchBtn" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 transition-opacity"
                                    onclick="vocabularyModal.clearSearch()">
                                <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="bg-gray-50 px-6 py-4">
                {{-- Dynamic Content Container --}}
                <div id="vocabularyContent" class="min-h-96">
                    {{-- Loading State --}}
                    <div id="vocabularyLoading" class="hidden">
                        <div class="flex items-center justify-center py-16">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-gray-600">{{ __('coa_traits.loading') }}</span>
                        </div>
                    </div>

                    {{-- Content will be loaded here dynamically --}}
                    <div id="vocabularyDynamicContent">
                        {{-- Initial content: categories for technique --}}
                    </div>
                </div>

                {{-- Selected Items Display --}}
                <div id="vocabularySelected" class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">
                        {{ __('coa_traits.selected_items') }}
                    </h4>

                    {{-- Selected Chips Container --}}
                    <div id="vocabularyChips" class="flex flex-wrap gap-2 min-h-[2rem]">
                        {{-- Chips will be added here dynamically --}}
                        <div id="vocabularyChipsEmpty" class="text-sm text-gray-500 italic">
                            {{ __('coa_traits.no_items_selected') }}
                        </div>
                    </div>

                    {{-- Add Custom Term Button --}}
                    <div class="mt-3">
                        <button id="addCustomTermBtn"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                onclick="vocabularyModal.showCustomTermInput()">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ __('coa_traits.add_custom') }}
                        </button>

                        {{-- Custom Term Input (initially hidden) --}}
                        <div id="customTermInput" class="hidden mt-2">
                            <div class="flex items-center space-x-2">
                                <input type="text"
                                       id="customTermText"
                                       placeholder="{{ __('coa_traits.custom_term_placeholder') }}"
                                       maxlength="60"
                                       class="flex-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <button onclick="vocabularyModal.addCustomTerm()"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('coa_traits.add') }}
                                </button>
                                <button onclick="vocabularyModal.hideCustomTermInput()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('coa_traits.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <span id="vocabularySelectionCount">0</span> {{ __('coa_traits.items_selected') }}
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            onclick="vocabularyModal.cancel()">
                        {{ __('coa_traits.cancel') }}
                    </button>
                    <button type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            onclick="vocabularyModal.confirm()">
                        {{ __('coa_traits.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS Styles --}}
<style>
    /* Vocabulary Modal Specific Styles */
    .vocabulary-tab {
        @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-all duration-200;
        border-bottom-width: 2px;
    }

    .vocabulary-tab.active {
        @apply border-blue-500 text-blue-600;
    }

    .vocabulary-tab:focus {
        @apply ring-2 ring-blue-500 ring-offset-2 outline-none;
    }

    .vocabulary-chip {
        @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-all duration-200;
        @apply bg-blue-100 text-blue-800 border border-blue-200;
    }

    .vocabulary-chip.custom {
        @apply bg-amber-100 text-amber-800 border-amber-200;
    }

    .vocabulary-chip.technique {
        @apply bg-green-100 text-green-800 border-green-200;
    }

    .vocabulary-chip.materials {
        @apply bg-purple-100 text-purple-800 border-purple-200;
    }

    .vocabulary-chip.support {
        @apply bg-indigo-100 text-indigo-800 border-indigo-200;
    }

    .vocabulary-chip .remove-btn {
        @apply ml-2 h-4 w-4 cursor-pointer transition-colors duration-200;
    }

    .vocabulary-chip .remove-btn:hover {
        @apply transform scale-110;
    }

    .vocabulary-chip.custom .remove-btn {
        @apply text-amber-600 hover:text-amber-800;
    }

    .vocabulary-chip.technique .remove-btn {
        @apply text-green-600 hover:text-green-800;
    }

    .vocabulary-chip.materials .remove-btn {
        @apply text-purple-600 hover:text-purple-800;
    }

    .vocabulary-chip.support .remove-btn {
        @apply text-indigo-600 hover:text-indigo-800;
    }

    /* Term selection states */
    .term-item {
        @apply transition-all duration-200 cursor-pointer;
    }

    .term-item:hover {
        @apply transform translate-y-[-1px] shadow-md;
    }

    .term-item.selected {
        @apply border-blue-400 bg-blue-50 shadow-sm;
    }

    .term-item.selected::before {
        content: '✓';
        @apply absolute top-2 right-2 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold;
        position: absolute;
    }

    .term-item {
        position: relative;
    }

    /* Count badges */
    .count-badge {
        @apply transition-all duration-200;
    }

    .count-badge.active {
        @apply bg-blue-100 text-blue-600;
    }

    /* Search enhancements */
    #vocabularySearch:focus ~ #clearSearchBtn.visible {
        @apply opacity-100;
    }

    #clearSearchBtn.visible {
        @apply opacity-100;
    }

    /* Focus trap styles */
    #vocabularyModal:focus-within {
        outline: none;
    }

    /* Animation for chips */
    @keyframes chipSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .vocabulary-chip {
        animation: chipSlideIn 0.2s ease-out;
    }

    /* Loading animation */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Smooth transitions for all interactive elements */
    .vocabulary-tab,
    .vocabulary-chip,
    .term-item,
    button {
        @apply transition-all duration-200 ease-in-out;
    }
</style>

{{-- Initialize Modal on Load --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize vocabulary modal if not already initialized
    if (typeof window.vocabularyModal === 'undefined') {
        console.log('VocabularyModal: Loading modal controller...');
        // The actual controller will be loaded via vocabulary-modal.js
    }
});
</script>
