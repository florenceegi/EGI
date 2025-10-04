{{--
/**
 * PA Acts Show View
 * 
 * ============================================================================
 * CONTESTO - DETTAGLIO ATTO PA TOKENIZZATO
 * ============================================================================
 * 
 * View per il dettaglio di un atto PA tokenizzato su blockchain.
 * 
 * TARGET USER: PA entities (autenticate, role:pa_entity)
 * ACCESS: Authenticated only, ownership or collection admin
 * 
 * PURPOSE:
 * - Visualizzazione completa metadati atto
 * - Info firma QES/PAdES (signer, certificate, timestamp)
 * - Dati blockchain (TXID, Merkle root, anchor timestamp)
 * - QR code per verifica pubblica
 * - URL verifica pubblica (share)
 * - Download PDF (se autorizzato)
 * 
 * ============================================================================
 * SECTIONS
 * ============================================================================
 * 
 * 1. HEADER:
 *    - Protocol number + Doc type badge
 *    - Anchor status (✅ Ancorato / ⏳ In attesa)
 *    - Back to list button
 * 
 * 2. METADATA SECTION:
 *    - Protocol info (number, date)
 *    - Title + Description
 *    - Upload timestamp
 * 
 * 3. SIGNATURE SECTION:
 *    - Signer info (nome, org, ruolo)
 *    - Certificate info (issuer, serial, validity)
 *    - Signature timestamp
 *    - Validation status badge
 * 
 * 4. BLOCKCHAIN SECTION:
 *    - Anchor status badge
 *    - Transaction ID (TXID) + Algorand Explorer link
 *    - Merkle root hash
 *    - Anchor timestamp
 *    - Document hash SHA-256
 * 
 * 5. VERIFICATION SECTION:
 *    - Public code display + copy button
 *    - QR code image
 *    - Public verification URL + copy button
 * 
 * 6. ACTIONS:
 *    - Download PDF (if authorized)
 *    - Edit metadata (if authorized)
 *    - Delete (with confirmation, if authorized)
 * 
 * ============================================================================
 * PA BRAND DESIGN
 * ============================================================================
 * 
 * COLORS:
 * - Primary: #1B365D (Blu Algoritmo)
 * - Accent: #D4A574 (Oro Fiorentino)
 * - Success: #2D5016 (Verde Rinascita - firma valida, ancorato)
 * - Warning: #E67E22 (Arancio Energia - pending)
 * 
 * LAYOUT:
 * - Cards con ombre eleganti
 * - Spazi bianchi generosi
 * - Sezioni ben separate
 * 
 * ICONS:
 * - Heroicons outline per consistenza
 * - Badge per status (firma, ancoraggio)
 * 
 * ============================================================================
 * ACCESSIBILITY
 * ============================================================================
 * 
 * WCAG 2.1 AA:
 * - Headings hierarchy: h1 → h2 → h3
 * - ARIA labels: All buttons, links, badges
 * - Keyboard navigation: Tab order logical
 * - Screen reader: Descriptive text for QR code
 * - Contrast: All text meets 4.5:1 minimum
 * 
 * ============================================================================
 * 
 * @package Resources\Views\Pa\Acts
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - PA Acts Tokenization)
 * @date 2025-10-04
 * @purpose PA act detail view with blockchain data
 * 
 * @architecture View Layer (PA brand)
 * @dependencies PaActController::show(), SimpleSoftwareIO/simple-qrcode
 * @accessibility WCAG 2.1 AA compliant
 */
--}}

