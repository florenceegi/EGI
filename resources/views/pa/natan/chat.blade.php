@extends('layouts.app')

@section('title', 'N.A.T.A.N. Chat AI')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('pa.acts.index') }}" class="rounded-lg bg-white p-2 shadow-sm transition-all hover:shadow-md">
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
        <div x-data="natanChat()" x-init="init()" class="mx-auto max-w-4xl">
            
            {{-- Chat Window --}}
            <div class="mb-4 rounded-2xl bg-white shadow-xl">
                
                {{-- Chat Header --}}
                <div class="border-b border-gray-200 bg-gradient-to-r from-[#1B365D] to-[#2D5016] p-6 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                            <span class="material-icons text-2xl text-white">smart_toy</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">N.A.T.A.N.</h2>
                            <p class="text-xs text-white/80">Nodo di Analisi e Tracciamento Atti Notarizzati</p>
                        </div>
                    </div>
                </div>

                {{-- Messages Container --}}
                <div id="chat-messages" 
                     class="h-[600px] overflow-y-auto p-6 space-y-4" 
                     x-ref="messagesContainer">
                    
                    {{-- Welcome Message --}}
                    <template x-if="messages.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center">
                            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#1B365D] to-[#2D5016]">
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
                                    @foreach($suggested_questions as $question)
                                    <button @click="sendMessage('{{ $question }}')"
                                            class="rounded-lg border-2 border-gray-200 bg-white p-4 text-left text-sm transition-all hover:border-[#2D5016] hover:shadow-md">
                                        <span class="material-icons mr-2 text-sm text-[#2D5016]">help_outline</span>
                                        {{ $question }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Message Bubbles --}}
                    <template x-for="(message, index) in messages" :key="index">
                        <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="message.role === 'user' 
                                    ? 'bg-[#2D5016] text-white rounded-2xl rounded-tr-sm max-w-md' 
                                    : 'bg-gray-100 text-gray-900 rounded-2xl rounded-tl-sm max-w-2xl'"
                                 class="px-4 py-3 shadow-sm">
                                
                                {{-- Message Content --}}
                                <div x-html="message.content" class="prose prose-sm max-w-none"></div>
                                
                                {{-- Sources (for AI responses) --}}
                                <template x-if="message.sources && message.sources.length > 0">
                                    <div class="mt-3 border-t border-gray-200 pt-3">
                                        <p class="mb-2 text-xs font-semibold text-gray-600">Fonti:</p>
                                        <div class="space-y-1">
                                            <template x-for="source in message.sources" :key="source.id">
                                                <a :href="source.url" 
                                                   target="_blank"
                                                   class="block rounded bg-white p-2 text-xs hover:bg-gray-50 border border-gray-200">
                                                    <span class="font-medium" x-text="source.protocol_number"></span>
                                                    <span class="text-gray-600"> - </span>
                                                    <span x-text="source.title"></span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                
                                {{-- Timestamp --}}
                                <div :class="message.role === 'user' ? 'text-white/60' : 'text-gray-500'" 
                                     class="mt-2 text-xs" 
                                     x-text="message.timestamp">
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Loading indicator --}}
                    <template x-if="isLoading">
                        <div class="flex justify-start">
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
                        </div>
                    </template>
                </div>

                {{-- Input Area --}}
                <div class="border-t border-gray-200 bg-gray-50 p-4 rounded-b-2xl">
                    <form @submit.prevent="handleSubmit" class="flex gap-2">
                        <input type="text" 
                               x-model="userInput"
                               :disabled="isLoading"
                               placeholder="Scrivi la tua domanda a N.A.T.A.N..."
                               class="flex-1 rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-[#2D5016] focus:outline-none focus:ring-2 focus:ring-[#2D5016]/20 disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <button type="submit" 
                                :disabled="isLoading || !userInput.trim()"
                                class="rounded-xl bg-[#2D5016] px-6 py-3 font-medium text-white transition-all hover:bg-[#3D6026] disabled:cursor-not-allowed disabled:opacity-50 flex items-center gap-2">
                            <span x-show="!isLoading">Invia</span>
                            <span x-show="isLoading" class="flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Invio...</span>
                            </span>
                            <span class="material-icons">send</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info Footer --}}
            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                <div class="flex items-start gap-3">
                    <span class="material-icons text-blue-600">info</span>
                    <div class="flex-1 text-sm text-blue-800">
                        <p class="font-medium mb-1">N.A.T.A.N. è alimentato da AI locale (Ollama Llama 3.1)</p>
                        <p class="text-xs text-blue-600">
                            Tutti i dati rimangono sul tuo server. Nessuna informazione viene inviata a servizi esterni. 
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
function natanChat() {
    return {
        messages: [],
        userInput: '',
        isLoading: false,

        init() {
            console.log('N.A.T.A.N. Chat initialized');
        },

        async handleSubmit() {
            if (!this.userInput.trim() || this.isLoading) return;

            const message = this.userInput.trim();
            this.userInput = '';

            // Add user message
            this.addMessage('user', message);

            // Scroll to bottom
            this.$nextTick(() => this.scrollToBottom());

            // Send to API
            await this.sendToApi(message);
        },

        async sendMessage(message) {
            this.userInput = message;
            await this.handleSubmit();
        },

        addMessage(role, content, sources = null) {
            const timestamp = new Date().toLocaleTimeString('it-IT', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            this.messages.push({
                role,
                content: this.formatMessage(content),
                sources,
                timestamp
            });
        },

        formatMessage(text) {
            // Simple markdown-like formatting
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>')
                .replace(/• /g, '<br>• ');
        },

        async sendToApi(message) {
            this.isLoading = true;

            try {
                const response = await fetch('{{ route("pa.natan.chat.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_history: this.getConversationHistory()
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.addMessage('assistant', data.response, data.sources);
                } else {
                    this.addMessage('assistant', 'Mi dispiace, si è verificato un errore: ' + (data.message || 'Errore sconosciuto'));
                }

            } catch (error) {
                console.error('API Error:', error);
                this.addMessage('assistant', 'Mi dispiace, non riesco a connettermi al servizio AI. Riprova tra poco.');
            } finally {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        getConversationHistory() {
            // Return last 10 messages for context
            return this.messages.slice(-10).map(msg => ({
                role: msg.role,
                content: msg.content.replace(/<[^>]*>/g, '') // Strip HTML
            }));
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    };
}
</script>
@endpush
@endsection

