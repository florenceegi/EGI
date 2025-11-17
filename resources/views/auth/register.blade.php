{{-- resources/views/auth/register.blade.php --}}
{{-- 📜 Oracode OS1 View: User Registration Page (GDPR Compliant) --}}
{{-- Updated with new user types: patron, collector, enterprise, trader_pro --}}
{{-- Full OS1 Implementation: Zero Placeholder, Complete JavaScript, Optimized UX --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Oracode OS1 Compliant -->
    <title>{{ __('register.seo_title') }}</title>
    <meta name="description" content="{{ __('register.seo_description') }}">
    <meta name="keywords" content="{{ __('register.seo_keywords') }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ __('register.og_title') }}">
    <meta property="og:description" content="{{ __('register.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ __('register.schema_page_name') }}",
        "description": "{{ __('register.schema_page_description') }}",
        "url": "{{ url()->current() }}",
        "isPartOf": {
            "@type": "WebSite",
            "name": "FlorenceEGI",
            "url": "{{ config('app.url') }}"
        }
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Stili del tema "Rinascimento" OS1 Enhanced --}}
    <style>
        :root {
            --oro-fiorentino: #D4A574;
            /* Oro Fiorentino */
            --verde-rinascita: #2D5016;
            /* Verde Rinascita Intenso */
            --blu-algoritmo: #1B365D;
            /* Blu Algoritmo Profondo */
            --grigio-pietra: #6B6B6B;
            /* Grigio Pietra Serena */
            --rosso-urgenza: #C13120;
            /* Rosso Urgenza Segnaletica */
            --amber-500: #f59e0b;
            /* Amber per Trader Pro */

            /* Palette Commissioner */
            --rosso-committente-extralight: #FBE9E9;
            --rosso-committente-light: #D97F81;
            --rosso-committente: #A12C2F;
            /* DEFAULT */
            --rosso-committente-dark: #802225;
            --rosso-committente-extradark: #5E1A1C;
            --rosso-committente-text: #FFFFFF;
        }

        .font-rinascimento {
            font-family: 'Playfair Display', serif;
        }

        .font-corpo {
            font-family: 'Source Sans Pro', sans-serif;
        }

        .bg-rinascimento-gradient {
            background: linear-gradient(135deg, rgba(212, 165, 116, 0.1) 0%, rgba(45, 80, 22, 0.05) 50%, rgba(27, 54, 93, 0.1) 100%);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(212, 165, 116, 0.2);
        }

        .btn-rinascimento {
            background: linear-gradient(135deg, var(--oro-fiorentino) 0%, #E6B887 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-rinascimento:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 165, 116, 0.3);
        }

        .btn-rinascimento:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        .input-rinascimento {
            border: 2px solid rgba(212, 165, 116, 0.3);
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #1f2937;
            /* Grigio scuro per il testo */
            background-color: #ffffff;
            /* Background bianco esplicito */
        }

        .input-rinascimento::placeholder {
            color: #6b7280;
            /* Grigio medio per placeholder */
            opacity: 1;
        }

        .input-rinascimento:focus {
            border-color: var(--oro-fiorentino);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
            outline: none;
            color: #111827;
            /* Grigio ancora più scuro quando in focus */
        }

        .input-rinascimento.error {
            border-color: var(--rosso-urgenza);
            box-shadow: 0 0 0 3px rgba(193, 49, 32, 0.1);
        }

        .input-rinascimento.success {
            border-color: var(--verde-rinascita);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .consent-card {
            border: 1px solid rgba(212, 165, 116, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .consent-card:hover {
            border-color: var(--oro-fiorentino);
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.1);
        }

        .consent-card.selected {
            border: 2px solid var(--oro-fiorentino);
            background-color: rgba(212, 165, 116, 0.05);
            box-shadow: 0 0 0 1px var(--oro-fiorentino);
        }

        .consent-card.error {
            border: 2px solid var(--rosso-urgenza);
            box-shadow: 0 0 0 1px var(--rosso-urgenza);
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            transition: all 0.3s ease;
            background: #e5e7eb;
        }

        .password-strength.weak {
            background: linear-gradient(90deg, #ef4444 30%, #e5e7eb 30%);
        }

        .password-strength.fair {
            background: linear-gradient(90deg, #f59e0b 50%, #e5e7eb 50%);
        }

        .password-strength.good {
            background: linear-gradient(90deg, #10b981 75%, #e5e7eb 75%);
        }

        .password-strength.strong {
            background: linear-gradient(90deg, var(--verde-rinascita) 100%, #e5e7eb 100%);
        }

        /* User Type Animation */
        .user-type-icon {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .consent-card.selected .user-type-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* Loading State */
        .btn-loading {
            position: relative;
            overflow: hidden;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        /* Form Progress */
        .form-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--oro-fiorentino);
            transition: width 0.3s ease;
            z-index: 9999;
        }

        /* Micro-interactions */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

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

        /* Color dynamic classes fix - Updated */
        .bg-oro-fiorentino {
            background-color: var(--oro-fiorentino);
        }

        .bg-verde-rinascita {
            background-color: var(--verde-rinascita);
        }

        .bg-blu-algoritmo {
            background-color: var(--blu-algoritmo);
        }

        .bg-grigio-pietra {
            background-color: var(--grigio-pietra);
        }

        .bg-rosso-committente {
            background-color: var(--rosso-committente);
        }

        .bg-teal-500 {
            background-color: #14b8a6;
        }

        .bg-amber-500 {
            background-color: var(--amber-500);
        }
    </style>
</head>

<body class="bg-rinascimento-gradient font-corpo min-h-screen text-grigio-pietra">
    <!-- Progress Bar -->
    <div id="form-progress" class="form-progress" style="width: 0%"></div>

    <!-- Skip Link -->
    <a href="#main-content"
        class="bg-oro-fiorentino sr-only rounded px-4 py-2 text-white focus:not-sr-only focus:absolute focus:left-4 focus:top-4">
        {{ __('register.skip_to_main') }}
    </a>

    <div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-2xl space-y-8">

            <header class="fade-in text-center" role="banner">
                <div class="bg-oro-fiorentino mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h1 class="font-rinascimento mb-4 text-4xl font-bold text-blu-algoritmo sm:text-5xl">
                    {!! __('register.main_title_html') !!}
                </h1>
                <p class="mx-auto max-w-lg text-xl leading-relaxed text-grigio-pietra">
                    {{ __('register.subtitle') }}
                </p>
                <p class="mt-4 font-semibold text-verde-rinascita">
                    {{ __('register.platform_grows_benefit') }}
                </p>
            </header>

            <main id="main-content" role="main" aria-labelledby="registration-title">
                <div class="glass-effect fade-in rounded-2xl p-8 shadow-xl sm:p-10">
                    <div class="mb-8">
                        <h2 id="registration-title"
                            class="font-rinascimento text-center text-2xl font-semibold text-blu-algoritmo">
                            {{ __('register.form_title') }}
                        </h2>
                        <p class="mt-2 text-center text-grigio-pietra">
                            {{ __('register.form_subtitle') }}
                        </p>
                    </div>

                    {{-- Error Messages --}}
                    @if ($errors->any() || session('error'))
                        <div class="fade-in mb-6 rounded-lg border border-rosso-urgenza bg-red-50 p-4" role="alert"
                            aria-live="polite">
                            <div class="flex">
                                <svg class="h-5 w-5 text-rosso-urgenza" fill="currentColor" viewBox="0 0 20 20"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-rosso-urgenza">{{ __('register.error_title') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-rosso-urgenza">
                                        @if (session('error'))
                                            <p>{{ session('error') }}</p>
                                        @endif
                                        @if ($errors->any())
                                            <ul class="list-inside list-disc">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-6" novalidate
                        id="registration-form">
                        @csrf

                        {{-- User Type Selection - Updated with new types --}}
                        <fieldset class="space-y-4">
                            <legend class="mb-4 text-lg font-semibold text-blu-algoritmo">
                                {{ __('register.user_type_legend') }}</legend>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @php
                                    $userTypes = [
                                        'creator' => [
                                            'icon_svg_path' =>
                                                'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
                                            'color' => 'oro-fiorentino',
                                        ],
                                        'patron' => [
                                            'icon_svg_path' =>
                                                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                                            'color' => 'verde-rinascita',
                                        ],
                                        'commissioner' => [
                                            // <-- 'collector' rinominato in 'commissioner'
                                            'icon_svg_path' =>
                                                'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', // <-- Icona aggiornata con stile outline
                                            'color' => 'rosso-committente', // <-- Colore rosso per il Commissioner
                                        ],
                                        'collector' => [
                                            'icon_svg_path' =>
                                                'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                                            'color' => 'blu-algoritmo',
                                        ],
                                        'enterprise' => [
                                            'icon_svg_path' =>
                                                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                            'color' => 'grigio-pietra',
                                        ],
                                        'trader_pro' => [
                                            'icon_svg_path' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                            'color' => 'amber-500',
                                        ],
                                        'epp' => [
                                            'icon_svg_path' =>
                                                'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c1.483 0 2.795-.298 3.996-.786M12 21c-1.483 0-2.795-.298-3.996-.786M3.786 15.004A9.004 9.004 0 0112 3c4.032 0 7.406 2.226 8.716 5.253M3.786 15.004A9.004 9.004 0 0012 21m-2.284-5.253A2.998 2.998 0 0012 15a2.998 2.998 0 002.284-1.253M12 12a2.998 2.998 0 01-2.284-1.253A2.998 2.998 0 0112 9a2.998 2.998 0 012.284 1.253A2.998 2.998 0 0112 12Z',
                                            'color' => 'teal-500',
                                        ],
                                        'pa_entity' => [
                                            'icon_svg_path' =>
                                                'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
                                            'color' => 'blu-algoritmo',
                                        ],
                                    ];
                                    $selectedUserType = old('user_type', 'creator');
                                @endphp
                                @foreach ($userTypes as $type => $details)
                                    <label
                                        class="consent-card {{ $selectedUserType === $type ? 'selected' : '' }} group cursor-pointer p-4 transition-all duration-300 ease-in-out"
                                        for="user_type_{{ $type }}" data-user-type="{{ $type }}">
                                        <input type="radio" id="user_type_{{ $type }}" name="user_type"
                                            value="{{ $type }}" class="sr-only"
                                            {{ $selectedUserType === $type ? 'checked' : '' }}
                                            aria-describedby="{{ $type }}-description" required>
                                        <div class="text-center">
                                            <div
                                                class="bg-{{ $details['color'] }} user-type-icon mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full transition-transform group-hover:scale-110">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="{{ $details['icon_svg_path'] }}" />
                                                </svg>
                                            </div>
                                            <h3 class="font-semibold text-blu-algoritmo">
                                                {{ __('register.user_type_' . $type) }}</h3>
                                            <p id="{{ $type }}-description"
                                                class="mt-1 text-sm text-grigio-pietra">
                                                {{ __('register.user_type_' . $type . '_desc') }}
                                            </p>

                                            {{-- ✅ NUOVO LINK AI TERMINI --}}
                                            <a href="{{ route('legal.terms', ['userType' => $type, 'redirect_url' => url()->full()]) }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="text-oro-fiorentino mt-3 inline-block text-xs transition-colors hover:text-verde-rinascita hover:underline"
                                                onclick="event.stopPropagation();">
                                                {{ __('register.read_the_terms') }}
                                            </a>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('user_type')
                                <p class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        {{-- Personal Information --}}
                        <div class="border-oro-fiorentino/20 grid grid-cols-1 gap-6 border-t pt-6">
                            <div>
                                <label for="name"
                                    class="mb-2 block text-sm font-medium text-blu-algoritmo">{{ __('register.label_name') }}
                                    *</label>
                                <input id="name" name="name" type="text" autocomplete="name" required
                                    class="input-rinascimento font-corpo block w-full px-4 py-3"
                                    value="{{ old('name') }}" aria-describedby="name-error name-help">
                                <p id="name-help" class="mt-1 text-xs text-grigio-pietra">
                                    {{ __('register.name_help') }}</p>
                                @error('name')
                                    <p id="name-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="nick_name"
                                    class="mb-2 block text-sm font-medium text-blu-algoritmo">{{ __('register.label_nick_name') }}</label>
                                <input id="nick_name" name="nick_name" type="text" autocomplete="nickname"
                                    class="input-rinascimento font-corpo block w-full px-4 py-3"
                                    value="{{ old('nick_name') }}" aria-describedby="nick-name-error nick-name-help">
                                <p id="nick-name-help" class="mt-1 text-xs text-grigio-pietra">
                                    {{ __('register.nick_name_help') }}
                                </p>
                                @error('nick_name')
                                    <p id="nick-name-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email"
                                    class="mb-2 block text-sm font-medium text-blu-algoritmo">{{ __('register.label_email') }}
                                    *</label>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="input-rinascimento font-corpo block w-full px-4 py-3"
                                    value="{{ old('email') }}" aria-describedby="email-error email-help">
                                <p id="email-help" class="mt-1 text-xs text-grigio-pietra">
                                    {{ __('register.email_help') }}</p>
                                @error('email')
                                    <p id="email-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Fields --}}
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="password"
                                    class="mb-2 block text-sm font-medium text-blu-algoritmo">{{ __('register.label_password') }}
                                    *</label>
                                <input id="password" name="password" type="password" autocomplete="new-password"
                                    required class="input-rinascimento font-corpo block w-full px-4 py-3"
                                    aria-describedby="password-error password-help password-strength-indicator">
                                <div id="password-strength-bar" class="password-strength"></div>
                                <p id="password-help" class="mt-2 text-xs text-grigio-pietra">
                                    {{ __('register.password_help') }}</p>
                                @error('password')
                                    <p id="password-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="mb-2 block text-sm font-medium text-blu-algoritmo">{{ __('register.label_password_confirmation') }}
                                    *</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    autocomplete="new-password" required
                                    class="input-rinascimento font-corpo block w-full px-4 py-3"
                                    aria-describedby="password-confirmation-error password-confirmation-help">
                                <p id="password-confirmation-help" class="mt-1 text-xs text-grigio-pietra">
                                    {{ __('register.password_confirmation_help') }}</p>
                                @error('password_confirmation')
                                    <p id="password-confirmation-error" class="mt-1 text-sm text-rosso-urgenza"
                                        role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- GDPR Consent Section --}}
                        <fieldset class="border-oro-fiorentino/20 space-y-4 border-t pt-6">
                            <legend class="mb-4 text-lg font-semibold text-blu-algoritmo">
                                {{ __('register.privacy_legend') }}
                                <span
                                    class="mt-1 block text-sm font-normal text-grigio-pietra">{{ __('register.privacy_subtitle') }}</span>
                            </legend>

                            {{-- Required Legal Consents --}}
                            <div class="space-y-4">
                                @php
                                    $requiredConsents = [
                                        'privacy_policy_accepted' => [
                                            'route' => 'gdpr.privacy-policy',
                                            'link_text_key' => 'register.privacy_policy_link_text',
                                        ],
                                        'terms_accepted' => [
                                            'route' => 'gdpr.terms',
                                            'link_text_key' => 'register.terms_link_text',
                                        ],
                                        'age_confirmation' => [],
                                    ];
                                @endphp
                                @foreach ($requiredConsents as $consentKey => $details)
                                    <div class="consent-card {{ $errors->has($consentKey) ? 'error' : '' }} bg-green-50/50 p-4"
                                        data-consent-type="required">
                                        <div class="flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input id="{{ $consentKey }}" name="{{ $consentKey }}"
                                                    type="checkbox" required value="1"
                                                    class="text-oro-fiorentino border-oro-fiorentino focus:ring-oro-fiorentino h-4 w-4 rounded"
                                                    {{ old($consentKey) ? 'checked' : '' }}
                                                    aria-describedby="{{ $consentKey }}-description">
                                            </div>
                                            <div class="ml-3">
                                                <label for="{{ $consentKey }}"
                                                    class="cursor-pointer text-sm font-medium text-blu-algoritmo">{{ __('register.consent_label_' . $consentKey) }}
                                                    *</label>
                                                <p id="{{ $consentKey }}-description"
                                                    class="mt-1 text-xs text-grigio-pietra">
                                                    {{ __('register.consent_desc_' . $consentKey) }}
                                                    @if (!empty($details))
                                                        <a href="{{ route($details['route']) }}"
                                                            class="text-oro-fiorentino hover:underline"
                                                            target="_blank" rel="noopener">
                                                            {{ __($details['link_text_key']) }} <span
                                                                class="sr-only">{{ __('register.opens_new_window') }}</span>
                                                        </a>
                                                    @endif
                                                </p>
                                                @error($consentKey)
                                                    <p class="mt-1 text-xs text-rosso-urgenza" role="alert">
                                                        {{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Optional Consents --}}
                            <div class="mt-6 space-y-3">
                                <h4 class="text-sm font-medium text-blu-algoritmo">
                                    {{ __('register.optional_consents_title') }}
                                    <span
                                        class="mt-1 block text-xs font-normal text-grigio-pietra">{{ __('register.optional_consents_subtitle') }}</span>
                                </h4>
                                @php
                                    $optionalConsents = ['analytics', 'marketing', 'profiling'];
                                @endphp
                                @foreach ($optionalConsents as $consentType)
                                    <div class="consent-card p-4" data-consent-type="optional">
                                        <div class="flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input id="consents_{{ $consentType }}"
                                                    name="consents[{{ $consentType }}]" type="checkbox"
                                                    value="1"
                                                    class="h-4 w-4 rounded border-verde-rinascita text-verde-rinascita focus:ring-verde-rinascita"
                                                    {{ old('consents.' . $consentType) ? 'checked' : '' }}
                                                    aria-describedby="{{ $consentType }}-description">
                                            </div>
                                            <div class="ml-3">
                                                <label for="consents_{{ $consentType }}"
                                                    class="cursor-pointer text-sm font-medium text-blu-algoritmo">{{ __('register.consent_label_optional_' . $consentType) }}</label>
                                                <p id="{{ $consentType }}-description"
                                                    class="mt-1 text-xs text-grigio-pietra">
                                                    {{ __('register.consent_desc_optional_' . $consentType) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="pt-6">
                            <button type="submit" id="submit-button"
                                class="btn-rinascimento focus:ring-oro-fiorentino w-full rounded-xl px-6 py-4 text-lg font-semibold focus:outline-none focus:ring-4 focus:ring-opacity-50 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-describedby="submit-help">
                                <span id="submit-text">{{ __('register.submit_button') }}</span>
                            </button>
                            <p id="submit-help" class="mt-3 text-center text-xs text-grigio-pietra">
                                {{ __('register.submit_help') }}</p>
                        </div>

                        <div class="border-oro-fiorentino/20 border-t pt-4 text-center">
                            <p class="text-grigio-pietra">
                                {{ __('register.already_registered_prompt') }}
                                <a href="{{ route('login') }}"
                                    class="text-oro-fiorentino font-medium transition-colors hover:text-verde-rinascita">{{ __('register.login_link') }}</a>
                            </p>
                        </div>
                    </form>
                </div>
            </main>

            <footer class="fade-in space-y-4 text-center" role="contentinfo">
                <div class="flex items-center justify-center space-x-6 text-sm text-grigio-pietra">
                    <div class="flex items-center">
                        <svg class="mr-2 h-4 w-4 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('register.footer_gdpr') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="mr-2 h-4 w-4 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('register.footer_data_protected') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="mr-2 h-4 w-4 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('register.footer_real_impact') }}
                    </div>
                </div>
                <p class="mx-auto max-w-md text-xs text-grigio-pietra">{{ __('register.footer_compliance_note') }}</p>
            </footer>
        </div>
    </div>

    {{-- OS1 Complete JavaScript Implementation - Updated for new user types --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form state
            const form = document.getElementById('registration-form');
            const submitButton = document.getElementById('submit-button');
            const progressBar = document.getElementById('form-progress');
            let formProgress = 0;

            // Update progress bar
            function updateProgress() {
                const requiredFields = form.querySelectorAll('input[required]');
                const filledFields = Array.from(requiredFields).filter(field => {
                    if (field.type === 'checkbox') return field.checked;
                    if (field.type === 'radio') {
                        const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                        return Array.from(radioGroup).some(radio => radio.checked);
                    }
                    return field.value.trim() !== '';
                });

                formProgress = Math.round((filledFields.length / requiredFields.length) * 100);
                progressBar.style.width = `${formProgress}%`;
            }

            // User type selection enhancement - Updated for new user types
            const userTypeInputs = document.querySelectorAll('input[name="user_type"]');
            const userTypeCards = document.querySelectorAll('label[data-user-type]');

            userTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    userTypeCards.forEach(card => {
                        card.classList.remove('selected');
                    });
                    if (this.checked) {
                        this.closest('label[data-user-type]').classList.add('selected');
                        updateProgress();
                    }
                });

                // Initialize selected state on load
                if (input.checked) {
                    input.closest('label[data-user-type]').classList.add('selected');
                }
            });

            // Password strength calculator (Complete Implementation)
            const passwordInput = document.getElementById('password');
            const passwordHelp = document.getElementById('password-help');
            const passwordStrengthBar = document.getElementById('password-strength-bar');

            if (passwordInput && passwordHelp && passwordStrengthBar) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strength = calculatePasswordStrength(password);
                    updatePasswordHelp(passwordHelp, passwordStrengthBar, strength, password.length);
                    updateProgress();
                });
            }

            // Calculate password strength (0-5 scale)
            function calculatePasswordStrength(password) {
                let strength = 0;

                // Length check
                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;

                // Character variety checks
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                return Math.min(strength, 5);
            }

            // Update password help text and visual indicator
            function updatePasswordHelp(helpElement, strengthBar, strength, length) {
                const messages = [
                    'Inserisci una password',
                    'Password molto debole',
                    'Password debole',
                    'Password accettabile',
                    'Password buona',
                    'Password forte',
                    'Password eccellente'
                ];

                const classes = ['', 'weak', 'weak', 'fair', 'good', 'strong', 'strong'];
                const colors = [
                    'text-grigio-pietra',
                    'text-rosso-urgenza',
                    'text-rosso-urgenza',
                    'text-yellow-600',
                    'text-verde-rinascita',
                    'text-verde-rinascita',
                    'text-verde-rinascita',

                ];

                if (length === 0) {
                    helpElement.textContent = 'Minimo 8 caratteri, includi maiuscole, minuscole e numeri';
                    helpElement.className = 'mt-2 text-xs text-grigio-pietra';
                    strengthBar.className = 'password-strength';
                } else {
                    helpElement.textContent = messages[strength] || messages[0];
                    helpElement.className = `mt-2 text-xs ${colors[strength]}`;
                    strengthBar.className = `password-strength ${classes[strength]}`;
                }
            }

            // Password confirmation validation
            const passwordConfirmation = document.getElementById('password_confirmation');
            if (passwordConfirmation) {
                passwordConfirmation.addEventListener('input', function() {
                    const password = passwordInput.value;
                    const confirmation = this.value;

                    this.classList.remove('error', 'success');

                    if (confirmation.length > 0) {
                        if (password === confirmation) {
                            this.classList.add('success');
                        } else {
                            this.classList.add('error');
                        }
                    }
                    updateProgress();
                });
            }

            // Email validation
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    const email = this.value;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    this.classList.remove('error', 'success');

                    if (email.length > 0) {
                        if (emailRegex.test(email)) {
                            this.classList.add('success');
                        } else {
                            this.classList.add('error');
                        }
                    }
                    updateProgress();
                });
            }

            // Name validation
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('input', function() {
                    const name = this.value.trim();

                    this.classList.remove('error', 'success');

                    if (name.length > 0) {
                        if (name.length >= 2) {
                            this.classList.add('success');
                        } else {
                            this.classList.add('error');
                        }
                    }
                    updateProgress();
                });
            }

            // GDPR Consent handling with visual feedback
            const requiredConsents = document.querySelectorAll('input[type="checkbox"][required]');
            requiredConsents.forEach(input => {
                input.addEventListener('change', function() {
                    const card = this.closest('.consent-card');
                    if (card) {
                        card.classList.remove('error');
                        if (!this.checked) {
                            card.classList.add('error');
                        }
                    }
                    updateProgress();
                });
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                let hasErrors = false;

                // Validate required consents
                requiredConsents.forEach(input => {
                    const card = input.closest('.consent-card');
                    if (!input.checked) {
                        hasErrors = true;
                        if (card) card.classList.add('error');
                    } else {
                        if (card) card.classList.remove('error');
                    }
                });

                // Validate password match
                const password = passwordInput.value;
                const confirmation = passwordConfirmation.value;
                if (password !== confirmation) {
                    hasErrors = true;
                    passwordConfirmation.classList.add('error');
                }

                // Validate email format
                const email = emailInput.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    hasErrors = true;
                    emailInput.classList.add('error');
                }

                // Validate name
                const name = nameInput.value.trim();
                if (name.length < 2) {
                    hasErrors = true;
                    nameInput.classList.add('error');
                }

                if (hasErrors) {
                    e.preventDefault();

                    // Scroll to first error
                    const firstError = document.querySelector(
                        '.consent-card.error, .input-rinascimento.error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }

                    // Show error message
                    const errorMessage = document.createElement('div');
                    errorMessage.className =
                        'fixed top-4 right-4 bg-rosso-urgenza text-white px-4 py-2 rounded-lg shadow-lg z-50 fade-in';
                    errorMessage.textContent = 'Per favore correggi gli errori evidenziati';
                    document.body.appendChild(errorMessage);

                    setTimeout(() => {
                        errorMessage.remove();
                    }, 5000);

                    return false;
                }

                // Show loading state
                submitButton.disabled = true;
                submitButton.classList.add('btn-loading');
                document.getElementById('submit-text').textContent = 'Registrazione in corso...';
                progressBar.style.width = '100%';
            });

            // Real-time form validation
            const allInputs = form.querySelectorAll('input');
            allInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    // Remove error states on valid input
                    if (this.checkValidity()) {
                        this.classList.remove('error');
                    }
                });

                input.addEventListener('input', updateProgress);
            });

            // Initialize progress
            updateProgress();

            // Smooth animations for form sections
            const sections = document.querySelectorAll('fieldset, .grid');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, {
                threshold: 0.1
            });

            sections.forEach(section => {
                observer.observe(section);
            });

            // Accessibility improvements
            document.addEventListener('keydown', function(e) {
                // Escape key to clear form errors
                if (e.key === 'Escape') {
                    const errors = document.querySelectorAll('.error');
                    errors.forEach(el => el.classList.remove('error'));
                }
            });
        });
    </script>
</body>

</html>
