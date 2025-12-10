<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gdpr\ConsentStatus;
use App\Enums\Gdpr\DataExportStatus;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Enums\Gdpr\GdprRequestStatus;
use App\Enums\Gdpr\GdprRequestType;
use App\Models\Egi;
use App\Traits\HasTeamRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Traits\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia {
    use HasApiTokens;
    use HasRoles;
    use InteractsWithMedia;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nick_name',
        'last_name',
        'email',
        'preferred_currency',
        'avatar_url',
        'password',
        'usertype',
        'current_collection_id',
        'consent_summary',
        'consents_updated_at',
        'processing_limitations',
        'limitations_updated_at',
        'has_pending_gdpr_requests',
        'last_gdpr_request_at',
        'gdpr_compliant',
        'gdpr_status_updated_at',
        'data_retention_until',
        'retention_reason',
        'privacy_settings',
        'preferred_communication_method',
        'last_activity_logged_at',
        'total_gdpr_requests',
        'profile_photo_path',
        'created_via',
        'language',
        'wallet',
        'wallet_balance',
        'personal_secret',
        'is_weak_auth',
        'icon_style',
        'natan_api_key',
        'natan_api_key_generated_at',
        'natan_api_key_last_used_at',
        // AI Credits (Task 5)
        'ai_credits_balance',
        'ai_credits_lifetime_earned',
        'ai_credits_lifetime_used',
        'ai_subscription_tier',
        'ai_subscription_ends_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wallet_balance' => 'decimal:4',
        'consent' => 'boolean',
        'consent_summary' => 'array',
        'processing_limitations' => 'array',
        'privacy_settings' => 'array',
        'consents_updated_at' => 'datetime',
        'limitations_updated_at' => 'datetime',
        'last_gdpr_request_at' => 'datetime',
        'gdpr_status_updated_at' => 'datetime',
        'data_retention_until' => 'datetime',
        'last_activity_logged_at' => 'datetime',
        'has_pending_gdpr_requests' => 'boolean',
        'gdpr_compliant' => 'boolean',
        'total_gdpr_requests' => 'integer',
        'is_weak_auth' => 'boolean',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'icon_style',
    ];

    public function getIconStyleAttribute(): string {
        // Ritorna l'icon_style dall'attributo o un valore di default
        return $this->attributes['icon_style'] ?? config('icons.styles.default');
    }

    /**
     * Get the display name following GDPR privacy rules.
     *
     * Logic:
     * 1. If nickname exists -> show nickname (user choice to be public)
     * 2. If no nickname but has name/last_name -> show wallet abbreviation (privacy protection)
     * 3. Fallback -> wallet abbreviation or "Utente Anonimo"
     *
     * @return string
     */
    public function getNameAttribute(): string {
        // Se c'è nickname, mostralo (scelta dell'utente di essere pubblico)
        if (!empty($this->attributes['nick_name'])) {
            return $this->attributes['nick_name'];
        }

        // Se ci sono nome/cognome ma NO nickname -> abbreviazione wallet (privacy GDPR)
        if (!empty($this->attributes['name']) || !empty($this->attributes['last_name'])) {
            return $this->getWalletAbbreviation();
        }

        // Fallback: abbreviazione wallet o anonimo
        return $this->getWalletAbbreviation();
    }

    /**
     * Get the legal name (always the raw name field).
     *
     * @return string
     */
    public function getLegalNameAttribute(): string {
        return $this->attributes['name'];
    }

    /**
     * Get wallet abbreviation for GDPR-compliant display.
     *
     * @return string
     */
    public function getWalletAbbreviation(): string {
        if (!empty($this->attributes['wallet'])) {
            $wallet = $this->attributes['wallet'];

            // Se il wallet è lungo, prendi primi 6 e ultimi 4 caratteri
            if (strlen($wallet) > 12) {
                return substr($wallet, 0, 6) . '...' . substr($wallet, -4);
            }

            // Se è più corto, prendi primi 8 caratteri
            return substr($wallet, 0, min(8, strlen($wallet))) . (strlen($wallet) > 8 ? '...' : '');
        }

        return __('user_personal_data.anonymous_user');
    }

    /**
     * Get the full legal name for administrative purposes only.
     * Use with caution - GDPR compliance required.
     *
     * @return string
     */
    public function getFullLegalName(): string {
        $name = trim(($this->attributes['name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? ''));
        return !empty($name) ? $name : $this->getWalletAbbreviation();
    }

    /**
     * Get the collections created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedCollections(): HasMany {
        return $this->hasMany(Collection::class, 'creator_id');
    }

    /**
     * Get the EGIs created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdEgis(): HasMany {
        return $this->hasMany(Egi::class, 'user_id');
    }

    /**
     * Alias for ownedCollections for consistency
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdCollections(): HasMany {
        return $this->ownedCollections();
    }

    /**
     * User's projects (PA Enterprise feature)
     *
     * ✨ NEW v4.0 - Projects System for Priority RAG
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects(): HasMany {
        return $this->hasMany(\App\Models\Project::class, 'user_id');
    }

    /**
     * Organization data for business/EPP users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organizationData() {
        return $this->hasOne(\App\Models\UserOrganizationData::class, 'user_id');
    }

    /**
     * EPP Projects owned by this EPP User (usertype='epp')
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eppProjects() {
        return $this->hasMany(\App\Models\EppProject::class, 'epp_user_id');
    }

    // In app/Models/User.php
    public function getCurrentCollectionDetails() {
        if (!$this->current_collection_id) {
            return [
                'current_collection_id' => null,
                'current_collection_name' => null,
                'can_edit_current_collection' => false,
            ];
        }

        $collection = $this->currentCollection;

        // Check role in collection_user pivot
        $pivot = \DB::table('collection_user')
            ->where('user_id', $this->id)
            ->where('collection_id', $collection->id)
            ->first();

        $canEdit = false;
        if ($pivot) {
            if ($pivot->is_owner) {
                $canEdit = true;
            } else if ($pivot->role) {
                try {
                    $role = \Spatie\Permission\Models\Role::findByName($pivot->role, 'web');
                    $canEdit = $role->hasPermissionTo('update_collection');
                } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
                    // Role does not exist - user cannot edit
                    $canEdit = false;
                }
            }
        }

        return [
            'current_collection_id' => $collection->id,
            'current_collection_name' => $collection->collection_name,
            'can_edit_current_collection' => $canEdit,
        ];
    }

    /**
     * Get the current collection name
     *
     * @return string|null
     */
    public function getCurrentCollectionName(): ?string {
        if (!$this->current_collection_id) {
            return null;
        }

        $collection = $this->currentCollection;
        return $collection ? $collection->collection_name : null;
    }

    /**
     * Get the current collection EGI count
     *
     * @return int
     */
    public function getCurrentCollectionEgiCount(): int {
        if (!$this->current_collection_id) {
            return 0;
        }

        $collection = $this->currentCollection;
        return $collection ? $collection->egis()->count() : 0;
    }

    /**
     * Get the collections the user collaborates on.
     *
     * This relationship retrieves collections where the user is listed as a collaborator
     * in the 'collection_user' pivot table, excluding collections where the user is the owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collaborations(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot(['role', 'is_owner', 'status']) // Include status from pivot table
            ->wherePivot('is_owner', '!=', 1) // Exclude collections where user is owner
            ->wherePivot('status', 'active') // Only active collaborations
            ->withTimestamps(); // Include created_at e updated_at dalla tabella pivot
    }

    /**
     * Get all collections accessible to the user (owned + collaborations).
     * This is a unified relationship that includes both owned and collaborated collections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collections(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->wherePivot('status', '!=', 'removed') // Exclude removed collaborations
            ->wherePivotNull('removed_at'); // Only active relationships
    }

    /**
     * Get all collections where user is the owner.
     * This relationship uses the pivot table's is_owner flag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ownedCollectionsViaPivot(): BelongsToMany {
        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->wherePivot('is_owner', true)
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Get collections where user has specific roles.
     *
     * @param array|string $roles
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collectionsWithRole($roles): BelongsToMany {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->whereIn('collection_user.role', $roles)
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Get collections where user can edit (owner or has update_collection permission).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function editableCollections(): BelongsToMany {
        // Get all roles that have update_collection permission
        $editableRoles = \Spatie\Permission\Models\Role::whereHas('permissions', function ($query) {
            $query->where('name', 'update_collection');
        })->pluck('name')->toArray();

        return $this->belongsToMany(Collection::class, 'collection_user', 'user_id', 'collection_id')
            ->withPivot([
                'role',
                'is_owner',
                'status',
                'joined_at',
                'removed_at',
                'metadata'
            ])
            ->withTimestamps()
            ->where(function ($query) use ($editableRoles) {
                $query->wherePivot('is_owner', true)
                    ->orWhereIn('collection_user.role', $editableRoles);
            })
            ->wherePivot('status', '!=', 'removed')
            ->wherePivotNull('removed_at');
    }

    /**
     * Check if user has access to a specific collection.
     *
     * @param int $collectionId
     * @return bool
     */
    public function hasAccessToCollection(int $collectionId): bool {
        return $this->collections()
            ->where('collection_id', $collectionId)
            ->exists();
    }

    /**
     * Get user's role in a specific collection.
     *
     * @param int $collectionId
     * @return string|null
     */
    public function getRoleInCollection(int $collectionId): ?string {
        $pivot = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        if (!$pivot) {
            return null;
        }

        // If owner, return 'owner', otherwise return the role
        return $pivot->pivot->is_owner ? 'owner' : $pivot->pivot->role;
    }

    /**
     * Check if user can edit a specific collection.
     *
     * @param int $collectionId
     * @return bool
     */
    public function canEditCollectionById(int $collectionId): bool {
        $pivot = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        if (!$pivot) {
            return false;
        }

        if ($pivot->pivot->is_owner) {
            return true;
        }

        if ($pivot->pivot->role) {
            try {
                $role = \Spatie\Permission\Models\Role::findByName($pivot->pivot->role, 'web');
                return $role->hasPermissionTo('update_collection');
            } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Join a collection with specified role.
     *
     * @param int $collectionId
     * @param string $role
     * @param bool $isOwner
     * @param array $metadata
     * @return bool
     */
    public function joinCollection(int $collectionId, string $role = 'viewer', bool $isOwner = false, array $metadata = []): bool {
        try {
            $this->collections()->attach($collectionId, [
                'role' => $role,
                'is_owner' => $isOwner,
                'status' => 'active',
                'joined_at' => now(),
                'metadata' => $metadata ? json_encode($metadata) : null,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Leave a collection (soft removal).
     *
     * @param int $collectionId
     * @return bool
     */
    public function leaveCollection(int $collectionId): bool {
        try {
            $this->collections()->updateExistingPivot($collectionId, [
                'status' => 'removed',
                'removed_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update user's role in a collection.
     *
     * @param int $collectionId
     * @param string $newRole
     * @return bool
     */
    public function updateRoleInCollection(int $collectionId, string $newRole): bool {
        try {
            $this->collections()->updateExistingPivot($collectionId, [
                'role' => $newRole,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the user's current active collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentCollection(): BelongsTo {
        return $this->belongsTo(Collection::class, 'current_collection_id');
    }

    /**
     * Get the user's personal data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function personalData(): HasOne {
        return $this->hasOne(UserPersonalData::class);
    }

    public function wallets() {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get user's primary wallet object (first wallet from wallets table)
     * NOTE: Use $user->primaryWallet to get the Wallet object
     *       Use $user->wallet to get the wallet address string (column)
     *
     * @return Wallet|null
     */
    public function getPrimaryWalletAttribute(): ?Wallet {
        return $this->wallets()->first();
    }

    /**
     * Get the user's Egili balance from their primary wallet.
     *
     * @return int
     */
    public function getEgiliBalanceAttribute(): int {
        return $this->primaryWallet?->egili_balance ?? 0;
    }

    public function customNotifications() {
        return $this->morphMany(CustomDatabaseNotification::class, 'notifiable');
    }

    public function walletChangeProposer() {
        return $this->hasMany(NotificationPayloadWallet::class, 'proposer_id');
    }

    public function walletChangeReceiver() {
        return $this->hasMany(NotificationPayloadWallet::class, 'receiver_id');
    }

    public function currentCollectionBySession() {
        $id = session('current_collection_id')
            ?? $this->current_collection_id;

        return \App\Models\Collection::find($id);
    }

    // Nel modello User
    public function canEditCollection(Collection $collection): bool {
        // È creator o owner
        if ($collection->creator_id === $this->id || $collection->owner_id === $this->id) {
            return true;
        }

        // O ha un ruolo con permesso update_collection nella pivot
        $pivot = $this->collaborations()
            ->where('collection_id', $collection->id)
            ->first();

        if ($pivot && $pivot->pivot->role) {
            try {
                $role = \Spatie\Permission\Models\Role::findByName($pivot->pivot->role, 'web');
                return $role->hasPermissionTo('update_collection');
            } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
                return false;
            }
        }

        return false;
    }

    public function getRouteKeyName(): string {
        return 'id';
    }

    public function getRouteKey(): string {
        return $this->getAttribute($this->getRouteKeyName());
    }

    public function getRouteKeyNameForCollection(): string {
        return 'collection_id';
    }

    public function getRouteKeyForCollection(): string {
        return $this->getAttribute($this->getRouteKeyNameForCollection());
    }


    /**
     * @Oracode User Model GDPR Extensions
     * 🎯 Purpose: Add GDPR relationships to existing User model
     * 🧱 Core Logic: Extend User model with all GDPR-related relationships
     * 📡 API: Relationships for GDPR data management
     * 🛡️ GDPR: Complete data subject relationship mapping
     *
     * ADD THESE METHODS TO THE EXISTING App\Models\User CLASS
     *
     * @package App\Models
     * @version 1.0
     */

    /**
     * Get all the consent records for the user.
     * This represents the user's consent history log.
     *
     * @return HasMany
     */
    public function consents(): HasMany {
        return $this->hasMany(UserConsent::class, 'user_id');
    }

    /**
     * Get the full forensic audit log for the user's consents.
     *
     * Recupera la cronologia di audit completa e dettagliata, come registrata
     * nella tabella `consent_histories`. Utile per scopi di compliance e legali.
     *
     * @return HasMany
     */
    public function consentAuditLog(): HasMany {
        return $this->hasMany(ConsentHistory::class, 'user_id');
    }

    /**
     * Get current active consents for this user.
     *
     * @return HasMany
     */
    public function activeConsents(): HasMany {
        return $this->hasMany(UserConsent::class)
            ->where('status', ConsentStatus::ACTIVE->value)
            ->whereNull('withdrawn_at');
    }


    /**
     * Get all GDPR requests made by this user.
     *
     * @return HasMany
     */
    public function gdprRequests(): HasMany {
        return $this->hasMany(GdprRequest::class);
    }

    /**
     * Get pending GDPR requests for this user.
     *
     * @return HasMany
     */
    public function pendingGdprRequests(): HasMany {
        return $this->hasMany(GdprRequest::class)
            ->whereIn('status', ['pending', 'in_progress', 'verification_required']);
    }

    /**
     * Get user activity logs for this user.
     *
     * @return HasMany
     */
    public function activities(): HasMany {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get login history for this user from user activities.
     *
     * @return HasMany
     */
    public function loginHistory(): HasMany {
        return $this->hasMany(UserActivity::class)
            ->where('category', GdprActivityCategory::AUTHENTICATION_LOGIN->value);
    }

    /**
     * Payment distributions received by this user
     * @return HasMany
     */
    public function paymentDistributions(): HasMany {
        return $this->hasMany(PaymentDistribution::class);
    }

    /**
     * Get security events related to this user.
     *
     * @return HasMany
     */
    public function securityEvents(): HasMany {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * Get high-risk security events for this user.
     *
     * @return HasMany
     */
    public function highRiskSecurityEvents(): HasMany {
        return $this->hasMany(SecurityEvent::class)
            ->highRisk()
            ->unresolved();
    }

    /**
     * Get data exports requested by this user.
     *
     * @return HasMany
     */
    public function dataExports(): HasMany {
        return $this->hasMany(DataExport::class);
    }

    /**
     * Get available data exports for download.
     *
     * @return HasMany
     */
    public function availableDataExports(): HasMany {
        return $this->hasMany(DataExport::class)
            ->where('status', DataExportStatus::COMPLETED->value)
            ->where('expires_at', '>', now());
    }

    /**
     * Get breach reports submitted by this user.
     *
     * @return HasMany
     */
    public function breachReports(): HasMany {
        return $this->hasMany(BreachReport::class);
    }

    /**
     * Get open breach reports from this user.
     *
     * @return HasMany
     */
    public function openBreachReports(): HasMany {
        return $this->hasMany(BreachReport::class)->open();
    }

    /**
     * Get GDPR audit logs where this user is the data subject.
     *
     * @return HasMany
     */
    public function gdprAuditLogs(): HasMany {
        return $this->hasMany(GdprAuditLog::class, 'data_subject_id');
    }

    /**
     * Get GDPR audit logs where this user performed the action.
     *
     * @return HasMany
     */
    public function performedAuditLogs(): HasMany {
        return $this->hasMany(GdprAuditLog::class, 'user_id');
    }

    /**
     * Get messages sent to DPO by this user.
     *
     * @return HasMany
     */
    public function dpoMessages(): HasMany {
        return $this->hasMany(DpoMessage::class);
    }

    /**
     * Get open DPO messages from this user.
     *
     * @return HasMany
     */
    public function openDpoMessages(): HasMany {
        return $this->hasMany(DpoMessage::class)->open();
    }

    /**
     * Get privacy policies created by this user (if admin).
     *
     * @return HasMany
     */
    public function createdPrivacyPolicies(): HasMany {
        return $this->hasMany(PrivacyPolicy::class, 'created_by');
    }

    /**
     * Get privacy policies approved by this user (if legal reviewer).
     *
     * @return HasMany
     */
    public function approvedPrivacyPolicies(): HasMany {
        return $this->hasMany(PrivacyPolicy::class, 'approved_by');
    }

    /**
     * Get breach reports assigned to this user for investigation.
     *
     * @return HasMany
     */
    public function assignedBreachReports(): HasMany {
        return $this->hasMany(BreachReport::class, 'assigned_to');
    }

    // ====================================================================================
    // ADD THESE GDPR-SPECIFIC HELPER METHODS TO THE User CLASS
    // ====================================================================================

    /**
     * Check if user has given consent for specific purpose.
     *
     * @param string $purpose Consent purpose
     * @return bool
     */
    public function hasActiveConsent(string $purpose): bool {
        return $this->activeConsents()
            ->where('consent_type', $purpose)
            ->exists();
    }

    /**
     * Get user's current consent status for specific purpose.
     *
     * @param string $purpose Consent purpose
     * @return string|null Status or null if no consent given
     */
    public function getConsentStatus(string $purpose): ?string {
        $consent = $this->consents()
            ->where('consent_type', $purpose)
            ->orderBy('created_at', 'desc')
            ->first();

        return $consent?->status;
    }

    /**
     * Check if user has any pending GDPR requests.
     *
     * @return bool
     */
    public function hasPendingGdprRequests(): bool {
        return $this->pendingGdprRequests()->exists();
    }

    /**
     * Check if user has requested account deletion.
     *
     * @return bool
     */
    public function hasRequestedDeletion(): bool {
        return $this->gdprRequests()
            ->where('request_type', GdprRequestType::ERASURE->value)
            ->whereIn('status', [
                GdprRequestStatus::PENDING->value,
                GdprRequestStatus::IN_PROGRESS->value,
                GdprRequestStatus::VERIFICATION_REQUIRED->value
            ])
            ->exists();
    }

    /**
     * Check if user has recent security incidents.
     *
     * @param int $hours Hours to look back (default 24)
     * @return bool
     */
    public function hasRecentSecurityIncidents(int $hours = 24): bool {
        return $this->securityEvents()
            ->where('created_at', '>=', now()->subHours($hours))
            ->highRisk()
            ->exists();
    }

    /**
     * Get user's GDPR compliance score (0-100).
     *
     * @return int
     */
    public function getGdprComplianceScore(): int {
        $score = 100;

        // Deduct points for missing consents
        $requiredConsents = ['marketing', 'analytics', 'cookies'];
        foreach ($requiredConsents as $purpose) {
            if (!$this->hasActiveConsent($purpose)) {
                $score -= 10;
            }
        }

        // Deduct points for unresolved security events
        $unresolvedEvents = $this->securityEvents()->unresolved()->count();
        $score -= min($unresolvedEvents * 5, 30);

        // Deduct points for overdue GDPR requests
        $overdueRequests = $this->gdprRequests()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'rejected'])
            ->count();
        $score -= min($overdueRequests * 15, 45);

        return max(0, $score);
    }

    /**
     * Get summary of user's GDPR data for dashboard.
     *
     * @return array
     */
    public function getGdprSummary(): array {
        return [
            'active_consents' => $this->activeConsents()->count(),
            'pending_requests' => $this->pendingGdprRequests()->count(),
            'completed_requests' => $this->gdprRequests()
                ->where('status', 'completed')
                ->count(),
            'available_exports' => $this->availableDataExports()->count(),
            'open_breach_reports' => $this->openBreachReports()->count(),
            'recent_security_events' => $this->securityEvents()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'compliance_score' => $this->getGdprComplianceScore(),
            'last_activity' => $this->activities()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at,
            'account_created' => $this->created_at,
            'last_consent_update' => $this->consents()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at,
        ];
    }

    /**
     * Check if user can request data export (rate limiting).
     *
     * @return bool
     */
    public function canRequestDataExport(): bool {
        // Allow one export per 30 days
        $recentExport = $this->dataExports()
            ->where('created_at', '>=', now()->subDays(30))
            ->first();

        return is_null($recentExport);
    }

    /**
     * Check if user can submit breach report (rate limiting).
     *
     * @return bool
     */
    public function canSubmitBreachReport(): bool {
        // Allow max 5 reports per day
        $todayReports = $this->breachReports()
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        return $todayReports < 5;
    }

    /**
     * Get user's preferred communication language for GDPR notices.
     *
     * @return string
     */
    public function getGdprLanguage(): string {
        return $this->gdpr_language ?? $this->language ?? 'en';
    }

    /**
     * Check if user has opted out of GDPR email notifications.
     *
     * @return bool
     */
    public function hasOptedOutGdprNotifications(): bool {
        return $this->gdpr_notifications_disabled ?? false;
    }

    /**
     * Mark user for GDPR data review (for compliance audits).
     *
     * @param string $reason Review reason
     * @return bool
     */
    public function markForGdprReview(string $reason): bool {
        $this->gdpr_review_required = true;
        $this->gdpr_review_reason = $reason;
        $this->gdpr_review_date = now();
        return $this->save();
    }

    /**
     * Clear GDPR review flag.
     *
     * @return bool
     */
    public function clearGdprReview(): bool {
        $this->gdpr_review_required = false;
        $this->gdpr_review_reason = null;
        $this->gdpr_review_completed_at = now();
        return $this->save();
    }

    // ====================================================================================
    // ADD THESE SCOPES TO THE User CLASS
    // ====================================================================================

    /**
     * Scope for users requiring GDPR review.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresGdprReview($query) {
        return $query->where('gdpr_review_required', true);
    }

    /**
     * Scope for users with pending GDPR requests.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPendingGdprRequests($query) {
        return $query->whereHas('gdprRequests', function ($q) {
            $q->whereIn('status', array_map(
                fn($status) => $status->value,
                array_filter(GdprRequestStatus::cases(), fn($status) => $status->isActive())
            ));
        });
    }

    /**
     * Get active processing restrictions.
     */
    public function activeProcessingRestrictions(): HasMany {
        return $this->processingRestrictions()
            ->where('is_active', true)
            ->whereNull('lifted_at');
    }

    /**
     * Check if user has active consent for specific purpose.
     *
     * @param string $purpose
     * @return bool
     */
    public function hasConsentFor(string $purpose): bool {
        return $this->consents()
            ->where('consent_type', $purpose)
            ->where('status', ConsentStatus::ACTIVE->value)
            ->whereNull('withdrawn_at')
            ->exists();
    }

    /**
     * Scope for users with recent security incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $hours Hours to look back
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRecentSecurityIncidents($query, int $hours = 24) {
        return $query->whereHas('securityEvents', function ($q) use ($hours) {
            $q->where('created_at', '>=', now()->subHours($hours))
                ->highRisk();
        });
    }

    /**
     * Scope for users eligible for data export.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEligibleForDataExport($query) {
        return $query->whereDoesntHave('dataExports', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        });
    }

    /**
     * @Oracode Relationship: User Biographies
     * 🔗 Purpose: One-to-many relationship with user biographies
     * 📊 Ordering: Most recent biographies first
     * 🔍 Usage: $user->biographies()->get()
     */
    public function biographies(): HasMany {
        return $this->hasMany(Biography::class)
            ->orderBy('updated_at', 'desc');
    }

    /**
     * @Oracode Relationship: Active Biography
     * 🔗 Purpose: Get user's primary/most recent biography
     * 🎯 Logic: Most recently updated biography (for quick access)
     * 🔍 Usage: $user->activeBiography
     */
    public function activeBiography(): HasOne {
        return $this->hasOne(Biography::class)
            ->latestOfMany('updated_at');
    }

    /**
     * @Oracode Relationship: Public Biographies
     * 🔗 Purpose: Only public biographies for profile display
     * 🛡️ Privacy: Respects user privacy settings
     * 🔍 Usage: $user->publicBiographies()->get()
     */
    public function publicBiographies(): HasMany {
        return $this->biographies()
            ->where('is_public', true);
    }

    /**
     * @Oracode Relationship: Completed Biographies
     * 🔗 Purpose: Filter biographies marked as completed
     * 📊 Quality: Show only finished biographies
     * 🔍 Usage: $user->completedBiographies()->get()
     */
    public function completedBiographies(): HasMany {
        return $this->biographies()
            ->where('is_completed', true);
    }

    /**
     * @Oracode Method: Has Biography
     * 🎯 Purpose: Quick check if user has any biography
     * 📤 Returns: Boolean indicating biography existence
     * 🔍 Usage: if ($user->hasBiography()) { ... }
     */
    public function hasBiography(): bool {
        return $this->biographies()->exists();
    }

    /**
     * @Oracode Method: Has Public Biography
     * 🎯 Purpose: Check if user has at least one public biography
     * 📤 Returns: Boolean for profile display logic
     * 🔍 Usage: if ($user->hasPublicBiography()) { ... }
     */
    public function hasPublicBiography(): bool {
        return $this->publicBiographies()->exists();
    }

    /**
     * @Oracode Method: Get Primary Biography
     * 🎯 Purpose: Get the main biography for display
     * 📊 Logic: Public > Completed > Most Recent
     * 📤 Returns: Biography model or null
     */
    public function getPrimaryBiography(): ?Biography {
        // Try public first
        $public = $this->publicBiographies()->first();
        if ($public) {
            return $public;
        }

        // Then completed
        $completed = $this->completedBiographies()->first();
        if ($completed) {
            return $completed;
        }

        // Finally most recent
        return $this->biographies()->first();
    }

    /**
     * @Oracode Method: Get Biography Summary
     * 🎯 Purpose: Generate user biography summary for profiles
     * 📤 Returns: Array with biography stats and info
     */
    public function getBiographySummary(): array {
        $primary = $this->getPrimaryBiography();

        return [
            'has_biography' => $this->hasBiography(),
            'has_public' => $this->hasPublicBiography(),
            'total_count' => $this->biographies()->count(),
            'public_count' => $this->publicBiographies()->count(),
            'primary_biography' => $primary,
            'primary_preview' => $primary?->content_preview,
            'estimated_reading_time' => $primary?->getEstimatedReadingTime(),
        ];
    }

    /**
     * @Oracode Spatie: Media Collections Configuration
     * 🎯 Purpose: Define media collections for user profile images and banners
     * 🖼️ Collections: profile_images for multiple profile photos, current_profile for active one, banner_images for background
     */
    public function registerMediaCollections(): void {
        // Get allowed image MIME types from config
        $allAllowedTypes = config('AllowedFileType.collection.allowed_mime_types', []);
        $allowedImageTypes = array_filter($allAllowedTypes, function ($mimeType) {
            return strpos($mimeType, 'image/') === 0;
        });

        $userProfile = $this->addMediaCollection('profile_images')
            ->acceptsMimeTypes($allowedImageTypes);

        $userProfile->singleFile = false;
        $userProfile->collectionSizeLimit = null;

        $this->addMediaCollection('current_profile')
            ->acceptsMimeTypes($allowedImageTypes)
            ->singleFile(true);

        // Banner images gallery for creator home page background
        $bannerProfile = $this->addMediaCollection('banner_images')
            ->acceptsMimeTypes($allowedImageTypes);

        $bannerProfile->singleFile = false;
        $bannerProfile->collectionSizeLimit = null;

        // Current active banner (single file)
        $this->addMediaCollection('current_banner')
            ->acceptsMimeTypes($allowedImageTypes)
            ->singleFile(true);
    }

    /**
     * @Oracode Spatie: Media Conversions for Performance
     * 🎯 Purpose: Auto-generate optimized image versions for profile photos and banners
     * ⚡ Performance: Thumbnail, avatar, web-optimized, and banner versions
     */
    public function registerMediaConversions(?Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('avatar')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('web')
            ->width(800)
            ->height(600)
            ->optimize()
            ->nonQueued();

        // Banner conversions for creator home background
        $this->addMediaConversion('banner')
            ->width(1920)
            ->height(600)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('banner_mobile')
            ->width(768)
            ->height(480)
            ->optimize()
            ->nonQueued();
    }

    /**
     * @Oracode Method: Get Creator Banner URL
     * 🎯 Purpose: Get the creator's home page banner image URL with fallback
     * 📤 Returns: Banner URL string or null
     */
    public function getCreatorBannerUrl(string $conversion = 'banner'): ?string {
        $currentBanner = $this->getCurrentBanner();
        return $currentBanner ? $currentBanner->getUrl($conversion) : null;
    }

    /**
     * @Oracode Method: Get Current Profile Image
     * 🎯 Purpose: Get the currently active profile image using standard Spatie method
     * 📤 Returns: Media model or null
     * 🔧 FIX: Usa metodo standard come Biography invece di query custom
     */
    public function getCurrentProfileImage(): ?Media {
        // Usa il metodo standard di Spatie come le biografie che funzionano
        return $this->getMedia('profile_images')->last();
    }

    /**
     * @Oracode Method: Get All Profile Images
     * 🎯 Purpose: Get all uploaded profile images
     * 📤 Returns: Collection of Media models
     */
    public function getAllProfileImages() {
        return $this->getMedia('profile_images');
    }

    /**
     * @Oracode Method: Set Current Profile Image
     * 🎯 Purpose: Set a specific image as the current profile photo using profile_photo_path
     * 📤 Returns: Boolean success status
     */
    public function setCurrentProfileImage(Media $media): bool {
        // Update the profile_photo_path field with the media file_name
        $this->update([
            'profile_photo_path' => $media->file_name
        ]);

        return true;
    }

    /**
     * @Oracode Method: Get Current Banner Image
     * 🎯 Purpose: Get the currently active banner image
     * 📤 Returns: Media model or null
     */
    public function getCurrentBanner(): ?Media {
        return $this->getFirstMedia('current_banner');
    }

    /**
     * @Oracode Method: Get All Banner Images
     * 🎯 Purpose: Get all uploaded banner images
     * 📤 Returns: Collection of Media models
     */
    public function getAllBannerImages() {
        return $this->getMedia('banner_images');
    }

    /**
     * @Oracode Method: Set Current Banner Image
     * 🎯 Purpose: Set a specific banner as the current active banner
     * 📤 Returns: Boolean success status
     */
    public function setCurrentBanner(Media $media): bool {
        // Clear current banner first
        $currentBanner = $this->getCurrentBanner();
        if ($currentBanner) {
            $currentBanner->delete();
        }

        // Copy the selected banner to current_banner collection
        $newBanner = $media->copy($this, 'current_banner');

        // Store reference to source media for tracking
        $newBanner->setCustomProperty('source_media_id', $media->id);
        $newBanner->save();

        return true;
    }

    /**
     * @Oracode Method: Get Profile Photo URL (Override)
     * 🎯 Purpose: Override default profile photo URL - se c'è immagine la mostra, altrimenti avatar generato
     * 📤 Returns: URL string for current profile image or generated avatar
     * 🛡️ GDPR: Se user carica immagine = consenso implicito alla pubblicazione
     */
    public function getProfilePhotoUrlAttribute(): string {
        // Se l'utente ha caricato un'immagine, la mostra (consenso implicito)
        $currentImage = $this->getCurrentProfileImage();

        if ($currentImage) {
            return $currentImage->getUrl('thumb');
        }

        // Altrimenti usa avatar generato
        return $this->defaultProfilePhotoUrl();
    }

    /**
     * @Oracode Method: Get Default Profile Photo URL
     * 🎯 Purpose: Get DiceBear generated avatar URL basato su identificatore unico
     * 📤 Returns: URL string for default avatar
     * � Logic: Usa wallet come seed se disponibile, altrimenti nome o fallback
     */
    public function defaultProfilePhotoUrl(): string {
        // Usa wallet come identificatore unico se disponibile, altrimenti nome
        $seed = urlencode($this->wallet ?? $this->name ?? "user-{$this->id}");

        return "https://api.dicebear.com/7.x/bottts/png?seed={$seed}&backgroundColor=transparent&size=512";
    }

    // ======================== COLLECTOR RELATIONSHIPS ========================

    /**
     * @Oracode Collector: Get all reservations made by this user
     * 🎯 Purpose: Track all EGI reservations/purchases by collector
     * 📤 Returns: HasMany relationship to Reservation model
     */
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    /**
     * @Oracode Collector: Get active reservations
     * 🎯 Purpose: Current active reservations by collector
     * 📤 Returns: HasMany relationship filtered for active reservations
     */
    public function activeReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->where('status', 'active')
            ->where('is_current', true);
    }

    /**
     * @Oracode Collector: Get completed/paid reservations
     * 🎯 Purpose: Successfully completed purchases by collector
     * 📤 Returns: HasMany relationship filtered for completed reservations
     */
    public function completedReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->where('status', 'completed');
    }

    /**
     * @Oracode Collector: Get valid reservations (active or completed)
     * 🎯 Purpose: All valid reservations including active bookings and completed purchases
     * 📤 Returns: HasMany relationship filtered for valid reservations
     */
    public function validReservations(): HasMany {
        return $this->hasMany(Reservation::class, 'user_id')
            ->whereIn('status', ['active', 'completed']);
    }

    /**
     * @Oracode Collector: Get owned EGIs
     * 🎯 Purpose: EGIs currently owned by this collector
     * 📤 Returns: HasMany relationship to EGI model via owner_id
     */
    public function ownedEgis(): HasMany {
        return $this->hasMany(Egi::class, 'owner_id');
    }

    /**
     * @Oracode Collector: Get published owned EGIs
     * 🎯 Purpose: Only public EGIs owned by collector for portfolio display
     * 📤 Returns: HasMany relationship filtered for published EGIs
     */
    public function publicOwnedEgis(): HasMany {
        return $this->hasMany(Egi::class, 'owner_id')
            ->where('is_published', true);
    }

    /**
     * @Oracode Collector: Owned EGIs created by other users
     * 🎯 Purpose: Identify purchases/rebinds distinct from creator's own works
     */
    public function publicOwnedEgisFromOthers(): HasMany {
        return $this->publicOwnedEgis()
            ->whereColumn('egis.owner_id', '<>', 'egis.user_id');
    }

    /**
     * @Oracode Collector: Get purchased EGIs via completed reservations
     * 🎯 Purpose: EGIs acquired through purchase transactions (valid reservations)
     * 📤 Returns: BelongsToMany relationship via reservations table
     * 🚀 FIX: Only returns EGIs with WINNING (current) reservations
     */
    public function purchasedEgis(): BelongsToMany {
        return $this->belongsToMany(Egi::class, 'reservations', 'user_id', 'egi_id')
            ->wherePivotIn('status', ['active', 'completed'])
            ->wherePivot('is_current', true)
            ->whereNull('reservations.superseded_by_id')
            ->withPivot(['offer_amount_fiat', 'fiat_currency', 'offer_amount_algo', 'exchange_rate', 'created_at', 'id', 'status'])
            ->withTimestamps();
    }

    /**
     * @Oracode Collector: Get published purchased EGIs
     * 🎯 Purpose: Only public EGIs purchased by collector for portfolio display
     * 📤 Returns: BelongsToMany relationship filtered for published purchased EGIs
     */
    public function publicPurchasedEgis(): BelongsToMany {
        return $this->purchasedEgis()
            ->where('egis.is_published', true);
    }

    /**
     * @Oracode Collector: Get EGI collection groups for purchased EGIs
     * 🎯 Purpose: Group purchased EGIs by collection for portfolio organization
     * 📤 Returns: Collection of collections with purchased EGIs
     */
    public function getCollectorCollectionsAttribute() {
        // Get collection IDs from purchased EGIs
        $collectionIds = $this->purchasedEgis()
            ->where('egis.is_published', true)
            ->pluck('collection_id')
            ->unique();

        return Collection::whereIn('id', $collectionIds)
            ->with(['egis' => function ($query) {
                // Only show EGIs that this collector has purchased
                $query->whereHas('reservations', function ($subQuery) {
                    $subQuery->where('user_id', $this->id)
                        ->whereIn('status', ['active', 'completed']);
                })->where('is_published', true);
            }])
            ->get();
    }

    /**
     * @Oracode Collector: Get reservation certificates
     * 🎯 Purpose: All reservation certificates for owned/reserved EGIs
     * 📤 Returns: Collection of certificates through reservations
     */
    public function reservationCertificates() {
        return $this->hasManyThrough(
            EgiReservationCertificate::class,
            Reservation::class,
            'user_id', // Foreign key on reservations table
            'reservation_id', // Foreign key on certificates table
            'id', // Local key on users table
            'id' // Local key on reservations table
        );
    }

    /**
     * @Oracode Collector: Get collectors stats for portfolio
     * 🎯 Purpose: Calculate collector statistics for home page display (based on purchases)
     * 📤 Returns: Array with collector stats
     */
    public function getCollectorStats(): array {
        $ownedEgiIds = $this->publicOwnedEgis()
            ->where('is_published', true)
            ->whereColumn('egis.user_id', '<>', 'egis.owner_id')
            ->pluck('egis.id');

        $winningEgiIds = $this->purchasedEgis()
            ->pluck('egis.id');

        $portfolioEgiIds = $ownedEgiIds
            ->merge($winningEgiIds)
            ->filter()
            ->unique()
            ->values();

        $totalOwnedEgis = $portfolioEgiIds->count();

        $activeReservations = $this->activeReservations()->count();
        $completedPurchases = $this->completedReservations()->count();
        $totalSpent = $this->completedReservations()->sum('offer_amount_fiat');

        $collectionsRepresented = 0;
        $creatorsSupported = 0;

        if ($totalOwnedEgis > 0) {
            $collectionsRepresented = Egi::whereIn('egis.id', $portfolioEgiIds)
                ->pluck('collection_id')
                ->filter()
                ->unique()
                ->count();

            $creatorsSupported = Egi::whereIn('egis.id', $portfolioEgiIds)
                ->join('collections', 'egis.collection_id', '=', 'collections.id')
                ->distinct('collections.creator_id')
                ->count('collections.creator_id');
        }

        // Calculate EPP impact based on portfolio size (placeholder logic)
        $eppImpact = $totalOwnedEgis * 0.15;

        return [
            'total_egis' => $totalOwnedEgis,
            'total_collections' => $collectionsRepresented,
            'active_reservations' => $activeReservations,
            'completed_purchases' => $completedPurchases,
            'total_spent_eur' => $totalSpent,
            'creators_supported' => $creatorsSupported,
            'epp_impact' => round($eppImpact, 2),
            'animate' => max($totalOwnedEgis, $completedPurchases) > 5
        ];
    }

    /**
     * @Oracode Collector: Check if user is collector
     * 🎯 Purpose: Quick check for collector role/permissions (must have valid reservations)
     * 📤 Returns: Boolean if user has collector capabilities
     */
    public function isCollector(): bool {
        return $this->hasRole('collector') ||
            $this->validReservations()->exists() ||
            $this->publicOwnedEgisFromOthers()->exists();
    }
}
