<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string|null $name         名称
 * @property string      $alias        别名
 * @property string      $config       配置
 * @property int         $is_enable    是否启用 0 不启用 1 启用
 * @property int         $is_record    是否记录 0 不记录 1 记录
 * @property int         $error_number 请求异常数量
 * @property int         $stop_number  请求停止数量
 * @property string|null $desc         描述
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|AppServiceConfig newModelQuery()
 * @method static Builder<static>|AppServiceConfig newQuery()
 * @method static Builder<static>|AppServiceConfig query()
 * @method static Builder<static>|AppServiceConfig whereAlias($value)
 * @method static Builder<static>|AppServiceConfig whereConfig($value)
 * @method static Builder<static>|AppServiceConfig whereCreatedAt($value)
 * @method static Builder<static>|AppServiceConfig whereDesc($value)
 * @method static Builder<static>|AppServiceConfig whereErrorNumber($value)
 * @method static Builder<static>|AppServiceConfig whereId($value)
 * @method static Builder<static>|AppServiceConfig whereIsEnable($value)
 * @method static Builder<static>|AppServiceConfig whereIsRecord($value)
 * @method static Builder<static>|AppServiceConfig whereName($value)
 * @method static Builder<static>|AppServiceConfig whereStopNumber($value)
 * @method static Builder<static>|AppServiceConfig whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppServiceConfig extends Model
{
    use DatetimeTrait;
    public const IS_ENABLE = 1; // 启用
    public const NO_ENABLE = 0; // 不启用

    public const IS_RECORD = 1; // 记录日志

    public const IBI_CHAT = 'ibi_chat'; // 国联云客服
    public const GEO_AMAP = 'geo_amap'; // 地理位置
    public const KUAI_DI_100 = 'kuai_di_100'; // 快递100

    public function getConfigAttribute($value)
    {
        return json_decode($value, true);
    }
}
