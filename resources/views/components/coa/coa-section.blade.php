{{--
    CoA (Certificate of Authenticity) Section Component
    🎯 Purpose: Display CoA status and actions for an EGI
    🛡️ Privacy: GDPR-compliant display of certificate information
    📍 Location: Sidebar integration in egis/show.blade.php

    @param App\Models\Egi $egi
    @param bool $isCreator
    @param bool $canUpdate
--}}

@props(['egi', 'isCreator' => false, 'canUpdate' => false])

@php
    // Get existing CoA certificate for this EGI
    $existingCoa = $egi->coa ?? null;
    $hasActiveCoa = $existingCoa && $existingCoa->status === 'valid';
    $canIssueCoa = $isCreator && !$hasActiveCoa;
    $canManageCoa = $isCreator && $hasActiveCoa;
@endphp

{{-- CoA Main Section --}}
<div class="p-6 space-y-4 border bg-gradient-to-br from-amber-900/20 to-yellow-900/20 rounded-2xl border-amber-500/30 backdrop-blur-sm">
    {{-- Header with Certificate Icon --}}
    <div class="flex items-center space-x-3">
        <div class="flex-shrink-0">
            @if($hasActiveCoa)
                {{-- Active Certificate Icon --}}
                <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-amber-400 to-yellow-500 rounded-xl">
                    <svg class="w-6 h-6 text-amber-900" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            @else
                {{-- Inactive/Missing Certificate Icon --}}
                <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            @endif
        </div>

        <div class="flex-1">
            <h3 class="text-lg font-bold text-white">
                {{ __('egi.coa.certificate') }}
            </h3>
            <p class="text-sm text-gray-300">
                @if($hasActiveCoa)
                    {{ __('egi.coa.certificate_active') }}
                @else
                    {{ __('egi.coa.no_certificate') }}
                @endif
            </p>
        </div>

        {{-- Status Badge --}}
        @if($hasActiveCoa)
            <div class="flex-shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3"/>
                    </svg>
                    {{ __('egi.coa.active') }}
                </span>
            </div>
        @else
            <div class="flex-shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3"/>
                    </svg>
                    {{ __('egi.coa.none') }}
                </span>
            </div>
        @endif
    </div>

    {{-- Certificate Information --}}
    @if($hasActiveCoa)
        <div class="pt-4 space-y-3 border-t border-amber-500/20">
            {{-- Certificate Details --}}
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-300">{{ __('egi.coa.serial_number') }}:</span>
                    <span class="font-mono text-xs text-white">{{ $existingCoa->serial }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-300">{{ __('egi.coa.issue_date') }}:</span>
                    <span class="text-white">{{ $existingCoa->issued_at?->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-300">{{ __('egi.coa.expires') }}:</span>
                    <span class="text-white">{{ $existingCoa->expiry_date?->format('M d, Y') }}</span>
                </div>
                @if($existingCoa->annexes_count > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-300">{{ __('egi.coa.annexes') }}:</span>
                    <span class="font-medium text-amber-400">{{ $existingCoa->annexes_count }} {{ __('egi.coa.pro') }}</span>
                </div>
                @endif
            </div>

            {{-- Quick Actions for Active Certificate --}}
                        <div class="flex space-x-2">
                {{-- View Certificate Button --}}
                <a href="{{ route('coa.verify.view', $existingCoa->verification_hash) }}"
                   target="_blank"
                   class="flex-1 px-3 py-2 text-sm font-medium text-center transition-all duration-200 rounded-lg shadow-lg bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-amber-900 hover:shadow-xl">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('egi.coa.view') }}
                </a>

                {{-- Download Bundle Button --}}
                <button onclick="downloadCoaBundle({{ $existingCoa->id }})"
                        class="flex-1 px-3 py-2 text-sm font-medium text-white transition-all duration-200 bg-gray-700 rounded-lg hover:bg-gray-600">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('egi.coa.pdf') }}
                </button>
            </div>

            {{-- Management Actions for Owner --}}
            @if($canManageCoa)
                <div class="pt-3 border-t border-amber-500/20">
                    <details class="group">
                        <summary class="flex items-center justify-between text-sm text-gray-300 transition-colors cursor-pointer hover:text-white">
                            <span>{{ __('Manage Certificate') }}</span>
                            <svg class="w-4 h-4 transition-transform transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>

                        <div class="mt-3 space-y-2">
                            {{-- Add Annex Button --}}
                            <button onclick="openCoaAnnexModal({{ $existingCoa->id }})"
                                    class="w-full px-3 py-2 text-sm font-medium text-white transition-all duration-200 bg-blue-600 rounded-lg hover:bg-blue-700">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                {{ __('egi.coa.add_annex') }}
                            </button>

                            {{-- Reissue Certificate Button --}}
                            <button onclick="reissueCoaCertificate({{ $existingCoa->id }})"
                                    class="w-full px-3 py-2 text-sm font-medium text-white transition-all duration-200 bg-purple-600 rounded-lg hover:bg-purple-700">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ __('egi.coa.reissue') }}
                            </button>

                            {{-- Revoke Certificate Button --}}
                            <button onclick="revokeCoaCertificate({{ $existingCoa->id }})"
                                    class="w-full px-3 py-2 text-sm font-medium text-white transition-all duration-200 bg-red-600 rounded-lg hover:bg-red-700">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                {{ __('Revoke') }}
                            </button>
                        </div>
                    </details>
                </div>
            @endif
        </div>
    @else
        {{-- No Certificate - Issue Certificate Section --}}
        <div class="pt-4 space-y-3 border-t border-amber-500/20">
            <div class="space-y-2 text-center">
                <p class="text-sm text-gray-300">
                    {{ __('This EGI does not have a Certificate of Authenticity') }}
                </p>

                @if($canIssueCoa)
                    <p class="text-xs text-gray-400">
                        {{ __('egi.coa.issue_description') }}
                    </p>
                @else
                    <p class="text-xs text-gray-400">
                        {{ __('egi.coa.creator_only') }}
                    </p>
                @endif
            </div>

            @if($canIssueCoa)
                {{-- Issue Certificate Button --}}
                <button onclick="issueCoaCertificate({{ $egi->id }})"
                        class="w-full px-4 py-3 font-bold text-white transition-all duration-200 rounded-lg shadow-lg bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 hover:shadow-xl">
                    <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('egi.coa.issue') }}
                </button>

                {{-- Pro Features Preview --}}
                <div class="p-3 border rounded-lg bg-gradient-to-r from-blue-900/30 to-purple-900/30 border-blue-500/20">
                    <h4 class="mb-2 text-sm font-medium text-blue-300">{{ __('egi.coa.unlock_pro') }}:</h4>
                    <ul class="space-y-1 text-xs text-gray-300">
                        <li class="flex items-center">
                            <svg class="w-3 h-3 mr-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('egi.coa.provenance') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-3 h-3 mr-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('Condition Reports') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-3 h-3 mr-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('Exhibition History') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-3 h-3 mr-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('egi.coa.pdf_bundle') }}
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Public Verification Info --}}
    <div class="pt-3 border-t border-amber-500/20">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-300">{{ __('Public Verification') }}</span>
            <a href="{{ route('coa.verify') }}"
               target="_blank"
               class="text-xs transition-colors text-amber-400 hover:text-amber-300">
                {{ __('Verify any certificate') }} →
            </a>
        </div>
    </div>
