{{-- Componente: Flusso di Incasso per Creator --}}
{{-- Visualizzazione del flusso dalla vendita EGI all'incasso --}}

<div class="mt-8">
    <h3 class="mb-4 text-center text-2xl font-bold text-emerald-700">
        Flusso di Incasso per <a href="#glossary-creator" class="glossary-link">Creator</a>
    </h3>

    <div class="mx-auto max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-md">
        <div class="flex flex-col items-center space-y-4 text-center">
            {{-- Step 1: Vendita --}}
            <div class="w-full rounded-lg bg-blue-100 p-4">
                <p class="font-semibold">1. Vendita <a href="#glossary-egi" class="glossary-link">EGI</a></p>
                <p class="text-sm">Un utente acquista il tuo EGI.</p>
            </div>

            {{-- Arrow --}}
            <div class="rotate-90 transform text-2xl font-bold text-emerald-500">&#10230;</div>

            {{-- Step 2: Accredito --}}
            <div class="w-full rounded-lg bg-emerald-100 p-4">
                <p class="font-semibold">2. Accredito Diretto</p>
                <p class="text-sm">
                    L'importo (al netto della <a href="#glossary-fee" class="glossary-link">fee</a> di piattaforma)
                    viene inviato istantaneamente.
                </p>
            </div>

            {{-- Arrow --}}
            <div class="rotate-90 transform text-2xl font-bold text-emerald-500">&#10230;</div>

            {{-- Step 3: Wallet --}}
            <div class="w-full rounded-lg bg-green-100 p-4">
                <p class="font-semibold">
                    3. Il Tuo <a href="#glossary-wallet" class="glossary-link">Wallet</a>
                </p>
                <p class="text-sm">Ricevi i fondi direttamente sul tuo wallet, senza intermediari.</p>
            </div>

            {{-- Arrow --}}
            <div class="rotate-90 transform text-2xl font-bold text-emerald-500">&#10230;</div>

            {{-- Step 4: Compliance --}}
            <div class="w-full rounded-lg bg-yellow-100 p-4">
                <p class="font-semibold">
                    4. <a href="#glossary-compliance" class="glossary-link">Compliance</a> Fiscale
                </p>
                <p class="text-sm">Emetti fattura/ricevuta e dichiari il reddito.</p>
            </div>
        </div>

        {{-- Nota importante --}}
        <div class="mt-6 rounded border border-amber-300 bg-amber-50 p-3">
            <p class="text-center text-sm text-amber-800">
                <strong>Ricorda:</strong> Sei l'unico responsabile della tua dichiarazione fiscale.
                FlorenceEGI non è un <a href="#glossary-sostituto-imposta" class="glossary-link">sostituto d'imposta</a>.
            </p>
        </div>
    </div>

    {{-- Dettaglio ripartizione --}}
    <div class="mx-auto mt-8 max-w-2xl">
        <h4 class="mb-4 text-center text-lg font-semibold text-gray-800">Esempio di Ripartizione</h4>

        <div class="rounded-lg bg-gray-50 p-6">
            <p class="mb-4 text-center text-gray-600">
                Per una vendita di €100 (Minting primario):
            </p>

            <div class="grid gap-3 text-center md:grid-cols-4">
                <div class="rounded-lg bg-blue-100 p-3">
                    <p class="text-2xl font-bold text-blue-800">€68</p>
                    <p class="text-xs text-blue-600">
                        <a href="#glossary-creator" class="glossary-link">Creator</a>
                    </p>
                </div>
                <div class="rounded-lg bg-green-100 p-3">
                    <p class="text-2xl font-bold text-green-800">€20</p>
                    <p class="text-xs text-green-600">
                        <a href="#glossary-epp" class="glossary-link">EPP</a> (Donazione)
                    </p>
                </div>
                <div class="rounded-lg bg-purple-100 p-3">
                    <p class="text-2xl font-bold text-purple-800">€10</p>
                    <p class="text-xs text-purple-600">FlorenceEGI (Fee)</p>
                </div>
                <div class="rounded-lg bg-amber-100 p-3">
                    <p class="text-2xl font-bold text-amber-800">€2</p>
                    <p class="text-xs text-amber-600">Frangette</p>
                </div>
            </div>

            <p class="mt-4 text-center text-xs text-gray-500">
                * Le percentuali sono configurabili e possono variare.
            </p>
        </div>
    </div>
</div>
