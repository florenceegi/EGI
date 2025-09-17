<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Cookie Banner - Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center mb-8">Test Cookie Banner con Debug</h1>

        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h2 class="text-xl font-semibold mb-4">Controlli Debug</h2>

            <div class="flex gap-4 mb-4">
                <button onclick="clearLocalStorage()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Cancella localStorage
                </button>
                <button onclick="showLocalStorage()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Mostra localStorage
                </button>
                <button onclick="location.reload()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Ricarica Pagina
                </button>
            </div>

            <div id="debug-info" class="bg-gray-100 p-4 rounded text-sm font-mono"></div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Test Banner</h2>
            <p>Il banner dovrebbe apparire in basso alla pagina se non hai ancora dato il consenso.</p>
            <p>Dopo aver cliccato su un bottone (Accetta Tutti o Solo Essenziali), il banner dovrebbe sparire e non riapparire al reload della pagina.</p>
        </div>
    </div>

    <!-- Include del banner cookie -->
    @include('components.gdpr.cookie-banner')

    <script>
        function clearLocalStorage() {
            localStorage.removeItem('florenceegi_cookie_consent');
            showLocalStorage();
            console.log('localStorage cleared');
        }

        function showLocalStorage() {
            const stored = localStorage.getItem('florenceegi_cookie_consent');
            const debugInfo = document.getElementById('debug-info');
            debugInfo.innerHTML = `
                <strong>localStorage Content:</strong><br>
                Key: florenceegi_cookie_consent<br>
                Value: ${stored || 'null'}<br>
                <br>
                <strong>Parsed Data:</strong><br>
                ${stored ? JSON.stringify(JSON.parse(stored), null, 2) : 'No data'}
            `;
        }

        // Show localStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            showLocalStorage();
        });
    </script>
</body>
</html>
