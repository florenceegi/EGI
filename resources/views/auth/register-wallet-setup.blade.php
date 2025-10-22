<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('wallet_welcome.title') }} - {{ config('app.name') }}</title>

    {{-- Styles inline per questa pagina standalone --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    {{-- Include wallet welcome modal component --}}
    @include('components.wallet-welcome-modal')

    {{-- Force modal to open immediately --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('walletWelcomeModal');
            if (modal) {
                console.log('[RegisterWalletSetup] Opening modal immediately');
                modal.classList.remove('hidden');
            } else {
                console.error('[RegisterWalletSetup] Modal not found!');
            }
        });
    </script>
</body>

</html>
