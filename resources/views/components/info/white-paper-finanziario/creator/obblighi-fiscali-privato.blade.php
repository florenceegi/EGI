{{-- Componente: Obblighi Fiscali per Privati --}}
{{-- Guida fiscale per Creator senza Partita IVA --}}

<div class="mt-8">
    <div class="rounded-lg bg-gray-50 p-6">
        <h4 class="mb-3 text-lg font-semibold text-gray-800">
            <span class="material-icons mr-2 align-middle text-blue-600">person</span>
            Se sei un Privato (senza Partita IVA)
        </h4>

        <p class="mb-4 text-gray-700">
            Per vendite <strong>occasionali</strong> di <a href="#glossary-egi" class="glossary-link">EGI</a>,
            devi seguire queste regole:
        </p>

        <div class="space-y-4">
            {{-- Ricevuta Occasionale --}}
            <div class="rounded border-l-4 border-blue-500 bg-white p-4">
                <h5 class="font-semibold text-blue-800">📄 Ricevuta per Prestazione Occasionale</h5>
                <p class="mt-1 text-sm text-gray-700">
                    Devi emettere una
                    <a href="#glossary-ricevuta-prestazione-occasionale" class="glossary-link">ricevuta per prestazione occasionale</a>
                    per ogni vendita effettuata.
                </p>
            </div>

            {{-- Dichiarazione Redditi --}}
            <div class="rounded border-l-4 border-green-500 bg-white p-4">
                <h5 class="font-semibold text-green-800">📊 Dichiarazione dei Redditi</h5>
                <p class="mt-1 text-sm text-gray-700">
                    I proventi vanno dichiarati come <strong>"reddito diverso"</strong> nel Modello 730 o Redditi PF,
                    al Quadro RL (Redditi Diversi).
                </p>
            </div>

            {{-- Soglia Abitualità --}}
            <div class="rounded border-l-4 border-amber-500 bg-white p-4">
                <h5 class="font-semibold text-amber-800">⚠️ Soglia di Abitualità</h5>
                <p class="mt-1 text-sm text-gray-700">
                    Se l'attività di vendita diventa <strong>abituale e continuativa</strong>
                    (non più occasionale), è <strong>obbligatorio</strong> aprire
                    <a href="#glossary-partita-iva" class="glossary-link">Partita IVA</a>.
                </p>
                <p class="mt-2 text-xs text-amber-600">
                    💡 Non esiste una soglia monetaria precisa: conta la frequenza e la sistematicità dell'attività.
                </p>
            </div>

            {{-- Ritenuta d'Acconto --}}
            <div class="rounded border-l-4 border-purple-500 bg-white p-4">
                <h5 class="font-semibold text-purple-800">💰 Ritenuta d'Acconto</h5>
                <p class="mt-1 text-sm text-gray-700">
                    Se il committente è un soggetto con P.IVA, potrebbe applicare una ritenuta d'acconto del 20%.
                    Nel caso di FlorenceEGI, la piattaforma <strong>non è sostituto d'imposta</strong>,
                    quindi non effettua ritenute.
                </p>
            </div>
        </div>

        {{-- Alert automatico --}}
        <div class="mt-6 rounded-lg border border-amber-300 bg-amber-50 p-4">
            <h5 class="flex items-center font-semibold text-amber-800">
                <span class="material-icons mr-2">notifications_active</span>
                Alert Automatici di FlorenceEGI
            </h5>
            <p class="mt-1 text-sm text-amber-700">
                La piattaforma ti invierà <a href="#glossary-alert-fiscale" class="glossary-link">alert automatici</a>
                quando raggiungi soglie di vendita che potrebbero indicare la necessità di valutare
                l'apertura della Partita IVA.
            </p>
        </div>
    </div>
</div>
