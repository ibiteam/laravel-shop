<?php

declare(strict_types=1);

namespace App\Http\Dao;

use App\Enums\CacheNameEnum;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RegionDao
{
    public function getRegionName(array $regionIds = []): EloquentCollection
    {
        if (count($regionIds) > 2) {
            sort($regionIds);
        }

        return Region::query()
            ->select('name')
            ->whereIn('id', $regionIds)
            ->get();
    }

    /**
     * 地区无限极
     */
    public function getRegionTree(): mixed
    {
        return Cache::rememberForever(CacheNameEnum::REGION_TREE->value, function () {
            $specialRegion = ['香港特别行政区', '澳门特别行政区', '台湾'];
            $rows = Region::query()
                ->select('id', 'parent_id', 'name', 'code', 'pinyin')
                ->where('parent_id', '<>', 0)
                ->whereNotIn('name', $specialRegion)
                ->get()->toArray();

            return $this->regionTree($rows)->toArray();
        });
    }

    private function regionTree(array $region_data, int $parent_id = 1, int $number = 0): Collection
    {
        $arr = [];

        foreach ($region_data as $v) {
            if ((int) $v['parent_id'] === $parent_id) {
                $arr[$v['id']]['value'] = $v['id'];
                $arr[$v['id']]['label'] = $v['name'];
                $arr[$v['id']]['text'] = $v['name'];

                if (isset($v['pinyin'])) {
                    $arr[$v['id']]['pinyin'] = $v['pinyin'];
                    $arr[$v['id']]['pinyin_first'] = strtoupper(substr($v['pinyin'], 0, 1));
                }
                $arr[$v['id']]['code'] = $v['code'] ?? '';
                $child = $this->regionTree($region_data, (int) $v['id'], $number + 1);

                if (count($child) > 0) {
                    $arr[$v['id']]['children'] = $child;
                }
            }
        }

        return collect($arr)->values();
    }
}
