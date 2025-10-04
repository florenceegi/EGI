{{--
/**
 * PA Acts Public Verification View
 * 
 * ============================================================================
 * CONTESTO - VERIFICA PUBBLICA ATTO PA
 * ============================================================================
 * 
 * View per la verifica pubblica di un atto PA tokenizzato.
 * 
 * TARGET USER: Pubblico (cittadini, aziende, altre PA)
 * ACCESS: Pubblico, NO autenticazione richiesta
 * 
 * PURPOSE:
 * - Trust-minimized verification: Chiunque può verificare autenticità
 * - Transparency: Dati pubblici visibili senza login
 * - Mobile-friendly: QR code scan → Browser → verifica immediata
 * - Privacy-aware: NO dati sensibili, solo metadati pubblici
 * 
 * ============================================================================
 * SCENARIOS
 * ============================================================================
 * 
 * 1. DOCUMENTO VERIFICATO (anchored + Merkle proof valid):
 *    ✅ Badge verde "DOCUMENTO VERIFICATO SU BLOCKCHAIN"
 *    Display: Protocol, Title, Signer, Hash, Blockchain TXID, Timestamp
 *    Trust indicators: Firma valida, Blockchain confirmed
 * 
 * 2. DOCUMENTO IN ATTESA (not anchored yet):
 *    ⏳ Badge arancione "DOCUMENTO IN ATTESA DI TOKENIZZAZIONE"
 *    Display: Protocol, Title, Signer, Hash
 *    Message: "Ancoraggio blockchain in corso (entro 24h)"
 * 
 * 3. CODICE NON TROVATO (invalid public_code):
 *    ❌ Badge rosso "CODICE VERIFICA NON VALIDO"
 *    Message: "Verifica di aver copiato correttamente il codice"
 *    No document data shown
 * 
 * ============================================================================
 * PUBLIC DATA DISPLAYED
 * ============================================================================
 * 
 * PUBBLICI (visible):
 * ✅ Protocol number + date
 * ✅ Doc type (delibera, determina, etc.)
 * ✅ Title (NO description - privacy)
 * ✅ Entity name (Comune di X)
 * ✅ Signer name, organization, role (from QES cert)
 * ✅ Signature timestamp
 * ✅ Document hash SHA-256
 * ✅ Blockchain TXID + anchor timestamp
 * ✅ Merkle proof verification result
 * 
 * PRIVATI (NOT visible):
 * ❌ Description (internal details)
 * ❌ PDF download (PA only)
 * ❌ Collection info
 * ❌ User emails/PII
 * 
 * ============================================================================
 * DESIGN NEUTRAL (NOT PA BRAND)
 * ============================================================================
 * 
 * RATIONALE:
 * - Public-facing page, NOT PA internal tool
 * - Neutral colors for institutional trust
 * - Focus on clarity and verification status
 * 
 * COLORS:
 * - Success: #10B981 (Green-500 - verified)
 * - Warning: #F59E0B (Amber-500 - pending)
 * - Error: #EF4444 (Red-500 - not found)
 * - Neutral: Gray scale for text/backgrounds
 * 
 * NO PA BRAND COLORS:
 * - No #1B365D (PA blue)
 * - No #D4A574 (PA gold)
 * - Reason: Public page, not PA property
 * 
 * ============================================================================
 * ACCESSIBILITY
 * ============================================================================
 * 
 * WCAG 2.1 AA:
 * - High contrast: Green/Amber/Red on white ≥ 4.5:1
 * - ARIA labels: Status badges, verification result
 * - Keyboard navigation: All interactive elements
 * - Screen reader: Descriptive verification status
 * - Print-friendly: Hash + TXID readable on paper
 * 
 * ============================================================================
 * 
 * @package Resources\Views\Pa\Acts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose Public verification page (unauthenticated access)
 * 
 * @architecture View Layer (Neutral public design)
 * @dependencies PaActPublicController::verify()
 * @accessibility WCAG 2.1 AA compliant
 */
--}}

@extends('layouts.guest')

