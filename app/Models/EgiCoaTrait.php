<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EgiCoaTrait extends Model {
    use HasFactory;

    protected $fillable = [
        'egi_id',
        'technique_slugs',
        'materials_slugs',
        'support_slugs',
        'technique_other',
        'materials_other',
        'support_other',
    ];

    protected $casts = [
        'technique_slugs' => 'array',
        'materials_slugs' => 'array',
        'support_slugs' => 'array',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relazione con il modello Egi
     */
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class, 'egi_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope per filtrare per EGI specifico
     */
    public function scopeForEgi(Builder $query, int $egiId): Builder {
        return $query->where('egi_id', $egiId);
    }

    /**
     * Scope per EGI con tecniche specifiche
     */
    public function scopeWithTechnique(Builder $query, string $techniqueSlug): Builder {
        return $query->whereJsonContains('technique_slugs', $techniqueSlug);
    }

    /**
     * Scope per EGI con materiali specifici
     */
    public function scopeWithMaterial(Builder $query, string $materialSlug): Builder {
        return $query->whereJsonContains('materials_slugs', $materialSlug);
    }

    /**
     * Scope per EGI con supporti specifici
     */
    public function scopeWithSupport(Builder $query, string $supportSlug): Builder {
        return $query->whereJsonContains('support_slugs', $supportSlug);
    }

    // ========================================
    // HELPER METHODS - TECHNIQUES
    // ========================================

    /**
     * Ottieni le tecniche come oggetti VocabularyTerm
     */
    public function getTechniqueTerms() {
        if (empty($this->technique_slugs)) {
            return collect();
        }

        return VocabularyTerm::whereIn('slug', $this->technique_slugs)
            ->byCategory('technique')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Ottieni le tecniche con traduzioni
     */
    public function getTechniquesWithTranslations(): array {
        $terms = $this->getTechniqueTerms();
        $translated = $terms->map(function ($term) {
            return [
                'slug' => $term->slug,
                'label' => $term->getTranslatedLabel(),
                'ui_group' => $term->ui_group,
            ];
        })->toArray();

        // Aggiungi campo "altro" se presente
        if (!empty($this->technique_other)) {
            $translated[] = [
                'slug' => 'other',
                'label' => $this->technique_other,
                'ui_group' => 'Altro',
            ];
        }

        return $translated;
    }

    // ========================================
    // HELPER METHODS - MATERIALS
    // ========================================

    /**
     * Ottieni i materiali come oggetti VocabularyTerm
     */
    public function getMaterialTerms() {
        if (empty($this->materials_slugs)) {
            return collect();
        }

        return VocabularyTerm::whereIn('slug', $this->materials_slugs)
            ->byCategory('materials')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Ottieni i materiali con traduzioni
     */
    public function getMaterialsWithTranslations(): array {
        $terms = $this->getMaterialTerms();
        $translated = $terms->map(function ($term) {
            return [
                'slug' => $term->slug,
                'label' => $term->getTranslatedLabel(),
                'ui_group' => $term->ui_group,
            ];
        })->toArray();

        // Aggiungi campo "altro" se presente
        if (!empty($this->materials_other)) {
            $translated[] = [
                'slug' => 'other',
                'label' => $this->materials_other,
                'ui_group' => 'Altro',
            ];
        }

        return $translated;
    }

    // ========================================
    // HELPER METHODS - SUPPORTS
    // ========================================

    /**
     * Ottieni i supporti come oggetti VocabularyTerm
     */
    public function getSupportTerms() {
        if (empty($this->support_slugs)) {
            return collect();
        }

        return VocabularyTerm::whereIn('slug', $this->support_slugs)
            ->byCategory('support')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Ottieni i supporti con traduzioni
     */
    public function getSupportsWithTranslations(): array {
        $terms = $this->getSupportTerms();
        $translated = $terms->map(function ($term) {
            return [
                'slug' => $term->slug,
                'label' => $term->getTranslatedLabel(),
                'ui_group' => $term->ui_group,
            ];
        })->toArray();

        // Aggiungi campo "altro" se presente
        if (!empty($this->support_other)) {
            $translated[] = [
                'slug' => 'other',
                'label' => $this->support_other,
                'ui_group' => 'Altro',
            ];
        }

        return $translated;
    }

    // ========================================
    // HELPER METHODS - COMPLETE DATA
    // ========================================

    /**
     * Ottieni tutti i trait completi con traduzioni
     */
    public function getAllTraitsWithTranslations(): array {
        return [
            'techniques' => $this->getTechniquesWithTranslations(),
            'materials' => $this->getMaterialsWithTranslations(),
            'supports' => $this->getSupportsWithTranslations(),
        ];
    }

    /**
     * Ottieni tutti i trait per una lingua specifica
     */
    public function getAllTraitsForLocale(string $locale): array {
        $currentLocale = app()->getLocale();
        app()->setLocale($locale);

        $traits = $this->getAllTraitsWithTranslations();

        app()->setLocale($currentLocale);

        return $traits;
    }

    /**
     * Verifica se l'EGI ha traits definiti
     */
    public function hasTraits(): bool {
        return !empty($this->technique_slugs) ||
            !empty($this->materials_slugs) ||
            !empty($this->support_slugs) ||
            !empty($this->technique_other) ||
            !empty($this->materials_other) ||
            !empty($this->support_other);
    }

    // ========================================
    // SETTER METHODS
    // ========================================

    /**
     * Imposta le tecniche (normalizza l'input)
     */
    public function setTechniques(array $slugs, ?string $other = null): void {
        $this->technique_slugs = array_filter($slugs);
        $this->technique_other = $other;
    }

    /**
     * Imposta i materiali (normalizza l'input)
     */
    public function setMaterials(array $slugs, ?string $other = null): void {
        $this->materials_slugs = array_filter($slugs);
        $this->materials_other = $other;
    }

    /**
     * Imposta i supporti (normalizza l'input)
     */
    public function setSupports(array $slugs, ?string $other = null): void {
        $this->support_slugs = array_filter($slugs);
        $this->support_other = $other;
    }

    // ========================================
    // VALIDATION RULES
    // ========================================

    /**
     * Regole di validazione per il modello
     */
    public static function validationRules(): array {
        return [
            'egi_id' => 'required|exists:egis,id',
            'technique_slugs' => 'nullable|array',
            'technique_slugs.*' => 'string|exists:vocabulary_terms,slug',
            'materials_slugs' => 'nullable|array',
            'materials_slugs.*' => 'string|exists:vocabulary_terms,slug',
            'support_slugs' => 'nullable|array',
            'support_slugs.*' => 'string|exists:vocabulary_terms,slug',
            'technique_other' => 'nullable|string|max:500',
            'materials_other' => 'nullable|string|max:500',
            'support_other' => 'nullable|string|max:500',
        ];
    }

    // ========================================
    // STATIC HELPERS
    // ========================================

    /**
     * Trova o crea CoA traits per un EGI
     */
    public static function findOrCreateForEgi(int $egiId): self {
        return static::firstOrCreate(['egi_id' => $egiId]);
    }

    /**
     * Aggiorna o crea i traits per un EGI
     */
    public static function updateOrCreateForEgi(int $egiId, array $data): self {
        return static::updateOrCreate(
            ['egi_id' => $egiId],
            $data
        );
    }

    /**
     * Ottieni statistiche utilizzo termini
     */
    public static function getTermUsageStats(): array {
        $stats = [
            'total_egis_with_traits' => static::count(),
            'technique_usage' => [],
            'material_usage' => [],
            'support_usage' => [],
        ];

        // Conta l'utilizzo dei termini (query complessa per JSON)
        $allTraits = static::all();

        foreach ($allTraits as $trait) {
            // Tecniche
            if (!empty($trait->technique_slugs)) {
                foreach ($trait->technique_slugs as $slug) {
                    $stats['technique_usage'][$slug] = ($stats['technique_usage'][$slug] ?? 0) + 1;
                }
            }

            // Materiali
            if (!empty($trait->materials_slugs)) {
                foreach ($trait->materials_slugs as $slug) {
                    $stats['material_usage'][$slug] = ($stats['material_usage'][$slug] ?? 0) + 1;
                }
            }

            // Supporti
            if (!empty($trait->support_slugs)) {
                foreach ($trait->support_slugs as $slug) {
                    $stats['support_usage'][$slug] = ($stats['support_usage'][$slug] ?? 0) + 1;
                }
            }
        }

        return $stats;
    }
}
