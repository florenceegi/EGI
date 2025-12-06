{{-- Componente: Fatturazione e IVA --}}
{{-- Dettaglio sulla gestione della fatturazione elettronica e regimi IVA --}}

<div class="mt-8">
    <h3 class="mb-4 text-2xl font-bold text-emerald-700">Fatturazione Elettronica e IVA</h3>

    <div class="space-y-6">
        {{-- Fatturazione Elettronica --}}
        <div class="rounded-lg bg-gray-50 p-6">
            <h4 class="mb-3 text-lg font-semibold text-gray-800">
                <span class="material-icons mr-2 align-middle text-emerald-600">receipt_long</span>
                Sistema di Fatturazione
            </h4>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>
                    Integrazione diretta con il <a href="#glossary-sdi" class="glossary-link">Sistema di Interscambio (SDI)</a>
                    dell'Agenzia delle Entrate.
                </li>
                <li>
                    Formato <strong>FatturaPA 1.6.1</strong> conforme agli standard italiani ed europei.
                </li>
                <li>
                    Conservazione sostitutiva digitale per <strong>10 anni</strong> a norma di legge.
                </li>
            </ul>
        </div>

        {{-- Fatturazione Batch --}}
        <div class="rounded-lg bg-gray-50 p-6">
            <h4 class="mb-3 text-lg font-semibold text-gray-800">
                <span class="material-icons mr-2 align-middle text-blue-600">batch_prediction</span>
                Fatturazione Batch (Alto Volume)
            </h4>
            <p class="mb-3 text-gray-700">
                Per utenti con elevato numero di transazioni (es. <a href="#glossary-trader" class="glossary-link">Trader</a>),
                la piattaforma offre un sistema di
                <a href="#glossary-fatturazione-batch" class="glossary-link">fatturazione cumulativa</a>:
            </p>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>
                    <strong>Una sola fattura periodica</strong> (mensile/trimestrale) per tutte le fee maturate.
                </li>
                <li>
                    Allegato dettagliato con il <a href="#glossary-report" class="glossary-link">report</a>
                    di ogni singola transazione.
                </li>
                <li>
                    Riduzione drastica del carico amministrativo senza perdere la
                    <a href="#glossary-tracciabilita" class="glossary-link">tracciabilità</a>.
                </li>
            </ul>
        </div>

        {{-- Regimi IVA --}}
        <div class="rounded-lg bg-gray-50 p-6">
            <h4 class="mb-3 text-lg font-semibold text-gray-800">
                <span class="material-icons mr-2 align-middle text-purple-600">public</span>
                Gestione IVA Internazionale
            </h4>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded border-l-4 border-blue-500 bg-white p-3">
                    <h5 class="font-semibold text-blue-800">🇮🇹 Italia</h5>
                    <p class="text-sm text-gray-600">IVA ordinaria italiana applicata</p>
                </div>
                <div class="rounded border-l-4 border-yellow-500 bg-white p-3">
                    <h5 class="font-semibold text-yellow-800">🇪🇺 UE</h5>
                    <p class="text-sm text-gray-600">
                        <a href="#glossary-oss" class="glossary-link">OSS</a> per privati,
                        <a href="#glossary-reverse-charge" class="glossary-link">Reverse Charge</a> per P.IVA
                    </p>
                </div>
                <div class="rounded border-l-4 border-green-500 bg-white p-3">
                    <h5 class="font-semibold text-green-800">🌍 Extra-UE</h5>
                    <p class="text-sm text-gray-600">Fattura senza IVA (esportazione servizi)</p>
                </div>
            </div>
        </div>
    </div>
</div>