</div>

{{-- Include CoA JavaScript functionality --}}
@push('scripts')
<script>
// CoA Certificate Management Functions
function issueCoaCertificate(egiId) {
    if (!confirm('{{ __("egi.coa.confirm_issue") }}')) {
        return;
    }

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>{{ __("Issuing...") }}';
    button.disabled = true;

    fetch(`{{ route('coa.issue') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            egi_id: egiId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('{{ __("egi.coa.issued_success") }}');
            // Reload page to show new certificate
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_issuing") }}: ' + (data.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_issuing") }}');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function downloadCoaBundle(coaId) {
    // Create download link
    const link = document.createElement('a');
    link.href = `{{ route('coa.bundle', ':id') }}`.replace(':id', coaId);
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function openCoaAnnexModal(coaId) {
    // TODO: Implement annex modal
    alert('{{ __("egi.coa.annex_coming_soon") }}');
}

function reissueCoaCertificate(coaId) {
    if (!confirm('{{ __("egi.coa.confirm_reissue") }}')) {
        return;
    }

    fetch(`{{ route('coa.reissue', ':id') }}`.replace(':id', coaId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('{{ __("egi.coa.reissued_success") }}');
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_reissuing") }}: ' + (data.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_reissuing") }}');
    });
}

function revokeCoaCertificate(coaId) {
    const reason = prompt('{{ __("egi.coa.revocation_reason") }}');
    if (!reason || !confirm('{{ __("egi.coa.confirm_revoke") }}')) {
        return;
    }

    fetch(`{{ route('coa.revoke', ':id') }}`.replace(':id', coaId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('{{ __("egi.coa.revoked_success") }}');
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_revoking") }}: ' + (data.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_revoking") }}');
    });
}
</script>
@endpush
