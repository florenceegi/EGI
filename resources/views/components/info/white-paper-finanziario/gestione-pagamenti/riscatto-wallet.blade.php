{{-- Componente: Riscatto Wallet (Collapsible) --}}
{{-- Trasferimento EGI al Wallet Personale --}}

<div class="p-6 border rounded-lg bg-gray-50">
    <details class="collapsible-section">
        <summary>
            <span class="text-xl material-icons">key_off</span> 
            Riscatto Wallet — Trasferimento EGI al Wallet Personale
        </summary>
        <div class="collapsible-content">
            <p class="mb-4 text-gray-700">
                L'utente che desidera ottenere la <strong>piena proprietà</strong> dei propri 
                <a href="#glossary-egi" class="glossary-link">EGI</a> può richiedere il <strong>riscatto del wallet</strong>. 
                Questa operazione trasferisce gli <a href="#glossary-asa" class="glossary-link">ASA</a> (Algorand Standard Asset) 
                dal <a href="#glossary-wallet" class="glossary-link">wallet</a> 
                <a href="#glossary-custodial" class="glossary-link">custodiale</a> della piattaforma al wallet personale dell'utente, 
                consegnandogli contestualmente la <a href="#glossary-mnemonic" class="glossary-link">frase segreta (seed phrase)</a>.
            </p>
            
            {{-- Perché è richiesto un pagamento --}}
            <h5 class="mt-6 mb-3 text-lg font-semibold text-gray-800">Perché è richiesto un pagamento?</h5>
            <p class="mb-4 text-gray-700">
                La <a href="#glossary-blockchain" class="glossary-link">blockchain</a> Algorand richiede che ogni 
                <a href="#glossary-wallet" class="glossary-link">wallet</a> che intende ricevere un 
                <a href="#glossary-asa" class="glossary-link">ASA</a> effettui preventivamente un'operazione chiamata 
                <strong><a href="#glossary-opt-in" class="glossary-link">opt-in</a></strong>. 
                Questa operazione blocca temporaneamente <strong>0,1 <a href="#glossary-algo" class="glossary-link">ALGO</a></strong> 
                per ogni ASA che il wallet intende detenere. Tale importo non viene speso ma resta vincolato come requisito 
                di bilancio minimo imposto dal protocollo Algorand, e viene liberato qualora l'utente decida in futuro 
                di rimuovere l'ASA dal proprio wallet (operazione di <strong>opt-out</strong>).
            </p>
            
            {{-- Formula di calcolo --}}
            <div class="p-4 my-4 border-l-4 border-amber-500 rounded-lg bg-amber-50">
                <h5 class="mb-2 font-semibold text-amber-900">Formula di calcolo del costo</h5>
                <p class="text-sm text-amber-800">Il costo totale del riscatto è determinato dalla seguente formula:</p>
                <p class="mt-2 font-mono text-sm text-amber-900">
                    <strong>Costo = (N × 0,1 ALGO) + 0,1 ALGO + fee di rete</strong>
                </p>
                <p class="mt-2 text-sm text-amber-800">
                    Dove <strong>N</strong> è il numero di <a href="#glossary-egi" class="glossary-link">EGI</a> posseduti dall'utente. 
                    Il secondo addendo (0,1 ALGO) rappresenta il bilancio minimo richiesto per mantenere attivo l'account Algorand, 
                    mentre le fee di rete coprono le transazioni di opt-in e trasferimento (circa 0,001 ALGO ciascuna).
                </p>
            </div>
            
            {{-- Esempio pratico --}}
            <h5 class="mt-6 mb-3 text-lg font-semibold text-gray-800">Esempio pratico</h5>
            <p class="mb-4 text-gray-700">
                Un utente che possiede <strong>50 EGI</strong> dovrà sostenere un costo di circa:
            </p>
            <ul class="mb-4 space-y-1 text-gray-700 list-disc list-inside">
                <li>50 × 0,1 ALGO = <strong>5 ALGO</strong> (per gli opt-in)</li>
                <li>0,1 ALGO (bilancio minimo account)</li>
                <li>~0,1 ALGO (fee di rete per ~100 transazioni)</li>
                <li><strong>Totale: ~5,2 ALGO</strong></li>
            </ul>
            <p class="mb-4 text-gray-700">
                Al cambio corrente (dicembre 2025: 1 ALGO ≈ 0,12 €), questo equivale a circa <strong>0,62 €</strong>. 
                L'importo viene addebitato in <a href="#glossary-egili" class="glossary-link">EGILI</a> al tasso di conversione vigente al momento della richiesta.
            </p>
            
            {{-- Flusso operativo --}}
            <h5 class="mt-6 mb-3 text-lg font-semibold text-gray-800">Flusso operativo del riscatto</h5>
            <div class="space-y-3 text-gray-700">
                <ol class="space-y-2 list-decimal list-inside">
                    <li><strong>Richiesta:</strong> L'utente avvia la procedura di riscatto dalla propria <a href="#glossary-dashboard" class="glossary-link">dashboard</a> e visualizza il costo calcolato.</li>
                    <li><strong>Pagamento:</strong> L'importo in <a href="#glossary-egili" class="glossary-link">EGILI</a> viene scalato dal saldo dell'utente.</li>
                    <li><strong>Funding:</strong> La piattaforma trasferisce gli <a href="#glossary-algo" class="glossary-link">ALGO</a> necessari dal <a href="#glossary-treasury" class="glossary-link">Treasury</a> al <a href="#glossary-wallet" class="glossary-link">wallet</a> dell'utente.</li>
                    <li><strong>Opt-in automatico:</strong> Il sistema esegue automaticamente l'<a href="#glossary-opt-in" class="glossary-link">opt-in</a> per ogni <a href="#glossary-asa" class="glossary-link">ASA</a> di proprietà dell'utente, firmando le transazioni con la chiave privata ancora in custodia.</li>
                    <li><strong>Trasferimento ASA:</strong> Gli <a href="#glossary-egi" class="glossary-link">EGI</a> vengono trasferiti dal Treasury al <a href="#glossary-wallet" class="glossary-link">wallet</a> personale dell'utente.</li>
                    <li><strong>Consegna seed phrase:</strong> L'utente visualizza la propria <a href="#glossary-mnemonic" class="glossary-link">frase segreta</a> (25 parole) e conferma di averla salvata in modo sicuro.</li>
                    <li><strong>Cancellazione:</strong> La frase segreta cifrata viene <strong>eliminata definitivamente</strong> dal database di FlorenceEGI.</li>
                </ol>
            </div>
            
            {{-- Politica Zero-Profitto --}}
            <div class="p-4 mt-6 border-l-4 border-blue-600 rounded-lg bg-blue-50">
                <h5 class="mb-2 font-semibold text-blue-900">Politica Zero-Profitto</h5>
                <p class="text-sm text-blue-800">
                    FlorenceEGI <strong>non applica alcun margine o commissione</strong> sull'operazione di riscatto. 
                    Il costo addebitato all'utente copre esclusivamente le spese imposte dalla 
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a> Algorand 
                    (requisiti di bilancio minimo e fee di transazione). Questa politica è coerente con la missione 
                    della piattaforma di facilitare l'accesso alla certificazione digitale senza barriere economiche aggiuntive.
                </p>
            </div>
            
            {{-- Dopo il riscatto --}}
            <div class="p-4 mt-4 border-l-4 border-green-600 rounded-lg bg-green-50">
                <h5 class="mb-2 font-semibold text-green-900">Dopo il riscatto</h5>
                <p class="text-sm text-green-800">
                    Una volta completato il riscatto, il <a href="#glossary-wallet" class="glossary-link">wallet</a> diventa 
                    <strong>completamente <a href="#glossary-non-custodial" class="glossary-link">non-custodial</a></strong>. 
                    L'utente è l'unico detentore delle chiavi private e può gestire i propri 
                    <a href="#glossary-egi" class="glossary-link">EGI</a> tramite qualsiasi client Algorand compatibile 
                    (Pera Wallet, Defly, etc.). FlorenceEGI non ha più alcun controllo né visibilità sugli asset trasferiti, 
                    se non per quanto riguarda la registrazione storica delle transazioni sulla 
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a> pubblica.
                </p>
            </div>
        </div>
    </details>
</div>
