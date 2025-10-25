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
$creatorId = $egi->user_id ?? ($collection->creator_id ?? null);
$isCreator = $isAuthenticated && $currentUserId === $creatorId;
$egiCreator = $egi->user ?? null;
$imageUrl = $egiCreator->profile_photo_url ?? '';

// 📦 COLLECTION INFO
$egiCollection = $egi->collection ?? ($collection ?? null);

// ===========================
// 💰 RESERVATION LOGIC
// ===========================

// Get highest reservation from eager loaded collection (if available)
$highestReservation = $egi->reservations
    ? $egi->reservations->where('sub_status', 'highest')->where('status', 'active')->first()
    : $egi->reservations()->where('sub_status', 'highest')->where('status', 'active')->first();

// Check if current user has active reservation
$userReservation = null;
if ($isAuthenticated) {
    // Use eager loaded reservations if available, otherwise query
    if ($egi->reservations) {
        $userReservation = $egi->reservations
            ->where('user_id', $currentUserId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->first();
    } else {
        $userReservation = $egi
            ->reservations()
            ->where('user_id', $currentUserId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->first();
    }
}

// Determine display price
$displayPrice = $highestReservation
    ? $highestReservation->amount_eur ?? $highestReservation->offer_amount_fiat
    : $egi->price;

// 📦 Portfolio: calcolo stato outbid per applicare opacità e badge corretti
$portfolioOutbid = false;
if ($portfolioContext && $portfolioOwner) {
    try {
        // Ultima prenotazione del proprietario del portfolio su questo EGI
        $ownerLastReservation = $egi
            ->reservations()
            ->where('user_id', $portfolioOwner->id)
            ->orderByDesc('created_at')
            ->first();

        $isWinning =
            $ownerLastReservation &&
            $ownerLastReservation->is_current &&
            $ownerLastReservation->status === 'active' &&
            !$ownerLastReservation->superseded_by_id;
        $portfolioOutbid = $ownerLastReservation && !$isWinning;
    } catch (\Throwable $th) {
        $portfolioOutbid = false;
    }
}

// 🎨 Determina lo stato reale dell'EGI per i badge
    $isMinted = $egi->isMinted();
    $hasActiveReservations = $egi->reservations()->where('is_current', true)->where('status', 'active')->exists();
    $showActivationBadge = false;
    $badgeStatus = 'not_activated'; // Default: da attivare

    // Check prezzo e modalità vendita
    $egiPrice = $egi->price ?? 0;
    $saleMode = $egi->sale_mode ?? null;
    $isNotForSale = $saleMode === 'not_for_sale';
    $isAvailable = $egiPrice > 0;

    // 🆕 PRIORITÀ MASSIMA: Check pubblicazione (solo per owner/creator)
    $isOwner =
        $isAuthenticated &&
        ($currentUserId === $creatorId ||
            $currentUserId === ($egiCollection->owner_id ?? null) ||
            $currentUserId === ($egiCollection->creator_id ?? null));
    $isPublished = $egi->is_published ?? true;

    // 🎯 DETERMINAZIONE STATO BADGE - Ordine di priorità
    if (!$isPublished && $isOwner) {
        // 1️⃣ NON PUBBLICATO (solo per owner) - ROSSO
        $badgeStatus = 'not_published';
        $showActivationBadge = false;
    } elseif ($isMinted) {
        // 2️⃣ MINTATO - VIOLA
        $badgeStatus = 'minted';
        $showActivationBadge = false;
    } elseif ($hasActiveReservations) {
        // 3️⃣ PRENOTATO - ARANCIO
        $badgeStatus = 'reserved';
        $showActivationBadge = false;
    } elseif ($isNotForSale) {
        // 4️⃣ NON IN VENDITA - BLU SCURO
        $badgeStatus = 'not_for_sale';
        $showActivationBadge = false;
    } elseif (!$isAvailable) {
        // 5️⃣ PREZZO ZERO (non disponibile) - GRIGIO SCURO
        $badgeStatus = 'not_available';
        $showActivationBadge = false;
    } else {
        // 6️⃣ DA ATTIVARE (disponibile) - VERDE
        $badgeStatus = 'not_activated';
        $showActivationBadge = true;
    }

    // 🎨 Determina colori badge basati su FlorenceEGI Brand Guidelines
    $badgeColor = match ($badgeStatus) {
        'not_published' => 'bg-[#C13120]/90', // 🔴 Rosso Urgenza - SOLO per non pubblicato
        'minted' => 'bg-[#8E44AD]/90', // 🟣 Viola Innovazione (premium, futuro)
        'reserved' => 'bg-[#E67E22]/90', // 🟠 Arancio Energia (notifiche positive)
        'not_for_sale' => 'bg-[#34495E]/90', // 🔵 Blu Scuro (non in vendita)
        'not_activated' => 'bg-[#2D5016]/90', // 🟢 Verde Rinascita (disponibile)
        'not_available' => 'bg-[#7F8C8D]/90', // ⚪ Grigio Scuro (prezzo zero)
        default => 'bg-gray-500/90', // Grigio per stati imprevisti
    };

    // Determina il label del badge basato sul contesto
    if ($badgeStatus === 'not_published') {
        // Badge NON PUBBLICATA ha priorità su tutto (solo per owner)
        $badgeLabel = __('egi.badge.not_published');
    } elseif ($creatorPortfolioContext) {
        // Nel Creator Portfolio
        $badgeLabel = match ($badgeStatus) {
            'minted' => __('egi.badge.minted'),
            'reserved' => __('egi.badge.reserved'),
            'not_for_sale' => __('egi.badge.not_for_sale'),
            default => __('egi.badge.to_activate'),
        };
    } elseif ($portfolioContext) {
        // Nel Portfolio (non creator)
        $badgeLabel = $hasActiveReservations || $isMinted ? __('egi.badge.winning_bid') : __('egi.badge.not_owned');
    } else {
        // Card normale (non portfolio)
        // 🔨 AUCTION MODE: Mostra "Da Mintare" se EGI è all'asta
    if (($egi->sale_mode ?? null) === 'auction' && $badgeStatus !== 'minted' && $badgeStatus !== 'not_for_sale') {
        $badgeLabel = __('egi.badge.auction_active'); // "Da Mintare"
        $badgeColor = 'bg-gradient-to-r from-amber-500 to-orange-600'; // Colore distintivo per asta
    } else {
        $badgeLabel = match ($badgeStatus) {
            'not_published' => __('egi.badge.not_published'),
            'minted' => __('egi.badge.minted'),
            'reserved' => __('egi.badge.reserved'),
            'not_for_sale' => __('egi.badge.not_for_sale'),
            'not_activated' => __('egi.badge.to_activate'),
            'not_available' => __('egi.status.not_available'),
            default => __('egi.badge.to_activate'),
        };
    }
}

// 🔒 Creator check: Determina se l'utente corrente è il creatore dell'EGI
$creatorId = $egi->user_id ?? ($collection->creator_id ?? null);
$isCreator = auth()->check() && auth()->id() === $creatorId;

// 🔄 Controlla se c'è una prenotazione corrente per il pulsante Rilancia
    $hasCurrentReservation = $egi->reservations && $egi->reservations->where('is_current', true)->first();
    $isCreator = auth()->check() && auth()->id() === $creatorId;
@endphp

{{-- Include CSS hyper se necessario --}}
@if ($isHyper)
    @once
        <link rel="stylesheet" href="{{ asset('css/egi-hyper.css') }}">
    @endonce
@endif

{{-- Include CSS tooltip per descrizioni --}}
@once
    <link rel="stylesheet" href="{{ asset('css/egi-card-tooltip.css') }}">
@endonce

{{-- 🧱 Card Container --}}
@php
    // 🎨 Design speciale per EGI mintati - ELEGANZA RINASCIMENTALE CERTIFICATA
    // Background: Gradient viola brillante + border oro spesso + glow intenso
    $mintedClasses = $isMinted
        ? 'bg-gradient-to-br from-[#4A1D96] via-[#8E44AD] to-[#B565D8] border-[#D4A574] shadow-[0_0_30px_rgba(212,165,116,0.6)] hover:shadow-[0_0_50px_rgba(212,165,116,0.9)]'
        : 'bg-gray-900 border-purple-500/30 border-2 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/20';

    // Border thickness: mintato = 4px oro, normale = 2px purple
    $mintedBorder = $isMinted ? 'border-[4px]' : 'border-2';
@endphp

<article
    class="egi-card {{ $isHyper ? 'egi-card--hiper' : '' }} {{ $portfolioOutbid ? 'opacity-35 hover:opacity-70' : '' }} {{ $mintedClasses }} {{ $mintedBorder }} group relative w-full overflow-hidden rounded-2xl transition-all duration-500"
    data-egi-id="{{ $egi->id }}" data-hyper="{{ $isHyper ? '1' : '0' }}" data-minted="{{ $isMinted ? '1' : '0' }}"
    style="{{ $isHyper ? '--energy:0.95; --foilHue:265; --edge:#9b5cf6; --accent:#a78bfa;' : '' }}">

    {{-- 🎨 Pattern rinascimentale GOLD per EGI mintati - MOLTO PIÙ VISIBILE --}}
    @if ($isMinted)
        <div class="pointer-events-none absolute inset-0 opacity-15" aria-hidden="true">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="renaissance-pattern-{{ $egi->id }}" x="0" y="0" width="60" height="60"
                        patternUnits="userSpaceOnUse">
                        {{-- Grandi cerchi oro --}}
                        <circle cx="10" cy="10" r="3" fill="#D4A574" opacity="0.6" />
                        <circle cx="40" cy="40" r="3" fill="#D4A574" opacity="0.6" />
                        {{-- Piccoli cerchi decorativi --}}
                        <circle cx="30" cy="10" r="1.5" fill="#F4D799" opacity="0.8" />
                        <circle cx="10" cy="40" r="1.5" fill="#F4D799" opacity="0.8" />
                        {{-- Curve rinascimentali --}}
                        <path d="M 15 15 Q 25 10 35 15 T 55 15" stroke="#D4A574" fill="none" stroke-width="1"
                            opacity="0.5" />
                        <path d="M 5 30 Q 15 25 25 30 T 45 30" stroke="#F4D799" fill="none" stroke-width="0.8"
                            opacity="0.4" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#renaissance-pattern-{{ $egi->id }})" />
            </svg>
        </div>

        {{-- Barra superiore GOLD accent per maggiore visibilità --}}
        <div class="pointer-events-none absolute left-0 right-0 top-0 h-1 bg-gradient-to-r from-transparent via-[#D4A574] to-transparent opacity-80"
            aria-hidden="true"></div>

        {{-- Effetto shimmer animato gold (eleganza certificato) --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div
                class="absolute -inset-full skew-x-12 animate-[shimmer_3s_ease-in-out_infinite] bg-gradient-to-r from-transparent via-[#D4A574]/20 to-transparent">
            </div>
        </div>
    @endif

    @if ($isHyper)
        <div class="egi-sparkles" aria-hidden="true"></div>
        {{-- Badge HYPER normale solo se NON c'è badge composto --}}
        @if (!$showPurchasePrice && !$portfolioContext)
            <div class="egi-hyper-badge">⭐ HYPER ⭐</div>
        @endif
    @endif
    {{-- 🖼️ Sezione Immagine --}}
    <figure class="relative aspect-[4/5] w-full overflow-hidden bg-black">
        <a href="{{ route('egis.show', $egi->id) }}" class="block h-full w-full">
            @php
                // � OTTIMIZZAZIONE IMMAGINI: usa l'accessor del modello con fallback interno
// getMainImageUrlAttribute() restituisce la variante 'card' se presente,
// altrimenti fa fallback all'originale su disco pubblico.
                $optimizedImageUrl = $egi->main_image_url;
            @endphp

            {{-- 🎯 Immagine Principale o Placeholder --}}
            @if ($optimizedImageUrl)
                <img src="{{ $optimizedImageUrl }}" {{-- Usa l'URL ottimizzato con fallback --}} alt="{{ $egi->title ?? 'EGI Image' }}"
                    class="h-full w-full bg-gray-800 object-contain object-center transition-transform duration-300 ease-in-out group-hover:scale-105"
                    loading="lazy" {{-- Supporto WebP con fallback automatico del browser --}}
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
            @else
                {{-- Placeholder --}}
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                    <svg class="h-16 w-16 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            {{-- Overlay leggero su hover --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            </div>
        </a>

        {{-- Logo piattaforma posizionato fuori dal badge --}}
        <img src="{{ asset('images/logo/logo_1.webp') }}" alt=""
            class="absolute left-2 top-2 h-6 w-6 opacity-70 transition-opacity duration-200 hover:opacity-100"
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
        <div class="absolute left-0 top-9 z-10">
            <span
                class="{{ $categoryClasses }} inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold tracking-wide shadow ring-1 ring-white/10 backdrop-blur-sm"
                title="{{ $categoryName }}" aria-label="EGI Category: {{ $categoryName }}"
                data-cat-name="{{ $categoryName }}" data-cat-classes="{{ $categoryClasses }}">
                {{ Str::limit($categoryName, 14) }}
            </span>
        </div>

        {{-- 🌟 BADGE COMPOSTO HYPER + POSSEDUTO (SOLUZIONE MICHELIN) --}}
        @if ($showPurchasePrice && $isHyper)
            <div class="badge-composite">
                <div class="hyper-overlay">⭐ HYPER ⭐</div>
                <div class="owned-base">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $badgeLabel }}
                </div>
            </div>
            {{-- Badge Owned normale (no HYPER) - Usa colore dinamico basato su status --}}
        @elseif ($showPurchasePrice)
            <span
                class="{{ $badgeColor }} absolute right-2 top-2 inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                {{ $badgeLabel }}
            </span>
            {{-- 🚀 NEW: Context-aware badges per portfolio --}}
        @elseif ($portfolioContext)
            @php
                // CREATOR PORTFOLIO: Logica speciale per il portfolio del creator
                if ($creatorPortfolioContext) {
                    // Nel Creator Portfolio, usa il badgeStatus già calcolato sopra
                    $isWinning = in_array($badgeStatus, ['minted', 'reserved']);
                    $badgeLabel = match ($badgeStatus) {
                        'minted' => __('egi.badge.minted'),
                        'reserved' => __('egi.badge.reserved'),
                        'not_for_sale' => __('egi.badge.not_for_sale'),
                        'not_available' => __('egi.status.not_available'),
                        'not_published' => __('egi.badge.not_published'),
                        default => __('egi.badge.to_activate'),
                    };
                } else {
                    // COLLECTOR PORTFOLIO: Logica normale per altri portfolio
                    $ownerReservation = $egi
                        ->reservations()
                        ->where('user_id', $portfolioOwner->id)
                        ->orderByDesc('created_at')
                        ->first();
                    $isWinning =
                        $ownerReservation &&
                        $ownerReservation->is_current &&
                        $ownerReservation->status === 'active' &&
                        !$ownerReservation->superseded_by_id;
                    $badgeLabel = $isWinning ? __('egi.badge.winning_bid') : __('egi.badge.not_owned');
                }
            @endphp

            @if ($isWinning)
                @if ($isHyper)
                    {{-- Badge composto HYPER + Status dynamico --}}
                    <div class="badge-composite" data-portfolio-badge="1" title="{{ $badgeLabel }}"
                        data-lbl-winning="{{ $badgeLabel }}" data-lbl-not-owned="{{ __('egi.badge.not_owned') }}">
                        <div class="hyper-overlay">⭐ HYPER ⭐</div>
                        <div class="owned-base">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                @if ($badgeStatus === 'minted')
                                    {{-- Icona "Mintato" (blockchain link) --}}
                                    <path fill-rule="evenodd"
                                        d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                                        clip-rule="evenodd" />
                                @elseif($badgeStatus === 'reserved')
                                    {{-- Icona "Prenotato" (bookmark) --}}
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                @else
                                    {{-- Icona "Check" default --}}
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                @endif
                            </svg>
                            {{ $badgeLabel }}
                        </div>
                    </div>
                @else
                    <span data-portfolio-badge="1" data-lbl-winning="{{ $badgeLabel }}"
                        data-lbl-not-owned="{{ __('egi.badge.not_owned') }}"
                        class="{{ $badgeColor }} absolute right-2 top-2 inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm"
                        title="{{ $badgeLabel }}">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            @if ($badgeStatus === 'minted')
                                {{-- Icona "Mintato" (blockchain link) --}}
                                <path fill-rule="evenodd"
                                    d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                                    clip-rule="evenodd" />
                            @elseif($badgeStatus === 'reserved')
                                {{-- Icona "Prenotato" (bookmark) --}}
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                            @else
                                {{-- Icona "Check" default --}}
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            @endif
                        </svg>
                        {{ $badgeLabel }}
                    </span>
                @endif
            @else
                @if ($isHyper)
                    {{-- Badge composto HYPER + NON ATTIVATO --}}
                    <div class="badge-composite" data-portfolio-badge="1" title="{{ $badgeLabel }}"
                        data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
                        data-lbl-not-owned="{{ $badgeLabel }}">
                        <div class="hyper-overlay">⭐ HYPER ⭐</div>
                        <div class="not-owned-base">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                @if ($showActivationBadge)
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
                            {{ $badgeLabel }}
                        </div>
                    </div>
                @else
                    <span data-portfolio-badge="1" data-lbl-winning="{{ __('egi.badge.winning_bid') }}"
                        data-lbl-not-owned="{{ $badgeLabel }}"
                        class="{{ $badgeColor }} absolute right-2 top-2 inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm"
                        title="{{ $badgeLabel }}">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            @if ($showActivationBadge)
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
                        {{ $badgeLabel }}
                    </span>
                @endif
            @endif
            {{-- Badge per card normale (non portfolio) - Mostra TUTTI gli stati --}}
        @elseif (!$isCreator)
            <span
                class="{{ $badgeColor }} absolute right-2 top-2 inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm"
                title="{{ $badgeLabel }}">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    @if ($badgeStatus === 'minted')
                        {{-- Icona "Mintato" (blockchain link) --}}
                        <path fill-rule="evenodd"
                            d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                            clip-rule="evenodd" />
                    @elseif($badgeStatus === 'reserved')
                        {{-- Icona "Prenotato" (bookmark) --}}
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                    @elseif($badgeStatus === 'not_for_sale')
                        {{-- Icona "Non in vendita" (ban/block) --}}
                        <path fill-rule="evenodd"
                            d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                            clip-rule="evenodd" />
                    @elseif($badgeStatus === 'not_available')
                        {{-- Icona "Non disponibile" (warning) --}}
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    @elseif($badgeStatus === 'not_published')
                        {{-- Icona "Non pubblicato" (eye-off) --}}
                        <path fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd" />
                    @else
                        {{-- Icona "Da attivare" (play) --}}
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8.108v3.784a1 1 0 001.555.94l3.108-1.892a1 1 0 000-1.688L9.555 7.168z"
                            clip-rule="evenodd" />
                    @endif
                </svg>
                {{ $badgeLabel }}
            </span>
            {{-- Badge per contenuto media --}}
        @elseif ($egi->media)
            <span
                class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-sm"
                title="{{ __('egi.badge.media_content') }}">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z" />
                </svg>
            </span>
        @endif
    </figure>

    {{-- ℹ️ Information Section --}}
    <div class="flex flex-1 flex-col justify-between bg-gradient-to-b from-gray-900/50 to-gray-900 p-4">
        {{-- Title and Like --}}
        <div>
            <div class="mb-2 flex items-center gap-2">
                <div
                    class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                    <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <h3
                    class="{{ $egi->description ? 'has-description' : '' }} flex-1 text-base font-bold leading-tight text-white transition-colors duration-200 group-hover:text-purple-300">
                    {{ Str::limit($egi->title ?? __('egi.title.untitled'), 45) }}

                    {{-- Tooltip for description --}}
                    @if ($egi->description)
                        <div
                            class="absolute bottom-full left-1/2 z-50 mb-2 min-w-64 max-w-80 rounded-lg border border-gray-700 bg-gray-900 px-3 py-2 text-sm font-normal text-white shadow-xl">
                            {{ Str::limit($egi->description, 200) }}
                        </div>
                    @endif
                </h3>

                {{-- Like Button --}}
                @if (!$isCreator)
                    <div class="flex-shrink-0">
                        <x-like-button :resourceType="'egi'" :resourceId="$egi->id" :isLiked="$egi->is_liked ?? false" :likesCount="$egi->likes_count ?? 0"
                            size="md" :showCounter="true" position="relative" theme="overlay" />
                    </div>
                @endif
            </div>

            {{-- 🎨 CREATOR INFO - SEMPRE VISIBILE --}}
            @if ($egiCreator)
                <div class="mb-2 flex items-center gap-2 rounded-lg border border-gray-700/50 bg-gray-800/50 p-2"
                    data-creator-info>
                    <div
                        class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-cyan-500">
                        <img src="{{ $imageUrl }}" alt="{{ $egiCreator->name }}"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy" decoding="async">
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.creator.created_by') }}</span>
                        <span class="ml-1 truncate text-xs font-semibold text-white">{{ $egiCreator->name }}</span>
                    </div>
                </div>
            @endif

            {{-- 📦 COLLECTION INFO --}}
            @if ($egiCollection)
                <div class="mb-2 flex items-center gap-2 rounded-lg border border-gray-700/50 bg-gray-800/50 p-2"
                    data-collection-info>
                    <div
                        class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-indigo-500">
                        @php
                            $collectionImageUrl = '';
                            if (method_exists($egiCollection, 'getFirstMediaUrl')) {
                                $collectionImageUrl = $egiCollection->getFirstMediaUrl('head', 'card');
                            }
                        @endphp
                        @if ($collectionImageUrl)
                            <img src="{{ $collectionImageUrl }}" alt="{{ $egiCollection->collection_name }}"
                                class="h-full w-full rounded-full object-cover transition-transform duration-300 group-hover:scale-105"
                                loading="lazy" decoding="async">
                        @else
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-medium text-gray-300">{{ __('egi.collection.part_of') }}</span>
                        <span
                            class="ml-1 truncate text-xs font-semibold text-white">{{ $egiCollection->collection_name }}</span>
                    </div>
                </div>
            @endif

            {{-- 📊 RESERVATION COUNT --}}
            @if ($egi->reservations && $egi->reservations->count() > 0)
                <div class="mb-2 flex items-center gap-2 rounded-lg border border-gray-700/50 bg-gray-800/50 p-2"
                    data-reservation-count>
                    <div
                        class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                        <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1a1 1 0 100-2 2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-medium text-gray-300">
                            {{ $egi->reservations->count() }} {{ __('egi.reservation.count') }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- 💰 PRICE SECTION - SIMPLIFIED --}}
        <div class="mt-4">
            @if ($displayPrice && $displayPrice > 0)
                {{-- ACTIVE PRICE - From highest reservation or base price --}}
                <div
                    class="rounded-xl border border-green-500/30 bg-gradient-to-r from-green-500/20 to-emerald-500/20 p-3">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500">
                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd" />
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

                    {{-- Show Co-Creator (if minted) or Reservation (if not minted) --}}
                    @if ($isMinted && $egi->blockchain && $egi->blockchain->buyer)
                        {{-- MINTED: Show Co-Creator from blockchain --}}
                        @php
                            $coCreatorDisplay = formatActivatorDisplay($egi->blockchain->buyer);
                        @endphp
                        <div class="flex items-center gap-2 border-t border-purple-500/20 pt-2">
                            <div
                                class="flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full bg-purple-600">
                                @if ($coCreatorDisplay && $coCreatorDisplay['avatar'])
                                    <img src="{{ $coCreatorDisplay['avatar'] }}"
                                        alt="{{ $coCreatorDisplay['name'] }}"
                                        class="h-4 w-4 rounded-full border border-white/20 object-cover">
                                @else
                                    <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span class="truncate text-xs text-purple-200">
                                {{ __('egi.creator.co_creator') }}
                                <span class="font-semibold" data-activator-name>{{ $coCreatorDisplay['name'] }}</span>
                            </span>
                        </div>
                    @elseif ($highestReservation && $highestReservation->user && !$isMinted)
                        {{-- NOT MINTED: Show Reservation --}}
                        @php
                            $isWeakReservation = $highestReservation->type === 'weak';
                            $reservationDisplay = !$isWeakReservation
                                ? formatActivatorDisplay($highestReservation->user)
                                : null;
                        @endphp
                        <div class="flex items-center gap-2 border-t border-green-500/20 pt-2">
                            <div
                                class="{{ $isWeakReservation ? 'bg-amber-600' : 'bg-green-600' }} flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full">
                                @if ($isWeakReservation)
                                    {{-- Weak reservation: generic icon --}}
                                    <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    {{-- Strong reservation: usa sempre l'avatar dal backend --}}
                                    @if ($reservationDisplay && $reservationDisplay['avatar'])
                                        <img src="{{ $reservationDisplay['avatar'] }}"
                                            alt="{{ $reservationDisplay['name'] }}"
                                            class="h-4 w-4 rounded-full border border-white/20 object-cover">
                                    @else
                                        {{-- Fallback solo se non c'è avatar dal backend (caso molto raro) --}}
                                        <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                @endif
                            </div>
                            <span
                                class="{{ $isWeakReservation ? 'text-amber-200' : 'text-green-200' }} truncate text-xs">
                                @if ($isWeakReservation)
                                    {{ __('egi.reservation.weak_bidder') }}:
                                    <span class="font-semibold"
                                        data-activator-name>{{ $highestReservation->fegi_code ?? 'FG#******' }}</span>
                                @else
                                    {{ __('egi.reservation.reserved_by') }}
                                    <span class="font-semibold"
                                        data-activator-name>{{ $reservationDisplay['name'] }}</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- AUCTION INFO BOX - Mostra dettagli asta se sale_mode = auction --}}
    @if (($egi->sale_mode ?? null) === 'auction' && !$egi->minted)
        <div class="px-2 pb-2">
            <div
                class="space-y-2 rounded-xl border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-orange-500/20 p-3">
                {{-- Auction Header --}}
                <div class="flex items-center gap-2 border-b border-amber-500/20 pb-2">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-500">
                        <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-amber-300">{{ __('egi.auction.auction_details') }}</span>
                </div>

                {{-- Minimum Price --}}
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-gray-400">{{ __('egi.auction.minimum_price') }}:</span>
                    <x-currency-price :price="$egi->auction_minimum_price ?? 0" :currency="__('utility.currency_symbol')" textSize="text-xs" fontWeight="font-bold"
                        color="text-amber-400" />
                </div>

                {{-- Current Bid (se ci sono offerte) --}}
                @php
                    $highestAuctionBid = $egi
                        ->reservations()
                        ->where('is_highest', true)
                        ->where('amount_eur', '>=', $egi->auction_minimum_price ?? 0)
                        ->orderBy('amount_eur', 'desc')
                        ->first();
                @endphp

                @if ($highestAuctionBid)
                    <div class="flex items-center justify-between border-t border-amber-500/20 pt-1">
                        <span class="text-[10px] text-gray-400">{{ __('egi.auction.current_bid') }}:</span>
                        <x-currency-price :price="$highestAuctionBid->amount_eur" :currency="__('utility.currency_symbol')" textSize="text-xs"
                            fontWeight="font-bold" color="text-green-400" />
                    </div>
                @else
                    <div class="flex items-center justify-center border-t border-amber-500/20 pt-1">
                        <span class="text-[10px] italic text-gray-500">{{ __('egi.auction.no_bids') }}</span>
                    </div>
                @endif

                {{-- Auction Dates --}}
                <div class="space-y-1 border-t border-amber-500/20 pt-2">
                    {{-- Start Date --}}
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-400">{{ __('egi.auction.starts_at') }}:</span>
                        <span class="text-[10px] font-medium text-amber-300">
                            {{ $egi->auction_start ? $egi->auction_start->format('d M Y, H:i') : 'N/A' }}
                        </span>
                    </div>

                    {{-- End Date with Status --}}
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-gray-400">{{ __('egi.auction.ends_at') }}:</span>
                        <span class="text-[10px] font-medium text-orange-300">
                            {{ $egi->auction_end ? $egi->auction_end->format('d M Y, H:i') : 'N/A' }}
                        </span>
                    </div>

                    {{-- Time Remaining (se l'asta è attiva) --}}
                    @if ($egi->auction_start && $egi->auction_end)
                        @php
                            $now = now();
                            $auctionStarted = $now->greaterThanOrEqualTo($egi->auction_start);
                            $auctionEnded = $now->greaterThanOrEqualTo($egi->auction_end);
                            $timeRemaining = null;

                            if ($auctionStarted && !$auctionEnded) {
                                $diff = $now->diff($egi->auction_end);
                                $timeRemaining = [
                                    'days' => $diff->d,
                                    'hours' => $diff->h,
                                    'minutes' => $diff->i,
                                ];
                            }
                        @endphp

                        @if (!$auctionStarted)
                            <div
                                class="mt-1 flex items-center justify-center gap-1 rounded-lg bg-blue-500/20 px-2 py-1">
                                <svg class="h-3 w-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span
                                    class="text-[10px] font-medium text-blue-300">{{ __('egi.auction.not_started') }}</span>
                            </div>
                        @elseif ($auctionEnded)
                            <div
                                class="mt-1 flex items-center justify-center gap-1 rounded-lg bg-red-500/20 px-2 py-1">
                                <svg class="h-3 w-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span
                                    class="text-[10px] font-medium text-red-300">{{ __('egi.auction.ended') }}</span>
                            </div>
                        @elseif ($timeRemaining)
                            <div
                                class="mt-1 flex items-center justify-center gap-1 rounded-lg bg-green-500/20 px-2 py-1">
                                <svg class="h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-[10px] font-medium text-green-300">
                                    @if ($timeRemaining['days'] > 0)
                                        {{ $timeRemaining['days'] }} {{ __('egi.auction.days') }}
                                    @elseif ($timeRemaining['hours'] > 0)
                                        {{ $timeRemaining['hours'] }}h {{ $timeRemaining['minutes'] }}m
                                    @else
                                        {{ $timeRemaining['minutes'] }} {{ __('egi.auction.minutes') }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Utility Images Carousel --}}
    @if ($egi->utility && $egi->utility->getMedia('utility_gallery')->count() > 0)
        <div class="border-t border-white/5 px-2 pb-2">

            @if ($egi->utility->getMedia('utility_gallery')->count() > 5)
                <div class="mt-1 text-center">
                    <span class="text-[10px] text-gray-400">
                        {{ __('utility.available_images', ['title' => $egi->utility->title, 'count' => $egi->utility->getMedia('utility_gallery')->count()]) }}
                    </span>
                </div>
            @endif

            <!-- Container con larghezza massima per forzare overflow -->
            <div class="relative w-full" style="max-width: 280px;">
                <!-- Scrollable Container -->
                <div class="utility-scroll-container scrollbar-hide flex gap-2 overflow-x-auto py-1"
                    style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                    @foreach ($egi->utility->getMedia('utility_gallery') as $index => $media)
                        <div class="h-12 w-12 flex-shrink-0 cursor-pointer overflow-hidden rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-lg hover:ring-2 hover:ring-white/50"
                            onclick="openUtilityImageModal('{{ $media->getUrl('large') }}', '{{ $egi->utility->title }}', {{ $index }})">
                            <img src="{{ $media->getUrl('thumb') }}"
                                alt="{{ $egi->utility->title }} - Image {{ $index + 1 }}"
                                class="h-full w-full object-cover opacity-80 transition-opacity duration-200 hover:opacity-100"
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
    {{-- @if (!$isCreator && !$hideReserveButton && $egi->price && $egi->price > 0)
        <div class="mt-3">
            <button type="button"
                    class="reserve-button w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white
                           {{ $userReservation ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700' : 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700' }}
                           rounded-t-none rounded-b-lg transition-all transform hover:scale-[1.01]"
                    data-egi-id="{{ $egi->id }}">
                @if ($userReservation)
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

    {{-- 🚀 PHASE 2: DUAL PATH BUTTONS (Mint Direct OR Reserve) + AUCTION MODE --}}
    @if (!$isCreator)
        @php
            // Use EgiAvailabilityService for comprehensive availability check
            $availabilityService = app(\App\Services\EgiAvailabilityService::class);
            $availability = $availabilityService->checkAvailability($egi, auth()->user());

            $canMint = $availability['can_mint'];
            $canReserve = $availability['can_reserve'];
            $isReservedByUser = $availability['is_reserved_by_user'];
            $recommendedAction = $availability['recommended_action'];
            $availableActions = $availability['available_actions'];

            // Price for display
            $displayPriceForAction = $egi->price ?? 0;

            // 🚨 CRITICAL: Determine if buttons should be shown based on sale_mode
            $saleMode = $egi->sale_mode ?? 'fixed_price'; // Default to fixed_price if not set
            $isAuctionMode = $saleMode === 'auction';
            $isFixedPrice = $saleMode === 'fixed_price';
            $isNotForSale = $saleMode === 'not_for_sale';

            // Show buttons logic:
            // - Auction mode: always show offer button (even if price = 0)
            // - Fixed price: show only if price > 0
            // - Not for sale: never show action buttons
            $showButtons = !$isNotForSale && ($isAuctionMode || ($isFixedPrice && $displayPriceForAction > 0));
        @endphp

        <div class="mt-3">
            @if ($showButtons && count($availableActions) > 0)
                {{-- ✅ SCENARIO 1: User has reservation → Show MINT button (complete purchase) - VIOLA --}}
                @if ($isReservedByUser && $canMint && $userReservation)
                    @if ($isAuctionMode)
                        {{-- Auction Mode: User won auction, show "Completa Acquisto" --}}
                        <a href="{{ route('mint.payment-form', ['egiId' => $egi->id]) }}?reservation_id={{ $userReservation->id }}"
                            class="mint-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-green-600 hover:to-green-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('egi.actions.complete_purchase') ?? 'Completa Acquisto' }} ·
                            €{{ number_format($userReservation->amount_eur, 2, ',', '.') }}
                        </a>
                    @else
                        {{-- Fixed Price Mode: User reserved, show "Minta Subito" --}}
                        <a href="{{ route('mint.payment-form', ['egiId' => $egi->id]) }}?reservation_id={{ $userReservation->id }}"
                            class="mint-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6" />
                            </svg>
                            {{ __('egi.actions.mint_now') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>
                    @endif

                    {{-- ✅ SCENARIO 2: Both MINT and RESERVE available (dual path) → Show both buttons --}}
                @elseif($canMint && $canReserve)
                    @if ($isAuctionMode && $displayPriceForAction > 0)
                        {{-- Auction Mode WITH Fixed Price: Show both "Minta Subito" (fixed) + "Fai un'Offerta" (auction) --}}
                        <div class="grid grid-cols-2 gap-2">
                            {{-- Fai un'Offerta Button (left) - AMBER/ORANGE (auction) --}}
                            <button type="button"
                                class="reserve-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 px-3 py-2 text-xs font-medium text-white transition-all hover:scale-[1.02] hover:from-amber-600 hover:to-orange-700"
                                data-egi-id="{{ $egi->id }}">
                                <span class="mr-1.5" aria-label="Asta" title="Asta">🔨</span>
                                {{ __('egi.actions.make_offer') }}
                            </button>

                            {{-- Minta Subito Button (right, emphasized) - VIOLA (fixed price) --}}
                            <a href="{{ route('egi.mint-direct', $egi->id) }}"
                                class="mint-direct-button flex transform items-center justify-center rounded-lg bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-3 py-2 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.02] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                                <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('egi.actions.mint_direct') }}
                            </a>
                        </div>
                    @elseif ($isAuctionMode && $displayPriceForAction == 0)
                        {{-- Auction Mode WITHOUT Fixed Price (price = 0): Show ONLY "Fai un'Offerta" --}}
                        <button type="button"
                            class="reserve-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-2 text-sm font-medium text-white transition-all hover:scale-[1.01] hover:from-amber-600 hover:to-orange-700"
                            data-egi-id="{{ $egi->id }}">
                            <span class="mr-2" aria-label="Asta" title="Asta">🔨</span>
                            {{ __('egi.actions.make_offer') }}
                        </button>
                    @elseif ($isFixedPrice && $displayPriceForAction > 0)
                        {{-- Fixed Price Mode: Show ONLY "Minta Subito" (full width) --}}
                        <a href="{{ route('egi.mint-direct', $egi->id) }}"
                            class="mint-direct-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('egi.actions.mint_direct') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>
                    @endif

                    {{-- ✅ SCENARIO 3: Only MINT available (no reservation possible) - VIOLA --}}
                @elseif($canMint && !$canReserve)
                    @if ($isFixedPrice && $displayPriceForAction > 0)
                        {{-- Fixed Price: Show "Minta Subito" --}}
                        <a href="{{ route('egi.mint-direct', $egi->id) }}"
                            class="mint-direct-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-[#8E44AD] to-[#9b59b6] px-4 py-2 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.01] hover:from-[#7d3c98] hover:to-[#8e44ad]">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('egi.actions.mint_direct') }} ·
                            €{{ number_format($displayPriceForAction, 2, ',', '.') }}
                        </a>
                    @endif

                    {{-- ✅ SCENARIO 4: Only RESERVE available (already reserved by others or user can only reserve) --}}
                @elseif(!$canMint && $canReserve)
                    @if ($isAuctionMode)
                        {{-- Auction Mode: Show "Fai un'Offerta" - AMBER/ORANGE --}}
                        <button type="button"
                            class="reserve-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-2 text-sm font-medium text-white transition-all hover:scale-[1.01] hover:from-amber-600 hover:to-orange-700"
                            data-egi-id="{{ $egi->id }}">
                            <span class="mr-2" aria-label="Asta" title="Asta">🔨</span>
                            {{ __('egi.actions.make_offer') }}
                        </button>
                    @else
                        {{-- Fixed Price Mode: Show "Attiva" or "Rilancia" - ARANCIONE --}}
                        <button type="button"
                            class="reserve-button flex w-full transform items-center justify-center rounded-b-lg rounded-t-none bg-gradient-to-r from-[#E67E22] to-[#d35400] px-4 py-2 text-sm font-medium text-white transition-all hover:scale-[1.01] hover:from-[#d35400] hover:to-[#ba4a00]"
                            data-egi-id="{{ $egi->id }}">
                            @if ($hasCurrentReservation)
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                {{ __('egi.actions.outbid') }}
                            @else
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('egi.actions.reserve') }}
                            @endif
                        </button>
                    @endif
                @endif
            @else
                {{-- ❌ No actions available (or price = 0) --}}
                @if ($egi->isMinted())
                    {{-- EGI già mintato - Link cliccabile per visualizzare dettagli mint --}}
                    <a href="{{ route('egi.mint-direct', $egi->id) }}"
                        class="flex w-full items-center justify-center rounded-b-lg rounded-t-none bg-green-50 px-4 py-2 text-sm font-medium text-green-700 transition-colors hover:bg-green-100">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('egi.status.view_mint_details') ?? 'Vedi Mint' }}
                    </a>
                @else
                    {{-- Stato non disponibile --}}
                    <div
                        class="flex w-full items-center justify-center rounded-b-lg rounded-t-none bg-gray-100 px-4 py-2 text-sm font-medium text-gray-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        @php $saleMode = $egi->sale_mode ?? 'fixed_price'; @endphp
                        @if ($saleMode === 'not_for_sale')
                            <span class="flex items-center gap-1">
                                <span class="inline-block h-4 w-4 text-red-500"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="2" fill="#6B6B6B" />
                                        <path stroke="#C13120" stroke-width="2" stroke-linecap="round"
                                            d="M8 16l8-8" />
                                    </svg></span>
                                {{ __('egi.status.not_for_sale') }}
                            </span>
                        @elseif (!$showButtons)
                            {{ __('egi.crud.price_not_set') }}
                        @elseif (!auth()->check())
                            {{ __('egi.status.login_required') ?? 'Login richiesto' }}
                        @else
                            {{ __('egi.status.not_available') ?? 'Non disponibile' }}
                        @endif
                    </div>
                @endif
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
