<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa SoftDeletes

/**
 * 📜 Oracode Eloquent Model: Egi
 * Represents an Ecological Goods Invent (EGI) record in the database.
 * Each EGI corresponds to a unique digital asset with associated metadata,
 * file information, ownership, and relationship to a Collection.
 *
 * @package     App\Models
 * @version     1.0.0
 * @author      Fabio Cherici & Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     Proprietary // Or your application's license
 *
 * @purpose     Provides an interface to the 'egi' database table, defining fillable attributes,
 *              data type casting, relationships with other models (User, Collection, EgiAudit),
 *              and enabling soft deletion functionality. Essential for creating, updating,
 *              and querying EGI data within the FlorenceEGI application.
 *
 * @context     Used by controllers, handlers (like EgiUploadHandler), services, and potentially
 *              views/Livewire components throughout the application to interact with EGI data.
 *
 * @state       Represents the state of a single row in the 'egi' database table.
 *
 * @property int $id Primary key.
 * @property int $collection_id Foreign key to the 'collections' table.
 * @property int|null $key_file Typically stores the EGI ID itself for image path construction (MVP Q1). Nullable. Indexed.
 * @property string|null $token_EGI Blockchain token identifier (Post-MVP). Nullable.
 * @property array|null $jsonMetadata JSON field for additional metadata (Post-MVP). Nullable. Cast to array.
 * @property int|null $user_id Foreign key to the 'users' table (who uploaded/created). Nullable. Indexed.
 * @property int|null $auction_id Foreign key for auction relationship (Future). Nullable. Indexed.
 * @property int|null $owner_id Foreign key to the 'users'table (current owner). Nullable. Indexed.
 * @property int|null $drop_id Foreign key for drop relationship (Future). Nullable. Indexed.
 * @property string|null $upload_id Identifier for the batch upload process. Nullable.
 * @property string|null $creator Wallet address or identifier of the original creator. Nullable.
 * @property string|null $owner_wallet Wallet address of the current owner. Nullable.
 * @property string|null $drop_title Title of the drop event (Future). Nullable.
 * @property string|null $title The title of the EGI (max 60 chars). Nullable. Indexed.
 * @property string|null $description Textual description of the EGI. Nullable.
 * @property string|null $extension File extension (e.g., 'jpg', 'png'). Nullable.
 * @property bool $media Indicates if it's a non-image media type. Default false (MVP Q1). Nullable. Cast to boolean.
 * @property string|null $type File category (e.g., 'image', 'audio'). Nullable.
 * @property int|null $bind Field potentially related to pairing (Future/Legacy). Nullable. Cast to integer.
 * @property int|null $paired Field potentially related to pairing (Future/Legacy). Nullable. Cast to integer.
 * @property float|null $price Current listing price. Nullable. Cast to decimal:2.
 * @property float|null $floorDropPrice Floor price set during a drop event. Nullable. Cast to decimal:2.
 * @property int|null $position Display order within the collection. Nullable. Cast to integer.
 * @property \Illuminate\Support\Carbon|null $creation_date Optional artistic creation date. Nullable. Cast to date.
 * @property string|null $size Formatted file size (e.g., "1.23 MB"). Nullable.
 * @property string|null $dimension Formatted image dimensions (e.g., "w:1920 x h:1080"). Nullable.
 * @property bool $is_published Indicates if the EGI is publicly visible. Default false. Nullable. Cast to boolean.
 * @property bool $mint Indicates minting status (Post-MVP). Default false. Nullable. Cast to boolean.
 * @property bool $rebind Indicates rebind status (Post-MVP). Default true. Nullable. Cast to boolean.
 * @property string|null $file_crypt Encrypted original filename. Nullable.
 * @property string|null $file_hash MD5 or SHA hash of the file content. Nullable.
 * @property string|null $file_IPFS IPFS hash/path (Post-MVP). Nullable.
 * @property string|null $file_mime File MIME type (e.g., 'image/jpeg'). Nullable.
 * @property string $status Current status ('draft', 'published', 'archived', etc.). Default 'draft'.
 * @property bool $is_public Alias or alternative visibility flag (confirm usage). Default true. Cast to boolean.
 * @property int|null $updated_by Foreign key to 'users' table (who last updated). Nullable.
 * @property \Illuminate\Support\Carbon|null $created_at Timestamp of creation.
 * @property \Illuminate\Support\Carbon|null $updated_at Timestamp of last update.
 * @property \Illuminate\Support\Carbon|null $deleted_at Timestamp for soft delete.
 *
 * @property-read Collection $collection The collection this EGI belongs to.
 * @property-read User|null $user The user who uploaded/created this EGI record.
 * @property-read User|null $owner The current owner of this EGI.
 * @property-read \Illuminate\Database\Eloquent\Collection|EgiAudit[] $audits Audit trail for this EGI.
 *
 * @method static \Database\Factories\EgiFactory factory($count = null, $state = [])
 */
