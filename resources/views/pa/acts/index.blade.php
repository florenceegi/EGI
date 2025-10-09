{{--
/**
 * PA Acts Index View
 *
 * ============================================================================
 * CONTESTO - LISTA ATTI PA TOKENIZZATI
 * ============================================================================
 *
 * View per la lista degli atti PA tokenizzati su blockchain.
 *
 * TARGET USER: PA entities (autenticate, role:pa_entity)
 * ACCESS: Authenticated only (middleware: auth, role:pa_entity)
 *
 * PURPOSE:
 * - Overview atti tokenizzati da PA entity
 * - Ricerca per protocollo, titolo
 * - Filtri: Tipo atto, Date range, Stato ancoraggio
 * - Stats: Total, Anchored, Pending
 *
 * ============================================================================
 * FEATURES
 * ============================================================================
 *
 * STATS CARDS:
 * - Total atti tokenizzati
 * - Atti ancorati su blockchain
 * - Atti in attesa di ancoraggio
 *
 * FILTERS:
 * - Search: Protocol number, Titolo (full-text)
 * - Doc type: Delibera, Determina, Ordinanza, Decreto, Atto
 * - Date range: Protocol date FROM → TO
 * - Status: All / Anchored / Pending
 *
 * TABLE COLUMNS:
 * - Protocol number + date
 * - Title (truncated)
 * - Doc type badge
 * - Anchor status (✅ Anchored / ⏳ Pending)
 * - Actions: View detail, QR code
 *
 * ============================================================================
 * PA BRAND DESIGN
 * ============================================================================
 *
 * COLORS:
 * - Primary: #1B365D (Blu Algoritmo - trust, blockchain)
 * - Accent: #D4A574 (Oro Fiorentino - premium, CTA)
 * - Success: #2D5016 (Verde Rinascita - confirmed, anchored)
 * - Warning: #E67E22 (Arancio Energia - pending status)
 *
 * TYPOGRAPHY:
 * - Headers: font-serif (Playfair Display fallback)
 * - Body: font-sans (Source Sans Pro fallback)
 *
 * LAYOUT:
 * - Spazi bianchi generosi (proporzioni auree)
 * - Cards con ombre leggere
 * - Bordi sottili eleganti
 *
 * ============================================================================
 * ACCESSIBILITY
 * ============================================================================
 *
 * WCAG 2.1 AA:
 * - Contrast ratios: Primary (#1B365D) on white = 11.4:1 ✅
 * - ARIA labels: All interactive elements
 * - Keyboard navigation: Tab order logical
 * - Screen reader: Descriptive text for badges/icons
 *
 * ============================================================================
 *
 * @package Resources\Views\Pa\Acts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose PA acts index view with filters and stats
 *
 * @architecture View Layer (PA brand)
 * @dependencies PaActController::index(), pa_acts localization
 * @accessibility WCAG 2.1 AA compliant
 */
--}}

