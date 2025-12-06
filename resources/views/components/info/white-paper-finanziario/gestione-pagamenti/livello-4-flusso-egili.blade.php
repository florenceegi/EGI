{{-- Componente: Livello 4 - Flusso Completo Pagamento EGILI (Collapsible) --}}
{{-- Dettaglio completo sul sistema EGILI --}}

<details class="collapsible-section">
    <summary>
        <span class="material-icons text-xl">stars</span>
        Il Flusso Completo del Pagamento EGILI
    </summary>
    <div class="collapsible-content">

        {{-- Cos'è EGILI --}}
        <h5 class="mb-3 mt-4 text-lg font-semibold text-gray-800">Cos'è EGILI?</h5>
        <p class="mb-4 text-gray-700">
            <strong><a href="#glossary-egili" class="glossary-link">EGILI</a></strong> è il
            <strong>token utility interno</strong> di FlorenceEGI con caratteristiche distintive:
        </p>
        <ul class="mb-4 list-inside list-disc space-y-1 text-gray-700">
            <li><strong>Non trasferibile:</strong> Non può essere scambiato tra utenti.</li>
            <li><strong>Non quotato:</strong> Nessuna quotazione su exchange esterni.</li>
            <li><strong>Account-bound:</strong> Legato all'account utente, non a un <a href="#glossary-wallet"
                    class="glossary-link">wallet</a> crypto.</li>
            <li><strong>Merit-based:</strong> Si guadagna attraverso attività meritevoli, non si compra direttamente.
            </li>
            <li><strong>Tasso di conversione:</strong> 1 EGILO = €0,01 (valore percepito interno).</li>
        </ul>

        {{-- Come si Guadagnano --}}
        <h5 class="mb-3 mt-6 text-lg font-semibold text-gray-800">Come si Guadagnano EGILI?</h5>
        <ul class="mb-4 list-inside list-disc space-y-1 text-gray-700">
            <li><strong>Volume vendite:</strong> Il Creator guadagna EGILI proporzionali alle vendite generate.</li>
            <li><strong>Referral verificati:</strong> Bonus per nuovi utenti portati che completano il KYC.</li>
            <li><strong>Donazioni EPP volontarie:</strong> Bonus aggiuntivo per donazioni oltre il 20% standard.</li>
            <li><strong>Partecipazione community:</strong> Eventi, feedback costruttivi, contributi.</li>
            <li><strong>Fondo distribuzione:</strong> 1% delle fee piattaforma → Pool EGILI distribuito secondo merito.
            </li>
        </ul>

        {{-- Il Meccanismo di Pagamento --}}
        <h5 class="mb-3 mt-6 text-lg font-semibold text-gray-800">Il Meccanismo di Pagamento</h5>
        <p class="mb-4 text-gray-700">
            Quando un Buyer paga con EGILI, <strong>non avviene alcun trasferimento diretto</strong>
            dal Buyer al Creator. Il flusso è gestito internamente dalla piattaforma:
        </p>
        <div class="space-y-3 text-gray-700">
            <ol class="list-inside list-decimal space-y-2">
                <li><strong>Verifica saldo:</strong> Il sistema controlla che il Buyer abbia EGILI sufficienti (es. EGI
                    a €25 richiede 2.500 EGILI).</li>
                <li><strong>Bruciatura (Burn):</strong> Gli EGILI del Buyer vengono <strong>rimossi
                        definitivamente</strong> dalla circolazione (meccanismo deflazionario).</li>
                <li><strong>Regalo al Creator:</strong> La piattaforma <strong>regala EGILI Gift</strong> al Creator in
                    quantità equivalente a quelli bruciati.</li>
                <li><strong>Mint EGI:</strong> L'<a href="#glossary-egi" class="glossary-link">EGI</a> viene mintato e
                    trasferito al Buyer normalmente.</li>
                <li><strong>Tracciabilità:</strong> Ogni transazione è registrata con audit trail completo.</li>
            </ol>
        </div>

        {{-- Esempio Pratico --}}
        <div class="my-4 rounded-lg border-l-4 border-amber-500 bg-amber-50 p-4">
            <h5 class="mb-2 font-semibold text-amber-900">Esempio Pratico</h5>
            <p class="text-sm text-amber-800">
                <strong>Scenario:</strong> Un EGI è in vendita a €25,00. Il Buyer ha 5.000 EGILI nel proprio saldo.
            </p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-amber-800">
                <li>EGILI richiesti: 2.500 (€25,00 ÷ €0,01)</li>
                <li>Saldo Buyer prima: 5.000 EGILI</li>
                <li>Dopo acquisto: 5.000 - 2.500 = <strong>2.500 EGILI rimanenti</strong></li>
                <li>Creator riceve: <strong>2.500 EGILI Gift</strong> dalla piattaforma</li>
            </ul>
        </div>

        {{-- Tipologie di EGILI --}}
        <h5 class="mb-3 mt-6 text-lg font-semibold text-gray-800">Tipologie di EGILI</h5>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">Tipo</th>
                        <th class="border px-4 py-2 text-left">Acquisizione</th>
                        <th class="border px-4 py-2 text-left">Scadenza</th>
                        <th class="border px-4 py-2 text-left">Priorità Consumo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Gift</td>
                        <td class="border px-4 py-2">Donati dalla piattaforma</td>
                        <td class="border px-4 py-2">⏰ N giorni</td>
                        <td class="border px-4 py-2">🔴 Prima (scadenza più vicina)</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2 font-semibold">Lifetime</td>
                        <td class="border px-4 py-2">Acquistati dall'utente</td>
                        <td class="border px-4 py-2">♾️ Mai</td>
                        <td class="border px-4 py-2">🟢 Dopo</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-sm text-gray-600">
            Quando si spendono EGILI, il sistema consuma <strong>prima i Gift</strong>
            (ordinati per data di scadenza, quelli in scadenza prima), <strong>poi i Lifetime</strong>
            (che non scadono mai). Questo garantisce che gli EGILI con scadenza vengano utilizzati prima di perderli.
        </p>

        {{-- Limitazioni --}}
        <h5 class="mb-3 mt-6 text-lg font-semibold text-gray-800">Limitazioni</h5>
        <ul class="mb-4 list-inside list-disc space-y-1 text-gray-700">
            <li><strong>Opt-in Creator:</strong> Il Creator deve abilitare esplicitamente l'opzione per ogni EGI.</li>
            <li><strong>Irreversibile:</strong> Gli EGILI spesi sono bruciati definitivamente (no refund).</li>
            <li><strong>Non cumulabile:</strong> Non si può pagare "metà EGILI + metà FIAT" — è tutto o niente.</li>
        </ul>

        {{-- Conformità MiCA --}}
        <div class="mt-6 rounded-lg border-l-4 border-green-600 bg-green-50 p-4">
            <h5 class="mb-2 font-semibold text-green-900">
                Conformità Normativa <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>
            </h5>
            <p class="text-sm text-green-800">
                Gli <a href="#glossary-egili" class="glossary-link">EGILI</a> <strong>NON sono crypto-asset</strong>
                ai sensi del Regolamento <a href="#glossary-mica" class="glossary-link">MiCA</a> perché:
                non sono trasferibili, non sono convertibili in denaro, hanno utilizzo esclusivo interno alla
                piattaforma.
                Sono classificati come <strong>punti fedeltà</strong>, analoghi ai programmi loyalty tradizionali.
                FlorenceEGI opera pertanto <strong>fuori dal perimetro MiCA</strong> per questa funzionalità,
                senza necessità di licenza CASP/EMI.
            </p>
        </div>
    </div>
</details>
