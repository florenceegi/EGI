{{--
    Componente: Rendicontazione Automatica
    Sezione: Rendicontazione & Archiviazione
    Descrizione: Sistema di reportistica automatica per utenti e fisco
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-chart-bar class="w-5 h-5 text-teal-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Rendicontazione Automatica
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI genera automaticamente report periodici per facilitare
        gli adempimenti fiscali e la gestione contabile degli utenti.
    </p>

    <!-- Tipi di Report -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-chart-bar class="w-5 h-5 text-teal-500" />
            Tipologie di Report
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">MENSILE</span>
                        <span class="font-medium text-gray-900">Estratto Conto</span>
                    </div>
                    <span class="text-sm text-gray-500">Generato il 1° del mese</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Riepilogo entrate/uscite del mese</li>
                    <li>• Dettaglio fee applicate</li>
                    <li>• Saldo iniziale e finale wallet</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded">TRIMESTRALE</span>
                        <span class="font-medium text-gray-900">Report IVA</span>
                    </div>
                    <span class="text-sm text-gray-500">Per liquidazione IVA</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Totale imponibile vendite</li>
                    <li>• IVA a debito per aliquota</li>
                    <li>• Operazioni intra/extra UE</li>
                </ul>
            </div>
            <div class="border border-teal-200 rounded-lg p-4 bg-teal-50">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-teal-200 text-teal-800 text-xs font-semibold px-2 py-1 rounded">ANNUALE</span>
                        <span class="font-medium text-gray-900">Certificazione Fiscale</span>
                    </div>
                    <span class="text-sm text-teal-700">Entro 28 febbraio</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Riepilogo completo anno fiscale</li>
                    <li>• Pre-compilazione Quadro RT (plusvalenze)</li>
                    <li>• Certificazione donazioni per detrazioni</li>
                    <li>• Export per commercialista</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Formati Export -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-blue-500" />
            Formati di Export
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <div class="text-2xl mb-1">📊</div>
                <div class="text-sm font-medium text-green-900">Excel</div>
                <span class="text-xs text-gray-500">.xlsx</span>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <div class="text-2xl mb-1">📄</div>
                <div class="text-sm font-medium text-red-900">PDF</div>
                <span class="text-xs text-gray-500">.pdf</span>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <div class="text-2xl mb-1">📋</div>
                <div class="text-sm font-medium text-blue-900">CSV</div>
                <span class="text-xs text-gray-500">.csv</span>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 text-center">
                <div class="text-2xl mb-1">🔗</div>
                <div class="text-sm font-medium text-amber-900">XML/JSON</div>
                <span class="text-xs text-gray-500">API</span>
            </div>
        </div>
    </div>

    <!-- Automazioni -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-purple-500" />
            Automazioni Disponibili
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Invio Automatico</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>Report mensile via email</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>Alert soglie fiscali raggiunte</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>Reminder scadenze</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Integrazioni</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>Export per software contabili</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>Formato compatibile Cassetto Fiscale</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-purple-600 mt-0.5 shrink-0" />
                            <span>API per commercialisti</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-green-500" />
            Dashboard Real-Time
        </h4>
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
            <p class="text-sm text-gray-700 mb-3">
                Accedi in tempo reale a:
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                <div class="bg-white rounded-lg p-2 border border-green-200">
                    <div class="text-lg font-bold text-green-600">€ ****</div>
                    <div class="text-xs text-gray-500">Saldo Wallet</div>
                </div>
                <div class="bg-white rounded-lg p-2 border border-green-200">
                    <div class="text-lg font-bold text-blue-600">123</div>
                    <div class="text-xs text-gray-500">Transazioni Mese</div>
                </div>
                <div class="bg-white rounded-lg p-2 border border-green-200">
                    <div class="text-lg font-bold text-purple-600">€ ****</div>
                    <div class="text-xs text-gray-500">Totale Entrate</div>
                </div>
                <div class="bg-white rounded-lg p-2 border border-green-200">
                    <div class="text-lg font-bold text-amber-600">€ ****</div>
                    <div class="text-xs text-gray-500">Fee Pagate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-teal-400">
        <h5 class="font-medium text-gray-900 mb-2">Supporto Commercialista</h5>
        <p class="text-sm text-gray-600">
            È possibile concedere <strong>accesso in sola lettura</strong> al proprio commercialista
            per visualizzare i report e scaricare la documentazione necessaria.
        </p>
    </div>
</div>
