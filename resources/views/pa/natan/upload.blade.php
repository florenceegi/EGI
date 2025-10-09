{{--
    File: upload.blade.php
    Package: FlorenceEGI PA/Enterprise - N.A.T.A.N. Module
    Author: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    Version: 1.0.0 (N.A.T.A.N. AI Document Intelligence)
    Date: 2025-10-09
    Purpose: Document upload page with drag&drop and processing queue
--}}

<x-pa-layout title="Carica Documento - N.A.T.A.N.">
    <x-slot:breadcrumb>N.A.T.A.N. / Upload</x-slot:breadcrumb>
    <x-slot:pageTitle>Carica Documento per Analisi AI</x-slot:pageTitle>

    <x-slot:styles>
        <style>
            .upload-container {
                background: white;
                border-radius: 12px;
                padding: 40px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                margin-bottom: 32px;
            }

            .dropzone {
                border: 3px dashed #D4A574;
                border-radius: 12px;
                padding: 80px 40px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: linear-gradient(135deg, rgba(142, 68, 173, 0.02) 0%, rgba(27, 54, 93, 0.02) 100%);
            }

            .dropzone:hover,
            .dropzone.dragover {
                border-color: #8E44AD;
                background: linear-gradient(135deg, rgba(142, 68, 173, 0.08) 0%, rgba(27, 54, 93, 0.08) 100%);
                transform: scale(1.01);
            }

            .dropzone.uploading {
                border-color: #2D5016;
                background: rgba(45, 80, 22, 0.05);
                cursor: not-allowed;
            }

            .upload-icon {
                font-size: 80px;
                color: #D4A574;
                margin-bottom: 24px;
            }

            .upload-title {
                font-size: 24px;
                font-weight: 700;
                color: #1B365D;
                margin-bottom: 12px;
            }

            .upload-subtitle {
                font-size: 16px;
                color: #6B6B6B;
                margin-bottom: 24px;
            }

            .upload-restrictions {
                font-size: 13px;
                color: #6B6B6B;
                background: #F8F9FA;
                border-radius: 8px;
                padding: 16px;
                margin-top: 24px;
            }

            .upload-restrictions ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .upload-restrictions li {
                padding: 6px 0;
            }

            .upload-restrictions li::before {
                content: '✓';
                color: #2D5016;
                font-weight: bold;
                margin-right: 8px;
            }

            /* Processing Queue */
            .queue-container {
                background: white;
                border-radius: 12px;
                padding: 32px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .queue-item {
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 16px;
                transition: all 0.2s ease;
            }

            .queue-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            }

            .queue-item.processing {
                border-left: 4px solid #FEF3C7;
                background: linear-gradient(to right, #FEF3C7 0%, white 10%);
            }

            .queue-item.completed {
                border-left: 4px solid #D1FAE5;
                background: linear-gradient(to right, #D1FAE5 0%, white 10%);
            }

            .queue-item.failed {
                border-left: 4px solid #FEE2E2;
                background: linear-gradient(to right, #FEE2E2 0%, white 10%);
            }

            .progress-bar {
                width: 100%;
                height: 6px;
                background: #E5E7EB;
                border-radius: 3px;
                overflow: hidden;
                margin-top: 12px;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #8E44AD 0%, #1B365D 100%);
                border-radius: 3px;
                transition: width 0.3s ease;
                animation: progress-animation 1.5s ease-in-out infinite;
            }

            @keyframes progress-animation {
                0% {
                    opacity: 0.6;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    opacity: 0.6;
                }
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: linear-gradient(135deg, #8E44AD 0%, #1B365D 100%);
                color: white;
                border-radius: 8px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(142, 68, 173, 0.3);
            }

            .btn-primary:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }
        </style>
    </x-slot:styles>

    <!-- Upload Section -->
    <div class="upload-container">
        <div class="dropzone" id="uploadDropzone">
            <input type="file" id="fileInput" accept=".pdf,.p7m" style="display: none;" multiple>

            <div class="upload-icon">
                <span class="material-icons" style="font-size: 80px;">cloud_upload</span>
            </div>

            <h2 class="upload-title">Trascina i documenti qui</h2>
            <p class="upload-subtitle">
                oppure clicca per selezionare i file dal tuo computer
            </p>

            <button type="button" class="btn-primary" onclick="document.getElementById('fileInput').click()">
                <span class="material-icons">folder_open</span>
                Seleziona File
            </button>

            <div class="upload-restrictions">
                <ul>
                    <li><strong>Formati accettati:</strong> PDF, P7M (PDF firmato digitalmente)</li>
                    <li><strong>Dimensione massima:</strong> 10 MB per file</li>
                    <li><strong>Upload multiplo:</strong> Puoi caricare più documenti contemporaneamente</li>
                    <li><strong>Tempo elaborazione:</strong> Circa 30 secondi per documento</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Processing Queue -->
    <div class="queue-container">
        <h3 style="font-size: 20px; font-weight: 700; color: #1B365D; margin-bottom: 24px;">
            Coda di Elaborazione
        </h3>

        <div id="processingQueue">
            <div style="text-align: center; padding: 40px; color: #6B6B6B;">
                <span class="material-icons" style="font-size: 48px; opacity: 0.3;">hourglass_empty</span>
                <p style="margin-top: 16px; font-size: 14px;">Nessun documento in elaborazione</p>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script type="module">
            // N.A.T.A.N. Upload Manager
            // Simple vanilla JS implementation - TypeScript version will be created separately

            const dropzone = document.getElementById('uploadDropzone');
            const fileInput = document.getElementById('fileInput');
            const queueContainer = document.getElementById('processingQueue');
            const uploadQueue = [];

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Highlight dropzone
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('dragover');
                });
            });

            // Handle drop
            dropzone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                handleFiles(files);
            });

            // Handle file input change
            fileInput.addEventListener('change', () => {
                handleFiles(fileInput.files);
            });

            function handleFiles(files) {
                if (!files || files.length === 0) return;

                Array.from(files).forEach(file => {
                    if (validateFile(file)) {
                        uploadFile(file);
                    }
                });

                // Reset input
                fileInput.value = '';
            }

            function validateFile(file) {
                // Validate type
                const validTypes = ['application/pdf', 'application/pkcs7-mime'];
                const validExtensions = ['.pdf', '.p7m'];
                const isValid = validTypes.includes(file.type) ||
                    validExtensions.some(ext => file.name.toLowerCase().endsWith(ext));

                if (!isValid) {
                    showMessage(`File "${file.name}" non valido. Solo PDF o P7M.`, 'error');
                    return false;
                }

                // Validate size
                if (file.size > 10 * 1024 * 1024) {
                    showMessage(`File "${file.name}" troppo grande. Max 10MB.`, 'error');
                    return false;
                }

                return true;
            }

            async function uploadFile(file) {
                const jobId = crypto.randomUUID();

                // Add to queue UI
                addToQueue(jobId, file.name, 'uploading');

                try {
                    const formData = new FormData();
                    formData.append('file', file);

                    const response = await fetch('/api/natan/analyze', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Upload failed: ${response.status}`);
                    }

                    const result = await response.json();

                    // Update queue item
                    updateQueueItem(jobId, 'processing', result.job_id);

                    // Start polling
                    pollJobStatus(result.job_id, jobId);

                    showMessage(`Upload completato: ${file.name}. Elaborazione in corso...`, 'success');

                } catch (error) {
                    console.error('Upload error:', error);
                    updateQueueItem(jobId, 'failed');
                    showMessage(`Errore upload: ${file.name}`, 'error');
                }
            }

            function addToQueue(jobId, filename, status) {
                // Clear empty state
                if (queueContainer.querySelector('[style*="hourglass_empty"]')) {
                    queueContainer.innerHTML = '';
                }

                const queueItem = document.createElement('div');
                queueItem.className = `queue-item ${status}`;
                queueItem.id = `queue-${jobId}`;
                queueItem.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1B365D; margin-bottom: 4px;">
                                ${filename}
                            </div>
                            <div class="queue-status" style="font-size: 13px; color: #6B6B6B;">
                                ${getStatusText(status)}
                            </div>
                        </div>
                        <div class="queue-icon">
                            ${getStatusIcon(status)}
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${status === 'uploading' ? '30%' : '60%'};"></div>
                    </div>
                `;

                queueContainer.insertBefore(queueItem, queueContainer.firstChild);

                uploadQueue.push({
                    id: jobId,
                    filename,
                    status
                });
            }

            function updateQueueItem(jobId, status, serverJobId = null) {
                const item = document.getElementById(`queue-${jobId}`);
                if (!item) return;

                item.className = `queue-item ${status}`;

                const statusEl = item.querySelector('.queue-status');
                const iconEl = item.querySelector('.queue-icon');
                const progressFill = item.querySelector('.progress-fill');

                if (statusEl) statusEl.textContent = getStatusText(status);
                if (iconEl) iconEl.innerHTML = getStatusIcon(status);

                if (progressFill) {
                    if (status === 'processing') {
                        progressFill.style.width = '60%';
                    } else if (status === 'completed') {
                        progressFill.style.width = '100%';
                        progressFill.style.background = '#2D5016';
                        setTimeout(() => {
                            progressFill.parentElement.style.display = 'none';
                        }, 1000);
                    } else if (status === 'failed') {
                        progressFill.style.width = '100%';
                        progressFill.style.background = '#C13120';
                        setTimeout(() => {
                            progressFill.parentElement.style.display = 'none';
                        }, 1000);
                    }
                }

                // Update queue array
                const queueItem = uploadQueue.find(q => q.id === jobId);
                if (queueItem) {
                    queueItem.status = status;
                    if (serverJobId) queueItem.serverJobId = serverJobId;
                }
            }

            async function pollJobStatus(serverJobId, localJobId) {
                const maxAttempts = 60; // 60 * 2s = 2 min max
                let attempts = 0;

                const poll = async () => {
                    try {
                        const response = await fetch(`/api/natan/jobs/${serverJobId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Polling failed');
                        }

                        const result = await response.json();

                        if (result.status === 'completed') {
                            updateQueueItem(localJobId, 'completed');
                            showMessage('Documento processato con successo!', 'success');
                            
                            // Refresh page after 2 seconds to show new act
                            setTimeout(() => {
                                window.location.href = '{{ route('pa.natan.dashboard') }}';
                            }, 2000);
                            return;
                        }

                        if (result.status === 'failed') {
                            updateQueueItem(localJobId, 'failed');
                            showMessage('Elaborazione fallita. Riprova.', 'error');
                            return;
                        }

                        // Still processing
                        attempts++;
                        if (attempts < maxAttempts) {
                            setTimeout(poll, 2000);
                        } else {
                            updateQueueItem(localJobId, 'failed');
                            showMessage('Timeout: elaborazione troppo lunga.', 'error');
                        }

                    } catch (error) {
                        console.error('Polling error:', error);
                        updateQueueItem(localJobId, 'failed');
                    }
                };

                poll();
            }

            function getStatusText(status) {
                const texts = {
                    'uploading': 'Caricamento in corso...',
                    'processing': 'Analisi AI in corso...',
                    'completed': 'Completato con successo',
                    'failed': 'Elaborazione fallita'
                };
                return texts[status] || status;
            }

            function getStatusIcon(status) {
                const icons = {
                    'uploading': '<span class="material-icons" style="color: #E67E22; font-size: 28px;">cloud_upload</span>',
                    'processing': '<span class="material-icons" style="color: #8E44AD; font-size: 28px; animation: spin 2s linear infinite;">psychology</span>',
                    'completed': '<span class="material-icons" style="color: #2D5016; font-size: 28px;">check_circle</span>',
                    'failed': '<span class="material-icons" style="color: #C13120; font-size: 28px;">error</span>'
                };
                return icons[status] || '';
            }

            function showMessage(message, type) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 16px 24px;
                    border-radius: 8px;
                    font-weight: 600;
                    z-index: 9999;
                    animation: slideIn 0.3s ease;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                `;

                if (type === 'success') {
                    toast.style.background = '#D1FAE5';
                    toast.style.color = '#065F46';
                } else if (type === 'error') {
                    toast.style.background = '#FEE2E2';
                    toast.style.color = '#991B1B';
                } else {
                    toast.style.background = '#E0E7FF';
                    toast.style.color = '#3730A3';
                }

                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }

            // Add spin animation for processing icon
            const style = document.createElement('style');
            style.textContent = `
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateX(100px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                @keyframes slideOut {
                    from {
                        opacity: 1;
                        transform: translateX(0);
                    }
                    to {
                        opacity: 0;
                        transform: translateX(100px);
                    }
                }
            `;
            document.head.appendChild(style);
        </script>
    </x-slot:scripts>
</x-pa-layout>

