{{-- resources/views/auth/wizard/step4-summary.blade.php --}}
{{-- 🎯 Step 4: Summary & Confirmation --}}
{{-- ✅ Usa traduzioni da register.php --}}
{{-- ✅ Contrasti corretti --}}

@extends('auth.wizard.layout')

@section('content')
    <div class="mb-6 text-center sm:mb-8">
        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-green-100 sm:h-16 sm:w-16">
            <svg class="h-6 w-6 text-verde-rinascita sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="font-rinascimento mb-2 text-xl font-bold text-blu-algoritmo sm:mb-3 sm:text-2xl lg:text-3xl">
            Conferma i tuoi dati
        </h1>
        <p class="text-sm text-gray-600 sm:text-base">
            Verifica che tutto sia corretto prima di completare la registrazione
        </p>
    </div>

    {{-- Summary Card --}}
    <div class="mb-6 space-y-4 sm:mb-8 sm:space-y-6">
        {{-- Account Type --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 sm:text-sm">
                    Tipo di account
                </h3>
                <a href="{{ route('register.wizard.step1') }}"
                    class="flex items-center text-xs font-medium text-blu-algoritmo hover:underline sm:text-sm">
                    <svg class="mr-1 h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifica
                </a>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-{{ $userTypeDetails['color'] }} flex h-10 w-10 items-center justify-center rounded-full">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $userTypeDetails['icon_svg_path'] }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-900 sm:text-lg">
                        {{ __('register.user_type_' . $wizardData['user_type']) }}</p>
                    <p class="text-xs text-gray-600 sm:text-sm">
                        {{ __('register.user_type_' . $wizardData['user_type'] . '_desc') }}</p>
                </div>
            </div>
        </div>

        {{-- Personal Data --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 sm:text-sm">
                    Dati personali
                </h3>
                <a href="{{ route('register.wizard.step3') }}"
                    class="flex items-center text-xs font-medium text-blu-algoritmo hover:underline sm:text-sm">
                    <svg class="mr-1 h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifica
                </a>
            </div>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                <div>
                    <dt class="text-xs text-gray-500">{{ __('register.label_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:text-base">{{ $wizardData['data']['name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">{{ __('register.label_nick_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:text-base">
                        @if (!empty($wizardData['data']['nick_name']))
                            {{ $wizardData['data']['nick_name'] }}
                        @else
                            <span class="italic text-gray-400">Non impostato</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500">{{ __('register.label_email') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 sm:text-base">{{ $wizardData['data']['email'] }}</dd>
                </div>
                @if (!empty($wizardData['data']['org_name']))
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-500">Nome Organizzazione</dt>
                        <dd class="text-sm font-medium text-gray-900 sm:text-base">{{ $wizardData['data']['org_name'] }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Consents Summary --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 sm:text-sm">
                    Consensi accettati
                </h3>
                <a href="{{ route('register.wizard.step2') }}"
                    class="flex items-center text-xs font-medium text-blu-algoritmo hover:underline sm:text-sm">
                    <svg class="mr-1 h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifica
                </a>
            </div>
            <div class="space-y-2">
                {{-- Required (always checked) --}}
                <div class="flex items-center text-verde-rinascita">
                    <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span
                        class="text-xs text-gray-800 sm:text-sm">{{ __('register.consent_label_privacy_policy_accepted') }}</span>
                </div>
                <div class="flex items-center text-verde-rinascita">
                    <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs text-gray-800 sm:text-sm">{{ __('register.consent_label_terms_accepted') }}</span>
                </div>
                <div class="flex items-center text-verde-rinascita">
                    <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span
                        class="text-xs text-gray-800 sm:text-sm">{{ __('register.consent_label_age_confirmation') }}</span>
                </div>

                {{-- Optional --}}
                @php
                    $optionalConsents = [
                        'analytics' => 'consent_label_optional_analytics',
                        'marketing' => 'consent_label_optional_marketing',
                        'profiling' => 'consent_label_optional_profiling',
                    ];
                @endphp
                @foreach ($optionalConsents as $key => $label)
                    @if (!empty($wizardData['consents'][$key]))
                        <div class="text-oro-fiorentino flex items-center">
                            <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs text-gray-800 sm:text-sm">{{ __('register.' . $label) }}</span>
                        </div>
                    @else
                        <div class="flex items-center text-gray-300">
                            <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs text-gray-400 sm:text-sm">{{ __('register.' . $label) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Final Confirmation --}}
    <form method="POST" action="{{ route('register.wizard.complete') }}">
        @csrf

        {{-- Error Display --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start">
                    <svg class="text-rosso-medici mr-2 mt-0.5 h-5 w-5 flex-shrink-0" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-rosso-medici text-sm font-semibold">{{ __('register.error_title') }}</p>
                        <ul class="text-rosso-medici mt-1 list-inside list-disc text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigation Buttons --}}
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:gap-0">
            <a href="{{ route('register.wizard.step3') }}"
                class="btn-secondary inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold sm:px-6 sm:py-3 sm:text-base">
                <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Indietro
            </a>
            <button type="submit"
                class="rounded-xl bg-verde-rinascita px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:bg-green-700 hover:shadow-xl sm:px-8 sm:py-3 sm:text-base">
                <svg class="mr-2 inline-block h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('register.submit_button') }}
            </button>
        </div>
    </form>
@endsection
