<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $app_decoration_id 装修页面ID
 * @property string $name 板块名称
 * @property string $component_name 组件名称
 * @property int $is_show 是否展示 1、展示 0、不展示
 * @property int $sort 排序
 * @property string $content 组件内容 json格式
 * @property int $is_fixed_assembly 是否是固定组件 1是 0否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereAppDecorationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereComponentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereIsFixedAssembly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AppDecorationItem extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    // 轮播图
    public const COMPONENT_NAME_HORIZONTAL_CAROUSEL = 'horizontal_carousel'; // 组件名称
    public const STYLE_TILED = 1; // 显示样式 - 平铺
    public const STYLE_TRANSITION = 2; // 显示样式 - 过渡

    protected $casts = [
        'content' => 'array',
    ];
}
