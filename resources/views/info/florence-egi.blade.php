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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">

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
            max-width: 1618px; /* 1000 * 1.618 */
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
    <header class="text-white shadow-lg bg-blu-algoritmo">
        <div class="px-4 py-4 golden-ratio-container sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="text-3xl fas fa-infinity sm:text-4xl text-oro-fiorentino"></i>
                    <div>
                        <h1 class="text-xl font-bold sm:text-2xl renaissance-title">FlorenceEGI</h1>
                        <p class="text-sm text-blue-200 sm:text-base font-body">Il Rinascimento Digitale</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden space-x-6 md:flex">
                    <a href="#visione" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">Visione</a>
                    <a href="#problema" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">Problema</a>
                    <a href="#soluzione" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">Soluzione</a>
                    <a href="#tecnologia" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">Tecnologia</a>
                    <a href="#coa" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">CoA</a>
                    <a href="#impatto" class="text-sm transition hover:text-oro-fiorentino lg:text-base font-body">Impatto</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="block p-2 transition-colors rounded-md md:hidden hover:bg-blue-700">
                    <i class="text-2xl fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden pb-4 mt-4 border-t border-blue-600 md:hidden">
                <div class="pt-4 space-y-3">
                    <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-home text-oro-fiorentino"></i>
                        Torna alla Home
                    </a>
                    <a href="#visione" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-eye text-oro-fiorentino"></i>
                        Visione
                    </a>
                    <a href="#problema" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-exclamation-triangle text-oro-fiorentino"></i>
                        Problema
                    </a>
                    <a href="#soluzione" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-lightbulb text-oro-fiorentino"></i>
                        Soluzione
                    </a>
                    <a href="#tecnologia" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-cogs text-oro-fiorentino"></i>
                        Tecnologia
                    </a>
                    <a href="#coa" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-certificate text-oro-fiorentino"></i>
                        CoA
                    </a>
                    <a href="#impatto" class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <i class="mr-3 text-lg fas fa-leaf text-oro-fiorentino"></i>
                        Impatto
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="visione" class="text-white hero-background">
        <div class="px-4 py-16 golden-ratio-container sm:px-6 sm:py-24">
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="mb-6 text-4xl font-bold leading-tight renaissance-title sm:text-5xl md:text-6xl">
                    Il <span class="text-oro-fiorentino">Rinascimento Digitale</span><br>
                    che Unisce Arte, Tecnologia<br>
                    e Rigenerazione Planetaria
                </h1>
                <p class="max-w-4xl mx-auto mb-8 text-xl text-green-100 font-body sm:text-2xl">
                    <strong class="text-oro-fiorentino">FlorenceEGI è il primo marketplace che risolve il trilemma impossibile dell'ecosistema NFT:</strong><br>
                    Qualità Artistica + Liquidità Massima + Impatto Ambientale Reale
                </p>
                <div class="max-w-4xl mx-auto mb-8 text-lg font-body">
                    <strong>Matematica del Miliardo:</strong> €1B volume = €200M+ impatto EPP automatico + €650M+ guadagni creator + €150M ricavi piattaforma
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="#soluzione" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white cta-primary rounded-xl elegant-hover">
                        <i class="mr-3 fas fa-rocket"></i>
                        Scopri la Soluzione
                    </a>
                    <a href="#tecnologia" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold transition-all border-2 border-oro-fiorentino text-oro-fiorentino rounded-xl hover:bg-oro-fiorentino hover:text-blu-algoritmo elegant-hover">
                        <i class="mr-3 fas fa-cogs"></i>
                        Tecnologia Algorand
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Il Problema: Trilemma NFT -->
    <section id="problema" class="py-16 bg-white sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl text-grigio-pietra">
                    Il Problema che <span class="text-rosso-urgenza">Risolviamo</span>
                </h2>
                <p class="max-w-3xl mx-auto text-xl font-body text-grigio-pietra">
                    Il mercato NFT è paralizzato da un trilemma che nessuna piattaforma è riuscita a risolvere
                </p>
            </div>

            <!-- Trilemma Diagram -->
            <div class="relative max-w-4xl mx-auto mb-16">
                <div class="flex items-center justify-center h-96">
                    <!-- Triangle visualization -->
                    <div class="relative">
                        <!-- Top point: Qualità Artistica -->
                        <div class="absolute transform -translate-x-1/2 -top-16 left-1/2">
                            <div class="trilemma-point bg-viola-innovazione">
                                <div>
                                    <i class="mb-2 text-2xl fas fa-palette"></i><br>
                                    QUALITÀ<br>ARTISTICA
                                </div>
                            </div>
                        </div>

                        <!-- Bottom left: Liquidità -->
                        <div class="absolute top-32 -left-16">
                            <div class="trilemma-point bg-blu-algoritmo">
                                <div>
                                    <i class="mb-2 text-2xl fas fa-chart-line"></i><br>
                                    LIQUIDITÀ<br>TRADING
                                </div>
                            </div>
                        </div>

                        <!-- Bottom right: Impatto -->
                        <div class="absolute top-32 -right-16">
                            <div class="trilemma-point bg-verde-rinascita">
                                <div>
                                    <i class="mb-2 text-2xl fas fa-leaf"></i><br>
                                    IMPATTO<br>AMBIENTALE
                                </div>
                            </div>
                        </div>

                        <!-- Center: Impossibilità -->
                        <div class="flex items-center justify-center w-32 h-32 font-bold text-center text-white rounded-full bg-rosso-urgenza">
                            <div>
                                <i class="mb-2 text-3xl fas fa-times"></i><br>
                                TRILEMMA<br>IMPOSSIBILE
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Competitor Analysis -->
            <div class="grid gap-8 mb-16 md:grid-cols-3">
                <div class="p-6 text-center renaissance-card">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full">
                        <i class="text-2xl fas fa-times text-rosso-urgenza"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold renaissance-title text-grigio-pietra">OpenSea</h3>
                    <p class="text-sm font-body text-grigio-pietra">
                        ✅ Qualità artistica<br>
                        ❌ Zero liquidità<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>

                <div class="p-6 text-center renaissance-card">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full">
                        <i class="text-2xl fas fa-times text-rosso-urgenza"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold renaissance-title text-grigio-pietra">Blur</h3>
                    <p class="text-sm font-body text-grigio-pietra">
                        ✅ Liquidità massima<br>
                        ❌ Zero qualità<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>

                <div class="p-6 text-center renaissance-card">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full">
                        <i class="text-2xl fas fa-times text-rosso-urgenza"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold renaissance-title text-grigio-pietra">Foundation</h3>
                    <p class="text-sm font-body text-grigio-pietra">
                        ✅ Qualità premium<br>
                        ❌ Liquidità inesistente<br>
                        ❌ Zero impatto ambientale
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- La Soluzione: Architettura Anti-Trilemma -->
    <section id="soluzione" class="py-16 text-white section-dark sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl">
                    La Soluzione: <span class="text-oro-fiorentino">Architettura Anti-Trilemma</span>
                </h2>
                <p class="max-w-3xl mx-auto text-xl text-green-100 font-body">
                    Il primo marketplace che offre TUTTE E TRE le caratteristiche simultaneamente
                </p>
            </div>

            <!-- EGI Dual Flow -->
            <div class="grid gap-8 mb-16 lg:grid-cols-2">
                <div class="p-8 renaissance-card bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-viola-innovazione/20">
                            <i class="text-2xl fas fa-gem text-viola-innovazione"></i>
                        </div>
                        <h3 class="text-2xl font-bold renaissance-title">EGI Fisici</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Arte autentica</strong> con certificazione CoA blockchain</p>
                        <p><strong>Utility reale</strong> con diritti e royalties</p>
                        <p><strong>20% automatico EPP</strong> per impatto ambientale</p>
                        <p class="font-semibold text-viola-innovazione">✅ Collezionismo premium con impatto certificato</p>
                    </div>
                </div>

                <div class="p-8 renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-blu-algoritmo/20">
                            <i class="text-2xl fas fa-bolt text-blu-algoritmo"></i>
                        </div>
                        <h3 class="text-2xl font-bold renaissance-title">EGI pt</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Trading velocity</strong> ad alta frequenza</p>
                        <p><strong>Fee competitive</strong> (1.5%) dinamiche</p>
                        <p><strong>Impact garantito</strong> su ogni transazione</p>
                        <p class="font-semibold text-blu-algoritmo">✅ Liquidità massima con rigenerazione automatica</p>
                    </div>
                </div>
            </div>

            <!-- Infrastructure -->
            <div class="text-center">
                <div class="max-w-4xl p-8 mx-auto renaissance-card bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 text-grigio-pietra">
                    <div class="flex items-center justify-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-verde-rinascita/20">
                            <i class="text-2xl fas fa-infinity text-verde-rinascita"></i>
                        </div>
                        <h3 class="text-2xl font-bold renaissance-title">Algorand Infrastructure</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-3 font-body">
                        <div>
                            <i class="mb-2 text-3xl fas fa-leaf text-verde-rinascita"></i>
                            <p><strong>Carbon Negative</strong><br>Blockchain che assorbe CO2</p>
                        </div>
                        <div>
                            <i class="mb-2 text-3xl fas fa-tachometer-alt text-verde-rinascita"></i>
                            <p><strong>6000 TPS</strong><br>Costi €0.0001/transazione</p>
                        </div>
                        <div>
                            <i class="mb-2 text-3xl fas fa-shield-alt text-verde-rinascita"></i>
                            <p><strong>Sicurezza Enterprise</strong><br>Finalità 2.5 secondi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tecnologia: Progressive Web3 -->
    <section id="tecnologia" class="py-16 bg-white sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl text-grigio-pietra">
                    Tecnologia: <span class="text-oro-fiorentino">Progressive Web3</span>
                </h2>
                <p class="max-w-3xl mx-auto text-xl font-body text-grigio-pietra">
                    L'infrastruttura per €1B sostenibile con adozione di massa
                </p>
            </div>

            <!-- Architecture Layers -->
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Layer 1 -->
                <div class="p-6 border-l-4 renaissance-card border-verde-rinascita">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 mr-4 font-bold text-white rounded-full bg-verde-rinascita">1</div>
                        <h3 class="text-xl font-bold renaissance-title text-grigio-pietra">Layer 1 (99% users)</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Email login + carta credito + UX familiare come Amazon</strong><br>
                        Esperienza Web2 tradizionale per adozione di massa
                    </p>
                </div>

                <!-- Layer 2 -->
                <div class="p-6 border-l-4 renaissance-card border-oro-fiorentino">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 mr-4 font-bold text-white rounded-full bg-oro-fiorentino">2</div>
                        <h3 class="text-xl font-bold renaissance-title text-grigio-pietra">Layer 2 (Bridge)</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Educazione graduale + wallet custodial + supporto umano</strong><br>
                        Transizione guidata verso la decentralizzazione
                    </p>
                </div>

                <!-- Layer 3 -->
                <div class="p-6 border-l-4 renaissance-card border-viola-innovazione">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 mr-4 font-bold text-white rounded-full bg-viola-innovazione">3</div>
                        <h3 class="text-xl font-bold renaissance-title text-grigio-pietra">Layer 3 (1% power users)</h3>
                    </div>
                    <p class="font-body text-grigio-pietra">
                        <strong>Self-custody + DeFi + API access + trading avanzato</strong><br>
                        Controllo completo per utenti esperti
                    </p>
                </div>
            </div>

            <!-- Smart Contracts Innovation -->
            <div class="mt-16">
                <h3 class="mb-8 text-2xl font-bold text-center renaissance-title text-grigio-pietra">Smart Contracts Rivoluzionari</h3>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="p-6 text-center renaissance-card">
                        <i class="mb-4 text-3xl fas fa-chart-line text-oro-fiorentino"></i>
                        <h4 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Fee Dinamiche On-Chain</h4>
                        <p class="text-sm font-body text-grigio-pietra">Primi al mondo con parametri auto-adattivi</p>
                    </div>
                    <div class="p-6 text-center renaissance-card">
                        <i class="mb-4 text-3xl fas fa-share-alt text-verde-rinascita"></i>
                        <h4 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">EPP Distribution</h4>
                        <p class="text-sm font-body text-grigio-pietra">20% split immutabile, trasparente, verificabile</p>
                    </div>
                    <div class="p-6 text-center renaissance-card">
                        <i class="mb-4 text-3xl fas fa-infinity text-viola-innovazione"></i>
                        <h4 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Royalty Engine</h4>
                        <p class="text-sm font-body text-grigio-pietra">Pagamenti automatici crescenti per sempre</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CoA: Certificate of Authenticity -->
    <section id="coa" class="py-16 text-white section-dark sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl">
                    <span class="text-oro-fiorentino">CoA</span>: Certificate of Authenticity
                </h2>
                <p class="max-w-3xl mx-auto text-xl text-green-100 font-body">
                    Sistema di certificazione digitale professionale per arte e beni culturali
                </p>
            </div>

            <!-- CoA Features -->
            <div class="grid gap-8 mb-16 lg:grid-cols-2">
                <div class="p-8 renaissance-card bg-gradient-to-br from-oro-fiorentino/10 to-oro-fiorentino/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-oro-fiorentino/20">
                            <i class="text-2xl fas fa-certificate text-oro-fiorentino"></i>
                        </div>
                        <h3 class="text-2xl font-bold renaissance-title">Certificazione Blockchain</h3>
                    </div>
                    <div class="space-y-4 font-body">
                        <p><strong>Hash SHA-256</strong> per verifiche immutabili</p>
                        <p><strong>Metadati tecnici</strong> con sistema traits avanzato</p>
                        <p><strong>Chain of custody</strong> tracciabile e verificabile</p>
                        <p><strong>PDF certificati</strong> con firme digitali</p>
                        <p class="font-semibold text-oro-fiorentino">✅ Standard professionale per autenticazione</p>
                    </div>
                </div>

                <div class="p-8 renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-blu-algoritmo/20">
                            <i class="text-2xl fas fa-building text-blu-algoritmo"></i>
                        </div>
                        <h3 class="text-2xl font-bold renaissance-title">Servizi PA & Istituzioni</h3>
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
            <div class="max-w-4xl mx-auto">
                <h3 class="mb-8 text-2xl font-bold text-center renaissance-title">Workflow CoA</h3>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-oro-fiorentino">1</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Caricamento Opera</h4>
                            <p class="text-green-100">Upload immagini HD, metadati tecnici, informazioni artistiche</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-oro-fiorentino">2</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Traits Classification</h4>
                            <p class="text-green-100">Sistema avanzato per tecnica, materiali, supporto con vocabolario standardizzato</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-oro-fiorentino">3</div>
                        <div class="font-body">
                            <h4 class="text-lg font-semibold">Blockchain Certification</h4>
                            <p class="text-green-100">Registrazione immutabile su Algorand con hash verification</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-oro-fiorentino">4</div>
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
    <section id="archetipi" class="py-16 bg-white sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl text-grigio-pietra">
                    I Sette <span class="text-oro-fiorentino">Archetipi del Miliardo</span>
                </h2>
                <p class="max-w-3xl mx-auto text-xl font-body text-grigio-pietra">
                    Ecosistema di attori con super-poteri specifici per raggiungere €1B di volume
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <!-- Creator -->
                <a href="{{ route('info.creator') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-viola-innovazione/10">
                        <i class="text-2xl fas fa-brush text-viola-innovazione"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Creator</h3>
                    <p class="text-sm font-body text-grigio-pietra">Artisti che creano EGI con impatto certificato</p>
                </a>

                <!-- Collector -->
                <a href="{{ route('archetypes.collector') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-oro-fiorentino/10">
                        <i class="text-2xl fas fa-gem text-oro-fiorentino"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Collector</h3>
                    <p class="text-sm font-body text-grigio-pietra">Collezionisti con passion e investimento sostenibile</p>
                </a>

                <!-- Patron -->
                <a href="{{ route('archetypes.patron') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-rosso-urgenza/10">
                        <i class="text-2xl fas fa-heart text-rosso-urgenza"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Mecenati</h3>
                    <p class="text-sm font-body text-grigio-pietra">Facilitatori prestigiosi arte-sostenibilità</p>
                </a>

                <!-- EPP -->
                <a href="{{ route('info.epp') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-verde-rinascita/10">
                        <i class="text-2xl fas fa-seedling text-verde-rinascita"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">EPP</h3>
                    <p class="text-sm font-body text-grigio-pietra">Progetti ambientali verificati per impatto reale</p>
                </a>

                <!-- Aziende -->
                <a href="{{ route('info.aziende') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-blu-algoritmo/10">
                        <i class="text-2xl fas fa-building text-blu-algoritmo"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Aziende</h3>
                    <p class="text-sm font-body text-grigio-pietra">Corporate ESG e marketing innovativo</p>
                </a>

                <!-- PA -->
                <a href="{{ route('archetypes.pa-entity') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-arancio-energia/10">
                        <i class="text-2xl fas fa-university text-arancio-energia"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">PA</h3>
                    <p class="text-sm font-body text-grigio-pietra">Pubbliche Amministrazioni e patrimonio culturale</p>
                </a>

                <!-- Trader Pro -->
                <a href="{{ route('info.trader-pro') }}" class="p-6 text-center renaissance-card elegant-hover block hover:scale-105 transition-transform">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-viola-innovazione/10">
                        <i class="text-2xl fas fa-chart-line text-viola-innovazione"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-bold renaissance-title text-grigio-pietra">Trader Pro</h3>
                    <p class="text-sm font-body text-grigio-pietra">Trading ad alta frequenza su EGI pt</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Impatto Planetario -->
    <section id="impatto" class="py-16 text-white section-dark sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="mb-4 text-3xl font-bold renaissance-title sm:text-4xl md:text-5xl">
                    Impatto Ambientale: <span class="text-oro-fiorentino">20% Automatico per EPP</span>
                </h2>
                <p class="max-w-3xl mx-auto text-xl text-green-100 font-body">
                    Ogni transazione destina automaticamente il 20% a progetti ambientali verificati
                </p>
            </div>

            <!-- Scenario Realistico -->
            <div class="mb-12 text-center">
                <div class="max-w-4xl p-8 mx-auto renaissance-card bg-gradient-to-br from-oro-fiorentino/10 to-oro-fiorentino/5 text-grigio-pietra">
                    <h3 class="mb-6 text-2xl font-bold renaissance-title">Scenario Anno 1: €100K Volume di Mercato</h3>
                    <div class="grid gap-6 md:grid-cols-3 font-body">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-oro-fiorentino">€20K</div>
                            <p class="text-sm">Destinati automaticamente agli EPP</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-verde-rinascita">3-5</div>
                            <p class="text-sm">Progetti EPP finanziabili</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blu-algoritmo">100%</div>
                            <p class="text-sm">Trasparenza blockchain</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EPP Projects Realistici -->
            <div class="grid gap-8 mb-16 md:grid-cols-3">
                <div class="p-8 renaissance-card bg-gradient-to-br from-verde-rinascita/10 to-verde-rinascita/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-verde-rinascita/20">
                            <i class="text-2xl fas fa-tree text-verde-rinascita"></i>
                        </div>
                        <h3 class="text-xl font-bold renaissance-title">Riforestazione Locale</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>€5-8K per progetto</strong></p>
                        <p>50-100 ettari per progetto</p>
                        <p>1000-2000 alberi piantati</p>
                        <p>10-20 tons CO2 assorbite/anno</p>
                        <p class="text-verde-rinascita text-sm"><i class="fas fa-check mr-1"></i>Partnership con vivai locali</p>
                    </div>
                </div>

                <div class="p-8 renaissance-card bg-gradient-to-br from-blu-algoritmo/10 to-blu-algoritmo/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-blu-algoritmo/20">
                            <i class="text-2xl fas fa-water text-blu-algoritmo"></i>
                        </div>
                        <h3 class="text-xl font-bold renaissance-title">Pulizia Acque</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>€3-5K per progetto</strong></p>
                        <p>1-2 ton plastica rimossa</p>
                        <p>Pulizia fiumi/coste locali</p>
                        <p>Coinvolgimento volontari</p>
                        <p class="text-blu-algoritmo text-sm"><i class="fas fa-check mr-1"></i>Collaborazione con associazioni</p>
                    </div>
                </div>

                <div class="p-8 renaissance-card bg-gradient-to-br from-oro-fiorentino/10 to-oro-fiorentino/5 text-grigio-pietra">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-oro-fiorentino/20">
                            <i class="text-2xl fas fa-leaf text-oro-fiorentino"></i>
                        </div>
                        <h3 class="text-xl font-bold renaissance-title">Biodiversità Urbana</h3>
                    </div>
                    <div class="space-y-2 font-body">
                        <p><strong>€2-4K per progetto</strong></p>
                        <p>Orti urbani condivisi</p>
                        <p>Hotel per insetti</p>
                        <p>Piante mellifere</p>
                        <p class="text-oro-fiorentino text-sm"><i class="fas fa-check mr-1"></i>Progetti pilota documentati</p>
                    </div>
                </div>
            </div>

            <!-- Crescita Scalabile -->
            <div class="text-center">
                <div class="max-w-4xl p-8 mx-auto renaissance-card bg-gradient-to-br from-viola-innovazione/10 to-viola-innovazione/5 text-grigio-pietra">
                    <h3 class="mb-6 text-2xl font-bold renaissance-title">Crescita Scalabile</h3>
                    <div class="grid gap-6 md:grid-cols-2 font-body">
                        <div>
                            <h4 class="font-bold text-viola-innovazione mb-2">Anno 2-3: €500K-1M Volume</h4>
                            <p>€100K-200K per EPP = 15-25 progetti finanziati</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-viola-innovazione mb-2">Anno 4+: Crescita Organica</h4>
                            <p>Reinvestimento impatti verificati per espansione</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('info.epp') }}" class="inline-flex items-center px-6 py-3 text-white transition-all bg-viola-innovazione rounded-lg hover:bg-viola-innovazione/80">
                            <i class="fas fa-seedling mr-2"></i>
                            Scopri i Progetti EPP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-white sm:py-24">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="mb-6 text-3xl font-bold renaissance-title sm:text-4xl text-grigio-pietra">
                    Unisciti al <span class="text-oro-fiorentino">Rinascimento Digitale</span>
                </h2>
                <p class="mb-8 text-xl font-body text-grigio-pietra">
                    <em>"Non promettiamo di cambiare il mondo. Creiamo le condizioni perché il mondo si cambi da solo, una transazione alla volta, un progetto EPP alla volta, €1 miliardo di impatto virtuoso alla volta."</em>
                </p>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white cta-primary rounded-xl elegant-hover">
                        <i class="mr-3 fas fa-rocket"></i>
                        Inizia il Tuo Viaggio
                    </a>
                    <a href="{{ route('archetypes.patron') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold transition-all border-2 border-oro-fiorentino text-oro-fiorentino rounded-xl hover:bg-oro-fiorentino hover:text-white elegant-hover">
                        <i class="mr-3 fas fa-users"></i>
                        Scopri i Ruoli
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Essenziale -->
    <footer class="py-8 text-white bg-blu-algoritmo">
        <div class="px-4 golden-ratio-container sm:px-6">
            <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                <div class="flex items-center space-x-3">
                    <i class="text-xl fas fa-infinity text-oro-fiorentino"></i>
                    <div class="text-center md:text-left">
                        <div class="font-bold renaissance-title">FlorenceEGI</div>
                        <div class="text-sm text-blue-200 font-body">Il Rinascimento Digitale</div>
                    </div>
                </div>
                <div class="flex space-x-6 text-sm font-body">
                    <a href="{{ route('home') }}" class="transition-colors hover:text-oro-fiorentino">Home</a>
                    <a href="/privacy-policy" class="transition-colors hover:text-oro-fiorentino">Privacy</a>
                    <a href="/terms" class="transition-colors hover:text-oro-fiorentino">Termini</a>
                    <a href="mailto:info@florenceegi.com" class="transition-colors hover:text-oro-fiorentino">Contatti</a>
                </div>
            </div>
        </div>
    </footer>

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
                anchor.addEventListener('click', function (e) {
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
