{{-- Componente: Livello 3 - Accetto pagamenti Crypto (opzionale) --}}
{{-- Card principale con griglia Merchant/Cliente --}}

<div class="rounded-lg border bg-gray-50 p-6">
    <h3 class="mb-4 text-2xl font-bold text-gray-800">Livello 3 — Accetto pagamenti Crypto (opzionale)</h3>
    <p class="mb-6 text-gray-600">
        Questo livello è facoltativo e gestito da partner esterni per mantenere la piattaforma
        <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>.
    </p>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Merchant</h4>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>Si affida a un <a href="#glossary-partner-autorizzato" class="glossary-link">Partner autorizzato
                        (CASP/EMI)</a>.</li>
                <li>I clienti pagano sul checkout del Partner.</li>
                <li>Il <a href="#glossary-settlement" class="glossary-link">settlement</a> è gestito dal Partner.</li>
            </ul>
        </div>
        <div>
            <h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Cliente</h4>
            <ul class="list-inside list-disc space-y-2 text-gray-700">
                <li>Paga in crypto sul checkout del Partner.</li>
                <li>Riceve l'<a href="#glossary-egi" class="glossary-link">EGI</a> come sempre.</li>
            </ul>
        </div>
    </div>
</div>
