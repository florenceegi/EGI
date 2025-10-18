{{-- 
    MINT PAGE - PAGINA 2 (READ-ONLY)
    🎯 Purpose: Mostra dati mint completato (riapribile)
    📍 Route: GET /mint/{egiBlockchainId}
    🔒 Auth: Solo buyer può vedere
--}}
<x-platform-layout :title="__('mint.minted_title', ['title' => $egi->title])">
    <div class="container px-4 py-8 mx-auto max-w-6xl">
        
        {{-- Header Success --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 text-white bg-green-500 rounded-full">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="mb-2 text-3xl font-bold text-gray-900">
                {{ __('mint.post_mint.congratulations') }}
            </h1>
            <p class="text-gray-600">
                {{ __('mint.post_mint.success_message') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            {{-- COLONNA 1: EGI Preview --}}
            <div class="space-y-6">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    @if($egi->utility && $egi->utility->getFirstMediaUrl('utility'))
                        <img src="{{ $egi->utility->getFirstMediaUrl('utility') }}" 
                             alt="{{ $egi->title }}"
                             class="object-cover w-full h-64">
                    @else
                        <div class="flex items-center justify-center w-full h-64 bg-gray-200">
                            <span class="text-4xl text-gray-400">🎨</span>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <h2 class="mb-2 text-lg font-bold text-gray-900">{{ $egi->title }}</h2>
                        <p class="text-sm text-gray-600">
                            {{ __('mint.egi_preview.creator_by', ['name' => $egi->user->name]) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- COLONNA 2: Blockchain Data --}}
            <div class="space-y-6">
                
                {{-- Blockchain Info Card --}}
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        {{ __('mint.post_mint.blockchain_info') }}
                    </h3>
                    
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-700">{{ __('mint.post_mint.asa_id') }}:</dt>
                            <dd class="font-mono text-sm font-bold text-green-900">{{ $blockchain->asa_id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-700">{{ __('mint.post_mint.tx_id') }}:</dt>
                            <dd class="font-mono text-xs text-gray-900 truncate" title="{{ $blockchain->blockchain_tx_id }}">
                                {{ Str::limit($blockchain->blockchain_tx_id, 20) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-700">{{ __('mint.post_mint.minted_at') }}:</dt>
                            <dd class="text-sm text-gray-900">{{ $blockchain->minted_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        <a href="https://explorer.perawallet.app/asset/{{ $blockchain->asa_id }}" 
                           target="_blank"
                           class="inline-flex items-center text-sm font-medium text-blue-600 transition-colors hover:text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            {{ __('mint.post_mint.view_pera_explorer') }}
                        </a>
                    </div>
                </div>

                {{-- Payment Breakdown --}}
                @if($paymentBreakdown && count($paymentBreakdown) > 0)
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        {{ __('mint.post_mint.payment_breakdown') }}
                    </h3>
                    
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-xs font-medium text-left text-gray-700">
                                        {{ __('mint.post_mint.recipient') }}
                                    </th>
                                    <th class="px-3 py-2 text-xs font-medium text-left text-gray-700">
                                        {{ __('mint.post_mint.role') }}
                                    </th>
                                    <th class="px-3 py-2 text-xs font-medium text-right text-gray-700">
                                        {{ __('mint.post_mint.amount') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($paymentBreakdown as $payment)
                                    <tr>
                                        <td class="px-3 py-2 text-xs text-gray-900">
                                            {{ $payment['recipient_name'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-600">
                                            {{ ucfirst($payment['role']) }}
                                        </td>
                                        <td class="px-3 py-2 text-xs font-medium text-right text-gray-900">
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

            {{-- COLONNA 3: Certificate --}}
            <div class="space-y-6">
                
                {{-- Certificate Card --}}
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        {{ __('mint.post_mint.certificate_title') }}
                    </h3>
                    <p class="mb-4 text-sm text-gray-600">
                        {{ __('mint.post_mint.certificate_description') }}
                    </p>

                    {{-- PDF Thumbnail --}}
                    @if($certificate)
                    <div class="mb-4">
                        <div id="certificate-thumbnail-container" 
                             class="overflow-hidden border border-gray-300 rounded-lg cursor-pointer hover:shadow-lg"
                             data-coa-id="{{ $certificate->id }}"
                             data-thumb-width="280"
                             onclick="window.open('{{ $certificate->getPdfUrl() }}', '_blank')">
                            <div class="flex items-center justify-center h-64 bg-gray-100">
                                <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-center text-gray-500">
                            {{ __('mint.post_mint.click_to_view') }}
                        </p>
                    </div>

                    {{-- Download Button --}}
                    <a href="{{ $certificate->getPdfUrl() }}" 
                       download
                       class="flex items-center justify-center w-full px-4 py-2 mb-3 text-sm font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('mint.post_mint.download_certificate') }}
                    </a>

                    {{-- View Certificate Button --}}
                    <a href="{{ route('egi-certificates.show', $certificate->certificate_uuid) }}" 
                       target="_blank"
                       class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 transition-colors border-2 border-blue-600 rounded-md hover:bg-blue-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('mint.post_mint.view_certificate') }}
                    </a>
                    @endif

                </div>

                {{-- Action Buttons --}}
                <div class="p-6 bg-white rounded-lg shadow-lg">
                    <a href="{{ route('egis.show', $egi->id) }}" 
                       class="flex items-center justify-center w-full px-6 py-3 mb-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        {{ __('mint.post_mint.view_egi') }}
                    </a>
                    <a href="{{ route('my-certificates') }}" 
                       class="flex items-center justify-center w-full px-6 py-3 text-blue-600 transition-colors border-2 border-blue-600 rounded-lg hover:bg-blue-50">
                        {{ __('mint.post_mint.my_certificates') }}
                    </a>
                </div>

            </div>
        </div>

    </div>

    {{-- JavaScript for PDF Thumbnail --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script>
        // Configure PDF.js worker
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
        }

        // Render PDF thumbnail
        async function renderCertificateThumbnail() {
            const container = document.getElementById('certificate-thumbnail-container');
            if (!container) return;

            try {
                const coaId = container.dataset.coaId;
                const targetWidth = parseInt(container.dataset.thumbWidth || '280', 10);

                // Check PDF exists
                const res = await fetch(`/coa/${coaId}/pdf/check`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const info = await res.json();

                if (!info || !info.pdf_exists || !info.download_url) {
                    container.innerHTML = '<div class="p-4 text-sm text-center text-gray-500">{{ __("mint.post_mint.generating_pdf") }}</div>';
                    return;
                }

                // Render PDF first page
                const pdf = await pdfjsLib.getDocument(info.download_url).promise;
                const page = await pdf.getPage(1);

                const viewport = page.getViewport({ scale: 1 });
                const scale = targetWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale });

                const canvas = document.createElement('canvas');
                canvas.width = Math.floor(scaledViewport.width);
                canvas.height = Math.floor(scaledViewport.height);
                canvas.style.width = '100%';
                canvas.style.height = 'auto';

                const ctx = canvas.getContext('2d');
                await page.render({
                    canvasContext: ctx,
                    viewport: scaledViewport
                }).promise;

                container.innerHTML = '';
                container.appendChild(canvas);

            } catch (error) {
                console.error('Certificate thumbnail error:', error);
                container.innerHTML = '<div class="p-4 text-sm text-center text-red-500">{{ __("mint.post_mint.thumbnail_error") }}</div>';
            }
        }

        // Render on page load
        document.addEventListener('DOMContentLoaded', renderCertificateThumbnail);
    </script>
    @endpush

</x-platform-layout>
