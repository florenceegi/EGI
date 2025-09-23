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
                class="flex-1 rounded bg-gradient-to-r from-red-600 to-red-700 px-2 py-1.5 text-center text-xs font-medium text-white shadow-sm transition-all duration-200 hover:from-red-700 hover:to-red-800">
                <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" />
                    <path d="M7 10V8h2v2h2l-3 3-3-3h2z" />
                </svg>
                {{ __('egi.coa.pdf') }}
            </a>
        </div>

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
                            // ✅ NEW: Enhanced success message with PDF status
                            let successMessage = @json(__('egi.coa.certificate_issued_successfully'));

                            if (data.data.pdf && data.data.pdf.generated) {
                                successMessage += '\n\n' + @json(__('egi.coa.pdf_generated_automatically'));

                                // ✅ NEW: Show PDF download option immediately
                                if (confirm(successMessage + '\n\n' + @json(__('egi.coa.download_pdf_now')))) {
                                    if (data.data.pdf.download_url) {
                                        window.open(data.data.pdf.download_url, '_blank');
                                    } else {
                                        // Fallback to bundle generation
                                        downloadCoaBundle(data.data.certificate.id);
                                    }
                                }
                            } else {
                                alert(successMessage);
                            }

                            window.location.reload();
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
            function downloadCoaPdf(e, coaId) {
                const button = e.target.closest('button');
                const originalText = button.innerHTML;

                // Show loading state
                button.innerHTML =
                    '<svg class="inline w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' +
                    @json(__('egi.coa.generating'));
                button.disabled = true;

                // First try to get existing PDF
                fetch(`/coa/${coaId}/pdf/check`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.pdf_exists && data.download_url) {
                            // PDF already exists, download directly
                            window.open(data.download_url, '_blank');
                        } else {
                            // Generate PDF automatically and download
                            button.innerHTML =
                                '<svg class="inline w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' +
                                @json(__('egi.coa.generating_pdf'));

                            return fetch(`/coa/${coaId}/pdf/generate`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    format: 'core',
                                    auto_download: true
                                })
                            });
                        }
                    })
                    .then(response => {
                        if (response && response.ok) {
                            return response.json();
                        }
                    })
                    .then(data => {
                        if (data && data.success && data.download_url) {
                            window.open(data.download_url, '_blank');
                        } else if (data && !data.success) {
                            // Fallback to legacy bundle system
                            console.warn('PDF generation failed, falling back to bundle system');
                            downloadCoaBundle(coaId);
                        }
                    })
                    .catch(error => {
                        console.error('PDF download error:', error);
                        // Fallback to legacy bundle system
                        downloadCoaBundle(coaId);
                    })
                    .finally(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
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
