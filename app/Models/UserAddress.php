<?php

namespace App\Models;

use App\Http\Dao\RegionDao;
use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $recipient_name 收货人姓名
 * @property string $recipient_phone 收货人手机号
 * @property int $province 省份
 * @property int $city 城市
 * @property int $district 区
 * @property string $address_detail 详细地址
 * @property int $is_default 是否是默认收货地址 1、默认地址 0、非默认地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $area_address
 * @property-read \App\Models\Region|null $regionCity
 * @property-read \App\Models\Region|null $regionDistrict
 * @property-read \App\Models\Region|null $regionProvince
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereRecipientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUserId($value)
 * @mixin \Eloquent
 */
class UserAddress extends Model
{
    use DatetimeTrait;
    // 默认地址
    public const DEFAULT = 1;
    // 非默认地址
    public const NOT_DEFAULT = 0;

    protected $guarded = [];

    /**
     * 获取用户的详细地址
     *
     * @return string
     */
    public function getAreaAddressAttribute($value)
    {
        $addr = app(RegionDao::class)->getRegionName([$this->province, $this->city, $this->district]);

        return $addr['0']['region_name'].$addr['1']['region_name'].$addr['2']['region_name'].$this->address;
    }

    /**
     * 省
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regionProvince()
    {
        return $this->belongsTo(Region::class, 'province', 'id');
    }

    /**
     * 市
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regionCity()
    {
        return $this->belongsTo(Region::class, 'city', 'id');
    }

    /**
     * 区.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regionDistrict()
    {
        return $this->belongsTo(Region::class, 'district', 'id');
    }

    /**
     * 用户.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
