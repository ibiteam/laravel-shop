<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $service_id 服务id
 * @property string|null $request_param 请求参数
 * @property string|null $result_data 返回数据
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppServiceConfig|null $app_service_config
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereRequestParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereResultData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppServiceLog whereUserId($value)
 * @mixin \Eloquent
 */
class AppServiceLog extends Model
{
    use DatetimeTrait;

    protected $fillable = ['user_id', 'request_param', 'result_data', 'service_id'];

    public function app_service_config()
    {
        return $this->belongsTo(AppServiceConfig::class, 'service_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
