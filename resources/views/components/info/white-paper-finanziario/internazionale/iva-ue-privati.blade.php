{{--
    Componente: IVA UE Privati
    Sezione: Internazionale
    Descrizione: Regime IVA per vendite a privati UE (B2C)
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-globe-europe-africa class="w-5 h-5 text-blue-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            IVA per Privati UE (B2C)
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Le vendite di servizi digitali (inclusi NFT) a <strong>privati consumatori</strong>
        residenti in altri paesi UE seguono il regime
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">OSS</a>
        (One Stop Shop).
    </p>

    <!-- Principio di Destinazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-5 h-5 text-blue-500" />
            Principio di Destinazione
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Per i servizi elettronici B2C, l'IVA si applica nel <strong>paese del consumatore</strong>:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-3 border border-blue-200">
                    <div class="font-medium text-blue-900 mb-2">Esempio: Vendita a Francia</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Venditore italiano P.IVA</li>
                        <li>• Acquirente privato francese</li>
                        <li>• IVA applicabile: <strong>20% (FR)</strong></li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg p-3 border border-blue-200">
                    <div class="font-medium text-blue-900 mb-2">Esempio: Vendita a Germania</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Venditore italiano P.IVA</li>
                        <li>• Acquirente privato tedesco</li>
                        <li>• IVA applicabile: <strong>19% (DE)</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Aliquote IVA UE -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-table-cells class="w-5 h-5 text-purple-500" />
            Aliquote IVA nei Principali Paesi UE
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-purple-50">
                        <th class="text-left p-2 font-semibold text-purple-900">Paese</th>
                        <th class="text-center p-2 font-semibold text-purple-900">Aliquota</th>
                        <th class="text-left p-2 font-semibold text-purple-900">Paese</th>
                        <th class="text-center p-2 font-semibold text-purple-900">Aliquota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-2">🇩🇪 Germania</td>
                        <td class="p-2 text-center font-medium">19%</td>
                        <td class="p-2">🇫🇷 Francia</td>
                        <td class="p-2 text-center font-medium">20%</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2">🇪🇸 Spagna</td>
                        <td class="p-2 text-center font-medium">21%</td>
                        <td class="p-2">🇳🇱 Paesi Bassi</td>
                        <td class="p-2 text-center font-medium">21%</td>
                    </tr>
                    <tr>
                        <td class="p-2">🇧🇪 Belgio</td>
                        <td class="p-2 text-center font-medium">21%</td>
                        <td class="p-2">🇦🇹 Austria</td>
                        <td class="p-2 text-center font-medium">20%</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2">🇵🇹 Portogallo</td>
                        <td class="p-2 text-center font-medium">23%</td>
                        <td class="p-2">🇬🇷 Grecia</td>
                        <td class="p-2 text-center font-medium">24%</td>
                    </tr>
                    <tr>
                        <td class="p-2">🇸🇪 Svezia</td>
                        <td class="p-2 text-center font-medium">25%</td>
                        <td class="p-2">🇩🇰 Danimarca</td>
                        <td class="p-2 text-center font-medium">25%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Regime OSS -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-green-500" />
            Regime OSS (One Stop Shop)
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                Il regime OSS semplifica gli adempimenti IVA per le vendite B2C in UE:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-green-900 mb-2">Vantaggi</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Una sola registrazione IVA (in Italia)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Una dichiarazione trimestrale per tutta l'UE</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span>Un unico versamento all'Agenzia Entrate</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-green-900 mb-2">Obblighi</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Registrazione al portale OSS italiano</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Applicare IVA del paese acquirente</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                            <span>Conservare documentazione 10 anni</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Soglia €10.000 -->
    <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-400">
        <h5 class="font-medium text-amber-900 mb-2 flex items-center gap-2">
            <x-heroicon-o-information-circle class="w-5 h-5" />
            Soglia €10.000 Annui
        </h5>
        <p class="text-sm text-gray-700">
            Se le vendite B2C verso altri paesi UE non superano <strong>€10.000/anno</strong>,
            è possibile applicare l'IVA italiana (22%) anziché quella del paese di destinazione.
            Superata la soglia, scatta l'obbligo OSS.
        </p>
    </div>
</div>
