{{-- Componente: Livello 3 - Pagamenti Stablecoin via PSP Partner (Collapsible) --}}
{{-- Dettaglio sulla modalità Wallet-to-Wallet Direct --}}

<details class="collapsible-section">
    <summary>
        <span class="material-icons text-xl">currency_bitcoin</span>
        Pagamenti Stablecoin via PSP Partner – Wallet-to-Wallet Direct
    </summary>
    <div class="collapsible-content">
        <p class="mb-4 text-gray-700">
            L'utente esperto che possiede un <a href="#glossary-wallet" class="glossary-link">wallet</a> Algorand
            può effettuare acquisti utilizzando stablecoin al posto della valuta
            <a href="#glossary-fiat" class="glossary-link">FIAT</a>.
        </p>

        <div class="space-y-3 text-gray-700">
            <ul class="list-inside list-disc space-y-2">
                <li>L'utente mantiene il <strong>controllo esclusivo</strong> del proprio <a href="#glossary-wallet"
                        class="glossary-link">wallet</a> (<a href="#glossary-non-custodial"
                        class="glossary-link">non-custodial</a>).</li>
                <li>Nel form di acquisto seleziona "Pagamento in stablecoin" e inserisce l'indirizzo del <a
                        href="#glossary-wallet" class="glossary-link">wallet</a>.</li>
                <li>Il <a href="#glossary-mint" class="glossary-link">mint</a> dell'<a href="#glossary-egi"
                        class="glossary-link">EGI</a> (<a href="#glossary-asa" class="glossary-link">ASA</a> univoco)
                    avviene con <strong>sender = wallet dell'utente</strong>.</li>
                <li>Il pagamento è eseguito <strong>direttamente wallet-to-wallet</strong> tra l'utente e un <a
                        href="#glossary-psp" class="glossary-link">Payment Service Provider (PSP)</a> partner della
                    piattaforma, scelto e sottoscritto dall'utente tramite accordo privato.</li>
                <li>FlorenceEGI <strong>non gestisce conversioni FIAT↔crypto</strong>, non detiene fondi, non partecipa
                    alla transazione in stablecoin e non custodisce chiavi private.</li>
                <li>Le stablecoin accettate devono essere <strong>emesse da soggetti conformi <a href="#glossary-mica"
                            class="glossary-link">MiCA</a></strong> e riconosciuti dalla piattaforma come <a
                        href="#glossary-psp" class="glossary-link">PSP</a> autorizzati.</li>
            </ul>
        </div>

        <div class="mt-6 rounded-lg border-l-4 border-purple-600 bg-purple-50 p-4">
            <h5 class="mb-2 font-semibold text-purple-900">
                Conformità Normativa <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>
            </h5>
            <p class="text-sm text-purple-800">
                In questa modalità FlorenceEGI opera <strong>unicamente come infrastruttura di registrazione su
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a></strong>, senza alcun ruolo
                finanziario
                o di intermediazione. La gestione rientra <strong>pienamente fuori dal perimetro
                    <a href="#glossary-mica" class="glossary-link">MiCA</a></strong>, poiché i pagamenti crypto sono
                gestiti
                esclusivamente da <a href="#glossary-psp" class="glossary-link">PSP</a> partner conformi
                (<a href="#glossary-casp" class="glossary-link">CASP</a>/<a href="#glossary-emi"
                    class="glossary-link">EMI</a>),
                con cui l'utente ha un rapporto contrattuale diretto.
            </p>
        </div>
    </div>
</details>
