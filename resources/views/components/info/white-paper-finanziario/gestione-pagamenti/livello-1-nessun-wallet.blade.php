{{-- Componente: Livello 1 - Nessun Wallet (100% tradizionale) --}}
{{-- Card principale con griglia Cliente/Merchant --}}

<div class="p-6 border rounded-lg bg-gray-50">
    <h3 class="mb-4 text-2xl font-bold text-gray-800">Livello 1 — Nessun wallet (100% tradizionale)</h3>
    <p class="mb-6 text-gray-600">L'esperienza d'uso è identica a un normale e-commerce. Zero cripto, zero complessità.</p>
    
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Cliente</h4>
            <ul class="space-y-2 text-gray-700 list-disc list-inside">
                <li>Paga in euro (<a href="#glossary-fiat" class="glossary-link">FIAT</a>) su pagina sicura del <a href="#glossary-psp" class="glossary-link">PSP</a>.</li>
                <li>Riceve l'<a href="#glossary-egi" class="glossary-link">EGI</a>: la piattaforma esegue <a href="#glossary-mint" class="glossary-link">mint</a> e <a href="#glossary-transfer" class="glossary-link">transfer</a> e salva l'<a href="#glossary-anchor-hash" class="glossary-link">anchor hash</a>.</li>
                <li>Verifica pubblica con QR.</li>
            </ul>
        </div>
        <div>
            <h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Merchant</h4>
            <ul class="space-y-2 text-gray-700 list-disc list-inside">
                <li>Riceve denaro in <a href="#glossary-fiat" class="glossary-link">FIAT</a> dal <a href="#glossary-psp" class="glossary-link">PSP</a> (<a href="#glossary-payout" class="glossary-link">payout</a>).</li>
                <li>Vede l'<a href="#glossary-egi" class="glossary-link">EGI</a> emesso e i report.</li>
                <li><a href="#glossary-royalties" class="glossary-link">Royalties</a> e ripartizioni sono gestite dal <a href="#glossary-psp" class="glossary-link">PSP</a> (<a href="#glossary-off-chain" class="glossary-link">off-chain</a>).</li>
            </ul>
        </div>
    </div>
</div>
