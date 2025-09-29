{{--
    File: egi-info.blade.php
    Versione: 1.0 EGI Definition Page
    Data: 29 Settembre 2025
    Descrizione: La regina delle pagine - Info completa sugli EGI (Ecological Goods Invent)
    Caratteristiche: Brand Guidelines, tre componenti principali, stile patron-standalone
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGI: Asset Digitali Beyond NFT | Certificazione Blockchain, Utilities e Impatto Ambientale</title>
    <meta name="description"
        content="EGI (Ecological Goods Invent): oltre gli NFT tradizionali. Asset digitali certificati che rappresentano opere d'arte, utility cards, servizi, contratti e token commerciali con impatto ambientale concreto su blockchain Algorand ARC-72.">

    <!-- Google Fonts - Brand Guidelines -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Brand Colors e Configurazione -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'oro-fiorentino': '#D4A574',
                        'verde-rinascita': '#2D5016',
                        'blu-algoritmo': '#1B365D',
                        'grigio-pietra': '#6B6B6B',
                        'rosso-urgenza': '#C13120',
                        'arancio-energia': '#E67E22',
                        'viola-innovazione': '#8E44AD'
                    },
                    fontFamily: {
                        'renaissance': ['Playfair Display', 'serif'],
                        'body': ['Source Sans Pro', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            overflow-x: hidden;
        }

        .renaissance-title {
            font-family: 'Playfair Display', serif;
        }

        /* Layout Rinascimentale - Sezione Aurea */
        .golden-ratio-container {
            max-width: 1618px;
            margin: 0 auto;
        }

        /* Animazioni eleganti */
        .elegant-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .elegant-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* CTA Oro Fiorentino */
        .cta-primary {
            background: linear-gradient(135deg, #D4A574 0%, #B8956A 100%);
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.3);
        }

        .cta-primary:hover {
            box-shadow: 0 6px 20px rgba(212, 165, 116, 0.4);
            transform: translateY(-1px);
        }

        /* Hero Background */
        .hero-background {
            background: linear-gradient(135deg, rgba(27, 54, 93, 0.95) 0%, rgba(45, 80, 22, 0.85) 100%),
                url('{{ asset('images/default/patron_banner_background_rinascimento_1.png') }}') no-repeat center center/cover;
            min-height: 70vh;
        }

        /* Cards eleganti */
        .renaissance-card {
            background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
            border: 1px solid rgba(212, 165, 116, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .renaissance-card:hover {
            border-color: #D4A574;
            box-shadow: 0 8px 30px rgba(212, 165, 116, 0.15);
        }

        /* Componenti EGI - Cerchi colorati */
        .egi-component {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 0.9rem;
            position: relative;
            margin: 0 auto 1.5rem;
        }

        .egi-epp {
            background: linear-gradient(135deg, #8E44AD 0%, #9B59B6 100%);
        }

        .egi-goods {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        }

        .egi-creativita {
            background: linear-gradient(135deg, #E91E63 0%, #F06292 100%);
        }

        /* Sezione scura alternata */
        .section-dark {
            background: linear-gradient(135deg, #1B365D 0%, #2D5016 100%);
        }

        /* Schema code blocks */
        .schema-block {
            background: #f8f9fa;
            border-left: 4px solid #D4A574;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="pt-20 bg-gray-50 text-grigio-pietra">

    <!-- Header con Navigazione - Fixed -->
    <header class="fixed top-0 left-0 right-0 z-50 text-white shadow-lg bg-blu-algoritmo">
        <div class="px-4 py-4 golden-ratio-container sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="text-3xl fas fa-leaf text-oro-fiorentino sm:text-4xl"></i>
                    <div>
                        <h1 class="text-xl font-bold renaissance-title sm:text-2xl">EGI Info</h1>
                        <p class="text-sm text-blue-200 font-body sm:text-base">Ecological Goods Invent</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-3 md:flex">
                    <a href="{{ route('home') }}"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">Home</a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">FlorenceEGI</a>
                    <a href="#definizione"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">Definizione</a>
                    <a href="#componenti"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">Componenti</a>
                    <a href="#funzioni"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">Funzioni</a>
                    <a href="#vantaggi"
                        class="text-sm transition hover:text-oro-fiorentino font-body lg:text-base">Vantaggi</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block p-2 transition-colors rounded-md hover:bg-blue-700 md:hidden">
                    <i class="text-2xl fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden pb-4 mt-4 border-t border-blue-600 md:hidden">
                <div class="pt-4 space-y-3">
                    <a href="{{ route('home') }}"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-home text-oro-fiorentino"></i>
                        Torna alla Home
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-infinity text-oro-fiorentino"></i>
                        FlorenceEGI
                    </a>
                    <a href="#definizione"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-book text-oro-fiorentino"></i>
                        Definizione
                    </a>
                    <a href="#componenti"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-puzzle-piece text-oro-fiorentino"></i>
                        Componenti
                    </a>
                    <a href="#funzioni"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-cogs text-oro-fiorentino"></i>
                        Funzioni
                    </a>
                    <a href="#vantaggi"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-star text-oro-fiorentino"></i>
                        Vantaggi
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-background">
        <div class="golden-ratio-container flex min-h-[70vh] items-center justify-center px-4">
            <div class="max-w-5xl mx-auto text-center text-white">
                <div class="mb-8">
                    <span class="px-4 py-2 text-sm font-semibold rounded-full bg-oro-fiorentino text-blu-algoritmo">
                        Ecological Goods Invent
                    </span>
                </div>
                <h1 class="mb-6 text-4xl font-bold renaissance-title sm:text-5xl lg:text-7xl">
                    EGI
                </h1>
                <p class="mb-8 text-xl leading-relaxed text-blue-100 font-body sm:text-2xl">
                    Beyond NFT: Asset Digitali Certificati Blockchain
                </p>
                <p class="mb-10 text-lg leading-relaxed text-blue-200 font-body">
                    <strong>Opere d'arte, utility cards, servizi, contratti, token commerciali</strong> e molto altro. 
                    Gli EGI rappresentano qualsiasi tipologia di bene o servizio con <strong>valore reale, 
                    componenti fisiche e impatto ambientale concreto</strong> certificato su blockchain Algorand.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#definizione"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white transition-all duration-300 rounded-lg cta-primary elegant-hover">
                        <i class="mr-3 fas fa-book-open"></i>
                        Scopri gli EGI
                    </a>
                    <a href="#componenti"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold text-blue-100 transition-all duration-300 border-2 border-white rounded-lg hover:bg-white hover:text-blu-algoritmo">
                        <i class="mr-3 fas fa-puzzle-piece"></i>
                        Le Tre Componenti
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Definizione EGI -->
    <section id="definizione" class="py-20 bg-white">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="mb-6 text-3xl font-bold renaissance-title text-blu-algoritmo sm:text-4xl">
                    Cos'è un EGI?
                </h2>
                <p class="mb-12 text-xl leading-relaxed font-body text-grigio-pietra">
                    L'EGI è l'unità fondamentale della piattaforma FlorenceEGI, un <strong>oggetto digitale
                        certificato</strong>
                    che rivoluziona il concetto di NFT tradizionale.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-oro-fiorentino fas fa-palette"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Beyond Digital Art
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Certificazione blockchain di opere d'arte, utility cards, servizi, contratti e token commerciali con valore reale.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-leaf text-verde-rinascita"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Impatto Ambientale
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Legame strutturale con progetti di protezione ambientale (EPP) per un futuro sostenibile.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-cog text-viola-innovazione"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Utilities Avanzate
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Gestione completa di utilities fisiche e immateriali, temporali e perpetue, con documentazione illimitata.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-shield-alt text-blu-algoritmo"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Certificazione
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Blockchain Algorand ARC-72 con metadati permanenti e certificazione proprietaria.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Le Tre Componenti -->
    <section id="componenti" class="py-20 section-dark">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold text-white renaissance-title sm:text-4xl">
                    Le Tre Componenti del Nostro Asset Digitale
                </h2>
                <p class="text-xl leading-relaxed text-blue-200 font-body">
                    Ogni EGI è composto da tre elementi fondamentali che ne definiscono unicità e valore
                </p>
            </div>

            <!-- EPP - Environment Protection Programs -->
            <div class="mb-20">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="text-center lg:text-left">
                        <div class="egi-component egi-epp">
                            <div>
                                <div class="text-2xl font-bold">EPP</div>
                                <div class="text-xs">Environment<br>Protection<br>Programs</div>
                            </div>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-oro-fiorentino renaissance-title">
                            Environment Protection Programs
                        </h3>
                        <p class="mb-6 text-lg leading-relaxed text-blue-100 font-body">
                            Gli Environment Protection Programs sono iniziative centrali nel progetto FlorenceEGI,
                            rappresentando il cuore dell'impegno dell'associazione verso la sostenibilità ambientale
                            e la rigenerazione ecologica.
                        </p>
                    </div>

                    <div class="p-8 bg-white rounded-lg renaissance-card">
                        <h4 class="mb-4 text-xl font-bold renaissance-title text-blu-algoritmo">
                            Programmi Attivi
                        </h4>
                        <div class="space-y-4">
                            <div class="p-4 border-l-4 border-viola-innovazione bg-purple-50">
                                <h5 class="font-semibold text-blu-algoritmo">🌊 Aquatic Plastic Removal</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Rimozione della plastica da vari ambienti acquatici, azione cruciale per la salute
                                    dei nostri mari, laghi e fiumi.
                                </p>
                            </div>
                            <div class="p-4 border-l-4 border-verde-rinascita bg-green-50">
                                <h5 class="font-semibold text-blu-algoritmo">🌳 Appropriate Restoration Forestry</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Riforestazione attenta e rispettosa degli ecosistemi, terramerici che le piante
                                    scelte siano quelle più adatte per ristabilire l'equilibrio naturale.
                                </p>
                            </div>
                            <div class="p-4 border-l-4 border-arancio-energia bg-orange-50">
                                <h5 class="font-semibold text-blu-algoritmo">🐝 Bee Population Enhancement</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Protezione e incremento delle popolazioni di api, supportando così la biodiversità e
                                    la pollinizzazione.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GOODS -->
            <div class="mb-20">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="order-2 text-center lg:order-1 lg:text-left">
                        <div class="p-8 bg-white rounded-lg renaissance-card">
                            <h4 class="mb-4 text-xl font-bold renaissance-title text-blu-algoritmo">
                                Flessibilità e Potenzialità
                            </h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                Gli EGI (Eco Goods Invent) di FlorenceEGI si distinguono nel panorama digitale per la
                                loro
                                straordinaria flessibilità e potenzialità creative e commerciali del marketing e del
                                branding.
                            </p>
                            <p class="mb-4 font-body text-grigio-pietra">
                                Ogni EGI non è soltanto una raffinata opera d'arte digitale, ma è anche progettato per
                                supportare un'ampia gamma di utilità. Questa caratteristica unica apre le porte a
                                innumerevoli possibilità creative e commerciali.
                            </p>
                            <p class="font-body text-grigio-pietra">
                                Le aziende possono sfruttare la forza espressiva e l'attrattiva delle opere d'arte per
                                creare campagne di marketing più coinvolgenti e memorabili.
                            </p>
                        </div>
                    </div>

                    <div class="order-1 text-center lg:order-2 lg:text-right">
                        <div class="egi-component egi-goods">
                            <div>
                                <div class="text-2xl font-bold">GOODS</div>
                                <div class="text-xs">Beni o<br>Servizi</div>
                            </div>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-oro-fiorentino renaissance-title">
                            Goods, ovvero Beni o Servizi
                        </h3>
                        <p class="text-lg leading-relaxed text-blue-100 font-body">
                            Una rivoluzionaria interfaccia tra arte, utility e marketing, offrendo nuove opportunità
                            per artisti e aziende di collaborare e innovare nel mondo digitale.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CREATIVITÀ E INVENTIVA -->
            <div>
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="text-center lg:text-left">
                        <div class="egi-component egi-creativita">
                            <div>
                                <div class="text-xl font-bold">CREATIVITÀ</div>
                                <div class="text-xs">E INVENTIVA</div>
                            </div>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold text-oro-fiorentino renaissance-title">
                            Creatività e Inventiva
                        </h3>
                        <p class="text-lg leading-relaxed text-blue-100 font-body">
                            Gli EGI incarnano l'essenza dell'arte autentica e originale, rappresentando una
                            rivoluzionaria
                            categoria di asset digitali che celebrano l'unicità e l'autenticità creativa.
                        </p>
                    </div>

                    <div class="p-8 bg-white rounded-lg renaissance-card">
                        <h4 class="mb-4 text-xl font-bold renaissance-title text-blu-algoritmo">
                            Beyond Digital Art: La Rivoluzione degli Asset Digitali
                        </h4>
                        <p class="mb-4 font-body text-grigio-pietra">
                            Gli <strong>EGI (Ecological Goods Invent)</strong> di FlorenceEGI non sono semplici NFT o opere d'arte digitali. 
                            Sono <strong>oggetti digitali certificati blockchain</strong> che possono rappresentare qualsiasi tipologia 
                            di bene, servizio o utility con valore reale e impatto ambientale concreto.
                        </p>
                        <div class="p-4 mb-4 border-l-4 bg-blue-50 border-oro-fiorentino">
                            <h5 class="mb-2 font-semibold text-blu-algoritmo">Cosa può essere un EGI:</h5>
                            <ul class="space-y-1 text-sm text-grigio-pietra">
                                <li>• <strong>Opera d'arte certificata</strong> (digitale o fisica)</li>
                                <li>• <strong>Documento firmato</strong> tra più soggetti (CoA, contratti)</li>
                                <li>• <strong>Utility card</strong> per servizi esclusivi</li>
                                <li>• <strong>Token commerciale</strong> per aziende</li>
                                <li>• <strong>Oggetto da collezione</strong> limitato e permanente</li>
                                <li>• <strong>Elemento narrativo</strong> di universi creativi</li>
                            </ul>
                        </div>
                        <p class="mb-4 font-body text-grigio-pietra">
                            Il termine <strong>"Goods"</strong> non è casuale: la piattaforma FlorenceEGI permette la <strong>gestione 
                            completa di componenti fisiche</strong> oltre a quelle digitali, andando ben oltre i tradizionali 
                            NFT collezionabili per abbracciare utilities concrete, servizi reali e asset tangibili.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Struttura Tecnica -->
    <section id="funzioni" class="py-20 bg-gray-100">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold renaissance-title text-blu-algoritmo sm:text-4xl">
                    Struttura e Funzioni Avanzate
                </h2>
                <p class="text-xl leading-relaxed font-body text-grigio-pietra">
                    Ogni EGI è un ecosistema completo di funzionalità innovative
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Struttura -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <h3 class="mb-6 text-2xl font-bold renaissance-title text-blu-algoritmo">
                        <i class="mr-3 text-oro-fiorentino fas fa-layer-group"></i>
                        Struttura di un EGI
                    </h3>
                    <div class="space-y-4">
                        <div class="schema-block">
                            <strong>Invent:</strong> Opera dell'ingegno umano (non AI)
                        </div>
                        <div class="schema-block">
                            <strong>Good:</strong> Bene o servizio concreto con valore percepibile
                        </div>
                        <div class="schema-block">
                            <strong>EPP:</strong> Environment Protection Program (min 20% valore vendita)
                        </div>
                        <div class="schema-block">
                            <strong>Metadata:</strong> Dati strutturati autore, collezione, diritti, versioni
                        </div>
                        <div class="schema-block">
                            <strong>CoA:</strong> Certificate of Authenticity firmato digitalmente
                        </div>
                        <div class="schema-block">
                            <strong>Smart Contract:</strong> ARC-72 su Algorand con funzioni personalizzate
                        </div>
                    </div>
                </div>

                <!-- Funzioni -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <h3 class="mb-6 text-2xl font-bold renaissance-title text-blu-algoritmo">
                        <i class="mr-3 text-oro-fiorentino fas fa-cogs"></i>
                        Funzioni Avanzate
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🔄 Rebind</h4>
                            <p class="text-sm text-grigio-pietra">Rivendita automatica con sistema royalties integrato
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🏢 Tokenizzazione Aziendale</h4>
                            <p class="text-sm text-grigio-pietra">L'EGI può rappresentare asset commerciali aziendali
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">👥 Gestione Multi-ruolo</h4>
                            <p class="text-sm text-grigio-pietra">Permessi granulari tramite sistema Spatie</p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🖥️ Interfaccia Doppia</h4>
                            <p class="text-sm text-grigio-pietra">Gestibile da pagina interna o CoA Dashboard</p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">✍️ Firma Elettronica</h4>
                            <p class="text-sm text-grigio-pietra">Possibile su ogni EGI con flusso Infocert integrato
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Traits e Utilities: La Potenza Nascosta -->
    <section id="traits-utilities" class="py-20 bg-gradient-to-br from-blue-50 to-green-50">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold renaissance-title text-blu-algoritmo sm:text-4xl">
                    Traits e Utilities: Versatilità Senza Limiti
                </h2>
                <p class="text-xl leading-relaxed font-body text-grigio-pietra">
                    Due sistemi di metadati avanzati e gestione completa di utilities fisiche e digitali
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Due Tipologie di Traits -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <div class="mb-6 text-center">
                        <i class="text-4xl fas fa-tags text-oro-fiorentino"></i>
                    </div>
                    <h3 class="mb-4 text-xl font-bold text-center renaissance-title text-blu-algoritmo">
                        Sistema Traits Duplice
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 border-l-4 bg-purple-50 border-purple-400">
                            <h4 class="mb-2 font-semibold text-purple-700">Traits EGI</h4>
                            <p class="text-sm text-grigio-pietra">
                                Metadati classici collegati direttamente all'EGI per contraddistinguere caratteristiche, 
                                rarità, tipologie e ogni aspetto dell'asset digitale.
                            </p>
                        </div>
                        <div class="p-4 border-l-4 bg-orange-50 border-orange-400">
                            <h4 class="mb-2 font-semibold text-orange-700">Traits CoA</h4>
                            <p class="text-sm text-grigio-pietra">
                                Metadati tecnici specifici collegati al Certificate of Authenticity per descrivere 
                                tecniche artistiche, materiali utilizzati, supporti fisici e processi creativi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Utilities Complete -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <div class="mb-6 text-center">
                        <i class="text-4xl fas fa-tools text-oro-fiorentino"></i>
                    </div>
                    <h3 class="mb-4 text-xl font-bold text-center renaissance-title text-blu-algoritmo">
                        Utilities Avanzate
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🎯 Fisiche e Immateriali</h4>
                            <p class="text-sm text-grigio-pietra">
                                Gestione completa di utilities fisiche (oggetti, prodotti) e immateriali (servizi, accessi, contenuti).
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">⏰ Temporali e Perpetue</h4>
                            <p class="text-sm text-grigio-pietra">
                                Utilities con scadenza programmata o vantaggi permanenti nel tempo.
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">📸 Documentazione Illimitata</h4>
                            <p class="text-sm text-grigio-pietra">
                                Caricamento di infinite immagini per completare la descrizione e documentazione dell'utility.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Beyond NFTs -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <div class="mb-6 text-center">
                        <i class="text-4xl fas fa-rocket text-oro-fiorentino"></i>
                    </div>
                    <h3 class="mb-4 text-xl font-bold text-center renaissance-title text-blu-algoritmo">
                        Oltre gli NFT Tradizionali
                    </h3>
                    <div class="space-y-4 text-sm text-grigio-pietra">
                        <p>
                            <strong>Certificazione Blockchain:</strong> Standard ARC-72 su Algorand per massima sicurezza e interoperabilità.
                        </p>
                        <p>
                            <strong>Valore Reale:</strong> Ogni EGI è collegato a beni, servizi o utilities concrete con valore percepibile.
                        </p>
                        <p>
                            <strong>Impatto Ambientale:</strong> Contributo automatico minimo 20% a progetti di protezione ambientale.
                        </p>
                        <p>
                            <strong>Gestione Professionale:</strong> Strumenti enterprise per PA, aziende e professionisti creativi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="max-w-3xl mx-auto mt-12 text-center">
                <div class="p-8 border-2 rounded-lg bg-gradient-to-r from-oro-fiorentino to-yellow-400 border-oro-fiorentino">
                    <h3 class="mb-4 text-2xl font-bold text-white renaissance-title">
                        "Fanne un EGI" - La Filosofia del Valore
                    </h3>
                    <p class="mb-6 text-lg text-white font-body">
                        Rendere qualsiasi oggetto, servizio, accordo o creazione <strong>riconoscibile, tracciabile e significativo</strong>. 
                        EGI è lo standard della cura per l'arte, l'ambiente e il futuro digitale sostenibile.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 text-sm">
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full">🎨 Arte Certificata</span>
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full">📄 Documenti Firmati</span>
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full">🎫 Utility Cards</span>
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full">🏢 Token Aziendali</span>
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full">🌍 Impatto Ambientale</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Citazione Padmin D. Curtis -->
    <section class="py-16 bg-gradient-to-r from-blu-algoritmo via-verde-rinascita to-blu-algoritmo">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <div class="relative">
                    <!-- Quote Icon -->
                    <div class="absolute top-0 left-0 text-6xl opacity-20 text-oro-fiorentino">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    
                    <!-- Citation Content -->
                    <div class="relative z-10 px-8 py-12">
                        <blockquote class="mb-8 text-2xl font-medium leading-relaxed text-white renaissance-title sm:text-3xl lg:text-4xl">
                            "Come il web ha annullato le distanze di tempo e spazio, 
                            <span class="text-oro-fiorentino">gli EGI rivoluzionano il concetto di proprietà privata</span>."
                        </blockquote>
                        
                        <!-- Attribution -->
                        <footer class="text-xl text-blue-200 font-body">
                            <span class="text-oro-fiorentino">—</span> 
                            <cite class="font-semibold not-italic text-oro-fiorentino">Padmin D. Curtis</cite>
                            <div class="mt-2 text-sm text-blue-300">
                                AI Partner OS3.0, FlorenceEGI Visionary
                            </div>
                        </footer>
                    </div>
                    
                    <!-- Decorative Element -->
                    <div class="absolute bottom-0 right-0 text-6xl opacity-20 text-oro-fiorentino">
                        <i class="fas fa-quote-right"></i>
                    </div>
                </div>
                
                <!-- Subtle separator -->
                <div class="flex items-center justify-center mt-8">
                    <div class="w-20 h-px bg-oro-fiorentino"></div>
                    <div class="mx-4 text-oro-fiorentino">
                        <i class="fas fa-infinity"></i>
                    </div>
                    <div class="w-20 h-px bg-oro-fiorentino"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Finalità e Vantaggi -->
    <section id="vantaggi" class="py-20 bg-white">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold renaissance-title text-blu-algoritmo sm:text-4xl">
                    Perché gli EGI Cambiano le Regole
                </h2>
                <p class="text-xl leading-relaxed font-body text-grigio-pietra">
                    Una rivoluzione nel mondo degli asset digitali
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-seedling text-verde-rinascita"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        NFT con Utilità Reale
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Ogni EGI ha un impatto ambientale concreto e utilità pratica, non solo valore speculativo.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-oro-fiorentino fas fa-certificate"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Certificazione Accessibile
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Piattaforma di certificazione digitale accessibile per PA, aziende e creativi.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-tools text-viola-innovazione"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Strumento Operativo
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Utilizzabile concretamente da enti pubblici, aziende e professionisti creativi.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-book-open text-arancio-energia"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Valuta Narrativa
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Oggetto semantico che racconta storie e crea connessioni significative.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-shield-alt text-rosso-urgenza"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Standard della Cura
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Per l'arte, per l'ambiente, per il futuro: EGI rappresenta l'eccellenza sostenibile.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl fas fa-infinity text-blu-algoritmo"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center renaissance-title text-blu-algoritmo">
                        Riconoscibile e Tracciabile
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        "Fanne un EGI" significa renderlo riconoscibile, tracciabile e significativo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Finale -->
    <section class="py-20 section-dark">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="mb-6 text-3xl font-bold text-oro-fiorentino renaissance-title sm:text-4xl">
                    La Visione: "Fanne un EGI"
                </h2>
                <p class="mb-8 text-xl leading-relaxed text-blue-100 font-body">
                    Significa rendere qualcosa <strong>riconoscibile, tracciabile e significativo</strong>.<br>
                    Un oggetto, un accordo, un'opera, un'intenzione: tutto può diventare un EGI.
                </p>
                <p class="mb-12 text-2xl font-bold text-white renaissance-title">
                    EGI è lo <span class="text-oro-fiorentino">standard della cura</span>: per l'arte, per l'ambiente,
                    per il futuro.
                </p>

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold transition-all duration-300 rounded-lg cta-primary elegant-hover text-blu-algoritmo">
                        <i class="mr-3 fas fa-rocket"></i>
                        Inizia con FlorenceEGI
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold transition-all duration-300 border-2 rounded-lg border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino hover:text-blu-algoritmo">
                        <i class="mr-3 fas fa-info-circle"></i>
                        Scopri FlorenceEGI
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('components.info-footer')

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = mobileMenuButton.querySelector('i');

            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.className = 'fas fa-times text-2xl';
                } else {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                }
            });

            // Close menu when clicking on a link
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.className = 'fas fa-bars text-2xl';
                }
            });

            // Smooth scrolling per i link interni
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
