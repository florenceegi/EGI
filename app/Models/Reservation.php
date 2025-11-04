<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI - Pre-Launch Queue System)
 * @date 2025-08-15
 * @purpose Reservation model for pre-launch ranking system with public queue
 *
 * Pre-Launch System:
 * - No immediate payments, only reservation amounts
 * - Public ranking visible to all users
 * - Multiple active reservations per EGI
 * - EUR as canonical currency
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $egi_id
 * @property string $type
 * @property string $status
 * @property string $sub_status
 * @property float $amount_eur
 * @property int|null $rank_position
 * @property int|null $previous_rank
 * @property bool $is_highest
 * @property bool $is_current
 * @property string $display_currency
 * @property float|null $display_amount
 * @property float|null $display_exchange_rate
 * @property string $input_currency
 * @property float $input_amount
 * @property float|null $input_exchange_rate
 * @property Carbon|null $input_timestamp
 * @property int|null $superseded_by_id
 * @property Carbon|null $superseded_at
 * @property Carbon|null $mint_window_starts_at
 * @property Carbon|null $mint_window_ends_at
 * @property bool $mint_confirmed
 * @property Carbon|null $mint_confirmed_at
 * @property array|null $metadata
 * @property string|null $user_note
 * @property string|null $admin_note
 * @property Carbon|null $last_notification_at
 * @property array|null $notification_history
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User|null $user
 * @property-read Egi $egi
 * @property-read Reservation|null $supersededBy
 * @property-read Reservation[] $supersededReservations
 */
class Reservation extends Model {
    use HasFactory;

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_WITHDRAWN = 'withdrawn';

    /**
     * Sub-status constants
     */
    const SUB_STATUS_PENDING = 'pending';
    const SUB_STATUS_HIGHEST = 'highest';
    const SUB_STATUS_SUPERSEDED = 'superseded';
    const SUB_STATUS_CONFIRMED = 'confirmed';
    const SUB_STATUS_MINTED = 'minted';
    const SUB_STATUS_WITHDRAWN = 'withdrawn';
    const SUB_STATUS_EXPIRED = 'expired';

