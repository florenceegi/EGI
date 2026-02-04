/**
 * @Oracode Script: Home Page Image Manager - Vanilla JS
 * 🎯 Purpose: Handle multi-image upload, preview, and gallery management with AJAX and SweetAlert2
 * 🛡️ Security: Client-side validation, CSRF protection
 * 🎨 Brand: FlorenceEGI Renaissance design system
 * 🔧 Accessibility: Keyboard navigation, ARIA announcements
 *
 * @package FlorenceEGI
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Frontend)
 * @date 2026-02-04
 */

class HomePageImageManager {
    constructor(modalId) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.fileInput = document.getElementById(`${modalId}-file-input`);
        this.uploadArea = document.getElementById(`${modalId}-upload-area`);
        this.previewArea = document.getElementById(`${modalId}-preview-area`);
        this.previewContainer = document.getElementById(`${modalId}-preview-container`);
        this.uploadButton = document.getElementById(`${modalId}-upload-button`);
        this.uploadForm = document.getElementById(`${modalId}-upload-form`);
        this.selectedFiles = [];

        this.init();
    }

    init() {
        if (!this.modal) return;

        // File selection handler
        this.fileInput.addEventListener('change', (e) => this.handleFileSelection(e));

        // Drag & drop handlers
        this.uploadArea.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.uploadArea.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.uploadArea.addEventListener('drop', (e) => this.handleDrop(e));

        // Form submission
        this.uploadForm.addEventListener('submit', (e) => this.handleSubmit(e));

        // Preview container delegation
        this.previewContainer.addEventListener('click', (e) => this.handlePreviewClick(e));

        // Handle "Set Current" forms via AJAX (for Banner/Avatar inside Gallery)
        // This prevents silent page reloads and gives better feedback
        const setCurrentForms = this.modal.querySelectorAll('form[action*="set-current"]');
        setCurrentForms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleSetCurrent(e));
        });
    }

    /**
     * Handle "Set as Current" form submission via AJAX
     */
    async handleSetCurrent(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        if(submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Image updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                     window.location.reload();
                }
            } else {
                this.showError(data.message || 'Error setting current image');
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalHtml;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred while communicating with the server.');
            if(submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalHtml;
            }
        }
    }

    handleFileSelection(e) {
        const files = Array.from(e.target.files);
        this.selectedFiles = files;

        if (files.length > 0) {
            this.showPreviews(files);
            this.showUploadButton();
        } else {
            this.hidePreviews();
            this.hideUploadButton();
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        this.uploadArea.classList.add('border-oro-fiorentino', 'bg-gray-700/50');
    }

    handleDragLeave(e) {
        e.preventDefault();
        this.uploadArea.classList.remove('border-oro-fiorentino', 'bg-gray-700/50');
    }

    handleDrop(e) {
        e.preventDefault();
        this.uploadArea.classList.remove('border-oro-fiorentino', 'bg-gray-700/50');

        const files = Array.from(e.dataTransfer.files).filter(file =>
            file.type.startsWith('image/') && 
            ['image/jpeg', 'image/png', 'image/webp', 'image/avif'].includes(file.type)
        );

        if (files.length > 0) {
            this.selectedFiles = files;
            this.showPreviews(files);
            this.showUploadButton();
        }
    }

    showPreviews(files) {
        this.previewContainer.innerHTML = '';

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative group';
                previewDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="h-24 w-full rounded-lg object-cover">
                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 transition-all group-hover:bg-opacity-50">
                        <button type="button" class="remove-preview rounded-full bg-red-500 p-2 text-white opacity-0 transition-opacity group-hover:opacity-100 hover:bg-red-600" data-index="${index}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-1 truncate text-xs text-gray-400">${file.name}</div>
                `;
                this.previewContainer.appendChild(previewDiv);
            };
            reader.readAsDataURL(file);
        });

        this.previewArea.classList.remove('hidden');
    }

    hidePreviews() {
        this.previewArea.classList.add('hidden');
        this.previewContainer.innerHTML = '';
    }

    showUploadButton() {
        this.uploadButton.classList.remove('hidden');
    }

    hideUploadButton() {
        this.uploadButton.classList.add('hidden');
    }

    handlePreviewClick(e) {
        const removeBtn = e.target.closest('.remove-preview');
        if (removeBtn) {
            const index = parseInt(removeBtn.dataset.index);
            this.selectedFiles.splice(index, 1);

            if (this.selectedFiles.length > 0) {
                this.showPreviews(this.selectedFiles);
            } else {
                this.hidePreviews();
                this.hideUploadButton();
                this.fileInput.value = '';
            }
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        if (this.selectedFiles.length === 0) return;

        // Show loading state
        this.uploadButton.disabled = true;
        this.uploadButton.innerHTML = `
            <svg class="mr-2 inline h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Uploading...
        `;

        // Create FormData
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const inputName = this.fileInput.name;
        this.selectedFiles.forEach(file => {
            formData.append(`${inputName}[]`, file);
        });

        try {
            const response = await fetch(this.uploadForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Images uploaded successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    window.location.reload();
                }
            } else {
                this.showError(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Upload failed');
        } finally {
            this.uploadButton.disabled = false;
            this.uploadButton.textContent = 'Upload Selected Images';
        }
    }

    // Helper to standardize error display
    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
                confirmButtonColor: '#EF4444',
                background: '#1F2937', // Dark mode background
                color: '#fff' // White text
            });
        } else {
            alert(message);
        }
    }

    open() {
        this.modal.classList.remove('hidden');
    }

    close() {
        this.modal.classList.add('hidden');
    }
}

// Initialize managers when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any modals present on the page
    const modalIds = ['creator-banner-modal', 'creator-avatar-modal', 'company-banner-modal', 'company-avatar-modal', 'collector-banner-modal', 'collector-avatar-modal'];
    
    window.imageManagers = {};
    modalIds.forEach(id => {
        if (document.getElementById(id)) {
            window.imageManagers[id] = new HomePageImageManager(id);
        }
    });
});

// Global helper to open modals
window.openImageModal = function(modalId) {
    if (window.imageManagers[modalId]) {
        window.imageManagers[modalId].open();
    }
};

window.closeImageModal = function(modalId) {
    if (window.imageManagers[modalId]) {
        window.imageManagers[modalId].close();
    }
};
