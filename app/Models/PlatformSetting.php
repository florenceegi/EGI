<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Platform Settings)
 * @date 2026-02-25
 * @purpose Model per la gestione di impostazioni di piattaforma nel DB.
 *          Sostituisce config/ai-credits.php e valori hardcoded nei config file.
 *
 * @property int    $id
 * @property string $group
 * @property string $key
 * @property string $value
 * @property string $value_type
 * @property string $label
 * @property string $description
 * @property bool   $is_editable
 */
class PlatformSetting extends Model {
    protected $table = 'platform_settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'value_type',
        'label',
        'description',
        'is_editable',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    // ─── CACHE TTL ────────────────────────────────────────────────────────────
    private const CACHE_TTL  = 3600; // 1 ora
    private const CACHE_KEY  = 'platform_settings';

    // ─── STATIC HELPERS ───────────────────────────────────────────────────────

    /**
     * Legge un setting dal DB (con cache).
     * Castato automaticamente in base a value_type.
     *
     * @param string $group  es: 'ai_credits'
     * @param string $key    es: 'usd_to_eur_rate'
     * @param mixed  $default
     * @return mixed
     */
    public static function get(string $group, string $key, mixed $default = null): mixed {
        $all = self::allCached();

        $setting = $all->first(fn($s) => $s->group === $group && $s->key === $key);

        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->value_type);
    }

    /**
     * Scrive un setting nel DB e invalida la cache.
     */
    public static function set(string $group, string $key, mixed $value): void {
        self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) || is_object($value) ? json_encode($value) : (string) $value]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Invalida la cache (da usare dopo bulk-update in seeder o admin panel).
     */
    public static function invalidateCache(): void {
        Cache::forget(self::CACHE_KEY);
    }

    // ─── INTERNALS ────────────────────────────────────────────────────────────

    private static function allCached(): \Illuminate\Support\Collection {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn() => self::all());
    }

    private static function castValue(mixed $value, string $type): mixed {
        return match ($type) {
            'integer' => (int)   $value,
            'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true),
            default   => (string) $value,
        };
    }
}
