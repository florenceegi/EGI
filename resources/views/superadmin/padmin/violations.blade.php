<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Violazioni Padmin'">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Violazioni recenti</h1>
        <a href="{{ route('superadmin.padmin.statistics') }}" class="btn btn-outline">Statistiche</a>
    </div>

    <div class="alert alert-info mb-4">
        Stream in lettura dalle chiavi Redis OS3 Guardian (mock UI). Integrare datasource in FASE 2.
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <p class="text-base-content/70">Nessun dato caricato. Collegare il microservizio OS3 Guardian per
                popolamento.</p>
        </div>
    </div>
</x-layouts.superadmin>
