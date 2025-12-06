{{--
    Componente: Responsabilità Plusvalenza
    Sezione: Trader Pro
    Descrizione: Gestione fiscale delle plusvalenze da trading NFT/crypto
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-chart-bar class="w-5 h-5 text-red-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Responsabilità sulle Plusvalenze
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        Le <a href="#glossario" class="text-primary-600 hover:underline font-medium">plusvalenze</a>
        derivanti dalla vendita di NFT e cripto-attività sono soggette a tassazione.
        La responsabilità del calcolo e versamento è del Trader Pro.
    </p>

    <!-- Normativa di Riferimento -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-scale class="w-5 h-5 text-red-500" />
            Quadro Normativo (L. 197/2022)
        </h4>
        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-400">
            <p class="text-sm text-gray-700 mb-3">
                La <strong>Legge di Bilancio 2023</strong> (L. 197/2022, art. 1 commi 126-147) ha introdotto
                una disciplina fiscale specifica per le cripto-attività:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-3 border border-red-200">
                    <div class="font-medium text-red-900 mb-1">Aliquota Plusvalenze</div>
                    <div class="text-2xl font-bold text-red-600">26%</div>
                    <p class="text-xs text-gray-500">Imposta sostitutiva sui redditi diversi</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-red-200">
                    <div class="font-medium text-red-900 mb-1">Soglia Esenzione</div>
                    <div class="text-2xl font-bold text-green-600">€2.000</div>
                    <p class="text-xs text-gray-500">Plusvalenze annue esenti sotto questa soglia</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calcolo Plusvalenza -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-calculator class="w-5 h-5 text-blue-500" />
            Calcolo della Plusvalenza
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="text-center mb-4">
                <div class="inline-flex items-center gap-2 text-lg">
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded font-semibold">Prezzo Vendita</span>
                    <span class="text-gray-500">−</span>
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded font-semibold">Costo Acquisto</span>
                    <span class="text-gray-500">=</span>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded font-semibold">Plusvalenza</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="font-medium text-blue-900 mb-2">Inclusi nel Costo di Acquisto:</div>
                    <ul class="text-gray-700 space-y-1">
                        <li>• Prezzo pagato per l'NFT/crypto</li>
                        <li>• Commissioni di acquisto (fee piattaforma)</li>
                        <li>• Gas fee della transazione</li>
                        <li>• Costi di minting (se creatore)</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-blue-900 mb-2">Non Deducibili:</div>
                    <ul class="text-gray-700 space-y-1">
                        <li>• Costi di custodia wallet</li>
                        <li>• Abbonamenti piattaforma</li>
                        <li>• Costi di marketing</li>
                        <li>• Spese generali non direttamente imputabili</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Esempio Pratico -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-purple-500" />
            Esempio Pratico
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="font-medium text-purple-900 mb-2">Operazione:</div>
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-1">Acquisto NFT:</td>
                            <td class="py-1 text-right font-medium">€500.00</td>
                        </tr>
                        <tr>
                            <td class="py-1">+ Fee acquisto (2.5%):</td>
                            <td class="py-1 text-right font-medium">€12.50</td>
                        </tr>
                        <tr class="border-t border-purple-200">
                            <td class="py-1 font-medium">Costo totale:</td>
                            <td class="py-1 text-right font-bold text-red-600">€512.50</td>
                        </tr>
                        <tr>
                            <td class="py-1">Vendita NFT:</td>
                            <td class="py-1 text-right font-medium">€1.500.00</td>
                        </tr>
                        <tr>
                            <td class="py-1">- Fee vendita (5%):</td>
                            <td class="py-1 text-right font-medium">−€75.00</td>
                        </tr>
                        <tr class="border-t border-purple-200">
                            <td class="py-1 font-medium">Netto vendita:</td>
                            <td class="py-1 text-right font-bold text-green-600">€1.425.00</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">Calcolo Imposta:</div>
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-1">Plusvalenza lorda:</td>
                            <td class="py-1 text-right font-medium">€912.50</td>
                        </tr>
                        <tr>
                            <td class="py-1">Soglia esenzione:</td>
                            <td class="py-1 text-right font-medium text-green-600">−€2.000</td>
                        </tr>
                        <tr class="border-t border-purple-200">
                            <td class="py-1 font-medium">Imponibile:</td>
                            <td class="py-1 text-right font-bold">€0.00</td>
                        </tr>
                        <tr>
                            <td class="py-1">Imposta (26%):</td>
                            <td class="py-1 text-right font-bold text-green-600">€0.00</td>
                        </tr>
                    </table>
                    <div class="mt-2 p-2 bg-green-100 rounded text-xs text-green-700">
                        ✓ Nessuna imposta dovuta (sotto soglia €2.000)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Obblighi Dichiarativi -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-amber-500" />
            Obblighi Dichiarativi
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="font-medium text-amber-900 mb-2">Quadro RT (Redditi Diversi)</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Dichiarazione plusvalenze/minusvalenze</li>
                    <li>• Calcolo imposta sostitutiva 26%</li>
                    <li>• Compensazione minusvalenze (4 anni)</li>
                </ul>
            </div>
            <div class="bg-amber-50 rounded-lg p-4">
                <div class="font-medium text-amber-900 mb-2">Quadro RW (Monitoraggio)</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Obbligo se detenute su wallet esteri</li>
                    <li>• Valore al 31/12 di ogni anno</li>
                    <li>• IVAFE: 2‰ sul valore (0.2%)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Strumenti Piattaforma -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
        <h5 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
            <x-heroicon-o-document-chart-bar class="w-5 h-5" />
            Strumenti della Piattaforma
        </h5>
        <p class="text-sm text-gray-700 mb-3">
            FlorenceEGI fornisce strumenti per facilitare gli adempimenti fiscali:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                <x-heroicon-o-document-arrow-down class="w-6 h-6 text-green-500 mx-auto mb-1" />
                <div class="text-sm font-medium">Report Fiscale</div>
                <p class="text-xs text-gray-500">Export per commercialista</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                <x-heroicon-o-calculator class="w-6 h-6 text-green-500 mx-auto mb-1" />
                <div class="text-sm font-medium">Calcolo Plusvalenze</div>
                <p class="text-xs text-gray-500">Metodo LIFO automatico</p>
            </div>
            <div class="bg-white rounded-lg p-3 border border-green-200 text-center">
                <x-heroicon-o-clock class="w-6 h-6 text-green-500 mx-auto mb-1" />
                <div class="text-sm font-medium">Storico Operazioni</div>
                <p class="text-xs text-gray-500">10 anni di archivio</p>
            </div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="mt-6 bg-gray-100 rounded-lg p-4 text-xs text-gray-500">
        <strong>Disclaimer:</strong> Le informazioni fiscali sono fornite a titolo informativo.
        Per la corretta applicazione della normativa, si consiglia di consultare un commercialista
        o un consulente fiscale qualificato.
    </div>
</div>
