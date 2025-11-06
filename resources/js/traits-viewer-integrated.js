/**
 * Integrated Traits Viewer & Image Manager - CLEAN VERSION
 * Handles trait display, editing, removal, and image management in a single module
 *
 * @package FlorenceEGI\Traits\Assets
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 3.0.0 (Clean System)
 * @date 2025-09-01
 */

// =============================================================================
// TRANSLATIONS & GLOBALS
// =============================================================================

// These will be populated by the Blade template
window.TraitsTranslations = window.TraitsTranslations || {};
window.traitTranslations = window.traitTranslations || {};

// =============================================================================
// TOAST MANAGER
// =============================================================================

window.ToastManager = {
    container: null,

    init() {
        this.container = document.getElementById("toast-container-viewer");
        if (!this.container) {
            this.container = document.createElement("div");
            this.container.className = "toast-container";
            this.container.id = "toast-container-viewer";
            document.body.appendChild(this.container);
        }
    },

    show(message, type = "info", title = null, duration = 4000) {
        this.init();

        const toast = document.createElement("div");
        toast.className = `toast ${type}`;

        const icons = {
            success: "✅",
            error: "❌",
            warning: "⚠️",
            info: "ℹ️",
        };

        const content = `
            <div class="toast-content">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <div class="toast-text">
                    ${title ? `<div class="toast-title">${title}</div>` : ""}
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;

        toast.innerHTML = content;
        this.container.appendChild(toast);

        // Auto-remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, duration);

        return toast;
    },

    success(message, title = null) {
        return this.show(message, "success", title);
    },

    error(message, title = null) {
        return this.show(message, "error", title);
    },

    warning(message, title = null) {
        return this.show(message, "warning", title);
    },

    info(message, title = null) {
        return this.show(message, "info", title);
    },
};

// =============================================================================
// TRAITS VIEWER (Original Logic)
// =============================================================================

const TraitsViewer = {
    state: {
        egiId: null,
        canEdit: false,
        container: null,
        categories: [],
        availableTypes: [],
        isInitialized: false,
    },

    // Flag globale per event delegation
    globalListenersAttached: false,

    /**
     * Helper function to translate trait values
     * @param {string} value - The English value to translate
     * @returns {string} - The translated value or original if not found
     */
    translateValue(value) {
        if (
            window.traitElementTranslations &&
            window.traitElementTranslations.values
        ) {
            return window.traitElementTranslations.values[value] || value;
        }
        return value;
    },

    init(egiId, canEdit = false, categories = [], availableTypes = []) {
        if (this.state.isInitialized) {
            console.log("TraitsViewer: Already initialized");
            return;
        }

        console.log("TraitsViewer: Initializing with egiId:", egiId);

        this.state.egiId = egiId;
        this.state.canEdit = canEdit;
        this.state.categories = categories;
        this.state.availableTypes = availableTypes;
        this.state.container = document.querySelector(
            `#traits-viewer-${egiId}`
        );

        if (!this.state.container) {
            console.error(
                "TraitsViewer: Container not found for egiId:",
                egiId
            );
            return;
        }

        this.setupEventListeners();
        this.state.isInitialized = true;

        console.log("TraitsViewer: Initialized successfully");
    },

    setupEventListeners() {
        // Event delegation globale - solo UNA volta per tutto il sistema
        if (!TraitsViewer.globalListenersAttached) {
            console.log("TraitsViewer: Setting up global event delegation...");

            document.addEventListener("click", (e) => {
                // Gestisci pulsante "remove trait"
                if (e.target.matches(".trait-remove")) {
                    e.preventDefault();
                    e.stopPropagation();
                    const traitId = e.target.dataset.traitId;
                    if (traitId) {
                        TraitsViewer.removeTrait(traitId);
                    }
                    return;
                }

                // Gestisci pulsante "add trait"
                if (
                    e.target.matches(".add-trait-btn") ||
                    e.target.closest(".add-trait-btn")
                ) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log(
                        "TraitsViewer: Add trait button clicked via delegation"
                    );
                    TraitsViewer.openModal();
                    return;
                }
            });

            TraitsViewer.globalListenersAttached = true;
            console.log("TraitsViewer: Global event delegation setup complete");
        } else {
            console.log(
                "TraitsViewer: Global event delegation already attached, skipping..."
            );
        }
    },

    async removeTrait(traitId) {
        if (
            !confirm(
                window.TraitsTranslations.confirm_remove ||
                    "Are you sure you want to remove this trait?"
            )
        ) {
            return;
        }

        console.log("TraitsViewer: Removing trait:", traitId);

        try {
            const response = await fetch(
                `/egis/${this.state.egiId}/traits/${traitId}`,
                {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                }
            );

            const data = await response.json();

            if (data.success) {
                ToastManager.success(
                    window.TraitsTranslations.remove_success,
                    "🎯 Trait Rimosso"
                );

                const traitCard = document.querySelector(
                    `[data-trait-id="${traitId}"]`
                );
                if (traitCard) {
                    traitCard.style.transition = "all 0.3s ease";
                    traitCard.style.opacity = "0";
                    traitCard.style.transform = "scale(0.8)";

                    setTimeout(() => {
                        traitCard.remove();
                    }, 300);
                }

                const counter = document.querySelector(".traits-count");
                if (counter) {
                    const currentCount = parseInt(counter.textContent) || 0;
                    counter.textContent = Math.max(0, currentCount - 1);
                }
            } else {
                ToastManager.error(
                    window.TraitsTranslations.remove_error +
                        ": " +
                        (data.message ||
                            window.TraitsTranslations.unknown_error),
                    "❌ Errore"
                );
            }
        } catch (error) {
            console.error("TraitsViewer: Error removing trait:", error);
            ToastManager.error(
                window.TraitsTranslations.network_error,
                "🌐 Errore di Rete"
            );
        }
    },

    async openModal() {
        console.log("TraitsViewer: Opening modal for adding traits");
        console.log("TraitsViewer: canEdit state:", this.state.canEdit);
        console.log("TraitsViewer: Looking for modal #trait-modal-viewer");

        // Trova il container del traits viewer per determinare canEdit
        const viewerContainer = document.querySelector(
            '[id^="traits-viewer-"]'
        );
        const canEdit = viewerContainer
            ? viewerContainer.dataset.canEdit === "true"
            : this.state.canEdit;

        console.log("TraitsViewer: Determined canEdit:", canEdit);

        if (!canEdit) {
            console.log("TraitsViewer: Cannot edit, showing warning");
            ToastManager.warning(
                window.TraitsTranslations.creator_only_modify ||
                    "Solo il creatore può modificare i traits"
            );
            return;
        }

        const modal = document.querySelector("#trait-modal-viewer");
        console.log("TraitsViewer: Modal element found:", !!modal);

        if (modal) {
            console.log("TraitsViewer: Setting up modal display...");

            // Move modal to body to avoid parent positioning issues
            if (modal.parentNode !== document.body) {
                console.log("TraitsViewer: Moving modal to body");
                document.body.appendChild(modal);
            }

            // Show the modal with proper styling
            modal.classList.remove("hidden");
            modal.style.display = "flex";
            modal.style.position = "fixed";
            modal.style.top = "0";
            modal.style.left = "0";
            modal.style.width = "100vw";
            modal.style.height = "100vh";
            modal.style.zIndex = "99999";
            modal.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
            modal.style.alignItems = "center";
            modal.style.justifyContent = "center";
            modal.style.padding = "1rem";
            modal.style.visibility = "visible";
            modal.style.opacity = "1";

            // Ensure modal content is visible
            const modalContent = modal.querySelector(".modal-content");
            console.log("TraitsViewer: Modal content found:", !!modalContent);

            if (modalContent) {
                console.log("TraitsViewer: Applying modal content styles");
                modalContent.style.cssText = `
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    transform: scale(1) !important;
                    z-index: 10001 !important;
                    background: #ffffff !important;
                    border-radius: 0.75rem !important;
                    max-width: 500px !important;
                    width: 90% !important;
                    max-height: 80vh !important;
                    overflow-y: auto !important;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
                    position: relative !important;
                    margin: auto !important;
                    pointer-events: auto !important;
                `;
            }

            console.log("TraitsViewer: Modal opened successfully");
            ToastManager.success("Modal aperto!", "Debug");

            // Carica le categorie dopo aver aperto il modal
            this.loadCategories();
        } else {
            console.error(
                "TraitsViewer: Modal not found with selector #trait-modal-viewer"
            );
            ToastManager.error("Modal non trovato!", "Errore");
        }
    },

    closeModal() {
        console.log("TraitsViewer: Closing modal");
        const modal = document.querySelector("#trait-modal-viewer");
        if (modal) {
            modal.style.display = "none";
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    },

    async loadCategories() {
        console.log("TraitsViewer: Loading categories...");
        try {
            const response = await fetch("/traits/categories");
            const data = await response.json();

            if (data.success) {
                this.renderCategories(data.categories);
            } else {
                console.error(
                    "TraitsViewer: Error loading categories:",
                    data.message
                );
                ToastManager.error(
                    "Errore nel caricamento delle categorie",
                    "Errore"
                );
            }
        } catch (error) {
            console.error(
                "TraitsViewer: Network error loading categories:",
                error
            );
            ToastManager.error(
                "Errore di rete nel caricamento delle categorie",
                "Errore"
            );
        }
    },

    renderCategories(categories) {
        console.log("TraitsViewer: Rendering categories:", categories);
        const container = document.getElementById("category-selector-viewer");
        if (!container) {
            console.error("TraitsViewer: Category container not found");
            return;
        }

        container.innerHTML = "";
        categories.forEach((category) => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "category-btn";
            button.dataset.categoryId = category.id;

            // Use translated_name if available, fallback to name
            const displayName = category.translated_name || category.name;
            button.innerHTML = `${category.icon} ${displayName}`;
            button.onclick = () => this.onCategorySelected(category.id);
            container.appendChild(button);
        });
    },

    async onCategorySelected(categoryId) {
        console.log("TraitsViewer: Category selected:", categoryId);

        // Highlight selected category
        const categoryBtns = document.querySelectorAll(
            "#category-selector-viewer .category-btn"
        );
        categoryBtns.forEach((btn) => btn.classList.remove("active"));
        const selectedBtn = document.querySelector(
            `#category-selector-viewer .category-btn[data-category-id="${categoryId}"]`
        );
        if (selectedBtn) {
            selectedBtn.classList.add("active");
        }

        // Reset subsequent selections
        const typeSelect = document.getElementById("trait-type-select-viewer");
        if (typeSelect) {
            typeSelect.value = "";
        }

        const valueGroup = document.getElementById(
            "value-selector-group-viewer"
        );
        if (valueGroup) {
            valueGroup.style.display = "none";
        }

        const preview = document.getElementById("trait-preview-viewer");
        if (preview) {
            preview.style.display = "none";
        }

        // Load trait types for this category
        await this.loadTraitTypes(categoryId);

        // Show trait type selector
        const typeGroup = document.getElementById("type-selector-group-viewer");
        if (typeGroup) {
            typeGroup.style.display = "block";
        }

        // Validate form
        this.validateForm();
    },

    async loadTraitTypes(categoryId) {
        console.log(
            "TraitsViewer: Loading trait types for category:",
            categoryId
        );
        try {
            const response = await fetch(
                `/traits/categories/${categoryId}/types`
            );
            const data = await response.json();

            if (data.success) {
                this.renderTraitTypes(data.types);
            } else {
                console.error(
                    "TraitsViewer: Error loading trait types:",
                    data.message
                );
                ToastManager.error(
                    "Errore nel caricamento dei tipi di trait",
                    "Errore"
                );
            }
        } catch (error) {
            console.error(
                "TraitsViewer: Network error loading trait types:",
                error
            );
            ToastManager.error(
                "Errore di rete nel caricamento dei tipi",
                "Errore"
            );
        }
    },

    renderTraitTypes(types) {
        console.log("TraitsViewer: Rendering trait types:", types);
        const select = document.getElementById("trait-type-select-viewer");
        if (!select) {
            console.error("TraitsViewer: Trait type select not found");
            return;
        }

        // Store types data for later use
        this.state.availableTypes = types;

        select.innerHTML = '<option value="">Scegli un tipo...</option>';
        types.forEach((type) => {
            console.log("TraitsViewer: Processing type:", type);
            console.log("- type.input_type:", type.input_type);
            console.log("- type.has_fixed_values:", type.has_fixed_values);
            console.log("- type.allowed_values:", type.allowed_values);

            const option = document.createElement("option");
            option.value = type.id;

            // Use translated_name if available, fallback to name
            const displayName = type.translated_name || type.name;
            option.textContent = displayName;
            option.dataset.inputType = type.input_type || "text";

            // Determina has_fixed_values: true se esiste allowed_values con elementi
            const hasAllowedValues =
                type.allowed_values &&
                Array.isArray(type.allowed_values) &&
                type.allowed_values.length > 0;
            option.dataset.hasFixedValues = hasAllowedValues ? "true" : "false";

            console.log("- hasAllowedValues:", hasAllowedValues);
            console.log(
                "- Setting hasFixedValues to:",
                option.dataset.hasFixedValues
            );

            // Store allowed_values as JSON string in dataset
            if (type.allowed_values) {
                option.dataset.allowedValues = JSON.stringify(
                    type.allowed_values
                );
                console.log(
                    "- Stored allowed_values:",
                    option.dataset.allowedValues
                );
            } else {
                console.log("- No allowed_values to store");
            }

            console.log("- Final dataset:", option.dataset);
            select.appendChild(option);
        });
    },

    onTypeSelected() {
        console.log("TraitsViewer: Trait type selected");
        const select = document.getElementById("trait-type-select-viewer");
        if (!select || !select.value) {
            console.log("TraitsViewer: No select or no value selected");
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const inputType = selectedOption.dataset.inputType;
        const hasFixedValues = selectedOption.dataset.hasFixedValues;

        console.log("TraitsViewer: Selected option details:");
        console.log("- Option text:", selectedOption.textContent);
        console.log("- Option value:", selectedOption.value);
        console.log("- inputType (raw):", inputType);
        console.log("- hasFixedValues (raw):", hasFixedValues);
        console.log('- hasFixedValues === "true":', hasFixedValues === "true");
        console.log("- hasFixedValues === true:", hasFixedValues === true);
        console.log("- typeof hasFixedValues:", typeof hasFixedValues);

        // Show value input section
        const valueGroup = document.getElementById(
            "value-selector-group-viewer"
        );
        if (valueGroup) {
            valueGroup.style.display = "block";
        }

        // Convert string to boolean
        const hasFixedValuesBool =
            hasFixedValues === "true" || hasFixedValues === true;
        console.log("TraitsViewer: hasFixedValuesBool:", hasFixedValuesBool);

        // Render appropriate input based on type
        this.renderValueInput(inputType, hasFixedValuesBool, select.value);
    },

    renderValueInput(inputType, hasFixedValues, typeId) {
        console.log("TraitsViewer: Rendering value input:", {
            inputType,
            hasFixedValues,
            typeId,
        });
        const container = document.getElementById(
            "value-input-container-viewer"
        );
        if (!container) {
            console.error("TraitsViewer: Value input container not found");
            return;
        }

        console.log("TraitsViewer: hasFixedValues =", hasFixedValues);

        if (hasFixedValues) {
            // Get allowed values from the stored type data
            const select = document.getElementById("trait-type-select-viewer");
            const selectedOption = select.options[select.selectedIndex];
            console.log("TraitsViewer: Selected option:", selectedOption);
            console.log(
                "TraitsViewer: Selected option dataset:",
                selectedOption.dataset
            );

            const allowedValuesJson = selectedOption.dataset.allowedValues;
            console.log("TraitsViewer: allowedValuesJson =", allowedValuesJson);

            if (allowedValuesJson && allowedValuesJson !== "undefined") {
                try {
                    const allowedValues = JSON.parse(allowedValuesJson);
                    console.log(
                        "TraitsViewer: Parsed allowed values:",
                        allowedValues
                    );
                    this.renderFixedValuesSelect(allowedValues, container);
                } catch (error) {
                    console.error(
                        "TraitsViewer: Error parsing allowed values:",
                        error
                    );
                    console.log(
                        "TraitsViewer: Falling back to text input due to parse error"
                    );
                    this.renderTextInput(container);
                }
            } else {
                console.log(
                    "TraitsViewer: No allowed values found in dataset, using text input"
                );
                this.renderTextInput(container);
            }
        } else {
            console.log(
                "TraitsViewer: No fixed values, rendering dynamic input"
            );
            // Create input based on type
            this.renderDynamicInput(inputType, container);
        }
    },

    renderFixedValuesSelect(allowedValues, container) {
        console.log(
            "TraitsViewer: Rendering fixed values select:",
            allowedValues
        );
        let html =
            '<select class="form-select" id="trait-value-select-viewer" onchange="TraitsViewer.updatePreview()"><option value="">Scegli un valore...</option>';

        // allowedValues might be an array or an object
        if (Array.isArray(allowedValues)) {
            allowedValues.forEach((value) => {
                if (typeof value === "string") {
                    const translatedValue = this.translateValue(value);
                    html += `<option value="${value}">${translatedValue}</option>`;
                } else if (
                    typeof value === "object" &&
                    value.value &&
                    value.label
                ) {
                    const translatedLabel = this.translateValue(value.label);
                    html += `<option value="${value.value}">${translatedLabel}</option>`;
                }
            });
        } else if (typeof allowedValues === "object") {
            Object.entries(allowedValues).forEach(([key, value]) => {
                const translatedValue = this.translateValue(value);
                html += `<option value="${key}">${translatedValue}</option>`;
            });
        }

        html += "</select>";
        container.innerHTML = html;
    },

    renderDynamicInput(inputType, container) {
        let input;
        switch (inputType) {
            case "number":
                input =
                    '<input type="number" class="form-input" id="trait-value-input-viewer" placeholder="Inserisci valore numerico..." oninput="TraitsViewer.updatePreview()">';
                break;
            case "text":
            default:
                input =
                    '<input type="text" class="form-input" id="trait-value-input-viewer" placeholder="Inserisci valore..." oninput="TraitsViewer.updatePreview()">';
                break;
        }
        container.innerHTML = input;
    },

    renderTextInput(container) {
        container.innerHTML =
            '<input type="text" class="form-input" id="trait-value-input-viewer" placeholder="Inserisci valore..." oninput="TraitsViewer.updatePreview()">';
    },

    updatePreview() {
        console.log("TraitsViewer: Updating preview");

        // Get selected type
        const typeSelect = document.getElementById("trait-type-select-viewer");
        if (!typeSelect || !typeSelect.value) {
            this.validateForm();
            return;
        }

        const typeName =
            typeSelect.options[typeSelect.selectedIndex].textContent;

        // Get selected/entered value
        let value = "";
        const valueSelect = document.getElementById(
            "trait-value-select-viewer"
        );
        const valueInput = document.getElementById("trait-value-input-viewer");

        if (valueSelect) {
            value =
                valueSelect.options[valueSelect.selectedIndex]?.textContent ||
                "";
        } else if (valueInput) {
            value = valueInput.value;
        }

        // Update preview
        const preview = document.getElementById("trait-preview-viewer");
        if (preview && typeName && value) {
            const previewType = preview.querySelector(".preview-type");
            const previewValue = preview.querySelector(".preview-value");

            if (previewType) previewType.textContent = typeName;
            if (previewValue) previewValue.textContent = value;

            preview.style.display = "block";
        } else if (preview) {
            preview.style.display = "none";
        }

        // Validate form to enable/disable submit button
        this.validateForm();
    },

    validateForm() {
        const categorySelected = document.querySelector(
            "#category-selector-viewer .category-btn.active"
        );
        const typeSelect = document.getElementById("trait-type-select-viewer");
        const valueSelect = document.getElementById(
            "trait-value-select-viewer"
        );
        const valueInput = document.getElementById("trait-value-input-viewer");
        const confirmBtn = document.getElementById("confirm-trait-btn-viewer");

        let isValid = false;

        // Check if category is selected
        if (categorySelected && typeSelect && typeSelect.value) {
            // Check if value is provided
            if (valueSelect && valueSelect.value) {
                isValid = true;
            } else if (valueInput && valueInput.value.trim()) {
                isValid = true;
            }
        }

        console.log("TraitsViewer: Form validation:", {
            categorySelected: !!categorySelected,
            typeSelected: typeSelect ? !!typeSelect.value : false,
            valueProvided:
                (valueSelect && valueSelect.value) ||
                (valueInput && valueInput.value.trim()),
            isValid,
        });

        // Enable/disable confirm button
        if (confirmBtn) {
            confirmBtn.disabled = !isValid;
            if (isValid) {
                confirmBtn.classList.remove("disabled");
            } else {
                confirmBtn.classList.add("disabled");
            }
        }

        return isValid;
    },

    async addTrait() {
        console.log("TraitsViewer: Adding trait...");

        if (!this.validateForm()) {
            ToastManager.error(
                "Compila tutti i campi richiesti",
                "Validazione"
            );
            return;
        }

        // Recupera l'ID dell'EGI dal DOM
        const traitsContainer = document.querySelector("[data-egi-id]");
        const egiId = traitsContainer
            ? traitsContainer.getAttribute("data-egi-id")
            : null;

        if (!egiId) {
            console.error("TraitsViewer: EGI ID not found");
            ToastManager.error("ID EGI non trovato", "Errore");
            return;
        }

        // Collect form data
        const categoryBtn = document.querySelector(
            "#category-selector-viewer .category-btn.active"
        );
        const typeSelect = document.getElementById("trait-type-select-viewer");
        const valueSelect = document.getElementById(
            "trait-value-select-viewer"
        );
        const valueInput = document.getElementById("trait-value-input-viewer");

        const categoryId = categoryBtn.dataset.categoryId;
        const typeId = typeSelect.value;
        const value = valueSelect ? valueSelect.value : valueInput.value.trim();

        console.log("TraitsViewer: Trait data:", {
            egiId,
            categoryId,
            typeId,
            value,
        });

        try {
            const response = await fetch(`/egis/${egiId}/traits/add-single`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    trait_category_id: categoryId,
                    trait_type_id: typeId,
                    value: value,
                }),
            });

            const data = await response.json();
            console.log("TraitsViewer: Add trait response:", data);

            if (data.success) {
                ToastManager.success(
                    "Trait aggiunto con successo!",
                    "Successo"
                );
                this.closeModal();
                // Refresh the traits display
                location.reload(); // Simple reload for now
            } else {
                ToastManager.error(
                    data.message || "Errore durante l'aggiunta del trait",
                    "Errore"
                );
            }
        } catch (error) {
            console.error("TraitsViewer: Error adding trait:", error);
            ToastManager.error("Errore di rete durante l'aggiunta", "Errore");
        }
    },
};

