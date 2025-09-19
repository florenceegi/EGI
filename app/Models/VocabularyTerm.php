<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class VocabularyTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'category',
        'ui_group',
        'aliases',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'aliases' => 'array',
        'is_active' => 'boolean',
    ];

    // ========================================
    // SCOPES - QUERY HELPERS
    // ========================================

    /**
     * Scope per filtrare per categoria
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope per filtrare solo i termini attivi
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per filtrare per gruppo UI
     */
    public function scopeByUiGroup(Builder $query, string $uiGroup): Builder
    {
        return $query->where('ui_group', $uiGroup);
    }

    /**
     * Scope per ordinamento standard (per sort_order e poi per slug)
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('slug');
    }

    /**
     * Scope per ricerca testuale su slug e aliases
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('slug', 'like', "%{$search}%")
              ->orWhereJsonContains('aliases', $search)
              ->orWhere('ui_group', 'like', "%{$search}%");
        });
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Ottieni la traduzione del termine per la lingua corrente
     */
    public function getTranslatedLabel(): string
    {
        return __("coa_vocabulary.{$this->slug}");
    }

    /**
     * Ottieni la traduzione per una lingua specifica
     */
    public function getTranslatedLabelFor(string $locale): string
    {
        return __("coa_vocabulary.{$this->slug}", [], $locale);
    }

    /**
     * Verifica se il termine corrisponde a una ricerca
     */
    public function matchesSearch(string $search): bool
    {
        $search = strtolower($search);
        
        // Cerca nello slug
        if (str_contains(strtolower($this->slug), $search)) {
            return true;
        }

        // Cerca nei tag/alias
        if (!empty($this->aliases)) {
            foreach ($this->aliases as $alias) {
                if (str_contains(strtolower($alias), $search)) {
                    return true;
                }
            }
        }

        // Cerca nella traduzione
        $translated = strtolower($this->getTranslatedLabel());
        if (str_contains($translated, $search)) {
            return true;
        }

        return false;
    }

    /**
     * Ottieni tutti i termini di una categoria con traduzioni
     */
    public static function getCategoryWithTranslations(string $category): array
    {
        return static::byCategory($category)
            ->active()
            ->ordered()
            ->get()
            ->map(function ($term) {
                return [
                    'slug' => $term->slug,
                    'category' => $term->category,
                    'ui_group' => $term->ui_group,
                    'label' => $term->getTranslatedLabel(),
                    'sort_order' => $term->sort_order,
                ];
            })
            ->toArray();
    }

    /**
     * Ottieni termini raggruppati per UI group
     */
    public static function getGroupedByUiGroup(string $category): array
    {
        $terms = static::byCategory($category)
            ->active()
            ->ordered()
            ->get();

        $grouped = [];
        foreach ($terms as $term) {
            $grouped[$term->ui_group][] = [
                'slug' => $term->slug,
                'label' => $term->getTranslatedLabel(),
                'sort_order' => $term->sort_order,
            ];
        }

        return $grouped;
    }

    // ========================================
    // VALIDATION RULES
    // ========================================

    /**
     * Regole di validazione per il modello
     */
    public static function validationRules(): array
    {
        return [
            'slug' => 'required|string|max:255|unique:vocabulary_terms,slug',
            'category' => 'required|in:technique,materials,support',
            'ui_group' => 'required|string|max:100',
            'aliases' => 'nullable|array',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Regole di validazione per aggiornamento
     */
    public static function updateValidationRules($id): array
    {
        $rules = static::validationRules();
        $rules['slug'] = "required|string|max:255|unique:vocabulary_terms,slug,{$id}";
        return $rules;
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relazione con EgiCoaTrait (termini usati nei CoA)
     * Un termine può essere usato in molti CoA
     */
    public function usedInCoaTraits()
    {
        // Relazione complessa perché i termini sono salvati in campi JSON
        // Questa è una relazione custom che useremo con query specifiche
        return null; // Implementeremo metodi specifici per questo
    }

    // ========================================
    // QUERY BUILDERS SPECIALIZZATI
    // ========================================

    /**
     * Ottieni tutti i termini techniques
     */
    public static function techniques()
    {
        return static::byCategory('technique');
    }

    /**
     * Ottieni tutti i termini materials
     */
    public static function materials()
    {
        return static::byCategory('materials');
    }

    /**
     * Ottieni tutti i termini support
     */
    public static function supports()
    {
        return static::byCategory('support');
    }
}