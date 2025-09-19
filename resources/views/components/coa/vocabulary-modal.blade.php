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

                {{-- Tabs + Search --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between border-b border-gray-200">
                        {{-- Tabs Navigation --}}
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button class="vocabulary-tab border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 whitespace-nowrap active"
                                    data-tab="technique"
                                    onclick="vocabularyModal.switchTab('technique')">
                                {{ __('coa_traits.category_technique') }}
                            </button>
                            <button class="vocabulary-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                                    data-tab="materials"
                                    onclick="vocabularyModal.switchTab('materials')">
                                {{ __('coa_traits.category_materials') }}
                            </button>
                            <button class="vocabulary-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                                    data-tab="support"
                                    onclick="vocabularyModal.switchTab('support')">
                                {{ __('coa_traits.category_support') }}
                            </button>
                        </nav>

                        {{-- Search Box --}}
                        <div class="ml-auto">
                            <div class="relative">
                                <input id="vocabularySearch"
                                       type="search"
                                       placeholder="{{ __('coa_traits.search_placeholder') }}"
                                       class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="bg-gray-50 px-6 py-4">
                {{-- Dynamic Content Container --}}
                <div id="vocabularyContent" class="min-h-96">
                    {{-- Loading State --}}
                    <div id="vocabularyLoading" class="hidden flex items-center justify-center py-16">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-600">{{ __('coa_traits.loading') }}</span>
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
    .vocabulary-tab.active {
        border-color: #3B82F6;
        color: #2563EB;
    }

    .vocabulary-chip {
        @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200;
    }

    .vocabulary-chip .remove-btn {
        @apply ml-2 h-4 w-4 text-blue-600 hover:text-blue-800 cursor-pointer;
    }

    .vocabulary-chip.custom {
        @apply bg-amber-100 text-amber-800 border-amber-200;
    }

    .vocabulary-chip.custom .remove-btn {
        @apply text-amber-600 hover:text-amber-800;
    }

    /* Focus trap styles */
    #vocabularyModal:focus-within {
        outline: none;
    }

    /* Loading animation */
    @keyframes spin {
        to { transform: rotate(360deg); }
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