// =============================================================================
// TRAIT IMAGE MANAGER (CLEAN VERSION)
// =============================================================================

class TraitImageManager {
    constructor() {
        console.log("TraitImageManager: Initializing clean version...");

        this.translations = window.traitTranslations || {};
        this.uploadInProgress = new Set();
        this.isInitialized = false;

        this.init();
    }

    init() {
        if (this.isInitialized) {
            console.log("TraitImageManager: Already initialized");
            return;
        }

        console.log("TraitImageManager: Setting up event listeners...");

        this.setupTraitCardListeners();
        this.setupImageUpload();
        this.setupImageDeletion();
        this.setupModalCloseEvents();
        this.setupDragAndDrop();

        this.isInitialized = true;
        console.log("TraitImageManager: Initialization complete");
    }

    setupTraitCardListeners() {
        console.log("TraitImageManager: Setting up trait card listeners...");

        // Single event delegation for all trait cards
        document.addEventListener(
            "click",
            (e) => {
                // Cerca il trait card più vicino
                const traitCard = e.target.closest("[data-trait-id]");

                // Verifica che non sia il pulsante rimuovi
                const isRemoveButton = e.target.closest(".trait-remove");

                // IMPORTANTE: Verifica che non sia dentro un modal di edit
                const isInsideModal = e.target.closest(
                    '.trait-modal, [id^="trait-modal-"]'
                );

                if (traitCard && !isRemoveButton && !isInsideModal) {
                    // console.log(
                    //     "TraitImageManager: Trait card element:",
                    //     traitCard
                    // );

                    // Controlla se l'utente può editare verificando se esiste il data-can-edit
                    const traitsViewer = traitCard.closest("[data-can-edit]");
                    const canEdit =
                        traitsViewer && traitsViewer.dataset.canEdit === "true";

                    if (canEdit) {
                        // Utente proprietario di EGI non pubblicato: apri modal di edit
                        e.preventDefault();
                        e.stopPropagation();
                        this.openImageModal(traitCard.dataset.traitId);
                        return;
                    } else {
                        // Utente non proprietario o EGI pubblicato: apri modal di visualizzazione
                        e.preventDefault();
                        e.stopPropagation();
                        this.openViewModal(traitCard.dataset.traitId);
                        return;
                    }
                }
            },
            { passive: false }
        );

        // Forza il setup delle carte esistenti con stili corretti
        this.setupCardStyles();
    }

