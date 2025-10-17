{{--
    File: disclaimer.blade.php
    Versione: 1.0 Disclaimer Sviluppo
    Data: 28 Settembre 2025
    Descrizione: Pagina disclaimer per portale in sviluppo su staging URL
    Caratteristiche: Brand Guidelines FlorenceEGI, Layout coerente, Info contatto
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disclaimer - FlorenceEGI Portale in Sviluppo</title>
    <meta name="description"
        content="Disclaimer per il portale FlorenceEGI in fase di sviluppo. Informazioni su stato di avanzamento e contatti per feedback.">
    <meta name="keywords" content="FlorenceEGI,Disclaimer,Sviluppo,Staging,Beta,Blockchain">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="FlorenceEGI">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Protocol -->
    <meta property="og:title" content="Disclaimer - FlorenceEGI in Sviluppo">
    <meta property="og:description" content="Portale in fase di sviluppo. Preview beta per stakeholder selezionati.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:locale" content="it_IT">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Disclaimer - FlorenceEGI in Sviluppo">
    <meta name="twitter:description" content="Portale in fase beta per stakeholder selezionati.">
    <meta name="twitter:site" content="@FlorenceEGI">

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Disclaimer - FlorenceEGI in Sviluppo",
        "description": "Pagina disclaimer per portale in fase di sviluppo",
        "url": "{{ url()->current() }}",
        "isPartOf": {
            "@type": "WebSite",
            "@id": "https://florence-egi.com/#website",
            "name": "FlorenceEGI"
        },
        "publisher": {
            "@type": "Organization",
            "name": "FlorenceEGI",
            "url": "https://florence-egi.com"
        }
    }
    </script>

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
            /* 1000 * 1.618 */
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

        /* Hero Background con Banner */
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

        /* Development badge */
        .dev-badge {
            background: linear-gradient(135deg, #8E44AD 0%, #9B59B6 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 pt-20 text-grigio-pietra">

    <!-- Header con Navigazione - Fixed -->
    <header class="fixed left-0 right-0 top-0 z-50 bg-blu-algoritmo text-white shadow-lg" role="banner">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-infinity text-oro-fiorentino text-3xl sm:text-4xl" role="img" aria-label="Icona infinito FlorenceEGI"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">FlorenceEGI</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">Il Rinascimento Digitale</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-3 md:flex" role="navigation" aria-label="Navigazione principale">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base" aria-label="Torna alla home">Home</a>
                    <a href="{{ route('info.florence-egi') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">FlorenceEGI</a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Patron</a>
                    <a href="{{ route('gdpr.privacy-policy') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Privacy</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block rounded-md p-2 transition-colors hover:bg-blue-700 md:hidden"
                    aria-label="Apri menu navigazione"
                    aria-expanded="false"
                    aria-controls="mobile-menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mt-4 hidden border-t border-blue-600 pb-4 md:hidden" role="navigation" aria-label="Menu mobile">
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
    <section class="hero-background" role="region" aria-labelledby="hero-heading">
        <div class="golden-ratio-container flex min-h-[50vh] items-center justify-center px-4">
            <div class="mx-auto max-w-4xl text-center text-white">
                <div class="mb-6">
                    <span class="dev-badge">
                        <i class="fas fa-code" aria-hidden="true"></i>
                        Ambiente di Sviluppo
                    </span>
                </div>
                <h1 id="hero-heading" class="renaissance-title mb-6 text-4xl font-bold sm:text-5xl lg:text-6xl">
                    Disclaimer
                </h1>
                <p class="mb-6 font-body text-lg leading-relaxed text-blue-100 sm:text-xl">
                    Informazioni importanti sul portale FlorenceEGI in fase di sviluppo
                </p>
                <div class="font-body text-sm text-blue-200">
                    <i class="fas fa-globe mr-2"></i>
                    Staging URL: <code class="rounded bg-blue-900 px-2 py-1">https://app.13.48.57.194.sslip.io</code>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenuto Principale -->
    <section class="py-16">
        <div class="golden-ratio-container px-4 sm:px-6 lg:px-8">

            <!-- Stato Sviluppo -->
            <div class="mx-auto mb-16 max-w-4xl">
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-4 text-3xl text-arancio-energia"></i>
                        <h2 class="renaissance-title text-2xl font-bold text-blu-algoritmo sm:text-3xl">
                            Portale in Sviluppo
                        </h2>
                    </div>

                    <div class="space-y-6 font-body text-lg leading-relaxed">
                        <p>
                            <strong>FlorenceEGI</strong> è attualmente in fase di sviluppo attivo. Il portale che stai
                            visitando
                            rappresenta il nostro ambiente di staging, accessibile all'indirizzo
                            <code
                                class="rounded bg-gray-100 px-2 py-1 text-sm text-blu-algoritmo">https://app.13.48.57.194.sslip.io</code>.
                        </p>

                        <p>
                            Questo significa che molte funzionalità sono ancora in corso di implementazione, testing e
                            ottimizzazione.
                            Potresti riscontrare:
                        </p>

                        <ul class="list-disc space-y-2 pl-6">
                            <li>Sezioni incomplete o in fase di costruzione</li>
                            <li>Funzionalità temporaneamente non disponibili</li>
                            <li>Design e contenuti soggetti a modifiche</li>
                            <li>Possibili interruzioni temporanee del servizio</li>
                            <li>Dati di test e contenuti dimostrativi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Invito all'Esplorazione -->
            <div class="mx-auto mb-16 max-w-4xl">
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <i class="fas fa-compass mr-4 text-3xl text-verde-rinascita"></i>
                        <h2 class="renaissance-title text-2xl font-bold text-blu-algoritmo sm:text-3xl">
                            Ti Invitiamo a Esplorare
                        </h2>
                    </div>

                    <div class="space-y-6 font-body text-lg leading-relaxed">
                        <p>
                            Nonostante lo stato di sviluppo, ti incoraggiamo caldamente a esplorare il portale
                            e a scoprire la <strong>visione innovativa di FlorenceEGI</strong>: il primo marketplace
                            che risolve il trilemma degli NFT unendo qualità artistica, liquidità massima e
                            impatto ambientale reale.
                        </p>

                        <p>
                            Potrai già vedere implementati:
                        </p>

                        <ul class="list-disc space-y-2 pl-6">
                            <li><strong>Architettura EGI Dual Flow</strong> su blockchain Algorand</li>
                            <li><strong>Sistema GDPR by Design</strong> con compliance integrata</li>
                            <li><strong>Pattern UltraLogManager</strong> per gestione avanzata errori</li>
                            <li><strong>Interfacce responsive</strong> seguendo le Brand Guidelines</li>
                            <li><strong>Documentazione tecnica</strong> dell'ecosistema Ultra</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Feedback e Contatti -->
            <div class="mx-auto mb-16 max-w-4xl">
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <i class="fas fa-comments text-oro-fiorentino mr-4 text-3xl"></i>
                        <h2 class="renaissance-title text-2xl font-bold text-blu-algoritmo sm:text-3xl">
                            Le Tue Impressioni Sono Preziose
                        </h2>
                    </div>

                    <div class="space-y-6 font-body text-lg leading-relaxed">
                        <p>
                            Il tuo feedback è fondamentale per il continuo miglioramento di FlorenceEGI.
                            Se hai osservazioni, suggerimenti, domande o hai riscontrato problemi durante
                            la navigazione, non esitare a condividerli con noi.
                        </p>

                        <div class="border-oro-fiorentino border-l-4 bg-blue-50 p-6">
                            <p class="mb-4 font-semibold text-blu-algoritmo">
                                <i class="fas fa-envelope mr-2"></i>
                                Contattaci per feedback e impressioni:
                            </p>
                            <a href="mailto:info@florenceegi.com"
                                class="cta-primary elegant-hover inline-flex items-center rounded-lg px-6 py-3 text-white transition-all duration-300">
                                <i class="fas fa-paper-plane mr-2"></i>
                                info@florenceegi.com
                            </a>
                        </div>

                        <p class="text-base text-grigio-pietra">
                            Ogni contributo ci aiuta a costruire un ecosistema digitale migliore,
                            più inclusivo e sostenibile per tutti.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Roadmap Trasparenza -->
            <div class="mx-auto max-w-4xl">
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <i class="fas fa-road mr-4 text-3xl text-viola-innovazione"></i>
                        <h2 class="renaissance-title text-2xl font-bold text-blu-algoritmo sm:text-3xl">
                            Trasparenza e Roadmap
                        </h2>
                    </div>

                    <div class="space-y-6 font-body text-lg leading-relaxed">
                        <p>
                            FlorenceEGI è costruito secondo i principi di <strong>trasparenza totale</strong>
                            e <strong>innovazione responsabile</strong>. Il nostro approccio di sviluppo
                            prevede il rilascio graduale di funzionalità, permettendo alla community
                            di seguire e contribuire al progresso.
                        </p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="border-oro-fiorentino rounded-lg border bg-orange-50 p-4">
                                <h3 class="mb-2 font-semibold text-blu-algoritmo">
                                    <i class="fas fa-clock text-oro-fiorentino mr-2"></i>
                                    Fase Attuale
                                </h3>
                                <p class="text-base">
                                    Sviluppo architettura core, implementazione GDPR,
                                    testing interfacce utente e integrazione blockchain Algorand.
                                </p>
                            </div>

                            <div class="rounded-lg border border-verde-rinascita bg-green-50 p-4">
                                <h3 class="mb-2 font-semibold text-blu-algoritmo">
                                    <i class="fas fa-rocket mr-2 text-verde-rinascita"></i>
                                    Prossimi Rilasci
                                </h3>
                                <p class="text-base">
                                    Marketplace operativo, sistema tokenomica EGI,
                                    certificazione artisti e dashboard patron completa.
                                </p>
                            </div>
                        </div>
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
        });
    </script>

</body>

</html>
