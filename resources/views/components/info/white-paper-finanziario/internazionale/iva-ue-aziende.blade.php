{{--
    Componente: IVA UE Aziende
    Sezione: Internazionale
    Descrizione: Regime IVA per vendite ad aziende UE (B2B)
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-building-office class="w-5 h-5 text-indigo-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            IVA per Aziende UE (B2B)
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Le vendite di servizi digitali (inclusi NFT) ad <strong>aziende con P.IVA</strong>
        in altri paesi UE seguono il regime di
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">reverse charge</a>
        intracomunitario.
    </p>

    <!-- Reverse Charge -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-indigo-500" />
            Meccanismo Reverse Charge
        </h4>
        <div class="bg-indigo-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-white rounded-lg p-3 border border-indigo-200 text-center">
                    <div class="text-3xl mb-2">🇮🇹</div>
                    <div class="text-sm font-medium text-gray-900">Venditore IT</div>
                    <p class="text-xs text-gray-500">Fattura senza IVA</p>
                </div>
                <div class="flex items-center justify-center">
                    <x-heroicon-o-arrow-right class="w-8 h-8 text-indigo-400" />
                </div>
                <div class="bg-white rounded-lg p-3 border border-indigo-200 text-center">
                    <div class="text-3xl mb-2">🇩🇪</div>
                    <div class="text-sm font-medium text-gray-900">Acquirente DE</div>
                    <p class="text-xs text-gray-500">Integra IVA locale</p>
                </div>
            </div>
            <p class="text-sm text-gray-700">
                Con il <strong>reverse charge</strong>, il venditore italiano emette fattura
                senza IVA. L'acquirente estero integra l'IVA nel proprio paese e la detrae
                contestualmente (operazione neutra).
            </p>
        </div>
    </div>

    <!-- Requisiti -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-green-500" />
            Requisiti per l'Applicazione
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Documenti Richiesti</div>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span><strong>Partita IVA UE</strong> dell'acquirente verificata</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Verifica tramite <strong>VIES</strong> (sistema UE)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Fattura con dicitura "Reverse charge art. 7-ter"</span>
                    </li>
                </ul>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="font-medium text-blue-900 mb-2">Verifica VIES</div>
                <p class="text-sm text-gray-700 mb-2">
                    Prima di emettere fattura senza IVA, verificare sempre la validità
                    della P.IVA su:
                </p>
                <a href="https://ec.europa.eu/taxation_customs/vies/" 
                   target="_blank"
                   class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                    ec.europa.eu/taxation_customs/vies
                </a>
            </div>
        </div>
    </div>

    <!-- Fatturazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-purple-500" />
            Contenuto della Fattura B2B Intra-UE
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Dati Obbligatori</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• P.IVA italiana del venditore</li>
                        <li>• P.IVA UE dell'acquirente</li>
                        <li>• Descrizione dettagliata servizio</li>
                        <li>• Imponibile in EUR</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Diciture da Inserire</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• "Operazione non soggetta a IVA"</li>
                        <li>• "Art. 7-ter DPR 633/72"</li>
                        <li>• "Reverse charge"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modello INTRA -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-chart-bar class="w-5 h-5 text-amber-500" />
            Modello INTRASTAT
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Le prestazioni di servizi intra-UE vanno dichiarate nel modello <strong>INTRASTAT Servizi</strong>:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-center">
                <div class="bg-white rounded-lg p-3 border border-amber-200">
                    <div class="font-medium text-amber-900">Soglia Trimestrale</div>
                    <div class="text-lg font-bold text-amber-600">€50.000</div>
                    <p class="text-xs text-gray-500">per obbligo mensile</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-amber-200">
                    <div class="font-medium text-amber-900">Scadenza</div>
                    <div class="text-lg font-bold text-amber-600">25 del mese</div>
                    <p class="text-xs text-gray-500">successivo al periodo</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-amber-200">
                    <div class="font-medium text-amber-900">Modalità</div>
                    <div class="text-lg font-bold text-amber-600">Telematica</div>
                    <p class="text-xs text-gray-500">via Agenzia Dogane</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400">
        <h5 class="font-medium text-gray-900 mb-2">FlorenceEGI e B2B</h5>
        <p class="text-sm text-gray-600">
            La piattaforma verifica automaticamente la P.IVA UE tramite VIES e
            genera fatture conformi per le transazioni B2B intracomunitarie.
        </p>
    </div>
</div>
