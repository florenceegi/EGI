{{--
    Componente: Nota Donazioni e Vendite
    Sezione: Internazionale
    Descrizione: Differenza tra donazioni e vendite ai fini fiscali internazionali
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-scale class="w-5 h-5 text-pink-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Donazioni vs Vendite: Aspetti Internazionali
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        La corretta qualificazione tra <a href="#glossario" class="text-primary-600 hover:underline font-medium">donazione</a>
        e <a href="#glossario" class="text-primary-600 hover:underline font-medium">vendita</a> è fondamentale
        per determinare il regime fiscale applicabile, specialmente nelle transazioni internazionali.
    </p>

    <!-- Confronto -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrows-right-left class="w-5 h-5 text-pink-500" />
            Confronto Fiscale
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-pink-50">
                        <th class="text-left p-3 font-semibold text-pink-900">Aspetto</th>
                        <th class="text-center p-3 font-semibold text-green-700">Donazione</th>
                        <th class="text-center p-3 font-semibold text-blue-700">Vendita</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-3 font-medium">Controprestazione</td>
                        <td class="p-3 text-center text-green-600">Assente</td>
                        <td class="p-3 text-center text-blue-600">Presente (NFT/bene)</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-3 font-medium">IVA</td>
                        <td class="p-3 text-center text-green-600">Esente / Fuori campo</td>
                        <td class="p-3 text-center text-blue-600">Applicabile (22% IT)</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium">Regime internazionale</td>
                        <td class="p-3 text-center text-green-600">Non rileva OSS/MOSS</td>
                        <td class="p-3 text-center text-blue-600">OSS per B2C UE</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-3 font-medium">Documentazione</td>
                        <td class="p-3 text-center text-green-600">Ricevuta donazione</td>
                        <td class="p-3 text-center text-blue-600">Fattura/Ricevuta fiscale</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium">Beneficio donatore</td>
                        <td class="p-3 text-center text-green-600">Detrazione/Deduzione</td>
                        <td class="p-3 text-center text-blue-600">Nessuno (è un acquisto)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Casistiche Miste -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-puzzle-piece class="w-5 h-5 text-amber-500" />
            Casistiche Miste su FlorenceEGI
        </h4>
        <div class="space-y-3">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Donazione Pura a EPP</div>
                <p class="text-sm text-gray-700">
                    L'utente dona a un EPP senza ricevere nulla in cambio.
                    <strong>Regime:</strong> Fuori campo IVA, ricevuta di donazione, benefici fiscali per il donatore.
                </p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Acquisto NFT da Creator</div>
                <p class="text-sm text-gray-700">
                    L'utente acquista un NFT pagando un prezzo.
                    <strong>Regime:</strong> Vendita soggetta a IVA, fattura/ricevuta, regole OSS se B2C UE.
                </p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="font-medium text-purple-900 mb-2">Acquisto NFT con Quota EPP</div>
                <p class="text-sm text-gray-700">
                    L'utente acquista un NFT e una quota (es. 20%) va automaticamente all'EPP.
                    <strong>Regime:</strong> Vendita per il 100%, poi il Creator destina parte del ricavo come donazione all'EPP.
                </p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="font-medium text-amber-900 mb-2">NFT "Reward" per Donazione</div>
                <p class="text-sm text-gray-700">
                    ⚠️ Se si riceve un NFT come "ringraziamento" per una donazione, potrebbe essere riqualificato come vendita.
                    <strong>Regime:</strong> Valutare caso per caso il valore del "reward".
                </p>
            </div>
        </div>
    </div>

    <!-- Regola Pratica -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-check-badge class="w-5 h-5 text-green-500" />
            Regola Pratica
        </h4>
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-green-900 mb-2">È DONAZIONE se:</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Nessuna controprestazione diretta</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Beneficiario è ETS/ONLUS/Fondazione</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Volontà liberale documentata</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-blue-900 mb-2">È VENDITA se:</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-x-mark class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                            <span>Si riceve un bene/servizio in cambio</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-x-mark class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                            <span>Prezzo prefissato per il bene</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-x-mark class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                            <span>Transazione commerciale standard</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="bg-gray-100 rounded-lg p-4 text-xs text-gray-500">
        <strong>Nota:</strong> La qualificazione corretta dipende dalle circostanze specifiche.
        In caso di dubbio, consultare un consulente fiscale prima di effettuare operazioni
        di importo significativo o con controparti estere.
    </div>
</div>
