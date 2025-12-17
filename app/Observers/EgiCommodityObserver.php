<?php

namespace App\Observers;

use App\Models\Egi;
use App\Models\EgiTrait;
use App\Models\TraitCategory;
use App\Models\TraitType;
use Illuminate\Support\Facades\Log;

class EgiCommodityObserver
{
    /**
     * Handle the Egi "saved" event.
     *
     * @param  \App\Models\Egi  $egi
     * @return void
     */
    public function saved(Egi $egi)
    {
        // Only proceed if commodity_type is set (e.g. 'goldbar')
        if (empty($egi->commodity_type)) {
            return;
        }

        // Only sync if commodity_metadata or commodity_type was changed
        if (!$egi->wasChanged('commodity_metadata') && !$egi->wasChanged('commodity_type')) {
            return;
        }

        $this->syncTraits($egi);
    }

    protected function syncTraits(Egi $egi)
    {
        // OS3 Strict: Data lives in JSON. Traits are only triggers/classifiers.
        // We only ensure the 'commodity-type' trait matches the EGI's commodity_type.
        
        $type = $egi->commodity_type; // e.g. 'goldbar'
        if (empty($type)) {
            return;
        }

        // Determine category: 'commodity'
        $categorySlug = 'commodity'; 
        $category = TraitCategory::where('slug', $categorySlug)->first();

        if (!$category) {
            // Log::warning("EgiCommodityObserver: Category '{$categorySlug}' not found.");
            return;
        }

        // Ensure the 'Commodity Type' trait itself is set (trigger trait)
        $triggerTypeSlug = 'commodity-type';
        $triggerType = TraitType::where('slug', $triggerTypeSlug)
            ->where('category_id', $category->id)
            ->first();
            
        if ($triggerType) {
             EgiTrait::updateOrCreate(
                [
                    'egi_id' => $egi->id,
                    'trait_type_id' => $triggerType->id,
                ],
                [
                    'category_id' => $category->id,
                    'value' => $type,
                    'display_value' => ucfirst($type), // e.g. Goldbar
                    'is_locked' => !is_null($egi->token_EGI),
                ]
            );
        }
    }
}
