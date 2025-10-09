/**
 * N.A.T.A.N. Acts Table
 *
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Sortable, filterable table for EGI Acts with pagination
 */

import type { EgiAct, ActsFilter, SortConfig, PaginationMeta } from "./types";
import { NatanApiClient } from "./ApiClient";

/**
 * Acts Table Manager
 *
 * Handles acts table rendering, filtering, sorting, and pagination
 */
export class ActsTable {
    private container: HTMLElement;
    private api: NatanApiClient;
    private currentPage: number = 1;
    private filters: ActsFilter = {};
    private sortConfig: SortConfig = { field: "created_at", direction: "desc" };
    private perPage: number = 20;

    /**
     * Constructor
     *
     * @param containerSelector CSS selector for table container
     * @param api NatanApiClient instance
     */
    constructor(containerSelector: string, api: NatanApiClient) {
        const container =
            document.querySelector<HTMLElement>(containerSelector);

        if (!container) {
            throw new Error("Table container not found");
        }

        this.container = container;
        this.api = api;

        this.init();
    }

    /**
     * Initialize table
     */
    private async init(): Promise<void> {
        await this.loadActs();

        // Listen for new acts processed
        window.addEventListener("natan:act-processed", () => {
            this.refresh();
        });
    }

    /**
     * Load acts from API
     */
    private async loadActs(): Promise<void> {
        try {
            this.showLoading();

            const response = await this.api.getActs(
                this.filters,
                this.currentPage,
                this.perPage,
                this.sortConfig.field,
                this.sortConfig.direction
            );

            this.render(response.data, response.meta);
        } catch (error) {
            const message =
                error instanceof Error
                    ? error.message
                    : "Errore caricamento atti";
            this.showError(message);
        }
    }

    /**
     * Render table
     *
     * @param acts Acts array
     * @param meta Pagination metadata
     */
    private render(acts: EgiAct[], meta?: PaginationMeta): void {
        if (acts.length === 0) {
            this.showEmpty();
            return;
        }

        const tableHtml = `
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th data-sort="tipo_atto">
                                Tipo Atto ${this.getSortIcon("tipo_atto")}
                            </th>
                            <th data-sort="numero_atto">
                                Numero ${this.getSortIcon("numero_atto")}
                            </th>
                            <th data-sort="data_atto">
                                Data ${this.getSortIcon("data_atto")}
                            </th>
                            <th>Oggetto</th>
                            <th>Direzione</th>
                            <th data-sort="importo">
                                Importo ${this.getSortIcon("importo")}
                            </th>
                            <th data-sort="created_at">
                                Processato ${this.getSortIcon("created_at")}
                            </th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${acts.map((act) => this.renderRow(act)).join("")}
                    </tbody>
                </table>
            </div>
        `;

        this.container.innerHTML = tableHtml;

        // Render pagination if exists
        if (meta) {
            const paginationContainer = document.getElementById(
                "paginationContainer"
            );
            if (paginationContainer) {
                paginationContainer.innerHTML = this.renderPagination(meta);
            }

            // Update results count
            this.updateResultsCount(meta);
        }

        this.attachEventListeners();
    }

    /**
     * Render single row
     *
     * @param act EgiAct object
     * @returns HTML string
     */
    private renderRow(act: EgiAct): string {
        const dataAtto = new Date(act.data_atto).toLocaleDateString("it-IT");
        const createdAt = new Date(act.created_at).toLocaleDateString("it-IT");
        const importo = act.importo
            ? `€ ${parseFloat(String(act.importo)).toLocaleString("it-IT", {
                  minimumFractionDigits: 2,
              })}`
            : "-";

        return `
            <tr data-act-id="${act.id}">
                <td><strong>${this.escapeHtml(act.tipo_atto)}</strong></td>
                <td>${
                    act.numero_atto ? this.escapeHtml(act.numero_atto) : "-"
                }</td>
                <td>${dataAtto}</td>
                <td>
                    <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${this.escapeHtml(act.oggetto)}
                    </div>
                </td>
                <td>${act.direzione ? this.escapeHtml(act.direzione) : "-"}</td>
                <td>${importo}</td>
                <td style="font-size: 13px; color: #6B6B6B;">${createdAt}</td>
                <td>
                    <button class="text-blue-600 hover:text-blue-800 font-semibold" 
                        data-action="view" data-act-id="${act.id}">
                        Dettagli →
                    </button>
                </td>
            </tr>
        `;
    }

