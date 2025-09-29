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
    <title>EGI - Ecological Goods Invent | Il Futuro degli Asset Digitali Sostenibili</title>
    <meta name="description"
        content="Scopri gli EGI: asset digitali rivoluzionari che uniscono arte, utilità e impatto ambientale. Environment Protection Programs, Goods e Creatività in un unico token ARC-72.">

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

<body class="bg-gray-50 text-grigio-pietra">

    <!-- Header con Navigazione -->
    <header class="text-white shadow-lg bg-blu-algoritmo">
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
        <div class="flex items-center justify-center min-h-[70vh] px-4 golden-ratio-container">
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
                    Il Futuro degli Asset Digitali Sostenibili
                </p>
                <p class="mb-10 text-lg leading-relaxed text-blue-200 font-body">
                    Non è un semplice NFT. È un <strong>oggetto digitale certificato</strong> che unisce 
                    valore artistico, impatto ambientale e utilità concreta in un'unica soluzione rivoluzionaria.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#definizione"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white transition-all duration-300 rounded-lg cta-primary elegant-hover">
                        <i class="mr-3 fas fa-book-open"></i>
                        Scopri gli EGI
                    </a>
                    <a href="#componenti"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold transition-all duration-300 border-2 border-white rounded-lg text-blue-100 hover:bg-white hover:text-blu-algoritmo">
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
                <h2 class="mb-6 text-3xl font-bold text-blu-algoritmo renaissance-title sm:text-4xl">
                    Cos'è un EGI?
                </h2>
                <p class="mb-12 text-xl leading-relaxed text-grigio-pietra font-body">
                    L'EGI è l'unità fondamentale della piattaforma FlorenceEGI, un <strong>oggetto digitale certificato</strong> 
                    che rivoluziona il concetto di NFT tradizionale.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-oro-fiorentino fas fa-palette"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Valore Artistico
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Opere dell'ingegno umano, non generate totalmente da AI, con valore estetico e culturale.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-verde-rinascita fas fa-leaf"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Impatto Ambientale
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Legame strutturale con progetti di protezione ambientale (EPP) per un futuro sostenibile.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-viola-innovazione fas fa-cog"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Utilità Concreta
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Servizi, funzioni e utility reali che vanno oltre il semplice possesso digitale.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-blu-algoritmo fas fa-shield-alt"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Certificazione
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
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
                        <h4 class="mb-4 text-xl font-bold text-blu-algoritmo renaissance-title">
                            Programmi Attivi
                        </h4>
                        <div class="space-y-4">
                            <div class="p-4 border-l-4 border-viola-innovazione bg-purple-50">
                                <h5 class="font-semibold text-blu-algoritmo">🌊 Aquatic Plastic Removal</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Rimozione della plastica da vari ambienti acquatici, azione cruciale per la salute dei nostri mari, laghi e fiumi.
                                </p>
                            </div>
                            <div class="p-4 border-l-4 border-verde-rinascita bg-green-50">
                                <h5 class="font-semibold text-blu-algoritmo">🌳 Appropriate Restoration Forestry</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Riforestazione attenta e rispettosa degli ecosistemi, terramerici che le piante scelte siano quelle più adatte per ristabilire l'equilibrio naturale.
                                </p>
                            </div>
                            <div class="p-4 border-l-4 border-arancio-energia bg-orange-50">
                                <h5 class="font-semibold text-blu-algoritmo">🐝 Bee Population Enhancement</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Protezione e incremento delle popolazioni di api, supportando così la biodiversità e la pollinizzazione.
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
                            <h4 class="mb-4 text-xl font-bold text-blu-algoritmo renaissance-title">
                                Flessibilità e Potenzialità
                            </h4>
                            <p class="mb-4 text-grigio-pietra font-body">
                                Gli EGI (Eco Goods Invent) di FlorenceEGI si distinguono nel panorama digitale per la loro 
                                straordinaria flessibilità e potenzialità creative e commerciali del marketing e del branding.
                            </p>
                            <p class="mb-4 text-grigio-pietra font-body">
                                Ogni EGI non è soltanto una raffinata opera d'arte digitale, ma è anche progettato per 
                                supportare un'ampia gamma di utilità. Questa caratteristica unica apre le porte a 
                                innumerevoli possibilità creative e commerciali.
                            </p>
                            <p class="text-grigio-pietra font-body">
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
                            Gli EGI incarnano l'essenza dell'arte autentica e originale, rappresentando una rivoluzionaria 
                            categoria di asset digitali che celebrano l'unicità e l'autenticità creativa.
                        </p>
                    </div>
                    
                    <div class="p-8 bg-white rounded-lg renaissance-card">
                        <h4 class="mb-4 text-xl font-bold text-blu-algoritmo renaissance-title">
                            L'Arte Autentica Digitale
                        </h4>
                        <p class="mb-4 text-grigio-pietra font-body">
                            Gli EGI (Eco Gate Invent) di FlorenceEGI rappresentano una rivoluzionaria categoria di asset digitali 
                            che incarnano l'essenza dell'arte autentica e originale.
                        </p>
                        <p class="mb-4 text-grigio-pietra font-body">
                            Ogni EGI è un'opera d'arte digitale che può variare da immagini visive, a composizioni musicali, 
                            fino a opere letterarie come ebook. Ciò che distingue gli EGI dai comuni collezionabili digitali 
                            è l'impegno verso l'unicità e l'autenticità creativa.
                        </p>
                        <p class="text-grigio-pietra font-body">
                            La nostra filosofia si basa su un principio fondamentale: ogni opera associata agli EGI deve essere 
                            frutto dell'ingegno e della creatività umana, rifiutando categoricamente l'uso di opere generate 
                            in serie da software.
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
                <h2 class="mb-6 text-3xl font-bold text-blu-algoritmo renaissance-title sm:text-4xl">
                    Struttura e Funzioni Avanzate
                </h2>
                <p class="text-xl leading-relaxed text-grigio-pietra font-body">
                    Ogni EGI è un ecosistema completo di funzionalità innovative
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Struttura -->
                <div class="p-8 bg-white rounded-lg renaissance-card elegant-hover">
                    <h3 class="mb-6 text-2xl font-bold text-blu-algoritmo renaissance-title">
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
                    <h3 class="mb-6 text-2xl font-bold text-blu-algoritmo renaissance-title">
                        <i class="mr-3 text-oro-fiorentino fas fa-cogs"></i>
                        Funzioni Avanzate
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🔄 Rebind</h4>
                            <p class="text-sm text-grigio-pietra">Rivendita automatica con sistema royalties integrato</p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-verde-rinascita">🏢 Tokenizzazione Aziendale</h4>
                            <p class="text-sm text-grigio-pietra">L'EGI può rappresentare asset commerciali aziendali</p>
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
                            <p class="text-sm text-grigio-pietra">Possibile su ogni EGI con flusso Infocert integrato</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Finalità e Vantaggi -->
    <section id="vantaggi" class="py-20 bg-white">
        <div class="px-4 golden-ratio-container sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto mb-16 text-center">
                <h2 class="mb-6 text-3xl font-bold text-blu-algoritmo renaissance-title sm:text-4xl">
                    Perché gli EGI Cambiano le Regole
                </h2>
                <p class="text-xl leading-relaxed text-grigio-pietra font-body">
                    Una rivoluzione nel mondo degli asset digitali
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-verde-rinascita fas fa-seedling"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        NFT con Utilità Reale
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Ogni EGI ha un impatto ambientale concreto e utilità pratica, non solo valore speculativo.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-oro-fiorentino fas fa-certificate"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Certificazione Accessibile
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Piattaforma di certificazione digitale accessibile per PA, aziende e creativi.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-viola-innovazione fas fa-tools"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Strumento Operativo
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Utilizzabile concretamente da enti pubblici, aziende e professionisti creativi.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-arancio-energia fas fa-book-open"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Valuta Narrativa
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Oggetto semantico che racconta storie e crea connessioni significative.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-rosso-urgenza fas fa-shield-alt"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Standard della Cura
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
                        Per l'arte, per l'ambiente, per il futuro: EGI rappresenta l'eccellenza sostenibile.
                    </p>
                </div>

                <div class="p-6 renaissance-card elegant-hover">
                    <div class="mb-4 text-center">
                        <i class="text-4xl text-blu-algoritmo fas fa-infinity"></i>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-center text-blu-algoritmo renaissance-title">
                        Riconoscibile e Tracciabile
                    </h3>
                    <p class="text-center text-grigio-pietra font-body">
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
                    EGI è lo <span class="text-oro-fiorentino">standard della cura</span>: per l'arte, per l'ambiente, per il futuro.
                </p>
                
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold text-blu-algoritmo transition-all duration-300 rounded-lg cta-primary elegant-hover">
                        <i class="mr-3 fas fa-rocket"></i>
                        Inizia con FlorenceEGI
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="inline-flex items-center px-8 py-4 text-lg font-semibold transition-all duration-300 border-2 border-oro-fiorentino rounded-lg text-oro-fiorentino hover:bg-oro-fiorentino hover:text-blu-algoritmo">
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