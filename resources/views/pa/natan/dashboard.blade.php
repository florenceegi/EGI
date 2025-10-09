{{--
    File: dashboard.blade.php
    Package: FlorenceEGI PA/Enterprise - N.A.T.A.N. Module
    Author: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    Version: 1.0.0 (N.A.T.A.N. AI Document Intelligence)
    Date: 2025-10-09
    Purpose: N.A.T.A.N. Dashboard with AI statistics and document management
--}}

<x-pa-layout title="N.A.T.A.N. Dashboard">
    <x-slot:breadcrumb>N.A.T.A.N. / Dashboard</x-slot:breadcrumb>
    <x-slot:pageTitle>N.A.T.A.N. AI Document Intelligence</x-slot:pageTitle>

    <x-slot:styles>
        <style>
            /* KPI Grid */
            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 20px;
                margin-bottom: 32px;
            }

            .kpi-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                border-left: 4px solid #1B365D;
                min-height: 140px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .kpi-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            }

            .kpi-card.ai-primary {
                border-left-color: #8E44AD;
                /* Viola Innovazione */
            }

            .kpi-card.ai-success {
                border-left-color: #2D5016;
                /* Verde Rinascita */
            }

            .kpi-card.ai-gold {
                border-left-color: #D4A574;
                /* Oro Fiorentino */
            }

            .kpi-card.ai-info {
                border-left-color: #1B365D;
                /* Blu Algoritmo */
            }

            .kpi-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }

            .kpi-icon {
                width: 44px;
                height: 44px;
                background: rgba(142, 68, 173, 0.1);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: #8E44AD;
            }

            .kpi-card.ai-success .kpi-icon {
                background: rgba(45, 80, 22, 0.1);
                color: #2D5016;
            }

            .kpi-card.ai-gold .kpi-icon {
                background: rgba(212, 165, 116, 0.1);
                color: #D4A574;
            }

            .kpi-card.ai-info .kpi-icon {
                background: rgba(27, 54, 93, 0.1);
                color: #1B365D;
            }

            .kpi-label {
                font-size: 11px;
                color: #6B6B6B;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .kpi-value {
                font-size: 32px;
                font-weight: 700;
                color: #1B365D;
                line-height: 1;
            }

            .kpi-subtitle {
                font-size: 13px;
                color: #6B6B6B;
                margin-top: 8px;
            }

            /* Quick Upload Widget */
            .upload-widget {
                background: linear-gradient(135deg, #8E44AD 0%, #1B365D 100%);
                border-radius: 12px;
                padding: 32px;
                color: white;
                margin-bottom: 32px;
            }

            .upload-dropzone {
                border: 2px dashed rgba(255, 255, 255, 0.4);
                border-radius: 10px;
                padding: 40px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.05);
            }

            .upload-dropzone:hover,
            .upload-dropzone.dragover {
                border-color: #D4A574;
                background: rgba(212, 165, 116, 0.1);
                transform: scale(1.02);
            }

            .upload-icon {
                font-size: 48px;
                margin-bottom: 16px;
                opacity: 0.9;
            }

            /* Acts Table */
            .acts-table {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .acts-table table {
                width: 100%;
                border-collapse: collapse;
            }

            .acts-table thead {
                background: #F8F9FA;
            }

            .acts-table th {
                padding: 16px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #6B6B6B;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 2px solid #E5E7EB;
            }

            .acts-table td {
                padding: 16px;
                border-bottom: 1px solid #E5E7EB;
                font-size: 14px;
                color: #383838;
            }

            .acts-table tbody tr:hover {
                background: #F8F9FA;
                cursor: pointer;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
            }

            .status-badge.completed {
                background: #D1FAE5;
                color: #065F46;
            }

            .status-badge.pending {
                background: #FEF3C7;
                color: #92400E;
            }

            .status-badge.failed {
                background: #FEE2E2;
                color: #991B1B;
            }

            /* Chart Container */
            .chart-container {
                background: white;
                border-radius: 10px;
                padding: 24px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                margin-bottom: 32px;
            }

            .chart-header {
                font-size: 16px;
                font-weight: 700;
                color: #1B365D;
                margin-bottom: 20px;
            }
        </style>
    </x-slot:styles>

    <!-- KPI Cards Grid -->
    <div class="kpi-grid">
        <!-- Total Acts -->
        <div class="kpi-card ai-primary">
            <div class="kpi-header">
                <div>
                    <div class="kpi-label">Atti Processati</div>
                    <div class="kpi-value">{{ $stats['total_acts'] }}</div>
                    <div class="kpi-subtitle">Totale documenti analizzati</div>
                </div>
                <div class="kpi-icon">
                    <span class="material-icons">description</span>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="kpi-card ai-success">
            <div class="kpi-header">
                <div>
                    <div class="kpi-label">Questo Mese</div>
                    <div class="kpi-value">{{ $stats['acts_this_month'] }}</div>
                    <div class="kpi-subtitle">Atti analizzati questo mese</div>
                </div>
                <div class="kpi-icon">
                    <span class="material-icons">calendar_today</span>
                </div>
            </div>
        </div>

        <!-- AI Cost -->
        <div class="kpi-card ai-gold">
            <div class="kpi-header">
                <div>
                    <div class="kpi-label">Costo AI</div>
                    <div class="kpi-value" style="font-size: 24px;">€ {{ number_format($stats['total_ai_cost'], 2) }}</div>
                    <div class="kpi-subtitle">Investimento in AI</div>
                </div>
                <div class="kpi-icon">
                    <span class="material-icons">psychology</span>
                </div>
            </div>
        </div>

        <!-- Processing Time -->
        <div class="kpi-card ai-info">
            <div class="kpi-header">
                <div>
                    <div class="kpi-label">Tempo Medio</div>
                    <div class="kpi-value" style="font-size: 24px;">{{ $stats['avg_processing_time'] }}s</div>
                    <div class="kpi-subtitle">Tempo elaborazione medio</div>
                </div>
                <div class="kpi-icon">
                    <span class="material-icons">speed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Upload Widget -->
    <div class="upload-widget">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Carica Nuovo Documento</h3>
        <p style="font-size: 14px; opacity: 0.9; margin-bottom: 20px;">
            Trascina qui il PDF firmato oppure clicca per selezionare
        </p>

        <div class="upload-dropzone" id="quickUploadZone">
            <input type="file" id="quickFileInput" accept=".pdf,.p7m" style="display: none;">
            <div class="upload-icon">
                <span class="material-icons" style="font-size: 48px;">cloud_upload</span>
            </div>
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">
                Trascina PDF o P7M qui
            </div>
            <div style="font-size: 13px; opacity: 0.8;">
                oppure clicca per selezionare (max 10MB)
            </div>
        </div>

        <div style="margin-top: 16px; text-align: center;">
            <a href="{{ route('pa.natan.upload') }}"
                class="inline-block px-6 py-3 bg-white/20 hover:bg-white/30 rounded-lg font-semibold transition-colors">
                Vai alla pagina upload completa →
            </a>
        </div>
    </div>

    <!-- Processing Status -->
    <div class="chart-container">
        <h3 class="chart-header">Stato Elaborazione Oggi</h3>
        <div class="kpi-grid" style="gap: 16px;">
            <div style="text-align: center; padding: 16px; background: #D1FAE5; border-radius: 8px;">
                <div style="font-size: 28px; font-weight: 700; color: #065F46;">{{ $processingStats['completed_today'] }}</div>
                <div style="font-size: 12px; color: #065F46; margin-top: 4px;">Completati</div>
            </div>
            <div style="text-align: center; padding: 16px; background: #FEF3C7; border-radius: 8px;">
                <div style="font-size: 28px; font-weight: 700; color: #92400E;">{{ $processingStats['pending'] }}</div>
                <div style="font-size: 12px; color: #92400E; margin-top: 4px;">In Coda</div>
            </div>
            <div style="text-align: center; padding: 16px; background: #FEE2E2; border-radius: 8px;">
                <div style="font-size: 28px; font-weight: 700; color: #991B1B;">{{ $processingStats['failed_today'] }}</div>
                <div style="font-size: 12px; color: #991B1B; margin-top: 4px;">Falliti</div>
            </div>
        </div>
    </div>

    <!-- Acts Distribution Chart -->
    @if (!empty($stats['by_tipo']))
        <div class="chart-container">
            <h3 class="chart-header">Distribuzione per Tipo Atto</h3>
            <canvas id="actTypeChart" style="max-height: 300px;"></canvas>
        </div>
    @endif

    <!-- Recent Acts Table -->
    <div class="acts-table">
        <div style="padding: 20px; border-bottom: 1px solid #E5E7EB;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1B365D;">Atti Recenti</h3>
            <p style="font-size: 13px; color: #6B6B6B; margin-top: 4px;">Ultimi 10 documenti processati</p>
        </div>

        @if ($recentActs->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Tipo Atto</th>
                        <th>Numero</th>
                        <th>Data Atto</th>
                        <th>Oggetto</th>
                        <th>Importo</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentActs as $act)
                        <tr onclick="window.location='{{ route('pa.natan.acts.show', $act->id) }}'">
                            <td>
                                <strong>{{ $act->tipo_atto }}</strong>
                            </td>
                            <td>{{ $act->numero_atto ?? '-' }}</td>
                            <td>{{ $act->getFormattedData() }}</td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $act->oggetto }}
                                </div>
                            </td>
                            <td>{{ $act->getFormattedImporto() }}</td>
                            <td>
                                <span class="status-badge {{ $act->processing_status }}">
                                    @if ($act->isCompleted())
                                        ✓ Completato
                                    @elseif ($act->isPending())
                                        ⏳ Elaborazione
                                    @else
                                        ✗ Fallito
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('pa.natan.acts.show', $act->id) }}"
                                    onclick="event.stopPropagation()"
                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                    Dettagli →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 60px 20px; text-align: center; color: #6B6B6B;">
                <span class="material-icons" style="font-size: 64px; opacity: 0.3; margin-bottom: 16px;">inbox</span>
                <p style="font-size: 16px; font-weight: 600;">Nessun atto ancora processato</p>
                <p style="font-size: 14px; margin-top: 8px; opacity: 0.8;">
                    Carica il primo documento per iniziare
                </p>
                <a href="{{ route('pa.natan.upload') }}"
                    class="inline-block mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    Carica Documento
                </a>
            </div>
        @endif

        @if ($recentActs->count() > 0)
            <div style="padding: 16px; background: #F8F9FA; text-align: center;">
                <a href="{{ route('pa.natan.acts') }}"
                    class="text-blue-600 hover:text-blue-800 font-semibold">
                    Vedi tutti gli atti →
                </a>
            </div>
        @endif
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Act Type Distribution Chart
            @if (!empty($stats['by_tipo']))
                const actTypeCtx = document.getElementById('actTypeChart');
                if (actTypeCtx) {
                    new Chart(actTypeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode(array_keys($stats['by_tipo'])) !!},
                            datasets: [{
                                data: {!! json_encode(array_values($stats['by_tipo'])) !!},
                                backgroundColor: [
                                    '#8E44AD', // Viola Innovazione
                                    '#1B365D', // Blu Algoritmo
                                    '#2D5016', // Verde Rinascita
                                    '#D4A574', // Oro Fiorentino
                                    '#6B6B6B', // Grigio Pietra
                                ],
                                borderWidth: 0,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 16,
                                        font: {
                                            size: 13,
                                            family: "'Inter', sans-serif"
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif

            // Quick Upload Drag & Drop
            const dropzone = document.getElementById('quickUploadZone');
            const fileInput = document.getElementById('quickFileInput');

            if (dropzone && fileInput) {
                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Highlight drop zone
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => {
                        dropzone.classList.add('dragover');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => {
                        dropzone.classList.remove('dragover');
                    }, false);
                });

                // Handle drop
                dropzone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFile(files[0]);
                    }
                });

                // Handle click
                dropzone.addEventListener('click', () => {
                    fileInput.click();
                });

                fileInput.addEventListener('change', () => {
                    if (fileInput.files.length > 0) {
                        handleFile(fileInput.files[0]);
                    }
                });

                function handleFile(file) {
                    // Validate file type
                    const validTypes = ['application/pdf', 'application/pkcs7-mime'];
                    const validExtensions = ['.pdf', '.p7m'];
                    const isValid = validTypes.includes(file.type) ||
                        validExtensions.some(ext => file.name.toLowerCase().endsWith(ext));

                    if (!isValid) {
                        alert('Tipo file non valido. Solo PDF o P7M.');
                        return;
                    }

                    // Validate file size (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File troppo grande. Massimo 10MB.');
                        return;
                    }

                    // Redirect to full upload page (will handle actual upload)
                    window.location.href = '{{ route('pa.natan.upload') }}';
                }
            }
        </script>
    </x-slot:scripts>
</x-pa-layout>


