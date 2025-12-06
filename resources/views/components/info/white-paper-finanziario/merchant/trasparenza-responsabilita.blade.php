{{--
    Componente: Trasparenza e Responsabilità
    Sezione: Merchant & Pagamenti
    Descrizione: Principi di trasparenza e responsabilità della piattaforma
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-eye class="w-5 h-5 text-sky-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Trasparenza e Responsabilità
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI si impegna a garantire la massima
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">trasparenza</a>
        nelle operazioni finanziarie, definendo chiaramente le responsabilità di ogni attore.
    </p>

    <!-- Principi di Trasparenza -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-sky-500" />
            Principi di Trasparenza
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-sky-50 rounded-lg p-4">
                <div class="font-medium text-sky-900 mb-2">Fee Esplicite</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Commissioni mostrate prima del pagamento</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Nessun costo nascosto</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Breakdown dettagliato delle fee</span>
                    </li>
                </ul>
            </div>
            <div class="bg-sky-50 rounded-lg p-4">
                <div class="font-medium text-sky-900 mb-2">Tracciabilità Completa</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Ogni transazione ha ID univoco</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Storico accessibile 24/7</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-sky-600 mt-0.5 shrink-0" />
                        <span>Export dati in formato standard</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Matrice Responsabilità -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-table-cells class="w-5 h-5 text-purple-500" />
            Matrice delle Responsabilità
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-purple-50">
                        <th class="text-left p-2 font-semibold text-purple-900">Ambito</th>
                        <th class="text-center p-2 font-semibold text-purple-900">Piattaforma</th>
                        <th class="text-center p-2 font-semibold text-purple-900">Merchant</th>
                        <th class="text-center p-2 font-semibold text-purple-900">PSP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-2 font-medium">Dichiarazione IVA</td>
                        <td class="p-2 text-center">—</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center">—</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2 font-medium">Emissione Fatture</td>
                        <td class="p-2 text-center text-amber-600">○</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center">—</td>
                    </tr>
                    <tr>
                        <td class="p-2 font-medium">Sicurezza Pagamenti</td>
                        <td class="p-2 text-center text-amber-600">○</td>
                        <td class="p-2 text-center">—</td>
                        <td class="p-2 text-center text-green-600">●</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2 font-medium">Compliance AML</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center text-amber-600">○</td>
                        <td class="p-2 text-center text-green-600">●</td>
                    </tr>
                    <tr>
                        <td class="p-2 font-medium">Protezione Dati</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center text-amber-600">○</td>
                        <td class="p-2 text-center text-amber-600">○</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2 font-medium">Reportistica Fiscale</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center text-green-600">●</td>
                        <td class="p-2 text-center">—</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex gap-4 mt-2 text-xs text-gray-500">
            <span><span class="text-green-600">●</span> Responsabile principale</span>
            <span><span class="text-amber-600">○</span> Co-responsabile / Supporto</span>
            <span>— Non applicabile</span>
        </div>
    </div>

    <!-- Garanzie -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5 text-green-500" />
            Garanzie per gli Utenti
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-green-600 mx-auto mb-2" />
                <div class="font-medium text-green-900">Fondi Protetti</div>
                <p class="text-xs text-gray-500 mt-1">Conti segregati presso PSP</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <x-heroicon-o-arrow-path class="w-8 h-8 text-blue-600 mx-auto mb-2" />
                <div class="font-medium text-blue-900">Rimborsi Garantiti</div>
                <p class="text-xs text-gray-500 mt-1">Entro 14 giorni se previsto</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <x-heroicon-o-document-magnifying-glass class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                <div class="font-medium text-purple-900">Audit Trail</div>
                <p class="text-xs text-gray-500 mt-1">Log immutabili delle operazioni</p>
            </div>
        </div>
    </div>

    <!-- Contatti -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-amber-500" />
            Canali di Supporto
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div>
                    <x-heroicon-o-envelope class="w-6 h-6 text-amber-600 mx-auto mb-1" />
                    <div class="text-sm font-medium">Email</div>
                    <a href="mailto:support@florenceegi.com" class="text-xs text-amber-700 hover:underline">
                        support@florenceegi.com
                    </a>
                </div>
                <div>
                    <x-heroicon-o-chat-bubble-oval-left-ellipsis class="w-6 h-6 text-amber-600 mx-auto mb-1" />
                    <div class="text-sm font-medium">Live Chat</div>
                    <span class="text-xs text-gray-500">Lun-Ven 9-18</span>
                </div>
                <div>
                    <x-heroicon-o-ticket class="w-6 h-6 text-amber-600 mx-auto mb-1" />
                    <div class="text-sm font-medium">Ticket</div>
                    <span class="text-xs text-gray-500">Risposta < 24h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Note Legali -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-sky-400">
        <h5 class="font-medium text-gray-900 mb-2">Disclaimer</h5>
        <p class="text-sm text-gray-600">
            FlorenceEGI agisce come <strong>intermediario tecnologico</strong> e non come
            istituto finanziario. I servizi di pagamento sono forniti da PSP autorizzati.
            Ogni utente è responsabile dei propri obblighi fiscali e dichiarativi.
        </p>
    </div>
</div>
