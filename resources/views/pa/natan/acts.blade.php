{{--
    File: acts.blade.php
    Package: FlorenceEGI PA/Enterprise - N.A.T.A.N. Module
    Author: Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    Version: 1.0.0 (N.A.T.A.N. AI Document Intelligence)
    Date: 2025-10-09
    Purpose: Acts list with advanced filters, search, and export
--}}

<x-pa-layout title="Atti Analizzati - N.A.T.A.N.">
    <x-slot:breadcrumb>N.A.T.A.N. / Atti</x-slot:breadcrumb>
    <x-slot:pageTitle>Atti Amministrativi Analizzati</x-slot:pageTitle>

    <x-slot:styles>
        <style>
            .filters-panel {
                background: white;
                border-radius: 12px;
                padding: 24px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                margin-bottom: 24px;
            }

            .filters-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
                margin-top: 20px;
            }

            .form-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .form-label {
                font-size: 12px;
                font-weight: 600;
                color: #6B6B6B;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-control {
                padding: 10px 14px;
                border: 1px solid #E5E7EB;
                border-radius: 6px;
                font-size: 14px;
                transition: all 0.2s ease;
            }

            .form-control:focus {
                outline: none;
                border-color: #8E44AD;
                box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
            }

            .btn {
                padding: 10px 20px;
                border-radius: 6px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-primary {
                background: linear-gradient(135deg, #8E44AD 0%, #1B365D 100%);
                color: white;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(142, 68, 173, 0.3);
            }

            .btn-secondary {
                background: #F8F9FA;
                color: #6B6B6B;
                border: 1px solid #E5E7EB;
            }

            .btn-secondary:hover {
                background: #E5E7EB;
            }

            .table-container {
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
            }

            .data-table thead {
                background: linear-gradient(135deg, #1B365D 0%, #8E44AD 100%);
                color: white;
            }

            .data-table th {
                padding: 16px;
                text-align: left;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                cursor: pointer;
                user-select: none;
                white-space: nowrap;
            }

            .data-table th:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .data-table td {
                padding: 16px;
                border-bottom: 1px solid #E5E7EB;
                font-size: 14px;
                color: #383838;
            }

            .data-table tbody tr:hover {
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

            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                padding: 24px;
                background: #F8F9FA;
            }

            .page-btn {
                padding: 8px 14px;
                border: 1px solid #E5E7EB;
                background: white;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: #6B6B6B;
                transition: all 0.2s ease;
            }

            .page-btn:hover:not(:disabled) {
                background: #8E44AD;
                color: white;
                border-color: #8E44AD;
            }

            .page-btn.active {
                background: #1B365D;
                color: white;
                border-color: #1B365D;
            }

            .page-btn:disabled {
                opacity: 0.4;
                cursor: not-allowed;
            }

            .loading-spinner {
                display: inline-block;
                width: 40px;
                height: 40px;
                border: 4px solid #E5E7EB;
                border-top-color: #8E44AD;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    </x-slot:styles>

    <!-- Filters Panel -->
    <div class="filters-panel">
        <h3 style="font-size: 18px; font-weight: 700; color: #1B365D; margin-bottom: 4px;">
            Filtri di Ricerca
        </h3>
        <p style="font-size: 13px; color: #6B6B6B; margin-bottom: 20px;">
            Utilizza i filtri per trovare gli atti specifici
        </p>

        <form id="filtersForm" onsubmit="return false;">
            <div class="filters-grid">
                <!-- Search Query -->
                <div class="form-group">
                    <label class="form-label" for="filterSearch">Ricerca Testo</label>
                    <input type="text" id="filterSearch" class="form-control"
                        placeholder="Cerca nell'oggetto..." />
                </div>

                <!-- Tipo Atto -->
                <div class="form-group">
                    <label class="form-label" for="filterTipo">Tipo Atto</label>
                    <select id="filterTipo" class="form-control">
                        <option value="">Tutti i tipi</option>
                        @foreach ($filterOptions['tipi_atto'] as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ente -->
                <div class="form-group">
                    <label class="form-label" for="filterEnte">Ente</label>
                    <select id="filterEnte" class="form-control">
                        <option value="">Tutti gli enti</option>
                        @foreach ($filterOptions['enti'] as $ente)
                            <option value="{{ $ente }}">{{ $ente }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Direzione -->
                <div class="form-group">
                    <label class="form-label" for="filterDirezione">Direzione</label>
                    <select id="filterDirezione" class="form-control">
                        <option value="">Tutte le direzioni</option>
                        @foreach ($filterOptions['direzioni'] as $direzione)
                            <option value="{{ $direzione }}">{{ $direzione }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Data From -->
                <div class="form-group">
                    <label class="form-label" for="filterDataFrom">Data Da</label>
                    <input type="date" id="filterDataFrom" class="form-control" />
                </div>

                <!-- Data To -->
                <div class="form-group">
                    <label class="form-label" for="filterDataTo">Data A</label>
                    <input type="date" id="filterDataTo" class="form-control" />
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <span class="material-icons" style="font-size: 18px;">search</span>
                    Applica Filtri
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                    <span class="material-icons" style="font-size: 18px;">refresh</span>
                    Reset
                </button>
                <button type="button" class="btn btn-secondary" onclick="exportData()">
                    <span class="material-icons" style="font-size: 18px;">download</span>
                    Esporta CSV
                </button>
            </div>
        </form>
    </div>

    <!-- Acts Table -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1B365D;">Atti Amministrativi</h3>
                <p style="font-size: 13px; color: #6B6B6B; margin-top: 4px;" id="resultsCount">Caricamento...</p>
            </div>
            <div>
                <button type="button" class="btn btn-secondary" onclick="refreshTable()">
                    <span class="material-icons" style="font-size: 18px;">refresh</span>
                    Aggiorna
                </button>
            </div>
        </div>

        <div id="tableContainer">
            <div style="padding: 60px; text-align: center;">
                <div class="loading-spinner"></div>
                <p style="margin-top: 16px; color: #6B6B6B;">Caricamento atti...</p>
            </div>
        </div>

        <!-- Pagination will be injected here by JavaScript -->
        <div id="paginationContainer"></div>
    </div>

    <x-slot:scripts>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script type="module">
            // N.A.T.A.N. Acts Table Manager
            // Simple vanilla JS implementation - TypeScript version will be created separately

            let currentPage = 1;
            let currentFilters = {};
            let sortField = 'created_at';
            let sortDirection = 'desc';

            // Load acts on page load
            loadActs();

            async function loadActs() {
                const tableContainer = document.getElementById('tableContainer');
                const paginationContainer = document.getElementById('paginationContainer');
                const resultsCount = document.getElementById('resultsCount');

                try {
                    // Show loading
                    tableContainer.innerHTML = `
                        <div style="padding: 60px; text-align: center;">
                            <div class="loading-spinner"></div>
                            <p style="margin-top: 16px; color: #6B6B6B;">Caricamento atti...</p>
                        </div>
                    `;

                    // Build query params
                    const params = new URLSearchParams({
                        page: currentPage,
                        sort: sortField,
                        direction: sortDirection,
                        ...currentFilters
                    });

                    const response = await fetch(`/api/natan/acts?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load acts');
                    }

                    const result = await response.json();
                    renderTable(result.data);
                    renderPagination(result.meta);
                    updateResultsCount(result.meta);

                } catch (error) {
                    console.error('Load error:', error);
                    tableContainer.innerHTML = `
                        <div style="padding: 60px; text-align: center; color: #C13120;">
                            <span class="material-icons" style="font-size: 64px; opacity: 0.3;">error</span>
                            <p style="margin-top: 16px; font-weight: 600;">Errore caricamento atti</p>
                            <button onclick="loadActs()" class="btn btn-primary" style="margin-top: 16px;">
                                Riprova
                            </button>
                        </div>
                    `;
                }
            }

            function renderTable(acts) {
                const tableContainer = document.getElementById('tableContainer');

                if (acts.length === 0) {
                    tableContainer.innerHTML = `
                        <div style="padding: 60px; text-align: center; color: #6B6B6B;">
                            <span class="material-icons" style="font-size: 64px; opacity: 0.3;">search_off</span>
                            <p style="margin-top: 16px; font-weight: 600;">Nessun atto trovato</p>
                            <p style="font-size: 14px; margin-top: 8px;">Prova a modificare i filtri di ricerca</p>
                        </div>
                    `;
                    return;
                }

                const tableHtml = `
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th onclick="sortBy('tipo_atto')">
                                    Tipo Atto ${getSortIcon('tipo_atto')}
                                </th>
                                <th onclick="sortBy('numero_atto')">
                                    Numero ${getSortIcon('numero_atto')}
                                </th>
                                <th onclick="sortBy('data_atto')">
                                    Data ${getSortIcon('data_atto')}
                                </th>
                                <th>Oggetto</th>
                                <th>Direzione</th>
                                <th onclick="sortBy('importo')">
                                    Importo ${getSortIcon('importo')}
                                </th>
                                <th onclick="sortBy('created_at')">
                                    Processato ${getSortIcon('created_at')}
                                </th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${acts.map(act => renderRow(act)).join('')}
                        </tbody>
                    </table>
                `;

                tableContainer.innerHTML = tableHtml;
            }

            function renderRow(act) {
                const dataAtto = new Date(act.data_atto).toLocaleDateString('it-IT');
                const createdAt = new Date(act.created_at).toLocaleDateString('it-IT');
                const importo = act.importo ? `€ ${parseFloat(act.importo).toLocaleString('it-IT', {
                    minimumFractionDigits: 2
                })}` : '-';

                return `
                    <tr onclick="window.location='/pa/natan/acts/${act.id}'">
                        <td><strong>${escapeHtml(act.tipo_atto)}</strong></td>
                        <td>${act.numero_atto ? escapeHtml(act.numero_atto) : '-'}</td>
                        <td>${dataAtto}</td>
                        <td>
                            <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                ${escapeHtml(act.oggetto)}
                            </div>
                        </td>
                        <td>${act.direzione ? escapeHtml(act.direzione) : '-'}</td>
                        <td>${importo}</td>
                        <td style="font-size: 13px; color: #6B6B6B;">${createdAt}</td>
                        <td>
                            <a href="/pa/natan/acts/${act.id}" 
                                onclick="event.stopPropagation()"
                                class="text-blue-600 hover:text-blue-800 font-semibold">
                                Dettagli →
                            </a>
                        </td>
                    </tr>
                `;
            }

            function renderPagination(meta) {
                const container = document.getElementById('paginationContainer');
                if (!meta || meta.last_page <= 1) {
                    container.innerHTML = '';
                    return;
                }

                const { current_page, last_page } = meta;
                let buttons = '';

                // Previous button
                buttons += `
                    <button class="page-btn" ${current_page === 1 ? 'disabled' : ''} 
                        onclick="goToPage(${current_page - 1})">
                        ‹ Precedente
                    </button>
                `;

                // Page numbers
                const range = 2;
                for (let i = Math.max(1, current_page - range); i <= Math.min(last_page, current_page + range); i++) {
                    buttons += `
                        <button class="page-btn ${i === current_page ? 'active' : ''}" 
                            onclick="goToPage(${i})"
                            ${i === current_page ? 'disabled' : ''}>
                            ${i}
                        </button>
                    `;
                }

                // Next button
                buttons += `
                    <button class="page-btn" ${current_page === last_page ? 'disabled' : ''}
                        onclick="goToPage(${current_page + 1})">
                        Successiva ›
                    </button>
                `;

                container.innerHTML = `<div class="pagination">${buttons}</div>`;
            }

            function updateResultsCount(meta) {
                const resultsCount = document.getElementById('resultsCount');
                if (meta) {
                    resultsCount.textContent = `${meta.total} atti trovati`;
                }
            }

            window.goToPage = function (page) {
                currentPage = page;
                loadActs();
            };

            window.sortBy = function (field) {
                if (sortField === field) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortField = field;
                    sortDirection = 'asc';
                }
                currentPage = 1;
                loadActs();
            };

            function getSortIcon(field) {
                if (sortField !== field) return '';
                return sortDirection === 'asc' ?
                    '<span class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_upward</span>' :
                    '<span class="material-icons" style="font-size: 16px; vertical-align: middle;">arrow_downward</span>';
            }

            window.applyFilters = function () {
                currentFilters = {};

                const search = document.getElementById('filterSearch').value.trim();
                if (search.length >= 3) {
                    currentFilters.search = search;
                }

                const tipo = document.getElementById('filterTipo').value;
                if (tipo) currentFilters.tipo = tipo;

                const ente = document.getElementById('filterEnte').value;
                if (ente) currentFilters.ente = ente;

                const direzione = document.getElementById('filterDirezione').value;
                if (direzione) currentFilters.direzione = direzione;

                const dataFrom = document.getElementById('filterDataFrom').value;
                if (dataFrom) currentFilters.data_from = dataFrom;

                const dataTo = document.getElementById('filterDataTo').value;
                if (dataTo) currentFilters.data_to = dataTo;

                currentPage = 1;
                loadActs();
            };

            window.resetFilters = function () {
                document.getElementById('filtersForm').reset();
                currentFilters = {};
                currentPage = 1;
                loadActs();
            };

            window.refreshTable = function () {
                loadActs();
            };

            window.exportData = function () {
                // Build export URL with current filters
                const params = new URLSearchParams(currentFilters);
                alert('Funzione export CSV sarà implementata nella fase successiva');
                // window.location.href = `/pa/natan/export?${params}`;
            };

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Make loadActs available globally for refresh
            window.loadActs = loadActs;
        </script>
    </x-slot:scripts>
</x-pa-layout>


