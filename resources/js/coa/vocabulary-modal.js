/**
 * Vocabulary Modal Controller for CoA Traits Selection
 *
 * Manages the vocabulary selection modal for technique, materials, and support traits.
 * Coordinates between the main modal and the individual vocabulary components.
 *
 * @author AI Assistant for FlorenceEGI CoA System
 * @version 1.0.0
 * @date 2025-09-19
 */

window.vocabularyModal = (function () {
    "use strict";

    // Private state
    let state = {
        isOpen: false,
        currentTab: "technique",
        searchQuery: "",
        searchTimeout: null,
        selections: {
            technique: [], // Array of {slug, name, isCustom}
            materials: [],
            support: [],
        },
        originalSelections: null, // Backup for cancel
        callbacks: {
            onConfirm: null,
            onCancel: null,
        },
    };

    // Private DOM references
    let elements = {};

    // Private methods
    function initializeElements() {
        elements = {
            modal: document.getElementById("vocabularyModal"),
            content: document.getElementById("vocabularyContent"),
            dynamicContent: document.getElementById("vocabularyDynamicContent"),
            loading: document.getElementById("vocabularyLoading"),
            search: document.getElementById("vocabularySearch"),
            chips: document.getElementById("vocabularyChips"),
            chipsEmpty: document.getElementById("vocabularyChipsEmpty"),
            selectionCount: document.getElementById("vocabularySelectionCount"),
            customTermBtn: document.getElementById("addCustomTermBtn"),
            customTermInput: document.getElementById("customTermInput"),
            customTermText: document.getElementById("customTermText"),
        };

        // Validate all elements exist
        for (const [key, element] of Object.entries(elements)) {
            if (!element) {
                console.error(
                    `VocabularyModal: Required element not found: ${key}`
                );
            }
        }
    }

    function setupEventListeners() {
        // Search input with debounce
        if (elements.search) {
            elements.search.addEventListener("input", function (e) {
                clearTimeout(state.searchTimeout);
                state.searchTimeout = setTimeout(() => {
                    handleSearch(e.target.value);
                }, 250);
            });

            // Clear search on escape
            elements.search.addEventListener("keydown", function (e) {
                if (e.key === "Escape") {
                    e.target.value = "";
                    handleSearch("");
                }
            });
        }

        // Custom term input enter key
        if (elements.customTermText) {
            elements.customTermText.addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    addCustomTerm();
                }
            });
        }

        // Keyboard navigation
        if (elements.modal) {
            elements.modal.addEventListener(
                "keydown",
                handleKeyboardNavigation
            );
        }

        // Focus trap
        setupFocusTrap();
    }

    function handleKeyboardNavigation(e) {
        switch (e.key) {
            case "Escape":
                close();
                break;
            case "Tab":
                // Handle tab navigation within modal
                trapFocus(e);
                break;
        }
    }

    function setupFocusTrap() {
        // Implementation of focus trapping for accessibility
        const focusableElements =
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

        elements.modal?.addEventListener("keydown", function (e) {
            if (e.key === "Tab") {
                trapFocus(e);
            }
        });
    }

    function trapFocus(e) {
        const focusableContent =
            elements.modal?.querySelectorAll(focusableElements);
        if (!focusableContent || focusableContent.length === 0) return;

        const firstFocusableElement = focusableContent[0];
        const lastFocusableElement =
            focusableContent[focusableContent.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstFocusableElement) {
                lastFocusableElement.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === lastFocusableElement) {
                firstFocusableElement.focus();
                e.preventDefault();
            }
        }
    }

    function showLoading() {
        if (elements.loading) {
            elements.loading.classList.remove("hidden");
        }
        if (elements.dynamicContent) {
            elements.dynamicContent.style.opacity = "0.5";
        }
    }

    function hideLoading() {
        if (elements.loading) {
            elements.loading.classList.add("hidden");
        }
        if (elements.dynamicContent) {
            elements.dynamicContent.style.opacity = "1";
        }
    }

    function loadContent(url, params = {}) {
        showLoading();

        const urlParams = new URLSearchParams(params);
        const fullUrl = `${url}?${urlParams.toString()}`;

        fetch(fullUrl, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "text/html",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `HTTP ${response.status}: ${response.statusText}`
                    );
                }
                return response.text();
            })
            .then((html) => {
                if (elements.dynamicContent) {
                    elements.dynamicContent.innerHTML = html;
                }
                hideLoading();
            })
            .catch((error) => {
                console.error("VocabularyModal: Error loading content:", error);
                if (elements.dynamicContent) {
                    elements.dynamicContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-600 mb-2">Errore nel caricamento</div>
                        <button onclick="vocabularyModal.retryLastOperation()"
                                class="text-blue-600 hover:text-blue-800 underline">
                            Riprova
                        </button>
                    </div>
                `;
                }
                hideLoading();
            });
    }

    function handleSearch(query) {
        state.searchQuery = query.trim();

        if (state.searchQuery.length >= 2) {
            // Search across all categories
            loadContent("/vocabulary/search", {
                q: state.searchQuery,
                category: state.currentTab,
                locale: document.documentElement.lang || "it",
            });
        } else if (state.searchQuery.length === 0) {
            // Return to category view
            loadCategories();
        }
    }

    function loadCategories() {
        loadContent("/vocabulary/categories", {
            locale: document.documentElement.lang || "it",
        });
    }

    function updateTabsUI() {
        const tabs = document.querySelectorAll(".vocabulary-tab");
        tabs.forEach((tab) => {
            const tabName = tab.dataset.tab;
            if (tabName === state.currentTab) {
                tab.classList.add("active");
                tab.classList.remove("border-transparent", "text-gray-500");
                tab.classList.add("border-blue-500", "text-blue-600");
            } else {
                tab.classList.remove("active");
                tab.classList.remove("border-blue-500", "text-blue-600");
                tab.classList.add("border-transparent", "text-gray-500");
            }
        });
    }

    function updateChipsDisplay() {
        if (!elements.chips) return;

        const currentSelections = state.selections[state.currentTab] || [];

        if (currentSelections.length === 0) {
            elements.chips.innerHTML = `
                <div id="vocabularyChipsEmpty" class="text-sm text-gray-500 italic">
                    Nessun elemento selezionato per ${state.currentTab}
                </div>
            `;
        } else {
            const chipsHtml = currentSelections
                .map(
                    (item) => `
                <div class="vocabulary-chip ${
                    item.isCustom ? "custom" : ""
                }" data-slug="${item.slug}">
                    <span>${item.name}</span>
                    <svg class="remove-btn" onclick="vocabularyModal.removeSelection('${
                        item.slug
                    }')"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            `
                )
                .join("");

            elements.chips.innerHTML = chipsHtml;
        }

        updateSelectionCount();
    }

    function updateSelectionCount() {
        if (!elements.selectionCount) return;

        const totalSelections = Object.values(state.selections).reduce(
            (total, category) => total + category.length,
            0
        );

        elements.selectionCount.textContent = totalSelections;
    }

    function backupSelections() {
        state.originalSelections = JSON.parse(JSON.stringify(state.selections));
    }

    function restoreSelections() {
        if (state.originalSelections) {
            state.selections = JSON.parse(
                JSON.stringify(state.originalSelections)
            );
        }
    }

    // Public API
    return {
        /**
         * Open the vocabulary modal
         * @param {Object} options - Configuration options
         * @param {Object} options.selections - Initial selections {technique: [], materials: [], support: []}
         * @param {Function} options.onConfirm - Callback when user confirms selections
         * @param {Function} options.onCancel - Callback when user cancels
         */
        open: function (options = {}) {
            console.log(
                "VocabularyModal: Opening modal with options:",
                options
            );

            if (!elements.modal) {
                initializeElements();
            }

            // Set initial state
            state.isOpen = true;
            state.currentTab = "technique";
            state.searchQuery = "";
            state.callbacks = {
                onConfirm: options.onConfirm || null,
                onCancel: options.onCancel || null,
            };

            // Set initial selections
            if (options.selections) {
                state.selections = JSON.parse(
                    JSON.stringify(options.selections)
                );
            } else {
                state.selections = {
                    technique: [],
                    materials: [],
                    support: [],
                };
            }

            backupSelections();

            // Setup event listeners if not already done
            if (!elements.search?.hasAttribute("data-listeners-attached")) {
                setupEventListeners();
                elements.search?.setAttribute(
                    "data-listeners-attached",
                    "true"
                );
            }

            // Show modal
            elements.modal?.classList.remove("hidden");

            // Load initial content
            this.switchTab("technique");

            // Focus on search input
            setTimeout(() => {
                elements.search?.focus();
            }, 100);

            // Dispatch open event
            window.dispatchEvent(
                new CustomEvent("vocabularyModal:opened", {
                    detail: { selections: state.selections },
                })
            );
        },

        /**
         * Close the modal
         */
        close: function () {
            console.log("VocabularyModal: Closing modal");

            if (!state.isOpen) return;

            state.isOpen = false;
            elements.modal?.classList.add("hidden");

            // Clear search
            if (elements.search) {
                elements.search.value = "";
            }

            // Dispatch close event
            window.dispatchEvent(new CustomEvent("vocabularyModal:closed"));
        },

        /**
         * Switch to a different tab
         * @param {string} tab - Tab name: 'technique', 'materials', 'support'
         */
        switchTab: function (tab) {
            console.log("VocabularyModal: Switching to tab:", tab);

            if (!["technique", "materials", "support"].includes(tab)) {
                console.error("VocabularyModal: Invalid tab:", tab);
                return;
            }

            state.currentTab = tab;

            // Update UI
            updateTabsUI();
            updateChipsDisplay();

            // Clear search
            if (elements.search) {
                elements.search.value = "";
            }
            state.searchQuery = "";

            // Load categories for the new tab
            loadCategories();
        },

        /**
         * Load category terms (called by vocabulary-categories component)
         * @param {string} category - Category slug
         */
        loadCategory: function (category) {
            console.log("VocabularyModal: Loading category:", category);

            loadContent(`/vocabulary/category/${category}`, {
                locale: document.documentElement.lang || "it",
            });
        },

        /**
         * Show categories view (called by vocabulary-terms component)
         */
        showCategories: function () {
            console.log("VocabularyModal: Showing categories");
            loadCategories();
        },

        /**
         * Select a term (called by vocabulary-terms component)
         * @param {string} termSlug - Term slug
         * @param {string} termName - Term display name
         */
        selectTerm: function (termSlug, termName) {
            console.log("VocabularyModal: Selecting term:", termSlug, termName);

            const currentSelections = state.selections[state.currentTab];

            // Check if already selected
            const existingIndex = currentSelections.findIndex(
                (item) => item.slug === termSlug
            );

            if (existingIndex >= 0) {
                // Remove if already selected
                currentSelections.splice(existingIndex, 1);
            } else {
                // Add new selection
                currentSelections.push({
                    slug: termSlug,
                    name: termName,
                    isCustom: false,
                });
            }

            updateChipsDisplay();
        },

        /**
         * Remove a selection
         * @param {string} slug - Term slug to remove
         */
        removeSelection: function (slug) {
            console.log("VocabularyModal: Removing selection:", slug);

            const currentSelections = state.selections[state.currentTab];
            const index = currentSelections.findIndex(
                (item) => item.slug === slug
            );

            if (index >= 0) {
                currentSelections.splice(index, 1);
                updateChipsDisplay();
            }
        },

        /**
         * Show custom term input
         */
        showCustomTermInput: function () {
            elements.customTermInput?.classList.remove("hidden");
            elements.customTermText?.focus();
        },

        /**
         * Hide custom term input
         */
        hideCustomTermInput: function () {
            elements.customTermInput?.classList.add("hidden");
            if (elements.customTermText) {
                elements.customTermText.value = "";
            }
        },

        /**
         * Add custom term
         */
        addCustomTerm: function () {
            const customText = elements.customTermText?.value?.trim();

            if (!customText || customText.length > 60) {
                alert("Inserisci un termine valido (max 60 caratteri)");
                return;
            }

            const customSlug = `custom_${Date.now()}_${Math.random()
                .toString(36)
                .substr(2, 9)}`;

            state.selections[state.currentTab].push({
                slug: customSlug,
                name: customText,
                isCustom: true,
            });

            this.hideCustomTermInput();
            updateChipsDisplay();
        },

        /**
         * Clear search and show categories
         */
        clearSearch: function () {
            if (elements.search) {
                elements.search.value = "";
            }
            state.searchQuery = "";
            loadCategories();
        },

        /**
         * Retry last operation (called by vocabulary-error component)
         */
        retryLastOperation: function () {
            console.log("VocabularyModal: Retrying last operation");
            loadCategories();
        },

        /**
         * Confirm selections and close
         */
        confirm: function () {
            console.log(
                "VocabularyModal: Confirming selections:",
                state.selections
            );

            // Dispatch confirm event
            window.dispatchEvent(
                new CustomEvent("vocabularyModal:confirmed", {
                    detail: { selections: state.selections },
                })
            );

            // Call callback
            if (typeof state.callbacks.onConfirm === "function") {
                state.callbacks.onConfirm(state.selections);
            }

            this.close();
        },

        /**
         * Cancel and restore original selections
         */
        cancel: function () {
            console.log("VocabularyModal: Canceling");

            restoreSelections();

            // Dispatch cancel event
            window.dispatchEvent(new CustomEvent("vocabularyModal:canceled"));

            // Call callback
            if (typeof state.callbacks.onCancel === "function") {
                state.callbacks.onCancel();
            }

            this.close();
        },

        /**
         * Get current selections
         * @returns {Object} Current selections
         */
        getSelections: function () {
            return JSON.parse(JSON.stringify(state.selections));
        },

        /**
         * Set selections programmatically
         * @param {Object} selections - New selections
         */
        setSelections: function (selections) {
            state.selections = JSON.parse(JSON.stringify(selections));
            updateChipsDisplay();
        },

        /**
         * Get current state (for debugging)
         * @returns {Object} Current state
         */
        getState: function () {
            return JSON.parse(JSON.stringify(state));
        },
    };
})();

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("VocabularyModal: Controller loaded and ready");
});

// Export for CommonJS/ES6 if needed
if (typeof module !== "undefined" && module.exports) {
    module.exports = window.vocabularyModal;
}
