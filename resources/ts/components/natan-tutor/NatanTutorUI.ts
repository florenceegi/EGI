/**
 * 🎓 Natan Tutor UI Component - Chat Interface
 *
 * Interfaccia chat per Natan Tutor.
 * Design: Chat bubble in basso a destra con input e messaggi.
 */

import { UserState, TutorResponse, AvailableAction } from './NatanTutorAPI';

type ActionCallback = (action: string, data?: any) => void;

interface ChatMessage {
    id: string;
    type: 'user' | 'natan' | 'action' | 'system';
    content: string;
    timestamp: Date;
    actionData?: any;
}

export class NatanTutorUI {
    private chatContainer: HTMLElement | null = null;
    private messagesArea: HTMLElement | null = null;
    private inputArea: HTMLElement | null = null;
    private actionCallback: ActionCallback | null = null;
    private messages: ChatMessage[] = [];
    private isVisible: boolean = false;

    /**
     * Create the chat panel
     */
    public createPanel(): void {
        // Remove existing panel if any
        const existing = document.getElementById('natan-tutor-chat');
        if (existing) existing.remove();

        // Create chat container
        this.chatContainer = document.createElement('div');
        this.chatContainer.id = 'natan-tutor-chat';
        this.chatContainer.className = 'natan-tutor-chat hidden';
        this.chatContainer.innerHTML = this.getChatHTML();

        // Append to body
        document.body.appendChild(this.chatContainer);

        // Add styles
        this.injectStyles();

        // Store references
        this.messagesArea = this.chatContainer.querySelector('.natan-chat-messages');
        this.inputArea = this.chatContainer.querySelector('.natan-chat-input');

        // Bind input events
        this.bindInputEvents();

        console.log('[🎓 NatanTutorUI] Chat panel created');
    }

    /**
     * Get the chat HTML template
     */
    private getChatHTML(): string {
        return `
            <div class="natan-chat-header">
                <div class="natan-chat-avatar">
                    <img src="/images/default/natan-face.png" alt="Natan" />
                </div>
                <div class="natan-chat-title">
                    <h3>Natan Tutor</h3>
                    <span class="natan-chat-status">🟢 Online</span>
                </div>
                <div class="natan-chat-balance">
                    <span class="balance-amount">--</span> 💎
                </div>
                <button class="natan-chat-close" data-action="close" aria-label="Chiudi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="natan-chat-messages">
                <!-- Messages will be inserted here -->
            </div>

            <div class="natan-chat-input-area">
                <input type="text" class="natan-chat-input" placeholder="Scrivi a Natan..." />
                <button class="natan-chat-send" data-action="send">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        `;
    }

