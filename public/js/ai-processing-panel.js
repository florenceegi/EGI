/**
 * AI Processing Panel Controller
 *
 * @package Resources\Views\Pa\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-27
 * @purpose Professional AI processing visualization controller
 */

const AIProcessingPanel = {
    // State
    startTime: null,
    timerInterval: null,
    currentStage: "search",
    chunkingMode: false, // ✨ NEW v4.0
    totalChunks: 0, // ✨ NEW v4.0
    completedChunks: 0, // ✨ NEW v4.0
    chunkResults: [], // ✨ NEW v4.0

    // DOM Elements (cached)
    elements: {
        panel: null,
        title: null,
        subtitle: null,
        icon: null, // ✨ NEW v4.0
        progressBar: null,
        progressPercentage: null,
        statActs: null,
        statRelevance: null,
        statTime: null,
        retryInfo: null,
        retryDetail: null,
        retryProgress: null,
        retryCountdown: null,
        // ✨ NEW v4.0 - Chunking elements
        chunkingProgress: null,
        chunkingSubtitle: null,
        chunkCurrentCount: null,
        chunksGrid: null,
        chunkProgressLabel: null,
        chunkProgressBar: null,
        chunkProgressPercentage: null,
        chunksCompleted: null,
        chunksProcessing: null,
        chunksPending: null,
        chunkResultsPreview: null,
        chunkResultsText: null,
    },

    /**
     * Initialize panel (cache DOM elements)
     */
    init() {
        this.elements.panel = document.getElementById("aiProcessingPanel");
        this.elements.title = document.getElementById("aiPanelTitle");
        this.elements.subtitle = document.getElementById("aiPanelSubtitle");
        this.elements.icon = document.getElementById("aiPanelIcon"); // ✨ NEW v4.0
        this.elements.progressBar = document.getElementById("aiProgressBar");
        this.elements.progressPercentage = document.getElementById(
            "aiProgressPercentage"
        );
        this.elements.statActs = document.getElementById("stat-acts");
        this.elements.statRelevance = document.getElementById("stat-relevance");
        this.elements.statTime = document.getElementById("stat-time");
        this.elements.retryInfo = document.getElementById("aiRetryInfo");
        this.elements.retryDetail = document.getElementById("aiRetryDetail");
        this.elements.retryProgress =
            document.getElementById("aiRetryProgress");
        this.elements.retryCountdown =
            document.getElementById("aiRetryCountdown");

        // ✨ NEW v4.0 - Cache chunking elements
        this.elements.chunkingProgress =
            document.getElementById("aiChunkingProgress");
        this.elements.chunkingSubtitle =
            document.getElementById("chunkingSubtitle");
        this.elements.chunkCurrentCount =
            document.getElementById("chunkCurrentCount");
        this.elements.chunksGrid = document.getElementById("chunksGrid");
        this.elements.chunkProgressLabel =
            document.getElementById("chunkProgressLabel");
        this.elements.chunkProgressBar =
            document.getElementById("chunkProgressBar");
        this.elements.chunkProgressPercentage = document.getElementById(
            "chunkProgressPercentage"
        );
        this.elements.chunksCompleted =
            document.getElementById("chunksCompleted");
        this.elements.chunksProcessing =
            document.getElementById("chunksProcessing");
        this.elements.chunksPending = document.getElementById("chunksPending");
        this.elements.chunkResultsPreview = document.getElementById(
            "chunkResultsPreview"
        );
        this.elements.chunkResultsText =
            document.getElementById("chunkResultsText");

        console.log("[AIProcessingPanel] Initialized (v4.0 with Chunking)");
    },

    /**
     * Show panel with initial stats
     */
    show(actsCount = 0) {
        if (this.elements.panel) {
            this.elements.panel.classList.remove("hidden");
            this.elements.panel.style.display = "flex"; // ✨ Override inline style
        }
        this.startTime = Date.now();

        // Reset progress
        this.updateProgress(10);

        // Update initial stats
        this.updateStats({
            acts: actsCount,
            relevance: 0,
        });

        // Start timer
        this.startTimer();

        // Update stages
        this.updateStage(
            "search",
            "completed",
            `${actsCount} atti trovati nel database`
        );
        this.updateStage(
            "context",
            "processing",
            "Ottimizzazione carico AI in corso..."
        );

        console.log("[AIProcessingPanel] Panel shown with", actsCount, "acts");
    },

    /**
     * Hide panel
     */
    hide() {
        if (this.elements.panel) {
            this.elements.panel.classList.add("hidden");
            this.elements.panel.style.display = "none"; // ✨ Reset inline style
        }
        this.stopTimer();
        console.log("[AIProcessingPanel] Panel hidden");
    },

    /**
     * Update progress bar
     */
    updateProgress(percentage) {
        if (this.elements.progressBar) {
            this.elements.progressBar.style.width = `${percentage}%`;
        }
        if (this.elements.progressPercentage) {
            this.elements.progressPercentage.textContent = `${percentage}%`;
        }
    },

    /**
     * Update stats
     */
    updateStats({ acts, relevance }) {
        if (acts !== undefined && this.elements.statActs) {
            this.elements.statActs.textContent = acts.toLocaleString();
        }
        if (relevance !== undefined && this.elements.statRelevance) {
            this.elements.statRelevance.textContent = `${relevance}%`;
        }
    },

    /**
     * Update processing stage
     */
    updateStage(stageId, status, detail) {
        const stage = document.getElementById(`stage-${stageId}`);
        const stageDetail = document.getElementById(`stage-${stageId}-detail`);

        if (!stage) return;

        // Remove all status classes
        stage.classList.remove(
            "bg-gray-50",
            "border-gray-200",
            "bg-green-50",
            "border-green-200",
            "bg-blue-50",
            "border-blue-200"
        );

        // Update icon
        const icon = stage.querySelector(".material-symbols-outlined");
        const iconContainer = icon?.parentElement;

        if (status === "completed") {
            stage.classList.add("bg-green-50", "border-green-200");
            if (icon) {
                icon.textContent = "check_circle";
                icon.className = "material-symbols-outlined text-green-600";
            }
            if (iconContainer) {
                iconContainer.classList.remove("animate-spin");
            }
        } else if (status === "processing") {
            stage.classList.add("bg-blue-50", "border-blue-200");
            if (icon) {
                icon.textContent = "sync";
                icon.className = "material-symbols-outlined text-blue-600";
            }
            if (iconContainer) {
                iconContainer.classList.add("animate-spin");
            }
        } else {
            stage.classList.add("bg-gray-50", "border-gray-200");
            if (icon) {
                icon.textContent = "pending";
                icon.className = "material-symbols-outlined text-gray-400";
            }
            if (iconContainer) {
                iconContainer.classList.remove("animate-spin");
            }
        }

        // Update detail
        if (stageDetail && detail) {
            stageDetail.textContent = detail;
        }
    },

    /**
     * Show adaptive retry info
     */
    showRetryInfo(attempt, totalAttempts, actsReduction, waitSeconds) {
        if (!this.elements.retryInfo) return;

        this.elements.retryInfo.classList.remove("hidden");

        if (this.elements.retryDetail) {
            this.elements.retryDetail.textContent = `Tentativo ${attempt}/${totalAttempts} - Riduzione contesto: ${actsReduction} atti - Attesa: ${waitSeconds}s`;
        }

        // Animate countdown
        if (waitSeconds > 0) {
            this.animateRetryCountdown(waitSeconds);
        }

        console.log("[AIProcessingPanel] Retry info shown:", {
            attempt,
            actsReduction,
            waitSeconds,
        });
    },

    /**
     * Hide retry info
     */
    hideRetryInfo() {
        this.elements.retryInfo?.classList.add("hidden");
    },

    /**
     * Animate retry countdown
     */
    animateRetryCountdown(seconds) {
        let remaining = seconds;

        const updateCountdown = () => {
            if (this.elements.retryCountdown) {
                this.elements.retryCountdown.textContent = `${remaining}s`;
            }
            if (this.elements.retryProgress) {
                const progress = ((seconds - remaining) / seconds) * 100;
                this.elements.retryProgress.style.width = `${progress}%`;
            }

            remaining--;

            if (remaining < 0) {
                this.hideRetryInfo();
            }
        };

        updateCountdown();
        const intervalId = setInterval(updateCountdown, 1000);

        setTimeout(() => clearInterval(intervalId), seconds * 1000);
    },

    /**
     * Start elapsed time timer
     */
    startTimer() {
        this.timerInterval = setInterval(() => {
            if (!this.startTime) return;

            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;

            if (this.elements.statTime) {
                this.elements.statTime.textContent = `${String(
                    minutes
                ).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
            }
        }, 1000);
    },

    /**
     * Stop timer
     */
    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    },

    /**
     * Complete processing (success animation)
     */
    complete() {
        this.updateProgress(100);
        this.updateStage("context", "completed", "Contesto ottimizzato");
        this.updateStage("ai", "completed", "Analisi AI completata");
        this.updateStage(
            "response",
            "processing",
            "Generazione risposta in corso..."
        );

        if (this.elements.title) {
            this.elements.title.textContent =
                "✨ Analisi completata! Generazione risposta...";
        }

        console.log("[AIProcessingPanel] Processing completed");

        // Auto-hide after 2 seconds
        setTimeout(() => this.hide(), 2000);
    },

    // ========================================
    // ✨ NEW v4.0 - CHUNKING METHODS
    // ========================================

    /**
     * Show panel in chunking mode
     * @param {number} totalChunks - Total number of chunks to process
     * @param {number} totalActs - Total acts across all chunks
     */
    showChunking(totalChunks, totalActs) {
        this.chunkingMode = true;
        this.totalChunks = totalChunks;
        this.completedChunks = 0;
        this.chunkResults = [];

        // Show panel
        if (this.elements.panel) {
            this.elements.panel.classList.remove("hidden");
            this.elements.panel.style.display = "flex"; // ✨ Override inline style
        }
        this.startTime = Date.now();

        // Update title and icon for chunking mode
        if (this.elements.title) {
            this.elements.title.textContent =
                "⚙️ N.A.T.A.N. sta processando dataset grande...";
        }
        if (this.elements.subtitle) {
            this.elements.subtitle.textContent = `Elaborazione intelligente in ${totalChunks} chunk per ottimizzare performance AI`;
        }
        if (this.elements.icon) {
            this.elements.icon.textContent = "splitscreen";
        }

        // Show chunking progress section
        this.elements.chunkingProgress?.classList.remove("hidden");

        // Update initial stats
        this.updateStats({
            acts: totalActs,
            relevance: 0,
        });

        // Initialize chunks grid
        this.initializeChunksGrid(totalChunks);

        // Update chunk counters
        this.updateChunkCounters(0, 0, totalChunks);

        // Start timer
        this.startTimer();

        console.log("[AIProcessingPanel] Chunking mode activated:", {
            totalChunks,
            totalActs,
        });
    },

    /**
     * Initialize visual chunks grid
     * @param {number} total - Total chunks
     */
    initializeChunksGrid(total) {
        if (!this.elements.chunksGrid) return;

        this.elements.chunksGrid.innerHTML = "";

        for (let i = 0; i < total; i++) {
            const chunkDiv = document.createElement("div");
            chunkDiv.id = `chunk-${i}`;
            chunkDiv.className =
                "h-8 rounded border-2 border-gray-300 bg-gray-100 transition-all duration-300";
            chunkDiv.title = `Chunk ${i + 1}`;
            this.elements.chunksGrid.appendChild(chunkDiv);
        }

        // Update current count display
        if (this.elements.chunkCurrentCount) {
            this.elements.chunkCurrentCount.textContent = `0/${total}`;
        }
    },

    /**
     * Update chunk progress
     * @param {number} chunkIndex - Current chunk index (0-based)
     * @param {number} progress - Progress percentage (0-100)
     * @param {number} actsInChunk - Number of acts in this chunk
     */
    updateChunkProgress(chunkIndex, progress, actsInChunk) {
        // Update chunk visual in grid
        const chunkDiv = document.getElementById(`chunk-${chunkIndex}`);
        if (chunkDiv) {
            if (progress === 100) {
                // Completed
                chunkDiv.className =
                    "h-8 rounded border-2 border-green-500 bg-green-100 transition-all duration-300";
                chunkDiv.innerHTML = `<div class="flex h-full items-center justify-center"><span class="material-symbols-outlined text-xs text-green-600">check</span></div>`;
            } else if (progress > 0) {
                // Processing
                chunkDiv.className =
                    "h-8 rounded border-2 border-blue-500 bg-blue-100 animate-pulse transition-all duration-300";
                chunkDiv.innerHTML = `<div class="flex h-full items-center justify-center"><span class="material-symbols-outlined text-xs text-blue-600 animate-spin">sync</span></div>`;
            }
        }

        // Update progress bar
        if (this.elements.chunkProgressBar) {
            this.elements.chunkProgressBar.style.width = `${progress}%`;
        }
        if (this.elements.chunkProgressPercentage) {
            this.elements.chunkProgressPercentage.textContent = `${progress}%`;
        }

        // Update label
        if (this.elements.chunkProgressLabel) {
            this.elements.chunkProgressLabel.textContent = `Chunk ${
                chunkIndex + 1
            }/${this.totalChunks}: Analizzando ${actsInChunk} atti...`;
        }

        // Update overall progress
        const overallProgress = Math.round(
            ((this.completedChunks + progress / 100) / this.totalChunks) * 100
        );
        this.updateProgress(overallProgress);
    },

    /**
     * Mark chunk as completed with results
     * @param {number} chunkIndex - Chunk index (0-based)
     * @param {object} result - Chunk result {relevantActs: number, summary: string}
     */
    completeChunk(chunkIndex, result) {
        this.completedChunks++;
        this.chunkResults.push({
            chunkIndex,
            ...result,
        });

        // Update chunk visual
        const chunkDiv = document.getElementById(`chunk-${chunkIndex}`);
        if (chunkDiv) {
            chunkDiv.className =
                "h-8 rounded border-2 border-green-500 bg-green-100 transition-all duration-300";
            chunkDiv.innerHTML = `<div class="flex h-full items-center justify-center"><span class="material-symbols-outlined text-xs text-green-600">check</span></div>`;
        }

        // Update counters
        this.updateChunkCounters(
            this.completedChunks,
            0,
            this.totalChunks - this.completedChunks
        );

        // Update current count
        if (this.elements.chunkCurrentCount) {
            this.elements.chunkCurrentCount.textContent = `${this.completedChunks}/${this.totalChunks}`;
        }

        // Show results preview
        this.updateChunkResultsPreview();

        console.log("[AIProcessingPanel] Chunk completed:", {
            chunkIndex,
            completed: this.completedChunks,
            total: this.totalChunks,
            result,
        });

        // If all chunks completed, show aggregation stage
        if (this.completedChunks === this.totalChunks) {
            this.startAggregation();
        }
    },

    /**
     * Update chunk counters
     * @param {number} completed - Completed chunks count
     * @param {number} processing - Processing chunks count
     * @param {number} pending - Pending chunks count
     */
    updateChunkCounters(completed, processing, pending) {
        if (this.elements.chunksCompleted) {
            this.elements.chunksCompleted.textContent = completed;
        }
        if (this.elements.chunksProcessing) {
            this.elements.chunksProcessing.textContent = processing;
        }
        if (this.elements.chunksPending) {
            this.elements.chunksPending.textContent = pending;
        }
    },

    /**
     * Update chunk results preview
     */
    updateChunkResultsPreview() {
        if (
            !this.elements.chunkResultsPreview ||
            !this.elements.chunkResultsText
        )
            return;

        this.elements.chunkResultsPreview.classList.remove("hidden");

        const resultsHTML = this.chunkResults
            .map(
                (r) =>
                    `Chunk ${r.chunkIndex + 1}: ${
                        r.relevantActs || 0
                    } atti rilevanti trovati`
            )
            .join("<br>");

        this.elements.chunkResultsText.innerHTML = resultsHTML;
    },

    /**
     * Start aggregation phase (all chunks completed)
     */
    startAggregation() {
        this.updateProgress(95);

        if (this.elements.title) {
            this.elements.title.textContent =
                "🎯 Aggregazione risultati in corso...";
        }
        if (this.elements.subtitle) {
            this.elements.subtitle.textContent = `Combinando risultati da ${this.totalChunks} chunk per risposta finale`;
        }
        if (this.elements.icon) {
            this.elements.icon.textContent = "merge";
        }

        // Update stages
        this.updateStage("context", "completed", "Tutti i chunk processati");
        this.updateStage("ai", "completed", "Analisi AI completata");
        this.updateStage(
            "response",
            "processing",
            "Aggregazione risultati in corso..."
        );

        console.log("[AIProcessingPanel] Starting aggregation phase");
    },

    /**
     * Complete chunking process
     */
    completeChunking() {
        this.updateProgress(100);

        if (this.elements.title) {
            this.elements.title.textContent =
                "✨ Analisi completata! Risposta finale generata.";
        }

        this.updateStage("response", "completed", "Risposta aggregata pronta");

        console.log("[AIProcessingPanel] Chunking process completed");

        // Auto-hide after 3 seconds
        setTimeout(() => this.hide(), 3000);
    },

    /**
     * Reset chunking state when hiding
     */
    hide() {
        if (this.elements.panel) {
            this.elements.panel.classList.add("hidden");
            this.elements.panel.style.display = "none"; // ✨ Reset inline style
        }
        this.elements.chunkingProgress?.classList.add("hidden"); // ✨ NEW v4.0
        this.stopTimer();

        // Reset chunking state
        this.chunkingMode = false;
        this.totalChunks = 0;
        this.completedChunks = 0;
        this.chunkResults = [];

        console.log("[AIProcessingPanel] Panel hidden and reset");
    },

    /**
     * Update stage indicator
     * ✨ NEW v6.0 - Real-time stage tracking for SSE
     *
     * @param {string} stageId - Stage ID (search|context|ai|response)
     * @param {string} status - Status (pending|active|completed|error)
     * @param {string} detail - Optional detail text
     */
    updateStage(stageId, status, detail = null) {
        // Use stageId directly (no mapping needed - HTML has correct IDs)
        const stageElement = document.getElementById(`stage-${stageId}`);

        if (!stageElement) {
            console.warn(
                `[AIProcessingPanel] Stage element not found: stage-${stageId}`
            );
            return;
        }

        const icon = stageElement.querySelector(".stage-icon");
        const label = stageElement.querySelector(".stage-label");
        const detailElement = stageElement.querySelector(".stage-detail");

        // Remove all status classes
        stageElement.classList.remove(
            "stage-pending",
            "stage-active",
            "stage-completed",
            "stage-error"
        );

        // Add new status class
        stageElement.classList.add(`stage-${status}`);

        // Update icon based on status
        if (status === "active") {
            icon.innerHTML = `<span class="material-symbols-outlined animate-spin text-blue-500">progress_activity</span>`;
        } else if (status === "completed") {
            icon.innerHTML = `<span class="material-symbols-outlined text-green-500">check_circle</span>`;
        } else if (status === "error") {
            icon.innerHTML = `<span class="material-symbols-outlined text-red-500">error</span>`;
        }

        // Update detail text if provided
        if (detail && detailElement) {
            detailElement.textContent = detail;
            detailElement.classList.remove("hidden");
        }

        console.log(
            `[AIProcessingPanel] Stage updated: ${stageId} → ${status}`,
            detail
        );
    },

    /**
     * Update cost tracking panel
     * ✨ NEW v6.0 - Real-time cost display during processing
     *
     * @param {object} costData - Cost tracking data
     * @param {number} costData.inputTokens - Input tokens consumed
     * @param {number} costData.outputTokens - Output tokens consumed
     * @param {number} costData.creditsUsed - AI credits deducted
     * @param {number} costData.costEur - Cost in EUR
     */
    updateCostTracking(costData) {
        // Find cost tracking panel in HTML
        let costPanel = document.getElementById("aiCostTracking");

        if (!costPanel) {
            // Create cost panel if doesn't exist
            const panel = this.elements.panel;
            if (!panel) return;

            costPanel = document.createElement("div");
            costPanel.id = "aiCostTracking";
            costPanel.className =
                "mt-4 rounded-lg bg-gray-50 p-3 border border-gray-200";
            costPanel.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-gray-600 text-lg">payments</span>
                    <h4 class="text-sm font-semibold text-gray-700">Costi Real-Time</h4>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-500">Token Input:</span>
                        <span id="cost-input-tokens" class="font-mono font-semibold text-gray-700 ml-1">0</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Token Output:</span>
                        <span id="cost-output-tokens" class="font-mono font-semibold text-gray-700 ml-1">0</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Crediti:</span>
                        <span id="cost-credits" class="font-mono font-semibold text-[#D4A574] ml-1">0</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Costo:</span>
                        <span id="cost-eur" class="font-mono font-semibold text-[#2D5016] ml-1">€0.00</span>
                    </div>
                </div>
            `;

            // Insert after progress section
            const progressSection = panel.querySelector(".space-y-4");
            if (progressSection) {
                progressSection.appendChild(costPanel);
            }
        }

        // SHOW the panel (remove 'hidden' class from HTML)
        if (costPanel && costPanel.classList.contains('hidden')) {
            costPanel.classList.remove('hidden');
            console.log("[AIProcessingPanel] Cost panel now VISIBLE");
        }

        // Update values in HTML elements
        const inputTokensEl = document.getElementById("costInputTokens");
        const outputTokensEl = document.getElementById("costOutputTokens");
        const creditsEl = document.getElementById("costCurrentCredits");
        const currentTotalEl = document.getElementById("costCurrentTotal");
        const estimatedFinalEl = document.getElementById("costEstimatedFinal");

        if (inputTokensEl) {
            inputTokensEl.textContent = (costData.inputTokens || 0).toLocaleString("it-IT");
        }
        if (outputTokensEl) {
            outputTokensEl.textContent = (costData.outputTokens || 0).toLocaleString("it-IT");
        }
        if (creditsEl) {
            creditsEl.textContent = (costData.creditsUsed || 0).toLocaleString("it-IT");
        }
        if (currentTotalEl) {
            currentTotalEl.textContent = (costData.creditsUsed || 0).toFixed(2);
        }
        if (estimatedFinalEl) {
            // Estimated final = current * 1.2 (rough estimate)
            const estimated = (costData.creditsUsed || 0) * 1.2;
            estimatedFinalEl.textContent = estimated.toFixed(2);
        }

        console.log("[AIProcessingPanel] Cost tracking updated AND VISIBLE:", costData);
    },
};

// Initialize on DOM load
document.addEventListener("DOMContentLoaded", () => {
    AIProcessingPanel.init();
});

// ========================================
// ✨ USAGE EXAMPLES v4.0
// ========================================

/*
// ESEMPIO 1: Processing normale (senza chunking)
AIProcessingPanel.show(150); // 150 atti totali
AIProcessingPanel.updateProgress(50);
AIProcessingPanel.updateStats({ acts: 150, relevance: 85 });
AIProcessingPanel.complete();

// ESEMPIO 2: Processing con chunking (dataset grande)
// Step 1: Avvia chunking mode
AIProcessingPanel.showChunking(5, 900); // 5 chunks, 900 atti totali

// Step 2: Processa ogni chunk
for (let i = 0; i < 5; i++) {
    // Aggiorna progress chunk corrente
    AIProcessingPanel.updateChunkProgress(i, 0, 180); // Chunk i, 0%, 180 atti

    // Simula progress durante processing
    AIProcessingPanel.updateChunkProgress(i, 50, 180); // 50% completato

    // Completa chunk con risultati
    AIProcessingPanel.completeChunk(i, {
        relevantActs: 23, // Atti rilevanti trovati in questo chunk
        summary: "Analisi completata" // Opzionale
    });
}

// Step 3: Sistema chiama automaticamente startAggregation() quando tutti i chunk sono completati

// Step 4: Completa processo finale
AIProcessingPanel.completeChunking();

// ESEMPIO 3: Retry con rate limit
AIProcessingPanel.showRetryInfo(
    2,      // Tentativo 2
    5,      // Di 5 totali
    50,     // Riduzione di 50 atti
    10      // Attesa 10 secondi
);

// ESEMPIO 4: Chiamata REALE da NatanChatController
// Backend chiama via polling o WebSocket:
window.addEventListener('natan-chunk-update', (event) => {
    const { chunkIndex, progress, actsInChunk } = event.detail;
    AIProcessingPanel.updateChunkProgress(chunkIndex, progress, actsInChunk);
});

window.addEventListener('natan-chunk-complete', (event) => {
    const { chunkIndex, result } = event.detail;
    AIProcessingPanel.completeChunk(chunkIndex, result);
});
*/
