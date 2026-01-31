<?php

namespace App\Models;

use App\Casts\EGIImageCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Collection extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes; // Gestione SoftDeletes
    use InteractsWithMedia;

    /**
     * Gli attributi assegnabili in massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'creator_id',
        'owner_id',
        'collection_name',
        'is_default',
        'description',
        'type',
        'status',
        'commercial_status', // P0 Commerce
        'delivery_policy',   // P0 Commerce
        'impact_mode',       // P0 Commerce
        'subscription_plan_id', // P0 Commerce
        'profile_type',    // NEW: 'contributor' or 'normal'
        'royalty_mode',    // NEW: 'standard' or 'subscriber'
        'is_published',
        'featured_in_guest',
        'featured_position',
        'image_banner',
        'image_card',
        'image_avatar',
        'image_egi',
        'url_collection_site',
        'position',
        'EGI_number',
        'EGI_asset_roles',
        'floor_price',
        'path_image_to_ipfs',
        'url_image_ipfs',
        'epp_project_id',
        'epp_donation_percentage', // Company voluntary EPP donation
        'is_epp_voluntary',        // Flag: EPP voluntary (company) vs mandatory (others)
        'EGI_asset_id',
    ];

    /**
     * Gli attributi che devono essere castati.
     *
     * @var array
     */
    protected $casts = [
        'image_banner' => EGIImageCast::class,
        'image_card'   => EGIImageCast::class,
        'image_avatar' => EGIImageCast::class,
        'image_EGI'    => EGIImageCast::class,
        'is_published' => 'boolean',
        'featured_in_guest' => 'boolean',
        'is_epp_voluntary' => 'boolean',
        'epp_donation_percentage' => 'decimal:2',
        'metadata' => 'array', // PA/Enterprise JSON metadata
        'commercial_status' => \App\Enums\Commerce\CommercialStatusEnum::class,
        'delivery_policy' => \App\Enums\Commerce\DeliveryPolicyEnum::class,
        'impact_mode' => \App\Enums\Commerce\ImpactModeEnum::class,
    ];

    /**
     * Relazione con il creator.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Relazione con l'owner.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relazione con gli EGI.
     */
    public function egis()
    {
        return $this->hasMany(Egi::class);
    }

    /**
     * Relazione con gli EGI originali (esclude i cloni).
     * Gli EGI clonati hanno parent_id != null
     */
    public function originalEgis()
    {
        return $this->hasMany(Egi::class)->whereNull('parent_id');
    }

    /**
     * Accessor: conta gli EGI originali (esclude i cloni).
     * Usato quando non c'è withCount() esplicito.
     * Priorità: original_egis_count (da withCount) > calcolo dinamico
     */
    public function getOriginalEgisCountAttribute(): int
    {
        // Se già calcolato da withCount, usa quello
        if (isset($this->attributes['original_egis_count'])) {
            return (int) $this->attributes['original_egis_count'];
        }
        // Altrimenti calcola dinamicamente
        return $this->originalEgis()->count();
    }

    /**
     * Relazione con gli utenti tramite la tabella pivot collection_user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'collection_user')
            ->withPivot('role', 'is_owner')
            ->withTimestamps();
    }

    /**
     * Relazione con i wallet.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Verifica se la collection è pubblicata.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if a user has a specific permission in this collection based on their role
     *
     * @param User|int $user User model or user ID
     * @param string $permission Permission name to check
     * @return bool
     */
    public function userHasPermission($user, string $permission): bool
    {
        $userId = is_numeric($user) ? (int) $user : ($user->id ?? null);
        if (!$userId) {
            return false;
        }

        // Usa la relazione pre-caricata se disponibile per evitare query aggiuntive
        $userRelation = null;
        if ($this->relationLoaded('users')) {
            $users = $this->getRelation('users');
            $userRelation = $users->firstWhere('id', $userId);
        }

        if (!$userRelation) {
            // Fallback: interroga la relazione
            $userRelation = $this->users()->where('users.id', $userId)->first();
        }

        if (!$userRelation) {
            return false; // L'utente non fa parte della collection
        }

        $userRole = $userRelation->pivot->role ?? null;
        if (!$userRole) {
            return false;
        }

        $role = \Spatie\Permission\Models\Role::where('name', $userRole)->first();
        if (!$role) {
            return false; // Ruolo inesistente
        }

        return $role->hasPermissionTo($permission);
    }

    /**
     * Verifica se la collection può essere pubblicata.
     *
     * @return bool
     */
    public function canBePublished(): bool
    {
        $hasPendingWalletProposals = NotificationPayloadWallet::whereHas('walletModel', function ($query) {
            $query->where('collection_id', $this->id);
        })
            ->where('status', 'LIKE', '%pending%')
            ->exists();

        // Si può pubblicare se non esistono proposte wallet in pending
        return !$hasPendingWalletProposals;
    }

    /**
     * Relazione con EppProject (progetto ambientale selezionato).
     * Una Collection supporta UN singolo EppProject.
     */
    public function eppProject()
    {
        return $this->belongsTo(EppProject::class, 'epp_project_id');
    }

    /**
     * Check if the collection creator is a company user
     *
     * @return bool
     */
    public function isCreatorCompany(): bool
    {
        if (!$this->relationLoaded('creator')) {
            $this->load('creator');
        }
        return $this->creator?->usertype === \App\Enums\User\MerchantUserTypeEnum::COMPANY->value;
    }

    /**
     * Check if EPP is voluntary for this collection (company users)
     *
     * @return bool
     */
    public function hasVoluntaryEpp(): bool
    {
        return $this->is_epp_voluntary === true;
    }

    /**
     * Check if collection has EPP donation configured
     *
     * @return bool
     */
    public function hasEppDonation(): bool
    {
        return $this->epp_project_id !== null && $this->epp_donation_percentage > 0;
    }

    /**
     * Get effective EPP percentage for payment distribution
     * For company: returns voluntary percentage (or 0 if none)
     * For others: returns standard EPP percentage from config
     *
     * @return float
     */
    public function getEffectiveEppPercentage(): float
    {
        // Logica basata sui NUOVI Profili Collection (Contributor vs Normal)
        $profile = $this->profile_type ?? 'contributor'; // Default per retrocompatibilità
        $mode = $this->royalty_mode ?? 'standard';

        // 1. Profilo NORMAL (Solo Company) -> EPP = Voluntary % (di solito 0)
        if ($profile === 'normal') {
            return (float) ($this->epp_donation_percentage ?? 0);
        }

        // 2. Profilo CONTRIBUTOR
        // Se modalità 'subscriber' -> EPP Esente (0%)
        if ($mode === 'subscriber') {
            return 0.0;
        }

        // Se modalità 'standard' -> EPP 20% Obbligatorio
        return 20.0;

        /* OLD LOGIC DEPRECATED
        if ($this->is_epp_voluntary) {
            // Company: use voluntary percentage or 0
            return (float) ($this->epp_donation_percentage ?? 0);
        }
        // Others: standard EPP percentage (configured globally)
        return $this->epp_project_id !== null ? (float) config('epp.default_percentage', 5.0) : 0;
        */
    }

    /**
     * DEPRECATED: Old EPP relationship - use eppProject() instead
     * @deprecated Use eppProject() relationship
     */
    public function epp()
    {
        return $this->belongsTo(Epp::class, 'epp_id');
    }

    /**
     * Relation: Collection has many likes (polymorphic).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes(): MorphMany
    {
        // Il secondo argomento 'likeable' deve corrispondere al nome usato
        // nel metodo morphs() nella migration della tabella likes.
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Check if this Collection is liked by the given user
     *
     * @param User|null $user
     * @return bool
     */
    public function isLikedBy(?User $user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        return $this->likes()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the likes count for this Collection
     *
     * @return int
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Get whether this Collection is liked by current user
     *
     * @return bool
     */
    public function getIsLikedAttribute(): bool
    {
        return $this->isLikedBy();
    }

    /**
     * Definisce la relazione: una Collection ha molte Reservations ATTRAVERSO i suoi Egi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reservations(): HasManyThrough
    {
        // Spiegazione parametri:
        // 1°: Modello finale che vogliamo ottenere (Reservation)
        // 2°: Modello intermedio attraverso cui passiamo (Egi)
        // 3°: Chiave esterna sul modello intermedio (Egi) che si riferisce a questo (Collection) -> 'collection_id'
        // 4°: Chiave esterna sul modello finale (Reservation) che si riferisce al modello intermedio (Egi) -> 'egi_id'
        // 5°: Chiave locale di questo modello (Collection) -> 'id' (usata per matchare il 3° parametro)
        // 6°: Chiave locale del modello intermedio (Egi) -> 'id' (usata per matchare il 4° parametro)
        return $this->hasManyThrough(
            Reservation::class,
            Egi::class,
            'collection_id', // Foreign key on the intermediate table (egis)
            'egi_id',        // Foreign key on the final table (reservations)
            'id',            // Local key on this table (collections)
            'id'             // Local key on the intermediate table (egis)
        );
    }

    /**
     * Payment distributions for this collection
     * @return HasMany
     */
    public function paymentDistributions(): HasMany
    {
        return $this->hasMany(PaymentDistribution::class);
    }

    /**
     * Get payment methods configured for this collection.
     * Uses polymorphic relationship to user_payment_methods table.
     *
     * @return MorphMany
     */
    public function paymentMethods(): MorphMany
    {
        return $this->morphMany(UserPaymentMethod::class, 'payable');
    }

    /**
     * Get effective payment methods for this collection.
     * Returns collection-specific methods if configured, otherwise inherits from creator.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEffectivePaymentMethods(): \Illuminate\Database\Eloquent\Collection
    {
        // Check if collection has its own payment methods configured
        $collectionMethods = $this->paymentMethods()->where('is_enabled', true)->get();

        if ($collectionMethods->isNotEmpty()) {
            return $collectionMethods;
        }

        // Fall back to creator's enabled payment methods
        return $this->creator?->enabledPaymentMethods()->get() ?? collect();
    }

    /**
     * Spatie Media: definizione della media collection per il banner (head)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('head')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
            ->singleFile();
    }

    /**
     * Spatie Media: conversioni per i tre formati richiesti
     * - banner: immagine wide per hero
     * - card: formato scheda
     * - thumb: miniatura quadrata
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Banner ampio per hero
        $this->addMediaConversion('banner')
            ->width(1920)
            ->height(600)
            ->optimize()
            ->nonQueued();

        // Formato scheda
        $this->addMediaConversion('card')
            ->width(800)
            ->height(600)
            ->optimize()
            ->nonQueued();

        // Miniatura
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->optimize()
            ->nonQueued();
    }

    /**
     * Calcola l'impatto stimato della Collection basato sulle prenotazioni più alte
     * per ciascun EGI (quota EPP del 20%)
     *
     * 🎯 MVP: Considera solo EPP id=2 e prenotazioni attive
     * 📊 Formula: Somma delle quote EPP (20%) delle prenotazioni più alte di ciascun EGI
     *
     * @return float L'impatto stimato totale in EUR
     */
    public function getEstimatedImpactAttribute(): float
    {
        // Solo prenotazioni attive per performance
        return $this->egis()
            ->whereHas('reservations', function ($query) {
                $query->where('is_current', true);
            })
            ->with(['reservations' => function ($query) {
                $query->where('is_current', true)
                    ->orderBy('offer_amount_fiat', 'desc')
                    ->orderBy('created_at', 'asc'); // Tiebreaker
            }])
            ->get()
            ->sum(function ($egi) {
                // Ottieni la prenotazione con l'offerta più alta per questo EGI
                $highestReservation = $egi->reservations->first();
                if (!$highestReservation) {
                    return 0;
                }

                // Calcola la quota EPP (20% del valore prenotato)
                return $highestReservation->offer_amount_fiat * 0.20;
            });
    }

    /**
     * Scope per ottenere le Collection in evidenza per il carousel guest
     * con logica di ordinamento basata su posizione forzata e impatto stimato
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit Numero massimo di risultati (default: 10)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeaturedForGuest($query, int $limit = 10)
    {
        return $query->where('is_published', true)
            ->where('featured_in_guest', true)
            ->with(['creator', 'egis.reservations' => function ($query) {
                $query->where('is_current', true)
                    ->orderBy('offer_amount_fiat', 'desc')
                    ->orderBy('created_at', 'asc');
            }])
            ->orderByRaw('CASE WHEN featured_position IS NOT NULL THEN featured_position ELSE 999 END ASC')
            ->take($limit);
    }

    /**
     * Accessor for Monetization Type
     * Determine if collection requires 'subscription' or 'epp' based on user type
     *
     * @return string|null 'subscription', 'epp', or null
     */
    public function getMonetizationTypeAttribute(): ?string
    {
        // If monetization_type is explicitly set in DB (future-proof), use it
        if (array_key_exists('monetization_type', $this->attributes) && $this->attributes['monetization_type']) {
            return $this->attributes['monetization_type'];
        }

        // Otherwise, derive from business rules (Transactional Truth)
        if ($this->isCreatorCompany()) {
            return 'subscription';
        }

        return 'epp';
    }

    /**
     * Accessor for Subscription Status
     * Queries the Ledger via Service to determine active status
     *
     * @return string 'active', 'inactive', 'expired'
     */
    public function getSubscriptionStatusAttribute(): string
    {
        // If subscription_status is explicitly set in DB (future-proof), use it
        if (array_key_exists('subscription_status', $this->attributes) && $this->attributes['subscription_status']) {
            return $this->attributes['subscription_status'];
        }

        // Derive from Transaction Ledger using Service
        try {
            /** @var \App\Services\CollectionSubscriptionService $service */
            $service = app(\App\Services\CollectionSubscriptionService::class);
            $statusData = $service->getSubscriptionStatus($this);

            if ($statusData['is_active']) {
                return 'active';
            }

            // If has subscription but expired
            if ($statusData['has_subscription'] && !$statusData['is_active']) {
                return 'expired';
            }

            return 'inactive';
        } catch (\Throwable $e) {
            // Service might not be available in all contexts (e.g. tests)
            return 'inactive';
        }
    }

    /**
     * Accessor for Subscription Tier
     *
     * @return string|null
     */
    public function getSubscriptionTierAttribute(): ?string
    {
        if (array_key_exists('subscription_tier', $this->attributes) && $this->attributes['subscription_tier']) {
            return $this->attributes['subscription_tier'];
        }

        try {
            /** @var \App\Services\CollectionSubscriptionService $service */
            $service = app(\App\Services\CollectionSubscriptionService::class);
            $statusData = $service->getSubscriptionStatus($this);

            return $statusData['transaction']?->subscription_tier ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Accessor for Subscription Expiration Date
     *
     * @return string|null
     */
    public function getSubscriptionExpiresAtAttribute(): ?string
    {
        if (array_key_exists('subscription_expires_at', $this->attributes) && $this->attributes['subscription_expires_at']) {
            return $this->attributes['subscription_expires_at'];
        }

        try {
            /** @var \App\Services\CollectionSubscriptionService $service */
            $service = app(\App\Services\CollectionSubscriptionService::class);
            $statusData = $service->getSubscriptionStatus($this);

            return $statusData['expires_at'] ? $statusData['expires_at']->toDateTimeString() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Accessor for Subscription Start Date
     *
     * @return string|null
     */
    public function getSubscriptionStartedAtAttribute(): ?string
    {
        if (array_key_exists('subscription_started_at', $this->attributes) && $this->attributes['subscription_started_at']) {
            return $this->attributes['subscription_started_at'];
        }

        try {
            /** @var \App\Services\CollectionSubscriptionService $service */
            $service = app(\App\Services\CollectionSubscriptionService::class);
            $statusData = $service->getSubscriptionStatus($this);

            return $statusData['transaction']?->created_at?->toDateTimeString() ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Accessor for Subscription Stripe ID (Payment Method)
     *
     * @return string|null
     */
    public function getSubscriptionStripeIdAttribute(): ?string
    {
        try {
            /** @var \App\Services\CollectionSubscriptionService $service */
            $service = app(\App\Services\CollectionSubscriptionService::class);
            $statusData = $service->getSubscriptionStatus($this);

            // Assuming payment_transaction_id might store this, or we verify payment_method
            if ($statusData['transaction'] && $statusData['transaction']->payment_method === 'stripe') {
                return $statusData['transaction']->payment_transaction_id;
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
    /**
     * Accessor for Auto-Renew Status
     *
     * @return bool
     */
    public function getIsAutoRenewActiveAttribute(): bool
    {
        try {
            /** @var \App\Services\RecurringPaymentService $service */
            $service = app(\App\Services\RecurringPaymentService::class);
            return $service->hasActiveSubscription(
                auth()->user() ?? $this->creator, // Use auth user or creator as fallback
                $this,
                'collection_subscription'
            );
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check if the collection is enabled for commerce.
     * Robust check handling Enum casting or raw values.
     *
     * @return bool
     */
    public function isCommercialEnabled(): bool
    {
        if ($this->commercial_status instanceof \App\Enums\Commerce\CommercialStatusEnum) {
            return $this->commercial_status === \App\Enums\Commerce\CommercialStatusEnum::COMMERCIAL_ENABLED;
        }

        return $this->commercial_status === 'commercial_enabled' 
            || $this->commercial_status === \App\Enums\Commerce\CommercialStatusEnum::COMMERCIAL_ENABLED->value;
    }
}
