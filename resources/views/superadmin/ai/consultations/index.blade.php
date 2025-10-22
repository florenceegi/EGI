<x-layouts.superadmin pageTitle="Consulenze AI">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">Consulenze AI</h1>
        <p class="mt-2 text-lg text-base-content/70">Monitora e gestisci tutte le generazioni AI della piattaforma</p>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Generations --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-base-content/60">Generazioni Totali</p>
                        <h3 class="text-3xl font-bold text-base-content">{{ $totalGenerations ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">🤖</div>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="card bg-warning/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-warning">In Attesa</p>
                        <h3 class="text-3xl font-bold text-warning">{{ $pendingGenerations ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">⏳</div>
                </div>
            </div>
        </div>

        {{-- Completed --}}
        <div class="card bg-success/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-success">Completate</p>
                        <h3 class="text-3xl font-bold text-success">{{ $completedGenerations ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
            </div>
        </div>

        {{-- Failed --}}
        <div class="card bg-error/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-error">Fallite</p>
                        <h3 class="text-3xl font-bold text-error">{{ $failedGenerations ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">❌</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6 bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="mb-4 text-lg font-semibold">Filtri</h3>
            <form method="GET" action="{{ route('superadmin.ai.consultations.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Status</span></label>
                    <select name="status" class="select select-bordered">
                        <option value="">Tutti</option>
                        <option value="pending">In Attesa</option>
                        <option value="processing">In Elaborazione</option>
                        <option value="completed">Completate</option>
                        <option value="failed">Fallite</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Feature</span></label>
                    <select name="feature" class="select select-bordered">
                        <option value="">Tutte</option>
                        <option value="traits">Traits</option>
                        <option value="analysis">Analysis</option>
                        <option value="pricing">Pricing</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Data Da</span></label>
                    <input type="date" name="date_from" class="input input-bordered" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Data A</span></label>
                    <input type="date" name="date_to" class="input input-bordered" />
                </div>

                <div class="col-span-full">
                    <button type="submit" class="btn btn-primary">Applica Filtri</button>
                    <a href="{{ route('superadmin.ai.consultations.index') }}" class="btn btn-ghost">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Consultations Table --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="mb-4 text-lg font-semibold">Elenco Consulenze</h3>

            @if (isset($generations) && $generations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utente</th>
                                <th>EGI</th>
                                <th>Feature</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($generations as $generation)
                                <tr>
                                    <td>{{ $generation->id }}</td>
                                    <td>{{ $generation->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('egi.dual-arch.show', $generation->egi_id) }}"
                                            class="link link-primary" target="_blank">
                                            EGI #{{ $generation->egi_id }}
                                        </a>
                                    </td>
                                    <td><span
                                            class="badge badge-outline">{{ ucfirst($generation->feature_type ?? 'N/A') }}</span>
                                    </td>
                                    <td>
                                        @switch($generation->status)
                                            @case('completed')
                                                <span class="badge badge-success">Completata</span>
                                            @break

                                            @case('pending')
                                                <span class="badge badge-warning">In Attesa</span>
                                            @break

                                            @case('processing')
                                                <span class="badge badge-info">In Elaborazione</span>
                                            @break

                                            @case('failed')
                                                <span class="badge badge-error">Fallita</span>
                                            @break

                                            @default
                                                <span class="badge">{{ $generation->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $generation->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('superadmin.ai.consultations.show', $generation->id) }}"
                                            class="btn btn-ghost btn-xs">
                                            Dettagli
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $generations->links() }}
                </div>
            @else
                <div class="py-8 text-center text-base-content/60">
                    <p>Nessuna consulenza trovata.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.superadmin>
