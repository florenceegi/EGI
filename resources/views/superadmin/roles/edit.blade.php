<x-layouts.superadmin pageTitle="Modifica Ruolo: {{ $role->name }}">
    {{-- Header --}}
    <div class="mb-8">
        <div class="mb-4 flex items-center gap-4">
            <a href="{{ route('superadmin.roles.index') }}" class="btn btn-ghost btn-sm">
                ← Torna ai Ruoli
            </a>
        </div>
        <h1 class="text-4xl font-bold text-base-content">✏️ Modifica Ruolo: {{ $role->name }}</h1>
        <p class="mt-2 text-lg text-base-content/70">Configura nome e permessi per questo ruolo</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('superadmin.roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left Column: Role Info --}}
            <div class="lg:col-span-1">
                <div class="card sticky top-4 bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">📝 Informazioni Ruolo</h2>

                        {{-- Role Name --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Nome Ruolo *</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                class="@error('name') input-error @enderror input input-bordered" required
                                placeholder="es: content-manager">
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        {{-- Description (TODO: add to DB) --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Descrizione</span>
                            </label>
                            <textarea name="description" rows="3" class="textarea textarea-bordered"
                                placeholder="Breve descrizione del ruolo...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Stats --}}
                        <div class="divider"></div>
                        <div class="stats stats-vertical shadow">
                            <div class="stat">
                                <div class="stat-title">Permessi Attuali</div>
                                <div class="stat-value text-primary" id="selectedCount">
                                    {{ $role->permissions->count() }}
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-title">Utenti Assegnati</div>
                                <div class="stat-value text-success">{{ $role->users_count }}</div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="divider"></div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" class="btn btn-primary">
                                💾 Salva Modifiche
                            </button>
                            <a href="{{ route('superadmin.roles.index') }}" class="btn btn-ghost">
                                Annulla
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Permissions --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="card-title">🔑 Seleziona Permessi</h2>
                            <div class="join">
                                <button type="button" onclick="selectAll()" class="btn btn-ghost join-item btn-sm">
                                    ☑️ Tutti
                                </button>
                                <button type="button" onclick="deselectAll()" class="btn btn-ghost join-item btn-sm">
                                    ☐ Nessuno
                                </button>
                            </div>
                        </div>

                        @error('permissions')
                            <div class="alert alert-error mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        {{-- Permissions Grouped by Module --}}
                        <div class="space-y-6">
                            @foreach ($groupedPermissions as $module => $permissions)
                                <div class="collapse collapse-arrow bg-base-200">
                                    <input type="checkbox" checked />
                                    <div class="collapse-title flex items-center gap-2 text-lg font-semibold">
                                        <span class="badge badge-primary">{{ count($permissions) }}</span>
                                        📦 {{ ucfirst($module) }}
                                    </div>
                                    <div class="collapse-content">
                                        <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-2">
                                            @foreach ($permissions as $permission)
                                                <label
                                                    class="label cursor-pointer justify-start gap-3 rounded p-3 hover:bg-base-300">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        class="permission-checkbox checkbox-primary checkbox"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                        onchange="updateCount()">
                                                    <span class="label-text">{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            function updateCount() {
                const selected = document.querySelectorAll('.permission-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = selected;
            }

            function selectAll() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateCount();
            }

            function deselectAll() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateCount();
            }
        </script>
    @endpush
</x-layouts.superadmin>









