<x-pa-layout noHero="true">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('pa.acts.index') }}"
                            class="rounded-lg bg-white p-2 shadow-sm transition-all hover:shadow-md">
                            <span class="material-icons text-[#1B365D]">arrow_back</span>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-[#1B365D]">N.A.T.A.N. Chat AI</h1>
                            <p class="text-sm text-gray-600">Assistente intelligente per i tuoi atti amministrativi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 rounded-lg bg-green-100 px-4 py-2">
                        <span class="material-icons text-sm text-green-600">check_circle</span>
                        <span class="text-sm font-medium text-green-800">AI Attiva</span>
                    </div>
                </div>
            </div>

            {{-- Chat Container --}}
            <div id="natanChat" class="mx-auto max-w-4xl">

                {{-- Chat Window --}}
                <div class="mb-4 rounded-2xl bg-white shadow-xl">

                    {{-- Chat Header --}}
                    <div
                        class="rounded-t-2xl border-b border-gray-200 bg-gradient-to-r from-[#1B365D] to-[#2D5016] p-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                                <span class="material-icons text-2xl text-white">smart_toy</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">N.A.T.A.N.</h2>
                                <p class="text-xs text-white/80">Nodo di Analisi e Tracciamento Atti Notarizzati</p>
                            </div>
                        </div>
                    </div>

                    {{-- Messages Container --}}
                    <div id="chatMessages" class="h-[600px] space-y-4 overflow-y-auto p-6">

                        {{-- Welcome Message (will be hidden after first message) --}}
                        <div id="welcomeMessage" class="flex h-full flex-col items-center justify-center text-center">
                            <div
                                class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#1B365D] to-[#2D5016]">
                                <span class="material-icons text-4xl text-white">smart_toy</span>
                            </div>
                            <h3 class="mb-2 text-2xl font-bold text-[#1B365D]">Ciao! Sono N.A.T.A.N.</h3>
                            <p class="mb-8 max-w-md text-gray-600">
                                Posso aiutarti ad analizzare i tuoi atti amministrativi, rispondere a domande specifiche
                                e fornirti insight strategici. Prova a chiedermi qualcosa!
                            </p>

                            {{-- Suggested Questions --}}
                            <div class="w-full max-w-2xl">
                                <p class="mb-4 text-sm font-medium text-gray-700">Domande suggerite:</p>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @foreach ($suggested_questions as $question)
                                        <button onclick="NatanChat.sendSuggestedMessage('{{ addslashes($question) }}')"
                                            class="rounded-lg border-2 border-gray-200 bg-white p-4 text-left text-sm transition-all hover:border-[#2D5016] hover:shadow-md">
                                            <span class="material-icons mr-2 text-sm text-[#2D5016]">help_outline</span>
                                            {{ $question }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Area --}}
                    <div class="rounded-b-2xl border-t border-gray-200 bg-gray-50 p-4">
                        <form id="chatForm" class="flex gap-2">
                            <input type="text" id="userInput" placeholder="Scrivi la tua domanda a N.A.T.A.N..."
                                class="flex-1 rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-[#2D5016] focus:outline-none focus:ring-2 focus:ring-[#2D5016]/20 disabled:cursor-not-allowed disabled:bg-gray-100">
                            <button type="submit" id="sendBtn"
                                class="flex items-center gap-2 rounded-xl bg-[#2D5016] px-6 py-3 font-medium text-white transition-all hover:bg-[#3D6026] disabled:cursor-not-allowed disabled:opacity-50">
                                <span id="sendBtnText">Invia</span>
                                <span id="sendBtnLoader" class="flex hidden items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Invio...</span>
                                </span>
                                <span class="material-icons">send</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info Footer --}}
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-icons text-blue-600">info</span>
                        <div class="flex-1 text-sm text-blue-800">
                            <p class="mb-1 font-medium">N.A.T.A.N. è alimentato da AI locale (Ollama Llama 3.1)</p>
                            <p class="text-xs text-blue-600">
                                Tutti i dati rimangono sul tuo server. Nessuna informazione viene inviata a servizi
                                esterni.
                                <strong>GDPR compliant</strong> per design.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            /**
             * N.A.T.A.N. Chat - Vanilla JavaScript Implementation
             *
             * NO Alpine.js, NO Livewire, NO jQuery - Pure ES6+ JavaScript
             * Enterprise-grade, readable, debuggable
             */
            const NatanChat = {
                // State
                messages: [],
                isLoading: false,
                conversationHistory: [],

                // DOM Elements (cached)
                elements: {
                    chatMessages: null,
                    welcomeMessage: null,
                    userInput: null,
                    sendBtn: null,
                    sendBtnText: null,
                    sendBtnLoader: null,
                    chatForm: null
                },

                // Configuration
                config: {
                    apiUrl: '{{ route('pa.natan.chat.message') }}',
                    csrfToken: '{{ csrf_token() }}',
                    maxHistoryLength: 10
                },

                /**
                 * Initialize chat
                 */
                init() {
                    console.log('[N.A.T.A.N.] Initializing chat...');

                    // Cache DOM elements
                    this.elements.chatMessages = document.getElementById('chatMessages');
                    this.elements.welcomeMessage = document.getElementById('welcomeMessage');
                    this.elements.userInput = document.getElementById('userInput');
                    this.elements.sendBtn = document.getElementById('sendBtn');
                    this.elements.sendBtnText = document.getElementById('sendBtnText');
                    this.elements.sendBtnLoader = document.getElementById('sendBtnLoader');
                    this.elements.chatForm = document.getElementById('chatForm');

                    // Bind events
                    this.elements.chatForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.handleSubmit();
                    });

                    console.log('[N.A.T.A.N.] Chat initialized successfully');
                },

                /**
                 * Handle form submit
                 */
                async handleSubmit() {
                    const message = this.elements.userInput.value.trim();

                    if (!message || this.isLoading) {
                        return;
                    }

                    // Clear input
                    this.elements.userInput.value = '';

                    // Hide welcome message
                    if (this.elements.welcomeMessage) {
                        this.elements.welcomeMessage.style.display = 'none';
                    }

                    // Add user message
                    this.addMessage('user', message);

                    // Send to API
                    await this.sendToApi(message);
                },

                /**
                 * Send suggested message
                 */
                sendSuggestedMessage(message) {
                    this.elements.userInput.value = message;
                    this.handleSubmit();
                },

                /**
                 * Add message to chat
                 */
                addMessage(role, content, sources = null) {
                    const timestamp = new Date().toLocaleTimeString('it-IT', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const message = {
                        role,
                        content,
                        sources,
                        timestamp
                    };

                    this.messages.push(message);
                    this.renderMessage(message);
                    this.scrollToBottom();
                },

                /**
                 * Render single message
                 */
                renderMessage(message) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = message.role === 'user' ? 'flex justify-end' : 'flex justify-start';

                    const bubbleDiv = document.createElement('div');
                    bubbleDiv.className = message.role === 'user' ?
                        'bg-[#2D5016] text-white rounded-2xl rounded-tr-sm max-w-md px-4 py-3 shadow-sm' :
                        'bg-white rounded-2xl rounded-tl-sm max-w-2xl px-4 py-3 shadow-md border border-gray-200';

                    // Message content
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'prose prose-sm max-w-none';
                    contentDiv.style.color = '#1B365D'; // Blu Algoritmo - massimo contrasto
                    contentDiv.innerHTML = this.formatMessage(message.content);
                    bubbleDiv.appendChild(contentDiv);

                    // Sources (for AI messages)
                    if (message.sources && message.sources.length > 0) {
                        const sourcesDiv = document.createElement('div');
                        sourcesDiv.className = 'mt-3 border-t border-gray-200 pt-3';
                        sourcesDiv.innerHTML = `
                            <p class="mb-2 text-xs font-semibold text-gray-600">Fonti:</p>
                            <div class="space-y-1">
                                ${message.sources.map(source => `
                                            <a href="${source.url}" target="_blank"
                                               class="block rounded border border-gray-200 bg-white p-2 text-xs hover:bg-gray-50">
                                                <span class="font-medium">${source.protocol_number}</span>
                                                <span class="text-gray-600"> - </span>
                                                <span>${source.title}</span>
                                            </a>
                                        `).join('')}
                            </div>
                        `;
                        bubbleDiv.appendChild(sourcesDiv);
                    }

                    // Timestamp
                    const timestampDiv = document.createElement('div');
                    timestampDiv.className = message.role === 'user' ? 'mt-2 text-xs text-white/60' :
                        'mt-2 text-xs text-gray-600';
                    timestampDiv.textContent = message.timestamp;
                    bubbleDiv.appendChild(timestampDiv);

                    messageDiv.appendChild(bubbleDiv);
                    this.elements.chatMessages.appendChild(messageDiv);
                },

                /**
                 * Format message (simple markdown-like)
                 */
                formatMessage(text) {
                    return text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>')
                        .replace(/• /g, '<br>• ');
                },

                /**
                 * Send message to API
                 */
                async sendToApi(message) {
                    this.setLoading(true);

                    // Show loading indicator
                    this.showLoadingIndicator();

                    try {
                        const response = await fetch(this.config.apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.config.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message,
                                conversation_history: this.getConversationHistory()
                            })
                        });

                        const data = await response.json();

                        // Remove loading indicator
                        this.hideLoadingIndicator();

                        if (data.success) {
                            this.addMessage('assistant', data.response, data.sources);
                        } else {
                            this.addMessage('assistant', 'Mi dispiace, si è verificato un errore: ' + (data.message ||
                                'Errore sconosciuto'));
                        }

                    } catch (error) {
                        console.error('[N.A.T.A.N.] API Error:', error);
                        this.hideLoadingIndicator();
                        this.addMessage('assistant',
                            'Mi dispiace, non riesco a connettermi al servizio AI. Riprova tra poco.');
                    } finally {
                        this.setLoading(false);
                    }
                },

                /**
                 * Show loading indicator
                 */
                showLoadingIndicator() {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.id = 'loadingIndicator';
                    loadingDiv.className = 'flex justify-start';
                    loadingDiv.innerHTML = `
                        <div class="max-w-2xl rounded-2xl rounded-tl-sm bg-gray-100 px-4 py-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="flex space-x-1">
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 0ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 150ms"></div>
                                    <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 300ms"></div>
                                </div>
                                <span class="text-xs text-gray-500">N.A.T.A.N. sta pensando...</span>
                            </div>
                        </div>
                    `;
                    this.elements.chatMessages.appendChild(loadingDiv);
                    this.scrollToBottom();
                },

                /**
                 * Hide loading indicator
                 */
                hideLoadingIndicator() {
                    const loadingIndicator = document.getElementById('loadingIndicator');
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                },

                /**
                 * Set loading state
                 */
                setLoading(loading) {
                    this.isLoading = loading;
                    this.elements.userInput.disabled = loading;
                    this.elements.sendBtn.disabled = loading;

                    if (loading) {
                        this.elements.sendBtnText.classList.add('hidden');
                        this.elements.sendBtnLoader.classList.remove('hidden');
                    } else {
                        this.elements.sendBtnText.classList.remove('hidden');
                        this.elements.sendBtnLoader.classList.add('hidden');
                    }
                },

                /**
                 * Get conversation history (last N messages)
                 */
                getConversationHistory() {
                    return this.messages
                        .slice(-this.config.maxHistoryLength)
                        .map(msg => ({
                            role: msg.role,
                            content: msg.content.replace(/<[^>]*>/g, '') // Strip HTML
                        }));
                },

                /**
                 * Scroll to bottom of chat
                 */
                scrollToBottom() {
                    setTimeout(() => {
                        this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
                    }, 100);
                }
            };

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => NatanChat.init());
            } else {
                NatanChat.init();
            }
        </script>
    @endpush
</x-pa-layout>
