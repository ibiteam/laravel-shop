<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $seller_id       入驻商家id
 * @property string                          $name            名称
 * @property string                          $logo            Logo
 * @property string|null                     $title           标题
 * @property string|null                     $keyword         关键字
 * @property string|null                     $description     描述
 * @property int                             $status          店铺状态：0关闭,1开启
 * @property int|null                        $country         所在国家
 * @property int|null                        $province        所在省份
 * @property int|null                        $city            所在城市
 * @property string|null                     $address         详细地址
 * @property string|null                     $ship_address    发货地址
 * @property string|null                     $main_cate       主营类目
 * @property string|null                     $kf_phone        客服电话
 * @property string|null                     $leader_name     负责人姓名
 * @property string|null                     $leader_position 负责人职位
 * @property string|null                     $leader_phone    负责人联系方式
 * @property string|null                     $leader_email    负责人邮箱
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SellerEnter|null $sellerEnter
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereKfPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereLeaderEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereLeaderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereLeaderPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereLeaderPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereMainCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereShipAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SellerShop whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SellerShop extends Model
{
    use DatetimeTrait;

    // 状态
    public const STATUS_OPEN = 1; // 店铺开启
    public const STATUS_CLOSE = 0; // 店铺关闭

    protected $guarded = [];

    /**
     * 关联入驻信息.
     */
    public function sellerEnter(): BelongsTo
    {
        return $this->belongsTo(SellerEnter::class, 'seller_id', 'id');
    }

    /**
     * 关联用户表.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'seller_id');
    }
}
