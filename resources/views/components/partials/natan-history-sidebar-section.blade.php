{{--
    N.A.T.A.N. Chat History Section - Integrated in Enterprise Sidebar
    
    GDPR-Compliant history viewer integrated in main navigation
    Visible only in /pa/natan/chat route
--}}

<div class="border-t border-white/10 px-4 py-4">
    {{-- New Conversation Button --}}
    <button id="newConversationBtn"
        class="mb-3 flex w-full items-center justify-center gap-2 rounded-lg bg-[#D4A574] px-4 py-2.5 text-sm font-medium text-white transition-all hover:bg-[#c19563]">
        <span class="material-icons text-sm">add</span>
        <span>{{ __('natan.history.new_conversation') }}</span>
    </button>

    {{-- Chat History Collapse --}}
    <details class="group collapse collapse-arrow bg-transparent" id="chatHistoryCollapse">
        <summary
            class="cursor-pointer list-none rounded-md transition-colors duration-150 ease-in-out hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
            <div class="collapse-title flex items-center gap-3 px-3 py-2 text-sm font-medium">
                <span class="material-icons text-lg opacity-60 transition-opacity group-hover:opacity-100">history</span>
                <span class="flex-grow truncate">{{ __('natan.history.title') }}</span>
                <span id="historyCount" class="rounded-full bg-white/20 px-2 py-0.5 text-xs">0</span>
            </div>
        </summary>

        {{-- History Content --}}
        <div class="collapse-content max-h-96 overflow-y-auto px-2 pb-2 pt-2">
            {{-- Loading State --}}
            <div id="historyLoading" class="flex items-center justify-center py-6">
                <div class="flex flex-col items-center gap-2">
                    <div class="h-6 w-6 animate-spin rounded-full border-4 border-white/30 border-t-white"></div>
                    <p class="text-xs text-white/70">{{ __('natan.history.loading') }}</p>
                </div>
            </div>

            {{-- Empty State --}}
            <div id="historyEmpty" class="hidden py-6 text-center">
                <span class="material-icons mb-2 text-3xl text-white/30">chat_bubble_outline</span>
                <p class="mb-1 text-xs font-medium text-white/80">{{ __('natan.history.empty') }}</p>
                <p class="text-xs text-white/50">{{ __('natan.history.empty_hint') }}</p>
            </div>

            {{-- Sessions List --}}
            <div id="historyList" class="hidden space-y-2">
                {{-- Sessions will be inserted here by JavaScript --}}
            </div>

            {{-- Error State --}}
            <div id="historyError" class="hidden py-6 text-center">
                <span class="material-icons mb-2 text-3xl text-red-400">error_outline</span>
                <p class="text-xs font-medium text-red-300" id="historyErrorMessage"></p>
            </div>
        </div>
    </details>
</div>

{{-- Session Card Template (hidden, used by JS) --}}
<template id="sessionCardTemplate">
    <div class="session-card group cursor-pointer rounded-lg border border-white/10 bg-white/5 p-2.5 transition-all hover:border-[#D4A574] hover:bg-white/10"
        data-session-id="">
        <div class="mb-1.5 flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs text-white/50" data-session-date></p>
                <p class="truncate text-xs font-medium text-white/70" data-message-count></p>
            </div>
            <button
                class="delete-session-btn flex-shrink-0 rounded p-1 text-white/30 opacity-0 transition-all hover:bg-red-500/20 hover:text-red-300 group-hover:opacity-100"
                data-delete-session-id="" title="{{ __('natan.history.delete_session') }}">
                <span class="material-icons text-sm">delete</span>
            </button>
        </div>
        <div class="mb-1 flex items-center gap-1">
            <span class="material-icons text-xs text-[#D4A574]">chat</span>
            <p class="truncate text-xs font-medium text-white/60" data-first-message></p>
        </div>
        <div class="line-clamp-2 text-xs leading-tight text-white/50" data-preview></div>
        <div class="mt-1.5 flex items-center justify-between gap-2">
            <span class="text-[10px] text-white/40" data-persona></span>
            <div class="flex items-center gap-2 text-[10px]">
                <span class="text-white/30" data-tokens></span>
                <span class="font-medium text-[#D4A574]" data-cost></span>
            </div>
        </div>
    </div>
</template>