class Egi extends Model {
    use HasFactory;
    use SoftDeletes; // Enable soft deletes

    /**
     * The table associated with the model.
     * Explicitly defined for clarity.
     *
     * @var string
     */
    protected $table = 'egis';

    /**
     * The attributes that are mass assignable.
     * These fields can be set using `Egi::create([...])` or `$egi->fill([...])`.
     * Includes all fields likely to be set during creation or update via forms/handlers.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_id',
        'key_file',
        'token_EGI',
        'jsonMetadata',
        'user_id', // User performing the action (e.g., upload)
        'auction_id',
        'owner_id', // Current owner
        'drop_id',
        'upload_id',
        'creator', // Original creator identifier/wallet
        'owner_wallet', // Current owner wallet
        'drop_title',
        'title',
        'description',
        'extension',
        'media',
        'type',
        'bind',
        'paired',
        'price',
        'floorDropPrice',
        'position',
        'creation_date',
        'size',
        'dimension',
        'is_published',
        'mint',
        'rebind',
        'file_crypt',
        'file_hash',
        'file_IPFS',
        'file_mime',
        'status',
        'hyper',
        'is_public',
        'updated_by',
        // Note: 'id', 'created_at', 'updated_at', 'deleted_at' are typically not fillable
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures data integrity and correct handling (e.g., booleans, dates, numbers).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jsonMetadata'   => 'array',        // Cast JSON string to PHP array
        'media'          => 'boolean',      // Cast 'media' to boolean
        'bind'           => 'integer',      // Cast 'bind' to integer (as per migration)
        'paired'         => 'integer',      // Cast 'paired' to integer (as per migration)
        'price'          => 'decimal:2',    // Cast 'price' to float/decimal with 2 places
        'floorDropPrice' => 'decimal:2',    // Cast 'floorDropPrice' to float/decimal with 2 places
        'position'       => 'integer',      // Cast 'position' to integer
        'creation_date'  => 'date',         // Cast 'creation_date' to Carbon date object (YYYY-MM-DD)
        'is_published'   => 'boolean',      // Cast 'is_published' to boolean
        'mint'           => 'boolean',      // Cast 'mint' to boolean
        'rebind'         => 'boolean',      // Cast 'rebind' to boolean
        'hyper'          => 'boolean',      // Cast 'hyper' to boolean
        'is_public'      => 'boolean',      // Cast 'is_public' to boolean
        'created_at'     => 'datetime',     // Standard timestamp casting
        'updated_at'     => 'datetime',     // Standard timestamp casting
        'deleted_at'     => 'datetime',     // Required for SoftDeletes
    ];

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the traits for this EGI
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function traits(): HasMany {
        return $this->hasMany(EgiTrait::class, 'egi_id')->orderBy('sort_order');
    }

    /**
     * Get traits grouped by category
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTraitsByCategoryAttribute() {
        return $this->traits->groupBy('category_id');
    }

    /**
     * Check if EGI has rare traits
     *
     * @return bool
     */
    public function hasRareTraits(): bool {
        return $this->traits->contains(function ($trait) {
            return $trait->isRare();
        });
    }

