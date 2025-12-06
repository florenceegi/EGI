{{--
    Componente: Sistema Economico
    Sezione: Economia
    Descrizione: Il valore che circola, per chi circola e perché
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-currency-euro class="w-5 h-5 text-emerald-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Sistema Economico
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Il valore che circola, per chi circola e perché.
    </p>

    <!-- Distribuzione Transazione -->
    <div class="mb-8 p-6 rounded-lg bg-gradient-to-r from-emerald-50 to-blue-50 border-2 border-emerald-300">
        <h4 class="text-xl font-bold text-emerald-800 mb-4 text-center">
            💰 Distribuzione di Una Transazione
        </h4>
        
        <!-- Grafico Simulato con Barre -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="text-center">
                <div class="h-32 bg-gray-100 rounded-lg flex items-end justify-center p-2">
                    <div class="w-full bg-emerald-500 rounded" style="height: 100%">
                        <span class="text-white text-xs font-bold pt-2 block">91.5%</span>
                    </div>
                </div>
                <div class="mt-2 text-xs font-medium text-gray-700">Owner</div>
            </div>
            <div class="text-center">
                <div class="h-32 bg-gray-100 rounded-lg flex items-end justify-center p-2">
                    <div class="w-full bg-blue-500 rounded" style="height: 30%">
                        <span class="text-white text-xs font-bold pt-1 block">4.5%</span>
                    </div>
                </div>
                <div class="mt-2 text-xs font-medium text-gray-700">Creator</div>
            </div>
            <div class="text-center">
                <div class="h-32 bg-gray-100 rounded-lg flex items-end justify-center p-2">
                    <div class="w-full bg-purple-500 rounded" style="height: 22%">
                        <span class="text-white text-xs font-bold pt-1 block">3%</span>
                    </div>
                </div>
                <div class="mt-2 text-xs font-medium text-gray-700">Piattaforma</div>
            </div>
            <div class="text-center">
                <div class="h-32 bg-gray-100 rounded-lg flex items-end justify-center p-2">
                    <div class="w-full bg-green-500 rounded" style="height: 8%">
                        <span class="text-white text-xs font-bold pt-1 block">1%</span>
                    </div>
                </div>
                <div class="mt-2 text-xs font-medium text-gray-700">EPP</div>
            </div>
        </div>

        <p class="text-center text-sm text-gray-600">
            Totale fee: <strong>8.5%</strong> | Ricavo netto Owner: <strong>91.5%</strong>
        </p>
    </div>

    <!-- Dettaglio Fee -->
    <div class="grid gap-4 md:grid-cols-2 mb-8">
        <!-- Royalty Creator -->
        <div class="p-5 border-l-4 border-blue-500 rounded-r-lg bg-blue-50">
            <h5 class="text-lg font-bold text-blue-800 mb-2 flex items-center gap-2">
                <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600" />
                Royalty Creator: 4.5%
            </h5>
            <p class="text-sm text-gray-700 mb-2">
                Su <strong>ogni</strong> rivendita secondaria, automatica via smart contract.
            </p>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• Pagamento istantaneo, no intermediari</li>
                <li>• Non eludibile (trustless enforcement)</li>
                <li>• Cumulabile con Diritto di Seguito SIAE</li>
            </ul>
        </div>

        <!-- Fee Piattaforma -->
        <div class="p-5 border-l-4 border-purple-500 rounded-r-lg bg-purple-50">
            <h5 class="text-lg font-bold text-purple-800 mb-2 flex items-center gap-2">
                <x-heroicon-o-building-office class="w-5 h-5 text-purple-600" />
                Fee Piattaforma: 3%
            </h5>
            <p class="text-sm text-gray-700 mb-2">
                Sostiene sviluppo tecnologico, sicurezza e operazioni.
            </p>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• Infrastruttura blockchain e web</li>
                <li>• Supporto e customer care</li>
                <li>• Sviluppo AI e nuove funzionalità</li>
            </ul>
        </div>

        <!-- Quota EPP -->
        <div class="p-5 border-l-4 border-green-500 rounded-r-lg bg-green-50 md:col-span-2">
            <h5 class="text-lg font-bold text-green-800 mb-2 flex items-center gap-2">
                <x-heroicon-o-globe-europe-africa class="w-5 h-5 text-green-600" />
                <a href="#glossario" class="hover:underline">EPP</a> (Environmental Protection Premium): 1%
            </h5>
            <p class="text-sm text-gray-700 mb-2">
                Destinato a progetti ambientali certificati → <strong>l'arte che rigenera il pianeta</strong>.
            </p>
            <div class="grid grid-cols-3 gap-3 mt-3">
                <div class="bg-white rounded-lg p-2 text-center">
                    <span class="text-lg">🌳</span>
                    <div class="text-xs text-gray-600">Riforestazione</div>
                </div>
                <div class="bg-white rounded-lg p-2 text-center">
                    <span class="text-lg">🌊</span>
                    <div class="text-xs text-gray-600">Protezione Mari</div>
                </div>
                <div class="bg-white rounded-lg p-2 text-center">
                    <span class="text-lg">⚡</span>
                    <div class="text-xs text-gray-600">Carbon Offset</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Esempio Pratico -->
    <div class="mb-6 p-5 rounded-lg bg-amber-50 border border-amber-200">
        <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5 text-amber-600" />
            Esempio Pratico: Vendita €10,000
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-amber-200">
                        <th class="p-2 text-left font-semibold text-amber-900">Destinatario</th>
                        <th class="p-2 text-right font-semibold text-amber-900">%</th>
                        <th class="p-2 text-right font-semibold text-amber-900">Importo</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <tr class="border-b border-amber-100">
                        <td class="p-2">🎨 Creator (royalty)</td>
                        <td class="p-2 text-right">4.5%</td>
                        <td class="p-2 text-right font-bold text-blue-700">€450</td>
                    </tr>
                    <tr class="border-b border-amber-100">
                        <td class="p-2">🏢 Piattaforma</td>
                        <td class="p-2 text-right">3%</td>
                        <td class="p-2 text-right font-bold text-purple-700">€300</td>
                    </tr>
                    <tr class="border-b border-amber-100">
                        <td class="p-2">🌍 EPP</td>
                        <td class="p-2 text-right">1%</td>
                        <td class="p-2 text-right font-bold text-green-700">€100</td>
                    </tr>
                    <tr class="bg-amber-100">
                        <td class="p-2 font-bold">💰 Ricavo Owner</td>
                        <td class="p-2 text-right font-bold">91.5%</td>
                        <td class="p-2 text-right font-bold text-emerald-700">€9,150</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Flusso del Valore -->
    <div class="p-5 rounded-lg bg-gray-50">
        <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrows-right-left class="w-5 h-5 text-gray-600" />
            Flusso del Valore
        </h4>
        <div class="flex items-center justify-between text-center overflow-x-auto gap-2">
            <div class="bg-blue-100 rounded-lg p-3 min-w-[80px]">
                <span class="text-2xl">🛒</span>
                <div class="text-xs text-gray-700 mt-1">Acquirente</div>
            </div>
            <x-heroicon-o-arrow-right class="w-6 h-6 text-gray-400 shrink-0" />
            <div class="bg-purple-100 rounded-lg p-3 min-w-[80px]">
                <span class="text-2xl">⚡</span>
                <div class="text-xs text-gray-700 mt-1">Smart Contract</div>
            </div>
            <x-heroicon-o-arrow-right class="w-6 h-6 text-gray-400 shrink-0" />
            <div class="bg-emerald-100 rounded-lg p-3 min-w-[80px]">
                <span class="text-2xl">📊</span>
                <div class="text-xs text-gray-700 mt-1">Distribuzione</div>
            </div>
            <x-heroicon-o-arrow-right class="w-6 h-6 text-gray-400 shrink-0" />
            <div class="flex flex-col gap-1">
                <div class="bg-gray-200 rounded px-2 py-1 text-xs">Owner</div>
                <div class="bg-gray-200 rounded px-2 py-1 text-xs">Creator</div>
                <div class="bg-gray-200 rounded px-2 py-1 text-xs">Piattaforma</div>
                <div class="bg-gray-200 rounded px-2 py-1 text-xs">EPP</div>
            </div>
        </div>
    </div>
</div>
