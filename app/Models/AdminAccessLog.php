<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $admin_user_id   后台用户ID
 * @property string      $ip              请求IP
 * @property string      $url             请求URL
 * @property string      $source          请求来源
 * @property string      $method          请求方式
 * @property string      $referer_url     来源URL
 * @property string      $user_agent      用户代理
 * @property string      $browser         浏览器
 * @property string      $system          请求系统
 * @property string      $request_data    请求数据
 * @property string      $access_datetime 访问时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|AdminAccessLog newModelQuery()
 * @method static Builder<static>|AdminAccessLog newQuery()
 * @method static Builder<static>|AdminAccessLog query()
 * @method static Builder<static>|AdminAccessLog whereAccessDatetime($value)
 * @method static Builder<static>|AdminAccessLog whereAdminUserId($value)
 * @method static Builder<static>|AdminAccessLog whereBrowser($value)
 * @method static Builder<static>|AdminAccessLog whereCreatedAt($value)
 * @method static Builder<static>|AdminAccessLog whereId($value)
 * @method static Builder<static>|AdminAccessLog whereIp($value)
 * @method static Builder<static>|AdminAccessLog whereMethod($value)
 * @method static Builder<static>|AdminAccessLog whereRefererUrl($value)
 * @method static Builder<static>|AdminAccessLog whereRequestData($value)
 * @method static Builder<static>|AdminAccessLog whereSource($value)
 * @method static Builder<static>|AdminAccessLog whereSystem($value)
 * @method static Builder<static>|AdminAccessLog whereUpdatedAt($value)
 * @method static Builder<static>|AdminAccessLog whereUrl($value)
 * @method static Builder<static>|AdminAccessLog whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
class AdminAccessLog extends Model
{
    protected $guarded = [];
}
