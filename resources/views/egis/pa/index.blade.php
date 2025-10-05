{{--
/**
 * PA Heritage List View (Universal EGI Architecture)
 *
 * @package Resources\Views\Egis\PA
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - PA Enterprise Architecture STEP 2.2)
 * @date 2025-10-04
 * @purpose Lista patrimonio culturale PA con filtri, grid cards, pagination
 *
 * Architecture:
 * - Migrated from: resources/views/pa/heritage/index.blade.php
 * - Routed by: ViewService (egis.pa.index)
 * - Controller: EgiController@index (universal) or PAHeritageController@index (deprecated)
 * - Service: EgiService->index() with PA filters
 *
 * Changes from v1.0:
 * - Moved to egis/pa/ structure for universal architecture
 * - Backward compatibility maintained via PAHeritageController routes
 * - Uses ViewService for role-based routing
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
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $egis->total() }}</p>
                    <p class="mt-1 text-sm uppercase tracking-wide text-white/70">Beni Totali</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $egis->count() }}</p>
                    <p class="mt-1 text-sm uppercase tracking-wide text-white/70">In Questa Pagina</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="mb-8 rounded-xl bg-white p-6 shadow-md">
        <form method="GET" action="{{ route('pa.heritage.index') }}" class="space-y-4">
            <div class="flex flex-col gap-4 md:flex-row">
                <div class="flex-1">
                    <label for="search" class="mb-2 block text-sm font-semibold text-[#1B365D]">
                        <span class="material-symbols-outlined align-middle text-base">search</span>
                        Cerca per Titolo, Artista o Descrizione
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Inserisci il termine di ricerca..."
                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 outline-none transition-all focus:border-[#D4A574] focus:ring-2 focus:ring-[#D4A574]/20" />
                </div>

                <div class="w-full md:w-64">
                    <label for="coa_status" class="mb-2 block text-sm font-semibold text-[#1B365D]">
                        <span class="material-symbols-outlined align-middle text-base">verified</span>
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
                <div class="border-t border-gray-200 pt-4">
                    <p class="mb-2 text-sm font-semibold text-gray-600">Filtri Attivi:</p>
                    <div class="flex flex-wrap gap-2">
                        @if (request('search'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#1B365D]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="material-symbols-outlined text-sm">search</span>
                                Ricerca: "{{ request('search') }}"
                            </span>
                        @endif
                        @if (request('coa_status'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#D4A574]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                Stato: {{ ucfirst(str_replace('_', ' ', request('coa_status'))) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </form>
    </div>

    {{-- Heritage Grid --}}
    @if ($egis->count() > 0)
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($egis as $item)
                <x-pa.pa-heritage-card :egi="$item" :showCoa="true" layout="grid" :showActions="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="rounded-xl bg-white p-6 shadow-md">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-sm text-gray-600">
                    Visualizzati <span class="font-semibold text-[#1B365D]">{{ $egis->firstItem() }}</span>
                    - <span class="font-semibold text-[#1B365D]">{{ $egis->lastItem() }}</span>
                    di <span class="font-semibold text-[#1B365D]">{{ $egis->total() }}</span> beni culturali
                </div>
                <div>
                    {{ $egis->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-xl bg-white p-12 text-center shadow-md">
            <span class="material-symbols-outlined mb-4 block text-8xl text-gray-300">inventory_2</span>
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
    @if ($egis->total() > 0)
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#2D5016]">
                    {{ $egis->where(fn($egi) => $egi->coa && $egi->coa->status === 'valid')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">CoA Validi</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#E67E22]">
                    {{ $egis->where(fn($egi) => $egi->coa && $egi->coa->status === 'pending')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">In Attesa</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#C13120]">
                    {{ $egis->where(fn($egi) => $egi->coa && $egi->coa->status === 'revoked')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">Revocati</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#6B6B6B]">{{ $egis->where(fn($egi) => !$egi->coa)->count() }}
                </p>
                <p class="mt-1 text-sm text-gray-600">Senza CoA</p>
            </div>
        </div>
    @endif

</x-pa-layout>
