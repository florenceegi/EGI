{{--
@Oracode View: Organization Data Management
🎯 Purpose: Complete organization/business data management for EPP and Company users
🛡️ Privacy: Business data with fiscal validation
🧱 Core Logic: Organization data CRUD with validation
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('organization_data.management_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('organization_data.management_subtitle') }}
                </p>
            </div>

            <x-button type="button" data-action="save-organization-data" class="hidden" id="save-button">
                {{ __('organization_data.save_changes') }}
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

                {{-- Main Content --}}
                <div class="space-y-6 lg:col-span-3">

                    {{-- Success/Error Messages --}}
                    @if (session('success'))
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Organization Data Form --}}
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-white px-6 py-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                {{ __('organization_data.business_information') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('organization_data.business_information_desc') }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('user.organization.update') }}"
                            id="organization-data-form" class="px-6 py-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                {{-- Organization Name --}}
                                <div>
                                    <label for="org_name" class="block text-sm font-medium text-gray-700">
                                        {{ __('organization_data.org_name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="org_name" id="org_name"
                                        value="{{ old('org_name', $organizationData->org_name) }}"
                                        class="@error('org_name') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required>
                                    @error('org_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Organization Email --}}
                                <div>
                                    <label for="org_email" class="block text-sm font-medium text-gray-700">
                                        {{ __('organization_data.org_email') }}
                                    </label>
                                    <input type="email" name="org_email" id="org_email"
                                        value="{{ old('org_email', $organizationData->org_email) }}"
                                        class="@error('org_email') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('org_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- About / Chi Siamo --}}
                                <div>
                                    <label for="about" class="block text-sm font-medium text-gray-700">
                                        {{ __('organization_data.about') }}
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('organization_data.about_help') }}
                                    </p>
                                    <textarea name="about" id="about" rows="6"
                                        class="@error('about') border-red-300 @enderror mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="{{ __('organization_data.about_placeholder') }}">{{ old('about', $organizationData->about) }}</textarea>
                                    @error('about')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Business Type --}}
                                <div>
                                    <label for="business_type" class="block text-sm font-medium text-gray-700">
                                        {{ __('organization_data.business_type') }}
                                    </label>
                                    <select name="business_type" id="business_type"
                                        class="@error('business_type') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('organization_data.select_business_type') }}
                                        </option>
                                        <option value="individual"
                                            {{ old('business_type', $organizationData->business_type) === 'individual' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_individual') }}
                                        </option>
                                        <option value="sole_proprietorship"
                                            {{ old('business_type', $organizationData->business_type) === 'sole_proprietorship' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_sole_proprietorship') }}
                                        </option>
                                        <option value="partnership"
                                            {{ old('business_type', $organizationData->business_type) === 'partnership' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_partnership') }}
                                        </option>
                                        <option value="corporation"
                                            {{ old('business_type', $organizationData->business_type) === 'corporation' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_corporation') }}
                                        </option>
                                        <option value="non_profit"
                                            {{ old('business_type', $organizationData->business_type) === 'non_profit' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_non_profit') }}
                                        </option>
                                        <option value="pa_entity"
                                            {{ old('business_type', $organizationData->business_type) === 'pa_entity' ? 'selected' : '' }}>
                                            {{ __('organization_data.business_type_pa_entity') }}
                                        </option>
                                    </select>
                                    @error('business_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Address Section --}}
                                <div class="border-t border-gray-200 pt-6">
                                    <h4 class="mb-4 text-base font-medium text-gray-900">
                                        {{ __('organization_data.address_section') }}
                                    </h4>

                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        {{-- Street --}}
                                        <div class="sm:col-span-2">
                                            <label for="org_street" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_street') }}
                                            </label>
                                            <input type="text" name="org_street" id="org_street"
                                                value="{{ old('org_street', $organizationData->org_street) }}"
                                                class="@error('org_street') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_street')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- City --}}
                                        <div>
                                            <label for="org_city" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_city') }}
                                            </label>
                                            <input type="text" name="org_city" id="org_city"
                                                value="{{ old('org_city', $organizationData->org_city) }}"
                                                class="@error('org_city') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_city')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- ZIP --}}
                                        <div>
                                            <label for="org_zip" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_zip') }}
                                            </label>
                                            <input type="text" name="org_zip" id="org_zip"
                                                value="{{ old('org_zip', $organizationData->org_zip) }}"
                                                class="@error('org_zip') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_zip')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Region --}}
                                        <div>
                                            <label for="org_region" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_region') }}
                                            </label>
                                            <input type="text" name="org_region" id="org_region"
                                                value="{{ old('org_region', $organizationData->org_region) }}"
                                                class="@error('org_region') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_region')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- State --}}
                                        <div>
                                            <label for="org_state" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_state') }}
                                            </label>
                                            <input type="text" name="org_state" id="org_state"
                                                value="{{ old('org_state', $organizationData->org_state) }}"
                                                class="@error('org_state') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_state')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Fiscal Data Section --}}
                                <div class="border-t border-gray-200 pt-6">
                                    <h4 class="mb-4 text-base font-medium text-gray-900">
                                        {{ __('organization_data.fiscal_data_section') }}
                                    </h4>

                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        {{-- Fiscal Code --}}
                                        <div>
                                            <label for="org_fiscal_code"
                                                class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_fiscal_code') }}
                                            </label>
                                            <input type="text" name="org_fiscal_code" id="org_fiscal_code"
                                                value="{{ old('org_fiscal_code', $organizationData->org_fiscal_code) }}"
                                                class="@error('org_fiscal_code') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_fiscal_code')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- VAT Number --}}
                                        <div>
                                            <label for="org_vat_number"
                                                class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_vat_number') }}
                                            </label>
                                            <div class="flex mt-1">
                                                <input type="text" name="org_vat_number" id="org_vat_number"
                                                    value="{{ old('org_vat_number', $organizationData->org_vat_number) }}"
                                                    class="@error('org_vat_number') border-red-300 @enderror block w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <button type="button" id="validate-vat-btn"
                                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                                    title="{{ __('organization_data.validate_vat') }}">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p id="vat-validation-result" class="mt-1 text-sm hidden"></p>
                                            @error('org_vat_number')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- REA --}}
                                        <div>
                                            <label for="rea" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.rea') }}
                                            </label>
                                            <input type="text" name="rea" id="rea"
                                                value="{{ old('rea', $organizationData->rea) }}"
                                                class="@error('rea') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('rea')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- ATECO Code --}}
                                        <div>
                                            <label for="ateco_code" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.ateco_code') }}
                                            </label>
                                            <input type="text" name="ateco_code" id="ateco_code"
                                                value="{{ old('ateco_code', $organizationData->ateco_code) }}"
                                                placeholder="es. 62.01"
                                                class="@error('ateco_code') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('ateco_code')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- ATECO Description --}}
                                        <div class="sm:col-span-2">
                                            <label for="ateco_description" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.ateco_description') }}
                                            </label>
                                            <input type="text" name="ateco_description" id="ateco_description"
                                                value="{{ old('ateco_description', $organizationData->ateco_description) }}"
                                                class="@error('ateco_description') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('ateco_description')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- PEC (Posta Elettronica Certificata) --}}
                                        <div class="sm:col-span-2">
                                            <label for="pec" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.pec') }}
                                            </label>
                                            <input type="email" name="pec" id="pec"
                                                value="{{ old('pec', $organizationData->pec) }}"
                                                placeholder="example@pec.it"
                                                class="@error('pec') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('pec')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Contact Section --}}
                                <div class="border-t border-gray-200 pt-6">
                                    <h4 class="mb-4 text-base font-medium text-gray-900">
                                        {{ __('organization_data.contact_section') }}
                                    </h4>

                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        {{-- Website --}}
                                        <div class="sm:col-span-2">
                                            <label for="org_site_url" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_site_url') }}
                                            </label>
                                            <input type="url" name="org_site_url" id="org_site_url"
                                                value="{{ old('org_site_url', $organizationData->org_site_url) }}"
                                                placeholder="https://example.com"
                                                class="@error('org_site_url') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_site_url')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Phone 1 --}}
                                        <div>
                                            <label for="org_phone_1" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_phone_1') }}
                                            </label>
                                            <input type="tel" name="org_phone_1" id="org_phone_1"
                                                value="{{ old('org_phone_1', $organizationData->org_phone_1) }}"
                                                class="@error('org_phone_1') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_phone_1')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Phone 2 --}}
                                        <div>
                                            <label for="org_phone_2" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_phone_2') }}
                                            </label>
                                            <input type="tel" name="org_phone_2" id="org_phone_2"
                                                value="{{ old('org_phone_2', $organizationData->org_phone_2) }}"
                                                class="@error('org_phone_2') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_phone_2')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Phone 3 --}}
                                        <div>
                                            <label for="org_phone_3" class="block text-sm font-medium text-gray-700">
                                                {{ __('organization_data.org_phone_3') }}
                                            </label>
                                            <input type="tel" name="org_phone_3" id="org_phone_3"
                                                value="{{ old('org_phone_3', $organizationData->org_phone_3) }}"
                                                class="@error('org_phone_3') border-red-300 @enderror mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('org_phone_3')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="flex items-center justify-end border-t border-gray-200 pt-6">
                                    <x-button type="submit" class="bg-indigo-600 hover:bg-indigo-700">
                                        {{ __('organization_data.save_changes') }}
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6 lg:col-span-1">
                    {{-- Quick Navigation --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ __('organization_data.quick_navigation') }}
                            </h4>
                        </div>
                        <div class="px-6 py-4">
                            <a href="{{ route('user.domains.personal-data') }}"
                                class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('organization_data.go_to_personal_data') }}
                            </a>
                        </div>
                    </div>

                    {{-- IBAN Management --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ __('organization_data.iban_management') }}
                            </h4>
                        </div>
                        <div class="px-6 py-4">
                            <p class="mb-3 text-sm text-gray-600">
                                {{ __('organization_data.iban_description') }}
                            </p>
                            <button type="button" onclick="openIbanModal('organization')"
                                class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                {{ __('organization_data.manage_iban') }}
                            </button>
                        </div>
                    </div>

                    {{-- Business Data Enrichment --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900 flex items-center">
                                <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                {{ __('organization_data.auto_fill_title') }}
                            </h4>
                        </div>
                        <div class="px-6 py-4">
                            <p class="mb-3 text-sm text-gray-600">
                                {{ __('organization_data.auto_fill_description') }}
                            </p>
                            
                            {{-- Website URL Input --}}
                            <div class="mb-3">
                                <label for="enrichment_url" class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ __('organization_data.website_url_label') }}
                                </label>
                                <input type="url" id="enrichment_url" 
                                    value="{{ old('org_site_url', $organizationData->org_site_url) }}"
                                    placeholder="https://www.example.com"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            {{-- OR VAT Number Input --}}
                            <div class="mb-4">
                                <label for="enrichment_vat" class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ __('organization_data.or_vat_number') }}
                                </label>
                                <input type="text" id="enrichment_vat" 
                                    value="{{ old('org_vat_number', $organizationData->org_vat_number) }}"
                                    placeholder="12345678901"
                                    maxlength="11"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            {{-- Extract Button --}}
                            <button type="button" id="extract-data-btn"
                                class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg id="extract-icon" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <svg id="extract-spinner" class="mr-2 h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="extract-btn-text">{{ __('organization_data.extract_data_btn') }}</span>
                            </button>

                            {{-- Result Message --}}
                            <div id="enrichment-result" class="mt-3 hidden">
                                <div id="enrichment-success" class="hidden p-3 rounded-md bg-green-50 border border-green-200">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span id="enrichment-success-text" class="text-sm text-green-700"></span>
                                    </div>
                                </div>
                                <div id="enrichment-error" class="hidden p-3 rounded-md bg-red-50 border border-red-200">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span id="enrichment-error-text" class="text-sm text-red-700"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Completeness Score --}}
                            <div id="completeness-score" class="mt-3 hidden">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>{{ __('organization_data.completeness') }}</span>
                                    <span id="completeness-value">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div id="completeness-bar" class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GDPR Quick Actions --}}
                    <x-personal-data.gdpr-actions :gdpr-summary="$gdprSummary" :can-edit="$canEdit" :auth-type="$authType" />

                    {{-- Data Summary --}}
                    <x-personal-data.data-summary :personal-data="$organizationData" :last-update="$organizationData->updated_at" :user-country="'IT'" />

                    {{-- Help Section --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ __('organization_data.need_help') }}
                            </h4>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-gray-600">
                                {{ __('organization_data.help_text') }}
                            </p>
                            <div class="mt-4">
                                <a href="mailto:support@florenceegi.it"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ __('organization_data.contact_support') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-show save button when form changes
            const form = document.getElementById('organization-data-form');
            const saveButton = document.getElementById('save-button');

            if (form && saveButton) {
                form.addEventListener('input', () => {
                    saveButton.classList.remove('hidden');
                });

                // Trigger save button click on data-action click
                const saveActionBtn = document.querySelector('[data-action="save-organization-data"]');
                if (saveActionBtn) {
                    saveActionBtn.addEventListener('click', () => {
                        form.submit();
                    });
                }
            }

            // ============================================
            // Business Data Enrichment
            // ============================================
            const extractBtn = document.getElementById('extract-data-btn');
            const extractIcon = document.getElementById('extract-icon');
            const extractSpinner = document.getElementById('extract-spinner');
            const extractBtnText = document.getElementById('extract-btn-text');
            const enrichmentResult = document.getElementById('enrichment-result');
            const enrichmentSuccess = document.getElementById('enrichment-success');
            const enrichmentError = document.getElementById('enrichment-error');
            const enrichmentSuccessText = document.getElementById('enrichment-success-text');
            const enrichmentErrorText = document.getElementById('enrichment-error-text');
            const completenessScore = document.getElementById('completeness-score');
            const completenessValue = document.getElementById('completeness-value');
            const completenessBar = document.getElementById('completeness-bar');

            // VAT Validation Button
            const validateVatBtn = document.getElementById('validate-vat-btn');
            const vatValidationResult = document.getElementById('vat-validation-result');

            if (validateVatBtn) {
                validateVatBtn.addEventListener('click', async () => {
                    const vatNumber = document.getElementById('org_vat_number').value.trim();
                    if (!vatNumber) {
                        showVatResult('{{ __("organization_data.enter_vat_first") }}', false);
                        return;
                    }

                    validateVatBtn.disabled = true;
                    try {
                        const response = await fetch(`/api/business/validate-vat/${vatNumber}`);
                        const data = await response.json();
                        
                        if (data.valid === true) {
                            showVatResult('{{ __("organization_data.vat_valid") }}', true);
                        } else if (data.valid === false) {
                            showVatResult('{{ __("organization_data.vat_invalid") }}: ' + (data.status || ''), false);
                        } else {
                            showVatResult('{{ __("organization_data.vat_unknown") }}', null);
                        }
                    } catch (e) {
                        showVatResult('{{ __("organization_data.vat_error") }}', false);
                    } finally {
                        validateVatBtn.disabled = false;
                    }
                });
            }

            function showVatResult(message, isValid) {
                vatValidationResult.classList.remove('hidden', 'text-green-600', 'text-red-600', 'text-yellow-600');
                vatValidationResult.textContent = message;
                if (isValid === true) {
                    vatValidationResult.classList.add('text-green-600');
                } else if (isValid === false) {
                    vatValidationResult.classList.add('text-red-600');
                } else {
                    vatValidationResult.classList.add('text-yellow-600');
                }
            }

            // Extract Data Button
            if (extractBtn) {
                extractBtn.addEventListener('click', async () => {
                    const websiteUrl = document.getElementById('enrichment_url').value.trim();
                    const vatNumber = document.getElementById('enrichment_vat').value.trim();

                    if (!websiteUrl && !vatNumber) {
                        showEnrichmentError('{{ __("organization_data.enter_url_or_vat") }}');
                        return;
                    }

                    // Show loading state
                    setLoadingState(true);

                    try {
                        const response = await fetch('/api/business/enrich', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                website_url: websiteUrl || null,
                                vat_number: vatNumber || null
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Fill form fields with enriched data
                            fillFormWithEnrichedData(data.organization_data);
                            
                            // Show success message
                            const fieldsCount = Object.values(data.organization_data).filter(v => v).length;
                            showEnrichmentSuccess(`{{ __("organization_data.fields_extracted") }}: ${fieldsCount}`);
                            
                            // Show completeness score
                            showCompletenessScore(data.metadata.completeness_score);
                            
                            // Show save button
                            saveButton.classList.remove('hidden');
                        } else {
                            showEnrichmentError(data.error || '{{ __("organization_data.extraction_failed") }}');
                        }
                    } catch (e) {
                        console.error('Enrichment error:', e);
                        showEnrichmentError('{{ __("organization_data.network_error") }}');
                    } finally {
                        setLoadingState(false);
                    }
                });
            }

            function setLoadingState(loading) {
                extractBtn.disabled = loading;
                extractIcon.classList.toggle('hidden', loading);
                extractSpinner.classList.toggle('hidden', !loading);
                extractBtnText.textContent = loading 
                    ? '{{ __("organization_data.extracting") }}...' 
                    : '{{ __("organization_data.extract_data_btn") }}';
            }

            function showEnrichmentSuccess(message) {
                enrichmentResult.classList.remove('hidden');
                enrichmentSuccess.classList.remove('hidden');
                enrichmentError.classList.add('hidden');
                enrichmentSuccessText.textContent = message;
            }

            function showEnrichmentError(message) {
                enrichmentResult.classList.remove('hidden');
                enrichmentError.classList.remove('hidden');
                enrichmentSuccess.classList.add('hidden');
                enrichmentErrorText.textContent = message;
            }

            function showCompletenessScore(score) {
                completenessScore.classList.remove('hidden');
                completenessValue.textContent = score + '%';
                completenessBar.style.width = score + '%';
                
                // Color based on score
                completenessBar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500', 'bg-indigo-600');
                if (score < 30) {
                    completenessBar.classList.add('bg-red-500');
                } else if (score < 60) {
                    completenessBar.classList.add('bg-yellow-500');
                } else if (score < 80) {
                    completenessBar.classList.add('bg-indigo-600');
                } else {
                    completenessBar.classList.add('bg-green-500');
                }
            }

            function fillFormWithEnrichedData(data) {
                const fieldMapping = {
                    'org_name': 'org_name',
                    'org_email': 'org_email',
                    'pec': 'pec',
                    'org_street': 'org_street',
                    'org_city': 'org_city',
                    'org_region': 'org_region',
                    'org_state': 'org_state',
                    'org_zip': 'org_zip',
                    'org_site_url': 'org_site_url',
                    'org_phone_1': 'org_phone_1',
                    'org_phone_2': 'org_phone_2',
                    'org_vat_number': 'org_vat_number',
                    'org_fiscal_code': 'org_fiscal_code',
                    'rea': 'rea',
                    'ateco_code': 'ateco_code',
                    'ateco_description': 'ateco_description'
                };

                for (const [dataKey, formField] of Object.entries(fieldMapping)) {
                    const value = data[dataKey];
                    const input = document.getElementById(formField);
                    
                    if (input && value) {
                        // Only fill if empty or user confirms overwrite
                        if (!input.value || input.value === value) {
                            input.value = value;
                            // Highlight the field briefly
                            input.classList.add('ring-2', 'ring-indigo-500', 'ring-opacity-50');
                            setTimeout(() => {
                                input.classList.remove('ring-2', 'ring-indigo-500', 'ring-opacity-50');
                            }, 2000);
                        }
                    }
                }
            }
        </script>
    @endpush
    @include('components.iban-management-modal')
</x-app-layout>
