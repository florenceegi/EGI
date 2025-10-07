<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>White Paper Finanziario Interattivo - FlorenceEGI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 140px;
            /* Offset per header sticky */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfcfb;
            color: #383838;
        }

        .active-nav {
            background-color: #047857;
            /* emerald-700 */
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .nav-item {
            transition: all 0.2s ease-in-out;
        }

        .content-section {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: auto;
            height: 300px;
        }

        .glossary-link {
            color: #1B365D;
            /* Blu Algoritmo - Brand FlorenceEGI */
            font-weight: 600;
            text-decoration: underline;
            text-decoration-color: #D4A574;
            /* Oro Fiorentino */
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .glossary-link:hover {
            color: #D4A574;
            /* Oro Fiorentino al hover */
            text-decoration-color: #1B365D;
            text-decoration-thickness: 2.5px;
            text-shadow: 0 0 8px rgba(212, 165, 116, 0.3);
        }

        /* Highlight temporaneo per voce di glossario target */
        dt:target {
            animation: highlightTerm 2s ease-in-out;
            scroll-margin-top: 140px;
        }

        @keyframes highlightTerm {
            0% {
                background-color: rgba(212, 165, 116, 0.3);
                /* Oro Fiorentino trasparente */
                transform: scale(1.02);
            }

            50% {
                background-color: rgba(212, 165, 116, 0.2);
            }

            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }

        /* Pulsante Torna al Testo */
        #backToTextButton {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #1B365D 0%, #2D5016 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(27, 54, 93, 0.3);
            display: none;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            border: 2px solid #D4A574;
        }

        #backToTextButton:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 15px 35px rgba(212, 165, 116, 0.4);
            background: linear-gradient(135deg, #2D5016 0%, #1B365D 100%);
        }

        #backToTextButton.show {
            display: flex;
            animation: slideInUp 0.4s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-50 bg-white shadow-sm">
            <div class="px-4 py-6 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-emerald-800">White Paper Finanziario Interattivo</h1>
                <p class="mt-2 text-gray-600 text-md">Esplora la gestione finanziaria e fiscale di FlorenceEGI in modo
                    semplice e intuitivo.</p>
            </div>
        </header>

        <main class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 mb-8 bg-white shadow-lg rounded-2xl">
                <h2 class="mb-4 text-xl font-bold text-center text-gray-800">Inizia da qui: seleziona un argomento o il
                    tuo ruolo.</h2>
                <nav id="role-nav" class="flex flex-wrap justify-center gap-3 sm:gap-4">
                </nav>
            </div>

            <div id="content-container">
            </div>

            <!-- GLOSSARIO INSERITO QUI -->
            <section id="glossario" class="mt-16 fade-in">
                <h2 class="pt-12 mb-10 text-4xl font-bold text-center border-t border-gray-200 text-emerald-800">
                    Glossario</h2>
                <div class="p-8 bg-white shadow-lg rounded-2xl">
                    <dl class="space-y-8">
                        <div>
                            <dt id="glossary-anchor-hash" class="text-xl font-bold text-emerald-700">Anchor hash</dt>
                            <dd class="mt-1 text-gray-700">L'azione di scrivere su <a href="#glossary-blockchain"
                                    class="glossary-link">blockchain</a> l'“impronta” digitale (hash) di un documento.
                                Questo non rivela il contenuto del documento ma crea una prova immutabile della sua
                                esistenza e integrità in un dato momento.</dd>
                        </div>
                        <div>
                            <dt id="glossary-asa" class="text-xl font-bold text-emerald-700">ASA (Algorand Standard
                                Asset)</dt>
                            <dd class="mt-1 text-gray-700">Uno standard per creare "token" (oggetti digitali) sulla <a
                                    href="#glossary-blockchain" class="glossary-link">blockchain</a> di Algorand. Nel
                                nostro caso, l'<a href="#glossary-egi" class="glossary-link">EGI</a> è un ASA.</dd>
                        </div>
                        <div>
                            <dt id="glossary-blockchain" class="text-xl font-bold text-emerald-700">Blockchain</dt>
                            <dd class="mt-1 text-gray-700">Un registro digitale distribuito, immutabile e trasparente
                                che rende verificabili la proprietà e la storia degli oggetti digitali registrati su di
                                essa.</dd>
                        </div>
                        <div>
                            <dt id="glossary-custodial" class="text-xl font-bold text-emerald-700">Custodial (Wallet)
                            </dt>
                            <dd class="mt-1 text-gray-700">Un tipo di <a href="#glossary-wallet"
                                    class="glossary-link">wallet</a> in cui un servizio di terze parti (il "custode")
                                gestisce le chiavi private per conto dell'utente. Offre maggiore comodità ma richiede
                                fiducia nel fornitore del servizio.</dd>
                        </div>
                        <div>
                            <dt id="glossary-egi" class="text-xl font-bold text-emerald-700">EGI</dt>
                            <dd class="mt-1 text-gray-700">Il certificato digitale che attesta l'autenticità e la
                                proprietà di un bene. Tecnicamente, è un token di tipo <a href="#glossary-asa"
                                    class="glossary-link">ASA</a> sulla <a href="#glossary-blockchain"
                                    class="glossary-link">blockchain</a> di Algorand.</dd>
                        </div>
                        <div>
                            <dt id="glossary-fiat" class="text-xl font-bold text-emerald-700">FIAT</dt>
                            <dd class="mt-1 text-gray-700">Denaro tradizionale emesso da una banca centrale (es. Euro,
                                Dollaro).</dd>
                        </div>
                        <div>
                            <dt id="glossary-mica-safe" class="text-xl font-bold text-emerald-700">MiCA-safe</dt>
                            <dd class="mt-1 text-gray-700">Indica che la piattaforma è progettata per operare in
                                conformità con il regolamento europeo MiCA (Markets in Crypto-Assets), evitando di
                                svolgere attività che richiederebbero licenze specifiche (come la custodia o lo scambio
                                di criptovalute per conto terzi).</dd>
                        </div>
                        <div>
                            <dt id="glossary-mint" class="text-xl font-bold text-emerald-700">Mint (o Minting)</dt>
                            <dd class="mt-1 text-gray-700">L'atto di creare un nuovo token (come un <a
                                    href="#glossary-egi" class="glossary-link">EGI</a>) sulla <a
                                    href="#glossary-blockchain" class="glossary-link">blockchain</a>, registrandolo per
                                la prima volta.</dd>
                        </div>
                        <div>
                            <dt id="glossary-non-custodial" class="text-xl font-bold text-emerald-700">Non-custodial
                                (Wallet)</dt>
                            <dd class="mt-1 text-gray-700">Un tipo di <a href="#glossary-wallet"
                                    class="glossary-link">wallet</a> in cui solo l'utente ha il pieno controllo delle
                                chiavi private e, di conseguenza, dei fondi. La responsabilità della sicurezza è
                                interamente dell'utente.</dd>
                        </div>
                        <div>
                            <dt id="glossary-off-chain" class="text-xl font-bold text-emerald-700">Off-chain</dt>
                            <dd class="mt-1 text-gray-700">Si riferisce a qualsiasi operazione o dato che avviene al di
                                fuori della <a href="#glossary-blockchain" class="glossary-link">blockchain</a>, tramite
                                sistemi tradizionali (es. un bonifico gestito da un <a href="#glossary-psp"
                                    class="glossary-link">PSP</a>).</dd>
                        </div>
                        <div>
                            <dt id="glossary-on-chain" class="text-xl font-bold text-emerald-700">On-chain</dt>
                            <dd class="mt-1 text-gray-700">Si riferisce a qualsiasi operazione o dato registrato
                                direttamente sulla <a href="#glossary-blockchain" class="glossary-link">blockchain</a>,
                                rendendolo pubblicamente verificabile e immutabile.</dd>
                        </div>
                        <div>
                            <dt id="glossary-opt-in" class="text-xl font-bold text-emerald-700">Opt-in</dt>
                            <dd class="mt-1 text-gray-700">Un'azione richiesta su alcune <a href="#glossary-blockchain"
                                    class="glossary-link">blockchain</a> (come Algorand) in cui un <a
                                    href="#glossary-wallet" class="glossary-link">wallet</a> deve esplicitamente
                                "accettare" di poter ricevere un determinato tipo di token (<a href="#glossary-asa"
                                    class="glossary-link">ASA</a>) prima che questo possa essergli trasferito.</dd>
                        </div>
                        <div>
                            <dt id="glossary-partner-autorizzato" class="text-xl font-bold text-emerald-700">Partner
                                autorizzato (CASP/EMI)</dt>
                            <dd class="mt-1 text-gray-700">Un soggetto terzo che possiede le licenze necessarie (es.
                                Crypto-Asset Service Provider o Electronic Money Institution) per gestire pagamenti e
                                servizi legati alle criptovalute per conto del merchant.</dd>
                        </div>
                        <div>
                            <dt id="glossary-payout" class="text-xl font-bold text-emerald-700">Payout</dt>
                            <dd class="mt-1 text-gray-700">Il processo con cui un <a href="#glossary-psp"
                                    class="glossary-link">PSP</a> trasferisce i fondi incassati dalle vendite sul conto
                                bancario del merchant.</dd>
                        </div>
                        <div>
                            <dt id="glossary-psp" class="text-xl font-bold text-emerald-700">PSP (Payment Service
                                Provider)</dt>
                            <dd class="mt-1 text-gray-700">Un fornitore di servizi di pagamento regolamentato che
                                incassa pagamenti in valuta <a href="#glossary-fiat" class="glossary-link">FIAT</a>
                                (es. tramite carta di credito), gestisce bonifici, rimborsi e ripartizioni per conto dei
                                commercianti (merchant).</dd>
                        </div>
                        <div>
                            <dt id="glossary-royalties" class="text-xl font-bold text-emerald-700">Royalties</dt>
                            <dd class="mt-1 text-gray-700">Percentuali sul prezzo di vendita che vengono pagate al
                                creatore originale o ad altri beneficiari in occasione di vendite secondarie di un bene.
                            </dd>
                        </div>
                        <div>
                            <dt id="glossary-settlement" class="text-xl font-bold text-emerald-700">Settlement</dt>
                            <dd class="mt-1 text-gray-700">Il processo finale di trasferimento di fondi al merchant
                                dopo che una transazione è stata completata e confermata.</dd>
                        </div>
                        <div>
                            <dt id="glossary-transfer" class="text-xl font-bold text-emerald-700">Transfer</dt>
                            <dd class="mt-1 text-gray-700">L'azione di trasferire la proprietà di un token da un <a
                                    href="#glossary-wallet" class="glossary-link">wallet</a> a un altro.</dd>
                        </div>
                        <div>
                            <dt id="glossary-wallet" class="text-xl font-bold text-emerald-700">Wallet</dt>
                            <dd class="mt-1 text-gray-700">Un portafoglio digitale utilizzato per custodire e gestire
                                oggetti digitali basati su <a href="#glossary-blockchain"
                                    class="glossary-link">blockchain</a> (come <a href="#glossary-egi"
                                    class="glossary-link">EGI</a> o criptovalute).</dd>
                        </div>
                        <div>
                            <dt id="glossary-fee" class="text-xl font-bold text-emerald-700">Fee (Commissione)</dt>
                            <dd class="mt-1 text-gray-700">La commissione applicata dalla piattaforma FlorenceEGI per i
                                servizi forniti (minting, trading, trasferimenti). Viene fatturata separatamente e non
                                include i fondi destinati a Creator o EPP.</dd>
                        </div>
                        <div>
                            <dt id="glossary-compliance" class="text-xl font-bold text-emerald-700">Compliance</dt>
                            <dd class="mt-1 text-gray-700">L'insieme di attività necessarie per operare nel rispetto
                                delle normative fiscali, legali e regolamentari vigenti in un determinato contesto o
                                giurisdizione.</dd>
                        </div>
                        <div>
                            <dt id="glossary-fatturazione-elettronica" class="text-xl font-bold text-emerald-700">
                                Fatturazione Elettronica</dt>
                            <dd class="mt-1 text-gray-700">Sistema obbligatorio in Italia per l'emissione, trasmissione
                                e conservazione delle fatture in formato digitale tramite il Sistema di Interscambio
                                (SDI) dell'Agenzia delle Entrate.</dd>
                        </div>
                        <div>
                            <dt id="glossary-iva" class="text-xl font-bold text-emerald-700">IVA (Imposta sul Valore
                                Aggiunto)</dt>
                            <dd class="mt-1 text-gray-700">Imposta indiretta sui consumi applicata in Italia e
                                nell'Unione Europea. L'aliquota e le modalità di applicazione variano in base al tipo di
                                operazione e alla residenza fiscale delle parti coinvolte.</dd>
                        </div>
                        <div>
                            <dt id="glossary-oss" class="text-xl font-bold text-emerald-700">OSS (One Stop Shop)</dt>
                            <dd class="mt-1 text-gray-700">Sistema UE che consente alle aziende di dichiarare e versare
                                l'<a href="#glossary-iva" class="glossary-link">IVA</a> per servizi B2C cross-border
                                tramite un unico portale, semplificando la <a href="#glossary-compliance"
                                    class="glossary-link">compliance</a> internazionale.</dd>
                        </div>
                        <div>
                            <dt id="glossary-moss" class="text-xl font-bold text-emerald-700">MOSS (Mini One Stop
                                Shop)</dt>
                            <dd class="mt-1 text-gray-700">Precedente versione del regime <a href="#glossary-oss"
                                    class="glossary-link">OSS</a>, specifico per servizi di telecomunicazione,
                                broadcasting ed elettronici. Ora integrato nell'OSS.</dd>
                        </div>
                        <div>
                            <dt id="glossary-reverse-charge" class="text-xl font-bold text-emerald-700">Reverse Charge
                            </dt>
                            <dd class="mt-1 text-gray-700">Meccanismo <a href="#glossary-iva"
                                    class="glossary-link">IVA</a> in cui l'obbligo di versare l'imposta passa dal
                                fornitore al cliente (tipicamente applicato nelle transazioni B2B intra-UE con Partita
                                IVA).</dd>
                        </div>
                        <div>
                            <dt id="glossary-partita-iva" class="text-xl font-bold text-emerald-700">Partita IVA</dt>
                            <dd class="mt-1 text-gray-700">Numero identificativo attribuito a chi svolge attività
                                economica abituale in Italia. È obbligatoria per professionisti, imprese e attività
                                commerciali continuative.</dd>
                        </div>
                        <div>
                            <dt id="glossary-ricevuta-prestazione-occasionale"
                                class="text-xl font-bold text-emerald-700">Ricevuta per Prestazione Occasionale</dt>
                            <dd class="mt-1 text-gray-700">Documento fiscale emesso da un privato (senza <a
                                    href="#glossary-partita-iva" class="glossary-link">Partita IVA</a>) per attività
                                non abituali. L'importo va dichiarato come "reddito diverso" nella dichiarazione dei
                                redditi.</dd>
                        </div>
                        <div>
                            <dt id="glossary-ets" class="text-xl font-bold text-emerald-700">ETS (Ente del Terzo
                                Settore)</dt>
                            <dd class="mt-1 text-gray-700">Organizzazioni non profit riconosciute dal Codice del Terzo
                                Settore italiano (es. associazioni, fondazioni, ONLUS). Godono di agevolazioni fiscali e
                                non devono emettere fattura per donazioni.</dd>
                        </div>
                        <div>
                            <dt id="glossary-onlus" class="text-xl font-bold text-emerald-700">ONLUS (Organizzazione
                                Non Lucrativa di Utilità Sociale)</dt>
                            <dd class="mt-1 text-gray-700">Ente senza scopo di lucro riconosciuto in Italia, ora
                                confluito nel regime <a href="#glossary-ets" class="glossary-link">ETS</a>. Le
                                donazioni ricevute beneficiano di agevolazioni fiscali per i donatori.</dd>
                        </div>
                        <div>
                            <dt id="glossary-epp" class="text-xl font-bold text-emerald-700">EPP (Environmental
                                Protection Project)</dt>
                            <dd class="mt-1 text-gray-700">Progetti di tutela ambientale collegati agli <a
                                    href="#glossary-egi" class="glossary-link">EGI</a> su FlorenceEGI. Gli EPP
                                ricevono donazioni dirette sul proprio <a href="#glossary-wallet"
                                    class="glossary-link">wallet</a> da ogni transazione di EGI associato.</dd>
                        </div>
                        <div>
                            <dt id="glossary-creator" class="text-xl font-bold text-emerald-700">Creator</dt>
                            <dd class="mt-1 text-gray-700">L'autore o il proprietario originale di un bene che crea un
                                <a href="#glossary-egi" class="glossary-link">EGI</a> sulla piattaforma. Il Creator
                                riceve i proventi delle vendite e può impostare <a href="#glossary-royalties"
                                    class="glossary-link">royalties</a> per vendite future.
                            </dd>
                        </div>
                        <div>
                            <dt id="glossary-mecenate" class="text-xl font-bold text-emerald-700">Mecenate</dt>
                            <dd class="mt-1 text-gray-700">Soggetto che sostiene finanziariamente un <a
                                    href="#glossary-creator" class="glossary-link">Creator</a> o un <a
                                    href="#glossary-epp" class="glossary-link">EPP</a> attraverso acquisto di <a
                                    href="#glossary-egi" class="glossary-link">EGI</a> o donazioni dirette sulla
                                piattaforma FlorenceEGI.</dd>
                        </div>
                        <div>
                            <dt id="glossary-trader" class="text-xl font-bold text-emerald-700">Trader</dt>
                            <dd class="mt-1 text-gray-700">Utente che acquista e rivende <a href="#glossary-egi"
                                    class="glossary-link">EGI</a> sul mercato secondario della piattaforma con
                                l'obiettivo di realizzare plusvalenze. È responsabile della dichiarazione fiscale dei
                                propri guadagni.</dd>
                        </div>
                        <div>
                            <dt id="glossary-plusvalenza" class="text-xl font-bold text-emerald-700">Plusvalenza</dt>
                            <dd class="mt-1 text-gray-700">Guadagno realizzato dalla vendita di un bene a un prezzo
                                superiore a quello di acquisto. Nel contesto fiscale italiano, le plusvalenze possono
                                essere soggette a tassazione come "redditi diversi" o "redditi da capitale".</dd>
                        </div>
                        <div>
                            <dt id="glossary-merchant" class="text-xl font-bold text-emerald-700">Merchant</dt>
                            <dd class="mt-1 text-gray-700">Termine generico per indicare il venditore o fornitore di
                                servizi che utilizza la piattaforma per vendere beni o emettere <a href="#glossary-egi"
                                    class="glossary-link">EGI</a>. Nel contesto FlorenceEGI, tipicamente un <a
                                    href="#glossary-creator" class="glossary-link">Creator</a>.</dd>
                        </div>
                        <div>
                            <dt id="glossary-casp" class="text-xl font-bold text-emerald-700">CASP (Crypto-Asset
                                Service Provider)</dt>
                            <dd class="mt-1 text-gray-700">Fornitore di servizi su cripto-attività regolamentato dal
                                regolamento europeo MiCA. Include exchange, custodian e altri intermediari autorizzati a
                                gestire criptovalute per conto terzi.</dd>
                        </div>
                        <div>
                            <dt id="glossary-emi" class="text-xl font-bold text-emerald-700">EMI (Electronic Money
                                Institution)</dt>
                            <dd class="mt-1 text-gray-700">Istituto di moneta elettronica autorizzato a emettere e
                                gestire moneta elettronica e fornire servizi di pagamento nell'Unione Europea.</dd>
                        </div>
                        <div>
                            <dt id="glossary-mica" class="text-xl font-bold text-emerald-700">MiCA (Markets in
                                Crypto-Assets Regulation)</dt>
                            <dd class="mt-1 text-gray-700">Regolamento europeo che disciplina i mercati di
                                cripto-attività, definendo regole chiare per l'emissione, l'offerta e l'ammissione al
                                trading di crypto-asset, nonché per i fornitori di servizi correlati.</dd>
                        </div>
                        <div>
                            <dt id="glossary-sostituto-imposta" class="text-xl font-bold text-emerald-700">Sostituto
                                d'Imposta</dt>
                            <dd class="mt-1 text-gray-700">Soggetto che, per legge, trattiene e versa le imposte dovute
                                da un altro contribuente (es. datore di lavoro che trattiene IRPEF sullo stipendio).
                                FlorenceEGI NON è sostituto d'imposta.</dd>
                        </div>
                        <div>
                            <dt id="glossary-fatturazione-batch" class="text-xl font-bold text-emerald-700">
                                Fatturazione Batch (Cumulativa)</dt>
                            <dd class="mt-1 text-gray-700">Sistema di fatturazione che raggruppa più operazioni in un
                                unico documento fiscale periodico (es. mensile), con allegato il dettaglio delle singole
                                transazioni. Utilizzato per gestire alto volume di micro-transazioni.</dd>
                        </div>
                        <div>
                            <dt id="glossary-dashboard" class="text-xl font-bold text-emerald-700">Dashboard</dt>
                            <dd class="mt-1 text-gray-700">Pannello di controllo sulla piattaforma FlorenceEGI che
                                fornisce a ogni utente una visione d'insieme delle proprie attività, incassi, <a
                                    href="#glossary-egi" class="glossary-link">EGI</a> posseduti, report fiscali e
                                strumenti di gestione.</dd>
                        </div>
                        <div>
                            <dt id="glossary-qr-code" class="text-xl font-bold text-emerald-700">QR Code (Verifica
                                Pubblica)</dt>
                            <dd class="mt-1 text-gray-700">Codice QR associato a ogni <a href="#glossary-egi"
                                    class="glossary-link">EGI</a> che permette la verifica pubblica e immediata
                                dell'autenticità del certificato digitale tramite scansione con smartphone, senza
                                necessità di autenticazione.</dd>
                        </div>
                        <div>
                            <dt id="glossary-erp" class="text-xl font-bold text-emerald-700">ERP (Enterprise Resource
                                Planning)</dt>
                            <dd class="mt-1 text-gray-700">Sistema software gestionale integrato utilizzato da aziende
                                ed enti per gestire processi aziendali, contabilità, risorse umane, supply chain e altre
                                attività operative.</dd>
                        </div>
                        <div>
                            <dt id="glossary-crm" class="text-xl font-bold text-emerald-700">CRM (Customer
                                Relationship Management)</dt>
                            <dd class="mt-1 text-gray-700">Sistema software per la gestione delle relazioni con i
                                clienti, utilizzato per tracciare interazioni, vendite, supporto e marketing. Utilizzato
                                da grandi enti per integrare dati da FlorenceEGI.</dd>
                        </div>
                        <div>
                            <dt id="glossary-sdi" class="text-xl font-bold text-emerald-700">SDI (Sistema di
                                Interscambio)</dt>
                            <dd class="mt-1 text-gray-700">Sistema dell'Agenzia delle Entrate italiana che gestisce la
                                ricezione, controllo e trasmissione delle fatture elettroniche tra emittenti e
                                destinatari, garantendo la <a href="#glossary-compliance"
                                    class="glossary-link">compliance</a> fiscale.</dd>
                        </div>
                        <div>
                            <dt id="glossary-export-csv" class="text-xl font-bold text-emerald-700">Export CSV/XML
                            </dt>
                            <dd class="mt-1 text-gray-700">Funzionalità della piattaforma che permette di scaricare i
                                dati delle proprie transazioni in formato CSV (tabella) o XML (strutturato), per
                                l'integrazione con software di contabilità esterni.</dd>
                        </div>
                        <div>
                            <dt id="glossary-alert-fiscale" class="text-xl font-bold text-emerald-700">Alert Fiscale
                            </dt>
                            <dd class="mt-1 text-gray-700">Notifica automatica inviata dalla piattaforma quando si
                                raggiungono soglie fiscali rilevanti (es. limite per prestazione occasionale, volume di
                                trading significativo) per ricordare all'utente i propri obblighi di <a
                                    href="#glossary-compliance" class="glossary-link">compliance</a>.</dd>
                        </div>
                        <div>
                            <dt id="glossary-ricevuta-donazione" class="text-xl font-bold text-emerald-700">Ricevuta
                                di Donazione</dt>
                            <dd class="mt-1 text-gray-700">Documento rilasciato da un ente (<a href="#glossary-ets"
                                    class="glossary-link">ETS</a>, <a href="#glossary-onlus"
                                    class="glossary-link">ONLUS</a>) che attesta la donazione ricevuta, permettendo al
                                donatore di usufruire di detrazioni o deduzioni fiscali secondo la normativa vigente.
                            </dd>
                        </div>
                        <div>
                            <dt id="glossary-audit-trail" class="text-xl font-bold text-emerald-700">Audit Trail</dt>
                            <dd class="mt-1 text-gray-700">Registrazione cronologica completa e immutabile di tutte le
                                operazioni effettuate, utilizzata per verifiche fiscali, controlli di <a
                                    href="#glossary-compliance" class="glossary-link">compliance</a> e tracciabilità.
                                Su FlorenceEGI garantita dalla <a href="#glossary-blockchain"
                                    class="glossary-link">blockchain</a>.</dd>
                        </div>
                        <div>
                            <dt id="glossary-tracciabilita" class="text-xl font-bold text-emerald-700">Tracciabilità
                            </dt>
                            <dd class="mt-1 text-gray-700">Capacità di ricostruire la storia completa di un <a
                                    href="#glossary-egi" class="glossary-link">EGI</a> (proprietari, transazioni,
                                donazioni) grazie alla registrazione <a href="#glossary-on-chain"
                                    class="glossary-link">on-chain</a> e agli <a href="#glossary-audit-trail"
                                    class="glossary-link">audit trail</a> della piattaforma.</dd>
                        </div>
                        <div>
                            <dt id="glossary-algoritmo" class="text-xl font-bold text-emerald-700">Algorand</dt>
                            <dd class="mt-1 text-gray-700"><a href="#glossary-blockchain"
                                    class="glossary-link">Blockchain</a> di tipo "proof-of-stake pura" ad alte
                                prestazioni, basso costo e impatto ambientale ridotto, utilizzata da FlorenceEGI per la
                                creazione e gestione degli <a href="#glossary-egi" class="glossary-link">EGI</a> come
                                <a href="#glossary-asa" class="glossary-link">ASA</a>.
                            </dd>
                        </div>
                        <div>
                            <dt id="glossary-hash" class="text-xl font-bold text-emerald-700">Hash (Impronta Digitale)
                            </dt>
                            <dd class="mt-1 text-gray-700">Sequenza alfanumerica unica generata da un algoritmo
                                crittografico a partire da un file o documento. Qualsiasi modifica al contenuto produce
                                un hash completamente diverso, garantendo integrità e verificabilità.</dd>
                        </div>
                        <div>
                            <dt id="glossary-chiave-privata" class="text-xl font-bold text-emerald-700">Chiave Privata
                            </dt>
                            <dd class="mt-1 text-gray-700">Codice segreto crittografico che permette di controllare e
                                autorizzare operazioni su un <a href="#glossary-wallet"
                                    class="glossary-link">wallet</a> blockchain. Chi possiede la chiave privata ha
                                pieno controllo dei fondi. Non va MAI condivisa.</dd>
                        </div>
                        <div>
                            <dt id="glossary-token" class="text-xl font-bold text-emerald-700">Token</dt>
                            <dd class="mt-1 text-gray-700">Unità digitale rappresentante un asset, un diritto o un
                                valore su una <a href="#glossary-blockchain" class="glossary-link">blockchain</a>. Nel
                                contesto FlorenceEGI, l'<a href="#glossary-egi" class="glossary-link">EGI</a> è un
                                token di tipo <a href="#glossary-asa" class="glossary-link">ASA</a>.</dd>
                        </div>
                        <div>
                            <dt id="glossary-immutabilita" class="text-xl font-bold text-emerald-700">Immutabilità
                            </dt>
                            <dd class="mt-1 text-gray-700">Caratteristica fondamentale della <a
                                    href="#glossary-blockchain" class="glossary-link">blockchain</a>: una volta
                                registrata, un'informazione non può essere modificata o cancellata, garantendo la
                                veridicità storica dei dati.</dd>
                        </div>
                        <div>
                            <dt id="glossary-report" class="text-xl font-bold text-emerald-700">Report Fiscale</dt>
                            <dd class="mt-1 text-gray-700">Documento riepilogativo generato automaticamente dalla
                                piattaforma che sintetizza tutte le transazioni, incassi, <a href="#glossary-fee"
                                    class="glossary-link">fee</a> e donazioni di un utente in un determinato periodo,
                                facilitando la dichiarazione fiscale.</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </main>

        <footer class="mt-16 text-white bg-gray-900">
            <div class="container px-6 py-12 mx-auto max-w-7xl">
                <p class="text-center text-gray-500">&copy; 2025 Florence EGI | Tutti i diritti riservati.</p>
            </div>
        </footer>

        <!-- Pulsante Torna al Testo -->
        <button id="backToTextButton" aria-label="Torna al testo">
            <span class="material-icons" style="font-size: 20px;">arrow_back</span>
            <span id="backToTextLabel">Torna al testo</span>
        </button>

    </div>

    <script>
        const contentData = {
            panoramica: {
                title: 'Panoramica Generale',
                nav: 'Panoramica',
                intro: 'Questa sezione offre una visione d\'insieme dei principi fondamentali che guidano la compliance fiscale sulla piattaforma FlorenceEGI. Qui puoi comprendere la filosofia di trasparenza, la divisione delle responsabilità e il funzionamento generale del sistema.',
                content: '<div class="grid items-center gap-8 md:grid-cols-2">' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-emerald-700">Principi Guida</h3>' +
                    '<div class="space-y-3">' +
                    '<div class="p-4 rounded-lg bg-emerald-50"><h4 class="font-semibold text-emerald-800">Trasparenza Radicale</h4><p class="text-sm text-gray-600">Ogni flusso economico è <a href="#glossary-tracciabilita" class="glossary-link">tracciabile</a>, ricostruibile e accessibile per garantire massima chiarezza grazie alla <a href="#glossary-blockchain" class="glossary-link">blockchain</a>.</p></div>' +
                    '<div class="p-4 rounded-lg bg-emerald-50"><h4 class="font-semibold text-emerald-800">Automazione Intelligente</h4><p class="text-sm text-gray-600">Generazione automatica di <a href="#glossary-report" class="glossary-link">report</a>, ricevute e <a href="#glossary-fatturazione-elettronica" class="glossary-link">fatture elettroniche</a> per ridurre il rischio di errore umano.</p></div>' +
                    '<div class="p-4 rounded-lg bg-emerald-50"><h4 class="font-semibold text-emerald-800">Responsabilità Chiara</h4><p class="text-sm text-gray-600">Ogni attore conosce esattamente i propri obblighi. La piattaforma non agisce mai da <a href="#glossary-sostituto-imposta" class="glossary-link">sostituto d\'imposta</a>.</p></div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-center text-emerald-700">Ripartizione delle Responsabilità</h3>' +
                    '<p class="mb-4 text-center text-gray-600">Il grafico illustra come la responsabilità fiscale sia principalmente in capo all\'utente, mentre la piattaforma fornisce gli strumenti per la <a href="#glossary-compliance" class="glossary-link">compliance</a>.</p>' +
                    '<div class="chart-container"><canvas id="responsibilityChart"></canvas></div>' +
                    '</div>' +
                    '</div>'
            },
            pagamenti: {
                title: 'Gestione Pagamenti & Blockchain',
                nav: 'Gestione Pagamenti',
                intro: 'Un approccio inclusivo in tre livelli per unire pagamenti tradizionali e certificazione digitale, in totale sicurezza e conformità normativa.',
                content: '<div class="p-6 mb-8 border-l-4 rounded-r-lg bg-emerald-50 border-emerald-500 text-emerald-800">' +
                    '<h4 class="mb-2 text-xl font-bold">Filosofia: Inclusione Progressiva</h4>' +
                    '<p class="text-gray-700">Il nostro sistema è progettato per tutti. Chi non conosce il mondo crypto usa la moneta <a href="#glossary-fiat" class="glossary-link">FIAT</a> e i metodi di pagamento tradizionali. Chi possiede un <a href="#glossary-wallet" class="glossary-link">wallet</a> può ricevere il certificato digitale <a href="#glossary-egi" class="glossary-link">EGI</a> direttamente lì. Chi vuole accettare pagamenti in criptovalute può farlo tramite <a href="#glossary-partner-autorizzato" class="glossary-link">partner esterni autorizzati</a>, senza imporre alcuna complessità agli altri utenti.</p>' +
                    '</div>' +
                    '<div class="space-y-8">' +
                    '<div class="p-6 border rounded-lg bg-gray-50"><h3 class="mb-4 text-2xl font-bold text-gray-800">Livello 1 — Nessun wallet (100% tradizionale)</h3><p class="mb-6 text-gray-600">L\'esperienza d\'uso è identica a un normale e-commerce. Zero cripto, zero complessità.</p><div class="grid gap-6 md:grid-cols-2"><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Cliente</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Paga in euro (<a href="#glossary-fiat" class="glossary-link">FIAT</a>) su pagina sicura del <a href="#glossary-psp" class="glossary-link">PSP</a>.</li><li>Riceve l\'<a href="#glossary-egi" class="glossary-link">EGI</a>: la piattaforma esegue <a href="#glossary-mint" class="glossary-link">mint</a> e <a href="#glossary-transfer" class="glossary-link">transfer</a> e salva l\'<a href="#glossary-anchor-hash" class="glossary-link">anchor hash</a>.</li><li>Verifica pubblica con QR.</li></ul></div><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Merchant</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Riceve denaro in <a href="#glossary-fiat" class="glossary-link">FIAT</a> dal <a href="#glossary-psp" class="glossary-link">PSP</a> (<a href="#glossary-payout" class="glossary-link">payout</a>).</li><li>Vede l\'<a href="#glossary-egi" class="glossary-link">EGI</a> emesso e i report.</li><li><a href="#glossary-royalties" class="glossary-link">Royalties</a> e ripartizioni sono gestite dal <a href="#glossary-psp" class="glossary-link">PSP</a> (<a href="#glossary-off-chain" class="glossary-link">off-chain</a>).</li></ul></div></div></div>' +
                    '<div class="p-6 border rounded-lg bg-gray-50"><h3 class="mb-4 text-2xl font-bold text-gray-800">Livello 2 — Ho un wallet, pago in FIAT</h3><p class="mb-6 text-gray-600">Gli utenti più esperti possono usare il proprio wallet per ricevere il certificato, senza imporre la cripto come pagamento.</p><div class="grid gap-6 md:grid-cols-2"><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Cliente</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Paga sempre in <a href="#glossary-fiat" class="glossary-link">FIAT</a>.</li><li>Sceglie dove ricevere l\'<a href="#glossary-egi" class="glossary-link">EGI</a>: nel <a href="#glossary-wallet" class="glossary-link">wallet</a> personale (<a href="#glossary-non-custodial" class="glossary-link">non-custodial</a>) o in uno <a href="#glossary-custodial" class="glossary-link">custodial</a> della piattaforma.</li></ul></div><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Merchant</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Incassa sempre in <a href="#glossary-fiat" class="glossary-link">FIAT</a>.</li><li>L\'<a href="#glossary-egi" class="glossary-link">EGI</a> viene trasferito con tracciabilità <a href="#glossary-on-chain" class="glossary-link">on-chain</a>.</li></ul></div></div></div>' +
                    '<div class="p-6 border rounded-lg bg-gray-50"><h3 class="mb-4 text-2xl font-bold text-gray-800">Livello 3 — Accetto pagamenti Crypto (opzionale)</h3><p class="mb-6 text-gray-600">Questo livello è facoltativo e gestito da partner esterni per mantenere la piattaforma <a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>.</p><div class="grid gap-6 md:grid-cols-2"><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Merchant</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Si affida a un <a href="#glossary-partner-autorizzato" class="glossary-link">Partner autorizzato (CASP/EMI)</a>.</li><li>I clienti pagano sul checkout del Partner.</li><li>Il <a href="#glossary-settlement" class="glossary-link">settlement</a> è gestito dal Partner.</li></ul></div><div><h4 class="mb-2 text-lg font-semibold text-emerald-800">Per il Cliente</h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Paga in crypto sul checkout del Partner.</li><li>Riceve l\'<a href="#glossary-egi" class="glossary-link">EGI</a> come sempre.</li></ul></div></div></div>' +
                    '</div>' +
                    '<div class="mt-8">' +
                    '<h3 class="mb-4 text-2xl font-bold text-center text-gray-800">Cosa fa (e non fa) la piattaforma</h3>' +
                    '<div class="grid max-w-4xl gap-6 mx-auto md:grid-cols-2">' +
                    '<div class="p-4 border-l-4 border-green-600 rounded-lg bg-green-50"><h4 class="mb-2 text-lg font-bold text-green-800">Cosa Fa</h4><ul class="space-y-1 text-gray-700 list-disc list-inside"><li>Incassa <a href="#glossary-fiat" class="glossary-link">FIAT</a> tramite <a href="#glossary-psp" class="glossary-link">PSP</a>.</li><li>Emette e trasferisce <a href="#glossary-egi" class="glossary-link">EGI</a>.</li><li>Scrive <a href="#glossary-anchor-hash" class="glossary-link">anchor hash</a>.</li><li>Gestisce QR e verifica pubblica.</li><li>Calcola <a href="#glossary-royalties" class="glossary-link">royalties</a> per il <a href="#glossary-psp" class="glossary-link">PSP</a>.</li></ul></div>' +
                    '<div class="p-4 border-l-4 border-red-600 rounded-lg bg-red-50"><h4 class="mb-2 text-lg font-bold text-red-800">Cosa NON Fa</h4><ul class="space-y-1 text-gray-700 list-disc list-inside"><li>Custodire criptovalute per terzi.</li><li>Fare da exchange crypto/<a href="#glossary-fiat" class="glossary-link">fiat</a>.</li><li>Processare pagamenti crypto.</li></ul></div>' +
                    '</div>' +
                    '</div>'
            },
            piattaforma: {
                title: 'Ruolo della Piattaforma',
                nav: 'Piattaforma',
                intro: 'In questa sezione viene definito il ruolo di FlorenceEGI: un facilitatore tecnologico che non gestisce fondi per conto terzi. Vengono illustrati gli obblighi fiscali specifici della piattaforma, la gestione delle proprie commissioni (fee) e come fornisce strumenti di supporto agli utenti senza mai sostituirsi a loro nelle responsabilità fiscali.',
                content: '<h3 class="mb-4 text-2xl font-bold text-emerald-700">Obblighi Fiscali di FlorenceEGI</h3>' +
                    '<div class="grid gap-8 md:grid-cols-2">' +
                    '<div class="p-6 rounded-lg bg-gray-50"><h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione <a href="#glossary-fee" class="glossary-link">Fee</a></h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Incassa **esclusivamente** la propria <a href="#glossary-fee" class="glossary-link">fee</a> di servizio.</li><li>I fondi non vengono mai trattenuti per conto di terzi (<a href="#glossary-mica-safe" class="glossary-link">MiCA-safe</a>).</li><li>Le <a href="#glossary-fee" class="glossary-link">fee</a> vengono accreditate direttamente sul <a href="#glossary-wallet" class="glossary-link">wallet</a> della piattaforma.</li></ul></div>' +
                    '<div class="p-6 rounded-lg bg-gray-50"><h4 class="mb-3 text-lg font-semibold text-gray-800">Fatturazione e <a href="#glossary-iva" class="glossary-link">IVA</a></h4><ul class="space-y-2 text-gray-700 list-disc list-inside"><li>Emette <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura elettronica</a> per ogni <a href="#glossary-fee" class="glossary-link">fee</a> incassata tramite <a href="#glossary-sdi" class="glossary-link">SDI</a>.</li><li>Adotta <a href="#glossary-fatturazione-batch" class="glossary-link">fatturazione cumulativa (batch)</a> per operazioni ad alto volume.</li><li>Gestisce l\'<a href="#glossary-iva" class="glossary-link">IVA</a> secondo le normative nazionali e internazionali (<a href="#glossary-oss" class="glossary-link">OSS</a>/<a href="#glossary-moss" class="glossary-link">MOSS</a>).</li></ul></div>' +
                    '</div>' +
                    '<div class="mt-8">' +
                    '<h4 class="mb-4 text-xl font-bold text-center text-emerald-700">Flusso Finanziario della Piattaforma</h4>' +
                    '<div class="flex flex-col items-center justify-center gap-4 text-center md:flex-row md:flex-wrap">' +
                    '<div class="p-4 bg-blue-100 rounded-lg shadow-sm"><p class="font-semibold">Transazione Utente</p><p class="text-sm">(es. <a href="#glossary-mint" class="glossary-link">Minting</a>, Trading)</p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 rounded-lg shadow-sm bg-emerald-100"><p class="font-semibold">Separazione <a href="#glossary-fee" class="glossary-link">Fee</a></p><p class="text-sm">La <a href="#glossary-fee" class="glossary-link">fee</a> viene separata</p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 bg-green-100 rounded-lg shadow-sm"><p class="font-semibold"><a href="#glossary-wallet" class="glossary-link">Wallet</a> FlorenceEGI</p><p class="text-sm">La <a href="#glossary-fee" class="glossary-link">fee</a> è incassata</p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 bg-yellow-100 rounded-lg shadow-sm"><p class="font-semibold">Fatturazione</p><p class="text-sm">Emissione <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura</a> all\'utente</p></div>' +
                    '</div>' +
                    '</div>'
            },
            creator: {
                title: 'Gestione Fiscale per Creator e Mecenati',
                nav: 'Creator / Mecenate',
                intro: 'Se sei un Creator o un Mecenate, questa sezione è per te. Qui troverai una guida chiara su come vengono gestiti i tuoi incassi, quali sono i tuoi obblighi fiscali a seconda che tu sia un privato o una Partita IVA, e quali strumenti di automazione e reportistica FlorenceEGI mette a tua disposizione per semplificare la tua compliance.',
                content: '<div class="grid gap-8 md:grid-cols-2">' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-emerald-700">I Tuoi Obblighi Fiscali</h3>' +
                    '<div class="space-y-4">' +
                    '<div class="p-4 rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Se sei un Privato</h4><p class="mt-2 text-gray-700">Per vendite occasionali, devi emettere una <a href="#glossary-ricevuta-prestazione-occasionale" class="glossary-link">ricevuta per prestazione occasionale</a> e dichiarare il reddito come "reddito diverso". Se l\'attività diventa abituale, è obbligatorio aprire <a href="#glossary-partita-iva" class="glossary-link">Partita IVA</a>.</p></div>' +
                    '<div class="p-4 rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Se hai <a href="#glossary-partita-iva" class="glossary-link">Partita IVA</a></h4><p class="mt-2 text-gray-700">È obbligatoria la <a href="#glossary-fatturazione-elettronica" class="glossary-link">fatturazione elettronica</a> per ogni incasso ricevuto, applicando il tuo regime fiscale e l\'<a href="#glossary-iva" class="glossary-link">IVA</a>, se prevista.</p></div>' +
                    '</div>' +
                    '<h3 class="mt-6 mb-4 text-2xl font-bold text-emerald-700">Strumenti a Tua Disposizione</h3>' +
                    '<ul class="space-y-2 text-gray-700 list-disc list-inside">' +
                    '<li><a href="#glossary-dashboard" class="glossary-link">Dashboard</a> con <a href="#glossary-report" class="glossary-link">report</a> dettagliato delle vendite.</li>' +
                    '<li><a href="#glossary-export-csv" class="glossary-link">Esportazione dati in CSV/XML</a> per la tua contabilità.</li>' +
                    '<li><a href="#glossary-alert-fiscale" class="glossary-link">Alert automatici</a> al raggiungimento di soglie fiscali.</li>' +
                    '<li>Modelli di ricevuta/fattura scaricabili.</li>' +
                    '</ul>' +
                    '</div>' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-center text-emerald-700">Flusso di Incasso per <a href="#glossary-creator" class="glossary-link">Creator</a></h3>' +
                    '<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">' +
                    '<div class="flex flex-col items-center space-y-4 text-center">' +
                    '<div class="w-full p-4 bg-blue-100 rounded-lg"><p class="font-semibold">1. Vendita <a href="#glossary-egi" class="glossary-link">EGI</a></p><p class="text-sm">Un utente acquista il tuo <a href="#glossary-egi" class="glossary-link">EGI</a>.</p></div>' +
                    '<div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>' +
                    '<div class="w-full p-4 rounded-lg bg-emerald-100"><p class="font-semibold">2. Accredito Diretto</p><p class="text-sm">L\'importo (al netto della <a href="#glossary-fee" class="glossary-link">fee</a> di piattaforma) viene inviato istantaneamente.</p></div>' +
                    '<div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>' +
                    '<div class="w-full p-4 bg-green-100 rounded-lg"><p class="font-semibold">3. Il Tuo <a href="#glossary-wallet" class="glossary-link">Wallet</a></p><p class="text-sm">Ricevi i fondi direttamente sul tuo <a href="#glossary-wallet" class="glossary-link">wallet</a>, senza intermediari.</p></div>' +
                    '<div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>' +
                    '<div class="w-full p-4 bg-yellow-100 rounded-lg"><p class="font-semibold">4. <a href="#glossary-compliance" class="glossary-link">Compliance</a> Fiscale</p><p class="text-sm">Emetti fattura/ricevuta e dichiari il reddito.</p></div>' +
                    '</div>' +
                    '<p class="mt-4 text-sm text-center text-gray-600"><strong>Ricorda:</strong> Sei l\'unico responsabile della tua dichiarazione fiscale. FlorenceEGI non è un <a href="#glossary-sostituto-imposta" class="glossary-link">sostituto d\'imposta</a>.</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
            },
            epp: {
                title: 'Gestione Fiscale per EPP',
                nav: 'Ente / EPP',
                intro: 'Questa area è dedicata agli EPP (Environmental Protection Project). Spiega come gli enti, sia piccoli no profit che grandi organizzazioni, ricevono i fondi, quali sono i loro obblighi specifici riguardo le donazioni (con un focus sull\'emissione delle ricevute) e come la piattaforma facilita la gestione e la reportistica senza intervenire nei flussi finanziari.',
                content: '<div class="grid gap-8 md:grid-cols-2">' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione Donazioni</h3>' +
                    '<div class="space-y-4">' +
                    '<div class="p-4 rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Piccoli Enti No Profit (<a href="#glossary-ets" class="glossary-link">ETS</a>/<a href="#glossary-onlus" class="glossary-link">ONLUS</a>)</h4><p class="mt-2 text-gray-700">Non devono emettere <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura elettronica</a> per le donazioni. Devono rilasciare una <a href="#glossary-ricevuta-donazione" class="glossary-link">ricevuta di donazione</a> (anche cumulativa annuale) solo su richiesta del donatore. I fondi sono ricevuti direttamente sul <a href="#glossary-wallet" class="glossary-link">wallet</a> dell\'ente.</p></div>' +
                    '<div class="p-4 rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Grandi Enti e Aziende</h4><p class="mt-2 text-gray-700">Gestiscono la <a href="#glossary-compliance" class="glossary-link">compliance</a> internamente tramite i propri sistemi contabili (<a href="#glossary-erp" class="glossary-link">ERP</a>, <a href="#glossary-crm" class="glossary-link">CRM</a>). FlorenceEGI fornisce <a href="#glossary-report" class="glossary-link">report</a> ed <a href="#glossary-export-csv" class="glossary-link">export</a> dei dati per facilitare l\'integrazione, ma la responsabilità della documentazione fiscale resta dell\'ente.</p></div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione Ricevute di Donazione</h3>' +
                    '<ul class="p-4 space-y-3 text-gray-700 list-disc list-inside rounded-lg bg-gray-50">' +
                    '<li>L\'emissione è obbligatoria <strong>solo su richiesta</strong> del donatore (<a href="#glossary-mecenate" class="glossary-link">mecenate</a>).</li>' +
                    '<li>Si raccomanda l\'emissione di una <a href="#glossary-ricevuta-donazione" class="glossary-link">ricevuta cumulativa</a> (annuale o mensile) per semplificare la gestione.</li>' +
                    '<li>L\'<a href="#glossary-epp" class="glossary-link">EPP</a> può abilitare la <strong>generazione automatica</strong> delle ricevute tramite la piattaforma.</li>' +
                    '<li>Il donatore può scaricare la ricevuta dalla propria <a href="#glossary-dashboard" class="glossary-link">dashboard</a>.</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mt-8">' +
                    '<h4 class="mb-4 text-xl font-bold text-center text-emerald-700">Flusso di Donazione per <a href="#glossary-epp" class="glossary-link">EPP</a></h4>' +
                    '<div class="flex flex-col items-center justify-center gap-4 text-center md:flex-row md:flex-wrap">' +
                    '<div class="p-4 bg-blue-100 rounded-lg shadow-sm"><p class="font-semibold">Donazione Utente</p><p class="text-sm">Tramite acquisto <a href="#glossary-egi" class="glossary-link">EGI</a></p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 rounded-lg shadow-sm bg-emerald-100"><p class="font-semibold">Accredito Diretto</p><p class="text-sm">La quota di donazione è inviata</p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 bg-green-100 rounded-lg shadow-sm"><p class="font-semibold"><a href="#glossary-wallet" class="glossary-link">Wallet</a> <a href="#glossary-epp" class="glossary-link">EPP</a></p><p class="text-sm">L\'ente riceve i fondi</p></div>' +
                    '<div class="text-2xl font-bold rotate-90 text-emerald-600 md:rotate-0">&#10230;</div>' +
                    '<div class="p-4 bg-yellow-100 rounded-lg shadow-sm"><p class="font-semibold"><a href="#glossary-ricevuta-donazione" class="glossary-link">Ricevuta</a> (su richiesta)</p><p class="text-sm">L\'ente emette la ricevuta</p></div>' +
                    '</div>' +
                    '</div>'
            },
            trader: {
                title: 'Gestione Fiscale per Trader e Alto Flusso',
                nav: 'Trader Pro',
                intro: 'Il trading ad alto volume presenta sfide fiscali uniche. Questa sezione spiega come FlorenceEGI affronta questa complessità attraverso l\'automazione, la fatturazione cumulativa per le fee di piattaforma e la gestione semplificata delle micro-donazioni agli EPP, garantendo la tracciabilità senza ostacolare l\'operatività.',
                content: '<h3 class="mb-4 text-2xl font-bold text-emerald-700"><a href="#glossary-compliance" class="glossary-link">Compliance</a> per il Trading <a href="#glossary-egi" class="glossary-link">EGI</a></h3>' +
                    '<div class="grid gap-8 md:grid-cols-2">' +
                    '<div class="p-6 rounded-lg bg-gray-50">' +
                    '<h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione <a href="#glossary-fee" class="glossary-link">Fee</a> di Piattaforma</h4>' +
                    '<p class="mb-3 text-gray-700">Per gestire l\'alto numero di micro-transazioni, la piattaforma adotta un sistema di <a href="#glossary-fatturazione-batch" class="glossary-link">fatturazione cumulativa batch</a>.</p>' +
                    '<ul class="space-y-2 text-gray-700 list-disc list-inside">' +
                    '<li>Riceverai <strong>una sola <a href="#glossary-fatturazione-elettronica" class="glossary-link">fattura elettronica</a> periodica</strong> (es. mensile) per tutte le <a href="#glossary-fee" class="glossary-link">fee</a> maturate.</li>' +
                    '<li>Alla fattura sarà allegato un <a href="#glossary-report" class="glossary-link">report</a> con il dettaglio di ogni singola transazione.</li>' +
                    '</ul>' +
                    '</div>' +
                    '<div class="p-6 rounded-lg bg-gray-50">' +
                    '<h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione Donazioni agli <a href="#glossary-epp" class="glossary-link">EPP</a></h4>' +
                    '<p class="mb-3 text-gray-700">Anche le micro-donazioni derivanti da migliaia di trade sono gestite in modo semplice.</p>' +
                    '<ul class="space-y-2 text-gray-700 list-disc list-inside">' +
                    '<li>Puoi richiedere una <a href="#glossary-ricevuta-donazione" class="glossary-link">ricevuta di donazione cumulativa</a> (annuale/mensile) all\'<a href="#glossary-epp" class="glossary-link">EPP</a>.</li>' +
                    '<li>La richiesta e il download possono essere automatizzati tramite la tua <a href="#glossary-dashboard" class="glossary-link">dashboard</a>.</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '<div class="p-6 mt-8 border-l-4 rounded-r-lg border-amber-400 bg-amber-50">' +
                    '<h4 class="font-bold text-amber-800">Responsabilità sulla <a href="#glossary-plusvalenza" class="glossary-link">Plusvalenza</a></h4>' +
                    '<p class="mt-2 text-amber-700"><strong>Attenzione:</strong> FlorenceEGI NON si occupa della fiscalità delle transazioni tra utenti. Sei tu il responsabile della dichiarazione dei tuoi guadagni (<a href="#glossary-plusvalenza" class="glossary-link">plusvalenze</a> o altri proventi) secondo il tuo regime fiscale. La piattaforma fornisce solo la reportistica completa per facilitare i tuoi calcoli.</p>' +
                    '</div>'
            },
            internazionale: {
                title: 'Gestione IVA e Fiscalità Internazionale',
                nav: 'IVA e Internaz.',
                intro: 'Operando in un contesto globale, la corretta gestione dell\'IVA è fondamentale. Questa sezione illustra come FlorenceEGI gestisce l\'IVA sulle proprie commissioni in base alla residenza dell\'utente (Italia, UE, Extra-UE), sfruttando i regimi OSS/MOSS e l\'automazione per garantire la compliance cross-border.',
                content: '<h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione <a href="#glossary-iva" class="glossary-link">IVA</a> sulle <a href="#glossary-fee" class="glossary-link">Fee</a> della Piattaforma</h3>' +
                    '<p class="mb-6 text-gray-600">L\'applicazione dell\'<a href="#glossary-iva" class="glossary-link">IVA</a> sulle <a href="#glossary-fee" class="glossary-link">fee</a> di FlorenceEGI dipende dalla tua residenza fiscale e dal tuo status (privato o azienda). Ecco come funziona:</p>' +
                    '<div class="space-y-4">' +
                    '<div class="p-4 border rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Utenti residenti in Italia</h4><p class="mt-2 text-gray-700">Viene applicata l\'aliquota <a href="#glossary-iva" class="glossary-link">IVA</a> ordinaria italiana su tutte le <a href="#glossary-fee" class="glossary-link">fee</a> incassate.</p></div>' +
                    '<div class="p-4 border rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Utenti residenti in Unione Europea</h4><ul class="mt-2 space-y-2 text-gray-700 list-disc list-inside"><li><strong>Privati:</strong> Viene applicata l\'<a href="#glossary-iva" class="glossary-link">IVA</a> del paese di residenza del consumatore, secondo il regime <a href="#glossary-oss" class="glossary-link">OSS (One Stop Shop)</a>.</li><li><strong>Aziende (con <a href="#glossary-partita-iva" class="glossary-link">Partita IVA</a> UE):</strong> Si applica il meccanismo del <a href="#glossary-reverse-charge" class="glossary-link">reverse charge</a>. La fattura viene emessa senza <a href="#glossary-iva" class="glossary-link">IVA</a>.</li></ul></div>' +
                    '<div class="p-4 border rounded-lg bg-gray-50"><h4 class="text-lg font-semibold text-gray-800">Utenti residenti Extra-UE</h4><p class="mt-2 text-gray-700">Generalmente, le fatture per le <a href="#glossary-fee" class="glossary-link">fee</a> vengono emesse senza <a href="#glossary-iva" class="glossary-link">IVA</a>. La transazione viene comunque <a href="#glossary-tracciabilita" class="glossary-link">tracciata</a> e segnalata secondo le normative vigenti.</p></div>' +
                    '</div>' +
                    '<div class="p-6 mt-8 border-l-4 border-blue-400 rounded-r-lg bg-blue-50">' +
                    '<h4 class="font-bold text-blue-800">Nota sulle Donazioni e sulle Vendite tra Utenti</h4>' +
                    '<p class="mt-2 text-blue-700">Le <strong>donazioni</strong> agli <a href="#glossary-epp" class="glossary-link">EPP</a> sono atti di liberalità e quindi <strong>non soggette a <a href="#glossary-iva" class="glossary-link">IVA</a></strong>. Per le <strong>vendite tra utenti</strong> (es. <a href="#glossary-creator" class="glossary-link">Creator</a> che vende <a href="#glossary-egi" class="glossary-link">EGI</a>), l\'applicazione dell\'<a href="#glossary-iva" class="glossary-link">IVA</a> dipende dal regime fiscale del venditore (<a href="#glossary-merchant" class="glossary-link">merchant</a>), che è responsabile della corretta fatturazione.</p>' +
                    '</div>'
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const roleNav = document.getElementById('role-nav');
            const contentContainer = document.getElementById('content-container');
            let chartInstance = null;

            const createNavButton = (id, text) => {
                const button = document.createElement('button');
                button.dataset.role = id;
                button.className =
                    'nav-item px-4 py-2 sm:px-5 sm:py-2.5 text-sm sm:text-base font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-emerald-600 hover:text-white hover:shadow-md';
                button.textContent = text;
                button.onclick = () => showSection(id);
                return button;
            };

            // Add Home button
            const homeButton = document.createElement('a');
            homeButton.href = '/';
            homeButton.className =
                'inline-flex items-center gap-2 px-5 py-3 font-medium text-white transition-all duration-200 ease-in-out transform bg-emerald-600 rounded-xl hover:bg-emerald-700 hover:scale-105 hover:shadow-lg nav-item';
            homeButton.innerHTML = '<span class="text-xl material-icons">home</span><span>Home</span>';
            roleNav.appendChild(homeButton);

            Object.keys(contentData).forEach(key => {
                const sectionData = contentData[key];
                roleNav.appendChild(createNavButton(key, sectionData.nav));
                const section = document.createElement('section');
                section.id = `section-${key}`;
                section.className = 'content-section';
                section.innerHTML = `
                    <div class="p-6 bg-white shadow-lg sm:p-8 rounded-2xl">
                        <h2 class="mb-2 text-3xl font-bold text-gray-800">${sectionData.title}</h2>
                        <p class="mb-6 text-gray-600">${sectionData.intro}</p>
                        <div class="pt-6 border-t border-gray-200">${sectionData.content}</div>
                    </div>`;
                contentContainer.appendChild(section);
            });

            const showSection = (roleId) => {
                document.querySelectorAll('.content-section').forEach(el => el.classList.remove('active'));
                document.getElementById(`section-${roleId}`).classList.add('active');
                document.querySelectorAll('#role-nav button').forEach(el => el.classList.remove('active-nav'));
                document.querySelector(`#role-nav button[data-role='${roleId}']`).classList.add('active-nav');
                window.scrollTo(0, 0);
                if (roleId === 'panoramica') renderChart();
            };

            const renderChart = () => {
                const ctx = document.getElementById('responsibilityChart');
                if (!ctx) return;
                if (chartInstance) chartInstance.destroy();
                chartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Responsabilità dell\'Utente (Creator, EPP, Trader)',
                            'Responsabilità della Piattaforma (FlorenceEGI)'
                        ],
                        datasets: [{
                            data: [85, 15],
                            backgroundColor: ['rgb(16, 185, 129)', 'rgb(209, 213, 219)'],
                            borderColor: '#fdfcfb',
                            borderWidth: 4,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    boxWidth: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (c) => `${c.label}: ${c.parsed}%`
                                }
                            }
                        }
                    }
                });
            }
            showSection('panoramica');

            // === LOGICA TORNA AL TESTO ===
            let previousSection = null;
            let previousScrollPosition = 0;
            const backButton = document.getElementById('backToTextButton');
            const backButtonLabel = document.getElementById('backToTextLabel');

            // Intercetta click sui glossary links
            document.addEventListener('click', (e) => {
                const glossaryLink = e.target.closest('.glossary-link');
                if (glossaryLink && glossaryLink.getAttribute('href')?.startsWith('#glossary-')) {
                    // Salva la sezione attiva corrente
                    const activeSection = document.querySelector('.content-section.active');
                    if (activeSection) {
                        previousSection = activeSection.id.replace('section-', '');
                        previousScrollPosition = window.scrollY;
                        
                        // Trova il nome della sezione per il pulsante
                        const sectionData = contentData[previousSection];
                        if (sectionData) {
                            backButtonLabel.textContent = `Torna a ${sectionData.nav}`;
                        }
                    }
                }
            });

            // Mostra/nascondi pulsante quando si è in una voce di glossario
            window.addEventListener('hashchange', () => {
                const hash = window.location.hash;
                if (hash.startsWith('#glossary-') && previousSection) {
                    // Sei in una voce di glossario
                    backButton.classList.add('show');
                } else {
                    // Non sei in una voce di glossario
                    backButton.classList.remove('show');
                    previousSection = null;
                }
            });

            // Gestisci click sul pulsante Torna al Testo
            backButton.addEventListener('click', () => {
                if (previousSection) {
                    // Torna alla sezione precedente
                    showSection(previousSection);
                    
                    // Riporta allo scroll precedente con un piccolo delay per permettere il render
                    setTimeout(() => {
                        window.scrollTo({
                            top: previousScrollPosition,
                            behavior: 'smooth'
                        });
                    }, 100);
                    
                    // Nascondi pulsante
                    backButton.classList.remove('show');
                    
                    // Rimuovi hash dall'URL
                    history.pushState('', document.title, window.location.pathname + window.location.search);
                }
            });

            // Check iniziale se siamo già in una voce di glossario (es. deep link)
            if (window.location.hash.startsWith('#glossary-')) {
                // Non possiamo sapere da dove veniva, quindi offriamo ritorno a panoramica
                previousSection = 'panoramica';
                backButtonLabel.textContent = 'Torna a Panoramica';
                backButton.classList.add('show');
            }
        });
    </script>
</body>

</html>
