/**
 * AI Sidebar - Onboarding Assistant
 * Shopify-style: Chat AI + Stripe-style checklist
 * 
 * P0-0: Vanilla JS ONLY (NO Alpine/Livewire)
 * 
 * @author EGI Team
 */

(function() {
    'use strict';

    // Sidebar state
    const state = {
        isOpen: false,
        sidebar: null,
        toggleBtn: null,
        closeBtn: null,
        chatForm: null,
        chatInput: null,
        chatContainer: null,
        messageEl: null,
        quickActionsEl: null,
        checklistEl: null,
        userId: null,
        userType: null,
        checklist: [],
        isLoading: false
    };

    /**
     * Initialize sidebar when DOM ready
     */
    function init() {
        // Get DOM elements
        state.sidebar = document.getElementById('ai-sidebar');
        state.toggleBtn = document.getElementById('ai-sidebar-toggle');
        state.closeBtn = document.getElementById('ai-sidebar-close');
        state.chatForm = document.getElementById('ai-sidebar-form');
        state.chatInput = document.getElementById('ai-sidebar-input');
        state.chatContainer = document.getElementById('ai-sidebar-chat');
        state.messageEl = document.getElementById('ai-sidebar-message');
        state.quickActionsEl = document.getElementById('ai-sidebar-quick-actions');
        state.checklistEl = document.getElementById('ai-sidebar-checklist');

        if (!state.sidebar || !state.toggleBtn) {
            return; // Component not on this page
        }

        // Get data attributes
        state.userId = state.sidebar.dataset.userId;
        state.userType = state.sidebar.dataset.userType;
        
        try {
            state.checklist = JSON.parse(state.sidebar.dataset.checklist || '[]');
        } catch (e) {
            console.error('AI Sidebar: Failed to parse checklist data', e);
            state.checklist = [];
        }

        // Bind events
        bindEvents();

        // Generate initial AI message based on checklist
        generateAIMessage();

        // Generate quick actions
        generateQuickActions();

        // Check localStorage for previous state
        const savedState = localStorage.getItem('ai-sidebar-open');
        if (savedState === 'true') {
            openSidebar();
        }
    }

    /**
     * Bind all event listeners
     */
    function bindEvents() {
        // Toggle button
        state.toggleBtn.addEventListener('click', toggleSidebar);

        // Close button
        if (state.closeBtn) {
            state.closeBtn.addEventListener('click', closeSidebar);
        }

        // Chat form
        if (state.chatForm) {
            state.chatForm.addEventListener('submit', handleChatSubmit);
        }

        // Checklist items
        if (state.checklistEl) {
            state.checklistEl.addEventListener('click', handleChecklistClick);
        }

        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.isOpen) {
                closeSidebar();
            }
        });

        // Close on outside click (mobile)
        document.addEventListener('click', function(e) {
            if (state.isOpen && 
                window.innerWidth < 768 && 
                !state.sidebar.contains(e.target) && 
                !state.toggleBtn.contains(e.target)) {
                closeSidebar();
            }
        });
    }

    /**
     * Toggle sidebar open/close
     */
    function toggleSidebar() {
        if (state.isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    /**
     * Open sidebar
     */
    function openSidebar() {
        state.isOpen = true;
        state.sidebar.classList.remove('collapsed');
        state.sidebar.setAttribute('aria-hidden', 'false');
        state.toggleBtn.dataset.sidebarOpen = 'true';
        localStorage.setItem('ai-sidebar-open', 'true');
        
        // Focus input
        setTimeout(() => {
            if (state.chatInput) {
                state.chatInput.focus();
            }
        }, 300);
    }

    /**
     * Close sidebar
     */
    function closeSidebar() {
        state.isOpen = false;
        state.sidebar.classList.add('collapsed');
        state.sidebar.setAttribute('aria-hidden', 'true');
        state.toggleBtn.dataset.sidebarOpen = 'false';
        localStorage.setItem('ai-sidebar-open', 'false');
    }

    /**
     * Generate AI message based on checklist status
     * This is programmatic, not real AI
     */
    function generateAIMessage() {
        if (!state.messageEl || !state.checklist.length) return;

        const completed = state.checklist.filter(item => item.completed).length;
        const total = state.checklist.length;
        const percent = Math.round((completed / total) * 100);

        let message = '';

        if (percent === 0) {
            // Just started
            message = getTranslation('ai_sidebar.messages.welcome', {
                userType: state.userType
            });
        } else if (percent < 50) {
            // Making progress
            const nextItem = state.checklist.find(item => !item.completed);
            message = getTranslation('ai_sidebar.messages.progress_low', {
                completed: completed,
                total: total,
                nextStep: nextItem ? getTranslation(nextItem.title_key) : ''
            });
        } else if (percent < 100) {
            // Almost there
            const remaining = total - completed;
            message = getTranslation('ai_sidebar.messages.progress_high', {
                remaining: remaining
            });
        } else {
            // All done!
            message = getTranslation('ai_sidebar.messages.complete');
        }

        state.messageEl.textContent = message;
    }

    /**
     * Generate quick action buttons for incomplete items
     */
    function generateQuickActions() {
        if (!state.quickActionsEl) return;

        const incompleteItems = state.checklist.filter(item => !item.completed).slice(0, 3);
        
        if (incompleteItems.length === 0) {
            state.quickActionsEl.innerHTML = `
                <div class="text-center py-4">
                    <div class="text-2xl mb-2">🎉</div>
                    <p class="text-sm text-gray-400">${getTranslation('ai_sidebar.messages.all_done')}</p>
                </div>
            `;
            return;
        }

        let html = '<p class="text-xs text-gray-500 mb-2">' + getTranslation('ai_sidebar.quick_actions_label') + '</p>';

        incompleteItems.forEach(item => {
            html += `
                <button 
                    class="quick-action-btn w-full text-left rounded-lg border border-gray-700 bg-gray-800/50 px-3 py-2.5 text-sm transition-all hover:border-indigo-500/50 hover:bg-indigo-900/20"
                    data-step-id="${item.id}"
                    data-action="${item.action || ''}"
                    data-modal="${item.modal || ''}"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-base">${item.icon || '→'}</span>
                        <span class="text-gray-200">${getTranslation(item.title_key)}</span>
                    </div>
                </button>
            `;
        });

        state.quickActionsEl.innerHTML = html;

        // Bind quick action events
        state.quickActionsEl.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                handleStepAction(this.dataset);
            });
        });
    }

    /**
     * Handle checklist item click
     */
    function handleChecklistClick(e) {
        const item = e.target.closest('.checklist-item');
        if (!item) return;

        // Don't do anything for completed items
        if (item.classList.contains('completed')) return;

        handleStepAction(item.dataset);
    }

    /**
     * Handle step action (open modal, navigate, etc.)
     */
    function handleStepAction(dataset) {
        const stepId = dataset.stepId;
        const action = dataset.action;
        const modal = dataset.modal;

        // Close sidebar on mobile
        if (window.innerWidth < 768) {
            closeSidebar();
        }

        // Handle different actions
        if (modal) {
            // Open specific modal
            openActionModal(modal, stepId);
        } else if (action) {
            // Execute action (URL navigation, function call, etc.)
            executeAction(action, stepId);
        }
    }

    /**
     * Open action modal
     */
    function openActionModal(modalId, stepId) {
        // Check for common modal systems
        const modalEl = document.getElementById(modalId);
        
        if (modalEl) {
            // Standard modal - add 'open' class or remove 'hidden'
            if (modalEl.classList.contains('hidden')) {
                modalEl.classList.remove('hidden');
                modalEl.classList.add('flex');
            }
            
            // Dispatch custom event for modals that listen
            modalEl.dispatchEvent(new CustomEvent('modal:open', { 
                detail: { stepId: stepId } 
            }));
        }

        // Check for window-level modal handlers
        if (window[modalId] && typeof window[modalId].open === 'function') {
            window[modalId].open();
        }

        // Specific modal handlers
        switch (modalId) {
            case 'avatar-upload-modal':
                if (window.avatarModal) window.avatarModal.open();
                break;
            case 'banner-upload-modal':
                if (window.bannerModal) window.bannerModal.open();
                break;
            case 'payment-modal':
                if (window.paymentModal) window.paymentModal.open();
                break;
            case 'collection-modal':
                if (window.collectionModal) window.collectionModal.open();
                break;
            // Add more modal handlers as needed
        }
    }

    /**
     * Execute action (URL, function)
     */
    function executeAction(action, stepId) {
        // Check if it's a URL
        if (action.startsWith('/') || action.startsWith('http')) {
            window.location.href = action;
            return;
        }

        // Check if it's a window function
        if (window[action] && typeof window[action] === 'function') {
            window[action](stepId);
            return;
        }

        console.warn('AI Sidebar: Unknown action', action);
    }

    /**
     * Handle chat form submit (real AI question)
     */
    async function handleChatSubmit(e) {
        e.preventDefault();

        const message = state.chatInput.value.trim();
        if (!message || state.isLoading) return;

        state.isLoading = true;
        state.chatInput.disabled = true;

        // Add user message to chat
        addChatMessage(message, 'user');
        state.chatInput.value = '';

        // Show typing indicator
        const typingId = showTypingIndicator();

        try {
            // Call Art Advisor API
            const response = await fetch('/art-advisor/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'text/event-stream'
                },
                body: JSON.stringify({
                    message: message,
                    context: {
                        user_type: state.userType,
                        user_id: state.userId,
                        mode: 'onboarding_help'
                    },
                    expert: 'platform'
                })
            });

            // Remove typing indicator
            removeTypingIndicator(typingId);

            if (response.ok) {
                // Handle SSE stream
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let fullMessage = '';
                const messageId = addChatMessage('', 'assistant');

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value);
                    const lines = chunk.split('\n');

                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const data = JSON.parse(line.slice(6));
                                if (data.content) {
                                    fullMessage += data.content;
                                    updateChatMessage(messageId, fullMessage);
                                }
                            } catch (e) {
                                // Not JSON, might be raw text
                                fullMessage += line.slice(6);
                                updateChatMessage(messageId, fullMessage);
                            }
                        }
                    }
                }
            } else {
                addChatMessage(getTranslation('ai_sidebar.errors.request_failed'), 'error');
            }
        } catch (error) {
            removeTypingIndicator(typingId);
            console.error('AI Sidebar: Chat error', error);
            addChatMessage(getTranslation('ai_sidebar.errors.connection_error'), 'error');
        } finally {
            state.isLoading = false;
            state.chatInput.disabled = false;
            state.chatInput.focus();
        }
    }

    /**
     * Add message to chat container
     */
    function addChatMessage(content, type) {
        const messageId = 'msg-' + Date.now();
        const div = document.createElement('div');
        div.id = messageId;
        div.className = `chat-message ${type} mb-3 rounded-lg p-3 text-sm`;

        if (type === 'user') {
            div.className += ' bg-indigo-600/30 ml-8 text-white';
            div.innerHTML = `<p>${escapeHtml(content)}</p>`;
        } else if (type === 'assistant') {
            div.className += ' bg-gray-800 mr-4 text-gray-200';
            div.innerHTML = `
                <div class="mb-1 flex items-center gap-1 text-xs text-indigo-400">
                    <span>✨</span>
                    <span>${getTranslation('ai_sidebar.assistant_name')}</span>
                </div>
                <div class="message-content">${content}</div>
            `;
        } else if (type === 'error') {
            div.className += ' bg-red-900/30 text-red-300';
            div.innerHTML = `<p>⚠️ ${escapeHtml(content)}</p>`;
        }

        state.chatContainer.appendChild(div);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;

        return messageId;
    }

    /**
     * Update existing chat message
     */
    function updateChatMessage(messageId, content) {
        const messageEl = document.getElementById(messageId);
        if (messageEl) {
            const contentEl = messageEl.querySelector('.message-content');
            if (contentEl) {
                contentEl.innerHTML = formatMarkdown(content);
            }
        }
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;
    }

    /**
     * Show typing indicator
     */
    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'typing-indicator mb-3 rounded-lg bg-gray-800 p-3 mr-4';
        div.innerHTML = `
            <div class="flex items-center gap-1 text-xs text-indigo-400">
                <span>✨</span>
                <span>${getTranslation('ai_sidebar.assistant_name')}</span>
            </div>
            <div class="flex gap-1 mt-2">
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 0ms"></span>
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 150ms"></span>
                <span class="h-2 w-2 rounded-full bg-gray-500 animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        `;
        state.chatContainer.appendChild(div);
        state.chatContainer.scrollTop = state.chatContainer.scrollHeight;
        return id;
    }

    /**
     * Remove typing indicator
     */
    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) {
            el.remove();
        }
    }

    /**
     * Simple markdown formatter
     */
    function formatMarkdown(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-gray-700 px-1 rounded">$1</code>')
            .replace(/\n/g, '<br>');
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Get translation (from window or fallback)
     */
    function getTranslation(key, params = {}) {
        // Check if Laravel translations are available
        if (window.translations && window.translations[key]) {
            let text = window.translations[key];
            // Replace placeholders
            Object.keys(params).forEach(param => {
                text = text.replace(`:${param}`, params[param]);
            });
            return text;
        }

        // Fallback translations
        const fallbacks = {
            'ai_sidebar.messages.welcome': 'Welcome! Let me help you complete your profile setup.',
            'ai_sidebar.messages.progress_low': `Great start! You've completed ${params.completed || 0} of ${params.total || 0} steps. Next: ${params.nextStep || 'continue setup'}`,
            'ai_sidebar.messages.progress_high': `Almost there! Just ${params.remaining || 0} more steps to go.`,
            'ai_sidebar.messages.complete': 'Congratulations! Your profile is fully set up. 🎉',
            'ai_sidebar.messages.all_done': 'All setup steps completed!',
            'ai_sidebar.quick_actions_label': 'Suggested next steps:',
            'ai_sidebar.assistant_name': 'EGI Assistant',
            'ai_sidebar.errors.request_failed': 'Request failed. Please try again.',
            'ai_sidebar.errors.connection_error': 'Connection error. Please check your internet.'
        };

        return fallbacks[key] || key;
    }

    /**
     * Refresh checklist data from server
     */
    async function refreshChecklist() {
        try {
            const response = await fetch(`/api/onboarding/checklist/${state.userType}/${state.userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                state.checklist = data.checklist || [];
                state.sidebar.dataset.checklist = JSON.stringify(state.checklist);
                
                // Re-render
                generateAIMessage();
                generateQuickActions();
                renderChecklist();
            }
        } catch (error) {
            console.error('AI Sidebar: Failed to refresh checklist', error);
        }
    }

    /**
     * Re-render checklist (after refresh)
     */
    function renderChecklist() {
        if (!state.checklistEl) return;

        const completed = state.checklist.filter(item => item.completed).length;
        const total = state.checklist.length;
        const percent = Math.round((completed / total) * 100);

        // Update progress
        const progressFill = state.sidebar.querySelector('.progress-fill');
        if (progressFill) {
            progressFill.style.width = percent + '%';
        }

        const progressText = state.sidebar.querySelector('[data-progress-text]');
        if (progressText) {
            progressText.textContent = `${completed}/${total}`;
        }

        // Update badge on toggle button
        const badge = state.toggleBtn.querySelector('span');
        if (badge) {
            if (percent === 100) {
                badge.innerHTML = `<svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>`;
                badge.className = 'absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-white';
            } else {
                badge.textContent = total - completed;
                badge.className = 'absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white';
            }
        }
    }

    // Public API
    window.aiSidebar = {
        open: openSidebar,
        close: closeSidebar,
        toggle: toggleSidebar,
        refresh: refreshChecklist,
        getState: () => ({ isOpen: state.isOpen, checklist: state.checklist })
    };

    // Initialize when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Listen for checklist updates
    window.addEventListener('checklist:updated', refreshChecklist);
    window.addEventListener('modal:closed', refreshChecklist);

})();
