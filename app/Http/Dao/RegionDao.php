<?php

declare(strict_types=1);

namespace App\Http\Dao;

use App\Models\Region;

class RegionDao
{
    /**
     * @param array $regionIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRegionName(array $regionIds = [])
    {
        if (count($regionIds) > 2) {
            sort($regionIds);
        }

        return Region::query()
            ->select('name')
            ->whereIn('id', $regionIds)
            ->get();
    }
}
