<x-legal-layout>
    {{-- Il "Canvas" ora è definito qui, dentro la pagina, non nel layout --}}
    <div class="w-full max-w-7xl mx-auto bg-[#F8F7F2] shadow-xl my-8 rounded-lg">
        <div class="lg:grid lg:grid-cols-4 lg:gap-12 lg:items-start">

            {{-- NAVIGAZIONE LATERALE (Desktop) --}}
            {{-- Questa è nascosta su mobile e appare come colonna sticky su desktop --}}
           <aside class="self-start hidden lg:block lg:col-span-1 lg:sticky lg:top-8 lg:p-8">
                <div class="pb-8">
                    <h2 class="text-2xl font-bold text-[#2D5016]">FlorenceEGI</h2>
                    <p class="text-sm text-gray-500">{{ $termsContent['metadata']['title'] ?? 'Termini di Servizio' }}</p>
                </div>
                <nav id="desktop-nav">
                    <ul class="space-y-2">
                        <li>
                            {{-- ✅ LINK ESPLICITO E ROBUSTO --}}
                            <a href="{{ request('redirect_url', url('/')) }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-700 transition-colors duration-200 bg-gray-200 rounded-md hover:bg-gray-300">
                                <span class="material-symbols-outlined">arrow_back</span>
                                <span>{{ __('legal_viewer.back_button') }}</span>
                            </a>
                        </li>
                        <hr class="!my-3 border-gray-300/80">
                        {{-- Link alle sezioni della pagina --}}
                        <li><a href="#welcome" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_welcome') }}</a></li>
                        <li><a href="#the-pact" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_pact') }}</a></li>
                        <li><a href="#platform-art" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_platform') }}</a></li>
                        <li><a href="#rules-risks" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_rules') }}</a></li>
                        <li><a href="#final-clauses" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_final') }}</a></li>
                        @auth
                            <li><a href="#acceptance" class="block px-3 py-2 text-gray-700 transition-colors duration-200 rounded-md hover:bg-[#EAE7DC]">{{ __('legal_viewer.section_title_acceptance') }}</a></li>
                        @endauth

                    </ul>
                </nav>
            </aside>

            {{-- Spazio per il titolo e la descrizione --}}

            {{-- Contenuto Principale --}}
            <main class="p-4 lg:col-span-3 md:p-8">

                {{-- NAVIGAZIONE (Mobile) --}}
                <div class="lg:hidden sticky top-0 z-40 mb-8 bg-[#F8F7F2] border-b border-gray-200 shadow-sm">
                    <button id="mobile-nav-toggle" class="flex items-center justify-between w-full p-4">
                        <span class="text-lg font-bold text-[#2D5016]">Indice dei Contenuti</span>
                        <span class="text-2xl text-[#8C6A4A] transition-transform duration-300 accordion-arrow">▼</span>
                    </button>
                    <div id="mobile-nav-content" class="accordion-content">
                        <nav id="mobile-nav" class="p-4 pt-0">
                            <ul class="space-y-1 text-sm">
                                <li>
                                    {{-- ✅ LINK ESPLICITO E ROBUSTO --}}
                                    <a href="{{ request('redirect_url', url('/')) }}"
                                    class="flex items-center justify-center w-full gap-2 px-3 py-3 mt-2 text-sm font-semibold text-gray-700 transition-colors duration-200 bg-gray-200 rounded-md hover:bg-gray-300">
                                    <span class="material-symbols-outlined">arrow_back</span>
                                    <span>{{ __('legal_viewer.back_button') }}</span>
                                </a>
                            </li>
                            <hr class="!my-3 border-gray-300/80">
                            {{-- Link alle sezioni della pagina --}}
                            <li><a href="#welcome" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_welcome') }}</a></li>
                            <li><a href="#the-pact" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_pact') }}</a></li>
                            <li><a href="#platform-art" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_platform') }}</a></li>
                            <li><a href="#rules-risks" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_rules') }}</a></li>
                            <li><a href="#final-clauses" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_final') }}</a></li>
                            @auth
                                <li><a href="#acceptance" class="block py-2 text-blue-700 hover:underline">{{ __('legal_viewer.section_title_acceptance') }}</a></li>
                            @endauth
                            </ul>
                        </nav>
                    </div>
                </div>

                {{-- Welcome Section con Summary Cards --}}
                <section id="welcome" class="mb-16 scroll-mt-20">
                    <h1 class="text-4xl font-bold text-[#2D5016] mb-4">{{ $termsContent['metadata']['title'] }}</h1>
                    <div class="mb-8 prose max-w-none prose-p:text-gray-800 prose-strong:text-black prose-strong:font-semibold">{!! \Illuminate\Support\Str::markdown($termsContent['preambolo']['content'] ?? '') !!}</div>

                    <div class="grid gap-6 mt-8 text-center md:grid-cols-3">
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">{{ __('legal_viewer.summary_card_ownership_title') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('legal_viewer.summary_card_ownership_content') }}</p>
                        </div>
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">{{ __('legal_viewer.summary_card_responsibility_title') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('legal_viewer.summary_card_responsibility_content') }}</p>
                        </div>
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">{{ __('legal_viewer.summary_card_risks_title') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('legal_viewer.summary_card_risks_content') }}</p>
                        </div>
                    </div>
                </section>

                {{-- SEZIONI CON ACCORDION --}}
                <section id="the-pact" class="mb-16 scroll-mt-20">
                    <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">{{ __('legal_viewer.section_title_pact') }}</h2>
                    <x-legal.accordion :items="$termsContent['articles']->where('category', 'pact')" />
                </section>

                <section id="platform-art" class="mb-16 scroll-mt-20">
                    <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">{{ __('legal_viewer.section_title_platform') }}</h2>
                    <x-legal.accordion :items="$termsContent['articles']->where('category', 'platform')" />
                </section>

                <section id="rules-risks" class="mb-16 scroll-mt-20">
                    <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">{{ __('legal_viewer.section_title_rules') }}</h2>
                    <x-legal.accordion :items="$termsContent['articles']->where('category', 'rules')" />
                </section>

                <section id="final-clauses" class="mb-16 scroll-mt-20">
                    <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">{{ __('legal_viewer.section_title_final') }}</h2>
                    <x-legal.accordion :items="$termsContent['articles']->where('category', 'final')" />
                </section>

                {{-- SEZIONE ACCETTAZIONE (per utenti loggati) --}}
                @auth
                <section id="acceptance" class="scroll-mt-20">
                    <div class="bg-[#EAE7DC]/60 backdrop-blur-lg p-8 rounded-2xl shadow-md border border-gray-300/50">
                        <h2 class="text-3xl font-bold text-center mb-4 text-[#2D5016]">{{ __('legal_viewer.acceptance_title') }}</h2>
                        @if ($consentStatus['hasAcceptedCurrent'])
                            <div class="flex items-center justify-center p-3 text-sm text-green-800 bg-green-100 rounded-md">
                                <span class="mr-2 material-symbols-outlined">verified_user</span>
                                <span>{{ __('legal_viewer.accepted_status') }}</span>
                            </div>
                        @else
                            <p class="mb-6 text-center text-gray-600">{{ __('legal_viewer.acceptance_prompt') }}</p>
                            <form action="{{ route('legal.accept', ['userType' => $user->usertype]) }}" method="POST" class="max-w-md mx-auto">
                                @csrf
                                <input type="hidden" name="version" value="{{ $currentVersion }}">
                                <input type="hidden" name="locale" value="{{ $locale }}">
                                <div class="flex items-center justify-center">
                                    <button type="submit" class="px-8 py-3 rounded-lg font-semibold text-white bg-[#2D5016] border border-[#2D5016] hover:bg-opacity-90 transition-colors duration-300">
                                        {{ __('legal_viewer.accept_button') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </section>
                @endauth
            </main>
        </div>
    </div>

    @include('components.info-footer')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script per l'highlight della nav desktop durante lo scroll
            const sections = document.querySelectorAll('main section');
            const desktopNavLinks = document.querySelectorAll('#desktop-nav a');
            if (window.innerWidth >= 1024) {
                const observerOptions = { root: null, rootMargin: '-20% 0px -70% 0px', threshold: 0 };
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const id = entry.target.getAttribute('id');
                            desktopNavLinks.forEach(link => {
                                link.classList.remove('active-nav');
                                if (link.getAttribute('href') === `#${id}`) {
                                    link.classList.add('active-nav');
                                }
                            });
                        }
                    });
                }, observerOptions);
                sections.forEach(section => { observer.observe(section); });
            }

            // Script per l'accordion del menu mobile
            const mobileNavToggle = document.getElementById('mobile-nav-toggle');
            const mobileNavContent = document.getElementById('mobile-nav-content');
            if (mobileNavToggle && mobileNavContent) {
                mobileNavToggle.addEventListener('click', () => {
                    const button = mobileNavToggle;
                    const content = mobileNavContent;
                    button.classList.toggle('open');
                    if (content.style.maxHeight) {
                        content.style.maxHeight = null;
                    } else {
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });
            }

            // Script per lo scorrimento morbido dei link interni del menu mobile
            const mobileNavInternalLinks = document.querySelectorAll('#mobile-nav a[href^="#"]');
            mobileNavInternalLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (mobileNavContent.style.maxHeight) {
                        mobileNavToggle.classList.remove('open');
                        mobileNavContent.style.maxHeight = null;
                    }

                    if (targetElement) {
                         setTimeout(() => {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 300); // 300ms per dare tempo all'animazione di chiusura
                    }
                });
            });
        });
    </script>
    @endpush
</x-legal-layout>
