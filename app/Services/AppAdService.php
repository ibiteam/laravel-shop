<?php

namespace App\Services;

use App\Models\AppAd;

class AppAdService
{
    // 获取广告图数据
    public function getAds($alias)
    {
        return AppAd::whereHas('ad_cate', fn ($query) => $query->whereAlias($alias))
            ->orderByDesc('sort')->get()->map(function(AppAd $app_ad){
                // 不展示 或者 时间限制广告
                if (AppAd::IS_NOT_SHOW == $app_ad->is_show && (AppAd::AD_TYPE_TIME_LIMIT == $app_ad->type && $app_ad->end_time < now() && $app_ad->start_time > now())) {
                    return null;
                }

                return [
                    'id' => $app_ad->id,
                    'name' => $app_ad->name,
                    'image' => $app_ad->image,
                    'link' => $app_ad->link,
               ];
            })->filter()->values()->toArray();
    }
}
