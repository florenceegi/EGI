{{--
    Componente: Metodi di Pagamento
    Sezione: Merchant & Pagamenti
    Descrizione: Metodi di pagamento accettati dalla piattaforma
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
            <x-heroicon-o-credit-card class="w-5 h-5 text-emerald-600" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900">
            Metodi di Pagamento Accettati
        </h3>
    </div>

    <p class="text-gray-600 mb-6">
        FlorenceEGI supporta diversi metodi di pagamento per offrire la massima
        flessibilità agli utenti, garantendo sempre la
        <a href="#glossario" class="text-primary-600 hover:underline font-medium">tracciabilità</a>
        richiesta dalle normative fiscali.
    </p>

    <!-- Carte -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-credit-card class="w-5 h-5 text-blue-500" />
            Carte di Credito / Debito
        </h4>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex flex-wrap gap-4 mb-4 justify-center">
                <div class="bg-white rounded-lg px-4 py-2 border border-blue-200 flex items-center gap-2">
                    <span class="text-2xl">💳</span>
                    <span class="font-medium">Visa</span>
                </div>
                <div class="bg-white rounded-lg px-4 py-2 border border-blue-200 flex items-center gap-2">
                    <span class="text-2xl">💳</span>
                    <span class="font-medium">Mastercard</span>
                </div>
                <div class="bg-white rounded-lg px-4 py-2 border border-blue-200 flex items-center gap-2">
                    <span class="text-2xl">💳</span>
                    <span class="font-medium">American Express</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-center">
                <div>
                    <div class="text-gray-500">Commissione</div>
                    <div class="font-medium text-blue-700">1.4% + €0.25</div>
                </div>
                <div>
                    <div class="text-gray-500">Tempo accredito</div>
                    <div class="font-medium text-blue-700">Istantaneo</div>
                </div>
                <div>
                    <div class="text-gray-500">3D Secure</div>
                    <div class="font-medium text-green-600">✓ Attivo</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bonifico -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-building-library class="w-5 h-5 text-purple-500" />
            Bonifico Bancario
        </h4>
        <div class="bg-purple-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="font-medium text-purple-900 mb-2">SEPA Standard</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Commissione: Gratuito</li>
                        <li>• Tempo: 1-3 giorni lavorativi</li>
                        <li>• Minimo: €50</li>
                    </ul>
                </div>
                <div>
                    <div class="font-medium text-purple-900 mb-2">SEPA Instant</div>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Commissione: €0.50</li>
                        <li>• Tempo: < 10 secondi</li>
                        <li>• Massimo: €100.000</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Digital Wallets -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-device-phone-mobile class="w-5 h-5 text-green-500" />
            Wallet Digitali
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-3xl mb-2">🅿️</div>
                <div class="font-medium text-blue-900">PayPal</div>
                <p class="text-xs text-gray-500 mt-1">Fee: 2.9% + €0.35</p>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 text-center">
                <div class="text-3xl mb-2">🍎</div>
                <div class="font-medium text-white">Apple Pay</div>
                <p class="text-xs text-gray-400 mt-1">Fee: come carta</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-3xl mb-2">🟢</div>
                <div class="font-medium text-green-900">Google Pay</div>
                <p class="text-xs text-gray-500 mt-1">Fee: come carta</p>
            </div>
        </div>
    </div>

    <!-- Crypto (futuro) -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <x-heroicon-o-currency-euro class="w-5 h-5 text-amber-500" />
            Cripto-Pagamenti <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded ml-2">Coming Soon</span>
        </h4>
        <div class="bg-amber-50 rounded-lg p-4">
            <p class="text-sm text-gray-700 mb-3">
                In roadmap l'integrazione con pagamenti in criptovaluta:
            </p>
            <div class="flex flex-wrap gap-3">
                <span class="bg-amber-100 text-amber-800 text-sm px-3 py-1 rounded-full">ALGO</span>
                <span class="bg-amber-100 text-amber-800 text-sm px-3 py-1 rounded-full">USDC</span>
                <span class="bg-amber-100 text-amber-800 text-sm px-3 py-1 rounded-full">USDT</span>
            </div>
        </div>
    </div>

    <!-- Tabella Riepilogo -->
    <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3">Riepilogo Commissioni</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left p-2 font-semibold">Metodo</th>
                        <th class="text-center p-2 font-semibold">Fee</th>
                        <th class="text-center p-2 font-semibold">Tempo</th>
                        <th class="text-center p-2 font-semibold">Valido Fiscale</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="p-2">Carta Credito/Debito</td>
                        <td class="p-2 text-center">1.4% + €0.25</td>
                        <td class="p-2 text-center">Istantaneo</td>
                        <td class="p-2 text-center text-green-600">✓</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2">Bonifico SEPA</td>
                        <td class="p-2 text-center">Gratuito</td>
                        <td class="p-2 text-center">1-3 giorni</td>
                        <td class="p-2 text-center text-green-600">✓</td>
                    </tr>
                    <tr>
                        <td class="p-2">PayPal</td>
                        <td class="p-2 text-center">2.9% + €0.35</td>
                        <td class="p-2 text-center">Istantaneo</td>
                        <td class="p-2 text-center text-green-600">✓</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="p-2">Apple/Google Pay</td>
                        <td class="p-2 text-center">1.4% + €0.25</td>
                        <td class="p-2 text-center">Istantaneo</td>
                        <td class="p-2 text-center text-green-600">✓</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Note -->
    <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-400">
        <h5 class="font-medium text-green-900 mb-2">Tracciabilità Garantita</h5>
        <p class="text-sm text-gray-600">
            Tutti i metodi di pagamento accettati sono <strong>tracciabili</strong>,
            permettendo ai donatori di beneficiare delle detrazioni fiscali e agli
            operatori di documentare correttamente le transazioni.
        </p>
    </div>
</div>
