<?php

namespace App\Models;

use App\Enums\RouterEnum;
use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $router_category_id 分类id
 * @property string                          $name               名称
 * @property string                          $alias              别名
 * @property string|null                     $h5_url             h5地址
 * @property array<array-key, mixed>|null    $params             额外参数
 * @property int                             $sort               排序
 * @property int                             $is_show            是否显示：1显示 2隐藏
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RouterCategory|null $routerCategory
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router newModelQuezry()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereH5Url($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereRouterCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Router whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Router extends Model
{
    use DatetimeTrait;

    public const IS_SHOW_YES = 1; // 显示
    public const IS_SHOW_NO = 0; // 不显示

    public static array $path = [
        RouterEnum::PAY_SUCCESS->value => RouterEnum::PAY_SUCCESS,
        RouterEnum::ORDER_SUCCESS->value => RouterEnum::ORDER_SUCCESS,
        RouterEnum::SUPERMARKET->value => RouterEnum::SUPERMARKET,
        RouterEnum::HOME_PREVIEW->value => RouterEnum::HOME_PREVIEW,
    ];

    protected $guarded = [];

    public function routerCategory(): BelongsTo
    {
        return $this->belongsTo(RouterCategory::class, 'router_category_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'params' => 'json',
        ];
    }
}
