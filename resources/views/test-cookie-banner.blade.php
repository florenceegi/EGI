<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Cookie Banner</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <h1 class="p-8 text-2xl font-bold text-center">Test Cookie Banner</h1>

    <!-- Include del banner cookie -->
    @include('components.gdpr.cookie-banner')

    <div class="p-8">
        <p>Questa è una pagina di test per verificare il funzionamento del cookie banner.</p>
        <p>Dovrebbe apparire il banner in basso alla pagina.</p>
    </div>
</body>
</html>
