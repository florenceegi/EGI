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

console.log("🚀 VocabularyModal: JavaScript file loading...");

window.VocabularyModalController = (function () {
    "use strict";

    console.log("🚀 VocabularyModal: IIFE starting...");

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

    // Utility function to determine category from slug
    function getCategoryFromSlug(slug) {
        console.log("VocabularyModal: Analyzing slug:", slug);

        // Pattern: {category}-{term-name}
        // Esempi: material-leather, support-wall, technique-something, ceramic-wheel-thrown

        if (slug.startsWith("material-")) {
            console.log(
                "VocabularyModal: Slug starts with 'material-' → materials"
            );
            return "materials";
        }

        if (slug.startsWith("support-")) {
            console.log(
                "VocabularyModal: Slug starts with 'support-' → support"
            );
            return "support";
        }

        // Per technique, potrebbe essere sia 'technique-' che altri pattern come 'ceramic-'
        // Quindi tutto il resto va in technique come fallback
        console.log("VocabularyModal: Slug categorized as → technique");
        return "technique";
    }

    // Utility function to get translated name from slug by looking in the current DOM
    function getTranslatedNameFromSlug(slug) {
        console.log(
            "VocabularyModal: Attempting to resolve translated name for slug:",
            slug
        );

        // Cerca nel DOM corrente per l'elemento con questo slug
        const termElement = document.querySelector(`[data-slug="${slug}"]`);
        if (termElement) {
            // Cerca il nome all'interno dell'elemento
            const nameElement =
                termElement.querySelector(".term-name") ||
                termElement.querySelector(".font-medium") ||
                termElement;

            if (nameElement && nameElement.textContent) {
                const translatedName = nameElement.textContent.trim();
                console.log(
                    "VocabularyModal: Found translated name in DOM:",
                    translatedName,
                    "for slug:",
                    slug
                );
                return translatedName;
            }
        }

        console.log(
            "VocabularyModal: Could not find translated name for slug:",
            slug
        );
        return null;
    }

    // Private methods
    function initializeElements() {
        console.log("VocabularyModal: Initializing DOM elements...");

        elements = {
            modal: document.getElementById("vocabularyModal"),
            content: document.getElementById("vocabularyContent"),
            dynamicContent: document.getElementById("vocabularyDynamicContent"),
            loading: document.getElementById("vocabularyLoading"),
            search: document.getElementById("vocabularySearch"),
            clearSearchBtn: document.getElementById("clearSearchBtn"),
            chips: document.getElementById("vocabularyChips"),
            chipsEmpty: document.getElementById("vocabularyChipsEmpty"),
            selectionCount: document.getElementById("vocabularySelectionCount"),
            customTermBtn: document.getElementById("addCustomTermBtn"),
            customTermInput: document.getElementById("customTermInput"),
            customTermText: document.getElementById("customTermText"),
            // Tab elements
            tabTechnique: document.getElementById("tabTechnique"),
            tabMaterials: document.getElementById("tabMaterials"),
            tabSupport: document.getElementById("tabSupport"),
            // Count badges
            techniqueCount: document.getElementById("techniqueCount"),
            materialsCount: document.getElementById("materialsCount"),
            supportCount: document.getElementById("supportCount"),
        };

        // Debug: Log which elements were found
        Object.keys(elements).forEach((key) => {
            const element = elements[key];
            console.log(
                `VocabularyModal: Element '${key}':`,
                element ? "FOUND" : "NOT FOUND"
            );
        });

        const foundElements = Object.values(elements).filter(
            (el) => el !== null
        ).length;
        const totalElements = Object.keys(elements).length;
        console.log(
            `VocabularyModal: Found ${foundElements}/${totalElements} DOM elements`
        );

        if (foundElements === 0) {
            console.error(
                "VocabularyModal: No DOM elements found! Modal HTML may not be present."
            );
        }

        // Validate critical elements exist
        const criticalElements = ["modal", "search", "chips", "dynamicContent"];
        for (const key of criticalElements) {
            if (!elements[key]) {
                console.error(
                    `VocabularyModal: Critical element not found: ${key}`
                );
            }
        }
    }

    function setupEventListeners() {
        // Search input with debounce
        if (elements.search) {
            elements.search.addEventListener("input", function (e) {
                clearTimeout(state.searchTimeout);
                const query = e.target.value;

                // Show/hide clear button
                updateClearSearchButton(query);

                state.searchTimeout = setTimeout(() => {
                    handleSearch(query);
                }, 250);
            });

            // Clear search on escape
            elements.search.addEventListener("keydown", function (e) {
                if (e.key === "Escape") {
                    e.target.value = "";
                    handleSearch("");
                    updateClearSearchButton("");
                }
            });
        }

        // Clear search button
        if (elements.clearSearchBtn) {
            elements.clearSearchBtn.addEventListener("click", function () {
                elements.search.value = "";
                handleSearch("");
                updateClearSearchButton("");
                elements.search.focus();
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

    function updateClearSearchButton(query) {
        if (!elements.clearSearchBtn) return;

        if (query && query.trim().length > 0) {
            elements.clearSearchBtn.classList.add("visible");
            elements.clearSearchBtn.style.opacity = "1";
        } else {
            elements.clearSearchBtn.classList.remove("visible");
            elements.clearSearchBtn.style.opacity = "0";
        }
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

        // Get CSRF token from meta tag
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        fetch(fullUrl, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                ...(csrfToken && { "X-CSRF-TOKEN": csrfToken }),
            },
            credentials: "same-origin", // Include cookies for authentication
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
            // Search within current tab context
            loadContent("/vocabulary/search", {
                q: state.searchQuery,
                tab: state.currentTab,
                locale: document.documentElement.lang || "it",
            });
        } else if (state.searchQuery.length === 0) {
            // Return to category view for current tab
            loadContent("/vocabulary/categories", {
                tab: state.currentTab,
                locale: document.documentElement.lang || "it",
            });
        }

        // Update selected states after search results load
        setTimeout(() => {
            updateSelectedStatesInView();
        }, 300);
    }

    function loadCategories() {
        loadContent("/vocabulary/categories", {
            locale: document.documentElement.lang || "it",
        });
    }

    function updateChipsDisplay() {
        if (!elements.chips) return;

        // Raccoglie TUTTE le selezioni da TUTTE le categorie
        const allSelections = [];

        // Aggiungi selezioni da tutte le categorie con informazione sulla categoria
        Object.keys(state.selections).forEach((category) => {
            state.selections[category].forEach((item) => {
                allSelections.push({
                    ...item,
                    category: category, // Aggiungi info categoria per styling
                });
            });
        });

        if (allSelections.length === 0) {
            elements.chips.innerHTML = `
                <div id="vocabularyChipsEmpty" class="text-sm italic text-gray-500 flex items-center justify-center w-full py-4">
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a2 2 0 012-2z"/>
                        </svg>
                        <p class="text-gray-500">Nessun tratto selezionato</p>
                        <p class="text-xs text-gray-400 mt-1">Scegli dalle categorie sopra</p>
                    </div>
                </div>
            `;
        } else {
            const chipsHtml = allSelections
                .map(
                    (item) => `
                <div class="vocabulary-chip new-chip ${
                    item.isCustom ? "custom" : item.category
                }" data-slug="${item.slug}" data-category="${item.category}">
                    <span class="chip-text">${item.name}</span>
                    <button class="remove-btn"
                            onclick="vocabularyModal.removeSelection('${
                                item.slug
                            }', '${item.category}')"
                            title="Rimuovi ${item.name}"
                            aria-label="Rimuovi ${item.name}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `
                )
                .join("");

            elements.chips.innerHTML = chipsHtml;

            // Rimuovi la classe new-chip dopo l'animazione
            setTimeout(() => {
                const newChips = elements.chips.querySelectorAll(".new-chip");
                newChips.forEach((chip) => chip.classList.remove("new-chip"));
            }, 1200);
        }

        updateSelectionCount();
        updateTabCounts();
    }

    function updateSelectionCount() {
        if (!elements.selectionCount) return;

        const totalSelections = Object.values(state.selections).reduce(
            (total, category) => total + category.length,
            0
        );

        elements.selectionCount.textContent = totalSelections;
    }

    function updateTabCounts() {
        // Update individual tab counts
        if (elements.techniqueCount) {
            const count = state.selections.technique.length;
            elements.techniqueCount.textContent = count;
            elements.techniqueCount.className =
                count > 0
                    ? "ml-2 bg-green-100 text-green-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge"
                    : "ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge";
        }

        if (elements.materialsCount) {
            const count = state.selections.materials.length;
            elements.materialsCount.textContent = count;
            elements.materialsCount.className =
                count > 0
                    ? "ml-2 bg-purple-100 text-purple-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge"
                    : "ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge";
        }

        if (elements.supportCount) {
            const count = state.selections.support.length;
            elements.supportCount.textContent = count;
            elements.supportCount.className =
                count > 0
                    ? "ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge"
                    : "ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium count-badge";
        }
    }

    function switchTab(tabName) {
        console.log("VocabularyModal: Switching to tab:", tabName);

        // Update state
        state.currentTab = tabName;

        // Update tab visual states
        const tabs = ["technique", "materials", "support"];
        tabs.forEach((tab) => {
            const tabElement =
                elements[`tab${tab.charAt(0).toUpperCase() + tab.slice(1)}`];
            if (tabElement) {
                if (tab === tabName) {
                    tabElement.classList.add("active");
                } else {
                    tabElement.classList.remove("active");
                }
            }
        });

        // Update chips display for current tab
        updateChipsDisplay();

        // Clear search and load categories for the new tab
        if (elements.search) {
            elements.search.value = "";
            updateClearSearchButton("");
        }
        state.searchQuery = "";

        // Load categories filtered by current tab
        loadContent("/vocabulary/categories", {
            tab: tabName,
            locale: document.documentElement.lang || "it",
        });

        // Update selected states in current view
        setTimeout(() => {
            updateSelectedStatesInView();
        }, 100);

        // Update custom button text based on current tab
        updateCustomButtonText(tabName);
    }

    function updateCustomButtonText(tabName) {
        const btnTextElement = document.getElementById('addCustomBtnText');
        if (btnTextElement && window.coaTraitsTranslations) {
            const translationKey = `add_custom_${tabName}`;
            const translatedText = window.coaTraitsTranslations[translationKey] || window.coaTraitsTranslations.add_custom;
            btnTextElement.textContent = translatedText;
        }
    }

    function updateSelectedStatesInView() {
        const currentSelections = state.selections[state.currentTab] || [];
        const selectedSlugs = currentSelections.map((item) => item.slug);

        // Update visual state of term items
        const termItems = document.querySelectorAll(".term-item");
        termItems.forEach((item) => {
            const slug = item.getAttribute("data-term-slug");
            if (selectedSlugs.includes(slug)) {
                item.classList.add("selected");
            } else {
                item.classList.remove("selected");
            }
        });
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

            // Initialize tab states
            switchTab("technique");

            // Update chips and tab counts after modal is visible
            updateChipsDisplay();

            // Load initial content - show categories for technique tab
            loadContent("/vocabulary/categories", {
                tab: "technique",
                locale: document.documentElement.lang || "it",
            });

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
            updateClearSearchButton("");

            // Reset to technique tab
            state.currentTab = "technique";

            // Dispatch close event
            window.dispatchEvent(new CustomEvent("vocabularyModal:closed"));
        },

        /**
         * Load category groups (called by vocabulary-categories component)
         * @param {string} category - Category slug
         */
        loadCategory: function (category) {
            console.log("VocabularyModal: Loading category groups:", category);

            loadContent(`/vocabulary/category/${category}/groups`, {
                locale: document.documentElement.lang || "it",
            });
        },

        /**
         * Load group terms (called by vocabulary-groups component)
         * @param {string} category - Category slug
         * @param {string} group - Group name
         */
        loadGroup: function (category, group) {
            console.log(
                "VocabularyModal: Loading group terms:",
                category,
                group
            );

            loadContent(
                `/vocabulary/category/${category}/group/${encodeURIComponent(
                    group
                )}`,
                {
                    locale: document.documentElement.lang || "it",
                }
            );
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

            // Determina la categoria corretta dal slug
            const correctCategory = getCategoryFromSlug(termSlug);

            const correctSelections = state.selections[correctCategory];

            // Check if already selected
            const existingIndex = correctSelections.findIndex(
                (item) => item.slug === termSlug
            );

            if (existingIndex >= 0) {
                // Remove if already selected
                correctSelections.splice(existingIndex, 1);
            } else {
                // Add new selection to CORRECT category
                correctSelections.push({
                    slug: termSlug,
                    name: termName,
                    isCustom: false,
                });
            }

            updateChipsDisplay();
            updateTabCounts(); // Aggiorna anche i contatori dei tab

            // Update visual states immediately
            setTimeout(() => {
                updateSelectedStatesInView();
            }, 50);
        },

        /**
         * Switch between tabs (technique, materials, support)
         * @param {string} tabName - Tab name to switch to
         */
        switchTab: function (tabName) {
            if (["technique", "materials", "support"].includes(tabName)) {
                switchTab(tabName);
            }
        },

        /**
         * Remove a selection
         * @param {string} slug - Term slug to remove
         * @param {string} category - Category of the term (optional, if not provided will search all)
         */
        removeSelection: function (slug, category = null) {
            console.log(
                "VocabularyModal: Removing selection:",
                slug,
                "from category:",
                category
            );

            // Trova il chip da rimuovere e applica animazione di uscita
            const chipToRemove = document.querySelector(
                `.vocabulary-chip[data-slug="${slug}"]`
            );

            if (chipToRemove) {
                // Aggiungi classe per animazione di rimozione
                chipToRemove.classList.add("removing");

                // Aspetta che l'animazione finisca prima di rimuovere dall'array
                setTimeout(() => {
                    if (category) {
                        // Rimuovi dalla categoria specifica
                        const categorySelections = state.selections[category];
                        if (categorySelections) {
                            const index = categorySelections.findIndex(
                                (item) => item.slug === slug
                            );
                            if (index >= 0) {
                                categorySelections.splice(index, 1);
                            }
                        }
                    } else {
                        // Cerca e rimuovi da tutte le categorie
                        Object.keys(state.selections).forEach((cat) => {
                            const categorySelections = state.selections[cat];
                            const index = categorySelections.findIndex(
                                (item) => item.slug === slug
                            );
                            if (index >= 0) {
                                categorySelections.splice(index, 1);
                            }
                        });
                    }

                    updateChipsDisplay();

                    // Aggiorna stati visivi se siamo nel tab giusto
                    setTimeout(() => {
                        updateSelectedStatesInView();
                    }, 50);
                }, 300); // Aspetta che l'animazione CSS finisca
            } else {
                // Fallback se non trova il chip, rimuovi direttamente
                if (category) {
                    const categorySelections = state.selections[category];
                    if (categorySelections) {
                        const index = categorySelections.findIndex(
                            (item) => item.slug === slug
                        );
                        if (index >= 0) {
                            categorySelections.splice(index, 1);
                        }
                    }
                } else {
                    Object.keys(state.selections).forEach((cat) => {
                        const categorySelections = state.selections[cat];
                        const index = categorySelections.findIndex(
                            (item) => item.slug === slug
                        );
                        if (index >= 0) {
                            categorySelections.splice(index, 1);
                        }
                    });
                }

                updateChipsDisplay();
                setTimeout(() => {
                    updateSelectedStatesInView();
                }, 50);
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
            updateClearSearchButton("");

            // Load categories for current tab
            loadContent("/vocabulary/categories", {
                tab: state.currentTab,
                locale: document.documentElement.lang || "it",
            });
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
            // Prima di impostare le selezioni, risolviamo i nomi tradotti per gli slug
            const resolvedSelections = JSON.parse(JSON.stringify(selections));

            Object.keys(resolvedSelections).forEach((category) => {
                resolvedSelections[category] = resolvedSelections[category].map(
                    (item) => {
                        if (
                            !item.isCustom &&
                            item.name &&
                            item.name.includes("-")
                        ) {
                            // Se il nome sembra essere un slug, proviamo a risolverlo
                            console.log(
                                "VocabularyModal: Attempting to resolve name for slug:",
                                item.slug,
                                "current name:",
                                item.name
                            );
                            return {
                                ...item,
                                name:
                                    getTranslatedNameFromSlug(item.slug) ||
                                    item.name,
                            };
                        }
                        return item;
                    }
                );
            });

            state.selections = resolvedSelections;
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

console.log(
    "🚀 VocabularyModal: IIFE completed, object created:",
    window.VocabularyModalController
);

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("VocabularyModalController: Controller loaded and ready");

    // Also expose as vocabularyModal for backward compatibility
    window.vocabularyModal = window.VocabularyModalController;
});

// Export for CommonJS/ES6 if needed
if (typeof module !== "undefined" && module.exports) {
    module.exports = window.VocabularyModalController;
}
