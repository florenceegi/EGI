{{-- resources/views/auth/wizard/step2-consents.blade.php --}}
{{-- 🎯 Step 2: Consents (Required pre-checked, Optional to choose) --}}
{{-- ✅ Usa traduzioni da register.php --}}
{{-- ✅ Contrasti corretti --}}

@extends('auth.wizard.layout')

@section('content')
    <div class="mb-6 text-center sm:mb-8">
        {{-- Selected Type Badge --}}
        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-100 px-3 py-1.5">
            <div
                class="bg-{{ $userTypeDetails['color'] }} flex h-5 w-5 items-center justify-center rounded-full sm:h-6 sm:w-6">
                <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="{{ $userTypeDetails['icon_svg_path'] }}" />
                </svg>
            </div>
            <span
                class="text-xs font-medium text-blu-algoritmo sm:text-sm">{{ __('register.user_type_' . $userType) }}</span>
        </div>

        <h1 class="font-rinascimento mb-2 text-xl font-bold text-blu-algoritmo sm:mb-3 sm:text-2xl lg:text-3xl">
            {{ __('register.privacy_legend') }}
        </h1>
        <p class="text-sm text-gray-600 sm:text-base">
            {{ __('register.privacy_subtitle') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register.wizard.step2.store') }}">
        @csrf

        {{-- Required Consents (Pre-checked & Disabled) --}}
        <div class="mb-6 sm:mb-8">
            <h3
                class="mb-3 flex items-center text-xs font-semibold uppercase tracking-wider text-blu-algoritmo sm:mb-4 sm:text-sm">
                <svg class="mr-2 h-4 w-4 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Consensi obbligatori
            </h3>

            <div class="space-y-2 sm:space-y-3">
                {{-- Privacy Policy --}}
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" checked disabled
                                class="h-4 w-4 cursor-not-allowed rounded border-green-400 text-verde-rinascita sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label class="text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_privacy_policy_accepted') }}
                                <span class="ml-1 text-xs text-verde-rinascita">(obbligatorio)</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_privacy_policy_accepted') }}
                                <a href="{{ route('gdpr.privacy-policy') }}" target="_blank"
                                    class="font-medium text-blu-algoritmo hover:underline">
                                    {{ __('register.privacy_policy_link_text') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Terms of Service --}}
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" checked disabled
                                class="h-4 w-4 cursor-not-allowed rounded border-green-400 text-verde-rinascita sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label class="text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_terms_accepted') }}
                                <span class="ml-1 text-xs text-verde-rinascita">(obbligatorio)</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_terms_accepted') }}
                                <a href="{{ route('gdpr.terms') }}" target="_blank"
                                    class="font-medium text-blu-algoritmo hover:underline">
                                    {{ __('register.terms_link_text') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Age Confirmation --}}
                <div class="rounded-xl border border-green-200 bg-green-50 p-3 sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" checked disabled
                                class="h-4 w-4 cursor-not-allowed rounded border-green-400 text-verde-rinascita sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label class="text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_age_confirmation') }}
                                <span class="ml-1 text-xs text-verde-rinascita">(obbligatorio)</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_age_confirmation') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-3 flex items-center text-xs italic text-gray-500">
                <svg class="mr-1 inline-block h-4 w-4 text-verde-rinascita" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                Questi consensi sono pre-selezionati perché necessari per utilizzare la piattaforma.
            </p>
        </div>

        {{-- Optional Consents --}}
        <div class="mb-6 sm:mb-8">
            <h3
                class="mb-3 flex items-center text-xs font-semibold uppercase tracking-wider text-blu-algoritmo sm:mb-4 sm:text-sm">
                <svg class="text-oro-fiorentino mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                </svg>
                {{ __('register.optional_consents_title') }}
            </h3>
            <p class="mb-3 text-xs text-gray-500">{{ __('register.optional_consents_subtitle') }}</p>

            <div class="space-y-2 sm:space-y-3">
                {{-- Analytics --}}
                <div
                    class="hover:border-oro-fiorentino/50 rounded-xl border border-gray-200 bg-white p-3 transition-colors sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" id="consent_analytics" name="consents[analytics]" value="1"
                                class="text-oro-fiorentino focus:ring-oro-fiorentino h-4 w-4 cursor-pointer rounded border-gray-300 sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label for="consent_analytics" class="cursor-pointer text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_optional_analytics') }}
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_optional_analytics') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Marketing --}}
                <div
                    class="hover:border-oro-fiorentino/50 rounded-xl border border-gray-200 bg-white p-3 transition-colors sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" id="consent_marketing" name="consents[marketing]" value="1"
                                class="text-oro-fiorentino focus:ring-oro-fiorentino h-4 w-4 cursor-pointer rounded border-gray-300 sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label for="consent_marketing" class="cursor-pointer text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_optional_marketing') }}
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_optional_marketing') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Profiling --}}
                <div
                    class="hover:border-oro-fiorentino/50 rounded-xl border border-gray-200 bg-white p-3 transition-colors sm:p-4">
                    <div class="flex items-start">
                        <div class="mt-0.5 flex h-5 items-center">
                            <input type="checkbox" id="consent_profiling" name="consents[profiling]" value="1"
                                class="text-oro-fiorentino focus:ring-oro-fiorentino h-4 w-4 cursor-pointer rounded border-gray-300 sm:h-5 sm:w-5">
                        </div>
                        <div class="ml-3">
                            <label for="consent_profiling" class="cursor-pointer text-sm font-medium text-gray-800">
                                {{ __('register.consent_label_optional_profiling') }}
                            </label>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ __('register.consent_desc_optional_profiling') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:gap-0">
            <a href="{{ route('register.wizard.step1') }}"
                class="btn-secondary inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold sm:px-6 sm:py-3 sm:text-base">
                <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Indietro
            </a>
            <button type="submit"
                class="btn-primary rounded-xl px-6 py-2.5 text-sm font-semibold sm:px-8 sm:py-3 sm:text-base">
                Continua
                <svg class="ml-2 inline-block h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </div>
    </form>
@endsection
