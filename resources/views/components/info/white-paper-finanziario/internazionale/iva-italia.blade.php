{{--
    Componente: IVA Italia
    Sezione: Internazionale
    Descrizione: Regime IVA per transazioni nazionali italiane
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-flag class="w-5 h-5 text-green-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            IVA per Transazioni in Italia
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Le transazioni tra soggetti residenti in Italia seguono il regime
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">IVA</a>
        ordinario italiano, con alcune particolarità per i beni digitali e gli NFT.
    </p>

    <!-- Aliquote IVA -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-receipt-percent class="w-5 h-5 text-green-500" />
            Aliquote IVA Applicabili
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-green-600">22%</div>
                <div class="text-sm font-medium text-gray-900 mt-1">Aliquota Ordinaria</div>
                <p class="text-xs text-gray-500 mt-1">NFT, servizi digitali, fee piattaforma</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-blue-600">10%</div>
                <div class="text-sm font-medium text-gray-900 mt-1">Aliquota Ridotta</div>
                <p class="text-xs text-gray-500 mt-1">Alcuni servizi culturali (raro per NFT)</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-purple-600">0%</div>
                <div class="text-sm font-medium text-gray-900 mt-1">Esente</div>
                <p class="text-xs text-gray-500 mt-1">Donazioni liberali (no controprestazione)</p>
            </div>
        </div>
    </div>

    <!-- Casistiche -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-list-bullet class="w-5 h-5 text-blue-500" />
            Casistiche per Tipo di Transazione
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">B2C</span>
                    <span class="font-medium text-gray-900">Vendita NFT a Privato</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Venditore P.IVA → IVA 22% inclusa nel prezzo</li>
                    <li>• Venditore privato → Nessuna IVA (occasionale)</li>
                    <li>• Fattura/ricevuta al consumatore finale</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">B2B</span>
                    <span class="font-medium text-gray-900">Vendita NFT tra Aziende</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• IVA 22% in fattura elettronica</li>
                    <li>• Acquirente detrae IVA se inerente</li>
                    <li>• Obbligo fattura entro 12 giorni</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded">DONAZIONE</span>
                    <span class="font-medium text-gray-900">Donazione a EPP</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Operazione fuori campo IVA (art. 2 DPR 633/72)</li>
                    <li>• Nessuna fattura richiesta</li>
                    <li>• Ricevuta di donazione sufficiente</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Fee Piattaforma -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-building-storefront class="w-5 h-5 text-amber-500" />
            IVA sulle Fee della Piattaforma
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Le commissioni applicate da FlorenceEGI sono soggette a IVA:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-3 border border-amber-200">
                    <div class="font-medium text-amber-900 mb-1">Fee su Vendita</div>
                    <div class="text-sm text-gray-600">
                        5% + IVA 22% = <strong>6.1%</strong> effettivo
                    </div>
                </div>
                <div class="bg-white rounded-lg p-3 border border-amber-200">
                    <div class="font-medium text-amber-900 mb-1">Fee su Minting</div>
                    <div class="text-sm text-gray-600">
                        Importo fisso + IVA 22%
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                Per i soggetti P.IVA, l'IVA sulle fee è detraibile se inerente all'attività.
            </p>
        </div>
    </div>

    <!-- Regime Forfettario -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
        <h5 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5" />
            Regime Forfettario
        </h5>
        <p class="text-sm text-gray-700 mb-2">
            I soggetti in <strong>regime forfettario</strong> (ricavi < €85.000) non applicano IVA:
        </p>
        <ul class="text-sm text-gray-700 space-y-1">
            <li>• Fattura senza IVA con dicitura "Operazione in franchigia"</li>
            <li>• Non possono detrarre IVA sugli acquisti</li>
            <li>• Imposta sostitutiva 15% (5% per nuove attività)</li>
        </ul>
    </div>
</div>
