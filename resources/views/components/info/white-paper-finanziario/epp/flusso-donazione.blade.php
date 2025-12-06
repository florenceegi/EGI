{{--
    Componente: Flusso Donazione EPP
    Sezione: EPP - Ente / Ente Progetto Partecipato
    Descrizione: Visualizzazione del flusso completo di una donazione verso un EPP
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-arrow-path class="w-5 h-5 text-violet-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Flusso Completo della Donazione EPP
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Schema del percorso di una donazione dall'utente all'
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">EPP</a>
        (Ente Progetto Partecipato), inclusi i passaggi fiscali e documentali.
    </p>

    <!-- Diagramma Flusso -->
    <div class="mb-6">
        <div class="relative">
            <!-- Step 1: Donatore -->
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold shrink-0">
                    1
                </div>
                <div class="flex-1 bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-user class="w-5 h-5 text-blue-600" />
                        <h4 class="font-semibold text-blue-900">Donatore (Mecenate)</h4>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">
                        L'utente sceglie un progetto EPP e avvia la donazione.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-white text-blue-700 text-xs px-2 py-1 rounded border border-blue-200">Carta di credito</span>
                        <span class="bg-white text-blue-700 text-xs px-2 py-1 rounded border border-blue-200">Bonifico</span>
                        <span class="bg-white text-blue-700 text-xs px-2 py-1 rounded border border-blue-200">PayPal</span>
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center mb-4">
                <x-heroicon-o-arrow-down class="w-6 h-6 text-gray-400" />
            </div>

            <!-- Step 2: PSP -->
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold shrink-0">
                    2
                </div>
                <div class="flex-1 bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-credit-card class="w-5 h-5 text-purple-600" />
                        <h4 class="font-semibold text-purple-900">PSP (Payment Service Provider)</h4>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">
                        Il pagamento viene processato dal PSP autorizzato (Stripe/MangoPay).
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white rounded p-2 border border-purple-200">
                            <span class="text-gray-500">Commissione PSP:</span>
                            <span class="text-purple-700 font-medium ml-1">1.4% + €0.25</span>
                        </div>
                        <div class="bg-white rounded p-2 border border-purple-200">
                            <span class="text-gray-500">Tempo:</span>
                            <span class="text-purple-700 font-medium ml-1">Istantaneo</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center mb-4">
                <x-heroicon-o-arrow-down class="w-6 h-6 text-gray-400" />
            </div>

            <!-- Step 3: Piattaforma -->
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold shrink-0">
                    3
                </div>
                <div class="flex-1 bg-indigo-50 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-server-stack class="w-5 h-5 text-indigo-600" />
                        <h4 class="font-semibold text-indigo-900">Piattaforma FlorenceEGI</h4>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">
                        La piattaforma registra la transazione e prepara la documentazione.
                    </p>
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-500" />
                            <span>Registrazione transazione</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-500" />
                            <span>Generazione ricevuta donazione</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-500" />
                            <span>Notifica all'ente beneficiario</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arrow Split -->
            <div class="flex justify-center mb-4">
                <div class="flex items-center gap-8">
                    <x-heroicon-o-arrow-down-left class="w-6 h-6 text-gray-400" />
                    <x-heroicon-o-arrow-down class="w-6 h-6 text-gray-400" />
                    <x-heroicon-o-arrow-down-right class="w-6 h-6 text-gray-400" />
                </div>
            </div>

            <!-- Step 4: Distribuzione -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Fee Piattaforma -->
                <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200">
                    <div class="text-center mb-2">
                        <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded">5%</span>
                    </div>
                    <div class="font-medium text-gray-900 text-center text-sm">Fee Piattaforma</div>
                    <p class="text-xs text-gray-500 text-center mt-1">Costi operativi e manutenzione</p>
                </div>
                
                <!-- Importo Netto EPP -->
                <div class="bg-green-50 rounded-lg p-4 border-2 border-green-300">
                    <div class="text-center mb-2">
                        <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded">~93%</span>
                    </div>
                    <div class="font-medium text-green-900 text-center text-sm">All'Ente (EPP)</div>
                    <p class="text-xs text-gray-500 text-center mt-1">Importo netto dopo commissioni</p>
                </div>

                <!-- Ricevuta Donatore -->
                <div class="bg-blue-50 rounded-lg p-4 border-2 border-blue-200">
                    <div class="text-center mb-2">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-500 mx-auto" />
                    </div>
                    <div class="font-medium text-blue-900 text-center text-sm">Ricevuta</div>
                    <p class="text-xs text-gray-500 text-center mt-1">Inviata al donatore (100%)</p>
                </div>
            </div>

            <!-- Arrow -->
            <div class="flex justify-center mb-4">
                <x-heroicon-o-arrow-down class="w-6 h-6 text-gray-400" />
            </div>

            <!-- Step 5: EPP -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold shrink-0">
                    4
                </div>
                <div class="flex-1 bg-green-50 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-building-library class="w-5 h-5 text-green-600" />
                        <h4 class="font-semibold text-green-900">Ente (EPP)</h4>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">
                        L'ente riceve i fondi sul proprio wallet e può procedere al prelievo.
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="bg-white text-green-700 px-2 py-1 rounded border border-green-200">Wallet EPP accreditato</span>
                        <span class="bg-white text-green-700 px-2 py-1 rounded border border-green-200">Payout su IBAN</span>
                        <span class="bg-white text-green-700 px-2 py-1 rounded border border-green-200">Report donazioni</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tempistiche Riepilogo -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-lg p-4 border border-violet-200">
        <h5 class="font-semibold text-violet-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clock class="w-5 h-5" />
            Tempistiche Complessive
        </h5>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
            <div>
                <div class="text-lg font-bold text-violet-600">~5 sec</div>
                <div class="text-xs text-gray-600">Conferma pagamento</div>
            </div>
            <div>
                <div class="text-lg font-bold text-violet-600">~1 min</div>
                <div class="text-xs text-gray-600">Ricevuta generata</div>
            </div>
            <div>
                <div class="text-lg font-bold text-violet-600">24-48h</div>
                <div class="text-xs text-gray-600">Disponibilità wallet</div>
            </div>
            <div>
                <div class="text-lg font-bold text-violet-600">2-5 gg</div>
                <div class="text-xs text-gray-600">Payout su IBAN</div>
            </div>
        </div>
    </div>
</div>
