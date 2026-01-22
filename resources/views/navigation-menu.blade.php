<script>
    console.log('resources/views/navigation-menu.blade.php');
</script>
<nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                @php
                    $user = App\Helpers\FegiAuth::user(); // User object or null
                @endphp
                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center">
                    <a href="{{ url('/home') }}" class="group flex items-center gap-2"
                        aria-label="{{ __('collection.logo_home_link_aria_label') }}">
                        <img src="{{ asset(config('app.logo')) }}" alt="Florence EGI Logo"
                            class="h-7 w-auto sm:h-8 md:h-9" loading="lazy" decoding="async">
                        <span
                            class="hidden text-base font-semibold text-gray-400 transition group-hover:text-emerald-400 sm:inline md:text-lg">{{ __('Florence EGI') }}</span>
                    </a>
                    {{-- Welcome Message - Dopo il logo per tutte le dimensioni --}}
                    <x-user-welcome />
                </div>
                {{-- Notification Badge (Desktop) --}}
                @if (App\Helpers\FegiAuth::check())
                    <div class="hidden md:block">
                        <x-notification-badge />
                    </div>
                @endif
                @php
                    $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
                @endphp
                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @include('partials.nav-links', ['isMobile' => false, 'authType' => $authType])
                </div>

            </div>

            {{-- <div class="flex">
                <div class="flex items-center text-4xl text-gray-700 shrink-0 dark:text-gray-500">
                    {{ Auth::user()?->name ?? '' }}
                </div>
            </div> --}}

            <!-- Desktop Mega Menu Component -->
            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-navigation.vanilla-desktop-menu />
            </div>

            <!-- Sezione mobile (schermi piccoli) con pulsante per la sidebar -->
            <div class="-me-2 flex items-center sm:hidden">
                {{-- Notification Badge (Mobile) --}}
                @if (App\Helpers\FegiAuth::check())
                    <x-notification-badge />
                @endif

                {{-- Menu Mobile Button --}}
                @if ($user)
                    <button type="button" data-mobile-menu-trigger
                        class="block rounded-full p-1 transition-colors hover:bg-gray-800/50 md:hidden">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="size-8 rounded-full object-cover ring-2 ring-gray-600"
                                src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                        @else
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-600 text-sm font-bold text-white">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </button>
                @endif

                {{-- Bottone Accedi (Solo per guest su mobile) --}}
                @if (!$user)
                    <div class="block md:hidden">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center rounded-md border border-gray-700 bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-300 transition hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ __('collection.login') }}
                        </a>
                        <a href="{{ route('register') }}" id="register-link-mobile"
                            class="ml-2 inline-flex items-center rounded-md border border-gray-700 bg-gray-800 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('collection.register') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Component -->
    <x-navigation.vanilla-mobile-menu />
</nav>
