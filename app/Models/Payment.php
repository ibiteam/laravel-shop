<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int                     $id
 * @property string                  $name         名称
 * @property string                  $alias        别名
 * @property bool                    $is_enabled   是否启用
 * @property string                  $icon         图标
 * @property string                  $description  描述
 * @property array<array-key, mixed> $config       配置信息
 * @property int                     $limit        限额，负数表示不限额
 * @property int                     $is_recommend 是否推荐
 * @property int                     $sort         排序
 * @property Carbon|null             $created_at
 * @property Carbon|null             $updated_at
 *
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment whereAlias($value)
 * @method static Builder<static>|Payment whereConfig($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereDescription($value)
 * @method static Builder<static>|Payment whereIcon($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereIsEnabled($value)
 * @method static Builder<static>|Payment whereIsRecommend($value)
 * @method static Builder<static>|Payment whereLimit($value)
 * @method static Builder<static>|Payment whereName($value)
 * @method static Builder<static>|Payment whereSort($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_recommend' => 'boolean',
            'config' => 'json',
        ];
    }
}
