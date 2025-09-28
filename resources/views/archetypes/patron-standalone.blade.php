{{--
    File: patron-standalone.blade.php
    Versione: 3.0 FlorenceEGI Brand Guidelines
    Data: 28 Settembre 2025
    Descrizione: Pagina Mecenati standalone seguendo Brand Guidelines FlorenceEGI
    Caratteristiche: Indipendente, banner rinascimentale, colori brand, font corretti
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mecenati FlorenceEGI - Il Nuovo Rinascimento Ecologico Digitale</title>
    <meta name="description"
        content="Diventa protagonista del nuovo Rinascimento: facilita connessioni significative tra arte contemporanea e rigenerazione ambientale. Un ruolo di prestigio per impatto misurabile.">

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
            background: linear-gradient(rgba(27, 54, 93, 0.85), rgba(27, 54, 93, 0.85)),
                url('{{ asset('images/default/patron_banner_background_rinascimento_1.png') }}') no-repeat center center/cover;
            min-height: 60vh;
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
    </style>
</head>

<body class="bg-gray-50 text-grigio-pietra">

    <!-- Header con Navigazione -->
    <header class="bg-blu-algoritmo text-white shadow-lg">
        <div class="golden-ratio-container px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-heart text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">Mecenati FlorenceEGI</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">Il Nuovo Rinascimento Ecologico Digitale
                        </p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-6 md:flex">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Home</a>
                    <a href="#ruolo" class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Il
                        Ruolo</a>
                    <a href="#casi" class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Casi
                        d'Uso</a>
                    <a href="#processo" class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Come
                        Iniziare</a>
                    <a href="#contatti"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Contatti</a>
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
                    <a href="#ruolo"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-crown text-oro-fiorentino mr-3 text-lg"></i>
                        Il Ruolo
                    </a>
                    <a href="#casi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-users text-oro-fiorentino mr-3 text-lg"></i>
                        Casi d'Uso
                    </a>
                    <a href="#processo"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-route text-oro-fiorentino mr-3 text-lg"></i>
                        Come Iniziare
                    </a>
                    <a href="#contatti"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-envelope text-oro-fiorentino mr-3 text-lg"></i>
                        Contatti
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section con Banner Rinascimentale -->
    <section class="hero-background text-white">
        <div class="golden-ratio-container px-4 py-16 sm:px-6 sm:py-24">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="renaissance-title mb-6 text-4xl font-bold leading-tight sm:text-5xl md:text-6xl">
                    Diventa Protagonista del<br>
                    <span class="text-oro-fiorentino">Nuovo Rinascimento</span>
                </h1>
                <p class="mx-auto mb-8 max-w-3xl font-body text-xl text-blue-100 sm:text-2xl">
                    Facilita connessioni significative tra arte contemporanea e rigenerazione ambientale.<br>
                    <strong class="text-oro-fiorentino">Un ruolo di prestigio per impatto misurabile.</strong>
                </p>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#ruolo"
                        class="cta-primary elegant-hover inline-flex items-center justify-center rounded-xl px-8 py-4 text-lg font-semibold text-white">
                        <i class="fas fa-crown mr-3"></i>
                        Scopri il Ruolo
                    </a>
                    <a href="#processo"
                        class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino elegant-hover inline-flex items-center justify-center rounded-xl border-2 px-8 py-4 text-lg font-semibold transition-all hover:text-blu-algoritmo">
                        <i class="fas fa-handshake mr-3"></i>
                        Come Iniziare
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Il Ruolo del Mecenate -->
    <section id="ruolo" class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Il Ruolo del <span class="text-oro-fiorentino">Mecenate</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    Un facilitatore professionale nell'ecosistema FlorenceEGI che connette artisti con opportunità di
                    impatto ambientale documentato.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                <div class="renaissance-card elegant-hover p-8 text-center">
                    <div
                        class="bg-oro-fiorentino/10 mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full">
                        <i class="fas fa-handshake text-oro-fiorentino text-2xl"></i>
                    </div>
                    <h3 class="renaissance-title mb-4 text-xl font-bold text-grigio-pietra">Facilitatore di Connessioni
                    </h3>
                    <p class="font-body text-grigio-pietra">
                        Colleghi artisti con opportunità concrete di impatto ambientale attraverso l'ecosistema
                        FlorenceEGI e i progetti EPP verificati.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-8 text-center">
                    <div
                        class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/10">
                        <i class="fas fa-seedling text-2xl text-verde-rinascita"></i>
                    </div>
                    <h3 class="renaissance-title mb-4 text-xl font-bold text-grigio-pietra">Ambasciatore Sostenibilità
                    </h3>
                    <p class="font-body text-grigio-pietra">
                        Promuovi la causa ambientale attraverso l'arte, con impatto tracciabile e verificabile via
                        blockchain per trasparenza totale.
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-8 text-center">
                    <div
                        class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/10">
                        <i class="fas fa-crown text-2xl text-viola-innovazione"></i>
                    </div>
                    <h3 class="renaissance-title mb-4 text-xl font-bold text-grigio-pietra">Costruttore di Prestigio
                    </h3>
                    <p class="font-body text-grigio-pietra">
                        Sviluppi un riconoscimento duraturo nel network arte-sostenibilità, con documentazione
                        permanente del tuo contributo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Casi d'Uso Realistici -->
    <section id="casi" class="bg-gray-50 py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Chi Può Diventare <span class="text-oro-fiorentino">Mecenate</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    Profili reali di persone che potrebbero trovare valore nel ruolo di Mecenate FlorenceEGI.
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Il Pensionato Colto -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div
                            class="bg-oro-fiorentino/10 mr-4 inline-flex h-14 w-14 items-center justify-center rounded-full">
                            <i class="fas fa-user-graduate text-oro-fiorentino text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold text-grigio-pietra">Il Pensionato Colto</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><span class="font-semibold text-grigio-pietra">Profilo:</span> Ex dirigente/professionista
                            con passione per l'arte e tempo disponibile.</p>
                        <p><span class="font-semibold text-grigio-pietra">Background:</span> Rete di contatti nel mondo
                            culturale, esperienza professionale consolidata.</p>
                        <p><span class="font-semibold text-grigio-pietra">Motivazione:</span> Dare un contributo
                            significativo alla cultura e all'ambiente.</p>
                        <p class="text-oro-fiorentino font-semibold"><i class="fas fa-star mr-2"></i>Facilitatore di
                            connessioni prestigiose arte-sostenibilità.</p>
                    </div>
                </div>

                <!-- Il Gallerista Innovativo -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div
                            class="mr-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-viola-innovazione/10">
                            <i class="fas fa-store text-2xl text-viola-innovazione"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold text-grigio-pietra">Il Gallerista Innovativo
                        </h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><span class="font-semibold text-grigio-pietra">Profilo:</span> Proprietario di galleria che
                            vuole modernizzare il proprio business.</p>
                        <p><span class="font-semibold text-grigio-pietra">Background:</span> Competenze nel mercato
                            dell'arte, portfolio di artisti esistente.</p>
                        <p><span class="font-semibold text-grigio-pietra">Motivazione:</span> Differenziarsi con
                            certificazione blockchain e impatto EPP.</p>
                        <p class="font-semibold text-viola-innovazione"><i class="fas fa-star mr-2"></i>Ponte tra arte
                            tradizionale e innovazione sostenibile.</p>
                    </div>
                </div>

                <!-- Il Filantropo Ambientale -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div
                            class="mr-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-verde-rinascita/10">
                            <i class="fas fa-leaf text-2xl text-verde-rinascita"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold text-grigio-pietra">Il Filantropo Ambientale
                        </h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><span class="font-semibold text-grigio-pietra">Profilo:</span> Imprenditore/professionista
                            attivo in cause ambientali.</p>
                        <p><span class="font-semibold text-grigio-pietra">Background:</span> Già coinvolto in progetti
                            di sostenibilità.</p>
                        <p><span class="font-semibold text-grigio-pietra">Motivazione:</span> Combinare passione per
                            l'arte con impatto misurabile.</p>
                        <p class="font-semibold text-verde-rinascita"><i class="fas fa-star mr-2"></i>Ambasciatore
                            autorevole della causa arte-sostenibilità.</p>
                    </div>
                </div>

                <!-- Il Curatore/Critico -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div
                            class="mr-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-blu-algoritmo/10">
                            <i class="fas fa-eye text-2xl text-blu-algoritmo"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold text-grigio-pietra">Il Curatore/Critico d'Arte
                        </h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><span class="font-semibold text-grigio-pietra">Profilo:</span> Professionista del settore
                            artistico-culturale.</p>
                        <p><span class="font-semibold text-grigio-pietra">Background:</span> Competenze tecniche,
                            network di artisti emergenti.</p>
                        <p><span class="font-semibold text-grigio-pietra">Motivazione:</span> Supportare artisti in
                            progetti con impatto sociale.</p>
                        <p class="font-semibold text-blu-algoritmo"><i class="fas fa-star mr-2"></i>Validazione
                            qualitativa delle proposte artistiche sostenibili.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Come Iniziare -->
    <section id="processo" class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Come <span class="text-oro-fiorentino">Iniziare</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    Un processo concreto e trasparente per valutare se il ruolo di Mecenate è adatto a te.
                </p>
            </div>

            <div class="mx-auto max-w-4xl">
                <div class="space-y-8">
                    <div class="flex items-start space-x-6">
                        <div
                            class="bg-oro-fiorentino flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold text-white">
                            1</div>
                        <div class="flex-1">
                            <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Valutazione
                                Iniziale</h3>
                            <p class="font-body text-grigio-pietra">Contatto diretto con il team FlorenceEGI per una
                                conversazione esplorativa. Niente impegni, solo una discussione aperta su obiettivi e
                                possibilità concrete.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-verde-rinascita text-lg font-bold text-white">
                            2</div>
                        <div class="flex-1">
                            <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Comprensione
                                dell'Ecosistema</h3>
                            <p class="font-body text-grigio-pietra">Approfondimento su come funziona FlorenceEGI, i
                                progetti EPP, e il sistema di certificazione blockchain. Tutto trasparente e
                                verificabile.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-viola-innovazione text-lg font-bold text-white">
                            3</div>
                        <div class="flex-1">
                            <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Primo Progetto
                                Pilota</h3>
                            <p class="font-body text-grigio-pietra">Collaborazione su un progetto concreto per testare
                                l'allineamento e valutare l'impatto reale. Approccio graduale e senza pressioni.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-blu-algoritmo text-lg font-bold text-white">
                            4</div>
                        <div class="flex-1">
                            <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Sviluppo del Ruolo
                            </h3>
                            <p class="font-body text-grigio-pietra">Se il progetto pilota ha successo, sviluppo
                                graduale del ruolo in base agli interessi specifici e alle competenze personali.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contatti -->
    <section id="contatti" class="bg-gray-50 py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="text-center">
                <h2 class="renaissance-title mb-8 text-3xl font-bold text-grigio-pietra sm:text-4xl">
                    Inizia la <span class="text-oro-fiorentino">Conversazione</span>
                </h2>
                <p class="mx-auto mb-8 max-w-2xl font-body text-xl text-grigio-pietra">
                    Sei interessato a esplorare il ruolo di Mecenate? Contattaci per una conversazione senza impegni.
                </p>
                <a href="mailto:mecenati@florenceegi.com"
                    class="cta-primary elegant-hover inline-flex items-center rounded-xl px-8 py-4 text-lg font-semibold text-white">
                    <i class="fas fa-envelope mr-3"></i>
                    mecenati@florenceegi.com
                </a>
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
