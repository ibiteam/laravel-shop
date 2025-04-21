<?php

namespace App\Models;




/**
 *
 *
 * @property int $id
 * @property int $app_decoration_id 装修页面ID
 * @property int $admin_user_id 保存人
 * @property string $app_decoration_item_ids 组件ID合集
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereAppDecorationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereAppDecorationItemIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecorationLog whereUpdatedAt($value)
 * @property-read \App\Models\AdminUser $admin_user
 * @mixin \Eloquent
 */
class AppDecorationLog extends BaseModel
{


    protected $guarded = [];

    public function getAppDecorationItemIdsAttribute($input)
    {
        return json_decode($input, true);
    }

    // 关联管理员
    public function admin_user()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
