{{--
    File: show.blade.php
    Package: FlorenceEGI PA/Enterprise - N.A.T.A.N. Module
    Author: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    Version: 1.0.0 (N.A.T.A.N. AI Document Intelligence)
    Date: 2025-10-09
    Purpose: Act detail page with full metadata and blockchain verification
--}}

<x-pa-layout title="{{ $act->tipo_atto }} - N.A.T.A.N.">
    <x-slot:breadcrumb>N.A.T.A.N. / Atti / {{ $act->numero_atto ?? $act->id }}</x-slot:breadcrumb>
    <x-slot:pageTitle>{{ $act->tipo_atto }}</x-slot:pageTitle>

    <x-slot:styles>
        <style>
            .detail-card {
                background: white;
                border-radius: 12px;
                padding: 32px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                margin-bottom: 24px;
            }

            .detail-header {
                border-bottom: 2px solid #E5E7EB;
                padding-bottom: 24px;
                margin-bottom: 24px;
            }

            .detail-title {
                font-size: 28px;
                font-weight: 700;
                color: #1B365D;
                margin-bottom: 12px;
            }

            .detail-subtitle {
                font-size: 16px;
                color: #6B6B6B;
            }

            .metadata-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 24px;
                margin-top: 24px;
            }

            .metadata-item {
                padding: 16px;
                background: #F8F9FA;
                border-radius: 8px;
                border-left: 3px solid #D4A574;
            }

            .metadata-label {
                font-size: 11px;
                font-weight: 700;
                color: #6B6B6B;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .metadata-value {
                font-size: 16px;
                font-weight: 600;
                color: #1B365D;
                word-break: break-word;
            }

            .blockchain-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: linear-gradient(135deg, #2D5016 0%, #1B365D 100%);
                color: white;
                border-radius: 8px;
                font-weight: 600;
                margin-top: 16px;
            }

            .json-viewer {
                background: #1E293B;
                color: #E2E8F0;
                border-radius: 8px;
                padding: 24px;
                font-family: 'JetBrains Mono', 'Fira Code', monospace;
                font-size: 13px;
                line-height: 1.6;
                overflow-x: auto;
                max-height: 600px;
                overflow-y: auto;
            }

            .collapsible {
                cursor: pointer;
                user-select: none;
                transition: all 0.2s ease;
            }

            .collapsible:hover {
                background: #F8F9FA;
            }

            .collapsible-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            .collapsible-content.active {
                max-height: 1000px;
            }

            .badge-list {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 12px;
            }

            .badge {
                padding: 6px 12px;
                background: #E0E7FF;
                color: #3730A3;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
            }
        </style>
    </x-slot:styles>

    <!-- Back Button -->
    <div style="margin-bottom: 24px;">
        <a href="{{ route('pa.natan.acts') }}"
            class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold">
            <span class="material-icons">arrow_back</span>
            Torna alla lista atti
        </a>
    </div>

    <!-- Header Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title">{{ $act->tipo_atto }}</div>
            <div class="detail-subtitle">
                @if ($act->numero_atto)
                    Numero: {{ $act->numero_atto }} •
                @endif
                Data: {{ $act->getFormattedData() }}
            </div>

            @if ($act->isCertified)
                <div class="blockchain-badge">
                    <span class="material-icons">verified</span>
                    Certificato su Blockchain Algorand
                    @if ($act->blockchain_tx)
                        <span style="font-size: 11px; opacity: 0.8;">(TX: {{ substr($act->blockchain_tx, 0, 12) }}...)</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Main Object -->
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 12px;">
                Oggetto
            </h3>
            <p style="font-size: 16px; line-height: 1.6; color: #383838;">
                {{ $act->oggetto }}
            </p>
        </div>

        <!-- Metadata Grid -->
        <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 16px;">
            Metadati Principali
        </h3>
        <div class="metadata-grid">
            @if ($act->ente)
                <div class="metadata-item">
                    <div class="metadata-label">Ente</div>
                    <div class="metadata-value">{{ $act->ente }}</div>
                </div>
            @endif

            @if ($act->direzione)
                <div class="metadata-item">
                    <div class="metadata-label">Direzione</div>
                    <div class="metadata-value">{{ $act->direzione }}</div>
                </div>
            @endif

            @if ($act->responsabile)
                <div class="metadata-item">
                    <div class="metadata-label">Responsabile</div>
                    <div class="metadata-value">{{ $act->responsabile }}</div>
                </div>
            @endif

            @if ($act->importo)
                <div class="metadata-item" style="border-left-color: #2D5016;">
                    <div class="metadata-label">Importo</div>
                    <div class="metadata-value">{{ $act->getFormattedImporto() }}</div>
                </div>
            @endif

            <div class="metadata-item" style="border-left-color: #8E44AD;">
                <div class="metadata-label">ID Documento</div>
                <div class="metadata-value" style="font-size: 12px; font-family: monospace;">
                    {{ substr($act->document_id, 0, 16) }}...
                </div>
            </div>

            <div class="metadata-item">
                <div class="metadata-label">Data Elaborazione</div>
                <div class="metadata-value">{{ $act->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Categories -->
        @if (!empty($act->categoria))
            <div style="margin-top: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 12px;">
                    Categorie
                </h3>
                <div class="badge-list">
                    @foreach ($act->categoria as $cat)
                        <span class="badge">{{ $cat }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Firmatari -->
        @if (!empty($act->firmatari))
            <div style="margin-top: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 12px;">
                    Firmatari
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($act->firmatari as $firmatario)
                        <li style="padding: 8px 0; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; gap: 8px;">
                            <span class="material-icons" style="color: #2D5016; font-size: 20px;">person</span>
                            <span style="font-size: 15px; color: #383838;">{{ $firmatario }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- AI Processing Info -->
    <div class="detail-card">
        <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 16px;">
            Informazioni Elaborazione AI
        </h3>
        <div class="metadata-grid">
            @if ($act->ai_tokens_used)
                <div class="metadata-item" style="border-left-color: #8E44AD;">
                    <div class="metadata-label">Token Utilizzati</div>
                    <div class="metadata-value">{{ number_format($act->ai_tokens_used) }}</div>
                </div>
            @endif

            @if ($act->ai_cost)
                <div class="metadata-item" style="border-left-color: #D4A574;">
                    <div class="metadata-label">Costo Elaborazione</div>
                    <div class="metadata-value">{{ $act->getFormattedAiCost() }}</div>
                </div>
            @endif

            <div class="metadata-item">
                <div class="metadata-label">Stato Elaborazione</div>
                <div class="metadata-value">
                    @if ($act->isCompleted())
                        <span style="color: #2D5016;">✓ Completato</span>
                    @elseif ($act->isPending())
                        <span style="color: #E67E22;">⏳ In Elaborazione</span>
                    @else
                        <span style="color: #C13120;">✗ Fallito</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JSON Metadata Viewer (Collapsible) -->
    <div class="detail-card">
        <div class="collapsible" onclick="toggleCollapsible('jsonViewer')">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1B365D;">
                    Metadati Completi (JSON)
                </h3>
                <span class="material-icons" id="jsonViewerIcon">expand_more</span>
            </div>
        </div>

        <div id="jsonViewer" class="collapsible-content">
            <div class="json-viewer" style="margin-top: 20px;">
                <pre style="margin: 0; white-space: pre-wrap; word-break: break-all;">{{ json_encode($act->metadata_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="detail-card">
        <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 16px;">
            Azioni
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
            @if ($act->verification_url)
                <a href="{{ $act->verification_url }}" target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    <span class="material-icons">open_in_new</span>
                    Verifica Pubblica
                </a>
            @endif

            <button onclick="copyToClipboard('{{ $act->document_id }}')"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                <span class="material-icons">content_copy</span>
                Copia Document ID
            </button>

            <button onclick="downloadJSON()"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                <span class="material-icons">download</span>
                Scarica JSON
            </button>

            @if ($act->blockchain_tx)
                <button onclick="copyToClipboard('{{ $act->blockchain_tx }}')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg font-semibold transition-colors">
                    <span class="material-icons">link</span>
                    Copia TX Blockchain
                </button>
            @endif
        </div>
    </div>

    <x-slot:scripts>
        <script>
            // Toggle collapsible sections
            window.toggleCollapsible = function(id) {
                const content = document.getElementById(id);
                const icon = document.getElementById(id + 'Icon');

                if (content.classList.contains('active')) {
                    content.classList.remove('active');
                    icon.textContent = 'expand_more';
                } else {
                    content.classList.add('active');
                    icon.textContent = 'expand_less';
                }
            };

            // Copy to clipboard
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Copiato negli appunti!', 'success');
                }).catch(err => {
                    console.error('Copy failed:', err);
                    showToast('Errore copia', 'error');
                });
            };

            // Download JSON
            window.downloadJSON = function() {
                const metadata = @json($act->metadata_json);
                const dataStr = JSON.stringify(metadata, null, 2);
                const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);

                const exportFileDefaultName = `natan_act_{{ $act->document_id }}.json`;

                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();

                showToast('Download JSON avviato', 'success');
            };

            // Toast notification
            function showToast(message, type) {
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
                } else {
                    toast.style.background = '#FEE2E2';
                    toast.style.color = '#991B1B';
                }

                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            const style = document.createElement('style');
            style.textContent = `
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


