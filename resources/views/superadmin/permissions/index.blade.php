<x-layouts.superadmin pageTitle="Permessi & Costi">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">🔐 Permessi & Costi</h1>
        <p class="mt-2 text-lg text-base-content/70">Gestisci i permessi della piattaforma e associa i costi</p>
    </div>

    {{-- Statistics --}}
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Totale Permessi</div>
            <div class="stat-value text-primary">{{ $permissions->total() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Visualizzati</div>
            <div class="stat-value text-info">{{ $permissions->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Con Costo</div>
            <div class="stat-value text-warning">0</div>
            <div class="stat-desc">Feature in arrivo</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Guards</div>
            <div class="stat-value text-success">{{ $permissions->pluck('guard_name')->unique()->count() }}</div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="card mb-6 bg-base-100 shadow-xl">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.permissions.index') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-4">
                {{-- Search --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">🔍 Cerca Permesso</span>
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="es: manage_roles"
                        class="input input-bordered w-full">
                </div>

                {{-- Guard Filter --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">🛡️ Guard</span>
                    </label>
                    <select name="guard" class="select select-bordered w-full">
                        <option value="">Tutti</option>
                        @foreach ($permissions->pluck('guard_name')->unique() as $guard)
                            <option value="{{ $guard }}" {{ request('guard') === $guard ? 'selected' : '' }}>
                                {{ $guard }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Module Filter (by prefix) --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">📦 Modulo</span>
                    </label>
                    <select name="module" class="select select-bordered w-full">
                        <option value="">Tutti</option>
                        <option value="manage_" {{ request('module') === 'manage_' ? 'selected' : '' }}>Manage (Admin)
                        </option>
                        <option value="view_" {{ request('module') === 'view_' ? 'selected' : '' }}>View</option>
                        <option value="create_" {{ request('module') === 'create_' ? 'selected' : '' }}>Create</option>
                        <option value="edit_" {{ request('module') === 'edit_' ? 'selected' : '' }}>Edit</option>
                        <option value="delete_" {{ request('module') === 'delete_' ? 'selected' : '' }}>Delete</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">&nbsp;</span>
                    </label>
                    <div class="join w-full">
                        <button type="submit" class="btn btn-primary join-item flex-1">
                            Applica Filtri
                        </button>
                        <a href="{{ route('superadmin.permissions.index') }}" class="btn btn-ghost join-item">
                            ↻
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions Form --}}
    <form id="bulkActionsForm" method="POST" action="{{ route('superadmin.permissions.bulk-update') }}">
        @csrf

        {{-- Permissions Table --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="card-title">Elenco Permessi</h2>

                    {{-- Bulk Actions --}}
                    <div class="join">
                        <button type="button" onclick="selectAll()" class="btn btn-ghost join-item btn-sm">
                            ☑️ Tutti
                        </button>
                        <button type="button" onclick="deselectAll()" class="btn btn-ghost join-item btn-sm">
                            ☐ Nessuno
                        </button>
                        <button type="submit" class="btn btn-primary join-item btn-sm" disabled id="bulkSubmitBtn">
                            💾 Salva Modifiche (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="w-12">
                                    <input type="checkbox" class="checkbox checkbox-sm" onclick="toggleAll(this)">
                                </th>
                                <th>Nome Permesso</th>
                                <th class="text-center">Guard</th>
                                <th class="text-center">Costo Egili</th>
                                <th class="text-center">Costo EUR</th>
                                <th class="text-center">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="permissions[{{ $permission->id }}][selected]"
                                            value="1" class="permission-checkbox checkbox checkbox-sm"
                                            onchange="updateSelectedCount()">
                                    </td>
                                    <td class="font-semibold">
                                        <code
                                            class="rounded bg-base-200 px-2 py-1 text-sm">{{ $permission->name }}</code>
                                        <input type="hidden" name="permissions[{{ $permission->id }}][name]"
                                            value="{{ $permission->name }}">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-ghost">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.01" min="0"
                                            name="permissions[{{ $permission->id }}][cost_egili]" placeholder="0.00"
                                            class="input input-sm input-bordered w-28 text-center">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.01" min="0"
                                            name="permissions[{{ $permission->id }}][cost_fiat_eur]"
                                            placeholder="0.00" class="input input-sm input-bordered w-28 text-center">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" name="permissions[{{ $permission->id }}][notes]"
                                            placeholder="Note..." class="input input-sm input-bordered w-40">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-base-content/50">
                                        Nessun permesso trovato. Prova a modificare i filtri.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </form>

    {{-- Info Box --}}
    <div class="alert alert-info mt-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            class="h-6 w-6 shrink-0 stroke-current">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">💡 Come Usare</h3>
            <div class="text-sm">
                <ol class="ml-4 mt-2 list-decimal space-y-1">
                    <li>Usa i <strong>filtri</strong> per trovare i permessi che vuoi configurare</li>
                    <li>Seleziona le checkbox dei permessi da modificare</li>
                    <li>Inserisci i <strong>costi in Egili e/o EUR</strong> direttamente nella tabella</li>
                    <li>Clicca <strong>"Salva Modifiche"</strong> per applicare in batch</li>
                </ol>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAll(source) {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = source.checked;
                });
                updateSelectedCount();
            }

            function selectAll() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = true);
                updateSelectedCount();
            }

            function deselectAll() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = false);
                updateSelectedCount();
            }

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.permission-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = selected;
                document.getElementById('bulkSubmitBtn').disabled = selected === 0;
            }
        </script>
    @endpush
</x-layouts.superadmin>
