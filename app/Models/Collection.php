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

class Collection extends Model implements HasMedia {
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
        'epp_id',
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
    ];

    /**
     * Relazione con il creator.
     */
    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Relazione con l'owner.
     */
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relazione con gli EGI.
     */
    public function egis() {
        return $this->hasMany(Egi::class);
    }


    /**
     * Relazione con gli utenti tramite la tabella pivot collection_user.
     */
    public function users() {
        return $this->belongsToMany(User::class, 'collection_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Relazione con i wallet.
     */
    public function wallets() {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Verifica se la collection è pubblicata.
     *
     * @return bool
     */
    public function isPublished(): bool {
        return $this->status === 'published';
    }

    /**
     * Check if a user has a specific permission in this collection based on their role
     * 
     * @param User|int $user User model or user ID
     * @param string $permission Permission name to check
     * @return bool
     */
    public function userHasPermission($user, string $permission): bool {
        $userId = is_numeric($user) ? $user : $user->id;

        // Get user's role in this collection
        $collectionUser = $this->users()->where('users.id', $userId)->first();

        if (!$collectionUser) {
            return false; // User is not part of this collection
        }

        // Get the role from pivot
        $userRole = $collectionUser->pivot->role;

        // Check if the role exists in Spatie and has the permission
        $role = \Spatie\Permission\Models\Role::where('name', $userRole)->first();

        if (!$role) {
            return false; // Role doesn't exist in Spatie
        }

        return $role->hasPermissionTo($permission);
    }

    /**
     * Verifica se la collection può essere pubblicata.
     *
     * @return bool
     */
    public function canBePublished(): bool {
        $hasPendingWalletProposals = NotificationPayloadWallet::whereHas('walletModel', function ($query) {
            $query->where('collection_id', $this->id);
        })
            ->where('status', 'LIKE', '%pending%')
            ->exists();

        // Si può pubblicare se non esistono proposte wallet in pending
        return !$hasPendingWalletProposals;
    }

    public function epp() {
        return $this->belongsTo(Epp::class, 'epp_id');
    }

    /**
     * Definisce la relazione polimorfica: una Collection può avere molti Like.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes(): MorphMany {
        // Il secondo argomento 'likeable' deve corrispondere al nome usato
        // nel metodo morphs() nella migration della tabella likes.
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Definisce la relazione: una Collection ha molte Reservations ATTRAVERSO i suoi Egi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reservations(): HasManyThrough {
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
    public function paymentDistributions(): HasMany {
        return $this->hasMany(PaymentDistribution::class);
    }

    /**
     * Spatie Media: definizione della media collection per il banner (head)
     */
    public function registerMediaCollections(): void {
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
    public function registerMediaConversions(?Media $media = null): void {
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
    public function getEstimatedImpactAttribute(): float {
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
    public function scopeFeaturedForGuest($query, int $limit = 10) {
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
}
