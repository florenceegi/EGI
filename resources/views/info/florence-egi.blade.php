{{--
    File: florence-egi.blade.php
    Version: 2.1.0 AMMk Edition ("The Genesis Core" - WebGL 2)
    Date: 2025-11-23
    Description: High-performance landing page with WebGL 2 Hero and Vanilla JS.
    Stack: Tailwind CSS (Vite), WebGL 2 (GLSL ES 3.0), Vanilla JS.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('info_florence_egi.meta.title') }}</title>
    <meta name="description" content="{{ __('info_florence_egi.meta.description') }}">
    <meta name="keywords" content="{{ __('info_florence_egi.meta.keywords') }}">
    <meta name="author" content="{{ __('info_florence_egi.meta.author') }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ __('info_florence_egi.meta.og_title') }}">
    <meta property="og:description" content="{{ __('info_florence_egi.meta.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Styles & Scripts (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/florence-shader.js'])

    {{-- Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">

    <style>
        /* Custom Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-dark {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #D4AF37 0%, #F2D06B 50%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* WebGL Canvas Container */
        #canvas-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            background: #000;
            /* Fallback */
        }

        /* Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-nero-profondo selection:bg-oro-fiorentino text-white antialiased selection:text-white">

    {{-- Navigation --}}
    <nav id="navbar" class="fixed z-50 w-full bg-transparent py-4 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <div class="flex flex-shrink-0 items-center">
                    <span class="font-serif text-2xl font-bold tracking-wider text-white">Florence<span
                            class="text-oro-fiorentino">EGI</span></span>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden items-center space-x-8 md:flex">
                    <a href="#concept"
                        class="hover:text-oro-fiorentino text-sm font-medium text-gray-300 transition-colors">{{ __('info_florence_egi.nav.concept') }}</a>
                    <a href="#problems"
                        class="hover:text-oro-fiorentino text-sm font-medium text-gray-300 transition-colors">{{ __('info_florence_egi.nav.problems') }}</a>
                    <a href="#how"
                        class="hover:text-oro-fiorentino text-sm font-medium text-gray-300 transition-colors">{{ __('info_florence_egi.nav.how') }}</a>
                    <a href="#ammk"
                        class="hover:text-oro-fiorentino text-sm font-medium text-gray-300 transition-colors">{{ __('info_florence_egi.nav.ammk') }}</a>
                    <a href="#"
                        class="text-nero-profondo bg-oro-fiorentino rounded-full px-5 py-2.5 text-sm font-semibold shadow-[0_0_20px_rgba(212,175,55,0.3)] transition-all duration-300 hover:bg-white">{{ __('info_florence_egi.nav.cta') }}</a>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="hover:text-oro-fiorentino text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu Panel --}}
        <div id="mobile-menu"
            class="bg-nero-profondo/95 absolute left-0 top-full hidden w-full border-b border-white/10 backdrop-blur-lg md:hidden">
            <div class="space-y-2 px-4 pb-6 pt-2">
                <a href="#concept"
                    class="hover:text-oro-fiorentino block px-3 py-2 text-base font-medium text-white">{{ __('info_florence_egi.nav.concept') }}</a>
                <a href="#problems"
                    class="hover:text-oro-fiorentino block px-3 py-2 text-base font-medium text-white">{{ __('info_florence_egi.nav.problems') }}</a>
                <a href="#how"
                    class="hover:text-oro-fiorentino block px-3 py-2 text-base font-medium text-white">{{ __('info_florence_egi.nav.how') }}</a>
                <a href="#ammk"
                    class="hover:text-oro-fiorentino block px-3 py-2 text-base font-medium text-white">{{ __('info_florence_egi.nav.ammk') }}</a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION: THE GENESIS CORE --}}
    <section class="relative flex h-screen w-full items-center justify-center overflow-hidden bg-black">
        {{-- WebGL Canvas --}}
        <div id="canvas-container"></div>

        {{-- Hero Content --}}
        <div
            class="hero-content-wrapper relative z-10 mx-auto mt-16 max-w-5xl px-4 text-center transition-opacity duration-300">
            <h1
                class="fade-in-up mb-6 font-serif text-5xl font-bold leading-tight tracking-tight text-white drop-shadow-2xl md:text-7xl lg:text-8xl">
                {!! __('info_florence_egi.hero.headline_html') !!}
            </h1>
            <p class="fade-in-up mx-auto mb-10 max-w-2xl text-lg font-light text-gray-300 drop-shadow-lg md:text-xl"
                style="transition-delay: 0.2s;">
                {{ __('info_florence_egi.hero.subheadline') }}
            </p>
            <div class="fade-in-up pointer-events-auto flex flex-col items-center justify-center gap-4 sm:flex-row"
                style="transition-delay: 0.4s;">
                <a href="#"
                    class="text-nero-profondo bg-oro-fiorentino w-full rounded-full px-8 py-4 text-base font-bold shadow-[0_0_30px_rgba(212,175,55,0.4)] transition-all duration-300 hover:scale-105 hover:bg-white sm:w-auto">
                    {{ __('info_florence_egi.hero.cta_primary') }}
                </a>
                <a href="#concept"
                    class="glass w-full rounded-full border border-white/30 px-8 py-4 text-base font-medium text-white transition-all duration-300 hover:border-white hover:bg-white/10 sm:w-auto">
                    {{ __('info_florence_egi.hero.cta_secondary') }}
                </a>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div
            class="absolute bottom-10 left-1/2 flex -translate-x-1/2 transform animate-bounce flex-col items-center opacity-70">
            <span
                class="text-oro-fiorentino mb-2 text-xs uppercase tracking-widest">{{ __('info_florence_egi.hero.scroll_text') }}</span>
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
            </svg>
        </div>
    </section>

    {{-- INTRO: CONCEPT --}}
    <section id="concept" class="relative overflow-hidden bg-white py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                <div class="fade-in-up">
                    <span
                        class="text-oro-fiorentino mb-2 block text-sm font-bold uppercase tracking-widest">{{ __('info_florence_egi.intro.concept_title') }}</span>
                    <h2 class="text-nero-profondo mb-6 font-serif text-4xl font-bold leading-tight md:text-5xl">
                        {{ __('info_florence_egi.intro.title') }}<br>
                        <span class="text-blu-algoritmo">{{ __('info_florence_egi.intro.subtitle') }}</span>
                    </h2>
                    <p class="mb-8 text-lg leading-relaxed text-gray-600">
                        {{ __('info_florence_egi.intro.description') }}
                        <br><br>
                        {{ __('info_florence_egi.intro.concept_desc') }}
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div
                                class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-500">
                                ✕</div>
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                    {{ __('info_florence_egi.intro.difference_old') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 text-green-500">
                                ✓</div>
                            <div class="ml-4">
                                <p class="text-base font-bold text-blu-algoritmo">
                                    {{ __('info_florence_egi.intro.difference_new') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fade-in-up relative" style="transition-delay: 0.2s;">
                    <div
                        class="from-oro-fiorentino absolute -inset-4 rounded-full bg-gradient-to-r to-blu-algoritmo opacity-20 blur-2xl">
                    </div>
                    <div class="relative rounded-2xl border border-gray-100 bg-grigio-pietra p-8 shadow-xl">
                        <h3 class="mb-6 text-xl font-bold">{{ __('info_florence_egi.intro.simple_title') }}</h3>
                        <ul class="space-y-3">
                            @foreach (['art', 'design', 'exp', 'content', 'docs', 'eco'] as $item)
                                <li class="flex items-center text-gray-700">
                                    <svg class="text-oro-fiorentino mr-3 h-5 w-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('info_florence_egi.intro.list_' . $item) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PROBLEMS GRID --}}
    <section id="problems" class="bg-nero-profondo py-24 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="fade-in-up mb-16 text-center">
                <h2 class="mb-4 font-serif text-4xl font-bold md:text-5xl">
                    {{ __('info_florence_egi.problems.title') }}</h2>
                <p class="text-xl text-gray-400">{{ __('info_florence_egi.problems.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @for ($i = 1; $i <= 12; $i++)
                    <div
                        class="fade-in-up group rounded-xl border border-white/10 bg-white/5 p-6 transition-all duration-300 hover:bg-white/10">
                        <h3 class="text-oro-fiorentino mb-3 text-lg font-bold">
                            {{ __('info_florence_egi.problems.p' . $i . '_title') }}</h3>
                        <div class="space-y-2 text-sm">
                            <p class="text-gray-500 line-through decoration-red-500/50">
                                {{ __('info_florence_egi.problems.p' . $i . '_old') }}</p>
                            <p class="flex items-center font-medium text-white">
                                <span class="mr-2 text-green-400">➜</span>
                                {{ __('info_florence_egi.problems.p' . $i . '_new') }}
                            </p>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="bg-grigio-pietra py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="fade-in-up mb-16 text-center">
                <h2 class="text-nero-profondo font-serif text-4xl font-bold">{{ __('info_florence_egi.how.title') }}
                </h2>
            </div>

            <div class="relative grid grid-cols-1 gap-12 md:grid-cols-3">
                {{-- Connecting Line (Desktop) --}}
                <div class="absolute left-0 top-12 z-0 hidden h-0.5 w-full bg-gray-300 md:block"></div>

                @foreach (['step1', 'step2', 'step3'] as $index => $step)
                    <div class="fade-in-up relative z-10 text-center"
                        style="transition-delay: {{ $index * 0.2 }}s;">
                        <div
                            class="border-oro-fiorentino mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full border-4 bg-white shadow-lg">
                            <span class="text-nero-profondo text-3xl font-bold">{{ $index + 1 }}</span>
                        </div>
                        <h3 class="mb-3 text-2xl font-bold">{{ __('info_florence_egi.how.' . $step . '_title') }}</h3>
                        <p class="px-4 text-gray-600">{{ __('info_florence_egi.how.' . $step . '_desc') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- AMMk SECTION --}}
    <section id="ammk" class="bg-blu-algoritmo py-24 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="fade-in-up mb-16 text-center">
                <h2 class="mb-6 font-serif text-4xl font-bold drop-shadow-lg md:text-5xl">
                    {{ __('info_florence_egi.ammk.title') }}
                </h2>
                <p class="mb-8 text-xl text-blue-200 drop-shadow">
                    {{ __('info_florence_egi.ammk.subtitle') }}
                </p>
                <p class="mx-auto max-w-3xl text-lg leading-relaxed text-gray-300 drop-shadow">
                    {{ __('info_florence_egi.ammk.desc') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach (['natan', 'asset', 'dist', 'compliance'] as $engine)
                    <div
                        class="fade-in-up hover:border-oro-fiorentino/50 group rounded-xl border border-white/20 bg-black/30 p-6 backdrop-blur-sm transition-all duration-300 hover:bg-black/50">
                        <h4 class="text-oro-fiorentino mb-3 text-lg font-bold drop-shadow">
                            {{ __('info_florence_egi.ammk.engine_' . $engine) }}
                        </h4>
                        <p class="text-sm text-gray-300">
                            {{ __('info_florence_egi.ammk.engine_' . $engine . '_desc') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TECH & COMPLIANCE TABS --}}
    <section class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="mb-4 font-serif text-4xl font-bold">{{ __('info_florence_egi.tech.title') }}</h2>
            </div>

            {{-- Vanilla JS Tabs --}}
            <div class="mx-auto w-full max-w-4xl" id="tech-tabs">
                <div class="mb-8 flex justify-center space-x-4 border-b border-gray-200">
                    <button
                        class="tab-btn text-oro-fiorentino border-oro-fiorentino border-b-2 px-6 py-3 text-lg font-medium focus:outline-none"
                        data-target="tab-tech">
                        {{ __('info_florence_egi.nav.tech') }}
                    </button>
                    <button
                        class="tab-btn px-6 py-3 text-lg font-medium text-gray-500 hover:text-gray-700 focus:outline-none"
                        data-target="tab-compliance">
                        Compliance
                    </button>
                    <button
                        class="tab-btn px-6 py-3 text-lg font-medium text-gray-500 hover:text-gray-700 focus:outline-none"
                        data-target="tab-payments">
                        Payments
                    </button>
                </div>

                {{-- Tab Contents --}}
                <div id="tab-tech" class="fade-in-up tab-content block">
                    <div class="rounded-2xl bg-grigio-pietra p-8">
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div>
                                <h3 class="mb-4 text-xl font-bold">{{ __('info_florence_egi.tech.user_view') }}</h3>
                                <p class="text-gray-600">{{ __('info_florence_egi.tech.user_list') }}</p>
                            </div>
                            <div>
                                <h3 class="mb-4 text-xl font-bold">{{ __('info_florence_egi.tech.system_view') }}</h3>
                                <p class="text-gray-600">{{ __('info_florence_egi.tech.system_list') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-compliance" class="fade-in-up tab-content hidden">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @foreach (['gdpr', 'mica', 'tax', 'ip'] as $item)
                            <div class="rounded-xl border border-gray-200 p-6 transition-shadow hover:shadow-lg">
                                <h3 class="mb-2 text-lg font-bold text-blu-algoritmo">
                                    {{ __('info_florence_egi.compliance.' . $item . '_title') }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ __('info_florence_egi.compliance.' . $item . '_desc') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="tab-payments" class="fade-in-up tab-content hidden">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
                                <div class="mb-2 text-2xl">💳</div>
                                <h3 class="mb-1 text-sm font-bold">
                                    {{ __('info_florence_egi.payments.lvl' . $i . '_title') }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ __('info_florence_egi.payments.lvl' . $i . '_desc') }}
                                </p>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="bg-nero-profondo relative overflow-hidden py-32 text-center">
        <div
            class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1634152962476-4b8a00e1915c?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-20">
        </div>
        <div class="from-nero-profondo via-nero-profondo/80 absolute inset-0 bg-gradient-to-t to-transparent"></div>

        <div class="relative z-10 mx-auto max-w-4xl px-4">
            <h2 class="mb-8 font-serif text-5xl font-bold text-white md:text-7xl">
                {!! __('info_florence_egi.cta_final.title') !!}
            </h2>
            <p class="text-oro-fiorentino mb-12 text-xl font-light italic">
                "{{ __('info_florence_egi.cta.quote') }}"
            </p>

            <div class="flex flex-col justify-center gap-6 sm:flex-row">
                <a href="#"
                    class="text-nero-profondo bg-oro-fiorentino rounded-full px-10 py-5 text-lg font-bold shadow-[0_0_40px_rgba(212,175,55,0.5)] transition-all duration-300 hover:scale-105 hover:bg-white">
                    {{ __('info_florence_egi.cta_final.btn_creator') }}
                </a>
                <a href="#"
                    class="hover:text-nero-profondo rounded-full border border-white px-10 py-5 text-lg font-bold text-white transition-all duration-300 hover:bg-white">
                    {{ __('info_florence_egi.cta_final.btn_collector') }}
                </a>
            </div>

            <p class="mt-12 text-sm uppercase tracking-widest text-gray-500">
                {{ __('info_florence_egi.cta_final.stats') }}
            </p>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-white/10 bg-black py-12 text-gray-400">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between px-4 sm:px-6 md:flex-row lg:px-8">
            <div class="mb-4 md:mb-0">
                <span class="font-serif text-xl font-bold text-white">Florence<span
                        class="text-oro-fiorentino">EGI</span></span>
                <p class="mt-2 text-xs">{{ __('info_florence_egi.footer.rights') }}</p>
            </div>
            <div class="flex space-x-6 text-sm">
                <a href="#"
                    class="transition-colors hover:text-white">{{ __('info_florence_egi.footer.privacy') }}</a>
                <a href="#"
                    class="transition-colors hover:text-white">{{ __('info_florence_egi.footer.terms') }}</a>
                <a href="#"
                    class="transition-colors hover:text-white">{{ __('info_florence_egi.footer.whitepaper') }}</a>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS --}}
    <script>
        // --- VANILLA JS UI ---
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile Menu Toggle
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const navbar = document.getElementById('navbar');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });

            // Navbar Scroll Effect
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('glass-dark', 'py-2');
                    navbar.classList.remove('bg-transparent', 'py-4');
                } else {
                    navbar.classList.remove('glass-dark', 'py-2');
                    navbar.classList.add('bg-transparent', 'py-4');
                }
            });

            // Tabs Logic
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all buttons
                    tabBtns.forEach(b => {
                        b.classList.remove('text-oro-fiorentino', 'border-b-2',
                            'border-oro-fiorentino');
                        b.classList.add('text-gray-500');
                    });
                    // Add active class to clicked button
                    btn.classList.add('text-oro-fiorentino', 'border-b-2', 'border-oro-fiorentino');
                    btn.classList.remove('text-gray-500');

                    // Hide all contents
                    tabContents.forEach(c => c.classList.add('hidden'));
                    tabContents.forEach(c => c.classList.remove('block'));

                    // Show target content
                    const target = document.getElementById(btn.dataset.target);
                    target.classList.remove('hidden');
                    target.classList.add('block');
                });
            });

            // Scroll Animations (Intersection Observer)
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
        });
    </script>

    {{-- WEBGL 2 SHADER SCRIPT --}}
    {{-- Logic moved to resources/js/florence-hero.js --}}
</body>

</html>
