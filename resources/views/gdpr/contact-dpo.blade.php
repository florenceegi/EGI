{{--
    * @package App\Views\Gdpr
    * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
    * @version 1.0.0 (DPO Contact Page - GDPR Art. 37-39)
    * @date 2025-11-26
    * @purpose Provide GDPR-compliant DPO contact interface
--}}

<x-app-layout :pageTitle="__('gdpr.dpo_contact')">

    {{-- Header Slot --}}
    <x-slot name="header">
        <h1 id="dpo-contact-title" class="text-3xl font-bold text-gray-900">
            {{ __('gdpr.dpo_contact') }}
        </h1>
        <p class="mt-2 text-gray-600" id="dpo-contact-desc">
            {{ __('gdpr.dpo_contact_description') }}
        </p>
    </x-slot>

    {{-- Main Content --}}
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-100 p-4 text-green-800" role="alert">
                    <div class="flex items-center">
                        <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-100 p-4 text-red-800" role="alert">
                    <div class="flex items-center">
                        <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- DPO Information Card --}}
            <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('gdpr.dpo_info_title') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('gdpr.dpo_info_subtitle') }}</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            {{-- DPO Name --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_name') }}</p>
                                <p class="text-gray-900">{{ $dpoInfo['name'] }}</p>
                            </div>

                            {{-- DPO Email --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_email_label') }}</p>
                                <a href="mailto:{{ $dpoInfo['email'] }}"
                                    class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $dpoInfo['email'] }}
                                </a>
                            </div>

                            @if ($dpoInfo['phone'])
                                {{-- DPO Phone --}}
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_phone') }}</p>
                                    <a href="tel:{{ $dpoInfo['phone'] }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $dpoInfo['phone'] }}
                                    </a>
                                </div>
                            @endif

                            {{-- Response Time --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_response_time') }}</p>
                                <p class="text-gray-900">{{ $dpoInfo['response_time'] }}</p>
                            </div>

                            {{-- Office Hours --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_office_hours') }}</p>
                                <p class="text-gray-900">{{ $dpoInfo['office_hours'] }}</p>
                            </div>

                            {{-- Languages --}}
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('gdpr.dpo_languages') }}</p>
                                <p class="text-gray-900">
                                    @foreach ($dpoInfo['languages'] as $lang)
                                        <span
                                            class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                            {{ strtoupper($lang) }}
                                        </span>
                                    @endforeach
                                </p>
                            </div>
                        </div>

                        @if ($dpoInfo['is_external'])
                            <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>{{ __('gdpr.dpo_external_notice') }}:</strong>
                                    {{ $dpoInfo['external_company'] ?? __('gdpr.dpo_external_service') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- GDPR Rights Reminder --}}
            <div class="rounded-2xl border border-gray-200 bg-white/80 p-6 shadow-lg backdrop-blur-lg">
                <h3 class="flex items-center text-lg font-semibold text-gray-900">
                    <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('gdpr.dpo_your_rights') }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">{{ __('gdpr.dpo_rights_intro') }}</p>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="mr-2 text-green-500">✓</span>
                        <span><strong>{{ __('gdpr.right_access') }}</strong> -
                            {{ __('gdpr.right_access_desc') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-green-500">✓</span>
                        <span><strong>{{ __('gdpr.right_rectification') }}</strong> -
                            {{ __('gdpr.right_rectification_desc') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-green-500">✓</span>
                        <span><strong>{{ __('gdpr.right_erasure') }}</strong> -
                            {{ __('gdpr.right_erasure_desc') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-green-500">✓</span>
                        <span><strong>{{ __('gdpr.right_portability') }}</strong> -
                            {{ __('gdpr.right_portability_desc') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-green-500">✓</span>
                        <span><strong>{{ __('gdpr.right_object') }}</strong> -
                            {{ __('gdpr.right_object_desc') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Contact Form --}}
            <div class="rounded-2xl border border-gray-200 bg-white/80 p-6 shadow-lg backdrop-blur-lg">
                <h3 class="flex items-center text-lg font-semibold text-gray-900">
                    <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    {{ __('gdpr.dpo_send_message') }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">{{ __('gdpr.dpo_form_intro') }}</p>

                <form action="{{ route('gdpr.contact-dpo.send') }}" method="POST" class="mt-6 space-y-6">
                    @csrf

                    {{-- Request Type --}}
                    <div>
                        <label for="request_type" class="block text-sm font-medium text-gray-700">
                            {{ __('gdpr.dpo_request_type') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="request_type" name="request_type" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('gdpr.dpo_select_request_type') }}</option>
                            @foreach ($dpoInfo['request_types'] as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('request_type') == $value ? 'selected' : '' }}>
                                    {{ __('gdpr.request_type_' . $value, ['default' => $label]) }}
                                </option>
                            @endforeach
                        </select>
                        @error('request_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">
                            {{ __('gdpr.dpo_subject') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" required value="{{ old('subject') }}"
                            maxlength="255"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ __('gdpr.dpo_subject_placeholder') }}">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">
                            {{ __('gdpr.dpo_priority') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="priority" name="priority" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($dpoInfo['priority_levels'] as $value => $info)
                                <option value="{{ $value }}"
                                    {{ old('priority', 'normal') == $value ? 'selected' : '' }}>
                                    {{ __('gdpr.priority_' . $value) }} ({{ __('gdpr.response_within') }}
                                    {{ $info['response_time'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">
                            {{ __('gdpr.dpo_message') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" required minlength="10" maxlength="2000"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ __('gdpr.dpo_message_placeholder') }}">{{ old('message') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">{{ __('gdpr.dpo_message_hint') }}</p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- GDPR Consent Notice --}}
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm text-blue-800">
                            <svg class="mr-1 inline h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('gdpr.dpo_gdpr_notice') }}
                        </p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            {{ __('gdpr.dpo_send_button') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Previous Messages --}}
            @if ($userMessages->count() > 0)
                <div class="rounded-2xl border border-gray-200 bg-white/80 p-6 shadow-lg backdrop-blur-lg">
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('gdpr.dpo_your_messages') }}
                    </h3>

                    <div class="mt-4 space-y-4">
                        @foreach ($userMessages as $msg)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $msg->subject }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $msg->created_at->format('d/m/Y H:i') }} •
                                            <span
                                                class="@switch($msg->status)
                                            @case('sent') bg-yellow-100 text-yellow-800 @break
                                            @case('read') bg-blue-100 text-blue-800 @break
                                            @case('answered') bg-green-100 text-green-800 @break
                                            @case('closed') bg-gray-100 text-gray-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch inline-flex items-center rounded px-2 py-0.5 text-xs font-medium">
                                                {{ __('gdpr.dpo_status_' . $msg->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <span
                                        class="@switch($msg->priority)
                                    @case('urgent') bg-red-100 text-red-800 @break
                                    @case('high') bg-orange-100 text-orange-800 @break
                                    @case('normal') bg-blue-100 text-blue-800 @break
                                    @case('low') bg-gray-100 text-gray-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch inline-flex items-center rounded px-2 py-0.5 text-xs font-medium">
                                        {{ __('gdpr.priority_' . $msg->priority) }}
                                    </span>
                                </div>
                                <p class="mt-2 line-clamp-2 text-sm text-gray-700">
                                    {{ Str::limit($msg->message_content ?? $msg->message, 200) }}</p>

                                @if ($msg->dpo_response)
                                    <div class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3">
                                        <p class="text-xs font-medium text-green-800">{{ __('gdpr.dpo_response') }}
                                            ({{ $msg->response_date?->format('d/m/Y H:i') }})</p>
                                        <p class="mt-1 text-sm text-green-700">
                                            {{ Str::limit($msg->dpo_response, 200) }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Supervisory Authority Info --}}
            <div
                class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-yellow-50 p-6 shadow-lg">
                <h3 class="flex items-center text-lg font-semibold text-gray-900">
                    <svg class="mr-2 h-5 w-5 text-amber-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('gdpr.dpo_supervisory_authority') }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">{{ __('gdpr.dpo_supervisory_intro') }}</p>

                <div class="mt-4 rounded-lg bg-white/50 p-4">
                    <p class="font-medium text-gray-900">Garante per la protezione dei dati personali</p>
                    <p class="text-sm text-gray-600">Piazza Venezia 11 - 00187 Roma</p>
                    <p class="text-sm text-gray-600">
                        <a href="mailto:protocollo@gpdp.it"
                            class="text-blue-600 hover:underline">protocollo@gpdp.it</a> |
                        <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener noreferrer"
                            class="text-blue-600 hover:underline">www.garanteprivacy.it</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