    /**
     * Render pagination
     *
     * @param meta Pagination metadata
     * @returns HTML string
     */
    private renderPagination(meta: PaginationMeta): string {
        const { current_page, last_page } = meta;

        let buttons = "";

        // Previous button
        buttons += `
            <button class="page-btn" ${current_page === 1 ? "disabled" : ""}
                data-page="${current_page - 1}">
                ‹ Precedente
            </button>
        `;

        // Page numbers
        const range = 2;
        for (
            let i = Math.max(1, current_page - range);
            i <= Math.min(last_page, current_page + range);
            i++
        ) {
            const isActive = i === current_page;
            buttons += `
                <button class="page-btn ${isActive ? "active" : ""}"
                    data-page="${i}"
                    ${isActive ? "disabled" : ""}>
                    ${i}
                </button>
            `;
        }

        // Next button
        buttons += `
            <button class="page-btn" ${
                current_page === last_page ? "disabled" : ""
            }
                data-page="${current_page + 1}">
                Successiva ›
            </button>
        `;

        return `<div class="pagination">${buttons}</div>`;
    }

    /**
     * Attach event listeners
     */
    private attachEventListeners(): void {
        // Row click - navigate to detail
        this.container
            .querySelectorAll<HTMLTableRowElement>("tr[data-act-id]")
            .forEach((row) => {
                row.addEventListener("click", (e) => {
                    const target = e.target as HTMLElement;
                    if (target.tagName !== "BUTTON") {
                        const actId = row.dataset.actId;
                        if (actId) {
                            window.location.href = `/pa/natan/acts/${actId}`;
                        }
                    }
                });
            });

        // Action buttons
        this.container
            .querySelectorAll<HTMLButtonElement>("button[data-action]")
            .forEach((btn) => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const action = btn.dataset.action;
                    const actId = btn.dataset.actId;

                    if (action === "view" && actId) {
                        window.location.href = `/pa/natan/acts/${actId}`;
                    }
                });
            });

        // Sort headers
        this.container
            .querySelectorAll<HTMLTableCellElement>("th[data-sort]")
            .forEach((th) => {
                th.addEventListener("click", () => {
                    const sortField = th.dataset.sort;
                    if (sortField) {
                        this.sortBy(sortField);
                    }
                });
            });

        // Pagination buttons
        const paginationContainer = document.getElementById(
            "paginationContainer"
        );
        if (paginationContainer) {
            paginationContainer
                .querySelectorAll<HTMLButtonElement>("button[data-page]")
                .forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const page = btn.dataset.page;
                        if (page) {
                            this.goToPage(parseInt(page));
                        }
                    });
                });
        }
    }

    /**
     * Sort by field
     *
     * @param field Field to sort by
     */
    private sortBy(field: string): void {
        if (this.sortConfig.field === field) {
            // Toggle direction
            this.sortConfig.direction =
                this.sortConfig.direction === "asc" ? "desc" : "asc";
        } else {
            // New field, default to asc
            this.sortConfig.field = field;
            this.sortConfig.direction = "asc";
        }

        this.currentPage = 1;
        this.loadActs();
    }

    /**
     * Go to page
     *
     * @param page Page number
     */
    private goToPage(page: number): void {
        this.currentPage = page;
        this.loadActs();
    }

    /**
     * Get sort icon for field
     *
     * @param field Field name
     * @returns HTML icon string
     */
    private getSortIcon(field: string): string {
        if (this.sortConfig.field !== field) return "";

        const icon =
            this.sortConfig.direction === "asc"
                ? "arrow_upward"
                : "arrow_downward";
        return `<span class="material-icons" style="font-size: 16px; vertical-align: middle;">${icon}</span>`;
    }

    /**
     * Apply filters
     *
     * @param filters Filter criteria
     */
    public applyFilters(filters: ActsFilter): void {
        this.filters = filters;
        this.currentPage = 1;
        this.loadActs();
    }

    /**
     * Reset filters
     */
    public resetFilters(): void {
        this.filters = {};
        this.currentPage = 1;
        this.loadActs();
    }

    /**
     * Refresh table
     */
    public refresh(): void {
        this.loadActs();
    }

    /**
     * Show loading state
     */
    private showLoading(): void {
        this.container.innerHTML = `
            <div style="padding: 60px; text-align: center;">
                <div class="loading-spinner"></div>
                <p style="margin-top: 16px; color: #6B6B6B;">Caricamento atti...</p>
            </div>
        `;
    }

    /**
     * Show error message
     *
     * @param message Error message
     */
    private showError(message: string): void {
        this.container.innerHTML = `
            <div style="padding: 60px; text-align: center; color: #C13120;">
                <span class="material-icons" style="font-size: 64px; opacity: 0.3;">error</span>
                <p style="margin-top: 16px; font-weight: 600;">Errore</p>
                <p style="font-size: 14px; color: #6B6B6B; margin-top: 8px;">${this.escapeHtml(
                    message
                )}</p>
                <button onclick="window.location.reload()" 
                    style="margin-top: 16px; padding: 10px 20px; background: #1B365D; color: white; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Ricarica Pagina
                </button>
            </div>
        `;
    }

    /**
     * Show empty state
     */
    private showEmpty(): void {
        this.container.innerHTML = `
            <div style="padding: 60px; text-align: center; color: #6B6B6B;">
                <span class="material-icons" style="font-size: 64px; opacity: 0.3;">search_off</span>
                <p style="margin-top: 16px; font-weight: 600;">Nessun atto trovato</p>
                <p style="font-size: 14px; margin-top: 8px;">Prova a modificare i filtri di ricerca</p>
            </div>
        `;

        // Clear pagination
        const paginationContainer = document.getElementById(
            "paginationContainer"
        );
        if (paginationContainer) {
            paginationContainer.innerHTML = "";
        }

        this.updateResultsCount();
    }

    /**
     * Update results count display
     *
     * @param meta Pagination metadata
     */
    private updateResultsCount(meta?: PaginationMeta): void {
        const resultsCount = document.getElementById("resultsCount");
        if (resultsCount) {
            resultsCount.textContent = meta
                ? `${meta.total} atti trovati`
                : "0 atti trovati";
        }
    }

    /**
     * Escape HTML to prevent XSS
     *
     * @param text Text to escape
     * @returns Escaped HTML string
     */
    private escapeHtml(text: string): string {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format date for display
     *
     * @param dateString ISO date string
     * @returns Formatted date
     */
    private formatDate(dateString: string): string {
        const date = new Date(dateString);
        return date.toLocaleDateString("it-IT", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
        });
    }

    /**
     * Format currency
     *
     * @param amount Amount in EUR
     * @returns Formatted currency string
     */
    private formatCurrency(amount: number): string {
        return new Intl.NumberFormat("it-IT", {
            style: "currency",
            currency: "EUR",
        }).format(amount);
    }
}

/**
 * Initialize acts table on page load
 *
 * @param containerSelector Container CSS selector
 * @param api NatanApiClient instance
 * @returns ActsTable instance
 */
export function initActsTable(
    containerSelector: string,
    api: NatanApiClient
): ActsTable {
    return new ActsTable(containerSelector, api);
}
