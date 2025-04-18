<?php

namespace App\Models;




/**
 * @property int                             $id
 * @property string                          $name       模板名称
 * @property string                          $values     模板值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsSkuTemplate whereValues($value)
 *
 * @mixin \Eloquent
 */
class GoodsSkuTemplate extends BaseModel
{


    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'values' => 'json',
        ];
    }
}
