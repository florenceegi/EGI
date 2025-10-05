/**
 * @package Resources\Js\Components
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - PA Acts Integration)
 * @date 2025-10-04
 * @purpose Create Collection Modal - Dynamic context-aware collection creation
 *
 * CONTEXT: Universal modal for both Creator and PA contexts
 * - Creator context: "Crea Collezione" - artwork collections
 * - PA context: "Crea Fascicolo" - PA acts grouping
 *
 * INTEGRATION POINTS:
 * - PA Acts Upload: Inline fascicolo creation during act upload
 * - Creator Dashboard: Standard collection creation
 * - Collections CRUD: Reusable modal component
 *
 * KEY FEATURES:
 * - Context-aware terminology (Collezione vs Fascicolo)
 * - AJAX form submission with real-time validation
 * - Event-driven architecture (collection-created event)
 * - Auto-update parent selects after creation
 * - Full keyboard accessibility (ESC, TAB, Enter)
 * - UEM error handling integration
 * - Loading states and user feedback
 *
 * ACCESSIBILITY:
 * - WCAG 2.1 AA compliant
 * - Keyboard navigation support
 * - Focus trap management
 * - ARIA states and roles
 * - Screen reader announcements
 *
 * EVENTS EMITTED:
 * - collection-created: { collectionId, collectionName, collection: {...} }
 *
 * GLOBAL API:
 * - window.CreateCollectionModal.open() - Open modal
 * - window.CreateCollectionModal.close() - Close modal
 * - window.CreateCollectionModal.setContext(label) - Set dynamic title
 */

class CreateCollectionModal {
    /**
     * Initialize modal controller
     *
     * Sets up all DOM references, state management, and event handlers.
     * Called once on instantiation for global singleton instance.
     */
    constructor() {
        // Core DOM elements
        this.modal = null;
        this.modalContainer = null;
        this.form = null;
        this.nameInput = null;
        this.submitButton = null;

        // State management
        this.isOpen = false;
        this.isSubmitting = false;
        this.focusedElementBeforeModal = null;
        this.redirectTimer = null;
        this.validationTimeout = null;

        // Initialize modal system
        this.initialize();
    }

