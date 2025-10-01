{{-- resources/views/gdpr/consent/preferences.blade.php --}}
<x-platform-layout
    :page-title="__('gdpr.consent.preferences_title')"
    :page-subtitle="__('gdpr.consent.preferences_subtitle')"
    :breadcrumb-items="[
        ['label' => __('gdpr.consent.breadcrumb'), 'url' => route('gdpr.consent')],
        ['label' => __('gdpr.consent.preferences_breadcrumb')]
    ]">

    {{-- Flash Messages & Alerts --}}
    @if(session('success'))
        <div class="mb-4 alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Consent Preferences Form --}}
    <div class="max-w-4xl mx-auto">
        {{-- Information Panel --}}
        <div class="p-6 mb-8 border border-blue-200 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">
                        {{ __('gdpr.consent.preferences_info_title') }}
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-600">
                        {{ __('gdpr.consent.preferences_info_description') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Preferences Form --}}
        <form method="POST" action="{{ route('gdpr.consent.update') }}" id="consent-preferences-form">
            @csrf

            <div class="space-y-6">
                @if($consentTypes && count($consentTypes) > 0)
                    @foreach($consentTypes as $type => $config)
                        <div class="transition-all duration-300 border-2 border-gray-200 consent-preference-card bg-base-100 rounded-xl hover:border-primary/30">
                            {{-- Card Header --}}
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-xl font-semibold text-gray-800">
                                                {{ $config['name'] }}
                                            </h3>
                                            <div class="badge {{ $config['required'] ? 'badge-success' : 'badge-info' }}">
                                                {{ $config['required'] ? __('gdpr.consent.required') : __('gdpr.consent.optional') }}
                                            </div>
                                            <div class="badge badge-outline">
                                                {{ ucfirst($config['privacy_level']) }}
                                            </div>
                                        </div>
                                        <p class="leading-relaxed text-gray-600">
                                            {{ $config['description'] }}
                                        </p>
                                    </div>

                                    {{-- Toggle Switch --}}
                                    <div class="flex flex-col items-center ml-6">
                                        @if($config['can_withdraw'])
                                            <input type="checkbox"
                                                   name="consents[{{ $type }}]"
                                                   value="1"
                                                   id="consent_{{ $type }}"
                                                   class="toggle toggle-primary toggle-lg"
                                                   {{ ($userConsents->where('purpose', $type)->first()?->granted ?? $config['default_value']) ? 'checked' : '' }}
                                                   onchange="updateConsentPreview('{{ $type }}', this.checked)">
                                            <label for="consent_{{ $type }}" class="mt-2 text-xs text-gray-500 cursor-pointer">
                                                {{ __('gdpr.consent.toggle_label') }}
                                            </label>
                                        @else
                                            <div class="opacity-75 toggle toggle-primary toggle-lg checked disabled"></div>
                                            <span class="mt-2 text-xs text-gray-500">{{ __('gdpr.consent.always_enabled') }}</span>
                                            <input type="hidden" name="consents[{{ $type }}]" value="1">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Card Content --}}
                            <div class="p-6 space-y-4">
                                {{-- Technical Details --}}
                                <div class="grid grid-cols-1 gap-4 p-4 rounded-lg md:grid-cols-2 bg-gray-50">
                                    <div>
                                        <span class="text-xs font-medium tracking-wide text-gray-500 uppercase">
                                            {{ __('gdpr.consent.legal_basis') }}
                                        </span>
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ ucfirst(str_replace('_', ' ', $config['legal_basis'])) }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium tracking-wide text-gray-500 uppercase">
                                            {{ __('gdpr.consent.data_retention') }}
                                        </span>
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ $config['retention_period'] }}
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-xs font-medium tracking-wide text-gray-500 uppercase">
                                            {{ __('gdpr.consent.processing_purpose') }}
                                        </span>
                                        <div class="mt-1 text-sm text-gray-700">
                                            {{ $config['data_processing_purpose'] }}
                                        </div>
                                    </div>
                                </div>

                                {{-- User Benefits --}}
                                @if(isset($config['user_benefits']) && count($config['user_benefits']) > 0)
                                    <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                                        <h4 class="flex items-center gap-2 mb-3 font-medium text-green-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ __('gdpr.consent.benefits_title') }}
                                        </h4>
                                        <ul class="space-y-2 text-sm text-green-700">
                                            @foreach($config['user_benefits'] as $benefit)
                                                <li class="flex items-start gap-2">
                                                    <span class="mt-1 text-green-500">•</span>
                                                    <span>{{ $benefit }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Withdrawal Consequences --}}
                                @if(isset($config['withdrawal_consequences']) && count($config['withdrawal_consequences']) > 0 && $config['can_withdraw'])
                                    <div class="p-4 border rounded-lg bg-amber-50 border-amber-200">
                                        <h4 class="flex items-center gap-2 mb-3 font-medium text-amber-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            {{ __('gdpr.consent.consequences_title') }}
                                        </h4>
                                        <ul class="space-y-2 text-sm text-amber-700">
                                            @foreach($config['withdrawal_consequences'] as $consequence)
                                                <li class="flex items-start gap-2">
                                                    <span class="mt-1 text-amber-500">•</span>
                                                    <span>{{ $consequence }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Third Parties --}}
                                @if(isset($config['third_parties']) && count($config['third_parties']) > 0)
                                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                        <h4 class="flex items-center gap-2 mb-3 font-medium text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ __('gdpr.consent.third_parties_title') }}
                                        </h4>
                                        <ul class="space-y-2 text-sm text-blue-700">
                                            @foreach($config['third_parties'] as $thirdParty)
                                                <li class="flex items-start gap-2">
                                                    <span class="mt-1 text-blue-500">•</span>
                                                    <span>{{ $thirdParty }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Empty State --}}
                    <div class="p-12 text-center bg-base-100 rounded-xl">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mb-4 text-lg font-semibold text-gray-600">
                            {{ __('gdpr.consent.no_preferences_available') }}
                        </h3>
                        <p class="text-gray-500">
                            {{ __('gdpr.consent.no_preferences_description') }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="p-6 mt-8 bg-gray-50 rounded-xl">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="text-sm text-gray-600">
                        @if($lastUpdate)
                            {{ __('gdpr.consent.last_updated') }}: {{ $lastUpdate->format('d/m/Y H:i') }}
                        @else
                            {{ __('gdpr.consent.never_updated') }}
                        @endif
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('gdpr.consent') }}" class="btn btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ __('gdpr.consent.back_to_overview') }}
                        </a>

                        <button type="submit" class="btn btn-primary" id="save-preferences-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('gdpr.consent.save_preferences') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        .consent-preference-card {
            transition: all 0.3s ease;
        }

        .consent-preference-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.1);
        }

        .toggle:disabled {
            cursor: not-allowed;
        }

        #consent-preferences-form {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[GDPR Consent Preferences] Page initialized');

            // Form submission confirmation
            const form = document.getElementById('consent-preferences-form');
            const saveBtn = document.getElementById('save-preferences-btn');

            if (form && saveBtn) {
                form.addEventListener('submit', function(e) {
                    saveBtn.classList.add('loading');
                    saveBtn.disabled = true;
                });
            }

            // Auto-save draft (optional - store in sessionStorage)
            const toggles = document.querySelectorAll('input[type="checkbox"][name^="consents"]');
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const formData = new FormData(form);
                    const preferences = {};

                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith('consents[')) {
                            const consentType = key.match(/consents\[(.+)\]/)[1];
                            preferences[consentType] = value === '1';
                        }
                    }

                    // Store draft in session storage
                    sessionStorage.setItem('gdpr_preferences_draft', JSON.stringify(preferences));
                    console.log('[GDPR] Draft preferences saved', preferences);
                });
            });
        });

        /**
         * Update consent preview when toggle changes
         */
        function updateConsentPreview(consentType, isEnabled) {
            console.log(`[GDPR] Consent ${consentType} changed to:`, isEnabled);

            // You can add visual feedback here
            const card = document.querySelector(`#consent_${consentType}`).closest('.consent-preference-card');
            if (card) {
                if (isEnabled) {
                    card.classList.add('border-green-300', 'bg-green-50/30');
                    card.classList.remove('border-red-300', 'bg-red-50/30');
                } else {
                    card.classList.add('border-red-300', 'bg-red-50/30');
                    card.classList.remove('border-green-300', 'bg-green-50/30');
                }

                // Remove highlight after animation
                setTimeout(() => {
                    card.classList.remove('border-green-300', 'bg-green-50/30', 'border-red-300', 'bg-red-50/30');
                }, 2000);
            }
        }
    </script>
    @endpush

</x-platform-layout>