    setupCardStyles() {
        // Funzione per applicare stili alle carte
        const applyCardStyles = () => {
            const traitCards = document.querySelectorAll("[data-trait-id]");
            // console.log(
            //     "TraitImageManager: Applying styles to",
            //     traitCards.length,
            //     "trait cards"
            // );

            traitCards.forEach((card) => {
                // Solo se non è il pulsante rimuovi
                if (!card.closest(".trait-remove")) {
                    card.style.cursor = "pointer";
                    card.style.pointerEvents = "auto";
                    card.style.position = "relative";
                    card.style.zIndex = "1";

                    // console.log('TraitImageManager: Styled card for trait:', card.dataset.traitId);
                }
            });
        };

        // Applica immediatamente
        applyCardStyles();

        // Applica con ritardi per catturare contenuto dinamico
        setTimeout(applyCardStyles, 100);
        setTimeout(applyCardStyles, 500);
        setTimeout(applyCardStyles, 1000);

        // Osserva le modifiche al DOM
        const observer = new MutationObserver((mutations) => {
            let hasNewCards = false;
            mutations.forEach((mutation) => {
                if (mutation.type === "childList") {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) {
                            // Element node
                            if (
                                node.hasAttribute &&
                                node.hasAttribute("data-trait-id")
                            ) {
                                hasNewCards = true;
                            } else if (
                                node.querySelector &&
                                node.querySelector("[data-trait-id]")
                            ) {
                                hasNewCards = true;
                            }
                        }
                    });
                }
            });

