{{--
    Componente: Diritti d'Autore & Diritto di Seguito
    Sezione: Sistema
    Descrizione: Normativa italiana ed europea - cosa spetta al Creator, cosa acquisisce l'Owner
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-document-text class="w-5 h-5 text-rose-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Diritti d'Autore & Diritto di Seguito
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Normativa italiana ed europea: cosa spetta al Creator, cosa acquisisce l'Owner.
    </p>

    <!-- Disclaimer -->
    <div class="mb-6 p-4 rounded-lg bg-amber-50 border-2 border-amber-300">
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600 shrink-0 mt-1" />
            <div>
                <h4 class="text-lg font-bold text-amber-900 mb-1">Premessa Importante</h4>
                <p class="text-sm text-gray-700">
                    Le informazioni seguenti sono fornite a scopo <strong>informativo e divulgativo</strong>. 
                    Non costituiscono consulenza legale. Per questioni specifiche, consultare un avvocato 
                    specializzato in diritto d'autore.
                </p>
            </div>
        </div>
    </div>

    <!-- Diritti del Creator -->
    <div class="mb-8">
        <h4 class="mb-4 text-xl font-bold text-center text-emerald-700">
            🎨 Diritti del Creator (Sempre e Comunque)
        </h4>
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Diritti Morali -->
            <div class="p-5 border-l-4 border-emerald-600 rounded-r-lg bg-emerald-50">
                <h5 class="text-lg font-bold text-emerald-800 mb-2 flex items-center gap-2">
                    <x-heroicon-o-pencil class="w-5 h-5 text-emerald-600" />
                    Diritti Morali (Inalienabili)
                </h5>
                <p class="text-xs text-gray-600 mb-3">
                    <strong>Legge 633/1941 Art. 20</strong> - Mai cedibili, anche dopo la vendita
                </p>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                        <span><strong>Paternità</strong>: Diritto di essere sempre riconosciuto come autore</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                        <span><strong>Integrità</strong>: Diritto di opporsi a modifiche che danneggino la reputazione</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-x-circle class="w-4 h-4 text-red-600 mt-0.5 shrink-0" />
                        <span class="text-red-700"><strong>L'Owner NON può</strong>: rimuovere firma, alterare l'opera</span>
                    </li>
                </ul>
            </div>

            <!-- Diritti Patrimoniali -->
            <div class="p-5 border-l-4 border-blue-600 rounded-r-lg bg-blue-50">
                <h5 class="text-lg font-bold text-blue-800 mb-2 flex items-center gap-2">
                    <x-heroicon-o-currency-euro class="w-5 h-5 text-blue-600" />
                    Diritti Patrimoniali (Copyright)
                </h5>
                <p class="text-xs text-gray-600 mb-3">
                    <strong>Legge 633/1941 Art. 12-19</strong> - Sfruttamento economico
                </p>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                        <span><strong>Riproduzione</strong>: Solo il Creator può fare copie/stampe</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                        <span><strong>Comunicazione pubblica</strong>: Uso in pubblicità/TV/online</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
                        <span class="text-amber-700"><strong>IMPORTANTE</strong>: Comprare NFT ≠ Comprare copyright</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tabella Comparativa -->
    <div class="mb-8 p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border-2 border-purple-300">
        <h4 class="text-lg font-bold text-purple-800 mb-4 text-center flex items-center justify-center gap-2">
            <x-heroicon-o-scale class="w-6 h-6 text-purple-600" />
            Royalty Piattaforma vs Diritto di Seguito
        </h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-purple-100">
                        <th class="p-3 text-left font-bold text-purple-900">Aspetto</th>
                        <th class="p-3 text-left font-bold text-purple-900">Royalty Piattaforma</th>
                        <th class="p-3 text-left font-bold text-purple-900">Diritto di Seguito</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <tr class="border-b">
                        <td class="p-3 font-semibold">Base giuridica</td>
                        <td class="p-3">Contratto smart contract</td>
                        <td class="p-3">L. 633/1941 Art. 19bis</td>
                    </tr>
                    <tr class="border-b bg-white">
                        <td class="p-3 font-semibold">Soglia minima</td>
                        <td class="p-3"><span class="text-emerald-700 font-bold">€0</span> (tutte le vendite)</td>
                        <td class="p-3"><span class="text-blue-700 font-bold">€3,000</span></td>
                    </tr>
                    <tr class="border-b">
                        <td class="p-3 font-semibold">Percentuale</td>
                        <td class="p-3"><span class="text-emerald-700 font-bold">4.5%</span> fisso</td>
                        <td class="p-3"><span class="text-blue-700 font-bold">4% → 0.25%</span> (decrescente)</td>
                    </tr>
                    <tr class="border-b bg-white">
                        <td class="p-3 font-semibold">Chi gestisce</td>
                        <td class="p-3">Smart contract automatico</td>
                        <td class="p-3">SIAE (manuale)</td>
                    </tr>
                    <tr class="bg-green-50">
                        <td class="p-3 font-semibold">Cumulabile</td>
                        <td class="p-3 text-center" colspan="2">
                            <span class="text-green-700 font-bold">✅ SÌ!</span> Il Creator può ricevere ENTRAMBI
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 bg-white rounded-lg">
            <p class="text-gray-700 text-sm">
                <x-heroicon-o-light-bulb class="w-5 h-5 inline text-yellow-500" />
                <strong>Esempio</strong>: Vendita €50,000 tramite galleria → Creator riceve 
                <span class="text-emerald-700 font-bold">€2,250 (4.5%)</span> + 
                <span class="text-blue-700 font-bold">€2,000 (4%)</span> = 
                <span class="text-purple-700 font-bold">€4,250 totali (8.5%)</span>
            </p>
        </div>
    </div>

    <!-- Diritti dell'Owner -->
    <div class="mb-8">
        <h4 class="mb-4 text-xl font-bold text-center text-blue-700">
            🏠 Diritti dell'Owner (Acquirente)
        </h4>
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Cosa PUÒ fare -->
            <div class="p-5 border-l-4 border-green-600 rounded-r-lg bg-green-50">
                <h5 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
                    <x-heroicon-o-check class="w-5 h-5 text-green-600" />
                    Cosa PUÒ Fare
                </h5>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>• Possedere fisicamente l'opera</li>
                    <li>• Esporre privatamente (casa/ufficio)</li>
                    <li>• Rivendere l'opera (con royalty Creator)</li>
                    <li>• Donare o lasciare in eredità</li>
                    <li>• Fotografare per documentazione personale</li>
                </ul>
            </div>

            <!-- Cosa NON PUÒ fare -->
            <div class="p-5 border-l-4 border-red-600 rounded-r-lg bg-red-50">
                <h5 class="text-lg font-bold text-red-800 mb-3 flex items-center gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-red-600" />
                    Cosa NON PUÒ Fare
                </h5>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>• <strong>Riprodurre commercialmente</strong> (stampe, merchandise)</li>
                    <li>• <strong>Modificare/alterare</strong> l'opera originale</li>
                    <li>• <strong>Usare in pubblicità/marketing</strong></li>
                    <li>• <strong>Creare opere derivative</strong></li>
                    <li>• <strong>Emettere NFT aggiuntivi</strong></li>
                </ul>
                <p class="mt-3 text-xs text-red-800">
                    <strong>Violazione = Art. 171 LDA</strong>: Multe fino €15,493
                </p>
            </div>
        </div>
    </div>

    <!-- Impegno FlorenceEGI -->
    <div class="p-5 rounded-lg bg-emerald-50 border-2 border-emerald-300">
        <h4 class="text-lg font-bold text-emerald-800 mb-3 text-center flex items-center justify-center gap-2">
            <x-heroicon-o-shield-check class="w-6 h-6 text-emerald-600" />
            Impegno FlorenceEGI
        </h4>
        <ul class="space-y-2 text-sm text-gray-700 max-w-3xl mx-auto">
            <li class="flex items-start gap-2">
                <x-heroicon-o-check class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span>Garantiamo attribuzione corretta in tutti gli EGI (paternità)</span>
            </li>
            <li class="flex items-start gap-2">
                <x-heroicon-o-check class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span>Blocchiamo modifiche post-mint (integrità blockchain)</span>
            </li>
            <li class="flex items-start gap-2">
                <x-heroicon-o-check class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span>Royalty automatiche 4.5% su tutte le rivendite</span>
            </li>
            <li class="flex items-start gap-2">
                <x-heroicon-o-check class="w-4 h-4 text-emerald-600 mt-0.5 shrink-0" />
                <span>Smart contract impedisce elusione royalty (trustless enforcement)</span>
            </li>
        </ul>
    </div>
</div>
