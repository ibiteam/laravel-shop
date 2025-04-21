<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id         用户id
 * @property string      $consignee  收货人姓名
 * @property string      $phone 收货人手机号
 * @property int         $province        省份
 * @property int         $city            城市
 * @property int         $district        区
 * @property string      $address_detail  详细地址
 * @property int         $is_default      是否是默认收货地址 1、默认地址 0、非默认地址
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Region|null $regionCity
 * @property-read Region|null $regionDistrict
 * @property-read Region|null $regionProvince
 * @property-read User $user
 *
 * @method static Builder<static>|UserAddress newModelQuery()
 * @method static Builder<static>|UserAddress newQuery()
 * @method static Builder<static>|UserAddress query()
 * @method static Builder<static>|UserAddress whereAddressDetail($value)
 * @method static Builder<static>|UserAddress whereCity($value)
 * @method static Builder<static>|UserAddress whereCreatedAt($value)
 * @method static Builder<static>|UserAddress whereDistrict($value)
 * @method static Builder<static>|UserAddress whereId($value)
 * @method static Builder<static>|UserAddress whereIsDefault($value)
 * @method static Builder<static>|UserAddress whereProvince($value)
 * @method static Builder<static>|UserAddress whereRecipientName($value)
 * @method static Builder<static>|UserAddress whereRecipientPhone($value)
 * @method static Builder<static>|UserAddress whereUpdatedAt($value)
 * @method static Builder<static>|UserAddress whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserAddress extends BaseModel
{


    // 默认地址
    public const DEFAULT = 1;

    // 非默认地址
    public const NOT_DEFAULT = 0;

    protected $guarded = [];

    /**
     * 省
     */
    public function regionProvince(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'province', 'id');
    }

    /**
     * 市
     */
    public function regionCity(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'city', 'id');
    }

    /**
     * 区.
     */
    public function regionDistrict(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'district', 'id');
    }

    /**
     * 用户.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
