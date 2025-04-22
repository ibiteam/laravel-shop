<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name 广告分类名称
 * @property string $alias 别名
 * @property int $type 类型 1、移动端 2、PC端
 * @property int $is_show 是否展示 1、是 0、否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AppAd> $app_ad
 * @property-read int|null $app_ad_count
 * @property int $width 宽度
 * @property int $height 高度
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdCate whereWidth($value)
 * @mixin \Eloquent
 */
class AdCate extends Model
{
    public const MOBILE_TYPE = 1;   // 移动端
    public const APP_SHOP_TOP_BANNER = 'app_shop_top_banner'; // 移动端超市顶部banner

    public function app_ad()
    {
        return $this->hasMany(AppAd::class, 'ad_cate_id', 'id');
    }

    // 分类名
    public static function getCateNames($type)
    {
        if ($type == AdCate::MOBILE_TYPE) {
            $data['supermarket']['alias'] = '超市';
        }

        return $data;
    }

    // 获取app广告图 -- 默认获取首页
    public static function getCates($adCateName = 'supermarket', $name = '')
    {
        $query = self::query();

        if ($name) {
            $query = $query->where('name', 'like', '%'.$name.'%');
        }

        switch ($adCateName) {
            case $adCateName == 'supermarket': // app 分类
                $alias = [
                    self::APP_SHOP_TOP_BANNER, // APP 分类专场 banner
                ];

                break;

            default:
                $alias = [];
        }

        return $query->whereIn('alias', $alias)->orderByRaw('FIND_IN_SET(alias, "'.implode(',', $alias).'")')->get();
    }
}
