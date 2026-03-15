<x-app-layout>
    {{--
    @Oracode View: Security Settings — Compact Professional Layout
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 3.0.0 (FlorenceEGI - EGI)
    @date 2026-03-15
    @purpose Gestione password, 2FA e sessioni browser in layout compatto
    --}}

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h1 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('profile.security_management') }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('profile.security_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    @push('styles')
        <style>
            /* Override Jetstream 3-col grid — scoped solo a questa pagina */
            .security-settings .md\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
            .security-settings .md\:col-span-2,
            .security-settings .md\:col-span-1 {
                grid-column: span 1 !important;
            }
            .security-settings .md\:mt-0 {
                margin-top: 0 !important;
            }
            /* Rimuovi arrotondamento superiore separato dalla sezione azioni */
            .security-settings .sm\:rounded-tl-md,
            .security-settings .sm\:rounded-tr-md {
                border-radius: 0.5rem 0.5rem 0 0 !important;
            }
        </style>
    @endpush

    <div class="security-settings mx-auto max-w-2xl space-y-5">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800/40 dark:bg-green-900/20 dark:text-green-400"
                role="alert" aria-live="polite">
                <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/40 dark:bg-red-900/20 dark:text-red-400"
                role="alert" aria-live="assertive">
                <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Password --}}
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="overflow-hidden rounded-2xl border border-gray-200/60 shadow-sm dark:border-gray-700/60">
                <div class="flex items-center gap-2 border-b border-gray-200/60 bg-gray-50/80 px-5 py-3 dark:border-gray-700/60 dark:bg-gray-800/60">
                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('profile.password_security') }}</h2>
                </div>
                <div class="bg-white p-5 dark:bg-gray-900">
                    @livewire('profile.update-password-form')
                </div>
            </div>
        @endif

        {{-- Two Factor Authentication --}}
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="overflow-hidden rounded-2xl border border-gray-200/60 shadow-sm dark:border-gray-700/60">
                <div class="flex items-center gap-2 border-b border-gray-200/60 bg-gray-50/80 px-5 py-3 dark:border-gray-700/60 dark:bg-gray-800/60">
                    <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Two Factor Authentication') }}</h2>
                </div>
                <div class="bg-white p-5 dark:bg-gray-900">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            </div>
        @endif

        {{-- Browser Sessions --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200/60 shadow-sm dark:border-gray-700/60">
            <div class="flex items-center gap-2 border-b border-gray-200/60 bg-gray-50/80 px-5 py-3 dark:border-gray-700/60 dark:bg-gray-800/60">
                <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('profile.browser_sessions') }}</h2>
            </div>
            <div class="bg-white p-5 dark:bg-gray-900">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            window.appConfig = @json(config('app'));
            window.gdprConfig = {
                locale: '{{ app()->getLocale() }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    consent: '{{ route('gdpr.consent') }}',
                    export: '{{ route('gdpr.export-data') }}',
                    delete: '{{ route('gdpr.delete-account') }}'
                }
            };
        </script>
    @endpush
</x-app-layout>
