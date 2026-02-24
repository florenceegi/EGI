<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Complaint Model
 * Purpose: DSA Notice-and-Action and internal complaint handling
 * Compliance: Digital Services Act (Reg. UE 2022/2065) Art. 16, 17, 20, 21
 *
 * @package App\Models
 * @version 1.0
 */
class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $fillable = [
        'type',
        'status',
        'reporter_user_id',
        'reported_content_type',
        'reported_content_id',
        'reported_user_id',
        'description',
        'evidence_urls',
        'decision',
        'decision_by',
        'decided_at',
        'appeal_text',
        'appeal_decided_at',
    ];

    protected $casts = [
        'evidence_urls' => 'array',
        'decided_at' => 'datetime',
        'appeal_decided_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Complaint type classification (Art. 16, 20 DSA)
     */
    public const TYPES = [
        'CONTENT_REPORT' => 'content_report',
        'IP_VIOLATION' => 'ip_violation',
        'FRAUD' => 'fraud',
        'MODERATION_APPEAL' => 'moderation_appeal',
        'GENERAL' => 'general',
    ];

    /**
     * Status workflow
     */
    public const STATUSES = [
        'RECEIVED' => 'received',
        'UNDER_REVIEW' => 'under_review',
        'ACTION_TAKEN' => 'action_taken',
        'DISMISSED' => 'dismissed',
        'APPEALED' => 'appealed',
        'RESOLVED' => 'resolved',
    ];

    /**
     * Reportable content types
     */
    public const CONTENT_TYPES = [
        'EGI' => 'egi',
        'COLLECTION' => 'collection',
        'USER_PROFILE' => 'user_profile',
        'COMMENT' => 'comment',
    ];

    /**
     * Get the user who submitted the complaint.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    /**
     * Get the reported user.
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Get the moderator who decided on this complaint.
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_by');
    }

    /**
     * Generate unique DSA reference number: DSA-2026-000001
     */
    public function getComplaintReferenceAttribute(): string
    {
        return 'DSA-' . $this->created_at->format('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if complaint is still open (not final).
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUSES['RECEIVED'],
            self::STATUSES['UNDER_REVIEW'],
            self::STATUSES['APPEALED'],
        ]);
    }

    /**
     * Check if the user can appeal this complaint's decision (Art. 20 DSA).
     * Only 'action_taken' or 'dismissed' decisions can be appealed.
     */
    public function canAppeal(): bool
    {
        return in_array($this->status, [
            self::STATUSES['ACTION_TAKEN'],
            self::STATUSES['DISMISSED'],
        ]);
    }

    /**
     * Scope: open complaints only.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            self::STATUSES['RECEIVED'],
            self::STATUSES['UNDER_REVIEW'],
            self::STATUSES['APPEALED'],
        ]);
    }

    /**
     * Scope: complaints by a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('reporter_user_id', $userId);
    }
}
