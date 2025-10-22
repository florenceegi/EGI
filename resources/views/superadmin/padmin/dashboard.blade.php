<x-layouts.superadmin :pageTitle="$pageTitle ?? 'Padmin Analyzer'">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-base-content">🧠 Padmin Analyzer</h1>
        <p class="mt-2 text-base text-base-content/70">Monitoraggio regole OS3, simboli e violazioni.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="card bg-gradient-to-br from-amber-500 to-yellow-600 text-white shadow-xl">
            <div class="card-body">
                <p class="text-sm opacity-80">Simboli Indicizzati</p>
                <h3 class="text-3xl font-bold">—</h3>
            </div>
        </div>
        <div class="card bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-xl">
            <div class="card-body">
                <p class="text-sm opacity-80">Violazioni Ultimi 7gg</p>
                <h3 class="text-3xl font-bold">—</h3>
            </div>
        </div>
        <div class="card bg-gradient-to-br from-indigo-500 to-blue-600 text-white shadow-xl">
            <div class="card-body">
                <p class="text-sm opacity-80">Query Semantiche</p>
                <h3 class="text-3xl font-bold">—</h3>
            </div>
        </div>
        <div class="card bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-xl">
            <div class="card-body">
                <p class="text-sm opacity-80">Indice Salute</p>
                <h3 class="text-3xl font-bold">—</h3>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <a href="{{ route('superadmin.padmin.violations') }}"
            class="card bg-base-100 shadow-xl transition hover:shadow-2xl">
            <div class="card-body">
                <h2 class="card-title">Violazioni recenti</h2>
                <p class="text-base-content/70">Analizza le ultime violazioni delle regole OS3.</p>
            </div>
        </a>
        <a href="{{ route('superadmin.padmin.symbols') }}"
            class="card bg-base-100 shadow-xl transition hover:shadow-2xl">
            <div class="card-body">
                <h2 class="card-title">Registro simboli</h2>
                <p class="text-base-content/70">Esplora simboli e metadati indicizzati.</p>
            </div>
        </a>
    </div>
</x-layouts.superadmin>
