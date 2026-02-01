{{--
@Oracode View: Personal Data Management (OS1-Compliant)
🎯 Purpose: Complete personal data management with GDPR compliance and fiscal validation
🛡️ Privacy: Full audit trail, consent management, data subject rights
🧱 Core Logic: FegiAuth integration, country-specific validation, UEM error handling
🌍 Scale: 6 MVP countries support with enterprise-grade validation
⏰ MVP: Critical Personal Data Domain for 30 June deadline

@package resources/views/user/domains/personal-data
@author Padmin D. Curtis (AI Partner OS1-Compliant)
@version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
@deadline 2025-06-30
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('user_personal_data.management_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('user_personal_data.management_subtitle') }}
                </p>
            </div>

            {{-- Auth Type Indicator --}}
            <div class="flex items-center space-x-3">
                <x-auth-type-badge :type="$authType" />

                @if ($canEdit)
                    <x-button type="button" data-action="save-personal-data" class="hidden" id="save-button">
                        {{ __('user_personal_data.save_changes') }}
                    </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Vite Assets --}}
    @vite(['resources/css/personal-data.css', 'resources/ts/domain/personal-data.ts'])

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

                {{-- Main Content --}}
                <div class="space-y-6 lg:col-span-3">

                    {{-- GDPR Notice Section --}}
                    <x-personal-data.gdpr-notice :gdpr-summary="$gdprSummary" />

                    {{-- Personal Data Form --}}
                    <x-personal-data.form :user="$user" :personal-data="$personalData" :gdprConsents="$gdprConsents" :user-country="$userCountry"
                        :available-countries="$availableCountries" :validation-config="$validationConfig" :can-edit="$canEdit" :auth-type="$authType" :platform-services-consent="$platformServicesConsent" />

                </div>

                {{-- Sidebar --}}
                <div class="space-y-6 lg:col-span-1">

                    {{-- DEBUG: Mostra usertype --}}
                    <div class="rounded border border-yellow-400 bg-yellow-100 p-4">
                        <p class="text-xs">DEBUG: usertype = {{ $user->usertype ?? 'NULL' }}</p>
                        <p class="text-xs">User ID = {{ $user->id ?? 'NULL' }}</p>
                    </div>

                    @if (in_array($user->usertype ?? '', ['epp', 'company', 'enterprise', 'epp_entity']))
                        {{-- Quick Navigation to Organization Data --}}
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                                <h4 class="text-sm font-medium text-gray-900">
                                    {{ __('user_personal_data.quick_navigation') }}
                                </h4>
                            </div>
                            <div class="px-6 py-4">
                                <a href="{{ route('user.organization.edit') }}"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ __('user_personal_data.go_to_organization_data') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- IBAN Management --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ __('user_personal_data.iban_management') }}
                            </h4>
                        </div>
                        <div class="px-6 py-4">
                            <p class="mb-3 text-sm text-gray-600">
                                {{ __('user_personal_data.iban_description') }}
                            </p>
                            <button type="button" onclick="openIbanModal('personal')"
                                class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                {{ __('user_personal_data.manage_iban') }}
                            </button>
                        </div>
                    </div>

                    {{-- Shipping Addresses Management --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ __('user_personal_data.shipping.title') ?? 'Indirizzi di Spedizione' }}
                            </h4>
                            <button type="button" data-action="open-shipping-modal"
                                data-url="{{ route('user.domains.personal-data.shipping-address.store') }}"
                                data-method="POST"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                + {{ __('common.add') ?? 'Aggiungi' }}
                            </button>
                        </div>
                        <div class="px-6 py-4">
                            @if ($shippingAddresses->count() > 0)
                                <ul role="list" class="divide-y divide-gray-100">
                                    @foreach ($shippingAddresses as $address)
                                        <li class="flex items-center justify-between gap-x-6 py-5">
                                            <div class="min-w-0">
                                                <div class="flex items-start gap-x-3">
                                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                                        {{ $address->city }}</p>
                                                    @if ($address->is_default)
                                                        <p
                                                            class="mt-0.5 whitespace-nowrap rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                            Default</p>
                                                    @endif
                                                </div>
                                                <div
                                                    class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                                    <p class="whitespace-nowrap">{{ $address->address_line_1 }}</p>
                                                    <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                                                        <circle cx="1" cy="1" r="1" />
                                                    </svg>
                                                    <p class="truncate">{{ $address->postal_code }}
                                                        ({{ $address->country }})
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-none items-center gap-x-4">
                                                <button type="button" data-action="open-shipping-modal"
                                                    data-url="{{ route('user.domains.personal-data.shipping-address.update', $address->id) }}"
                                                    data-method="PUT" data-payload="{{ json_encode($address) }}"
                                                    class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">
                                                    {{ __('common.edit') ?? 'Modifica' }}
                                                </button>

                                                @if (!$address->is_default)
                                                    <form method="POST"
                                                        action="{{ route('user.domains.personal-data.shipping-address.default', $address->id) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-xs text-indigo-600 hover:text-indigo-900">Set
                                                            Default</button>
                                                    </form>

                                                    <form method="POST"
                                                        action="{{ route('user.domains.personal-data.shipping-address.destroy', $address->id) }}"
                                                        onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="py-6 text-center">
                                    <p class="text-sm text-gray-500">Nessun indirizzo di spedizione salvato.</p>
                                    <button type="button" data-action="open-shipping-modal"
                                        data-url="{{ route('user.domains.personal-data.shipping-address.store') }}"
                                        data-method="POST" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500">
                                        Aggiungi il primo indirizzo
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- GDPR Quick Actions --}}
                    <x-personal-data.gdpr-actions :gdpr-summary="$gdprSummary" :can-edit="$canEdit" :auth-type="$authType" />

                    {{-- Data Summary --}}
                    <x-personal-data.data-summary :personal-data="$personalData" :last-update="$lastUpdate" :user-country="$userCountry" />

                    {{-- Validation Info --}}
                    <x-personal-data.validation-info :user-country="$userCountry" :validation-config="$validationConfig" />

                </div>
            </div>
        </div>
    </div>

    {{-- Error Display Container --}}
    <div id="error-container" class="fixed right-4 top-4 z-50"></div>

    {{-- Loading Overlay --}}
    <div id="loading-overlay" class="fixed inset-0 z-40 flex hidden items-center justify-center bg-black bg-opacity-50">
        <div class="flex items-center space-x-3 rounded-lg bg-white p-6">
            <x-loading-spinner />
            <span class="text-gray-700">{{ __('user_personal_data.processing_update') }}</span>
        </div>
    </div>

    {{-- Pass data to TypeScript --}}
    @php
        $personalDataConfig = [
            'canEdit' => $canEdit,
            'authType' => $authType,
            'userCountry' => $userCountry,
            'availableCountries' => $availableCountries,
            'validationConfig' => $validationConfig,
            'csrfToken' => csrf_token(),
            'updateUrl' => route('user.domains.personal-data.update'),
            'exportUrl' => route('user.domains.personal-data.export'),
            'translations' => [
                'confirmChanges' => __('user_personal_data.confirm_changes'),
                'updateSuccess' => __('user_personal_data.update_success'),
                'updateError' => __('user_personal_data.update_error'),
                'validationError' => __('user_personal_data.validation_error'),
                'exportStarted' => __('user_personal_data.export_started'),
                'processing' => __('user_personal_data.processing_update'),
                'shipping_add_new' => __('user_personal_data.shipping.add_new'),
                'shipping_edit_address' => __('user_personal_data.shipping.edit_address'),
            ],
        ];
    @endphp

    <script>
        window.personalDataConfig = @json($personalDataConfig);
    </script>

    @include('components.iban-management-modal')
    <x-personal-data.shipping-address-modal :countries="$availableCountries" />
</x-app-layout>
