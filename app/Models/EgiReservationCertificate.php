<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @Oracode Eloquent Model: EgiReservationCertificate
 * 🎯 Purpose: Represents a certificate generated for an EGI reservation
 * 🧱 Core Logic: Stores certificate data, manages verification and PDF generation
 * 🛡️ GDPR: Contains pseudonymized wallet_address, privacy-focused PDF retrieval
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $egi_id
 * @property int|null $user_id
 * @property string $wallet_address
 * @property string $reservation_type
 * @property float $offer_amount_fiat
 * @property float $offer_amount_algo
 * @property string $certificate_uuid
 * @property string $signature_hash
 * @property bool $is_superseded
 * @property bool $is_current_highest
 * @property string|null $pdf_path
 * @property string|null $public_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Reservation $reservation
 * @property-read \App\Models\Egi $egi
 * @property-read \App\Models\User|null $user
 */
class EgiReservationCertificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'certificate_type',
        'reservation_id',
        'egi_blockchain_id',
        'egi_id',
        'user_id',
        'wallet_address',
        'reservation_type',
        'offer_amount_fiat',
        'offer_amount_algo',
        'certificate_uuid',
        'signature_hash',
        'is_superseded',
        'is_current_highest',
        'pdf_path',
        'public_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'offer_amount_fiat' => 'decimal:2',
        'offer_amount_algo' => 'decimal:8',
        'is_superseded' => 'boolean',
        'is_current_highest' => 'boolean',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Generate UUID before creating
        static::creating(function ($certificate) {
            if (!$certificate->certificate_uuid) {
                $certificate->certificate_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the reservation associated with the certificate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the EGI associated with the certificate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egi()
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user associated with the certificate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the blockchain record associated with the mint certificate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egiBlockchain()
    {
        return $this->belongsTo(EgiBlockchain::class, 'egi_blockchain_id');
    }

    /**
     * Scope a query to only include blockchain (mint) certificates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlockchainType($query)
    {
        return $query->where('certificate_type', 'mint');
    }

    /**
     * Scope a query to only include reservation certificates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReservationType($query)
    {
        return $query->where('certificate_type', 'reservation');
    }

    /**
     * Verify the signature on this certificate.
     *
     * @param string $data The data to verify against the signature
     * @return bool Whether the signature is valid
     */
    public function verifySignature(string $data): bool
    {
        // Create a hash of the provided data
        $hash = hash('sha256', $data);

        // Compare it with the stored signature hash
        return hash_equals($this->signature_hash, $hash);
    }

    /**
     * Generate the verification data string used for signature.
     *
     * @return string The data string used for signature verification
     */
    public function generateVerificationData(): string
    {

        $createdAt = $this->created_at ?? now();

        // Different verification data based on certificate type
        // Use egi_blockchain_id to determine type (NOT certificate_type)
        if ($this->egi_blockchain_id !== null) {
            // Blockchain certificate: use blockchain-specific fields
            $blockchain = $this->egiBlockchain;

            return implode('|', [
                $this->certificate_uuid,
                $this->egi_id,
                $this->egi_blockchain_id,
                $blockchain->asa_id ?? '',
                $blockchain->blockchain_tx_id ?? '',
                $blockchain->paid_amount ?? '',
                $createdAt->toIso8601String()
            ]);
        }

        // Reservation certificate: use reservation-specific fields
        return implode('|', [
            $this->certificate_uuid,
            $this->egi_id,
            $this->wallet_address,
            $this->reservation_type,
            $this->offer_amount_fiat,
            $createdAt->toIso8601String()
        ]);
    }

    /**
     * Get the public URL attribute for the certificate.
     *
     * @return string
     * @privacy-safe The URL generation involves no PII beyond what's public
     */
    public function getPublicUrlAttribute(): string
    {
        if ($this->attributes['public_url']) {
            return $this->attributes['public_url'];
        }

        // Generate URL if not already set
        return route('egi-certificates.show', $this->certificate_uuid);
    }

    /**
     * Get the verification URL for the certificate.
     *
     * @return string
     */
    public function getVerificationUrl(): string
    {
        return route('egi-certificates.verify', $this->certificate_uuid);
    }

    /**
     * Get the PDF download URL for the certificate.
     *
     * @return string|null
     */
    public function getPdfUrl(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }

        return route('egi-certificates.download', $this->certificate_uuid);
    }

    /**
     * Check if the certificate has a generated PDF.
     *
     * @return bool
     */
    public function hasPdf(): bool
    {
        return !empty($this->pdf_path) && Storage::exists($this->pdf_path);
    }

    /**
     * Mark this certificate as superseded.
     *
     * @return bool Whether the update was successful
     */
    public function markAsSuperseded(): bool
    {
        $this->is_superseded = true;
        $this->is_current_highest = false;
        return $this->save();
    }
}
