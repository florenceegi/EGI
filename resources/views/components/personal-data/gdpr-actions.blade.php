{{--
@Oracode Component: GDPR Actions Sidebar (OS1-Compliant)
🎯 Purpose: Quick access to GDPR data subject rights and actions
🛡️ Privacy: Compliant data export, deletion, and consent management
🧱 Core Logic: Context-aware actions based on user auth type and permissions

@props [
    'gdprSummary' => array,
    'canEdit' => bool,
    'authType' => string
]
--}}

@props([
    'gdprSummary',
    'canEdit' => true,
    'authType' => 'strong'
])

<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-4">
        <h3 class="mb-4 text-lg font-medium text-gray-900">
            {{ __('user_personal_data.gdpr_actions_title') }}
        </h3>

        <div class="space-y-3">
            {{-- Export Personal Data --}}
            <div class="p-3 border border-gray-200 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">
                            {{ __('user_personal_data.export_data') }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('user_personal_data.export_description') }}
                        </p>
                    </div>
                    <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                @if($gdprSummary['export_available'])
                    <button
                        type="button"
                        data-action="export-personal-data"
                        class="w-full px-3 py-2 mt-2 text-xs text-blue-700 transition-colors border border-blue-200 rounded bg-blue-50 hover:bg-blue-100">
                        {{ __('user_personal_data.export_now') }}
                    </button>
                @else
                    <div class="px-3 py-2 mt-2 text-xs border rounded text-amber-600 bg-amber-50 border-amber-200">
                        {{ __('user_personal_data.export_rate_limit') }}
                    </div>
                @endif
            </div>

            {{-- Manage Consent --}}
            <div class="p-3 border border-gray-200 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">
                            {{ __('user_personal_data.manage_consent') }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('user_personal_data.consent_description_short') }}
                        </p>
                    </div>
                    <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>

                <a
                    href="{{ route('gdpr.consent') }}"
                    class="block w-full px-3 py-2 mt-2 text-xs text-center text-indigo-700 transition-colors border border-indigo-200 rounded bg-indigo-50 hover:bg-indigo-100">
                    {{ __('user_personal_data.manage_consent_action') }}
                </a>
            </div>

            {{-- Activity Log --}}
            <div class="p-3 border border-gray-200 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">
                            {{ __('user_personal_data.activity_log') }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('user_personal_data.activity_log_description') }}
                        </p>
                    </div>
                    <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>

                <a
                    href="{{ route('gdpr.activity-log') }}"
                    class="block w-full px-3 py-2 mt-2 text-xs text-center text-gray-700 transition-colors border border-gray-200 rounded bg-gray-50 hover:bg-gray-100">
                    {{ __('user_personal_data.view_activity_log') }}
                </a>
            </div>

            {{-- Account Deletion (Strong Auth Only) --}}
            @if($authType === 'strong')
                <div class="p-3 border border-red-200 rounded-lg bg-red-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-red-900">
                                {{ __('user_personal_data.delete_account') }}
                            </h4>
                            <p class="mt-1 text-xs text-red-700">
                                {{ __('user_personal_data.delete_account_description') }}
                            </p>
                        </div>
                        <svg class="w-5 h-5 ml-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    @if($gdprSummary['deletion_available'])
                        <a
                            href="{{ route('gdpr.delete-account') }}"
                            class="block w-full px-3 py-2 mt-2 text-xs text-center text-red-700 transition-colors bg-red-100 border border-red-300 rounded hover:bg-red-200">
                            {{ __('user_personal_data.request_deletion') }}
                        </a>
                    @else
                        <div class="px-3 py-2 mt-2 text-xs text-red-600 bg-red-100 border border-red-200 rounded">
                            {{ __('user_personal_data.deletion_not_available') }}
                        </div>
                    @endif
                </div>
            @else
                {{-- Weak Auth Upgrade Notice --}}
                <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-yellow-900">
                                {{ __('user_personal_data.upgrade_for_full_access') }}
                            </h4>
                            <p class="mt-1 text-xs text-yellow-700">
                                {{ __('user_personal_data.upgrade_description') }}
                            </p>
                            <a
                                href="{{ route('user.domains.upgrade') }}"
                                class="inline-block mt-2 text-xs text-yellow-800 underline hover:text-yellow-900">
                                {{ __('user_personal_data.upgrade_account') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Last Update Info --}}
        @if($gdprSummary['last_data_update'])
            <div class="pt-3 mt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    {{ __('user_personal_data.gdpr_notices.audit_notice') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    {{ __('user_personal_data.last_updated') }}: {{ $gdprSummary['last_data_update']->format('d/m/Y H:i') }}
                </p>
            </div>
        @endif
    </div>
</div>
