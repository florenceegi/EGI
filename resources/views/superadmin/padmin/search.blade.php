<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Ricerca Semantica'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Ricerca Semantica</h1>
        <p class="text-base-content/70">UI minimale per interrogare OS3 Guardian (mock).</p>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <label class="form-control">
                <div class="label"><span class="label-text">Query</span></div>
                <input type="text" class="input input-bordered w-full" placeholder="Es. 'hasConsent method'" />
            </label>
            <button class="btn btn-primary">Cerca</button>
            <div class="divider"></div>
            <div class="prose max-w-none">
                <p>Nessun risultato. Collegare backend Node/Redis per risultati reali.</p>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
