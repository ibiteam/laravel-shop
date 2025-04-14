<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $name 名称
 * @property int $is_show 是否显示
 * @property string $alias 页面别名
 * @property int $parent_id 父级集合id
 * @property int $admin_user_id 最后一次装修人ID/管理员ID
 * @property string $image_url 封面地址
 * @property string|null $title 网页TDK:标题
 * @property string|null $keywords 网页TDK:关键词
 * @property string|null $description 网页TDK:描述
 * @property string|null $release_time 发布时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereReleaseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppDecoration whereUpdatedAt($value)
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppDecoration> $children
 * @property-read int|null $children_count
 * @property-read AppDecoration|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AppDecorationItem> $item
 * @property-read int|null $item_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AppDecorationItemDraft> $itemDraft
 * @property-read int|null $item_draft_count
 * @mixin \Eloquent
 */
class AppDecoration extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    // 操作类型 - 发布
    public const OPERATE_TYPE_RELEASE = 3;
    // 操作类型 - 预览
    public const OPERATE_TYPE_PREVIEW = 2;
    // 操作类型 - 保存草稿
    public const OPERATE_TYPE_SAVE_DRAFT = 1;

    public const ALIAS_HOME = 'home'; // 首页
    public const MOBILE_HOME_BY_H5 = 'mobile_home_by_h5'; // h5 首页缓存

    public function adminUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    public function children(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function item(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppDecorationItem::class, 'app_decoration_id', 'id')->orderBy('sort');
    }

    public function itemDraft(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppDecorationItemDraft::class, 'app_decoration_id', 'id')->orderBy('sort');
    }
}
