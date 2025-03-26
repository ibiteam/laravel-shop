<?php

namespace App\Http\Controllers\Home;
use App\Http\Dao\RegionDao;

class RegionController extends BaseController
{
    /**
     * 获取省市区信息
     */
    public function getRegion(RegionDao $region_dao)
    {
        return $this->success($region_dao->getRegionTree());
    }

}

