<?php

namespace App\Http\Controllers\Manage;

use App\Enums\CacheNameEnum;
use App\Http\Dao\RegionDao;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class RegionController extends BaseController
{
    /**
     * 地区管理.
     */
    public function index()
    {
        $checked = [];
        $region_list = Region::query()->with('allChildren')
            ->whereType(Region::REGION_TYPE_PROVINCE)
            ->get()->map(function (Region $region, $key) use (&$checked) {
                if ($region->allChildren->isNotEmpty()) {
                    if ($key === 0) {
                        $checked[] = $region->id;
                    }
                    $region->name = "[{$region->id}]".$region->name."【{$region->code}】";
                    $region->allChildren->map(function (Region $region, $index) use (&$checked, $key) {
                        if ($index === 0 && $key === 0) {
                            $checked[] = $region->id;
                        }
                        $region->name = "[{$region->id}]".$region->name."【{$region->code}】";

                        if ($region->allChildren->isNotEmpty()) {
                            $region->allChildren->map(function (Region $region, $k) use (&$checked, $key, $index) {
                                unset($region->allChildren);

                                if ($index === 0 && $key === 0 && $k === 0) {
                                    $checked[] = $region->id;
                                }
                                $region->name = "[{$region->id}]".$region->name."【{$region->code}】";
                            });
                        }
                    });
                }

                return $region;
            });

        $data = [
            'region_checked' => $checked,
            'region_list' => $region_list,
        ];

        return $this->success($data);
    }

    /**
     * 清除地区缓存.
     */
    public function clearCache()
    {
        Cache::forget(CacheNameEnum::REGION_TREE->value);

        return $this->success('清除成功');
    }

    /**
     * 获取地区数据.
     */
    public function regionTree(RegionDao $regionDao)
    {
        return $this->success($regionDao->getRegionTree());
    }
}
