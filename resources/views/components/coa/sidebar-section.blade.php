{{--
    CoA Sidebar Section (Compact)
    Purpose: Compact CoA section for sidebar integration
    Privacy: GDPR-compliant CoA display
    Location: Include in egis/show.blade.php sidebar

    @param App\Models\Egi $egi
    @param bool $isCreator
--}}

@php
    // Get existing CoA certificate for this EGI (with annexes count)
    $existingCoa = $egi->coa()->with('annexes')->first();
    $hasActiveCoa = $existingCoa && $existingCoa->status === 'valid';
    $canIssueCoa = $isCreator && !$hasActiveCoa;
    $canManageCoa = $isCreator && $hasActiveCoa;
    $annexesCount = $existingCoa ? $existingCoa->annexes->count() : 0;
@endphp

{{-- CoA Compact Section --}}
<div class="space-y-3 rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-900/20 to-yellow-900/20 p-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="flex items-center text-sm font-bold text-white">
            @if ($hasActiveCoa)
                <div class="mr-2 flex h-4 w-4 items-center justify-center rounded-full bg-amber-400">
                    <svg class="h-2.5 w-2.5 text-amber-900" fill="currentColor" viewBox="0 0 8 8">
                        <path
                            d="m2.3 6.73 3.53-4.24c.049-.06.146-.06.195 0L9.6 6.73a.116.116 0 0 1-.096.17H2.4a.116.116 0 0 1-.1-.17Z" />
                    </svg>
                </div>
            @else
                <div class="mr-2 h-4 w-4 rounded-full bg-gray-600"></div>
            @endif
            {{ __('egi.coa.certificate') }}
        </h3>

        @if ($hasActiveCoa)
            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-800">{{ __('egi.coa.active') }}</span>
        @else
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ __('egi.coa.none') }}</span>
        @endif
    </div>

    @if ($hasActiveCoa)
        {{-- Active Certificate Info --}}
        <div class="space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-gray-300">{{ __('egi.coa.serial') }}:</span>
                <span class="font-mono text-white">{{ Str::limit($existingCoa->serial, 12) }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-gray-300">{{ __('egi.coa.issued') }}:</span>
                <span class="text-white">{{ $existingCoa->issued_at?->format('M Y') }}</span>
            </div>
            @if ($annexesCount > 0)
                <div class="flex justify-between text-xs">
                    <span class="text-gray-300">{{ __('egi.coa.pro_features') }}:</span>
                    <span class="font-medium text-amber-400">{{ $annexesCount }} {{ __('egi.coa.annexes') }}</span>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="flex space-x-2">
            @if ($existingCoa->verification_hash)
                <a href="{{ route('coa.verify.view', $existingCoa->verification_hash) }}" target="_blank"
                    class="flex-1 rounded bg-amber-500 px-2 py-1.5 text-center text-xs font-medium text-amber-900 transition-colors hover:bg-amber-600">
                    {{ __('egi.coa.view') }}
                </a>
            @else
                <a href="{{ route('coa.verify.certificate.view', $existingCoa->serial) }}" target="_blank"
                    class="flex-1 rounded bg-amber-500 px-2 py-1.5 text-center text-xs font-medium text-amber-900 transition-colors hover:bg-amber-600">
                    {{ __('egi.coa.view') }}
                </a>
            @endif
            <a href="{{ route('coa.pdf.download', $existingCoa) }}" target="_blank"
                onclick="return downloadCoaPdf(event, {{ $existingCoa->id }}, '{{ route('coa.pdf.download', $existingCoa) }}')"
                class="flex-1 rounded bg-gradient-to-r from-red-600 to-red-700 px-2 py-1.5 text-center text-xs font-medium text-white shadow-sm transition-all duration-200 hover:from-red-700 hover:to-red-800">
                <svg class="mr-1 inline h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" />
                    <path d="M7 10V8h2v2h2l-3 3-3-3h2z" />
                </svg>
                {{ __('egi.coa.pdf') }}
            </a>
        </div>

        {{-- PDF Thumbnail Preview (collapsible + small thumb) --}}
        <details class="group mt-3" id="coaPdfThumbSection-{{ $existingCoa->id }}">
            <summary
                class="flex cursor-pointer select-none items-center justify-between text-xs text-gray-300 hover:text-white">
                <span>Anteprima PDF</span>
                <svg class="h-3 w-3 transform transition-transform group-open:rotate-180" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="mt-2 flex justify-center">
                <div id="coaPdfPreview-{{ $existingCoa->id }}" data-coa-id="{{ $existingCoa->id }}"
                    data-thumb-width="140"
                    class="relative flex aspect-[3/4] w-[140px] cursor-pointer items-center justify-center overflow-hidden rounded border border-amber-500/20 bg-gray-900">
                    <div class="absolute inset-0 animate-pulse bg-gradient-to-br from-gray-800/50 to-gray-900/50"></div>
                    <svg class="relative h-6 w-6 text-amber-300" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-center">
                <a href="{{ route('coa.pdf.download', $existingCoa) }}" target="_blank"
                    class="text-xs text-amber-400 hover:text-amber-300">
                    {{ __('egi.coa.pdf') }} →
                </a>
            </div>
        </details>

        @if ($canManageCoa)
            {{-- Management Actions --}}
            <details class="group">
                <summary
                    class="flex cursor-pointer items-center justify-between text-xs text-gray-300 transition-colors hover:text-white">
                    <span>{{ __('egi.coa.manage_coa') }}</span>
                    <svg class="h-3 w-3 transform transition-transform group-open:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <div class="mt-2 space-y-1">
                    <button onclick="openCoaAnnexModal({{ $existingCoa->id }})"
                        class="w-full rounded bg-blue-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-blue-700">
                        {{ __('egi.coa.add_annex') }}
                    </button>
                    <button onclick="reissueCoaCertificate({{ $existingCoa->id }})"
                        class="w-full rounded bg-purple-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-purple-700">
                        {{ __('egi.coa.reissue') }}
                    </button>
                </div>
            </details>
        @endif
    @else
        {{-- No Certificate --}}
        <div class="space-y-2 text-center">
            <p class="text-xs text-gray-300">
                {{ __('egi.coa.no_certificate_issued') }}
            </p>

            @if ($canIssueCoa)
                <button onclick="issueCoaCertificate({{ $egi->id }})"
                    class="w-full rounded bg-green-500 px-3 py-2 font-bold text-white transition-colors hover:bg-green-600">
                    <svg class="mr-1 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('egi.coa.issue_certificate') }}
                </button>

                <div class="rounded border border-blue-500/20 bg-blue-900/30 p-2">
                    <p class="mb-1 text-xs font-medium text-blue-300">{{ __('egi.coa.unlock_pro_features') }}:</p>
                    <ul class="space-y-0.5 text-xs text-gray-300">
                        <li>• {{ __('egi.coa.provenance_docs') }}</li>
                        <li>• {{ __('egi.coa.condition_reports') }}</li>
                        <li>• {{ __('egi.coa.exhibition_history') }}</li>
                        <li>• {{ __('egi.coa.professional_pdf') }}</li>
                    </ul>
                </div>
            @else
                <p class="text-xs text-gray-400">
                    {{ __('egi.coa.only_creator_can_issue') }}
                </p>
            @endif
        </div>
    @endif

    {{-- Public Verification Link --}}
    <div class="border-t border-amber-500/20 pt-2">
        <a href="{{ route('coa.verify.page') }}" target="_blank"
            class="block text-center text-xs text-amber-400 transition-colors hover:text-amber-300">
            {{ __('egi.coa.verify_any_certificate') }} →
        </a>
    </div>
</div>

{{-- Include CoA functionality scripts --}}
@once
    @push('scripts')
        <script>
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

            async function renderCoaPdfThumb(container, coaId) {
                try {
                    // 1) Check existing PDF and get URL
                    const res = await fetch(`/coa/${coaId}/pdf/check`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const info = await res.json();
                    if (!info || !info.pdf_exists || !info.download_url) {
                        container.innerHTML =
                            `<div class="p-3 text-xs text-center text-gray-400">{{ __('egi.coa.generating_pdf') }}</div>`;
                        return;
                    }

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
                        `<div class="p-3 text-xs text-center text-red-300">{{ __('egi.coa.unexpected_error') }}</div>`;
                }
            }

            // Observe and render when visible
            document.addEventListener('DOMContentLoaded', () => {
                const el = document.getElementById('coaPdfPreview-{{ $existingCoa->id ?? 'none' }}');
                const section = document.getElementById('coaPdfThumbSection-{{ $existingCoa->id ?? 'none' }}');
                if (!el || !section) return;
                const coaId = el.getAttribute('data-coa-id');
                if (!coaId) return;

                // Render on first open
                let rendered = false;
                section.addEventListener('toggle', () => {
                    if (section.open && !rendered) {
                        rendered = true;
                        renderCoaPdfThumb(el, coaId);
                    }
                });

                // Also render if already open and becomes visible
                if (section.open) {
                    renderCoaPdfThumb(el, coaId);
                    rendered = true;
                }
            });

            function showCoaToast(opts) {
                const {
                    message,
                    actionText,
                    actionUrl,
                    type = 'success',
                    timeout = 5000
                } = opts || {};
                const base = document.createElement('div');
                base.className =
                    `fixed bottom-4 right-4 z-50 max-w-sm w-[360px] shadow-lg rounded-lg border ${type === 'success' ? 'bg-emerald-900/80 border-emerald-700 text-emerald-50' : 'bg-red-900/80 border-red-700 text-red-50'} backdrop-blur`;
                base.innerHTML = `
                    <div class="flex items-start p-4 space-x-3">
                        <div class="shrink-0 mt-0.5">
                            <svg class="h-5 w-5 ${type === 'success' ? 'text-emerald-300' : 'text-red-300'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"/>
                            </svg>
                        </div>
                        <div class="flex-1 text-sm leading-5">${message || ''}</div>
                        <div class="flex items-center space-x-2 shrink-0">
                            ${actionText && actionUrl ? `<a href="${actionUrl}" target="_blank" class="px-2 py-1 text-xs font-medium rounded bg-white/10 hover:bg-white/20">${actionText}</a>` : ''}
                            <button type="button" aria-label="Close" class="p-1 rounded hover:bg-white/10" onclick="this.closest('[role=alert]').remove()">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </div>
                `;
                base.setAttribute('role', 'alert');
                document.body.appendChild(base);
                if (timeout > 0) {
                    setTimeout(() => {
                        try {
                            base.remove();
                        } catch (e) {}
                    }, timeout);
                }
            }

            // CoA Certificate Management Functions
            function issueCoaCertificate(egiId) {
                if (!confirm(@json(__('coa_traits.issue_certificate_confirm')))) {
                    return;
                }

                // Show loading state with progress steps
                const button = event.target;
                const originalText = button.innerHTML;

                // ✅ NEW: Enhanced loading with workflow steps
                const loadingSteps = [
                    @json(__('egi.coa.step_creating_certificate')),
                    @json(__('egi.coa.step_generating_snapshot')),
                    @json(__('egi.coa.step_generating_pdf')),
                    @json(__('egi.coa.step_finalizing'))
                ];

                let currentStep = 0;
                button.innerHTML =
                    `<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${loadingSteps[0]}`;
                button.disabled = true;

                // ✅ NEW: Simulate workflow progress steps
                const progressInterval = setInterval(() => {
                    currentStep++;
                    if (currentStep < loadingSteps.length) {
                        button.innerHTML =
                            `<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${loadingSteps[currentStep]}`;
                    } else {
                        clearInterval(progressInterval);
                    }
                }, 1500); // Change step every 1.5 seconds

                fetch(`{{ route('coa.issue') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            egi_id: egiId,
                            auto_generate_pdf: true, // ✅ NEW: Request auto PDF generation
                            enable_signature: false, // Future: QES integration toggle
                            notification_email: true // Future: Email notification toggle
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const msg = @json(__('egi.coa.certificate_issued_successfully'));
                            const pdfGenerated = !!(data && data.data && ((data.data.pdf && data.data.pdf.generated) || data
                                .data.pdf_generated === true));
                            const downloadUrl = data && data.data ? ((data.data.pdf && data.data.pdf.download_url) || data
                                .data.pdf_url) : null;

                            if (pdfGenerated && downloadUrl) {
                                showCoaToast({
                                    message: @json(__('egi.coa.pdf_generated_automatically')),
                                    actionText: @json(__('egi.coa.download_pdf_now')),
                                    actionUrl: downloadUrl,
                                    type: 'success',
                                    timeout: 6000
                                });
                            } else {
                                showCoaToast({
                                    message: msg,
                                    type: 'success'
                                });
                            }

                            setTimeout(() => {
                                window.location.reload();
                            }, 1800);
                        } else {
                            alert(@json(__('egi.coa.error_issuing_certificate')) + (data.message || @json(__('egi.coa.unknown_error'))));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(@json(__('egi.coa.error_issuing_certificate')));
                    })
                    .finally(() => {
                        // ✅ NEW: Clear progress interval and restore button
                        if (typeof progressInterval !== 'undefined') {
                            clearInterval(progressInterval);
                        }
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
            }

            // ✅ NEW: Enhanced PDF download with auto-generation fallback
            function downloadCoaPdf(e, coaId, directUrl) {
                e.preventDefault();
                const el = e.currentTarget;
                const originalHtml = el.innerHTML;
                el.innerHTML =
                    '<svg class="inline w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' +
                    @json(__('egi.coa.generating'));

                fetch(`/coa/${coaId}/pdf/check`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.pdf_exists) {
                            const url = directUrl || data.download_url;
                            if (url) window.open(url, '_blank');
                        } else {
                            showCoaToast({
                                message: @json(__('egi.coa.generating_pdf')) + ' ' + @json(__('egi.coa.retry')),
                                type: 'success',
                                timeout: 4000
                            });
                        }
                    })
                    .catch(err => {
                        console.error('PDF check error:', err);
                        showCoaToast({
                            message: @json(__('egi.coa.unexpected_error')),
                            type: 'error'
                        });
                    })
                    .finally(() => {
                        el.innerHTML = originalHtml;
                    });

                return false;
            }

            function downloadCoaBundle(coaId) {
                const link = document.createElement('a');
                link.href = `/coa/${coaId}/bundle`;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function openCoaAnnexModal(coaId) {
                if (typeof currentCoaId === 'undefined') {
                    alert(@json(__('egi.coa.annex_coming_soon')));
                    return;
                }

                currentCoaId = coaId;
                document.getElementById('coaAnnexModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function reissueCoaCertificate(coaId) {
                if (!confirm(@json(__('egi.coa.reissue_certificate_confirm')))) {
                    return;
                }

                fetch(`/coa/${coaId}/reissue`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(@json(__('egi.coa.certificate_reissued_successfully')));
                            window.location.reload();
                        } else {
                            alert(@json(__('egi.coa.error_reissuing_certificate')) + (data.message || @json(__('egi.coa.unknown_error'))));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(@json(__('egi.coa.error_reissuing_certificate')));
                    });
            }
        </script>
    @endpush
@endonce
