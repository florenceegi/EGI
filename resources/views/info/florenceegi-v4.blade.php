<!DOCTYPE html>
<html lang="it" prefix="og: https://ogp.me/ns#">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Primary Meta Tags (from localization) --}}
    <title>{{ $translations['meta']['title'] }} - V4 Preview</title>
    <meta name="title" content="{{ $translations['meta']['title'] }} - V4">
    <meta name="description" content="{{ $translations['meta']['description'] }}">
    <meta name="keywords" content="{{ $translations['meta']['keywords'] }}">
    <meta name="author" content="{{ $translations['meta']['author'] }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="{{ url('/info/florenceegi-v4') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:title" content="{{ $translations['meta']['og_title'] }} - V4 Preview">
    <meta property="og:description" content="{{ $translations['meta']['og_description'] }}">
    <meta property="og:url" content="{{ url('/info/florenceegi-v4') }}">
    <meta property="og:image" content="{{ asset('images/florenceegi-og-image.jpg') }}">
    <meta property="og:locale" content="it_IT">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $translations['meta']['og_title'] }} - V4 Preview">
    <meta name="twitter:description" content="{{ $translations['meta']['og_description'] }}">

    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "name": "FlorenceEGI",
                "url": "{{ url('/') }}",
                "description": "{{ $translations['meta']['description'] }}"
            },
            {
                "@type": "WebPage",
                "url": "{{ url('/info/florenceegi-v4') }}",
                "name": "{{ $translations['meta']['title'] }} - V4 Preview",
                "description": "{{ $translations['meta']['description'] }}"
            }
        ]
    }
    </script>

    {{-- Critical CSS Reset --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        #root {
            width: 100%;
            min-height: 100vh;
        }

        /* Screen reader only */
        .skip-link {
            position: absolute;
            left: -9999px;
            z-index: 999;
            padding: 1rem;
            background-color: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 4px;
        }

        .skip-link:focus {
            left: 1rem;
            top: 1rem;
        }

        /* V4 Preview Banner */
        .v4-preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(90deg, #d4af37, #f4d03f);
            color: #0d0d14;
            text-align: center;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
        }
    </style>

    {{-- Pass translations to React --}}
    <script>
        window.florenceEgiTranslations = @json($translations);
    </script>

    {{-- Vite React App - V4 Version --}}
    @vite(['resources/react/florenceegi-info-light/mainV4.tsx'])
</head>

<body>
    {{-- V4 Preview Banner --}}
    <div class="v4-preview-banner">
        🚧 ANTEPRIMA V4 - Pagina in sviluppo
    </div>

    {{-- Skip to content (Accessibility) --}}
    <a href="#root" class="skip-link">
        Salta al contenuto principale
    </a>

    {{-- React mount point --}}
    <div id="root" role="main" aria-label="FlorenceEGI Informative Page - V4 Preview" style="margin-top: 36px;"></div>

    <noscript>
        <div style="padding: 2rem; text-align: center; background: #1a1a2e; color: white;">
            <h1>JavaScript Required</h1>
            <p>Per visualizzare questa pagina è necessario abilitare JavaScript.</p>
        </div>
    </noscript>
</body>

</html>
