<?php

declare(strict_types=1);

namespace App\Http\Dao;

use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class RegionDao
{
    /**
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

    /**
     * 地区无限极
     *
     * @return mixed
     */
    public function getRegionTree()
    {
        $specialRegion = ['香港特别行政区', '澳门特别行政区', '台湾'];

        return Cache::rememberForever('getRegionTree', function () use ($specialRegion) {
            $rows = Region::query()
                ->select('id', 'parent_id', 'name', 'code', 'pinyin')
                ->where('is_enable', Region::ENABLE)
                ->where('parent_id', '<>', 0)
                ->whereNotIn('name', $specialRegion)
                ->get()->toArray();

            return $this->region_tree($rows)->toArray();
        });
    }

    public function region_tree(array $datas, int $parent_id = 1, int $number = 0)
    {
        $arr = [];

        foreach ($datas as $v) {
            if ((int) $v['parent_id'] === $parent_id) {
                $arr[$v['id']]['value'] = $v['id'];
                $arr[$v['id']]['label'] = $v['name'];
                $arr[$v['id']]['text'] = $v['name'];

                if (isset($v['pinyin'])) {
                    $arr[$v['id']]['pinyin'] = $v['pinyin'];
                    $arr[$v['id']]['pinyin_first'] = strtoupper(substr($v['pinyin'], 0, 1));
                }
                $arr[$v['id']]['code'] = isset($v['code']) ? $v['code'] : '';
                $child = $this->region_tree($datas, (int) $v['id'], $number + 1);

                if (count($child) > 0) {
                    $arr[$v['id']]['children'] = $child;
                }
            }
        }

        return collect($arr)->values();
    }
}
