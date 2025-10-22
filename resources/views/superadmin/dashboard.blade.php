<x-layouts.superadmin pageTitle="Dashboard SuperAdmin">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">
            🌟 SuperAdmin Dashboard
        </h1>
        <p class="mt-2 text-lg text-base-content/70">
            Benvenuto nel pannello di controllo SuperAdmin di FlorenceEGI
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- AI Consultations --}}
        <div class="card bg-gradient-to-br from-yellow-500 to-amber-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Consulenze AI</p>
                        <h3 class="text-3xl font-bold">
                            {{ \App\Models\AiTraitGeneration::count() }}
                        </h3>
                    </div>
                    <div class="text-5xl opacity-50">🤖</div>
                </div>
            </div>
        </div>

        {{-- Total EGIs --}}
        <div class="card bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">EGI Totali</p>
                        <h3 class="text-3xl font-bold">
                            {{ \App\Models\Egi::count() }}
                        </h3>
                    </div>
                    <div class="text-5xl opacity-50">🎨</div>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="card bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Utenti Attivi</p>
                        <h3 class="text-3xl font-bold">
                            {{ \App\Models\User::count() }}
                        </h3>
                    </div>
                    <div class="text-5xl opacity-50">👥</div>
                </div>
            </div>
        </div>

        {{-- Traits Created --}}
        <div class="card bg-gradient-to-br from-purple-500 to-pink-600 text-white shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Traits Creati</p>
                        <h3 class="text-3xl font-bold">
                            {{ \App\Models\EgiTrait::count() }}
                        </h3>
                    </div>
                    <div class="text-5xl opacity-50">✨</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8">
        <h2 class="mb-4 text-2xl font-bold text-base-content">Azioni Rapide</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('superadmin.ai.consultations.index') }}" class="btn btn-primary btn-lg shadow-lg">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M11.25 5.337c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.036 1.007-1.875 2.25-1.875S15 2.34 15 3.375c0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959 0 .332.278.598.61.578 1.91-.114 3.79-.342 5.632-.676a.75.75 0 01.878.645 49.17 49.17 0 01.376 5.452.657.657 0 01-.66.664c-.354 0-.675-.186-.958-.401a1.647 1.647 0 00-1.003-.349c-1.035 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401.31 0 .557.262.534.571a48.774 48.774 0 01-.595 4.845.75.75 0 01-.61.61c-1.82.317-3.673.533-5.555.642a.58.58 0 01-.611-.581c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.035-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959a.641.641 0 01-.658.643 49.118 49.118 0 01-4.708-.36.75.75 0 01-.645-.878c.293-1.614.504-3.257.629-4.924A.53.53 0 005.337 15c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.036 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.369 0 .713.128 1.003.349.283.215.604.401.959.401a.656.656 0 00.659-.663 47.703 47.703 0 00-.31-4.82.75.75 0 01.83-.832c1.343.155 2.703.254 4.077.294a.64.64 0 00.657-.642z" />
                </svg>
                Gestione Consulenze AI
            </a>

            <a href="{{ route('superadmin.ai.credits.index') }}" class="btn btn-accent btn-lg shadow-lg">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z"
                        clip-rule="evenodd" />
                </svg>
                Gestione Crediti AI
            </a>

            <a href="{{ route('superadmin.egili.index') }}" class="btn btn-warning btn-lg shadow-lg">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM9 7.5A.75.75 0 009 9h1.5c.98 0 1.813.626 2.122 1.5H9A.75.75 0 009 12h3.622a2.251 2.251 0 01-2.122 1.5H9a.75.75 0 00-.53 1.28l3 3a.75.75 0 101.06-1.06L10.8 14.988A3.752 3.752 0 0014.175 12H15a.75.75 0 000-1.5h-.825A3.733 3.733 0 0013.5 9.01V8.5a.75.75 0 00-1.5 0v.51A3.752 3.752 0 009 7.5z"
                        clip-rule="evenodd" />
                </svg>
                Gestione Egili
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div>
        <h2 class="mb-4 text-2xl font-bold text-base-content">Attività Recente</h2>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <p class="text-base-content/60">Dashboard in costruzione. Maggiori funzionalità in arrivo!</p>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
