/**
 * 🎓 Natan Tutor - Frontend Component
 *
 * Componente TypeScript per l'assistente operativo Natan Tutor.
 * Permette agli utenti di eseguire azioni sulla piattaforma con assistenza
 * guidata, consumando Egili come valuta interna.
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-11-25
 */

import { NatanTutorAPI, UserState, ActionCost, TutorResponse } from './NatanTutorAPI';
import { NatanTutorUI } from './NatanTutorUI';

export interface NatanTutorConfig {
    toggleButtonId: string;
    panelContainerId: string;
    mode: 'tutoring' | 'expert';
    autoOpen: boolean;
    debug: boolean;
}

const defaultConfig: NatanTutorConfig = {
    toggleButtonId: 'natan-tutor-toggle',
    panelContainerId: 'natan-tutor-panel',
    mode: 'tutoring',
    autoOpen: false,
    debug: true,
};

/**
 * Main Natan Tutor Controller
 */
export class NatanTutor {
    private config: NatanTutorConfig;
    private api: NatanTutorAPI;
    private ui: NatanTutorUI;
    private isOpen: boolean = false;
    private userState: UserState | null = null;
    private toggleButton: HTMLElement | null = null;

    constructor(config: Partial<NatanTutorConfig> = {}) {
        this.config = { ...defaultConfig, ...config };
        this.api = new NatanTutorAPI();
        this.ui = new NatanTutorUI();

        this.log('🎓 Natan Tutor initializing...');
        this.init();
    }

