<?php

namespace App\Models;

use App\Enums\RouterEnum;
use App\Services\RouterService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int                             $id
 * @property string                          $name          名称
 * @property int                             $is_show       是否显示
 * @property string                          $alias         页面别名
 * @property int                             $parent_id     父级集合id
 * @property int                             $admin_user_id 最后一次装修人ID/管理员ID
 * @property string                          $image_url     封面地址
 * @property string|null                     $title         网页TDK:标题
 * @property string|null                     $keywords      网页TDK:关键词
 * @property string|null                     $description   网页TDK:描述
 * @property string|null                     $release_time  发布时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AdminUser|null $adminUser
 * @property-read Collection<int, AppDecoration> $children
 * @property-read int|null $children_count
 * @property-read Collection<int, AppDecorationItem> $item
 * @property-read int|null $item_count
 * @property-read Collection<int, AppDecorationItemDraft> $itemDraft
 * @property-read int|null $item_draft_count
 * @property-read AppDecoration|null $parent
 * @property-read mixed $url
 *
 * @method static Builder<static>|AppDecoration newModelQuery()
 * @method static Builder<static>|AppDecoration newQuery()
 * @method static Builder<static>|AppDecoration query()
 * @method static Builder<static>|AppDecoration whereAdminUserId($value)
 * @method static Builder<static>|AppDecoration whereAlias($value)
 * @method static Builder<static>|AppDecoration whereCreatedAt($value)
 * @method static Builder<static>|AppDecoration whereDescription($value)
 * @method static Builder<static>|AppDecoration whereId($value)
 * @method static Builder<static>|AppDecoration whereImageUrl($value)
 * @method static Builder<static>|AppDecoration whereIsShow($value)
 * @method static Builder<static>|AppDecoration whereKeywords($value)
 * @method static Builder<static>|AppDecoration whereName($value)
 * @method static Builder<static>|AppDecoration whereParentId($value)
 * @method static Builder<static>|AppDecoration whereReleaseTime($value)
 * @method static Builder<static>|AppDecoration whereTitle($value)
 * @method static Builder<static>|AppDecoration whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppDecoration extends BaseModel
{
    // 操作类型 - 发布
    public const OPERATE_TYPE_RELEASE = 3;

    // 操作类型 - 预览
    public const OPERATE_TYPE_PREVIEW = 2;

    // 操作类型 - 保存草稿
    public const OPERATE_TYPE_SAVE_DRAFT = 1;
    public const ALIAS_HOME = 'home'; // 首页
    public const MOBILE_HOME_BY_H5 = 'mobile_home_by_h5'; // h5 首页缓存

    public static array $path = [
        self::ALIAS_HOME => RouterEnum::HOME_PREVIEW->value,
    ];
    protected $guarded = [];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function item(): HasMany
    {
        return $this->hasMany(AppDecorationItem::class, 'app_decoration_id', 'id')->orderBy('sort');
    }

    public function itemDraft(): HasMany
    {
        return $this->hasMany(AppDecorationItemDraft::class, 'app_decoration_id', 'id')->orderBy('sort');
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $routerService = new RouterService;

                if ($this->alias == self::ALIAS_HOME) {
                    return $routerService->getRouterPath(RouterEnum::HOME->value);
                }
            }
        );
    }
}
