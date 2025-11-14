{{-- Desktop Mega Menu Component --}}

{{-- Component-specific styles --}}
@push('styles')
    @vite('resources/css/mega-menu.css')
@endpush

<div class="relative ms-3">
    <x-dropdown align="right" width="96" contentClasses="bg-transparent border-0 shadow-none p-0">
        <x-slot name="trigger">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <button
                    class="flex text-sm transition duration-300 transform border-2 border-transparent rounded-full hover:scale-110 focus:border-gray-300 focus:outline-none">
                    <img class="object-cover transition-all duration-300 rounded-full size-8 ring-2 ring-blue-500/20 hover:ring-blue-500/60"
                        src="{{ Auth::user()?->profile_photo_url ?? null }}" alt="{{ Auth::user()?->name ?? '' }}" />
                </button>
            @else
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition-all duration-300 ease-in-out bg-white border border-transparent rounded-lg group hover:scale-105 hover:bg-gray-50 hover:text-gray-700 hover:shadow-lg focus:bg-gray-50 focus:outline-none active:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:bg-gray-700 dark:active:bg-gray-700">
                        <div class="flex items-center space-x-2">
                            <div
                                class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-br from-blue-500 to-purple-600">
                                {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="hidden sm:block">{{ Auth::user()?->name ?? '' }}</span>
                        </div>
                        <svg class="-me-0.5 ms-2 size-4 transition-transform duration-300 group-hover:rotate-180"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </span>
            @endif
        </x-slot>

        <x-slot name="content">
            <!-- Revolutionary Mega Menu Container -->
            <div
                class="mega-menu-container min-w-[380px] rounded-2xl border border-gray-200/50 bg-white/95 p-6 shadow-2xl backdrop-blur-xl dark:border-gray-700/50 dark:bg-gray-900/95 sm:min-w-[420px] lg:min-w-[500px]">

                <!-- User Header Card -->
                <div
                    class="p-4 mb-6 border user-header-card mobile-header-gradient rounded-xl border-blue-300/40 bg-gradient-to-r from-blue-500 to-purple-600 dark:border-blue-700/40">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex items-center justify-center w-12 h-12 text-lg font-bold text-white rounded-full shadow-lg bg-white/20 ring-2 ring-white/30">
                            {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">{{ Auth::user()?->name ?? '' }}</h3>
                            <p class="text-sm text-white/80">{{ Auth::user()?->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Grid -->
                <div class="grid grid-cols-1 gap-4 menu-grid sm:grid-cols-2">

                    <x-navigation.account-management-carousel :user="Auth::user()"
                        container-class="p-4 transition-all duration-300 border cursor-pointer menu-card desktop-emerald group rounded-xl border-emerald-200/30 bg-gradient-to-br from-emerald-50 to-teal-50 hover:scale-105 hover:shadow-lg dark:border-emerald-800/30 dark:from-emerald-900/20 dark:to-teal-900/20" />

                    <!-- Privacy & GDPR Card -->
                    <div
                        class="p-4 transition-all duration-300 border cursor-pointer menu-card desktop-blue group rounded-xl border-blue-200/30 bg-gradient-to-br from-blue-50 to-indigo-50 hover:scale-105 hover:shadow-lg dark:border-blue-800/30 dark:from-blue-900/20 dark:to-indigo-900/20">
                        <div class="flex items-center mb-3 space-x-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 text-white transition-transform duration-300 bg-blue-500 rounded-lg group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.gdpr_privacy') }}
                            </h4>
                        </div>
                        <div class="space-y-2">
                            @can('manage_consents')
                                <a href="{{ route('gdpr.consent') }}"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                    {{ __('gdpr.menu.gdpr_center') }}
                                </a>
                            @endcan
                            <a href="{{ route('gdpr.security') }}"
                                class="block text-sm text-gray-600 transition-colors duration-200 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                {{ __('menu.security_password') }}
                            </a>
                            <a href="{{ route('gdpr.profile-images') }}"
                                class="block text-sm text-gray-600 transition-colors duration-200 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                {{ __('menu.profile_images') }}
                            </a>
                            @can('gdpr.export_data')
                                <a href="{{ route('gdpr.export-data') }}"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                    {{ __('menu.export_data') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Collections Card -->
                    @can('create_collection')
                        <div
                            class="p-4 transition-all duration-300 border cursor-pointer menu-card desktop-purple group rounded-xl border-purple-200/30 bg-gradient-to-br from-purple-50 to-pink-50 hover:scale-105 hover:shadow-lg dark:border-purple-800/30 dark:from-purple-900/20 dark:to-pink-900/20">
                            <div class="flex items-center mb-3 space-x-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 text-white transition-transform duration-300 bg-purple-500 rounded-lg group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.collections') }}
                                </h4>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('collections.index') }}"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400">
                                    {{ __('menu.my_collections') }}
                                </a>
                                <button type="button" data-action="open-create-collection-modal"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400">
                                    {{ __('menu.new_collection') }}
                                </button>
                            </div>
                        </div>
                    @endcan

                    <!-- Activity & Notifications Card -->
                    <div
                        class="p-4 transition-all duration-300 border cursor-pointer menu-card desktop-orange group rounded-xl border-orange-200/30 bg-gradient-to-br from-orange-50 to-red-50 hover:scale-105 hover:shadow-lg dark:border-orange-800/30 dark:from-orange-900/20 dark:to-red-900/20">
                        <div class="flex items-center mb-3 space-x-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 text-white transition-transform duration-300 bg-orange-500 rounded-lg group-hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.activity') }}</h4>
                        </div>
                        <div class="space-y-2">
                            @can('view_activity_log')
                                <a href="{{ route('gdpr.activity-log') }}"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-orange-600 dark:text-gray-300 dark:hover:text-orange-400">
                                    {{ __('menu.activity_log') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <!-- Admin Tools Card (Only for admins) -->
                    @can('manage_roles')
                        <div
                            class="p-4 transition-all duration-300 border cursor-pointer menu-card desktop-gray group rounded-xl border-gray-200/30 bg-gradient-to-br from-gray-50 to-slate-50 hover:scale-105 hover:shadow-lg dark:border-gray-800/30 dark:from-gray-900/20 dark:to-slate-900/20 sm:col-span-2">
                            <div class="flex items-center mb-3 space-x-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 text-white transition-transform duration-300 bg-gray-600 rounded-lg group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('menu.admin_tools') }}
                                </h4>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                {{-- SuperAdmin Dashboard - Only for superadmin role --}}
                                @if (Auth::check() && Auth::user()->hasRole('superadmin'))
                                    <a href="{{ route('superadmin.dashboard') }}"
                                        class="flex items-center col-span-2 gap-2 px-3 py-2 text-sm font-semibold text-yellow-600 transition-all duration-200 rounded-lg bg-gradient-to-r from-yellow-500/10 to-amber-500/10 ring-1 ring-yellow-500/20 hover:from-yellow-500/20 hover:to-amber-500/20 hover:ring-yellow-500/30 dark:text-yellow-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                                            <path
                                                d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                                        </svg>
                                        🌟 SuperAdmin Dashboard
                                    </a>
                                @endif

                                <a href="{{ route('admin.roles.index') }}"
                                    class="block text-sm text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">
                                    {{ __('menu.permissions_roles') }}
                                </a>
                                {{-- TODO: Implementare route admin.users.index --}}
                                {{-- <a href="{{ route('admin.users.index') }}" class="block text-sm text-gray-600 transition-colors duration-200 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                                    {{ __('menu.user_management') }}
                                </a> --}}
                                @can('view_statistics')
                                    <a href="{{ route('statistics.index') }}"
                                        class="block text-sm text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">
                                        {{ __('menu.statistics') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endcan

                </div>

                <!-- Support & Legal Section -->
                <div class="pt-4 mt-6 border-t support-section border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-4">
                            <a href="{{ route('gdpr.privacy-policy') }}"
                                class="text-xs text-gray-500 transition-colors duration-200 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                {{ __('menu.privacy_policy') }}
                            </a>
                            <a href="{{ route('gdpr.terms') }}"
                                class="text-xs text-gray-500 transition-colors duration-200 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                {{ __('menu.terms_of_service') }}
                            </a>
                            @can('contact_dpo')
                                <a href="{{ route('gdpr.contact-dpo') }}"
                                    class="text-xs text-gray-500 transition-colors duration-200 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    {{ __('menu.contact_dpo') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Danger Zone & Logout -->
                <div class="pt-4 mt-4 border-t action-section border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        @can('gdpr.delete_account')
                            <a href="{{ route('gdpr.delete-account') }}"
                                class="text-xs text-red-500 transition-colors duration-200 hover:text-red-700">
                                {{ __('menu.delete_account') }}
                            </a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}" x-data class="ml-auto">
                            @csrf
                            <button type="submit"
                                class="flex items-center px-4 py-2 space-x-2 text-sm text-white transition-all duration-300 bg-red-500 rounded-lg hover:scale-105 hover:bg-red-600 hover:shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>{{ __('menu.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </x-slot>
    </x-dropdown>
</div>
