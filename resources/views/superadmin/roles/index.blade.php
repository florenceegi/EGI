<x-layouts.superadmin pageTitle="Ruoli & Permessi">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-base-content">🔐 Gestione Ruoli & Permessi</h1>
            <p class="mt-2 text-lg text-base-content/70">Centro di controllo Enterprise per RBAC (Role-Based Access
                Control)</p>
        </div>
        <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Crea Nuovo Ruolo
        </a>
    </div>

    {{-- Statistics --}}
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Totale Ruoli</div>
            <div class="stat-value text-primary">{{ $roles->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Ruoli Attivi</div>
            <div class="stat-value text-success">{{ $roles->where('users_count', '>', 0)->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Totale Permessi</div>
            <div class="stat-value text-info">{{ $roles->sum('permissions_count') }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Ruoli Sistema</div>
            <div class="stat-value text-warning">
                {{ $roles->whereIn('name', ['superadmin', 'admin'])->count() }}
            </div>
        </div>
    </div>

    {{-- Roles Cards Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($roles as $role)
            <div class="card bg-base-100 shadow-xl transition-shadow hover:shadow-2xl">
                <div class="card-body">
                    {{-- Card Header --}}
                    <div class="flex items-start justify-between">
                        <h2 class="card-title text-2xl">
                            @if (in_array($role->name, ['superadmin', 'admin']))
                                <span class="text-error">🛡️</span>
                            @else
                                <span class="text-primary">👥</span>
                            @endif
                            {{ ucfirst($role->name) }}
                        </h2>

                        @if (in_array($role->name, ['superadmin', 'admin']))
                            <span class="badge badge-error badge-sm">Sistema</span>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div class="mt-4 flex gap-4">
                        <div class="stat flex-1 rounded bg-base-200 p-3">
                            <div class="stat-title text-xs">Permessi</div>
                            <div class="stat-value text-2xl text-primary">{{ $role->permissions_count }}</div>
                        </div>
                        <div class="stat flex-1 rounded bg-base-200 p-3">
                            <div class="stat-title text-xs">Utenti</div>
                            <div class="stat-value text-2xl text-success">{{ $role->users_count }}</div>
                        </div>
                    </div>

                    {{-- Permissions Preview --}}
                    <div class="mt-4">
                        <p class="mb-2 text-sm font-semibold">Permessi principali:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($role->permissions->take(5) as $permission)
                                <span class="badge badge-ghost badge-sm">{{ $permission->name }}</span>
                            @endforeach
                            @if ($role->permissions_count > 5)
                                <span class="badge badge-info badge-sm">+{{ $role->permissions_count - 5 }} altri</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card-actions mt-4 justify-end">
                        <a href="{{ route('superadmin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                            ✏️ Modifica
                        </a>

                        @if (!in_array($role->name, ['superadmin', 'admin']))
                            <form action="{{ route('superadmin.roles.destroy', $role) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-role btn btn-error btn-sm">
                                    🗑️ Elimina
                                </button>
                            </form>
                        @else
                            <button class="btn btn-ghost btn-sm" disabled title="Ruolo di sistema protetto">
                                🔒 Protetto
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="py-12 text-center">
                            <div class="mb-6 text-6xl">🔐</div>
                            <h2 class="mb-4 text-2xl font-bold text-base-content">Nessun Ruolo Configurato</h2>
                            <p class="text-base-content/70">
                                Clicca su "Crea Nuovo Ruolo" per iniziare a configurare i ruoli della piattaforma.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Info Box --}}
    <div class="alert alert-info mt-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            class="h-6 w-6 shrink-0 stroke-current">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="font-bold">💡 Come Funziona</h3>
            <div class="text-sm">
                <ol class="ml-4 mt-2 list-decimal space-y-1">
                    <li><strong>Crea un Ruolo</strong> → Definisci il nome e seleziona i permessi</li>
                    <li><strong>Assegna Permessi</strong> → Scegli quali funzionalità il ruolo può usare</li>
                    <li><strong>Assegna agli Utenti</strong> → Gli utenti erediteranno tutti i permessi del ruolo</li>
                    <li><strong>Configura Pricing</strong> → Associa costi ai tier/abbonamenti (TODO)</li>
                </ol>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // SweetAlert for delete confirmation
            document.querySelectorAll('.btn-delete-role').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Sei sicuro?',
                        text: "Questa operazione è irreversibile! Il ruolo verrà eliminato definitivamente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sì, elimina!',
                        cancelButtonText: 'Annulla'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.superadmin>
