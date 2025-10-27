{{-- 
    N.A.T.A.N. Chat History Sidebar Component
    
    GDPR-Compliant history viewer with session management
    
    Features:
    - Toggle sidebar (collapsed by default on mobile)
    - List of past sessions with preview
    - Load session (restores conversation)
    - Delete session (GDPR right to be forgotten)
    - Vanilla JS for API calls
--}}

<div id="historySidebar" class="fixed inset-y-0 left-0 z-50 w-80 transform bg-white shadow-2xl transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 -translate-x-full">
    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-[#1B365D] to-[#2D5016] p-4">
        <div class="flex items-center gap-3">
            <span class="material-icons text-white">history</span>
            <h3 class="text-lg font-bold text-white">{{ __('natan.history.title') }}</h3>
        </div>
        <button id="closeSidebarBtn" class="rounded-lg p-1 text-white hover:bg-white/20 lg:hidden">
            <span class="material-icons">close</span>
        </button>
    </div>

    {{-- New Conversation Button --}}
    <div class="border-b border-gray-200 p-4">
        <button id="newConversationBtn" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#D4A574] px-4 py-2 font-medium text-white transition-all hover:bg-[#c19563]">
            <span class="material-icons text-sm">add</span>
            <span>{{ __('natan.history.new_conversation') }}</span>
        </button>
    </div>

    {{-- Sessions List --}}
    <div id="historyContent" class="h-[calc(100vh-180px)] overflow-y-auto">
        {{-- Loading State --}}
        <div id="historyLoading" class="flex items-center justify-center p-8">
            <div class="flex flex-col items-center gap-3">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-[#1B365D] border-t-transparent"></div>
                <p class="text-sm text-gray-600">{{ __('natan.history.loading') }}</p>
            </div>
        </div>

        {{-- Empty State --}}
        <div id="historyEmpty" class="hidden p-6 text-center">
            <span class="material-icons mb-3 text-4xl text-gray-400">chat_bubble_outline</span>
            <p class="mb-2 font-medium text-gray-700">{{ __('natan.history.empty') }}</p>
            <p class="text-sm text-gray-500">{{ __('natan.history.empty_hint') }}</p>
        </div>

        {{-- Sessions List Container --}}
        <div id="historyList" class="hidden space-y-2 p-4">
            {{-- Sessions will be inserted here by JavaScript --}}
        </div>

        {{-- Error State --}}
        <div id="historyError" class="hidden p-6 text-center">
            <span class="material-icons mb-3 text-4xl text-red-400">error_outline</span>
            <p class="mb-2 font-medium text-red-700" id="historyErrorMessage"></p>
        </div>
    </div>
</div>

{{-- Overlay (mobile only) --}}
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

{{-- Toggle Button (floating, always visible) --}}
<button id="toggleHistoryBtn" class="fixed left-4 top-20 z-30 flex items-center gap-2 rounded-lg bg-[#1B365D] px-3 py-2 shadow-lg transition-all hover:bg-[#2D5016] lg:left-4">
    <span class="material-icons text-white">history</span>
    <span class="hidden text-sm font-medium text-white sm:inline">{{ __('natan.history.toggle') }}</span>
</button>

{{-- Session Card Template (hidden, used by JS) --}}
<template id="sessionCardTemplate">
    <div class="session-card group cursor-pointer rounded-lg border border-gray-200 bg-white p-3 transition-all hover:border-[#1B365D] hover:shadow-md" data-session-id="">
        <div class="mb-2 flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-500" data-session-date></p>
                <p class="text-sm font-medium text-gray-700" data-message-count></p>
            </div>
            <button class="delete-session-btn rounded p-1 text-gray-400 opacity-0 transition-all hover:bg-red-100 hover:text-red-600 group-hover:opacity-100" data-delete-session-id="" title="{{ __('natan.history.delete_session') }}">
                <span class="material-icons text-sm">delete</span>
            </button>
        </div>
        <div class="mb-2 flex items-center gap-1">
            <span class="material-icons text-xs text-[#D4A574]">chat</span>
            <p class="text-xs font-medium text-gray-600" data-first-message></p>
        </div>
        <div class="line-clamp-2 text-xs text-gray-500" data-preview></div>
        <div class="mt-2 flex items-center gap-1">
            <span class="text-[10px] text-gray-400" data-persona></span>
        </div>
    </div>
</template>

<style>
    /* Smooth sidebar transitions */
    #historySidebar {
        transition: transform 0.3s ease-in-out;
    }
    
    #historySidebar.sidebar-open {
        transform: translateX(0);
    }
    
    /* Ensure content doesn't overflow */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
