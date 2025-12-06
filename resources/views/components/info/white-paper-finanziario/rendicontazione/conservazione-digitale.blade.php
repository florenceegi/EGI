{{--
    Componente: Conservazione Digitale
    Sezione: Rendicontazione & Archiviazione
    Descrizione: Sistema di conservazione sostitutiva a norma
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-archive-box class="w-5 h-5 text-indigo-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Conservazione Digitale a Norma
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI garantisce la
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">conservazione sostitutiva</a>
        dei documenti fiscali conforme al CAD (D.Lgs. 82/2005) e alle regole AgID.
    </p>

    <!-- Requisiti Normativi -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-scale class="w-5 h-5 text-indigo-500" />
            Requisiti Normativi
        </h4>
        <div class="bg-indigo-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-indigo-900 mb-2">Normativa di Riferimento</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• <strong>CAD</strong> - D.Lgs. 82/2005</li>
                        <li>• <strong>DPCM 3/12/2013</strong> - Regole tecniche</li>
                        <li>• <strong>Linee Guida AgID</strong> 2021</li>
                        <li>• <strong>D.M. 17/06/2014</strong> - Fattura PA</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-indigo-900 mb-2">Durata Conservazione</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Fatture: <strong>10 anni</strong></li>
                        <li>• Documenti contabili: <strong>10 anni</strong></li>
                        <li>• Contratti: <strong>10 anni</strong></li>
                        <li>• Log transazioni: <strong>10 anni</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Processo di Conservazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-blue-500" />
            Processo di Conservazione
        </h4>
        <div class="space-y-3">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">1</div>
                <div class="flex-1 bg-blue-50 rounded-lg p-3">
                    <div class="font-medium text-blue-900">Acquisizione</div>
                    <p class="text-sm text-gray-600">Il documento (fattura, ricevuta, log) viene acquisito nel sistema</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">2</div>
                <div class="flex-1 bg-blue-50 rounded-lg p-3">
                    <div class="font-medium text-blue-900">Firma Digitale</div>
                    <p class="text-sm text-gray-600">Applicazione firma digitale qualificata (FEQ) con timestamp</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">3</div>
                <div class="flex-1 bg-blue-50 rounded-lg p-3">
                    <div class="font-medium text-blue-900">Marcatura Temporale</div>
                    <p class="text-sm text-gray-600">Apposizione marca temporale da TSA (Time Stamp Authority) accreditata</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">4</div>
                <div class="flex-1 bg-blue-50 rounded-lg p-3">
                    <div class="font-medium text-blue-900">Pacchetto di Archiviazione</div>
                    <p class="text-sm text-gray-600">Creazione PdA (Pacchetto di Archiviazione) con indice e metadati</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">5</div>
                <div class="flex-1 bg-green-50 rounded-lg p-3">
                    <div class="font-medium text-green-900">Conservazione</div>
                    <p class="text-sm text-gray-600">Archiviazione sicura con backup georidondanti (minimo 10 anni)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Garanzie -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-5 h-5 text-green-500" />
            Garanzie di Integrità
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <x-heroicon-o-finger-print class="w-8 h-8 text-green-600 mx-auto mb-2" />
                <div class="font-medium text-green-900">Autenticità</div>
                <p class="text-xs text-gray-500 mt-1">Firma digitale qualificata</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-blue-600 mx-auto mb-2" />
                <div class="font-medium text-blue-900">Integrità</div>
                <p class="text-xs text-gray-500 mt-1">Hash crittografici SHA-256</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <x-heroicon-o-clock class="w-8 h-8 text-purple-600 mx-auto mb-2" />
                <div class="font-medium text-purple-900">Data Certa</div>
                <p class="text-xs text-gray-500 mt-1">Marca temporale TSA</p>
            </div>
        </div>
    </div>

    <!-- Provider -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-server-stack class="w-5 h-5 text-amber-500" />
            Provider di Conservazione
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                La conservazione è affidata a provider <strong>accreditati AgID</strong>:
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="bg-white text-amber-700 text-sm px-3 py-1 rounded-full border border-amber-200">ISO 27001</span>
                <span class="bg-white text-amber-700 text-sm px-3 py-1 rounded-full border border-amber-200">Certificazione AgID</span>
                <span class="bg-white text-amber-700 text-sm px-3 py-1 rounded-full border border-amber-200">Data Center EU</span>
                <span class="bg-white text-amber-700 text-sm px-3 py-1 rounded-full border border-amber-200">Backup 3-2-1</span>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400">
        <h5 class="font-medium text-gray-900 mb-2">Valore Legale</h5>
        <p class="text-sm text-gray-600">
            I documenti conservati a norma hanno <strong>pieno valore legale e probatorio</strong>
            in sede civile, penale e tributaria, come gli originali cartacei.
        </p>
    </div>
</div>
