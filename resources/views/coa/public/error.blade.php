<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Errore Certificato - CoA Error</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-red-50 to-rose-50">

{{-- Error Page --}}
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">

        {{-- Title Header --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-red-900 mb-2">
                Errore Certificato
            </h1>
            <p class="text-red-700 text-lg">
                Impossibile caricare il certificato richiesto
            </p>
        </div>

        {{-- Error Banner --}}
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-red-500 text-white rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="font-bold text-xl mb-1">Certificato Non Disponibile</div>
                        <div class="text-base opacity-90">
                            {{ $message ?? 'Si è verificato un errore durante il caricamento del certificato.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Details --}}
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center">
                    <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Problemi possibili:
                    </h2>

                    <div class="space-y-4 text-left max-w-2xl mx-auto">
                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <p class="text-gray-700">
                                <strong>Numero seriale non valido:</strong> Il numero seriale inserito potrebbe non essere corretto o non esistere nel database.
                            </p>
                        </div>

                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <p class="text-gray-700">
                                <strong>Certificato non ancora disponibile:</strong> Il certificato potrebbe essere in elaborazione e non ancora pubblicato.
                            </p>
                        </div>

                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <p class="text-gray-700">
                                <strong>Errore tecnico temporaneo:</strong> Si è verificato un problema tecnico durante il caricamento. Riprova più tardi.
                            </p>
                        </div>

                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                            <p class="text-gray-700">
                                <strong>Certificato revocato:</strong> Il certificato potrebbe essere stato revocato o non più valido.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 pt-6 border-t border-gray-200">
                    <button onclick="history.back()"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Torna Indietro
                    </button>

                    <button onclick="window.location.reload()"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Riprova
                    </button>
                </div>
            </div>
        </div>

        {{-- Help Section --}}
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-amber-600 mr-3 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-amber-800 mb-2">
                            Hai bisogno di aiuto?
                        </h3>
                        <p class="text-amber-700 mb-2">
                            Se continui ad avere problemi con la verifica del certificato, puoi:
                        </p>
                        <ul class="list-disc list-inside text-amber-700 space-y-1">
                            <li>Verificare che il numero seriale sia stato copiato correttamente</li>
                            <li>Controllare che non ci siano spazi o caratteri extra</li>
                            <li>Contattare il supporto tecnico per assistenza</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Footer --}}
<footer class="bg-gray-800 text-white py-8 mt-16">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-300">
            &copy; {{ date('Y') }} Florence EGI - Sistema di Certificazione Autenticità
        </p>
        <p class="text-gray-400 text-sm mt-2">
            Servizio di verifica certificati digitali per opere d'arte
        </p>
    </div>
</footer>

</body>
</html>
