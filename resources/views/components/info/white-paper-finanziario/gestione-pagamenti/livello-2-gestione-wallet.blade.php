{{-- Componente: Livello 2 - Gestione Wallet Non-Custodial FIAT (Collapsible) --}}
{{-- Dettaglio sulla modalità non-custodial --}}

<details class="collapsible-section">
    <summary>
        <span class="material-icons text-xl">vpn_key</span>
        Gestione Wallet Utenti Livello 2 – Modello Non-Custodial FIAT
    </summary>
    <div class="collapsible-content">
        <p class="mb-4 text-gray-700">
            Quando l'utente decide di utilizzare un proprio <a href="#glossary-wallet" class="glossary-link">wallet</a>
            Algorand,
            la gestione passa in modalità <a href="#glossary-non-custodial" class="glossary-link">non-custodial</a>.
        </p>

        <div class="space-y-3 text-gray-700">
            <ul class="list-inside list-disc space-y-2">
                <li>L'utente esporta la frase segreta dal <a href="#glossary-wallet" class="glossary-link">wallet</a>
                    generato in precedenza, la importa in Pera Wallet (o altro client compatibile) e richiede la
                    cancellazione definitiva dal database FlorenceEGI.</li>
                <li>Da quel momento FlorenceEGI <strong>non detiene più alcuna chiave privata</strong> né può accedere
                    ai suoi asset.</li>
                <li>Durante un nuovo acquisto in valuta <a href="#glossary-fiat" class="glossary-link">FIAT</a>,
                    l'utente inserisce l'indirizzo del proprio <a href="#glossary-wallet"
                        class="glossary-link">wallet</a>.</li>
                <li>Il <a href="#glossary-mint" class="glossary-link">mint</a> dell'<a href="#glossary-egi"
                        class="glossary-link">EGI</a> (<a href="#glossary-asa" class="glossary-link">ASA</a> supply = 1)
                    viene eseguito direttamente con <strong>sender = wallet dell'utente</strong>, senza alcuna
                    transazione di trasferimento successiva.</li>
                <li>FlorenceEGI paga le micro-fee di rete come <strong>fee-payer tecnico</strong>.</li>
                <li><strong>Nessun fondo in criptovaluta</strong> transita tra le parti; il pagamento rimane interamente
                    in <a href="#glossary-fiat" class="glossary-link">FIAT</a> tramite <a href="#glossary-psp"
                        class="glossary-link">PSP</a>.</li>
                <li>Il <a href="#glossary-wallet" class="glossary-link">wallet</a> appartiene e resta sotto il
                    <strong>controllo esclusivo dell'utente</strong>.</li>
            </ul>
        </div>

        <div class="mt-6 rounded-lg border-l-4 border-green-600 bg-green-50 p-4">
            <h5 class="mb-2 font-semibold text-green-900">
                Conformità Normativa <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>
            </h5>
            <p class="text-sm text-green-800">
                FlorenceEGI <strong>non svolge funzioni di custodia, intermediazione o scambio</strong> di asset
                digitali.
                Questa modalità è <strong>pienamente fuori dal perimetro <a href="#glossary-mica"
                        class="glossary-link">MiCA</a></strong>,
                trattandosi di semplice <strong>emissione di NFT unici</strong> verso un
                <a href="#glossary-wallet" class="glossary-link">wallet</a> di proprietà dell'utente,
                con pagamento in valuta tradizionale.
            </p>
        </div>
    </div>
</details>
