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

    // Suggested location prefill: prefer CoA.location, otherwise derive from user's personal data
$pd = optional(optional($egi->user)->personalData);
$parts = [];
if (!empty($pd->city)) {
    $parts[] = $pd->city;
}
if (!empty($pd->region) && $pd->region !== $pd->city) {
    $parts[] = $pd->region;
}
if (!empty($pd->country)) {
    $parts[] = $pd->country;
}
$userDerivedLocation = implode(', ', array_filter($parts));
    $suggestedLocation = $existingCoa && !empty($existingCoa->location) ? $existingCoa->location : $userDerivedLocation;
@endphp

{{-- CoA Compact Section --}}
<div class="p-4 space-y-3 border rounded-xl border-amber-500/30 bg-gradient-to-br from-amber-900/20 to-yellow-900/20">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="flex items-center text-sm font-bold text-white">
            @if ($hasActiveCoa)
                <div class="flex items-center justify-center w-4 h-4 mr-2 rounded-full bg-amber-400">
                    <svg class="h-2.5 w-2.5 text-amber-900" fill="currentColor" viewBox="0 0 8 8">
                        <path
                            d="m2.3 6.73 3.53-4.24c.049-.06.146-.06.195 0L9.6 6.73a.116.116 0 0 1-.096.17H2.4a.116.116 0 0 1-.1-.17Z" />
                    </svg>
                </div>
            @else
                <div class="w-4 h-4 mr-2 bg-gray-600 rounded-full"></div>
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
                <span class="font-mono text-white">{{ \Illuminate\Support\Str::limit($existingCoa->serial, 12) }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-gray-300">{{ __('egi.coa.issued') }}:</span>
                <span class="text-white">{{ $existingCoa->issued_at?->format('M Y') }}</span>
            </div>
            {{-- Signature & Integrity Badges (no PII) --}}
            @php
                $signMeta = (array) data_get($existingCoa, 'metadata.signatures', []);
                $hasAuthorSig =
                    collect($signMeta)->firstWhere('role', 'creator') &&
                    (collect($signMeta)->firstWhere('role', 'creator')['status'] ?? 'valid') !== 'invalid';
                $hasInspectorSig =
                    collect($signMeta)->firstWhere('role', 'inspector') && config('coa.signature.inspector.enabled');
                $tsaEnabled = (bool) config('coa.signature.tsa.enabled');
                $hasTsa = $tsaEnabled && (bool) data_get($existingCoa, 'metadata.signatures.0.timestamp');
                $hasIntegrityHash = !empty($existingCoa->verification_hash);
            @endphp
            <div class="flex flex-wrap gap-1.5 pt-1">
                @if ($hasAuthorSig)
                    <span
                        class="inline-flex items-center rounded-full bg-emerald-100/90 px-2 py-0.5 text-[10px] font-semibold text-emerald-800">
                        {{ __('egi.coa.badge_author_signed') }}
                    </span>
                @endif
                @if ($hasInspectorSig)
                    <span
                        class="inline-flex items-center rounded-full bg-indigo-100/90 px-2 py-0.5 text-[10px] font-semibold text-indigo-800">
                        {{ __('egi.coa.badge_inspector_signed') }}
                    </span>
                @endif
                @if ($hasTsa)
                    <span
                        class="inline-flex items-center rounded-full bg-amber-100/90 px-2 py-0.5 text-[10px] font-semibold text-amber-800">
                        {{ __('egi.coa.badge_timestamped') }}
                    </span>
                @endif
                @if ($hasIntegrityHash)
                    <span
                        class="inline-flex items-center rounded-full bg-sky-100/90 px-2 py-0.5 text-[10px] font-semibold text-sky-800">
                        {{ __('egi.coa.badge_integrity_ok') }}
                    </span>
                @endif
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
                onclick="return downloadCoaPdf(event, '{{ $existingCoa->id }}', '{{ route('coa.pdf.download', $existingCoa) }}')"
                class="flex-1 rounded bg-gradient-to-r from-red-600 to-red-700 px-2 py-1.5 text-center text-xs font-medium text-white shadow-sm transition-all duration-200 hover:from-red-700 hover:to-red-800">
                <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" />
                    <path d="M7 10V8h2v2h2l-3 3-3-3h2z" />
                </svg>
                {{ __('egi.coa.pdf') }}
            </a>
        </div>

        {{-- PDF Thumbnail Preview (collapsible + small thumb) --}}
        <details class="mt-3 group" id="coaPdfThumbSection-{{ $existingCoa->id }}">
            <summary
                class="flex items-center justify-between text-xs text-gray-300 cursor-pointer select-none hover:text-white">
                <span>Anteprima PDF</span>
                <svg class="w-3 h-3 transition-transform transform group-open:rotate-180" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="flex justify-center mt-2">
                <div id="coaPdfPreview-{{ $existingCoa->id }}" data-coa-id="{{ $existingCoa->id }}"
                    data-thumb-width="140"
                    class="relative flex aspect-[3/4] w-[140px] cursor-pointer items-center justify-center overflow-hidden rounded border border-amber-500/20 bg-gray-900">
                    <div class="absolute inset-0 animate-pulse bg-gradient-to-br from-gray-800/50 to-gray-900/50"></div>
                    <svg class="relative w-6 h-6 text-amber-300" fill="none" viewBox="0 0 24 24"
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

        {{-- Inspector Signature Button (visible to inspectors) --}}
        @if (
            $hasActiveCoa &&
                config('coa.signature.inspector.enabled') &&
                auth()->check() &&
                auth()->user()->hasRole('inspector') &&
                auth()->user()->can('sign_coa'))
            <button onclick="countersignInspector('{{ $existingCoa->id }}')"
                class="w-full rounded bg-indigo-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-indigo-700">
                {{ __('egi.coa.inspector_countersign') }}
            </button>
        @endif

        {{-- Signature Removal Buttons (visible to owner/admin) --}}
        @if ($hasActiveCoa && auth()->check() && ($isCreator || auth()->user()->hasRole('admin')))
            @php
                $signatures = (array) data_get($existingCoa, 'metadata.signatures', []);
                $hasCreatorSig = collect($signatures)->firstWhere('role', 'creator');
                $hasInspectorSig = collect($signatures)->firstWhere('role', 'inspector');
            @endphp

            @if ($hasCreatorSig)
                <button onclick="removeSignature('{{ $existingCoa->id }}', 'creator')"
                    class="w-full rounded bg-red-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-red-700">
                    {{ __('egi.coa.remove_signature') }} (Creator)
                </button>
            @endif

            @if ($hasInspectorSig)
                <button onclick="removeSignature('{{ $existingCoa->id }}', 'inspector')"
                    class="w-full rounded bg-red-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-red-700">
                    {{ __('egi.coa.remove_signature') }} (Inspector)
                </button>
            @endif
        @endif

        @if ($canManageCoa)
            {{-- Management Actions --}}
            <details class="group">
                <summary
                    class="flex items-center justify-between text-xs text-gray-300 transition-colors cursor-pointer hover:text-white">
                    <span>{{ __('egi.coa.manage_coa') }}</span>
                    <svg class="w-3 h-3 transition-transform transform group-open:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <div class="mt-2 space-y-1">
                    <button onclick="openCoaAnnexModal('{{ $existingCoa->id }}')"
                        class="w-full rounded bg-blue-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-blue-700">
                        {{ __('egi.coa.add_annex') }}
                    </button>
                    {{-- <button onclick="reissueCoaCertificate('{{ $existingCoa->id }}')"
                        class="w-full rounded bg-purple-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-purple-700">
                        {{ __('egi.coa.reissue') }}
                    </button> --}}

                    @if (config('coa.signature.enabled'))
                        <button onclick="signAuthor('{{ $existingCoa->id }}')"
                            class="w-full rounded bg-emerald-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-emerald-700">
                            {{ __('egi.coa.author_countersign') }}
                        </button>
                        <button onclick="regenerateCoaPdf('{{ $existingCoa->id }}')"
                            class="w-full rounded bg-amber-600 px-2 py-1.5 text-xs font-medium text-white transition-colors hover:bg-amber-700">
                            {{ __('egi.coa.regenerate_pdf') }}
                        </button>
                    @endif


                    {{-- Location Quick Edit --}}
                    <div class="p-2 mt-2 border rounded border-amber-500/20 bg-amber-900/20">
                        <label class="mb-1 block text-[11px] text-amber-200">{{ __('egi.coa.issue_place') }}</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="coaLocationInput-{{ $existingCoa->id }}"
                                value="{{ $suggestedLocation }}"
                                placeholder="{{ __('egi.coa.location_placeholder') }}"
                                class="flex-1 px-2 py-1 text-xs text-white placeholder-gray-400 bg-gray-800 border border-gray-700 rounded" />
                            <button onclick="saveCoaLocation({{ $existingCoa->id }})"
                                class="px-2 py-1 text-xs font-medium rounded bg-amber-400 text-amber-900 hover:bg-amber-500">{{ __('egi.coa.save') }}</button>
                        </div>
                        <p class="mt-1 text-[10px] text-gray-400">{{ __('egi.coa.location_hint') }}</p>
                    </div>
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
                {{-- Pre-issuance Location (required) --}}
                <div class="p-2 mb-2 border rounded border-amber-500/20 bg-amber-900/20">
                    <label class="mb-1 block text-[11px] text-amber-200">{{ __('egi.coa.issue_place') }}</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="preCoaLocationInput-{{ $egi->id }}"
                            value="{{ $suggestedLocation }}" placeholder="{{ __('egi.coa.location_placeholder') }}"
                            class="flex-1 px-2 py-1 text-xs text-white placeholder-gray-400 bg-gray-800 border border-gray-700 rounded" />
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400">{{ __('egi.coa.location_hint') }}</p>
                </div>

                @if (config('coa.signature.inspector.enabled'))
                    <div class="mb-2 text-left">
                        <label class="inline-flex items-center space-x-2 text-xs text-amber-100">
                            <input type="checkbox" id="preCoaInspectorFlag-{{ $egi->id }}"
                                class="bg-gray-800 border-gray-600 rounded">
                            <span>{{ __('egi.coa.inspector_countersign') }}</span>
                        </label>
                    </div>
                @endif

                <button onclick="issueCoaCertificate('{{ $egi->id }}')"
                    class="w-full px-3 py-2 font-bold text-white transition-colors bg-green-500 rounded hover:bg-green-600">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('egi.coa.issue_certificate') }}
                </button>

                <div class="p-2 border rounded border-blue-500/20 bg-blue-900/30">
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
    <div class="pt-2 border-t border-amber-500/20">
        <a href="{{ route('coa.verify.page') }}" target="_blank"
            class="block text-xs text-center transition-colors text-amber-400 hover:text-amber-300">
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
            // --- Lazy SweetAlert2 loader ---
            let __swalReady = null;

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

            // Regenerate PDF and reapply signatures
            window.regenerateCoaPdf = function(coaId) {
                console.log('regenerateCoaPdf', coaId);
                fetch(`/coa/${coaId}/pdf/regenerate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            const url = (data.download_url || (data.data && data.data.download_url));
                            showCoaToast({
                                message: @json(__('egi.coa.pdf_regenerated')),
                                actionText: @json(__('egi.coa.download_pdf_now')),
                                actionUrl: url || (`/coa/${coaId}/pdf/download`),
                                type: 'success',
                                timeout: 5000
                            });
                            if (url) {
                                try {
                                    window.open(url, '_blank');
                                } catch (e) {}
                            }
                            setTimeout(() => window.location.reload(), 1800);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('egi.coa.pdf_regenerate_failed')),
                                text: (data && data.message) || I18N.unknown_error
                            });
                        }
                    })
                    .catch(() => Swal.fire({
                        icon: 'error',
                        title: @json(__('egi.coa.pdf_regenerate_failed'))
                    }));
            }

            function ensureSwalLoaded() {
                if (window['Swal']) return Promise.resolve();
                if (__swalReady) return __swalReady;
                __swalReady = new Promise((resolve, reject) => {
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
                    s.async = true;
                    s.onload = () => resolve();
                    s.onerror = reject;
                    document.head.appendChild(s);
                });
                return __swalReady;
            }

            // Centralized i18n for JS strings
            const I18N = {
                generating_pdf: @json(__('egi.coa.generating_pdf')),
                unexpected_error: @json(__('egi.coa.unexpected_error')),
                generating: @json(__('egi.coa.generating')),
                retry: @json(__('egi.coa.retry')),
                annex_coming_soon: @json(__('egi.coa.annex_coming_soon')),
                reissue_confirm: @json(__('egi.coa.reissue_certificate_confirm')),
                reissued_ok: @json(__('egi.coa.certificate_reissued_successfully')),
                error_reissuing: @json(__('egi.coa.error_reissuing_certificate')),
                error_issuing: @json(__('egi.coa.error_issuing_certificate')),
                unknown_error: @json(__('egi.coa.unknown_error')),
                issue_confirm: @json(__('coa_traits.issue_certificate_confirm')),
                step_creating_certificate: @json(__('egi.coa.step_creating_certificate')),
                step_generating_snapshot: @json(__('egi.coa.step_generating_snapshot')),
                step_generating_pdf: @json(__('egi.coa.step_generating_pdf')),
                step_finalizing: @json(__('egi.coa.step_finalizing')),
                pdf_generated_automatically: @json(__('egi.coa.pdf_generated_automatically')),
                download_pdf_now: @json(__('egi.coa.download_pdf_now')),
                certificate_issued_successfully: @json(__('egi.coa.certificate_issued_successfully')),
                confirm: @json(__('coa_traits.confirm')),
                cancel: @json(__('coa_traits.cancel')),
                location_required: @json(__('egi.coa.location_required')),
                inspector_countersign: @json(__('egi.coa.inspector_countersign')),
                confirm_inspector_countersign: @json(__('egi.coa.confirm_inspector_countersign')),
                inspector_countersign_applied: @json(__('egi.coa.inspector_countersign_applied')),
                operation_failed: @json(__('egi.coa.operation_failed')),
                remove_signature: @json(__('egi.coa.remove_signature')),
                confirm_remove_signature: @json(__('egi.coa.confirm_remove_signature')),
                signature_removed: @json(__('egi.coa.signature_removed')),
                signature_removal_failed: @json(__('egi.coa.signature_removal_failed')),
                signature_removal_warning: @json(__('egi.coa.signature_removal_warning'))
            };

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
                            `<div class="p-3 text-xs text-center text-gray-400">${I18N.generating_pdf}</div>`;
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
                        `<div class="p-3 text-xs text-center text-red-300">${I18N.unexpected_error}</div>`;
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
                        try {
                            showCoaToast({
                                message: I18N.generating_pdf,
                                type: 'info',
                                timeout: 3000
                            });
                        } catch (e) {}
                        renderCoaPdfThumb(el, coaId);
                    }
                });

                // Also render if already open and becomes visible
                if (section.open) {
                    try {
                        showCoaToast({
                            message: I18N.generating_pdf,
                            type: 'info',
                            timeout: 3000
                        });
                    } catch (e) {}
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
                const styles = {
                    success: {
                        wrap: 'bg-emerald-900/80 border-emerald-700 text-emerald-50',
                        icon: 'text-emerald-300',
                        path: 'M5 13l4 4L19 7'
                    },
                    error: {
                        wrap: 'bg-red-900/80 border-red-700 text-red-50',
                        icon: 'text-red-300',
                        path: 'M6 18L18 6M6 6l12 12'
                    },
                    info: {
                        wrap: 'bg-sky-900/80 border-sky-700 text-sky-50',
                        icon: 'text-sky-300',
                        path: 'M13 16h-1v-4h-1m1-4h.01'
                    },
                    warning: {
                        wrap: 'bg-amber-900/80 border-amber-700 text-amber-50',
                        icon: 'text-amber-300',
                        path: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'
                    }
                };
                const s = styles[type] || styles.success;
                const base = document.createElement('div');
                base.className =
                    `fixed bottom-4 right-4 z-50 max-w-sm w-[360px] shadow-lg rounded-lg border ${s.wrap} backdrop-blur`;
                base.innerHTML = `
                    <div class="flex items-start p-4 space-x-3">
                        <div class="shrink-0 mt-0.5">
                            <svg class="h-5 w-5 ${s.icon}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.path}"/>
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
            window.issueCoaCertificate = async function(egiId, ev) {
                await ensureSwalLoaded();
                const conf = await Swal.fire({
                    icon: 'question',
                    title: I18N.issue_confirm,
                    showCancelButton: true,
                    confirmButtonText: I18N.confirm,
                    cancelButtonText: I18N.cancel,
                });
                if (!conf.isConfirmed) return;

                // Show loading state with progress steps
                const evt = ev || window.event;
                const button = evt && (evt.currentTarget || evt.target);
                const originalText = button ? button.innerHTML : '';

                // ✅ NEW: Enhanced loading with workflow steps
                const loadingSteps = [
                    I18N.step_creating_certificate,
                    I18N.step_generating_snapshot,
                    I18N.step_generating_pdf,
                    I18N.step_finalizing
                ];

                let currentStep = 0;
                if (button) {
                    button.innerHTML =
                        `<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${loadingSteps[0]}`;
                    button.disabled = true;
                }

                // ✅ NEW: Simulate workflow progress steps
                const progressInterval = setInterval(() => {
                    currentStep++;
                    if (currentStep < loadingSteps.length) {
                        if (button) {
                            button.innerHTML =
                                `<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>${loadingSteps[currentStep]}`;
                        }
                    } else {
                        clearInterval(progressInterval);
                    }
                }, 1500); // Change step every 1.5 seconds

                // Collect pre-issuance location if present (required)
                const preLocInput = document.getElementById(`preCoaLocationInput-${egiId}`);
                const payload = {
                    egi_id: egiId,
                    auto_generate_pdf: true,
                    enable_signature: false,
                    notification_email: true
                };
                if (preLocInput) {
                    const locVal = (preLocInput.value || '').trim();
                    if (!locVal) {
                        await Swal.fire({
                            icon: 'warning',
                            title: I18N.location_required
                        });
                        // Restore button state before returning
                        if (typeof progressInterval !== 'undefined') clearInterval(progressInterval);
                        if (button) {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                        return;
                    }
                    payload.location = locVal;
                }
                // Optional inspector countersign request
                const inspectorFlag = document.getElementById(`preCoaInspectorFlag-${egiId}`);
                if (inspectorFlag && inspectorFlag.checked) {
                    payload['request_inspector_countersign'] = true;
                }

                fetch(`{{ route('coa.issue') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(async data => {
                        if (data.success) {
                            const msg = @json(__('egi.coa.certificate_issued_successfully'));
                            const pdfGenerated = !!(data && data.data && ((data.data.pdf && data.data.pdf
                                    .generated) || data
                                .data.pdf_generated === true));
                            const downloadUrl = data && data.data ? ((data.data.pdf && data.data.pdf
                                    .download_url) || data
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
                            await ensureSwalLoaded();
                            Swal.fire({
                                icon: 'error',
                                title: I18N.error_issuing,
                                text: (data.message || I18N.unknown_error)
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ensureSwalLoaded().then(() => Swal.fire({
                            icon: 'error',
                            title: I18N.error_issuing
                        }));
                    })
                    .finally(() => {
                        // ✅ NEW: Clear progress interval and restore button
                        if (typeof progressInterval !== 'undefined') {
                            clearInterval(progressInterval);
                        }
                        if (button) {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    });
            }

            // ✅ NEW: Enhanced PDF download with auto-generation fallback
            window.downloadCoaPdf = function(e, coaId, directUrl) {
                e.preventDefault();
                const el = e.currentTarget;
                const originalHtml = el.innerHTML;
                el.innerHTML =
                    '<svg class="inline w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' +
                    I18N.generating;

                // Se abbiamo già un URL diretto, apri subito e ripristina UI
                if (directUrl) {
                    try {
                        window.open(directUrl, '_blank');
                    } catch (_) {}
                    el.innerHTML = originalHtml;
                    return false;
                }

                fetch(`/coa/${coaId}/pdf/check`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(async (r) => {
                        const ct = r.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            return r.json();
                        }
                        // Se non è JSON, non tentare il parse: mostra errore generico
                        throw new Error('Unexpected content-type for /pdf/check: ' + ct);
                    })
                    .then(data => {
                        if (data && data.pdf_exists) {
                            const url = data.download_url || `/coa/${coaId}/pdf/download`;
                            if (url) window.open(url, '_blank');
                        } else {
                            showCoaToast({
                                message: I18N.generating_pdf + ' ' + I18N.retry,
                                type: 'info',
                                timeout: 5000
                            });
                        }
                    })
                    .catch(err => {
                        console.error('PDF check error:', err);
                        showCoaToast({
                            message: I18N.unexpected_error,
                            type: 'error'
                        });
                    })
                    .finally(() => {
                        el.innerHTML = originalHtml;
                    });

                return false;
            }

            // Save CoA Location
            window.saveCoaLocation = async function(coaId) {
                try {
                    await ensureSwalLoaded();
                    const input = document.getElementById(`coaLocationInput-${coaId}`);
                    const value = (input?.value || '').trim();
                    if (!value) {
                        await Swal.fire({
                            icon: 'warning',
                            title: @json(__('egi.coa.location_required'))
                        });
                        return;
                    }
                    const res = await fetch(`/coa/${coaId}/location`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            location: value
                        })
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        showCoaToast({
                            message: @json(__('egi.coa.location_saved')),
                            type: 'success',
                            timeout: 3000
                        });
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: @json(__('egi.coa.location_save_failed')),
                            text: data?.message || I18N.unexpected_error
                        });
                    }
                } catch (e) {
                    await ensureSwalLoaded();
                    await Swal.fire({
                        icon: 'error',
                        title: @json(__('egi.coa.location_save_failed'))
                    });
                }
            }

            function downloadCoaBundle(coaId) {
                const link = document.createElement('a');
                link.href = `/coa/${coaId}/bundle`;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            window.openCoaAnnexModal = function(coaId) {
                if (typeof currentCoaId === 'undefined') {
                    showCoaToast({
                        message: I18N.annex_coming_soon,
                        type: 'success',
                        timeout: 3000
                    });
                    return;
                }

                currentCoaId = coaId;
                document.getElementById('coaAnnexModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            window.reissueCoaCertificate = async function(coaId) {
                await ensureSwalLoaded();
                const conf = await Swal.fire({
                    icon: 'question',
                    title: I18N.reissue_confirm,
                    showCancelButton: true,
                    confirmButtonText: I18N.confirm,
                    cancelButtonText: I18N.cancel,
                });
                if (!conf.isConfirmed) return;

                fetch(`/coa/${coaId}/reissue`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showCoaToast({
                                message: I18N.reissued_ok,
                                type: 'success',
                                timeout: 3000
                            });
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: I18N.error_reissuing,
                                text: (data.message || I18N.unknown_error)
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: I18N.error_reissuing
                        });
                    });
            }

            // Author signature
            window.signAuthor = async function(coaId) {
                console.log('signAuthor', coaId);
                await ensureSwalLoaded();
                const conf = await Swal.fire({
                    icon: 'question',
                    title: I18N.confirm,
                    text: @json(__('egi.coa.confirm_author_countersign')),
                    showCancelButton: true,
                    confirmButtonText: I18N.confirm,
                    cancelButtonText: I18N.cancel,
                });
                if (!conf.isConfirmed) return;

                try {
                    const res = await fetch(`/coa/${coaId}/sign/author`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({})
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        const hash = (data.data && data.data.pdf_sha256) ? (data.data.pdf_sha256.substring(0, 8)) : '';
                        showCoaToast({
                            message: @json(__('egi.coa.author_countersign_applied')) + (hash ? ` [${hash}]` : '') + ' - ' + @json(__('egi.coa.regenerating_pdf')),
                            type: 'success',
                            timeout: 3000
                        });

                        // ✅ REFACTOR: Instead of opening old PDF, regenerate with signature
                        setTimeout(() => {
                            regenerateCoaPdf(coaId);
                        }, 1000);
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: I18N.operation_failed,
                            text: (data && data.message) || I18N.unknown_error
                        });
                    }
                } catch (e) {
                    await Swal.fire({
                        icon: 'error',
                        title: I18N.unknown_error
                    });
                }
            }

            // Inspector countersignature (backend feature-flagged and role-protected)
            window.countersignInspector = async function(coaId) {
                console.log('countersignInspector', coaId);
                await ensureSwalLoaded();
                console.log('ensureSwalLoaded', ensureSwalLoaded);
                const conf = await Swal.fire({
                    icon: 'question',
                    title: I18N.confirm,
                    text: I18N.confirm_inspector_countersign,
                    showCancelButton: true,
                    confirmButtonText: I18N.confirm,
                    cancelButtonText: I18N.cancel,
                });
                if (!conf.isConfirmed) return;

                try {
                    const res = await fetch(`/coa/${coaId}/sign/inspector`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({})
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        showCoaToast({
                            message: (data.message || I18N.inspector_countersign_applied) + ' - ' + @json(__('egi.coa.regenerating_pdf')),
                            type: 'success',
                            timeout: 3000
                        });

                        // ✅ REFACTOR: Regenerate PDF with inspector signature instead of reload
                        setTimeout(() => {
                            regenerateCoaPdf(coaId);
                        }, 1000);
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: I18N.operation_failed,
                            text: (data && data.message) || I18N.unknown_error
                        });
                    }
                } catch (e) {
                    await Swal.fire({
                        icon: 'error',
                        title: I18N.unknown_error
                    });
                }
            }

            // Remove signature function
            window.removeSignature = async function(coaId, role) {
                console.log('removeSignature', coaId, role);
                await ensureSwalLoaded();

                const roleDisplay = role === 'creator' ? 'Creator' : 'Inspector';
                const conf = await Swal.fire({
                    icon: 'warning',
                    title: I18N.confirm,
                    html: `
                        <p>${I18N.confirm_remove_signature.replace('{role}', roleDisplay)}</p>
                        <p class="mt-2 text-sm text-gray-600">${I18N.signature_removal_warning}</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: I18N.confirm,
                    cancelButtonText: I18N.cancel,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280'
                });

                if (!conf.isConfirmed) return;

                try {
                    const res = await fetch(`/coa/${coaId}/sign/${role}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await res.json();

                    if (data && data.success) {
                        showCoaToast({
                            message: I18N.signature_removed.replace('{role}', roleDisplay),
                            type: 'success',
                            timeout: 3500
                        });

                        // Open PDF if download URL is available
                        if (data.data && data.data.download_url) {
                            try {
                                window.open(data.data.download_url, '_blank');
                            } catch (e) {
                                console.warn('Could not open PDF:', e);
                            }
                        }

                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: I18N.signature_removal_failed,
                            text: (data && data.message) || I18N.unknown_error
                        });
                    }
                } catch (e) {
                    await Swal.fire({
                        icon: 'error',
                        title: I18N.signature_removal_failed,
                        text: I18N.unknown_error
                    });
                }
            }
        </script>
    @endpush
@endonce
