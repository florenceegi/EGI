{{--
    File: dashboard.blade.php
    Package: FlorenceEGI PA/Enterprise
    Author: Padmin D. Curtis (AI Partner OS3.0)
    Version: 2.0.0 (Enterprise Blade Component)
    Date: 2025-10-02
--}}

<x-pa-layout title="Dashboard PA">
    <x-slot:breadcrumb>Dashboard</x-slot:breadcrumb>
    <x-slot:pageTitle>Dashboard Ente Pubblico</x-slot:pageTitle>

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
            }

            .kpi-card.green {
                border-left-color: #2D5016;
            }

            .kpi-card.gold {
                border-left-color: #D4A574;
            }

            .kpi-card.grey {
                border-left-color: #6B6B6B;
            }

            .kpi-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .kpi-icon {
                width: 44px;
                height: 44px;
                background: rgba(27, 54, 93, 0.1);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: #1B365D;
            }

            .kpi-card.green .kpi-icon {
                background: rgba(45, 80, 22, 0.1);
                color: #2D5016;
            }

            .kpi-card.gold .kpi-icon {
                background: rgba(212, 165, 116, 0.1);
                color: #D4A574;
            }

            .kpi-card.grey .kpi-icon {
                background: rgba(107, 107, 107, 0.1);
                color: #6B6B6B;
            }

            .kpi-trend {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
            }

            .kpi-trend.positive {
                background: #D1FAE5;
                color: #065F46;
            }

            .kpi-trend.neutral {
                background: #E0E7FF;
                color: #3730A3;
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
                margin-bottom: 6px;
            }

            .kpi-desc {
                font-size: 12px;
                color: #6B6B6B;
            }

            /* Section */
            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .section-title {
                font-size: 18px;
                font-weight: 700;
                color: #1B365D;
            }

            .btn-primary {
                padding: 10px 20px;
                background: #1B365D;
                color: white;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-primary:hover {
                background: #2D5016;
            }

            /* Table */
            .table-container {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                margin-bottom: 32px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table thead {
                background: linear-gradient(135deg, #1B365D 0%, #0F2342 100%);
                color: white;
            }

            .table th {
                padding: 14px 16px;
                text-align: left;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .table td {
                padding: 14px 16px;
                border-bottom: 1px solid #E5E7EB;
                font-size: 14px;
            }

            .table tbody tr:hover {
                background: #F8F9FA;
            }

            .doc-title {
                font-weight: 600;
                color: #1B365D;
                margin-bottom: 4px;
            }

            .doc-meta {
                font-size: 12px;
                color: #6B6B6B;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 16px;
                font-size: 12px;
                font-weight: 600;
            }

            .badge-success {
                background: #D1FAE5;
                color: #065F46;
            }

            .badge-warning {
                background: #FEF3C7;
                color: #92400E;
            }

            .badge-grey {
                background: #F3F4F6;
                color: #6B6B6B;
            }

            .btn-view {
                padding: 8px 16px;
                background: #1B365D;
                color: white;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-view:hover {
                background: #2D5016;
            }

            /* Actions */
            .actions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            .action-card {
                background: white;
                border-radius: 10px;
                padding: 24px;
                text-align: center;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                transition: all 0.3s;
                text-decoration: none;
                display: block;
            }

            .action-card:hover {
                box-shadow: 0 4px 12px rgba(27, 54, 93, 0.15);
                transform: translateY(-2px);
            }

            .action-icon {
                width: 60px;
                height: 60px;
                margin: 0 auto 16px;
                background: linear-gradient(135deg, #1B365D, #2D5016);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                color: white;
            }

            .action-title {
                font-size: 16px;
                font-weight: 700;
                color: #1B365D;
                margin-bottom: 8px;
            }

            .action-desc {
                font-size: 13px;
                color: #6B6B6B;
                margin-bottom: 16px;
            }

            .action-link {
                color: #1B365D;
                font-weight: 600;
                font-size: 14px;
            }

            /* Empty State */
            .empty {
                text-align: center;
                padding: 60px 20px;
                color: #6B6B6B;
            }

            .empty i {
                font-size: 64px;
                opacity: 0.3;
                margin-bottom: 20px;
            }

            /* Responsive */
            @media (max-width: 968px) {
                .table-container {
                    overflow-x: auto;
                }

                .table {
                    min-width: 700px;
                }
            }

            @media (max-width: 640px) {
                .kpi-grid {
                    gap: 12px;
                    grid-template-columns: 1fr;
                }

                .kpi-card {
                    padding: 16px;
                    min-height: 120px;
                }

                .kpi-value {
                    font-size: 28px;
                }

                .section-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 12px;
                }

                .actions-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:styles>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Heritage Count --}}
        <x-pa.pa-stat-card
            title="Patrimonio Totale"
            :value="$stats['total_heritage'] ?? 0"
            icon="museum"
            trend="neutral"
            subtitle="Beni culturali catalogati"
            variant="default"
        />

        {{-- CoA Issued --}}
        <x-pa.pa-stat-card
            title="CoA Emessi"
            :value="$stats['coa_issued'] ?? 0"
            icon="verified"
            trend="up"
            trendValue="+12%"
            subtitle="Certificati di autenticità"
            variant="success"
        />

        {{-- Pending CoA --}}
        <x-pa.pa-stat-card
            title="CoA Pendenti"
            :value="$stats['coa_pending'] ?? 0"
            icon="pending"
            trend="neutral"
            subtitle="In attesa di approvazione"
            variant="warning"
        />

        {{-- Inspections --}}
        <x-pa.pa-stat-card
            title="Ispezioni Anno"
            :value="$stats['inspections_year'] ?? 0"
            icon="fact_check"
            trend="up"
            trendValue="+5%"
            subtitle="Verifiche effettuate"
            variant="secondary"
        />
    </div>

    {{-- OLD KPI GRID REMOVED - REPLACED WITH COMPONENTS ABOVE --}}
    @if(false)
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                <div class="kpi-trend positive"><i class="fas fa-arrow-up"></i> {{ $stats['growth_rate'] ?? '12%' }}</div>
            </div>
            <div class="kpi-label">Documenti Totali</div>
            <div class="kpi-value">{{ $stats['total_heritage'] ?? 0 }}</div>
            <div class="kpi-desc">Documenti certificati nel sistema</div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-certificate"></i></div>
                <div class="kpi-trend positive"><i class="fas fa-check-circle"></i> {{ $stats['cert_rate'] ?? '75%' }}
                </div>
            </div>
            <div class="kpi-label">CoA Emessi</div>
            <div class="kpi-value">{{ $stats['total_coa'] ?? 0 }}</div>
            <div class="kpi-desc">Certificati di Autenticità attivi</div>
        </div>

        <div class="kpi-card gold">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-user-shield"></i></div>
                <div class="kpi-trend neutral"><i class="fas fa-users"></i> Attivi</div>
            </div>
            <div class="kpi-label">Ispettori</div>
            <div class="kpi-value">{{ $stats['total_inspectors'] ?? 0 }}</div>
            <div class="kpi-desc">Ispettori assegnati alle collezioni</div>
        </div>

        <div class="kpi-card grey">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-trend neutral"><i class="fas fa-clock"></i> In corso</div>
            </div>
            <div class="kpi-label">Verifiche Pending</div>
            <div class="kpi-value">{{ $stats['pending_verifications'] ?? 0 }}</div>
            <div class="kpi-desc">Documenti in attesa di certificazione</div>
        </div>
    </div>
    @endif
    {{-- END OLD KPI GRID --}}

    <!-- Recent Documents -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-[#1B365D] flex items-center gap-2">
            <span class="material-symbols-outlined">history</span>
            Patrimonio Recente
        </h2>
        <x-pa.pa-action-button
            label="Vedi Tutti"
            route="pa.heritage.index"
            icon="arrow_forward"
            variant="secondary"
            size="md"
        />
    </div>

    @if ($recentHeritage && $recentHeritage->count() > 0)
        {{-- Heritage Grid with pa-heritage-card components --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($recentHeritage->take(6) as $item)
                <x-pa.pa-heritage-card
                    :egi="$item"
                    :showCoa="true"
                    layout="grid"
                    :showActions="true"
                />
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
            <p class="text-gray-600 mb-2">Nessun bene culturale catalogato</p>
            <p class="text-sm text-gray-500">Inizia creando la tua prima collezione di patrimonio</p>
        </div>
    @endif

    {{-- OLD TABLE VERSION (kept for reference, can be deleted) --}}
    @if(false)
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Data Creazione</th>
                        <th>Stato CoA</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentHeritage as $item)
                        <tr>
                            <td>
                                <div class="doc-title">{{ $item->title }}</div>
                                <div class="doc-meta">
                                    <i class="fas fa-folder"></i> {{ $item->collection->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="doc-meta">{{ $item->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @if ($item->activeCoa)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Certificato
                                    </span>
                                @elseif($item->coa)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> In Verifica
                                    </span>
                                @else
                                    <span class="badge-grey badge">
                                        <i class="fas fa-minus-circle"></i> Nessun CoA
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pa.heritage.show', $item->id) }}" class="btn-view">
                                    <i class="fas fa-eye"></i> Dettagli
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    {{-- END OLD TABLE VERSION --}}

    <!-- Quick Actions -->
    <div class="flex justify-between items-center mb-6 mt-12">
        <h2 class="text-2xl font-bold text-[#1B365D] flex items-center gap-2">
            <span class="material-symbols-outlined">bolt</span>
            Azioni Rapide
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Archivio Completo --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-[#1B365D] to-[#2D5016] rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-white">folder_open</span>
            </div>
            <h3 class="text-lg font-bold text-[#1B365D] mb-2">Archivio Completo</h3>
            <p class="text-sm text-gray-600 mb-4">Visualizza tutti i beni certificati del tuo ente</p>
            <x-pa.pa-action-button
                label="Apri Archivio"
                route="pa.heritage.index"
                icon="arrow_forward"
                variant="secondary"
                size="sm"
                fullWidth
            />
        </div>

        {{-- Nuovo Certificato --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-[#D4A574] to-[#E67E22] rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-white">add_circle</span>
            </div>
            <h3 class="text-lg font-bold text-[#1B365D] mb-2">Nuovo Certificato</h3>
            <p class="text-sm text-gray-600 mb-4">Richiedi un nuovo CoA per un bene culturale</p>
            <x-pa.pa-action-button
                label="Crea CoA"
                href="#"
                icon="add_circle"
                variant="primary"
                size="sm"
                fullWidth
            />
        </div>

        {{-- Assegna Ispettore --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-[#2D5016] to-[#1F3810] rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-white">person_add</span>
            </div>
            <h3 class="text-lg font-bold text-[#1B365D] mb-2">Assegna Ispettore</h3>
            <p class="text-sm text-gray-600 mb-4">Gestisci gli ispettori assegnati alle collezioni</p>
            <x-pa.pa-action-button
                label="Gestisci Ispettori"
                href="#"
                icon="manage_accounts"
                variant="success"
                size="sm"
                fullWidth
            />
        </div>
    </div>

    {{-- OLD ACTIONS GRID (kept for reference) --}}
    @if(false)
    <div class="actions-grid">
        <a href="{{ route('pa.heritage.index') }}" class="action-card">
            <div class="action-icon"><i class="fas fa-folder-open"></i></div>
            <div class="action-title">Archivio Completo</div>
            <div class="action-desc">Visualizza tutti i documenti certificati del tuo ente</div>
            <div class="action-link">Apri Archivio <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="#" class="action-card">
            <div class="action-icon"><i class="fas fa-award"></i></div>
            <div class="action-title">Certificati CoA</div>
            <div class="action-desc">Gestisci i Certificati di Autenticità emessi</div>
            <div class="action-link">Gestisci CoA <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="#" class="action-card">
            <div class="action-icon"><i class="fas fa-user-check"></i></div>
            <div class="action-title">Ispettori</div>
            <div class="action-desc">Visualizza e gestisci gli ispettori assegnati</div>
            <div class="action-link">Gestisci Ispettori <i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="#" class="action-card">
            <div class="action-icon"><i class="fas fa-question-circle"></i></div>
            <div class="action-title">Guida Sistema</div>
            <div class="action-desc">Documentazione e supporto per utilizzare FlorenceEGI</div>
            <div class="action-link">Apri Guida <i class="fas fa-arrow-right"></i></div>
        </a>
    </div>
    @endif
    {{-- END OLD ACTIONS GRID --}}

</x-pa-layout>
