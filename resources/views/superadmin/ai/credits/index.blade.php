<x-enterprise-sidebar logo="FlorenceEGI" badge="SuperAdmin" theme="superadmin">
    <div class="p-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                💳 Gestione Crediti AI
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Monitora il consumo e la distribuzione dei crediti AI
            </p>
        </div>

        {{-- Stats Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Crediti Totali Utilizzati</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">12,458</div>
                <div class="mt-1 text-xs text-gray-500">~€249.16</div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Questo Mese</div>
                <div class="mt-2 text-3xl font-bold text-blue-600">3,721</div>
                <div class="mt-1 text-xs text-gray-500">~€74.42</div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Media per Consulenza</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">284</div>
                <div class="mt-1 text-xs text-gray-500">crediti</div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Utenti Attivi</div>
                <div class="mt-2 text-3xl font-bold text-green-600">47</div>
                <div class="mt-1 text-xs text-gray-500">ultimi 30 giorni</div>
            </div>
        </div>

        {{-- Coming Soon Message --}}
        <div class="rounded-lg bg-gradient-to-r from-yellow-50 to-amber-50 p-8 text-center">
            <div class="mb-4 text-6xl">🚧</div>
            <h2 class="mb-2 text-2xl font-bold text-gray-900">Funzionalità in Sviluppo</h2>
            <p class="text-gray-600">
                La gestione avanzata dei crediti AI sarà disponibile a breve.
                Include analitiche dettagliate, billing, e allocazione per utente.
            </p>
        </div>
    </div>
</x-enterprise-sidebar>
