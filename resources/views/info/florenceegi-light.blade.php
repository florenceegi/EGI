<!DOCTYPE html>
<html lang="it" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    {{-- Primary Meta Tags (from localization) --}}
    <title>{{ $translations['meta']['title'] }} - Light</title>
    <meta name="title" content="{{ $translations['meta']['title'] }}">
    <meta name="description" content="{{ $translations['meta']['description'] }}">
    <meta name="keywords" content="{{ $translations['meta']['keywords'] }}">
    <meta name="author" content="{{ $translations['meta']['author'] }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url('/info/florenceegi-light') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="FlorenceEGI">
    <meta property="og:title" content="{{ $translations['meta']['og_title'] }}">
    <meta property="og:description" content="{{ $translations['meta']['og_description'] }}">
    <meta property="og:url" content="{{ url('/info/florenceegi-light') }}">
    <meta property="og:image" content="{{ asset('images/florenceegi-og-image.jpg') }}">
    <meta property="og:locale" content="it_IT">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $translations['meta']['og_title'] }}">
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
                "url": "{{ url('/info/florenceegi-light') }}",
                "name": "{{ $translations['meta']['title'] }}",
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
        
        html, body {
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
    </style>

    {{-- Pass translations to React --}}
    <script>
        window.florenceEgiTranslations = @json($translations);
    </script>

    {{-- Vite React App - Light Version --}}
    @vite(['resources/react/florenceegi-info-light/main.tsx'])
</head>

<body>
    {{-- Skip to content (Accessibility) --}}
    <a href="#root" class="skip-link">
        Salta al contenuto principale
    </a>

    {{-- React mount point --}}
    <div id="root" role="main" aria-label="FlorenceEGI Informative Page - Light Version"></div>

    <noscript>
        <div style="padding: 2rem; text-align: center; background: #1a1a2e; color: white;">
            <h1>JavaScript Required</h1>
            <p>Per visualizzare questa pagina è necessario abilitare JavaScript.</p>
        </div>
    </noscript>
</body>
</html>
