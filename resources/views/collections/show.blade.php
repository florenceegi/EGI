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
// Prendiamo il primo elemento se è una collezione di collezioni
// $firstCollection = $collection->first();

@endphp

<x-collection-layout :title="$collection->collection_name . ' | FlorenceEGI'"
    :metaDescription="Str::limit($collection->description, 155) ?? __('collection.show.details_for_collection') . ' ' . $collection->collection_name">

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
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="container px-4 py-6 mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb migliorato --}}
            <nav class="flex items-center mb-4 space-x-2 text-sm text-gray-400" aria-label="Breadcrumb">
                <a href="{{ route('home.collections.index') }}"
                    class="transition-colors duration-200 hover:text-emerald-400">
                    <span class="mr-1 text-base material-symbols-outlined">collections</span>
                    {{ __('collection.show.collections_breadcrumb') }}
                </a>
                <span class="text-xs material-symbols-outlined">chevron_right</span>
                <span class="font-medium text-gray-300">{{ Str::limit($collection->collection_name, 30) }}</span>
            </nav>

            {{-- Statistiche PaymentDistribution per desktop e mobile --}}
            <x-hero-banner-stats :collection="$collection" />
        </div>
    </div>

    {{-- 🎨 HERO BANNER POTENZIATO - Mobile Responsive --}}
    <section class="relative overflow-hidden min-h-[50vh] sm:min-h-[55vh]">
        {{-- Background senza Parallax Effect --}}
        <div class="absolute inset-0 z-0">
            @php
                // Prova ad usare Spatie Media se disponibile
                $bannerUrl = method_exists($collection, 'getFirstMediaUrl')
                    ? $collection->getFirstMediaUrl('head', 'banner')
                    : null;
            @endphp
            @if($bannerUrl || $collection->image_banner)
            <img src="{{ $bannerUrl ?: $collection->image_banner }}" alt="Banner for {{ $collection->collection_name }}"
                class="object-cover w-full h-full">
            @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900"></div>
            @endif
            {{-- Overlay gradiente potenziato --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
        </div>

        {{-- Top Section: Creator Info + Upload Button - Solo mobile positioning --}}
        <div class="absolute z-20 flex items-center justify-between top-4 left-4 right-4 sm:top-8 sm:left-8 sm:right-8 sm:bottom-8 sm:flex-col sm:items-start sm:justify-between sm:h-auto sm:static sm:bg-transparent">
            {{-- Creator Info - Desktop: parte della hero content, Mobile: top bar --}}
            <div class="sm:hidden">
                @if($collection->creator)
                <a href="{{ route('creator.home', ['id' => $collection->creator->id]) }}"
                    class="flex items-center gap-3 transition-all duration-200 hover:opacity-80 group">
                    <div class="flex-shrink-0">
                        @if ($collection->creator->profile_photo_url)
                        <img src="{{ $collection->creator->profile_photo_url }}"
                            alt="{{ $collection->creator->name }}"
                            class="object-cover w-10 h-10 transition-transform duration-200 border-2 rounded-full border-white/30 group-hover:scale-110">
                        @else
                        <div
                            class="flex items-center justify-center w-10 h-10 transition-transform duration-200 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 group-hover:scale-110">
                            <span class="text-sm font-bold text-white">{{ substr($collection->creator->name ?? 'U', 0, 1) }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-white transition-colors duration-200 group-hover:text-emerald-400">{{
                                $collection->creator->name ?? __('collection.show.unknown_creator') }}</span>
                            @if ($collection->creator->usertype === 'verified')
                            <span class="text-sm text-blue-400 material-symbols-outlined"
                                title="{{ __('collection.show.verified_creator') }}">verified</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-300 transition-colors duration-200 group-hover:text-gray-200">{{
                            __('collection.show.collection_creator') }}</p>
                    </div>
                </a>
                @else
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500">
                            <span class="text-sm font-bold text-white">U</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-white">{{ __('collection.show.unknown_creator') }}</span>
                        <p class="text-xs text-gray-300">{{ __('collection.show.collection_creator') }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Upload Button - Solo mobile top bar --}}
            <div class="sm:hidden">
                @can('create_collection')
                <button id="uploadBannerBtn" class="flex items-center justify-center w-10 h-10 text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700 backdrop-blur-sm"
                        data-uploading-label="{{ __('collection.show.uploading') }}"
                        data-upload-success="{{ __('collection.show.banner_updated') }}"
                        data-upload-error="{{ __('collection.show.banner_upload_error') }}"
                        data-upload-label="{{ __('collection.show.upload_banner') }}"
                        title="{{ __('collection.show.upload_banner') }}">
                    <span class="text-lg material-symbols-outlined">upload</span>
                </button>
                <input type="file" id="bannerFileInput" accept="image/*" class="hidden" />
                @endcan
            </div>
        </div>

        {{-- Desktop Upload Button - Posizionamento originale --}}
        <div class="absolute z-20 hidden top-8 right-8 sm:block">
            {{-- @if(auth()->check() && auth()->id() === ($collection->creator_id ?? null)) --}}
            @if($collection->userHasPermission(Auth::id(), 'create_collection'))
            <button id="uploadBannerBtnDesktop" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700 backdrop-blur-sm"
                    data-uploading-label="{{ __('collection.show.uploading') }}"
                    data-upload-success="{{ __('collection.show.banner_updated') }}"
                    data-upload-error="{{ __('collection.show.banner_upload_error') }}"
                    data-upload-label="{{ __('collection.show.upload_banner') }}"
                    title="{{ __('collection.show.upload_banner') }}">
                <span class="text-base material-symbols-outlined">upload</span>
                <span>{{ __('collection.show.upload_banner') }}</span>
            </button>
            <input type="file" id="bannerFileInputDesktop" accept="image/*" class="hidden" />
            @endif
        </div>

        {{-- CTA Section - Positioned at bottom right --}}
        <div class="absolute z-20 flex gap-2 bottom-6 right-4 sm:bottom-8 sm:right-8">
                {{-- Like Button - New Component --}}
                <x-like-button
                    :resourceType="'collection'"
                    :resourceId="$collection->id"
                    :isLiked="$collection->is_liked ?? false"
                    :likesCount="$collection->likes_count ?? 0"
                    size="medium"
                />

                {{-- Share Button - Compact --}}
                <button
                    class="flex items-center justify-center w-10 h-10 text-sm font-medium text-white transition-all duration-300 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20 hover:bg-white/20 sm:w-auto sm:h-auto sm:px-4 sm:py-2"
                    onclick="navigator.share ? navigator.share({title: '{{ $collection->collection_name }}', url: window.location.href}) : copyToClipboard(window.location.href)"
                    title="{{ __('collection.show.share') }}">
                    <span class="mr-0 text-lg material-symbols-outlined sm:text-base sm:mr-1">share</span>
                    <span class="hidden text-sm sm:inline">{{ __('collection.show.share') }}</span>
                </button>

                {{-- Edit Button - Compact --}}
                @if($collection->userHasPermission(Auth::id(), 'create_collection'))
                <button id="editMetaBtn"
                    class="flex items-center justify-center w-10 h-10 text-sm font-medium text-white transition-all duration-300 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20 hover:bg-white/20 sm:w-auto sm:h-auto sm:px-4 sm:py-2"
                    title="{{ __('collection.show.edit_button') }}">
                    <span class="mr-0 text-lg material-symbols-outlined sm:text-base sm:mr-1">edit</span>
                    <span class="hidden text-sm sm:inline">{{ __('collection.show.edit_button') }}</span>
                </button>
                @endif

                {{-- Team Management Button - Compact --}}
                @if($collection->userHasPermission(Auth::id(), 'create_team'))
                <a href="{{ route('collections.collection_user', ['id' => $collection->id]) }}"
                    class="flex items-center justify-center w-10 h-10 text-sm font-medium text-white transition-all duration-300 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20 hover:bg-white/20 sm:w-auto sm:h-auto sm:px-4 sm:py-2"
                    title="{{ __('collection.show.manage_team') }}">
                    <span class="mr-0 text-lg material-symbols-outlined sm:text-base sm:mr-1">group</span>
                    <span class="hidden text-sm sm:inline">{{ __('collection.show.manage_team') }}</span>
                </a>
                @endif
            </div>
        </div>

        {{-- Hero Content - Layout responsive --}}
        <div class="container relative z-10 px-4 py-8 mx-auto sm:px-6 lg:px-8 sm:py-12 lg:py-16">
            <div class="max-w-4xl">
                {{-- Creator Info - Solo Desktop in alto --}}
                <div class="hidden mb-8 sm:block">
                    @if($collection->creator)
                    <a href="{{ route('creator.home', ['id' => $collection->creator->id]) }}"
                        class="inline-flex items-center gap-4 transition-all duration-200 hover:opacity-90 group">
                        <div class="flex-shrink-0">
                            @if ($collection->creator->profile_photo_url)
                            <img src="{{ $collection->creator->profile_photo_url }}"
                                alt="{{ $collection->creator->name }}"
                                class="object-cover w-16 h-16 transition-transform duration-200 border-2 rounded-full border-white/40 group-hover:scale-105">
                            @else
                            <div
                                class="flex items-center justify-center w-16 h-16 transition-transform duration-200 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 group-hover:scale-105">
                                <span class="text-xl font-bold text-white">{{ substr($collection->creator->name ?? 'U', 0, 1) }}</span>
                            </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xl font-bold text-white transition-colors duration-200 group-hover:text-emerald-400">{{
                                    $collection->creator->name ?? __('collection.show.unknown_creator') }}</span>
                                @if ($collection->creator->usertype === 'verified')
                                <span class="text-xl text-blue-400 material-symbols-outlined"
                                    title="{{ __('collection.show.verified_creator') }}">verified</span>
                                @endif
                            </div>
                            <p class="text-base text-gray-200 transition-colors duration-200 group-hover:text-white">{{
                                __('collection.show.collection_creator') }}</p>
                        </div>
                    </a>
                    @else
                    <div class="inline-flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500">
                                <span class="text-xl font-bold text-white">U</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-white">{{ __('collection.show.unknown_creator') }}</span>
                            <p class="text-base text-gray-200">{{ __('collection.show.collection_creator') }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Collection Title + Description --}}
                <div class="mt-16 sm:mt-40">
                    <h1 id="collection-title" class="mb-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                        {{ $collection->collection_name }}
                    </h1>

                    @if($collection->description)
                    <p id="collection-description" class="text-base leading-relaxed text-gray-200 sm:text-lg line-clamp-3 sm:line-clamp-none">
                        {{ $collection->description }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Edit Meta Modal (partial) --}}
    @include('collections.partials.edit-meta-modal', ['collection' => $collection])

    {{-- 🌳 EPP SECTION (Se presente) --}}
    @if($collection->epp)
    <div class="border-b border-gray-800 bg-gradient-to-r from-green-900/20 to-emerald-900/20">
        <div class="container px-4 py-6 mx-auto sm:px-6 lg:px-8">
            <div class="p-4 hero-glass rounded-xl sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 p-3 rounded-lg bg-green-500/20">
                        <span class="text-2xl text-green-400 material-symbols-outlined">eco</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="mb-2 text-lg font-semibold text-white">{{
                            __('collection.show.supporting_environmental_project') }}</h3>
                        <h4 class="mb-2 font-medium text-emerald-400">{{ $collection->epp->name }}</h4>
                        <p class="mb-3 text-sm text-gray-300 line-clamp-2">{{ $collection->epp->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-green-400">{{ __('collection.show.epp_percentage') }}
                                {{
                                __('collection.show.of_sales_support_this_project') }}</span>
                            <a href="{{ route('epps.show', $collection->epp_id) }}"
                                class="flex items-center gap-1 text-sm font-medium text-emerald-400 hover:text-emerald-300">
                                {{ __('collection.show.learn_more') }}
                                <span class="text-sm material-symbols-outlined">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 🎨 GRIGLIA EGI PRINCIPALE --}}
    <main class="py-8 bg-gray-900 sm:py-12">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            {{-- Header con Filtri --}}
            <div class="flex flex-col items-start justify-between gap-4 mb-8 sm:flex-row sm:items-center">
                <div>
                    <h2 class="mb-2 text-2xl font-bold text-white sm:text-3xl">
                        {{ __('collection.show.collection_items') }}
                    </h2>
                    <p class="text-sm text-gray-400">
                        {{ $collection->egis_count ?? 0 }} {{ __('collection.show.unique_digital_assets') }}
                    </p>
                </div>

                {{-- Filtri e ordinamento --}}
                <div class="flex flex-col w-full gap-3 sm:flex-row sm:w-auto">
                    <select id="egis-sort"
                        class="px-4 py-2 text-sm text-white bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                    <x-collection.view-selector
                        :totalItems="$collection->egis_count ?? 0"
                        :totalHolders="$totalHolders" />
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
                    <x-egi-card :egi="$egi" :collection="$collection" :portfolioContext="$isCreatorViewing"
                        :portfolioOwner="$isCreatorViewing ? $collection->creator : null"
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
                        <div class="inline-flex items-center justify-center w-16 h-16 mb-6 bg-gray-800 rounded-full">
                            <span class="text-2xl text-gray-400 material-symbols-outlined">image</span>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">{{ __('collection.show.no_egis_yet') }}</h3>
                        <p class="max-w-md mx-auto mb-6 text-gray-400">
                            {{ __('collection.show.no_egis_message') }}
                        </p>
                        @if(auth()->id() === $collection->creator_id)
                        <button class="px-6 py-3 font-semibold text-white rounded-lg btn-primary-glow">
                            <span class="mr-2 material-symbols-outlined">add</span>
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
                <div class="p-12 text-center bg-gray-800 rounded-lg">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-700 rounded-full flex items-center justify-center">
                        <span class="text-2xl text-gray-400 material-symbols-outlined">category</span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ __('collection.traits.coming_soon') }}</h3>
                    <p class="text-gray-400">{{ __('collection.traits.coming_soon_message') }}</p>
                </div>
            </div>

            {{-- Load More Button (se necessario) --}}
            @if($collection->egis->count() >= 20)
            <div class="mt-12 text-center">
                <button
                    class="px-8 py-3 font-medium text-white transition-colors duration-200 bg-gray-800 rounded-lg hover:bg-gray-700">
                    {{ __('collection.show.load_more_items') }}
                </button>
            </div>
            @endif
        </div>
    </main>

    {{-- 📚 COLLEZIONI CORRELATE --}}
    @if(isset($relatedCollections) && $relatedCollections->count() > 0)
    <section class="py-12 bg-gray-800">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            <h2 class="mb-8 text-2xl font-bold text-center text-white">
                {{ __('collection.show.more_from_this_creator') }}
            </h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedCollections->take(3) as $relatedCollection)
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
            class="flex items-center gap-3 px-4 py-3 border border-gray-700 rounded-full bg-gray-900/90 backdrop-blur-sm">
            <button
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-full hover:bg-indigo-700">
                <span class="text-sm material-symbols-outlined">favorite_border</span>
                {{ __('collection.show.like_collection') }}
            </button>
            <button
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-700 rounded-full hover:bg-gray-600">
                <span class="text-sm material-symbols-outlined">share</span>
                {{ __('collection.show.share') }}
            </button>
        </div>
    </div>

    {{-- JavaScript Enhancements --}}
    @push('scripts')
    <script>
        // EGI sorting functionality
        document.getElementById('egis-sort').addEventListener('change', function() {
            const sortValue = this.value;
            const container = document.getElementById('egis-container');
            const items = Array.from(container.querySelectorAll('.egi-item'));

            items.sort((a, b) => {
                switch(sortValue) {
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
        toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-green-600 rounded-lg bottom-4 left-1/2';
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
        const url = "{{ route('collections.banner.upload', ['collection' => $collection->id ?? 0], false) }}";

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
            const newSrc = (data.banner_url || data.original_url) ? (data.banner_url || data.original_url) + `?t=${Date.now()}` : null;
            if (newSrc && img) {
                img.src = newSrc;
            }

            // Toast
            const toast = document.createElement('div');
            toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-emerald-600 rounded-lg bottom-4 left-1/2';
            toast.textContent = btn.getAttribute('data-upload-success') || 'Updated';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);

            btn.innerHTML = originalContent;
        } catch (e) {
            console.error('Upload error', e);
            const toast = document.createElement('div');
            toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-red-600 rounded-lg bottom-4 left-1/2';
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
    @endpush

</x-collection-layout>
