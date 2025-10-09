/**
 * N.A.T.A.N. Module Entry Point
 *
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Main entry point for N.A.T.A.N. TypeScript module
 */

// Export types
export * from "./types";

// Export API client
export { NatanApiClient, getNatanApiClient } from "./ApiClient";
export { default as apiClient } from "./ApiClient";

// Export components
export { UploadManager } from "./UploadManager";
export { ActsTable, initActsTable } from "./ActsTable";
export { StatsPanel, initStatsPanel } from "./StatsPanel";

// Import for convenience
import { getNatanApiClient } from "./ApiClient";
import { UploadManager } from "./UploadManager";
import { initActsTable } from "./ActsTable";
import { initStatsPanel } from "./StatsPanel";

/**
 * Initialize N.A.T.A.N. components on page
 *
 * Call this from your page scripts to auto-initialize all components
 *
 * @example
 * ```typescript
 * import { initNatan } from '@/natan';
 *
 * document.addEventListener('DOMContentLoaded', () => {
 *     initNatan();
 * });
 * ```
 */
export function initNatan(): void {
    const api = getNatanApiClient();

    // Initialize upload manager if dropzone exists
    const dropzone = document.querySelector("#uploadDropzone");
    const fileInput = document.querySelector("#fileInput");
    if (dropzone && fileInput) {
        const uploadManager = new UploadManager(
            "#uploadDropzone",
            "#fileInput",
            api
        );
        console.log("N.A.T.A.N. UploadManager initialized");

        // Make available globally for debugging
        (window as any).natanUploadManager = uploadManager;
    }

    // Initialize acts table if container exists
    const tableContainer = document.querySelector("#tableContainer");
    if (tableContainer) {
        const actsTable = initActsTable("#tableContainer", api);
        console.log("N.A.T.A.N. ActsTable initialized");

        // Make available globally for debugging
        (window as any).natanActsTable = actsTable;
    }

    // Initialize stats panel if charts exist
    const statsCharts = document.querySelector(
        "#actTypeChart, #monthlyTrendChart"
    );
    if (statsCharts) {
        const statsPanel = initStatsPanel(api);
        console.log("N.A.T.A.N. StatsPanel initialized");

        // Make available globally for debugging
        (window as any).natanStatsPanel = statsPanel;
    }
}

/**
 * Auto-initialize on DOM ready
 */
if (typeof window !== "undefined") {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initNatan);
    } else {
        // DOM already loaded
        initNatan();
    }
}