<x-pa-layout :title="__('pa_acts.show.page_title', ['protocol' => $metadata['protocol_number']])">
    <x-slot:breadcrumb>
        <a href="{{ route('pa.dashboard') }}" class="text-[#D4A574] hover:text-[#C39463]">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('pa.acts.index') }}" class="text-[#D4A574] hover:text-[#C39463]">{{ __('pa_acts.index.title') }}</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">{{ $metadata['protocol_number'] ?? 'N/A' }}</span>
    </x-slot:breadcrumb>

    <x-slot:pageTitle>{{ __('pa_acts.show.page_title', ['protocol' => $metadata['protocol_number']]) }}</x-slot:pageTitle>

    {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('pa.acts.index') }}"
                    class="inline-flex items-center font-medium text-[#1B365D] transition-colors hover:text-[#D4A574]"
                    aria-label="{{ __('pa_acts.show.back_to_list') }}">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('pa_acts.show.back_to_list') }}
                </a>
            </div>

            {{-- Header Section --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="mb-2 flex items-center space-x-3">
                            <h1 class="font-serif text-2xl font-bold text-[#1B365D]">
                                {{ $metadata['protocol_number'] ?? 'N/A' }}
                            </h1>
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
                                class="{{ $colorClass }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium">
                                {{ $docType ? __('pa_acts.doc_types.' . $docType . '.label') : 'N/A' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ __('pa_acts.show.protocol_date') }}:
                            {{ isset($metadata['protocol_date']) ? \Carbon\Carbon::parse($metadata['protocol_date'])->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>

                    {{-- Anchor Status Badge --}}
                    <div>
                        @if ($metadata['anchored'] ?? false)
                            <div
                                class="inline-flex items-center rounded-lg bg-[#2D5016] bg-opacity-10 px-4 py-2 font-semibold text-[#2D5016]">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('pa_acts.show.status.anchored') }}
                            </div>
                        @else
                            <div
                                class="inline-flex items-center rounded-lg bg-[#E67E22] bg-opacity-10 px-4 py-2 font-semibold text-[#E67E22]">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('pa_acts.show.status.pending') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Metadata Section --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                <h2 class="mb-6 flex items-center text-xl font-semibold text-[#1B365D]">
                    <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('pa_acts.show.metadata.title') }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-600">
                            {{ __('pa_acts.show.metadata.act_title') }}
                        </label>
                        <p class="text-lg text-gray-900">{{ $egi->title }}</p>
                    </div>

                    @if ($egi->description)
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.metadata.description') }}
                            </label>
                            <p class="text-gray-700">{{ $egi->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4 border-t border-gray-200 pt-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.metadata.upload_date') }}
                            </label>
                            <p class="text-gray-900">{{ $egi->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.metadata.entity') }}
                            </label>
                            <p class="text-gray-900">{{ $metadata['signer_organization'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Signature Section --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                <h2 class="mb-6 flex items-center text-xl font-semibold text-[#1B365D]">
                    <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('pa_acts.show.signature.title') }}
                </h2>

                <div class="mb-6 flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#2D5016] bg-opacity-10">
                            <svg class="h-7 w-7 text-[#2D5016]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#2D5016]">
                            {{ __('pa_acts.show.signature.valid') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ __('pa_acts.show.signature.qes_pades') }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.signature.signer_name') }}
                            </label>
                            <p class="text-gray-900">{{ $metadata['signer_cn'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.signature.signer_role') }}
                            </label>
                            <p class="text-gray-900">{{ $metadata['signer_role'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-600">
                            {{ __('pa_acts.show.signature.organization') }}
                        </label>
                        <p class="text-gray-900">{{ $metadata['signer_organization'] ?? 'N/A' }}</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <label class="mb-1 block text-sm font-medium text-gray-600">
                            {{ __('pa_acts.show.signature.certificate_issuer') }}
                        </label>
                        <p class="text-sm text-gray-700">{{ $metadata['cert_issuer'] ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-600">
                            {{ __('pa_acts.show.signature.timestamp') }}
                        </label>
                        <p class="text-gray-900">
                            {{ isset($metadata['signature_timestamp']) ? \Carbon\Carbon::parse($metadata['signature_timestamp'])->format('d/m/Y H:i:s') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Blockchain Section --}}
            @if ($metadata['anchored'] ?? false)
                <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                    <h2 class="mb-6 flex items-center text-xl font-semibold text-[#1B365D]">
                        <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('pa_acts.show.blockchain.title') }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.blockchain.txid') }}
                            </label>
                            <div class="flex items-center space-x-2">
                                <p class="break-all font-mono text-sm text-gray-900">
                                    {{ $metadata['anchor_txid'] ?? 'N/A' }}</p>
                                @if ($algorand_explorer_url)
                                    <a href="{{ $algorand_explorer_url }}" target="_blank"
                                        class="inline-flex items-center rounded-lg bg-[#1B365D] px-3 py-1 text-xs text-white transition-colors hover:bg-[#0F2544]"
                                        aria-label="{{ __('pa_acts.show.blockchain.view_explorer') }}">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        {{ __('pa_acts.show.blockchain.explorer') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.blockchain.merkle_root') }}
                            </label>
                            <p class="break-all font-mono text-sm text-gray-900">{{ $metadata['anchor_root'] ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.blockchain.document_hash') }}
                            </label>
                            <p class="break-all font-mono text-sm text-gray-900">{{ $metadata['doc_hash'] ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.blockchain.anchored_at') }}
                            </label>
                            <p class="text-gray-900">
                                {{ isset($metadata['anchored_at']) ? \Carbon\Carbon::parse($metadata['anchored_at'])->format('d/m/Y H:i:s') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#E67E22] bg-opacity-10">
                                <svg class="h-7 w-7 text-[#E67E22]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 text-lg font-semibold text-[#E67E22]">
                                {{ __('pa_acts.show.blockchain.pending_title') }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ __('pa_acts.show.blockchain.pending_description') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Verification Section --}}
            <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                <h2 class="mb-6 flex items-center text-xl font-semibold text-[#1B365D]">
                    <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    {{ __('pa_acts.show.verification.title') }}
                </h2>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    {{-- QR Code --}}
                    <div class="text-center">
                        <div class="inline-block rounded-xl border-4 border-[#1B365D] bg-white p-4">
                            {!! QrCode::size(200)->generate($verification_url) !!}
                        </div>
                        <p class="mt-4 text-sm text-gray-600">
                            {{ __('pa_acts.show.verification.qr_description') }}
                        </p>
                    </div>

                    {{-- Public Code & URL --}}
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.verification.public_code') }}
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="public-code" value="{{ $metadata['public_code'] ?? 'N/A' }}"
                                    readonly
                                    class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 font-mono text-sm text-gray-900">
                                <button onclick="copyToClipboard('public-code')"
                                    class="rounded-lg bg-[#1B365D] px-4 py-2 text-white transition-colors hover:bg-[#0F2544]"
                                    aria-label="{{ __('pa_acts.show.verification.copy_code') }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-600">
                                {{ __('pa_acts.show.verification.public_url') }}
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="verification-url" value="{{ $verification_url }}" readonly
                                    class="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 text-sm text-gray-900">
                                <button onclick="copyToClipboard('verification-url')"
                                    class="rounded-lg bg-[#1B365D] px-4 py-2 text-white transition-colors hover:bg-[#0F2544]"
                                    aria-label="{{ __('pa_acts.show.verification.copy_url') }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="{{ $verification_url }}" target="_blank"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-[#D4A574] px-6 py-3 font-semibold text-white transition-colors hover:bg-[#C39563]">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                {{ __('pa_acts.show.verification.open_public_page') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

    @push('scripts')
        <script>
            function copyToClipboard(elementId) {
                const input = document.getElementById(elementId);
                input.select();
                input.setSelectionRange(0, 99999); // Mobile compatibility

                navigator.clipboard.writeText(input.value).then(() => {
                    // Show success toast (Toastify or similar)
                    alert('{{ __('pa_acts.show.verification.copied') }}');
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            }
        </script>
    @endpush
</x-pa-layout>
