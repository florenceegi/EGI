{{-- resources/views/company/index.blade.php --}}
<x-platform-layout :title="__('company.index.page_title')" :metaDescription="__('company.index.meta_description')">

    <x-slot name="platformHeaderBanner">
        <div class="relative overflow-hidden bg-gradient-to-br from-[#1E3A5F] via-[#2A4A73] to-[#1E3A5F]">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                    style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23C9A227\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                </div>
            </div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="text-center">
                    <span
                        class="mb-4 inline-block rounded-full bg-[#1E3A5F]/50 px-4 py-1 text-sm font-semibold uppercase tracking-wider text-[#C9A227]">
                        {{ __('company.index.badge') }}
                    </span>
                    <h1 class="font-display text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">
                        {{ __('company.index.main_title') }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-xl text-gray-300">
                        {{ __('company.index.subtitle') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="heroFullWidth">
        <div class="relative bg-gray-900 py-8">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                {{-- Search & Filters --}}
                <div class="mb-8 rounded-xl border border-[#1E3A5F]/30 bg-gray-800 p-6 shadow-lg">
                    <form action="{{ route('company.index') }}" method="GET"
                        class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <label for="query" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('company.index.search_placeholder') }}
                            </label>
                            <div class="relative">
                                <input type="search" name="query" id="query"
                                    value="{{ $filters['query'] ?? '' }}"
                                    placeholder="{{ __('company.index.search_placeholder') }}"
                                    class="block w-full rounded-lg border border-[#1E3A5F]/50 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 focus:border-[#C9A227] focus:ring-[#C9A227]">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="sort" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('company.index.sort_by') }}
                            </label>
                            <select id="sort" name="sort" onchange="this.form.submit()"
                                class="block w-full rounded-lg border border-[#1E3A5F]/50 bg-gray-700 px-4 py-3 text-white focus:border-[#C9A227] focus:ring-[#C9A227]">
                                <option value="latest" @selected(($filters['sort'] ?? '') == 'latest')>
                                    {{ __('company.index.sort_latest') }}</option>
                                <option value="oldest" @selected(($filters['sort'] ?? '') == 'oldest')>
                                    {{ __('company.index.sort_oldest') }}</option>
                                <option value="name_asc" @selected(($filters['sort'] ?? '') == 'name_asc')>
                                    {{ __('company.index.sort_name_asc') }}</option>
                                <option value="name_desc" @selected(($filters['sort'] ?? '') == 'name_desc')>
                                    {{ __('company.index.sort_name_desc') }}</option>
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Companies Grid --}}
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($companies as $company)
                        <a href="{{ route('company.home', $company->id) }}"
                            class="group block rounded-2xl border border-[#1E3A5F]/30 bg-gray-800/50 p-6 transition-all duration-300 hover:border-[#C9A227]/50 hover:bg-gray-800">
                            {{-- Company Logo --}}
                            <div class="relative mb-4 flex justify-center">
                                <div
                                    class="h-24 w-24 overflow-hidden rounded-xl ring-2 ring-[#1E3A5F]/50 transition-all group-hover:ring-[#C9A227]/50">
                                    <img src="{{ $company->profile_photo_url }}" alt="{{ $company->name }}"
                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                                </div>
                                @if ($company->is_verified ?? false)
                                    <div class="absolute -bottom-2 -right-2 rounded-full bg-[#2D7D46] p-1.5 shadow-lg">
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Company Info --}}
                            <div class="text-center">
                                <span
                                    class="mb-2 inline-block rounded bg-[#1E3A5F]/50 px-2 py-0.5 text-xs font-semibold uppercase tracking-wider text-[#C9A227]">
                                    {{ __('company.index.badge_company') }}
                                </span>
                                <h3 class="text-lg font-bold text-white transition-colors group-hover:text-[#C9A227]">
                                    {{ $company->name }}
                                </h3>
                                @if ($company->tagline)
                                    <p class="mt-1 line-clamp-2 text-sm text-gray-400">{{ $company->tagline }}</p>
                                @endif
                            </div>

                            {{-- Stats --}}
                            <div class="mt-4 flex justify-center gap-6 border-t border-gray-700/50 pt-4">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-[#C9A227]">
                                        {{ $company->createdEgis()->where('is_published', true)->count() }}</p>
                                    <p class="text-xs text-gray-400">EGI</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-[#C9A227]">
                                        {{ $company->collections()->where('is_published', true)->count() }}</p>
                                    <p class="text-xs text-gray-400">{{ __('company.index.collections') }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <div
                                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#1E3A5F]/20">
                                <svg class="h-10 w-10 text-[#C9A227]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-white">{{ __('company.index.empty_title') }}
                            </h3>
                            <p class="text-gray-400">{{ __('company.index.empty_description') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($companies->hasPages())
                    <div class="mt-8">
                        {{ $companies->links() }}
                    </div>
                @endif

            </div>
        </div>
    </x-slot>

</x-platform-layout>
