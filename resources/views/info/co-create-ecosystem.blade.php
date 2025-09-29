{{--
    File: co-create-ecosystem.blade.php
    Versione: 1.0 FlorenceEGI Co-Creation Ecosystem
    Data: 29 Settembre 2025
    Descrizione: Pagina unificata Co-Creare, Co-Creatore, Trader Pro
    Caratteristiche: Brand Guidelines, sezioni strutturate, navbar info standard
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecosistema Co-Creazione - FlorenceEGI</title>
    <meta name="description"
        content="Scopri l'ecosistema completo FlorenceEGI: Co-Creare opere d'arte, Co-Creatori permanenti e strumenti Trader Pro per il trading professionale di EGI.">

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
            background: linear-gradient(135deg, rgba(27, 54, 93, 0.95) 0%, rgba(45, 80, 22, 0.85) 100%);
            min-height: 50vh;
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

        /* Sezioni alternate */
        .section-alt {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Co-Create gradient */
        .co-create-gradient {
            background: linear-gradient(135deg, #8E44AD 0%, #9B59B6 100%);
        }

        /* Co-Creator gradient */
        .co-creator-gradient {
            background: linear-gradient(135deg, #E67E22 0%, #F39C12 100%);
        }

        /* Trader Pro gradient */
        .trader-gradient {
            background: linear-gradient(135deg, #C13120 0%, #E74C3C 100%);
        }

        /* Professional highlight boxes */
        .highlight-box {
            background: rgba(212, 165, 116, 0.1);
            border-left: 4px solid #D4A574;
            padding: 1.5rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }

        /* Section Navigation Styles */
        .section-nav-link,
        .section-nav-link-mobile {
            color: #6B6B6B;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .section-nav-link:hover {
            transform: translateY(-1px);
        }

        /* Scrollbar nascosta per desktop */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Desktop section nav - scroll orizzontale fluido */
        #desktop-section-nav {
            scroll-behavior: smooth;
        }

        /* Mobile sections menu animation */
        #mobile-sections-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        #mobile-sections-menu.show {
            max-height: 300px;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Section spacing for fixed header + sticky navbar */
        section[id] {
            scroll-margin-top: 140px;
            /* Fixed header (80px) + sticky nav (60px) */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            section[id] {
                scroll-margin-top: 160px;
                /* More space for mobile */
            }
        }
    </style>
</head>

<body class="bg-gray-50 pt-20 text-grigio-pietra">

    <!-- Header con Navigazione - Fixed -->
    <header class="fixed left-0 right-0 top-0 z-50 bg-blu-algoritmo text-white shadow-lg">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-infinity text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">FlorenceEGI</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">Il Rinascimento Digitale</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-3 md:flex">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Home</a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">FlorenceEGI</a>
                    <a href="{{ route('info.egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">EGI</a>
                    <a href="{{ route('info.epp') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">EPP</a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Patron</a>
                    <a href="{{ route('gdpr.privacy-policy') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Privacy</a>
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
                        Home
                    </a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-infinity text-oro-fiorentino mr-3 text-lg"></i>
                        FlorenceEGI
                    </a>
                    <a href="{{ route('info.egi') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-gem text-oro-fiorentino mr-3 text-lg"></i>
                        EGI
                    </a>
                    <a href="{{ route('info.epp') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-leaf text-oro-fiorentino mr-3 text-lg"></i>
                        EPP
                    </a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-crown text-oro-fiorentino mr-3 text-lg"></i>
                        Patron
                    </a>
                    <a href="{{ route('gdpr.privacy-policy') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-shield-alt text-oro-fiorentino mr-3 text-lg"></i>
                        Privacy
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-background">
        <div class="golden-ratio-container flex min-h-[50vh] items-center justify-center px-4">
            <div class="mx-auto max-w-4xl text-center text-white">
                <h1 class="renaissance-title mb-6 text-4xl font-bold sm:text-5xl lg:text-6xl">
                    L'Ecosistema della Co-Creazione
                </h1>
                <p class="mb-8 font-body text-lg leading-relaxed text-blue-100 sm:text-xl">
                    Dove ogni partecipante diventa protagonista dell'arte digitale e dell'innovazione sostenibile
                </p>
                <div class="flex flex-wrap justify-center gap-4 font-body text-sm">
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-palette text-oro-fiorentino mr-2"></i>
                        Co-Creare
                    </div>
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-users text-oro-fiorentino mr-2"></i>
                        Co-Creatori
                    </div>
                    <div class="flex items-center rounded-full bg-white bg-opacity-20 px-4 py-2">
                        <i class="fas fa-chart-line text-oro-fiorentino mr-2"></i>
                        Trader Pro
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navbar Interna delle Sezioni - Sticky & Responsive -->
    <nav class="sticky top-20 z-40 border-b border-gray-200 bg-white shadow-sm">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">

            <!-- Desktop: Scroll orizzontale -->
            <div class="hidden py-4 md:block">
                <div class="flex justify-center">
                    <div class="scrollbar-hide flex max-w-full space-x-2 overflow-x-auto px-4" id="desktop-section-nav">

                        <!-- Co-Creare -->
                        <a href="#co-creare"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-purple-200 hover:bg-purple-50 hover:text-purple-700"
                            data-section="co-creare">
                            <i class="fas fa-palette mr-2 text-purple-600"></i>
                            Co-Creare
                        </a>

                        <!-- Co-Creatore -->
                        <a href="#co-creatore"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700"
                            data-section="co-creatore">
                            <i class="fas fa-users mr-2 text-orange-600"></i>
                            Co-Creatore
                        </a>

                        <!-- Trader Pro -->
                        <a href="#trader-pro"
                            class="section-nav-link flex-shrink-0 rounded-lg border border-transparent px-4 py-2 text-sm font-medium transition-all duration-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                            data-section="trader-pro">
                            <i class="fas fa-chart-line mr-2 text-red-600"></i>
                            Trader Pro
                        </a>

                    </div>
                </div>
            </div>

            <!-- Mobile: Menu Hamburger -->
            <div class="block py-3 md:hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-list text-oro-fiorentino mr-2 text-lg"></i>
                        <span class="text-sm font-medium text-grigio-pietra">Sezioni Ecosistema</span>
                    </div>
                    <button id="mobile-sections-toggle"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition-colors hover:bg-gray-200">
                        <i class="fas fa-chevron-down text-sm text-grigio-pietra" id="mobile-sections-icon"></i>
                    </button>
                </div>

                <!-- Mobile Dropdown Menu -->
                <div id="mobile-sections-menu" class="mt-3 hidden space-y-1">

                    <!-- Co-Creare -->
                    <a href="#co-creare"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-purple-50 hover:text-purple-700"
                        data-section="co-creare">
                        <i class="fas fa-palette mr-3 text-purple-600"></i>
                        Co-Creare
                    </a>

                    <!-- Co-Creatore -->
                    <a href="#co-creatore"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-orange-50 hover:text-orange-700"
                        data-section="co-creatore">
                        <i class="fas fa-users mr-3 text-orange-600"></i>
                        Co-Creatore
                    </a>

                    <!-- Trader Pro -->
                    <a href="#trader-pro"
                        class="section-nav-link-mobile flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 hover:bg-red-50 hover:text-red-700"
                        data-section="trader-pro">
                        <i class="fas fa-chart-line mr-3 text-red-600"></i>
                        Trader Pro
                    </a>

                </div>
            </div>

        </div>
    </nav>

    <!-- Sezione Co-Creare -->
    <section id="co-creare" class="py-16">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="co-create-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-palette text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        Il Concetto di Co-Creare
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        Partecipa attivamente alla nascita pubblica di un'opera d'arte
                    </p>
                </div>

                <!-- Contenuto Co-Creare -->
                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="renaissance-card elegant-hover p-8">
                        <h3 class="renaissance-title mb-4 text-xl font-semibold text-blu-algoritmo">
                            L'Atto della Creazione Condivisa
                        </h3>
                        <p class="mb-4 font-body leading-relaxed text-grigio-pietra">
                            Co-creare in FlorenceEGI significa partecipare al momento più magico dell'arte digitale:
                            il <strong>primo acquisto</strong>. Non si tratta semplicemente di una transazione
                            commerciale,
                            ma dell'<strong>atto fondativo</strong> che trasforma un'opera privata in un bene culturale
                            pubblico.
                        </p>
                        <div class="highlight-box">
                            <p class="font-semibold text-blu-algoritmo">
                                <i class="fas fa-lightbulb text-oro-fiorentino mr-2"></i>
                                Il primo acquisto è l'atto che rende "viva e pubblica" l'opera d'arte
                            </p>
                        </div>
                    </div>

                    <div class="renaissance-card elegant-hover p-8">
                        <h3 class="renaissance-title mb-4 text-xl font-semibold text-blu-algoritmo">
                            Dal Privato al Pubblico
                        </h3>
                        <p class="mb-4 font-body leading-relaxed text-grigio-pietra">
                            Attraverso il <strong>minting</strong> e il primo acquisto, l'opera abbandona la dimensione
                            privata dello studio dell'artista per entrare nella sfera pubblica della cultura condivisa.
                            Il Co-creatore non acquisisce solo un'opera, ma diventa <strong>parte integrante della sua
                                storia</strong>.
                        </p>
                        <ul class="space-y-2 font-body text-sm text-grigio-pietra">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                Partecipazione attiva al processo creativo
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                Contributo alla visibilità dell'artista
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-2 text-verde-rinascita"></i>
                                Sostegno diretto ai progetti ambientali EPP
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="renaissance-card mt-8 p-8 text-center">
                    <h3 class="renaissance-title mb-4 text-2xl font-bold text-blu-algoritmo">
                        L'Opportunità Unica
                    </h3>
                    <p class="mx-auto max-w-3xl font-body text-lg leading-relaxed text-grigio-pietra">
                        Anche se l'artista ha materialmente creato l'opera, <strong>il primo acquisto è l'atto che la
                            rende viva</strong>
                        nella dimensione digitale e culturale globale. Chi co-crea non solo possiede un'opera d'arte
                        unica,
                        ma diventa <strong>custode della sua prima esistenza pubblica</strong>, partecipando così al
                        miracolo
                        della trasformazione artistica.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Co-Creatore -->
    <section id="co-creatore" class="section-alt py-16">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="co-creator-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        La Figura del Co-Creatore
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        Un legame permanente con l'opera e visibilità mondiale garantita
                    </p>
                </div>

                <!-- Contenuto Co-Creatore -->
                <div class="grid gap-8 lg:grid-cols-3">
                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-infinity text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            Legame Permanente
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            Il Co-creatore rimane <strong>per sempre unito all'EGI</strong>, insieme al Creator
                            originale.
                            Questo legame indissolubile garantisce il riconoscimento perpetuo del suo ruolo fondativo.
                        </p>
                    </div>

                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-globe text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            Visibilità Mondiale
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            Attraverso la registrazione e il profilo personalizzato, il Co-creatore ottiene
                            <strong>massima visibilità in tutto il mondo</strong> e riconoscimento globale del suo
                            contributo.
                        </p>
                    </div>

                    <div class="renaissance-card elegant-hover p-6">
                        <div class="mb-4">
                            <i class="fas fa-user-edit text-oro-fiorentino text-3xl"></i>
                        </div>
                        <h3 class="renaissance-title mb-3 text-lg font-semibold text-blu-algoritmo">
                            Identità Personalizzata
                        </h3>
                        <p class="font-body text-grigio-pietra">
                            Libertà completa nella scelta dell'identità: <strong>nickname, nome d'arte,
                                nome completo</strong> e immagine personale per massimo riconoscimento.
                        </p>
                    </div>
                </div>

                <div class="renaissance-card mt-8 p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        Il Profilo del Co-Creatore
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">Personalizzazione Completa</h4>
                            <ul class="space-y-2 font-body text-grigio-pietra">
                                <li class="flex items-center">
                                    <i class="fas fa-tag mr-3 text-verde-rinascita"></i>
                                    <span><strong>Nickname personalizzato</strong> a scelta libera</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-id-card mr-3 text-verde-rinascita"></i>
                                    <span><strong>Nome e cognome reali</strong> o nome d'arte</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-camera mr-3 text-verde-rinascita"></i>
                                    <span><strong>Immagine profilo</strong> caricabile nell'area personale</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">Vantaggi Esclusivi</h4>
                            <ul class="space-y-2 font-body text-grigio-pietra">
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-crown mr-3"></i>
                                    <span><strong>Riconoscimento perpetuo</strong> legato all'opera</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-trophy mr-3"></i>
                                    <span><strong>Status esclusivo</strong> di primo acquirente</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="text-oro-fiorentino fas fa-network-wired mr-3"></i>
                                    <span><strong>Visibilità globale</strong> nell'ecosistema FlorenceEGI</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="from-oro-fiorentino mt-6 rounded-lg bg-gradient-to-r to-yellow-400 p-6 text-center">
                        <p class="text-lg font-semibold text-white">
                            <i class="fas fa-star mr-2"></i>
                            Il Co-creatore non è solo un acquirente: è il <strong>co-fondatore dell'esistenza
                                pubblica</strong> dell'opera d'arte
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Sezione Trader Pro -->
    <section id="trader-pro" class="py-16">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">

                <!-- Header Sezione -->
                <div class="mb-12 text-center">
                    <div
                        class="trader-gradient mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full text-white">
                        <i class="fas fa-chart-line text-3xl"></i>
                    </div>
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-blu-algoritmo sm:text-4xl">
                        Trader Pro: Strategia Duplice
                    </h2>
                    <p class="font-body text-lg text-grigio-pietra">
                        Strumenti professionali per trader e marketing rivoluzionario per creator
                    </p>
                </div>

                <!-- La Strategia Duplice -->
                <div class="renaissance-card mb-8 p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        La Visione Strategica
                    </h3>
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div class="border-l-4 border-verde-rinascita bg-green-50 p-6">
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">
                                <i class="fas fa-tools mr-2"></i>
                                Per i Trader Professionali
                            </h4>
                            <p class="font-body text-grigio-pietra">
                                Forniamo i <strong>massimi strumenti di trading</strong> a chi fa del trading la propria
                                attività principale.
                                EGI PT offre opportunità uniche per chi comprende il mercato dell'arte digitale.
                            </p>
                        </div>
                        <div class="border-l-4 border-arancio-energia bg-orange-50 p-6">
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">
                                <i class="fas fa-rocket mr-2"></i>
                                Per i Creator
                            </h4>
                            <p class="font-body text-grigio-pietra">
                                Offriamo uno <strong>strumento di marketing incredibile</strong> che amplifica la
                                visibilità
                                e la portata delle opere attraverso gli "emissari digitali" EGI PT.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sistema EGI PT -->
                <div class="mb-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        Il Sistema EGI PT: Gli Emissari Digitali
                    </h3>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-clone text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">5 EGI PT Massimo</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                Per ogni EGI con bene fisico, si creano <strong>massimo 5 EGI PT</strong>,
                                garantendo scarsità e valore.
                            </p>
                        </div>

                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-fingerprint text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">Numerazione Unica</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                Ogni EGI PT è <strong>reso unico da un numero di serie</strong>,
                                pur rimanendo identico all'originale.
                            </p>
                        </div>

                        <div class="renaissance-card elegant-hover p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-paper-plane text-4xl text-viola-innovazione"></i>
                            </div>
                            <h4 class="mb-3 text-lg font-semibold text-blu-algoritmo">Emissari Digitali</h4>
                            <p class="font-body text-sm text-grigio-pietra">
                                Gli EGI PT sono <strong>messaggeri dell'EGI fisico</strong>
                                nel mondo digitale globale.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vantaggi per Trader -->
                <div class="renaissance-card mb-8 p-8">
                    <h3 class="renaissance-title mb-6 text-xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-chart-line mr-3"></i>
                        Vantaggi per i Trader Professionali
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">Rarità Garantita</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                La limitazione a <strong>massimo 5 esemplari</strong> per ogni opera garantisce
                                scarsità naturale e potenziale apprezzamento sul mercato.
                            </p>
                            <div class="highlight-box">
                                <p class="text-sm font-semibold text-blu-algoritmo">
                                    Scarsità = Valore. Mai più di 5 EGI PT per opera.
                                </p>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-verde-rinascita">Arte Vera e Propria</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                Possedere un EGI PT significa detenere una <strong>rappresentazione digitale
                                    di un'opera d'arte reale</strong>, con valore artistico intrinseco.
                            </p>
                            <ul class="space-y-1 text-sm text-grigio-pietra">
                                <li><i class="fas fa-check mr-2 text-verde-rinascita"></i>Legame con opera fisica</li>
                                <li><i class="fas fa-check mr-2 text-verde-rinascita"></i>Valore artistico reale</li>
                                <li><i class="fas fa-check mr-2 text-verde-rinascita"></i>Collezionabilità garantita
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Struttura Economica -->
                <div class="renaissance-card mb-8 bg-gradient-to-br from-blue-50 to-green-50 p-8">
                    <h3 class="renaissance-title mb-6 text-xl font-bold text-blu-algoritmo">
                        <i class="text-oro-fiorentino fas fa-calculator mr-3"></i>
                        Struttura Economica Ottimizzata
                    </h3>
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-rosso-urgenza">Zero Fee Creator</h4>
                            <p class="text-sm text-grigio-pietra">
                                Il Creator <strong>non paga commissioni</strong> sugli EGI PT,
                                mantenendo il focus sul marketing gratuito.
                            </p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-rosso-urgenza">Zero Fee Piattaforma</h4>
                            <p class="text-sm text-grigio-pietra">
                                FlorenceEGI <strong>non trattiene commissioni</strong> sugli EGI PT,
                                massimizzando i volumi di trading.
                            </p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow-sm">
                            <h4 class="mb-2 text-lg font-semibold text-verde-rinascita">Solo 0,5% EPP</h4>
                            <p class="text-sm text-grigio-pietra">
                                Unica commissione: <strong>0,5% per progetti ambientali</strong>,
                                garantendo impatto sostenibile minimo.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 rounded-lg bg-gradient-to-r from-verde-rinascita to-green-600 p-4 text-center">
                        <p class="font-semibold text-white">
                            <i class="fas fa-trophy mr-2"></i>
                            Massima redditività per i trader + contributo ambientale responsabile
                        </p>
                    </div>
                </div>

                <!-- Impatto Ecosistema -->
                <div class="renaissance-card p-8">
                    <h3 class="renaissance-title mb-6 text-center text-2xl font-bold text-blu-algoritmo">
                        L'Impatto sull'Intero Ecosistema
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">Volumi e Visibilità</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                Le <strong>transazioni rapide e numerose</strong> degli EGI PT generano volumi
                                significativi
                                che aumentano la visibilità dell'intera piattaforma.
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-3 text-lg font-semibold text-arancio-energia">Ritorno per Tutti</h4>
                            <p class="mb-4 font-body text-grigio-pietra">
                                L'aumento di visibilità beneficia <strong>tutti i creator e reseller</strong>
                                del mercato secondario, creando un circolo virtuoso.
                            </p>
                        </div>
                    </div>
                    <div class="from-oro-fiorentino mt-6 rounded-lg bg-gradient-to-r to-yellow-400 p-6 text-center">
                        <p class="text-lg font-semibold text-white">
                            <i class="fas fa-infinity mr-2"></i>
                            Una strategia che eleva l'intero ecosistema FlorenceEGI attraverso la sinergia tra arte e
                            trading professionale
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('components.info-footer')

    <!-- Mobile Menu & Section Navigation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Logic
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

            // Mobile Sections Menu Logic
            const mobileSectionsToggle = document.getElementById('mobile-sections-toggle');
            const mobileSectionsMenu = document.getElementById('mobile-sections-menu');
            const mobileSectionsIcon = document.getElementById('mobile-sections-icon');

            if (mobileSectionsToggle) {
                mobileSectionsToggle.addEventListener('click', function() {
                    if (mobileSectionsMenu.classList.contains('hidden')) {
                        mobileSectionsMenu.classList.remove('hidden');
                        mobileSectionsMenu.classList.add('show');
                        mobileSectionsIcon.className = 'fas fa-chevron-up text-sm text-grigio-pietra';
                    } else {
                        mobileSectionsMenu.classList.add('hidden');
                        mobileSectionsMenu.classList.remove('show');
                        mobileSectionsIcon.className = 'fas fa-chevron-down text-sm text-grigio-pietra';
                    }
                });
            }

            // Section Navigation Logic (Desktop + Mobile)
            const sectionNavLinks = document.querySelectorAll('.section-nav-link, .section-nav-link-mobile');
            const sections = document.querySelectorAll('section[id]');

            // Smooth scrolling for section navigation
            sectionNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);

                    if (targetSection) {
                        // Calculate offset for fixed header + sticky navbar
                        const headerHeight = document.querySelector('header.fixed').offsetHeight;
                        const navbarHeight = document.querySelector('nav.sticky').offsetHeight;
                        const extraOffset = window.innerWidth < 768 ? 20 : 10;
                        const totalOffset = headerHeight + navbarHeight + extraOffset;
                        const targetPosition = targetSection.offsetTop - totalOffset;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Close mobile menu after click
                        if (window.innerWidth < 768 && mobileSectionsMenu) {
                            mobileSectionsMenu.classList.add('hidden');
                            mobileSectionsMenu.classList.remove('show');
                            mobileSectionsIcon.className =
                                'fas fa-chevron-down text-sm text-grigio-pietra';
                        }
                    }
                });
            });

            // Highlight active section on scroll
            function highlightActiveSection() {
                const headerHeight = document.querySelector('header.fixed').offsetHeight;
                const navbarHeight = document.querySelector('nav.sticky').offsetHeight;
                const scrollPosition = window.scrollY + headerHeight + navbarHeight + 50;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionBottom = sectionTop + section.offsetHeight;
                    const sectionId = section.getAttribute('id');

                    // Get both desktop and mobile links
                    const desktopLink = document.querySelector(
                        `.section-nav-link[data-section="${sectionId}"]`);
                    const mobileLink = document.querySelector(
                        `.section-nav-link-mobile[data-section="${sectionId}"]`);

                    if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                        // Remove active class from all links
                        sectionNavLinks.forEach(link => {
                            link.classList.remove('bg-oro-fiorentino', 'text-white',
                                'border-oro-fiorentino');
                            link.classList.add('text-grigio-pietra');
                        });

                        // Add active class to current links (both desktop and mobile)
                        [desktopLink, mobileLink].forEach(link => {
                            if (link) {
                                link.classList.add('bg-oro-fiorentino', 'text-white',
                                    'border-oro-fiorentino');
                                link.classList.remove('text-grigio-pietra');
                            }
                        });
                    }
                });
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (mobileSectionsToggle && mobileSectionsMenu &&
                    !mobileSectionsToggle.contains(event.target) &&
                    !mobileSectionsMenu.contains(event.target)) {
                    mobileSectionsMenu.classList.add('hidden');
                    mobileSectionsMenu.classList.remove('show');
                    mobileSectionsIcon.className = 'fas fa-chevron-down text-sm text-grigio-pietra';
                }
            });

            // Run on scroll with throttling
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                scrollTimeout = setTimeout(highlightActiveSection, 10);
            });

            // Initial highlight on page load
            setTimeout(highlightActiveSection, 100);
        });
    </script>

</body>

</html>
