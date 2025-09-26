<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CoaEvent Model
 *
 * Audit trail for all CoA events including issuance, revocation, annexes, and addenda.
 * Provides complete transparency and traceability for professional workflows.
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI CoA Pro System)
 * @date 2025-09-18
 *
 * @property string $id ULID primary key
 * @property string $coa_id ULID foreign key to CoA
 * @property string $type Event type (ISSUED, REVOKED, ANNEX_ADDED, ADDENDUM_ISSUED)
 * @property array|null $payload Event details, reasons, file lists, hashes
 * @property int|null $actor_id Platform user who performed the action
 * @property \Carbon\Carbon $created_at
 */
class CoaEvent extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'coa_events';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'coa_id',
        'type',
        'payload',
        'actor_id',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    //--------------------------------------------------------------------------
    // Constants
    //--------------------------------------------------------------------------

    public const TYPE_ISSUED = 'ISSUED';
    public const TYPE_REVOKED = 'REVOKED';
    public const TYPE_ANNEX_ADDED = 'ANNEX_ADDED';
    public const TYPE_ADDENDUM_ISSUED = 'ADDENDUM_ISSUED';

    // Chain of Custody Events
    public const TYPE_AUTHOR_SIGNED = 'AUTHOR_SIGNED';
    public const TYPE_INSPECTOR_SIGNED = 'INSPECTOR_SIGNED';
    public const TYPE_PDF_REGENERATED = 'PDF_REGENERATED';
    public const TYPE_PDF_DOWNLOADED = 'PDF_DOWNLOADED';
    public const TYPE_SIGNATURE_VALIDATED = 'SIGNATURE_VALIDATED';

    //--------------------------------------------------------------------------
    // Relationships
    //--------------------------------------------------------------------------

    /**
     * Get the CoA this event belongs to
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }

    /**
     * Get the user who performed this action
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    //--------------------------------------------------------------------------
    // Scopes & Methods
    //--------------------------------------------------------------------------

    /**
     * Scope for specific event type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for timeline (chronological order)
     */
    public function scopeTimeline($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create event for CoA issuance
     */
    public static function createIssued(string $coaId, ?int $actorId = null, array $details = []): self
    {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_ISSUED,
            'payload' => array_merge([
                'event' => 'CoA issued successfully',
                'timestamp' => now()->toISOString(),
            ], $details),
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for CoA revocation
     */
    public static function createRevoked(string $coaId, string $reason, ?int $actorId = null): self
    {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_REVOKED,
            'payload' => [
                'event' => 'CoA revoked',
                'reason' => $reason,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for annex addition
     */
    public static function createAnnexAdded(
        string $coaId,
        string $annexCode,
        int $version,
        string $sha256,
        ?int $actorId = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_ANNEX_ADDED,
            'payload' => [
                'event' => 'Annex added/updated',
                'code' => $annexCode,
                'version' => $version,
                'sha256' => $sha256,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for addendum issuance
     */
    public static function createAddendumIssued(
        string $coaId,
        array $annexCodes,
        string $addendumSha256,
        ?int $actorId = null,
        ?string $note = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_ADDENDUM_ISSUED,
            'payload' => [
                'event' => 'Addendum issued',
                'annexes_updated' => $annexCodes,
                'addendum_sha256' => $addendumSha256,
                'note' => $note,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for author signature
     */
    public static function createAuthorSigned(
        string $coaId,
        array $signatureInfo,
        ?int $actorId = null,
        ?string $reason = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_AUTHOR_SIGNED,
            'payload' => [
                'event' => 'Author signature applied',
                'signature_info' => $signatureInfo,
                'reason' => $reason,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for inspector signature
     */
    public static function createInspectorSigned(
        string $coaId,
        array $signatureInfo,
        ?int $actorId = null,
        ?string $reason = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_INSPECTOR_SIGNED,
            'payload' => [
                'event' => 'Inspector signature applied',
                'signature_info' => $signatureInfo,
                'reason' => $reason,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for PDF regeneration
     */
    public static function createPdfRegenerated(
        string $coaId,
        array $fileInfo,
        array $signatureStatus,
        ?int $actorId = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_PDF_REGENERATED,
            'payload' => [
                'event' => 'PDF regenerated with signatures',
                'file_info' => $fileInfo,
                'signature_status' => $signatureStatus,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for PDF download
     */
    public static function createPdfDownloaded(
        string $coaId,
        array $downloadInfo,
        ?int $actorId = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_PDF_DOWNLOADED,
            'payload' => [
                'event' => 'PDF downloaded',
                'download_info' => $downloadInfo,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Create event for signature validation
     */
    public static function createSignatureValidated(
        string $coaId,
        array $validationResult,
        ?int $actorId = null
    ): self {
        return static::create([
            'coa_id' => $coaId,
            'type' => self::TYPE_SIGNATURE_VALIDATED,
            'payload' => [
                'event' => 'Signature validation performed',
                'validation_result' => $validationResult,
                'timestamp' => now()->toISOString(),
            ],
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }

    /**
     * Get human readable event description
     */
    public function getDisplayDescription(): string
    {
        $actorName = $this->actor?->name ?? 'System';

        return match ($this->type) {
            self::TYPE_ISSUED => "CoA issued by {$actorName}",
            self::TYPE_REVOKED => "CoA revoked by {$actorName}: " . ($this->payload['reason'] ?? 'No reason provided'),
            self::TYPE_ANNEX_ADDED => "Annex {$this->payload['code']} v{$this->payload['version']} added by {$actorName}",
            self::TYPE_ADDENDUM_ISSUED => "Addendum issued by {$actorName}" .
                ($this->payload['note'] ? ": {$this->payload['note']}" : ''),
            self::TYPE_AUTHOR_SIGNED => "Author signature applied by {$actorName}",
            self::TYPE_INSPECTOR_SIGNED => "Inspector signature applied by {$actorName}",
            self::TYPE_PDF_REGENERATED => "PDF regenerated by {$actorName}",
            self::TYPE_PDF_DOWNLOADED => "PDF downloaded by {$actorName}",
            self::TYPE_SIGNATURE_VALIDATED => "Signature validation performed by {$actorName}",
            default => "Event {$this->type} by {$actorName}",
        };
    }

    /**
     * Get event icon for UI
     */
    public function getIcon(): string
    {
        return match ($this->type) {
            self::TYPE_ISSUED => '✅',
            self::TYPE_REVOKED => '❌',
            self::TYPE_ANNEX_ADDED => '📎',
            self::TYPE_ADDENDUM_ISSUED => '📄',
            self::TYPE_AUTHOR_SIGNED => '✍️',
            self::TYPE_INSPECTOR_SIGNED => '🔍',
            self::TYPE_PDF_REGENERATED => '🔄',
            self::TYPE_PDF_DOWNLOADED => '📥',
            self::TYPE_SIGNATURE_VALIDATED => '🔐',
            default => '📋',
        };
    }

    /**
     * Get event color class for UI
     */
    public function getColorClass(): string
    {
        return match ($this->type) {
            self::TYPE_ISSUED => 'text-green-600',
            self::TYPE_REVOKED => 'text-red-600',
            self::TYPE_ANNEX_ADDED => 'text-blue-600',
            self::TYPE_ADDENDUM_ISSUED => 'text-purple-600',
            self::TYPE_AUTHOR_SIGNED => 'text-emerald-600',
            self::TYPE_INSPECTOR_SIGNED => 'text-indigo-600',
            self::TYPE_PDF_REGENERATED => 'text-amber-600',
            self::TYPE_PDF_DOWNLOADED => 'text-cyan-600',
            self::TYPE_SIGNATURE_VALIDATED => 'text-violet-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Check if this event affects CoA validity
     */
    public function affectsValidity(): bool
    {
        return in_array($this->type, [
            self::TYPE_ISSUED,
            self::TYPE_REVOKED,
            self::TYPE_AUTHOR_SIGNED,
            self::TYPE_INSPECTOR_SIGNED
        ]);
    }

    /**
     * Check if this is a signature-related event
     */
    public function isSignatureEvent(): bool
    {
        return in_array($this->type, [
            self::TYPE_AUTHOR_SIGNED,
            self::TYPE_INSPECTOR_SIGNED,
            self::TYPE_SIGNATURE_VALIDATED
        ]);
    }

    /**
     * Check if this is a PDF-related event
     */
    public function isPdfEvent(): bool
    {
        return in_array($this->type, [
            self::TYPE_PDF_REGENERATED,
            self::TYPE_PDF_DOWNLOADED
        ]);
    }
}
