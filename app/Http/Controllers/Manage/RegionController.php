<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\RegionDao;

class RegionController extends BaseController
{
    // 获取地区数据
    public function region(RegionDao $regionDao)
    {
        return $this->success($regionDao->getRegionTree());
    }
}
