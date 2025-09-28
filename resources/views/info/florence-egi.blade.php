{{--
    File: florence-egi.blade.php
    Versione: 1.0 FlorenceEGI Istituzionale
    Data: 28 Settembre 2025
    Descrizione: Pagina istituzionale completa FlorenceEGI basata su White Paper OS1
    Caratteristiche: Brand Guidelines, CoA Integration, Vision tecnica completa
--}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlorenceEGI - Il Rinascimento Digitale che Unisce Arte, Tecnologia e Rigenerazione Planetaria</title>
    <meta name="description"
        content="FlorenceEGI è il primo marketplace che risolve il trilemma NFT: Qualità Artistica + Liquidità Massima + Impatto Ambientale Reale. Architettura EGI Dual Flow su blockchain Algorand.">

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

        /* Sezioni alternate */
        .section-dark {
            background: linear-gradient(135deg, #1B365D 0%, #2D5016 100%);
        }

        /* Code blocks */
        .code-block {
            background: #1a1a1a;
            border-left: 4px solid #D4A574;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
        }

        /* Trilemma diagram */
        .trilemma-point {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-align: center;
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
                    <i class="fas fa-infinity text-oro-fiorentino text-3xl sm:text-4xl"></i>
                    <div>
                        <h1 class="renaissance-title text-xl font-bold sm:text-2xl">FlorenceEGI</h1>
                        <p class="font-body text-sm text-blue-200 sm:text-base">Il Rinascimento Digitale</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-6 md:flex">
                    <a href="{{ route('home') }}"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Home</a>
                    <a href="#visione"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Visione</a>
                    <a href="#problema"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Problema</a>
                    <a href="#soluzione"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Soluzione</a>
                    <a href="#tecnologia"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Tecnologia</a>
                    <a href="#coa"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">CoA</a>
                    <a href="#impatto"
                        class="hover:text-oro-fiorentino font-body text-sm transition lg:text-base">Impatto</a>
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
                    <a href="#visione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-eye text-oro-fiorentino mr-3 text-lg"></i>
                        Visione
                    </a>
                    <a href="#problema"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-exclamation-triangle text-oro-fiorentino mr-3 text-lg"></i>
                        Problema
                    </a>
                    <a href="#soluzione"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-lightbulb text-oro-fiorentino mr-3 text-lg"></i>
                        Soluzione
                    </a>
                    <a href="#tecnologia"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-cogs text-oro-fiorentino mr-3 text-lg"></i>
                        Tecnologia
                    </a>
                    <a href="#coa"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-certificate text-oro-fiorentino mr-3 text-lg"></i>
                        CoA
                    </a>
                    <a href="#impatto"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-blue-700">
                        <i class="fas fa-leaf text-oro-fiorentino mr-3 text-lg"></i>
                        Impatto
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="visione" class="hero-background text-white">
        <div class="golden-ratio-container px-4 py-16 sm:px-6 sm:py-24">
            <div class="mx-auto max-w-5xl text-center">
                <h1 class="renaissance-title mb-6 text-4xl font-bold leading-tight sm:text-5xl md:text-6xl">
                    Il <span class="text-oro-fiorentino">Rinascimento Digitale</span><br>
                    che Unisce Arte, Tecnologia<br>
                    e Rigenerazione Planetaria
                </h1>
                <p class="mx-auto mb-8 max-w-4xl font-body text-xl text-green-100 sm:text-2xl">
                    <strong class="text-oro-fiorentino">FlorenceEGI è il primo marketplace che risolve il trilemma
                        dell'ecosistema NFT:</strong><br>
                    Qualità Artistica + Liquidità Massima + Impatto Ambientale Reale
                </p>
                <div class="mx-auto mb-8 max-w-4xl font-body text-lg">
                    <strong>Architettura Sostenibile:</strong> Ogni transazione genera impatto EPP automatico, guadagni equi per i creator e sostenibilità della piattaforma
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#soluzione"
                        class="cta-primary elegant-hover inline-flex items-center justify-center rounded-xl px-8 py-4 text-lg font-semibold text-white">
                        <i class="fas fa-rocket mr-3"></i>
                        Scopri la Soluzione
                    </a>
                    <a href="#tecnologia"
                        class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino elegant-hover inline-flex items-center justify-center rounded-xl border-2 px-8 py-4 text-lg font-semibold transition-all hover:text-blu-algoritmo">
                        <i class="fas fa-cogs mr-3"></i>
                        Tecnologia Algorand
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Il Problema: Trilemma NFT -->
    <section id="problema" class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Il Problema che <span class="text-rosso-urgenza">Risolviamo</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    Il mercato NFT è paralizzato da un trilemma che nessuna piattaforma è riuscita a risolvere
                </p>
            </div>

            <!-- Trilemma Diagram -->
            <div class="relative mx-auto mb-16 max-w-4xl">
                <div class="flex h-96 items-center justify-center">
                    <!-- Triangle visualization -->
                    <div class="relative">
                        <!-- Top point: Qualità Artistica -->
                        <div class="absolute -top-16 left-1/2 -translate-x-1/2 transform">
                            <div class="trilemma-point bg-viola-innovazione">
                                <div>
                                    <i class="fas fa-palette mb-2 text-2xl"></i><br>
                                    QUALITÀ<br>ARTISTICA
                                </div>
                            </div>
                        </div>

                        <!-- Bottom left: Liquidità -->
                        <div class="absolute -left-16 top-32">
                            <div class="trilemma-point bg-blu-algoritmo">
                                <div>
                                    <i class="fas fa-chart-line mb-2 text-2xl"></i><br>
                                    LIQUIDITÀ<br>TRADING
                                </div>
                            </div>
                        </div>

                        <!-- Bottom right: Impatto -->
                        <div class="absolute -right-16 top-32">
                            <div class="trilemma-point bg-verde-rinascita">
                                <div>
                                    <i class="fas fa-leaf mb-2 text-2xl"></i><br>
                                    IMPATTO<br>AMBIENTALE
                                </div>
                            </div>
                        </div>

                        <!-- Center: Impossibilità -->
                        <div
                            class="flex h-32 w-32 items-center justify-center rounded-full bg-rosso-urgenza text-center font-bold text-white">
                            <div>
                                <i class="fas fa-times mb-2 text-3xl"></i><br>
                                TRILEMMA<br>IMPOSSIBILE
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Competitor Analysis -->
            <div class="mb-16 grid gap-8 md:grid-cols-3">
                <div class="renaissance-card p-6 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <i class="fas fa-times text-2xl text-rosso-urgenza"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">OpenSea</h3>
                    <p class="font-body text-sm text-grigio-pietra">
                        ✅ Qualità artistica<br>
                        ❌ Zero liquidità<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>

                <div class="renaissance-card p-6 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <i class="fas fa-times text-2xl text-rosso-urgenza"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Blur</h3>
                    <p class="font-body text-sm text-grigio-pietra">
                        ✅ Liquidità massima<br>
                        ❌ Zero qualità<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>

                <div class="renaissance-card p-6 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <i class="fas fa-times text-2xl text-rosso-urgenza"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-xl font-bold text-grigio-pietra">Foundation</h3>
                    <p class="font-body text-sm text-grigio-pietra">
                        ✅ Qualità premium<br>
                        ❌ Liquidità inesistente<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cosa sono gli EGI -->
    <section id="egi" class="bg-gray-50 py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Che cosa sono gli <span class="text-oro-fiorentino">EGI</span>?
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    <strong>EGI = Ecological, Goods & Inventive</strong><br>
                    Gli asset digitali che uniscono arte, impatto ambientale e utility pratica
                </p>
            </div>

            <!-- Le Tre Componenti dell'Asset Digitale -->
            <div class="grid gap-8 mb-16 md:grid-cols-3">
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center justify-center">
                        <div class="bg-verde-rinascita/20 flex h-20 w-20 items-center justify-center rounded-full">
                            <i class="fas fa-seedling text-verde-rinascita text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="renaissance-title text-center text-xl font-bold mb-4">Environment Protection Programs</h3>
                    <p class="font-body text-grigio-pietra text-center">
                        I progetti ambientali concreti che danno sostanza all'impatto ecologico di ogni EGI
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center justify-center">
                        <div class="bg-oro-fiorentino/20 flex h-20 w-20 items-center justify-center rounded-full">
                            <i class="fas fa-box text-oro-fiorentino text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="renaissance-title text-center text-xl font-bold mb-4">Goods - Beni o Servizi</h3>
                    <p class="font-body text-grigio-pietra text-center">
                        La componente economica e di utilità pratica che rende ogni EGI un asset funzionale
                    </p>
                </div>

                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center justify-center">
                        <div class="bg-viola-innovazione/20 flex h-20 w-20 items-center justify-center rounded-full">
                            <i class="fas fa-lightbulb text-viola-innovazione text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="renaissance-title text-center text-xl font-bold mb-4">Inventive - Creatività</h3>
                    <p class="font-body text-grigio-pietra text-center">
                        L'elemento artistico e innovativo che rende unico ogni EGI nel panorama digitale
                    </p>
                </div>
            </div>

            <!-- Co-creazione -->
            <div class="bg-white rounded-2xl p-8 md:p-12">
                <h3 class="renaissance-title text-2xl font-bold text-grigio-pietra mb-6 text-center">
                    Il Concetto di <span class="text-oro-fiorentino">Co-creazione</span>
                </h3>
                <div class="grid gap-8 md:grid-cols-2 items-center">
                    <div class="font-body space-y-4">
                        <p class="text-lg">
                            <strong>Il Co-Creatore è colui che effettua il mint</strong> - il primo acquisto di un EGI. 
                            Diventa co-creatore perché un'opera d'arte riposta nel magazzino dell'artista è come se non fosse mai stata creata.
                        </p>
                        <p>
                            Solo quando qualcuno la acquista per esporla in qualche modo, essa prende davvero vita.
                        </p>
                        <div class="bg-oro-fiorentino/10 p-4 rounded-lg">
                            <p class="text-oro-fiorentino font-semibold">
                                <i class="fas fa-star mr-2"></i>
                                Il Co-Creatore rimane impresso nell'EGI per sempre, insieme al Creator, anche nel mercato secondario
                            </p>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="bg-oro-fiorentino/20 inline-flex items-center justify-center rounded-full h-32 w-32 mb-4">
                            <i class="fas fa-handshake text-oro-fiorentino text-4xl"></i>
                        </div>
                        <p class="font-body text-grigio-pietra">
                            <strong>Visibilità e Riconoscimento</strong><br>
                            La Co-Creazione risponde al bisogno di visibilità e riconoscimento degli internauti
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- La Soluzione: Architettura Anti-Trilemma -->
    <section id="soluzione" class="section-dark py-16 text-white sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                    La Soluzione: <span class="text-oro-fiorentino">Architettura Anti-Trilemma</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                    Il primo marketplace che offre TUTTE E TRE le caratteristiche simultaneamente
                </p>
            </div>

            <!-- EGI Dual Flow -->
            <div class="mb-16 grid gap-8 lg:grid-cols-2">
                <div
                    class="renaissance-card bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div
                            class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/20">
                            <i class="fas fa-gem text-2xl text-viola-innovazione"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold">EGI Fisici</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Arte autentica</strong> con certificazione CoA blockchain</p>
                        <p><strong>Utility reale</strong> con diritti e royalties</p>
                        <p><strong>20% automatico EPP</strong> per impatto ambientale</p>
                        <p class="font-semibold text-viola-innovazione">✅ Collezionismo premium con impatto certificato
                        </p>
                    </div>
                </div>

                <div
                    class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                            <i class="fas fa-bolt text-2xl text-blu-algoritmo"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold">EGI pt</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Trading velocity</strong> ad alta frequenza</p>
                        <p><strong>Fee competitive</strong> (1.5%) dinamiche</p>
                        <p><strong>Impact garantito</strong> su ogni transazione</p>
                        <p class="font-semibold text-blu-algoritmo">✅ Liquidità massima con rigenerazione automatica
                        </p>
                    </div>
                </div>
            </div>

            <!-- Infrastructure -->
            <div class="text-center">
                <div
                    class="renaissance-card mx-auto max-w-4xl bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center justify-center">
                        <div
                            class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                            <i class="fas fa-infinity text-2xl text-verde-rinascita"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold">Algorand Infrastructure</h3>
                    </div>
                    <div class="grid gap-6 font-body md:grid-cols-3">
                        <div>
                            <i class="fas fa-leaf mb-2 text-3xl text-verde-rinascita"></i>
                            <p><strong>Carbon Negative</strong><br>Blockchain che assorbe CO2</p>
                        </div>
                        <div>
                            <i class="fas fa-tachometer-alt mb-2 text-3xl text-verde-rinascita"></i>
                            <p><strong>6000 TPS</strong><br>Performance ultra-veloce</p>
                        </div>
                        <div>
                            <i class="fas fa-shield-alt mb-2 text-3xl text-verde-rinascita"></i>
                            <p><strong>Sicurezza Enterprise</strong><br>Finalità 2.5 secondi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tecnologia: Progressive Web3 -->
    <section id="tecnologia" class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Tecnologia: <span class="text-oro-fiorentino">Progressive Web3</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    L'infrastruttura sostenibile per l'adozione di massa del Web3
                </p>
            </div>

            <!-- Architecture Layers -->
            <div class="mx-auto max-w-4xl space-y-8">
                <!-- Layer 1 -->
                <div class="renaissance-card border-l-4 border-verde-rinascita p-6">
                    <div class="mb-4 flex items-center">
                        <div
                            class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-verde-rinascita font-bold text-white">
                            1</div>
                        <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">Layer 1 (99% users)</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Email login + carta credito + UX familiare come Amazon</strong><br>
                        Esperienza Web2 tradizionale per adozione di massa
                    </p>
                </div>

                <!-- Layer 2 -->
                <div class="renaissance-card border-oro-fiorentino border-l-4 p-6">
                    <div class="mb-4 flex items-center">
                        <div
                            class="bg-oro-fiorentino mr-4 flex h-12 w-12 items-center justify-center rounded-full font-bold text-white">
                            2</div>
                        <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">Layer 2 (Bridge)</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Educazione graduale + wallet custodial + supporto umano</strong><br>
                        Transizione guidata verso la decentralizzazione
                    </p>
                </div>

                <!-- Layer 3 -->
                <div class="renaissance-card border-l-4 border-viola-innovazione p-6">
                    <div class="mb-4 flex items-center">
                        <div
                            class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-viola-innovazione font-bold text-white">
                            3</div>
                        <h3 class="renaissance-title text-xl font-bold text-grigio-pietra">Layer 3 (1% power users)
                        </h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Self-custody + DeFi + API access + trading avanzato</strong><br>
                        Controllo completo per utenti esperti
                    </p>
                </div>
            </div>

            <!-- Smart Contracts Innovation -->
            <div class="mt-16">
                <h3 class="renaissance-title mb-8 text-center text-2xl font-bold text-grigio-pietra">Smart Contracts
                    Rivoluzionari</h3>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="renaissance-card p-6 text-center">
                        <i class="fas fa-chart-line text-oro-fiorentino mb-4 text-3xl"></i>
                        <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Fee Dinamiche On-Chain
                        </h4>
                        <p class="font-body text-sm text-grigio-pietra">Primi al mondo con parametri auto-adattivi</p>
                    </div>
                    <div class="renaissance-card p-6 text-center">
                        <i class="fas fa-share-alt mb-4 text-3xl text-verde-rinascita"></i>
                        <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">EPP Distribution</h4>
                        <p class="font-body text-sm text-grigio-pietra">20% split immutabile, trasparente, verificabile
                        </p>
                    </div>
                    <div class="renaissance-card p-6 text-center">
                        <i class="fas fa-infinity mb-4 text-3xl text-viola-innovazione"></i>
                        <h4 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Royalty Engine</h4>
                        <p class="font-body text-sm text-grigio-pietra">Pagamenti automatici crescenti per sempre</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CoA: Certificate of Authenticity -->
    <section id="coa" class="section-dark py-16 text-white sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                    <span class="text-oro-fiorentino">CoA</span>: Certificate of Authenticity
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                    Sistema di certificazione digitale professionale per arte e beni culturali
                </p>
            </div>

            <!-- CoA Features -->
            <div class="mb-16 grid gap-8 lg:grid-cols-2">
                <div
                    class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 bg-gradient-to-br p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-certificate text-oro-fiorentino text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold">Certificazione Blockchain</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Hash SHA-256</strong> per verifiche immutabili</p>
                        <p><strong>Metadati tecnici</strong> con sistema traits avanzato</p>
                        <p><strong>Chain of custody</strong> tracciabile e verificabile</p>
                        <p><strong>PDF certificati</strong> con firme digitali</p>
                        <p class="text-oro-fiorentino font-semibold">✅ Standard professionale per autenticazione</p>
                    </div>
                </div>

                <div
                    class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                            <i class="fas fa-building text-2xl text-blu-algoritmo"></i>
                        </div>
                        <h3 class="renaissance-title text-2xl font-bold">Servizi PA & Istituzioni</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>eIDAS compliance</strong> per Pubbliche Amministrazioni</p>
                        <p><strong>Patrimonio culturale</strong> musei, biblioteche, archivi</p>
                        <p><strong>Catalogazione digitale</strong> con standard internazionali</p>
                        <p><strong>Export certification</strong> per opere d'arte</p>
                        <p class="font-semibold text-blu-algoritmo">✅ Soluzione enterprise per istituzioni</p>
                    </div>
                </div>
            </div>

            <!-- CoA Workflow -->
            <div class="mx-auto max-w-4xl">
                <h3 class="renaissance-title mb-8 text-center text-2xl font-bold">Workflow CoA</h3>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                            1</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Caricamento Opera</h4>
                            <p class="text-green-100">Upload immagini HD, metadati tecnici, informazioni artistiche</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                            2</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Traits Classification</h4>
                            <p class="text-green-100">Sistema avanzato per tecnica, materiali, supporto con vocabolario
                                standardizzato</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                            3</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Blockchain Certification</h4>
                            <p class="text-green-100">Registrazione immutabile su Algorand con hash verification</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div
                            class="bg-oro-fiorentino flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white">
                            4</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Certificato Digitale</h4>
                            <p class="text-green-100">PDF professionale con QR code per verifica pubblica</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gli Archetipi del Miliardo -->
    <section id="archetipi" class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    Gli <span class="text-oro-fiorentino">Archetipi FlorenceEGI</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    Ecosistema di attori specializzati che danno vita alla co-creazione artistica e ambientale
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <!-- Creator -->
                <a href="{{ route('info.creator') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/10">
                        <i class="fas fa-brush text-2xl text-viola-innovazione"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Creator</h3>
                    <p class="font-body text-sm text-grigio-pietra">Artisti che creano EGI con impatto certificato</p>
                </a>

                <!-- Collector -->
                <a href="{{ route('archetypes.collector') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="bg-oro-fiorentino/10 mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full">
                        <i class="fas fa-gem text-oro-fiorentino text-2xl"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Collector</h3>
                    <p class="font-body text-sm text-grigio-pietra">Collezionisti con passion e investimento
                        sostenibile</p>
                </a>

                <!-- Patron -->
                <a href="{{ route('archetypes.patron') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-rosso-urgenza/10">
                        <i class="fas fa-heart text-2xl text-rosso-urgenza"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Mecenati</h3>
                    <p class="font-body text-sm text-grigio-pietra">Facilitatori prestigiosi arte-sostenibilità</p>
                </a>

                <!-- EPP -->
                <a href="{{ route('info.epp') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/10">
                        <i class="fas fa-seedling text-2xl text-verde-rinascita"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">EPP</h3>
                    <p class="font-body text-sm text-grigio-pietra">Progetti ambientali verificati per impatto reale
                    </p>
                </a>

                <!-- Aziende -->
                <a href="{{ route('info.aziende') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/10">
                        <i class="fas fa-building text-2xl text-blu-algoritmo"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Aziende</h3>
                    <p class="font-body text-sm text-grigio-pietra">Corporate ESG e marketing innovativo</p>
                </a>

                <!-- PA -->
                <a href="{{ route('archetypes.pa-entity') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-arancio-energia/10">
                        <i class="fas fa-university text-2xl text-arancio-energia"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">PA</h3>
                    <p class="font-body text-sm text-grigio-pietra">Pubbliche Amministrazioni e patrimonio culturale
                    </p>
                </a>

                <!-- Trader Pro -->
                <a href="{{ route('info.trader-pro') }}"
                    class="renaissance-card elegant-hover block p-6 text-center transition-transform hover:scale-105">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-viola-innovazione/10">
                        <i class="fas fa-chart-line text-2xl text-viola-innovazione"></i>
                    </div>
                    <h3 class="renaissance-title mb-2 text-lg font-bold text-grigio-pietra">Trader Pro</h3>
                    <p class="font-body text-sm text-grigio-pietra">Trading ad alta frequenza su EGI pt</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Impatto Planetario -->
    <section id="impatto" class="section-dark py-16 text-white sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold sm:text-4xl md:text-5xl">
                    Impatto Ambientale: <span class="text-oro-fiorentino">20% Automatico per EPP</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-green-100">
                    Ogni transazione destina automaticamente il 20% a progetti ambientali verificati
                </p>
            </div>

            <!-- Scenario Realistico -->
            <div class="mb-12 text-center">
                <div
                    class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 mx-auto max-w-4xl bg-gradient-to-br p-8 text-grigio-pietra">
                    <h3 class="renaissance-title mb-6 text-2xl font-bold">Impatto Concreto e Verificabile</h3>
                    <div class="grid gap-6 font-body md:grid-cols-3">
                        <div class="text-center">
                            <div class="text-oro-fiorentino text-3xl font-bold">20%</div>
                            <p class="text-sm">Delle transazioni destinate agli EPP</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-verde-rinascita">Crescita</div>
                            <p class="text-sm">Progetti EPP attivi e verificati</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blu-algoritmo">100%</div>
                            <p class="text-sm">Trasparenza blockchain</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EPP Projects Realistici -->
            <div class="mb-16 grid gap-8 md:grid-cols-3">
                <div
                    class="renaissance-card bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div
                            class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-verde-rinascita/20">
                            <i class="fas fa-tree text-2xl text-verde-rinascita"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Appropriate Restoration Forestry</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>Riforestazione Attenta e Rispettosa</strong></p>
                        <p>La deforestazione erode il polmone verde del pianeta</p>
                        <p>Riforestazione mirata per ecosistemi locali</p>
                        <p>Riabilitazione equilibrio ecosistemi</p>
                        <p class="text-sm text-verde-rinascita"><i class="fas fa-exclamation-triangle mr-1"></i>Ogni secondo = un campo di calcio perduto</p>
                    </div>
                </div>

                <div
                    class="renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div class="mr-4 flex h-16 w-16 items-center justify-center rounded-full bg-blu-algoritmo/20">
                            <i class="fas fa-water text-2xl text-blu-algoritmo"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Aquatic Plastic Removal</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>Rimozione Plastica dagli Oceani</strong></p>
                        <p>Isole di plastica = estensione di un continente</p>
                        <p>Azione cruciale per mari, laghi e fiumi</p>
                        <p>Affrontiamo la crisi globale con coraggio</p>
                        <p class="text-sm text-blu-algoritmo"><i class="fas fa-exclamation-triangle mr-1"></i>Emergenza plastica acquatica</p>
                    </div>
                </div>

                <div
                    class="renaissance-card from-oro-fiorentino/10 to-oro-fiorentino/5 bg-gradient-to-br p-8 text-grigio-pietra">
                    <div class="mb-6 flex items-center">
                        <div class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-bee text-oro-fiorentino text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Bee Population Enhancement</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>Protezione degli Impollinatori</strong></p>
                        <p>Potenziamento popolazioni di api</p>
                        <p>Supporto biodiversità</p>
                        <p>Preservazione impollinazione</p>
                        <p class="text-oro-fiorentino text-sm"><i class="fas fa-check mr-1"></i>Un terzo del cibo dipende dalle api</p>
                    </div>
                </div>
            </div>

            <!-- Crescita Scalabile -->
            <div class="text-center">
                <div
                    class="renaissance-card mx-auto max-w-4xl bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 p-8 text-grigio-pietra">
                    <h3 class="renaissance-title mb-6 text-2xl font-bold">Crescita Scalabile</h3>
                    <div class="grid gap-6 font-body md:grid-cols-2">
                        <div>
                            <h4 class="mb-2 font-bold text-viola-innovazione">Fase Iniziale: Crescita della Community</h4>
                            <p>Sviluppo della rete di artisti, collezionisti e progetti EPP verificati</p>
                        </div>
                        <div>
                            <h4 class="mb-2 font-bold text-viola-innovazione">Crescita Organica: Espansione Impatti</h4>
                            <p>Moltiplicazione degli impatti ambientali attraverso la community attiva</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('info.epp') }}"
                            class="inline-flex items-center rounded-lg bg-viola-innovazione px-6 py-3 text-white transition-all hover:bg-viola-innovazione/80">
                            <i class="fas fa-seedling mr-2"></i>
                            Scopri i Progetti EPP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- I Nostri Valori -->
    <section class="bg-gray-50 py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                    I Valori che <span class="text-oro-fiorentino">Guidano le Nostre Azioni</span>
                </h2>
                <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                    I nostri valori rappresentano i principi guida e gli ideali fondamentali che informano e orientano tutte le nostre attività
                </p>
            </div>

            <div class="grid gap-8 mb-16 md:grid-cols-2 lg:grid-cols-3">
                <!-- Rigenerazione dell'Ecosistema -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-verde-rinascita/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-seedling text-verde-rinascita text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Rigenerazione dell'Ecosistema</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        La piattaforma si impegna attivamente nella pulizia delle acque del pianeta, nella riforestazione e nel sostegno alla popolazione delle api, contribuendo a un impatto ambientale positivo.
                    </p>
                </div>

                <!-- Sostegno agli Artisti -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-viola-innovazione/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-palette text-viola-innovazione text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Sostegno agli Artisti</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        FlorenceEGI offre supporto agli artisti tradizionali per entrare e navigare nel mondo digitale, facilitando la transizione e la valorizzazione del loro lavoro nel metaverso digitale.
                    </p>
                </div>

                <!-- Innovazione e Utilità Pragmatica -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-lightbulb text-oro-fiorentino text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Innovazione e Utilità Pragmatica</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        Gli EGI acquisiscono un valore speculativo e una valenza pratica e funzionale con un impatto sociale e ambientale concreto.
                    </p>
                </div>

                <!-- Investimento Concreto e Responsabile -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-blu-algoritmo/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-shield-alt text-blu-algoritmo text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Investimento Concreto e Responsabile</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        Gli EGI rappresentano un impegno verso progetti umanitari e di protezione ambientale, riflettendo un approccio etico e sostenibile negli affari.
                    </p>
                </div>

                <!-- Rivoluzione del Concetto di Proprietà -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-rosso-urgenza/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-exchange-alt text-rosso-urgenza text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Rivoluzione del Concetto di Proprietà</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        La piattaforma mira a rivoluzionare il concetto di proprietà privata, incoraggiando un cambiamento sociale positivo attraverso l'innovazione tecnologica.
                    </p>
                </div>

                <!-- Equilibrio tra Ambiente, Economia e Società -->
                <div class="renaissance-card elegant-hover p-8">
                    <div class="mb-6 flex items-center">
                        <div class="bg-arancio-energia/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                            <i class="fas fa-balance-scale text-arancio-energia text-2xl"></i>
                        </div>
                        <h3 class="renaissance-title text-xl font-bold">Equilibrio tra Ambiente, Economia e Società</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        Il focus sugli EGI mira a creare un equilibrio tra ambiente, economia e benessere sociale, attraverso un'economia che si armonizzi con l'ambiente e la società.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- L'Equilibrium -->
    <section class="bg-gradient-to-br from-oro-fiorentino/10 to-verde-rinascita/10 py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center">
                    <h2 class="renaissance-title mb-4 text-3xl font-bold text-grigio-pietra sm:text-4xl md:text-5xl">
                        L'<span class="text-oro-fiorentino">Equilibrium</span>
                    </h2>
                    <p class="mx-auto max-w-3xl font-body text-xl text-grigio-pietra">
                        Il cuore pulsante dell'ecosistema FlorenceEGI: dove arte, tecnologia e impatto ambientale si fondono in perfetta armonia
                    </p>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Cos'è l'Equilibrium -->
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div class="bg-oro-fiorentino/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-atom text-oro-fiorentino text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">Il Carburante del Cambiamento</h3>
                        </div>
                        <p class="font-body text-grigio-pietra mb-4">
                            L'Equilibrium rappresenta i fondi raccolti attraverso le attività della piattaforma FlorenceEGI, 
                            destinati automaticamente ai progetti EPP (Environment Protection Programs).
                        </p>
                        <p class="font-body text-grigio-pietra">
                            Ogni transazione sulla piattaforma genera un 20% di Equilibrium che fluisce direttamente 
                            verso la rigenerazione ambientale, creando un ciclo virtuoso tra arte digitale e sostenibilità.
                        </p>
                    </div>

                    <!-- Come Funziona -->
                    <div class="renaissance-card elegant-hover p-8">
                        <div class="mb-6 flex items-center">
                            <div class="bg-verde-rinascita/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-cogs text-verde-rinascita text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">Meccanismo Automatico</h3>
                        </div>
                        <p class="font-body text-grigio-pietra mb-4">
                            Quando un Creator crea una collection, sceglie un EPP specifico. Lo smart contract è programmato 
                            per inviare automaticamente il 20% di ogni transazione (Mint o Rebind) al wallet dell'EPP selezionato.
                        </p>
                        <p class="font-body text-grigio-pietra">
                            Questo processo è trasparente, immediato e verificabile sulla blockchain, garantendo che ogni 
                            opera d'arte contribuisca concretamente alla rigenerazione ambientale.
                        </p>
                    </div>

                    <!-- Visione Narrativa -->
                    <div class="renaissance-card elegant-hover p-8 lg:col-span-2">
                        <div class="mb-6 flex items-center justify-center">
                            <div class="bg-blu-algoritmo/20 mr-4 flex h-16 w-16 items-center justify-center rounded-full">
                                <i class="fas fa-heart text-blu-algoritmo text-2xl"></i>
                            </div>
                            <h3 class="renaissance-title text-xl font-bold">Agapi e Aisthitikè: Amore ed Estetica</h3>
                        </div>
                        <p class="font-body text-grigio-pietra text-center max-w-4xl mx-auto">
                            Nell'universo narrativo di Natan, l'Equilibrium è la molecola fondamentale composta da due proteine: 
                            <strong>agapi</strong> (amore) e <strong>aisthitikè</strong> (estetica). È il carburante che permette 
                            di trasformare il negativo in positivo, l'inquinato in puro, lo scarso in abbondante. 
                            L'Equilibrium non è solo una risorsa, ma il simbolo di un impegno collettivo verso un mondo 
                            più equilibrato, sostenibile e artisticamente ricco.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="bg-white py-16 sm:py-24">
        <div class="golden-ratio-container px-4 sm:px-6">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="renaissance-title mb-6 text-3xl font-bold text-grigio-pietra sm:text-4xl">
                    Unisciti al <span class="text-oro-fiorentino">Rinascimento Digitale</span>
                </h2>
                <p class="mb-8 font-body text-xl text-grigio-pietra">
                    <em>"Non promettiamo di cambiare il mondo. Creiamo le condizioni perché il mondo si cambi da solo,
                        una transazione alla volta, un progetto EPP alla volta, un EGI alla volta."</em>
                </p>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('register') }}"
                        class="cta-primary elegant-hover inline-flex items-center justify-center rounded-xl px-8 py-4 text-lg font-semibold text-white">
                        <i class="fas fa-rocket mr-3"></i>
                        Inizia il Tuo Viaggio
                    </a>
                    <a href="{{ route('archetypes.patron') }}"
                        class="border-oro-fiorentino text-oro-fiorentino hover:bg-oro-fiorentino elegant-hover inline-flex items-center justify-center rounded-xl border-2 px-8 py-4 text-lg font-semibold transition-all hover:text-white">
                        <i class="fas fa-users mr-3"></i>
                        Scopri i Ruoli
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
