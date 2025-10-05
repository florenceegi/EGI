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

<x-pa-layout title="{{ __('pa_heritage.title') }}">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">{{ __('pa_heritage.title') }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ __('pa_heritage.title') }}</x-slot:pageTitle>

    {{-- Page Header con Stats --}}
    <div class="mb-8 rounded-xl bg-gradient-to-r from-[#1B365D] to-[#0F2342] p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="mb-2 text-2xl font-bold md:text-3xl">{{ __('pa_heritage.title') }}</h1>
                <p class="text-white/80">{{ __('pa_heritage.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $egis->total() }}</p>
                    <p class="mt-1 text-sm uppercase tracking-wide text-white/70">{{ __('pa_heritage.total_items') }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-[#D4A574]">{{ $egis->count() }}</p>
                    <p class="mt-1 text-sm uppercase tracking-wide text-white/70">{{ __('pa_heritage.items_in_page') }}
                    </p>
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
                        {{ __('pa_heritage.filter_search_label') }}
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('pa_heritage.filter_search_placeholder') }}"
                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 outline-none transition-all focus:border-[#D4A574] focus:ring-2 focus:ring-[#D4A574]/20" />
                </div>

                <div class="w-full md:w-64">
                    <label for="coa_status" class="mb-2 block text-sm font-semibold text-[#1B365D]">
                        <span class="material-symbols-outlined align-middle text-base">verified</span>
                        {{ __('pa_heritage.filter_coa_status_label') }}
                    </label>
                    <select id="coa_status" name="coa_status"
                        class="w-full rounded-lg border-2 border-gray-300 px-4 py-2.5 outline-none transition-all focus:border-[#D4A574] focus:ring-2 focus:ring-[#D4A574]/20">
                        <option value="">{{ __('pa_heritage.filter_all_states') }}</option>
                        <option value="valid" {{ request('coa_status') === 'valid' ? 'selected' : '' }}>
                            {{ __('pa_heritage.filter_coa_valid') }}
                        </option>
                        <option value="revoked" {{ request('coa_status') === 'revoked' ? 'selected' : '' }}>
                            {{ __('pa_heritage.filter_coa_revoked') }}</option>
                        <option value="pending" {{ request('coa_status') === 'pending' ? 'selected' : '' }}>
                            {{ __('pa_heritage.filter_coa_pending') }}
                        </option>
                        <option value="no_coa" {{ request('coa_status') === 'no_coa' ? 'selected' : '' }}>
                            {{ __('pa_heritage.filter_no_coa') }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-pa.pa-action-button :label="__('pa_heritage.btn_apply_filters')" type="submit" icon="filter_alt" variant="primary"
                    size="md" />
                @if (request()->hasAny(['search', 'coa_status']))
                    <x-pa.pa-action-button :label="__('pa_heritage.btn_reset_filters')" href="{{ route('pa.heritage.index') }}"
                        icon="filter_alt_off" variant="outline" size="md" />
                @endif
                <div class="sm:ml-auto">
                    <x-pa.pa-action-button :label="__('pa_heritage.btn_new_item')" href="#" icon="add_circle" variant="success"
                        size="md" />
                </div>
            </div>

            @if (request()->hasAny(['search', 'coa_status']))
                <div class="border-t border-gray-200 pt-4">
                    <p class="mb-2 text-sm font-semibold text-gray-600">{{ __('pa_heritage.active_filters') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if (request('search'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#1B365D]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="material-symbols-outlined text-sm">search</span>
                                {{ __('pa_heritage.filter_search_term') }} "{{ request('search') }}"
                            </span>
                        @endif
                        @if (request('coa_status'))
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-[#D4A574]/10 px-3 py-1 text-sm font-medium text-[#1B365D]">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                {{ __('pa_heritage.filter_status_term') }}
                                {{ ucfirst(str_replace('_', ' ', request('coa_status'))) }}
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
                    {{ __('pa_heritage.displayed_items') }} <span
                        class="font-semibold text-[#1B365D]">{{ $egis->firstItem() }}</span>
                    - <span class="font-semibold text-[#1B365D]">{{ $egis->lastItem() }}</span>
                    {{ __('pa_heritage.of_items') }} <span
                        class="font-semibold text-[#1B365D]">{{ $egis->total() }}</span>
                    {{ __('pa_heritage.cultural_assets') }}
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
                <h3 class="mb-3 text-2xl font-bold text-[#1B365D]">{{ __('pa_heritage.no_results_title') }}</h3>
                <p class="mb-6 text-gray-600">{{ __('pa_heritage.no_results_message') }}</p>
                <x-pa.pa-action-button :label="__('pa_heritage.btn_reset_filters')" href="{{ route('pa.heritage.index') }}" icon="filter_alt_off"
                    variant="secondary" size="md" />
            @else
                <h3 class="mb-3 text-2xl font-bold text-[#1B365D]">{{ __('pa_heritage.no_items_cataloged') }}</h3>
                <p class="mb-6 text-gray-600">{{ __('pa_heritage.no_items_loaded') }}</p>
                <div class="flex flex-col justify-center gap-3 sm:flex-row">
                    <x-pa.pa-action-button :label="__('pa_heritage.btn_create_collection')" href="#" icon="create_new_folder" variant="primary"
                        size="lg" />
                    <x-pa.pa-action-button :label="__('pa_heritage.btn_intro_guide')" href="#" icon="help" variant="outline"
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
                <p class="mt-1 text-sm text-gray-600">{{ __('pa_heritage.footer_coa_valid') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#E67E22]">
                    {{ $egis->where(fn($egi) => $egi->coa && $egi->coa->status === 'pending')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">{{ __('pa_heritage.footer_pending') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#C13120]">
                    {{ $egis->where(fn($egi) => $egi->coa && $egi->coa->status === 'revoked')->count() }}</p>
                <p class="mt-1 text-sm text-gray-600">{{ __('pa_heritage.footer_revoked') }}</p>
            </div>
            <div class="rounded-lg bg-white p-4 text-center shadow">
                <p class="text-2xl font-bold text-[#6B6B6B]">{{ $egis->where(fn($egi) => !$egi->coa)->count() }}
                </p>
                <p class="mt-1 text-sm text-gray-600">{{ __('pa_heritage.footer_no_coa') }}</p>
            </div>
        </div>
    @endif

</x-pa-layout>