// N.A.T.A.N. History Sidebar JavaScript (Vanilla JS)
(function() {
    'use strict';

    const HistorySidebar = {
        sidebar: null,
        overlay: null,
        toggleBtn: null,
        closeBtn: null,
        newConversationBtn: null,
        historyList: null,
        historyLoading: null,
        historyEmpty: null,
        historyError: null,
        sessions: [],

        init() {
            // Cache DOM elements
            this.sidebar = document.getElementById('historySidebar');
            this.overlay = document.getElementById('sidebarOverlay');
            this.toggleBtn = document.getElementById('toggleHistoryBtn');
            this.closeBtn = document.getElementById('closeSidebarBtn');
            this.newConversationBtn = document.getElementById('newConversationBtn');
            this.historyList = document.getElementById('historyList');
            this.historyLoading = document.getElementById('historyLoading');
            this.historyEmpty = document.getElementById('historyEmpty');
            this.historyError = document.getElementById('historyError');

            // Bind events
            this.toggleBtn?.addEventListener('click', () => this.toggleSidebar());
            this.closeBtn?.addEventListener('click', () => this.closeSidebar());
            this.overlay?.addEventListener('click', () => this.closeSidebar());
            this.newConversationBtn?.addEventListener('click', () => this.newConversation());

            // Load history on init
            this.loadHistory();
        },

        toggleSidebar() {
            this.sidebar?.classList.toggle('sidebar-open');
            this.overlay?.classList.toggle('hidden');
        },

        closeSidebar() {
            this.sidebar?.classList.remove('sidebar-open');
            this.overlay?.classList.add('hidden');
        },

        async loadHistory() {
            this.showLoading();

            try {
                const response = await fetch('/pa/natan/chat/history', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
                this.showError('{{ __("natan.errors.generic") }}');
            }
        },

        renderSessions() {
            if (this.sessions.length === 0) {
                this.showEmpty();
                return;
            }

            this.historyList.innerHTML = '';
            const template = document.getElementById('sessionCardTemplate');

            this.sessions.forEach(session => {
                const card = template.content.cloneNode(true);
                const cardDiv = card.querySelector('.session-card');
                
                // Set data
                cardDiv.dataset.sessionId = session.session_id;
                card.querySelector('[data-session-date]').textContent = this.formatDate(session.session_start);
                card.querySelector('[data-message-count]').textContent = `${session.message_count} {{ __('natan.history.message_count', ['count' => '']) }}`.replace(/\d+/, session.message_count);
                card.querySelector('[data-first-message]').textContent = '{{ __("natan.history.first_message") }}';
                card.querySelector('[data-preview]').textContent = session.preview || '---';
                card.querySelector('[data-persona]').textContent = session.first_persona ? `{{ __('natan.history.with_persona', ['persona' => '']) }}`.replace('', session.first_persona) : '';
                
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
                                window.NatanChat.addMessage('assistant', msg.content, msg.persona);
                            }
                        });
                    }

                    this.closeSidebar();
                } else {
                    alert(this.getErrorMessage(data.error));
                }
            } catch (error) {
                console.error('[History] Load session failed:', error);
                alert('{{ __("natan.errors.generic") }}');
            }
        },

        async deleteSession(sessionId) {
            if (!confirm('{{ __("natan.history.delete_confirm") }}')) {
                return;
            }

            try {
                const response = await fetch(`/pa/natan/chat/session/${sessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove from UI
                    this.sessions = this.sessions.filter(s => s.session_id !== sessionId);
                    this.renderSessions();
                    
                    // Show success message (optional toast)
                    console.log('[History] Session deleted successfully');
                } else {
                    alert(this.getErrorMessage(data.error));
                }
            } catch (error) {
                console.error('[History] Delete failed:', error);
                alert('{{ __("natan.history.deleted_error") }}');
            }
        },

        newConversation() {
            if (typeof window.NatanChat !== 'undefined') {
                window.NatanChat.clearChat();
            }
            this.closeSidebar();
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
                'consent_required': '{{ __("natan.history.no_consent") }}',
                'unauthorized': '{{ __("natan.history.unauthorized") }}',
                'retrieval_failed': '{{ __("natan.errors.generic") }}'
            };
            return messages[errorCode] || messages.retrieval_failed;
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => HistorySidebar.init());
    } else {
        HistorySidebar.init();
    }

    // Expose globally for external access if needed
    window.HistorySidebar = HistorySidebar;
})();
</script>

