<?php

namespace App\Models;



/**
 *
 *
 * @property int $id
 * @property string $name 名称
 * @property int $type 类型 1、违禁词  2、广告敏感词
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SensitiveWord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SensitiveWord extends BaseModel
{
    protected $guarded = [];
}
