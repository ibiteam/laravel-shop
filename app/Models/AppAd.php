<?php

namespace App\Models;

/**
 *
 *
 * @property int $id
 * @property string|null $name 标题
 * @property string $image 广告图
 * @property int $sort 排序
 * @property int $link_type 链接类型 1、https 2、移动端链接
 * @property string|null $link 链接
 * @property int $is_show 是否展示 1、是 0、否
 * @property int $type 广告类型 1、时间限制 2、长久广告
 * @property string|null $start_time 开始时间
 * @property string|null $end_time 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereUpdatedAt($value)
 * @property int $ad_cate_id 广告分类id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppAd whereAdCateId($value)
 * @property-read \App\Models\AdCate|null $ad_cate
 * @mixin \Eloquent
 */
class AppAd extends BaseModel
{
    protected $guarded = [];

    public const IS_SHOW = 1; // 展示
    public const IS_NOT_SHOW = 0; // 不展示


    public const AD_TYPE_TIME_LIMIT = 1; // 限时广告
    public const AD_TYPE_LONG_TERM = 2; // 长久广告

    public function ad_cate()
    {
        return $this->belongsTo(AdCate::class, 'ad_cate_id', 'id');
    }
}
