<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Statistiche Padmin'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Statistiche Padmin</h1>
        <p class="text-base-content/70">Metriche di utilizzo e qualità (stub).</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Violazioni/100 richieste</h3>
                <p class="text-3xl font-bold">—</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Tempo medio risposta</h3>
                <p class="text-3xl font-bold">—</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Similitudini calcolate</h3>
                <p class="text-3xl font-bold">—</p>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