            if (hasNewCards) {
                console.log(
                    "TraitImageManager: New cards detected, applying styles..."
                );
                setTimeout(applyCardStyles, 10);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    openImageModal(traitId) {
        // Usa la nuova funzione globale window.openTraitModal{id}() se disponibile
        const openFunction = window[`openTraitModal${traitId}`];
        if (typeof openFunction === 'function') {
            openFunction();
        } else {
            // Fallback al metodo precedente se funzione globale non trovata
            const modal = document.querySelector(`#trait-modal-${traitId}`);
            if (modal) {
                modal.style.display = "block";
                modal.classList.remove("hidden");
            } else {
                console.error(
                    "TraitImageManager: Modal not found for trait:",
                    traitId
                );
                ToastManager.error("Modal not found for this trait");
            }
        }
    }

    openViewModal(traitId) {
        // Creiamo il modal dinamicamente
        this.createAndShowImageModal(traitId);
    }

    createAndShowImageModal(traitId) {
        // Rimuovi modal esistente se presente
        const existingModal = document.querySelector(
            `#trait-view-modal-${traitId}`
        );
        if (existingModal) {
            existingModal.remove();
        }

        // Trova il trait card per ottenere info
        const traitCard = document.querySelector(
            `[data-trait-id="${traitId}"]`
        );
        if (!traitCard) {
            console.error("Trait card not found");
            return;
        }

        // Cerca l'immagine del trait
        const traitImage = traitCard.querySelector("img");
        const imageUrl = traitImage ? traitImage.src : null;
        const traitName =
            traitCard.querySelector(".trait-type")?.textContent || "Trait";

        // Crea il modal HTML
        const modalHTML = `
            <div id="trait-view-modal-${traitId}"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75"
                 style="display: flex;">

                <div class="relative max-w-4xl max-h-[90vh] mx-4">
                    <!-- Pulsante chiudi -->
                    <button type="button"
                            class="absolute top-4 right-4 z-10 text-white text-3xl hover:text-gray-300"
                            style="background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
                            onclick="document.getElementById('trait-view-modal-${traitId}').remove()">
                        &times;
                    </button>

                    <!-- Contenuto -->
                    <div class="text-center">
                        ${
                            imageUrl
                                ? `<img src="${imageUrl}"
                                 alt="${traitName}"
                                 class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">`
                                : `<div class="bg-white rounded-lg p-8 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-2">${traitName}</h3>
                                    <p class="text-gray-500">Nessuna immagine disponibile</p>
                                </div>
                            </div>`
                        }

                        ${
                            imageUrl
                                ? `<div class="mt-4 text-center">
                                <div class="bg-black bg-opacity-50 rounded-lg px-4 py-2 inline-block">
                                    <h3 class="text-white text-lg font-semibold">${traitName}</h3>
                                </div>
                            </div>`
                                : ""
                        }
                    </div>
                </div>
            </div>
        `;

        // Aggiungi il modal al DOM
        document.body.insertAdjacentHTML("beforeend", modalHTML);

        // Aggiungi listener per chiudere con click sul backdrop
        const modal = document.getElementById(`trait-view-modal-${traitId}`);
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    setupImageUpload() {
        console.log("TraitImageManager: Setting up image upload...");

        // Single change listener for all file inputs
        document.addEventListener("change", (e) => {
            if (e.target.matches('input[name="trait_image"]')) {
                console.log("TraitImageManager: File selected for upload");
                this.handleImageUpload(e);
            }
        });
    }

    async handleImageUpload(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        if (!file) {
            console.log("TraitImageManager: No file selected");
            return;
        }

        const form = fileInput.closest("form");
        if (!form) {
            console.error("TraitImageManager: Form not found");
            ToastManager.error("Form not found");
            return;
        }

        const traitIdInput = form.querySelector('input[name="trait_id"]');
        if (!traitIdInput) {
            console.error("TraitImageManager: Trait ID not found");
            ToastManager.error("Trait ID not found");
            return;
        }

        const traitId = traitIdInput.value;
        console.log("TraitImageManager: Uploading file for trait:", traitId);

        // Validate file
        if (!this.validateFile(file)) {
            return;
        }

        // Show preview immediately
        this.previewFile(fileInput, traitId);

        // Prevent duplicate uploads
        if (this.uploadInProgress.has(traitId)) {
            console.log(
                "TraitImageManager: Upload already in progress for trait:",
                traitId
            );
            return;
        }

        this.uploadInProgress.add(traitId);

        const formData = new FormData();
        formData.append("trait_id", traitId);
        formData.append("trait_image", file);

        try {
            ToastManager.info("Caricamento in corso...", "Upload");

            const response = await fetch("/traits/image/upload", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            const data = await response.json();
            console.log("TraitImageManager: Upload response:", data);

            if (data.success) {
                ToastManager.success(
                    "Immagine caricata con successo!",
                    "Successo"
                );
                this.updateImageDisplay(
                    traitId,
                    data.image_url,
                    data.thumbnail_url
                );
            } else {
                ToastManager.error(
                    data.message || "Errore durante il caricamento",
                    "Errore"
                );
                // Reset preview on error
                this.clearPreview(traitId);
            }
        } catch (error) {
            console.error("TraitImageManager: Upload error:", error);
            ToastManager.error("Errore durante il caricamento", "Errore");
            this.clearPreview(traitId);
        } finally {
            this.uploadInProgress.delete(traitId);
            // Reset form
            form.reset();
        }
    }

    setupImageDeletion() {
        console.log(
            "TraitImageManager: Setting up image deletion listeners..."
        );

        document.addEventListener("click", (e) => {
            if (e.target.matches('button[id^="trait-delete-image-btn-"]')) {
                e.preventDefault();
                e.stopPropagation();

                // Estrai il trait ID dall'ID del pulsante
                const buttonId = e.target.id;
                const traitId = buttonId.replace("trait-delete-image-btn-", "");

                console.log(
                    "TraitImageManager: Delete button clicked for trait:",
                    traitId
                );
                this.handleImageDeletion(traitId);
            }
        });
    }

    async handleImageDeletion(traitId) {
        console.log(
            "TraitImageManager: Handling image deletion for trait:",
            traitId
        );

        // Conferma eliminazione con SweetAlert2
        let confirmed = false;

        if (window.Swal) {
            const result = await Swal.fire({
                title: "Conferma eliminazione",
                text: "Sei sicuro di voler eliminare questa immagine? L'azione non può essere annullata.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sì, elimina!",
                cancelButtonText: "Annulla",
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-secondary",
                },
                buttonsStyling: false,
            });

            confirmed = result.isConfirmed;
        } else {
            // Fallback per browsers senza SweetAlert2
            confirmed = confirm(
                this.translations.confirm_delete ||
                    "Are you sure you want to delete this image?"
            );
        }

        if (!confirmed) {
            console.log("TraitImageManager: Image deletion cancelled by user");
            return;
        }

        try {
            ToastManager.info("Eliminazione in corso...", "Elimina");

            const response = await fetch(`/traits/image/${traitId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            });

