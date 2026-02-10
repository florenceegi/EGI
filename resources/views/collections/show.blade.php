{{-- resources/views/collections/show.blade.php --}}
{{-- 🎨 ORACODE REDESIGN: Galleria Imponente Mobile-First --}}
{{-- Trasformazione orchestrata per massimo impatto visivo e UX --}}
{{-- Focus: Hero Impact + Info Critica + Griglia Adattiva + Micro-animazioni --}}
{{-- Includiamo il layout principale per le collezioni --}}
{{-- Questo layout gestisce il titolo, la descrizione e gli script condivisi --}}

@props([
    'collection' => collect(),
])

@php
    if (is_array($collection)) {
        $collection = collect($collection);
    }
    // Verifica se questa collection è essa stessa un'iniziativa EPP (es. creata da un EPP user o legata al progetto come core)
$isEppCollection = $collection->creator && in_array($collection->creator->usertype, ['epp', 'natan', 'frangette']);
@endphp

<x-collection-layout :title="$collection->collection_name . ' | FlorenceEGI'" :metaDescription="Str::limit($collection->description, 155) ??
    __('collection.show.details_for_collection') . ' ' . $collection->collection_name">

    {{-- Set View Context for AI Sidebar --}}
    @push('scripts')
        <script>
            // Set current view context for AI
            window.currentView = 'collection.show';
        </script>
    @endpush

    {{-- Schema.org ottimizzato --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "{{ $collection->collection_name }}",
            "description": "{{ $collection->description }}",
            "image": "{{ (method_exists($collection, 'getFirstMediaUrl') && $collection->getFirstMediaUrl('head', 'banner')) ? $collection->getFirstMediaUrl('head', 'banner') : ($collection->image_banner ? Storage::url($collection->image_banner) : asset('images/default_banner.jpg')) }}",
            "author": {
                "@type": "Person",
                "name": "{{ $collection->creator->name ?? __('collection.show.unknown_creator_schema') }}"
            },
            "numberOfItems": "{{ $collection->egis_count ?? 0 }}",
            "mainEntity": {
                "@type": "CreativeWork",
                "name": "{{ $collection->collection_name }}"
            }
        }
        </script>
    </x-slot>

    {{-- 🎯 SEZIONE STATISTICHE PAYMENT DISTRIBUTION --}}
    <div class="border-b border-gray-800 bg-gray-900">
        <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
            {{-- Breadcrumb migliorato --}}
            <nav class="mb-4 flex items-center space-x-2 text-sm text-gray-400" aria-label="Breadcrumb">
                <a href="{{ route('home.collections.index') }}"
                    class="transition-colors duration-200 hover:text-emerald-400">
                    <span class="material-symbols-outlined mr-1 text-base">collections</span>
                    {{ __('collection.show.collections_breadcrumb') }}
                </a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="font-medium text-gray-300">{{ Str::limit($collection->collection_name, 30) }}</span>
            </nav>

            {{-- Statistiche PaymentDistribution per desktop e mobile --}}
            <x-hero-banner-stats :collection="$collection" />
        </div>
    </div>

    {{-- 🎨 HERO BANNER POTENZIATO - Mobile Responsive --}}
    <section class="relative min-h-[50vh] overflow-hidden sm:min-h-[55vh]">
        {{-- Background senza Parallax Effect --}}
        <div class="absolute inset-0 z-0">
            @php
                // Prova ad usare Spatie Media se disponibile
                $bannerUrl = method_exists($collection, 'getFirstMediaUrl')
                    ? $collection->getFirstMediaUrl('head', 'banner')
                    : null;
            @endphp
            @if ($bannerUrl || $collection->image_banner)
                <img src="{{ $bannerUrl ?: $collection->image_banner }}"
                    alt="Banner for {{ $collection->collection_name }}" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900"></div>
            @endif
            {{-- Overlay gradiente potenziato --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
        </div>

        {{-- Top Section: Creator Info + Upload Button - Solo mobile positioning --}}
        <div
            class="absolute left-4 right-4 top-4 z-20 flex items-center justify-between sm:static sm:bottom-8 sm:left-8 sm:right-8 sm:top-8 sm:h-auto sm:flex-col sm:items-start sm:justify-between sm:bg-transparent">
            {{-- Creator Info - Desktop: parte della hero content, Mobile: top bar --}}
            <div class="sm:hidden">
                @if ($collection->creator)
                    <a href="{{ route('creator.home', ['id' => $collection->creator->id]) }}"
                        class="group flex items-center gap-3 transition-all duration-200 hover:opacity-80">
                        <div class="flex-shrink-0">
                            @if ($collection->creator->profile_photo_url)
                                <img src="{{ $collection->creator->profile_photo_url }}"
                                    alt="{{ $collection->creator->name }}"
                                    class="h-10 w-10 rounded-full border-2 border-white/30 object-cover transition-transform duration-200 group-hover:scale-110">
                            @else
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 transition-transform duration-200 group-hover:scale-110">
                                    <span
                                        class="text-sm font-bold text-white">{{ substr($collection->creator->name ?? 'U', 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-semibold text-white transition-colors duration-200 group-hover:text-emerald-400">{{ $collection->creator->name ?? __('collection.show.unknown_creator') }}</span>
                                @if ($collection->creator->usertype === 'verified')
                                    <span class="material-symbols-outlined text-sm text-blue-400"
                                        title="{{ __('collection.show.verified_creator') }}">verified</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-300 transition-colors duration-200 group-hover:text-gray-200">
                                {{ __('collection.show.collection_creator') }}</p>
                        </div>
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-blue-500">
                                <span class="text-sm font-bold text-white">U</span>
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-sm font-semibold text-white">{{ __('collection.show.unknown_creator') }}</span>
                            <p class="text-xs text-gray-300">{{ __('collection.show.collection_creator') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Upload Button - Solo mobile top bar --}}
            <div class="sm:hidden">
                @can('create_collection')
                    <button id="uploadBannerBtn"
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 text-white backdrop-blur-sm transition-colors hover:bg-indigo-700"
                        data-uploading-label="{{ __('collection.show.uploading') }}"
                        data-upload-success="{{ __('collection.show.banner_updated') }}"
                        data-upload-error="{{ __('collection.show.banner_upload_error') }}"
                        data-upload-label="{{ __('collection.show.upload_banner') }}"
                        title="{{ __('collection.show.upload_banner') }}">
                        <span class="material-symbols-outlined text-lg">upload</span>
                    </button>
                    <input type="file" id="bannerFileInput" accept="image/*" class="hidden" />
                @endcan
            </div>
        </div>

        {{-- Desktop Upload Button - Posizionamento originale --}}
        <div class="absolute right-8 top-8 z-20 hidden sm:block">
            {{-- @if (auth()->check() && auth()->id() === ($collection->creator_id ?? null)) --}}
            @if ($collection->userHasPermission(Auth::id(), 'create_collection'))
                <button id="uploadBannerBtnDesktop"
                    class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition-colors hover:bg-indigo-700"
                    data-uploading-label="{{ __('collection.show.uploading') }}"
                    data-upload-success="{{ __('collection.show.banner_updated') }}"
                    data-upload-error="{{ __('collection.show.banner_upload_error') }}"
                    data-upload-label="{{ __('collection.show.upload_banner') }}"
                    title="{{ __('collection.show.upload_banner') }}">
                    <span class="material-symbols-outlined text-base">upload</span>
                    <span>{{ __('collection.show.upload_banner') }}</span>
                </button>
                <input type="file" id="bannerFileInputDesktop" accept="image/*" class="hidden" />
            @endif
        </div>

        {{-- CTA Section - Positioned at bottom right --}}
        <div class="absolute bottom-6 right-4 z-20 flex gap-2 sm:bottom-8 sm:right-8">
            {{-- Like Button - New Component --}}
            <x-like-button :resourceType="'collection'" :resourceId="$collection->id" :isLiked="$collection->is_liked ?? false" :likesCount="$collection->likes_count ?? 0" size="medium" />

            {{-- Commerce Setup Button - P0 NEW --}}
            @if ($collection->userHasPermission(Auth::id(), 'create_collection'))
                <a href="{{ route('collections.commerce.wizard', $collection) }}"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-500/40 bg-green-600/80 text-sm font-medium text-white backdrop-blur-sm transition-all duration-300 hover:bg-green-600 sm:h-auto sm:w-auto sm:px-4 sm:py-2"
                    title="Commerce Setup">
                    <span class="material-symbols-outlined mr-0 text-lg sm:mr-1 sm:text-base">storefront</span>
                    <span class="hidden text-sm sm:inline">Commerce</span>
                </a>
            @endif

            {{-- Dashboard Button - NEW --}}
            @if ($collection->userHasPermission(Auth::id(), 'create_collection'))
                <button id="openDashboardBtn"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-500/40 bg-indigo-600/80 text-sm font-medium text-white backdrop-blur-sm transition-all duration-300 hover:bg-indigo-600 sm:h-auto sm:w-auto sm:px-4 sm:py-2"
                    title="Dashboard & Monetization">
                    <span class="material-symbols-outlined mr-0 text-lg sm:mr-1 sm:text-base">dashboard</span>
                    <span class="hidden text-sm sm:inline">Dashboard</span>
                </button>
            @endif

            {{-- Share Button - Compact --}}
            <button
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-sm font-medium text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 sm:h-auto sm:w-auto sm:px-4 sm:py-2"
                onclick="navigator.share ? navigator.share({title: '{{ $collection->collection_name }}', url: window.location.href}) : copyToClipboard(window.location.href)"
                title="{{ __('collection.show.share') }}">
                <span class="material-symbols-outlined mr-0 text-lg sm:mr-1 sm:text-base">share</span>
                <span class="hidden text-sm sm:inline">{{ __('collection.show.share') }}</span>
            </button>

            {{-- Edit Button - Compact --}}
            @if ($collection->userHasPermission(Auth::id(), 'create_collection'))
                <button id="editMetaBtn"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-sm font-medium text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 sm:h-auto sm:w-auto sm:px-4 sm:py-2"
                    title="{{ __('collection.show.edit_button') }}">
                    <span class="material-symbols-outlined mr-0 text-lg sm:mr-1 sm:text-base">edit</span>
                    <span class="hidden text-sm sm:inline">{{ __('collection.show.edit_button') }}</span>
                </button>
            @endif

            {{-- Team Management Button - Compact --}}
            @if ($collection->userHasPermission(Auth::id(), 'create_team'))
                <a href="{{ route('collections.collection_user', ['id' => $collection->id]) }}"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-sm font-medium text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20 sm:h-auto sm:w-auto sm:px-4 sm:py-2"
                    title="{{ __('collection.show.manage_team') }}">
                    <span class="material-symbols-outlined mr-0 text-lg sm:mr-1 sm:text-base">group</span>
                    <span class="hidden text-sm sm:inline">{{ __('collection.show.manage_team') }}</span>
                </a>
            @endif
        </div>
        </div>

        {{-- Hero Content - Layout responsive --}}
        <div class="container relative z-10 mx-auto px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16">
            <div class="max-w-4xl">
                {{-- Creator Info - Solo Desktop in alto --}}
                <div class="mb-8 hidden sm:block">
                    @if ($collection->creator)
                        <a href="{{ route('creator.home', ['id' => $collection->creator->id]) }}"
                            class="group inline-flex items-center gap-4 transition-all duration-200 hover:opacity-90">
                            <div class="flex-shrink-0">
                                @if ($collection->creator->profile_photo_url)
                                    <img src="{{ $collection->creator->profile_photo_url }}"
                                        alt="{{ $collection->creator->name }}"
                                        class="h-16 w-16 rounded-full border-2 border-white/40 object-cover transition-transform duration-200 group-hover:scale-105">
                                @else
                                    <div
                                        class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 transition-transform duration-200 group-hover:scale-105">
                                        <span
                                            class="text-xl font-bold text-white">{{ substr($collection->creator->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="mb-1 flex items-center gap-2">
                                    <span
                                        class="text-xl font-bold text-white transition-colors duration-200 group-hover:text-emerald-400">{{ $collection->creator->name ?? __('collection.show.unknown_creator') }}</span>
                                    @if ($collection->creator->usertype === 'verified')
                                        <span class="material-symbols-outlined text-xl text-blue-400"
                                            title="{{ __('collection.show.verified_creator') }}">verified</span>
                                    @endif
                                </div>
                                <p
                                    class="text-base text-gray-200 transition-colors duration-200 group-hover:text-white">
                                    {{ __('collection.show.collection_creator') }}</p>
                            </div>
                        </a>
                    @else
                        <div class="inline-flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-blue-500">
                                    <span class="text-xl font-bold text-white">U</span>
                                </div>
                            </div>
                            <div>
                                <span
                                    class="text-xl font-bold text-white">{{ __('collection.show.unknown_creator') }}</span>
                                <p class="text-base text-gray-200">{{ __('collection.show.collection_creator') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Collection Title + Description --}}
                <div class="mt-16 sm:mt-40">
                    <div class="mb-4 flex items-start gap-4">
                        <h1 id="collection-title"
                            class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                            {{ $collection->collection_name }}
                        </h1>

                        @if ($isEppCollection)
                            <div
                                class="mt-1 rounded-full border border-green-600/50 bg-[#2D5016] px-3 py-1 text-sm font-bold text-white shadow-lg backdrop-blur-sm">
                                {{ __('collection.show.official_epp_collection') }}
                            </div>
                        @endif
                    </div>

                    @if ($collection->description)
                        <p id="collection-description"
                            class="line-clamp-3 text-base leading-relaxed text-gray-200 sm:line-clamp-none sm:text-lg">
                            {{ $collection->description }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Edit Meta Modal (partial) --}}
    @include('collections.partials.edit-meta-modal', ['collection' => $collection])

    {{-- Dashboard Monetization Modal (partial) --}}
    @if ($collection)
        @include('collections.partials.dashboard-modal', ['collection' => $collection])
    @endif

    {{-- 🌱 EPP PROJECT SELECTION MODAL --}}
    <div id="eppProjectSelectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 backdrop-blur-sm"
        aria-labelledby="eppProjectSelectionModalLabel" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="relative w-full max-w-6xl rounded-2xl border border-gray-700 bg-gray-900 shadow-2xl">
                {{-- Header --}}
                <div
                    class="flex items-center justify-between border-b border-gray-700 bg-gradient-to-r from-green-900/30 to-emerald-900/30 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600/20">
                            <span class="material-symbols-outlined text-xl text-green-400">eco</span>
                        </div>
                        <div>
                            <h3 id="eppProjectSelectionModalLabel" class="text-xl font-bold text-white">
                                {{ __('collection.show.select_environmental_project') }}</h3>
                            <p class="text-sm text-gray-400">
                                {{ __('collection.show.choose_epp_project_description') }}</p>
                        </div>
                    </div>
                    <button id="closeEppProjectSelectionX" class="text-gray-400 transition-colors hover:text-white">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-6">
                    {{-- Filters --}}
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row">
                        <input type="text" id="eppProjectSearchInput"
                            placeholder="{{ __('collection.show.search_projects_organizations') }}"
                            class="flex-1 rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        <select id="eppProjectTypeFilter"
                            class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            <option value="">{{ __('collection.show.all_project_types') }}</option>
                            <option value="ARF">{{ __('collection.show.project_type_arf') }}</option>
                            <option value="APR">{{ __('collection.show.project_type_apr') }}</option>
                            <option value="BPE">{{ __('collection.show.project_type_bpe') }}</option>
                        </select>
                    </div>

                    {{-- Loading State --}}
                    <div id="eppProjectLoadingState" class="hidden p-12 text-center">
                        <div
                            class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-green-500 border-t-transparent">
                        </div>
                        <p class="mt-4 text-gray-400">{{ __('collection.show.loading_environmental_projects') }}</p>
                    </div>

                    {{-- EPP Project Grid --}}
                    <div id="eppProjectGrid" class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"></div>

                    {{-- Empty State --}}
                    <div id="eppProjectEmptyState"
                        class="hidden rounded-xl border border-gray-700 bg-gray-800/30 p-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-600">search_off</span>
                        <p class="mt-2 text-gray-400">{{ __('collection.show.no_projects_found') }}</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-between gap-3 border-t border-gray-700 bg-gray-800/30 px-6 py-4">
                    <button id="closeEppProjectSelectionBtn"
                        class="rounded-lg border border-gray-600 px-6 py-2 text-gray-300 transition-colors hover:bg-gray-700">
                        {{ __('collection.show.cancel') }}
                    </button>
                    <button id="confirmEppProjectSelectionBtn" disabled
                        class="rounded-lg bg-green-600 px-6 py-2 font-semibold text-white transition-colors hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50">
                        {{ __('collection.show.confirm_selection') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌳 EPP PROJECT SECTION (Mostra SOLO se NON è una collezione di tipo EPP) --}}
    @if ($collection->eppProject && !$isEppCollection)
        <div class="border-b border-gray-800 bg-gradient-to-r from-green-900/20 to-emerald-900/20">
            <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-xl border border-white/10 p-4 sm:p-6">
                    {{-- Background Image with Overlay DENTRO IL BOX --}}
                    @if ($collection->eppProject->getFirstMediaUrl('project_images'))
                        <div class="absolute inset-0">
                            <img src="{{ $collection->eppProject->getFirstMediaUrl('project_images') }}"
                                alt="{{ $collection->eppProject->name }}" class="h-full w-full object-cover">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-[#2D5016]/95 via-[#2D5016]/85 to-[#1B365D]/90">
                            </div>
                        </div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-r from-green-900/60 to-emerald-900/60"></div>
                    @endif
                    <div class="relative flex items-start gap-4">
                        {{-- Avatar del progetto --}}
                        @if ($collection->eppProject->getFirstMediaUrl('project_avatar'))
                            <img src="{{ $collection->eppProject->getFirstMediaUrl('project_avatar') }}"
                                alt="{{ $collection->eppProject->name }}"
                                class="h-16 w-16 flex-shrink-0 rounded-full object-cover ring-4 ring-green-400/50">
                        @else
                            <div class="flex-shrink-0 rounded-full bg-green-500/30 p-3 ring-4 ring-green-400/50">
                                <span class="text-2xl text-green-400">
                                    @if ($collection->eppProject->project_type === 'ARF')
                                        🌳
                                    @elseif($collection->eppProject->project_type === 'APR')
                                        🌊
                                    @elseif($collection->eppProject->project_type === 'BPE')
                                        🐝
                                    @else
                                        🌱
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <h3 class="mb-2 text-lg font-semibold text-white drop-shadow-lg">
                                {{ __('collection.show.supporting_environmental_project') }}</h3>
                            <h4 class="mb-1 text-xl font-bold text-emerald-300 drop-shadow-lg">
                                {{ $collection->eppProject->name }}</h4>
                            <p class="mb-2 text-xs font-medium text-gray-200 drop-shadow">
                                {{ __('collection.show.by') }}
                                {{ $collection->eppProject->eppUser->organizationData->organization_name ?? $collection->eppProject->eppUser->name }}
                            </p>
                            <p class="mb-3 line-clamp-2 text-sm text-gray-100 drop-shadow">
                                {{ $collection->eppProject->description }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-green-900/50 px-2 py-1 text-xs font-medium text-green-300">
                                        {{ round($collection->eppProject->completion_percentage) }}%
                                        {{ __('collection.show.completed') }}
                                    </span>
                                    @if ($collection->eppProject->project_type)
                                        <span
                                            class="@if ($collection->eppProject->project_type === 'ARF') bg-green-500/30 text-green-300
                                            @elseif($collection->eppProject->project_type === 'APR') bg-blue-500/30 text-blue-300
                                            @elseif($collection->eppProject->project_type === 'BPE') bg-yellow-500/30 text-yellow-300
                                            @else bg-gray-500/30 text-gray-300 @endif rounded-full px-2 py-0.5 text-xs font-medium">
                                            {{ $collection->eppProject->project_type }}
                                        </span>
                                    @endif
                                </div>
                                <span class="rounded bg-green-900/50 px-2 py-1 text-xs font-medium text-green-300">
                                    {{ __('collection.show.epp_percentage') }}
                                    {{ __('collection.show.of_sales_support_this_project') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($collection->epp && !$isEppCollection)
        {{-- FALLBACK: Old EPP relationship (deprecated) --}}
        <div class="border-b border-gray-800 bg-gradient-to-r from-green-900/20 to-emerald-900/20">
            <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="hero-glass rounded-xl p-4 sm:p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 rounded-lg bg-green-500/20 p-3">
                            <span class="material-symbols-outlined text-2xl text-green-400">eco</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="mb-2 text-lg font-semibold text-white">
                                {{ __('collection.show.supporting_environmental_project') }}</h3>
                            <h4 class="mb-2 font-medium text-emerald-400">{{ $collection->epp->name }}</h4>
                            <p class="mb-3 line-clamp-2 text-sm text-gray-300">{{ $collection->epp->description }}</p>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-medium text-green-400">{{ __('collection.show.epp_percentage') }}
                                    {{ __('collection.show.of_sales_support_this_project') }}</span>
                                <a href="{{ route('epps.show', $collection->epp_id) }}"
                                    class="flex items-center gap-1 text-sm font-medium text-emerald-400 hover:text-emerald-300">
                                    {{ __('collection.show.learn_more') }}
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 🎨 GRIGLIA EGI PRINCIPALE --}}
    <main class="bg-gray-900 py-8 sm:py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header con Filtri --}}
            <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="mb-2 text-2xl font-bold text-white sm:text-3xl">
                        {{ __('collection.show.collection_items') }}
                    </h2>
                    <p class="text-sm text-gray-400">
                        {{ $collection->egis_count ?? 0 }} {{ __('collection.show.unique_digital_assets') }}
                    </p>
                </div>

                {{-- Filtri e ordinamento --}}
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <select id="egis-sort"
                        class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-2 text-sm text-white focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                        <option value="position">{{ __('collection.show.position') }}</option>
                        <option value="newest">{{ __('collection.show.newest') }}</option>
                        <option value="oldest">{{ __('collection.show.oldest') }}</option>
                        <option value="price_low">{{ __('collection.show.price_low_to_high') }}</option>
                        <option value="price_high">{{ __('collection.show.price_high_to_low') }}</option>
                    </select>

                    {{-- View Selector Component --}}
                    @php
                        // Calcola il numero di holder unici per questa collezione
                        $totalHolders = DB::table('reservations')
                            ->join('egis', 'egis.id', '=', 'reservations.egi_id')
                            ->where('egis.collection_id', $collection->id)
                            ->where('reservations.is_highest', true)
                            ->where('reservations.is_current', true)
                            ->whereNull('egis.deleted_at')
                            ->distinct('reservations.user_id')
                            ->count('reservations.user_id');
                    @endphp
                    <x-collection.view-selector :totalItems="$collection->egis_count ?? 0" :totalHolders="$totalHolders" />
                </div>
            </div>

            {{-- Container EGI Responsivo --}}
            <div class="space-y-4" id="egis-container">
                @php
                    // Determina se l'utente corrente è il creator di questa collezione
$isCreatorViewing = false;
if (auth()->check()) {
    $isCreatorViewing = auth()->id() === $collection->creator_id;
} elseif (session('connected_user_id')) {
    $isCreatorViewing = session('connected_user_id') === $collection->creator_id;
                    }

                    // TEMPORARY: Forziamo per test se è la collezione del creator ID 4
                    if ($collection->creator_id === 4) {
                        $isCreatorViewing = true;
                    }
                @endphp

                @forelse($collection->egis as $index => $egi)
                    {{-- Grid Item (shown in grid mode) --}}
                    <div class="egi-item card-hover grid-view" style="display: none;">
                        <x-egi-card :egi="$egi" :collection="$collection" :portfolioContext="$isCreatorViewing" :portfolioOwner="$isCreatorViewing ? $collection->creator : null"
                            :creatorPortfolioContext="$isCreatorViewing" />
                    </div>

                    {{-- List Item (shown in list mode) --}}
                    <div class="egi-item list-view">
                        <x-egi-card-list :egi="$egi" :context="'collection'" :showBadge="false" :showPurchasePrice="false"
                            :showOwnershipBadge="false" />
                    </div>
                @empty
                    {{-- Stato Vuoto Migliorato --}}
                    <div class="col-span-full">
                        <div class="px-6 py-16 text-center">
                            <div
                                class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-800">
                                <span class="material-symbols-outlined text-2xl text-gray-400">image</span>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-white">{{ __('collection.show.no_egis_yet') }}
                            </h3>
                            <p class="mx-auto mb-6 max-w-md text-gray-400">
                                {{ __('collection.show.no_egis_message') }}
                            </p>
                            @if (auth()->id() === $collection->creator_id)
                                <button class="btn-primary-glow rounded-lg px-6 py-3 font-semibold text-white">
                                    <span class="material-symbols-outlined mr-2">add</span>
                                    {{ __('collection.show.add_first_egi') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Holders Container --}}
            <div id="holders-container" style="display: none;">
                <x-collection.holders-list :collection="$collection" />
            </div>

            {{-- Traits Container (placeholder for future) --}}
            <div id="traits-container" style="display: none;">
                <div class="rounded-lg bg-gray-800 p-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-700">
                        <span class="material-symbols-outlined text-2xl text-gray-400">category</span>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-white">{{ __('collection.traits.coming_soon') }}</h3>
                    <p class="text-gray-400">{{ __('collection.traits.coming_soon_message') }}</p>
                </div>
            </div>

            {{-- Load More Button (se necessario) --}}
            @if ($collection->egis->count() >= 20)
                <div class="mt-12 text-center">
                    <button
                        class="rounded-lg bg-gray-800 px-8 py-3 font-medium text-white transition-colors duration-200 hover:bg-gray-700">
                        {{ __('collection.show.load_more_items') }}
                    </button>
                </div>
            @endif
        </div>
    </main>

    {{-- 📚 COLLEZIONI CORRELATE --}}
    @if (isset($relatedCollections) && $relatedCollections->count() > 0)
        <section class="bg-gray-800 py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="mb-8 text-center text-2xl font-bold text-white">
                    {{ __('collection.show.more_from_this_creator') }}
                </h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedCollections->take(3) as $relatedCollection)
                        <div class="card-hover">
                            <x-collection-card :collection="$relatedCollection" imageType="card" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 🚀 FLOATING ACTIONS (Mobile) --}}
    <div class="floating-actions lg:hidden">
        <div
            class="flex items-center gap-3 rounded-full border border-gray-700 bg-gray-900/90 px-4 py-3 backdrop-blur-sm">
            <button
                class="flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700">
                <span class="material-symbols-outlined text-sm">favorite_border</span>
                {{ __('collection.show.like_collection') }}
            </button>
            <button
                class="flex items-center gap-2 rounded-full bg-gray-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-600">
                <span class="material-symbols-outlined text-sm">share</span>
                {{ __('collection.show.share') }}
            </button>
        </div>
    </div>

    {{-- 🤖 AI Sidebar - Context-Aware Assistant (Owner/Visitor/Guest) --}}
    @php
        $isOwner = auth()->check() && $collection->creator && auth()->id() === $collection->creator->id;
        $sidebarUser = $isOwner ? $collection->creator : (auth()->user() ?? $collection->creator);
        $sidebarUserType = $isOwner
            ? (in_array($collection->creator->usertype, ['company', 'Company']) ? 'company' : 'creator')
            : (auth()->check() ? (auth()->user()->usertype ?? 'creator') : 'creator');
    @endphp

    <x-ai-sidebar
        :user="$sidebarUser"
        :userType="$sidebarUserType"
        :checklist="$isOwner ? $onboardingChecklist : []"
        :contextMessage="$sidebarContextMessage"
        :showChecklist="$isOwner"
    />

    {{-- JavaScript Enhancements --}}
    @push('scripts')
        <script>
            // EGI sorting functionality
            document.getElementById('egis-sort').addEventListener('change', function() {
                const sortValue = this.value;
                const container = document.getElementById('egis-container');
                const items = Array.from(container.querySelectorAll('.egi-item'));

                items.sort((a, b) => {
                    switch (sortValue) {
                        case 'newest':
                            // Assume data-created attribute exists or implement accordingly
                            return new Date(b.dataset.created || 0) - new Date(a.dataset.created || 0);
                        case 'oldest':
                            return new Date(a.dataset.created || 0) - new Date(b.dataset.created || 0);
                        case 'price_low':
                            return (parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0));
                        case 'price_high':
                            return (parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0));
                        default: // position
                            return (parseInt(a.dataset.position || 999) - parseInt(b.dataset.position || 999));
                    }
                });

                // Re-append sorted items
                items.forEach(item => container.appendChild(item));
            });

            // Parallax rimosso per evitare effetti strani sui pulsanti

            // Copy to clipboard utility
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    // Show toast notification
                    const toast = document.createElement('div');
                    toast.className =
                        'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-green-600 rounded-lg bottom-4 left-1/2';
                    toast.textContent = '{{ __('collection.show.link_copied_to_clipboard') }}';
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                });
            }

            // Enhanced scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.egi-item, .stat-card').forEach(el => {
                observer.observe(el);
            });

            // Banner upload (vanilla JS) - Gestisce sia mobile che desktop
            (function() {
                // Setup per mobile
                const btnMobile = document.getElementById('uploadBannerBtn');
                const inputMobile = document.getElementById('bannerFileInput');

                // Setup per desktop
                const btnDesktop = document.getElementById('uploadBannerBtnDesktop');
                const inputDesktop = document.getElementById('bannerFileInputDesktop');

                // Funzione condivisa per upload
                const handleUpload = async (btn, input) => {
                    const file = input.files && input.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('banner', file);

                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const url =
                        "{{ route('collections.banner.upload', ['collection' => $collection->id ?? 0], false) }}";

                    try {
                        btn.disabled = true;
                        const originalContent = btn.innerHTML;
                        btn.innerHTML = btn.getAttribute('data-uploading-label') || 'Uploading…';

                        const uploadWithFetch = async () => {
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin',
                            });

                            const data = await res.clone().json().catch(() => null);
                            if (!res.ok || !data || !data.success) {
                                throw new Error((data && data.message) || 'Upload failed');
                            }

                            return data;
                        };

                        const uploadWithXHR = () => new Promise((resolve, reject) => {
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', url, true);
                            xhr.responseType = 'json';
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                            xhr.onload = () => {
                                const data = xhr.response ?? null;
                                if (xhr.status >= 200 && xhr.status < 300 && data && data.success) {
                                    resolve(data);
                                } else {
                                    reject(new Error((data && data.message) || 'Upload failed'));
                                }
                            };

                            xhr.onerror = () => reject(new Error('Upload failed (network error)'));
                            xhr.send(formData);
                        });

                        let data;
                        try {
                            data = await uploadWithFetch();
                        } catch (err) {
                            if (err instanceof TypeError) {
                                data = await uploadWithXHR();
                            } else {
                                throw err;
                            }
                        }

                        // Aggiorna l'immagine del banner a caldo
                        let img = document.querySelector('section img');
                        const newSrc = (data.banner_url || data.original_url) ? (data.banner_url || data
                            .original_url) + `?t=${Date.now()}` : null;
                        if (newSrc && img) {
                            img.src = newSrc;
                        }

                        // Toast
                        const toast = document.createElement('div');
                        toast.className =
                            'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-emerald-600 rounded-lg bottom-4 left-1/2';
                        toast.textContent = btn.getAttribute('data-upload-success') || 'Updated';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 2500);

                        btn.innerHTML = originalContent;
                    } catch (e) {
                        console.error('Upload error', e);
                        const toast = document.createElement('div');
                        toast.className =
                            'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-red-600 rounded-lg bottom-4 left-1/2';
                        toast.textContent = btn.getAttribute('data-upload-error') || 'Error';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);

                        btn.innerHTML = btn.getAttribute('data-upload-label') || 'Upload banner';
                    } finally {
                        btn.disabled = false;
                        input.value = '';
                    }
                };

                // Event listeners per mobile
                if (btnMobile && inputMobile) {
                    btnMobile.addEventListener('click', () => inputMobile.click());
                    inputMobile.addEventListener('change', () => handleUpload(btnMobile, inputMobile));
                }

                // Event listeners per desktop
                if (btnDesktop && inputDesktop) {
                    btnDesktop.addEventListener('click', () => inputDesktop.click());
                    inputDesktop.addEventListener('change', () => handleUpload(btnDesktop, inputDesktop));
                }
            })();
        </script>
        {{-- JS per modale Edit Meta --}}
        @vite(['resources/js/collection-edit-modal.js'])

        {{-- Dashboard Modal JS --}}
        <script>
            // Dashboard Modal Management
            const dashboardModal = document.getElementById('dashboardModal');
            const openDashboardBtn = document.getElementById('openDashboardBtn');
            const closeDashboardX = document.getElementById('closeDashboardX');
            const closeDashboardBtn = document.getElementById('closeDashboardBtn');

            if (openDashboardBtn) {
                openDashboardBtn.addEventListener('click', () => {
                    dashboardModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            function closeDashboard() {
                dashboardModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Close handlers
            if (closeDashboardX) {
                closeDashboardX.addEventListener('click', closeDashboard);
            }
            if (closeDashboardBtn) {
                closeDashboardBtn.addEventListener('click', closeDashboard);
            }

            // Close on backdrop click
            dashboardModal.addEventListener('click', (e) => {
                if (e.target === dashboardModal) {
                    closeDashboard();
                }
            });

            // Tab Switching (Unified for Desktop and Mobile)
            function switchTab(tabId) {
                // Update Desktop Tabs
                document.querySelectorAll('.dashboard-tab').forEach(t => {
                    if (t.dataset.tab === tabId) {
                        t.classList.add('active');
                    } else {
                        t.classList.remove('active');
                    }
                });

                // Update Mobile Tabs
                document.querySelectorAll('.dashboard-tab-mobile').forEach(t => {
                    if (t.dataset.tab === tabId) {
                        t.classList.add('active');
                    } else {
                        t.classList.remove('active');
                    }
                });

                // Show Content
                document.querySelectorAll('.dashboard-tab-content').forEach(c => c.classList.add('hidden'));

                // Map 'payments' to new ID if needed, or unify.
                let targetId = `tab-${tabId}`;
                if (tabId === 'payments') {
                    targetId = 'tab-payments-content';
                }

                const content = document.getElementById(targetId);
                if (content) content.classList.remove('hidden');
            }

            // Event Listeners for Desktop Tabs
            document.querySelectorAll('.dashboard-tab').forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });

            // Event Listeners for Mobile Tabs
            document.querySelectorAll('.dashboard-tab-mobile').forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });

            // Switch to Subscription
            function selectSubscriptionTier(tierCode) {
                const tierNames = {
                    'tier_1_19': 'Starter (€4.90/month)',
                    'tier_20_49': 'Basic (€7.90/month)',
                    'tier_50_99': 'Professional (€9.90/month)',
                    'tier_100_plus': 'Unlimited (€19.90/month)'
                };

                Swal.fire({
                    title: 'Switch to Subscription?',
                    html: `You are about to switch to <strong>${tierNames[tierCode]}</strong>.<br><br>
                       <span class="text-yellow-400">⚠️ Collection will be non-publishable until payment is completed.</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Continue to Payment',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#6B7280',
                    background: '#1F2937',
                    color: '#F3F4F6',
                    customClass: {
                        popup: 'border border-gray-700',
                        confirmButton: 'px-4 py-2 rounded-lg',
                        cancelButton: 'px-4 py-2 rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        Swal.fire({
                            title: 'Processing...',
                            text: 'Redirecting to payment',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: '#1F2937',
                            color: '#F3F4F6'
                        });

                        fetch(`/home/collections/{{ $collection->id }}/monetization/switch-to-subscription`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    tier_code: tierCode
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log('Subscription response:', data);
                                Swal.close();

                                if (data.success) {
                                    // Subscription activated successfully
                                    Swal.fire({
                                        title: 'Success!',
                                        html: `
                                            <p class="mb-2">Subscription activated successfully!</p>
                                            <p class="text-sm text-gray-400">Expires: ${data.data.expires_at}</p>
                                            <p class="text-sm text-gray-400">Days remaining: ${data.data.days_remaining}</p>
                                        `,
                                        icon: 'success',
                                        confirmButtonColor: '#10B981',
                                        background: '#1F2937',
                                        color: '#F3F4F6'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else if (data.message === 'Insufficient Egili balance') {
                                    // Insufficient balance - Open Egili purchase modal (same style as AI features)
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Crediti Insufficienti',
                                        html: `
                                            <p class="mb-3">Non hai abbastanza Egili per questa operazione.</p>
                                            <div class="p-3 text-left border border-red-200 rounded bg-red-50">
                                                <p class="text-sm"><strong>Richiesti:</strong> ${data.required_egili || 5000} Egili</p>
                                                <p class="text-sm"><strong>Disponibili:</strong> ${data.current_balance || 0} Egili</p>
                                                <p class="text-sm text-red-600"><strong>Mancanti:</strong> ${data.missing_egili || 0} Egili</p>
                                            </div>
                                            <p class="mt-3 text-xs text-gray-600">Acquista Egili per continuare.</p>
                                        `,
                                        confirmButtonText: 'Acquista Egili',
                                        showCancelButton: true,
                                        cancelButtonText: 'Chiudi',
                                        confirmButtonColor: '#f97316'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Apri modale acquisto Egili con retry logic
                                            console.log('Trying to open Egili modal...');

                                            const tryOpenModal = (attempts = 0) => {
                                                if (typeof window.openEgiliPurchaseModal ===
                                                    'function') {
                                                    console.log(
                                                        '✅ openEgiliPurchaseModal found, calling...'
                                                    );
                                                    window.openEgiliPurchaseModal();
                                                } else if (attempts < 10) {
                                                    console.log(
                                                        `⏳ Waiting for openEgiliPurchaseModal... attempt ${attempts + 1}/10`
                                                    );
                                                    setTimeout(() => tryOpenModal(attempts + 1), 100);
                                                } else {
                                                    console.error(
                                                        '❌ openEgiliPurchaseModal() not found after 10 attempts!'
                                                    );
                                                    // Fallback: try to show the modal directly
                                                    const modal = document.getElementById(
                                                        'egili-purchase-modal');
                                                    if (modal) {
                                                        console.log(
                                                            '🔧 Fallback: directly showing modal element'
                                                        );
                                                        modal.classList.remove('hidden');
                                                        modal.style.display = '';
                                                    } else {
                                                        console.error(
                                                            '❌ Modal element #egili-purchase-modal not found in DOM'
                                                        );
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Errore',
                                                            text: 'Impossibile aprire la modale acquisto Egili. Ricarica la pagina e riprova.',
                                                            confirmButtonColor: '#4F46E5'
                                                        });
                                                    }
                                                }
                                            };

                                            tryOpenModal();
                                        }
                                    });
                                } else {
                                    // Other error
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message || 'Error activating subscription',
                                        icon: 'error',
                                        confirmButtonColor: '#4F46E5',
                                        background: '#1F2937',
                                        color: '#F3F4F6'
                                    });
                                }
                            })
                            .catch(err => {
                                console.error('Subscription error:', err);
                                Swal.close();
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to activate subscription',
                                    icon: 'error',
                                    confirmButtonColor: '#4F46E5',
                                    background: '#1F2937',
                                    color: '#F3F4F6'
                                });
                            });
                    }
                });
            }

            // Close modal on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!dashboardModal.classList.contains('hidden')) {
                        closeDashboard();
                    }
                    if (eppProjectModal && !eppProjectModal.classList.contains('hidden')) {
                        closeEppProjectSelectionModal();
                    }
                }
            });

            // ========================
            // EPP PROJECT SELECTION MODAL
            // ========================

            const eppProjectModal = document.getElementById('eppProjectSelectionModal');
            const closeEppProjectSelectionX = document.getElementById('closeEppProjectSelectionX');
            const closeEppProjectSelectionBtn = document.getElementById('closeEppProjectSelectionBtn');
            const confirmEppProjectSelectionBtn = document.getElementById('confirmEppProjectSelectionBtn');
            const eppProjectGrid = document.getElementById('eppProjectGrid');
            const eppProjectLoadingState = document.getElementById('eppProjectLoadingState');
            const eppProjectEmptyState = document.getElementById('eppProjectEmptyState');
            const eppProjectSearchInput = document.getElementById('eppProjectSearchInput');
            const eppProjectTypeFilter = document.getElementById('eppProjectTypeFilter');

            let allEppProjects = [];
            let selectedProjectId = null;

            function openEppProjectSelectionModal() {
                if (!eppProjectModal) return;
                eppProjectModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                loadEppProjects();
            }

            function closeEppProjectSelectionModal() {
                if (!eppProjectModal) return;
                eppProjectModal.classList.add('hidden');
                document.body.style.overflow = '';
                selectedProjectId = null;
                if (confirmEppProjectSelectionBtn) {
                    confirmEppProjectSelectionBtn.disabled = true;
                }
            }

            if (closeEppProjectSelectionX) {
                closeEppProjectSelectionX.addEventListener('click', closeEppProjectSelectionModal);
            }
            if (closeEppProjectSelectionBtn) {
                closeEppProjectSelectionBtn.addEventListener('click', closeEppProjectSelectionModal);
            }

            // Close on backdrop click
            if (eppProjectModal) {
                eppProjectModal.addEventListener('click', (e) => {
                    if (e.target === eppProjectModal) {
                        closeEppProjectSelectionModal();
                    }
                });
            }

            // Load EPP Projects from API
            async function loadEppProjects() {
                if (!eppProjectLoadingState || !eppProjectGrid || !eppProjectEmptyState) return;

                eppProjectLoadingState.classList.remove('hidden');
                eppProjectGrid.innerHTML = '';
                eppProjectEmptyState.classList.add('hidden');

                try {
                    const response = await fetch('/api/epp-projects/active');
                    const result = await response.json();

                    if (result.success && result.data) {
                        allEppProjects = result.data;
                        renderEppProjects(allEppProjects);
                    } else {
                        throw new Error('Failed to load EPP projects');
                    }
                } catch (error) {
                    console.error('Error loading EPP projects:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load environmental projects',
                        icon: 'error',
                        confirmButtonColor: '#4F46E5',
                        background: '#1F2937',
                        color: '#F3F4F6'
                    });
                    closeEppProjectSelectionModal();
                } finally {
                    eppProjectLoadingState.classList.add('hidden');
                }
            }

            function renderEppProjects(projects) {
                if (!eppProjectGrid || !eppProjectEmptyState) return;

                eppProjectGrid.innerHTML = '';

                if (projects.length === 0) {
                    eppProjectEmptyState.classList.remove('hidden');
                    return;
                } else {
                    eppProjectEmptyState.classList.add('hidden');
                }

                projects.forEach(project => {
                    const card = createEppProjectCard(project);
                    eppProjectGrid.appendChild(card);
                });
            }

            function createEppProjectCard(project) {
                const card = document.createElement('div');
                card.className =
                    'epp-project-card-selectable p-4 border border-gray-700 rounded-lg cursor-pointer transition-all hover:border-green-500 hover:bg-green-500/5';
                card.dataset.projectId = project.id;

                const typeColors = {
                    'ARF': 'bg-green-500/20 text-green-400',
                    'APR': 'bg-blue-500/20 text-blue-400',
                    'BPE': 'bg-yellow-500/20 text-yellow-400'
                };
                const typeIcons = {
                    'ARF': 'forest',
                    'APR': 'waves',
                    'BPE': 'nest_eco_leaf'
                };

                card.innerHTML = `
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex-shrink-0 p-2 rounded-lg ${typeColors[project.project_type] || 'bg-gray-500/20 text-gray-400'}">
                            <span class="text-xl material-symbols-outlined">${typeIcons[project.project_type] || 'eco'}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="mb-1 text-sm font-semibold text-white truncate">${project.name}</h4>
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full ${typeColors[project.project_type] || 'bg-gray-500/20 text-gray-400'}">${project.project_type}</span>
                        </div>
                    </div>
                    <p class="mb-2 text-xs font-medium text-emerald-400">${project.epp_user.organization_name || project.epp_user.name}</p>
                    <p class="mb-3 text-xs text-gray-400 line-clamp-2">${project.description || ''}</p>
                    <div class="flex items-center justify-between mb-2 text-xs">
                        <div class="flex items-center gap-1 text-gray-500">
                            <span class="material-symbols-outlined" style="font-size: 14px;">collections</span>
                            <span>${project.collections_count} collections</span>
                        </div>
                        <div class="font-semibold text-green-400">${Math.round(project.completion_percentage)}%</div>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full transition-all" style="width: ${project.completion_percentage}%"></div>
                    </div>
                `;

                card.addEventListener('click', () => selectEppProject(project.id, card));

                return card;
            }

            function selectEppProject(projectId, cardElement) {
                // Remove selection from all cards
                document.querySelectorAll('.epp-project-card-selectable').forEach(c => {
                    c.classList.remove('border-green-500', 'bg-green-500/10');
                    c.classList.add('border-gray-700');
                });

                // Mark selected card
                cardElement.classList.remove('border-gray-700');
                cardElement.classList.add('border-green-500', 'bg-green-500/10');

                selectedProjectId = projectId;
                if (confirmEppProjectSelectionBtn) {
                    confirmEppProjectSelectionBtn.disabled = false;
                }
            }

            // Confirm EPP Project Selection
            if (confirmEppProjectSelectionBtn) {
                confirmEppProjectSelectionBtn.addEventListener('click', async () => {
                    if (!selectedProjectId) return;

                    const selectedProject = allEppProjects.find(p => p.id === selectedProjectId);

                    Swal.fire({
                        title: 'Confirm Project Selection',
                        html: `Support <strong>${selectedProject.name}</strong>?<br>
                           <span class="text-sm text-gray-400">By ${selectedProject.epp_user.organization_name || selectedProject.epp_user.name}</span><br><br>
                           <span class="text-green-400">✓ Unlimited mints for free</span><br>
                           <span class="text-green-400">✓ Support environmental projects</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        background: '#1F2937',
                        color: '#F3F4F6',
                        customClass: {
                            popup: 'border border-gray-700'
                        }
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            await updateCollectionEppProject(selectedProjectId);
                        }
                    });
                });
            }

            async function updateCollectionEppProject(projectId) {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const isCompanyCollection =
                    {{ $collection->is_epp_voluntary || ($collection->creator && $collection->creator->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value) ? 'true' : 'false' }};

                // For company collections, get the donation percentage from slider
                let donationPercentage = null;
                if (isCompanyCollection) {
                    const slider = document.getElementById('companyDonationPercentage');
                    if (slider) {
                        donationPercentage = parseInt(slider.value, 10);
                    }
                }

                Swal.fire({
                    title: 'Updating...',
                    text: 'Saving your selection',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#1F2937',
                    color: '#F3F4F6'
                });

                try {
                    const requestBody = {
                        epp_project_id: projectId
                    };

                    // Add donation percentage for company collections
                    if (isCompanyCollection && donationPercentage !== null) {
                        requestBody.epp_donation_percentage = donationPercentage;
                    }

                    const response = await fetch(`/api/collections/{{ $collection->id }}/epp-project`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(requestBody)
                    });

                    const result = await response.json();

                    if (result.success) {
                        await Swal.fire({
                            title: 'Success!',
                            html: `Now supporting <strong>${result.data.epp_project.name}</strong>!<br>
                               <span class="text-sm text-gray-400">By ${result.data.epp_project.epp_user.organization_name || result.data.epp_project.epp_user.name}</span><br><br>
                               Your collection will contribute to environmental protection.`,
                            icon: 'success',
                            confirmButtonText: 'Great!',
                            confirmButtonColor: '#10B981',
                            background: '#1F2937',
                            color: '#F3F4F6'
                        });

                        // Close modals and reload
                        closeEppProjectSelectionModal();
                        closeDashboard();
                        location.reload();
                    } else {
                        throw new Error(result.message || 'Update failed');
                    }
                } catch (error) {
                    console.error('EPP project update error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Failed to update EPP project selection',
                        icon: 'error',
                        confirmButtonColor: '#4F46E5',
                        background: '#1F2937',
                        color: '#F3F4F6'
                    });
                }
            }

            // Filter EPP Projects
            function filterEppProjects() {
                if (!eppProjectSearchInput || !eppProjectTypeFilter) return;

                const searchTerm = eppProjectSearchInput.value.toLowerCase();
                const selectedType = eppProjectTypeFilter.value;

                const filtered = allEppProjects.filter(project => {
                    const matchesSearch = project.name.toLowerCase().includes(searchTerm) ||
                        project.description.toLowerCase().includes(searchTerm) ||
                        (project.epp_user.organization_name && project.epp_user.organization_name.toLowerCase()
                            .includes(searchTerm)) ||
                        project.epp_user.name.toLowerCase().includes(searchTerm);
                    const matchesType = !selectedType || project.project_type === selectedType;

                    return matchesSearch && matchesType;
                });

                renderEppProjects(filtered);
            }

            if (eppProjectSearchInput) {
                eppProjectSearchInput.addEventListener('input', filterEppProjects);
            }
            if (eppProjectTypeFilter) {
                eppProjectTypeFilter.addEventListener('change', filterEppProjects);
            }

            // ========================
            // END EPP PROJECT SELECTION
            // ========================

            // ========================
            // COMPANY DONATION MANAGEMENT
            // ========================

            // Slider for donation percentage
            const donationSlider = document.getElementById('companyDonationPercentage');
            const donationValueDisplay = document.getElementById('donationPercentageValue');

            if (donationSlider && donationValueDisplay) {
                donationSlider.addEventListener('input', (e) => {
                    donationValueDisplay.textContent = e.target.value + '%';
                });
            }

            // Update company donation percentage
            async function updateCompanyDonation() {
                const slider = document.getElementById('companyDonationPercentage');
                if (!slider) return;

                const percentage = parseInt(slider.value, 10);
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                Swal.fire({
                    title: '{{ __('collection.update_donation') }}',
                    text: `{{ __('collection.donation_percentage_label') }}: ${percentage}%`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('collection.update_donation') }}',
                    cancelButtonText: '{{ __('collection.show.dashboard.close') }}',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    background: '#1F2937',
                    color: '#F3F4F6'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '{{ __('common.loading') }}...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: '#1F2937',
                            color: '#F3F4F6'
                        });

                        try {
                            const response = await fetch(
                                `/api/collections/{{ $collection->id }}/epp-project`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        epp_project_id: {{ $collection->epp_project_id ?? 'null' }},
                                        epp_donation_percentage: percentage
                                    })
                                });

                            const result = await response.json();

                            if (result.success) {
                                await Swal.fire({
                                    title: '{{ __('common.success') }}!',
                                    text: '{{ __('collection.donation_updated') }}',
                                    icon: 'success',
                                    confirmButtonColor: '#10B981',
                                    background: '#1F2937',
                                    color: '#F3F4F6'
                                });
                                location.reload();
                            } else {
                                throw new Error(result.message || 'Update failed');
                            }
                        } catch (error) {
                            console.error('Donation update error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: '{{ __('collection.donation_update_error') }}',
                                icon: 'error',
                                confirmButtonColor: '#4F46E5',
                                background: '#1F2937',
                                color: '#F3F4F6'
                            });
                        }
                    }
                });
            }

            // Make function available globally
            window.updateCompanyDonation = updateCompanyDonation;

            // ========================
            // END COMPANY DONATION MANAGEMENT
            // ========================
        </script>

        <style>
            .dashboard-tab {
                @apply font-medium text-gray-400 transition-colors border-b-2 border-transparent hover:text-white hover:border-gray-600;
            }

            .dashboard-tab.active {
                @apply text-indigo-400 border-indigo-500;
            }

            .dashboard-tab-content {
                @apply transition-opacity duration-200;
            }
        </style>
    @endpush

</x-collection-layout>
