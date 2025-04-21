<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id         用户ID
 * @property string      $ip              访问IP
 * @property string      $url             访问URL
 * @property string      $source          来源
 * @property string      $method          请求方式
 * @property string      $referer_url     来源URL
 * @property string      $user_agent      用户代理
 * @property string      $browser         浏览器
 * @property string      $system          系统
 * @property string      $request_data    请求数据
 * @property Carbon      $access_datetime 访问时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|AccessLog newModelQuery()
 * @method static Builder<static>|AccessLog newQuery()
 * @method static Builder<static>|AccessLog query()
 * @method static Builder<static>|AccessLog whereAccessDatetime($value)
 * @method static Builder<static>|AccessLog whereBrowser($value)
 * @method static Builder<static>|AccessLog whereCreatedAt($value)
 * @method static Builder<static>|AccessLog whereId($value)
 * @method static Builder<static>|AccessLog whereIp($value)
 * @method static Builder<static>|AccessLog whereMethod($value)
 * @method static Builder<static>|AccessLog whereRefererUrl($value)
 * @method static Builder<static>|AccessLog whereRequestData($value)
 * @method static Builder<static>|AccessLog whereSource($value)
 * @method static Builder<static>|AccessLog whereSystem($value)
 * @method static Builder<static>|AccessLog whereUpdatedAt($value)
 * @method static Builder<static>|AccessLog whereUrl($value)
 * @method static Builder<static>|AccessLog whereUserAgent($value)
 * @method static Builder<static>|AccessLog whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AccessLog extends BaseModel
{


    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'access_datetime' => 'datetime',
        ];
    }
}