            const data = await response.json();
            console.log("TraitImageManager: Delete response:", data);

            if (data.success) {
                ToastManager.success(
                    "Immagine eliminata con successo!",
                    "Successo"
                );
                this.clearImageDisplay(traitId);

                // Mostra anche conferma con SweetAlert2 se disponibile
                if (window.Swal) {
                    Swal.fire({
                        title: "Eliminata!",
                        text: "L'immagine è stata eliminata con successo.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } else {
                ToastManager.error(
                    data.message || "Errore durante l'eliminazione",
                    "Errore"
                );

                if (window.Swal) {
                    Swal.fire({
                        title: "Errore!",
                        text:
                            data.message ||
                            "Si è verificato un errore durante l'eliminazione.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            }
        } catch (error) {
            console.error("TraitImageManager: Delete error:", error);
            ToastManager.error("Errore durante l'eliminazione", "Errore");

            if (window.Swal) {
                Swal.fire({
                    title: "Errore di rete!",
                    text: "Si è verificato un errore di connessione. Riprova più tardi.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            }
        }
    }

    setupModalCloseEvents() {
        // Handle modal close buttons (edit modal)
        document.addEventListener("click", (e) => {
            if (e.target.matches(".trait-modal-close")) {
                e.preventDefault();
                e.stopPropagation(); // FERMA LA PROPAGAZIONE!
                
                // Prova a usare la funzione globale closeTraitModal{id}() se disponibile
                const modal = e.target.closest(".trait-modal");
                if (modal) {
                    const traitId = modal.dataset.traitId;
                    const closeFunction = window[`closeTraitModal${traitId}`];
                    
                    if (typeof closeFunction === 'function') {
                        closeFunction();
                    } else {
                        // Fallback al metodo precedente
                        modal.style.display = "none";
                        modal.classList.add("hidden");
                        modal.classList.remove("flex");
                    }
                }
            }
        });

        // Handle view modal close buttons
        document.addEventListener("click", (e) => {
            if (e.target.matches(".trait-view-modal-close")) {
                e.preventDefault();
                e.stopPropagation(); // FERMA LA PROPAGAZIONE!
                const modal = e.target.closest('[id^="trait-view-modal-"]');
                if (modal) {
                    modal.style.display = "none";
                    modal.classList.add("hidden");
                    modal.classList.remove("flex");
                }
            }
        });

        // Handle backdrop clicks (edit modal)
        document.addEventListener("click", (e) => {
            if (e.target.matches(".trait-modal")) {
                e.preventDefault();
                e.stopPropagation(); // FERMA LA PROPAGAZIONE!
                e.target.style.display = "none";
                e.target.classList.add("hidden");
                e.target.classList.remove("flex");
            }
        });

        // Handle backdrop clicks (view modal)
        document.addEventListener("click", (e) => {
            if (e.target.matches('[id^="trait-view-modal-"]')) {
                e.preventDefault();
                e.stopPropagation(); // FERMA LA PROPAGAZIONE!
                e.target.style.display = "none";
                e.target.classList.add("hidden");
                e.target.classList.remove("flex");
            }
        });
    }

    setupDragAndDrop() {
        // Setup drag and drop on all trait upload areas
        document.addEventListener("dragover", (e) => {
            const uploadArea = e.target.closest(".trait-upload-area");
            if (uploadArea) {
                e.preventDefault();
                uploadArea.classList.add("dragover");
            }
        });

        document.addEventListener("dragleave", (e) => {
            const uploadArea = e.target.closest(".trait-upload-area");
            if (uploadArea) {
                uploadArea.classList.remove("dragover");
            }
        });

        document.addEventListener("drop", (e) => {
            const uploadArea = e.target.closest(".trait-upload-area");
            if (uploadArea) {
                e.preventDefault();
                uploadArea.classList.remove("dragover");

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const fileInput = uploadArea.querySelector(
                        'input[name="trait_image"]'
                    );
                    if (fileInput) {
                        fileInput.files = files;
                        // Trigger change event
                        const changeEvent = new Event("change", {
                            bubbles: true,
                        });
                        fileInput.dispatchEvent(changeEvent);
                    }
                }
            }
        });
    }

    validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/webp",
        ];

        if (file.size > maxSize) {
            ToastManager.error(
                "File troppo grande. Massimo 5MB.",
                "File Error"
            );
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            ToastManager.error(
                "Tipo di file non supportato. Usa JPG, PNG, GIF o WebP.",
                "File Error"
            );
            return false;
        }

        return true;
    }

    previewFile(input, traitId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const modal = document.querySelector(`#trait-modal-${traitId}`);
                if (modal) {
                    const previewContainer = modal.querySelector(
                        `#trait-image-preview-${traitId}`
                    );
                    if (previewContainer) {
                        // Clear existing content
                        previewContainer.innerHTML = "";

                        // Create image element
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.alt = "Preview";
                        img.className =
                            "object-contain h-auto max-w-full mx-auto rounded-lg max-h-64";

                        previewContainer.appendChild(img);
                        console.log(
                            "TraitImageManager: Preview updated for trait:",
                            traitId
                        );
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    }

    updateImageDisplay(traitId, imageUrl, thumbnailUrl) {
        console.log(
            "TraitImageManager: Updating image display for trait:",
            traitId,
            "with URL:",
            imageUrl
        );

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(
                `#trait-image-preview-${traitId}`
            );
            const deleteBtn = modal.querySelector(
                `#trait-delete-image-btn-${traitId}`
            );

            if (previewContainer) {
                // Clear and update with new image
                previewContainer.innerHTML = "";

                const img = document.createElement("img");
                img.src = imageUrl;
                img.alt = "Trait image";
                img.className =
                    "object-contain h-auto max-w-full mx-auto rounded-lg max-h-64";

                previewContainer.appendChild(img);
                console.log("TraitImageManager: Image display updated");
            }

            // Mostra il pulsante di eliminazione dopo caricamento riuscito
            if (deleteBtn) {
                deleteBtn.style.display = "inline-block";
                deleteBtn.classList.remove("hidden");
                console.log("TraitImageManager: Delete button shown");
            } else {
                // Se il pulsante non esiste, crealo dinamicamente
                console.log(
                    "TraitImageManager: Creating delete button dynamically"
                );
                const formDiv = modal.querySelector(".flex.space-x-2");
                if (formDiv) {
                    const newDeleteBtn = document.createElement("button");
                    newDeleteBtn.type = "button";
                    newDeleteBtn.id = `trait-delete-image-btn-${traitId}`;
                    newDeleteBtn.className =
                        "px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-md hover:bg-red-700";
                    newDeleteBtn.textContent = "Elimina Immagine";
                    formDiv.appendChild(newDeleteBtn);
                    console.log(
                        "TraitImageManager: Delete button created dynamically"
                    );
                }
            }
        } else {
            console.error(
                "TraitImageManager: Modal not found for trait:",
                traitId
            );
        }
    }
    clearImageDisplay(traitId) {
        console.log(
            "TraitImageManager: Clearing image display for trait:",
            traitId
        );

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(
                `#trait-image-preview-${traitId}`
            );
            const deleteBtn = modal.querySelector(
                `#trait-delete-image-btn-${traitId}`
            );

            if (previewContainer) {
                previewContainer.innerHTML = `
                    <div class="py-8 text-gray-500 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2">Nessuna immagine caricata</p>
                        <p class="mt-1 text-xs text-gray-400">Trascina e rilascia i file qui</p>
                        <p class="mt-1 text-xs text-gray-400">Supportati: JPG, PNG, WebP, GIF</p>
                    </div>
                `;
                console.log("TraitImageManager: Preview cleared");
            }

            // Nascondi o rimuovi il pulsante di eliminazione
            if (deleteBtn) {
                deleteBtn.style.display = "none";
                deleteBtn.classList.add("hidden");
                console.log("TraitImageManager: Delete button hidden");
            }
        } else {
            console.error(
                "TraitImageManager: Modal not found for trait:",
                traitId
            );
        }
    }

    clearPreview(traitId) {
        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(
                `#trait-image-preview-${traitId}`
            );
            if (previewContainer) {
                previewContainer.innerHTML = `
                    <div class="py-8 text-gray-500 text-center">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>Nessuna immagine caricata</p>
                    </div>
                `;
            }
        }
    }
}

// =============================================================================
// INITIALIZATION
// =============================================================================

// Prevent multiple initializations
if (!window.IntegratedTraitsSystemInitialized) {
    // Auto-initialize when DOM is ready
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Integrated Traits System: DOM loaded, initializing...");

        // Initialize TraitsViewer
        const container = document.querySelector('[id^="traits-viewer-"]');
        if (container) {
            const egiId = container.getAttribute("data-egi-id");
            if (egiId) {
                TraitsViewer.init(egiId);
            }
        }

        // Initialize TraitImageManager (single instance)
        if (!window.TraitImageManagerInstance) {
            window.TraitImageManagerInstance = new TraitImageManager();
            console.log("TraitImageManager initialized");
        }

        console.log("Integrated Traits System: Initialization complete");
    });

    // Mark as initialized
    window.IntegratedTraitsSystemInitialized = true;
} else {
    console.log("Integrated Traits System: Already initialized, skipping...");
}

// Export for global access
window.TraitsViewer = TraitsViewer;
window.TraitImageManager = TraitImageManager;
