<?php

namespace App\Services;

use App\Models\Egi;

/**
 * Service to handle serial number generation for Child EGIs
 */
class SerialService
{
    /**
     * Generate the next serial number for a child of the given Master EGI.
     * Format: {MasterID}-{SequenceNumber} (e.g. 104-1, 104-2)
     * 
     * @param Egi $master
     * @return string
     */
    public function nextFor(Egi $master): string
    {
        // Count existing children (including soft-deleted ones if needed, 
        // but usually we want unique active serials. Adjust scope if needed).
        // Using withTrashed() guarantees uniqueness even if some are deleted.
        $count = $master->children()->withTrashed()->count();
        
        $sequence = $count + 1;
        
        return sprintf('%d-%d', $master->id, $sequence);
    }
}
