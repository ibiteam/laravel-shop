<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\RegionDao;

class RegionController extends BaseController
{
    public function regionTree(RegionDao $regionDao)
    {
        return $this->success($regionDao->getRegionTree());
    }
}
