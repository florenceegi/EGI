@php
    // Expected inputs: $variant ('mobile' or 'desktop'), $layout ('guest' or 'app'), $authType
    $variant = $variant ?? 'mobile';
    $layout = $layout ?? 'guest';
    // Prefer FegiAuth if available in project; fallback to Laravel Auth
    try {
        $authType = $authType ?? (\App\Helpers\FegiAuth::check() ? 'authenticated' : 'guest');
    } catch (\Throwable $e) {
        $authType = $authType ?? (Auth::check() ? 'authenticated' : 'guest');
    }

    $pad = $variant === 'mobile' ? 'px-4 py-3' : 'px-3 py-2';
    $rounded = $variant === 'mobile' ? 'rounded-xl' : 'rounded-lg';
    $extraItemClass = $variant === 'mobile' ? 'mobile-nav-item' : '';
    $marginGuest = $variant === 'mobile' ? 'mx-4' : 'mx-2';
    $marginApp = $variant === 'mobile' ? 'mx-3' : 'mx-2';
    $dropdownSuffix = $layout === 'app' ? '-app' : '';
    $idPrefix = $variant === 'mobile' ? 'mobile' : 'desktop';
@endphp

@if ($layout === 'guest')
    {{-- Home --}}
    <a href="{{ url('/') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ (request()->routeIs('home') || request()->is('/')) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.home') }}</span>
    </a>

    {{-- Creators --}}
    <a href="{{ url('/creator') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('creator.index') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.creators') }}</span>
    </a>

    {{-- Collections (carousel) --}}
    <a href="{{ route('collections.carousel') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('home.collections.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.collections') }}</span>
    </a>

    {{-- Collectors --}}
    <a href="{{ route('collector.index') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('collector.*') ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-cyan-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.collectors') }}</span>
    </a>

    {{-- Companies - Corporate Blue #1E3A5F, Gold #C9A227 --}}
    <a href="{{ url('/company') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-[#1E3A5F]/10 dark:hover:bg-[#1E3A5F]/30 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('company.*') ? 'bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 text-[#1E3A5F] dark:text-[#C9A227]' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-[#C9A227] rounded-lg bg-[#1E3A5F]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.companies') }}</span>
    </a>

    {{-- EPPs --}}
    <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="font-medium">{{ __('guest_layout.epps') }}</span>
    </a>

    {{-- Le mie Collezioni - dropdown (guest) --}}
    @can('create_EGI')
        @auth
            <button type="button" id="{{ $idPrefix }}-collection-list-dropdown-button" class="flex items-center justify-between w-full {{ $pad }} text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ $rounded }} {{ $extraItemClass }}" aria-expanded="false" aria-haspopup="true">
                <span class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 {{ $rounded }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                </span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
            </button>
            <div id="{{ $idPrefix }}-collection-list-dropdown-menu" class="{{ $marginGuest }} mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-xl border border-gray-200 bg-white py-2 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div id="{{ $idPrefix }}-collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">{{ __('collection.loading_galleries') }}</div>
                <div id="{{ $idPrefix }}-collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">{{ __('collection.no_galleries_found') }} <button type="button" data-action="open-create-collection-modal" class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</button></div>
                <div id="{{ $idPrefix }}-collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-500">{{ __('collection.error_loading_galleries') }}</div>
            </div>
        @endauth
    @endcan

    {{-- Create EGI --}}
    @can('create_EGI')
        <button type="button" class="flex items-center w-full {{ $pad }} space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 {{ $rounded }} {{ $extraItemClass }}" data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}" aria-label="{{ __('guest_layout.create_egi') }}">
            <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 {{ $rounded }}">
                <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
            </div>
            <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
        </button>
    @endcan

    {{-- Create Collection --}}
    @can('create_collection')
        <button type="button" data-action="open-create-collection-modal" class="flex items-center w-full {{ $pad }} space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 {{ $rounded }} {{ $extraItemClass }}" aria-label="{{ __('collection.create_collection') }}">
            <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 {{ $rounded }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
            </div>
            <span class="font-medium">{{ __('collection.create_collection') }}</span>
        </button>
    @endcan

@else
    {{-- App layout --}}
    <a href="{{ route('home') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <span class="font-medium">{{ __('Home') }}</span>
    </a>

    {{-- Standard Dashboard --}}
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <span class="font-medium">{{ __('Dashboard') }}</span>
    </a>

    {{-- EPP Dashboard - Only for EPP users --}}
    @if(Auth::check() && Auth::user()->usertype === 'epp')
        <a href="{{ route('epp.dashboard.index') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('epp.dashboard.*') ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}">
            <div class="flex items-center justify-center w-8 h-8 text-white bg-green-600 {{ $rounded }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="font-medium">{{ __('menu.epp_dashboard') }}</span>
        </a>
    @endif

    <a href="{{ route('collections.carousel') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('collections.carousel.*') ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white rounded-lg bg-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <span class="font-medium">{{ __('Collections') }}</span>
    </a>

    <a href="{{ route('epps.index') }}" class="flex items-center space-x-3 {{ $pad }} text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/20 {{ $rounded }} transition-colors {{ $extraItemClass }} {{ request()->routeIs('epps.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 text-white bg-orange-500 {{ $rounded }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="font-medium">{{ __('EPPS') }}</span>
    </a>

    {{-- Le mie Collezioni - dropdown (app) --}}
    @can('create_EGI')
        @auth
            <button type="button" id="{{ $idPrefix }}-collection-list-dropdown-button-app" class="flex items-center justify-between w-full {{ $pad }} text-gray-700 transition-colors dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 {{ $rounded }} {{ $extraItemClass }}" aria-expanded="false" aria-haspopup="true">
                <span class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 text-white bg-purple-500 {{ $rounded }}">
                        <span class="text-sm material-symbols-outlined" aria-hidden="true">view_carousel</span>
                    </div>
                    <span class="font-medium">{{ __('collection.my_galleries') }}</span>
                </span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
            </button>
            <div id="{{ $idPrefix }}-collection-list-dropdown-menu-app" class="{{ $marginApp }} mb-2 mt-1 hidden max-h-[40vh] overflow-y-auto rounded-md border border-gray-700 bg-gray-800 py-1 shadow-lg">
                <div id="{{ $idPrefix }}-collection-list-loading-app" class="px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.loading_galleries') }}</div>
                <div id="{{ $idPrefix }}-collection-list-empty-app" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.no_galleries_found') }} <button type="button" data-action="open-create-collection-modal" class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</button></div>
                <div id="{{ $idPrefix }}-collection-list-error-app" class="hidden px-4 py-3 text-sm text-center text-red-400">{{ __('collection.error_loading_galleries') }}</div>
            </div>
        @endauth
    @endcan

    {{-- Create EGI --}}
    @can('create_EGI')
        <button type="button" class="flex items-center w-full {{ $pad }} space-x-3 text-gray-700 transition-colors js-create-egi-contextual-button dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/20 {{ $rounded }} {{ $extraItemClass }}" data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}" aria-label="{{ __('guest_layout.create_egi') }}">
            <div class="flex items-center justify-center w-8 h-8 text-white bg-green-500 {{ $rounded }}">
                <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
            </div>
            <span class="font-medium js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
        </button>
    @endcan

    {{-- Create Collection --}}
    @can('create_collection')
        <button type="button" data-action="open-create-collection-modal" class="flex items-center w-full {{ $pad }} space-x-3 text-gray-700 transition-colors dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 {{ $rounded }} {{ $extraItemClass }}" aria-label="{{ __('collection.create_collection') }}">
            <div class="flex items-center justify-center w-8 h-8 text-white bg-indigo-500 {{ $rounded }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" /></svg>
            </div>
            <span class="font-medium">{{ __('collection.create_collection') }}</span>
        </button>
    @endcan
@endif
