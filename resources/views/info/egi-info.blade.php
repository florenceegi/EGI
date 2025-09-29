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
    <header class="bg-blu-algoritmo text-white shadow-lg">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-leaf text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">EGI Info</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">Ecological Goods Invent</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-3 md:flex">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Home</a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">FlorenceEGI</a>
                    <a href="#definizione"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Definizione</a>
                    <a href="#componenti"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Componenti</a>
                    <a href="#funzioni"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Funzioni</a>
                    <a href="#vantaggi"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Vantaggi</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block rounded-md p-2 transition-colors hover:bg-blue-700 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mt-4 hidden border-t border-blue-600 pb-4 md:hidden">
                <div class="space-y-3 pt-4">
                    <a href="{{ route('home') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-home text-oro-fiorentino mr-3 text-lg"></i>
                        Torna alla Home
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-infinity text-oro-fiorentino mr-3 text-lg"></i>
                        FlorenceEGI
                    </a>
                    <a href="#definizione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-book text-oro-fiorentino mr-3 text-lg"></i>
                        Definizione
                    </a>
                    <a href="#componenti"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-puzzle-piece text-oro-fiorentino mr-3 text-lg"></i>
                        Componenti
                    </a>
                    <a href="#funzioni"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-cogs text-oro-fiorentino mr-3 text-lg"></i>
                        Funzioni
                    </a>
                    <a href="#vantaggi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-star text-oro-fiorentino mr-3 text-lg"></i>
                        Vantaggi
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-background">
        <div class="golden-ratio-container flex min-h-[70vh] items-center justify-center px-4">
            <div class="mx-auto max-w-5xl text-center text-white">
                <div class="mb-8">
                    <span class="bg-oro-fiorentino rounded-full px-4 py-2 text-sm font-semibold text-blu-algoritmo">
                        Ecological Goods Invent
                    </span>
                </div>
                <h1 class="renaissance-title mb-6 text-4xl font-bold sm:text-5xl lg:text-7xl">
                    EGI
                </h1>
                <p class="mb-8 font-body text-xl leading-relaxed text-blue-100 sm:text-2xl">
                    Il Futuro degli Asset Digitali Sostenibili
                </p>
                <p class="mb-10 font-body text-lg leading-relaxed text-blue-200">
                    Non è un semplice NFT. È un <strong>oggetto digitale certificato</strong> che unisce
                    valore artistico, impatto ambientale e utilità concreta in un'unica soluzione rivoluzionaria.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#definizione"
                        class="cta-primary elegant-hover inline-flex items-center rounded-lg px-8 py-4 text-lg font-semibold text-white transition-all duration-300">
                        <i class="fas fa-book-open mr-3"></i>
                        Scopri gli EGI
                    </a>
                    <a href="#componenti"
                        class="inline-flex items-center rounded-lg border-2 border-white px-8 py-4 text-lg font-semibold text-blue-100 transition-all duration-300 hover:bg-white hover:text-blu-algoritmo">
                        <i class="fas fa-puzzle-piece mr-3"></i>
                        Le Tre Componenti
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Definizione EGI -->
    <section id="definizione" class="bg-white py-20">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="renaissance-title mb-6 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                    Cos'è un EGI?
                </h2>
                <p class="mb-12 font-body text-xl leading-relaxed text-grigio-pietra">
                    L'EGI è l'unità fondamentale della piattaforma FlorenceEGI, un <strong>oggetto digitale
                        certificato</strong>
                    che rivoluziona il concetto di NFT tradizionale.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="text-oro-fiorentino fas fa-palette text-4xl"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Valore Artistico
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Opere dell'ingegno umano, non generate totalmente da AI, con valore estetico e culturale.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-leaf text-4xl text-verde-rinascita"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Impatto Ambientale
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Legame strutturale con progetti di protezione ambientale (EPP) per un futuro sostenibile.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-cog text-4xl text-viola-innovazione"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Utilità Concreta
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Servizi, funzioni e utility reali che vanno oltre il semplice possesso digitale.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-shield-alt text-4xl text-blu-algoritmo"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
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
    <section id="componenti" class="section-dark py-20">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h2 class="renaissance-title mb-6 text-3xl font-bold text-white sm:text-4xl">
                    Le Tre Componenti del Nostro Asset Digitale
                </h2>
                <p class="font-body text-xl leading-relaxed text-blue-200">
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
                        <h3 class="text-oro-fiorentino renaissance-title mb-4 text-2xl font-bold">
                            Environment Protection Programs
                        </h3>
                        <p class="mb-6 font-body text-lg leading-relaxed text-blue-100">
                            Gli Environment Protection Programs sono iniziative centrali nel progetto FlorenceEGI,
                            rappresentando il cuore dell'impegno dell'associazione verso la sostenibilità ambientale
                            e la rigenerazione ecologica.
                        </p>
                    </div>

                    <div class="renaissance-card rounded-lg bg-white p-8">
                        <h4 class="renaissance-title mb-4 text-xl font-bold text-blu-algoritmo">
                            Programmi Attivi
                        </h4>
                        <div class="space-y-4">
                            <div class="border-l-4 border-viola-innovazione bg-purple-50 p-4">
                                <h5 class="font-semibold text-blu-algoritmo">🌊 Aquatic Plastic Removal</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Rimozione della plastica da vari ambienti acquatici, azione cruciale per la salute
                                    dei nostri mari, laghi e fiumi.
                                </p>
                            </div>
                            <div class="border-l-4 border-verde-rinascita bg-green-50 p-4">
                                <h5 class="font-semibold text-blu-algoritmo">🌳 Appropriate Restoration Forestry</h5>
                                <p class="text-sm text-grigio-pietra">
                                    Riforestazione attenta e rispettosa degli ecosistemi, terramerici che le piante
                                    scelte siano quelle più adatte per ristabilire l'equilibrio naturale.
                                </p>
                            </div>
                            <div class="border-l-4 border-arancio-energia bg-orange-50 p-4">
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
                        <div class="renaissance-card rounded-lg bg-white p-8">
                            <h4 class="renaissance-title mb-4 text-xl font-bold text-blu-algoritmo">
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
                        <h3 class="text-oro-fiorentino renaissance-title mb-4 text-2xl font-bold">
                            Goods, ovvero Beni o Servizi
                        </h3>
                        <p class="font-body text-lg leading-relaxed text-blue-100">
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
                        <h3 class="text-oro-fiorentino renaissance-title mb-4 text-2xl font-bold">
                            Creatività e Inventiva
                        </h3>
                        <p class="font-body text-lg leading-relaxed text-blue-100">
                            Gli EGI incarnano l'essenza dell'arte autentica e originale, rappresentando una
                            rivoluzionaria
                            categoria di asset digitali che celebrano l'unicità e l'autenticità creativa.
                        </p>
                    </div>

                    <div class="renaissance-card rounded-lg bg-white p-8">
                        <h4 class="renaissance-title mb-4 text-xl font-bold text-blu-algoritmo">
                            L'Arte Autentica Digitale
                        </h4>
                        <p class="mb-4 font-body text-grigio-pietra">
                            Gli EGI (Eco Gate Invent) di FlorenceEGI rappresentano una rivoluzionaria categoria di asset
                            digitali
                            che incarnano l'essenza dell'arte autentica e originale.
                        </p>
                        <p class="mb-4 font-body text-grigio-pietra">
                            Ogni EGI è un'opera d'arte digitale che può variare da immagini visive, a composizioni
                            musicali,
                            fino a opere letterarie come ebook. Ciò che distingue gli EGI dai comuni collezionabili
                            digitali
                            è l'impegno verso l'unicità e l'autenticità creativa.
                        </p>
                        <p class="font-body text-grigio-pietra">
                            La nostra filosofia si basa su un principio fondamentale: ogni opera associata agli EGI deve
                            essere
                            frutto dell'ingegno e della creatività umana, rifiutando categoricamente l'uso di opere
                            generate
                            in serie da software.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Struttura Tecnica -->
    <section id="funzioni" class="bg-gray-100 py-20">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h2 class="renaissance-title mb-6 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                    Struttura e Funzioni Avanzate
                </h2>
                <p class="font-body text-xl leading-relaxed text-grigio-pietra">
                    Ogni EGI è un ecosistema completo di funzionalità innovative
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Struttura -->
                <div class="renaissance-card elegant-hover rounded-lg bg-white p-8">
                    <h3 class="renaissance-title mb-6 text-2xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-layer-group mr-3"></i>
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
                <div class="renaissance-card elegant-hover rounded-lg bg-white p-8">
                    <h3 class="renaissance-title mb-6 text-2xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-cogs mr-3"></i>
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

    <!-- Finalità e Vantaggi -->
    <section id="vantaggi" class="bg-white py-20">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h2 class="renaissance-title mb-6 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                    Perché gli EGI Cambiano le Regole
                </h2>
                <p class="font-body text-xl leading-relaxed text-grigio-pietra">
                    Una rivoluzione nel mondo degli asset digitali
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-seedling text-4xl text-verde-rinascita"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        NFT con Utilità Reale
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Ogni EGI ha un impatto ambientale concreto e utilità pratica, non solo valore speculativo.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="text-oro-fiorentino fas fa-certificate text-4xl"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Certificazione Accessibile
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Piattaforma di certificazione digitale accessibile per PA, aziende e creativi.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-tools text-4xl text-viola-innovazione"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Strumento Operativo
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Utilizzabile concretamente da enti pubblici, aziende e professionisti creativi.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-book-open text-4xl text-arancio-energia"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Valuta Narrativa
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Oggetto semantico che racconta storie e crea connessioni significative.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-shield-alt text-4xl text-rosso-urgenza"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
                        Standard della Cura
                    </h3>
                    <p class="text-center font-body text-grigio-pietra">
                        Per l'arte, per l'ambiente, per il futuro: EGI rappresenta l'eccellenza sostenibile.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-6">
                    <div class="mb-4 text-center">
                        <i class="fas fa-infinity text-4xl text-blu-algoritmo"></i>
                    </div>
                    <h3 class="renaissance-title mb-3 text-center text-lg font-bold text-blu-algoritmo">
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
    <section class="section-dark py-20">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-oro-fiorentino renaissance-title mb-6 text-3xl font-bold sm:text-4xl">
                    La Visione: "Fanne un EGI"
                </h2>
                <p class="mb-8 font-body text-xl leading-relaxed text-blue-100">
                    Significa rendere qualcosa <strong>riconoscibile, tracciabile e significativo</strong>.<br>
                    Un oggetto, un accordo, un'opera, un'intenzione: tutto può diventare un EGI.
                </p>
                <p class="renaissance-title mb-12 text-2xl font-bold text-white">
                    EGI è lo <span class="text-oro-fiorentino">standard della cura</span>: per l'arte, per l'ambiente,
                    per il futuro.
                </p>

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('home') }}"
                        class="cta-primary elegant-hover inline-flex items-center rounded-lg px-8 py-4 text-lg font-semibold text-blu-algoritmo transition-all duration-300">
                        <i class="fas fa-rocket mr-3"></i>
                        Inizia con FlorenceEGI
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino inline-flex items-center rounded-lg border-2 px-8 py-4 text-lg font-semibold transition-all duration-300 hover:text-blu-algoritmo">
                        <i class="fas fa-info-circle mr-3"></i>
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
