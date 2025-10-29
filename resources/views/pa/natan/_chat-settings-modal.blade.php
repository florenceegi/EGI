{{-- ⚙️ N.A.T.A.N. Chat Settings Modal --}}
<div id="chatSettingsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex min-h-screen items-center justify-center px-4 py-6">
        <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-[#1B365D] to-[#2D5016] p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                            <span class="material-icons text-2xl">settings</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Impostazioni Chat</h2>
                            <p class="text-sm text-white/80">Configura N.A.T.A.N. secondo le tue preferenze</p>
                        </div>
                    </div>
                    <button onclick="ChatSettings.close()" class="transition-transform hover:scale-110 active:scale-95">
                        <span class="material-icons text-3xl">close</span>
                    </button>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex border-b border-gray-200 bg-gray-50">
                <button onclick="ChatSettings.switchTab('parameters')" data-tab="parameters"
                    class="settings-tab active flex items-center gap-2 border-b-2 border-transparent px-6 py-4 text-sm font-medium transition-all hover:bg-white">
                    <span class="material-icons">tune</span>
                    <span>Parametri</span>
                </button>
                <button onclick="ChatSettings.switchTab('memory')" data-tab="memory"
                    class="settings-tab flex items-center gap-2 border-b-2 border-transparent px-6 py-4 text-sm font-medium transition-all hover:bg-white">
                    <span class="material-icons">psychology</span>
                    <span>Memoria</span>
                    <span id="memoryTabBadge"
                        class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-bold text-purple-800">0</span>
                </button>
                <button onclick="ChatSettings.switchTab('advanced')" data-tab="advanced"
                    class="settings-tab flex items-center gap-2 border-b-2 border-transparent px-6 py-4 text-sm font-medium transition-all hover:bg-white">
                    <span class="material-icons">code</span>
                    <span>Avanzate</span>
                </button>
            </div>

            {{-- Tab Content --}}
            <div class="max-h-[70vh] overflow-y-auto p-6">

                {{-- PARAMETRI TAB --}}
                <div id="parametersTab" class="settings-tab-content">
                    <div class="space-y-6">

                        {{-- Web Search Toggle --}}
                        <div class="rounded-lg border-2 border-blue-100 bg-blue-50/50 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="material-icons text-xl text-blue-600">travel_explore</span>
                                        <h3 class="text-lg font-bold text-gray-900">Ricerca Web (Perplexity)</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">Arricchisce le risposte con informazioni aggiornate
                                        da Internet</p>
                                    <p class="mt-1 text-xs text-blue-600">💡 Utile per normative recenti, best
                                        practices, trend tecnologici</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" id="webSearchToggleSetting" class="peer sr-only">
                                    <div
                                        class="peer h-7 w-14 rounded-full bg-gray-200 after:absolute after:start-[4px] after:top-0.5 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rtl:peer-checked:after:-translate-x-full">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Streaming Mode --}}
                        <div class="rounded-lg border-2 border-purple-100 bg-purple-50/50 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="material-icons text-xl text-purple-600">stream</span>
                                        <h3 class="text-lg font-bold text-gray-900">Modalità Streaming (SSE)</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">Mostra il progresso dell'analisi in tempo reale</p>
                                    <p class="mt-1 text-xs text-purple-600">⚡ Vedi ricerca semantica, atti analizzati,
                                        costi aggiornati live</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" id="streamingModeSetting" class="peer sr-only" checked>
                                    <div
                                        class="peer h-7 w-14 rounded-full bg-gray-200 after:absolute after:start-[4px] after:top-0.5 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-purple-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rtl:peer-checked:after:-translate-x-full">
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Auto-submit on Shift+Enter --}}
                        <div class="rounded-lg border-2 border-gray-100 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="material-icons text-xl text-gray-600">keyboard</span>
                                        <h3 class="text-lg font-bold text-gray-900">Invio Rapido (Shift+Enter)</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">Invia il messaggio premendo Shift+Enter invece di
                                        Enter</p>
                                    <p class="mt-1 text-xs text-gray-500">ℹ️ Enter per nuova riga, Shift+Enter per
                                        inviare</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" id="shiftEnterSetting" class="peer sr-only" checked>
                                    <div
                                        class="peer h-7 w-14 rounded-full bg-gray-200 after:absolute after:start-[4px] after:top-0.5 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rtl:peer-checked:after:-translate-x-full">
                                    </div>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- MEMORIA TAB --}}
                <div id="memoryTab" class="settings-tab-content hidden">
                    <div class="space-y-6">

                        {{-- Memory System Toggle --}}
                        <div
                            class="rounded-lg border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-pink-50 p-4">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="material-icons text-2xl text-purple-600">psychology</span>
                                        <h3 class="text-xl font-bold text-gray-900">Sistema Memoria</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">Permette a N.A.T.A.N. di ricordare informazioni
                                        importanti tra le conversazioni</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" id="memorySystemToggle" class="peer sr-only" checked>
                                    <div
                                        class="peer h-7 w-14 rounded-full bg-gray-200 after:absolute after:start-[4px] after:top-0.5 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-purple-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rtl:peer-checked:after:-translate-x-full">
                                    </div>
                                </label>
                            </div>

                            {{-- Stats --}}
                            <div class="grid grid-cols-3 gap-3 rounded-lg bg-white p-3">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600" id="totalMemoriesCount">0</div>
                                    <div class="text-xs text-gray-600">Memorie Totali</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600" id="mostUsedMemoryCount">0</div>
                                    <div class="text-xs text-gray-600">Più Usata</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600" id="recentMemoriesCount">0</div>
                                    <div class="text-xs text-gray-600">Recenti (7gg)</div>
                                </div>
                            </div>
                        </div>

                        {{-- Add New Memory --}}
                        <div class="rounded-lg border-2 border-green-200 bg-green-50/50 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-icons text-xl text-green-600">add_circle</span>
                                <h3 class="text-lg font-bold text-gray-900">Aggiungi Memoria Manuale</h3>
                            </div>
                            <form id="addMemoryForm" class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Contenuto</label>
                                    <textarea id="newMemoryContent" rows="2"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-green-500"
                                        placeholder="Es: Il mio comune è Firenze, lavoro nell'ufficio ambiente..."></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Tipo</label>
                                        <select id="newMemoryType"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-green-500">
                                            <option value="fact">📝 Fatto</option>
                                            <option value="preference">⭐ Preferenza</option>
                                            <option value="context">🏢 Contesto</option>
                                            <option value="instruction">📋 Istruzione</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit"
                                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-4 py-2 text-sm font-medium text-white transition-all hover:shadow-lg active:scale-95">
                                            <span class="material-icons">save</span>
                                            <span>Salva Memoria</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Memories List --}}
                        <div class="rounded-lg border-2 border-gray-200 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-xl text-gray-600">format_list_bulleted</span>
                                    <h3 class="text-lg font-bold text-gray-900">Le Mie Memorie</h3>
                                </div>
                                <button onclick="ChatSettings.refreshMemories()"
                                    class="flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 transition-all hover:bg-gray-200 active:scale-95">
                                    <span class="material-icons text-sm">refresh</span>
                                    Aggiorna
                                </button>
                            </div>

                            <div id="memoriesList" class="max-h-[400px] space-y-2 overflow-y-auto">
                                <div class="flex items-center justify-center py-8 text-gray-400">
                                    <div class="text-center">
                                        <span class="material-icons text-4xl">hourglass_empty</span>
                                        <p class="mt-2 text-sm">Caricamento memorie...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- AVANZATE TAB --}}
                <div id="advancedTab" class="settings-tab-content hidden">
                    <div class="space-y-6">

                        {{-- Export/Import Memories --}}
                        <div class="rounded-lg border-2 border-indigo-200 bg-indigo-50/50 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-icons text-xl text-indigo-600">import_export</span>
                                <h3 class="text-lg font-bold text-gray-900">Esporta/Importa Memorie</h3>
                            </div>
                            <div class="flex gap-3">
                                <button onclick="ChatSettings.exportMemories()"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-lg border-2 border-indigo-200 bg-white px-4 py-2 text-sm font-medium text-indigo-700 transition-all hover:bg-indigo-50 active:scale-95">
                                    <span class="material-icons">download</span>
                                    <span>Esporta JSON</span>
                                </button>
                                <button onclick="document.getElementById('importMemoriesFile').click()"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-lg border-2 border-indigo-200 bg-white px-4 py-2 text-sm font-medium text-indigo-700 transition-all hover:bg-indigo-50 active:scale-95">
                                    <span class="material-icons">upload</span>
                                    <span>Importa JSON</span>
                                </button>
                                <input type="file" id="importMemoriesFile" class="hidden" accept=".json"
                                    onchange="ChatSettings.importMemories(event)">
                            </div>
                        </div>

                        {{-- Clear All Data --}}
                        <div class="rounded-lg border-2 border-red-200 bg-red-50/50 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-icons text-xl text-red-600">warning</span>
                                <h3 class="text-lg font-bold text-gray-900">Zona Pericolosa</h3>
                            </div>
                            <p class="mb-3 text-sm text-gray-600">Queste azioni sono irreversibili. Procedi con
                                cautela.</p>
                            <div class="space-y-2">
                                <button onclick="ChatSettings.clearAllMemories()"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 transition-all hover:bg-red-50 active:scale-95">
                                    <span class="material-icons">delete_forever</span>
                                    <span>Elimina Tutte le Memorie</span>
                                </button>
                                <button onclick="NatanChat.clearChat()"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-orange-200 bg-white px-4 py-2 text-sm font-medium text-orange-700 transition-all hover:bg-orange-50 active:scale-95">
                                    <span class="material-icons">clear_all</span>
                                    <span>Pulisci Chat Corrente</span>
                                </button>
                            </div>
                        </div>

                        {{-- Debug Info --}}
                        <div class="rounded-lg border-2 border-gray-200 bg-gray-50 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="material-icons text-xl text-gray-600">bug_report</span>
                                <h3 class="text-lg font-bold text-gray-900">Informazioni Debug</h3>
                            </div>
                            <div class="space-y-1 font-mono text-xs text-gray-600">
                                <div><strong>User ID:</strong> {{ auth()->id() }}</div>
                                <div><strong>Session ID:</strong> <span id="debugSessionId">-</span></div>
                                <div><strong>Memory Enabled:</strong> <span id="debugMemoryEnabled">-</span></div>
                                <div><strong>Web Search:</strong> <span id="debugWebSearch">-</span></div>
                                <div><strong>Streaming:</strong> <span id="debugStreaming">-</span></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">
                <div class="text-xs text-gray-500">
                    N.A.T.A.N. v7.1 · Sistema Memoria Persistente
                </div>
                <div class="flex gap-3">
                    <button onclick="ChatSettings.resetToDefaults()"
                        class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 active:scale-95">
                        <span class="material-icons">restart_alt</span>
                        <span>Reset Defaults</span>
                    </button>
                    <button onclick="ChatSettings.close()"
                        class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-6 py-2 text-sm font-medium text-white transition-all hover:shadow-lg active:scale-95">
                        <span class="material-icons">check</span>
                        <span>Salva e Chiudi</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Tab Styling */
    .settings-tab {
        @apply text-gray-600;
    }

    .settings-tab.active {
        @apply text-[#1B365D] bg-white border-[#2D5016];
    }

    /* Memory Card Styling */
    .memory-card {
        @apply p-3 border rounded-lg bg-gradient-to-br transition-all hover:shadow-md;
    }

    .memory-card.fact {
        @apply from-blue-50 to-blue-100 border-blue-200;
    }

    .memory-card.preference {
        @apply from-purple-50 to-purple-100 border-purple-200;
    }

    .memory-card.context {
        @apply from-green-50 to-green-100 border-green-200;
    }

    .memory-card.instruction {
        @apply from-orange-50 to-orange-100 border-orange-200;
    }
</style>
