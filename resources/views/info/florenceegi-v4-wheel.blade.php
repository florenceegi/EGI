<!DOCTYPE html>
<html lang="it" prefix="og: https://ogp.me/ns#">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Primary Meta Tags --}}
    <title>{{ $translations['meta']['title'] }} - Wheel Experience</title>
    <meta name="title" content="{{ $translations['meta']['title'] }} - Wheel">
    <meta name="description" content="{{ $translations['meta']['description'] }}">
    <meta name="robots" content="noindex, nofollow">

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
            height: 100%;
            overflow-x: hidden;
            background: #0a0a0f;
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

        /* Loading state */
        .wheel-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0a0f;
            z-index: 99999;
        }

        .wheel-loading__spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(212, 175, 55, 0.1);
            border-top-color: #d4af37;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Skip link */
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

    {{-- Vite React App - V4 Wheel Version --}}
    @vite(['resources/react/florenceegi-info-light/mainV4Wheel.tsx'])
</head>

<body>
    {{-- Skip to content (Accessibility) --}}
    <a href="#root" class="skip-link">
        Salta al contenuto principale
    </a>

    {{-- React mount point --}}
    <div id="root" role="main" aria-label="FlorenceEGI - Wheel Menu Experience">
        {{-- Loading state (replaced by React) --}}
        <div class="wheel-loading">
            <div class="wheel-loading__spinner"></div>
        </div>
    </div>

    <noscript>
        <div style="padding: 2rem; text-align: center; background: #0a0a0f; color: white;">
            <h1>JavaScript Required</h1>
            <p>Per visualizzare questa pagina è necessario abilitare JavaScript.</p>
        </div>
    </noscript>
</body>

</html>
