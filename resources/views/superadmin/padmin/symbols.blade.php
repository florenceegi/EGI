<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Simboli Padmin'">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Registro simboli</h1>
        <a href="{{ route('superadmin.padmin.search') }}" class="btn btn-outline">Ricerca semantica</a>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <p class="text-base-content/70">Elenco simboli (mock). Integrare sorgente da Redis Stack (RediSearch) in FASE
                2.</p>
        </div>
    </div>
</x-layouts.superadmin>