    /**
     * Type constants
     */
    const TYPE_WEAK = 'weak';
    const TYPE_STRONG = 'strong';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'egi_id',
        'type',
        'status',
        'sub_status',
        // Amounts
        'amount_eur',
        'display_currency',
        'display_amount',
        'display_exchange_rate',
        'input_currency',
        'input_amount',
        'input_exchange_rate',
        'input_timestamp',
        // Ranking
        'rank_position',
        'previous_rank',
        'is_highest',
        'is_current',
        // Supersession
        'superseded_by_id',
        'superseded_at',
        // Future mint fields
        'mint_window_starts_at',
        'mint_window_ends_at',
        'mint_confirmed',
        'mint_confirmed_at',
        // Future payment fields
        'payment_method',
        'payment_amount_eur',
        'payment_currency',
        'payment_amount',
        'payment_exchange_rate',
        'payment_executed_at',
        // Algorand fields
        'algo_amount_micro',
        'algo_tx_id',
        'asa_id',
        // Metadata
        'metadata',
        'user_note',
        'admin_note',
        'last_notification_at',
        'notification_history',
        // Legacy fields
        'original_currency',
        'original_price',
        'algo_price',
        'exchange_rate',
        'rate_timestamp',
        'fiat_currency',
        'offer_amount_fiat',
        'offer_amount_algo',
        'exchange_timestamp',
        'expires_at',
        'contact_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount_eur' => 'decimal:2',
        'display_amount' => 'decimal:2',
        'display_exchange_rate' => 'decimal:10',
        'input_amount' => 'decimal:2',
        'input_exchange_rate' => 'decimal:10',
        'input_timestamp' => 'datetime',
        'superseded_at' => 'datetime',
        'mint_window_starts_at' => 'datetime',
        'mint_window_ends_at' => 'datetime',
        'mint_confirmed' => 'boolean',
        'mint_confirmed_at' => 'datetime',
        'payment_amount_eur' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'payment_exchange_rate' => 'decimal:10',
        'payment_executed_at' => 'datetime',
        'algo_amount_micro' => 'integer',
        'metadata' => 'json',
        'notification_history' => 'json',
        'last_notification_at' => 'datetime',
        'is_highest' => 'boolean',
        'is_current' => 'boolean',
        // Legacy casts
        'original_price' => 'decimal:8',
        'algo_price' => 'integer',
        'exchange_rate' => 'decimal:8',
        'rate_timestamp' => 'datetime',
        'offer_amount_fiat' => 'decimal:2',
        'offer_amount_algo' => 'decimal:8',
        'exchange_timestamp' => 'datetime',
        'expires_at' => 'datetime',
        'contact_data' => 'json',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($reservation) {
            // Set defaults
            $reservation->input_timestamp = $reservation->input_timestamp ?? now();
            $reservation->is_current = true;

            // Set input tracking if not set
            if (empty($reservation->input_currency)) {
                $reservation->input_currency = $reservation->display_currency ?? 'EUR';
            }
            if (empty($reservation->input_amount)) {
                $reservation->input_amount = $reservation->amount_eur;
            }
        });

        static::created(function ($reservation) {
            // Update rankings after new reservation
            $reservation->updateEgiRankings();
        });

        static::updated(function ($reservation) {
            // Update rankings if amount changed
            if ($reservation->isDirty('amount_eur')) {
                $reservation->updateEgiRankings();
            }
        });
    }

    // ===== RELATIONSHIPS =====

    /**
     * User who made the reservation
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * EGI being reserved
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Reservation that superseded this one
     */
    public function supersededBy(): BelongsTo {
        return $this->belongsTo(Reservation::class, 'superseded_by_id');
    }

    /**
     * Reservations superseded by this one
     */
    public function supersededReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'superseded_by_id');
    }

    /**
     * Certificate (future implementation)
     */
    public function certificate(): HasOne {
        return $this->hasOne(EgiReservationCertificate::class);
    }

    /**
     * Payment distributions for this reservation
     * @return HasMany
     */
    public function paymentDistributions(): HasMany {
        return $this->hasMany(PaymentDistribution::class);
    }

    // ===== RANKING METHODS =====

    /**
     * Update rankings for all reservations of this EGI
     */
    public function updateEgiRankings(): void {
        DB::transaction(function () {
            // Get all active reservations for this EGI ordered by amount
            $reservations = static::where('egi_id', $this->egi_id)
                ->where('status', self::STATUS_ACTIVE)
                ->where('is_current', true)
                ->orderByDesc('amount_eur')
                ->orderBy('created_at') // Earlier reservation wins ties
                ->get();

            $previousHighest = null;

            foreach ($reservations as $index => $reservation) {
                $newRank = $index + 1;
                $wasHighest = $reservation->is_highest;
                $isNowHighest = ($newRank === 1);

                // Store previous rank before updating
                if ($reservation->rank_position !== $newRank) {
                    $reservation->previous_rank = $reservation->rank_position;
                }

                // Update rank and highest flag
                $reservation->rank_position = $newRank;
                $reservation->is_highest = $isNowHighest;

                // Update sub_status based on position
                if ($isNowHighest) {
                    $reservation->sub_status = self::SUB_STATUS_HIGHEST;

                    // Mark the previous highest as superseded
                    if ($previousHighest && $previousHighest->id !== $reservation->id) {
                        $previousHighest->sub_status = self::SUB_STATUS_SUPERSEDED;
                        $previousHighest->superseded_by_id = $reservation->id;
                        $previousHighest->superseded_at = now();
                        $previousHighest->save();
                    }

                    $previousHighest = $reservation;
                } elseif ($reservation->sub_status === self::SUB_STATUS_HIGHEST) {
                    // Was highest but no longer
                    $reservation->sub_status = self::SUB_STATUS_SUPERSEDED;
                }

                $reservation->save();
            }
        });
    }

    /**
     * Get rank change indicator
     */
    public function getRankChange(): string {
        if (!$this->previous_rank) {
            return 'new';
        }

        if ($this->rank_position < $this->previous_rank) {
            return 'up';
        } elseif ($this->rank_position > $this->previous_rank) {
            return 'down';
        }

        return 'same';
    }

    /**
     * Get competitors (other reservations for same EGI)
     */
    public function getCompetitors(): Builder {
        return static::where('egi_id', $this->egi_id)
            ->where('status', self::STATUS_ACTIVE)
            ->where('is_current', true)
            ->where('id', '!=', $this->id)
            ->orderByDesc('amount_eur');
    }

    /**
     * Check if user can supersede this reservation
     */
    public function canBeSupersededBy(float $newAmount): bool {
        // In pre-launch, any amount can be placed
        // No minimum increment required
        return true;
    }

    // ===== STATUS METHODS =====

    /**
     * Check if reservation is active
     */
    public function isActive(): bool {
        return $this->status === self::STATUS_ACTIVE && $this->is_current;
    }

    /**
     * Check if reservation is the highest for its EGI
     */
    public function isHighest(): bool {
        return $this->is_highest && $this->isActive();
    }

    /**
     * Check if reservation has been superseded
     */
    public function isSuperseded(): bool {
        return $this->sub_status === self::SUB_STATUS_SUPERSEDED ||
            $this->superseded_by_id !== null;
    }

    /**
     * Withdraw reservation
     */
    public function withdraw(): bool {
        $this->status = self::STATUS_WITHDRAWN;
        $this->sub_status = self::SUB_STATUS_WITHDRAWN;
        $this->is_current = false;
        $this->is_highest = false;

        $saved = $this->save();

        if ($saved) {
            // Recalculate rankings for remaining reservations
            $this->updateEgiRankings();
        }

        return $saved;
    }

    // ===== DISPLAY METHODS =====

    /**
     * Get formatted amount in EUR
     */
    public function getFormattedAmountEur(): string {
        return '€' . number_format($this->amount_eur, 2);
    }

    /**
     * Get formatted display amount
     */
    public function getFormattedDisplayAmount(): string {
        $symbol = $this->getCurrencySymbol($this->display_currency);
        return $symbol . number_format($this->display_amount ?? $this->amount_eur, 2);
    }

    /**
     * Get currency symbol
     */
    protected function getCurrencySymbol(string $currency): string {
        return match ($currency) {
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            default => $currency . ' '
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string {
        if ($this->isHighest()) {
            return 'green';
        }

        if ($this->isSuperseded()) {
            return 'yellow';
        }

        if ($this->status === self::STATUS_WITHDRAWN) {
            return 'gray';
        }

        return 'blue';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string {
        if ($this->isHighest()) {
            return 'Offerta più alta';
        }

        if ($this->isSuperseded()) {
            return 'Superato';
        }

        if ($this->status === self::STATUS_WITHDRAWN) {
            return 'Ritirato';
        }

        if ($this->rank_position) {
            return 'Posizione #' . $this->rank_position;
        }

        return 'Attivo';
    }

    /**
     * Get UI display data
     */
    public function getDisplayData(): array {
        return [
            'id' => $this->id,
            'egi_id' => $this->egi_id,
            'egi_title' => $this->egi->title ?? 'EGI #' . $this->egi_id,
            'amount_eur' => $this->amount_eur,
            'formatted_amount' => $this->getFormattedAmountEur(),
            'display_amount' => $this->getFormattedDisplayAmount(),
            'rank' => $this->rank_position,
            'rank_change' => $this->getRankChange(),
            'is_highest' => $this->is_highest,
            'status' => $this->status,
            'sub_status' => $this->sub_status,
            'status_color' => $this->getStatusColor(),
            'status_label' => $this->getStatusLabel(),
            'user_name' => $this->user?->name ?? 'Anonimo',
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'superseded_by' => $this->supersededBy?->user?->name,
            'superseded_at' => $this->superseded_at?->format('d/m/Y H:i'),
        ];
    }

    // ===== SCOPE METHODS =====

    /**
     * Active reservations
     */
    public function scopeActive(Builder $query): Builder {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_current', true);
    }

    /**
     * Highest reservations only
     */
    public function scopeHighest(Builder $query): Builder {
        return $query->where('is_highest', true);
    }

    /**
     * Reservations for a specific EGI
     */
    public function scopeForEgi(Builder $query, int $egiId): Builder {
        return $query->where('egi_id', $egiId);
    }

    /**
     * Reservations for a specific user
     */
    public function scopeForUser(Builder $query, int $userId): Builder {
        return $query->where('user_id', $userId);
    }

    /**
     * Ranked reservations (ordered by rank)
     */
    public function scopeRanked(Builder $query): Builder {
        return $query->whereNotNull('rank_position')
            ->orderBy('rank_position');
    }

    /**
     * Reservations that need ranking update
     */
    public function scopeNeedsRanking(Builder $query): Builder {
        return $query->active()
            ->whereNull('rank_position');
    }

    // ===== FUTURE MINT METHODS (Pre-populated for launch) =====

    /**
     * Check if user is in mint window (future)
     */
    public function isInMintWindow(): bool {
        if (!$this->mint_window_starts_at || !$this->mint_window_ends_at) {
            return false;
        }

        $now = now();
        return $now->between($this->mint_window_starts_at, $this->mint_window_ends_at);
    }

    /**
     * Get remaining mint time (future)
     */
    public function getMintTimeRemaining(): ?int {
        if (!$this->isInMintWindow()) {
            return null;
        }

        return $this->mint_window_ends_at->diffInMinutes(now());
    }

    /**
     * Confirm mint intention (future)
     */
    public function confirmMint(): bool {
        if (!$this->isInMintWindow()) {
            return false;
        }

        $this->mint_confirmed = true;
        $this->mint_confirmed_at = now();
        $this->sub_status = self::SUB_STATUS_CONFIRMED;

        return $this->save();
    }

    // ===== NOTIFICATION METHODS =====

    /**
     * Record notification sent
     */
    public function recordNotification(string $type, array $data = []): void {
        $history = $this->notification_history ?? [];

        $history[] = [
            'type' => $type,
            'sent_at' => now()->toIso8601String(),
            'data' => $data,
        ];

        $this->notification_history = $history;
        $this->last_notification_at = now();
        $this->save();
    }

    /**
     * Check if notification needed
     */
    public function needsNotification(string $type): bool {
        if (!$this->last_notification_at) {
            return true;
        }

        // Check if specific notification type was already sent
        $history = collect($this->notification_history ?? []);
        $lastOfType = $history->where('type', $type)->last();

        if (!$lastOfType) {
            return true;
        }

        // Don't send same notification within 24 hours
        $lastSent = Carbon::parse($lastOfType['sent_at']);
        return $lastSent->diffInHours(now()) > 24;
    }

    // ===== LEGACY COMPATIBILITY =====

    /**
     * Get offer amount (legacy compatibility)
     */
    public function getOfferAmountFiatAttribute(): float {
        return $this->display_amount ?? $this->amount_eur;
    }

    /**
     * Get fiat currency (legacy compatibility)
     */
    public function getFiatCurrencyAttribute(): string {
        return $this->display_currency ?? 'EUR';
    }

    /**
     * Create a certificate for this reservation
     *
     * @param array $additionalData
     * @return EgiReservationCertificate
     */
    public function createCertificate(array $additionalData = []): EgiReservationCertificate {
        // Generate certificate UUID first
        $certificateUuid = \Illuminate\Support\Str::uuid();
        
        // FIX: user->wallet returns Wallet object, not string!
        // Get actual wallet address string (max 58 chars for Algorand)
        $walletAddress = $this->wallet_address 
            ?? ($this->user?->wallets()->first()?->wallet ?? 'Unknown Wallet');
        
        $reservationType = $this->type ?? 'strong';
        $offerAmountFiat = $this->amount_eur;

        // Generate verification data string (MUST match EgiReservationCertificate::generateVerificationData())
        $verificationData = implode('|', [
            $certificateUuid,
            $this->egi_id,
            $walletAddress,
            $reservationType,
            $offerAmountFiat,
            now()->toIso8601String()
        ]);

        // Generate signature hash from verification data
        $signatureHash = hash('sha256', $verificationData);

        return EgiReservationCertificate::create([
            'reservation_id' => $this->id,
            'egi_id' => $this->egi_id,
            'user_id' => $this->user_id,
            'wallet_address' => $walletAddress,
            'reservation_type' => $reservationType,
            'offer_amount_fiat' => $offerAmountFiat,
            'offer_amount_algo' => $this->offer_amount_algo ?? 0,
            'certificate_uuid' => $certificateUuid,
            'signature_hash' => $signatureHash,
            'is_superseded' => false,
            'is_current_highest' => $this->is_highest ?? false,
            ...$additionalData
        ]);
    }

    /**
     * Mark this reservation as superseded by another reservation
     *
     * @param Reservation $supersedingReservation
     * @return bool
     */
    public function markAsSuperseded(Reservation $supersedingReservation): bool {
        $this->is_current = false;
        $this->sub_status = self::SUB_STATUS_SUPERSEDED;
        $this->superseded_by_id = $supersedingReservation->id;
        $this->superseded_at = now();

        return $this->save();
    }

    /**
     * Conta il numero totale di opere (EGI) attualmente prenotate sulla piattaforma
     *
     * @return int
     */
    public static function getTotalReservedWorks(): int {
        return static::where('is_current', true)
            ->distinct('egi_id')
            ->count('egi_id');
    }

    /**
     * Conta il numero totale di artisti (creators) che hanno almeno un'opera prenotata
     *
     * @return int
     */
    public static function getTotalArtistsWithReservations(): int {
        return static::where('is_current', true)
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->distinct('collections.creator_id')
            ->count('collections.creator_id');
    }
}