    /**
     * Initialize the component
     */
    private async init(): Promise<void> {
        // Wait for DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * Setup DOM elements and event listeners
     */
    private setup(): void {
        this.log('🔧 Setting up Natan Tutor...');

        // Find toggle button (support multiple IDs for compatibility)
        this.toggleButton = this.findToggleButton();

        if (!this.toggleButton) {
            this.log('⚠️ Toggle button not found, will retry...');
            setTimeout(() => this.setup(), 500);
            return;
        }

        this.log('✅ Toggle button found:', this.toggleButton.id);

        // Create the panel UI
        this.ui.createPanel();

        // Bind events
        this.bindEvents();

        // Load initial state
        this.loadUserState();

        this.log('🎓 Natan Tutor ready!');
    }

    /**
     * Find the toggle button with fallback IDs
     */
    private findToggleButton(): HTMLElement | null {
        const ids = [
            'natan-tutor-toggle',
            'natan-assistant-toggle-global',
            'natan-assistant-toggle-desktop',
            'natan-assistant-toggle-mobile',
            'natan-assistant-toggle',
        ];

        for (const id of ids) {
            const el = document.getElementById(id);
            if (el) return el;
        }
        return null;
    }

    /**
     * Bind event listeners
     */
    private bindEvents(): void {
        if (!this.toggleButton) return;

        // Toggle button click
        this.toggleButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.log('🖱️ Toggle clicked');
            this.toggle();
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.ui.panelContains(e.target as Node) && e.target !== this.toggleButton) {
                this.close();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Listen for action buttons inside the panel
        this.ui.onAction((action: string, data?: any) => {
            this.handleAction(action, data);
        });
    }

    /**
     * Toggle panel visibility
     */
    public toggle(): void {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Open the panel
     */
    public async open(): Promise<void> {
        this.log('📖 Opening panel...');
        this.isOpen = true;

        // Refresh user state
        await this.loadUserState();

        // Show panel with current state
        this.ui.show(this.userState);

        // Update toggle button state
        this.toggleButton?.setAttribute('aria-expanded', 'true');
    }

    /**
     * Close the panel
     */
    public close(): void {
        this.log('📕 Closing panel...');
        this.isOpen = false;
        this.ui.hide();
        this.toggleButton?.setAttribute('aria-expanded', 'false');
    }

    /**
     * Load user state from API
     */
    private async loadUserState(): Promise<void> {
        try {
            this.log('📡 Loading user state...');
            const response = await this.api.getUserState();

            if (response.success && response.data) {
                this.userState = response.data;
                this.log('✅ User state loaded:', this.userState);
            } else {
                this.log('⚠️ Failed to load user state:', response.error);
                this.userState = null;
            }
        } catch (error) {
            this.log('❌ Error loading user state:', error);
            this.userState = null;
        }
    }

    /**
     * Handle action from UI
     */
    private async handleAction(action: string, data?: any): Promise<void> {
        this.log(`🎬 Handling action: ${action}`, data);

        switch (action) {
            case 'message':
                // User sent a chat message
                await this.handleUserMessage(data.text);
                break;

            case 'confirm_action':
                // User confirmed an action
                await this.executeConfirmedAction(data.code);
                break;

            case 'cancel_action':
                // User cancelled an action
                this.ui.addMessage('natan', 'Ok, ho annullato l\'operazione. Posso aiutarti in altro modo?');
                break;

            case 'navigate':
                response = await this.api.navigate(data.destination, this.config.mode);
                if (response.success && response.data?.url) {
                    window.location.href = response.data.url;
                    return;
                }
                break;

            case 'close':
                this.close();
                break;

            case 'refresh':
                await this.loadUserState();
                this.ui.updateState(this.userState);
                break;

            default:
                this.log(`⚠️ Unknown action: ${action}`);
        }
    }

    /**
     * Handle user chat message
     */
    private async handleUserMessage(text: string): Promise<void> {
        this.log('💬 User message:', text);

        // Show typing indicator
        this.ui.setLoading(true);

        try {
            // Parse intent and get response from backend
            const response = await this.api.explain(text, this.config.mode);

            this.ui.setLoading(false);

            if (response.success) {
                // Show Natan's response
                this.ui.addMessage('natan', response.data?.explanation || response.message || 'Come posso aiutarti?');

                // If there's an action to confirm, show action card
                if (response.data?.suggested_action) {
                    const action = response.data.suggested_action;
                    this.ui.showActionCard(
                        action.code,
                        action.description,
                        action.cost,
                        this.userState?.balance || 0
                    );
                }
            } else {
                this.ui.addMessage('natan', response.message || 'Mi dispiace, non ho capito. Puoi riformulare?');
            }

        } catch (error) {
            this.log('❌ Error handling message:', error);
            this.ui.setLoading(false);
            this.ui.addMessage('natan', 'Si è verificato un errore. Riprova più tardi.');
        }
    }

    /**
     * Execute a confirmed action
     */
    private async executeConfirmedAction(actionCode: string): Promise<void> {
        this.log('🚀 Executing confirmed action:', actionCode);

        this.ui.setLoading(true);
        this.ui.addMessage('system', 'Esecuzione in corso...');

        try {
            let response: TutorResponse;

            // Route to appropriate API based on action code
            if (actionCode.includes('mint')) {
                response = await this.api.assistMint({}, this.config.mode);
            } else if (actionCode.includes('collection')) {
                response = await this.api.assistCollectionCreate({}, this.config.mode);
            } else if (actionCode.includes('reservation')) {
                response = await this.api.assistReservation(0, this.config.mode);
            } else {
                response = await this.api.explain(actionCode, this.config.mode);
            }

            this.ui.setLoading(false);
            this.ui.showAssistanceResult(response);

            // Refresh balance
            await this.loadUserState();
            this.ui.updateState(this.userState);

        } catch (error) {
            this.log('❌ Error executing action:', error);
            this.ui.setLoading(false);
            this.ui.showError('Errore durante l\'esecuzione. Riprova.');
        }
    }

    /**
     * Set mode (tutoring or expert)
     */
    public setMode(mode: 'tutoring' | 'expert'): void {
        this.config.mode = mode;
        this.ui.updateMode(mode);
    }

    /**
     * Get current mode
     */
    public getMode(): string {
        return this.config.mode;
    }

    /**
     * Check if user can afford an action
     */
    public async canAfford(action: string): Promise<boolean> {
        const result = await this.api.canAfford(action, this.config.mode);
        return result.can_afford ?? false;
    }

    /**
     * Debug logging
     */
    private log(...args: any[]): void {
        if (this.config.debug) {
            console.log('[🎓 NatanTutor]', ...args);
        }
    }
}

// Auto-initialize on DOM ready
let natanTutorInstance: NatanTutor | null = null;

export function initNatanTutor(config?: Partial<NatanTutorConfig>): NatanTutor {
    if (!natanTutorInstance) {
        natanTutorInstance = new NatanTutor(config);
        (window as any).natanTutor = natanTutorInstance;
    }
    return natanTutorInstance;
}

// Export for use
export default NatanTutor;
