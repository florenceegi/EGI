@extends('layouts.gdpr')

@section('title', __('complaints.title'))

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8" role="main" aria-labelledby="complaints-title">
    <div class="max-w-4xl mx-auto">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="p-4 mb-6 border border-green-300 rounded-2xl bg-green-50" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Header Card --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 id="complaints-title" class="text-3xl font-bold text-gray-900">
                        {{ __('complaints.title') }}
                    </h1>
                    <p class="mt-2 text-gray-600" id="complaints-desc">
                        {{ __('complaints.subtitle') }}
                    </p>
                </div>
                <div class="hidden sm:block" aria-hidden="true">
                    <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- DSA Info Notice --}}
        <div class="p-6 mb-8 border shadow-lg border-amber-300 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl"
             role="region"
             aria-labelledby="dsa-info-heading">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 id="dsa-info-heading" class="text-lg font-semibold text-amber-900">{{ __('complaints.dsa_info_title') }}</h2>
                    <p class="mt-1 text-amber-700">{{ __('complaints.dsa_info_text') }}</p>
                    <p class="mt-2 text-sm text-amber-600">
                        {{ __('complaints.legal_contact') }}:
                        <a href="mailto:legal@florenceegi.com"
                           class="font-medium underline hover:text-amber-800">
                            legal@florenceegi.com
                        </a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Complaint Form --}}
        <form method="POST"
              action="{{ route('complaints.store') }}"
              class="space-y-6"
              id="complaint-form"
              aria-describedby="complaints-desc"
              novalidate>
            @csrf

            {{-- Complaint Type & Content Section --}}
            <section class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                     aria-labelledby="complaint-type-heading">
                <h2 id="complaint-type-heading" class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ __('complaints.form_title') }}
                </h2>

                {{-- Complaint Type --}}
                <div class="mb-6">
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('complaints.complaint_type') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select name="type"
                            id="type"
                            required
                            aria-required="true"
                            aria-describedby="type-error type-help"
                            class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <option value="">{{ __('complaints.select_type') }}</option>
                        @foreach($complaintTypes as $key => $value)
                            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                {{ __('complaints.types.' . $value) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500" id="type-help"></p>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600" id="type-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reported Content Type (conditional) --}}
                <div class="mb-6" id="content-type-group" style="display: none;">
                    <label for="reported_content_type" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('complaints.reported_content_type') }}
                    </label>
                    <select name="reported_content_type"
                            id="reported_content_type"
                            aria-describedby="content-type-error"
                            class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <option value="">{{ __('complaints.select_content_type') }}</option>
                        @foreach($contentTypes as $key => $value)
                            <option value="{{ $value }}" {{ old('reported_content_type') === $value ? 'selected' : '' }}>
                                {{ __('complaints.content_types.' . $value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('reported_content_type')
                        <p class="mt-1 text-sm text-red-600" id="content-type-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reported Content ID (conditional) --}}
                <div class="mb-6" id="content-id-group" style="display: none;">
                    <label for="reported_content_id" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('complaints.reported_content_id') }}
                    </label>
                    <input type="number"
                           name="reported_content_id"
                           id="reported_content_id"
                           min="1"
                           value="{{ old('reported_content_id') }}"
                           aria-describedby="content-id-help content-id-error"
                           class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                           placeholder="123">
                    <p class="mt-1 text-sm text-gray-500" id="content-id-help">
                        {{ __('complaints.reported_content_id_help') }}
                    </p>
                    @error('reported_content_id')
                        <p class="mt-1 text-sm text-red-600" id="content-id-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Description Section --}}
            <section class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                     aria-labelledby="description-heading">
                <h2 id="description-heading" class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('complaints.description') }}
                </h2>

                <div class="mb-6">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('complaints.description') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="6"
                              required
                              aria-required="true"
                              minlength="20"
                              maxlength="5000"
                              aria-describedby="description-help description-counter description-error"
                              class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                              placeholder="{{ __('complaints.description_placeholder') }}">{{ old('description') }}</textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-sm text-gray-500" id="description-help"></p>
                        <p class="text-sm text-gray-400" id="description-counter">
                            <span id="char-count">0</span> / 5000
                        </p>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600" id="description-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Evidence URLs --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('complaints.evidence_urls') }}
                    </label>
                    <p class="mb-3 text-sm text-gray-500">{{ __('complaints.evidence_urls_help') }}</p>
                    <div id="evidence-urls-container">
                        @if(old('evidence_urls'))
                            @foreach(old('evidence_urls') as $i => $url)
                                <div class="flex items-center gap-2 mb-2 evidence-url-row">
                                    <input type="url"
                                           name="evidence_urls[]"
                                           value="{{ $url }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                           placeholder="{{ __('complaints.evidence_url_placeholder') }}">
                                    <button type="button"
                                            onclick="this.closest('.evidence-url-row').remove(); updateAddButton();"
                                            class="px-3 py-2 text-sm text-red-600 transition-colors border border-red-200 rounded-lg hover:bg-red-50"
                                            aria-label="{{ __('complaints.remove_evidence_url') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button"
                            id="add-evidence-url"
                            onclick="addEvidenceUrl()"
                            class="inline-flex items-center px-3 py-2 mt-2 text-sm font-medium transition-colors border rounded-lg text-amber-700 border-amber-300 hover:bg-amber-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('complaints.add_evidence_url') }}
                    </button>
                    @error('evidence_urls')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    @error('evidence_urls.*')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Consent & Submit --}}
            <div class="p-6 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox"
                               name="consent_to_processing"
                               id="consent_to_processing"
                               value="1"
                               required
                               aria-required="true"
                               aria-describedby="consent-desc consent-error"
                               class="w-4 h-4 mt-1 border-gray-300 rounded text-amber-600 focus:ring-amber-500"
                               {{ old('consent_to_processing') ? 'checked' : '' }}>
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">
                                {{ __('complaints.consent_label') }}
                            </span>
                            <span class="block text-sm text-gray-500" id="consent-desc">
                                {{ __('complaints.consent_text') }}
                            </span>
                        </div>
                    </label>
                    @error('consent_to_processing')
                        <p class="mt-2 text-sm text-red-600" id="consent-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col justify-between gap-4 sm:flex-row">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center justify-center px-6 py-3 text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('complaints.cancel') }}
                    </a>

                    <button type="submit"
                            id="submit-btn"
                            class="inline-flex items-center justify-center px-6 py-3 text-white transition-all duration-200 border border-transparent rounded-lg bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span id="submit-text">{{ __('complaints.submit') }}</span>
                    </button>
                </div>

                <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-status"></div>
            </div>
        </form>

        {{-- Previous Complaints --}}
        @if($previousComplaints->isNotEmpty())
        <section class="p-6 mt-8 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                 aria-labelledby="previous-complaints-heading">
            <h2 id="previous-complaints-heading" class="mb-2 text-xl font-semibold text-gray-900">
                {{ __('complaints.your_complaints') }}
            </h2>
            <p class="mb-6 text-sm text-gray-500">{{ __('complaints.your_complaints_description') }}</p>

            <div class="overflow-x-auto" role="region" aria-label="{{ __('complaints.your_complaints') }}">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('complaints.date') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('complaints.reference') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('complaints.type') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('complaints.status') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('complaints.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previousComplaints as $complaint)
                        <tr>
                            <td class="px-4 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <time datetime="{{ $complaint->created_at->toISOString() }}">
                                    {{ $complaint->created_at->format('d/m/Y H:i') }}
                                </time>
                            </td>
                            <td class="px-4 py-4 text-sm whitespace-nowrap">
                                <code class="px-2 py-1 text-xs font-mono rounded bg-gray-100 text-gray-700">{{ $complaint->complaint_reference }}</code>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ __('complaints.types.' . $complaint->type) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($complaint->status)
                                        @case('received') bg-gray-100 text-gray-800 @break
                                        @case('under_review') bg-yellow-100 text-yellow-800 @break
                                        @case('action_taken') bg-blue-100 text-blue-800 @break
                                        @case('dismissed') bg-red-100 text-red-800 @break
                                        @case('appealed') bg-orange-100 text-orange-800 @break
                                        @case('resolved') bg-green-100 text-green-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ __('complaints.statuses.' . $complaint->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm whitespace-nowrap">
                                <a href="{{ route('complaints.show', $complaint) }}"
                                   class="font-medium text-amber-600 hover:text-amber-800">
                                    {{ __('complaints.view_details') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('complaint-form');
    const typeSelect = document.getElementById('type');
    const contentTypeGroup = document.getElementById('content-type-group');
    const contentIdGroup = document.getElementById('content-id-group');
    const contentTypeSelect = document.getElementById('reported_content_type');
    const descriptionField = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    const typeHelp = document.getElementById('type-help');
    const statusRegion = document.getElementById('form-status');

    // Type descriptions for helper text
    const typeDescriptions = @json(__('complaints.type_descriptions'));

    // Show/hide content fields based on complaint type
    typeSelect.addEventListener('change', function() {
        const showContentFields = ['content_report', 'ip_violation'].includes(this.value);
        contentTypeGroup.style.display = showContentFields ? 'block' : 'none';
        contentIdGroup.style.display = showContentFields && contentTypeSelect.value ? 'block' : 'none';

        // Update type description
        typeHelp.textContent = typeDescriptions[this.value] || '';

        if (!showContentFields) {
            contentTypeSelect.value = '';
            document.getElementById('reported_content_id').value = '';
        }
    });

    // Show content ID field when content type is selected
    contentTypeSelect.addEventListener('change', function() {
        contentIdGroup.style.display = this.value ? 'block' : 'none';
    });

    // Restore conditional fields on page load (validation errors)
    if (typeSelect.value) {
        typeSelect.dispatchEvent(new Event('change'));
    }
    if (contentTypeSelect.value) {
        contentTypeSelect.dispatchEvent(new Event('change'));
    }

    // Character counter
    descriptionField.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        if (count > 4800) {
            charCount.parentElement.classList.add('text-red-500');
            charCount.parentElement.classList.remove('text-gray-400');
        } else {
            charCount.parentElement.classList.remove('text-red-500');
            charCount.parentElement.classList.add('text-gray-400');
        }
    });

    // Init char count
    charCount.textContent = descriptionField.value.length;

    // Real-time validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
    });

    function validateField(field) {
        let isValid = true;
        if (field.type === 'checkbox') {
            isValid = field.checked;
        } else if (field.tagName === 'TEXTAREA' && field.minLength) {
            isValid = field.value.trim().length >= field.minLength;
        } else {
            isValid = field.value.trim() !== '';
        }

        if (!isValid) {
            field.setAttribute('aria-invalid', 'true');
            field.classList.add('border-red-500');
            field.classList.remove('border-gray-300');
        } else {
            field.setAttribute('aria-invalid', 'false');
            field.classList.remove('border-red-500');
            field.classList.add('border-gray-300');
        }
    }

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        let isValid = true;

        requiredFields.forEach(field => {
            validateField(field);
            if (field.getAttribute('aria-invalid') === 'true') {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            statusRegion.textContent = '{{ __("complaints.validation.description_required") }}';
            const firstInvalid = form.querySelector('[aria-invalid="true"]');
            if (firstInvalid) firstInvalid.focus();
            return false;
        }

        // Loading state
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        submitBtn.disabled = true;
        submitText.textContent = '{{ __("complaints.submitting") }}';
        statusRegion.textContent = '{{ __("complaints.submitting") }}';
    });
});

// Evidence URLs management
const MAX_EVIDENCE_URLS = 5;

function addEvidenceUrl() {
    const container = document.getElementById('evidence-urls-container');
    const rows = container.querySelectorAll('.evidence-url-row');

    if (rows.length >= MAX_EVIDENCE_URLS) return;

    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2 evidence-url-row';
    row.innerHTML = `
        <input type="url"
               name="evidence_urls[]"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
               placeholder="{{ __('complaints.evidence_url_placeholder') }}">
        <button type="button"
                onclick="this.closest('.evidence-url-row').remove(); updateAddButton();"
                class="px-3 py-2 text-sm text-red-600 transition-colors border border-red-200 rounded-lg hover:bg-red-50"
                aria-label="{{ __('complaints.remove_evidence_url') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(row);
    row.querySelector('input').focus();
    updateAddButton();
}

function updateAddButton() {
    const container = document.getElementById('evidence-urls-container');
    const rows = container.querySelectorAll('.evidence-url-row');
    const addBtn = document.getElementById('add-evidence-url');
    addBtn.style.display = rows.length >= MAX_EVIDENCE_URLS ? 'none' : 'inline-flex';
}
</script>
@endpush
@endsection
