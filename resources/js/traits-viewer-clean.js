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
        this.container = document.getElementById('toast-container-viewer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toast-container-viewer';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info', title = null, duration = 4000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const content = `
            <div class="toast-content">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <div class="toast-text">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
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
        return this.show(message, 'success', title);
    },

    error(message, title = null) {
        return this.show(message, 'error', title);
    },

    warning(message, title = null) {
        return this.show(message, 'warning', title);
    },

    info(message, title = null) {
        return this.show(message, 'info', title);
    }
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
        isInitialized: false
    },

    init(egiId, canEdit = false, categories = [], availableTypes = []) {
        if (this.state.isInitialized) {
            console.log('TraitsViewer: Already initialized');
            return;
        }

        console.log('TraitsViewer: Initializing with egiId:', egiId);

        this.state.egiId = egiId;
        this.state.canEdit = canEdit;
        this.state.categories = categories;
        this.state.availableTypes = availableTypes;
        this.state.container = document.querySelector(`#traits-viewer-${egiId}`);

        if (!this.state.container) {
            console.error('TraitsViewer: Container not found for egiId:', egiId);
            return;
        }

        this.setupEventListeners();
        this.state.isInitialized = true;

        console.log('TraitsViewer: Initialized successfully');
    },

    setupEventListeners() {
        console.log('TraitsViewer: Setting up event listeners');

        // Remove trait buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.trait-remove')) {
                e.preventDefault();
                e.stopPropagation();
                const traitId = e.target.dataset.traitId;
                if (traitId) {
                    this.removeTrait(traitId);
                }
            }
        });

        // Add trait button
        const addButton = this.state.container?.querySelector('.add-trait-btn');
        if (addButton) {
            addButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.openModal();
            });
        }

        console.log('TraitsViewer: Event listeners setup complete');
    },

    async removeTrait(traitId) {
        if (!confirm(window.TraitsTranslations.confirm_remove || 'Are you sure you want to remove this trait?')) {
            return;
        }

        console.log('TraitsViewer: Removing trait:', traitId);

        try {
            const response = await fetch(`/traits/${traitId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success(window.TraitsTranslations.remove_success, '🎯 Trait Rimosso');

                const traitCard = document.querySelector(`[data-trait-id="${traitId}"]`);
                if (traitCard) {
                    traitCard.style.transition = 'all 0.3s ease';
                    traitCard.style.opacity = '0';
                    traitCard.style.transform = 'scale(0.8)';

                    setTimeout(() => {
                        traitCard.remove();
                    }, 300);
                }

                const counter = document.querySelector('.traits-count');
                if (counter) {
                    const currentCount = parseInt(counter.textContent) || 0;
                    counter.textContent = Math.max(0, currentCount - 1);
                }
            } else {
                ToastManager.error(window.TraitsTranslations.remove_error + ': ' + (data.message || window.TraitsTranslations.unknown_error), '❌ Errore');
            }
        } catch (error) {
            console.error('TraitsViewer: Error removing trait:', error);
            ToastManager.error(window.TraitsTranslations.network_error, '🌐 Errore di Rete');
        }
    },

    async openModal() {
        console.log('TraitsViewer: Opening modal for adding traits');

        if (!this.state.canEdit) {
            ToastManager.warning(window.TraitsTranslations.creator_only_modify);
            return;
        }

        const modal = document.querySelector('#trait-modal-viewer');
        if (modal) {
            // Move modal to body to avoid parent positioning issues
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }

            // Show the modal with proper styling
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.zIndex = '99999';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.padding = '1rem';
            modal.style.visibility = 'visible';
            modal.style.opacity = '1';

            // Ensure modal content is visible
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
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

            // console.log('TraitsViewer: Modal opened successfully');
        } else {
            console.error('TraitsViewer: Modal not found');
        }
    }
};

// =============================================================================
// TRAIT IMAGE MANAGER (CLEAN VERSION)
// =============================================================================

class TraitImageManager {
    constructor() {
        // console.log('TraitImageManager: Initializing clean version...');

        this.translations = window.traitTranslations || {};
        this.uploadInProgress = new Set();
        this.isInitialized = false;

        this.init();
    }

    init() {
        if (this.isInitialized) {
            console.log('TraitImageManager: Already initialized');
            return;
        }

        console.log('TraitImageManager: Setting up event listeners...');

        this.setupTraitCardListeners();
        this.setupImageUpload();
        this.setupImageDeletion();
        this.setupModalCloseEvents();
        this.setupDragAndDrop();

        this.isInitialized = true;
        console.log('TraitImageManager: Initialization complete');
    }

    setupTraitCardListeners() {
        console.log('TraitImageManager: Setting up trait card listeners...');

        // Single event delegation for all trait cards
        document.addEventListener('click', (e) => {
            const traitCard = e.target.closest('[data-trait-id]:not(.trait-remove):not(.trait-remove *)');

            if (traitCard && !e.target.closest('.trait-remove')) {
                console.log('TraitImageManager: Trait card clicked:', traitCard.dataset.traitId);
                e.preventDefault();
                e.stopPropagation();
                this.openImageModal(traitCard.dataset.traitId);
            }
        }, { passive: false });
    }

    openImageModal(traitId) {
        console.log('TraitImageManager: Opening image modal for trait:', traitId);

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.display = 'flex';
            console.log('TraitImageManager: Modal opened successfully');
        } else {
            console.error('TraitImageManager: Modal not found for trait:', traitId);
            ToastManager.error('Modal not found for this trait');
        }
    }

    setupImageUpload() {
        console.log('TraitImageManager: Setting up image upload...');

        // Single change listener for all file inputs
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[name="trait_image"]')) {
                console.log('TraitImageManager: File selected for upload');
                this.handleImageUpload(e);
            }
        });
    }

    async handleImageUpload(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        if (!file) {
            console.log('TraitImageManager: No file selected');
            return;
        }

        const form = fileInput.closest('form');
        if (!form) {
            console.error('TraitImageManager: Form not found');
            ToastManager.error('Form not found');
            return;
        }

        const traitIdInput = form.querySelector('input[name="trait_id"]');
        if (!traitIdInput) {
            console.error('TraitImageManager: Trait ID not found');
            ToastManager.error('Trait ID not found');
            return;
        }

        const traitId = traitIdInput.value;
        console.log('TraitImageManager: Uploading file for trait:', traitId);

        // Validate file
        if (!this.validateFile(file)) {
            return;
        }

        // Show preview immediately
        this.previewFile(fileInput, traitId);

        // Prevent duplicate uploads
        if (this.uploadInProgress.has(traitId)) {
            console.log('TraitImageManager: Upload already in progress for trait:', traitId);
            return;
        }

        this.uploadInProgress.add(traitId);

        const formData = new FormData();
        formData.append('trait_id', traitId);
        formData.append('trait_image', file);

        try {
            ToastManager.info('Caricamento in corso...', 'Upload');

            const response = await fetch('/traits/image/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            console.log('TraitImageManager: Upload response:', data);

            if (data.success) {
                ToastManager.success('Immagine caricata con successo!', 'Successo');
                this.updateImageDisplay(traitId, data.image_url, data.thumbnail_url);
            } else {
                ToastManager.error(data.message || 'Errore durante il caricamento', 'Errore');
                // Reset preview on error
                this.clearPreview(traitId);
            }
        } catch (error) {
            console.error('TraitImageManager: Upload error:', error);
            ToastManager.error('Errore durante il caricamento', 'Errore');
            this.clearPreview(traitId);
        } finally {
            this.uploadInProgress.delete(traitId);
            // Reset form
            form.reset();
        }
    }

    setupImageDeletion() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('button[id^="trait-delete-image-btn-"]')) {
                e.preventDefault();
                const traitId = e.target.dataset.traitId;
                this.handleImageDeletion(traitId);
            }
        });
    }

    async handleImageDeletion(traitId) {
        if (!confirm(this.translations.confirm_delete || 'Are you sure you want to delete this image?')) {
            return;
        }

        try {
            const response = await fetch(`/traits/image/delete/${traitId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                ToastManager.success('Immagine eliminata con successo!', 'Successo');
                this.clearImageDisplay(traitId);
            } else {
                ToastManager.error(data.message || 'Errore durante l\'eliminazione', 'Errore');
            }
        } catch (error) {
            console.error('TraitImageManager: Delete error:', error);
            ToastManager.error('Errore durante l\'eliminazione', 'Errore');
        }
    }

    setupModalCloseEvents() {
        // Handle modal close buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.trait-modal-close')) {
                const modal = e.target.closest('.trait-modal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        });

        // Handle backdrop clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.trait-modal')) {
                e.target.style.display = 'none';
                e.target.classList.add('hidden');
                e.target.classList.remove('flex');
            }
        });
    }

    setupDragAndDrop() {
        // Setup drag and drop on all trait upload areas
        document.addEventListener('dragover', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            }
        });

        document.addEventListener('dragleave', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                uploadArea.classList.remove('dragover');
            }
        });

        document.addEventListener('drop', (e) => {
            const uploadArea = e.target.closest('.trait-upload-area');
            if (uploadArea) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const fileInput = uploadArea.querySelector('input[name="trait_image"]');
                    if (fileInput) {
                        fileInput.files = files;
                        // Trigger change event
                        const changeEvent = new Event('change', { bubbles: true });
                        fileInput.dispatchEvent(changeEvent);
                    }
                }
            }
        });
    }

    validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (file.size > maxSize) {
            ToastManager.error('File troppo grande. Massimo 5MB.', 'File Error');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            ToastManager.error('Tipo di file non supportato. Usa JPG, PNG, GIF o WebP.', 'File Error');
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
                    const previewContainer = modal.querySelector(`#trait-image-preview-${traitId}`);
                    if (previewContainer) {
                        // Clear existing content
                        previewContainer.innerHTML = '';

                        // Create image element
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Preview';
                        img.className = 'object-contain h-auto max-w-full mx-auto rounded-lg max-h-64';

                        previewContainer.appendChild(img);
                        console.log('TraitImageManager: Preview updated for trait:', traitId);
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    }

    updateImageDisplay(traitId, imageUrl, thumbnailUrl) {
        console.log('TraitImageManager: Updating image display for trait:', traitId, 'with URL:', imageUrl);

        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(`#trait-image-preview-${traitId}`);
            const deleteBtn = modal.querySelector(`#trait-delete-image-btn-${traitId}`);

            if (previewContainer) {
                // Clear and update with new image
                previewContainer.innerHTML = '';

                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = 'Trait image';
                img.className = 'object-contain h-auto max-w-full mx-auto rounded-lg max-h-64';

                previewContainer.appendChild(img);
                console.log('TraitImageManager: Image display updated');
            }

            if (deleteBtn) {
                deleteBtn.style.display = 'block';
                console.log('TraitImageManager: Delete button shown');
            }
        }
    }

    clearImageDisplay(traitId) {
        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(`#trait-image-preview-${traitId}`);
            const deleteBtn = modal.querySelector(`#trait-delete-image-btn-${traitId}`);

            if (previewContainer) {
                previewContainer.innerHTML = `
                    <div class="py-8 text-gray-500 text-center">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>Nessuna immagine caricata</p>
                    </div>
                `;
            }

            if (deleteBtn) {
                deleteBtn.style.display = 'none';
            }
        }
    }

    clearPreview(traitId) {
        const modal = document.querySelector(`#trait-modal-${traitId}`);
        if (modal) {
            const previewContainer = modal.querySelector(`#trait-image-preview-${traitId}`);
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

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Integrated Traits System: DOM loaded, initializing...');

    // Initialize TraitsViewer
    const container = document.querySelector('[id^="traits-viewer-"]');
    if (container) {
        const egiId = container.getAttribute('data-egi-id');
        if (egiId) {
            TraitsViewer.init(egiId);
        }
    }

    // Initialize TraitImageManager (single instance)
    if (!window.TraitImageManagerInstance) {
        window.TraitImageManagerInstance = new TraitImageManager();
        console.log('TraitImageManager initialized');
    }

    console.log('Integrated Traits System: Initialization complete');
});

// Export for global access
window.TraitsViewer = TraitsViewer;
window.TraitImageManager = TraitImageManager;
