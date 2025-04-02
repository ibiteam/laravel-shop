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
 * @method static Builder<static>|PaymentMethod newModelQuery()
 * @method static Builder<static>|PaymentMethod newQuery()
 * @method static Builder<static>|PaymentMethod query()
 * @method static Builder<static>|PaymentMethod whereAlias($value)
 * @method static Builder<static>|PaymentMethod whereConfig($value)
 * @method static Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static Builder<static>|PaymentMethod whereDescription($value)
 * @method static Builder<static>|PaymentMethod whereIcon($value)
 * @method static Builder<static>|PaymentMethod whereId($value)
 * @method static Builder<static>|PaymentMethod whereIsEnabled($value)
 * @method static Builder<static>|PaymentMethod whereIsRecommend($value)
 * @method static Builder<static>|PaymentMethod whereLimit($value)
 * @method static Builder<static>|PaymentMethod whereName($value)
 * @method static Builder<static>|PaymentMethod whereSort($value)
 * @method static Builder<static>|PaymentMethod whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PaymentMethod extends Model
{
    use DatetimeTrait;

    public const WECHAT = 'wechat'; // 微信支付

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
