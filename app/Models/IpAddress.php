<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $ip               IP
 * @property string                          $country_ad_code  国家AdCode
 * @property string                          $province_ad_code 省份AdCode
 * @property string                          $city_ad_code     城市AdCode
 * @property string                          $district_ad_code 区县AdCode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCityAdCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCountryAdCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereDistrictAdCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereProvinceAdCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IpAddress extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
