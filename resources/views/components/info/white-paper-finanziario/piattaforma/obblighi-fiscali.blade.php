{{-- Componente: Obblighi Fiscali della Piattaforma --}}
{{-- Gestione delle Fee e responsabilità fiscali di FlorenceEGI --}}

<div class="mt-8">
    <h3 class="mb-4 text-2xl font-bold text-emerald-700">Obblighi Fiscali di FlorenceEGI</h3>

    <div class="grid gap-8 md:grid-cols-2">
        {{-- Gestione Fee --}}
        <div class="rounded-lg bg-gray-50 p-6">
            <h4 class="mb-3 text-lg font-semibold text-gray-800">
                Gestione <a href="#glossary-fee" class="glossary-link">Fee</a>
            </h4>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>
                    Incassa <strong>esclusivamente</strong> la propria
                    <a href="#glossary-fee" class="glossary-link">fee</a> di servizio.
                </li>
                <li>
                    I fondi non vengono mai trattenuti per conto di terzi
                    (<a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>).
                </li>
                <li>
                    Le <a href="#glossary-fee" class="glossary-link">fee</a> vengono accreditate
                    direttamente sul <a href="#glossary-wallet" class="glossary-link">wallet</a> della piattaforma.
                </li>
            </ul>
        </div>

        {{-- Fatturazione e IVA --}}
        <div class="rounded-lg bg-gray-50 p-6">
            <h4 class="mb-3 text-lg font-semibold text-gray-800">
                Fatturazione e <a href="#glossary-iva" class="glossary-link">IVA</a>
            </h4>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>
                    Emette <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura elettronica</a>
                    per ogni <a href="#glossary-fee" class="glossary-link">fee</a> incassata tramite
                    <a href="#glossary-sdi" class="glossary-link">SDI</a>.
                </li>
                <li>
                    Adotta <a href="#glossary-fatturazione-batch" class="glossary-link">fatturazione cumulativa (batch)</a>
                    per operazioni ad alto volume.
                </li>
                <li>
                    Gestisce l'<a href="#glossary-iva" class="glossary-link">IVA</a> secondo le normative
                    nazionali e internazionali (<a href="#glossary-oss" class="glossary-link">OSS</a>/<a href="#glossary-moss" class="glossary-link">MOSS</a>).
                </li>
            </ul>
        </div>
    </div>

    {{-- Nota MiCA-safe --}}
    <div class="mt-6 rounded-lg border-l-4 border-green-600 bg-green-50 p-4">
        <h4 class="font-semibold text-green-800">
            ✅ Conformità <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>
        </h4>
        <p class="mt-1 text-sm text-green-700">
            FlorenceEGI opera <strong>fuori dal perimetro MiCA</strong> in quanto non custodisce fondi per conto terzi,
            non effettua operazioni di cambio e non intermedia transazioni crypto.
            La piattaforma è un <strong>facilitatore tecnologico</strong>, non un intermediario finanziario.
        </p>
    </div>
</div>
