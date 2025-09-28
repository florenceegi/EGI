{{--
    File: patron.blade.php
    Versione: 2.0 Silent Growth
    Data: 28 Settembre 2025
    Descrizione: Pagina Mecenati completamente riscritta seguendo strategia Silent Growth
    Principi: Prestigio, professionalità, impatto misurabile, niente promesse MLM
--}}

<x-guest-layout title="Mecenati FlorenceEGI - Facilitatori Arte-Sostenibilità"
    meta-description="Diventa Mecenate FlorenceEGI: facilita connessioni significative tra arte contemporanea e rigenerazione ambientale. Un ruolo di prestigio per impatto misurabile.">

    <!-- Header con Navigazione Mobile -->
    <header class="bg-gradient-to-r from-amber-900 to-amber-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 sm:px-6 sm:py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <i class="fas fa-heart text-3xl text-rose-300 sm:text-4xl"></i>
                    <div>
                        <h1 class="text-xl font-bold sm:text-2xl">Mecenati FlorenceEGI</h1>
                        <p class="text-sm text-amber-200 sm:text-base">Facilitatori Arte-Sostenibilità</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden space-x-4 sm:space-x-6 md:flex">
                    <a href="#ruolo" class="text-sm transition hover:text-amber-200 lg:text-base">Il Ruolo</a>
                    <a href="#casi" class="text-sm transition hover:text-amber-200 lg:text-base">Casi d'Uso</a>
                    <a href="#processo" class="text-sm transition hover:text-amber-200 lg:text-base">Come Iniziare</a>
                    <a href="#faq" class="text-sm transition hover:text-amber-200 lg:text-base">FAQ</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="block rounded-md p-2 transition-colors hover:bg-amber-600 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mt-4 hidden border-t border-amber-600 pb-4 md:hidden">
                <div class="space-y-3 pt-4">
                    <a href="{{ route('home') }}"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-amber-600">
                        <i class="fas fa-home mr-3 text-lg"></i>
                        Torna alla Home
                    </a>
                    <a href="#ruolo"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-amber-600">
                        <i class="fas fa-user-tie mr-3 text-lg"></i>
                        Il Ruolo
                    </a>
                    <a href="#casi"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-amber-600">
                        <i class="fas fa-users mr-3 text-lg"></i>
                        Casi d'Uso
                    </a>
                    <a href="#processo"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-amber-600">
                        <i class="fas fa-route mr-3 text-lg"></i>
                        Come Iniziare
                    </a>
                    <a href="#faq"
                        class="flex items-center rounded-md px-4 py-2 text-sm transition-colors hover:bg-amber-600">
                        <i class="fas fa-question-circle mr-3 text-lg"></i>
                        FAQ
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-amber-800 to-amber-600 py-12 text-white sm:py-16">
        <div class="container mx-auto px-4 text-center sm:px-6">
            <div class="mx-auto max-w-4xl">
                <h1 class="mb-4 text-3xl font-bold leading-tight sm:mb-6 sm:text-4xl md:text-5xl">
                    Mecenate FlorenceEGI
                </h1>
                <p class="mb-6 text-lg text-amber-100 sm:mb-8 sm:text-xl">
                    Facilita connessioni significative tra arte contemporanea e rigenerazione ambientale.<br>
                    Un ruolo di prestigio per impatto misurabile.
                </p>
                <div
                    class="mx-auto flex w-full max-w-md flex-col justify-center gap-3 sm:gap-4 md:max-w-none md:flex-row">
                    <a href="#ruolo"
                        class="w-full rounded-lg bg-rose-600 px-6 py-3 text-base font-semibold text-white transition hover:bg-rose-700 sm:px-8 sm:py-4 sm:text-lg md:w-auto">
                        <i class="fas fa-info-circle mr-2 text-base sm:text-lg"></i>
                        Scopri il Ruolo
                    </a>
                    <a href="#processo"
                        class="w-full rounded-lg border-2 border-white bg-transparent px-6 py-3 text-base font-semibold text-white transition hover:bg-white hover:text-amber-800 sm:px-8 sm:py-4 sm:text-lg md:w-auto">
                        <i class="fas fa-handshake mr-2 text-base sm:text-lg"></i>
                        Come Iniziare
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Il Ruolo del Mecenate -->
    <section id="ruolo" class="bg-white py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold sm:mb-4 sm:text-3xl md:text-4xl">
                        Il Ruolo del Mecenate
                    </h2>
                    <p class="mx-auto max-w-3xl text-lg text-gray-600 sm:text-xl">
                        Un facilitatore professionale nell'ecosistema FlorenceEGI che connette artisti con opportunità
                        di impatto ambientale documentato.
                    </p>
                </div>

                <div class="grid gap-6 sm:gap-8 md:grid-cols-3">
                    <div class="rounded-xl border-l-4 border-rose-500 bg-gray-50 p-6 sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-handshake mr-3 text-2xl text-rose-500 sm:text-3xl"></i>
                            <h3 class="text-lg font-bold sm:text-xl">Facilitatore di Connessioni</h3>
                        </div>
                        <p class="text-gray-600">
                            Colleghi artisti con opportunità concrete di impatto ambientale attraverso l'ecosistema
                            FlorenceEGI e i progetti EPP verificati.
                        </p>
                    </div>

                    <div class="rounded-xl border-l-4 border-amber-500 bg-gray-50 p-6 sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-seedling mr-3 text-2xl text-amber-500 sm:text-3xl"></i>
                            <h3 class="text-lg font-bold sm:text-xl">Ambasciatore Sostenibilità</h3>
                        </div>
                        <p class="text-gray-600">
                            Promuovi la causa ambientale attraverso l'arte, con impatto tracciabile e verificabile via
                            blockchain per trasparenza totale.
                        </p>
                    </div>

                    <div class="rounded-xl border-l-4 border-emerald-500 bg-gray-50 p-6 sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-award mr-3 text-2xl text-emerald-500 sm:text-3xl"></i>
                            <h3 class="text-lg font-bold sm:text-xl">Costruttore di Prestigio</h3>
                        </div>
                        <p class="text-gray-600">
                            Sviluppi un riconoscimento duraturo nel network arte-sostenibilità, con documentazione
                            permanente del tuo contributo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Casi d'Uso Realistici -->
    <section id="casi" class="bg-gray-50 py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold sm:mb-4 sm:text-3xl md:text-4xl">
                        Chi Può Diventare Mecenate
                    </h2>
                    <p class="mx-auto max-w-3xl text-lg text-gray-600 sm:text-xl">
                        Profili reali di persone che potrebbero trovare valore nel ruolo di Mecenate FlorenceEGI.
                    </p>
                </div>

                <div class="grid gap-6 sm:gap-8 lg:grid-cols-2">
                    <!-- Il Pensionato Colto -->
                    <div class="rounded-xl bg-white p-6 shadow-lg sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-user-graduate mr-4 text-3xl text-amber-600"></i>
                            <h3 class="text-xl font-bold">Il Pensionato Colto</h3>
                        </div>
                        <div class="space-y-3">
                            <p><strong>Profilo:</strong> Ex dirigente/professionista con passione per l'arte e tempo
                                disponibile.</p>
                            <p><strong>Background:</strong> Rete di contatti nel mondo culturale, esperienza
                                professionale consolidata.</p>
                            <p><strong>Motivazione:</strong> Dare un contributo significativo alla cultura e
                                all'ambiente nel tempo libero.</p>
                            <p><strong>Valore:</strong> Credibilità, network consolidato, esperienza di vita.</p>
                            <p class="text-amber-700"><strong>Impatto:</strong> Facilitatore di connessioni prestigiose
                                arte-sostenibilità.</p>
                        </div>
                    </div>

                    <!-- Il Gallerista Innovativo -->
                    <div class="rounded-xl bg-white p-6 shadow-lg sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-store mr-4 text-3xl text-rose-600"></i>
                            <h3 class="text-xl font-bold">Il Gallerista Innovativo</h3>
                        </div>
                        <div class="space-y-3">
                            <p><strong>Profilo:</strong> Proprietario di galleria che vuole modernizzare il proprio
                                business.</p>
                            <p><strong>Background:</strong> Competenze nel mercato dell'arte, portfolio di artisti
                                esistente.</p>
                            <p><strong>Motivazione:</strong> Differenziarsi con certificazione blockchain e impatto EPP.
                            </p>
                            <p><strong>Valore:</strong> Expertise settoriale, credibilità nel mercato dell'arte.</p>
                            <p class="text-rose-700"><strong>Impatto:</strong> Ponte tra arte tradizionale e innovazione
                                sostenibile.</p>
                        </div>
                    </div>

                    <!-- Il Filantropo Ambientale -->
                    <div class="rounded-xl bg-white p-6 shadow-lg sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-leaf mr-4 text-3xl text-emerald-600"></i>
                            <h3 class="text-xl font-bold">Il Filantropo Ambientale</h3>
                        </div>
                        <div class="space-y-3">
                            <p><strong>Profilo:</strong> Imprenditore/professionista attivo in cause ambientali.</p>
                            <p><strong>Background:</strong> Già coinvolto in progetti di sostenibilità, cerca nuovi modi
                                per contribuire.</p>
                            <p><strong>Motivazione:</strong> Combinare passione per l'arte con impatto ambientale
                                misurabile.</p>
                            <p><strong>Valore:</strong> Risorse, network, commitment genuino alla causa.</p>
                            <p class="text-emerald-700"><strong>Impatto:</strong> Ambasciatore autorevole della causa
                                arte-sostenibilità.</p>
                        </div>
                    </div>

                    <!-- Il Curatore/Critico -->
                    <div class="rounded-xl bg-white p-6 shadow-lg sm:p-8">
                        <div class="mb-4 flex items-center">
                            <i class="fas fa-eye mr-4 text-3xl text-purple-600"></i>
                            <h3 class="text-xl font-bold">Il Curatore/Critico d'Arte</h3>
                        </div>
                        <div class="space-y-3">
                            <p><strong>Profilo:</strong> Professionista del settore artistico-culturale.</p>
                            <p><strong>Background:</strong> Competenze tecniche approfondite, network di artisti
                                emergenti.</p>
                            <p><strong>Motivazione:</strong> Supportare artisti in progetti con impatto sociale
                                documentato.</p>
                            <p><strong>Valore:</strong> Eye for talent, credibilità accademica e culturale.</p>
                            <p class="text-purple-700"><strong>Impatto:</strong> Validazione qualitativa delle proposte
                                artistiche sostenibili.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Come Iniziare -->
    <section id="processo" class="bg-white py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="mx-auto max-w-4xl">
                <div class="mb-8 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold sm:mb-4 sm:text-3xl md:text-4xl">
                        Come Iniziare
                    </h2>
                    <p class="text-lg text-gray-600 sm:text-xl">
                        Un processo concreto e trasparente per valutare se il ruolo di Mecenate è adatto a te.
                    </p>
                </div>

                <div class="space-y-6 sm:space-y-8">
                    <div class="flex items-start space-x-4 sm:space-x-6">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-600 font-bold text-white">
                            1</div>
                        <div>
                            <h3 class="text-lg font-bold">Valutazione Iniziale</h3>
                            <p class="text-gray-600">Contatta il team FlorenceEGI per una conversazione esplorativa.
                                Niente impegni, solo una discussione aperta su obiettivi e possibilità concrete.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 sm:space-x-6">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-600 font-bold text-white">
                            2</div>
                        <div>
                            <h3 class="text-lg font-bold">Comprensione dell'Ecosistema</h3>
                            <p class="text-gray-600">Approfondimento su come funziona FlorenceEGI, i progetti EPP, e il
                                sistema di certificazione blockchain. Tutto trasparente e verificabile.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 sm:space-x-6">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 font-bold text-white">
                            3</div>
                        <div>
                            <h3 class="text-lg font-bold">Primo Progetto Pilota</h3>
                            <p class="text-gray-600">Collaborazione su un progetto concreto per testare l'allineamento
                                e valutare l'impatto reale. Approccio graduale e senza pressioni.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 sm:space-x-6">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-600 font-bold text-white">
                            4</div>
                        <div>
                            <h3 class="text-lg font-bold">Sviluppo del Ruolo</h3>
                            <p class="text-gray-600">Se il progetto pilota ha successo, sviluppo graduale del ruolo in
                                base agli interessi specifici e alle competenze personali.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center sm:mt-12">
                    <a href="mailto:mecenati@florenceegi.com"
                        class="inline-flex items-center rounded-lg bg-amber-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-amber-700">
                        <i class="fas fa-envelope mr-2"></i>
                        Inizia la Conversazione
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Essenziali -->
    <section id="faq" class="bg-gray-50 py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="mx-auto max-w-4xl">
                <div class="mb-8 text-center sm:mb-12">
                    <h2 class="mb-3 text-2xl font-bold sm:mb-4 sm:text-3xl md:text-4xl">
                        Domande Frequenti
                    </h2>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-3 text-lg font-bold">È richiesta esperienza specifica nel mondo dell'arte?</h3>
                        <p class="text-gray-600">Non necessariamente. Cerchiamo persone con passione genuina per l'arte
                            e la sostenibilità, buone capacità relazionali e credibilità nel proprio network.
                            L'esperienza tecnica può essere sviluppata.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-3 text-lg font-bold">Quanto tempo richiede il ruolo di Mecenate?</h3>
                        <p class="text-gray-600">Dipende dal livello di coinvolgimento desiderato. Alcuni Mecenati
                            contribuiscono qualche ora a settimana, altri sviluppano il ruolo come attività principale.
                            La flessibilità è un elemento chiave.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-3 text-lg font-bold">Come viene misurato l'impatto ambientale?</h3>
                        <p class="text-gray-600">Ogni progetto EPP ha metriche specifiche e verificabili (alberi
                            piantati, CO2 catturata, etc.). L'impatto è tracciato via blockchain per trasparenza totale
                            e documentazione permanente.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="mb-3 text-lg font-bold">Quali sono le responsabilità concrete?</h3>
                        <p class="text-gray-600">Facilitare connessioni tra artisti e opportunità, supportare la
                            comunicazione di progetti sostenibili, contribuire alla crescita qualitativa dell'ecosistema
                            FlorenceEGI.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

</x-guest-layout>
