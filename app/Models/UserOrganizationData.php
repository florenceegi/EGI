<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Organization Data
 * 🎯 Purpose: Manages business and organizational information
 * 🛡️ Privacy: Business data with moderate sensitivity
 * 🧱 Core Logic: Handles seller verification and business compliance
 */
class UserOrganizationData extends Model {
    protected $table = 'user_organization_data';

    protected $fillable = [
        'user_id',
        'org_name',
        'org_email',
        'pec',
        'org_street',
        'org_city',
        'org_region',
        'org_state',
        'org_zip',
        'org_site_url',
        'org_phone_1',
        'org_phone_2',
        'org_phone_3',
        'rea',
        'ateco_code',
        'ateco_description',
        'org_fiscal_code',
        'org_vat_number',
        'is_seller_verified',
        'can_issue_invoices',
        'business_type',
        'iban',
        'enrichment_sources',
        'enriched_at',
    ];

    protected $casts = [
        'is_seller_verified' => 'boolean',
        'can_issue_invoices' => 'boolean',
        'vat_registered' => 'boolean',
        'requires_compliance_review' => 'boolean',
        'business_categories' => 'array',
        'enrichment_sources' => 'array',
        'seller_verified_at' => 'datetime',
        'compliance_checked_at' => 'datetime',
        'enriched_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getFullOrganizationAddressAttribute(): ?string {
        $parts = array_filter([
            $this->org_street,
            $this->org_city,
            $this->org_region,
            $this->org_state,
            $this->org_zip
        ]);
        return empty($parts) ? null : implode(', ', $parts);
    }

    public function hasCompleteSellerData(): bool {
        return !empty($this->org_name) &&
            (!empty($this->org_fiscal_code) || !empty($this->org_vat_number)) &&
            $this->hasCompleteAddress();
    }

    public function hasCompleteAddress(): bool {
        return !empty($this->org_street) && !empty($this->org_city) && !empty($this->org_zip);
    }

    public function getMissingSellerDataFields(): array {
        $missing = [];

        if (empty($this->org_name)) $missing[] = 'org_name';
        if (empty($this->org_fiscal_code) && empty($this->org_vat_number)) {
            $missing[] = 'org_fiscal_code_or_vat';
        }
        if (empty($this->org_street)) $missing[] = 'org_street';
        if (empty($this->org_city)) $missing[] = 'org_city';
        if (empty($this->org_zip)) $missing[] = 'org_zip';

        return $missing;
    }
}
