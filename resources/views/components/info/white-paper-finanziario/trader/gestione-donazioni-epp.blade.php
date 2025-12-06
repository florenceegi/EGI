{{--
    Componente: Gestione Donazioni EPP (per Trader)
    Sezione: Trader Pro
    Descrizione: Come i trader possono destinare parte dei ricavi a EPP
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-heart class="w-5 h-5 text-pink-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Donazioni EPP dai Trader Pro
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        I <a href="#glossario" class="text-primary-600 hover:underline font-medium">Trader Pro</a>
        possono destinare una percentuale delle loro vendite agli
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">EPP</a> (Enti Progetto Partecipato),
        beneficiando di vantaggi fiscali e di immagine.
    </p>

    <!-- Meccanismo di Donazione -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-pink-500" />
            Meccanismo di Donazione Automatica
        </h4>
        <div class="space-y-3">
            <div class="bg-pink-50 rounded-lg p-4">
                <div class="font-medium text-pink-900 mb-2">Configurazione per il Trader</div>
                <p class="text-sm text-gray-700 mb-3">
                    Il Trader Pro può impostare una donazione automatica su ogni vendita:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="bg-white rounded-lg p-3 border border-pink-200 text-center">
                        <div class="text-2xl font-bold text-pink-600">1-5%</div>
                        <div class="text-xs text-gray-500">del prezzo di vendita</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-pink-200 text-center">
                        <div class="text-2xl font-bold text-pink-600">1-∞</div>
                        <div class="text-xs text-gray-500">EPP selezionabili</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-pink-200 text-center">
                        <div class="text-2xl font-bold text-pink-600">Auto</div>
                        <div class="text-xs text-gray-500">Trasferimento istantaneo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flusso Operativo -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-500" />
            Flusso della Donazione
        </h4>
        <div class="relative">
            <div class="border-l-2 border-blue-200 ml-4 pl-6 space-y-4">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="absolute -left-8 w-4 h-4 bg-blue-500 rounded-full"></div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <div class="font-medium text-blue-900">Vendita NFT Completata</div>
                        <p class="text-sm text-gray-600">Acquirente paga €100 per un NFT del Trader</p>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="relative">
                    <div class="absolute -left-8 w-4 h-4 bg-blue-500 rounded-full"></div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <div class="font-medium text-blue-900">Calcolo Automatico</div>
                        <p class="text-sm text-gray-600">Sistema calcola: 3% donazione = €3.00</p>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="relative">
                    <div class="absolute -left-8 w-4 h-4 bg-green-500 rounded-full"></div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <div class="font-medium text-green-900">Distribuzione</div>
                        <div class="text-sm text-gray-600 grid grid-cols-2 gap-2 mt-2">
                            <div>→ Trader: €92.00 (netto fee)</div>
                            <div>→ EPP: €3.00 (donazione)</div>
                            <div>→ Piattaforma: €5.00 (fee 5%)</div>
                            <div></div>
                        </div>
                    </div>
                </div>
                <!-- Step 4 -->
                <div class="relative">
                    <div class="absolute -left-8 w-4 h-4 bg-purple-500 rounded-full"></div>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <div class="font-medium text-purple-900">Documentazione</div>
                        <p class="text-sm text-gray-600">Ricevuta donazione generata per il Trader</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefici Fiscali -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-receipt-percent class="w-5 h-5 text-green-500" />
            Benefici Fiscali per il Trader
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Trader Persona Fisica (P.IVA)</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span><strong>Detrazione 30%</strong> fino a €30.000/anno</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Oppure <strong>deduzione</strong> fino al 10% del reddito</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Donazione a ETS iscritti al RUNTS</span>
                    </li>
                </ul>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="font-medium text-green-900 mb-2">Trader Società (SRL/SPA)</div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span><strong>Deduzione</strong> fino al 10% del reddito d'impresa</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Eccedenza riportabile in 4 anni successivi</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                        <span>Valorizzazione CSR (Corporate Social Responsibility)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Badge e Visibilità -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-sparkles class="w-5 h-5 text-amber-500" />
            Badge e Visibilità
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                I Trader Pro che donano agli EPP ottengono badge speciali e maggiore visibilità:
            </p>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center gap-2 bg-white rounded-full px-3 py-1 border border-amber-200">
                    <x-heroicon-o-heart class="w-4 h-4 text-pink-500" />
                    <span class="text-sm font-medium">Supporter Bronze</span>
                    <span class="text-xs text-gray-400">(€100+)</span>
                </div>
                <div class="flex items-center gap-2 bg-white rounded-full px-3 py-1 border border-gray-300">
                    <x-heroicon-o-heart class="w-4 h-4 text-gray-400" />
                    <span class="text-sm font-medium">Supporter Silver</span>
                    <span class="text-xs text-gray-400">(€500+)</span>
                </div>
                <div class="flex items-center gap-2 bg-white rounded-full px-3 py-1 border border-amber-400">
                    <x-heroicon-o-heart class="w-4 h-4 text-amber-500" />
                    <span class="text-sm font-medium">Supporter Gold</span>
                    <span class="text-xs text-gray-400">(€1.000+)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-pink-400">
        <h5 class="font-medium text-gray-900 mb-2">Report Donazioni</h5>
        <p class="text-sm text-gray-600">
            Il sistema genera automaticamente un <strong>report annuale delle donazioni</strong>
            utile per la dichiarazione dei redditi, con dettaglio di ogni EPP beneficiario
            e le relative ricevute.
        </p>
    </div>
</div>
