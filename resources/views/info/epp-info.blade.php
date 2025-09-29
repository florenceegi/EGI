{{--
    File: epp-info.blade.php
    Versione: 1.0 EPP Istituzionale
    Data: 29 Settembre 2025
    Descrizione: Pagina istituzionale Environment Protection Programs con stile PA e colori impatto ambientale
    Caratteristiche: Layout formale, credibilità istituzionale, focus conservazione ambientale
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environment Protection Programs | FlorenceEGI</title>
    <meta name="description"
        content="Programmi di protezione ambientale istituzionali: Appropriate Restoration Forestry, Aquatic Plastic Removal, Bee Population Enhancement. Impatto concreto per la rigenerazione ecosistemica.">

    <!-- Google Fonts - Professional -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Environmental Impact Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'forest-green': '#1B5E20',
                        'forest-light': '#2E7D32',
                        'ocean-blue': '#0277BD',
                        'ocean-light': '#0288D1',
                        'bee-amber': '#FF8F00',
                        'bee-light': '#FFA000',
                        'earth-brown': '#5D4037',
                        'institutional-navy': '#1A237E',
                        'conservation-teal': '#00695C',
                        'ecosystem-gray': '#455A64',
                        'impact-orange': '#E65100'
                    },
                    fontFamily: {
                        'institutional': ['Inter', 'sans-serif'],
                        'heading': ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .institutional-title {
            font-family: 'Playfair Display', serif;
        }

        /* Professional Layout */
        .institutional-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Professional Cards */
        .institutional-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(203, 213, 225, 0.5);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .institutional-card:hover {
            border-color: rgba(26, 35, 126, 0.3);
            box-shadow: 0 10px 40px rgba(26, 35, 126, 0.1);
        }

        /* Environmental Impact Sections */
        .forest-section {
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.05) 0%, rgba(46, 125, 50, 0.05) 100%);
            border-left: 4px solid #1B5E20;
        }

        .ocean-section {
            background: linear-gradient(135deg, rgba(2, 119, 189, 0.05) 0%, rgba(2, 136, 209, 0.05) 100%);
            border-left: 4px solid #0277BD;
        }

        .bee-section {
            background: linear-gradient(135deg, rgba(255, 143, 0, 0.05) 0%, rgba(255, 160, 0, 0.05) 100%);
            border-left: 4px solid #FF8F00;
        }

        /* Hero Environmental Background */
        .hero-environmental {
            background: linear-gradient(135deg, rgba(26, 35, 126, 0.9) 0%, rgba(0, 105, 92, 0.85) 100%),
                linear-gradient(45deg, rgba(27, 94, 32, 0.3) 0%, rgba(2, 119, 189, 0.3) 100%);
            min-height: 70vh;
        }

        /* Statistics Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 8px;
            padding: 2px;
            background: linear-gradient(135deg, #1B5E20, #0277BD, #FF8F00);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: exclude;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
        }

        /* Impact Metrics */
        .impact-metric {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1B5E20 0%, #0277BD 50%, #FF8F00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="bg-gray-50 pt-20 text-gray-900">

    <!-- Header Istituzionale - Fixed -->
    <header class="bg-institutional-navy fixed left-0 right-0 top-0 z-50 text-white shadow-lg">
        <div class="institutional-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div
                        class="bg-conservation-teal flex h-12 w-12 items-center justify-center rounded-full text-white sm:h-16 sm:w-16">
                        <i class="fas fa-leaf text-2xl sm:text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="institutional-title text-xl font-bold sm:text-2xl">Environment Protection Programs
                        </h1>
                        <p class="font-institutional text-sm text-blue-200 sm:text-base">FlorenceEGI Impatto Ambientale
                        </p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-4 md:flex">
                    <a href="{{ route('home') }}"
                        class="font-institutional text-sm transition hover:text-blue-200 lg:text-base">Home</a>
                    <a href="#programmi"
                        class="font-institutional text-sm transition hover:text-blue-200 lg:text-base">Programmi</a>
                    <a href="#impatto"
                        class="font-institutional text-sm transition hover:text-blue-200 lg:text-base">Impatto</a>
                    <a href="#iniziative"
                        class="font-institutional text-sm transition hover:text-blue-200 lg:text-base">Iniziative</a>
                    <a href="#partecipazione"
                        class="font-institutional text-sm transition hover:text-blue-200 lg:text-base">Partecipazione</a>
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
                        <i class="fas fa-home text-conservation-teal mr-3 text-lg"></i>
                        Torna alla Home
                    </a>
                    <a href="#programmi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-tree text-conservation-teal mr-3 text-lg"></i>
                        Programmi
                    </a>
                    <a href="#impatto"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-chart-line text-conservation-teal mr-3 text-lg"></i>
                        Impatto
                    </a>
                    <a href="#iniziative"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-lightbulb text-conservation-teal mr-3 text-lg"></i>
                        Iniziative
                    </a>
                    <a href="#partecipazione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-handshake text-conservation-teal mr-3 text-lg"></i>
                        Partecipazione
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-environmental">
        <div class="institutional-container flex min-h-[70vh] items-center justify-center px-4">
            <div class="mx-auto max-w-5xl text-center text-white">
                <div class="mb-8">
                    <div
                        class="mb-6 inline-flex items-center rounded-full border border-white border-opacity-30 bg-white bg-opacity-10 px-6 py-3 text-sm font-medium text-white backdrop-blur-sm">
                        <i class="fas fa-globe-americas mr-2"></i>
                        Programmi di Protezione Ambientale Istituzionali
                    </div>
                </div>
                <h1 class="institutional-title mb-8 text-4xl font-bold sm:text-5xl lg:text-6xl">
                    Il Nostro Impegno per<br>
                    <span
                        class="bg-gradient-to-r from-green-300 via-blue-300 to-yellow-300 bg-clip-text text-transparent">
                        il Ripristino dell'Ecosistema
                    </span>
                </h1>
                <p class="font-institutional mb-10 text-lg leading-relaxed text-blue-100 sm:text-xl lg:text-2xl">
                    Tre iniziative concrete per la rigenerazione ambientale: riforestazione appropriata,
                    rimozione plastica acquatica e potenziamento popolazioni di api.
                    <strong>Impatto misurabile, risultati verificabili.</strong>
                </p>

                <!-- Impact Statistics -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="stat-card rounded-lg p-6">
                        <div class="impact-metric">3</div>
                        <p class="text-sm font-medium uppercase tracking-wide text-gray-600">Aree di Intervento</p>
                    </div>
                    <div class="stat-card rounded-lg p-6">
                        <div class="impact-metric">∞</div>
                        <p class="text-sm font-medium uppercase tracking-wide text-gray-600">Impatto Ecosistemico</p>
                    </div>
                    <div class="stat-card rounded-lg p-6">
                        <div class="impact-metric">20%</div>
                        <p class="text-sm font-medium uppercase tracking-wide text-gray-600">Contributo EGI Minimo</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Le Nostre Tre Aree di Intervento -->
    <section id="programmi" class="py-20">
        <div class="institutional-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h2 class="text-institutional-navy institutional-title mb-6 text-3xl font-bold sm:text-4xl">
                    Le Nostre Tre Aree di Intervento
                </h2>
                <p class="text-ecosystem-gray font-institutional text-lg leading-relaxed">
                    Ogni Environment Protection Program rappresenta un pilastro strategico per la conservazione
                    e il ripristino degli equilibri naturali del nostro pianeta.
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">

                <!-- Appropriate Restoration Forestry -->
                <div class="institutional-card forest-section">
                    <div class="p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-forest-green flex h-16 w-16 items-center justify-center rounded-full text-white">
                                <i class="fas fa-tree text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-forest-green institutional-title text-xl font-bold">
                                    Appropriate Restoration Forestry
                                </h3>
                                <p class="text-sm text-gray-600">Riforestazione Responsabile</p>
                            </div>
                        </div>
                        <p class="font-institutional mb-6 leading-relaxed text-gray-700">
                            La deforestazione sta erodendo il polmone verde del nostro pianeta ad un ritmo allarmante.
                            Ogni secondo che passa, perdiamo aree di foresta vitale, equivalenti alla dimensione di un
                            campo di calcio.
                        </p>
                        <div class="border-forest-green mb-6 rounded-lg border border-opacity-20 bg-white p-4">
                            <h4 class="text-forest-green mb-2 font-semibold">Obiettivi del Programma:</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <li><i class="text-forest-green fas fa-check mr-2"></i>Rigenerare foreste degradate</li>
                                <li><i class="text-forest-green fas fa-check mr-2"></i>Promuovere pratiche di
                                    silvicoltura sostenibili</li>
                                <li><i class="text-forest-green fas fa-check mr-2"></i>Ripristinare ecosistemi forestali
                                </li>
                                <li><i class="text-forest-green fas fa-check mr-2"></i>Conservare biodiversità nativa
                                </li>
                            </ul>
                        </div>
                        <a href="#forestry-details"
                            class="bg-forest-green hover:bg-forest-light inline-flex items-center rounded-lg px-6 py-3 text-sm font-medium text-white transition-all duration-300">
                            Scopri di Più
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Aquatic Plastic Removal -->
                <div class="institutional-card ocean-section">
                    <div class="p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-ocean-blue flex h-16 w-16 items-center justify-center rounded-full text-white">
                                <i class="fas fa-water text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-ocean-blue institutional-title text-xl font-bold">
                                    Aquatic Plastic Removal
                                </h3>
                                <p class="text-sm text-gray-600">Rimozione Plastica Marina</p>
                            </div>
                        </div>
                        <p class="font-institutional mb-6 leading-relaxed text-gray-700">
                            Se unisse tutte le isole di plastica che oggi galleggiano nei nostri oceani,
                            la loro estensione sfiderebbe quella di un continente. È tempo di affrontare con coraggio
                            questa crisi globale!
                        </p>
                        <div class="border-ocean-blue mb-6 rounded-lg border border-opacity-20 bg-white p-4">
                            <h4 class="text-ocean-blue mb-2 font-semibold">Strategia di Intervento:</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <li><i class="text-ocean-blue fas fa-check mr-2"></i>Tecnologie innovative di raccolta
                                </li>
                                <li><i class="text-ocean-blue fas fa-check mr-2"></i>Metodi efficaci di riciclo</li>
                                <li><i class="text-ocean-blue fas fa-check mr-2"></i>Riduzione materiale inquinante
                                </li>
                                <li><i class="text-ocean-blue fas fa-check mr-2"></i>Protezione ecosistemi marini</li>
                            </ul>
                        </div>
                        <a href="#plastic-details"
                            class="bg-ocean-blue hover:bg-ocean-light inline-flex items-center rounded-lg px-6 py-3 text-sm font-medium text-white transition-all duration-300">
                            Scopri di Più
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Bee Population Enhancement -->
                <div class="institutional-card bee-section">
                    <div class="p-8">
                        <div class="mb-6 flex items-center">
                            <div
                                class="bg-bee-amber flex h-16 w-16 items-center justify-center rounded-full text-white">
                                <i class="fas fa-spa text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-bee-amber institutional-title text-xl font-bold">
                                    Bee Population Enhancement
                                </h3>
                                <p class="text-sm text-gray-600">Potenziamento Popolazioni Api</p>
                            </div>
                        </div>
                        <p class="font-institutional mb-6 leading-relaxed text-gray-700">
                            Le api, architetti silenziosi della biodiversità del nostro pianeta, sono in un allarmante
                            declino.
                            Un terzo del nostro cibo dipende dalla loro esistenza!
                        </p>
                        <div class="border-bee-amber mb-6 rounded-lg border border-opacity-20 bg-white p-4">
                            <h4 class="text-bee-amber mb-2 font-semibold">Iniziative Concrete:</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <li><i class="text-bee-amber fas fa-check mr-2"></i>Conservazione habitat naturali</li>
                                <li><i class="text-bee-amber fas fa-check mr-2"></i>Pratiche agricole sostenibili</li>
                                <li><i class="text-bee-amber fas fa-check mr-2"></i>Riduzione pesticidi nocivi</li>
                                <li><i class="text-bee-amber fas fa-check mr-2"></i>Promozione biodiversità locale</li>
                            </ul>
                        </div>
                        <a href="#bee-details"
                            class="bg-bee-amber hover:bg-bee-light inline-flex items-center rounded-lg px-6 py-3 text-sm font-medium text-white transition-all duration-300">
                            Scopri di Più
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Impatto Dettagliato -->
    <section id="impatto" class="bg-gray-100 py-20">
        <div class="institutional-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h2 class="text-institutional-navy institutional-title mb-6 text-3xl font-bold sm:text-4xl">
                    Impatto Misurabile e Verificabile
                </h2>
                <p class="text-ecosystem-gray font-institutional text-lg leading-relaxed">
                    Ogni contributo degli EGI genera risultati concreti e documentati per la rigenerazione ambientale
                    globale.
                </p>
            </div>

            <!-- Forestry Details -->
            <div id="forestry-details" class="mb-16">
                <div class="institutional-card rounded-xl bg-white p-8 shadow-lg">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="mb-6 flex items-center">
                                <div class="bg-forest-green mr-4 h-12 w-3 rounded"></div>
                                <h3 class="text-forest-green institutional-title text-2xl font-bold">
                                    Appropriate Restoration Forestry
                                </h3>
                            </div>
                            <div class="font-institutional space-y-4 text-gray-700">
                                <p>
                                    <strong>La deforestazione e il degrado degli ecosistemi forestali</strong>
                                    rappresentano una delle maggiori sfide
                                    ambientali del nostro tempo. La perdita di foreste non solo minaccia la
                                    biodiversità, ma contribuisce anche
                                    al cambiamento climatico e alla diminuzione delle risorse naturali.
                                </p>
                                <p>
                                    In questo contesto, il programma <strong>"Appropriate Restoration Forestry"</strong>
                                    assume un'importanza critica.
                                    L'obiettivo del programma è duplice: rigenerare le foreste degradate e promuovere
                                    pratiche di silvicoltura sostenibili.
                                </p>
                                <p>
                                    Attraverso il sostegno finanziario a progetti di riforestazione e di gestione
                                    forestale responsabile,
                                    ci impegniamo a ripristinare e conservare gli ecosistemi forestali.
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="bg-forest-green rounded bg-opacity-5 p-6">
                                <p class="font-institutional text-gray-700">
                                    <strong>Questo approccio non solo aiuta a recuperare aree precedentemente
                                        deforestate</strong>, ma assicura
                                    anche che le nuove piantumazioni vengano gestite in modo da preservare la
                                    biodiversità e
                                    migliorare la resilienza degli ecosistemi.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    <strong>Appropriate Restoration Forestry</strong> non si limita solo alla
                                    piantumazione di alberi;
                                    sosteniamo anche a ricerca e l'adozione di tecniche di silvicoltura che rispettino
                                    l'equilibrio naturale e
                                    che favoriscano la salute a lungo termine delle foreste.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    Con questo programma, ci impegniamo a creare un futuro in cui le foreste possano
                                    essere fonte di vita, diversità e sostenibilità per le generazioni a venire.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plastic Removal Details -->
            <div id="plastic-details" class="mb-16">
                <div class="institutional-card rounded-xl bg-white p-8 shadow-lg">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="mb-6 flex items-center">
                                <div class="bg-ocean-blue mr-4 h-12 w-3 rounded"></div>
                                <h3 class="text-ocean-blue institutional-title text-2xl font-bold">
                                    Aquatic Plastic Removal
                                </h3>
                            </div>
                            <div class="font-institutional space-y-4 text-gray-700">
                                <p>
                                    <strong>L'urgenza di affrontare l'inquinamento da plastica nei nostri
                                        oceani</strong> è evidenziata dai dati allarmanti:
                                    ogni anno, 8 milioni di tonnellate di plastica vengono riversate nelle acque marine,
                                    con gravi
                                    conseguenze per l'ambiente e la biodiversità.
                                </p>
                                <p>
                                    Di fronte a questa crisi ambientale, <strong>Aquatic Plastic Removal</strong> si
                                    impegna non solo a sensibilizzare, ma
                                    anche a fornire un sostegno economico concreto alle iniziative dedicate alla pulizia
                                    e al riciclaggio dei
                                    rifiuti plastici nei mari.
                                </p>
                                <p>
                                    Il nostro impegno finanziario si traduce in supporto alle organizzazioni e ai
                                    progetti che lavorano
                                    attivamente per ridurre l'inquinamento marino.
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="bg-ocean-blue rounded bg-opacity-5 p-6">
                                <p class="font-institutional text-gray-700">
                                    <strong>Attraverso il finanziamento di tecnologie innovative</strong> e metodi
                                    efficaci di raccolta e riciclo
                                    della plastica, contribuiamo direttamente alla diminuzione del materiale inquinante
                                    negli oceani.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    <strong>La nostra visione è quella di un futuro</strong> in cui gli oceani siano
                                    liberi dalla minaccia plastica, e ogni
                                    contributo finanziario che forniamo è un passo verso la realizzazione di questo
                                    obiettivo.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    Con <strong>Aquatic Plastic Removal</strong>, ci impegniamo a trasformare le risorse
                                    economiche in azioni
                                    concrete per un impatto ambientale positivo e duraturo, proteggendo i nostri mari
                                    per le
                                    generazioni presenti e future.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bee Enhancement Details -->
            <div id="bee-details">
                <div class="institutional-card rounded-xl bg-white p-8 shadow-lg">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <div class="mb-6 flex items-center">
                                <div class="bg-bee-amber mr-4 h-12 w-3 rounded"></div>
                                <h3 class="text-bee-amber institutional-title text-2xl font-bold">
                                    Bee Population Enhancement
                                </h3>
                            </div>
                            <div class="font-institutional space-y-4 text-gray-700">
                                <p>
                                    <strong>La diminuzione della popolazione di api in tutto il mondo</strong> è
                                    un'allarmante realtà che minaccia la
                                    biodiversità e la stabilità degli ecosistemi. Circa un terzo delle colture
                                    alimentari dipende
                                    dall'impollinazione, principalmente da api, rendendo la loro presenza cruciale per
                                    la sicurezza alimentare globale.
                                </p>
                                <p>
                                    Di fronte a questo scenario preoccupante, <strong>il programma Bee Population
                                        Enhancement</strong> gioca
                                    un ruolo vitale. Attraverso il programma Bee Population Enhancement, miriamo a
                                    invertire il declino delle
                                    api, sostenendo attivamente soluzioni innovative e pratiche sostenibili.
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="bg-bee-amber rounded bg-opacity-5 p-6">
                                <p class="font-institutional text-gray-700">
                                    <strong>Il nostro obiettivo è quello di creare un ambiente</strong> in cui le api
                                    possano prosperare, contribuendo
                                    così alla salute degli ecosistemi e alla sicurezza alimentare.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    <strong>Riconosciamo che proteggere le api significa proteggere il nostro
                                        futuro</strong>, e ci impegniamo a
                                    fare la nostra parte per assicurare la loro sopravvivenza e prosperità.
                                </p>
                                <br>
                                <p class="font-institutional text-gray-700">
                                    Il nostro impegno si concentra sul sostegno economico a progetti e ricerche mirate
                                    al
                                    miglioramento della salute e della popolazione delle api. <strong>Finanziamo
                                        iniziative che vanno dalla
                                        conservazione degli habitat naturali delle api</strong>, al sostegno di pratiche
                                    agricole sostenibili che
                                    promuovono la biodiversità e riducono l'uso di pesticidi nocivi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Come Partecipare -->
    <section id="partecipazione" class="py-20">
        <div class="institutional-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <div class="institutional-card rounded-xl bg-white p-8 text-center shadow-lg">
                    <div class="mb-8">
                        <div
                            class="bg-conservation-teal mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full text-white">
                            <i class="fas fa-handshake text-3xl"></i>
                        </div>
                        <h2 class="text-institutional-navy institutional-title mb-4 text-3xl font-bold">
                            Come Contribuire agli EPP
                        </h2>
                        <p class="text-ecosystem-gray font-institutional text-lg">
                            Ogni EGI creato sulla piattaforma FlorenceEGI destina automaticamente
                            <strong>il 20% del valore di vendita</strong> ai programmi EPP selezionati.
                        </p>
                    </div>

                    <div class="mb-8 grid gap-6 md:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 p-6">
                            <i class="text-forest-green fas fa-plus-circle mb-4 text-3xl"></i>
                            <h3 class="text-institutional-navy mb-2 font-semibold">Crea un EGI</h3>
                            <p class="font-institutional text-sm text-gray-600">
                                Ogni EGI contribuisce automaticamente alla rigenerazione ambientale
                            </p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-6">
                            <i class="text-ocean-blue fas fa-heart mb-4 text-3xl"></i>
                            <h3 class="text-institutional-navy mb-2 font-semibold">Scegli il Programma</h3>
                            <p class="font-institutional text-sm text-gray-600">
                                Seleziona quale EPP sostenere con il tuo contributo
                            </p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-6">
                            <i class="text-bee-amber fas fa-chart-line mb-4 text-3xl"></i>
                            <h3 class="text-institutional-navy mb-2 font-semibold">Monitora l'Impatto</h3>
                            <p class="font-institutional text-sm text-gray-600">
                                Ricevi aggiornamenti sui risultati concreti ottenuti
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 sm:flex sm:justify-center sm:space-x-4 sm:space-y-0">
                        <a href="{{ route('home') }}"
                            class="bg-conservation-teal inline-flex items-center rounded-lg px-8 py-4 text-white transition-all duration-300 hover:bg-opacity-90">
                            <i class="fas fa-rocket mr-2"></i>
                            Inizia Ora con un EGI
                        </a>
                        <a href="{{ route('info.florence-egi') }}"
                            class="text-conservation-teal border-conservation-teal hover:bg-conservation-teal inline-flex items-center rounded-lg border-2 px-8 py-4 transition-all duration-300 hover:text-white">
                            <i class="fas fa-info-circle mr-2"></i>
                            Scopri FlorenceEGI
                        </a>
                    </div>
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
