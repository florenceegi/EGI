<x-layouts.superadmin pageTitle="Crediti AI">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">Gestione Crediti AI</h1>
        <p class="mt-2 text-lg text-base-content/70">Monitora e gestisci i crediti AI degli utenti</p>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Credits Distributed --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-base-content/60">Crediti Distribuiti</p>
                        <h3 class="text-3xl font-bold text-base-content">
                            {{ number_format($totalCreditsDistributed ?? 0) }}</h3>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>
        </div>

        {{-- Active Users with Credits --}}
        <div class="card bg-success/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-success">Utenti Attivi</p>
                        <h3 class="text-3xl font-bold text-success">{{ $activeUsersWithCredits ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">👥</div>
                </div>
            </div>
        </div>

        {{-- Premium Subscribers --}}
        <div class="card bg-warning/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-warning">Abbonati Premium</p>
                        <h3 class="text-3xl font-bold text-warning">{{ $premiumSubscribers ?? 0 }}</h3>
                    </div>
                    <div class="text-4xl">⭐</div>
                </div>
            </div>
        </div>

        {{-- Revenue (Month) --}}
        <div class="card bg-info/10 shadow-xl">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-info">Revenue (Mese)</p>
                        <h3 class="text-3xl font-bold text-info">€{{ number_format($monthlyRevenue ?? 0, 2) }}</h3>
                    </div>
                    <div class="text-4xl">📈</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8">
        <h2 class="mb-4 text-2xl font-bold text-base-content">Azioni Rapide</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <button class="btn btn-primary btn-lg" onclick="assignCreditsModal.showModal()">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z"
                        clip-rule="evenodd" />
                </svg>
                Assegna Crediti
            </button>

            <a href="{{ route('superadmin.ai.credits.transactions') }}" class="btn btn-accent btn-lg">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zm6 16.5c.66 0 1.277-.19 1.797-.518l1.048 1.048a.75.75 0 001.06-1.06l-1.047-1.048A3.375 3.375 0 1011.625 18z"
                        clip-rule="evenodd" />
                </svg>
                Vedi Transazioni
            </a>

            <a href="{{ route('superadmin.ai.credits.packages') }}" class="btn btn-info btn-lg">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z"
                        clip-rule="evenodd" />
                </svg>
                Gestisci Pacchetti
            </a>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="mb-4 text-lg font-semibold">Transazioni Recenti</h3>
            <div class="py-8 text-center text-base-content/60">
                <p>Funzionalità in costruzione. Maggiori dettagli disponibili a breve!</p>
            </div>
        </div>
    </div>

    {{-- Assign Credits Modal --}}
    <dialog id="assignCreditsModal" class="modal">
        <div class="modal-box">
            <h3 class="mb-4 text-lg font-bold">Assegna Crediti</h3>
            <form method="POST" action="{{ route('superadmin.ai.credits.assign') }}">
                @csrf
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Utente (ID o Email)</span></label>
                    <input type="text" name="user_identifier" class="input input-bordered" required />
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Crediti</span></label>
                    <input type="number" name="credits" class="input input-bordered" min="1" required />
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Motivo</span></label>
                    <textarea name="reason" class="textarea textarea-bordered" rows="3"></textarea>
                </div>

                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">Assegna</button>
                    <button type="button" class="btn" onclick="assignCreditsModal.close()">Annulla</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>Chiudi</button>
        </form>
    </dialog>
</x-layouts.superadmin>