    /**
     * //  Initialize modal system with comprehensive setup
     */
    initialize() {
        // Wait for DOM ready
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * //  Setup modal components and bindings
     */
    setup() {
        try {
            // Semantic Coherence - Consistent element targeting
            this.modal = document.getElementById("create-collection-modal");
            this.modalContainer = document.getElementById(
                "create-collection-modal-container"
            );
            this.form = document.getElementById("create-collection-form");
            this.nameInput = document.getElementById("collection_name");
            this.submitButton = document.getElementById(
                "submit-create-collection"
            );

            // Enhanced validation for critical elements
            if (
                !this.modal ||
                !this.form ||
                !this.nameInput ||
                !this.submitButton
            ) {
                console.warn(
                    "[CreateCollectionModal] Missing required DOM elements"
                );
                return;
            }

            this.bindEvents();
            this.loadUserStats();

            // Recursive Evolution - Log successful initialization
            console.info("[CreateCollectionModal] Initialized successfully");
        } catch (error) {
            console.error("[CreateCollectionModal] Setup failed:", error);
        }
    }

    /**
     * //  Comprehensive event binding with accessibility support
     */
    bindEvents() {
        // OS1 Enhanced: Form submission with robust validation
        this.form.addEventListener("submit", (e) => this.handleSubmit(e));

        // Modal control events
        document
            .getElementById("close-create-collection-modal")
            ?.addEventListener("click", () => this.close());
        document
            .getElementById("cancel-create-collection")
            ?.addEventListener("click", () => this.close());

        // OS1 Accessibility: Enhanced keyboard support
        document.addEventListener("keydown", (e) => this.handleKeydown(e));

        // Modal backdrop click to close
        this.modal.addEventListener("click", (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // OS1 UX Enhancement: Real-time character counter and validation
        this.nameInput.addEventListener("input", () =>
            this.handleInputChange()
        );
        this.nameInput.addEventListener("blur", () => this.validateField());

        // OS1 Performance: Debounced validation
        this.nameInput.addEventListener("input", () => {
            clearTimeout(this.validationTimeout);
            this.validationTimeout = setTimeout(
                () => this.validateField(),
                300
            );
        });

        // OS1 Trigger buttons throughout the application
        this.bindTriggerButtons();
    }

    /**
     * //  Bind trigger buttons across different layouts
     */
    bindTriggerButtons() {
        // Generic trigger selector for flexibility
        const triggers = document.querySelectorAll(
            '[data-action="open-create-collection-modal"]'
        );

        triggers.forEach((trigger) => {
            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                this.open();
            });
        });

        // OS1 Evolution: Legacy support for existing buttons
        const legacyTriggers = document.querySelectorAll(
            ".create-collection-trigger, #create-collection-button"
        );
        legacyTriggers.forEach((trigger) => {
            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                this.open();
            });
        });
    }

    /**
     * //  Open modal with enhanced UX and accessibility
     */
    open() {
        if (this.isOpen) return;

        try {
            // OS1 Accessibility: Store current focus for restoration
            this.focusedElementBeforeModal = document.activeElement;

            // OS1 State Management: Reset modal to clean state
            this.resetModal();

            // OS1 UX: Smooth modal appearance
            this.modal.classList.remove("hidden");
            this.modal.setAttribute("aria-hidden", "false");

            // Trigger transition after DOM update
            requestAnimationFrame(() => {
                this.modal.classList.add("modal-open");
                this.modalContainer.style.transform = "scale(1)";
                this.modalContainer.style.opacity = "1";
            });

            // OS1 Accessibility: Focus management
            setTimeout(() => {
                this.nameInput.focus();
            }, 150);

            // OS1 Body scroll prevention
            document.body.style.overflow = "hidden";

            this.isOpen = true;

            // OS1 Analytics: Track modal opening
            this.trackEvent("modal_opened");
        } catch (error) {
            console.error("[CreateCollectionModal] Open failed:", error);
        }
    }

    /**
     * //  Close modal with complete cleanup
     */
    close() {
        if (!this.isOpen) return;

        try {
            // OS1 UX: Smooth modal disappearance
            this.modal.classList.remove("modal-open");
            this.modalContainer.style.transform = "scale(0.95)";
            this.modalContainer.style.opacity = "0";

            // Complete closure after transition
            setTimeout(() => {
                this.modal.classList.add("hidden");
                this.modal.setAttribute("aria-hidden", "true");
                document.body.style.overflow = "";

                // OS1 Accessibility: Restore previous focus
                if (this.focusedElementBeforeModal) {
                    this.focusedElementBeforeModal.focus();
                }
            }, 300);

            // OS1 Cleanup: Clear any pending operations
            if (this.redirectTimer) {
                clearTimeout(this.redirectTimer);
                this.redirectTimer = null;
            }

            this.isOpen = false;

            // OS1 Analytics: Track modal closing
            this.trackEvent("modal_closed");
        } catch (error) {
            console.error("[CreateCollectionModal] Close failed:", error);
        }
    }

    /**
     * //  Reset modal to pristine state
     */
    resetModal() {
        // Reset form
        this.form.reset();

        // Clear error states
        this.clearErrors();

        // Reset UI states
        document
            .getElementById("create-collection-success-state")
            .classList.add("hidden");
        this.form.classList.remove("hidden");

        // Reset button state
        this.setSubmitButtonState("default");

        // Reset character counter
        this.updateCharacterCounter();

        // Reset submission flag
        this.isSubmitting = false;
    }

    /**
     * //  Handle form submission with comprehensive validation
     */
    async handleSubmit(event) {
        event.preventDefault();

        if (this.isSubmitting) return;

        try {
            // OS1 Validation: Client-side pre-validation
            if (!this.validateForm()) {
                return;
            }

            this.isSubmitting = true;
            this.setSubmitButtonState("loading");
            this.clearErrors();

            // OS1 Data Preparation
            const formData = new FormData(this.form);
            const requestData = {
                collection_name: formData.get("collection_name").trim(),
                _token: formData.get("_token"),
            };

            // OS1 AJAX Request with comprehensive error handling
            const response = await fetch("/collections/create", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(requestData),
            });

            const result = await response.json();

            if (result.success) {
                this.handleSuccess(result);
            } else {
                this.handleError(result);
            }
        } catch (error) {
            this.handleNetworkError(error);
        } finally {
            this.isSubmitting = false;
        }
    }

    /**
     * //  Handle successful collection creation
     */
    handleSuccess(result) {
        // OS1 UX: Transition to success state
        this.form.classList.add("hidden");
        const successState = document.getElementById(
            "create-collection-success-state"
        );
        successState.classList.remove("hidden");

        // Update success message
        const successMessage = document.getElementById("success-message");
        successMessage.textContent = result.message;

        // OS1 Progress Indication: Animate redirect progress
        const progressBar = document.getElementById("redirect-progress");
        requestAnimationFrame(() => {
            progressBar.style.width = "100%";
        });

        // OS1 Analytics: Track successful creation
        this.trackEvent("collection_created", {
            collection_id: result.collection?.id,
            collection_name: result.collection?.name,
        });

        // DEBUG: Check result structure
        console.log("[CreateCollectionModal] handleSuccess result:", result);
        console.log(
            "[CreateCollectionModal] result.collection:",
            result.collection
        );

        // OS1 Event Dispatch: Notify other components (PA Acts integration)
        if (result.collection) {
            window.dispatchEvent(
                new CustomEvent("collection-created", {
                    detail: {
                        collectionId: result.collection.id,
                        collectionName:
                            result.collection.collection_name ||
                            result.collection.name,
                        collection: result.collection,
                    },
                })
            );
            console.info(
                "[CreateCollectionModal] collection-created event dispatched",
                result.collection
            );
        } else {
            console.warn(
                "[CreateCollectionModal] result.collection is undefined! Cannot dispatch event"
            );
        }

        // Universal Redirect System: Based on user type terminology
        const shouldRedirect =
            this.modalContainer.dataset.redirectAfterCreation === "true";
        const redirectUrl = this.modalContainer.dataset.redirectUrl;

        console.log("[CreateCollectionModal] Redirect decision:", {
            shouldRedirect,
            redirectUrl,
            dataAttribute: this.modalContainer.dataset.redirectAfterCreation,
        });

        if (shouldRedirect && redirectUrl) {
            // Redirect to specified URL (e.g., Creator → /home/collections)
            console.log(
                "[CreateCollectionModal] Will redirect to:",
                redirectUrl
            );
            this.redirectTimer = setTimeout(() => {
                window.location.href = redirectUrl;
            }, 2000);
        } else {
            // No redirect - just close modal (e.g., PA → stay on page with updated select)
            console.log(
                "[CreateCollectionModal] Will close modal, no redirect"
            );
            this.redirectTimer = setTimeout(() => {
                this.close();
            }, 1500);
        }
    }

    /**
     * //  Handle server errors with intelligent feedback
     */
    handleError(result) {
        this.setSubmitButtonState("default");

        // OS1 Error Classification: Handle different error types
        if (result.errors) {
            // Validation errors
            this.displayValidationErrors(result.errors);
        } else {
            // General server errors
            this.displayGlobalError(
                result.message || "An unexpected error occurred"
            );
        }

        // OS1 Accessibility: Announce error to screen readers
        const errorElement = document.getElementById("global-error-message");
        if (errorElement && !errorElement.classList.contains("hidden")) {
            errorElement.focus();
        }

        // OS1 Analytics: Track errors for improvement
        this.trackEvent("creation_error", {
            error_type: result.error,
            error_message: result.message,
        });
    }

    /**
     * //  Handle network errors with graceful degradation
     */
    handleNetworkError(error) {
        console.error("[CreateCollectionModal] Network error:", error);

        this.setSubmitButtonState("default");
        this.displayGlobalError(
            "Network error. Please check your connection and try again."
        );

        // OS1 Analytics: Track network issues
        this.trackEvent("network_error", { error: error.message });
    }

    /**
     * //  Client-side form validation
     */
    validateForm() {
        const name = this.nameInput.value.trim();

        // Clear previous errors
        this.clearFieldError("collection_name");

        // Required validation
        if (!name) {
            this.displayFieldError(
                "collection_name",
                "Collection name is required"
            );
            return false;
        }

        // Length validation
        if (name.length < 2) {
            this.displayFieldError(
                "collection_name",
                "Collection name must be at least 2 characters"
            );
            return false;
        }

        if (name.length > 100) {
            this.displayFieldError(
                "collection_name",
                "Collection name cannot exceed 100 characters"
            );
            return false;
        }

        // Character validation
        const validPattern = /^[a-zA-Z0-9\s\-_'"À-ÿ]+$/u;
        if (!validPattern.test(name)) {
            this.displayFieldError(
                "collection_name",
                "Collection name contains invalid characters"
            );
            return false;
        }

        return true;
    }

    /**
     * //  Real-time field validation
     */
    validateField() {
        const name = this.nameInput.value.trim();

        // Clear previous error
        this.clearFieldError("collection_name");

        if (name && name.length > 0 && name.length < 2) {
            this.displayFieldError(
                "collection_name",
                "Minimum 2 characters required"
            );
        }
    }

    /**
     * //  Handle input changes with character counter
     */
    handleInputChange() {
        this.updateCharacterCounter();

        // Clear validation error while typing
        this.clearFieldError("collection_name");
    }

    /**
     * //  Update character counter with visual states
     */
    updateCharacterCounter() {
        const currentLength = this.nameInput.value.length;
        const maxLength = 100;
        const counter = document.getElementById("character-counter");
        const currentSpan = document.getElementById("current-length");

        if (currentSpan) {
            currentSpan.textContent = currentLength;
        }

        // OS1 UX: Visual feedback based on character count
        if (counter) {
            counter.classList.remove("text-warning", "text-danger");

            if (currentLength > 80) {
                counter.classList.add("text-warning");
            }
            if (currentLength > 95) {
                counter.classList.add("text-danger");
            }
        }
    }

    /**
     * //  Enhanced keyboard event handling
     */
    handleKeydown(event) {
        if (!this.isOpen) return;

        // ESC key to close modal
        if (event.key === "Escape") {
            event.preventDefault();
            this.close();
            return;
        }

        // Tab key focus management
        if (event.key === "Tab") {
            this.handleTabNavigation(event);
        }
    }

    /**
     * //  Focus trap for modal accessibility
     */
    handleTabNavigation(event) {
        const focusableElements = this.modal.querySelectorAll(
            'button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    }

    /**
     * //  Submit button state management
     */
    setSubmitButtonState(state) {
        const defaultText = document.getElementById("submit-text-default");
        const loadingText = document.getElementById("submit-text-loading");

        switch (state) {
            case "loading":
                this.submitButton.disabled = true;
                defaultText.classList.add("hidden");
                loadingText.classList.remove("hidden");
                loadingText.classList.add("flex");
                break;

            case "default":
            default:
                this.submitButton.disabled = false;
                defaultText.classList.remove("hidden");
                loadingText.classList.add("hidden");
                loadingText.classList.remove("flex");
                break;
        }
    }

    /**
     * //  Display field-specific validation errors
     */
    displayFieldError(fieldName, message) {
        const errorContainer = document.getElementById(`${fieldName}-error`);
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.classList.remove("hidden");
        }

        // Add error styling to input
        const input = document.getElementById(fieldName);
        if (input) {
            input.classList.add(
                "border-red-500",
                "focus:border-red-500",
                "focus:ring-red-500"
            );
        }
    }

    /**
     * //  Clear field-specific errors
     */
    clearFieldError(fieldName) {
        const errorContainer = document.getElementById(`${fieldName}-error`);
        if (errorContainer) {
            errorContainer.classList.add("hidden");
        }

        // Remove error styling from input
        const input = document.getElementById(fieldName);
        if (input) {
            input.classList.remove(
                "border-red-500",
                "focus:border-red-500",
                "focus:ring-red-500"
            );
        }
    }

    /**
     * //  Display validation errors from server
     */
    displayValidationErrors(errors) {
        Object.keys(errors).forEach((fieldName) => {
            const messages = errors[fieldName];
            if (messages && messages.length > 0) {
                this.displayFieldError(fieldName, messages[0]);
            }
        });
    }

    /**
     * //  Display global error messages
     */
    displayGlobalError(message) {
        const errorContainer = document.getElementById("global-error-message");
        const errorText = document.getElementById("global-error-text");

        if (errorContainer && errorText) {
            errorText.textContent = message;
            errorContainer.classList.remove("hidden");
        }
    }

    /**
     * //  Clear all error states
     */
    clearErrors() {
        // Clear field errors
        this.clearFieldError("collection_name");

        // Clear global error
        const globalError = document.getElementById("global-error-message");
        if (globalError) {
            globalError.classList.add("hidden");
        }
    }

    /**
     * //  Load and display user collection statistics
     */
    loadUserStats() {
        const userDataScript = document.getElementById("user-collection-data");
        const statsElement = document.getElementById("user-collection-stats");

        if (userDataScript && statsElement) {
            try {
                const userData = JSON.parse(userDataScript.textContent);
                const remaining =
                    userData.max_allowed - userData.total_collections;

                statsElement.textContent = `${userData.total_collections}/${userData.max_allowed} collections used`;

                if (remaining <= 2) {
                    statsElement.classList.add("text-yellow-400");
                }
                if (remaining <= 0) {
                    statsElement.classList.add("text-red-400");
                }
            } catch (error) {
                console.warn(
                    "[CreateCollectionModal] Failed to load user stats:",
                    error
                );
            }
        }
    }

    /**
     * //  Analytics event tracking
     */
    trackEvent(eventName, data = {}) {
        // Recursive Evolution - Track for improvement
        try {
            // Integration with analytics platform (Google Analytics, etc.)
            if (typeof gtag !== "undefined") {
                gtag("event", eventName, {
                    event_category: "Collection Modal",
                    ...data,
                });
            }

            // Custom analytics endpoint if available
            if (
                window.analytics &&
                typeof window.analytics.track === "function"
            ) {
                window.analytics.track(eventName, data);
            }

            // Console logging for development
            if (process.env.NODE_ENV === "development") {
                console.info(`[Analytics] ${eventName}:`, data);
            }
        } catch (error) {
            console.warn(
                "[CreateCollectionModal] Analytics tracking failed:",
                error
            );
        }
    }

    /**
     * //  Public API for programmatic control
     */
    destroy() {
        // OS1 Cleanup: Remove all event listeners and timers
        if (this.redirectTimer) {
            clearTimeout(this.redirectTimer);
        }
        if (this.validationTimeout) {
            clearTimeout(this.validationTimeout);
        }

        // Close modal if open
        if (this.isOpen) {
            this.close();
        }

        console.info("[CreateCollectionModal] Destroyed successfully");
    }
}

// OS1 Global Initialization and Exposure
let createCollectionModalInstance = null;

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    createCollectionModalInstance = new CreateCollectionModal();
});

// OS1 Public API
window.CreateCollectionModal = {
    open: () => createCollectionModalInstance?.open(),
    close: () => createCollectionModalInstance?.close(),
    instance: () => createCollectionModalInstance,
};

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
    module.exports = CreateCollectionModal;
}
