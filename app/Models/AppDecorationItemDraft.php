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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereAppDecorationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereComponentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereIsFixedAssembly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationItemDraft whereUpdatedAt($value)
 * @property-read \App\Models\AppWebsiteDecoration $app_website_decoration
 * @mixin \Eloquent
 */
class AppDecorationItemDraft extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'content' => 'json'
        ];
    }

    public function app_decoration()
    {
        return $this->belongsTo(AppWebsiteDecoration::class, 'app_decoration_id', 'id');
    }
}
