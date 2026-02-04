{{-- resources/views/auth/login.blade.php --}}
{{-- 📜 Oracode OS1 View: User Login Page (Enhanced UX & Security) --}}
{{-- Upgraded to OS1 standards while maintaining all backend logic --}}
{{-- Features: Progressive validation, 2FA support, social login, premium UX --}}

<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Oracode OS1 Compliant -->
    <title>{{ __('login.seo_title') }}</title>
    <meta name="description" content="{{ __('login.seo_description') }}">
    <meta name="keywords" content="{{ __('login.seo_keywords') }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ __('login.og_title') }}">
    <meta property="og:description" content="{{ __('login.og_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ __('login.schema_page_name') }}",
        "description": "{{ __('login.schema_page_description') }}",
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Critical CSS OS1 Enhanced -->
    <style>
        :root {
            --oro-fiorentino: #D4A574;
            --verde-rinascita: #2D5016;
            --blu-algoritmo: #1B365D;
            --grigio-pietra: #6B6B6B;
            --rosso-urgenza: #C13120;
        }

        .font-rinascimento { font-family: 'Playfair Display', serif; }
        .font-corpo { font-family: 'Source Sans Pro', sans-serif; }

        .bg-rinascimento-gradient {
            background: linear-gradient(135deg,
                rgba(212, 165, 116, 0.1) 0%,
                rgba(45, 80, 22, 0.05) 50%,
                rgba(27, 54, 93, 0.1) 100%);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(212, 165, 116, 0.2);
        }

        .btn-rinascimento {
            background: linear-gradient(135deg, var(--oro-fiorentino) 0%, #E6B887 100%);
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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

        .btn-rinascimento.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .input-rinascimento {
            border: 2px solid rgba(212, 165, 116, 0.3);
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #1f2937; /* Grigio scuro per il testo */
            background-color: #ffffff; /* Background bianco esplicito */
        }

        .input-rinascimento::placeholder {
            color: #6b7280; /* Grigio medio per placeholder */
            opacity: 1;
        }

        .input-rinascimento:focus {
            border-color: var(--oro-fiorentino);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
            outline: none;
            color: #111827; /* Grigio ancora più scuro quando in focus */
        }

        .input-rinascimento.error {
            border-color: var(--rosso-urgenza);
            box-shadow: 0 0 0 3px rgba(193, 49, 32, 0.1);
        }

        .input-rinascimento.success {
            border-color: var(--verde-rinascita);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }

        .hero-pattern {
            background-image:
                radial-gradient(circle at 25% 25%, rgba(212, 165, 116, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(45, 80, 22, 0.05) 0%, transparent 50%);
        }

        /* Enhanced micro-interactions */
        .stats-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .fade-in-delay {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Password visibility toggle enhancement */
        .password-toggle {
            transition: all 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--oro-fiorentino);
        }

        /* Form progress indicator */
        .login-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--oro-fiorentino);
            transition: width 0.3s ease;
            z-index: 9999;
        }

        /* Success state animations */
        .success-check {
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% { transform: scale(0) rotate(0deg); }
            50% { transform: scale(1.2) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        /* Enhanced error states */
        .error-shake {
            animation: shake 0.6s ease-in-out;
        }

        @keyframes shake {
            0%, 20%, 40%, 60%, 80% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        }

        /* Two-factor input styling */
        .two-factor-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 1.25rem;
        }

        /* Social login enhancements */
        .social-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.3s ease;
        }

        .social-btn:hover::before {
            left: 100%;
        }
    </style>
</head>

<body class="min-h-screen bg-rinascimento-gradient font-corpo hero-pattern">
    <!-- Progress Bar -->
    <div id="login-progress" class="login-progress" style="width: 0%"></div>

    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="z-50 px-4 py-2 text-white rounded sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-oro-fiorentino">
        {{ __('login.skip_to_main') }}
    </a>

    <div class="flex min-h-screen">
        <!-- Left Side - Welcome Back -->
        <div class="relative hidden overflow-hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blu-algoritmo via-blu-algoritmo/90 to-verde-rinascita">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 flex flex-col justify-center px-12 text-white fade-in">
                <div class="mb-8">
                    <div class="flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-oro-fiorentino">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>

                    <h1 class="mb-4 text-4xl font-bold font-rinascimento">
                        {{ __('login.welcome_title_line1') }}<br>
                        <span class="text-oro-fiorentino">{{ __('login.welcome_title_line2') }}</span>
                    </h1>

                    <p class="mb-6 text-xl opacity-90">
                        {{ __('login.welcome_subtitle') }}
                    </p>
                </div>

                <!-- Enhanced Stats Dashboard -->
                <div class="p-6 mb-8 bg-white/10 backdrop-blur-sm rounded-xl stats-card fade-in-delay">
                    <h3 class="mb-4 font-semibold text-oro-fiorentino">{{ __('login.stats_title') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="stats-item">
                            <div class="text-2xl font-bold" id="epp-funds">€47.2K</div>
                            <div class="opacity-75">{{ __('login.stats_epp_funds') }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="text-2xl font-bold" id="active-creators">1.847</div>
                            <div class="opacity-75">{{ __('login.stats_active_creators') }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="text-2xl font-bold" id="current-fee">3.2%</div>
                            <div class="opacity-75">{{ __('login.stats_current_fee') }}</div>
                        </div>
                        <div class="stats-item">
                            <div class="text-2xl font-bold" id="daily-volume">₿ 12.4</div>
                            <div class="opacity-75">{{ __('login.stats_daily_volume') }}</div>
                        </div>
                    </div>
                </div>

                <blockquote class="pl-4 italic border-l-4 border-oro-fiorentino opacity-90 fade-in-delay">
                    {{ __('login.welcome_quote') }}
                </blockquote>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex items-center justify-center w-full p-8 lg:w-1/2">
            <div class="w-full max-w-md">

                <!-- Mobile Logo -->
                <div class="mb-8 text-center lg:hidden fade-in">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-oro-fiorentino">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold font-rinascimento text-blu-algoritmo">
                        FlorenceEGI
                    </h1>
                    <p class="text-grigio-pietra">{{ __('login.mobile_tagline') }}</p>
                </div>

                <main id="main-content" role="main" aria-labelledby="login-title">
                    <div class="p-8 shadow-xl glass-effect rounded-2xl fade-in">

                        <div class="mb-8">
                            <h2 id="login-title" class="mb-2 text-3xl font-semibold text-center font-rinascimento text-blu-algoritmo">
                                {{ __('login.form_title') }}
                            </h2>
                            <p class="text-center text-grigio-pietra">
                                {{ __('login.form_subtitle') }}
                            </p>
                        </div>

                        <!-- Success Messages -->
                        @if (session('success'))
                            <div class="p-4 mb-6 border rounded-lg bg-green-50 border-verde-rinascita fade-in" role="alert" aria-live="polite">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-verde-rinascita success-check" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3 text-verde-rinascita">
                                        {{ session('success') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Enhanced Error Messages -->
                        @if ($errors->any() || session('error'))
                            <div class="p-4 mb-6 border rounded-lg bg-red-50 border-rosso-urgenza error-shake fade-in" role="alert" aria-live="polite">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-rosso-urgenza" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-rosso-urgenza">
                                            Credenziali non valide
                                        </h3>
                                        <div class="mt-2 text-sm text-rosso-urgenza">
                                            @if(session('error'))
                                                <p>{{ session('error') }}</p>
                                            @endif
                                            @if ($errors->any())
                                                <ul class="list-disc list-inside">
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

                        <!-- Enhanced Two-Factor Challenge Message -->
                        @if (session('two-factor-challenge'))
                            <div class="p-4 mb-6 border rounded-lg bg-blue-50 border-blu-algoritmo fade-in" role="alert" aria-live="polite">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blu-algoritmo" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3 text-blu-algoritmo">
                                        <h3 class="font-medium">{{ __('login.two_factor_title') }}</h3>
                                        <p class="mt-1 text-sm">{{ __('login.two_factor_subtitle') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate id="login-form">
                            @csrf

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-blu-algoritmo">
                                    {{ __('login.label_email') }}
                                </label>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       class="block w-full px-4 py-3 text-lg input-rinascimento font-corpo"
                                       value="{{ old('email') }}"
                                       placeholder="{{ __('login.placeholder_email') }}"
                                       aria-describedby="email-error email-help">
                                <p id="email-help" class="mt-1 text-xs text-grigio-pietra">{{ __('login.help_email') }}</p>
                                @error('email')
                                    <p id="email-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-blu-algoritmo">
                                    {{ __('login.label_password') }}
                                </label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required
                                           class="block w-full px-4 py-3 pr-12 text-lg input-rinascimento font-corpo"
                                           placeholder="{{ __('login.placeholder_password') }}"
                                           aria-describedby="password-error password-help">
                                    <button type="button"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 password-toggle"
                                            onclick="togglePasswordVisibility()"
                                            aria-label="{{ __('login.toggle_password_visibility') }}">
                                        <svg id="eye-open" class="w-5 h-5 text-grigio-pietra" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="eye-closed" class="hidden w-5 h-5 text-grigio-pietra" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.122 3.122M12 12l-3.122-3.122m0 0L3 3m6.878 6.878L12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p id="password-help" class="mt-1 text-xs text-grigio-pietra">{{ __('login.help_password') }}</p>
                                @error('password')
                                    <p id="password-error" class="mt-1 text-sm text-rosso-urgenza" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Two-Factor Code (if needed) -->
                            @if (session('two-factor-challenge'))
                                <div class="fade-in">
                                    <label for="code" class="block mb-2 text-sm font-medium text-blu-algoritmo">
                                        {{ __('login.label_2fa_code') }}
                                    </label>
                                    <input id="code" name="code" type="text" autocomplete="one-time-code"
                                           class="block w-full px-4 py-3 text-lg input-rinascimento font-corpo two-factor-input"
                                           placeholder="{{ __('login.placeholder_2fa_code') }}"
                                           maxlength="6"
                                           aria-describedby="code-help">
                                    <p id="code-help" class="mt-1 text-xs text-grigio-pietra">
                                        {{ __('login.help_2fa_code') }}
                                    </p>
                                </div>

                                <div class="text-center fade-in">
                                    <p class="mb-2 text-sm text-grigio-pietra">{{ __('login.2fa_trouble_text') }}</p>
                                    <label for="recovery_code" class="block mb-2 text-sm font-medium text-blu-algoritmo">
                                        {{ __('login.label_recovery_code') }}
                                    </label>
                                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="one-time-code"
                                           class="block w-full px-4 py-3 text-lg text-center input-rinascimento font-corpo"
                                           placeholder="{{ __('login.placeholder_recovery_code') }}"
                                           aria-describedby="recovery-help">
                                    <p id="recovery-help" class="mt-1 text-xs text-grigio-pietra">
                                        {{ __('login.help_recovery_code') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox"
                                           class="w-4 h-4 rounded text-oro-fiorentino border-oro-fiorentino focus:ring-oro-fiorentino"
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember" class="block ml-2 text-sm cursor-pointer text-grigio-pietra">
                                        {{ __('login.remember_me') }}
                                    </label>
                                </div>

                                @if (Route::has('password.request'))
                                    <div class="text-sm">
                                        <a href="{{ route('password.request') }}"
                                           class="font-medium transition-colors text-oro-fiorentino hover:text-verde-rinascita">
                                            {{ __('login.forgot_password') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Enhanced Submit Button -->
                            <div>
                                <button type="submit"
                                        class="w-full px-6 py-4 text-lg font-semibold btn-rinascimento rounded-xl focus:outline-none focus:ring-4 focus:ring-oro-fiorentino focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="submit-btn"
                                        aria-describedby="submit-help">
                                    <span id="submit-text">{{ __('login.submit_button') }}</span>
                                    <span id="submit-loading" class="hidden">
                                        <svg class="inline w-5 h-5 mr-3 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('login.submit_loading') }}
                                    </span>
                                </button>
                                <p id="submit-help" class="mt-3 text-xs text-center text-grigio-pietra">
                                    {{ __('login.submit_help') }}
                                </p>
                            </div>

                            <!-- Enhanced Social Login Options -->
                            @if(config('services.google.client_id') || config('services.github.client_id'))
                                <div class="mt-6">
                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-oro-fiorentino/20"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-grigio-pietra">{{ __('login.social_divider') }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 mt-6">
                                        @if(config('services.google.client_id'))
                                            <a href="{{ route('auth.google') }}"
                                               class="inline-flex justify-center w-full px-4 py-3 text-sm font-medium transition-colors bg-white border border-oro-fiorentino/30 rounded-xl text-grigio-pietra hover:bg-oro-fiorentino/5 social-btn">
                                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                                </svg>
                                                {{ __('login.google_login_text') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Enhanced Registration Link -->
                            <div class="pt-4 text-center border-t border-oro-fiorentino/20">
                                <p class="text-grigio-pietra">
                                    {{ __('login.no_account_text') }}
                                    <a href="{{ route('register.wizard.step1') }}" class="font-medium transition-colors text-oro-fiorentino hover:text-verde-rinascita">
                                        {{ __('login.register_link') }}
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Enhanced GDPR Notice -->
                    <div class="mt-6 text-center fade-in-delay">
                        <p class="text-xs text-grigio-pietra">
                            {{ __('login.gdpr_notice_text') }}
                            <a href="{{ route('gdpr.privacy-policy') }}" class="text-oro-fiorentino hover:underline">{{ __('login.privacy_policy_link') }}</a>
                            {{ __('login.gdpr_and_text') }}
                            <a href="{{ route('gdpr.terms') }}" class="text-oro-fiorentino hover:underline">{{ __('login.terms_link') }}</a>
                        </p>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript OS1 Implementation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form elements
            const form = document.querySelector('#login-form');
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            const progressBar = document.getElementById('login-progress');

            // Form progress tracking
            function updateProgress() {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const codeInput = document.getElementById('code');
                const recoveryInput = document.getElementById('recovery_code');

                let filledFields = 0;
                let totalRequiredFields = 2; // email and password

                if (emailInput.value.trim() !== '') filledFields++;
                if (passwordInput.value.trim() !== '') filledFields++;

                // If two-factor is active, adjust requirements
                if (codeInput || recoveryInput) {
                    totalRequiredFields = 3;
                    if ((codeInput && codeInput.value.trim() !== '') ||
                        (recoveryInput && recoveryInput.value.trim() !== '')) {
                        filledFields++;
                    }
                }

                const progress = Math.round((filledFields / totalRequiredFields) * 100);
                progressBar.style.width = `${progress}%`;
            }

            // Enhanced password visibility toggle
            window.togglePasswordVisibility = function() {
                const passwordInput = document.getElementById('password');
                const eyeOpen = document.getElementById('eye-open');
                const eyeClosed = document.getElementById('eye-closed');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            };

            // Progressive form validation
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

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

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;

                    this.classList.remove('error', 'success');

                    if (password.length > 0) {
                        if (password.length >= 6) {
                            this.classList.add('success');
                        } else {
                            this.classList.add('error');
                        }
                    }
                    updateProgress();
                });
            }

            // Enhanced two-factor code handling
            const codeInput = document.getElementById('code');
            if (codeInput) {
                codeInput.addEventListener('input', function(e) {
                    // Remove any non-digit characters
                    e.target.value = e.target.value.replace(/\D/g, '');

                    // Auto-submit when 6 digits are entered
                    if (e.target.value.length === 6) {
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                    updateProgress();
                });

                // Focus the code input automatically
                codeInput.focus();
            }

            // Enhanced form submission with loading state
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    // Validate form before submission
                    let isValid = true;

                    // Email validation
                    const email = emailInput.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!email || !emailRegex.test(email)) {
                        emailInput.classList.add('error');
                        isValid = false;
                    }

                    // Password validation
                    const password = passwordInput.value.trim();
                    if (!password || password.length < 6) {
                        passwordInput.classList.add('error');
                        isValid = false;
                    }

                    if (!isValid) {
                        e.preventDefault();

                        // Scroll to first error
                        const firstError = document.querySelector('.input-rinascimento.error');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstError.focus();
                        }

                        // Show error notification
                        showNotification('Per favore controlla i campi evidenziati', 'error');
                        return;
                    }

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('loading');
                    submitText.classList.add('hidden');
                    submitLoading.classList.remove('hidden');
                    progressBar.style.width = '100%';
                });
            }

            // Auto-clear error states on input
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('error');
                    const errorElement = document.getElementById(this.id + '-error');
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                });

                // Update progress on any input change
                input.addEventListener('input', updateProgress);
            });

            // Enhanced keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.type !== 'submit') {
                    const formInputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled])'));
                    const currentIndex = formInputs.indexOf(e.target);
                    const nextInput = formInputs[currentIndex + 1];

                    if (nextInput) {
                        nextInput.focus();
                        e.preventDefault();
                    } else {
                        // If last input, submit form
                        form.submit();
                    }
                }

                // Escape key to clear errors
                if (e.key === 'Escape') {
                    const errors = document.querySelectorAll('.error');
                    errors.forEach(el => el.classList.remove('error'));
                }
            });

            // Notification system
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 fade-in ${
                    type === 'error' ? 'bg-rosso-urgenza text-white' :
                    type === 'success' ? 'bg-verde-rinascita text-white' :
                    'bg-blu-algoritmo text-white'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            // Real-time stats update simulation
            function updateStats() {
                const statsItems = [
                    { id: 'epp-funds', values: ['€47.2K', '€47.5K', '€47.8K', '€48.1K'] },
                    { id: 'active-creators', values: ['1.847', '1.851', '1.856', '1.862'] },
                    { id: 'current-fee', values: ['3.2%', '3.1%', '3.3%', '3.0%'] },
                    { id: 'daily-volume', values: ['₿ 12.4', '₿ 12.7', '₿ 13.1', '₿ 13.5'] }
                ];

                statsItems.forEach(item => {
                    const element = document.getElementById(item.id);
                    if (element) {
                        const randomValue = item.values[Math.floor(Math.random() * item.values.length)];
                        if (element.textContent !== randomValue) {
                            element.textContent = randomValue;
                            element.classList.add('text-oro-fiorentino');
                            setTimeout(() => {
                                element.classList.remove('text-oro-fiorentino');
                            }, 1000);
                        }
                    }
                });
            }

            // Update stats every 15 seconds
            setInterval(updateStats, 15000);

            // Initialize progress
            updateProgress();

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            // Observe all elements that should animate
            const animatableElements = document.querySelectorAll('.stats-card, .social-btn');
            animatableElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
