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
    <div class="min-h-screen py-12 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container px-4 mx-auto max-w-7xl">

            {{-- Header Success --}}
            <div class="mb-12 text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full shadow-lg bg-gradient-to-br from-green-400 to-green-600">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="mb-3 text-4xl font-bold text-gray-900">
                    {{ __('mint.post_mint.congratulations') }}
                </h1>
                <p class="text-lg text-gray-600">
                    {{ __('mint.post_mint.success_message') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                {{-- COLONNA 1: EGI Preview (4 cols) --}}
                <div class="lg:col-span-4">
                    <div class="overflow-hidden bg-white shadow-xl rounded-2xl">
                        @if ($egi->main_image_url)
                            <div class="aspect-[3/4] w-full overflow-hidden">
                                <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                                    class="object-cover w-full h-full transition-transform hover:scale-105">
                            </div>
                        @else
                            <div
                                class="flex aspect-[3/4] w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <span class="text-6xl">🎨</span>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h2 class="mb-1 text-xl font-bold text-gray-900">{{ $egi->title }}</h2>
                                    <p class="text-sm text-gray-600">
                                        {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 ml-4 text-right">
                                    <p class="text-xs text-gray-500">{{ __('mint.post_mint.sale_price') }}</p>
                                    <p class="text-2xl font-bold text-green-700">
                                        €{{ number_format($salePrice, 2, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- View EGI Button --}}
                            <a href="{{ route('egis.show', $egi->id) }}"
                                class="flex items-center justify-center w-full px-6 py-3 font-semibold text-white transition-all shadow-lg rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="p-8 bg-white shadow-xl rounded-2xl">
                        <div class="flex items-center mb-6">
                            <div
                                class="flex items-center justify-center w-12 h-12 mr-4 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
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
                            <div class="p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50">
                                <dt class="mb-1 text-xs font-medium tracking-wide text-gray-600 uppercase">
                                    {{ __('mint.post_mint.asa_id') }}</dt>
                                <dd class="font-mono text-2xl font-bold text-green-700">
                                    <a href="{{ $explorerUrl }}/asset/{{ $blockchain->asa_id }}" target="_blank"
                                        class="text-green-700 transition-all hover:text-green-900 hover:underline">
                                        {{ $blockchain->asa_id }}
                                    </a>
                                </dd>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50">
                                <dt class="mb-1 text-xs font-medium tracking-wide text-gray-600 uppercase">
                                    {{ __('mint.post_mint.tx_id') }}</dt>
                                <dd class="font-mono text-xs text-gray-900 break-all">
                                    <a href="{{ $explorerUrl }}/tx/{{ $blockchain->blockchain_tx_id }}"
                                        target="_blank"
                                        class="text-blue-600 transition-all hover:text-blue-800 hover:underline">
                                        {{ $blockchain->blockchain_tx_id }}
                                    </a>
                                </dd>
                            </div>

                            <div class="p-4 rounded-xl bg-blue-50">
                                <dt class="mb-1 text-xs font-medium tracking-wide text-gray-600 uppercase">
                                    {{ __('mint.post_mint.minted_at') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $blockchain->minted_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ $explorerUrl }}/asset/{{ $blockchain->asa_id }}" target="_blank"
                                class="flex items-center justify-center w-full px-6 py-3 font-semibold text-blue-700 transition-all border-2 border-blue-600 rounded-xl hover:bg-blue-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                {{ __('mint.post_mint.view_pera_explorer') }}
                            </a>
                        </div>
                    </div>

                    {{-- Payment Breakdown --}}
                    @if ($paymentBreakdown && count($paymentBreakdown) > 0)
                        <div class="p-8 bg-white shadow-xl rounded-2xl">
                            <div class="flex items-center mb-6">
                                <div
                                    class="flex items-center justify-center w-12 h-12 mr-4 rounded-xl bg-gradient-to-br from-amber-400 to-orange-600">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ __('mint.post_mint.payment_breakdown') }}
                                </h3>
                            </div>

                            <div class="overflow-hidden border border-gray-200 rounded-xl">
                                <table class="w-full">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                                {{ __('mint.post_mint.recipient') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                                {{ __('mint.post_mint.role') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-xs font-bold tracking-wider text-right text-gray-700 uppercase">
                                                {{ __('mint.post_mint.amount') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($paymentBreakdown as $payment)
                                            <tr class="transition-colors hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                    {{ $payment['recipient_name'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                        {{ ucfirst($payment['role']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm font-bold text-right text-gray-900">
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
                    <div class="p-6 bg-white shadow-xl rounded-2xl">
                        <div class="flex items-center mb-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
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
                                    <details class="mt-3 group" id="coaPdfThumbSection-{{ $certificate->id }}" open>
                                        <summary
                                            class="mb-3 text-sm font-semibold text-indigo-700 cursor-pointer hover:text-indigo-900">
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
                                            <svg class="w-12 h-12 text-indigo-600 animate-spin"
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

                                    <p class="mt-2 text-xs text-center text-gray-500">
                                        {{ __('mint.post_mint.click_to_view') }}
                                    </p>
                                @else
                                    {{-- PDF non ancora generato --}}
                                    <div
                                        class="flex items-center justify-center border-2 border-gray-300 h-96 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none"
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
                                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-white transition-all shadow-lg cursor-pointer rounded-xl bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
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
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
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
                                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-indigo-700 transition-all border-2 border-indigo-600 rounded-xl hover:bg-indigo-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    {{ __('mint.post_mint.view_certificate') }}
                                </a>

                                @if ($isOwner)
                                    <a href="{{ route('my-certificates') }}"
                                        class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-gray-700 transition-all bg-gray-100 rounded-xl hover:bg-gray-200">
                                        {{ __('mint.post_mint.my_certificates') }}
                                    </a>
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

                // --- Certificate Regeneration Handler ---
                document.getElementById('regenerate-cert-btn')?.addEventListener('click', async function(e) {
                    e.preventDefault();

                    const btn = this;
                    const btnText = document.getElementById('regenerate-btn-text');
                    const form = document.getElementById('regenerate-cert-form');

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
                            btnText.textContent = '✅ Rigenerato!';

                            // Reload PDF thumbnail - use the actual container ID
                            const thumbnailContainer = document.querySelector('#coaPdfPreview-{{ $certificate->id }}');
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
                            throw new Error(data.message || 'Regeneration failed');
                        }
                    } catch (error) {
                        console.error('Certificate regeneration error:', error);
                        btnText.textContent = '❌ Errore';
                        setTimeout(() => {
                            btnText.textContent = '{{ __('mint.post_mint.regenerate_certificate') }}';
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }, 2000);
                    }
                });
            </script>
        @endpush
    @endonce

</x-platform-layout>
```
