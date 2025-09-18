{{-- resources/views/components/egi-card.blade.php --}}
{{-- 📜 Oracode Blade Component: EGI Card --}}

{{-- Uses Tailwind CSS for a modern, responsive design. --}}

{{-- Props: Definisci l'oggetto egi come richiesto --}}
@props([
'egi',
'collection' => null,
'showPurchasePrice' => false,
'hideReserveButton' => false,
'portfolioContext' => false,
'portfolioOwner' => null,
'creatorPortfolioContext' => false, // 🆕 Nuovo prop per Creator Portfolio
]) {{-- 🚀 Nuovo prop per context portfolio --}}

@php
// 🔥 HYPER MODE: Leggiamo direttamente dal database il campo hyper dell'EGI
// ===========================
// 🎯 CORE DATA PREPARATION
// ===========================

// 🔥 HYPER MODE
$isHyper = $egi->hyper ?? false;

// 👤 USER CONTEXT
$currentUser = auth()->user();
$isAuthenticated = auth()->check();
$currentUserId = $isAuthenticated ? auth()->id() : null;

// 🎨 CREATOR INFO
$creatorId = $egi->user_id ?? $collection->creator_id ?? null;
$isCreator = $isAuthenticated && $currentUserId === $creatorId;
$egiCreator = $egi->user ?? null;
$imageUrl = $egiCreator->profile_photo_url ?? '';

// 📦 COLLECTION INFO
$egiCollection = $egi->collection ?? $collection ?? null;

// ===========================
// 💰 RESERVATION LOGIC
// ===========================

// Get highest reservation (sub_status = 'highest')
$highestReservation = $egi->reservations()
    ->where('sub_status', 'highest')
    ->where('status', 'active')
    ->first();

// Check if current user has active reservation
$userReservation = null;
if ($isAuthenticated) {
    $userReservation = $egi->reservations()
        ->where('user_id', $currentUserId)
        ->where('is_current', true)
        ->where('status', 'active')
        ->first();
}

// Determine display price
$displayPrice = $highestReservation
    ? ($highestReservation->amount_eur ?? $highestReservation->offer_amount_fiat)
    : $egi->price;

// 📦 Portfolio: calcolo stato outbid per applicare opacità e badge corretti
$portfolioOutbid = false;
if ($portfolioContext && $portfolioOwner) {
try {
// Ultima prenotazione del proprietario del portfolio su questo EGI
$ownerLastReservation = $egi->reservations()
->where('user_id', $portfolioOwner->id)
->orderByDesc('created_at')
->first();

$isWinning = $ownerLastReservation && $ownerLastReservation->is_current && $ownerLastReservation->status ===
'active' &&
!$ownerLastReservation->superseded_by_id;
$portfolioOutbid = $ownerLastReservation && !$isWinning;
} catch (\Throwable $th) {
$portfolioOutbid = false;
}
}

// 🎨 Creator Portfolio Context: gestione badge "DA ATTIVARE" per opere non attivate
$showActivationBadge = false;
if ($creatorPortfolioContext && $portfolioOwner) {
// Nel Creator Portfolio, se l'EGI non ha prenotazioni attive, mostra "DA ATTIVARE"
$hasActiveReservations = $egi->reservations()->where('is_current', true)->exists();
$showActivationBadge = !$hasActiveReservations;
}

// 🔒 Creator check: Determina se l'utente corrente è il creatore dell'EGI
$creatorId = $egi->user_id ?? $collection->creator_id ?? null;
$isCreator = auth()->check() && auth()->id() === $creatorId;

// 🔄 Controlla se c'è una prenotazione corrente per il pulsante Rilancia
$hasCurrentReservation = $egi->reservations &&
$egi->reservations->where('is_current', true)->first();
$isCreator = auth()->check() && auth()->id() === $creatorId;
@endphp

{{-- Include CSS hyper se necessario --}}
@if($isHyper)
@once
<link rel="stylesheet" href="{{ asset('css/egi-hyper.css') }}">
@endonce
@endif

{{-- Include CSS tooltip per descrizioni --}}
@once
<link rel="stylesheet" href="{{ asset('css/egi-card-tooltip.css') }}">
@endonce

{{-- 🧱 Card Container --}}
<article
    class="egi-card {{ $isHyper ? 'egi-card--hiper' : '' }} group relative w-full overflow-hidden rounded-2xl border-2 border-purple-500/30 bg-gray-900 transition-all duration-300 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/20 {{ $portfolioOutbid ? 'opacity-35 hover:opacity-70' : '' }}"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}" style="{{ $isHyper
        ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;'
        : '' }}">

    @if($isHyper)
    <div class="egi-sparkles" aria-hidden="true"></div>
    {{-- Badge HYPER normale solo se NON c'è badge composto --}}
    @if(!$showPurchasePrice && !$portfolioContext)
    <div class="egi-hyper-badge">⭐ HYPER ⭐</div>
    @endif
    @endif
    {{-- 🖼️ Sezione Immagine --}}
    <figure class="relative aspect-[4/5] w-full overflow-hidden bg-black">
        <a href="{{ route('egis.show', $egi->id) }}" class="block w-full h-full">
            @php
            // � OTTIMIZZAZIONE IMMAGINI: usa l'accessor del modello con fallback interno
            // getMainImageUrlAttribute() restituisce la variante 'card' se presente,
            // altrimenti fa fallback all'originale su disco pubblico.
            $optimizedImageUrl = $egi->main_image_url;
            @endphp

            {{-- 🎯 Immagine Principale o Placeholder --}}
            @if ($optimizedImageUrl)
            <img src="{{ $optimizedImageUrl }}" {{-- Usa l'URL ottimizzato con fallback --}}
                alt="{{ $egi->title ?? 'EGI Image' }}"
                class="object-contain object-center w-full h-full transition-transform duration-300 ease-in-out bg-gray-800 group-hover:scale-105"
                loading="lazy"
                {{-- Supporto WebP con fallback automatico del browser --}}
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
            @else
            {{-- Placeholder --}}
            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-gray-800 to-gray-900">
                <svg class="w-16 h-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            @endif

            {{-- Overlay leggero su hover --}}
            <div class="absolute inset-0 transition-opacity duration-300 opacity-0 bg-black/40 group-hover:opacity-100">
            </div>
        </a>

        {{-- Logo piattaforma posizionato fuori dal badge --}}
        <img src="{{ asset('images/logo/logo_1.webp') }}" alt=""
            class="absolute w-6 h-6 transition-opacity duration-200 left-2 top-2 opacity-70 hover:opacity-100"
            loading="lazy" decoding="async" aria-hidden="true" role="img"
            title="{{ __('egi.platform.powered_by', ['platform' => 'Frangette']) }}">

        {{-- Badge del numero EGI --}}
        @if ($egi->position)
        <span
            class="position-badge absolute left-10 top-2 rounded-full bg-black/50 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
            #{{ $egi->position }}
        </span>
        @endif

        {{-- Badge Categoria (Trait category) --}}
        @php
            $categoryName = $egi->category_name; // accessor (localized)
            $categoryClasses = $egi->category_badge_classes; // palette classes
            // Fallback di sicurezza se per qualche motivo le classi non vengono generate / purge Tailwind
            if (empty($categoryClasses)) {

                $categoryClasses = 'bg-gradient-to-r from-amber-400 to-yellow-500 text-white';
            }
        @endphp
        {{-- Fix posizione badge: alcune palette (Art, Science) includono 'relative' per pseudo-element overlays
             che sovrascriveva 'absolute' rendendo invisibile il badge solo su questa card.
             Avvolgiamo in un container assoluto e lasciamo la <span> relativa per gli effetti. --}}
        <div class="absolute left-0 z-10 top-9">
            <span
                class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold tracking-wide rounded-full backdrop-blur-sm ring-1 ring-white/10 shadow {{ $categoryClasses }}"
                title="{{ $categoryName }}" aria-label="EGI Category: {{ $categoryName }}" data-cat-name="{{ $categoryName }}" data-cat-classes="{{ $categoryClasses }}">
                {{ Str::limit($categoryName, 14) }}
            </span>
        </div>

        {{-- 🌟 BADGE COMPOSTO HYPER + POSSEDUTO (SOLUZIONE MICHELIN) --}}
        @if ($showPurchasePrice && $isHyper)
        <div class="badge-composite">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('egi.badge.to_activate') }}
            </div>
        </div>
        {{-- Badge Owned normale (no HYPER) --}}
        @elseif ($showPurchasePrice)
        <span
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-green-500/90 backdrop-blur-sm">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('egi.badge.to_activate') }}
        </span>
        {{-- 🚀 NEW: Context-aware badges per portfolio --}}
        @elseif ($portfolioContext)
        @php
        // CREATOR PORTFOLIO: Logica speciale per il portfolio del creator
        if ($creatorPortfolioContext) {
        // Nel Creator Portfolio, controlla se l'EGI ha prenotazioni attive (da chiunque)
        $hasAnyActiveReservations = $egi->reservations()->where('is_current', true)->exists();
        $isWinning = $hasAnyActiveReservations; // Se ha prenotazioni = "attivato"
        } else {
        // COLLECTOR PORTFOLIO: Logica normale per altri portfolio
        $ownerReservation = $egi->reservations()
        ->where('user_id', $portfolioOwner->id)
        ->orderByDesc('created_at')
        ->first();
        $isWinning = $ownerReservation && $ownerReservation->is_current && $ownerReservation->status === 'active' &&
        !$ownerReservation->superseded_by_id;
        }
        @endphp

        @if ($isWinning)
        @if ($isHyper)
        {{-- Badge composto HYPER + (ATTIVATO nel Creator Portfolio / OFFERTA VINCENTE negli altri) --}}
        <div class="badge-composite" data-portfolio-badge="1"
            title="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-winning="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if($creatorPortfolioContext)
                    {{-- Icona "Attivato" (check con cerchio) --}}
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                    @else
                    {{-- Icona "Offerta Vincente" originale --}}
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                    @endif
                </svg>
                {{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}
            </div>
        </div>
        @else
        <span data-portfolio-badge="1"
            data-lbl-winning="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 bg-green-500/90 backdrop-blur-sm"
            title="{{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                @if($creatorPortfolioContext)
                {{-- Icona "Attivato" (check con cerchio) --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
                @else
                {{-- Icona "Offerta Vincente" originale --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
                @endif
            </svg>
            {{ $creatorPortfolioContext ? __('egi.badge.activated') : __('egi.badge.winning_bid') }}
        </span>
        @endif
        @else
        @if ($isHyper)
        {{-- Badge composto HYPER + (DA ATTIVARE / NON POSSEDUTO) --}}
        <div class="badge-composite" data-portfolio-badge="1"
            title="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <div class="hyper-overlay">⭐ HYPER ⭐</div>
            <div class="not-owned-base">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if($creatorPortfolioContext && $showActivationBadge)
                    {{-- Icona "Attivazione" (play/start) --}}
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                        clip-rule="evenodd" />
                    @else
                    {{-- Icona "Non Posseduto" (persona) --}}
                    <path fill-rule="evenodd" d="M10 9a3 3 0 11-6 0 3 3 0 016 0zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd" />
                    @endif
                </svg>
                {{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') :
                __('egi.badge.not_owned') }}
            </div>
        </div>
        @else
        <span data-portfolio-badge="1" data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
            data-lbl-not-owned="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}"
            class="absolute inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white rounded-full right-2 top-2 {{ $creatorPortfolioContext && $showActivationBadge ? 'bg-blue-600/90' : 'bg-red-600/90' }} backdrop-blur-sm"
            title="{{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') : __('egi.badge.not_owned') }}">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                @if($creatorPortfolioContext && $showActivationBadge)
                {{-- Icona "Attivazione" (play/start) --}}
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                    clip-rule="evenodd" />
                @else
                {{-- Icona "Non Posseduto" (warning) --}}
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
                @endif
            </svg>
            {{ $creatorPortfolioContext && $showActivationBadge ? __('egi.badge.to_activate') :
            __('egi.badge.not_owned') }}
        </span>
        @endif
        @endif
        {{-- Badge per contenuto media --}}
        @elseif ($egi->media)
        <span
            class="absolute inline-flex items-center justify-center w-6 h-6 text-white rounded-full right-2 top-2 bg-black/50 backdrop-blur-sm"
            title="{{ __('egi.badge.media_content') }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path
                    d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
            </svg>
        </span>
        @endif
    </figure>

    {{-- ℹ️ Information Section --}}
    <div class="flex flex-col justify-between flex-1 p-4 bg-gradient-to-b from-gray-900/50 to-gray-900">
        {{-- Title and Like --}}
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <h3 class="flex-1 text-base font-bold leading-tight text-white transition-colors duration-200 group-hover:text-purple-300 {{ $egi->description ? 'has-description' : '' }}">
                    {{ Str::limit($egi->title ?? __('egi.title.untitled'), 45) }}

                    {{-- Tooltip for description --}}
                    @if($egi->description)
                        <div class="absolute z-50 px-3 py-2 mb-2 text-sm font-normal text-white bg-gray-900 border border-gray-700 rounded-lg shadow-xl bottom-full left-1/2 min-w-64 max-w-80">
                            {{ Str::limit($egi->description, 200) }}
                        </div>
                    @endif
                </h3>

                {{-- Like Button --}}
                @if(!$isCreator)
                    <div class="flex-shrink-0">
                        <x-like-button
                            :resourceType="'egi'"
                            :resourceId="$egi->id"
                            :isLiked="$egi->is_liked ?? false"
                            :likesCount="$egi->likes_count ?? 0"
                            size="md"
                            :showCounter="true"
                            position="relative"
                            theme="overlay"
                        />
                    </div>
                @endif
            </div>

            {{-- 🎨 CREATOR INFO - SEMPRE VISIBILE --}}
            @if ($egiCreator)
                <div class="flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50" data-creator-info>
                    <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                         <img src="{{ $imageUrl }}" alt="{{ $egiCreator->name }}"
                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105" loading="lazy"
                            decoding="async">
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.creator.created_by') }}</span>
                        <span class="ml-1 text-xs font-semibold text-white truncate">{{ $egiCreator->name }}</span>
                    </div>
                </div>
            @endif

            {{-- 📦 COLLECTION INFO --}}
            @if ($egiCollection)
                <div class="flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50" data-collection-info>
                    <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.collection.part_of') }}</span>
                        <span class="ml-1 text-xs font-semibold text-white truncate">{{ $egiCollection->collection_name }}</span>
                    </div>
                </div>
            @endif

            {{-- 📊 RESERVATION COUNT --}}
            @if ($egi->reservations && $egi->reservations->count() > 0)
                <div class="flex items-center gap-2 p-2 mb-2 border rounded-lg border-gray-700/50 bg-gray-800/50" data-reservation-count>
                    <div class="flex items-center justify-center flex-shrink-0 w-5 h-5 rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1a1 1 0 100-2 2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-xs font-medium text-gray-300">
                            {{ $egi->reservations->count() }} {{ __('egi.reservation.count') }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- 💰 PRICE SECTION - SIMPLIFIED --}}
        <div class="mt-4">
            @if (!(bool) $egi->is_published)
                {{-- DRAFT Status --}}
                <div class="flex items-center justify-center p-3 border rounded-xl border-yellow-500/30 bg-gradient-to-r from-yellow-600/20 to-amber-500/20">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-6 h-6 bg-yellow-500 rounded-full">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-yellow-300">{{ __('egi.status.draft') }}</span>
                    </div>
                </div>

            @elseif ($displayPrice && $displayPrice > 0)
                {{-- ACTIVE PRICE - From highest reservation or base price --}}
                <div class="p-3 border rounded-xl border-green-500/30 bg-gradient-to-r from-green-500/20 to-emerald-500/20">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center w-6 h-6 bg-green-500 rounded-full">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-green-300">
                                @if ($highestReservation)
                                    {{ __('egi.reservation.highest_bid') }}
                                @else
                                    {{ __('egi.price.price') }}
                                @endif
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-white" data-price-display>
                                <x-currency-price :price="$displayPrice" :egi="$egi" size="small" />
                            </span>
                        </div>
                    </div>

                    {{-- Show who has highest reservation --}}
                    @if ($highestReservation && $highestReservation->user)
                        @php
                        $isWeakReservation = $highestReservation->type === 'weak';
                        $activatorDisplay = !$isWeakReservation ? formatActivatorDisplay($highestReservation->user) : null;
                        @endphp
                        <div class="flex items-center gap-2 pt-2 border-t border-green-500/20">
                            <div class="flex items-center justify-center flex-shrink-0 w-4 h-4 {{ $isWeakReservation ? 'bg-amber-600' : 'bg-green-600' }} rounded-full">
                                @if ($isWeakReservation)
                                    {{-- Weak reservation: generic icon --}}
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    {{-- Strong reservation: usa sempre l'avatar dal backend --}}
                                    @if ($activatorDisplay && $activatorDisplay['avatar'])
                                        <img src="{{ $activatorDisplay['avatar'] }}"
                                             alt="{{ $activatorDisplay['name'] }}"
                                             class="object-cover w-4 h-4 border rounded-full border-white/20">
                                    @else
                                        {{-- Fallback solo se non c'è avatar dal backend (caso molto raro) --}}
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                @endif
                            </div>
                            <span class="text-xs {{ $isWeakReservation ? 'text-amber-200' : 'text-green-200' }} truncate">
                                @if ($isWeakReservation)
                                    {{ __('egi.reservation.weak_bidder') }}:
                                    <span class="font-semibold" data-activator-name>{{ $highestReservation->fegi_code ?? 'FG#******' }}</span>
                                @else
                                    {{ __('egi.reservation.activator') }}:
                                    <span class="font-semibold" data-activator-name>{{ $activatorDisplay['name'] }}</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>

            @else
                {{-- NOT FOR SALE --}}
                <div class="flex items-center justify-center p-3 border rounded-xl border-gray-500/30 bg-gradient-to-r from-gray-600/20 to-gray-500/20">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-6 h-6 bg-gray-500 rounded-full">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM4 10a6 6 0 1112 0A6 6 0 014 10z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.status.not_for_sale') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Utility Images Carousel --}}
    @if($egi->utility && $egi->utility->getMedia('utility_gallery')->count() > 0)
        <div class="px-2 pb-2 border-t border-white/5">

            @if($egi->utility->getMedia('utility_gallery')->count() > 5)
            <div class="mt-1 text-center">
                <span class="text-[10px] text-gray-400">
                    {{ __('utility.available_images', ['title' => $egi->utility->title, 'count' => $egi->utility->getMedia('utility_gallery')->count()]) }}
                </span>
            </div>
            @endif

            <!-- Container con larghezza massima per forzare overflow -->
            <div class="relative w-full" style="max-width: 280px;">
                <!-- Scrollable Container -->
                <div class="flex gap-2 py-1 overflow-x-auto utility-scroll-container scrollbar-hide"
                    style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                    @foreach($egi->utility->getMedia('utility_gallery') as $index => $media)
                    <div class="flex-shrink-0 w-12 h-12 overflow-hidden transition-all duration-200 rounded-lg cursor-pointer hover:scale-105 hover:shadow-lg hover:ring-2 hover:ring-white/50"
                        onclick="openUtilityImageModal('{{ $media->getUrl('large') }}', '{{ $egi->utility->title }}', {{ $index }})">
                        <img src="{{ $media->getUrl('thumb') }}"
                            alt="{{ $egi->utility->title }} - Image {{ $index + 1 }}"
                            class="object-cover w-full h-full transition-opacity duration-200 opacity-80 hover:opacity-100"
                            loading="lazy">
                    </div>
                    @endforeach
                </div>
            </div>


        </div>

        <style>
        .scrollbar-hide {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Forza lo scroll anche su desktop */
        .utility-scroll-container {
            overflow-x: scroll !important;
            cursor: grab;
        }

        .utility-scroll-container:active {
            cursor: grabbing;
        }
        </style>

        <script>
        // Abilita drag scroll su desktop
        document.querySelectorAll('.utility-scroll-container').forEach(container => {
            let isDown = false;
            let startX;
            let scrollLeft;

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });
        });
        </script>
    @endif


    {{-- 🔥 Reserve/Outbid Button - MANTIENE LA CLASSE reserve-button PER TYPESCRIPT --}}
    {{-- @if(!$isCreator && !$hideReserveButton && $egi->price && $egi->price > 0)
        <div class="mt-3">
            <button type="button"
                    class="reserve-button w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white
                           {{ $userReservation ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700' : 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700' }}
                           rounded-t-none rounded-b-lg transition-all transform hover:scale-[1.01]"
                    data-egi-id="{{ $egi->id }}">
                @if($userReservation)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    {{ __('egi.actions.outbid') }}
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('egi.actions.reserve') }}
                @endif
            </button>
        </div>
    @elseif(!$isCreator && !$hideReserveButton)
        <div class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-t-none rounded-b-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            {{ __('egi.status.not_for_sale') }}
        </div>
    @endif --}}
    @if(!$isCreator)
        <div class="mt-3">
            @if($egi->price && $egi->price > 0)
            <button type="button" class="reserve-button w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white
                    {{ $hasCurrentReservation ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700' : 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700' }}
                    rounded-t-none rounded-b-lg transition-all transform hover:scale-[1.01]" data-egi-id="{{ $egi->id }}">
                @if($hasCurrentReservation)
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                {{ __('egi.actions.outbid') ?? 'Rilancia' }}
                @else
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('egi.actions.reserve') ?? 'Prenota' }}
                @endif
            </button>
            @else
            <div
                class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-t-none rounded-b-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                {{ __('egi.status.not_for_sale') ?? 'Non in vendita' }}
            </div>
            @endif
        </div>
    @endif
</article>

<style>
.scrollbar-hide {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
