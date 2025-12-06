{{--
    Componente: Fatturazione Elettronica
    Sezione: Merchant & Pagamenti
    Descrizione: Sistema di fatturazione elettronica integrato
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-document-text class="w-5 h-5 text-rose-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Fatturazione Elettronica
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI gestisce la
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">fatturazione elettronica</a>
        in conformità alle normative italiane (obbligo FE) tramite integrazione con il
        Sistema di Interscambio (SdI).
    </p>

    <!-- Flusso FE -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrow-path class="w-5 h-5 text-rose-500" />
            Flusso Fatturazione Elettronica
        </h4>
        <div class="bg-rose-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 text-center">
                <div class="bg-white rounded-lg p-3 border border-rose-200">
                    <x-heroicon-o-shopping-cart class="w-6 h-6 text-rose-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Transazione</div>
                </div>
                <div class="flex items-center justify-center">
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400" />
                </div>
                <div class="bg-white rounded-lg p-3 border border-rose-200">
                    <x-heroicon-o-document-plus class="w-6 h-6 text-rose-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Generazione XML</div>
                </div>
                <div class="flex items-center justify-center">
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400" />
                </div>
                <div class="bg-white rounded-lg p-3 border border-rose-200">
                    <x-heroicon-o-paper-airplane class="w-6 h-6 text-rose-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Invio SdI</div>
                </div>
            </div>
            <div class="flex justify-center mt-2">
                <x-heroicon-o-arrow-down class="w-5 h-5 text-gray-400" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-center mt-2">
                <div class="bg-green-100 rounded-lg p-3 border border-green-200">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Consegnata</div>
                </div>
                <div class="bg-amber-100 rounded-lg p-3 border border-amber-200">
                    <x-heroicon-o-clock class="w-6 h-6 text-amber-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Mancata Consegna</div>
                </div>
                <div class="bg-red-100 rounded-lg p-3 border border-red-200">
                    <x-heroicon-o-x-circle class="w-6 h-6 text-red-500 mx-auto mb-1" />
                    <div class="text-xs font-medium">Scartata</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipi di Documento -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-duplicate class="w-5 h-5 text-blue-500" />
            Tipi di Documento Generati
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">TD01</span>
                    <span class="font-medium text-gray-900">Fattura</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Fee piattaforma verso merchant P.IVA</li>
                    <li>• Vendite B2B</li>
                    <li>• Servizi professionali</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded">TD04</span>
                    <span class="font-medium text-gray-900">Nota di Credito</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Rimborsi</li>
                    <li>• Storni parziali</li>
                    <li>• Rettifiche</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">TD26</span>
                    <span class="font-medium text-gray-900">Cessione Beni Ammortizzabili</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Vendita NFT come beni digitali</li>
                    <li>• Trasferimento proprietà</li>
                </ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-1 rounded">RIC</span>
                    <span class="font-medium text-gray-900">Ricevuta</span>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Vendite B2C sotto soglia</li>
                    <li>• Donazioni (ricevuta donazione)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Codice Destinatario -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-at-symbol class="w-5 h-5 text-amber-500" />
            Codice Destinatario e PEC
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-amber-900 mb-2">Per Aziende (B2B)</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Codice SDI a 7 caratteri</li>
                        <li>• Oppure PEC aziendale</li>
                        <li>• Registrato in anagrafica</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-amber-900 mb-2">Per Privati (B2C)</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Codice: <code class="bg-amber-100 px-1 rounded">0000000</code></li>
                        <li>• Copia PDF inviata via email</li>
                        <li>• Disponibile nel cassetto fiscale</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Automazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-green-500" />
            Automazione Completa
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Il sistema genera automaticamente la documentazione fiscale:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <div class="text-2xl font-bold text-green-600">< 1 min</div>
                    <div class="text-xs text-gray-500">Generazione XML</div>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <div class="text-2xl font-bold text-green-600">5 giorni</div>
                    <div class="text-xs text-gray-500">Invio SdI max</div>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                    <div class="text-2xl font-bold text-green-600">10 anni</div>
                    <div class="text-xs text-gray-500">Conservazione</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-rose-400">
        <h5 class="font-medium text-gray-900 mb-2">Regime Forfettario</h5>
        <p class="text-sm text-gray-600">
            I soggetti in regime forfettario sono <strong>esonerati</strong> dall'obbligo
            di fatturazione elettronica per operazioni fino a €25.000 (fino al 2024).
            Dal 2024 l'obbligo è esteso a tutti.
        </p>
    </div>
</div>