    /**
     * Inject CSS styles
     */
    private injectStyles(): void {
        if (document.getElementById('natan-tutor-chat-styles')) return;

        const style = document.createElement('style');
        style.id = 'natan-tutor-chat-styles';
        style.textContent = `
            .natan-tutor-chat {
                position: fixed;
                bottom: 90px;
                right: 24px;
                width: 380px;
                max-width: calc(100vw - 48px);
                height: 500px;
                max-height: calc(100vh - 120px);
                background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
                border: 1px solid rgba(16, 185, 129, 0.3);
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                display: flex;
                flex-direction: column;
                z-index: 99998;
                overflow: hidden;
                transition: opacity 0.3s, transform 0.3s;
            }

            .natan-tutor-chat.hidden {
                display: none !important;
                opacity: 0;
                transform: translateY(20px);
            }

            /* Header */
            .natan-chat-header {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                background: rgba(0, 0, 0, 0.3);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .natan-chat-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                overflow: hidden;
                border: 2px solid #10b981;
                flex-shrink: 0;
            }

            .natan-chat-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .natan-chat-title {
                flex: 1;
                min-width: 0;
            }

            .natan-chat-title h3 {
                margin: 0;
                color: #fff;
                font-size: 15px;
                font-weight: 600;
            }

            .natan-chat-status {
                font-size: 11px;
                color: #10b981;
            }

            .natan-chat-balance {
                padding: 4px 10px;
                background: rgba(16, 185, 129, 0.2);
                border-radius: 12px;
                font-size: 13px;
                font-weight: 600;
                color: #10b981;
            }

            .natan-chat-close {
                background: none;
                border: none;
                color: #666;
                cursor: pointer;
                padding: 6px;
                border-radius: 6px;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .natan-chat-close:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
            }

            /* Messages Area */
            .natan-chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            /* Message Bubbles */
            .natan-message {
                max-width: 85%;
                padding: 10px 14px;
                border-radius: 16px;
                font-size: 14px;
                line-height: 1.5;
                animation: messageIn 0.3s ease;
            }

            @keyframes messageIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .natan-message.user {
                align-self: flex-end;
                background: #10b981;
                color: #fff;
                border-bottom-right-radius: 4px;
            }

            .natan-message.natan {
                align-self: flex-start;
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                border-bottom-left-radius: 4px;
            }

            .natan-message.system {
                align-self: center;
                background: rgba(59, 130, 246, 0.2);
                color: #93c5fd;
                font-size: 12px;
                padding: 6px 12px;
            }

            /* Action Card */
            .natan-action-card {
                align-self: flex-start;
                max-width: 90%;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(16, 185, 129, 0.3);
                border-radius: 12px;
                overflow: hidden;
                animation: messageIn 0.3s ease;
            }

            .natan-action-header {
                padding: 12px 14px;
                background: rgba(16, 185, 129, 0.1);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .natan-action-header h4 {
                margin: 0;
                color: #10b981;
                font-size: 14px;
                font-weight: 600;
            }

            .natan-action-body {
                padding: 12px 14px;
                color: #ccc;
                font-size: 13px;
            }

            .natan-action-cost {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .natan-action-cost .cost {
                font-weight: 600;
                color: #10b981;
            }

            .natan-action-cost .balance {
                font-size: 12px;
                color: #888;
            }

            .natan-action-buttons {
                display: flex;
                gap: 8px;
                padding: 12px 14px;
                background: rgba(0, 0, 0, 0.2);
            }

            .natan-action-btn {
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .natan-action-btn.confirm {
                background: #10b981;
                color: #fff;
            }

            .natan-action-btn.confirm:hover {
                background: #059669;
            }

            .natan-action-btn.cancel {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
            }

            .natan-action-btn.cancel:hover {
                background: rgba(239, 68, 68, 0.3);
            }

            /* Input Area */
            .natan-chat-input-area {
                display: flex;
                gap: 8px;
                padding: 12px 16px;
                background: rgba(0, 0, 0, 0.3);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .natan-chat-input {
                flex: 1;
                padding: 10px 14px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 20px;
                color: #fff;
                font-size: 14px;
                outline: none;
                transition: all 0.2s;
            }

            .natan-chat-input:focus {
                border-color: #10b981;
                background: rgba(255, 255, 255, 0.15);
            }

            .natan-chat-input::placeholder {
                color: #666;
            }

            .natan-chat-send {
                width: 40px;
                height: 40px;
                background: #10b981;
                border: none;
                border-radius: 50%;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
                flex-shrink: 0;
            }

            .natan-chat-send:hover {
                background: #059669;
                transform: scale(1.05);
            }

            /* Typing indicator */
            .natan-typing {
                display: flex;
                gap: 4px;
                padding: 12px 16px;
                align-self: flex-start;
            }

            .natan-typing span {
                width: 8px;
                height: 8px;
                background: #10b981;
                border-radius: 50%;
                animation: typing 1.4s infinite;
            }

            .natan-typing span:nth-child(2) { animation-delay: 0.2s; }
            .natan-typing span:nth-child(3) { animation-delay: 0.4s; }

            @keyframes typing {
                0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
                30% { transform: translateY(-6px); opacity: 1; }
            }

            /* Welcome message */
            .natan-welcome {
                text-align: center;
                padding: 20px;
                color: #888;
            }

            .natan-welcome h4 {
                color: #fff;
                margin: 12px 0 8px;
            }

            .natan-welcome p {
                font-size: 13px;
                margin: 0;
            }

            .natan-suggestions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 16px;
                justify-content: center;
            }

            .natan-suggestion {
                padding: 8px 14px;
                background: rgba(16, 185, 129, 0.2);
                border: 1px solid rgba(16, 185, 129, 0.3);
                border-radius: 16px;
                color: #10b981;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .natan-suggestion:hover {
                background: rgba(16, 185, 129, 0.3);
            }

            /* Mobile responsive */
            @media (max-width: 480px) {
                .natan-tutor-chat {
                    width: 100%;
                    max-width: 100%;
                    height: 100%;
                    max-height: 100%;
                    bottom: 0;
                    right: 0;
                    border-radius: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Bind input events
     */
    private bindInputEvents(): void {
        const input = this.chatContainer?.querySelector('.natan-chat-input') as HTMLInputElement;
        const sendBtn = this.chatContainer?.querySelector('.natan-chat-send');

        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage(input.value);
                }
            });
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', () => {
                if (input) this.sendMessage(input.value);
            });
        }

        // Close button
        this.chatContainer?.querySelector('.natan-chat-close')?.addEventListener('click', () => {
            this.actionCallback?.('close', {});
        });

        // Action buttons delegation
        this.chatContainer?.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;

            // Suggestion click
            if (target.classList.contains('natan-suggestion')) {
                const text = target.textContent || '';
                if (input) {
                    input.value = text;
                    this.sendMessage(text);
                }
            }

            // Action confirm/cancel
            if (target.classList.contains('natan-action-btn')) {
                const action = target.dataset.action;
                const actionCode = target.dataset.code;
                if (action && this.actionCallback) {
                    this.actionCallback(action, { code: actionCode });
                }
            }
        });
    }

    /**
     * Send a message
     */
    private sendMessage(text: string): void {
        if (!text.trim()) return;

        const input = this.chatContainer?.querySelector('.natan-chat-input') as HTMLInputElement;
        if (input) input.value = '';

        // Add user message
        this.addMessage('user', text);

        // Trigger callback
        this.actionCallback?.('message', { text });
    }

    /**
     * Set callback for action events
     */
    public onAction(callback: ActionCallback): void {
        this.actionCallback = callback;
    }

    /**
     * Show the chat panel
     */
    public show(userState: UserState | null): void {
        if (!this.chatContainer) return;

        this.chatContainer.classList.remove('hidden');
        this.isVisible = true;
        this.updateState(userState);

        // Show welcome if no messages
        if (this.messages.length === 0) {
            this.showWelcome(userState);
        }

        // Focus input
        setTimeout(() => {
            const input = this.chatContainer?.querySelector('.natan-chat-input') as HTMLInputElement;
            input?.focus();
        }, 100);
    }

    /**
     * Hide the chat panel
     */
    public hide(): void {
        this.chatContainer?.classList.add('hidden');
        this.isVisible = false;
    }

    /**
     * Check if panel contains an element
     */
    public panelContains(node: Node): boolean {
        return this.chatContainer?.contains(node) ?? false;
    }

    /**
     * Update the UI with user state
     */
    public updateState(state: UserState | null): void {
        if (!this.chatContainer) return;

        // Update balance
        const balanceEl = this.chatContainer.querySelector('.balance-amount');
        if (balanceEl) {
            balanceEl.textContent = state?.balance?.toString() ?? '--';
        }
    }

    /**
     * Show welcome message
     */
    private showWelcome(state: UserState | null): void {
        if (!this.messagesArea) return;

        this.messagesArea.innerHTML = `
            <div class="natan-welcome">
                <div class="natan-chat-avatar" style="width: 60px; height: 60px; margin: 0 auto;">
                    <img src="/images/default/natan-face.png" alt="Natan" />
                </div>
                <h4>Ciao! Sono Natan 🎩</h4>
                <p>Posso aiutarti a usare la piattaforma.<br>Cosa vuoi fare?</p>
                <div class="natan-suggestions">
                    <button class="natan-suggestion">Voglio creare un EGI</button>
                    <button class="natan-suggestion">Come funziona il mint?</button>
                    <button class="natan-suggestion">Mostra le mie opere</button>
                    <button class="natan-suggestion">Aiutami a iniziare</button>
                </div>
            </div>
        `;
    }

    /**
     * Add a message to the chat
     */
    public addMessage(type: 'user' | 'natan' | 'system', content: string): void {
        if (!this.messagesArea) return;

        // Clear welcome on first message
        const welcome = this.messagesArea.querySelector('.natan-welcome');
        if (welcome) welcome.remove();

        const msg: ChatMessage = {
            id: Date.now().toString(),
            type,
            content,
            timestamp: new Date(),
        };
        this.messages.push(msg);

        const msgEl = document.createElement('div');
        msgEl.className = `natan-message ${type}`;
        msgEl.textContent = content;
        this.messagesArea.appendChild(msgEl);

        // Scroll to bottom
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
    }

    /**
     * Show action confirmation card
     */
    public showActionCard(action: string, description: string, cost: number, balance: number): void {
        if (!this.messagesArea) return;

        const cardHtml = `
            <div class="natan-action-card">
                <div class="natan-action-header">
                    <h4>📋 Conferma Azione</h4>
                </div>
                <div class="natan-action-body">
                    <p>${description}</p>
                    <div class="natan-action-cost">
                        <span class="cost">💎 ${cost} Egili</span>
                        <span class="balance">Saldo: ${balance} Egili</span>
                    </div>
                </div>
                <div class="natan-action-buttons">
                    <button class="natan-action-btn cancel" data-action="cancel_action" data-code="${action}">
                        ❌ Annulla
                    </button>
                    <button class="natan-action-btn confirm" data-action="confirm_action" data-code="${action}">
                        ✅ Conferma
                    </button>
                </div>
            </div>
        `;

        const cardEl = document.createElement('div');
        cardEl.innerHTML = cardHtml;
        this.messagesArea.appendChild(cardEl.firstElementChild!);
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
    }

    /**
     * Show typing indicator
     */
    public showTyping(): void {
        if (!this.messagesArea) return;

        // Remove existing typing indicator
        this.hideTyping();

        const typing = document.createElement('div');
        typing.className = 'natan-typing';
        typing.id = 'natan-typing-indicator';
        typing.innerHTML = '<span></span><span></span><span></span>';
        this.messagesArea.appendChild(typing);
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
    }

    /**
     * Hide typing indicator
     */
    public hideTyping(): void {
        const typing = this.messagesArea?.querySelector('#natan-typing-indicator');
        typing?.remove();
    }

    /**
     * Update mode display
     */
    public updateMode(mode: 'tutoring' | 'expert'): void {
        // Could update status text if needed
    }

    /**
     * Set loading state
     */
    public setLoading(loading: boolean): void {
        if (loading) {
            this.showTyping();
        } else {
            this.hideTyping();
        }
    }

    /**
     * Show explanation result
     */
    public showExplanation(response: TutorResponse): void {
        this.hideTyping();
        if (response.success && response.data) {
            this.addMessage('natan', response.data.explanation || response.message || 'Ecco la spiegazione.');
        } else {
            this.addMessage('natan', response.message || 'Mi dispiace, non sono riuscito a trovare una risposta.');
        }
    }

    /**
     * Show assistance result
     */
    public showAssistanceResult(response: TutorResponse): void {
        this.hideTyping();
        if (response.success) {
            let msg = '✅ ' + (response.message || 'Operazione completata!');
            if (response.cost_charged) {
                msg += `\n\n💎 Costo: ${response.cost_charged} Egili\n💰 Nuovo saldo: ${response.new_balance} Egili`;
            }
            this.addMessage('natan', msg);
        } else {
            this.addMessage('natan', '❌ ' + (response.message || 'Si è verificato un errore.'));
        }
    }

    /**
     * Show error message
     */
    public showError(message: string): void {
        this.hideTyping();
        this.addMessage('natan', '❌ ' + message);
    }
}

export default NatanTutorUI;
