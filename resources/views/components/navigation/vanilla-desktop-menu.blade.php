{{-- Vanilla Desktop Mega Menu Component - No Alpine.js --}}

@php
    $user = App\Helpers\FegiAuth::user();
@endphp

{{-- Component-specific styles --}}
@push('styles')
    @vite('resources/css/mega-menu.css')
@endpush

{{-- Component-specific JavaScript --}}
@push('scripts')
    @vite('resources/js/components/vanilla-desktop-menu.js')
@endpush

<div class="relative ms-3" data-dropdown-container>
    <!-- Dropdown Trigger Button -->
    <div class="relative">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <button type="button" data-dropdown-trigger
                class="flex transform rounded-full border-2 border-transparent text-sm transition duration-300 hover:scale-110 focus:border-gray-300 focus:outline-none">
                <img class="size-8 rounded-full object-cover ring-2 ring-blue-500/20 transition-all duration-300 hover:ring-blue-500/60"
                    src="{{ $user?->profile_photo_url ?? null }}" alt="{{ $user?->name ?? '' }}" />
            </button>
        @else
            <span class="inline-flex rounded-md">
                <button type="button" data-dropdown-trigger
                    class="group inline-flex items-center rounded-lg border border-transparent bg-gray-800 px-3 py-2 text-sm font-medium leading-4 text-gray-400 transition-all duration-300 ease-in-out hover:scale-105 hover:bg-gray-700 hover:text-gray-300 hover:shadow-lg focus:bg-gray-700 focus:outline-none active:bg-gray-700">
                    <div class="flex items-center space-x-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-sm font-bold text-white">
                            {{ substr($user?->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="hidden sm:block">{{ $user?->name ?? '' }}</span>
                    </div>
                    <svg class="-me-0.5 ms-2 size-4 transition-transform duration-300 group-hover:rotate-180"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
        @endif
    </div>

    <!-- Dropdown Content -->
    <div data-dropdown-content
        class="invisible absolute right-0 z-50 mt-2 origin-top-right scale-95 transform opacity-0 transition-all duration-200 ease-out">

        <!-- Revolutionary Mega Menu Container -->
        <div
            class="mega-menu-container max-h-[70vh] min-w-[380px] overflow-y-auto rounded-2xl border border-gray-700/50 bg-gray-900/95 p-6 shadow-2xl backdrop-blur-xl sm:min-w-[420px] lg:min-w-[500px]">

            <!-- User Header Card -->
            <div
                class="user-header-card mobile-header-gradient mb-6 rounded-xl border border-blue-300/40 bg-gradient-to-r from-blue-500 to-purple-600 p-4 dark:border-blue-700/40">
                <div class="flex items-center space-x-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        @if ($user && $user->id)
                            <a href="{{ route('creator.home', $user->id) }}"
                                class="block transition-transform duration-300 hover:scale-105">
                                <img class="size-12 rounded-full object-cover ring-2 ring-white/30 transition-all duration-300 hover:ring-white/60"
                                    src="{{ $user?->profile_photo_url ?? null }}" alt="{{ $user?->name ?? '' }}" />
                            </a>
                        @else
                            <img class="size-12 rounded-full object-cover ring-2 ring-white/30"
                                src="{{ $user?->profile_photo_url ?? null }}" alt="{{ $user?->name ?? '' }}" />
                        @endif
                    @else
                        @if ($user && $user->id)
                            <a href="{{ route('creator.home', $user->id) }}"
                                class="block transition-transform duration-300 hover:scale-105">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-lg font-bold text-white transition-all duration-300 hover:bg-white/30">
                                    {{ substr($user?->name ?? 'U', 0, 1) }}
                                </div>
                            </a>
                        @else
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-lg font-bold text-white">
                                {{ substr($user?->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    @endif
                    <div>
                        <h3 class="font-semibold text-white">{{ $user?->name ?? '' }}</h3>
                        <p class="text-sm text-white/80">{{ $user?->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Collection Badge Section -->
            <div class="mb-6">
                <x-collection-badge size="desktop" :show-when-empty="true" position="desktop-menu" />
            </div>

            <!-- Menu Grid -->
            <div class="grid grid-cols-1 gap-4">

                @php
                    // Tipi utente che possono possedere collezioni
                    $canOwnCollections =
                        $user && in_array($user->usertype, ['creator', 'mecenate', 'azienda', 'epp_entity']);
                @endphp

                <!-- Dynamic Collections Carousel Card - Solo per creator, mecenate, azienda, epp_entity -->
                @if ($canOwnCollections)
                    <x-menu-collections-carousel :collections="$user->ownedCollections()->orderBy('position')->get()" />

                    <!-- Shared Collections Carousel Card - Collections where user is collaborator -->
                    <x-menu-guest-collections-carousel :collections="$user->collaborations()->orderBy('position')->get()" />
                @endif

                <x-navigation.account-management-carousel :user="$user"
                    container-class="p-4 border mega-card rounded-2xl border-emerald-200/30 bg-gradient-to-br from-emerald-50 to-teal-50 dark:border-emerald-800/30 dark:from-emerald-900/20 dark:to-teal-900/20" />

                {{-- Egili Wallet Card --}}
                <x-navigation.egili-wallet-card />

                <!-- Privacy & GDPR Card -->
                <div
                    class="mega-card rounded-2xl border border-blue-200/30 bg-gradient-to-br from-blue-50 to-indigo-50 p-4 dark:border-blue-800/30 dark:from-blue-900/20 dark:to-indigo-900/20">
                    <div class="mb-3 flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-100">{{ __('menu.gdpr_privacy') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('manage_consents')
                            <a href="{{ route('gdpr.consent') }}"
                                class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-blue-400">
                                {{ __('gdpr.menu.gdpr_center') }}
                            </a>
                        @endcan
                        <a href="{{ route('gdpr.security') }}"
                            class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-blue-400">
                            {{ __('menu.security_password') }}
                        </a>
                        @can('gdpr.export_data')
                            <a href="{{ route('gdpr.export-data') }}"
                                class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-blue-400">
                                {{ __('menu.export_data') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Activity & Notifications Card -->
                <div
                    class="mega-card rounded-2xl border border-orange-200/30 bg-gradient-to-br from-orange-50 to-red-50 p-4 dark:border-orange-800/30 dark:from-orange-900/20 dark:to-red-900/20">
                    <div class="mb-3 flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-red-500">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-100">{{ __('menu.activity') }}</h4>
                    </div>
                    <div class="space-y-2">
                        @can('view_activity_log')
                            <a href="{{ route('gdpr.activity-log') }}"
                                class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-orange-400">
                                {{ __('menu.activity_log') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Admin Tools Card -->
                @can('manage_roles')
                    <div
                        class="mega-card col-span-1 rounded-2xl border border-gray-200/30 bg-gradient-to-br from-gray-50 to-slate-50 p-4 dark:border-gray-800/30 dark:from-gray-900/20 dark:to-slate-900/20 sm:col-span-2">
                        <div class="mb-3 flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-gray-600 to-slate-600">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-100">{{ __('menu.admin_tools') }}</h4>
                        </div>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            {{-- SuperAdmin Dashboard - Only for superadmin role --}}
                            @if ($user && $user->hasRole('superadmin'))
                                <a href="{{ route('superadmin.dashboard') }}"
                                    class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-yellow-500/20 to-amber-500/20 px-3 py-2 text-sm font-semibold text-yellow-300 ring-1 ring-yellow-500/30 transition-all duration-200 hover:from-yellow-500/30 hover:to-amber-500/30 hover:ring-yellow-400/50">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                                        <path
                                            d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                                    </svg>
                                    🌟 SuperAdmin
                                </a>
                            @endif

                            <a href="{{ route('admin.roles.index') }}"
                                class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-gray-100">
                                {{ __('menu.permissions_roles') }}
                            </a>
                            {{-- TODO: Implementare route admin.users.index --}}
                            {{-- <a href="{{ route('admin.users.index') }}"
                                class="block px-2 py-1 text-sm text-gray-300 transition-colors duration-200 rounded-lg hover:bg-black/20 hover:text-gray-100">
                                {{ __('menu.user_management') }}
                            </a> --}}
                            @can('view_statistics')
                                <a href="{{ route('statistics.index') }}"
                                    class="block rounded-lg px-2 py-1 text-sm text-gray-300 transition-colors duration-200 hover:bg-black/20 hover:text-gray-100">
                                    {{ __('menu.statistics') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcan

            </div>

            <!-- Support Section -->
            <div class="mt-6 border-t border-gray-200/50 pt-4 dark:border-gray-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-4">
                        <a href="{{ route('gdpr.privacy-policy') }}"
                            class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.privacy_policy') }}
                        </a>
                        <a href="{{ route('gdpr.cookie-policy') }}"
                            class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.cookie_policy') }}
                        </a>
                        <a href="{{ route('gdpr.terms') }}"
                            class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.terms_of_service') }}
                        </a>
                        <a href="{{ route('gdpr.contact-dpo') }}"
                            class="text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('menu.contact_dpo') }}
                        </a>
                    </div>

                    <!-- Danger Zone & Logout -->
                    <div class="flex items-center space-x-4">
                        @can('gdpr.delete_account')
                            <a href="{{ route('gdpr.delete-account') }}"
                                class="text-xs text-red-500 transition-colors hover:text-red-700">
                                {{ __('menu.delete_account') }}
                            </a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center space-x-2 rounded-lg bg-red-500 px-4 py-2 text-sm text-white transition-all duration-300 hover:scale-105 hover:bg-red-600 hover:shadow-lg">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>{{ __('menu.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
