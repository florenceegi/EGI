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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

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
            background: #000; /* Fallback */
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
<body class="bg-nero-profondo text-white antialiased selection:bg-oro-fiorentino selection:text-white">

    {{-- Navigation --}}
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 bg-transparent py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-serif text-2xl font-bold text-white tracking-wider">Florence<span class="text-oro-fiorentino">EGI</span></span>
                </div>
                
                {{-- Desktop Menu --}}
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#concept" class="text-sm font-medium text-gray-300 hover:text-oro-fiorentino transition-colors">{{ __('info_florence_egi.nav.concept') }}</a>
                    <a href="#problems" class="text-sm font-medium text-gray-300 hover:text-oro-fiorentino transition-colors">{{ __('info_florence_egi.nav.problems') }}</a>
                    <a href="#how" class="text-sm font-medium text-gray-300 hover:text-oro-fiorentino transition-colors">{{ __('info_florence_egi.nav.how') }}</a>
                    <a href="#ammk" class="text-sm font-medium text-gray-300 hover:text-oro-fiorentino transition-colors">{{ __('info_florence_egi.nav.ammk') }}</a>
                    <a href="#" class="px-5 py-2.5 text-sm font-semibold text-nero-profondo bg-oro-fiorentino rounded-full hover:bg-white transition-all duration-300 shadow-[0_0_20px_rgba(212,175,55,0.3)]">{{ __('info_florence_egi.nav.cta') }}</a>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-white hover:text-oro-fiorentino focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Mobile Menu Panel --}}
        <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-nero-profondo/95 backdrop-blur-lg border-b border-white/10">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#concept" class="block px-3 py-2 text-base font-medium text-white hover:text-oro-fiorentino">{{ __('info_florence_egi.nav.concept') }}</a>
                <a href="#problems" class="block px-3 py-2 text-base font-medium text-white hover:text-oro-fiorentino">{{ __('info_florence_egi.nav.problems') }}</a>
                <a href="#how" class="block px-3 py-2 text-base font-medium text-white hover:text-oro-fiorentino">{{ __('info_florence_egi.nav.how') }}</a>
                <a href="#ammk" class="block px-3 py-2 text-base font-medium text-white hover:text-oro-fiorentino">{{ __('info_florence_egi.nav.ammk') }}</a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION: THE GENESIS CORE --}}
    <section class="relative h-screen w-full flex items-center justify-center overflow-hidden bg-black">
        {{-- WebGL Canvas --}}
        <div id="canvas-container"></div>
        
        {{-- Hero Content --}}
        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto mt-16 hero-content-wrapper transition-opacity duration-300">
            <h1 class="font-serif text-5xl md:text-7xl lg:text-8xl font-bold text-white leading-tight mb-6 tracking-tight fade-in-up drop-shadow-2xl">
                {!! __('info_florence_egi.hero.headline_html') !!}
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-2xl mx-auto font-light fade-in-up drop-shadow-lg" style="transition-delay: 0.2s;">
                {{ __('info_florence_egi.hero.subheadline') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center fade-in-up pointer-events-auto" style="transition-delay: 0.4s;">
                <a href="#" class="px-8 py-4 text-base font-bold text-nero-profondo bg-oro-fiorentino rounded-full hover:bg-white hover:scale-105 transition-all duration-300 shadow-[0_0_30px_rgba(212,175,55,0.4)] w-full sm:w-auto">
                    {{ __('info_florence_egi.hero.cta_primary') }}
                </a>
                <a href="#concept" class="px-8 py-4 text-base font-medium text-white border border-white/30 rounded-full hover:bg-white/10 hover:border-white transition-all duration-300 w-full sm:w-auto glass">
                    {{ __('info_florence_egi.hero.cta_secondary') }}
                </a>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center animate-bounce opacity-70">
            <span class="text-xs text-oro-fiorentino uppercase tracking-widest mb-2">{{ __('info_florence_egi.hero.scroll_text') }}</span>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    {{-- INTRO: CONCEPT --}}
    <section id="concept" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="fade-in-up">
                    <span class="text-oro-fiorentino font-bold tracking-widest uppercase text-sm mb-2 block">{{ __('info_florence_egi.intro.concept_title') }}</span>
                    <h2 class="font-serif text-4xl md:text-5xl font-bold text-nero-profondo mb-6 leading-tight">
                        {{ __('info_florence_egi.intro.title') }}<br>
                        <span class="text-blu-algoritmo">{{ __('info_florence_egi.intro.subtitle') }}</span>
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        {{ __('info_florence_egi.intro.description') }}
                        <br><br>
                        {{ __('info_florence_egi.intro.concept_desc') }}
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-red-100 text-red-500 mt-1">✕</div>
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">{{ __('info_florence_egi.intro.difference_old') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-green-100 text-green-500 mt-1">✓</div>
                            <div class="ml-4">
                                <p class="text-base font-bold text-blu-algoritmo">{{ __('info_florence_egi.intro.difference_new') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative fade-in-up" style="transition-delay: 0.2s;">
                    <div class="absolute -inset-4 bg-gradient-to-r from-oro-fiorentino to-blu-algoritmo opacity-20 blur-2xl rounded-full"></div>
                    <div class="relative bg-grigio-pietra rounded-2xl p-8 shadow-xl border border-gray-100">
                        <h3 class="text-xl font-bold mb-6">{{ __('info_florence_egi.intro.simple_title') }}</h3>
                        <ul class="space-y-3">
                            @foreach(['art', 'design', 'exp', 'content', 'docs', 'eco'] as $item)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-oro-fiorentino mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
    <section id="problems" class="py-24 bg-nero-profondo text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="font-serif text-4xl md:text-5xl font-bold mb-4">{{ __('info_florence_egi.problems.title') }}</h2>
                <p class="text-xl text-gray-400">{{ __('info_florence_egi.problems.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @for ($i = 1; $i <= 12; $i++)
                <div class="bg-white/5 border border-white/10 p-6 rounded-xl hover:bg-white/10 transition-all duration-300 group fade-in-up">
                    <h3 class="text-lg font-bold text-oro-fiorentino mb-3">{{ __('info_florence_egi.problems.p'.$i.'_title') }}</h3>
                    <div class="space-y-2 text-sm">
                        <p class="text-gray-500 line-through decoration-red-500/50">{{ __('info_florence_egi.problems.p'.$i.'_old') }}</p>
                        <p class="text-white font-medium flex items-center">
                            <span class="text-green-400 mr-2">➜</span>
                            {{ __('info_florence_egi.problems.p'.$i.'_new') }}
                        </p>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="py-24 bg-grigio-pietra">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="font-serif text-4xl font-bold text-nero-profondo">{{ __('info_florence_egi.how.title') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                {{-- Connecting Line (Desktop) --}}
                <div class="hidden md:block absolute top-12 left-0 w-full h-0.5 bg-gray-300 z-0"></div>

                @foreach(['step1', 'step2', 'step3'] as $index => $step)
                <div class="relative z-10 text-center fade-in-up" style="transition-delay: {{ $index * 0.2 }}s;">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center shadow-lg border-4 border-oro-fiorentino mb-6">
                        <span class="text-3xl font-bold text-nero-profondo">{{ $index + 1 }}</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">{{ __('info_florence_egi.how.'.$step.'_title') }}</h3>
                    <p class="text-gray-600 px-4">{{ __('info_florence_egi.how.'.$step.'_desc') }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- AMMk SECTION --}}
    <section id="ammk" class="py-24 bg-blu-algoritmo text-white relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#D4AF37 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="fade-in-up">
                    <h2 class="font-serif text-4xl md:text-6xl font-bold mb-6">
                        {{ __('info_florence_egi.ammk.title') }}
                    </h2>
                    <p class="text-xl text-blue-200 mb-8 font-light">
                        {{ __('info_florence_egi.ammk.subtitle') }}
                    </p>
                    <p class="text-gray-300 mb-10">
                        {{ __('info_florence_egi.ammk.desc') }}
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach(['natan', 'asset', 'dist', 'compliance'] as $engine)
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20">
                            <h4 class="font-bold text-oro-fiorentino">{{ __('info_florence_egi.ammk.engine_'.$engine) }}</h4>
                            <p class="text-sm text-gray-300">{{ __('info_florence_egi.ammk.engine_'.$engine.'_desc') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="relative h-[500px] w-full bg-black/30 rounded-2xl border border-white/10 overflow-hidden fade-in-up shadow-2xl">
                    {{-- Placeholder for AMMk Visualization (could be another canvas or SVG) --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-64 h-64 border-2 border-oro-fiorentino rounded-full animate-[spin_10s_linear_infinite]"></div>
                        <div class="absolute w-48 h-48 border-2 border-white/30 rounded-full animate-[spin_15s_linear_infinite_reverse]"></div>
                        <div class="absolute text-center">
                            <span class="block text-4xl font-bold text-white">AMMk</span>
                            <span class="text-xs text-oro-fiorentino tracking-widest">ENGINE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TECH & COMPLIANCE TABS --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="font-serif text-4xl font-bold mb-4">{{ __('info_florence_egi.tech.title') }}</h2>
            </div>

            {{-- Vanilla JS Tabs --}}
            <div class="w-full max-w-4xl mx-auto" id="tech-tabs">
                <div class="flex justify-center space-x-4 mb-8 border-b border-gray-200">
                    <button class="tab-btn px-6 py-3 text-lg font-medium text-oro-fiorentino border-b-2 border-oro-fiorentino focus:outline-none" data-target="tab-tech">
                        {{ __('info_florence_egi.nav.tech') }}
                    </button>
                    <button class="tab-btn px-6 py-3 text-lg font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-target="tab-compliance">
                        Compliance
                    </button>
                    <button class="tab-btn px-6 py-3 text-lg font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-target="tab-payments">
                        Payments
                    </button>
                </div>

                {{-- Tab Contents --}}
                <div id="tab-tech" class="tab-content block fade-in-up">
                    <div class="bg-grigio-pietra p-8 rounded-2xl">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="font-bold text-xl mb-4">{{ __('info_florence_egi.tech.user_view') }}</h3>
                                <p class="text-gray-600">{{ __('info_florence_egi.tech.user_list') }}</p>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-4">{{ __('info_florence_egi.tech.system_view') }}</h3>
                                <p class="text-gray-600">{{ __('info_florence_egi.tech.system_list') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-compliance" class="tab-content hidden fade-in-up">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['gdpr', 'mica', 'tax', 'ip'] as $item)
                        <div class="p-6 border border-gray-200 rounded-xl hover:shadow-lg transition-shadow">
                            <h3 class="font-bold text-lg text-blu-algoritmo mb-2">{{ __('info_florence_egi.compliance.'.$item.'_title') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('info_florence_egi.compliance.'.$item.'_desc') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="tab-payments" class="tab-content hidden fade-in-up">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @for($i=1; $i<=4; $i++)
                        <div class="bg-white p-4 rounded-xl border border-gray-200 text-center">
                            <div class="text-2xl mb-2">💳</div>
                            <h3 class="font-bold text-sm mb-1">{{ __('info_florence_egi.payments.lvl'.$i.'_title') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('info_florence_egi.payments.lvl'.$i.'_desc') }}</p>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="py-32 bg-nero-profondo relative overflow-hidden text-center">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1634152962476-4b8a00e1915c?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-nero-profondo via-nero-profondo/80 to-transparent"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4">
            <h2 class="font-serif text-5xl md:text-7xl font-bold text-white mb-8">
                {!! __('info_florence_egi.cta_final.title') !!}
            </h2>
            <p class="text-oro-fiorentino text-xl mb-12 font-light italic">
                "{{ __('info_florence_egi.cta.quote') }}"
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#" class="px-10 py-5 text-lg font-bold text-nero-profondo bg-oro-fiorentino rounded-full hover:bg-white hover:scale-105 transition-all duration-300 shadow-[0_0_40px_rgba(212,175,55,0.5)]">
                    {{ __('info_florence_egi.cta_final.btn_creator') }}
                </a>
                <a href="#" class="px-10 py-5 text-lg font-bold text-white border border-white rounded-full hover:bg-white hover:text-nero-profondo transition-all duration-300">
                    {{ __('info_florence_egi.cta_final.btn_collector') }}
                </a>
            </div>
            
            <p class="mt-12 text-gray-500 text-sm tracking-widest uppercase">
                {{ __('info_florence_egi.cta_final.stats') }}
            </p>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-black text-gray-400 py-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <span class="font-serif text-xl font-bold text-white">Florence<span class="text-oro-fiorentino">EGI</span></span>
                <p class="text-xs mt-2">{{ __('info_florence_egi.footer.rights') }}</p>
            </div>
            <div class="flex space-x-6 text-sm">
                <a href="#" class="hover:text-white transition-colors">{{ __('info_florence_egi.footer.privacy') }}</a>
                <a href="#" class="hover:text-white transition-colors">{{ __('info_florence_egi.footer.terms') }}</a>
                <a href="#" class="hover:text-white transition-colors">{{ __('info_florence_egi.footer.whitepaper') }}</a>
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
                        b.classList.remove('text-oro-fiorentino', 'border-b-2', 'border-oro-fiorentino');
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
            }, { threshold: 0.1 });

            document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
        });
    </script>

    {{-- WEBGL 2 SHADER SCRIPT --}}
    {{-- Logic moved to resources/js/florence-hero.js --}}
</body>
</html>
