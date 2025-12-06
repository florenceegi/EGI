{{--
    Componente: Gestione Fee Batch
    Sezione: Trader Pro
    Descrizione: Come vengono gestite le commissioni per operazioni batch/bulk
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-square-3-stack-3d class="w-5 h-5 text-teal-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Gestione Fee per Operazioni Batch
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        I <a href="#glossario" class="text-primary-600 hover:underline font-medium">Trader Pro</a>
        possono effettuare operazioni in batch (bulk) per la creazione, listing e trasferimento
        massivo di NFT, con una struttura di fee ottimizzata.
    </p>

    <!-- Struttura Fee -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5 text-teal-500" />
            Struttura delle Commissioni Batch
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-teal-50">
                        <th class="text-left p-3 font-semibold text-teal-900">Operazione</th>
                        <th class="text-center p-3 font-semibold text-teal-900">Singola</th>
                        <th class="text-center p-3 font-semibold text-teal-900">Batch (10+)</th>
                        <th class="text-center p-3 font-semibold text-teal-900">Bulk (100+)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-3 font-medium">Minting NFT</td>
                        <td class="p-3 text-center">€2.00</td>
                        <td class="p-3 text-center text-green-600">€1.50 <span class="text-xs text-gray-400">(-25%)</span></td>
                        <td class="p-3 text-center text-green-600">€1.00 <span class="text-xs text-gray-400">(-50%)</span></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-3 font-medium">Listing Marketplace</td>
                        <td class="p-3 text-center">€0.50</td>
                        <td class="p-3 text-center text-green-600">€0.35 <span class="text-xs text-gray-400">(-30%)</span></td>
                        <td class="p-3 text-center text-green-600">€0.20 <span class="text-xs text-gray-400">(-60%)</span></td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium">Transfer</td>
                        <td class="p-3 text-center">€0.30</td>
                        <td class="p-3 text-center text-green-600">€0.20 <span class="text-xs text-gray-400">(-33%)</span></td>
                        <td class="p-3 text-center text-green-600">€0.10 <span class="text-xs text-gray-400">(-67%)</span></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-3 font-medium">Fee Transazione</td>
                        <td class="p-3 text-center">5%</td>
                        <td class="p-3 text-center text-green-600">4% <span class="text-xs text-gray-400">(-20%)</span></td>
                        <td class="p-3 text-center text-green-600">3% <span class="text-xs text-gray-400">(-40%)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fatturazione Batch -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
            Fatturazione delle Fee Batch
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Modalità di Addebito</div>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="bg-blue-200 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">A</span>
                        <span><strong>Prepagato:</strong> Crediti batch acquistati in anticipo con sconto</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="bg-blue-200 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">B</span>
                        <span><strong>Postpagato:</strong> Fatturazione mensile con soglia minima €500</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="bg-blue-200 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">C</span>
                        <span><strong>Ibrido:</strong> Prepagato + fattura delta a fine mese</span>
                    </li>
                </ul>
            </div>
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="font-medium text-amber-900 mb-2">Documenti Emessi</div>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-document-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                        <span><strong>Fattura elettronica</strong> per le fee (con IVA 22%)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-document-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                        <span><strong>Report dettagliato</strong> operazioni batch</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-document-check class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                        <span><strong>Estratto conto</strong> crediti prepagati</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Esempio Calcolo -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-purple-500" />
            Esempio: Batch di 50 NFT
        </h4>
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 mb-1">Minting (50 × €1.50)</div>
                    <div class="text-lg font-bold text-purple-600">€75.00</div>
                </div>
                <div>
                    <div class="text-gray-500 mb-1">Listing (50 × €0.35)</div>
                    <div class="text-lg font-bold text-purple-600">€17.50</div>
                </div>
                <div>
                    <div class="text-gray-500 mb-1">Totale Fee Batch</div>
                    <div class="text-lg font-bold text-green-600">€92.50</div>
                    <div class="text-xs text-gray-500">vs €125.00 singole (-26%)</div>
                </div>
            </div>
            <hr class="my-3 border-purple-200" />
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Se venduti tutti a €100 ciascuno:</span>
                <div class="text-right">
                    <span class="text-gray-500">Fee transazione (4%):</span>
                    <span class="font-bold text-purple-600 ml-2">€200.00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- API Batch -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-teal-400">
        <h5 class="font-medium text-gray-900 mb-2 flex items-center gap-2">
            <x-heroicon-o-code-bracket class="w-5 h-5 text-teal-500" />
            API per Operazioni Batch
        </h5>
        <p class="text-sm text-gray-600 mb-2">
            I Trader Pro hanno accesso alle API dedicate per operazioni batch:
        </p>
        <div class="flex flex-wrap gap-2 text-xs">
            <code class="bg-gray-200 text-gray-700 px-2 py-1 rounded">POST /api/v1/batch/mint</code>
            <code class="bg-gray-200 text-gray-700 px-2 py-1 rounded">POST /api/v1/batch/list</code>
            <code class="bg-gray-200 text-gray-700 px-2 py-1 rounded">POST /api/v1/batch/transfer</code>
            <code class="bg-gray-200 text-gray-700 px-2 py-1 rounded">GET /api/v1/batch/status/{id}</code>
        </div>
    </div>
</div>
