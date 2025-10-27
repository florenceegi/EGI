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
    currentStage: 'search',
    
    // DOM Elements (cached)
    elements: {
        panel: null,
        title: null,
        subtitle: null,
        progressBar: null,
        progressPercentage: null,
        statActs: null,
        statRelevance: null,
        statTime: null,
        retryInfo: null,
        retryDetail: null,
        retryProgress: null,
        retryCountdown: null,
    },

    /**
     * Initialize panel (cache DOM elements)
     */
    init() {
        this.elements.panel = document.getElementById('aiProcessingPanel');
        this.elements.title = document.getElementById('aiPanelTitle');
        this.elements.subtitle = document.getElementById('aiPanelSubtitle');
        this.elements.progressBar = document.getElementById('aiProgressBar');
        this.elements.progressPercentage = document.getElementById('aiProgressPercentage');
        this.elements.statActs = document.getElementById('stat-acts');
        this.elements.statRelevance = document.getElementById('stat-relevance');
        this.elements.statTime = document.getElementById('stat-time');
        this.elements.retryInfo = document.getElementById('aiRetryInfo');
        this.elements.retryDetail = document.getElementById('aiRetryDetail');
        this.elements.retryProgress = document.getElementById('aiRetryProgress');
        this.elements.retryCountdown = document.getElementById('aiRetryCountdown');
        
        console.log('[AIProcessingPanel] Initialized');
    },

    /**
     * Show panel with initial stats
     */
    show(actsCount = 0) {
        this.elements.panel?.classList.remove('hidden');
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
        this.updateStage('search', 'completed', `${actsCount} atti trovati nel database`);
        this.updateStage('context', 'processing', 'Ottimizzazione carico AI in corso...');
        
        console.log('[AIProcessingPanel] Panel shown with', actsCount, 'acts');
    },

    /**
     * Hide panel
     */
    hide() {
        this.elements.panel?.classList.add('hidden');
        this.stopTimer();
        console.log('[AIProcessingPanel] Panel hidden');
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
        stage.classList.remove('bg-gray-50', 'border-gray-200', 'bg-green-50', 'border-green-200', 'bg-blue-50', 'border-blue-200');
        
        // Update icon
        const icon = stage.querySelector('.material-symbols-outlined');
        const iconContainer = icon?.parentElement;
        
        if (status === 'completed') {
            stage.classList.add('bg-green-50', 'border-green-200');
            if (icon) {
                icon.textContent = 'check_circle';
                icon.className = 'material-symbols-outlined text-green-600';
            }
            if (iconContainer) {
                iconContainer.classList.remove('animate-spin');
            }
        } else if (status === 'processing') {
            stage.classList.add('bg-blue-50', 'border-blue-200');
            if (icon) {
                icon.textContent = 'sync';
                icon.className = 'material-symbols-outlined text-blue-600';
            }
            if (iconContainer) {
                iconContainer.classList.add('animate-spin');
            }
        } else {
            stage.classList.add('bg-gray-50', 'border-gray-200');
            if (icon) {
                icon.textContent = 'pending';
                icon.className = 'material-symbols-outlined text-gray-400';
            }
            if (iconContainer) {
                iconContainer.classList.remove('animate-spin');
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
        
        this.elements.retryInfo.classList.remove('hidden');
        
        if (this.elements.retryDetail) {
            this.elements.retryDetail.textContent = 
                `Tentativo ${attempt}/${totalAttempts} - Riduzione contesto: ${actsReduction} atti - Attesa: ${waitSeconds}s`;
        }
        
        // Animate countdown
        if (waitSeconds > 0) {
            this.animateRetryCountdown(waitSeconds);
        }
        
        console.log('[AIProcessingPanel] Retry info shown:', { attempt, actsReduction, waitSeconds });
    },

    /**
     * Hide retry info
     */
    hideRetryInfo() {
        this.elements.retryInfo?.classList.add('hidden');
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
                this.elements.statTime.textContent = 
                    `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
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
        this.updateStage('context', 'completed', 'Contesto ottimizzato');
        this.updateStage('ai', 'completed', 'Analisi AI completata');
        this.updateStage('response', 'processing', 'Generazione risposta in corso...');
        
        if (this.elements.title) {
            this.elements.title.textContent = '✨ Analisi completata! Generazione risposta...';
        }
        
        console.log('[AIProcessingPanel] Processing completed');
        
        // Auto-hide after 2 seconds
        setTimeout(() => this.hide(), 2000);
    },
};

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    AIProcessingPanel.init();
});