<style>
    /* Ensure content doesn't overflow */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom scrollbar for history list */
    .collapse-content::-webkit-scrollbar {
        width: 6px;
    }

    .collapse-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }

    .collapse-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .collapse-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    // N.A.T.A.N. History Sidebar Section JavaScript (Vanilla JS)
    // Integrated in Enterprise Sidebar
    (function() {
        'use strict';

        const NatanHistorySidebar = {
            historyList: null,
            historyLoading: null,
            historyEmpty: null,
            historyError: null,
            historyCount: null,
            newConversationBtn: null,
            sessions: [],

            init() {
                // Cache DOM elements
                this.historyList = document.getElementById('historyList');
                this.historyLoading = document.getElementById('historyLoading');
                this.historyEmpty = document.getElementById('historyEmpty');
                this.historyError = document.getElementById('historyError');
                this.historyCount = document.getElementById('historyCount');
                this.newConversationBtn = document.getElementById('newConversationBtn');

                // Bind events
                this.newConversationBtn?.addEventListener('click', () => this.newConversation());

                // Load history on init
                this.loadHistory();
            },

            async loadHistory() {
                this.showLoading();

                try {
                    const response = await fetch('/pa/natan/chat/history', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || ''
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.sessions = data.sessions || [];
                        this.renderSessions();
                    } else {
                        this.showError(this.getErrorMessage(data.error));
                    }
                } catch (error) {
                    console.error('[History] Load failed:', error);
                    this.showError('{{ __('natan.errors.generic') }}');
                }
            },

            renderSessions() {
                if (this.sessions.length === 0) {
                    this.showEmpty();
                    return;
                }

                // Update count badge
                if (this.historyCount) {
                    this.historyCount.textContent = this.sessions.length;
                }

                this.historyList.innerHTML = '';
                const template = document.getElementById('sessionCardTemplate');

                this.sessions.forEach(session => {
                    const card = template.content.cloneNode(true);
                    const cardDiv = card.querySelector('.session-card');

                    // Set data
                    cardDiv.dataset.sessionId = session.session_id;
                    card.querySelector('[data-session-date]').textContent = this.formatDate(session
                        .session_start);
                    card.querySelector('[data-message-count]').textContent =
                        `${session.message_count} {{ __('natan.history.message_count', ['count' => '']) }}`
                        .replace(/\d+/, session.message_count);
                    card.querySelector('[data-first-message]').textContent =
                        '{{ __('natan.history.first_message') }}';
                    card.querySelector('[data-preview]').textContent = session.preview || '---';
                    card.querySelector('[data-persona]').textContent = session.first_persona ?
                        `{{ __('natan.history.with_persona', ['persona' => '']) }}`.replace('', session
                            .first_persona) : '';

                    // ✅ NEW: Show tokens and cost
                    card.querySelector('[data-tokens]').textContent = session.total_tokens ?
                        `${session.total_tokens.toLocaleString()} tokens` : '';
                    card.querySelector('[data-cost]').textContent = session.total_cost_eur ?
                        `€${session.total_cost_eur.toFixed(4)}` : '€0.00';

                    // Set delete button ID
                    const deleteBtn = card.querySelector('.delete-session-btn');
                    deleteBtn.dataset.deleteSessionId = session.session_id;

                    // Events
                    cardDiv.addEventListener('click', (e) => {
                        if (!e.target.closest('.delete-session-btn')) {
                            this.loadSession(session.session_id);
                        }
                    });

                    deleteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.deleteSession(session.session_id);
                    });

                    this.historyList.appendChild(card);
                });

                this.showList();
            },

            async loadSession(sessionId) {
                console.log('[History] Loading session:', sessionId);

                try {
                    const response = await fetch(`/pa/natan/chat/session/${sessionId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || ''
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Clear current chat
                        if (typeof window.NatanChat !== 'undefined') {
                            window.NatanChat.clearChat();

                            // Load messages into chat
                            data.messages.forEach(msg => {
                                if (msg.role === 'user') {
                                    window.NatanChat.addMessage('user', msg.content);
                                } else {
                                    window.NatanChat.addMessage('assistant', msg.content, msg
                                        .persona);
                                }
                            });
                        }
                    } else {
                        alert(this.getErrorMessage(data.error));
                    }
                } catch (error) {
                    console.error('[History] Load session failed:', error);
                    alert('{{ __('natan.errors.generic') }}');
                }
            },

            async deleteSession(sessionId) {
                if (!confirm('{{ __('natan.history.delete_confirm') }}')) {
                    return;
                }

                try {
                    const response = await fetch(`/pa/natan/chat/session/${sessionId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || ''
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Remove from UI
                        this.sessions = this.sessions.filter(s => s.session_id !== sessionId);
                        this.renderSessions();

                        console.log('[History] Session deleted successfully');
                    } else {
                        alert(this.getErrorMessage(data.error));
                    }
                } catch (error) {
                    console.error('[History] Delete failed:', error);
                    alert('{{ __('natan.history.deleted_error') }}');
                }
            },

            newConversation() {
                if (typeof window.NatanChat !== 'undefined') {
                    window.NatanChat.clearChat();
                }
            },

            showLoading() {
                this.historyLoading?.classList.remove('hidden');
                this.historyList?.classList.add('hidden');
                this.historyEmpty?.classList.add('hidden');
                this.historyError?.classList.add('hidden');
            },

            showList() {
                this.historyLoading?.classList.add('hidden');
                this.historyList?.classList.remove('hidden');
                this.historyEmpty?.classList.add('hidden');
                this.historyError?.classList.add('hidden');
            },

            showEmpty() {
                this.historyLoading?.classList.add('hidden');
                this.historyList?.classList.add('hidden');
                this.historyEmpty?.classList.remove('hidden');
                this.historyError?.classList.add('hidden');

                // Update count badge to 0
                if (this.historyCount) {
                    this.historyCount.textContent = '0';
                }
            },

            showError(message) {
                this.historyLoading?.classList.add('hidden');
                this.historyList?.classList.add('hidden');
                this.historyEmpty?.classList.add('hidden');
                this.historyError?.classList.remove('hidden');

                const errorMsg = document.getElementById('historyErrorMessage');
                if (errorMsg) errorMsg.textContent = message;
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('it-IT', options);
            },

            getErrorMessage(errorCode) {
                const messages = {
                    'consent_required': '{{ __('natan.history.no_consent') }}',
                    'unauthorized': '{{ __('natan.history.unauthorized') }}',
                    'retrieval_failed': '{{ __('natan.errors.generic') }}'
                };
                return messages[errorCode] || messages.retrieval_failed;
            }
        };

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => NatanHistorySidebar.init());
        } else {
            NatanHistorySidebar.init();
        }

        // Expose globally for external access if needed
        window.NatanHistorySidebar = NatanHistorySidebar;
    })();
</script>
