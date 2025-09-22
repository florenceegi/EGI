{{-- CoA Vocabulary Modal - Main Container for Traits Selection --}}
<div id="vocabularyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="vocabularyModal.close()"></div>

        {{-- Modal Content --}}
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-2xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            {{-- Header --}}
            <div class="px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                        {{ __('coa_traits.modal_title') }}
                    </h3>
                    <button type="button"
                            class="text-gray-400 transition-colors hover:text-gray-600 focus:outline-none focus:text-gray-600"
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
                                    class="px-1 py-2 text-sm font-medium border-b-2 vocabulary-tab focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active"
                                    onclick="vocabularyModal.switchTab('technique')"
                                    data-tab="technique">
                                {{ __('coa_traits.technique') }}
                                <span id="techniqueCount" class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs font-medium">0</span>
                            </button>
                            <button id="tabMaterials"
                                    class="px-1 py-2 text-sm font-medium border-b-2 vocabulary-tab focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    onclick="vocabularyModal.switchTab('materials')"
                                    data-tab="materials">
                                {{ __('coa_traits.materials') }}
                                <span id="materialsCount" class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium">0</span>
                            </button>
                            <button id="tabSupport"
                                    class="px-1 py-2 text-sm font-medium border-b-2 vocabulary-tab focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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
                                   class="block w-64 py-2 pl-10 pr-10 text-sm leading-5 placeholder-gray-500 transition-all bg-white border border-gray-300 rounded-md focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button id="clearSearchBtn"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 transition-opacity opacity-0"
                                    onclick="vocabularyModal.clearSearch()">
                                <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4 bg-gray-50">
                {{-- Dynamic Content Container --}}
                <div id="vocabularyContent" class="min-h-96">
                    {{-- Loading State --}}
                    <div id="vocabularyLoading" class="hidden">
                        <div class="flex items-center justify-center py-16">
                            <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
                            <span class="ml-3 text-gray-600">{{ __('coa_traits.loading') }}</span>
                        </div>
                    </div>

                    {{-- Content will be loaded here dynamically --}}
                    <div id="vocabularyDynamicContent">
                        {{-- Initial content: categories for technique --}}
                    </div>
                </div>

                {{-- Selected Items Display --}}
                <div id="vocabularySelected" class="pt-4 mt-6 border-t border-gray-200">
                    <h4 class="mb-3 text-sm font-medium text-gray-900">
                        {{ __('coa_traits.selected_items') }}
                    </h4>

                    {{-- Selected Chips Container --}}
                    <div id="vocabularyChips" class="flex flex-wrap gap-3 min-h-[3rem] p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-inner">
                        {{-- Chips will be added here dynamically --}}
                        <div id="vocabularyChipsEmpty" class="text-sm italic text-gray-500 flex items-center justify-center w-full py-4">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a2 2 0 012-2z"/>
                                </svg>
                                <p class="text-gray-500">{{ __('coa_traits.no_items_selected') }}</p>
                                <p class="text-xs text-gray-400 mt-1">Scegli dalle categorie sopra</p>
                            </div>
                        </div>
                    </div>

                    {{-- Add Custom Term Button --}}
                    <div class="mt-3">
                        <button id="addCustomTermBtn"
                                class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
                                       class="flex-1 block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <button onclick="vocabularyModal.addCustomTerm()"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('coa_traits.add') }}
                                </button>
                                <button onclick="vocabularyModal.hideCustomTermInput()"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('coa_traits.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="text-sm text-gray-500">
                    <span id="vocabularySelectionCount">0</span> {{ __('coa_traits.items_selected') }}
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            onclick="vocabularyModal.cancel()">
                        {{ __('coa_traits.cancel') }}
                    </button>
                    <button type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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

    /* Container specifico per i chip nella modale */
    #vocabularyChips {
        @apply flex flex-wrap gap-3 min-h-[3rem] p-3 bg-gray-50 rounded-lg border border-gray-200;
    }

    /* Stili specifici per i chip nella modale con massima priorità */
    #vocabularyChips .vocabulary-chip {
        display: inline-flex !important;
        align-items: center !important;
        padding: 8px 16px !important;
        border-radius: 9999px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        color: white !important;
        border: 2px solid !important;
        position: relative !important;
        min-height: 36px !important;
        transition: all 0.3s ease-in-out !important;
        cursor: default !important;
        backdrop-filter: blur(8px) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15), 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        border-color: #60a5fa !important;
    }

    #vocabularyChips .vocabulary-chip:hover {
        transform: translateY(-2px) scale(1.05) !important;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25), 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }

    #vocabularyChips .vocabulary-chip.custom {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        border-color: #fbbf24 !important;
    }

    #vocabularyChips .vocabulary-chip.technique {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        border-color: #34d399 !important;
    }

    #vocabularyChips .vocabulary-chip.materials {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        border-color: #a78bfa !important;
    }

    #vocabularyChips .vocabulary-chip.support {
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        border-color: #818cf8 !important;
    }

    #vocabularyChips .vocabulary-chip .remove-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 20px !important;
        height: 20px !important;
        margin-left: 8px !important;
        background: rgba(255, 255, 255, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
        backdrop-filter: blur(4px) !important;
    }

    #vocabularyChips .vocabulary-chip .remove-btn:hover {
        background: #ef4444 !important;
        border-color: #f87171 !important;
        transform: rotate(90deg) scale(1.1) !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4) !important;
    }

    #vocabularyChips .vocabulary-chip .remove-btn svg {
        width: 12px !important;
        height: 12px !important;
        stroke-width: 2.5 !important;
        color: white !important;
    }

    .vocabulary-chip {
        @apply inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-300 ease-in-out;
        @apply shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 hover:scale-105;
        @apply border-2 cursor-default;
        border-radius: 9999px !important;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        @apply text-white border-blue-300;
        position: relative;
        overflow: hidden;
        min-height: 36px;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15), 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    .vocabulary-chip::before {
        content: '';
        position: absolute;
        top: 1px;
        left: 1px;
        right: 1px;
        bottom: 1px;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0));
        border-radius: 9999px;
        pointer-events: none;
    }

    .vocabulary-chip::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 9999px;
        box-shadow: inset 0 1px 2px rgba(255,255,255,0.2), inset 0 -1px 2px rgba(0,0,0,0.1);
        pointer-events: none;
    }

    .vocabulary-chip.custom {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        @apply text-white border-amber-300;
        border-radius: 9999px !important;
    }

    .vocabulary-chip.custom .chip-text::after {
        content: ' ✨';
        @apply opacity-70;
    }

    .vocabulary-chip.technique {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        @apply text-white border-emerald-300;
        border-radius: 9999px !important;
    }

    .vocabulary-chip.technique .chip-text::after {
        content: ' 🎨';
        @apply opacity-70;
    }

    .vocabulary-chip.materials {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        @apply text-white border-purple-300;
        border-radius: 9999px !important;
    }

    .vocabulary-chip.materials .chip-text::after {
        content: ' 🧱';
        @apply opacity-70;
    }

    .vocabulary-chip.support {
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        @apply text-white border-indigo-300;
        border-radius: 9999px !important;
    }

    .vocabulary-chip.support .chip-text::after {
        content: ' 📋';
        @apply opacity-70;
    }

    .vocabulary-chip:hover {
        @apply scale-105 shadow-lg;
    }

    .vocabulary-chip.new-chip {
        animation: chipSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), pulseGlow 0.8s ease-out;
    }

    @keyframes pulseGlow {
        0% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }

    .vocabulary-chip .remove-btn {
        @apply ml-2 h-6 w-6 cursor-pointer transition-all duration-200;
        @apply bg-white bg-opacity-25 rounded-full p-1;
        @apply hover:bg-opacity-35 hover:scale-110;
        @apply flex items-center justify-center;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .vocabulary-chip .remove-btn:hover {
        @apply bg-red-500 bg-opacity-95 text-white;
        @apply shadow-lg transform rotate-90 scale-125;
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .vocabulary-chip .remove-btn svg {
        @apply h-3 w-3 stroke-2;
        transition: all 0.2s ease-in-out;
    }

    .vocabulary-chip .remove-btn:hover svg {
        filter: drop-shadow(0 0 2px rgba(255,255,255,0.8));
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

    /* Focus e accessibilità */
    .vocabulary-chip:focus-within {
        @apply ring-2 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-500;
        outline: none;
    }

    .vocabulary-chip .remove-btn:focus {
        @apply ring-2 ring-white ring-opacity-60 ring-offset-1;
        outline: none;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .vocabulary-chip {
            @apply px-2 py-1 text-xs;
        }

        .vocabulary-chip .remove-btn {
            @apply h-4 w-4 ml-1;
        }

        .vocabulary-chip .remove-btn svg {
            @apply h-2.5 w-2.5;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .vocabulary-chip {
            @apply shadow-2xl;
        }

        .vocabulary-chip .remove-btn {
            @apply bg-black bg-opacity-20;
        }
    }

    /* Animation for chips */
    @keyframes chipSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.8) rotate(-3deg);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1) rotate(0deg);
        }
    }

    @keyframes chipSlideOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1) rotate(0deg);
        }
        to {
            opacity: 0;
            transform: translateY(-10px) scale(0.8) rotate(3deg);
        }
    }

    .vocabulary-chip {
        animation: chipSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .vocabulary-chip.removing {
        animation: chipSlideOut 0.3s ease-in-out forwards;
    }

    /* Sparkle effect for custom chips */
    .vocabulary-chip.custom {
        position: relative;
        overflow: visible;
    }

    .vocabulary-chip.custom::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, transparent 30%, rgba(255,215,0,0.6) 50%, transparent 70%);
        border-radius: 0.5rem;
        animation: shimmer 2s infinite;
        z-index: -1;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
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