<x-pa-layout title="{{ __('pa_acts.index.page_title') }}">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">{{ __('pa_acts.index.title') }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ __('pa_acts.index.title') }}</x-slot:pageTitle>

    {{-- Subtitle --}}
    <div class="mb-8">
        <p class="text-gray-600">
            {{ __('pa_acts.index.subtitle') }}
        </p>
        {{-- Upload Button: TODO - Create upload.blade.php view and GET route --}}
        {{--
        <a href="{{ route('pa.acts.upload') }}"
            class="inline-flex transform items-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:bg-[#C39563]"
            aria-label="{{ __('pa_acts.index.upload_new_act') }}">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('pa_acts.index.upload_new_act') }}
        </a>
        --}}
    </div>

    {{-- Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Total Acts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        {{ __('pa_acts.index.stats.total') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#1B365D]">
                        {{ $stats['total'] ?? 0 }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#1B365D] bg-opacity-10 p-3">
                    <svg class="h-8 w-8 text-[#1B365D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Anchored Acts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        {{ __('pa_acts.index.stats.anchored') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#2D5016]">
                        {{ $stats['anchored'] ?? 0 }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#2D5016] bg-opacity-10 p-3">
                    <svg class="h-8 w-8 text-[#2D5016]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Acts --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">
                        {{ __('pa_acts.index.stats.pending') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-[#E67E22]">
                        {{ $stats['pending'] ?? 0 }}
                    </p>
                </div>
                <div class="rounded-lg bg-[#E67E22] bg-opacity-10 p-3">
                    <svg class="h-8 w-8 text-[#E67E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- N.A.T.A.N. AI Analysis Widget --}}
        <div
            class="relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1B365D] to-[#D4A574] p-6 text-white shadow-lg">
            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/5"></div>

            <div class="relative">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-white/20 p-2">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="text-xs font-medium text-white/80">N.A.T.A.N.</div>
                    </div>

                    <div class="mb-1 text-2xl font-bold text-white">
                        {{ $stats['natan_analyzed'] ?? 0 }}
                    </div>

                    <div class="text-xs text-white/70">
                        Atti analizzati con AI
                    </div>
                </div>

                <div class="text-sm text-white/90">
                    Neuro-Analytical Text Analysis Network
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('pa.acts.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                {{-- Search --}}
                <div>
                    <label for="search" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_acts.index.filters.search') }}
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('pa_acts.index.filters.search_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-[#1B365D]"
                        aria-label="{{ __('pa_acts.index.filters.search') }}">
                </div>

                {{-- Doc Type --}}
                <div>
                    <label for="doc_type" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_acts.index.filters.doc_type') }}
                    </label>
                    <select id="doc_type" name="doc_type"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-[#1B365D]"
                        aria-label="{{ __('pa_acts.index.filters.doc_type') }}">
                        <option value="">{{ __('pa_acts.index.filters.all_types') }}</option>
                        <option value="delibera" {{ request('doc_type') === 'delibera' ? 'selected' : '' }}>
                            {{ __('pa_acts.doc_types.delibera.label') }}
                        </option>
                        <option value="determina" {{ request('doc_type') === 'determina' ? 'selected' : '' }}>
                            {{ __('pa_acts.doc_types.determina.label') }}
                        </option>
                        <option value="ordinanza" {{ request('doc_type') === 'ordinanza' ? 'selected' : '' }}>
                            {{ __('pa_acts.doc_types.ordinanza.label') }}
                        </option>
                        <option value="decreto" {{ request('doc_type') === 'decreto' ? 'selected' : '' }}>
                            {{ __('pa_acts.doc_types.decreto.label') }}
                        </option>
                        <option value="atto" {{ request('doc_type') === 'atto' ? 'selected' : '' }}>
                            {{ __('pa_acts.doc_types.atto.label') }}
                        </option>
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label for="date_from" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_acts.index.filters.date_from') }}
                    </label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-[#1B365D]"
                        aria-label="{{ __('pa_acts.index.filters.date_from') }}">
                </div>

                {{-- Date To --}}
                <div>
                    <label for="date_to" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ __('pa_acts.index.filters.date_to') }}
                    </label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-[#1B365D]"
                        aria-label="{{ __('pa_acts.index.filters.date_to') }}">
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">
                    {{ __('pa_acts.index.filters.status') }}:
                </label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value=""
                            {{ request('status') === null ? 'checked' : '' }}
                            class="form-radio text-[#1B365D] focus:ring-[#1B365D]">
                        <span class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_all') }}</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="anchored"
                            {{ request('status') === 'anchored' ? 'checked' : '' }}
                            class="form-radio text-[#2D5016] focus:ring-[#2D5016]">
                        <span
                            class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_anchored') }}</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="pending"
                            {{ request('status') === 'pending' ? 'checked' : '' }}
                            class="form-radio text-[#E67E22] focus:ring-[#E67E22]">
                        <span
                            class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_pending') }}</span>
                    </label>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center space-x-4 border-t border-gray-200 pt-4">
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-[#1B365D] px-6 py-2 font-semibold text-white transition-colors hover:bg-[#0F2544]">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    {{ __('pa_acts.index.filters.apply') }}
                </button>
                <a href="{{ route('pa.acts.index') }}"
                    class="inline-flex items-center rounded-lg bg-gray-200 px-6 py-2 font-semibold text-gray-700 transition-colors hover:bg-gray-300">
                    {{ __('pa_acts.index.filters.reset') }}
                </a>
            </div>
        </form>
    </div>

    {{-- Acts Table --}}
    @if ($acts->count() > 0)
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('pa_acts.index.table.protocol') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('pa_acts.index.table.title') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('pa_acts.index.table.type') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('pa_acts.index.table.status') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('pa_acts.index.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($acts as $act)
                            <tr class="transition-colors hover:bg-gray-50">
                                {{-- Protocol --}}
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium text-[#1B365D]">
                                        {{ $act->pa_protocol_number ?? ($act->jsonMetadata['protocol_number'] ?? 'N/A') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $act->pa_protocol_date ? $act->pa_protocol_date->format('d/m/Y') : (isset($act->jsonMetadata['protocol_date']) ? \Carbon\Carbon::parse($act->jsonMetadata['protocol_date'])->format('d/m/Y') : '') }}
                                    </div>
                                </td>

                                {{-- Title --}}
                                <td class="px-6 py-4">
                                    <div class="max-w-xs truncate text-sm text-gray-900" title="{{ $act->title }}">
                                        {{ $act->title }}
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                        $docType = $act->pa_act_type ?? ($act->jsonMetadata['doc_type'] ?? null);
                                        $colors = [
                                            'delibera' => 'bg-blue-100 text-blue-800',
                                            'determina' => 'bg-green-100 text-green-800',
                                            'ordinanza' => 'bg-red-100 text-red-800',
                                            'decreto' => 'bg-purple-100 text-purple-800',
                                            'atto' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $colorClass = $colors[$docType] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="{{ $colorClass }} inline-flex items-center rounded-full px-3 py-1 text-xs font-medium">
                                        {{ $docType ? __('pa_acts.doc_types.' . $docType . '.label') : 'N/A' }}
                                    </span>
                                </td>

                                {{-- Status N.A.T.A.N. Tokenization --}}
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($act->pa_anchored)
                                        <div class="flex flex-col space-y-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-[#2D5016] bg-opacity-10 px-3 py-1 text-xs font-medium text-[#2D5016]">
                                                <span class="material-icons mr-1 text-sm">verified</span>
                                                Tokenizzato
                                            </span>
                                            @if ($act->pa_anchored_at)
                                                <span class="text-xs text-gray-500">
                                                    {{ $act->pa_anchored_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-[#E67E22] bg-opacity-10 px-3 py-1 text-xs font-medium text-[#E67E22]">
                                            <span class="material-icons mr-1 text-sm">schedule</span>
                                            In Attesa
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('pa.acts.show', $act) }}"
                                        class="inline-flex items-center text-[#1B365D] transition-colors hover:text-[#D4A574]"
                                        aria-label="{{ __('pa_acts.index.actions.view_detail') }}">
                                        <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ __('pa_acts.index.actions.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                {{ $acts->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto mb-4 h-24 w-24 text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mb-2 text-xl font-semibold text-gray-700">
                {{ __('pa_acts.index.empty.title') }}
            </h3>
            <p class="mb-6 text-gray-500">
                {{ __('pa_acts.index.empty.description') }}
            </p>
            <a href="{{ route('pa.acts.upload') }}"
                class="inline-flex items-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white shadow-md transition-all hover:bg-[#C39563]">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('pa_acts.index.empty.cta') }}
            </a>
        </div>
    @endif
</x-pa-layout>