    /**
     * 🔗 Defines the relationship: An EGI belongs to one Collection.
     *
     * @return BelongsTo
     */
    public function collection(): BelongsTo {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI record was created/uploaded by one User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        // Assuming 'user_id' is the foreign key linking to the user who uploaded/created the record
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI is currently owned by one User.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo {
        // Assuming 'owner_id' is the foreign key linking to the current owner user
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * 🔗 Defines the relationship: An EGI can have many Audit records.
     * (Based on the 'egi_audits' migration)
     *
     * @return HasMany
     */
    public function audits(): HasMany {
        return $this->hasMany(EgiAudit::class, 'egi_id');
    }

    //--------------------------------------------------------------------------
    // CoA (Certificate of Authenticity) Relationships
    //--------------------------------------------------------------------------

    /**
     * 🔗 CoA: An EGI can have multiple Certificates of Authenticity
     *
     * @return HasMany
     */
    public function coas(): HasMany {
        return $this->hasMany(Coa::class, 'egi_id')->orderBy('issued_at', 'desc');
    }

    /**
     * 🔗 CoA: Get the active (valid) CoA for this EGI
     *
     * @return HasOne
     */
    public function activeCoa(): HasOne {
        return $this->hasOne(Coa::class, 'egi_id')->where('status', 'valid')->latest('issued_at');
    }

    /**
     * 🔗 CoA: Get current/active CoA (alias for convenience)
     *
     * @return HasOne
     */
    public function coa(): HasOne {
        return $this->activeCoa();
    }

    /**
     * 🔗 CoA: Get traits version history for this EGI
     *
     * @return HasMany
     */
    public function traitsVersions(): HasMany {
        return $this->hasMany(EgiTraitsVersion::class, 'egi_id')->orderBy('version', 'desc');
    }

    /**
     * 🔗 CoA: Get vocabulary traits for Certificate of Authenticity
     *
     * @return HasOne
     */
    public function coaTraits(): HasOne {
        return $this->hasOne(EgiCoaTrait::class, 'egi_id');
    }

    //--------------------------------------------------------------------------
    // Likes & Social Relationships
    //--------------------------------------------------------------------------

    /**
     * @Oracode Polymorphic relationship for likes
     * 🎯 Purpose: Enable users to like EGIs
     * 🧱 Core Logic: Polymorphic many-to-many via likes table
     *
     * @return MorphMany
     */
    public function likes(): MorphMany {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Check if this EGI is liked by the given user
     *
     * @param User|null $user
     * @return bool
     */
    public function isLikedBy(?User $user = null): bool {
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
     * Get the likes count for this EGI
     *
     * @return int
     */
    public function getLikesCountAttribute(): int {
        return $this->likes()->count();
    }

    /**
     * Get whether this EGI is liked by current user
     *
     * @return bool
     */
    public function getIsLikedAttribute(): bool {
        return $this->isLikedBy();
    }

    /**
     * Get the reservations associated with the EGI.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'egi_id', 'id');
    }

    /**
     * Relazione con i certificati di prenotazione
     * Ordinamento: strong prima di weak, poi per offer_amount_fiat decrescente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservationCertificates() {
        return $this->hasMany(EgiReservationCertificate::class, 'egi_id')
            ->orderByRaw("CASE
                        WHEN reservation_type = 'strong' THEN 0
                        WHEN reservation_type = 'weak' THEN 1
                        ELSE 2
                    END")
            ->orderBy('offer_amount_fiat', 'desc')
            ->orderBy('created_at', 'desc'); // Tie-breaker per stesso prezzo
    }

    // ---------------------------------------------------------------------
    // Categoria (Trait con category.slug = 'category') Accessors
    // ---------------------------------------------------------------------
    /**
     * Restituisce il trait di categoria primario (se presente).
     * Per definizione di business ce n'è al massimo uno; se più di uno, prende il primo.
     */
    public function getCategoryTraitAttribute() {
        // Garantiamo che category e traitType siano caricati (permette fallback se slug category mancante)
        if (!$this->relationLoaded('traits')) {
            $this->load(['traits.category', 'traits.traitType']);
        } else {
            $this->traits->loadMissing(['category', 'traitType']);
        }

        $needles = ['category', 'categories'];

        return $this->traits->first(function ($trait) use ($needles) {
            $catSlug  = strtolower(trim($trait->category->slug ?? ''));
            $typeSlug = strtolower(trim($trait->traitType->slug ?? ''));
            $typeName = strtolower(trim($trait->traitType->name ?? ''));

            return in_array($catSlug, $needles, true)
                || in_array($typeSlug, $needles, true)
                || in_array($typeName, $needles, true);
        });
    }

    /**
     * Nome categoria (display_value > value > default config)
     */
    public function getCategoryNameAttribute(): string {
        $trait = $this->category_trait;
        $raw = $trait ? ($trait->display_value ?: $trait->value) : null;
        // Restituiamo sempre la versione LOCALIZZATA da trait_elements.values
        [$_canonical, $localized] = $this->resolveCategoryCanonicalAndLocalized($raw);
        return $localized;
    }

    /**
     * Classi CSS Tailwind per il badge della categoria.
     */
    public function getCategoryBadgeClassesAttribute(): string {
        $trait = $this->category_trait;
        $raw = $trait ? ($trait->display_value ?: $trait->value) : null;
        [$canonical, $_localized] = $this->resolveCategoryCanonicalAndLocalized($raw);
        $map = config('egi_category_badges.map', []);
        $default = config('egi_category_badges.default', 'Art');
        return $map[$canonical]['classes'] ?? ($map[$default]['classes'] ?? 'bg-gray-600 text-white');
    }

    /**
     * Debug helper: restituisce array grezzo delle informazioni categoria per troubleshooting.
     * NON usare in produzione (solo log / tinker).
     */
    public function getCategoryDebugAttribute(): array {
        $trait = $this->category_trait;
        if (!$trait) {
            return [
                'found' => false,
                'reason' => 'no_trait',
            ];
        }
        $raw = $trait->display_value ?: $trait->value;
        [$canonical, $localized] = $this->resolveCategoryCanonicalAndLocalized($raw);
        return [
            'found' => true,
            'trait_id' => $trait->id,
            'raw_value' => $raw,
            'display_value' => $trait->display_value,
            'stored_value' => $trait->value,
            'category_slug' => $trait->category->slug ?? null,
            'trait_type_slug' => $trait->traitType->slug ?? null,
            'trait_type_name' => $trait->traitType->name ?? null,
            'canonical' => $canonical,
            'localized' => $localized,
        ];
    }

    /**
     * Risolve il nome canonico (EN) e quello localizzato (IT) partendo da un valore raw che può
     * essere già inglese oppure già tradotto (es. "Nature" | "Natura").
     * Il mapping di stile (palette) resta ancorato alla chiave inglese in config/egi_category_badges.php
     * mentre in UI mostriamo sempre il valore localizzato.
     *
     * @param string|null $raw Valore grezzo del trait (display_value o value)
     * @return array [canonicalEnglish, localizedLabel]
     */
    private function resolveCategoryCanonicalAndLocalized(?string $raw): array {
        $defaultCanonical = config('egi_category_badges.default', 'Art');
        $translations = trans('trait_elements.values'); // english => italian
        if (!is_array($translations)) {
            $translations = [];
        }

        $canonical = $defaultCanonical;
        $localized = $translations[$defaultCanonical] ?? $defaultCanonical; // fallback se mancasse la traduzione

        if ($raw !== null) {
            $candidate = trim($raw);
            if ($candidate !== '') {
                // Caso 1: già in inglese (chiave presente)
                if (array_key_exists($candidate, $translations)) {
                    $canonical = $candidate;
                    $localized = $translations[$candidate] ?? $candidate;
                } else {
                    // Caso 2: è una traduzione italiana -> cerchiamo la chiave inglese
                    $found = array_search($candidate, $translations, true);
                    if ($found !== false) {
                        $canonical = $found;
                        $localized = $candidate; // già localizzato
                    } else {
                        // Caso 3: valore sconosciuto -> mostriamo così com'è ma usiamo default per stile
                        $localized = $candidate;
                    }
                }
            }
        }

        return [$canonical, $localized];
    }

    /**
     * Debug helper (non usato in produzione direttamente): restituisce contesto categoria grezzo.
     * Utile per capire perché cade nel fallback.
     */
    public function getCategoryDebugContextAttribute(): array {
        $rawTrait = $this->category_trait; // Tratto filtrato
        $all = $this->relationLoaded('traits') ? $this->traits->map(function ($t) {
            return [
                'id' => $t->id,
                'value' => $t->value,
                'display_value' => $t->display_value,
                'category_slug' => $t->category?->slug,
                'category_id' => $t->category_id,
            ];
        }) : [];
        [$canonical, $localized] = $this->resolveCategoryCanonicalAndLocalized($rawTrait?->display_value ?: $rawTrait?->value);
        return [
            'raw_trait_found' => (bool)$rawTrait,
            'raw_value' => $rawTrait?->value,
            'raw_display_value' => $rawTrait?->display_value,
            'raw_category_slug' => $rawTrait?->category?->slug,
            'canonical' => $canonical,
            'localized' => $localized,
            'all_traits_sample' => $all,
        ];
    }


    /**
     * Relationship with utility
     * One-to-one relationship: each EGI can have one utility
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function utility() {
        return $this->hasOne(Utility::class);
    }

    /**
     * Get main image URL with optimization support
     * Uses 'card' variant for optimal card display (400x400 WebP)
     * Falls back to original if optimization not available
     *
     * @return string|null
     */
    public function getMainImageUrlAttribute(): ?string {
        if (!$this->collection_id || !$this->user_id || !$this->key_file || !$this->extension) {
            return null;
        }

        // Try to get optimized 'card' variant first
        $storageBasePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $this->collection_id,
            $this->user_id
        );

        $optimizedUrl = \App\Services\ImageVariantHelper::getVariantUrlWithFallback(
            $storageBasePath,
            $this->key_file,
            'card', // 400x400 optimized variant
            'public'
        );

        if ($optimizedUrl) {
            return $optimizedUrl;
        }

        // Fallback to original
        $path = sprintf(
            'storage/users_files/collections_%d/creator_%d/%d.%s',
            $this->collection_id,
            $this->user_id,
            $this->key_file,
            $this->extension
        );

        return asset($path);
    }

    /**
     * Get thumbnail image URL for smaller displays
     * Uses 'thumbnail' variant (200x200 WebP)
     *
     * @return string|null
     */
    public function getThumbnailImageUrlAttribute(): ?string {
        if (!$this->collection_id || !$this->user_id || !$this->key_file) {
            return null;
        }

        $storageBasePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $this->collection_id,
            $this->user_id
        );

        return \App\Services\ImageVariantHelper::getVariantUrlWithFallback(
            $storageBasePath,
            $this->key_file,
            'thumbnail', // 200x200 optimized variant
            'public'
        );
    }

    /**
     * Get avatar image URL for very small displays
     * Uses 'avatar' variant (80x80 WebP)
     *
     * @return string|null
     */
    public function getAvatarImageUrlAttribute(): ?string {
        if (!$this->collection_id || !$this->user_id || !$this->key_file) {
            return null;
        }

        $storageBasePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $this->collection_id,
            $this->user_id
        );

        return \App\Services\ImageVariantHelper::getVariantUrlWithFallback(
            $storageBasePath,
            $this->key_file,
            'avatar', // 80x80 optimized variant
            'public'
        );
    }

    /**
     * Get original image URL for full-size display (e.g., zoom view)
     * Uses 'original' optimized variant or falls back to original file
     *
     * @return string|null
     */
    public function getOriginalImageUrlAttribute(): ?string {
        if (!$this->collection_id || !$this->user_id || !$this->key_file) {
            return null;
        }

        $storageBasePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $this->collection_id,
            $this->user_id
        );

        return \App\Services\ImageVariantHelper::getVariantUrlWithFallback(
            $storageBasePath,
            $this->key_file,
            'original', // Optimized original
            'public'
        );
    }

    //--------------------------------------------------------------------------
    // CoA Helper Methods
    //--------------------------------------------------------------------------

    /**
     * Check if this EGI has any valid CoA
     */
    public function hasValidCoa(): bool {
        return $this->coas()->where('status', 'valid')->exists();
    }

    /**
     * Get the latest valid CoA
     */
    public function getLatestValidCoa(): ?Coa {
        return $this->activeCoa;
    }

    /**
     * Check if this EGI can have a new CoA issued
     * (business rule: only one valid CoA at a time)
     */
    public function canIssueNewCoa(): bool {
        return !$this->hasValidCoa();
    }

    /**
     * Get CoA count for this EGI
     */
    public function getCoaCount(): int {
        return $this->coas()->count();
    }

    /**
     * Get valid CoA count for this EGI
     */
    public function getValidCoaCount(): int {
        return $this->coas()->where('status', 'valid')->count();
    }

    // Add other relationships as needed (e.g., with Auction, Drop models later)

}
