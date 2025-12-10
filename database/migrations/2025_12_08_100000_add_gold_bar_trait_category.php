<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration to add Gold Bar trait category and types
 *
 * This migration adds the Gold Bar category without truncating existing data.
 * Safe for production use.
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Check if category already exists
        $exists = DB::table('trait_categories')
            ->where('slug', 'gold-bar')
            ->exists();

        if ($exists) {
            return; // Already migrated
        }

        // Insert Gold Bar category
        $categoryId = DB::table('trait_categories')->insertGetId([
            'name' => 'Gold Bar',
            'slug' => 'gold-bar',
            'icon' => '🥇',
            'color' => '#FFD700',
            'is_system' => true,
            'sort_order' => 9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Gold Bar trait types
        $traitTypes = [
            [
                'name' => 'Gold Weight',
                'slug' => 'gold-weight',
                'display_type' => 'number',
                'allowed_values' => null,
            ],
            [
                'name' => 'Gold Weight Unit',
                'slug' => 'gold-weight-unit',
                'display_type' => 'text',
                'allowed_values' => json_encode([
                    'Grams',
                    'Ounces',
                    'Troy Ounces'
                ]),
            ],
            [
                'name' => 'Gold Purity',
                'slug' => 'gold-purity',
                'display_type' => 'text',
                'allowed_values' => json_encode([
                    '999',   // 24k - 99.9% pure
                    '995',   // 99.5% pure
                    '990',   // 99.0% pure
                    '916',   // 22k - 91.6% pure
                    '750'    // 18k - 75.0% pure
                ]),
            ],
            [
                'name' => 'Gold Margin Percent',
                'slug' => 'gold-margin-percent',
                'display_type' => 'number',
                'allowed_values' => null,
            ],
            [
                'name' => 'Gold Margin Fixed',
                'slug' => 'gold-margin-fixed',
                'display_type' => 'number',
                'allowed_values' => null,
            ],
        ];

        foreach ($traitTypes as $type) {
            DB::table('trait_types')->insert(array_merge($type, [
                'category_id' => $categoryId,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        // Get category ID
        $category = DB::table('trait_categories')
            ->where('slug', 'gold-bar')
            ->first();

        if ($category) {
            // Delete trait types first (foreign key)
            DB::table('trait_types')
                ->where('category_id', $category->id)
                ->delete();

            // Delete category
            DB::table('trait_categories')
                ->where('id', $category->id)
                ->delete();
        }
    }
};
