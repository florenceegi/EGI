<?php

namespace App\Services;

use App\Models\TraitCategory;
use App\Models\TraitType;
use App\Models\EgiTrait;
use App\Models\Egi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * TraitCreationService
 *
 * Service per creare dinamicamente nuove trait categories, types e values
 * quando l'AI propone traits che non esistono nel sistema.
 *
 * Gestisce:
 * - Creazione sicura di nuove categories
 * - Creazione di nuovi types dentro categories esistenti
 * - Aggiunta di nuovi values agli allowed_values
 * - Creazione di EgiTrait associati agli EGI
 *
 * @package FlorenceEGI\Services
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0
 * @date 2025-10-21
 */
class TraitCreationService
{
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Crea una nuova trait category
     *
     * @param string $name Nome category (es: "Materials")
     * @param array $options Opzioni aggiuntive (icon, color, is_system, etc.)
     * @return TraitCategory
     * @throws \Exception
     */
    public function createCategory(string $name, array $options = []): TraitCategory
    {
        $this->logger->info("[TraitCreation] Creating new category", [
            'name' => $name,
            'options' => $options,
        ]);

        try {
            // Genera slug univoco
            $slug = $this->generateUniqueSlug($name, TraitCategory::class);

            // Default values
            $categoryData = array_merge([
                'name' => $name,
                'slug' => $slug,
                'icon' => $options['icon'] ?? '🏷️',
                'color' => $options['color'] ?? '#6366F1',
                'is_system' => false, // Le categorie create dall'AI non sono system
                'collection_id' => $options['collection_id'] ?? null,
                'sort_order' => $options['sort_order'] ?? 999,
            ], $options);

            $category = TraitCategory::create($categoryData);

            $this->logger->info("[TraitCreation] Category created successfully", [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]);

            return $category;
        } catch (\Exception $e) {
            $this->logger->error("[TraitCreation] Failed to create category", [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Crea un nuovo trait type dentro una category
     *
     * @param int $categoryId ID della category
     * @param string $name Nome type (es: "Primary Material")
     * @param array $options Opzioni (display_type, unit, allowed_values, etc.)
     * @return TraitType
     * @throws \Exception
     */
    public function createType(int $categoryId, string $name, array $options = []): TraitType
    {
        $this->logger->info("[TraitCreation] Creating new trait type", [
            'category_id' => $categoryId,
            'name' => $name,
            'options' => $options,
        ]);

        try {
            // Verifica che category esista
            $category = TraitCategory::findOrFail($categoryId);

            // Genera slug univoco
            $slug = $this->generateUniqueSlug($name, TraitType::class, ['category_id' => $categoryId]);

            // Default values
            $typeData = array_merge([
                'category_id' => $categoryId,
                'name' => $name,
                'slug' => $slug,
                'display_type' => 'text', // Default: testo libero
                'unit' => null,
                'allowed_values' => [], // Inizialmente vuoto, valori aggiunti dopo
                'is_system' => false,
                'collection_id' => $options['collection_id'] ?? null,
            ], $options);

            $type = TraitType::create($typeData);

            $this->logger->info("[TraitCreation] Trait type created successfully", [
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'category' => $category->name,
            ]);

            return $type;
        } catch (\Exception $e) {
            $this->logger->error("[TraitCreation] Failed to create trait type", [
                'category_id' => $categoryId,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Aggiunge un nuovo value agli allowed_values di un TraitType
     *
     * @param int $typeId ID del trait type
     * @param string $value Nuovo valore da aggiungere
     * @return TraitType Updated trait type
     * @throws \Exception
     */
    public function addValueToType(int $typeId, string $value): TraitType
    {
        $this->logger->info("[TraitCreation] Adding value to trait type", [
            'type_id' => $typeId,
            'value' => $value,
        ]);

        try {
            $type = TraitType::findOrFail($typeId);

            // Se display_type è numeric/date/boost_number, non aggiungiamo agli allowed_values
            if (in_array($type->display_type, ['number', 'percentage', 'date', 'boost_number'])) {
                $this->logger->info("[TraitCreation] Type allows free input, not adding to allowed_values", [
                    'type_id' => $typeId,
                    'display_type' => $type->display_type,
                ]);

                return $type;
            }

            $allowedValues = $type->allowed_values ?? [];

            // Check se value già esiste (case-insensitive)
            $existingValue = collect($allowedValues)->first(function ($val) use ($value) {
                return strtolower($val) === strtolower($value);
            });

            if ($existingValue) {
                $this->logger->info("[TraitCreation] Value already exists in allowed_values", [
                    'type_id' => $typeId,
                    'value' => $value,
                ]);

                return $type;
            }

            // Aggiungi nuovo valore
            $allowedValues[] = $value;
            $type->update(['allowed_values' => $allowedValues]);

            $this->logger->info("[TraitCreation] Value added successfully", [
                'type_id' => $typeId,
                'value' => $value,
                'total_values' => count($allowedValues),
            ]);

            return $type->fresh();
        } catch (\Exception $e) {
            $this->logger->error("[TraitCreation] Failed to add value", [
                'type_id' => $typeId,
                'value' => $value,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Crea un EgiTrait associato a un EGI
     *
     * @param int $egiId ID dell'EGI
     * @param int $categoryId ID della category
     * @param int $typeId ID del type
     * @param string $value Valore del trait
     * @param array $options Opzioni (display_value, rarity_percentage, etc.)
     * @return EgiTrait
     * @throws \Exception
     */
    public function createEgiTrait(
        int $egiId,
        int $categoryId,
        int $typeId,
        string $value,
        array $options = []
    ): EgiTrait {
        $this->logger->info("[TraitCreation] Creating EGI trait", [
            'egi_id' => $egiId,
            'category_id' => $categoryId,
            'type_id' => $typeId,
            'value' => $value,
        ]);

        try {
            // Verifica che EGI, category, type esistano
            $egi = Egi::findOrFail($egiId);
            $category = TraitCategory::findOrFail($categoryId);
            $type = TraitType::findOrFail($typeId);

            // Check se EGI è mintato → trait deve essere locked
            $isLocked = !is_null($egi->egi_type); // Se egi_type != NULL, è mintato

            // Default values
            $traitData = array_merge([
                'egi_id' => $egiId,
                'category_id' => $categoryId,
                'trait_type_id' => $typeId,
                'value' => $value,
                'display_value' => $options['display_value'] ?? $value,
                'rarity_percentage' => $options['rarity_percentage'] ?? null,
                'ipfs_hash' => $options['ipfs_hash'] ?? null,
                'is_locked' => $isLocked,
                'sort_order' => $options['sort_order'] ?? 0,
            ], $options);

            $egiTrait = EgiTrait::create($traitData);

            $this->logger->info("[TraitCreation] EGI trait created successfully", [
                'id' => $egiTrait->id,
                'egi_id' => $egiId,
                'category' => $category->name,
                'type' => $type->name,
                'value' => $value,
                'is_locked' => $isLocked,
            ]);

            return $egiTrait;
        } catch (\Exception $e) {
            $this->logger->error("[TraitCreation] Failed to create EGI trait", [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Crea un trait completo dall'AI proposal
     *
     * Gestisce tutta la logica:
     * - Crea category se nuova
     * - Crea type se nuovo
     * - Aggiunge value se nuovo
     * - Crea EgiTrait
     *
     * @param int $egiId
     * @param array $proposal Dati dalla AiTraitProposal
     * @return array{category: TraitCategory, type: TraitType, egiTrait: EgiTrait}
     * @throws \Exception
     */
    public function createTraitFromProposal(int $egiId, array $proposal): array
    {
        $this->logger->info("[TraitCreation] Creating trait from AI proposal", [
            'egi_id' => $egiId,
            'proposal' => $proposal,
        ]);

        return DB::transaction(function () use ($egiId, $proposal) {
            $category = null;
            $type = null;

            // STEP 1: Category
            if (!empty($proposal['matched_category_id'])) {
                $category = TraitCategory::findOrFail($proposal['matched_category_id']);
                $this->logger->info("[TraitCreation] Using existing category", ['id' => $category->id]);
            } elseif (!empty($proposal['created_category_id'])) {
                $category = TraitCategory::findOrFail($proposal['created_category_id']);
                $this->logger->info("[TraitCreation] Using previously created category", ['id' => $category->id]);
            } else {
                // Crea nuova category
                $category = $this->createCategory($proposal['category_suggestion']);
            }

            // STEP 2: Type
            if (!empty($proposal['matched_type_id'])) {
                $type = TraitType::findOrFail($proposal['matched_type_id']);
                $this->logger->info("[TraitCreation] Using existing type", ['id' => $type->id]);
            } elseif (!empty($proposal['created_type_id'])) {
                $type = TraitType::findOrFail($proposal['created_type_id']);
                $this->logger->info("[TraitCreation] Using previously created type", ['id' => $type->id]);
            } else {
                // Crea nuovo type
                $type = $this->createType($category->id, $proposal['type_suggestion']);
            }

            // STEP 3: Value (aggiungi agli allowed_values se necessario)
            $value = $proposal['matched_value'] ?? $proposal['value_suggestion'];
            $this->addValueToType($type->id, $value);

            // STEP 4: EgiTrait
            $egiTrait = $this->createEgiTrait(
                $egiId,
                $category->id,
                $type->id,
                $value,
                [
                    'display_value' => $proposal['display_value_suggestion'] ?? $value,
                    'sort_order' => $proposal['sort_order'] ?? 0,
                ]
            );

            $this->logger->info("[TraitCreation] Trait from proposal created successfully", [
                'egi_id' => $egiId,
                'category_id' => $category->id,
                'type_id' => $type->id,
                'egi_trait_id' => $egiTrait->id,
            ]);

            return [
                'category' => $category,
                'type' => $type,
                'egiTrait' => $egiTrait,
            ];
        });
    }

    /**
     * Genera slug univoco per un model
     *
     * @param string $name
     * @param string $modelClass
     * @param array $extraWhere Condizioni extra (es: ['category_id' => 1])
     * @return string
     */
    private function generateUniqueSlug(string $name, string $modelClass, array $extraWhere = []): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        $query = $modelClass::where('slug', $slug);

        foreach ($extraWhere as $column => $value) {
            $query->where($column, $value);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;

            $query = $modelClass::where('slug', $slug);
            foreach ($extraWhere as $column => $value) {
                $query->where($column, $value);
            }
        }

        return $slug;
    }
}









