<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $name       模板名称
 * @property string                          $values     模板值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsParameterTemplate whereValues($value)
 *
 * @mixin \Eloquent
 */
class GoodsParameterTemplate extends Model
{
    use DatetimeTrait;

    protected $guarded = [];
}
