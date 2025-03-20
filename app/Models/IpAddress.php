<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $ip         IP
 * @property string                          $country    国家名称
 * @property string                          $province   省份名称
 * @property string                          $city       城市名称
 * @property string                          $district   区县名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IpAddress extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
