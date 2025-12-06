{{-- Componente: Cosa fa (e non fa) la piattaforma --}}
{{-- Riepilogo delle responsabilità della piattaforma --}}

<div class="mt-8">
    <h3 class="mb-4 text-center text-2xl font-bold text-gray-800">Cosa fa (e non fa) la piattaforma</h3>

    <div class="mx-auto grid max-w-4xl gap-6 md:grid-cols-2">
        {{-- Cosa Fa --}}
        <div class="rounded-lg border-l-4 border-green-600 bg-green-50 p-4">
            <h4 class="mb-2 text-lg font-bold text-green-800">Cosa Fa</h4>
            <ul class="list-inside list-disc space-y-1 text-gray-700">
                <li>Incassa <a href="#glossary-fiat" class="glossary-link">FIAT</a> tramite <a href="#glossary-psp"
                        class="glossary-link">PSP</a>.</li>
                <li>Emette e trasferisce <a href="#glossary-egi" class="glossary-link">EGI</a>.</li>
                <li>Scrive <a href="#glossary-anchor-hash" class="glossary-link">anchor hash</a>.</li>
                <li>Gestisce QR e verifica pubblica.</li>
                <li>Calcola <a href="#glossary-royalties" class="glossary-link">royalties</a> per il <a
                        href="#glossary-psp" class="glossary-link">PSP</a>.</li>
            </ul>
        </div>

        {{-- Cosa NON Fa --}}
        <div class="rounded-lg border-l-4 border-red-600 bg-red-50 p-4">
            <h4 class="mb-2 text-lg font-bold text-red-800">Cosa NON Fa</h4>
            <ul class="list-inside list-disc space-y-1 text-gray-700">
                <li>Custodire criptovalute per terzi.</li>
                <li>Fare da exchange crypto/<a href="#glossary-fiat" class="glossary-link">fiat</a>.</li>
                <li>Processare pagamenti crypto.</li>
            </ul>
        </div>
    </div>
</div>
