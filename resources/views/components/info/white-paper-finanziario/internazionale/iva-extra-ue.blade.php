{{--
    Componente: IVA Extra UE
    Sezione: Internazionale
    Descrizione: Regime IVA per transazioni con paesi extra-UE
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-globe-alt class="w-5 h-5 text-orange-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            IVA per Paesi Extra-UE
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Le vendite di servizi digitali a soggetti residenti in paesi <strong>extra-UE</strong>
        (USA, UK post-Brexit, Svizzera, ecc.) seguono regole specifiche per
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">l'esportazione di servizi</a>.
    </p>

    <!-- Principio Generale -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-orange-500" />
            Principio Generale
        </h4>
        <div class="bg-orange-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                I servizi elettronici (NFT inclusi) venduti a soggetti extra-UE sono
                generalmente <strong>fuori campo IVA</strong> in Italia:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">B2B</span>
                        <span class="font-medium text-gray-900">Azienda Extra-UE</span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Fuori campo IVA (art. 7-ter)</li>
                        <li>• Fattura senza IVA</li>
                        <li>• Nessun obbligo INTRASTAT</li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">B2C</span>
                        <span class="font-medium text-gray-900">Privato Extra-UE</span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Fuori campo IVA italiana</li>
                        <li>• Possibile IVA locale nel paese cliente</li>
                        <li>• Verificare normativa locale</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Paesi Principali -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-flag class="w-5 h-5 text-blue-500" />
            Focus su Paesi Principali
        </h4>
        <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">🇺🇸</span>
                    <span class="font-medium text-gray-900">Stati Uniti</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Nessuna IVA federale (ma possibile Sales Tax statale)</li>
                    <li>• Verificare obblighi di nexus per high-volume sellers</li>
                    <li>• Fattura in USD accettata</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">🇬🇧</span>
                    <span class="font-medium text-gray-900">Regno Unito (Post-Brexit)</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• VAT 20% per B2C se registrati UK</li>
                    <li>• Soglia registrazione £0 per servizi digitali</li>
                    <li>• Reverse charge per B2B</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">🇨🇭</span>
                    <span class="font-medium text-gray-900">Svizzera</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Fuori campo IVA italiana</li>
                    <li>• IVA svizzera 8.1% se superate soglie</li>
                    <li>• Soglia CHF 100.000 per registrazione</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Fatturazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-purple-500" />
            Fatturazione Extra-UE
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Contenuto Fattura</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Dati completi venditore italiano</li>
                        <li>• Dati acquirente estero</li>
                        <li>• Descrizione servizio in inglese</li>
                        <li>• Importo (EUR o valuta estera)</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Diciture</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• "Operazione fuori campo IVA"</li>
                        <li>• "Art. 7-ter DPR 633/72"</li>
                        <li>• "Service outside EU VAT scope"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Valute -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-currency-dollar class="w-5 h-5 text-green-500" />
            Gestione Valute Estere
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Per transazioni in valuta estera:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-3 border border-green-200">
                    <div class="font-medium text-green-900 mb-1">Contabilità</div>
                    <p class="text-sm text-gray-600">
                        Convertire in EUR al cambio BCE del giorno dell'operazione
                    </p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200">
                    <div class="font-medium text-green-900 mb-1">Fattura</div>
                    <p class="text-sm text-gray-600">
                        Può essere in valuta estera con indicazione del cambio
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert -->
    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
        <div class="flex gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 shrink-0" />
            <div>
                <h5 class="font-medium text-red-900 mb-1">Paesi in Blacklist</h5>
                <p class="text-sm text-red-700">
                    Transazioni con paesi in blacklist fiscale richiedono comunicazioni
                    aggiuntive all'Agenzia delle Entrate e possono avere limitazioni.
                </p>
            </div>
        </div>
    </div>
</div>
