<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property string                          $statistic_date 统计日期
 * @property string|null                     $referer        访问来源
 * @property int                             $pv_number      访问量
 * @property int                             $uv_number      独立访问人数
 * @property int                             $ip_number      独立访问IP数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereIpNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic wherePvNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereStatisticDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessStatistic whereUvNumber($value)
 *
 * @mixin \Eloquent
 */
class AccessStatistic extends BaseModel
{


    protected $guarded = [];
}
