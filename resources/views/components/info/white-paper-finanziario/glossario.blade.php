{{-- Componente: Glossario --}}
{{-- Glossario completo del White Paper Finanziario --}}

<section id="glossario" class="fade-in mt-16">
    <h2 class="mb-10 border-t border-gray-200 pt-12 text-center text-4xl font-bold text-emerald-800">
        Glossario
    </h2>
    <div class="rounded-2xl bg-white p-8 shadow-lg">
        <dl class="space-y-8">
            {{-- Anchor Hash --}}
            <div>
                <dt id="glossary-anchor-hash" class="text-xl font-bold text-emerald-700">Anchor hash</dt>
                <dd class="mt-1 text-gray-700">L'azione di scrivere su <a href="#glossary-blockchain"
                        class="glossary-link">blockchain</a> l'"impronta" digitale (hash) di un documento. Questo non
                    rivela il contenuto del documento ma crea una prova immutabile della sua esistenza e integrità in un
                    dato momento.</dd>
            </div>

            {{-- ASA --}}
            <div>
                <dt id="glossary-asa" class="text-xl font-bold text-emerald-700">ASA (Algorand Standard Asset)</dt>
                <dd class="mt-1 text-gray-700">Uno standard per creare "token" (oggetti digitali) sulla <a
                        href="#glossary-blockchain" class="glossary-link">blockchain</a> di Algorand. Nel nostro caso,
                    l'<a href="#glossary-egi" class="glossary-link">EGI</a> è un ASA.</dd>
            </div>

            {{-- Blockchain --}}
            <div>
                <dt id="glossary-blockchain" class="text-xl font-bold text-emerald-700">Blockchain</dt>
                <dd class="mt-1 text-gray-700">Un registro digitale distribuito, immutabile e trasparente che rende
                    verificabili la proprietà e la storia degli oggetti digitali registrati su di essa.</dd>
            </div>

            {{-- CAD --}}
            <div>
                <dt id="glossary-cad" class="text-xl font-bold text-emerald-700">CAD (Codice Amministrazione Digitale)</dt>
                <dd class="mt-1 text-gray-700">D.Lgs. 82/2005, testo normativo italiano che disciplina la gestione
                    documentale digitale della PA e dei privati. Definisce le regole per firma digitale, conservazione
                    sostitutiva, PEC e validità legale dei documenti informatici.</dd>
            </div>

            {{-- Conservazione Sostitutiva --}}
            <div>
                <dt id="glossary-conservazione-sostitutiva" class="text-xl font-bold text-emerald-700">Conservazione Sostitutiva</dt>
                <dd class="mt-1 text-gray-700">Procedura regolamentata che consente di conservare documenti in formato
                    digitale con pieno valore legale, sostituendo l'originale cartaceo. Richiede firma digitale, marca
                    temporale e archiviazione presso conservatori accreditati AgID per almeno 10 anni.</dd>
            </div>

            {{-- Custodial --}}
            <div>
                <dt id="glossary-custodial" class="text-xl font-bold text-emerald-700">Custodial (Wallet)</dt>
                <dd class="mt-1 text-gray-700">Un tipo di <a href="#glossary-wallet" class="glossary-link">wallet</a> in
                    cui un servizio di terze parti (il "custode") gestisce le chiavi private per conto dell'utente.
                    Offre maggiore comodità ma richiede fiducia nel fornitore del servizio.</dd>
            </div>

            {{-- EGI --}}
            <div>
                <dt id="glossary-egi" class="text-xl font-bold text-emerald-700">EGI</dt>
                <dd class="mt-1 text-gray-700">Il certificato digitale che attesta l'autenticità e la proprietà di un
                    bene. Tecnicamente, è un token di tipo <a href="#glossary-asa" class="glossary-link">ASA</a> sulla
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a> di Algorand.</dd>
            </div>

            {{-- EGILI --}}
            <div>
                <dt id="glossary-egili" class="text-xl font-bold text-emerald-700">EGILI (Token Utility Interno)</dt>
                <dd class="mt-1 text-gray-700">Token utility interno di FlorenceEGI, non trasferibile e non quotato su
                    exchange. Si guadagna attraverso attività meritevoli sulla piattaforma (vendite, referral,
                    partecipazione community). Tasso di conversione percepito: 1 EGILO = €0,01. Classificato come
                    <strong>punto fedeltà</strong>, fuori dal perimetro <a href="#glossary-mica"
                        class="glossary-link">MiCA</a>. Può essere usato per pagare <a href="#glossary-egi"
                        class="glossary-link">EGI</a> (se abilitato dal Creator) o servizi della piattaforma.</dd>
            </div>

            {{-- FIAT --}}
            <div>
                <dt id="glossary-fiat" class="text-xl font-bold text-emerald-700">FIAT</dt>
                <dd class="mt-1 text-gray-700">Denaro tradizionale emesso da una banca centrale (es. Euro, Dollaro).
                </dd>
            </div>

            {{-- MiCA-safe --}}
            <div>
                <dt id="glossary-mica-safe" class="text-xl font-bold text-emerald-700">MiCA-safe</dt>
                <dd class="mt-1 text-gray-700">Indica che la piattaforma è progettata per operare in conformità con il
                    regolamento europeo MiCA (Markets in Crypto-Assets), evitando di svolgere attività che
                    richiederebbero licenze specifiche (come la custodia o lo scambio di criptovalute per conto terzi).
                </dd>
            </div>

            {{-- Mint --}}
            <div>
                <dt id="glossary-mint" class="text-xl font-bold text-emerald-700">Mint (o Minting)</dt>
                <dd class="mt-1 text-gray-700">L'atto di creare un nuovo token (come un <a href="#glossary-egi"
                        class="glossary-link">EGI</a>) sulla <a href="#glossary-blockchain"
                        class="glossary-link">blockchain</a>, registrandolo per la prima volta.</dd>
            </div>

            {{-- Non-custodial --}}
            <div>
                <dt id="glossary-non-custodial" class="text-xl font-bold text-emerald-700">Non-custodial (Wallet)</dt>
                <dd class="mt-1 text-gray-700">Un tipo di <a href="#glossary-wallet" class="glossary-link">wallet</a> in
                    cui solo l'utente ha il pieno controllo delle chiavi private e, di conseguenza, dei fondi. La
                    responsabilità della sicurezza è interamente dell'utente.</dd>
            </div>

            {{-- OAM --}}
            <div>
                <dt id="glossary-oam" class="text-xl font-bold text-emerald-700">OAM (Organismo Agenti e Mediatori)</dt>
                <dd class="mt-1 text-gray-700">Ente italiano che gestisce il registro pubblico degli operatori in
                    cripto-attività (VASP). L'iscrizione all'OAM è obbligatoria per exchange, custodian e piattaforme
                    che offrono servizi su crypto-asset in Italia, a garanzia della <a href="#glossary-compliance"
                        class="glossary-link">compliance</a> normativa.</dd>
            </div>

            {{-- Off-chain --}}
            <div>
                <dt id="glossary-off-chain" class="text-xl font-bold text-emerald-700">Off-chain</dt>
                <dd class="mt-1 text-gray-700">Si riferisce a qualsiasi operazione o dato che avviene al di fuori della
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a>, tramite sistemi tradizionali
                    (es. un bonifico gestito da un <a href="#glossary-psp" class="glossary-link">PSP</a>).</dd>
            </div>

            {{-- On-chain --}}
            <div>
                <dt id="glossary-on-chain" class="text-xl font-bold text-emerald-700">On-chain</dt>
                <dd class="mt-1 text-gray-700">Si riferisce a qualsiasi operazione o dato registrato direttamente sulla
                    <a href="#glossary-blockchain" class="glossary-link">blockchain</a>, rendendolo pubblicamente
                    verificabile e immutabile.</dd>
            </div>

            {{-- Opt-in generico --}}
            <div>
                <dt id="glossary-opt-in-generico" class="text-xl font-bold text-emerald-700">Opt-in</dt>
                <dd class="mt-1 text-gray-700">Un'azione richiesta su alcune <a href="#glossary-blockchain"
                        class="glossary-link">blockchain</a> (come Algorand) in cui un <a href="#glossary-wallet"
                        class="glossary-link">wallet</a> deve esplicitamente "accettare" di poter ricevere un
                    determinato tipo di token (<a href="#glossary-asa" class="glossary-link">ASA</a>) prima che questo
                    possa essergli trasferito.</dd>
            </div>

            {{-- Partner autorizzato --}}
            <div>
                <dt id="glossary-partner-autorizzato" class="text-xl font-bold text-emerald-700">Partner autorizzato
                    (CASP/EMI)</dt>
                <dd class="mt-1 text-gray-700">Un soggetto terzo che possiede le licenze necessarie (es. Crypto-Asset
                    Service Provider o Electronic Money Institution) per gestire pagamenti e servizi legati alle
                    criptovalute per conto del merchant.</dd>
            </div>

            {{-- Payout --}}
            <div>
                <dt id="glossary-payout" class="text-xl font-bold text-emerald-700">Payout</dt>
                <dd class="mt-1 text-gray-700">Il processo con cui un <a href="#glossary-psp"
                        class="glossary-link">PSP</a> trasferisce i fondi incassati dalle vendite sul conto bancario del
                    merchant.</dd>
            </div>

            {{-- PSP --}}
            <div>
                <dt id="glossary-psp" class="text-xl font-bold text-emerald-700">PSP (Payment Service Provider)</dt>
                <dd class="mt-1 text-gray-700">Un fornitore di servizi di pagamento regolamentato che incassa pagamenti
                    in valuta <a href="#glossary-fiat" class="glossary-link">FIAT</a> (es. tramite carta di credito),
                    gestisce bonifici, rimborsi e ripartizioni per conto dei commercianti (merchant).</dd>
            </div>

            {{-- Royalties --}}
            <div>
                <dt id="glossary-royalties" class="text-xl font-bold text-emerald-700">Royalties</dt>
                <dd class="mt-1 text-gray-700">Termine generico per percentuali sul prezzo di vendita pagate al
                    creatore. Su FlorenceEGI esistono <strong>due tipi distinti</strong>: (1) <a
                        href="#glossary-royalty-piattaforma" class="glossary-link">Royalty Piattaforma</a>
                    (contrattuale, 4.5%, sempre), (2) <a href="#glossary-diritto-seguito" class="glossary-link">Diritto
                        di Seguito</a> (legale, 4%-0.25%, solo >€3k). Possono essere cumulabili.</dd>
            </div>

            {{-- Royalty Piattaforma --}}
            <div>
                <dt id="glossary-royalty-piattaforma" class="text-xl font-bold text-emerald-700">Royalty Piattaforma
                    (Contrattuale)</dt>
                <dd class="mt-1 text-gray-700">Percentuale (4.5%) che FlorenceEGI <strong>garantisce
                        contrattualmente</strong> al Creator su ogni rivendita secondaria, <strong>anche sotto
                        €3,000</strong>. Gestita automaticamente via smart contract e distribuita istantaneamente.
                    Questa royalty è <strong>separata e aggiuntiva</strong> al <a href="#glossary-diritto-seguito"
                        class="glossary-link">Diritto di Seguito</a> legale. Si applica su tutte le vendite P2P sulla
                    piattaforma, indipendentemente dalla normativa SIAE.</dd>
            </div>

            {{-- SCA --}}
            <div>
                <dt id="glossary-sca" class="text-xl font-bold text-emerald-700">SCA (Strong Customer Authentication)</dt>
                <dd class="mt-1 text-gray-700">Autenticazione forte del cliente prevista dalla <a href="#glossary-psd2"
                        class="glossary-link">PSD2</a>. Richiede almeno due dei tre fattori: qualcosa che l'utente
                    conosce (password), possiede (telefono) o è (biometria). Obbligatoria per pagamenti online >€30.</dd>
            </div>

            {{-- SdI --}}
            <div>
                <dt id="glossary-sdi" class="text-xl font-bold text-emerald-700">SdI (Sistema di Interscambio)</dt>
                <dd class="mt-1 text-gray-700">Sistema dell'Agenzia delle Entrate italiana che gestisce la ricezione,
                    controllo e trasmissione delle fatture elettroniche tra emittenti e destinatari, garantendo la <a
                        href="#glossary-compliance" class="glossary-link">compliance</a> fiscale.</dd>
            </div>

            {{-- Settlement --}}
            <div>
                <dt id="glossary-diritto-seguito" class="text-xl font-bold text-emerald-700">Diritto di Seguito (Droit
                    de Suite)</dt>
                <dd class="mt-1 text-gray-700">Diritto <strong>previsto dalla legge italiana</strong> (L. 633/1941 Art.
                    19bis, D.Lgs. 118/2006) che garantisce al Creator un compenso sulle rivendite delle opere d'arte. Si
                    applica <strong>solo se</strong>: (1) prezzo di vendita ≥ €3,000, (2) vendita tramite professionisti
                    del mercato dell'arte (gallerie, case d'asta, dealer), (3) vendita nell'Unione Europea. Aliquote: 4%
                    (0-€50k), 3% (€50k-€200k), 1% (€200k-€350k), 0.5% (€350k-€500k), 0.25% (oltre €500k). Massimo:
                    €12,500 per vendita. Gestito da SIAE. Durata: vita dell'artista + 70 anni.</dd>
            </div>

            {{-- Diritti Morali --}}
            <div>
                <dt id="glossary-diritti-morali" class="text-xl font-bold text-emerald-700">Diritti Morali d'Autore
                </dt>
                <dd class="mt-1 text-gray-700">Diritti <strong>inalienabili, irrinunciabili e perpetui</strong> del
                    Creator previsti dalla Legge 633/1941 Art. 20: (1) <strong>Diritto di Paternità</strong> -
                    rivendicare sempre la paternità dell'opera e opporsi ad attribuzioni false, (2) <strong>Diritto
                        all'Integrità</strong> - opporsi a deformazioni, mutilazioni o modifiche che possano danneggiare
                    l'onore o la reputazione dell'artista. Questi diritti <strong>restano sempre al Creator</strong>,
                    anche dopo la vendita dell'opera. L'Owner NON può mai modificare, alterare o rimuovere la
                    firma/attribuzione.</dd>
            </div>

            {{-- Diritti Patrimoniali --}}
            <div>
                <dt id="glossary-diritti-patrimoniali" class="text-xl font-bold text-emerald-700">Diritti Patrimoniali
                    d'Autore (Sfruttamento Economico)</dt>
                <dd class="mt-1 text-gray-700">Diritti economici del Creator (Legge 633/1941 Art. 12-19) che includono:
                    <strong>riproduzione</strong> (realizzare copie fisiche/digitali, stampe), <strong>comunicazione al
                        pubblico</strong> (pubblicare online, esposizioni commerciali, TV),
                    <strong>distribuzione</strong> (vendere copie/riproduzioni), <strong>elaborazione</strong> (opere
                    derivative). <strong>IMPORTANTE</strong>: l'acquisto dell'opera fisica (o del NFT) <strong>NON
                        trasferisce</strong> automaticamente questi diritti. L'Owner possiede l'oggetto ma il Creator
                    conserva il copyright sull'immagine. Qualsiasi uso commerciale richiede licenza scritta del Creator.
                </dd>
            </div>

            {{-- Settlement --}}
            <div>
                <dt id="glossary-settlement" class="text-xl font-bold text-emerald-700">Settlement</dt>
                <dd class="mt-1 text-gray-700">Il processo finale di trasferimento di fondi al merchant dopo che una
                    transazione è stata completata e confermata.</dd>
            </div>

            {{-- Transfer --}}
            <div>
                <dt id="glossary-transfer" class="text-xl font-bold text-emerald-700">Transfer</dt>
                <dd class="mt-1 text-gray-700">L'azione di trasferire la proprietà di un token da un <a
                        href="#glossary-wallet" class="glossary-link">wallet</a> a un altro.</dd>
            </div>

            {{-- Wallet --}}
            <div>
                <dt id="glossary-wallet" class="text-xl font-bold text-emerald-700">Wallet</dt>
                <dd class="mt-1 text-gray-700">Un portafoglio digitale utilizzato per custodire e gestire oggetti
                    digitali basati su <a href="#glossary-blockchain" class="glossary-link">blockchain</a> (come <a
                        href="#glossary-egi" class="glossary-link">EGI</a> o criptovalute).</dd>
            </div>

            {{-- Fee --}}
            <div>
                <dt id="glossary-fee" class="text-xl font-bold text-emerald-700">Fee (Commissione)</dt>
                <dd class="mt-1 text-gray-700">La commissione applicata dalla piattaforma FlorenceEGI per i servizi
                    forniti (minting, trading, trasferimenti). Viene fatturata separatamente e non include i fondi
                    destinati a Creator o EPP.</dd>
            </div>

            {{-- Compliance --}}
            <div>
                <dt id="glossary-compliance" class="text-xl font-bold text-emerald-700">Compliance</dt>
                <dd class="mt-1 text-gray-700">L'insieme di attività necessarie per operare nel rispetto delle
                    normative fiscali, legali e regolamentari vigenti in un determinato contesto o giurisdizione.</dd>
            </div>

            {{-- Fatturazione Elettronica --}}
            <div>
                <dt id="glossary-fatturazione-elettronica" class="text-xl font-bold text-emerald-700">Fatturazione
                    Elettronica</dt>
                <dd class="mt-1 text-gray-700">Sistema obbligatorio in Italia per l'emissione, trasmissione e
                    conservazione delle fatture in formato digitale tramite il Sistema di Interscambio (SDI)
                    dell'Agenzia delle Entrate.</dd>
            </div>

            {{-- IVA --}}
            <div>
                <dt id="glossary-iva" class="text-xl font-bold text-emerald-700">IVA (Imposta sul Valore Aggiunto)
                </dt>
                <dd class="mt-1 text-gray-700">Imposta indiretta sui consumi applicata in Italia e nell'Unione Europea.
                    L'aliquota e le modalità di applicazione variano in base al tipo di operazione e alla residenza
                    fiscale delle parti coinvolte.</dd>
            </div>

            {{-- OSS --}}
            <div>
                <dt id="glossary-oss" class="text-xl font-bold text-emerald-700">OSS (One Stop Shop)</dt>
                <dd class="mt-1 text-gray-700">Sistema UE che consente alle aziende di dichiarare e versare l'<a
                        href="#glossary-iva" class="glossary-link">IVA</a> per servizi B2C cross-border tramite un
                    unico portale, semplificando la <a href="#glossary-compliance"
                        class="glossary-link">compliance</a> internazionale.</dd>
            </div>

            {{-- MOSS --}}
            <div>
                <dt id="glossary-moss" class="text-xl font-bold text-emerald-700">MOSS (Mini One Stop Shop)</dt>
                <dd class="mt-1 text-gray-700">Precedente versione del regime <a href="#glossary-oss"
                        class="glossary-link">OSS</a>, specifico per servizi di telecomunicazione, broadcasting ed
                    elettronici. Ora integrato nell'OSS.</dd>
            </div>

            {{-- Reverse Charge --}}
            <div>
                <dt id="glossary-reverse-charge" class="text-xl font-bold text-emerald-700">Reverse Charge</dt>
                <dd class="mt-1 text-gray-700">Meccanismo <a href="#glossary-iva" class="glossary-link">IVA</a> in
                    cui l'obbligo di versare l'imposta passa dal fornitore al cliente (tipicamente applicato nelle
                    transazioni B2B intra-UE con Partita IVA).</dd>
            </div>

            {{-- Partita IVA --}}
            <div>
                <dt id="glossary-partita-iva" class="text-xl font-bold text-emerald-700">Partita IVA</dt>
                <dd class="mt-1 text-gray-700">Numero identificativo attribuito a chi svolge attività economica
                    abituale in Italia. È obbligatoria per professionisti, imprese e attività commerciali continuative.
                </dd>
            </div>

            {{-- Ricevuta Prestazione Occasionale --}}
            <div>
                <dt id="glossary-ricevuta-prestazione-occasionale" class="text-xl font-bold text-emerald-700">Ricevuta
                    per Prestazione Occasionale</dt>
                <dd class="mt-1 text-gray-700">Documento fiscale emesso da un privato (senza <a
                        href="#glossary-partita-iva" class="glossary-link">Partita IVA</a>) per attività non abituali.
                    L'importo va dichiarato come "reddito diverso" nella dichiarazione dei redditi.</dd>
            </div>

            {{-- ETS --}}
            <div>
                <dt id="glossary-ets" class="text-xl font-bold text-emerald-700">ETS (Ente del Terzo Settore)</dt>
                <dd class="mt-1 text-gray-700">Organizzazioni non profit riconosciute dal Codice del Terzo Settore
                    italiano (es. associazioni, fondazioni, ONLUS). Godono di agevolazioni fiscali e non devono emettere
                    fattura per donazioni.</dd>
            </div>

            {{-- ONLUS --}}
            <div>
                <dt id="glossary-onlus" class="text-xl font-bold text-emerald-700">ONLUS (Organizzazione Non Lucrativa
                    di Utilità Sociale)</dt>
                <dd class="mt-1 text-gray-700">Ente senza scopo di lucro riconosciuto in Italia, ora confluito nel
                    regime <a href="#glossary-ets" class="glossary-link">ETS</a>. Le donazioni ricevute beneficiano di
                    agevolazioni fiscali per i donatori.</dd>
            </div>

            {{-- EPP --}}
            <div>
                <dt id="glossary-epp" class="text-xl font-bold text-emerald-700">EPP (Environmental Protection
                    Project)</dt>
                <dd class="mt-1 text-gray-700">Progetti di tutela ambientale collegati agli <a href="#glossary-egi"
                        class="glossary-link">EGI</a> su FlorenceEGI. Gli EPP ricevono donazioni dirette sul proprio <a
                        href="#glossary-wallet" class="glossary-link">wallet</a> da ogni transazione di EGI associato.
                </dd>
            </div>

            {{-- Creator --}}
            <div>
                <dt id="glossary-creator" class="text-xl font-bold text-emerald-700">Creator (Artista/Autore)</dt>
                <dd class="mt-1 text-gray-700">L'autore originale dell'opera che crea un <a href="#glossary-egi"
                        class="glossary-link">EGI</a> sulla piattaforma. Il Creator riceve i proventi delle vendite
                    primarie e <a href="#glossary-royalty-piattaforma" class="glossary-link">royalty</a> automatiche
                    sulle rivendite (4.5% sempre + eventuale <a href="#glossary-diritto-seguito"
                        class="glossary-link">Diritto di Seguito</a> legale se >€3k). <strong>IMPORTANTE</strong>: il
                    Creator <strong>conserva sempre</strong> i <a href="#glossary-diritti-morali"
                        class="glossary-link">Diritti Morali</a> (paternità, integrità) e i <a
                        href="#glossary-diritti-patrimoniali" class="glossary-link">Diritti Patrimoniali</a>
                    (riproduzione, copyright immagine), anche dopo la vendita dell'opera fisica o del NFT. L'Owner
                    acquisisce solo il possesso materiale, non il copyright.</dd>
            </div>

            {{-- Mecenate --}}
            <div>
                <dt id="glossary-mecenate" class="text-xl font-bold text-emerald-700">Mecenate</dt>
                <dd class="mt-1 text-gray-700">Soggetto che sostiene finanziariamente un <a href="#glossary-creator"
                        class="glossary-link">Creator</a> o un <a href="#glossary-epp" class="glossary-link">EPP</a>
                    attraverso acquisto di <a href="#glossary-egi" class="glossary-link">EGI</a> o donazioni dirette
                    sulla piattaforma FlorenceEGI.</dd>
            </div>

            {{-- Trader --}}
            <div>
                <dt id="glossary-trader" class="text-xl font-bold text-emerald-700">Trader</dt>
                <dd class="mt-1 text-gray-700">Utente che acquista e rivende <a href="#glossary-egi"
                        class="glossary-link">EGI</a> sul mercato secondario della piattaforma con l'obiettivo di
                    realizzare plusvalenze. È responsabile della dichiarazione fiscale dei propri guadagni.</dd>
            </div>

            {{-- Plusvalenza --}}
            <div>
                <dt id="glossary-plusvalenza" class="text-xl font-bold text-emerald-700">Plusvalenza</dt>
                <dd class="mt-1 text-gray-700">Guadagno realizzato dalla vendita di un bene a un prezzo superiore a
                    quello di acquisto. Nel contesto fiscale italiano, le plusvalenze possono essere soggette a
                    tassazione come "redditi diversi" o "redditi da capitale".</dd>
            </div>

            {{-- PSD2 --}}
            <div>
                <dt id="glossary-psd2" class="text-xl font-bold text-emerald-700">PSD2 (Payment Services Directive 2)</dt>
                <dd class="mt-1 text-gray-700">Direttiva europea 2015/2366 che regola i servizi di pagamento nel
                    mercato interno. Introduce la <a href="#glossary-sca" class="glossary-link">SCA</a> (autenticazione
                    forte), l'open banking e tutele rafforzate per i consumatori. Recepita in Italia con D.Lgs. 11/2010.</dd>
            </div>

            {{-- PSP --}}
            <div>
                <dt id="glossary-merchant" class="text-xl font-bold text-emerald-700">Merchant</dt>
                <dd class="mt-1 text-gray-700">Termine generico per indicare il venditore o fornitore di servizi che
                    utilizza la piattaforma per vendere beni o emettere <a href="#glossary-egi"
                        class="glossary-link">EGI</a>. Nel contesto FlorenceEGI, tipicamente un <a
                        href="#glossary-creator" class="glossary-link">Creator</a>.</dd>
            </div>

            {{-- CASP --}}
            <div>
                <dt id="glossary-casp" class="text-xl font-bold text-emerald-700">CASP (Crypto-Asset Service Provider)
                </dt>
                <dd class="mt-1 text-gray-700">Fornitore di servizi su cripto-attività regolamentato dal regolamento
                    europeo MiCA. Include exchange, custodian e altri intermediari autorizzati a gestire criptovalute
                    per conto terzi.</dd>
            </div>

            {{-- EMI --}}
            <div>
                <dt id="glossary-emi" class="text-xl font-bold text-emerald-700">EMI (Electronic Money Institution)
                </dt>
                <dd class="mt-1 text-gray-700">Istituto di moneta elettronica autorizzato a emettere e gestire moneta
                    elettronica e fornire servizi di pagamento nell'Unione Europea.</dd>
            </div>

            {{-- GDPR --}}
            <div>
                <dt id="glossary-gdpr" class="text-xl font-bold text-emerald-700">GDPR (General Data Protection
                    Regulation)</dt>
                <dd class="mt-1 text-gray-700">Regolamento europeo (UE) 2016/679 sulla protezione dei dati personali e
                    sulla privacy. Stabilisce obblighi rigorosi per la raccolta, il trattamento, la conservazione e la
                    cancellazione dei dati personali, garantendo diritti fondamentali agli utenti come il diritto
                    all'oblio e la portabilità dei dati. FlorenceEGI è pienamente conforme al GDPR nella gestione delle
                    chiavi private e dei dati degli utenti.</dd>
            </div>

            {{-- MiCA --}}
            <div>
                <dt id="glossary-mica" class="text-xl font-bold text-emerald-700">MiCA (Markets in Crypto-Assets
                    Regulation)</dt>
                <dd class="mt-1 text-gray-700">Regolamento europeo che disciplina i mercati di cripto-attività,
                    definendo regole chiare per l'emissione, l'offerta e l'ammissione al trading di crypto-asset, nonché
                    per i fornitori di servizi correlati.</dd>
            </div>

            {{-- Sostituto d'Imposta --}}
            <div>
                <dt id="glossary-sostituto-imposta" class="text-xl font-bold text-emerald-700">Sostituto d'Imposta
                </dt>
                <dd class="mt-1 text-gray-700">Soggetto che, per legge, trattiene e versa le imposte dovute da un altro
                    contribuente (es. datore di lavoro che trattiene IRPEF sullo stipendio). FlorenceEGI NON è sostituto
                    d'imposta.</dd>
            </div>

            {{-- Fatturazione Batch --}}
            <div>
                <dt id="glossary-fatturazione-batch" class="text-xl font-bold text-emerald-700">Fatturazione Batch
                    (Cumulativa)</dt>
                <dd class="mt-1 text-gray-700">Sistema di fatturazione che raggruppa più operazioni in un unico
                    documento fiscale periodico (es. mensile), con allegato il dettaglio delle singole transazioni.
                    Utilizzato per gestire alto volume di micro-transazioni.</dd>
            </div>

            {{-- Dashboard --}}
            <div>
                <dt id="glossary-dashboard" class="text-xl font-bold text-emerald-700">Dashboard</dt>
                <dd class="mt-1 text-gray-700">Pannello di controllo sulla piattaforma FlorenceEGI che fornisce a ogni
                    utente una visione d'insieme delle proprie attività, incassi, <a href="#glossary-egi"
                        class="glossary-link">EGI</a> posseduti, report fiscali e strumenti di gestione.</dd>
            </div>

            {{-- DPO --}}
            <div>
                <dt id="glossary-dpo" class="text-xl font-bold text-emerald-700">DPO (Data Protection Officer)</dt>
                <dd class="mt-1 text-gray-700">Responsabile della Protezione dei Dati, figura prevista dal <a
                        href="#glossary-gdpr" class="glossary-link">GDPR</a> che supervisiona la conformità al
                    regolamento privacy, gestisce le richieste degli interessati e funge da punto di contatto con
                    l'Autorità Garante.</dd>
            </div>

            {{-- Drops --}}
            <div>
                <dt id="glossary-qr-code" class="text-xl font-bold text-emerald-700">QR Code (Verifica Pubblica)</dt>
                <dd class="mt-1 text-gray-700">Codice QR associato a ogni <a href="#glossary-egi"
                        class="glossary-link">EGI</a> che permette la verifica pubblica e immediata dell'autenticità
                    del certificato digitale tramite scansione con smartphone, senza necessità di autenticazione.</dd>
            </div>

            {{-- ERP --}}
            <div>
                <dt id="glossary-erp" class="text-xl font-bold text-emerald-700">ERP (Enterprise Resource Planning)
                </dt>
                <dd class="mt-1 text-gray-700">Sistema software gestionale integrato utilizzato da aziende ed enti per
                    gestire processi aziendali, contabilità, risorse umane, supply chain e altre attività operative.
                </dd>
            </div>

            {{-- CRM --}}
            <div>
                <dt id="glossary-crm" class="text-xl font-bold text-emerald-700">CRM (Customer Relationship
                    Management)</dt>
                <dd class="mt-1 text-gray-700">Sistema software per la gestione delle relazioni con i clienti,
                    utilizzato per tracciare interazioni, vendite, supporto e marketing. Utilizzato da grandi enti per
                    integrare dati da FlorenceEGI.</dd>
            </div>

            {{-- Export CSV/XML --}}
            <div>
                <dt id="glossary-export-csv" class="text-xl font-bold text-emerald-700">Export CSV/XML</dt>
                <dd class="mt-1 text-gray-700">Funzionalità della piattaforma che permette di scaricare i dati delle
                    proprie transazioni in formato CSV (tabella) o XML (strutturato), per l'integrazione con software di
                    contabilità esterni.</dd>
            </div>

            {{-- Alert Fiscale --}}
            <div>
                <dt id="glossary-alert-fiscale" class="text-xl font-bold text-emerald-700">Alert Fiscale</dt>
                <dd class="mt-1 text-gray-700">Notifica automatica inviata dalla piattaforma quando si raggiungono
                    soglie fiscali rilevanti (es. limite per prestazione occasionale, volume di trading significativo)
                    per ricordare all'utente i propri obblighi di <a href="#glossary-compliance"
                        class="glossary-link">compliance</a>.</dd>
            </div>

            {{-- Ricevuta Donazione --}}
            <div>
                <dt id="glossary-ricevuta-donazione" class="text-xl font-bold text-emerald-700">Ricevuta di Donazione
                </dt>
                <dd class="mt-1 text-gray-700">Documento rilasciato da un ente (<a href="#glossary-ets"
                        class="glossary-link">ETS</a>, <a href="#glossary-onlus" class="glossary-link">ONLUS</a>) che
                    attesta la donazione ricevuta, permettendo al donatore di usufruire di detrazioni o deduzioni
                    fiscali secondo la normativa vigente.</dd>
            </div>

            {{-- Audit Trail --}}
            <div>
                <dt id="glossary-audit-trail" class="text-xl font-bold text-emerald-700">Audit Trail</dt>
                <dd class="mt-1 text-gray-700">Registrazione cronologica completa e immutabile di tutte le operazioni
                    effettuate, utilizzata per verifiche fiscali, controlli di <a href="#glossary-compliance"
                        class="glossary-link">compliance</a> e tracciabilità. Su FlorenceEGI garantita dalla <a
                        href="#glossary-blockchain" class="glossary-link">blockchain</a>.</dd>
            </div>

            {{-- Tracciabilità --}}
            <div>
                <dt id="glossary-tracciabilita" class="text-xl font-bold text-emerald-700">Tracciabilità</dt>
                <dd class="mt-1 text-gray-700">Capacità di ricostruire la storia completa di un <a
                        href="#glossary-egi" class="glossary-link">EGI</a> (proprietari, transazioni, donazioni)
                    grazie alla registrazione <a href="#glossary-on-chain" class="glossary-link">on-chain</a> e agli
                    <a href="#glossary-audit-trail" class="glossary-link">audit trail</a> della piattaforma.</dd>
            </div>

            {{-- 2FA --}}
            <div>
                <dt id="glossary-2fa" class="text-xl font-bold text-emerald-700">2FA (Two-Factor Authentication)</dt>
                <dd class="mt-1 text-gray-700">Autenticazione a due fattori che richiede, oltre alla password, un
                    secondo elemento di verifica (es. codice SMS, app authenticator, chiave hardware) per accedere
                    all'account. Obbligatoria per operazioni sensibili su FlorenceEGI.</dd>
            </div>

            {{-- Algorand --}}
            <div>
                <dt id="glossary-algoritmo" class="text-xl font-bold text-emerald-700">Algorand</dt>
                <dd class="mt-1 text-gray-700"><a href="#glossary-blockchain" class="glossary-link">Blockchain</a> di
                    tipo "proof-of-stake pura" ad alte prestazioni, basso costo e impatto ambientale ridotto, utilizzata
                    da FlorenceEGI per la creazione e gestione degli <a href="#glossary-egi"
                        class="glossary-link">EGI</a> come <a href="#glossary-asa" class="glossary-link">ASA</a>.
                </dd>
            </div>

            {{-- AML --}}
            <div>
                <dt id="glossary-aml" class="text-xl font-bold text-emerald-700">AML (Anti-Money Laundering)</dt>
                <dd class="mt-1 text-gray-700">Insieme di normative e procedure volte a prevenire il riciclaggio di
                    denaro e il finanziamento del terrorismo. In Italia regolato dal D.Lgs. 231/2007, richiede
                    l'adeguata verifica della clientela e la segnalazione di operazioni sospette.</dd>
            </div>

            {{-- Hash --}}
            <div>
                <dt id="glossary-hash" class="text-xl font-bold text-emerald-700">Hash (Impronta Digitale)</dt>
                <dd class="mt-1 text-gray-700">Sequenza alfanumerica unica generata da un algoritmo crittografico a
                    partire da un file o documento. Qualsiasi modifica al contenuto produce un hash completamente
                    diverso, garantendo integrità e verificabilità.</dd>
            </div>

            {{-- INTRASTAT --}}
            <div>
                <dt id="glossary-intrastat" class="text-xl font-bold text-emerald-700">INTRASTAT</dt>
                <dd class="mt-1 text-gray-700">Sistema di rilevazione statistica degli scambi di beni e servizi tra
                    Stati membri UE. Obbligo mensile/trimestrale per soggetti IVA che superano determinate soglie di
                    operazioni intracomunitarie. Gestito in Italia dall'Agenzia delle Dogane.</dd>
            </div>

            {{-- KYC --}}
            <div>
                <dt id="glossary-kyc" class="text-xl font-bold text-emerald-700">KYC (Know Your Customer)</dt>
                <dd class="mt-1 text-gray-700">Processo di identificazione e verifica dell'identità dei clienti,
                    obbligatorio per normativa <a href="#glossary-aml" class="glossary-link">AML</a>. Include verifica
                    documenti, controlli antiriciclaggio e valutazione del rischio. Su FlorenceEGI si applica a più
                    livelli in base alle soglie operative.</dd>
            </div>

            {{-- Mecenate --}}
            <div>
                <dt id="glossary-chiave-privata" class="text-xl font-bold text-emerald-700">Chiave Privata</dt>
                <dd class="mt-1 text-gray-700">Codice segreto crittografico che permette di controllare e autorizzare
                    operazioni su un <a href="#glossary-wallet" class="glossary-link">wallet</a> blockchain. Chi
                    possiede la chiave privata ha pieno controllo dei fondi. Non va MAI condivisa.</dd>
            </div>

            {{-- Mnemonic --}}
            <div>
                <dt id="glossary-mnemonic" class="text-xl font-bold text-emerald-700">Mnemonic (Seed Phrase / Frase
                    Segreta)</dt>
                <dd class="mt-1 text-gray-700">Sequenza di 25 parole (nel caso di Algorand) che rappresenta la <a
                        href="#glossary-chiave-privata" class="glossary-link">chiave privata</a> di un <a
                        href="#glossary-wallet" class="glossary-link">wallet</a> in formato leggibile. Chiunque
                    possieda questa frase può ricostruire il wallet e controllarne i contenuti. Deve essere conservata
                    in modo sicuro e offline.</dd>
            </div>

            {{-- Opt-in (Algorand specifico) --}}
            <div>
                <dt id="glossary-opt-in" class="text-xl font-bold text-emerald-700">Opt-in (Algorand)</dt>
                <dd class="mt-1 text-gray-700">Operazione obbligatoria su <a href="#glossary-algoritmo"
                        class="glossary-link">Algorand</a> che un <a href="#glossary-wallet"
                        class="glossary-link">wallet</a> deve eseguire prima di poter ricevere un determinato <a
                        href="#glossary-asa" class="glossary-link">ASA</a>. L'opt-in richiede il blocco di 0,1 <a
                        href="#glossary-algo" class="glossary-link">ALGO</a> come bilancio minimo per ogni asset,
                    importo che viene liberato con l'operazione inversa (opt-out).</dd>
            </div>

            {{-- ALGO --}}
            <div>
                <dt id="glossary-algo" class="text-xl font-bold text-emerald-700">ALGO</dt>
                <dd class="mt-1 text-gray-700">Criptovaluta nativa della <a href="#glossary-blockchain"
                        class="glossary-link">blockchain</a> <a href="#glossary-algoritmo"
                        class="glossary-link">Algorand</a>. Viene utilizzata per pagare le fee di transazione e come
                    requisito di bilancio minimo per detenere <a href="#glossary-asa" class="glossary-link">ASA</a>.
                </dd>
            </div>

            {{-- Treasury --}}
            <div>
                <dt id="glossary-treasury" class="text-xl font-bold text-emerald-700">Treasury (Wallet di Piattaforma)
                </dt>
                <dd class="mt-1 text-gray-700"><a href="#glossary-wallet" class="glossary-link">Wallet</a>
                    controllato dalla piattaforma FlorenceEGI utilizzato per le operazioni tecniche di <a
                        href="#glossary-mint" class="glossary-link">minting</a> e gestione degli <a
                        href="#glossary-egi" class="glossary-link">EGI</a>. Nel modello <a href="#glossary-custodial"
                        class="glossary-link">custodiale</a>, gli ASA sono conservati nel Treasury fino al riscatto da
                    parte dell'utente.</dd>
            </div>

            {{-- Token --}}
            <div>
                <dt id="glossary-token" class="text-xl font-bold text-emerald-700">Token</dt>
                <dd class="mt-1 text-gray-700">Unità digitale rappresentante un asset, un diritto o un valore su una <a
                        href="#glossary-blockchain" class="glossary-link">blockchain</a>. Nel contesto FlorenceEGI,
                    l'<a href="#glossary-egi" class="glossary-link">EGI</a> è un token di tipo <a
                        href="#glossary-asa" class="glossary-link">ASA</a>.</dd>
            </div>

            {{-- Immutabilità --}}
            <div>
                <dt id="glossary-immutabilita" class="text-xl font-bold text-emerald-700">Immutabilità</dt>
                <dd class="mt-1 text-gray-700">Caratteristica fondamentale della <a href="#glossary-blockchain"
                        class="glossary-link">blockchain</a>: una volta registrata, un'informazione non può essere
                    modificata o cancellata, garantendo la veridicità storica dei dati.</dd>
            </div>

            {{-- Report Fiscale --}}
            <div>
                <dt id="glossary-report" class="text-xl font-bold text-emerald-700">Report Fiscale</dt>
                <dd class="mt-1 text-gray-700">Documento riepilogativo generato automaticamente dalla piattaforma che
                    sintetizza tutte le transazioni, incassi, <a href="#glossary-fee" class="glossary-link">fee</a> e
                    donazioni di un utente in un determinato periodo, facilitando la dichiarazione fiscale.</dd>
            </div>

            {{-- Registro Immutabile --}}
            <div>
                <dt id="glossary-registro-immutabile" class="text-xl font-bold text-emerald-700">Registro Immutabile</dt>
                <dd class="mt-1 text-gray-700">Sistema di archiviazione dove i dati, una volta scritti, non possono
                    essere modificati o cancellati. Su FlorenceEGI le transazioni sono registrate sia in database
                    tradizionale (audit trail) sia <a href="#glossary-on-chain" class="glossary-link">on-chain</a>,
                    garantendo tracciabilità completa e valore probatorio.</dd>
            </div>

            {{-- RUNTS --}}
            <div>
                <dt id="glossary-runts" class="text-xl font-bold text-emerald-700">RUNTS (Registro Unico Nazionale Terzo Settore)</dt>
                <dd class="mt-1 text-gray-700">Registro pubblico istituito dal D.Lgs. 117/2017 che censisce tutti gli
                    <a href="#glossary-ets" class="glossary-link">ETS</a> italiani. L'iscrizione al RUNTS è condizione
                    per accedere alle agevolazioni fiscali del Terzo Settore e per il riconoscimento giuridico dell'ente.</dd>
            </div>

            {{-- Royalties --}}
            <div>
                <dt id="glossary-ammk" class="text-xl font-bold text-emerald-700">AMMk (Asset Market Maker)</dt>
                <dd class="mt-1 text-gray-700">
                    <p>Termine coniato da FlorenceEGI per descrivere piattaforme che trasformano opere o contenuti in <a
                            href="#glossary-egi" class="glossary-link">EGI</a> (asset digitali) e ne governano
                        l'intero ciclo di valore.</p>
                    <p class="mt-2">FlorenceEGI è il primo AMMk al mondo: il <a href="#glossary-florenceegi-core"
                            class="glossary-link">FlorenceEGI Core</a> coordina cinque blocchi specializzati, ognuno
                        con responsabilità precise:</p>
                    <ul class="mt-3 list-inside list-disc space-y-3 text-gray-700">
                        <li>
                            <strong>NATAN Market Engine</strong> – l'intelligenza del <a href="#glossary-natan"
                                class="glossary-link">tenant NATAN</a> che rende la piattaforma un vero market maker.
                            Comprende due componenti inscindibili:
                            <ul class="ml-5 mt-2 list-inside list-disc space-y-1 text-gray-700">
                                <li><strong>Valuation</strong> – definisce valore, floor price e traiettoria economica
                                    analizzando qualità, storico e domanda.</li>
                                <li><strong>Activation</strong> – orchestra campagne, alert e suggerimenti operativi
                                    basati su trigger on/off-chain.</li>
                            </ul>
                        </li>
                        <li><strong>Asset Engine</strong> – gestisce listing, aste, vendite secondarie e liquidità
                            dell'asset digitale.</li>
                        <li><strong>Distribution Engine</strong> – automatizza royalty, fee piattaforma e quota <a
                                href="#glossary-epp" class="glossary-link">EPP</a>.</li>
                        <li><strong>Co-Creation Engine</strong> – governa il flusso <a href="#glossary-creator"
                                class="glossary-link">Creator</a> / Co-Creator / Collector e la generazione dell'EGI
                            (minting, notarizzazione, firme digitali).</li>
                        <li><strong>Compliance Engine</strong> – integra GDPR, audit trail e tutela <a
                                href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>.</li>
                    </ul>
                </dd>
            </div>

            {{-- NATAN --}}
            <div>
                <dt id="glossary-natan" class="text-xl font-bold text-emerald-700">NATAN (Neural Assistant)</dt>
                <dd class="mt-1 text-gray-700">Tenant funzionale di FlorenceEGI dedicato ad assistenza documentale,
                    notarizzazione e automazioni AI. Opera come layer cognitivo per enti pubblici e privati, con servizi
                    RAG, verifica prove e suggerimenti operativi attivati da trigger intelligenti.</dd>
            </div>

            {{-- Drops --}}
            <div>
                <dt id="glossary-drops" class="text-xl font-bold text-emerald-700">Drops (Trimestrali)</dt>
                <dd class="mt-1 text-gray-700">Eventi trimestrali che selezionano opere eccellenti e culminano in una
                    Serata Memorabile, concentrando attenzione e liquidità.</dd>
            </div>

            {{-- FlorenceEGI Core --}}
            <div>
                <dt id="glossary-florenceegi-core" class="text-xl font-bold text-emerald-700">FlorenceEGI Core (SaaS)
                </dt>
                <dd class="mt-1 text-gray-700">Nodo centrale dell'ecosistema: gestisce onboarding, autenticazione,
                    billing, governance, logging ULM/UEM e registro tenant. Garantisce che i tenant condividano policy,
                    sicurezza e compliance comuni.</dd>
            </div>

            {{-- Tenant specializzato --}}
            <div>
                <dt id="glossary-tenant-specializzato" class="text-xl font-bold text-emerald-700">Tenant specializzato
                </dt>
                <dd class="mt-1 text-gray-700">Istanza verticale collegata a FlorenceEGI Core con funzioni dedicate:
                    es. <strong>Natan</strong> (assistente documentale/AI) o <strong>FlorenceArtEGI</strong> (arte e
                    marketplace). Ogni tenant eredita sicurezza e fiscalità dal core, mantenendo processi e interfacce
                    proprie.</dd>
            </div>

            {{-- TSA --}}
            <div>
                <dt id="glossary-tsa" class="text-xl font-bold text-emerald-700">TSA (Time Stamp Authority)</dt>
                <dd class="mt-1 text-gray-700">Ente accreditato che emette marche temporali con valore legale,
                    attestando che un documento esisteva in un determinato momento. Essenziale per la <a
                        href="#glossary-conservazione-sostitutiva" class="glossary-link">conservazione sostitutiva</a>
                    e la validità probatoria dei documenti digitali.</dd>
            </div>

            {{-- VASP --}}
            <div>
                <dt id="glossary-vasp" class="text-xl font-bold text-emerald-700">VASP (Virtual Asset Service Provider)</dt>
                <dd class="mt-1 text-gray-700">Fornitore di servizi su asset virtuali (exchange, custodian, wallet
                    provider). Soggetto a normativa <a href="#glossary-aml" class="glossary-link">AML</a> e, in Italia,
                    obbligo di iscrizione al registro <a href="#glossary-oam" class="glossary-link">OAM</a>. Con
                    l'entrata in vigore del <a href="#glossary-mica" class="glossary-link">MiCA</a>, diventa <a
                        href="#glossary-casp" class="glossary-link">CASP</a>.</dd>
            </div>
        </dl>
    </div>
</section>
