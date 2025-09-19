{{--
    CoA Sidebar Section (Compact)
    🎯 Purpose: Compact CoA section for sidebar integration
    🛡️ Privacy: GDPR-compliant CoA display
    📍 Location: Direct include in egis/show.blade.php sidebar

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
<div class="p-4 space-y-3 border bg-gradient-to-br from-amber-900/20 to-yellow-900/20 rounded-xl border-amber-500/30">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="flex items-center text-sm font-bold text-white">
            @if($hasActiveCoa)
                <div class="flex items-center justify-center w-4 h-4 mr-2 rounded-full bg-amber-400">
                    <svg class="w-2.5 h-2.5 text-amber-900" fill="currentColor" viewBox="0 0 8 8">
                        <path d="m2.3 6.73 3.53-4.24c.049-.06.146-.06.195 0L9.6 6.73a.116.116 0 0 1-.096.17H2.4a.116.116 0 0 1-.1-.17Z"/>
                    </svg>
                </div>
            @else
                <div class="w-4 h-4 mr-2 bg-gray-600 rounded-full"></div>
            @endif
            {{ __('egi.coa.certificate') }}
        </h3>

        @if($hasActiveCoa)
            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">{{ __('egi.coa.active') }}</span>
        @else
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ __('egi.coa.none') }}</span>
        @endif
    </div>

    @if($hasActiveCoa)
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
            @if($annexesCount > 0)
            <div class="flex justify-between text-xs">
                <span class="text-gray-300">{{ __('egi.coa.pro_features') }}:</span>
                <span class="font-medium text-amber-400">{{ $annexesCount }} {{ __('egi.coa.annexes') }}</span>
            </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="flex space-x-2">
            @if($existingCoa->verification_hash)
                <a href="{{ route('coa.verify.view', $existingCoa->verification_hash) }}"
                   target="_blank"
                   class="flex-1 bg-amber-500 hover:bg-amber-600 text-amber-900 font-medium py-1.5 px-2 rounded text-xs text-center transition-colors">
                    {{ __('egi.coa.view') }}
                </a>
            @else
                <a href="{{ route('coa.verify.certificate.view', $existingCoa->serial) }}"
                   target="_blank"
                   class="flex-1 bg-amber-500 hover:bg-amber-600 text-amber-900 font-medium py-1.5 px-2 rounded text-xs text-center transition-colors">
                    {{ __('egi.coa.view') }}
                </a>
            @endif
            <button onclick="downloadCoaBundle({{ $existingCoa->id }})"
                    class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-medium py-1.5 px-2 rounded text-xs transition-colors">
                {{ __('egi.coa.pdf') }}
            </button>
        </div>

        @if($canManageCoa)
        {{-- Management Actions --}}
        <details class="group">
            <summary class="flex items-center justify-between text-xs text-gray-300 transition-colors cursor-pointer hover:text-white">
                <span>{{ __('egi.coa.manage_coa') }}</span>
                <svg class="w-3 h-3 transition-transform transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>

            <div class="mt-2 space-y-1">
                <button onclick="openCoaAnnexModal({{ $existingCoa->id }})"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-2 rounded text-xs transition-colors">
                    {{ __('egi.coa.add_annex') }}
                </button>
                <button onclick="reissueCoaCertificate({{ $existingCoa->id }})"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-1.5 px-2 rounded text-xs transition-colors">
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

            @if($canIssueCoa)
                <button onclick="issueCoaCertificate({{ $egi->id }})"
                        class="w-full px-3 py-2 font-bold text-white transition-colors bg-green-500 rounded hover:bg-green-600">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('egi.coa.issue_certificate') }}
                </button>

                <div class="p-2 border rounded bg-blue-900/30 border-blue-500/20">
                    <p class="mb-1 text-xs font-medium text-blue-300">{{ __('egi.coa.unlock_pro_features') }}:</p>
                    <ul class="text-xs text-gray-300 space-y-0.5">
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
        <a href="{{ route('coa.verify.page') }}"
           target="_blank"
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
    if (!confirm('{{ __("egi.coa.issue_certificate_confirm") }}')) {
        return;
    }

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="inline w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>{{ __("egi.coa.issuing") }}';
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
            alert('{{ __("egi.coa.certificate_issued_successfully") }}');
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_issuing_certificate") }}' + (data.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_issuing_certificate") }}');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function downloadCoaBundle(coaId) {
    const link = document.createElement('a');
    link.href = `{{ route('coa.bundle', ':id') }}`.replace(':id', coaId);
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function openCoaAnnexModal(coaId) {
    if (typeof currentCoaId === 'undefined') {
        alert('{{ __("egi.coa.annex_coming_soon") }}');
        return;
    }

    currentCoaId = coaId;
    document.getElementById('coaAnnexModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function downloadCoaBundle(coaId) {
    // Create form for POST request to bundle endpoint
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('coa.bundle', ':id') }}`.replace(':id', coaId);
    form.style.display = 'none';

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function reissueCoaCertificate(coaId) {
    if (!confirm('{{ __("egi.coa.reissue_certificate_confirm") }}')) {
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
            alert('{{ __("egi.coa.certificate_reissued_successfully") }}');
            window.location.reload();
        } else {
            alert('{{ __("egi.coa.error_reissuing_certificate") }}' + (data.message || '{{ __("egi.coa.unknown_error") }}'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("egi.coa.error_reissuing_certificate") }}');
    });
}
</script>
@endpush
@endonce
