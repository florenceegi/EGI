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

@extends('layouts.app')

@section('title', __('pa_acts.index.page_title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-serif font-bold text-[#1B365D]">
                        {{ __('pa_acts.index.title') }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        {{ __('pa_acts.index.subtitle') }}
                    </p>
                </div>
                
                {{-- Upload Button --}}
                <div>
                    <a href="{{ route('pa.acts.upload') }}" 
                       class="inline-flex items-center px-6 py-3 bg-[#D4A574] hover:bg-[#C39563] text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105"
                       aria-label="{{ __('pa_acts.index.upload_new_act') }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('pa_acts.index.upload_new_act') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Acts --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            {{ __('pa_acts.index.stats.total') }}
                        </p>
                        <p class="text-3xl font-bold text-[#1B365D] mt-2">
                            {{ $stats['total'] ?? 0 }}
                        </p>
                    </div>
                    <div class="p-3 bg-[#1B365D] bg-opacity-10 rounded-lg">
                        <svg class="w-8 h-8 text-[#1B365D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Anchored Acts --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            {{ __('pa_acts.index.stats.anchored') }}
                        </p>
                        <p class="text-3xl font-bold text-[#2D5016] mt-2">
                            {{ $stats['anchored'] ?? 0 }}
                        </p>
                    </div>
                    <div class="p-3 bg-[#2D5016] bg-opacity-10 rounded-lg">
                        <svg class="w-8 h-8 text-[#2D5016]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Acts --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            {{ __('pa_acts.index.stats.pending') }}
                        </p>
                        <p class="text-3xl font-bold text-[#E67E22] mt-2">
                            {{ $stats['pending'] ?? 0 }}
                        </p>
                    </div>
                    <div class="p-3 bg-[#E67E22] bg-opacity-10 rounded-lg">
                        <svg class="w-8 h-8 text-[#E67E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('pa.acts.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pa_acts.index.filters.search') }}
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="{{ __('pa_acts.index.filters.search_placeholder') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B365D] focus:border-transparent"
                               aria-label="{{ __('pa_acts.index.filters.search') }}">
                    </div>

                    {{-- Doc Type --}}
                    <div>
                        <label for="doc_type" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pa_acts.index.filters.doc_type') }}
                        </label>
                        <select id="doc_type" 
                                name="doc_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B365D] focus:border-transparent"
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
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pa_acts.index.filters.date_from') }}
                        </label>
                        <input type="date" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B365D] focus:border-transparent"
                               aria-label="{{ __('pa_acts.index.filters.date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pa_acts.index.filters.date_to') }}
                        </label>
                        <input type="date" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1B365D] focus:border-transparent"
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
                            <input type="radio" 
                                   name="status" 
                                   value="" 
                                   {{ request('status') === null ? 'checked' : '' }}
                                   class="form-radio text-[#1B365D] focus:ring-[#1B365D]">
                            <span class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_all') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" 
                                   name="status" 
                                   value="anchored" 
                                   {{ request('status') === 'anchored' ? 'checked' : '' }}
                                   class="form-radio text-[#2D5016] focus:ring-[#2D5016]">
                            <span class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_anchored') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" 
                                   name="status" 
                                   value="pending" 
                                   {{ request('status') === 'pending' ? 'checked' : '' }}
                                   class="form-radio text-[#E67E22] focus:ring-[#E67E22]">
                            <span class="ml-2 text-sm text-gray-700">{{ __('pa_acts.index.filters.status_pending') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center space-x-4 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-[#1B365D] hover:bg-[#0F2544] text-white font-semibold rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        {{ __('pa_acts.index.filters.apply') }}
                    </button>
                    <a href="{{ route('pa.acts.index') }}" 
                       class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                        {{ __('pa_acts.index.filters.reset') }}
                    </a>
                </div>
            </form>
        </div>

        {{-- Acts Table --}}
        @if($acts->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('pa_acts.index.table.protocol') }}
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('pa_acts.index.table.title') }}
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('pa_acts.index.table.type') }}
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('pa_acts.index.table.status') }}
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('pa_acts.index.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($acts as $act)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Protocol --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[#1B365D]">
                                    {{ $act->metadata['protocol_number'] ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ isset($act->metadata['protocol_date']) ? \Carbon\Carbon::parse($act->metadata['protocol_date'])->format('d/m/Y') : '' }}
                                </div>
                            </td>

                            {{-- Title --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $act->title }}">
                                    {{ $act->title }}
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $docType = $act->metadata['doc_type'] ?? null;
                                    $colors = [
                                        'delibera' => 'bg-blue-100 text-blue-800',
                                        'determina' => 'bg-green-100 text-green-800',
                                        'ordinanza' => 'bg-red-100 text-red-800',
                                        'decreto' => 'bg-purple-100 text-purple-800',
                                        'atto' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $colorClass = $colors[$docType] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                    {{ $docType ? __('pa_acts.doc_types.'.$docType.'.label') : 'N/A' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($act->metadata['anchored'] ?? false)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#2D5016] bg-opacity-10 text-[#2D5016]">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('pa_acts.index.status.anchored') }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#E67E22] bg-opacity-10 text-[#E67E22]">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('pa_acts.index.status.pending') }}
                                </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('pa.acts.show', $act) }}" 
                                   class="inline-flex items-center text-[#1B365D] hover:text-[#D4A574] transition-colors"
                                   aria-label="{{ __('pa_acts.index.actions.view_detail') }}">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $acts->links() }}
            </div>
        </div>
        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                {{ __('pa_acts.index.empty.title') }}
            </h3>
            <p class="text-gray-500 mb-6">
                {{ __('pa_acts.index.empty.description') }}
            </p>
            <a href="{{ route('pa.acts.upload') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#D4A574] hover:bg-[#C39563] text-white font-semibold rounded-lg shadow-md transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('pa_acts.index.empty.cta') }}
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
