<?php

namespace App\Egi\Commodity;

use Exception;

class CommodityFactory
{
    /**
     * Create a commodity instance based on the type.
     *
     * @param string $type
     * @return CommodityContract
     * @throws Exception
     */
    public static function make(string $type): CommodityContract
    {
        return match (strtolower($type)) {
            'goldbar', 'gold-bar' => new GoldBarCommodity(),
            // Future extensions:
            // 'silverbar' => new SilverBarCommodity(),
            default => throw new Exception("Commodity type unsupported: {$type}"),
        };
    }
}
