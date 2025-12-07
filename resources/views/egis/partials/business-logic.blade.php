{{-- resources/views/egis/partials/business-logic.blade.php --}}
{{-- 
    Business Logic per calcolo variabili EGI
    ORIGINE: righe 130-205 di show.blade.php
    VARIABILI CALCOLATE: $canUpdateEgi, $canDeleteEgi, $reservationService, $highestPriorityReservation, 
                        $displayPrice, $displayUser, $priceLabel, $isForSale, $canBeReserved, 
                        $canModifyPrice, $isPriceLocked
--}}

@php
    // CORREZIONE: Sostituito auth() con App\Helpers\FegiAuth::
    $currentUserId = App\Helpers\FegiAuth::id();
    $isAuthenticated = App\Helpers\FegiAuth::check();
    $currentUser = $isAuthenticated ? App\Helpers\FegiAuth::user() : null;
    $isCreator = $isAuthenticated && (int) $egi->user_id === (int) $currentUserId;
    $isOwner = $isAuthenticated && $egi->owner_id && (int) $egi->owner_id === (int) $currentUserId;
    $isMinted = !is_null($egi->token_EGI);

    // Get user's role in this collection and check Spatie permissions
$collectionMembership = $isAuthenticated ? $collection->users()->where('user_id', $currentUserId)->first() : null;
$collectionRole = $collectionMembership?->pivot?->role;

// Check if the collection role has the required Spatie permission
$roleHasUpdatePermission = false;
$roleHasDeletePermission = false;
if ($collectionRole) {
    $spatieRole = \Spatie\Permission\Models\Role::where('name', $collectionRole)->first();
    if ($spatieRole) {
        $roleHasUpdatePermission =
            $spatieRole->hasPermissionTo('manage_EGI') || $spatieRole->hasPermissionTo('update_EGI');
        $roleHasDeletePermission =
            $spatieRole->hasPermissionTo('manage_EGI') || $spatieRole->hasPermissionTo('delete_EGI');
    }
}

$canUpdateEgi =
    ($isAuthenticated && $currentUser && $currentUser->can('update_EGI') && $roleHasUpdatePermission) ||
    ($isOwner && $isMinted);

$canDeleteEgi =
    $isAuthenticated && $currentUser && $currentUser->can('delete_EGI') && $roleHasDeletePermission && !$isMinted; // Cannot delete minted EGIs

// Inizializzazione delle variabili di prenotazione e prezzo
// Ottengo la prenotazione con priorità più alta per questo EGI
$reservationService = app('App\Services\ReservationService');
$highestPriorityReservation = $reservationService->getHighestPriorityReservation($egi);

// Determino il prezzo da mostrare
$displayPrice = $egi->price; // Prezzo base di default
$displayUser = null;
$priceLabel = __('egi.current_price');

// Se c'è una prenotazione attiva, uso il suo prezzo e utente
    if ($highestPriorityReservation && $highestPriorityReservation->status === 'active') {
        // 🚀 DEBUG: Log per capire quale prenotazione viene selezionata
        \Log::info('EGI Show Debug', [
            'egi_id' => $egi->id,
            'reservation_id' => $highestPriorityReservation->id,
            'user_id' => $highestPriorityReservation->user_id,
            'offer_amount_fiat' => $highestPriorityReservation->offer_amount_fiat,
            'offer_amount_algo' => $highestPriorityReservation->offer_amount_algo,
            'is_current' => $highestPriorityReservation->is_current,
            'status' => $highestPriorityReservation->status,
            'created_at' => $highestPriorityReservation->created_at,
            'base_price' => $egi->price,
        ]);

        // 🔧 FIX: Proteggo da valori null o non numerici
        $fallbackPrice = $egi->price && is_numeric($egi->price) ? $egi->price * 0.3 : 0;
        $displayPrice = $highestPriorityReservation->offer_amount_fiat ?? $fallbackPrice;
        $displayUser = $highestPriorityReservation->user;

        // 🎯 EUR-ONLY SYSTEM: Sistema semplificato
        // - displayPrice = prezzo della prenotazione convertito in EUR
        // - Mostriamo sempre EUR con note per prenotazioni in altre valute

        // Convertiamo il prezzo della prenotazione in EUR se necessario
        if ($highestPriorityReservation->fiat_currency !== 'EUR') {
            // Per ora usiamo il prezzo EUR già convertito, in futuro potremo implementare conversione real-time
            $displayPrice = $highestPriorityReservation->amount_eur ?? $displayPrice;
        }

        // Label diversa per STRONG vs WEAK
        if ($highestPriorityReservation->type === 'weak') {
            $priceLabel = __('egi.reservation.fegi_reservation');
        } else {
            $priceLabel = __('egi.reservation.highest_bid');
        }
    } else {
        // Se NON c'è prenotazione, usa il prezzo base dell'EGI (sempre in EUR)
        // Sistema semplificato: tutto in EUR
    }

    // 🔧 VALIDATION: Assicuro che displayPrice sia sempre un numero valido
    $displayPrice = is_numeric($displayPrice) ? (float) $displayPrice : 0;

    $isForSale = $displayPrice && $displayPrice > 0 && !$egi->mint;
    $canBeReserved =
        !$egi->mint &&
        ($egi->is_published ||
            (App\Helpers\FegiAuth::check() && App\Helpers\FegiAuth::id() === $collection->creator_id)) &&
        $displayPrice &&
        $displayPrice > 0 &&
        !$isCreator;

    // 🔒 PRICE LOCK: Determina se il prezzo può essere modificato dal creator
    $canModifyPrice = ($isCreator || ($isOwner && $isMinted)) && !$highestPriorityReservation;
    $isPriceLocked = ($isCreator || ($isOwner && $isMinted)) && $highestPriorityReservation;
@endphp
