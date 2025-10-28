/**
 * AI Credits Cost Tracking System
 *
 * @package Resources\Js\Components
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - AI Credits Cost Tracking Frontend)
 * @date 2025-10-28
 * @purpose Frontend cost preview, real-time tracking, and final summary for AI Credits system
 *
 * FEATURES:
 * - Cost preview modal before analysis (estimate API call)
 * - Real-time cost display during processing (integrated with AIProcessingPanel)
 * - Final cost summary after completion
 * - Balance checks and insufficient credits handling
 * - Exchange rate display (ECB)
 * - GDPR-compliant financial data display
 */

/**
 * AI Cost Preview Modal Handler
 * Shows estimated cost before starting analysis
 */
const AICostPreview = {
    config: {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        modal: null,
        estimateData: null,
        onProceedCallback: null,
    },

    /**
     * Initialize modal
     */
    init() {
        this.config.modal = document.getElementById('aiCostPreviewModal');
        console.log('[AICostPreview] Initialized');
    },

    /**
     * Show cost preview modal with estimation
     *
     * @param {Object} params - Analysis parameters (query, acts_ids, chunks_count, etc.)
     * @param {Function} onProceed - Callback to execute when user confirms
     */
    async show(params, onProceed) {
        console.log('[AICostPreview] 💰 Opening cost preview modal', params);

        this.config.onProceedCallback = onProceed;

        // Show modal
        if (this.config.modal) {
            this.config.modal.classList.remove('hidden');
            this.config.modal.classList.add('flex');
        }

        // Show loading state
        document.getElementById('costPreviewLoading')?.classList.remove('hidden');
        document.getElementById('costPreviewContent')?.classList.add('hidden');

        // Fetch cost estimation
        try {
            const response = await fetch('/pa/natan/estimate-cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                },
                body: JSON.stringify(params),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('[AICostPreview] 📊 Estimation received:', data);

            this.config.estimateData = data;
            this.renderEstimation(data);

        } catch (error) {
            console.error('[AICostPreview] ❌ Estimation failed:', error);
            this.showError(error.message);
        }
    },

    /**
     * Render estimation data in modal
     *
     * @param {Object} data - Estimation response from backend
     */
    renderEstimation(data) {
        // Hide loading, show content
        document.getElementById('costPreviewLoading')?.classList.add('hidden');
        document.getElementById('costPreviewContent')?.classList.remove('hidden');

        // Update subtitle
        const subtitle = document.getElementById('costPreviewSubtitle');
        if (subtitle) {
            subtitle.textContent = data.chunks_count > 1
                ? `${data.acts_count} atti, ${data.chunks_count} chunks`
                : `${data.acts_count} atti`;
        }

        // User balance
        const currentBalance = parseFloat(data.user_balance || 0);
        const estimatedCost = parseFloat(data.estimated_credits || 0);
        const balanceAfter = currentBalance - estimatedCost;

        document.getElementById('userCurrentBalance').textContent = currentBalance.toFixed(2);
        document.getElementById('userBalanceAfter').textContent = balanceAfter.toFixed(2);

        // Balance status card styling
        const balanceCard = document.getElementById('balanceStatusCard');
        const balanceAfterEl = document.getElementById('userBalanceAfter');

        if (balanceAfter >= 0) {
            // Sufficient credits
            balanceCard?.classList.remove('border-red-300', 'bg-red-50');
            balanceCard?.classList.add('border-green-300', 'bg-green-50');
            balanceAfterEl?.classList.remove('text-red-600');
            balanceAfterEl?.classList.add('text-green-600');
        } else {
            // Insufficient credits
            balanceCard?.classList.remove('border-green-300', 'bg-green-50');
            balanceCard?.classList.add('border-red-300', 'bg-red-50');
            balanceAfterEl?.classList.remove('text-green-600');
            balanceAfterEl?.classList.add('text-red-600');
        }

        // Analysis details
        document.getElementById('estimateActsCount').textContent = data.acts_count || 0;
        document.getElementById('estimateChunksCount').textContent = data.chunks_count || 1;
        document.getElementById('estimateTotalTokens').textContent = (data.total_tokens || 0).toLocaleString();

        // Cost breakdown
        document.getElementById('estimateInputTokens').textContent = (data.input_tokens || 0).toLocaleString();
        document.getElementById('estimateOutputTokens').textContent = (data.output_tokens || 0).toLocaleString();
        document.getElementById('inputTokenPrice').textContent = `$${(data.input_price_per_million || 3).toFixed(3)}`;
        document.getElementById('outputTokenPrice').textContent = `$${(data.output_price_per_million || 15).toFixed(3)}`;
        document.getElementById('estimatedCostCredits').textContent = estimatedCost.toFixed(2);

        // Exchange rate
        document.getElementById('exchangeRateDisplay').textContent = (data.exchange_rate || 0.92).toFixed(4);
        const updatedDate = data.exchange_rate_updated ? new Date(data.exchange_rate_updated).toLocaleDateString() : '-';
        document.getElementById('exchangeRateUpdated').textContent = updatedDate;

        // Insufficient credits warning
        const insufficientWarning = document.getElementById('insufficientCreditsWarning');
        const proceedBtn = document.getElementById('costPreviewProceedBtn');

        if (balanceAfter < 0) {
            insufficientWarning?.classList.remove('hidden');
            document.getElementById('creditsRequired').textContent = estimatedCost.toFixed(2);
            document.getElementById('creditsBalance').textContent = currentBalance.toFixed(2);
            proceedBtn?.setAttribute('disabled', 'true');
        } else {
            insufficientWarning?.classList.add('hidden');
            proceedBtn?.removeAttribute('disabled');
        }
    },

    /**
     * Show error in modal
     */
    showError(message) {
        document.getElementById('costPreviewLoading')?.classList.add('hidden');
        const content = document.getElementById('costPreviewContent');
        if (content) {
            content.classList.remove('hidden');
            content.innerHTML = `
                <div class="rounded-xl border-2 border-red-300 bg-red-50 p-6 text-center">
                    <span class="material-symbols-outlined mb-3 text-5xl text-red-600">error</span>
                    <h4 class="mb-2 font-bold text-red-900">Estimation Failed</h4>
                    <p class="text-sm text-red-800">${message}</p>
                </div>
            `;
        }
    },

    /**
     * User confirmed - proceed with analysis
     */
    proceed() {
        console.log('[AICostPreview] ✅ User confirmed, proceeding with analysis');
        this.close();

        if (typeof this.config.onProceedCallback === 'function') {
            this.config.onProceedCallback(this.config.estimateData);
        }
    },

    /**
     * Close modal
     */
    close() {
        if (this.config.modal) {
            this.config.modal.classList.add('hidden');
            this.config.modal.classList.remove('flex');
        }
        this.config.estimateData = null;
        this.config.onProceedCallback = null;
    },
};

/**
 * AI Cost Real-Time Tracker
 * Updates cost display during processing (integrated with AIProcessingPanel)
 */
const AICostTracker = {
    config: {
        isVisible: false,
        currentCost: 0,
        estimatedFinal: 0,
        inputTokens: 0,
        outputTokens: 0,
        chunkCosts: [],
    },

    /**
     * Show cost tracking panel
     */
    show() {
        const panel = document.getElementById('aiCostTracking');
        if (panel) {
            panel.classList.remove('hidden');
            this.config.isVisible = true;
            console.log('[AICostTracker] 💰 Cost tracking panel shown');
        }
    },

    /**
     * Hide cost tracking panel
     */
    hide() {
        const panel = document.getElementById('aiCostTracking');
        if (panel) {
            panel.classList.add('hidden');
            this.config.isVisible = false;
        }
    },

    /**
     * Update cost display with new data
     *
     * @param {Object} costData - { credits, input_tokens, output_tokens, estimated_final }
     */
    update(costData) {
        if (!this.config.isVisible) return;

        this.config.currentCost = parseFloat(costData.credits || 0);
        this.config.inputTokens = parseInt(costData.input_tokens || 0);
        this.config.outputTokens = parseInt(costData.output_tokens || 0);
        this.config.estimatedFinal = parseFloat(costData.estimated_final || 0);

        // Update DOM
        document.getElementById('costCurrentCredits').textContent = this.config.currentCost.toFixed(2);
        document.getElementById('costInputTokens').textContent = this.config.inputTokens.toLocaleString();
        document.getElementById('costOutputTokens').textContent = this.config.outputTokens.toLocaleString();
        document.getElementById('costCurrentTotal').textContent = this.config.currentCost.toFixed(2);
        document.getElementById('costEstimatedFinal').textContent = this.config.estimatedFinal.toFixed(2);

        console.log('[AICostTracker] 📊 Cost updated:', this.config.currentCost, 'credits');
    },

    /**
     * Add chunk cost to list (for chunking mode)
     *
     * @param {number} chunkNumber - Chunk index (1-based)
     * @param {number} cost - Credits consumed by this chunk
     */
    addChunkCost(chunkNumber, cost) {
        this.config.chunkCosts.push({ chunk: chunkNumber, cost });

        const chunksList = document.getElementById('costChunksList');
        const perChunkContainer = document.getElementById('costPerChunk');

        if (chunksList && perChunkContainer) {
            perChunkContainer.classList.remove('hidden');

            const chunkItem = document.createElement('div');
            chunkItem.className = 'flex items-center justify-between rounded-lg bg-gray-100 px-3 py-2';
            chunkItem.innerHTML = `
                <span class="text-xs font-medium text-gray-700">Chunk ${chunkNumber}</span>
                <span class="text-sm font-bold text-[#E67E22]">${cost.toFixed(2)} credits</span>
            `;
            chunksList.appendChild(chunkItem);
        }
    },

    /**
     * Reset tracker for new analysis
     */
    reset() {
        this.config.currentCost = 0;
        this.config.estimatedFinal = 0;
        this.config.inputTokens = 0;
        this.config.outputTokens = 0;
        this.config.chunkCosts = [];

        const chunksList = document.getElementById('costChunksList');
        if (chunksList) {
            chunksList.innerHTML = '';
        }

        document.getElementById('costPerChunk')?.classList.add('hidden');
    },
};

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        AICostPreview.init();
    });
} else {
    AICostPreview.init();
}

// Export for use in other scripts
window.AICostPreview = AICostPreview;
window.AICostTracker = AICostTracker;
