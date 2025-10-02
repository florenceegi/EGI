{{--
/**
 * PA Heritage List View
 *
 * @package Resources\Views\PA\Heritage
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Enterprise MVP)
 * @date 2025-10-02
 * @purpose Lista patrimonio culturale PA con filtri, grid cards, pagination
 */
--}}

<x-pa-layout title="Patrimonio Culturale">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Patrimonio Culturale</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>Patrimonio Culturale</x-slot:pageTitle>

    {{-- Page Header con Stats --}}
    <div class="mb-8 rounded-xl bg-gradient-to-r from-[#1B365D] to-[#0F2342] p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="mb-2 text-2xl font-bold md:text-3xl">Patrimonio Culturale</h1>
                <p class="text-white/80">Gestisci e monitora i beni culturali certificati del tuo ente</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $heritage->total() }}</p>
                    <p class="mt-1 text-sm tracking-wide uppercase text-white/70">Beni Totali</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $heritage->count() }}</p>
                    <p class="mt-1 text-sm tracking-wide uppercase text-white/70">In Questa Pagina</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="p-6 mb-8 bg-white shadow-md rounded-xl">
        <form method="GET" action="{{ route('pa.heritage.index') }}" class="space-y-4">
            <div class="flex flex-col gap-4 md:flex-row">
                <div class="flex-1">
                    <label for="search" class="mb-2 block text-sm font-semibold text-[#1B365D]">
                        <span class="text-base align-middle material-symbols-outlined">search</span>
                        Cerca per Titolo, Artista o Descrizione
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Inserisci il termine di ricerca..."
                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 outline-none transition-all focus:border-[#D4A574] focus:ring-2 focus:ring-[#D4A574]/20" />
                </div>

                <div class="w-full md:w-64">
                    <label for="coa_status" class="mb-2 block text-sm font-semibold text-[#1B365D]">
                        <span class="text-base align-middle material-symbols-outlined">verified</span>
                        Stato CoA
                    </label>
                    <select id="coa_status" name="coa_status"
                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 outline-none transition-all focus:border-[#D4A574] focus:ring-2 focus:ring-[#D4A574]/20">
                        <option value="">Tutti gli stati</option>
                        <option value="valid" {{ request('coa_status') === 'valid' ? 'selected' : '' }}>✓ CoA Valido
                        </option>
                        <option value="revoked" {{ request('coa_status') === 'revoked' ? 'selected' : '' }}>✗ CoA
                            Revocato</option>
                        <option value="pending" {{ request('coa_status') === 'pending' ? 'selected' : '' }}>⏳ In Attesa
                        </option>
                        <option value="no_coa" {{ request('coa_status') === 'no_coa' ? 'selected' : '' }}>− Nessun CoA
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-pa.pa-action-button label="Applica Filtri" type="submit" icon="filter_alt" variant="primary"
                    size="md" />
                @if (request()->hasAny(['search', 'coa_status']))
                    <x-pa.pa-action-button label="Azzera Filtri" href="{{ route('pa.heritage.index') }}"
                        icon="filter_alt_off" variant="outline" size="md" />
                @endif
                <div class="sm:ml-auto">
                    <x-pa.pa-action-button label="Nuovo Bene" href="#" icon="add_circle" variant="success"
                        size="md" />
                </div>
            </div>

            @if (request()->hasAny(['search', 'coa_status']))
                <div class="pt-4 border-t border-gray-200">
                    <p class="mb-2 text-sm font-semibold text-gray-600">Filtri Attivi:</p>
                    <div class="flex flex-wrap gap-2">
                        @if (request('search'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#1B365D]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="text-sm material-symbols-outlined">search</span>
                                Ricerca: "{{ request('search') }}"
                            </span>
                        @endif
                        @if (request('coa_status'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#D4A574]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="text-sm material-symbols-outlined">verified</span>
                                Stato: {{ ucfirst(str_replace('_', ' ', request('coa_status'))) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </form>
    </div>

    {{-- Heritage Grid --}}
    @if ($heritage->count() > 0)
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($heritage as $item)
                <x-pa.pa-heritage-card :egi="$item" :showCoa="true" layout="grid" :showActions="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="p-6 bg-white shadow-md rounded-xl">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-sm text-gray-600">
                    Visualizzati <span class="font-semibold text-[#1B365D]">{{ $heritage->firstItem() }}</span>
                    - <span class="font-semibold text-[#1B365D]">{{ $heritage->lastItem() }}</span>
                    di <span class="font-semibold text-[#1B365D]">{{ $heritage->total() }}</span> beni culturali
                </div>
                <div>
                    {{ $heritage->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="p-12 text-center bg-white shadow-md rounded-xl">
            <span class="block mb-4 text-gray-300 material-symbols-outlined text-8xl">inventory_2</span>
            @if (request()->hasAny(['search', 'coa_status']))
                <h3 class="mb-3 text-2xl font-bold text-[#1B365D]">Nessun risultato trovato</h3>
                <p class="mb-6 text-gray-600">Non ci sono beni culturali che corrispondono ai filtri applicati. Prova a
                    modificare i criteri di ricerca.</p>
                <x-pa.pa-action-button label="Azzera Filtri" href="{{ route('pa.heritage.index') }}"
                    icon="filter_alt_off" variant="secondary" size="md" />
            @else
                <h3 class="mb-3 text-2xl font-bold text-[#1B365D]">Nessun bene culturale catalogato</h3>
                <p class="mb-6 text-gray-600">Il tuo ente non ha ancora caricato beni culturali nel sistema. Inizia
                    creando la tua prima collezione di patrimonio.</p>
                <div class="flex flex-col justify-center gap-3 sm:flex-row">
                    <x-pa.pa-action-button label="Crea Collezione" href="#" icon="create_new_folder"
                        variant="primary" size="lg" />
                    <x-pa.pa-action-button label="Guida Introduttiva" href="#" icon="help" variant="outline"
                        size="lg" />
                </div>
            @endif
        </div>
    @endif

    {{-- Quick Stats Footer --}}
    @if ($heritage->total() > 0)
        <div class="grid grid-cols-1 gap-4 mt-8 md:grid-cols-4">
            <div class="p-4 text-center bg-white rounded-lg shadow">
                <p class="text-2xl font-bold text-[#2D5016]">
                    {{ $heritage->where(fn($egi) => $egi->coa && $egi->coa->status === 'valid')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">CoA Validi</p>
            </div>
            <div class="p-4 text-center bg-white rounded-lg shadow">
                <p class="text-2xl font-bold text-[#E67E22]">
                    {{ $heritage->where(fn($egi) => $egi->coa && $egi->coa->status === 'pending')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">In Attesa</p>
            </div>
            <div class="p-4 text-center bg-white rounded-lg shadow">
                <p class="text-2xl font-bold text-[#C13120]">
                    {{ $heritage->where(fn($egi) => $egi->coa && $egi->coa->status === 'revoked')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">Revocati</p>
            </div>
            <div class="p-4 text-center bg-white rounded-lg shadow">
                <p class="text-2xl font-bold text-[#6B6B6B]">{{ $heritage->where(fn($egi) => !$egi->coa)->count() }}
                </p>
                <p class="mt-1 text-sm text-gray-600">Senza CoA</p>
            </div>
        </div>
    @endif

</x-pa-layout>

