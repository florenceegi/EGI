{{--
    MINT PAGE - PAGINA 2 (READ-ONLY)
    🎯 Purpose: Mostra dati mint completato (riapribile)
    📍 Route: GET /mint/{egiBlockchainId}
    🔒 Auth: Buyer può vedere
--}}
<x-platform-layout :title="__('mint.minted_title', ['title' => $egi->title])">
    @php
        $isOwner = Auth::id() !== null && $blockchain && Auth::id() === $blockchain->buyer_user_id;
    @endphp
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12">
        <div class="container mx-auto max-w-7xl px-4">

            {{-- Header Success --}}
            <div class="mb-12 text-center">
                <div
                    class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="mb-3 text-4xl font-bold text-gray-900">
                    @if ($isOwner)
                        {{ __('mint.post_mint.congratulations') }}
                    @else
                        {{ __('mint.post_mint.title_guest') }}
                    @endif
                </h1>
                <p class="text-lg text-gray-600">
                    @if ($isOwner)
                        {{ __('mint.post_mint.success_message') }}
                    @else
                        {{ __('mint.post_mint.message_guest') }}
                    @endif
                </p>

                {{-- Mostra owner solo se NON è l'owner corrente che visualizza --}}
                @if (!$isOwner && $blockchain->buyer)
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="font-medium text-gray-700">{{ __('mint.post_mint.current_owner') }}:</span>
                        {{ $blockchain->buyer->name }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                {{-- COLONNA 1: EGI Preview (4 cols) --}}
                <div class="lg:col-span-4">
                    <div class="overflow-hidden rounded-2xl bg-white shadow-xl">
                        @if ($egi->main_image_url)
                            <div class="aspect-[3/4] w-full overflow-hidden">
                                <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                    class="h-full w-full object-cover transition-transform hover:scale-105">
                            </div>
                        @else
                            <div
                                class="flex aspect-[3/4] w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <span class="text-6xl">🎨</span>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <h2 class="mb-1 text-xl font-bold text-gray-900">{{ $egi->title }}</h2>
                                    <p class="text-sm text-gray-600">
                                        {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-xs text-gray-500">{{ __('mint.post_mint.sale_price') }}</p>
                                    <p class="text-2xl font-bold text-green-700">
                                        €{{ number_format($salePrice, 2, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- View EGI Button --}}
                            <a href="{{ route('egis.show', $egi->id) }}"
                                class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 font-semibold text-white shadow-lg transition-all hover:from-blue-700 hover:to-blue-800 hover:shadow-xl">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ $isOwner ? __('mint.post_mint.view_egi') : __('common.view_egi') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- COLONNA 2: Blockchain + Payment (5 cols) --}}
                <div class="space-y-8 lg:col-span-5">

                    {{-- Blockchain Info Card --}}
                    <div class="rounded-2xl bg-white p-8 shadow-xl">
                        <div class="mb-6 flex items-center">
                            <div
                                class="mr-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400 to-purple-600">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ __('mint.post_mint.blockchain_info') }}
                            </h3>
                        </div>

                        @php
                            $network = config('algorand.algorand.network', 'testnet');
                            $explorerUrl = config(
                                "algorand.algorand.{$network}.explorer_url",
                                'https://testnet.explorer.perawallet.app',
                            );
                        @endphp

                        <div class="space-y-4">
                            <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-4">
                                <dt class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-600">
                                    {{ __('mint.post_mint.asa_id') }}</dt>
                                <dd class="font-mono text-2xl font-bold text-green-700">
                                    <a href="{{ $explorerUrl }}/asset/{{ $blockchain->asa_id }}" target="_blank"
                                        class="text-green-700 transition-all hover:text-green-900 hover:underline">
                                        {{ $blockchain->asa_id }}
                                    </a>
                                </dd>
                            </div>

                            <div class="rounded-xl bg-gray-50 p-4">
                                <dt class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-600">
                                    {{ __('mint.post_mint.tx_id') }}</dt>
                                <dd class="break-all font-mono text-xs text-gray-900">
                                    <a href="{{ $explorerUrl }}/tx/{{ $blockchain->blockchain_tx_id }}"
                                        target="_blank"
                                        class="text-blue-600 transition-all hover:text-blue-800 hover:underline">
                                        {{ $blockchain->blockchain_tx_id }}
                                    </a>
                                </dd>
                            </div>

                            <div class="rounded-xl bg-blue-50 p-4">
                                <dt class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-600">
                                    {{ __('mint.post_mint.minted_at') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $blockchain->minted_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ $explorerUrl }}/asset/{{ $blockchain->asa_id }}" target="_blank"
                                class="flex w-full items-center justify-center rounded-xl border-2 border-blue-600 px-6 py-3 font-semibold text-blue-700 transition-all hover:bg-blue-50">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                {{ __('mint.post_mint.view_pera_explorer') }}
                            </a>
                        </div>
                    </div>

                    {{-- Payment Breakdown --}}
                    @if ($paymentBreakdown && count($paymentBreakdown) > 0)
                        <div class="rounded-2xl bg-white p-8 shadow-xl">
                            <div class="mb-6 flex items-center">
                                <div
                                    class="mr-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-600">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ __('mint.post_mint.payment_breakdown') }}
                                </h3>
                            </div>

                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-700">
                                                {{ __('mint.post_mint.recipient') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-700">
                                                {{ __('mint.post_mint.role') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700">
                                                {{ __('mint.post_mint.amount') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($paymentBreakdown as $payment)
                                            <tr class="transition-colors hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                    {{ $payment['recipient_name'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                                        {{ ucfirst($payment['role']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                                    €{{ number_format($payment['amount_eur'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- COLONNA 3: Certificate (3 cols) --}}
                <div class="lg:col-span-3">
                    <div class="rounded-2xl bg-white p-6 shadow-xl">
                        {{-- FIX TEMPORANEO: Calcola isOwner nella view perché il controller passa FALSE --}}
                        @php
                            $debugBuyerId = $blockchain->buyer_user_id ?? $egi->user_id;
                            $debugIsOwner = Auth::check() && (int)Auth::id() === (int)$debugBuyerId;
                            // Usa il valore calcolato correttamente
                            $isOwner = $debugIsOwner;
                        @endphp
                        
                        {{-- DEBUG INFO (remove after testing) --}}


                        <div class="mb-4 flex items-center">
                            <div
                                class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ __('mint.post_mint.certificate_title') }}
                            </h3>
                        </div>

                        <p class="mb-6 text-sm text-gray-600">
                            {{ __('mint.post_mint.certificate_description') }}
                        </p>

                        @if ($certificate)
                            {{-- Certificate Preview Card --}}
                            <div class="mb-6">
                                @if ($certificate->hasPdf())
                                    {{-- CoA PDF Thumbnail Section (SAME SYSTEM AS egis-show) --}}
                                    <details class="group mt-3" id="coaPdfThumbSection-{{ $certificate->id }}" open>
                                        <summary
                                            class="mb-3 cursor-pointer text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                                            📄 {{ __('mint.post_mint.certificate_preview') }}
                                        </summary>

                                        {{-- PDF Thumbnail Container - FIX: responsive width --}}
                                        <div id="coaPdfPreview-{{ $certificate->id }}"
                                            data-egi-id="{{ $egi->id }}" data-thumb-width="280"
                                            data-download-url="{{ $certificate->getPdfUrl() }}"
                                            class="relative mx-auto flex aspect-[3/4] w-full max-w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-4 border-indigo-200 bg-gradient-to-br from-gray-50 to-gray-100 shadow-lg transition-all hover:border-indigo-400 hover:shadow-2xl">
                                            {{-- Placeholder while loading --}}
                                            <div
                                                class="absolute inset-0 animate-pulse bg-gradient-to-br from-indigo-50 to-purple-50">
                                            </div>
                                            <svg class="h-12 w-12 animate-spin text-indigo-600"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <p class="relative z-10 mt-4 text-sm text-gray-600">
                                                {{ __('mint.post_mint.loading_preview') }}</p>
                                        </div>
                                    </details>

                                    <p class="mt-2 text-center text-xs text-gray-500">
                                        {{ __('mint.post_mint.click_to_view') }}
                                    </p>
                                @else
                                    {{-- PDF non ancora generato --}}
                                    <div
                                        class="flex h-96 items-center justify-center rounded-xl border-2 border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">
                                                {{ __('mint.post_mint.generating_pdf') }}</p>
                                        </div>
                                    </div>
                                @endif

                            </div>

                            {{-- Action Buttons --}}
                            <div class="space-y-3">
                                {{-- Download button - Back to _blank (works) --}}
                                <button type="button" id="download-cert-btn"
                                    onclick="window.open('{{ $certificate->getPdfUrl() }}', '_blank');"
                                    class="flex w-full cursor-pointer items-center justify-center rounded-xl bg-gradient-to-r from-green-600 to-green-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:from-green-700 hover:to-green-800 hover:shadow-xl">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ __('mint.post_mint.download_certificate') }}
                                </button>

                                {{-- Regenerate Certificate Button - ONLY FOR OWNER --}}
                                @if ($isOwner)
                                    <form id="regenerate-cert-form"
                                        action="{{ route('mint.regenerate-certificate', $blockchain->id) }}"
                                        method="POST" class="w-full">
                                        @csrf
                                        <button type="button" id="regenerate-cert-btn"
                                            class="flex w-full cursor-pointer items-center justify-center rounded-xl bg-gradient-to-r from-[#1B365D] to-[#2D5016] px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:from-[#2a4a7a] hover:to-[#3d6b21] hover:shadow-xl">
                                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            <span
                                                id="regenerate-btn-text">{{ __('mint.post_mint.regenerate_certificate') }}</span>
                                        </button>
                                    </form>
                                @endif

                                <a id="view-cert-link"
                                    href="{{ route('egi-certificates.show', $certificate->certificate_uuid) }}"
                                    target="_blank"
                                    class="flex w-full items-center justify-center rounded-xl border-2 border-indigo-600 px-4 py-3 text-sm font-semibold text-indigo-700 transition-all hover:bg-indigo-50">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    {{ __('mint.post_mint.view_certificate') }}
                                </a>

                                @if ($isOwner)
                                    <a href="{{ route('my-certificates') }}"
                                        class="flex w-full items-center justify-center rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-200">
                                        {{ __('mint.post_mint.my_certificates') }}
                                    </a>
                                @endif
                            </div>
                        @else
                            {{-- Certificato NON ancora creato - Mostra messaggio e bottone per generarlo --}}
                            <div class="rounded-xl border-2 border-amber-300 bg-amber-50 p-6">
                                <div class="mb-4 flex items-start space-x-3">
                                    <svg class="h-6 w-6 flex-shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-amber-900">{{ __('mint.post_mint.certificate_not_created_title') }}</h4>
                                        <p class="mt-1 text-sm text-amber-700">
                                            {{ __('mint.post_mint.certificate_not_created_message') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($isOwner)
                                    {{-- Bottone per generare il certificato - STESSO SISTEMA del regenerate --}}
                                    <form id="generate-cert-form" action="{{ route('mint.regenerate-certificate', $blockchain->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="button" id="generate-cert-btn"
                                            class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:from-amber-700 hover:to-orange-700 hover:shadow-xl">
                                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span id="generate-btn-text">{{ __('mint.post_mint.generate_certificate') }}</span>
                                        </button>
                                    </form>
                                @else
                                    <p class="text-xs text-amber-600 mt-2">
                                        {{ __('mint.post_mint.certificate_owner_only') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- CoA PDF Thumbnail Rendering Script (EXACT COPY from coa/sidebar-section.blade.php) --}}
    @once
        @push('scripts')
            <script>
                // --- Force Download Certificate PDF via hidden iframe (most compatible) ---
                function downloadCertificatePdf(url, filename) {
                    console.log('🔽 Starting download via iframe:', url);

                    // Method 1: Try with hidden iframe (works with Content-Disposition)
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = url;
                    document.body.appendChild(iframe);

                    console.log('✅ Download iframe created');

                    // Cleanup iframe after 3 seconds
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        console.log('🧹 Iframe cleaned up');
                    }, 3000);
                }

                // --- Lazy PDF.js loader ---
                let __pdfjsReady = null;

                function ensurePdfJsLoaded() {
                    if (window['pdfjsLib']) return Promise.resolve();
                    if (__pdfjsReady) return __pdfjsReady;
                    __pdfjsReady = new Promise((resolve, reject) => {
                        const s = document.createElement('script');
                        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                        s.async = true;
                        s.onload = () => {
                            try {
                                // Set worker
                                window['pdfjsLib'].GlobalWorkerOptions.workerSrc =
                                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                                resolve();
                            } catch (e) {
                                reject(e);
                            }
                        };
                        s.onerror = reject;
                        document.head.appendChild(s);
                    });
                    return __pdfjsReady;
                }

                // Centralized i18n for JS strings
                const I18N_MINT = {
                    generating_pdf: @json(__('mint.post_mint.loading_preview')),
                    unexpected_error: @json(__('mint.post_mint.thumbnail_error'))
                };

                async function renderCoaPdfThumb(container, egiId) {
                    try {
                        // 1) Check existing PDF and get URL (MINT CERTIFICATE ENDPOINT)
                        const res = await fetch(`/mint/${egiId}/certificate/pdf/check`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        // Check response status BEFORE parsing JSON
                        if (!res.ok) {
                            console.error('Certificate PDF check failed:', {
                                status: res.status,
                                statusText: res.statusText,
                                egiId: egiId
                            });

                            if (res.status === 403) {
                                container.innerHTML =
                                    `<div class="p-3 text-xs text-center text-red-300">🔒 Accesso non autorizzato</div>`;
                                return;
                            }

                            if (res.status === 404) {
                                container.innerHTML =
                                    `<div class="p-3 text-xs text-center text-gray-400">📄 Certificato non trovato</div>`;
                                return;
                            }

                            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                        }

                        const info = await res.json();

                        console.log('Certificate PDF check response:', info);

                        if (!info || !info.pdf_exists || !info.download_url) {
                            console.warn('PDF not available:', info);
                            container.innerHTML =
                                `<div class="p-3 text-xs text-center text-gray-400">${I18N_MINT.generating_pdf}</div>`;
                            return;
                        }

                        console.log('PDF exists, rendering thumbnail...', {
                            download_url: info.download_url,
                            egiId: egiId
                        });

                        // 2) Load pdf.js
                        await ensurePdfJsLoaded();

                        // 3) Render first page to canvas
                        const url = info.download_url;
                        container.dataset.downloadUrl = url;
                        const pdf = await window['pdfjsLib'].getDocument({
                            url
                        }).promise;
                        const page = await pdf.getPage(1);

                        const view = page.getViewport({
                            scale: 1
                        });
                        const targetW = parseInt(container.getAttribute('data-thumb-width') || '140', 10);
                        const scale = targetW / view.width;
                        const vp = page.getViewport({
                            scale
                        });

                        const canvas = document.createElement('canvas');
                        canvas.width = Math.floor(vp.width);
                        canvas.height = Math.floor(vp.height);
                        const ctx = canvas.getContext('2d');
                        await page.render({
                            canvasContext: ctx,
                            viewport: vp
                        }).promise;

                        container.innerHTML = '';
                        canvas.style.width = '100%';
                        canvas.style.height = 'auto';
                        container.appendChild(canvas);
                        container.addEventListener('click', () => {
                            const du = container.getAttribute('data-download-url') || container.dataset.downloadUrl;
                            if (du) window.open(du, '_blank');
                        }, {
                            once: true
                        });
                    } catch (e) {
                        console.warn('CoA PDF thumb error', e);
                        container.innerHTML =
                            `<div class="p-3 text-xs text-center text-red-300">${I18N_MINT.unexpected_error}</div>`;
                    }
                }

                // Observe and render when visible
                document.addEventListener('DOMContentLoaded', () => {
                    const el = document.getElementById('coaPdfPreview-{{ $certificate->id ?? 'none' }}');
                    const section = document.getElementById('coaPdfThumbSection-{{ $certificate->id ?? 'none' }}');
                    if (!el || !section) return;
                    const egiId = el.getAttribute('data-egi-id');
                    if (!egiId) return;

                    // Render on first open
                    let rendered = false;
                    section.addEventListener('toggle', () => {
                        if (section.open && !rendered) {
                            rendered = true;
                            renderCoaPdfThumb(el, egiId);
                        }
                    });

                    // Also render if already open (details has 'open' attribute)
                    if (section.open) {
                        renderCoaPdfThumb(el, egiId);
                        rendered = true;
                    }
                });

                // --- Certificate Generation/Regeneration Handler (UNIFIED) ---
                function setupCertificateHandler(btnId, formId, textId) {
                    const btn = document.getElementById(btnId);
                    if (!btn) return;
                    
                    btn.addEventListener('click', async function(e) {
                        e.preventDefault();

                        const btnText = document.getElementById(textId);
                        const form = document.getElementById(formId);

                    // Disable button during regeneration
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btnText.textContent = 'Rigenerazione in corso...';

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            btnText.textContent = '✅ Completato!';

                            // Se è prima generazione (generate-cert-btn), ricarica la pagina
                            const isFirstGeneration = btnId === 'generate-cert-btn';
                            
                            if (isFirstGeneration) {
                                console.log('🔄 Prima generazione certificato, reload pagina...');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                                return;
                            }

                            // Se è rigenerazione, aggiorna thumbnail
                            const thumbnailContainer = document.querySelector('#coaPdfPreview-{{ $certificate->id ?? 'none' }}');
                            if (thumbnailContainer) {
                                const egiId = data.egi_id;
                                console.log('🔄 Reloading thumbnail for EGI:', egiId);

                                // Add loading animation to thumbnail
                                thumbnailContainer.classList.add('opacity-50', 'scale-95', 'transition-all',
                                    'duration-300');

                                await renderCoaPdfThumb(thumbnailContainer, egiId);

                                // Remove loading, add success flash
                                thumbnailContainer.classList.remove('opacity-50', 'scale-95');
                                thumbnailContainer.classList.add('scale-100');

                                // Green flash effect
                                const flash = document.createElement('div');
                                flash.className =
                                    'absolute inset-0 bg-green-500 opacity-30 animate-pulse pointer-events-none z-50';
                                thumbnailContainer.style.position = 'relative';
                                thumbnailContainer.appendChild(flash);

                                // Remove flash after 1 second
                                setTimeout(() => {
                                    flash.remove();
                                }, 1000);

                                console.log('✅ PDF thumbnail reloaded with flash effect');
                            } else {
                                console.warn('❌ Thumbnail container not found');
                            }

                            // Update certificate action links to new regenerated URLs
                            try {
                                // 1) Update Download button onclick to use new pdf_url
                                const downloadBtn = document.getElementById('download-cert-btn');
                                if (downloadBtn && data.pdf_url) {
                                    downloadBtn.setAttribute('onclick', `window.open('${data.pdf_url}', '_blank');`);
                                }

                                // 2) Update "View Certificate" link href to new public_url
                                const viewLink = document.getElementById('view-cert-link');
                                if (viewLink && data.public_url) {
                                    viewLink.setAttribute('href', data.public_url);
                                }
                            } catch (linkErr) {
                                console.warn('Link update after regeneration failed', linkErr);
                            }

                            // Reset button after 2 seconds
                            setTimeout(() => {
                                btnText.textContent = '{{ __('mint.post_mint.regenerate_certificate') }}';
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Generation/Regeneration failed');
                        }
                    } catch (error) {
                        console.error('Certificate generation/regeneration error:', error);
                        btnText.textContent = '❌ Errore';
                        setTimeout(() => {
                            btnText.textContent = 'Riprova';
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }, 2000);
                    }
                    });
                }
                
                // Setup handlers per entrambi i bottoni (generate e regenerate)
                setupCertificateHandler('regenerate-cert-btn', 'regenerate-cert-form', 'regenerate-btn-text');
                setupCertificateHandler('generate-cert-btn', 'generate-cert-form', 'generate-btn-text');
            </script>
        @endpush
    @endonce

</x-platform-layout>
```
