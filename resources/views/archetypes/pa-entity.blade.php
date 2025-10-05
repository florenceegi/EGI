<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigillo Digitale per Atti Pubblici - La Soluzione per la Trasparenza</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Custom Colors & Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                colors: {
                    'institutional-blue': '#1E3A8A',
                    'florence-gold': '#D97706',
                    'compliance-green': '#166534'
                }
            }
        }
    }
    </script>

    <style>
    /* Animazione per elementi in fade-in */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    </style>
</head>

<body class="overflow-x-hidden text-gray-900 bg-gray-50">

    <!-- Header Istituzionale -->
    <header class="sticky top-0 z-50 py-4 text-white shadow-lg bg-institutional-blue sm:py-5">
        <div class="container px-4 mx-auto max-w-6xl sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <span class="text-3xl material-icons sm:text-4xl">account_balance</span>
                    <div>
                        <h1 class="text-xl font-bold sm:text-2xl">Florence EGI</h1>
                        <p class="text-sm text-blue-200 sm:text-base">Soluzioni per la P.A.</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden space-x-4 sm:space-x-6 md:flex">
                    <a href="#" class="text-sm transition hover:text-blue-200 lg:text-base">Home</a>
                    <a href="#sfide" class="text-sm transition hover:text-blue-200 lg:text-base">Le Sfide</a>
                    <a href="#contatti" class="text-sm transition hover:text-blue-200 lg:text-base">Contatti</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block p-2 transition-colors rounded-md hover:bg-blue-700 md:hidden">
                    <span class="text-2xl material-icons">menu</span>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden pb-4 mt-4 border-t border-blue-600 md:hidden">
                <div class="pt-4 space-y-3">
                    <a href="#"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">home</span>Home
                    </a>
                    <a href="#sfide"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">business_center</span>Le Sfide
                    </a>
                    <a href="#contatti"
                        class="flex items-center px-4 py-2 text-sm transition-colors rounded-md hover:bg-blue-700">
                        <span class="mr-3 text-lg material-icons">contact_mail</span>Contatti
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto max-w-6xl px-4 py-8 md:py-16">
        <!-- Main Header -->
        <header class="text-center mb-12 md:mb-20 fade-in">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Atti Pubblici a Prova di Dubbio</h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-4xl mx-auto">La soluzione definitiva per garantire
                autenticità, trasparenza e certezza legale ai documenti della Vostra Amministrazione.</p>
        </header>

        <main>
            <!-- Sezione 1: Il Problema -->
            <section class="bg-white p-8 rounded-xl shadow-md border border-gray-200 mb-12 fade-in"
                style="animation-delay: 0.2s;">
                <h2 class="text-3xl font-bold text-institutional-blue mb-4">La sfida quotidiana di ogni dirigente</h2>
                <p class="mb-4 text-lg">Ogni giorno producete documenti cruciali: delibere, determine, permessi. Ma come
                    garantire a tutti — cittadini, imprese, organi di controllo — che la copia che stanno leggendo sia
                    <span class="font-bold text-red-700">l'unica e autentica versione ufficiale?</span></p>
                <p class="text-gray-600 text-lg">La circolazione di copie modificate, le contestazioni via PEC e il
                    tempo speso in continue verifiche generano incertezza, minano la fiducia e sovraccaricano gli
                    uffici.</p>
            </section>

            <!-- Sezione: I 10 Punti Chiave -->
            <section id="sfide" class="mb-12 fade-in" style="animation-delay: 0.4s;">
                <h2 class="text-3xl font-bold text-institutional-blue mb-10 text-center">Le 10 sfide che risolviamo, una
                    per una</h2>
                <div class="grid md:grid-cols-2 gap-8">

                    <!-- Punto 1: Autenticità/Manomissioni -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">1. Garantire l'Autenticità</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Circolano PDF modificati o versioni “alternative” di atti
                                ufficiali.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">PEC e contestazioni continue, con perdita di tempo e credibilità.
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Un sigillo digitale post-protocollo e un QR Code pubblico: chiunque
                                può verificare che il PDF è l'originale.</p>
                        </div>
                    </div>

                    <!-- Punto 2: Trasparenza Visibile -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">2. Rendere la Trasparenza "Visibile"</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">L’Albo Pretorio mostra i file, ma non dà una prova immediata di
                                autenticità.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Pressione mediatica e politica; fiducia dei cittadini fragile.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Una pagina di verifica in 1 click e un "bollino di autenticità"
                                (QR) da inserire direttamente sull'Albo.</p>
                        </div>
                    </div>

                    <!-- Punto 3: Reperibilità -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">3. Trovare l'Atto Giusto, Subito</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">È difficile e lento trovare subito l'atto corretto o la sua ultima
                                versione.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">URP sommerso di richieste; dirigenti coinvolti per dare
                                chiarimenti.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Un link univoco e un QR Code per ogni atto, con una pagina pubblica
                                sempre raggiungibile.</p>
                        </div>
                    </div>

                    <!-- Punto 4: Versioni e Rettifiche -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">4. Gestire Rettifiche e Revoche</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Non si capisce cosa è cambiato tra una versione e l'altra, e
                                quando.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Malintesi interni ed esterni, con rischio di accuse di scarsa
                                trasparenza.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Uno storico leggibile: ogni rettifica è una nuova versione con un
                                nuovo sigillo e un rimando chiaro alla precedente.</p>
                        </div>
                    </div>

                    <!-- Punto 5: Tempo Operativo -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">5. Ottimizzare il Tempo Operativo</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Poco personale e troppi micro-adempimenti dopo la pubblicazione
                                degli atti.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Ritardi, stress e arretrati che si accumulano inesorabilmente.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Un'integrazione semplice, senza cambiare software. Il sigillo e il
                                QR si generano automaticamente.</p>
                        </div>
                    </div>

                    <!-- Punto 6: Compliance -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">6. Lavorare in Piena Conformità</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Timore costante di commettere errori su privacy (GDPR) e validità
                                legale (CAD).</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Blocchi decisionali, paura di esporre dati sensibili o di sbagliare
                                procedure.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Nel registro pubblico finiscono solo codici, mai il contenuto. Log
                                e audit sono sempre pronti e a norma.</p>
                        </div>
                    </div>

                    <!-- Punto 7: Lock-in Tecnologico -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">7. Evitare il "Lock-in" Tecnologico</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Cambiare fornitore o piattaforma informatica spesso significa
                                perdere le "prove" di validità.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Dipendenza da un unico vendor, con il rischio di dover rifare tutto
                                da capo.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">La prova di validità è pubblica e indipendente. Resta valida per
                                sempre, anche cambiando fornitore.</p>
                        </div>
                    </div>

                    <!-- Punto 8: KPI e Accountability -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">8. Misurare Efficienza e Risultati</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Mancano numeri chiari da portare in report e audit per dimostrare
                                l'efficienza.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Non si riesce a dimostrare i miglioramenti ottenuti o i tempi reali
                                di gestione.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Un cruscotto con i dati chiave: n. atti sigillati, tempi medi, n.
                                verifiche. Report pronti per i controlli.</p>
                        </div>
                    </div>

                    <!-- Punto 9: Copie Ufficiali -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">9. Tracciare le Copie Ufficiali</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Attestati e permessi sono difficili da tracciare una volta emessi.
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Confusione su copie valide e revocate, con alti rischi di abuso.
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Copie numerate e tracciate pubblicamente. Ogni copia ha il suo QR e
                                la sua storia, inclusa l'eventuale revoca.</p>
                        </div>
                    </div>

                    <!-- Punto 10: Budget e Quick-Win -->
                    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">10. Partire Subito con Poche Risorse</h3>
                        <div class="mb-3">
                            <p class="font-semibold text-red-700">Problema:</p>
                            <p class="text-gray-600">Poche risorse economiche e poca tolleranza per progetti lunghi e
                                complessi.</p>
                        </div>
                        <div class="mb-3">
                            <p class="font-semibold text-orange-600">Sofferenza Percepita:</p>
                            <p class="text-gray-600">Buone iniziative che non partono mai, con la pressione a "fare
                                presto e bene".</p>
                        </div>
                        <div>
                            <p class="font-semibold text-compliance-green">Soluzione Proposta:</p>
                            <p class="text-gray-600">Un'attivazione a basso impatto, con valore visibile in poche
                                settimane. Si parte subito e si scala in un secondo momento.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section id="contatti" class="text-center bg-institutional-blue text-white p-10 rounded-xl fade-in"
                style="animation-delay: 0.6s;">
                <h2 class="text-3xl font-bold mb-4">Pronti a vedere la certezza in azione?</h2>
                <p class="max-w-2xl mx-auto mb-6">Saremmo lieti di mostrarvi, senza impegno, una demo di 15 minuti
                    applicata a un Vostro documento tipo. Vedrete con i vostri occhi com'è semplice trasformare un
                    dubbio in una certezza.</p>
                <a href="mailto:pa-services@florenceegi.com?subject=Richiesta%20Demo%20Sigillo%20Digitale"
                    class="bg-florence-gold text-white font-bold py-3 px-8 rounded-full hover:bg-yellow-600 transition-colors duration-300 inline-flex items-center">
                    <span class="material-icons mr-2">description</span>
                    Richiedi una Demo
                </a>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container max-w-6xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-xl font-bold">Florence EGI</h3>
                    <p class="mt-2 text-gray-400">Servizi enterprise di certificazione digitale eIDAS-compliant per
                        Pubbliche Amministrazioni.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold">Link Utili</h4>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="#sfide" class="text-gray-400 hover:text-white transition">Le Sfide che
                                Risolviamo</a></li>
                        <li><a href="#contatti" class="text-gray-400 hover:text-white transition">Richiedi Demo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold">Contatti Istituzionali</h4>
                    <ul class="mt-4 space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <span class="material-icons mr-2">email</span>
                            <span>pa-services@florenceegi.com</span>
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons mr-2">phone</span>
                            <span>+39 055 123 4567</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-800 pt-8 text-center text-gray-500">
                <p>&copy; 2025 Florence EGI | Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = mobileMenuButton.querySelector('.material-icons');

        mobileMenuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                menuIcon.textContent = 'close';
            } else {
                mobileMenu.classList.add('hidden');
                menuIcon.textContent = 'menu';
            }
        });

        // Close menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                menuIcon.textContent = 'menu';
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                menuIcon.textContent = 'menu';
            }
        });
    });
    </script>
</body>

</html>