@section('title', __('pa_acts.verify.page_title'))

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-12 text-center">
                <div
                    class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-600">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="mb-3 text-4xl font-bold text-gray-900">
                    {{ __('pa_acts.verify.title') }}
                </h1>
                <p class="text-lg text-gray-600">
                    {{ __('pa_acts.verify.subtitle') }}
                </p>
            </div>

            @if ($found)
                @if ($verification['verified'] === true)
                    {{-- SCENARIO 1: Documento verificato su blockchain --}}
                    <div class="mb-8 overflow-hidden rounded-2xl border-2 border-green-500 bg-white shadow-lg">
                        {{-- Status Header --}}
                        <div class="bg-green-500 px-8 py-6">
                            <div class="flex items-center justify-center space-x-3">
                                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h2 class="text-2xl font-bold text-white">
                                    {{ __('pa_acts.verify.verified.title') }}
                                </h2>
                            </div>
                            <p class="mt-2 text-center text-green-50">
                                {{ $verification['message'] }}
                            </p>
                        </div>

                        {{-- Document Info --}}
                        <div class="space-y-6 p-8">
                            {{-- Protocol & Type --}}
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ $metadata['protocol_number'] }}
                                    </h3>
                                    <p class="text-gray-600">
                                        {{ __('pa_acts.verify.protocol_date') }}:
                                        {{ \Carbon\Carbon::parse($metadata['protocol_date'])->format('d/m/Y') }}
                                    </p>
                                </div>
                                @php
                                    $docType = $metadata['doc_type'] ?? null;
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
                                    class="{{ $colorClass }} inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold">
                                    {{ $docType ? __('pa_acts.doc_types.' . $docType . '.label') : 'N/A' }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-600">
                                    {{ __('pa_acts.verify.document_title') }}
                                </label>
                                <p class="text-lg text-gray-900">{{ $metadata['title'] }}</p>
                            </div>

                            {{-- Entity --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-600">
                                    {{ __('pa_acts.verify.entity') }}
                                </label>
                                <p class="text-gray-900">{{ $metadata['entity_name'] }}</p>
                            </div>

                            {{-- Signer Info --}}
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="mb-4 flex items-center text-lg font-semibold text-gray-900">
                                    <svg class="mr-2 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ __('pa_acts.verify.digital_signature') }}
                                </h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">
                                            {{ __('pa_acts.verify.signer_name') }}
                                        </label>
                                        <p class="text-gray-900">{{ $metadata['signer_cn'] ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">
                                            {{ __('pa_acts.verify.signer_role') }}
                                        </label>
                                        <p class="text-gray-900">{{ $metadata['signer_role'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-600">
                                        {{ __('pa_acts.verify.certificate_issuer') }}
                                    </label>
                                    <p class="text-sm text-gray-700">{{ $metadata['cert_issuer'] ?? 'N/A' }}</p>
                                </div>
                                <div class="mt-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-600">
                                        {{ __('pa_acts.verify.signature_timestamp') }}
                                    </label>
                                    <p class="text-gray-900">
                                        {{ \Carbon\Carbon::parse($metadata['signature_timestamp'])->format('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Blockchain Info --}}
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="mb-4 flex items-center text-lg font-semibold text-gray-900">
                                    <svg class="mr-2 h-5 w-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    {{ __('pa_acts.verify.blockchain_data') }}
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">
                                            {{ __('pa_acts.verify.transaction_id') }}
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <p class="flex-1 break-all font-mono text-sm text-gray-900">
                                                {{ $metadata['anchor_txid'] }}
                                            </p>
                                            @if ($algorand_explorer_url)
                                                <a href="{{ $algorand_explorer_url }}" target="_blank"
                                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1 text-xs text-white transition-colors hover:bg-blue-700"
                                                    aria-label="{{ __('pa_acts.verify.view_explorer') }}">
                                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    {{ __('pa_acts.verify.explorer') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">
                                            {{ __('pa_acts.verify.document_hash') }}
                                        </label>
                                        <p class="break-all font-mono text-sm text-gray-900">
                                            {{ $metadata['doc_hash'] }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">
                                            {{ __('pa_acts.verify.anchored_at') }}
                                        </label>
                                        <p class="text-gray-900">
                                            {{ \Carbon\Carbon::parse($metadata['anchored_at'])->format('d/m/Y H:i:s') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Trust Indicators --}}
                            <div class="border-t border-gray-200 pt-6">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="flex items-start space-x-3 rounded-lg bg-green-50 p-4">
                                        <svg class="h-6 w-6 flex-shrink-0 text-green-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-green-900">
                                                {{ __('pa_acts.verify.signature_valid') }}</p>
                                            <p class="text-sm text-green-700">{{ __('pa_acts.verify.qes_certificate') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 rounded-lg bg-blue-50 p-4">
                                        <svg class="h-6 w-6 flex-shrink-0 text-blue-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-blue-900">
                                                {{ __('pa_acts.verify.blockchain_confirmed') }}</p>
                                            <p class="text-sm text-blue-700">{{ __('pa_acts.verify.algorand_network') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($verification['verified'] === null)
                    {{-- SCENARIO 2: Documento in attesa di ancoraggio --}}
                    <div class="mb-8 overflow-hidden rounded-2xl border-2 border-amber-500 bg-white shadow-lg">
                        {{-- Status Header --}}
                        <div class="bg-amber-500 px-8 py-6">
                            <div class="flex items-center justify-center space-x-3">
                                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h2 class="text-2xl font-bold text-white">
                                    {{ __('pa_acts.verify.pending.title') }}
                                </h2>
                            </div>
                            <p class="mt-2 text-center text-amber-50">
                                {{ $verification['message'] }}
                            </p>
                        </div>

                        {{-- Document Info (Minimal) --}}
                        <div class="space-y-6 p-8">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ $metadata['protocol_number'] }}
                                    </h3>
                                    <p class="text-gray-600">
                                        {{ __('pa_acts.verify.protocol_date') }}:
                                        {{ \Carbon\Carbon::parse($metadata['protocol_date'])->format('d/m/Y') }}
                                    </p>
                                </div>
                                @php
                                    $docType = $metadata['doc_type'] ?? null;
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
                                    class="{{ $colorClass }} inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold">
                                    {{ $docType ? __('pa_acts.doc_types.' . $docType . '.label') : 'N/A' }}
                                </span>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-600">
                                    {{ __('pa_acts.verify.document_title') }}
                                </label>
                                <p class="text-lg text-gray-900">{{ $metadata['title'] }}</p>
                            </div>

                            <div class="rounded-lg bg-amber-50 p-4">
                                <p class="text-amber-900">
                                    {{ __('pa_acts.verify.pending.info') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- SCENARIO 3: Verifica fallita (rare) --}}
                    <div class="mb-8 overflow-hidden rounded-2xl border-2 border-red-500 bg-white shadow-lg">
                        <div class="bg-red-500 px-8 py-6">
                            <div class="flex items-center justify-center space-x-3">
                                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h2 class="text-2xl font-bold text-white">
                                    {{ __('pa_acts.verify.failed.title') }}
                                </h2>
                            </div>
                        </div>
                        <div class="p-8">
                            <p class="text-center text-gray-700">
                                {{ $verification['message'] }}
                            </p>
                        </div>
                    </div>
                @endif
            @else
                {{-- SCENARIO 4: Codice non trovato --}}
                <div class="mb-8 overflow-hidden rounded-2xl border-2 border-red-500 bg-white shadow-lg">
                    <div class="bg-red-500 px-8 py-6">
                        <div class="flex items-center justify-center space-x-3">
                            <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <h2 class="text-2xl font-bold text-white">
                                {{ __('pa_acts.verify.not_found.title') }}
                            </h2>
                        </div>
                    </div>
                    <div class="p-8 text-center">
                        <p class="mb-6 text-gray-700">
                            {{ $error ?? __('pa_acts.verify.not_found.message') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('pa_acts.verify.not_found.code_shown') }}: <code
                                class="rounded bg-gray-100 px-2 py-1 font-mono">{{ $public_code }}</code>
                        </p>
                    </div>
                </div>
            @endif

            {{-- How to Verify Section --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <details class="cursor-pointer">
                    <summary class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="mr-2 h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('pa_acts.verify.how_to.title') }}
                    </summary>
                    <div class="mt-4 space-y-3 pl-7 text-gray-700">
                        <p><strong>1.</strong> {{ __('pa_acts.verify.how_to.step1') }}</p>
                        <p><strong>2.</strong> {{ __('pa_acts.verify.how_to.step2') }}</p>
                        <p><strong>3.</strong> {{ __('pa_acts.verify.how_to.step3') }}</p>
                        <p><strong>4.</strong> {{ __('pa_acts.verify.how_to.step4') }}</p>
                    </div>
                </details>
            </div>

        </div>
    </div>
@endsection
