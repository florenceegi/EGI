<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>White Paper Fiscale Interattivo - FlorenceEGI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chosen Palette: Warm Neutrals with Green Accent -->
    <!-- Application Structure Plan: Ho progettato l'applicazione attorno a una navigazione basata sui ruoli ("Chi Sei?"). Questa struttura, invece di replicare l'indice del report, permette agli utenti (creator, EPP, trader) di accedere immediatamente alle informazioni pertinenti al loro caso d'uso. La vista di default ("Panoramica") offre una sintesi dei principi chiave e della divisione delle responsabilità, fornendo un contesto generale. Ogni sezione specifica per ruolo presenta un riepilogo degli obblighi, una visualizzazione del flusso finanziario e gli strumenti offerti dalla piattaforma, ottimizzando la chiarezza e l'usabilità. Questa architettura trasforma un documento testuale in uno strumento interattivo e orientato all'utente. -->
    <!-- Visualization & Content Choices: Report Info: Divisione delle responsabilità fiscali. -> Goal: Informare. -> Viz/Presentation Method: Doughnut Chart (Chart.js) e diagrammi di flusso (HTML/CSS). -> Interaction: Hover sul grafico per dettagli, selezione del ruolo per visualizzare il flusso specifico. -> Justification: Il doughnut chart comunica istantaneamente che la maggior parte della responsabilità fiscale ricade sull'utente, un punto chiave del report. I diagrammi di flusso HTML/CSS semplificano i complessi percorsi finanziari descritti testualmente, rendendoli più comprensibili rispetto a un lungo paragrafo. -> Library/Method: Chart.js for Canvas rendering. -->
    <!-- CONFIRMATION: NO SVG graphics used. NO Mermaid JS used. -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfcfb;
            color: #383838;
        }
        .active-nav {
            background-color: #047857;
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            height: 300px;
            max-height: 400px;
        }
        @media (min-width: 768px) {
            .chart-container {
                height: 350px;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen">
        <header class="bg-white shadow-sm">
            <div class="px-4 py-6 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-emerald-800">White Paper Fiscale Interattivo</h1>
                <p class="mt-2 text-gray-600 text-md">Esplora la gestione fiscale di FlorenceEGI in modo semplice e intuitivo.</p>
            </div>
        </header>

        <main class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 mb-8 bg-white shadow-lg rounded-2xl">
                <h2 class="mb-4 text-xl font-bold text-center text-gray-800">Chi sei? Seleziona il tuo ruolo per visualizzare le tue responsabilità fiscali.</h2>
                <nav id="role-nav" class="flex flex-wrap justify-center gap-3 sm:gap-4">
                </nav>
            </div>

            <div id="content-container">
            </div>
        </main>

        <footer class="mt-12 bg-white">
            <div class="px-4 py-4 mx-auto text-sm text-center text-gray-500 max-w-7xl sm:px-6 lg:px-8">
                <p>&copy; 2025 FlorenceEGI. Questo è un documento interattivo basato sul White Paper Fiscale (FEGI TAX WP-OS1).</p>
            </div>
        </footer>
    </div>

    <script>
        const contentData = {
            panoramica: {
                title: 'Panoramica Generale',
                nav: 'Panoramica',
                intro: 'Questa sezione offre una visione d\'insieme dei principi fondamentali che guidano la compliance fiscale sulla piattaforma FlorenceEGI. Qui puoi comprendere la filosofia di trasparenza, la divisione delle responsabilità e il funzionamento generale del sistema, prima di approfondire i dettagli specifici per il tuo ruolo.',
                content: `
                    <div class="grid items-center gap-8 md:grid-cols-2">
                        <div>
                            <h3 class="mb-4 text-2xl font-bold text-emerald-700">Principi Guida (OS1)</h3>
                            <div class="space-y-3">
                                <div class="p-4 rounded-lg bg-emerald-50">
                                    <h4 class="font-semibold text-emerald-800">Trasparenza Radicale</h4>
                                    <p class="text-sm text-gray-600">Ogni flusso economico è tracciabile, ricostruibile e accessibile per garantire massima chiarezza.</p>
                                </div>
                                <div class="p-4 rounded-lg bg-emerald-50">
                                    <h4 class="font-semibold text-emerald-800">Automazione Intelligente</h4>
                                    <p class="text-sm text-gray-600">Generazione automatica di report, ricevute e fatture per ridurre il rischio di errore umano.</p>
                                </div>
                                <div class="p-4 rounded-lg bg-emerald-50">
                                    <h4 class="font-semibold text-emerald-800">Responsabilità Chiara</h4>
                                    <p class="text-sm text-gray-600">Ogni attore conosce esattamente i propri obblighi. La piattaforma non agisce mai da sostituto d'imposta.</p>
                                </div>
                                 <div class="p-4 rounded-lg bg-emerald-50">
                                    <h4 class="font-semibold text-emerald-800">Auditabilità Integrata</h4>
                                    <p class="text-sm text-gray-600">Ogni transazione e documento è storicizzato e pronto per qualsiasi verifica interna o esterna.</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-4 text-2xl font-bold text-center text-emerald-700">Ripartizione delle Responsabilità</h3>
                            <p class="mb-4 text-center text-gray-600">Il grafico illustra come la responsabilità fiscale sia principalmente in capo all'utente, mentre la piattaforma fornisce gli strumenti per la compliance.</p>
                            <div class="chart-container">
                                <canvas id="responsibilityChart"></canvas>
                            </div>
                        </div>
                    </div>
                `
            },
            piattaforma: {
                title: 'Ruolo della Piattaforma',
                nav: 'Piattaforma',
                intro: 'In questa sezione viene definito il ruolo di FlorenceEGI: un facilitatore tecnologico che non gestisce fondi per conto terzi. Vengono illustrati gli obblighi fiscali specifici della piattaforma, la gestione delle proprie commissioni (fee) e come fornisce strumenti di supporto agli utenti senza mai sostituirsi a loro nelle responsabilità fiscali.',
                content: `
                    <h3 class="mb-4 text-2xl font-bold text-emerald-700">Obblighi Fiscali di FlorenceEGI</h3>
                    <div class="grid gap-8 md:grid-cols-2">
                        <div class="p-6 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione Fee</h4>
                             <ul class="space-y-2 text-gray-700 list-disc list-inside">
                                <li>Incassa **esclusivamente** la propria fee di servizio.</li>
                                <li>I fondi non vengono mai trattenuti per conto di terzi.</li>
                                <li>Le fee vengono accreditate direttamente sul wallet della piattaforma.</li>
                            </ul>
                        </div>
                         <div class="p-6 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-lg font-semibold text-gray-800">Fatturazione e IVA</h4>
                            <ul class="space-y-2 text-gray-700 list-disc list-inside">
                                <li>Emette **fattura elettronica** per ogni fee incassata.</li>
                                <li>Adotta fatturazione cumulativa (batch) per operazioni ad alto volume.</li>
                                <li>Gestisce l'IVA secondo le normative nazionali e internazionali (OSS/MOSS).</li>
                            </ul>
                        </div>
                    </div>
                     <div class="mt-8">
                        <h4 class="mb-4 text-xl font-bold text-center text-emerald-700">Flusso Finanziario della Piattaforma</h4>
                        <div class="flex flex-col items-center justify-center gap-4 text-center md:flex-row">
                            <div class="p-4 bg-blue-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Transazione Utente</p>
                                <p class="text-sm">(es. Minting, Trading)</p>
                            </div>
                            <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                            <div class="p-4 rounded-lg shadow-sm bg-emerald-100">
                                <p class="font-semibold">Separazione Fee</p>
                                <p class="text-sm">La fee viene separata</p>
                            </div>
                            <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                            <div class="p-4 bg-green-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Wallet FlorenceEGI</p>
                                <p class="text-sm">La fee è incassata</p>
                            </div>
                             <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                             <div class="p-4 bg-yellow-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Fatturazione</p>
                                <p class="text-sm">Emissione fattura all'utente</p>
                            </div>
                        </div>
                    </div>
                `
            },
            creator: {
                title: 'Gestione Fiscale per Creator e Mecenati',
                nav: 'Creator / Mecenate',
                intro: 'Se sei un Creator o un Mecenate, questa sezione è per te. Qui troverai una guida chiara su come vengono gestiti i tuoi incassi, quali sono i tuoi obblighi fiscali a seconda che tu sia un privato o una Partita IVA, e quali strumenti di automazione e reportistica FlorenceEGI mette a tua disposizione per semplificare la tua compliance.',
                content: `
                    <div class="grid gap-8 md:grid-cols-2">
                        <div>
                            <h3 class="mb-4 text-2xl font-bold text-emerald-700">I Tuoi Obblighi Fiscali</h3>
                             <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <h4 class="text-lg font-semibold text-gray-800">Se sei un Privato</h4>
                                    <p class="mt-2 text-gray-700">Per vendite occasionali, devi emettere una <strong>ricevuta per prestazione occasionale</strong> e dichiarare il reddito come "reddito diverso". Se l'attività diventa abituale, è obbligatorio aprire Partita IVA.</p>
                                </div>
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <h4 class="text-lg font-semibold text-gray-800">Se hai Partita IVA</h4>
                                    <p class="mt-2 text-gray-700">È obbligatoria la <strong>fatturazione elettronica</strong> per ogni incasso ricevuto, applicando il tuo regime fiscale e l'IVA, se prevista.</p>
                                </div>
                            </div>
                             <h3 class="mt-6 mb-4 text-2xl font-bold text-emerald-700">Strumenti a Tua Disposizione</h3>
                             <ul class="space-y-2 text-gray-700 list-disc list-inside">
                                <li>Dashboard con report dettagliato delle vendite.</li>
                                <li>Esportazione dati in CSV/XML per la tua contabilità.</li>
                                <li>Alert automatici al raggiungimento di soglie fiscali.</li>
                                <li>Modelli di ricevuta/fattura scaricabili.</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="mb-4 text-2xl font-bold text-center text-emerald-700">Flusso di Incasso per Creator</h3>
                            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                                <div class="flex flex-col items-center space-y-4 text-center">
                                    <div class="w-full p-4 bg-blue-100 rounded-lg">
                                        <p class="font-semibold">1. Vendita EGI</p>
                                        <p class="text-sm">Un utente acquista il tuo EGI.</p>
                                    </div>
                                    <div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>
                                    <div class="w-full p-4 rounded-lg bg-emerald-100">
                                        <p class="font-semibold">2. Accredito Diretto</p>
                                        <p class="text-sm">L'importo (al netto della fee di piattaforma) viene inviato istantaneamente.</p>
                                    </div>
                                    <div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>
                                    <div class="w-full p-4 bg-green-100 rounded-lg">
                                        <p class="font-semibold">3. Il Tuo Wallet</p>
                                        <p class="text-sm">Ricevi i fondi direttamente sul tuo wallet, senza intermediari.</p>
                                    </div>
                                    <div class="text-2xl font-bold transform rotate-90 text-emerald-500">&#10230;</div>
                                    <div class="w-full p-4 bg-yellow-100 rounded-lg">
                                        <p class="font-semibold">4. Compliance Fiscale</p>
                                        <p class="text-sm">Emetti fattura/ricevuta e dichiari il reddito.</p>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-center text-gray-600"><strong>Ricorda:</strong> Sei l'unico responsabile della tua dichiarazione fiscale. FlorenceEGI non è un sostituto d'imposta.</p>
                            </div>
                        </div>
                    </div>
                `
            },
            epp: {
                title: 'Gestione Fiscale per EPP',
                nav: 'Ente / EPP',
                intro: 'Questa area è dedicata agli EPP (Environmental Protection Project). Spiega come gli enti, sia piccoli no profit che grandi organizzazioni, ricevono i fondi, quali sono i loro obblighi specifici riguardo le donazioni (con un focus sull\'emissione delle ricevute) e come la piattaforma facilita la gestione e la reportistica senza intervenire nei flussi finanziari.',
                content: `
                    <div class="grid gap-8 md:grid-cols-2">
                        <div>
                           <h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione Donazioni</h3>
                             <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <h4 class="text-lg font-semibold text-gray-800">Piccoli Enti No Profit (ETS/ONLUS)</h4>
                                    <p class="mt-2 text-gray-700">Non devono emettere fattura elettronica per le donazioni. Devono rilasciare una <strong>ricevuta di donazione</strong> (anche cumulativa annuale) solo su richiesta del donatore. I fondi sono ricevuti direttamente sul wallet dell'ente.</p>
                                </div>
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <h4 class="text-lg font-semibold text-gray-800">Grandi Enti e Aziende</h4>
                                    <p class="mt-2 text-gray-700">Gestiscono la compliance internamente tramite i propri sistemi contabili (ERP, CRM). FlorenceEGI fornisce report ed export dei dati per facilitare l'integrazione, ma la responsabilità della documentazione fiscale resta dell'ente.</p>
                                </div>
                            </div>
                        </div>
                        <div>
                             <h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione Ricevute di Donazione</h3>
                              <ul class="p-4 space-y-3 text-gray-700 list-disc list-inside rounded-lg bg-gray-50">
                                <li>L'emissione è obbligatoria <strong>solo su richiesta</strong> del donatore.</li>
                                <li>Si raccomanda l'emissione di una <strong>ricevuta cumulativa</strong> (annuale o mensile) per semplificare la gestione.</li>
                                <li>L'EPP può abilitare la <strong>generazione automatica</strong> delle ricevute tramite la piattaforma.</li>
                                <li>Il donatore può scaricare la ricevuta dalla propria dashboard.</li>
                            </ul>
                        </div>
                    </div>
                     <div class="mt-8">
                        <h4 class="mb-4 text-xl font-bold text-center text-emerald-700">Flusso di Donazione per EPP</h4>
                        <div class="flex flex-col items-center justify-center gap-4 text-center md:flex-row">
                            <div class="p-4 bg-blue-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Donazione Utente</p>
                                <p class="text-sm">Tramite acquisto EGI</p>
                            </div>
                            <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                            <div class="p-4 rounded-lg shadow-sm bg-emerald-100">
                                <p class="font-semibold">Accredito Diretto</p>
                                <p class="text-sm">La quota di donazione è inviata</p>
                            </div>
                            <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                            <div class="p-4 bg-green-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Wallet EPP</p>
                                <p class="text-sm">L'ente riceve i fondi</p>
                            </div>
                             <div class="text-2xl font-bold text-emerald-600">&#10230;</div>
                             <div class="p-4 bg-yellow-100 rounded-lg shadow-sm">
                                <p class="font-semibold">Ricevuta (su richiesta)</p>
                                <p class="text-sm">L'ente emette la ricevuta</p>
                            </div>
                        </div>
                    </div>
                `
            },
            trader: {
                title: 'Gestione Fiscale per Trader e Alto Flusso',
                nav: 'Trader Pro',
                intro: 'Il trading ad alto volume presenta sfide fiscali uniche. Questa sezione spiega come FlorenceEGI affronta questa complessità attraverso l\'automazione, la fatturazione cumulativa per le fee di piattaforma e la gestione semplificata delle micro-donazioni agli EPP, garantendo la tracciabilità senza ostacolare l\'operatività.',
                content: `
                    <h3 class="mb-4 text-2xl font-bold text-emerald-700">Compliance per il Trading EGI pt</h3>
                     <div class="grid gap-8 md:grid-cols-2">
                        <div class="p-6 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione Fee di Piattaforma</h4>
                             <p class="mb-3 text-gray-700">Per gestire l'alto numero di micro-transazioni, la piattaforma adotta un sistema di <strong>fatturazione cumulativa batch</strong>.</p>
                             <ul class="space-y-2 text-gray-700 list-disc list-inside">
                                <li>Riceverai <strong>una sola fattura elettronica periodica</strong> (es. mensile) per tutte le fee maturate.</li>
                                <li>Alla fattura sarà allegato un report con il dettaglio di ogni singola transazione.</li>
                            </ul>
                        </div>
                         <div class="p-6 rounded-lg bg-gray-50">
                            <h4 class="mb-3 text-lg font-semibold text-gray-800">Gestione Donazioni agli EPP</h4>
                            <p class="mb-3 text-gray-700">Anche le micro-donazioni derivanti da migliaia di trade sono gestite in modo semplice.</p>
                            <ul class="space-y-2 text-gray-700 list-disc list-inside">
                                <li>Puoi richiedere una <strong>ricevuta di donazione cumulativa</strong> (annuale/mensile) all'EPP.</li>
                                <li>La richiesta e il download possono essere automatizzati tramite la tua dashboard.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-6 mt-8 border-l-4 border-yellow-400 rounded-r-lg bg-yellow-50">
                        <h4 class="font-bold text-yellow-800">Responsabilità sulla Plusvalenza</h4>
                        <p class="mt-2 text-yellow-700"><strong>Attenzione:</strong> FlorenceEGI NON si occupa della fiscalità delle transazioni tra utenti. Sei tu il responsabile della dichiarazione dei tuoi guadagni (plusvalenze o altri proventi) secondo il tuo regime fiscale. La piattaforma fornisce solo la reportistica completa per facilitare i tuoi calcoli.</p>
                    </div>
                `
            },
            internazionale: {
                title: 'Gestione IVA e Fiscalità Internazionale',
                nav: 'IVA e Internaz.',
                intro: 'Operando in un contesto globale, la corretta gestione dell\'IVA è fondamentale. Questa sezione illustra come FlorenceEGI gestisce l\'IVA sulle proprie commissioni in base alla residenza dell\'utente (Italia, UE, Extra-UE), sfruttando i regimi OSS/MOSS e l\'automazione per garantire la compliance cross-border.',
                content: `
                    <h3 class="mb-4 text-2xl font-bold text-emerald-700">Gestione IVA sulle Fee della Piattaforma</h3>
                    <p class="mb-6 text-gray-600">L'applicazione dell'IVA sulle fee di FlorenceEGI dipende dalla tua residenza fiscale e dal tuo status (privato o azienda). Ecco come funziona:</p>
                     <div class="space-y-4">
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-800">Utenti residenti in Italia</h4>
                            <p class="mt-2 text-gray-700">Viene applicata l'aliquota IVA ordinaria italiana su tutte le fee incassate.</p>
                        </div>
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-800">Utenti residenti in Unione Europea</h4>
                            <ul class="mt-2 space-y-2 text-gray-700 list-disc list-inside">
                               <li><strong>Privati:</strong> Viene applicata l'IVA del paese di residenza del consumatore, secondo il regime <strong>OSS (One Stop Shop)</strong>.</li>
                               <li><strong>Aziende (con Partita IVA UE):</strong> Si applica il meccanismo del <strong>"reverse charge"</strong>. La fattura viene emessa senza IVA.</li>
                           </ul>
                        </div>
                        <div class="p-4 border rounded-lg bg-gray-50">
                             <h4 class="text-lg font-semibold text-gray-800">Utenti residenti Extra-UE</h4>
                             <p class="mt-2 text-gray-700">Generalmente, le fatture per le fee vengono emesse senza IVA. La transazione viene comunque tracciata e segnalata secondo le normative vigenti.</p>
                        </div>
                     </div>
                      <div class="p-6 mt-8 border-l-4 border-blue-400 rounded-r-lg bg-blue-50">
                        <h4 class="font-bold text-blue-800">Nota sulle Donazioni e sulle Vendite tra Utenti</h4>
                        <p class="mt-2 text-blue-700">Le <strong>donazioni</strong> agli EPP sono atti di liberalità e quindi <strong>non soggette a IVA</strong>. Per le <strong>vendite tra utenti</strong> (es. Creator che vende EGI), l'applicazione dell'IVA dipende dal regime fiscale del venditore, che è responsabile della corretta fatturazione.</p>
                    </div>
                `
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const roleNav = document.getElementById('role-nav');
            const contentContainer = document.getElementById('content-container');
            let chartInstance = null;

            const createNavButton = (id, text) => {
                const button = document.createElement('button');
                button.dataset.role = id;
                button.className = 'nav-item px-4 py-2 sm:px-5 sm:py-2.5 text-sm sm:text-base font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-emerald-600 hover:text-white hover:shadow-md';
                button.textContent = text;
                button.onclick = () => showSection(id);
                return button;
            };

            Object.keys(contentData).forEach(key => {
                const sectionData = contentData[key];
                roleNav.appendChild(createNavButton(key, sectionData.nav));

                const section = document.createElement('section');
                section.id = `section-${key}`;
                section.className = 'content-section bg-white p-6 sm:p-8 rounded-2xl shadow-lg';
                section.innerHTML = `
                    <h2 class="mb-2 text-3xl font-bold text-gray-800">${sectionData.title}</h2>
                    <p class="mb-6 text-gray-600">${sectionData.intro}</p>
                    <div class="pt-6 border-t border-gray-200">${sectionData.content}</div>
                `;
                contentContainer.appendChild(section);
            });

            const showSection = (roleId) => {
                document.querySelectorAll('.content-section').forEach(el => el.classList.remove('active'));
                document.getElementById(`section-${roleId}`).classList.add('active');

                document.querySelectorAll('#role-nav button').forEach(el => el.classList.remove('active-nav'));
                document.querySelector(`#role-nav button[data-role='${roleId}']`).classList.add('active-nav');

                if (roleId === 'panoramica') {
                    renderChart();
                }
            };

            const renderChart = () => {
                const ctx = document.getElementById('responsibilityChart');
                if (!ctx) return;
                if(chartInstance) {
                    chartInstance.destroy();
                }
                chartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Responsabilità dell\'Utente (Creator, EPP, Trader)', 'Responsabilità della Piattaforma (FlorenceEGI)'],
                        datasets: [{
                            label: 'Ripartizione delle Responsabilità Fiscali',
                            data: [85, 15],
                            backgroundColor: [
                                'rgb(16, 185, 129)',
                                'rgb(209, 213, 219)'
                            ],
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
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            showSection('panoramica');
        });
    </script>
</body>
</html>
