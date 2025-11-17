<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Personal Data (GDPR Sensitive)
 * 🎯 Purpose: Manages GDPR-sensitive personal identification data
 * 🛡️ Privacy: Ultra-sensitive data with strict access controls
 * 🧱 Core Logic: Handles personal identity and compliance tracking
 */
class UserPersonalData extends Model {
    protected $table = 'user_personal_data';

    protected $fillable = [
        'user_id',
        'street',
        'city',
        'region',
        'state',
        'zip',
        'country',
        'province',
        'home_phone',
        'cell_phone',
        'work_phone',
        'birth_date',
        'birth_place',
        'gender',
        'fiscal_code',
        'tax_id_number',
        'allow_personal_data_processing',
        'processing_purposes',
        'consent_updated_at',
        'iban'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'allow_personal_data_processing' => 'boolean',
        'processing_purposes' => 'array',
        'consent_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'fiscal_code',
        'tax_id_number',
        'birth_date'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): ?string {
        $parts = array_filter([$this->street, $this->city, $this->region, $this->state, $this->zip]);
        return empty($parts) ? null : implode(', ', $parts);
    }

    public function hasCompleteAddress(): bool {
        return !empty($this->street) && !empty($this->city) && !empty($this->zip);
    }

    public function isDataProcessingAllowed(?string $purpose = null): bool {
        if (!$this->allow_personal_data_processing) {
            return false;
        }

        if ($purpose && $this->processing_purposes) {
            return in_array($purpose, $this->processing_purposes);
        }

        return true;
    }
}
