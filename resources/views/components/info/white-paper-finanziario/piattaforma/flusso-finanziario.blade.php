{{-- Componente: Flusso Finanziario della Piattaforma --}}
{{-- Visualizzazione del flusso delle fee dalla transazione alla fatturazione --}}

<div class="mt-8">
    <h3 class="mb-4 text-center text-xl font-bold text-emerald-700">Flusso Finanziario della Piattaforma</h3>

    {{-- Flow Diagram --}}
    <div class="flex flex-col items-center justify-center gap-4 text-center md:flex-row md:flex-wrap">
        {{-- Step 1: Transazione --}}
        <div class="w-full rounded-lg bg-blue-100 p-4 shadow-sm md:w-auto">
            <p class="font-semibold">Transazione Utente</p>
            <p class="text-sm">
                (es. <a href="#glossary-mint" class="glossary-link">Minting</a>, Trading)
            </p>
        </div>

        {{-- Arrow --}}
        <div class="rotate-90 text-2xl font-bold text-emerald-600 md:rotate-0">&#10230;</div>

        {{-- Step 2: Separazione Fee --}}
        <div class="w-full rounded-lg bg-emerald-100 p-4 shadow-sm md:w-auto">
            <p class="font-semibold">
                Separazione <a href="#glossary-fee" class="glossary-link">Fee</a>
            </p>
            <p class="text-sm">La fee viene separata automaticamente</p>
        </div>

        {{-- Arrow --}}
        <div class="rotate-90 text-2xl font-bold text-emerald-600 md:rotate-0">&#10230;</div>

        {{-- Step 3: Wallet FlorenceEGI --}}
        <div class="w-full rounded-lg bg-green-100 p-4 shadow-sm md:w-auto">
            <p class="font-semibold">
                <a href="#glossary-wallet" class="glossary-link">Wallet</a> FlorenceEGI
            </p>
            <p class="text-sm">La fee è incassata</p>
        </div>

        {{-- Arrow --}}
        <div class="rotate-90 text-2xl font-bold text-emerald-600 md:rotate-0">&#10230;</div>

        {{-- Step 4: Fatturazione --}}
        <div class="w-full rounded-lg bg-yellow-100 p-4 shadow-sm md:w-auto">
            <p class="font-semibold">Fatturazione</p>
            <p class="text-sm">
                Emissione <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura</a> all'utente
            </p>
        </div>
    </div>

    {{-- Dettaglio percentuali --}}
    <div class="mx-auto mt-8 max-w-3xl">
        <h4 class="mb-4 text-center text-lg font-semibold text-gray-800">Ripartizione Tipica di una Transazione</h4>

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-emerald-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-emerald-800">Destinatario</th>
                        <th class="px-4 py-3 text-center font-semibold text-emerald-800">% Minting</th>
                        <th class="px-4 py-3 text-center font-semibold text-emerald-800">% Rebind</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr>
                        <td class="px-4 py-3">
                            <a href="#glossary-creator" class="glossary-link">Creator</a>
                        </td>
                        <td class="px-4 py-3 text-center font-mono">68%</td>
                        <td class="px-4 py-3 text-center font-mono">4.5%</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="#glossary-epp" class="glossary-link">EPP</a> (Donazione)
                        </td>
                        <td class="px-4 py-3 text-center font-mono">20%</td>
                        <td class="px-4 py-3 text-center font-mono">0.8%</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">
                            FlorenceEGI (<a href="#glossary-fee" class="glossary-link">Fee</a>)
                        </td>
                        <td class="px-4 py-3 text-center font-mono">10%</td>
                        <td class="px-4 py-3 text-center font-mono">0.7%</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3">Frangette (Associazione)</td>
                        <td class="px-4 py-3 text-center font-mono">2%</td>
                        <td class="px-4 py-3 text-center font-mono">0.1%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-3 text-center text-xs text-gray-500">
            * Le percentuali possono variare in base alla configurazione specifica dell'<a href="#glossary-egi" class="glossary-link">EGI</a>.
        </p>
    </div>

    {{-- Nota importante --}}
    <div class="mx-auto mt-6 max-w-3xl rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
        <h4 class="font-semibold text-blue-800">ℹ️ Flusso Diretto</h4>
        <p class="mt-1 text-sm text-blue-700">
            I fondi destinati a <a href="#glossary-creator" class="glossary-link">Creator</a>,
            <a href="#glossary-epp" class="glossary-link">EPP</a> e altri beneficiari vengono
            <strong>accreditati direttamente</strong> sui rispettivi wallet, senza mai transitare
            dal conto di FlorenceEGI. La piattaforma incassa <strong>solo ed esclusivamente</strong>
            la propria fee di servizio.
        </p>
    </div>
</div>
